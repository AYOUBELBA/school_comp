#!/usr/bin/env php
<?php
// bin/chat-server.php

// Set up autoloading
require_once __DIR__ . '/../vendor/autoload.php';

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use WebSocket\ChatServer;

// Initialize database config
$configFile = __DIR__ . '/../config/database.php';
if (file_exists($configFile)) {
    require_once $configFile;
}

echo "Starting WebSocket Server...\n";
echo "Server will run on ws://localhost:8080\n";
echo "Press Ctrl+C to stop\n\n";

try {
    $server = IoServer::factory(
        new HttpServer(
            new WsServer(
                new ChatServer()
            )
        ),
        8080
    );
    
    $server->run();
} catch (\Exception $e) {
    echo "Error starting server: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}