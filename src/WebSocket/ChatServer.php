<?php
// src/WebSocket/ChatServer.php

namespace WebSocket;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Core\Database;

class ChatServer implements MessageComponentInterface
{
    protected $clients;
    protected $db;
    private $userConnections;
    private $connectionMap;
    
    public function __construct()
    {
        $this->clients = new \SplObjectStorage();
        $this->userConnections = [];
        $this->connectionMap = [];
        
        // Initialize database
        try {
            $this->db = Database::getInstance();
        } catch (\Exception $e) {
            echo "Database connection failed: " . $e->getMessage() . "\n";
            $this->db = null;
        }
    }
    
    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
        
        // Store connection
        $this->connectionMap[$conn->resourceId] = $conn;
    }
    
    public function onMessage(ConnectionInterface $from, $msg)
    {
        try {
            $data = json_decode($msg, true);
            
            if (!isset($data['type'])) {
                return;
            }
            
            switch ($data['type']) {
                case 'authenticate':
                    $this->authenticateUser($from, $data);
                    break;
                    
                case 'message':
                    $this->handleMessage($from, $data);
                    break;
                    
                case 'typing':
                    $this->handleTyping($from, $data);
                    break;
                    
                case 'read':
                    $this->markAsRead($from, $data);
                    break;
                    
                default:
                    $from->send(json_encode([
                        'type' => 'error',
                        'message' => 'Unknown message type'
                    ]));
            }
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
            $from->send(json_encode([
                'type' => 'error',
                'message' => 'Server error: ' . $e->getMessage()
            ]));
        }
    }
    
    private function authenticateUser($conn, $data)
    {
        if (!isset($data['user_id'])) {
            $conn->send(json_encode([
                'type' => 'error',
                'message' => 'Authentication failed: No user ID'
            ]));
            return;
        }
        
        $userId = (int)$data['user_id'];
        
        // Store user connection mapping
        $this->userConnections[$userId] = $conn->resourceId;
        $conn->userId = $userId;
        
        $conn->send(json_encode([
            'type' => 'authenticated',
            'user_id' => $userId,
            'message' => 'Authentication successful'
        ]));
        
        echo "User {$userId} authenticated\n";
        
        // Send unread count
        if ($this->db) {
            $this->sendUnreadCount($userId);
        }
    }
    
    private function handleMessage($from, $data)
    {
        if (!isset($from->userId)) {
            $from->send(json_encode([
                'type' => 'error',
                'message' => 'Not authenticated'
            ]));
            return;
        }
        
        // Validate required fields
        if (!isset($data['chat_id']) || !isset($data['receiver_id']) || !isset($data['message'])) {
            $from->send(json_encode([
                'type' => 'error',
                'message' => 'Missing required fields'
            ]));
            return;
        }
        
        $chatId = (int)$data['chat_id'];
        $receiverId = (int)$data['receiver_id'];
        $message = trim($data['message']);
        
        if (empty($message)) {
            $from->send(json_encode([
                'type' => 'error',
                'message' => 'Message cannot be empty'
            ]));
            return;
        }
        
        // Store in database
        if ($this->db) {
            try {
                $messageData = [
                    'chat_id' => $chatId,
                    'sender_id' => $from->userId,
                    'receiver_id' => $receiverId,
                    'message' => $message,
                    'is_read' => 0,
                    'created_at' => date('Y-m-d H:i:s')
                ];
                
                $messageId = $this->db->insert('messages', $messageData);
                
                // Update chat last message
                $this->db->update('chats', [
                    'last_message' => $message,
                    'last_message_time' => date('Y-m-d H:i:s')
                ], 'id = ?', [$chatId]);
                
                $deliveryData = [
                    'type' => 'message',
                    'id' => $messageId,
                    'chat_id' => $chatId,
                    'sender_id' => $from->userId,
                    'receiver_id' => $receiverId,
                    'message' => $message,
                    'created_at' => date('Y-m-d H:i:s')
                ];
                
                $deliveryJson = json_encode($deliveryData);
                
                // Send to receiver if connected
                $sent = false;
                if (isset($this->userConnections[$receiverId])) {
                    $receiverResourceId = $this->userConnections[$receiverId];
                    if (isset($this->connectionMap[$receiverResourceId])) {
                        $receiverConn = $this->connectionMap[$receiverResourceId];
                        $receiverConn->send($deliveryJson);
                        $sent = true;
                        echo "Message sent to user {$receiverId}\n";
                    }
                }
                
                if (!$sent) {
                    echo "User {$receiverId} is offline\n";
                }
                
                // Send confirmation to sender
                $from->send($deliveryJson);
                
            } catch (\Exception $e) {
                echo "Database error: " . $e->getMessage() . "\n";
                $from->send(json_encode([
                    'type' => 'error',
                    'message' => 'Failed to save message'
                ]));
            }
        } else {
            // Database not available, send echo
            $from->send(json_encode([
                'type' => 'message',
                'chat_id' => $chatId,
                'sender_id' => $from->userId,
                'receiver_id' => $receiverId,
                'message' => $message,
                'created_at' => date('Y-m-d H:i:s'),
                'note' => 'Database not available - message not stored'
            ]));
        }
    }
    
    private function handleTyping($from, $data)
    {
        if (!isset($from->userId) || !isset($data['receiver_id'])) {
            return;
        }
        
        $receiverId = (int)$data['receiver_id'];
        $chatId = isset($data['chat_id']) ? (int)$data['chat_id'] : 0;
        $isTyping = isset($data['is_typing']) ? (bool)$data['is_typing'] : true;
        
        $typingData = [
            'type' => 'typing',
            'chat_id' => $chatId,
            'sender_id' => $from->userId,
            'is_typing' => $isTyping
        ];
        
        if (isset($this->userConnections[$receiverId])) {
            $receiverResourceId = $this->userConnections[$receiverId];
            if (isset($this->connectionMap[$receiverResourceId])) {
                $receiverConn = $this->connectionMap[$receiverResourceId];
                $receiverConn->send(json_encode($typingData));
            }
        }
    }
    
    private function markAsRead($from, $data)
    {
        if (!isset($from->userId) || !isset($data['chat_id'])) {
            return;
        }
        
        $chatId = (int)$data['chat_id'];
        
        if ($this->db) {
            try {
                $this->db->update('messages', [
                    'is_read' => 1,
                    'read_at' => date('Y-m-d H:i:s')
                ], 'chat_id = ? AND receiver_id = ? AND is_read = 0', [
                    $chatId,
                    $from->userId
                ]);
                
                echo "Messages marked as read for user {$from->userId} in chat {$chatId}\n";
                
                // Notify sender
                if (isset($data['sender_id'])) {
                    $senderId = (int)$data['sender_id'];
                    if (isset($this->userConnections[$senderId])) {
                        $senderResourceId = $this->userConnections[$senderId];
                        if (isset($this->connectionMap[$senderResourceId])) {
                            $senderConn = $this->connectionMap[$senderResourceId];
                            $senderConn->send(json_encode([
                                'type' => 'read',
                                'chat_id' => $chatId,
                                'reader_id' => $from->userId
                            ]));
                        }
                    }
                }
            } catch (\Exception $e) {
                echo "Error marking messages as read: " . $e->getMessage() . "\n";
            }
        }
    }
    
    private function sendUnreadCount($userId)
    {
        if (!$this->db) return;
        
        try {
            $result = $this->db->fetch(
                "SELECT COUNT(*) as count FROM messages 
                 WHERE receiver_id = ? AND is_read = 0",
                [$userId]
            );
            
            $count = (int)($result['count'] ?? 0);
            
            if (isset($this->userConnections[$userId])) {
                $resourceId = $this->userConnections[$userId];
                if (isset($this->connectionMap[$resourceId])) {
                    $conn = $this->connectionMap[$resourceId];
                    $conn->send(json_encode([
                        'type' => 'unread_count',
                        'count' => $count
                    ]));
                }
            }
        } catch (\Exception $e) {
            echo "Error fetching unread count: " . $e->getMessage() . "\n";
        }
    }
    
    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        
        if (isset($conn->userId)) {
            $userId = $conn->userId;
            unset($this->userConnections[$userId]);
            echo "User {$userId} disconnected\n";
        }
        
        unset($this->connectionMap[$conn->resourceId]);
        echo "Connection {$conn->resourceId} closed\n";
    }
    
    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "Error: " . $e->getMessage() . "\n";
        $conn->close();
    }
}