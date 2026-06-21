<?php
// src/Controllers/AdminController.php

namespace Controllers;

use Core\Controller;
use Core\Auth;
use Core\Database;

class AdminController extends Controller
{
    private $db;
    
    public function __construct()
    {
        Auth::requireLogin();
        
        // Check if user is admin
        $user = Auth::getUser();
        if (!$user || $user['role'] !== 'admin') {
            header('Location: /');
            exit;
        }
        
        $this->db = Database::getInstance();
    }
    
    public function products()
    {
        // Get all products with seller info
        $products = $this->db->fetchAll(
            "SELECT p.*, 
                    u.full_name as seller_name,
                    u.email as seller_email,
                    (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1) as primary_image
             FROM products p
             JOIN users u ON p.seller_id = u.id
             ORDER BY p.created_at DESC"
        );
        
        $this->view('admin/products', [
            'products' => $products,
            'currentPage' => 'admin'
        ]);
    }
    
    public function users()
    {
        $users = $this->db->fetchAll(
            "SELECT u.*, 
                    (SELECT COUNT(*) FROM products WHERE seller_id = u.id) as total_products,
                    (SELECT COUNT(*) FROM orders WHERE seller_id = u.id OR buyer_id = u.id) as total_orders
             FROM users u
             ORDER BY u.created_at DESC"
        );
        
        $this->view('admin/users', [
            'users' => $users,
            'currentPage' => 'admin'
        ]);
    }
    
    public function orders()
    {
        $orders = $this->db->fetchAll(
            "SELECT o.*, 
                    p.title as product_title,
                    buyer.full_name as buyer_name,
                    seller.full_name as seller_name,
                    (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1) as product_image
             FROM orders o
             JOIN products p ON o.product_id = p.id
             JOIN users buyer ON o.buyer_id = buyer.id
             JOIN users seller ON o.seller_id = seller.id
             ORDER BY o.created_at DESC"
        );
        
        $this->view('admin/orders', [
            'orders' => $orders,
            'currentPage' => 'admin'
        ]);
    }
    
    public function deleteProduct($id)
    {
        $product = $this->db->fetch(
            "SELECT id, title FROM products WHERE id = ?",
            [$id]
        );
        
        if (!$product) {
            $_SESSION['flash']['error'] = 'Product not found';
            header('Location: /admin/products');
            exit;
        }
        
        // Delete product
        $this->db->delete('products', 'id = ?', [$id]);
        
        $_SESSION['flash']['success'] = 'Product "' . $product['title'] . '" deleted successfully!';
        header('Location: /admin/products');
        exit;
    }
    
    public function deleteUser($id)
    {
        // Prevent deleting self
        if ($id == Auth::getUserId()) {
            $_SESSION['flash']['error'] = 'You cannot delete your own account';
            header('Location: /admin/users');
            exit;
        }
        
        $user = $this->db->fetch(
            "SELECT id, full_name FROM users WHERE id = ?",
            [$id]
        );
        
        if (!$user) {
            $_SESSION['flash']['error'] = 'User not found';
            header('Location: /admin/users');
            exit;
        }
        
        // Delete user
        $this->db->delete('users', 'id = ?', [$id]);
        
        $_SESSION['flash']['success'] = 'User "' . $user['full_name'] . '" deleted successfully!';
        header('Location: /admin/users');
        exit;
    }
    public function updateUser($id)
{
    $user = $this->db->fetch(
        "SELECT * FROM users WHERE id = ?",
        [$id]
    );
    
    if (!$user) {
        $_SESSION['flash']['error'] = 'User not found';
        header('Location: /admin/users');
        exit;
    }
    
    $action = $_POST['action'] ?? 'update';
    
    // Handle Ban Action
    if ($action === 'ban') {
        $banReason = trim($_POST['ban_reason'] ?? 'No reason provided');
        
        $this->db->update('users', [
            'is_banned' => 1,
            'banned_at' => date('Y-m-d H:i:s'),
            'ban_reason' => $banReason
        ], 'id = ?', [$id]);
        
        $_SESSION['flash']['success'] = 'User "' . $user['full_name'] . '" has been banned.';
        header('Location: /admin/users');
        exit;
    }
    
    // Handle Unban Action
    if ($action === 'unban') {
        $this->db->update('users', [
            'is_banned' => 0,
            'banned_at' => null,
            'ban_reason' => null
        ], 'id = ?', [$id]);
        
        $_SESSION['flash']['success'] = 'User "' . $user['full_name'] . '" has been unbanned.';
        header('Location: /admin/users');
        exit;
    }
    
    // Handle Update Action
    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $role = $_POST['role'] ?? 'buyer';
    $password = $_POST['password'] ?? '';
    $passwordConfirm = $_POST['password_confirm'] ?? '';
    
    // Validate
    if (empty($fullName) || empty($email) || empty($username)) {
        $_SESSION['flash']['error'] = 'All fields are required';
        header('Location: /admin/user/edit/' . $id);
        exit;
    }
    
    // Check if password was provided and matches
    if (!empty($password) && $password !== $passwordConfirm) {
        $_SESSION['flash']['error'] = 'Passwords do not match';
        header('Location: /admin/user/edit/' . $id);
        exit;
    }
    
    // Check if email exists (but not for this user)
    $existing = $this->db->fetch(
        "SELECT id FROM users WHERE email = ? AND id != ?",
        [$email, $id]
    );
    if ($existing) {
        $_SESSION['flash']['error'] = 'Email already in use';
        header('Location: /admin/user/edit/' . $id);
        exit;
    }
    
    // Check if username exists (but not for this user)
    $existing = $this->db->fetch(
        "SELECT id FROM users WHERE username = ? AND id != ?",
        [$username, $id]
    );
    if ($existing) {
        $_SESSION['flash']['error'] = 'Username already in use';
        header('Location: /admin/user/edit/' . $id);
        exit;
    }
    
    // Update user
    $updateData = [
        'full_name' => $fullName,
        'email' => $email,
        'username' => $username,
        'role' => $role
    ];
    
    // If password is set, update it
    if (!empty($password)) {
        $updateData['password'] = password_hash($password, PASSWORD_DEFAULT);
    }
    
    $this->db->update('users', $updateData, 'id = ?', [$id]);
    
    $_SESSION['flash']['success'] = 'User "' . $fullName . '" updated successfully!';
    header('Location: /admin/users');
    exit;
}
public function banUser($id)
{
    $user = $this->db->fetch(
        "SELECT id, full_name FROM users WHERE id = ?",
        [$id]
    );
    
    if (!$user) {
        $_SESSION['flash']['error'] = 'User not found';
        header('Location: /admin/users');
        exit;
    }
    
    // Prevent banning self
    if ($id == Auth::getUserId()) {
        $_SESSION['flash']['error'] = 'You cannot ban yourself';
        header('Location: /admin/users');
        exit;
    }
    
    $this->db->update('users', [
        'is_banned' => 1,
        'banned_at' => date('Y-m-d H:i:s'),
        'ban_reason' => 'Banned by admin'
    ], 'id = ?', [$id]);
    
    $_SESSION['flash']['success'] = 'User "' . $user['full_name'] . '" has been banned.';
    header('Location: /admin/users');
    exit;
}

public function unbanUser($id)
{
    $user = $this->db->fetch(
        "SELECT id, full_name FROM users WHERE id = ?",
        [$id]
    );
    
    if (!$user) {
        $_SESSION['flash']['error'] = 'User not found';
        header('Location: /admin/users');
        exit;
    }
    
    $this->db->update('users', [
        'is_banned' => 0,
        'banned_at' => null,
        'ban_reason' => null
    ], 'id = ?', [$id]);
    
    $_SESSION['flash']['success'] = 'User "' . $user['full_name'] . '" has been unbanned.';
    header('Location: /admin/users');
    exit;
}
    public function updateUserRole()
    {
        $userId = (int)($_POST['user_id'] ?? 0);
        $role = $_POST['role'] ?? '';
        
        if (!$userId || !in_array($role, ['buyer', 'seller', 'admin'])) {
            $_SESSION['flash']['error'] = 'Invalid request';
            header('Location: /admin/users');
            exit;
        }
        
        // Prevent admin from changing their own role
        if ($userId == Auth::getUserId()) {
            $_SESSION['flash']['error'] = 'You cannot change your own role';
            header('Location: /admin/users');
            exit;
        }
        
        $this->db->update('users', [
            'role' => $role
        ], 'id = ?', [$userId]);
        
        $_SESSION['flash']['success'] = 'User role updated successfully!';
        header('Location: /admin/users');
        exit;
    }
}