<?php
// src/Controllers/DashboardController.php

namespace Controllers;

use Core\Controller;
use Core\Auth;
use Core\Database;

class DashboardController extends Controller
{
    private $db;
    
    public function __construct()
    {
        Auth::requireLogin();
        $this->db = Database::getInstance();
    }
    
public function index()
{
    Auth::requireLogin();
    $user = Auth::getUser();
    
    if ($user['role'] === 'admin') {
        return $this->adminDashboard();
    } elseif ($user['role'] === 'seller') {
        return $this->sellerDashboard();
    } else {
        return $this->buyerDashboard();
    }
}
    
    public function sellerDashboard()
    {
        $userId = Auth::getUserId();
        
        // Get seller stats
        $stats = $this->db->fetch(
            "SELECT 
                (SELECT COUNT(*) FROM products WHERE seller_id = ? AND status = 'active') as active_products,
                (SELECT COUNT(*) FROM products WHERE seller_id = ? AND status = 'sold') as sold_products,
                (SELECT COUNT(*) FROM orders WHERE seller_id = ? AND status = 'pending') as pending_orders,
                (SELECT COUNT(*) FROM orders WHERE seller_id = ? AND status = 'completed') as completed_orders,
                (SELECT COUNT(*) FROM offers WHERE seller_id = ? AND status = 'pending') as pending_offers,
                (SELECT COALESCE(SUM(amount), 0) FROM orders WHERE seller_id = ? AND status = 'completed') as total_earnings
            ",
            [$userId, $userId, $userId, $userId, $userId, $userId]
        );
        
        // Get recent orders
        $recentOrders = $this->db->fetchAll(
            "SELECT o.*, p.title as product_title, u.full_name as buyer_name
             FROM orders o
             JOIN products p ON o.product_id = p.id
             JOIN users u ON o.buyer_id = u.id
             WHERE o.seller_id = ?
             ORDER BY o.created_at DESC
             LIMIT 10",
            [$userId]
        );
        
        // Get recent offers
        $recentOffers = $this->db->fetchAll(
            "SELECT o.*, p.title as product_title, u.full_name as buyer_name
             FROM offers o
             JOIN products p ON o.product_id = p.id
             JOIN users u ON o.buyer_id = u.id
             WHERE o.seller_id = ? AND o.status = 'pending'
             ORDER BY o.created_at DESC
             LIMIT 10",
            [$userId]
        );
        
        // Get recent products
        $recentProducts = $this->db->fetchAll(
            "SELECT p.*, 
             (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1) as primary_image
             FROM products p
             WHERE p.seller_id = ?
             ORDER BY p.created_at DESC
             LIMIT 10",
            [$userId]
        );
        
        $this->view('dashboard/seller', [
            'stats' => $stats,
            'recentOrders' => $recentOrders,
            'recentOffers' => $recentOffers,
            'recentProducts' => $recentProducts
        ]);
    }
    
    public function buyerDashboard()
    {
        $userId = Auth::getUserId();
        
        // Get buyer stats
        $stats = $this->db->fetch(
            "SELECT 
                (SELECT COUNT(*) FROM orders WHERE buyer_id = ? AND status = 'pending') as pending_orders,
                (SELECT COUNT(*) FROM orders WHERE buyer_id = ? AND status = 'completed') as completed_orders,
                (SELECT COUNT(*) FROM offers WHERE buyer_id = ? AND status = 'pending') as pending_offers,
                (SELECT COUNT(*) FROM favorites WHERE user_id = ?) as favorites_count
            ",
            [$userId, $userId, $userId, $userId]
        );
        
        // Get recent orders
        $recentOrders = $this->db->fetchAll(
            "SELECT o.*, p.title as product_title, u.full_name as seller_name,
             (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1) as product_image
             FROM orders o
             JOIN products p ON o.product_id = p.id
             JOIN users u ON o.seller_id = u.id
             WHERE o.buyer_id = ?
             ORDER BY o.created_at DESC
             LIMIT 10",
            [$userId]
        );
        
        // Get favorites
        $favorites = $this->db->fetchAll(
            "SELECT p.*, u.full_name as seller_name,
             (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1) as primary_image
             FROM favorites f
             JOIN products p ON f.product_id = p.id
             JOIN users u ON p.seller_id = u.id
             WHERE f.user_id = ? AND p.status = 'active'
             ORDER BY f.created_at DESC
             LIMIT 10",
            [$userId]
        );
        
        $this->view('dashboard/buyer', [
            'stats' => $stats,
            'recentOrders' => $recentOrders,
            'favorites' => $favorites
        ]);
    }
    
    public function adminDashboard()
    {
        // Get admin stats
        $stats = $this->db->fetch(
            "SELECT 
                (SELECT COUNT(*) FROM users) as total_users,
                (SELECT COUNT(*) FROM users WHERE role = 'seller') as total_sellers,
                (SELECT COUNT(*) FROM products) as total_products,
                (SELECT COUNT(*) FROM products WHERE status = 'active') as active_products,
                (SELECT COUNT(*) FROM orders) as total_orders,
                (SELECT COUNT(*) FROM orders WHERE status = 'pending') as pending_orders,
                (SELECT COALESCE(SUM(amount), 0) FROM orders WHERE status = 'completed') as total_revenue
            "
        );
        
        // Get recent users
        $recentUsers = $this->db->fetchAll(
            "SELECT * FROM users ORDER BY created_at DESC LIMIT 10"
        );
        
        // Get recent products
        $recentProducts = $this->db->fetchAll(
            "SELECT p.*, u.full_name as seller_name,
             (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1) as primary_image
             FROM products p
             JOIN users u ON p.seller_id = u.id
             ORDER BY p.created_at DESC
             LIMIT 10"
        );
        
        $this->view('dashboard/admin', [
            'stats' => $stats,
            'recentUsers' => $recentUsers,
            'recentProducts' => $recentProducts
        ]);
    }
}