<?php
// src/Controllers/OrdersController.php

namespace Controllers;

use Core\Controller;
use Core\Auth;
use Core\Database;

class OrdersController extends Controller
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
        $user = Auth::getUser();
        
        if ($user['role'] === 'seller' || $user['role'] === 'admin') {
            $orders = $this->db->fetchAll(
                "SELECT o.*, p.title as product_title, u.full_name as buyer_name,
                 (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1) as product_image
                 FROM orders o
                 JOIN products p ON o.product_id = p.id
                 JOIN users u ON o.buyer_id = u.id
                 WHERE o.seller_id = ?
                 ORDER BY o.created_at DESC",
                [$userId]
            );
        } else {
            $orders = $this->db->fetchAll(
                "SELECT o.*, p.title as product_title, u.full_name as seller_name,
                 (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1) as product_image
                 FROM orders o
                 JOIN products p ON o.product_id = p.id
                 JOIN users u ON o.seller_id = u.id
                 WHERE o.buyer_id = ?
                 ORDER BY o.created_at DESC",
                [$userId]
            );
        }
        
        $this->view('orders/index', [
            'orders' => $orders,
            'currentPage' => 'orders'
        ]);
    }
}