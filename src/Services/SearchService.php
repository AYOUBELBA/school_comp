<?php
// src/Services/SearchService.php

namespace Services;

use Core\Database;

class SearchService
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    public function search($filters, $limit = 20, $offset = 0)
    {
        $where = ['p.status = "active"'];
        $params = [];
        
        // Keyword search
        if (!empty($filters['search'])) {
            $where[] = '(p.title LIKE ? OR p.description LIKE ?)';
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        // Category filter
        if (!empty($filters['category'])) {
            $where[] = 'p.category_id = ?';
            $params[] = (int)$filters['category'];
        }
        
        // Price range
        if (!empty($filters['min_price'])) {
            $where[] = 'p.price >= ?';
            $params[] = (float)$filters['min_price'];
        }
        
        if (!empty($filters['max_price'])) {
            $where[] = 'p.price <= ?';
            $params[] = (float)$filters['max_price'];
        }
        
        // Condition filter
        if (!empty($filters['condition'])) {
            $where[] = 'p.`condition` = ?';
            $params[] = $filters['condition'];
        }
        
        // Location filter
        if (!empty($filters['location'])) {
            $where[] = 'p.location LIKE ?';
            $params[] = '%' . $filters['location'] . '%';
        }
        
        $whereClause = implode(' AND ', $where);
        
        // Get total count
        $countSql = "SELECT COUNT(*) as total FROM products p WHERE {$whereClause}";
        $totalResult = $this->db->fetch($countSql, $params);
        $total = $totalResult['total'] ?? 0;
        
        // Get products - FIXED LIMIT SYNTAX
        $orderBy = 'p.created_at DESC';
        if (!empty($filters['sort'])) {
            switch ($filters['sort']) {
                case 'price_low':
                    $orderBy = 'p.price ASC';
                    break;
                case 'price_high':
                    $orderBy = 'p.price DESC';
                    break;
                case 'newest':
                    $orderBy = 'p.created_at DESC';
                    break;
            }
        }
        
        // Use integer casting for LIMIT and OFFSET
        $limit = (int)$limit;
        $offset = (int)$offset;
        
        $sql = "SELECT p.*, 
                (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1) as primary_image,
                u.full_name as seller_name,
                u.rating as seller_rating
                FROM products p
                JOIN users u ON p.seller_id = u.id
                WHERE {$whereClause}
                ORDER BY {$orderBy}
                LIMIT {$limit} OFFSET {$offset}";
        
        $products = $this->db->fetchAll($sql, $params);
        
        return [
            'products' => $products,
            'total' => (int)$total,
            'limit' => $limit,
            'offset' => $offset
        ];
    }
}