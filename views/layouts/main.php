<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ReCircle - Marketplace for Conscious Consumers</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Load CSS -->
    <link rel="stylesheet" href="/assets/css/modern-style.css">
    
    <!-- Fallback inline styles if CSS doesn't load -->
    <style>
        /* Critical fallback styles */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; 
            background: #F7FAFC; 
            color: #1A202C;
            line-height: 1.6;
        }
        .container { max-width: 1280px; margin: 0 auto; padding: 0 20px; }
        .modern-header { 
            background: white; 
            border-bottom: 1px solid #E2E8F0; 
            position: sticky; 
            top: 0; 
            z-index: 1000;
        }
        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 0;
            gap: 16px;
            flex-wrap: wrap;
        }
        .logo {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 24px;
            font-weight: 800;
            color: #2C7A7B;
            text-decoration: none;
        }
        .logo-icon {
            width: 32px;
            height: 32px;
            background: #2C7A7B;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }
        .logo span { color: #1A5C5C; }
        .modern-nav { display: flex; gap: 16px; align-items: center; }
        .modern-nav a {
            color: #4A5568;
            text-decoration: none;
            font-weight: 500;
            padding: 6px 12px;
            border-radius: 8px;
            transition: all 0.2s;
        }
        .modern-nav a:hover { color: #2C7A7B; background: #EDF2F7; }
        .modern-nav a.active { color: #2C7A7B; background: #EDF2F7; }
        .search-bar-modern {
            flex: 1;
            max-width: 500px;
            position: relative;
        }
        .search-bar-modern input {
            width: 100%;
            padding: 10px 16px 10px 44px;
            border: 2px solid #E2E8F0;
            border-radius: 9999px;
            font-size: 14px;
            transition: all 0.3s;
            background: #F7FAFC;
        }
        .search-bar-modern input:focus {
            outline: none;
            border-color: #2C7A7B;
            background: white;
        }
        .search-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #A0AEC0;
        }
        .nav-actions { display: flex; align-items: center; gap: 8px; }
        .nav-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 9999px;
            color: #4A5568;
            transition: all 0.2s;
            cursor: pointer;
            border: none;
            background: none;
            position: relative;
            text-decoration: none;
        }
        .nav-icon:hover { background: #EDF2F7; color: #2C7A7B; }
        .nav-icon svg { width: 22px; height: 22px; }
        .nav-badge {
            position: absolute;
            top: -2px;
            right: -2px;
            background: #E53E3E;
            color: white;
            font-size: 10px;
            font-weight: 700;
            min-width: 18px;
            height: 18px;
            border-radius: 9999px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 4px;
        }
        .auth-buttons { display: flex; gap: 8px; }
        .btn-modern {
            padding: 8px 20px;
            border: none;
            border-radius: 9999px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-modern-primary { background: #2C7A7B; color: white; }
        .btn-modern-primary:hover { background: #1A5C5C; transform: translateY(-1px); }
        .btn-modern-outline { background: transparent; color: #4A5568; border: 2px solid #E2E8F0; }
        .btn-modern-outline:hover { border-color: #2C7A7B; color: #2C7A7B; }
        .user-menu { position: relative; display: flex; align-items: center; cursor: pointer; }
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 9999px;
            background: #2C7A7B;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.2s;
            border: 2px solid transparent;
            cursor: pointer;
        }
        .user-avatar:hover { border-color: #2C7A7B; transform: scale(1.05); }
        .dropdown-menu {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
            min-width: 240px;
            padding: 8px 0;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s;
            border: 1px solid #E2E8F0;
            z-index: 1000;
        }
        .dropdown-menu.open { opacity: 1; visibility: visible; transform: translateY(0); }
        .dropdown-header { padding: 12px 16px; border-bottom: 1px solid #E2E8F0; }
        .dropdown-header .user-name { font-weight: 600; color: #1A202C; }
        .dropdown-header .user-email { font-size: 12px; color: #A0AEC0; }
        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 16px;
            color: #4A5568;
            transition: all 0.2s;
            cursor: pointer;
            border: none;
            background: none;
            width: 100%;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
        }
        .dropdown-item:hover { background: #F7FAFC; color: #2C7A7B; }
        .dropdown-divider { height: 1px; background: #E2E8F0; margin: 8px 0; }
        @media (max-width: 1024px) {
            .search-bar-modern { order: 3; flex-basis: 100%; max-width: 100%; }
            .modern-nav { display: none; }
        }
        @media (max-width: 768px) {
            .nav-actions { gap: 2px; }
            .nav-icon { width: 36px; height: 36px; }
            .nav-icon svg { width: 18px; height: 18px; }
            .user-avatar { width: 34px; height: 34px; font-size: 14px; }
        }
    </style>
    
    <meta name="csrf-token" content="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
</head>
<body>
    <!-- Modern Header -->
    <header class="modern-header">
        <div class="container">
            <div class="header-content">
                <!-- Logo -->
                <a href="/" class="logo">
                    <div class="logo-icon">♻️</div>
                    Re<span>Circle</span>
                </a>
                
                <!-- Navigation -->
                <nav class="modern-nav">
                    <a href="/" class="<?php echo $currentPage === 'home' ? 'active' : ''; ?>">Browse</a>
                    <a href="/categories">Categories</a>
                    <a href="/how-it-works">How it works</a>
                </nav>
                
                <!-- Search -->
                <div class="search-bar-modern">
                    <span class="search-icon">🔍</span>
                    <input type="text" placeholder="Search for items..." id="searchInput" 
                           onkeypress="if(event.key==='Enter') window.location='/products?search='+encodeURIComponent(this.value)">
                </div>
                
                <!-- Auth / User Menu -->
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="nav-actions">
                        <!-- Dashboard Icon -->
                        <a href="/dashboard" class="nav-icon" title="Dashboard">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="3" width="7" height="7" rx="1"/>
                                <rect x="14" y="3" width="7" height="7" rx="1"/>
                                <rect x="3" y="14" width="7" height="7" rx="1"/>
                                <rect x="14" y="14" width="7" height="7" rx="1"/>
                            </svg>
                        </a>
                        
                        <!-- Chat Icon -->
                        <a href="/chat" class="nav-icon" title="Messages" id="chatNavLink">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>
    </svg>
    <span class="nav-badge" id="unreadBadge" style="display: none;">0</span>
</a>
                        
                        <!-- Favorites Icon -->
                        <a href="/favorites" class="nav-icon" title="Favorites">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/>
                            </svg>
                        </a>
                        
                        <!-- User Menu -->
                        <div class="user-menu" id="userMenu">
                            <div class="user-avatar" onclick="toggleDropdown()">
                                <?php 
                                $initial = strtoupper(substr($_SESSION['user_name'] ?? 'U', 0, 1));
                                echo $initial;
                                ?>
                            </div>
                            
                            <!-- Dropdown -->
                            <div class="dropdown-menu" id="dropdownMenu">
                                <div class="dropdown-header">
                                    <div class="user-name"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></div>
                                    <div class="user-email"><?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?></div>
                                </div>
                                
                                <a href="/dashboard" class="dropdown-item">
                                    <span class="icon">📊</span> Dashboard
                                </a>
                                <a href="/offers" class="dropdown-item">
                                    <span class="icon">💰</span> Offers
                                </a>
                                <a href="/orders" class="dropdown-item">
                                    <span class="icon">📦</span> Orders
                                </a>
                                <a href="/favorites" class="dropdown-item">
                                    <span class="icon">❤️</span> Favorites
                                </a>
                                <a href="/chat" class="dropdown-item">
                                    <span class="icon">💬</span> Messages
                                </a>
                                
                                <div class="dropdown-divider"></div>
                                
                                <a href="/profile" class="dropdown-item">
                                    <span class="icon">⚙️</span> Settings
                                </a>
                                <a href="/logout" class="dropdown-item">
                                    <span class="icon">🚪</span> Logout
                                </a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="auth-buttons">
                        <a href="/login" class="btn-modern btn-modern-outline">Login</a>
                        <a href="/register" class="btn-modern btn-modern-primary">Sign up</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Flash Messages -->
    <?php if (isset($_SESSION['flash'])): ?>
        <div class="container" style="margin-top: 16px;">
            <?php foreach ($_SESSION['flash'] as $key => $message): ?>
                <div style="
                    padding: 12px 20px;
                    border-radius: 8px;
                    margin-bottom: 8px;
                    <?php echo $key === 'success' ? 'background: #C6F6D5; color: #22543D;' : ''; ?>
                    <?php echo $key === 'error' ? 'background: #FED7D7; color: #742A2A;' : ''; ?>
                ">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endforeach; ?>
            <?php unset($_SESSION['flash']); ?>
        </div>
    <?php endif; ?>

    <!-- Main Content -->
    <main>
        <div class="container">
            <?php echo $content ?? ''; ?>
        </div>
    </main>

    <!-- Footer -->
    <footer style="background: #1A202C; color: #A0AEC0; padding: 48px 0 24px; margin-top: 48px;">
        <div class="container">
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:24px;margin-bottom:24px;">
                <div>
                    <h4 style="color:white;margin-bottom:12px;">ReCircle</h4>
                    <p style="font-size:14px;">Give goods a second life. The premium marketplace for conscious consumers.</p>
                </div>
                <div>
                    <h4 style="color:white;margin-bottom:12px;">Shop</h4>
                    <a href="/products" style="color:#A0AEC0;display:block;margin-bottom:8px;font-size:14px;text-decoration:none;">Browse</a>
                    <a href="/categories" style="color:#A0AEC0;display:block;margin-bottom:8px;font-size:14px;text-decoration:none;">Categories</a>
                    <a href="/how-it-works" style="color:#A0AEC0;display:block;margin-bottom:8px;font-size:14px;text-decoration:none;">How it works</a>
                </div>
                <div>
                    <h4 style="color:white;margin-bottom:12px;">Support</h4>
                    <a href="/help" style="color:#A0AEC0;display:block;margin-bottom:8px;font-size:14px;text-decoration:none;">Help Center</a>
                    <a href="/terms" style="color:#A0AEC0;display:block;margin-bottom:8px;font-size:14px;text-decoration:none;">Terms of Service</a>
                    <a href="/privacy" style="color:#A0AEC0;display:block;margin-bottom:8px;font-size:14px;text-decoration:none;">Privacy Policy</a>
                </div>
            </div>
            <div style="border-top:1px solid #2D3748;padding-top:16px;text-align:center;font-size:14px;">
                &copy; <?php echo date('Y'); ?> ReCircle. All rights reserved. ♻️ Give goods a second life.
            </div>
        </div>
    </footer>

    <script>
        // Toggle dropdown
        function toggleDropdown() {
            const menu = document.getElementById('dropdownMenu');
            menu.classList.toggle('open');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const menu = document.getElementById('userMenu');
            const dropdown = document.getElementById('dropdownMenu');
            if (menu && dropdown && !menu.contains(event.target)) {
                dropdown.classList.remove('open');
            }
        });

        // Auto-hide flash messages
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(el => {
                el.style.transition = 'opacity 0.5s';
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 500);
            });
        }, 5000);

        // Fetch unread message count
function updateUnreadCount() {
    <?php if (isset($_SESSION['user_id'])): ?>
    fetch('/chat/unread-count')
        .then(response => response.json())
        .then(data => {
            const badge = document.getElementById('unreadBadge');
            if (data.count > 0) {
                badge.textContent = data.count > 99 ? '99+' : data.count;
                badge.style.display = 'flex';
                // Add pulse animation for new messages
                badge.style.animation = 'pulse 1.5s ease-in-out 3';
            } else {
                badge.style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error fetching unread count:', error);
        });
    <?php endif; ?>
}

// Update unread count every 10 seconds
<?php if (isset($_SESSION['user_id'])): ?>
setInterval(updateUnreadCount, 10000);
// Initial load
document.addEventListener('DOMContentLoaded', updateUnreadCount);
<?php endif; ?>
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>