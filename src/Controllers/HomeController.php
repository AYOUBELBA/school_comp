<?php
// src/Controllers/HomeController.php

namespace Controllers;

use Core\Controller;
use Core\Database;

class HomeController extends Controller
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    public function index()
    {
        // Initialize with empty arrays to avoid undefined variable errors
        $featuredProducts = [];
        $latestProducts = [];
        $categories = [];
        
        // Get featured products with error handling
        try {
            $featuredProducts = $this->db->fetchAll(
                "SELECT p.*, 
                 (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1) as primary_image,
                 u.full_name as seller_name
                 FROM products p
                 JOIN users u ON p.seller_id = u.id
                 WHERE p.status = 'active' AND p.is_featured = 1
                 ORDER BY p.created_at DESC
                 LIMIT 8"
            );
        } catch (\Exception $e) {
            error_log("Error fetching featured products: " . $e->getMessage());
        }
        
        // Get latest products
        try {
            $latestProducts = $this->db->fetchAll(
                "SELECT p.*, 
                 (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1) as primary_image,
                 u.full_name as seller_name
                 FROM products p
                 JOIN users u ON p.seller_id = u.id
                 WHERE p.status = 'active'
                 ORDER BY p.created_at DESC
                 LIMIT 12"
            );
        } catch (\Exception $e) {
            error_log("Error fetching latest products: " . $e->getMessage());
        }
        
        // Get categories
        try {
            $categories = $this->db->fetchAll("SELECT * FROM categories ORDER BY name");
        } catch (\Exception $e) {
            error_log("Error fetching categories: " . $e->getMessage());
        }
        
        $this->view('home/index', [
            'featuredProducts' => $featuredProducts,
            'latestProducts' => $latestProducts,
            'categories' => $categories
        ]);
    }
}