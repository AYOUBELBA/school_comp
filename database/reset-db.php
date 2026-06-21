<?php
// database/reset-db.php

$host = 'localhost';
$dbname = 'marketplace';
$username = 'root';
$password = '';

try {
    // Connect without database
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Drop and recreate database
    echo "Dropping database '$dbname'...\n";
    $pdo->exec("DROP DATABASE IF EXISTS $dbname");
    echo "✅ Database dropped\n";
    
    echo "Creating database '$dbname'...\n";
    $pdo->exec("CREATE DATABASE $dbname");
    echo "✅ Database created\n";
    
    $pdo->exec("USE $dbname");
    
    // Read and execute schema
    $schemaFile = __DIR__ . '/schema.sql';
    if (!file_exists($schemaFile)) {
        die("❌ Schema file not found: $schemaFile\n");
    }
    
    $sql = file_get_contents($schemaFile);
    if (!$sql) {
        die("❌ Could not read schema file\n");
    }
    
    // Split queries
    $queries = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($queries as $query) {
        if (!empty($query)) {
            try {
                $pdo->exec($query);
            } catch (PDOException $e) {
                echo "⚠️  Warning: " . $e->getMessage() . "\n";
                echo "Query: " . substr($query, 0, 100) . "...\n";
            }
        }
    }
    
    echo "✅ Schema imported successfully\n";
    
    // Seed data
    if (file_exists(__DIR__ . '/seed.php')) {
        include __DIR__ . '/seed.php';
    }
    
    echo "\n✅ Database reset complete!\n";
    echo "Credentials:\n";
    echo "  Admin: admin@marketplace.com / admin123\n";
    echo "  Seller: seller@marketplace.com / seller123\n";
    echo "  Buyer: buyer@marketplace.com / buyer123\n";
    
} catch (PDOException $e) {
    die("❌ Error: " . $e->getMessage() . "\n");
}