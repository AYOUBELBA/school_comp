<?php
// src/Controllers/OfferController.php

namespace Controllers;

use Core\Controller;
use Core\Auth;
use Core\Database;

class OfferController extends Controller
{
    private $db;
    
    public function __construct()
    {
        Auth::requireLogin();
        $this->db = Database::getInstance();
    }
    
    public function manage()
    {
        $userId = Auth::getUserId();
        $user = Auth::getUser();
        $isSeller = ($user['role'] === 'seller' || $user['role'] === 'admin');
        
        if ($isSeller) {
            // Get offers as seller
            $offers = $this->db->fetchAll(
                "SELECT o.*, p.title as product_title, p.price as original_price, u.full_name as buyer_name,
                 (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1) as product_image
                 FROM offers o
                 JOIN products p ON o.product_id = p.id
                 JOIN users u ON o.buyer_id = u.id
                 WHERE o.seller_id = ?
                 ORDER BY o.created_at DESC",
                [$userId]
            );
        } else {
            // Get offers as buyer
            $offers = $this->db->fetchAll(
                "SELECT o.*, p.title as product_title, p.price as original_price, u.full_name as seller_name,
                 (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1) as product_image
                 FROM offers o
                 JOIN products p ON o.product_id = p.id
                 JOIN users u ON o.seller_id = u.id
                 WHERE o.buyer_id = ?
                 ORDER BY o.created_at DESC",
                [$userId]
            );
        }
        
        $this->view('offers/manage', [
            'offers' => $offers,
            'isSeller' => $isSeller
        ]);
    }
    
    public function create()
    {
        Auth::requireLogin();
        
        $validation = $this->validate($_POST, [
            'product_id' => 'required|numeric',
            'offered_price' => 'required|numeric|min:0.01',
            'message' => 'max:500'
        ]);
        
        if (!$validation['valid']) {
            return $this->json(['errors' => $validation['errors']], 422);
        }
        
        $product = $this->db->fetch(
            "SELECT id, seller_id, price, is_negotiable, status FROM products WHERE id = ?",
            [$_POST['product_id']]
        );
        
        if (!$product) {
            return $this->json(['error' => 'Product not found'], 404);
        }
        
        if ($product['status'] !== 'active') {
            return $this->json(['error' => 'Product is not available'], 422);
        }
        
        if (!$product['is_negotiable']) {
            return $this->json(['error' => 'This product is not negotiable'], 422);
        }
        
        if ($_POST['offered_price'] >= $product['price']) {
            return $this->json(['error' => 'Offer price must be less than listing price'], 422);
        }
        
        // Check for existing pending offer
        $existing = $this->db->fetch(
            "SELECT id FROM offers 
             WHERE product_id = ? AND buyer_id = ? AND status IN ('pending', 'countered')
             ORDER BY created_at DESC LIMIT 1",
            [$_POST['product_id'], Auth::getUserId()]
        );
        
        if ($existing) {
            return $this->json(['error' => 'You already have a pending offer for this product'], 422);
        }
        
        // Prevent offering on own product
        if ($product['seller_id'] == Auth::getUserId()) {
            return $this->json(['error' => 'You cannot make an offer on your own product'], 422);
        }
        
        $offerId = $this->db->insert('offers', [
            'product_id' => $_POST['product_id'],
            'buyer_id' => Auth::getUserId(),
            'seller_id' => $product['seller_id'],
            'offered_price' => $_POST['offered_price'],
            'message' => $_POST['message'] ?? null,
            'status' => 'pending'
        ]);
        
        // Create notification for seller
        $this->createNotification(
            $product['seller_id'],
            'new_offer',
            'New Offer Received',
            "You have a new offer of $" . number_format($_POST['offered_price'], 2) . " on your product"
        );
        
        return $this->json([
            'success' => true,
            'offer_id' => $offerId,
            'message' => 'Offer sent successfully!'
        ]);
    }
    
    public function accept($id)
    {
        Auth::requireLogin();
        
        $offer = $this->db->fetch(
            "SELECT o.*, p.seller_id, p.id as product_id, p.price as original_price
             FROM offers o
             JOIN products p ON o.product_id = p.id
             WHERE o.id = ? AND o.status IN ('pending', 'countered')",
            [$id]
        );
        
        if (!$offer) {
            return $this->redirect('/offers', ['error' => 'Offer not found or already processed']);
        }
        
        // Check if user is the seller
        if ($offer['seller_id'] !== Auth::getUserId()) {
            return $this->redirect('/offers', ['error' => 'Unauthorized']);
        }
        
        // Determine final price
        $finalPrice = $offer['offered_price'];
        if ($offer['status'] === 'countered' && !empty($offer['counter_offer_price'])) {
            $finalPrice = $offer['counter_offer_price'];
        }
        
        // Create order from accepted offer
        $orderId = $this->db->insert('orders', [
            'buyer_id' => $offer['buyer_id'],
            'product_id' => $offer['product_id'],
            'seller_id' => $offer['seller_id'],
            'amount' => $finalPrice,
            'status' => 'pending'
        ]);
        
        // Update offer status
        $this->db->update('offers', [
            'status' => 'accepted'
        ], 'id = ?', [$id]);
        
        // Update product status
        $this->db->update('products', [
            'status' => 'sold'
        ], 'id = ?', [$offer['product_id']]);
        
        // Notify buyer
        $this->createNotification(
            $offer['buyer_id'],
            'offer_accepted',
            'Offer Accepted!',
            "Your offer of $" . number_format($finalPrice, 2) . " has been accepted!"
        );
        
        return $this->redirect('/offers', ['success' => 'Offer accepted! Order created.']);
    }
    
    public function reject($id)
    {
        Auth::requireLogin();
        
        $offer = $this->db->fetch(
            "SELECT o.*, p.seller_id
             FROM offers o
             JOIN products p ON o.product_id = p.id
             WHERE o.id = ? AND o.status IN ('pending', 'countered')",
            [$id]
        );
        
        if (!$offer) {
            return $this->redirect('/offers', ['error' => 'Offer not found']);
        }
        
        if ($offer['seller_id'] !== Auth::getUserId()) {
            return $this->redirect('/offers', ['error' => 'Unauthorized']);
        }
        
        $this->db->update('offers', [
            'status' => 'rejected'
        ], 'id = ?', [$id]);
        
        $this->createNotification(
            $offer['buyer_id'],
            'offer_rejected',
            'Offer Declined',
            "Your offer for the product has been declined"
        );
        
        return $this->redirect('/offers', ['success' => 'Offer rejected.']);
    }
    
    public function counter($id)
    {
        Auth::requireLogin();
        
        // Check if this is an AJAX request
        $isAjax = $this->isAjax();
        
        if (!$isAjax) {
            // Handle form submission from the offer page
            $validation = $this->validate($_POST, [
                'counter_price' => 'required|numeric|min:0.01'
            ]);
            
            if (!$validation['valid']) {
                return $this->redirect('/offers', ['error' => 'Please enter a valid counter price']);
            }
            
            $offer = $this->db->fetch(
                "SELECT o.*, p.seller_id
                 FROM offers o
                 JOIN products p ON o.product_id = p.id
                 WHERE o.id = ? AND o.status = 'pending'",
                [$id]
            );
            
            if (!$offer) {
                return $this->redirect('/offers', ['error' => 'Offer not found']);
            }
            
            if ($offer['seller_id'] !== Auth::getUserId()) {
                return $this->redirect('/offers', ['error' => 'Unauthorized']);
            }
            
            $this->db->update('offers', [
                'status' => 'countered',
                'counter_offer_price' => $_POST['counter_price']
            ], 'id = ?', [$id]);
            
            $this->createNotification(
                $offer['buyer_id'],
                'offer_countered',
                'Counter Offer Received',
                "The seller has countered with $" . number_format($_POST['counter_price'], 2)
            );
            
            return $this->redirect('/offers', ['success' => 'Counter offer sent!']);
        }
        
        // Handle AJAX request
        $validation = $this->validate($_POST, [
            'counter_price' => 'required|numeric|min:0.01'
        ]);
        
        if (!$validation['valid']) {
            return $this->json(['errors' => $validation['errors']], 422);
        }
        
        $offer = $this->db->fetch(
            "SELECT o.*, p.seller_id
             FROM offers o
             JOIN products p ON o.product_id = p.id
             WHERE o.id = ? AND o.status = 'pending'",
            [$id]
        );
        
        if (!$offer) {
            return $this->json(['error' => 'Offer not found'], 404);
        }
        
        if ($offer['seller_id'] !== Auth::getUserId()) {
            return $this->json(['error' => 'Unauthorized'], 403);
        }
        
        $this->db->update('offers', [
            'status' => 'countered',
            'counter_offer_price' => $_POST['counter_price']
        ], 'id = ?', [$id]);
        
        $this->createNotification(
            $offer['buyer_id'],
            'offer_countered',
            'Counter Offer Received',
            "The seller has countered with $" . number_format($_POST['counter_price'], 2)
        );
        
        return $this->json([
            'success' => true,
            'action' => 'countered',
            'counter_price' => $_POST['counter_price']
        ]);
    }
    
    public function acceptCounter($id)
    {
        Auth::requireLogin();
        
        $offer = $this->db->fetch(
            "SELECT o.*, p.seller_id, p.id as product_id
             FROM offers o
             JOIN products p ON o.product_id = p.id
             WHERE o.id = ? AND o.status = 'countered'",
            [$id]
        );
        
        if (!$offer) {
            return $this->redirect('/offers', ['error' => 'Offer not found']);
        }
        
        // Check if user is the buyer (who can accept the counter offer)
        if ($offer['buyer_id'] !== Auth::getUserId()) {
            return $this->redirect('/offers', ['error' => 'Unauthorized']);
        }
        
        // Create order with counter offer price
        $orderId = $this->db->insert('orders', [
            'buyer_id' => $offer['buyer_id'],
            'product_id' => $offer['product_id'],
            'seller_id' => $offer['seller_id'],
            'amount' => $offer['counter_offer_price'],
            'status' => 'pending'
        ]);
        
        // Update offer status
        $this->db->update('offers', [
            'status' => 'accepted'
        ], 'id = ?', [$id]);
        
        // Update product status
        $this->db->update('products', [
            'status' => 'sold'
        ], 'id = ?', [$offer['product_id']]);
        
        // Notify seller
        $this->createNotification(
            $offer['seller_id'],
            'offer_accepted',
            'Counter Offer Accepted!',
            "Your counter offer of $" . number_format($offer['counter_offer_price'], 2) . " has been accepted!"
        );
        
        return $this->redirect('/offers', ['success' => 'Counter offer accepted! Order created.']);
    }
    
    public function rejectCounter($id)
    {
        Auth::requireLogin();
        
        $offer = $this->db->fetch(
            "SELECT o.*, p.seller_id
             FROM offers o
             JOIN products p ON o.product_id = p.id
             WHERE o.id = ? AND o.status = 'countered'",
            [$id]
        );
        
        if (!$offer) {
            return $this->redirect('/offers', ['error' => 'Offer not found']);
        }
        
        // Check if user is the buyer
        if ($offer['buyer_id'] !== Auth::getUserId()) {
            return $this->redirect('/offers', ['error' => 'Unauthorized']);
        }
        
        $this->db->update('offers', [
            'status' => 'rejected'
        ], 'id = ?', [$id]);
        
        $this->createNotification(
            $offer['seller_id'],
            'offer_rejected',
            'Counter Offer Declined',
            "Your counter offer has been declined"
        );
        
        return $this->redirect('/offers', ['success' => 'Counter offer rejected.']);
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