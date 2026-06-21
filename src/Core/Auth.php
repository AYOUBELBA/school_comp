<?php
// src/Core/Auth.php

namespace Core;

class Auth {
    public static function login($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_name'] = $user['full_name'];
    }
    
    public static function logout() {
        session_destroy();
        $_SESSION = [];
    }
    
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    public static function getUserId() {
        return $_SESSION['user_id'] ?? null;
    }
    
    public static function getUserRole() {
        return $_SESSION['user_role'] ?? null;
    }
    
    public static function getUser() {
        if (!self::isLoggedIn()) return null;
        
        $db = Database::getInstance();
        return $db->fetch(
            "SELECT * FROM users WHERE id = ?",
            [self::getUserId()]
        );
    }
    
    public static function isAdmin() {
        return self::isLoggedIn() && self::getUserRole() === 'admin';
    }
    
    public static function isSeller() {
        return self::isLoggedIn() && (self::getUserRole() === 'seller' || self::getUserRole() === 'admin');
    }
    
    public static function requireLogin() {
        if (!self::isLoggedIn()) {
            header('Location: /login');
            exit;
        }
    }
    
    public static function requireRole($role) {
        self::requireLogin();
        if (self::getUserRole() !== $role && self::getUserRole() !== 'admin') {
            header('Location: /');
            exit;
        }
    }
    
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT);
    }
    
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
}