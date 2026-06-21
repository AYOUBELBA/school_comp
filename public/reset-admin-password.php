<?php
// public/reset-admin-password.php

try {
    $pdo = new PDO('mysql:host=localhost;dbname=marketplace', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h1>Reset Admin Password</h1>";
    
    // Check if admin exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute(['admin@marketplace.com']);
    $admin = $stmt->fetch();
    
    if ($admin) {
        // Reset password
        $newPassword = 'admin123';
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->execute([$hashedPassword, 'admin@marketplace.com']);
        
        echo "<p style='color: green;'>✅ Admin password reset successfully!</p>";
        echo "<p><strong>Email:</strong> admin@marketplace.com</p>";
        echo "<p><strong>Password:</strong> {$newPassword}</p>";
        
        // Verify the new password
        $stmt = $pdo->prepare("SELECT password FROM users WHERE email = ?");
        $stmt->execute(['admin@marketplace.com']);
        $result = $stmt->fetch();
        
        if (password_verify($newPassword, $result['password'])) {
            echo "<p style='color: green;'>✅ Password verification successful!</p>";
        } else {
            echo "<p style='color: red;'>❌ Password verification failed!</p>";
        }
    } else {
        // Create admin if doesn't exist
        echo "<p>Admin user not found. Creating...</p>";
        
        $newPassword = 'admin123';
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("
            INSERT INTO users (email, password, full_name, username, role, is_verified) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            'admin@marketplace.com',
            $hashedPassword,
            'Admin User',
            'admin',
            'admin',
            1
        ]);
        
        echo "<p style='color: green;'>✅ Admin user created!</p>";
        echo "<p><strong>Email:</strong> admin@marketplace.com</p>";
        echo "<p><strong>Password:</strong> {$newPassword}</p>";
    }
    
    // Show all users
    echo "<h2>All Users:</h2>";
    $users = $pdo->query("SELECT id, email, username, role FROM users")->fetchAll();
    echo "<table border='1' cellpadding='8'>";
    echo "<tr><th>ID</th><th>Email</th><th>Username</th><th>Role</th></tr>";
    foreach ($users as $user) {
        echo "<tr>";
        echo "<td>{$user['id']}</td>";
        echo "<td>{$user['email']}</td>";
        echo "<td>{$user['username']}</td>";
        echo "<td>{$user['role']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<p><a href='/login'>Go to Login</a></p>";
    
} catch (PDOException $e) {
    echo "<h2 style='color: red;'>❌ Error: " . $e->getMessage() . "</h2>";
}