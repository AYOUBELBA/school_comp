<?php
// views/orders/index.php

$orders = $orders ?? [];
$isSeller = isset($_SESSION['user_role']) && ($_SESSION['user_role'] === 'seller' || $_SESSION['user_role'] === 'admin');
?>

<div class="orders-page">
    <h1 class="page-title"><?php echo $isSeller ? '📦 Orders Received' : '📦 My Orders'; ?></h1>
    
    <?php if (empty($orders)): ?>
        <div class="empty-state-modern">
            <div class="empty-icon">📭</div>
            <h2>No orders yet</h2>
            <p><?php echo $isSeller ? 'You haven\'t received any orders yet.' : 'You haven\'t placed any orders yet.'; ?></p>
            <a href="/products" class="btn-modern btn-modern-primary">Browse Products</a>
        </div>
    <?php else: ?>
        <div class="orders-grid">
            <?php foreach ($orders as $order): ?>
                <div class="order-card">
                    <div class="order-header">
                        <div class="order-product">
                            <?php if (!empty($order['product_image'])): ?>
                                <img src="/uploads/products/<?php echo htmlspecialchars($order['product_image']); ?>" 
                                     alt="<?php echo htmlspecialchars($order['product_title']); ?>" 
                                     class="order-product-image">
                            <?php endif; ?>
                            <div>
                                <h3><?php echo htmlspecialchars($order['product_title']); ?></h3>
                                <span class="order-participant">
                                    <?php echo $isSeller ? 'Buyer: ' . htmlspecialchars($order['buyer_name']) : 'Seller: ' . htmlspecialchars($order['seller_name']); ?>
                                </span>
                            </div>
                        </div>
                        <span class="order-status status-<?php echo $order['status']; ?>">
                            <?php echo ucfirst($order['status']); ?>
                        </span>
                    </div>
                    
                    <div class="order-details">
                        <div class="order-amount">
                            <span class="label">Amount:</span>
                            <span class="value">$<?php echo number_format($order['amount'], 2); ?></span>
                        </div>
                        <div class="order-date">
                            <span class="label">Date:</span>
                            <span class="value"><?php echo date('M d, Y h:i A', strtotime($order['created_at'])); ?></span>
                        </div>
                        <?php if (!empty($order['shipping_address'])): ?>
                            <div class="order-address">
                                <span class="label">Shipping Address:</span>
                                <span class="value"><?php echo htmlspecialchars($order['shipping_address']); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.orders-page {
    padding: var(--space-xl) 0;
}

.orders-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: var(--space-lg);
}

.order-card {
    background: var(--white);
    border-radius: var(--border-radius-lg);
    padding: var(--space-lg);
    box-shadow: var(--shadow-sm);
    transition: all 0.3s;
}

.order-card:hover {
    box-shadow: var(--shadow-md);
}

.order-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-bottom: var(--space-md);
    border-bottom: 1px solid var(--gray-200);
}

.order-product {
    display: flex;
    align-items: center;
    gap: var(--space-md);
}

.order-product-image {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: var(--border-radius-md);
}

.order-product h3 {
    font-size: var(--font-size-md);
    margin-bottom: var(--space-xs);
}

.order-participant {
    font-size: var(--font-size-sm);
    color: var(--gray-500);
}

.order-status {
    padding: 4px 12px;
    border-radius: var(--border-radius-full);
    font-size: var(--font-size-xs);
    font-weight: 600;
}

.status-pending {
    background: #FEFCBF;
    color: #744210;
}

.status-paid {
    background: #C6F6D5;
    color: #22543D;
}

.status-shipped {
    background: #BEE3F8;
    color: #2A4365;
}

.status-delivered {
    background: #C6F6D5;
    color: #22543D;
}

.status-completed {
    background: #C6F6D5;
    color: #22543D;
}

.status-cancelled {
    background: #FED7D7;
    color: #742A2A;
}

.order-details {
    padding-top: var(--space-md);
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: var(--space-sm);
}

.order-details .label {
    color: var(--gray-500);
    font-size: var(--font-size-sm);
}

.order-details .value {
    font-weight: 500;
    color: var(--gray-700);
}
</style>