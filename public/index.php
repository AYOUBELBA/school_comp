<?php
// public/index.php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once __DIR__ . '/../vendor/autoload.php';

$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Remove query string
if (strpos($requestUri, '?') !== false) {
    $requestUri = substr($requestUri, 0, strpos($requestUri, '?'));
}

// Simple routing
$routes = [
    '/' => ['HomeController', 'index'],
    '/login' => ['AuthController', 'login'],
    '/authenticate' => ['AuthController', 'authenticate'],
    '/register' => ['AuthController', 'register'],
    '/register/store' => ['AuthController', 'store'],
    '/logout' => ['AuthController', 'logout'],
    '/products' => ['ProductController', 'index'],
    '/product/create' => ['ProductController', 'create'],
    '/product/store' => ['ProductController', 'store'],
    '/product/{id}' => ['ProductController', 'show'],
    '/product/edit/{id}' => ['ProductController', 'edit'],
    '/product/update/{id}' => ['ProductController', 'update'],
    '/product/delete/{id}' => ['ProductController', 'delete'],
    '/product/favorite/{id}' => ['ProductController', 'toggleFavorite'],
    '/dashboard' => ['DashboardController', 'index'],
    '/dashboard/seller' => ['DashboardController', 'sellerDashboard'],
    '/dashboard/buyer' => ['DashboardController', 'buyerDashboard'],
    '/dashboard/admin' => ['DashboardController', 'adminDashboard'],
    '/offers' => ['OfferController', 'manage'],
    '/offer/create' => ['OfferController', 'create'],
    '/offer/accept/{id}' => ['OfferController', 'accept'],
    '/offer/reject/{id}' => ['OfferController', 'reject'],
    '/offer/counter/{id}' => ['OfferController', 'counter'],
    '/chat' => ['ChatController', 'index'],
    '/profile' => ['ProfileController', 'index'],
    '/product/create' => ['ProductController', 'create'],
    '/product/store' => ['ProductController', 'store'],
    '/product/{id}' => ['ProductController', 'show'],
    '/product/edit/{id}' => ['ProductController', 'edit'],
    '/product/update/{id}' => ['ProductController', 'update'],
    '/product/delete/{id}' => ['ProductController', 'delete'],
    '/product/favorite/{id}' => ['ProductController', 'toggleFavorite'],
    '/offer/create' => ['OfferController', 'create'],
    '/offer/accept/{id}' => ['OfferController', 'accept'],
    '/offer/reject/{id}' => ['OfferController', 'reject'],
    '/offer/counter/{id}' => ['OfferController', 'counter'],
    '/offer/accept-counter/{id}' => ['OfferController', 'acceptCounter'],
    '/offer/reject-counter/{id}' => ['OfferController', 'rejectCounter']
];

$matched = false;
foreach ($routes as $route => $handler) {
    $pattern = preg_replace('/\{[a-z]+\}/', '([0-9]+)', $route);
    if (preg_match("#^$pattern$#", $requestUri, $matches)) {
        array_shift($matches);
        list($controller, $method) = $handler;
        $controllerClass = "Controllers\\$controller";
        
        if (class_exists($controllerClass)) {
            $controllerInstance = new $controllerClass();
            if (method_exists($controllerInstance, $method)) {
                call_user_func_array([$controllerInstance, $method], $matches);
                $matched = true;
                break;
            }
        }
    }
}

if (!$matched) {
    // Try to load home page
    $homeController = new Controllers\HomeController();
    $homeController->index();
}