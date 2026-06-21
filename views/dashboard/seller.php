<?php
// views/dashboard/seller.php

$stats = $stats ?? [];
$recentOrders = $recentOrders ?? [];
$recentOffers = $recentOffers ?? [];
$recentProducts = $recentProducts ?? [];
?>

<div class="dashboard-container">
    <h1>Seller Dashboard</h1>
    
    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number"><?php echo $stats['active_products'] ?? 0; ?></div>
            <div class="stat-label">Active Products</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo $stats['sold_products'] ?? 0; ?></div>
            <div class="stat-label">Sold Products</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">$<?php echo number_format($stats['total_earnings'] ?? 0, 2); ?></div>
            <div class="stat-label">Total Earnings</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo $stats['pending_orders'] ?? 0; ?></div>
            <div class="stat-label">Pending Orders</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo $stats['pending_offers'] ?? 0; ?></div>
            <div class="stat-label">Pending Offers</div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="quick-actions">
        <a href="/product/create" class="btn btn-primary">📦 Create New Listing</a>
        <a href="/products" class="btn btn-secondary">👁️ View All Products</a>
        <a href="/offers" class="btn btn-secondary">💰 Manage Offers</a>
    </div>
    
    <!-- Recent Products -->
    <section class="dashboard-section">
        <h2>Your Recent Products</h2>
        <?php if (!empty($recentProducts)): ?>
            <div class="product-grid">
                <?php foreach ($recentProducts as $product): ?>
                    <div class="product-card">
                        <?php if (!empty($product['primary_image'])): ?>
                            <img src="/uploads/products/<?php echo htmlspecialchars($product['primary_image']); ?>" 
                                 alt="<?php echo htmlspecialchars($product['title']); ?>" 
                                 class="product-image">
                        <?php endif; ?>
                        <div class="product-info">
                            <h3><?php echo htmlspecialchars($product['title']); ?></h3>
                            <div class="product-price">$<?php echo number_format($product['price'], 2); ?></div>
                            <div class="product-status status-<?php echo $product['status']; ?>">
                                <?php echo ucfirst($product['status']); ?>
                            </div>
                            <div class="product-actions">
                                <a href="/product/edit/<?php echo $product['id']; ?>" class="btn-small">Edit</a>
                                <a href="/product/delete/<?php echo $product['id']; ?>" class="btn-small btn-danger" 
                                   onclick="return confirm('Are you sure?')">Delete</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>You haven't listed any products yet. <a href="/product/create">Create your first listing!</a></p>
        <?php endif; ?>
    </section>
    
    <!-- Recent Orders -->
    <section class="dashboard-section">
        <h2>Recent Orders</h2>
        <?php if (!empty($recentOrders)): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Buyer</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentOrders as $order): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($order['product_title']); ?></td>
                            <td><?php echo htmlspecialchars($order['buyer_name']); ?></td>
                            <td>$<?php echo number_format($order['amount'], 2); ?></td>
                            <td><span class="status-<?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span></td>
                            <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No orders yet.</p>
        <?php endif; ?>
    </section>
    
    <!-- Recent Offers -->
    <section class="dashboard-section">
        <h2>Pending Offers</h2>
        <?php if (!empty($recentOffers)): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Buyer</th>
                        <th>Offer Price</th>
                        <th>Message</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentOffers as $offer): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($offer['product_title']); ?></td>
                            <td><?php echo htmlspecialchars($offer['buyer_name']); ?></td>
                            <td>$<?php echo number_format($offer['offered_price'], 2); ?></td>
                            <td><?php echo htmlspecialchars($offer['message'] ?? ''); ?></td>
                            <td>
                                <a href="/offer/accept/<?php echo $offer['id']; ?>" class="btn-small btn-success">Accept</a>
                                <a href="/offer/reject/<?php echo $offer['id']; ?>" class="btn-small btn-danger">Reject</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No pending offers.</p>
        <?php endif; ?>
    </section>
</div>

<style>
.dashboard-container {
    padding: 20px 0;
}

.dashboard-container h1 {
    margin-bottom: 24px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 16px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    text-align: center;
}

.stat-number {
    font-size: 32px;
    font-weight: bold;
    color: #0654ba;
}

.stat-label {
    font-size: 14px;
    color: #666;
    margin-top: 4px;
}

.quick-actions {
    display: flex;
    gap: 12px;
    margin-bottom: 30px;
    flex-wrap: wrap;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    text-decoration: none;
    font-size: 14px;
    transition: all 0.3s;
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

.btn-small {
    padding: 4px 12px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    text-decoration: none;
    font-size: 12px;
    transition: all 0.3s;
    display: inline-block;
}

.btn-success {
    background: #34a853;
    color: white;
}

.btn-danger {
    background: #ea4335;
    color: white;
}

.dashboard-section {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

.dashboard-section h2 {
    margin-bottom: 16px;
    font-size: 20px;
}

.table {
    width: 100%;
    border-collapse: collapse;
}

.table th {
    background: #f8f9fa;
    padding: 10px;
    text-align: left;
    font-weight: 600;
}

.table td {
    padding: 10px;
    border-bottom: 1px solid #eee;
}

.status-active {
    color: #34a853;
}

.status-sold {
    color: #ea4335;
}

.status-pending {
    color: #fbbc04;
}

.status-completed {
    color: #34a853;
}

.product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 16px;
}

.product-card {
    background: #f8f9fa;
    border-radius: 8px;
    overflow: hidden;
    transition: all 0.3s;
}

.product-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.product-image {
    width: 100%;
    height: 150px;
    object-fit: cover;
}

.product-info {
    padding: 12px;
}

.product-info h3 {
    font-size: 14px;
    margin: 0 0 8px 0;
}

.product-price {
    font-weight: bold;
    color: #0654ba;
    font-size: 16px;
}

.product-status {
    font-size: 12px;
    margin: 4px 0;
}

.product-actions {
    display: flex;
    gap: 8px;
    margin-top: 8px;
}
</style>