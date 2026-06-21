<?php
// views/home/index.php

// Ensure variables are arrays
$featuredProducts = $featuredProducts ?? [];
$latestProducts = $latestProducts ?? [];
$categories = $categories ?? [];
?>

<div class="container">
    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Welcome to MarketPlace</h1>
            <p>Buy and sell used items with real-time chat and offers</p>
            <div class="hero-actions">
                <a href="/products" class="btn btn-primary">Browse Products</a>
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <a href="/register" class="btn btn-secondary">Start Selling</a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Categories -->
    <?php if (!empty($categories)): ?>
    <section class="categories-section">
        <h2>Browse Categories</h2>
        <div class="category-grid">
            <?php foreach ($categories as $category): ?>
                <a href="/products?category=<?php echo $category['id']; ?>" class="category-card">
                    <h3><?php echo htmlspecialchars($category['name']); ?></h3>
                    <p><?php echo htmlspecialchars($category['description'] ?? ''); ?></p>
                </a>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Featured Products -->
    <?php if (!empty($featuredProducts)): ?>
    <section class="featured-section">
        <h2>🌟 Featured Products</h2>
        <div class="product-grid">
            <?php foreach ($featuredProducts as $product): ?>
                <div class="product-card" onclick="window.location='/product/<?php echo $product['id']; ?>'">
                    <?php if (!empty($product['primary_image'])): ?>
                        <img src="/uploads/products/<?php echo htmlspecialchars($product['primary_image']); ?>" 
                             alt="<?php echo htmlspecialchars($product['title']); ?>" 
                             class="product-image">
                    <?php else: ?>
                        <div class="product-image placeholder">No Image</div>
                    <?php endif; ?>
                    <div class="product-info">
                        <h3 class="product-title"><?php echo htmlspecialchars($product['title']); ?></h3>
                        <div class="product-price">$<?php echo number_format($product['price'], 2); ?></div>
                        <div class="product-seller">by <?php echo htmlspecialchars($product['seller_name'] ?? 'Unknown'); ?></div>
                        <span class="product-condition"><?php echo ucfirst($product['condition'] ?? 'used'); ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Latest Products -->
    <?php if (!empty($latestProducts)): ?>
    <section class="latest-section">
        <h2>🆕 Latest Listings</h2>
        <div class="product-grid">
            <?php foreach ($latestProducts as $product): ?>
                <div class="product-card" onclick="window.location='/product/<?php echo $product['id']; ?>'">
                    <?php if (!empty($product['primary_image'])): ?>
                        <img src="/uploads/products/<?php echo htmlspecialchars($product['primary_image']); ?>" 
                             alt="<?php echo htmlspecialchars($product['title']); ?>" 
                             class="product-image">
                    <?php else: ?>
                        <div class="product-image placeholder">No Image</div>
                    <?php endif; ?>
                    <div class="product-info">
                        <h3 class="product-title"><?php echo htmlspecialchars($product['title']); ?></h3>
                        <div class="product-price">$<?php echo number_format($product['price'], 2); ?></div>
                        <div class="product-seller">by <?php echo htmlspecialchars($product['seller_name'] ?? 'Unknown'); ?></div>
                        <span class="product-condition"><?php echo ucfirst($product['condition'] ?? 'used'); ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <?php if (empty($featuredProducts) && empty($latestProducts)): ?>
    <section class="empty-state">
        <h2>Welcome to Marketplace!</h2>
        <p>No products available yet. Be the first to list an item!</p>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="/product/create" class="btn btn-primary">Create Listing</a>
        <?php else: ?>
            <a href="/register" class="btn btn-primary">Register to Start Selling</a>
        <?php endif; ?>
    </section>
    <?php endif; ?>
</div>

<style>
.hero {
    text-align: center;
    padding: 60px 0;
    background: linear-gradient(135deg, #0654ba 0%, #004b9a 100%);
    color: white;
    border-radius: 12px;
    margin: 20px 0;
}

.hero h1 {
    font-size: 48px;
    margin-bottom: 12px;
}

.hero p {
    font-size: 20px;
    opacity: 0.9;
}

.categories {
    margin: 40px 0;
}

.category-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 16px;
    margin-top: 16px;
}

.category-card {
    background: white;
    padding: 20px;
    text-align: center;
    border-radius: 8px;
    text-decoration: none;
    color: #333;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: transform 0.2s;
}

.category-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.category-card h3 {
    font-size: 16px;
    margin: 0;
}

.featured-products, .latest-products {
    margin: 40px 0;
}

.featured-products h2, .latest-products h2 {
    margin-bottom: 20px;
}

.placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f0f0f0;
    color: #999;
}
</style>