<?php
// src/Controllers/FavoritesController.php

namespace Controllers;

use Core\Controller;
use Core\Auth;
use Core\Database;

class FavoritesController extends Controller
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
        
        $favorites = $this->db->fetchAll(
            "SELECT p.*, u.full_name as seller_name,
             (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1) as primary_image
             FROM favorites f
             JOIN products p ON f.product_id = p.id
             JOIN users u ON p.seller_id = u.id
             WHERE f.user_id = ? AND p.status = 'active'
             ORDER BY f.created_at DESC",
            [$userId]
        );
        
        $this->view('favorites/index', [
            'favorites' => $favorites,
            'currentPage' => 'favorites'
        ]);
    }
    
    public function remove($productId)
    {
        Auth::requireLogin();
        $userId = Auth::getUserId();
        
        $this->db->delete(
            'favorites',
            'user_id = ? AND product_id = ?',
            [$userId, $productId]
        );
        
        return $this->json(['success' => true]);
    }
}