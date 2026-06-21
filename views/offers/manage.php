<?php
// views/offers/manage.php

$offers = $offers ?? [];
$isSeller = $isSeller ?? false;
?>

<div class="offers-container">
    <h1><?php echo $isSeller ? 'Manage Offers Received' : 'My Offers'; ?></h1>
    
    <?php if (isset($_SESSION['flash']['success'])): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($_SESSION['flash']['success']); ?>
            <?php unset($_SESSION['flash']['success']); ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['flash']['error'])): ?>
        <div class="alert alert-error">
            <?php echo htmlspecialchars($_SESSION['flash']['error']); ?>
            <?php unset($_SESSION['flash']['error']); ?>
        </div>
    <?php endif; ?>
    
    <?php if (empty($offers)): ?>
        <div class="empty-state">
            <p>No offers found.</p>
            <?php if (!$isSeller): ?>
                <a href="/products" class="btn btn-primary">Browse Products</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="offers-grid">
            <?php foreach ($offers as $offer): ?>
                <div class="offer-card">
                    <div class="offer-header">
                        <?php if (!empty($offer['product_image'])): ?>
                            <img src="/uploads/products/<?php echo htmlspecialchars($offer['product_image']); ?>" 
                                 alt="<?php echo htmlspecialchars($offer['product_title']); ?>" 
                                 class="offer-product-image">
                        <?php endif; ?>
                        <div class="offer-product-info">
                            <h3><?php echo htmlspecialchars($offer['product_title']); ?></h3>
                            <p>Original Price: $<?php echo number_format($offer['original_price'] ?? 0, 2); ?></p>
                        </div>
                    </div>
                    
                    <div class="offer-details">
                        <div class="offer-price">
                            <span class="label">Offer Price:</span>
                            <span class="value">$<?php echo number_format($offer['offered_price'], 2); ?></span>
                        </div>
                        
                        <?php if ($offer['status'] === 'countered' && !empty($offer['counter_offer_price'])): ?>
                            <div class="offer-counter">
                                <span class="label">Counter Offer:</span>
                                <span class="value">$<?php echo number_format($offer['counter_offer_price'], 2); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <div class="offer-status status-<?php echo $offer['status']; ?>">
                            Status: <?php echo ucfirst($offer['status']); ?>
                        </div>
                        
                        <?php if (!empty($offer['message'])): ?>
                            <div class="offer-message">
                                <strong>Message:</strong>
                                <p><?php echo htmlspecialchars($offer['message']); ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <div class="offer-meta">
                            <span class="offer-date">
                                <?php echo date('M d, Y h:i A', strtotime($offer['created_at'])); ?>
                            </span>
                            <span class="offer-participant">
                                <?php if ($isSeller): ?>
                                    From: <?php echo htmlspecialchars($offer['buyer_name']); ?>
                                <?php else: ?>
                                    To: <?php echo htmlspecialchars($offer['seller_name']); ?>
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>
                    
                    <!-- Seller Actions -->
                    <?php if ($isSeller && $offer['status'] === 'pending'): ?>
                        <div class="offer-actions">
                            <a href="/offer/accept/<?php echo $offer['id']; ?>" class="btn btn-success">Accept</a>
                            <a href="/offer/reject/<?php echo $offer['id']; ?>" class="btn btn-danger">Reject</a>
                            <button onclick="showCounterOffer(<?php echo $offer['id']; ?>)" class="btn btn-secondary">Counter</button>
                        </div>
                        
                        <div id="counter-<?php echo $offer['id']; ?>" class="counter-form" style="display: none;">
                            <form action="/offer/counter/<?php echo $offer['id']; ?>" method="POST">
                                <div class="form-group">
                                    <label for="counter_price_<?php echo $offer['id']; ?>">Your Counter Offer:</label>
                                    <input type="number" id="counter_price_<?php echo $offer['id']; ?>" 
                                           name="counter_price" step="0.01" min="0.01" 
                                           class="form-control" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Send Counter Offer</button>
                                <button type="button" onclick="hideCounterOffer(<?php echo $offer['id']; ?>)" 
                                        class="btn btn-secondary">Cancel</button>
                            </form>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Buyer Actions for Counter Offers -->
                    <?php if (!$isSeller && $offer['status'] === 'countered'): ?>
                        <div class="offer-actions">
                            <a href="/offer/accept-counter/<?php echo $offer['id']; ?>" class="btn btn-success">Accept Counter Offer</a>
                            <a href="/offer/reject-counter/<?php echo $offer['id']; ?>" class="btn btn-danger">Decline</a>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Chat Action -->
                    <?php if ($offer['status'] === 'pending' || $offer['status'] === 'countered'): ?>
                        <div class="offer-actions">
                            <a href="/chat?offer_id=<?php echo $offer['id']; ?>" class="btn btn-primary">💬 Chat</a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
function showCounterOffer(offerId) {
    document.getElementById('counter-' + offerId).style.display = 'block';
}

function hideCounterOffer(offerId) {
    document.getElementById('counter-' + offerId).style.display = 'none';
}
</script>

<style>
.offers-container {
    padding: 20px 0;
}

.offers-container h1 {
    margin-bottom: 24px;
}

.offers-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
    gap: 20px;
}

.offer-card {
    background: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: all 0.3s;
}

.offer-card:hover {
    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
}

.offer-header {
    display: flex;
    gap: 16px;
    margin-bottom: 16px;
    align-items: center;
}

.offer-product-image {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 8px;
}

.offer-product-info h3 {
    margin: 0 0 4px 0;
    font-size: 16px;
}

.offer-product-info p {
    margin: 0;
    color: #666;
    font-size: 14px;
}

.offer-details {
    padding: 12px 0;
    border-top: 1px solid #eee;
    border-bottom: 1px solid #eee;
}

.offer-price, .offer-counter {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
}

.offer-price .value {
    font-weight: bold;
    color: #0654ba;
    font-size: 18px;
}

.offer-counter .value {
    font-weight: bold;
    color: #fbbc04;
    font-size: 16px;
}

.offer-status {
    margin: 8px 0;
    font-weight: 500;
}

.status-pending {
    color: #fbbc04;
}

.status-accepted {
    color: #34a853;
}

.status-rejected {
    color: #ea4335;
}

.status-countered {
    color: #4285f4;
}

.offer-message {
    margin: 8px 0;
    padding: 8px 12px;
    background: #f8f9fa;
    border-radius: 4px;
}

.offer-message p {
    margin: 4px 0 0 0;
}

.offer-meta {
    display: flex;
    justify-content: space-between;
    font-size: 13px;
    color: #666;
    margin-top: 8px;
}

.offer-actions {
    display: flex;
    gap: 8px;
    margin-top: 16px;
    flex-wrap: wrap;
}

.btn {
    padding: 8px 16px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    text-decoration: none;
    font-size: 14px;
    transition: all 0.3s;
    display: inline-block;
}

.btn-success {
    background: #34a853;
    color: white;
}

.btn-success:hover {
    background: #2d9248;
}

.btn-danger {
    background: #ea4335;
    color: white;
}

.btn-danger:hover {
    background: #d33426;
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

.counter-form {
    margin-top: 12px;
    padding: 12px;
    background: #f8f9fa;
    border-radius: 6px;
}

.form-group {
    margin-bottom: 12px;
}

.form-group label {
    display: block;
    margin-bottom: 4px;
    font-size: 14px;
}

.form-control {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

.alert {
    padding: 12px 16px;
    border-radius: 6px;
    margin-bottom: 16px;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 8px;
}

@media (max-width: 768px) {
    .offers-grid {
        grid-template-columns: 1fr;
    }
}
</style>