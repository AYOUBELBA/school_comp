<?php


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
            <!-- Buy Now Button -->
            <button onclick="buyNow(<?php echo $product['id']; ?>)" 
                    class="btn btn-success btn-buy-now">
                ⚡ Buy Now - $<?php echo number_format($product['price'], 2); ?>
            </button>
            
            <!-- Make Offer Button -->
            <button onclick="makeOffer(<?php echo $product['id']; ?>)" 
                    class="btn btn-primary" 
                    <?php echo !$product['is_negotiable'] ? 'disabled' : ''; ?>>
                💰 Make Offer
            </button>
            
            <a href="/chat/start?product_id=<?php echo $product['id']; ?>" class="btn btn-secondary">
                💬 Contact Seller
            </a>
            
            <button onclick="toggleFavorite(<?php echo $product['id']; ?>)" 
                    class="btn <?php echo $isFavorited ? 'btn-danger' : 'btn-outline'; ?>">
                <?php echo $isFavorited ? '❤️ Favorited' : '🤍 Add to Favorites'; ?>
            </button>
        <?php else: ?>
            <a href="/login" class="btn btn-primary">Login to Buy</a>
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
<div id="offerModal" class="modal-overlay" style="display: none;">
    <div class="modal-container">
        <div class="modal-content">
            <div class="modal-header">
                <h2>💰 Make an Offer</h2>
                <button class="modal-close" onclick="closeOfferModal()">×</button>
            </div>
            
            <div class="modal-body">
                <div class="offer-product-info">
                    <div class="offer-product-details">
                        <h3><?php echo htmlspecialchars($product['title']); ?></h3>
                        <p class="original-price">Original Price: <strong>$<?php echo number_format($product['price'], 2); ?></strong></p>
                        <p class="negotiable-status">This item is negotiable</p>
                    </div>
                </div>
                
                <form id="offerForm" onsubmit="submitOffer(event)">
                    <input type="hidden" id="offerProductId" name="product_id" value="<?php echo $product['id']; ?>">
                    
                    <div class="form-group-modern">
                        <label for="offeredPrice">Your Offer Price ($)</label>
                        <input type="number" id="offeredPrice" name="offered_price" 
                               class="form-control-modern" step="0.01" min="0.01" 
                               placeholder="Enter your offer amount" required>
                        <small class="help-text">Enter an amount less than $<?php echo number_format($product['price'], 2); ?></small>
                    </div>
                    
                    <div class="form-group-modern">
                        <label for="offerMessage">Message to Seller (Optional)</label>
                        <textarea id="offerMessage" name="message" class="form-control-modern" 
                                  rows="3" placeholder="Add a personal message to the seller..."></textarea>
                    </div>
                    
                    <div class="modal-actions">
                        <button type="button" class="btn-secondary" onclick="closeOfferModal()">Cancel</button>
                        <button type="submit" class="btn-primary">Submit Offer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Buy Now Modal -->
<div id="buyNowModal" class="modal-overlay" style="display: none;">
    <div class="modal-container">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #38A169 0%, #2F855A 100%);">
                <h2 style="color: white;">⚡ Confirm Purchase</h2>
                <button class="modal-close" onclick="closeBuyNowModal()" style="background: rgba(255,255,255,0.2); color: white;">×</button>
            </div>
            
            <div class="modal-body">
                <div class="buy-product-info">
                    <div class="buy-product-details">
                        <h3><?php echo htmlspecialchars($product['title']); ?></h3>
                        <div class="price-breakdown">
                            <div class="price-row">
                                <span>Item Price:</span>
                                <strong>$<?php echo number_format($product['price'], 2); ?></strong>
                            </div>
                            <div class="price-row">
                                <span>Shipping:</span>
                                <strong>Free</strong>
                            </div>
                            <div class="price-row total">
                                <span>Total:</span>
                                <strong>$<?php echo number_format($product['price'], 2); ?></strong>
                            </div>
                        </div>
                        <p class="seller-info">Sold by: <strong><?php echo htmlspecialchars($product['seller_name'] ?? 'Unknown'); ?></strong></p>
                        <p class="condition-info">Condition: <strong><?php echo ucfirst($product['condition'] ?? 'used'); ?></strong></p>
                    </div>
                </div>
                
                <form id="buyNowForm" onsubmit="submitBuyNow(event)">
                    <input type="hidden" id="buyProductId" name="product_id" value="<?php echo $product['id']; ?>">
                    
                    <div class="form-group-modern">
                        <label for="shippingAddress">Shipping Address</label>
                        <textarea id="shippingAddress" name="shipping_address" class="form-control-modern" 
                                  rows="3" placeholder="Enter your full shipping address..." required></textarea>
                    </div>
                    
                    <div class="form-group-modern">
                        <label for="paymentMethod">Payment Method</label>
                        <select id="paymentMethod" name="payment_method" class="form-control-modern" required>
                            <option value="">Select payment method</option>
                            <option value="credit_card">💳 Credit Card</option>
                            <option value="paypal">💰 PayPal</option>
                            <option value="bank_transfer">🏦 Bank Transfer</option>
                            <option value="mobile_money">📱 Mobile Money</option>
                        </select>
                    </div>
                    
                    <div class="payment-note">
                        <small>💡 This is a demo. No actual payment will be processed.</small>
                    </div>
                    
                    <div class="modal-actions">
                        <button type="button" class="btn-secondary" onclick="closeBuyNowModal()">Cancel</button>
                        <button type="submit" class="btn-success">Confirm Purchase</button>
                    </div>
                </form>
            </div>
        </div>
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
    document.getElementById('offerModal').classList.add('active');
    document.getElementById('offerModal').style.display = 'flex';
}

function closeOfferModal() {
    document.getElementById('offerModal').classList.remove('active');
    document.getElementById('offerModal').style.display = 'none';
    document.getElementById('offerForm').reset();
}

function submitOffer(event) {
    event.preventDefault();
    
    const form = document.getElementById('offerForm');
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    
    submitBtn.textContent = 'Submitting...';
    submitBtn.disabled = true;
    
    fetch('/offer/create', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Offer Submitted!',
                text: 'Your offer has been sent to the seller.',
                confirmButtonColor: '#2C7A7B'
            });
            closeOfferModal();
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.error || data.message || 'Failed to submit offer',
                confirmButtonColor: '#2C7A7B'
            });
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'An error occurred. Please try again.',
            confirmButtonColor: '#2C7A7B'
        });
        console.error('Error:', error);
    })
    .finally(() => {
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    });
}

// Close modal on click outside
window.onclick = function(event) {
    const modal = document.getElementById('offerModal');
    if (event.target === modal) {
        closeOfferModal();
    }
}

// Close modal on ESC key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeOfferModal();
    }
});

// buy now script

function buyNow(productId) {
    document.getElementById('buyProductId').value = productId;
    document.getElementById('buyNowModal').classList.add('active');
    document.getElementById('buyNowModal').style.display = 'flex';
}

function closeBuyNowModal() {
    document.getElementById('buyNowModal').classList.remove('active');
    document.getElementById('buyNowModal').style.display = 'none';
    document.getElementById('buyNowForm').reset();
}

function submitBuyNow(event) {
    event.preventDefault();
    
    const form = document.getElementById('buyNowForm');
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    
    // Show loading state
    submitBtn.textContent = 'Processing...';
    submitBtn.disabled = true;
    
    fetch('/order/create', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Purchase Successful! 🎉',
                text: 'Your order has been placed. You will receive a confirmation email shortly.',
                confirmButtonColor: '#38A169',
                timer: 5000,
                timerProgressBar: true
            });
            closeBuyNowModal();
            // Optionally redirect to orders page
            setTimeout(() => {
                window.location.href = '/orders';
            }, 2000);
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Purchase Failed',
                text: data.error || data.message || 'Failed to process your order',
                confirmButtonColor: '#38A169'
            });
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'An error occurred. Please try again.',
            confirmButtonColor: '#38A169'
        });
        console.error('Error:', error);
    })
    .finally(() => {
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    });
}

// Close buy modal on click outside
window.onclick = function(event) {
    const offerModal = document.getElementById('offerModal');
    const buyModal = document.getElementById('buyNowModal');
    
    if (event.target === offerModal) {
        closeOfferModal();
    }
    if (event.target === buyModal) {
        closeBuyNowModal();
    }
}

// Close modals on ESC key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeOfferModal();
        closeBuyNowModal();
    }
});
</script>