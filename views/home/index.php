<?php
// views/home/index.php

$featuredProducts = $featuredProducts ?? [];
$latestProducts = $latestProducts ?? [];
$categories = $categories ?? [];
$currentPage = 'home';
?>

<!-- Hero Section -->
<section class="hero-modern animate-fade-in">
    <div class="hero-content">
        <h1>Give goods a second life.</h1>
        <p>ReCircle is the premium marketplace for conscious consumers to buy and sell pre-loved items.</p>
        <div class="hero-buttons">
            <a href="/products" class="btn-hero-primary">Shop Now</a>
            <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="/register" class="btn-hero-secondary">Start Selling</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Categories -->
<section>
    <div class="section-header">
        <h2>Shop by Category</h2>
        <a href="/categories">View All →</a>
    </div>
    
    <?php if (!empty($categories)): ?>
        <div class="category-grid-modern">
            <?php 
            $categoryIcons = [
                'Electronics' => '📱',
                'Clothing' => '👕',
                'Furniture' => '🪑',
                'Books' => '📚',
                'Sports' => '⚽',
                'Collectibles' => '🎯',
                'Vehicles' => '🚗',
                'Garden & Plants' => '🌱',
                'Toys & Games' => '🎮'
            ];
            foreach ($categories as $category): 
            ?>
                <a href="/products?category=<?php echo $category['id']; ?>" class="category-card-modern">
                    <div class="category-icon"><?php echo $categoryIcons[$category['name']] ?? '📦'; ?></div>
                    <div class="category-name"><?php echo htmlspecialchars($category['name']); ?></div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<!-- Featured Products -->
<?php if (!empty($featuredProducts)): ?>
<section>
    <div class="section-header">
        <h2>🌟 Featured Listings</h2>
        <a href="/products">View All →</a>
    </div>
    
    <div class="product-grid-modern">
        <?php foreach ($featuredProducts as $product): ?>
            <div class="product-card-modern" onclick="window.location='/product/<?php echo $product['id']; ?>'">
                <?php if (!empty($product['primary_image'])): ?>
                    <img src="/uploads/products/<?php echo htmlspecialchars($product['primary_image']); ?>" 
                         alt="<?php echo htmlspecialchars($product['title']); ?>" 
                         class="product-image-modern">
                <?php else: ?>
                    <div class="product-image-modern" style="display:flex;align-items:center;justify-content:center;color:var(--gray-400);">
                        No Image
                    </div>
                <?php endif; ?>
                <div class="product-info-modern">
                    <div class="product-title-modern"><?php echo htmlspecialchars($product['title']); ?></div>
                    <div class="product-price-modern">$<?php echo number_format($product['price'], 2); ?></div>
                    <div class="product-meta-modern">
                        <span><?php echo htmlspecialchars($product['seller_name'] ?? 'Unknown'); ?></span>
                        <span class="badge badge-success"><?php echo ucfirst($product['condition'] ?? 'used'); ?></span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- Latest Products -->
<?php if (!empty($latestProducts)): ?>
<section>
    <div class="section-header">
        <h2>🆕 Latest Listings</h2>
        <a href="/products">View All →</a>
    </div>
    
    <div class="product-grid-modern">
        <?php foreach ($latestProducts as $product): ?>
            <div class="product-card-modern" onclick="window.location='/product/<?php echo $product['id']; ?>'">
                <?php if (!empty($product['primary_image'])): ?>
                    <img src="/uploads/products/<?php echo htmlspecialchars($product['primary_image']); ?>" 
                         alt="<?php echo htmlspecialchars($product['title']); ?>" 
                         class="product-image-modern">
                <?php else: ?>
                    <div class="product-image-modern" style="display:flex;align-items:center;justify-content:center;color:var(--gray-400);">
                        No Image
                    </div>
                <?php endif; ?>
                <div class="product-info-modern">
                    <div class="product-title-modern"><?php echo htmlspecialchars($product['title']); ?></div>
                    <div class="product-price-modern">$<?php echo number_format($product['price'], 2); ?></div>
                    <div class="product-meta-modern">
                        <span><?php echo htmlspecialchars($product['seller_name'] ?? 'Unknown'); ?></span>
                        <span class="badge badge-success"><?php echo ucfirst($product['condition'] ?? 'used'); ?></span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- Empty State -->
<?php if (empty($featuredProducts) && empty($latestProducts)): ?>
<section class="text-center" style="padding:var(--space-2xl) 0;">
    <h2 style="margin-bottom:var(--space-md);">Welcome to ReCircle!</h2>
    <p style="color:var(--gray-500);margin-bottom:var(--space-lg);">No products available yet. Be the first to list an item!</p>
    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="/product/create" class="btn-modern btn-modern-primary">Create Listing</a>
    <?php else: ?>
        <a href="/register" class="btn-modern btn-modern-primary">Start Selling</a>
    <?php endif; ?>
</section>
<?php endif; ?>