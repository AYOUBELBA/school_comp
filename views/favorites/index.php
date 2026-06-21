<?php
// views/favorites/index.php

$favorites = $favorites ?? [];
?>

<div class="favorites-page">
    <h1 class="page-title">❤️ Your Favorites</h1>
    
    <?php if (empty($favorites)): ?>
        <div class="empty-state-modern">
            <div class="empty-icon">💔</div>
            <h2>No favorites yet</h2>
            <p>Start exploring products and save your favorites!</p>
            <a href="/products" class="btn-modern btn-modern-primary">Browse Products</a>
        </div>
    <?php else: ?>
        <div class="product-grid-modern">
            <?php foreach ($favorites as $product): ?>
                <div class="product-card-modern">
                    <div class="product-image-wrapper">
                        <?php if (!empty($product['primary_image'])): ?>
                            <img src="/uploads/products/<?php echo htmlspecialchars($product['primary_image']); ?>" 
                                 alt="<?php echo htmlspecialchars($product['title']); ?>" 
                                 class="product-image-modern"
                                 onclick="window.location='/product/<?php echo $product['id']; ?>'">
                        <?php else: ?>
                            <div class="product-image-modern placeholder" onclick="window.location='/product/<?php echo $product['id']; ?>'">
                                No Image
                            </div>
                        <?php endif; ?>
                        <button class="remove-favorite" onclick="removeFavorite(<?php echo $product['id']; ?>)" title="Remove from favorites">
                            ❌
                        </button>
                    </div>
                    <div class="product-info-modern" onclick="window.location='/product/<?php echo $product['id']; ?>'">
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
    <?php endif; ?>
</div>

<script>
function removeFavorite(productId) {
    if (!confirm('Remove this product from favorites?')) return;
    
    fetch('/favorites/remove/' + productId, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}
</script>

<style>
.favorites-page {
    padding: var(--space-xl) 0;
}

.page-title {
    margin-bottom: var(--space-xl);
    font-weight: 800;
}

.product-image-wrapper {
    position: relative;
}

.remove-favorite {
    position: absolute;
    top: var(--space-sm);
    right: var(--space-sm);
    background: rgba(255, 255, 255, 0.9);
    border: none;
    border-radius: var(--border-radius-full);
    padding: 4px 8px;
    cursor: pointer;
    transition: all 0.3s;
    font-size: var(--font-size-sm);
    box-shadow: var(--shadow-sm);
}

.remove-favorite:hover {
    transform: scale(1.1);
    background: white;
    box-shadow: var(--shadow-md);
}

.empty-state-modern {
    text-align: center;
    padding: var(--space-2xl) var(--space-xl);
    background: var(--white);
    border-radius: var(--border-radius-xl);
    box-shadow: var(--shadow-sm);
}

.empty-icon {
    font-size: 64px;
    margin-bottom: var(--space-md);
}

.empty-state-modern h2 {
    margin-bottom: var(--space-sm);
    color: var(--gray-800);
}

.empty-state-modern p {
    color: var(--gray-500);
    margin-bottom: var(--space-lg);
}
</style>