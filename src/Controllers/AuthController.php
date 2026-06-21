<?php
// src/Controllers/AuthController.php

namespace Controllers;

use Core\Controller;
use Core\Auth;
use Core\Database;

class AuthController extends Controller
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    public function login()
    {
        // If already logged in, redirect to home
        if (Auth::isLoggedIn()) {
            return $this->redirect('/');
        }
        
        $this->view('auth/login');
    }
    
    public function authenticate()
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if (empty($email) || empty($password)) {
            return $this->redirect('/login', ['error' => 'Please fill in all fields']);
        }
        
        $user = $this->db->fetch(
            "SELECT * FROM users WHERE email = ?",
            [$email]
        );
        
        if (!$user || !Auth::verifyPassword($password, $user['password'])) {
            return $this->redirect('/login', ['error' => 'Invalid email or password']);
        }
        
        Auth::login($user);
        
        // Redirect based on role
        $redirect = '/';
        if ($user['role'] === 'admin') {
            $redirect = '/admin';
        } elseif ($user['role'] === 'seller') {
            $redirect = '/dashboard/seller';
        } else {
            $redirect = '/dashboard/buyer';
        }
        
        return $this->redirect($redirect, ['success' => 'Welcome back, ' . $user['full_name'] . '!']);
    }
    
    public function register()
    {
        if (Auth::isLoggedIn()) {
            return $this->redirect('/');
        }
        
        $this->view('auth/register');
    }
    
public function store()
{
    $validation = $this->validate($_POST, [
        'full_name' => 'required|min:2|max:100',
        'username' => 'required|min:3|max:50',
        'email' => 'required|email',
        'password' => 'required|min:6',
        'confirm_password' => 'required',
        'role' => 'required|in:buyer,seller'
    ]);
    
    if ($_POST['password'] !== $_POST['confirm_password']) {
        return $this->redirect('/register', ['error' => 'Passwords do not match']);
    }
    
    // Check if email exists
    $existing = $this->db->fetch(
        "SELECT id FROM users WHERE email = ?",
        [$_POST['email']]
    );
    
    if ($existing) {
        return $this->redirect('/register', ['error' => 'Email already registered']);
    }
    
    // Check if username exists
    $existing = $this->db->fetch(
        "SELECT id FROM users WHERE username = ?",
        [$_POST['username']]
    );
    
    if ($existing) {
        return $this->redirect('/register', ['error' => 'Username already taken']);
    }
    
    $role = $_POST['role'] ?? 'buyer';
    
    $userId = $this->db->insert('users', [
        'email' => $_POST['email'],
        'password' => Auth::hashPassword($_POST['password']),
        'full_name' => $_POST['full_name'],
        'username' => $_POST['username'],
        'role' => $role,
        'is_verified' => 0
    ]);
    
    // Auto-login after registration
    $user = $this->db->fetch("SELECT * FROM users WHERE id = ?", [$userId]);
    Auth::login($user);
    
    $redirect = $role === 'seller' ? '/dashboard/seller' : '/dashboard/buyer';
    return $this->redirect($redirect, ['success' => 'Registration successful! Welcome to MarketPlace!']);
}
    
    public function logout()
    {
        Auth::logout();
        return $this->redirect('/login', ['success' => 'Logged out successfully']);
    }
}