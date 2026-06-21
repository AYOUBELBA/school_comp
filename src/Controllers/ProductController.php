<?php
// src/Controllers/ProductController.php

namespace Controllers;

use Core\Controller;
use Core\Auth;
use Core\Database;
use Services\FileUploadService;
use Services\SearchService;

class ProductController extends Controller
{
    private $db;
    private $fileUpload;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
        
        // Only initialize FileUploadService if it exists
        if (class_exists('Services\FileUploadService')) {
            $this->fileUpload = new FileUploadService();
        } else {
            $this->fileUpload = null;
        }
    }
    
    public function index()
    {
        $page = $_GET['page'] ?? 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $searchService = new SearchService();
        $result = $searchService->search($_GET, $limit, $offset);
        
        $this->view('products/listing', [
            'products' => $result['products'],
            'total' => $result['total'],
            'page' => $page,
            'limit' => $limit,
            'filters' => $_GET
        ]);
    }
    
    public function show($id)
    {
        try {
            $product = $this->db->fetch(
                "SELECT p.*, u.full_name as seller_name, u.rating as seller_rating,
                 (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1) as primary_image
                 FROM products p
                 JOIN users u ON p.seller_id = u.id
                 WHERE p.id = ? AND p.status = 'active'",
                [$id]
            );
            
            if (!$product) {
                return $this->redirect('/products', ['error' => 'Product not found']);
            }
            
            // Increment view count
            $this->db->query(
                "UPDATE products SET views_count = views_count + 1 WHERE id = ?",
                [$id]
            );
            
            // Get additional images
            $images = $this->db->fetchAll(
                "SELECT * FROM product_images WHERE product_id = ? ORDER BY sort_order",
                [$id]
            );
            
            // Get similar products
            $similar = $this->db->fetchAll(
                "SELECT p.*, 
                 (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1) as primary_image
                 FROM products p
                 WHERE p.category_id = ? AND p.id != ? AND p.status = 'active'
                 LIMIT 6",
                [$product['category_id'], $id]
            );
            
            // Check if favorited
            $isFavorited = false;
            if (Auth::isLoggedIn()) {
                $favorite = $this->db->fetch(
                    "SELECT id FROM favorites WHERE user_id = ? AND product_id = ?",
                    [Auth::getUserId(), $id]
                );
                $isFavorited = (bool)$favorite;
            }
            
            $this->view('products/details', [
                'product' => $product,
                'images' => $images,
                'similar' => $similar,
                'isFavorited' => $isFavorited
            ]);
            
        } catch (\Exception $e) {
            return $this->redirect('/products', ['error' => 'Product not found']);
        }
    }
    
    public function create()
    {
        Auth::requireRole('seller');
        
        $categories = $this->db->fetchAll("SELECT * FROM categories ORDER BY name");
        $this->view('products/create', ['categories' => $categories]);
    }
    
    public function store()
    {
        Auth::requireRole('seller');
        
        $validation = $this->validate($_POST, [
            'title' => 'required|min:3|max:255',
            'description' => 'required|min:10',
            'price' => 'required|numeric|min:0.01',
            'category_id' => 'required',
            'condition' => 'required|in:new,used,refurbished,damaged'
        ]);
        
        if (!$validation['valid']) {
            return $this->redirect('/product/create', [
                'errors' => $validation['errors'],
                'old' => $_POST
            ]);
        }
        
        try {
            $productId = $this->db->insert('products', [
                'seller_id' => Auth::getUserId(),
                'category_id' => $_POST['category_id'],
                'title' => $_POST['title'],
                'description' => $_POST['description'],
                'price' => $_POST['price'],
                'is_negotiable' => isset($_POST['is_negotiable']) ? 1 : 0,
                '`condition`' => $_POST['condition'],
                'location' => $_POST['location'] ?? null,
                'status' => 'active'
            ]);
            
            // Handle image uploads
            if (!empty($_FILES['images']) && $this->fileUpload) {
                $uploadedImages = $this->fileUpload->uploadMultiple($_FILES['images'], 'products');
                
                foreach ($uploadedImages as $index => $imagePath) {
                    $this->db->insert('product_images', [
                        'product_id' => $productId,
                        'image_path' => $imagePath,
                        'is_primary' => $index === 0 ? 1 : 0,
                        'sort_order' => $index
                    ]);
                }
            }
            
            return $this->redirect('/product/' . $productId, ['success' => 'Product created successfully!']);
            
        } catch (\Exception $e) {
            return $this->redirect('/product/create', ['error' => 'Failed to create product: ' . $e->getMessage()]);
        }
    }
    
    public function edit($id)
    {
        Auth::requireRole('seller');
        
        $product = $this->db->fetch(
            "SELECT * FROM products WHERE id = ? AND seller_id = ?",
            [$id, Auth::getUserId()]
        );
        
        if (!$product) {
            return $this->redirect('/dashboard/seller', ['error' => 'Product not found']);
        }
        
        $categories = $this->db->fetchAll("SELECT * FROM categories ORDER BY name");
        $images = $this->db->fetchAll(
            "SELECT * FROM product_images WHERE product_id = ? ORDER BY sort_order",
            [$id]
        );
        
        $this->view('products/edit', [
            'product' => $product,
            'categories' => $categories,
            'images' => $images
        ]);
    }
    
    public function update($id)
    {
        Auth::requireRole('seller');
        
        $product = $this->db->fetch(
            "SELECT id FROM products WHERE id = ? AND seller_id = ?",
            [$id, Auth::getUserId()]
        );
        
        if (!$product) {
            return $this->json(['error' => 'Product not found'], 404);
        }
        
        $validation = $this->validate($_POST, [
            'title' => 'required|min:3|max:255',
            'description' => 'required|min:10',
            'price' => 'required|numeric|min:0.01',
            'category_id' => 'required',
            'condition' => 'required|in:new,used,refurbished,damaged'
        ]);
        
        if (!$validation['valid']) {
            return $this->json(['errors' => $validation['errors']], 422);
        }
        
        $this->db->update('products', [
            'category_id' => $_POST['category_id'],
            'title' => $_POST['title'],
            'description' => $_POST['description'],
            'price' => $_POST['price'],
            'is_negotiable' => isset($_POST['is_negotiable']) ? 1 : 0,
            '`condition`' => $_POST['condition'],
            'location' => $_POST['location'] ?? null,
            'status' => $_POST['status'] ?? 'active'
        ], 'id = ?', [$id]);
        
        return $this->redirect('/product/' . $id, ['success' => 'Product updated successfully!']);
    }
    
    public function delete($id)
    {
        Auth::requireRole('seller');
        
        $product = $this->db->fetch(
            "SELECT id FROM products WHERE id = ? AND seller_id = ?",
            [$id, Auth::getUserId()]
        );
        
        if (!$product) {
            return $this->json(['error' => 'Product not found'], 404);
        }
        
        $this->db->delete('products', 'id = ?', [$id]);
        
        if ($this->isAjax()) {
            return $this->json(['success' => true]);
        }
        
        return $this->redirect('/dashboard/seller', ['success' => 'Product deleted successfully!']);
    }
    
    public function toggleFavorite($id)
    {
        Auth::requireLogin();
        
        $userId = Auth::getUserId();
        $product = $this->db->fetch(
            "SELECT id FROM products WHERE id = ? AND status = 'active'",
            [$id]
        );
        
        if (!$product) {
            return $this->json(['error' => 'Product not found'], 404);
        }
        
        $existing = $this->db->fetch(
            "SELECT id FROM favorites WHERE user_id = ? AND product_id = ?",
            [$userId, $id]
        );
        
        if ($existing) {
            $this->db->delete('favorites', 'id = ?', [$existing['id']]);
            $favorited = false;
        } else {
            $this->db->insert('favorites', [
                'user_id' => $userId,
                'product_id' => $id
            ]);
            $favorited = true;
        }
        
        return $this->json(['favorited' => $favorited]);
    }
}