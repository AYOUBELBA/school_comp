<?php
// views/admin/users.php
$users = $users ?? [];
?>

<div class="admin-container" style="padding: 20px 0; max-width: 1400px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
        <h1 style="font-size: 28px; font-weight: 800; margin: 0; color: #1A202C;">👥 User Management</h1>
        <div style="display: flex; gap: 8px;">
            <a href="/admin/products" style="padding: 10px 24px; background: #EDF2F7; color: #4A5568; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 14px;">Products</a>
            <a href="/admin/users" style="padding: 10px 24px; background: #2C7A7B; color: white; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 14px;">Users</a>
            <a href="/admin/orders" style="padding: 10px 24px; background: #EDF2F7; color: #4A5568; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 14px;">Orders</a>
        </div>
    </div>
    
    <?php if (isset($_SESSION['flash']['success'])): ?>
        <div style="padding: 12px 16px; background: #C6F6D5; color: #22543D; border-radius: 8px; margin-bottom: 16px; border: 1px solid #9AE6B4;">
            <?php echo htmlspecialchars($_SESSION['flash']['success']); ?>
            <?php unset($_SESSION['flash']['success']); ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['flash']['error'])): ?>
        <div style="padding: 12px 16px; background: #FED7D7; color: #742A2A; border-radius: 8px; margin-bottom: 16px; border: 1px solid #FECACA;">
            <?php echo htmlspecialchars($_SESSION['flash']['error']); ?>
            <?php unset($_SESSION['flash']['error']); ?>
        </div>
    <?php endif; ?>
    
    <div style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; min-width: 1000px;">
                <thead style="background: #F7FAFC;">
                    <tr>
                        <th style="padding: 14px 16px; text-align: left; font-weight: 600; font-size: 12px; color: #718096; text-transform: uppercase; letter-spacing: 0.5px;">ID</th>
                        <th style="padding: 14px 16px; text-align: left; font-weight: 600; font-size: 12px; color: #718096; text-transform: uppercase; letter-spacing: 0.5px;">User</th>
                        <th style="padding: 14px 16px; text-align: left; font-weight: 600; font-size: 12px; color: #718096; text-transform: uppercase; letter-spacing: 0.5px;">Email</th>
                        <th style="padding: 14px 16px; text-align: left; font-weight: 600; font-size: 12px; color: #718096; text-transform: uppercase; letter-spacing: 0.5px;">Role</th>
                        <th style="padding: 14px 16px; text-align: left; font-weight: 600; font-size: 12px; color: #718096; text-transform: uppercase; letter-spacing: 0.5px;">Status</th>
                        <th style="padding: 14px 16px; text-align: left; font-weight: 600; font-size: 12px; color: #718096; text-transform: uppercase; letter-spacing: 0.5px;">Products</th>
                        <th style="padding: 14px 16px; text-align: left; font-weight: 600; font-size: 12px; color: #718096; text-transform: uppercase; letter-spacing: 0.5px;">Joined</th>
                        <th style="padding: 14px 16px; text-align: left; font-weight: 600; font-size: 12px; color: #718096; text-transform: uppercase; letter-spacing: 0.5px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="8" style="text-align:center;padding:60px;color:#A0AEC0;font-size:16px;">
                                No users found
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                            <tr style="border-bottom: 1px solid #EDF2F7; <?php echo ($user['is_banned'] ?? false) ? 'background: #FFF5F5;' : ''; ?>">
                                <td style="padding: 14px 16px; font-weight: 500; color: #4A5568;">#<?php echo $user['id']; ?></td>
                                <td style="padding: 14px 16px;">
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <div style="width: 40px; height: 40px; border-radius: 50%; background: <?php echo ($user['is_banned'] ?? false) ? '#E53E3E' : '#2C7A7B'; ?>; color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 16px;">
                                            <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                                        </div>
                                        <span style="font-weight: 500; <?php echo ($user['is_banned'] ?? false) ? 'text-decoration: line-through; color: #A0AEC0;' : ''; ?>">
                                            <?php echo htmlspecialchars($user['full_name']); ?>
                                        </span>
                                    </div>
                                </td>
                                <td style="padding: 14px 16px; color: #4A5568;"><?php echo htmlspecialchars($user['email']); ?></td>
                                <td style="padding: 14px 16px;">
                                    <span style="padding: 4px 12px; border-radius: 9999px; font-size: 12px; font-weight: 600; 
                                        <?php if (($user['role'] ?? '') == 'admin'): ?>background: #FED7D7; color: #742A2A;
                                        <?php elseif (($user['role'] ?? '') == 'seller'): ?>background: #FEFCBF; color: #744210;
                                        <?php else: ?>background: #C6F6D5; color: #22543D;<?php endif; ?>">
                                        <?php echo ucfirst($user['role'] ?? 'buyer'); ?>
                                    </span>
                                </td>
                                <td style="padding: 14px 16px;">
                                    <?php if ($user['is_banned'] ?? false): ?>
                                        <span style="padding: 4px 12px; border-radius: 9999px; font-size: 12px; font-weight: 600; background: #FED7D7; color: #742A2A;">
                                            🚫 Banned
                                        </span>
                                    <?php else: ?>
                                        <span style="padding: 4px 12px; border-radius: 9999px; font-size: 12px; font-weight: 600; background: #C6F6D5; color: #22543D;">
                                            ✅ Active
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 14px 16px; text-align: center;"><?php echo $user['total_products'] ?? 0; ?></td>
                                <td style="padding: 14px 16px; font-size: 14px; color: #718096;"><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                <td style="padding: 14px 16px;">
                                    <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                                        <a href="/admin/user/edit/<?php echo $user['id']; ?>" 
                                           style="padding: 6px 14px; background: #EDF2F7; color: #4A5568; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 600; transition: all 0.2s;"
                                           onmouseover="this.style.background='#E2E8F0'">✏️ Edit</a>
                                        <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                            <?php if ($user['is_banned'] ?? false): ?>
                                                <a href="/admin/user/unban/<?php echo $user['id']; ?>" 
                                                   style="padding: 6px 14px; background: #38A169; color: white; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 600; transition: all 0.2s;"
                                                   onmouseover="this.style.background='#2F855A'">✅ Unban</a>
                                            <?php else: ?>
                                                <a href="/admin/user/ban/<?php echo $user['id']; ?>" 
                                                   style="padding: 6px 14px; background: #E53E3E; color: white; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 600; transition: all 0.2s;"
                                                   onclick="return confirm('Ban this user?\n\nUser: <?php echo addslashes($user['full_name']); ?>\nThey will not be able to login or use the site.')"
                                                   onmouseover="this.style.background='#C53030'">🚫 Ban</a>
                                            <?php endif; ?>
                                            <a href="/admin/user/delete/<?php echo $user['id']; ?>" 
                                               style="padding: 6px 14px; background: #E53E3E; color: white; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 600; transition: all 0.2s;" 
                                               onclick="return confirm('Are you sure you want to DELETE this user?\n\nUser: <?php echo addslashes($user['full_name']); ?>\nThis will delete ALL their data!\nThis action cannot be undone!')"
                                               onmouseover="this.style.background='#C53030'">🗑️ Delete</a>
                                        <?php else: ?>
                                            <span style="padding: 6px 14px; background: #EDF2F7; color: #A0AEC0; border-radius: 6px; font-size: 12px; font-weight: 600;">You</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>