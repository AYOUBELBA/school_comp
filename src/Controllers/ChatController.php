<?php
// src/Controllers/ChatController.php

namespace Controllers;

use Core\Controller;
use Core\Auth;
use Core\Database;

class ChatController extends Controller
{
    private $db;
    
    public function __construct()
    {
        Auth::requireLogin();
        $this->db = Database::getInstance();
    }
    
    public function index()
    {
        $userId = Auth::getUserId();
        
        // Get all chats for the user
        $chats = $this->db->fetchAll(
            "SELECT c.*, 
                    p.title as product_title,
                    p.id as product_id,
                    (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1) as product_image,
                    CASE 
                        WHEN c.buyer_id = ? THEN u2.full_name 
                        ELSE u1.full_name 
                    END as other_party_name,
                    CASE 
                        WHEN c.buyer_id = ? THEN u2.id 
                        ELSE u1.id 
                    END as other_party_id,
                    (SELECT COUNT(*) FROM messages WHERE chat_id = c.id AND receiver_id = ? AND is_read = 0) as unread_count
             FROM chats c
             JOIN products p ON c.product_id = p.id
             JOIN users u1 ON c.buyer_id = u1.id
             JOIN users u2 ON c.seller_id = u2.id
             WHERE c.buyer_id = ? OR c.seller_id = ?
             ORDER BY c.last_message_time DESC",
            [$userId, $userId, $userId, $userId, $userId]
        );
        
        $this->view('chat/index', [
            'chats' => $chats,
            'userId' => $userId,
            'currentPage' => 'chat'
        ]);
    }
    
    public function getUnreadCount()
    {
        $userId = Auth::getUserId();
        
        $result = $this->db->fetch(
            "SELECT COUNT(*) as count FROM messages 
             WHERE receiver_id = ? AND is_read = 0",
            [$userId]
        );
        
        return $this->json([
            'count' => (int)($result['count'] ?? 0)
        ]);
    }
    
    public function getMessages($chatId)
    {
        $userId = Auth::getUserId();
        
        // Verify user is part of this chat
        $chat = $this->db->fetch(
            "SELECT * FROM chats WHERE id = ? AND (buyer_id = ? OR seller_id = ?)",
            [$chatId, $userId, $userId]
        );
        
        if (!$chat) {
            return $this->json(['error' => 'Unauthorized'], 403);
        }
        
        // Mark messages as read
        $this->db->update('messages', [
            'is_read' => 1,
            'read_at' => date('Y-m-d H:i:s')
        ], 'chat_id = ? AND receiver_id = ? AND is_read = 0', [$chatId, $userId]);
        
        // Get messages
        $messages = $this->db->fetchAll(
            "SELECT m.*, u.full_name as sender_name 
             FROM messages m
             JOIN users u ON m.sender_id = u.id
             WHERE m.chat_id = ?
             ORDER BY m.created_at ASC",
            [$chatId]
        );
        
        return $this->json([
            'chat' => $chat,
            'messages' => $messages
        ]);
    }
    
    public function start()
    {
        $productId = (int)($_GET['product_id'] ?? 0);
        $userId = Auth::getUserId();
        
        if (!$productId) {
            return $this->redirect('/chat');
        }
        
        // Get product and seller
        $product = $this->db->fetch(
            "SELECT id, seller_id, title FROM products WHERE id = ? AND status = 'active'",
            [$productId]
        );
        
        if (!$product) {
            return $this->redirect('/chat', ['error' => 'Product not found']);
        }
        
        // Check if chat exists
        $chat = $this->db->fetch(
            "SELECT id FROM chats 
             WHERE product_id = ? AND buyer_id = ? AND seller_id = ?",
            [$productId, $userId, $product['seller_id']]
        );
        
        if (!$chat) {
            // Create new chat
            $chatId = $this->db->insert('chats', [
                'product_id' => $productId,
                'buyer_id' => $userId,
                'seller_id' => $product['seller_id'],
                'status' => 'active'
            ]);
        } else {
            $chatId = $chat['id'];
        }
        
        return $this->redirect('/chat?chat_id=' . $chatId);
    }
    
    public function send()
    {
        $validation = $this->validate($_POST, [
            'chat_id' => 'required|numeric',
            'message' => 'required|min:1|max:1000'
        ]);
        
        if (!$validation['valid']) {
            return $this->json(['errors' => $validation['errors']], 422);
        }
        
        $userId = Auth::getUserId();
        $chatId = (int)$_POST['chat_id'];
        $message = trim($_POST['message']);
        
        // Get chat details
        $chat = $this->db->fetch(
            "SELECT * FROM chats WHERE id = ?",
            [$chatId]
        );
        
        if (!$chat) {
            return $this->json(['error' => 'Chat not found'], 404);
        }
        
        // Determine receiver
        $receiverId = ($chat['buyer_id'] == $userId) ? $chat['seller_id'] : $chat['buyer_id'];
        
        // Save message
        $messageId = $this->db->insert('messages', [
            'chat_id' => $chatId,
            'sender_id' => $userId,
            'receiver_id' => $receiverId,
            'message' => $message,
            'is_read' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        
        // Update chat last message
        $this->db->update('chats', [
            'last_message' => $message,
            'last_message_time' => date('Y-m-d H:i:s')
        ], 'id = ?', [$chatId]);
        
        // Send via WebSocket if available
        $this->sendWebSocketMessage($chatId, $userId, $receiverId, $message);
        
        return $this->json([
            'success' => true,
            'message_id' => $messageId,
            'message' => $message,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
    
    private function sendWebSocketMessage($chatId, $senderId, $receiverId, $message)
    {
        // This will be handled by the WebSocket server
    }
}