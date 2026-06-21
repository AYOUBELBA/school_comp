<?php
// views/products/listing.php

$products = $products ?? [];
$total = $total ?? 0;
$page = $page ?? 1;
$limit = $limit ?? 20;
$filters = $filters ?? [];
$totalPages = ceil($total / $limit);
?>

<div class="products-listing-container">
    <div class="products-header">
        <h1>Products</h1>
        <?php if (isset($_SESSION['user_id']) && ($_SESSION['user_role'] === 'seller' || $_SESSION['user_role'] === 'admin')): ?>
            <a href="/product/create" class="btn btn-primary">+ Create Listing</a>
        <?php endif; ?>
    </div>
    
    <!-- Filters -->
    <div class="filters-section">
        <form action="/products" method="GET" class="filters-form">
            <div class="filter-group">
                <input type="text" name="search" placeholder="Search products..." 
                       value="<?php echo htmlspecialchars($filters['search'] ?? ''); ?>" 
                       class="form-control">
            </div>
            
            <div class="filter-group">
                <select name="category" class="form-control">
                    <option value="">All Categories</option>
                    <?php 
                    $categories = \Core\Database::getInstance()->fetchAll("SELECT * FROM categories ORDER BY name");
                    foreach ($categories as $category): 
                    ?>
                        <option value="<?php echo $category['id']; ?>" 
                                <?php echo (isset($filters['category']) && $filters['category'] == $category['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="filter-group">
                <select name="condition" class="form-control">
                    <option value="">All Conditions</option>
                    <option value="new" <?php echo (isset($filters['condition']) && $filters['condition'] == 'new') ? 'selected' : ''; ?>>New</option>
                    <option value="used" <?php echo (isset($filters['condition']) && $filters['condition'] == 'used') ? 'selected' : ''; ?>>Used</option>
                    <option value="refurbished" <?php echo (isset($filters['condition']) && $filters['condition'] == 'refurbished') ? 'selected' : ''; ?>>Refurbished</option>
                    <option value="damaged" <?php echo (isset($filters['condition']) && $filters['condition'] == 'damaged') ? 'selected' : ''; ?>>Damaged</option>
                </select>
            </div>
            
            <div class="filter-group price-range">
                <input type="number" name="min_price" placeholder="Min Price" 
                       value="<?php echo htmlspecialchars($filters['min_price'] ?? ''); ?>" 
                       class="form-control" step="0.01" min="0">
                <span>-</span>
                <input type="number" name="max_price" placeholder="Max Price" 
                       value="<?php echo htmlspecialchars($filters['max_price'] ?? ''); ?>" 
                       class="form-control" step="0.01" min="0">
            </div>
            
            <div class="filter-group">
                <select name="sort" class="form-control">
                    <option value="newest" <?php echo (isset($filters['sort']) && $filters['sort'] == 'newest') ? 'selected' : ''; ?>>Newest First</option>
                    <option value="price_low" <?php echo (isset($filters['sort']) && $filters['sort'] == 'price_low') ? 'selected' : ''; ?>>Price: Low to High</option>
                    <option value="price_high" <?php echo (isset($filters['sort']) && $filters['sort'] == 'price_high') ? 'selected' : ''; ?>>Price: High to Low</option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary">Apply Filters</button>
            <a href="/products" class="btn btn-secondary">Reset</a>
        </form>
    </div>
    
    <!-- Results -->
    <div class="results-info">
        <span><?php echo $total; ?> products found</span>
    </div>
    
    <?php if (empty($products)): ?>
        <div class="empty-state">
            <p>No products found matching your criteria.</p>
            <a href="/products" class="btn btn-primary">View All Products</a>
        </div>
    <?php else: ?>
        <div class="product-grid">
            <?php foreach ($products as $product): ?>
                <div class="product-card" onclick="window.location='/product/<?php echo $product['id']; ?>'">
                    <?php if (!empty($product['primary_image'])): ?>
                        <img src="/uploads/products/<?php echo htmlspecialchars($product['primary_image']); ?>" 
                             alt="<?php echo htmlspecialchars($product['title']); ?>" 
                             class="product-image">
                    <?php else: ?>
                        <div class="product-image placeholder">No Image</div>
                    <?php endif; ?>
                    <div class="product-info">
                        <h3><?php echo htmlspecialchars($product['title']); ?></h3>
                        <div class="product-price">$<?php echo number_format($product['price'], 2); ?></div>
                        <div class="product-seller">by <?php echo htmlspecialchars($product['seller_name'] ?? 'Unknown'); ?></div>
                        <span class="product-condition"><?php echo ucfirst($product['condition'] ?? 'used'); ?></span>
                        <?php if ($product['is_negotiable']): ?>
                            <span class="negotiable-tag">Negotiable</span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?>&<?php echo http_build_query($filters); ?>" class="btn btn-secondary">&laquo; Previous</a>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <?php if ($i == $page): ?>
                        <span class="pagination-current"><?php echo $i; ?></span>
                    <?php else: ?>
                        <a href="?page=<?php echo $i; ?>&<?php echo http_build_query($filters); ?>" class="btn btn-secondary"><?php echo $i; ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
                
                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?php echo $page + 1; ?>&<?php echo http_build_query($filters); ?>" class="btn btn-secondary">Next &raquo;</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<style>
.products-listing-container {
    padding: 20px 0;
}

.products-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
}

.products-header h1 {
    margin: 0;
}

.filters-section {
    background: white;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 24px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.filters-form {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    align-items: center;
}

.filter-group {
    flex: 1;
    min-width: 150px;
}

.filter-group.price-range {
    display: flex;
    gap: 8px;
    align-items: center;
    min-width: 200px;
}

.filter-group.price-range span {
    color: #666;
}

.form-control {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 14px;
}

.form-control:focus {
    outline: none;
    border-color: #0654ba;
}

.btn {
    padding: 8px 16px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
    transition: all 0.3s;
    text-decoration: none;
    display: inline-block;
}

.btn-primary {
    background: #0654ba;
    color: white;
}

.btn-primary:hover {
    background: #004b9a;
}

.btn-secondary {
    background: #f0f0f0;
    color: #333;
}

.btn-secondary:hover {
    background: #e0e0e0;
}

.results-info {
    margin-bottom: 16px;
    color: #666;
}

.product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.product-card {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    cursor: pointer;
    transition: all 0.3s;
}

.product-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
}

.product-image {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.product-image.placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f0f0f0;
    color: #999;
}

.product-info {
    padding: 16px;
}

.product-info h3 {
    font-size: 16px;
    margin: 0 0 8px 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.product-price {
    font-size: 20px;
    font-weight: bold;
    color: #0654ba;
}

.product-seller {
    font-size: 13px;
    color: #666;
}

.product-condition {
    display: inline-block;
    padding: 2px 10px;
    background: #f0f0f0;
    border-radius: 12px;
    font-size: 12px;
    margin-top: 8px;
}

.negotiable-tag {
    display: inline-block;
    padding: 2px 10px;
    background: #fff3cd;
    color: #856404;
    border-radius: 12px;
    font-size: 12px;
    margin-top: 8px;
    margin-left: 8px;
}

.pagination {
    display: flex;
    gap: 8px;
    justify-content: center;
    margin-top: 20px;
}

.pagination-current {
    padding: 8px 16px;
    background: #0654ba;
    color: white;
    border-radius: 6px;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 8px;
}

@media (max-width: 768px) {
    .filters-form {
        flex-direction: column;
    }
    
    .filter-group {
        width: 100%;
    }
    
    .filter-group.price-range {
        width: 100%;
    }
}
</style>