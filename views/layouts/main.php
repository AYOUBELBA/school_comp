<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marketplace</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <meta name="csrf-token" content="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header-inner">
                <a href="/" class="logo">Market<span>Place</span></a>
                
                <div class="search-bar">
                    <input type="text" placeholder="Search for products..." id="search-input">
                    <button onclick="performSearch()">Search</button>
                </div>
                
                <nav class="nav-links">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="/dashboard">Dashboard</a>
                        <a href="/offers">Offers</a>
                        <a href="/chat">Chat</a>
                        <span>Welcome, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></span>
                        <a href="/logout" class="btn">Logout</a>
                    <?php else: ?>
                        <a href="/login">Login</a>
                        <a href="/register" class="btn">Register</a>
                    <?php endif; ?>
                </nav>
            </div>
        </div>
    </header>

    <main>
        <?php if (isset($_SESSION['flash'])): ?>
            <div class="flash-messages">
                <?php foreach ($_SESSION['flash'] as $key => $message): ?>
                    <div class="flash-<?php echo $key; ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endforeach; ?>
                <?php unset($_SESSION['flash']); ?>
            </div>
        <?php endif; ?>
        
        <?php echo $content ?? ''; ?>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2024 Marketplace. All rights reserved.</p>
        </div>
    </footer>

    <script src="/assets/js/app.js"></script>
</body>
</html>