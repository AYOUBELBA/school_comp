<?php
// views/products/details.php

$product = $product ?? [];
$images = $images ?? [];
$similar = $similar ?? [];
$isFavorited = $isFavorited ?? false;
$user = \Core\Auth::getUser();
$isOwner = $user && $user['id'] == $product['seller_id'];
?>

<div class="product-details-container">
    <div class="product-details">
        <!-- Product Images -->
        <div class="product-gallery">
            <?php if (!empty($images)): ?>
                <img src="/uploads/products/<?php echo htmlspecialchars($images[0]['image_path']); ?>" 
                     alt="<?php echo htmlspecialchars($product['title']); ?>" 
                     class="main-image" id="mainImage">
                
                <?php if (count($images) > 1): ?>
                    <div class="thumbnail-grid">
                        <?php foreach ($images as $image): ?>
                            <img src="/uploads/products/<?php echo htmlspecialchars($image['image_path']); ?>" 
                                 alt="<?php echo htmlspecialchars($product['title']); ?>" 
                                 class="thumbnail <?php echo $image['is_primary'] ? 'active' : ''; ?>"
                                 onclick="changeMainImage(this)">
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="main-image placeholder">No Image Available</div>
            <?php endif; ?>
        </div>
        
        <!-- Product Info -->
        <div class="product-info-details">
            <h1 class="product-title-details"><?php echo htmlspecialchars($product['title']); ?></h1>
            
            <div class="product-meta">
                <span class="product-condition-badge"><?php echo ucfirst($product['condition'] ?? 'used'); ?></span>
                <span class="product-status <?php echo $product['status']; ?>">
                    <?php echo ucfirst($product['status'] ?? 'active'); ?>
                </span>
            </div>
            
            <div class="product-price-details">
                $<?php echo number_format($product['price'], 2); ?>
                <?php if ($product['is_negotiable']): ?>
                    <span class="negotiable-badge">Negotiable</span>
                <?php endif; ?>
            </div>
            
            <div class="product-seller-info">
                <div class="seller-avatar">
                    <?php echo strtoupper(substr($product['seller_name'] ?? 'U', 0, 1)); ?>
                </div>
                <div>
                    <div class="seller-name"><?php echo htmlspecialchars($product['seller_name'] ?? 'Unknown'); ?></div>
                    <div class="seller-rating">
                        ⭐ <?php echo number_format($product['seller_rating'] ?? 0, 1); ?>
                    </div>
                </div>
            </div>
            
            <div class="product-description-details">
                <h3>Description</h3>
                <p><?php echo nl2br(htmlspecialchars($product['description'] ?? '')); ?></p>
            </div>
            
            <div class="product-location">
                📍 <?php echo htmlspecialchars($product['location'] ?? 'Location not specified'); ?>
            </div>
            
            <?php if (!$isOwner && $product['status'] === 'active'): ?>
                <div class="product-actions-details">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <button onclick="makeOffer(<?php echo $product['id']; ?>)" 
                                class="btn btn-primary" 
                                <?php echo !$product['is_negotiable'] ? 'disabled' : ''; ?>>
                            💰 Make Offer
                        </button>
                        <a href="/chat?product_id=<?php echo $product['id']; ?>" class="btn btn-secondary">
                            💬 Contact Seller
                        </a>
                        <button onclick="toggleFavorite(<?php echo $product['id']; ?>)" 
                                class="btn <?php echo $isFavorited ? 'btn-danger' : 'btn-outline'; ?>">
                            <?php echo $isFavorited ? '❤️ Favorited' : '🤍 Add to Favorites'; ?>
                        </button>
                    <?php else: ?>
                        <a href="/login" class="btn btn-primary">Login to Make Offer</a>
                        <a href="/register" class="btn btn-secondary">Register</a>
                    <?php endif; ?>
                </div>
            <?php elseif ($isOwner): ?>
                <div class="product-actions-details">
                    <a href="/product/edit/<?php echo $product['id']; ?>" class="btn btn-primary">✏️ Edit Listing</a>
                    <a href="/product/delete/<?php echo $product['id']; ?>" class="btn btn-danger" 
                       onclick="return confirm('Are you sure you want to delete this listing?')">🗑️ Delete</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Similar Products -->
    <?php if (!empty($similar)): ?>
        <section class="similar-products">
            <h2>Similar Products</h2>
            <div class="product-grid">
                <?php foreach ($similar as $similarProduct): ?>
                    <div class="product-card" onclick="window.location='/product/<?php echo $similarProduct['id']; ?>'">
                        <?php if (!empty($similarProduct['primary_image'])): ?>
                            <img src="/uploads/products/<?php echo htmlspecialchars($similarProduct['primary_image']); ?>" 
                                 alt="<?php echo htmlspecialchars($similarProduct['title']); ?>" 
                                 class="product-image">
                        <?php else: ?>
                            <div class="product-image placeholder">No Image</div>
                        <?php endif; ?>
                        <div class="product-info">
                            <h3><?php echo htmlspecialchars($similarProduct['title']); ?></h3>
                            <div class="product-price">$<?php echo number_format($similarProduct['price'], 2); ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
</div>

<!-- Offer Modal -->
<div id="offerModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="modal-close" onclick="closeOfferModal()">&times;</span>
        <h2>Make an Offer</h2>
        <form id="offerForm" onsubmit="submitOffer(event)">
            <input type="hidden" id="offerProductId" name="product_id">
            <div class="form-group">
                <label for="offeredPrice">Your Offer Price ($)</label>
                <input type="number" id="offeredPrice" name="offered_price" 
                       class="form-control" step="0.01" min="0.01" required>
                <small>Original Price: $<?php echo number_format($product['price'], 2); ?></small>
            </div>
            <div class="form-group">
                <label for="offerMessage">Message (Optional)</label>
                <textarea id="offerMessage" name="message" class="form-control" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Submit Offer</button>
        </form>
    </div>
</div>

<script>
function changeMainImage(element) {
    document.getElementById('mainImage').src = element.src;
    document.querySelectorAll('.thumbnail').forEach(el => el.classList.remove('active'));
    element.classList.add('active');
}

function toggleFavorite(productId) {
    fetch('/product/favorite/' + productId, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.favorited) {
            location.reload();
        } else {
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function makeOffer(productId) {
    document.getElementById('offerProductId').value = productId;
    document.getElementById('offerModal').style.display = 'block';
}

function closeOfferModal() {
    document.getElementById('offerModal').style.display = 'none';
}

function submitOffer(event) {
    event.preventDefault();
    
    const formData = new FormData(document.getElementById('offerForm'));
    
    fetch('/offer/create', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Offer submitted successfully!');
            closeOfferModal();
        } else {
            alert('Error: ' + (data.error || data.message || 'Failed to submit offer'));
        }
    })
    .catch(error => {
        alert('Error: ' + error.message);
    });
}

// Close modal on click outside
window.onclick = function(event) {
    const modal = document.getElementById('offerModal');
    if (event.target == modal) {
        closeOfferModal();
    }
}
</script>

<style>
.product-details-container {
    padding: 20px 0;
}

.product-details {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 40px;
    background: white;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.product-gallery {
    position: sticky;
    top: 100px;
}

.main-image {
    width: 100%;
    height: 400px;
    object-fit: contain;
    background: #f8f9fa;
    border-radius: 8px;
    border: 1px solid #eee;
}

.main-image.placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    color: #999;
    font-size: 18px;
}

.thumbnail-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 10px;
    margin-top: 10px;
}

.thumbnail {
    width: 100%;
    height: 80px;
    object-fit: cover;
    border-radius: 4px;
    cursor: pointer;
    border: 2px solid transparent;
    transition: all 0.3s;
}

.thumbnail:hover {
    border-color: #0654ba;
}

.thumbnail.active {
    border-color: #0654ba;
}

.product-info-details {
    padding: 0 20px;
}

.product-title-details {
    font-size: 28px;
    margin: 0 0 12px 0;
}

.product-meta {
    display: flex;
    gap: 12px;
    margin-bottom: 16px;
}

.product-condition-badge {
    padding: 4px 12px;
    background: #f0f0f0;
    border-radius: 12px;
    font-size: 13px;
}

.product-status {
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 13px;
}

.product-status.active {
    background: #d4edda;
    color: #155724;
}

.product-status.sold {
    background: #f8d7da;
    color: #721c24;
}

.product-price-details {
    font-size: 32px;
    font-weight: bold;
    color: #0654ba;
    margin: 16px 0;
}

.negotiable-badge {
    font-size: 14px;
    background: #fff3cd;
    color: #856404;
    padding: 4px 12px;
    border-radius: 12px;
    margin-left: 12px;
}

.product-seller-info {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    background: #f8f9fa;
    border-radius: 8px;
    margin: 16px 0;
}

.seller-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: #0654ba;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 18px;
}

.seller-name {
    font-weight: 500;
}

.seller-rating {
    font-size: 14px;
    color: #666;
}

.product-description-details {
    margin: 20px 0;
}

.product-description-details h3 {
    margin-bottom: 8px;
}

.product-description-details p {
    color: #555;
    line-height: 1.6;
}

.product-location {
    color: #666;
    padding: 8px 0;
    border-top: 1px solid #eee;
}

.product-actions-details {
    display: flex;
    gap: 12px;
    margin-top: 20px;
    flex-wrap: wrap;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 16px;
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

.btn-danger {
    background: #ea4335;
    color: white;
}

.btn-danger:hover {
    background: #d33426;
}

.btn-outline {
    background: transparent;
    color: #0654ba;
    border: 2px solid #0654ba;
}

.btn-outline:hover {
    background: #0654ba;
    color: white;
}

.similar-products {
    margin-top: 40px;
}

.similar-products h2 {
    margin-bottom: 20px;
}

.product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 20px;
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
    height: 150px;
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
    padding: 12px;
}

.product-info h3 {
    font-size: 14px;
    margin: 0 0 8px 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.product-price {
    font-weight: bold;
    color: #0654ba;
}

/* Modal */
.modal {
    position: fixed;
    z-index: 1001;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-content {
    background: white;
    padding: 30px;
    border-radius: 8px;
    max-width: 500px;
    width: 100%;
    position: relative;
}

.modal-close {
    position: absolute;
    right: 20px;
    top: 10px;
    font-size: 28px;
    cursor: pointer;
    color: #999;
}

.modal-close:hover {
    color: #333;
}

@media (max-width: 768px) {
    .product-details {
        grid-template-columns: 1fr;
    }
    
    .product-gallery {
        position: relative;
        top: 0;
    }
}
</style>