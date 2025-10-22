<?php

/**
 * Database Installation Script
 * Run this file once to create all database tables
 */

// Load configuration
$config = require __DIR__ . '/../config/config.php';

// Database connection
$dbConfig = $config['database']['connections'][$config['database']['default']];

try {
    // Connect to MySQL server (without database)
    $dsn = sprintf(
        "%s:host=%s;port=%s;charset=%s",
        $dbConfig['driver'],
        $dbConfig['host'],
        $dbConfig['port'],
        $dbConfig['charset']
    );
    
    $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✓ Connected to MySQL server\n";
    
    // Create database if not exists
    $dbName = $dbConfig['database'];
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET {$dbConfig['charset']} COLLATE {$dbConfig['collation']}");
    echo "✓ Database '{$dbName}' created/verified\n";
    
    // Use database
    $pdo->exec("USE `{$dbName}`");
    
    // Get all migration files
    $migrationFiles = glob(__DIR__ . '/migrations/*.sql');
    sort($migrationFiles);
    
    echo "\n--- Running Migrations ---\n";
    
    foreach ($migrationFiles as $file) {
        $filename = basename($file);
        echo "Running: {$filename} ... ";
        
        $sql = file_get_contents($file);
        
        try {
            $pdo->exec($sql);
            echo "✓ Success\n";
        } catch (PDOException $e) {
            echo "✗ Failed: " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n✓ Database installation completed successfully!\n";
    echo "\nDefault Admin Credentials:\n";
    echo "Email: admin@example.com\n";
    echo "Password: admin123\n";
    echo "\n⚠ IMPORTANT: Please change the default password after first login!\n";
    
} catch (PDOException $e) {
    echo "✗ Database installation failed: " . $e->getMessage() . "\n";
    exit(1);
}
