<?php
// views/admin/products.php

$products = $products ?? [];
?>

<div class="admin-container" style="padding: 20px 0;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
        <h1 style="font-weight: 800; margin: 0;">📦 Product Management</h1>
        <div style="display: flex; gap: 10px;">
            <a href="/admin/products" style="padding: 8px 20px; background: #2C7A7B; color: white; border-radius: 8px; text-decoration: none;">Products</a>
            <a href="/admin/users" style="padding: 8px 20px; background: #EDF2F7; color: #4A5568; border-radius: 8px; text-decoration: none;">Users</a>
            <a href="/admin/orders" style="padding: 8px 20px; background: #EDF2F7; color: #4A5568; border-radius: 8px; text-decoration: none;">Orders</a>
        </div>
    </div>
    
    <?php if (isset($_SESSION['flash']['success'])): ?>
        <div style="padding: 12px 16px; background: #C6F6D5; color: #22543D; border-radius: 8px; margin-bottom: 16px;">
            <?php echo htmlspecialchars($_SESSION['flash']['success']); ?>
            <?php unset($_SESSION['flash']['success']); ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['flash']['error'])): ?>
        <div style="padding: 12px 16px; background: #FED7D7; color: #742A2A; border-radius: 8px; margin-bottom: 16px;">
            <?php echo htmlspecialchars($_SESSION['flash']['error']); ?>
            <?php unset($_SESSION['flash']['error']); ?>
        </div>
    <?php endif; ?>
    
    <div style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background: #F7FAFC;">
                <tr>
                    <th style="padding: 12px 16px; text-align: left; font-weight: 600; font-size: 12px; color: #718096; text-transform: uppercase;">ID</th>
                    <th style="padding: 12px 16px; text-align: left; font-weight: 600; font-size: 12px; color: #718096; text-transform: uppercase;">Product</th>
                    <th style="padding: 12px 16px; text-align: left; font-weight: 600; font-size: 12px; color: #718096; text-transform: uppercase;">Seller</th>
                    <th style="padding: 12px 16px; text-align: left; font-weight: 600; font-size: 12px; color: #718096; text-transform: uppercase;">Price</th>
                    <th style="padding: 12px 16px; text-align: left; font-weight: 600; font-size: 12px; color: #718096; text-transform: uppercase;">Status</th>
                    <th style="padding: 12px 16px; text-align: left; font-weight: 600; font-size: 12px; color: #718096; text-transform: uppercase;">Created</th>
                    <th style="padding: 12px 16px; text-align: left; font-weight: 600; font-size: 12px; color: #718096; text-transform: uppercase;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products)): ?>
                    <tr>
                        <td colspan="7" style="text-align:center;padding:40px;color:#A0AEC0;">
                            No products found
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($products as $product): ?>
                        <tr style="border-bottom: 1px solid #EDF2F7;">
                            <td style="padding: 12px 16px;">#<?php echo $product['id']; ?></td>
                            <td style="padding: 12px 16px;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <?php if (!empty($product['primary_image'])): ?>
                                        <img src="/uploads/products/<?php echo htmlspecialchars($product['primary_image']); ?>" 
                                             alt="<?php echo htmlspecialchars($product['title']); ?>" 
                                             style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                                    <?php endif; ?>
                                    <span><?php echo htmlspecialchars($product['title']); ?></span>
                                </div>
                            </td>
                            <td style="padding: 12px 16px;">
                                <div><?php echo htmlspecialchars($product['seller_name']); ?></div>
                                <small style="color: #A0AEC0; font-size: 12px;"><?php echo htmlspecialchars($product['seller_email']); ?></small>
                            </td>
                            <td style="padding: 12px 16px; font-weight: 600;">$<?php echo number_format($product['price'], 2); ?></td>
                            <td style="padding: 12px 16px;">
                                <span style="padding: 4px 12px; border-radius: 9999px; font-size: 12px; font-weight: 600; 
                                    <?php if ($product['status'] == 'active'): ?>background: #C6F6D5; color: #22543D;
                                    <?php elseif ($product['status'] == 'sold'): ?>background: #FED7D7; color: #742A2A;
                                    <?php else: ?>background: #FEFCBF; color: #744210;<?php endif; ?>">
                                    <?php echo ucfirst($product['status']); ?>
                                </span>
                            </td>
                            <td style="padding: 12px 16px; font-size: 14px;"><?php echo date('M d, Y', strtotime($product['created_at'])); ?></td>
                            <td style="padding: 12px 16px;">
                                <div style="display: flex; gap: 4px;">
                                    <a href="/product/<?php echo $product['id']; ?>" 
                                       style="padding: 4px 12px; background: #EDF2F7; color: #4A5568; border-radius: 4px; text-decoration: none; font-size: 12px; font-weight: 600;" 
                                       target="_blank">View</a>
                                    <a href="/admin/product/delete/<?php echo $product['id']; ?>" 
                                       style="padding: 4px 12px; background: #E53E3E; color: white; border-radius: 4px; text-decoration: none; font-size: 12px; font-weight: 600;" 
                                       onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>