<?php
// src/Controllers/OrderController.php

namespace Controllers;

use Core\Controller;
use Core\Auth;
use Core\Database;

class OrderController extends Controller
{
    private $db;
    
    public function __construct()
    {
        Auth::requireLogin();
        $this->db = Database::getInstance();
    }
    
    public function create()
    {
        $validation = $this->validate($_POST, [
            'product_id' => 'required|numeric',
            'shipping_address' => 'required|min:10',
            'payment_method' => 'required'
        ]);
        
        if (!$validation['valid']) {
            return $this->json(['errors' => $validation['errors']], 422);
        }
        
        $productId = (int)$_POST['product_id'];
        $buyerId = Auth::getUserId();
        
        // Get product details
        $product = $this->db->fetch(
            "SELECT p.*, u.full_name as seller_name 
             FROM products p 
             JOIN users u ON p.seller_id = u.id 
             WHERE p.id = ? AND p.status = 'active'",
            [$productId]
        );
        
        if (!$product) {
            return $this->json(['error' => 'Product not available'], 404);
        }
        
        // Prevent buying own product
        if ($product['seller_id'] == $buyerId) {
            return $this->json(['error' => 'You cannot buy your own product'], 422);
        }
        
        // Create order
        $orderId = $this->db->insert('orders', [
            'buyer_id' => $buyerId,
            'product_id' => $productId,
            'seller_id' => $product['seller_id'],
            'amount' => $product['price'],
            'status' => 'pending',
            'payment_method' => $_POST['payment_method'],
            'shipping_address' => $_POST['shipping_address'],
            'transaction_id' => 'TXN-' . uniqid()
        ]);
        
        // Update product status
        $this->db->update('products', [
            'status' => 'sold'
        ], 'id = ?', [$productId]);
        
        // Create notification for seller
        $this->createNotification(
            $product['seller_id'],
            'new_order',
            'New Order Received!',
            "Your product '{$product['title']}' has been purchased for $" . number_format($product['price'], 2)
        );
        
        // Create notification for buyer
        $this->createNotification(
            $buyerId,
            'order_confirmed',
            'Order Confirmed!',
            "Your purchase of '{$product['title']}' has been confirmed."
        );
        
        return $this->json([
            'success' => true,
            'order_id' => $orderId,
            'message' => 'Order placed successfully!'
        ]);
    }
    
    private function createNotification($userId, $type, $title, $message)
    {
        $this->db->insert('notifications', [
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'is_read' => 0
        ]);
    }
}