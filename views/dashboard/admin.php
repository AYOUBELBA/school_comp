<?php
// views/dashboard/admin.php

$stats = $stats ?? [];
$recentUsers = $recentUsers ?? [];
$recentProducts = $recentProducts ?? [];
?>

<div class="dashboard-container">
    <h1>Admin Dashboard</h1>
    
    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number"><?php echo $stats['total_users'] ?? 0; ?></div>
            <div class="stat-label">Total Users</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo $stats['total_sellers'] ?? 0; ?></div>
            <div class="stat-label">Total Sellers</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo $stats['total_products'] ?? 0; ?></div>
            <div class="stat-label">Total Products</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo $stats['active_products'] ?? 0; ?></div>
            <div class="stat-label">Active Products</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo $stats['total_orders'] ?? 0; ?></div>
            <div class="stat-label">Total Orders</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">$<?php echo number_format($stats['total_revenue'] ?? 0, 2); ?></div>
            <div class="stat-label">Total Revenue</div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="quick-actions">
        <a href="/admin/users" class="btn btn-primary">👥 Manage Users</a>
        <a href="/admin/products" class="btn btn-secondary">📦 Moderate Products</a>
        <a href="/admin/orders" class="btn btn-secondary">📋 View Orders</a>
    </div>
    
    <!-- Recent Users -->
    <section class="dashboard-section">
        <h2>Recent Users</h2>
        <?php if (!empty($recentUsers)): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentUsers as $user): ?>
                        <tr>
                            <td>#<?php echo $user['id']; ?></td>
                            <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><span class="role-badge role-<?php echo $user['role']; ?>"><?php echo ucfirst($user['role']); ?></span></td>
                            <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                            <td>
                                <a href="/admin/user/edit/<?php echo $user['id']; ?>" class="btn-small">Edit</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No users found.</p>
        <?php endif; ?>
    </section>
    
    <!-- Recent Products -->
    <section class="dashboard-section">
        <h2>Recent Products</h2>
        <?php if (!empty($recentProducts)): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Seller</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentProducts as $product): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($product['title']); ?></td>
                            <td><?php echo htmlspecialchars($product['seller_name']); ?></td>
                            <td>$<?php echo number_format($product['price'], 2); ?></td>
                            <td><span class="status-<?php echo $product['status']; ?>"><?php echo ucfirst($product['status']); ?></span></td>
                            <td><?php echo date('M d, Y', strtotime($product['created_at'])); ?></td>
                            <td>
                                <a href="/product/<?php echo $product['id']; ?>" class="btn-small">View</a>
                                <a href="/admin/product/delete/<?php echo $product['id']; ?>" class="btn-small btn-danger" 
                                   onclick="return confirm('Are you sure?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No products found.</p>
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

.btn-danger {
    background: #ea4335;
    color: white;
}

.btn-danger:hover {
    background: #d33426;
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

.role-badge {
    padding: 2px 10px;
    border-radius: 12px;
    font-size: 12px;
}

.role-admin {
    background: #ea4335;
    color: white;
}

.role-seller {
    background: #fbbc04;
    color: #333;
}

.role-buyer {
    background: #34a853;
    color: white;
}

.status-active {
    color: #34a853;
}

.status-sold {
    color: #ea4335;
}

.status-inactive {
    color: #666;
}

.status-pending {
    color: #fbbc04;
}
</style>