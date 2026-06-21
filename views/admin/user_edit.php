<?php
// views/admin/user_edit.php
$user = $user ?? [];
?>

<div style="max-width: 700px; margin: 40px auto; padding: 0 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h1 style="font-size: 28px; font-weight: 800; margin: 0;">✏️ Edit User</h1>
        <a href="/admin/users" style="padding: 8px 16px; background: #EDF2F7; color: #4A5568; border-radius: 8px; text-decoration: none; font-weight: 600;">← Back</a>
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
    
    <div style="background: white; border-radius: 12px; padding: 30px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <!-- User Info Card -->
        <div style="background: #F7FAFC; border-radius: 8px; padding: 16px; margin-bottom: 24px; display: flex; align-items: center; gap: 16px;">
            <div style="width: 64px; height: 64px; border-radius: 50%; background: #2C7A7B; color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 24px;">
                <?php echo strtoupper(substr($user['full_name'] ?? 'U', 0, 1)); ?>
            </div>
            <div>
                <div style="font-weight: 600; font-size: 18px;"><?php echo htmlspecialchars($user['full_name'] ?? 'Unknown'); ?></div>
                <div style="color: #718096; font-size: 14px;"><?php echo htmlspecialchars($user['email'] ?? ''); ?></div>
                <div style="margin-top: 4px;">
                    <span style="padding: 2px 10px; border-radius: 9999px; font-size: 12px; font-weight: 600; 
                        <?php if (($user['role'] ?? '') == 'admin'): ?>background: #FED7D7; color: #742A2A;
                        <?php elseif (($user['role'] ?? '') == 'seller'): ?>background: #FEFCBF; color: #744210;
                        <?php else: ?>background: #C6F6D5; color: #22543D;<?php endif; ?>">
                        <?php echo ucfirst($user['role'] ?? 'buyer'); ?>
                    </span>
                    <?php if ($user['is_banned'] ?? false): ?>
                        <span style="padding: 2px 10px; border-radius: 9999px; font-size: 12px; font-weight: 600; background: #FED7D7; color: #742A2A;">
                            🚫 Banned
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <form action="/admin/user/update/<?php echo $user['id']; ?>" method="POST">
            <!-- Basic Info -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 6px; font-weight: 600; color: #4A5568;">Full Name</label>
                <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>" 
                       style="width: 100%; padding: 12px 16px; border: 2px solid #E2E8F0; border-radius: 8px; font-size: 16px; transition: all 0.3s;" 
                       required>
            </div>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 6px; font-weight: 600; color: #4A5568;">Email</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" 
                       style="width: 100%; padding: 12px 16px; border: 2px solid #E2E8F0; border-radius: 8px; font-size: 16px; transition: all 0.3s;" 
                       required>
            </div>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 6px; font-weight: 600; color: #4A5568;">Username</label>
                <input type="text" name="username" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" 
                       style="width: 100%; padding: 12px 16px; border: 2px solid #E2E8F0; border-radius: 8px; font-size: 16px; transition: all 0.3s;" 
                       required>
            </div>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 6px; font-weight: 600; color: #4A5568;">Role</label>
                <select name="role" style="width: 100%; padding: 12px 16px; border: 2px solid #E2E8F0; border-radius: 8px; font-size: 16px; background: white;">
                    <option value="buyer" <?php echo ($user['role'] ?? '') == 'buyer' ? 'selected' : ''; ?>>Buyer</option>
                    <option value="seller" <?php echo ($user['role'] ?? '') == 'seller' ? 'selected' : ''; ?>>Seller</option>
                    <option value="admin" <?php echo ($user['role'] ?? '') == 'admin' ? 'selected' : ''; ?>>Admin</option>
                </select>
            </div>
            
            <!-- Change Password -->
            <div style="background: #F7FAFC; border-radius: 8px; padding: 16px; margin-bottom: 20px;">
                <h3 style="font-size: 16px; font-weight: 600; margin: 0 0 12px 0; color: #4A5568;">🔑 Change Password</h3>
                <div style="margin-bottom: 12px;">
                    <label style="display: block; margin-bottom: 6px; font-weight: 500; color: #4A5568; font-size: 14px;">New Password</label>
                    <input type="password" name="password" placeholder="Enter new password (leave blank to keep current)" 
                           style="width: 100%; padding: 12px 16px; border: 2px solid #E2E8F0; border-radius: 8px; font-size: 16px; transition: all 0.3s;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 6px; font-weight: 500; color: #4A5568; font-size: 14px;">Confirm New Password</label>
                    <input type="password" name="password_confirm" placeholder="Confirm new password" 
                           style="width: 100%; padding: 12px 16px; border: 2px solid #E2E8F0; border-radius: 8px; font-size: 16px; transition: all 0.3s;">
                </div>
            </div>
            
            <!-- Ban/Unban -->
            <div style="background: <?php echo ($user['is_banned'] ?? false) ? '#FED7D7' : '#F7FAFC'; ?>; border-radius: 8px; padding: 16px; margin-bottom: 20px; border: 2px solid <?php echo ($user['is_banned'] ?? false) ? '#FECACA' : '#E2E8F0'; ?>;">
                <h3 style="font-size: 16px; font-weight: 600; margin: 0 0 12px 0; color: <?php echo ($user['is_banned'] ?? false) ? '#742A2A' : '#4A5568'; ?>;">
                    <?php echo ($user['is_banned'] ?? false) ? '🚫 User is Banned' : '🔓 User Status'; ?>
                </h3>
                
                <?php if ($user['is_banned'] ?? false): ?>
                    <div style="margin-bottom: 12px; padding: 8px 12px; background: white; border-radius: 6px; font-size: 14px;">
                        <strong>Ban Reason:</strong> <?php echo htmlspecialchars($user['ban_reason'] ?? 'No reason provided'); ?>
                        <br>
                        <strong>Banned On:</strong> <?php echo date('M d, Y h:i A', strtotime($user['banned_at'] ?? 'now')); ?>
                    </div>
                    <button type="submit" name="action" value="unban" 
                            style="padding: 10px 24px; background: #38A169; color: white; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.3s;"
                            onmouseover="this.style.background='#2F855A'" onmouseout="this.style.background='#38A169'">
                        ✅ Unban User
                    </button>
                <?php else: ?>
                    <div style="margin-bottom: 12px;">
                        <label style="display: block; margin-bottom: 6px; font-weight: 500; color: #4A5568; font-size: 14px;">Ban Reason</label>
                        <textarea name="ban_reason" placeholder="Why are you banning this user?" 
                                  style="width: 100%; padding: 12px 16px; border: 2px solid #E2E8F0; border-radius: 8px; font-size: 14px; resize: vertical; min-height: 60px;"></textarea>
                    </div>
                    <button type="submit" name="action" value="ban" 
                            style="padding: 10px 24px; background: #E53E3E; color: white; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.3s;"
                            onmouseover="this.style.background='#C53030'" onmouseout="this.style.background='#E53E3E'">
                        🚫 Ban User
                    </button>
                <?php endif; ?>
            </div>
            
            <!-- Action Buttons -->
            <div style="display: flex; gap: 12px; margin-top: 20px; flex-wrap: wrap;">
                <button type="submit" name="action" value="update" 
                        style="padding: 12px 32px; background: #2C7A7B; color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s; flex: 1;"
                        onmouseover="this.style.background='#1A5C5C'" onmouseout="this.style.background='#2C7A7B'">
                    💾 Update User
                </button>
                <?php if ($user['id'] != $_SESSION['user_id']): ?>
                    <a href="/admin/user/delete/<?php echo $user['id']; ?>" 
                       style="padding: 12px 32px; background: #E53E3E; color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; text-decoration: none; text-align: center; transition: all 0.3s;"
                       onclick="return confirm('Are you sure you want to DELETE this user?\n\nUser: <?php echo addslashes($user['full_name'] ?? ''); ?>\nThis will delete ALL their data!\nThis action cannot be undone!')"
                       onmouseover="this.style.background='#C53030'" onmouseout="this.style.background='#E53E3E'">
                        🗑️ Delete User
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>