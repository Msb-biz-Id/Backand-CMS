<?php

/**
 * Front Controller - Application Entry Point
 * Best Practice: Single entry point for all requests
 */

// Start output buffering
ob_start();

// Error reporting based on environment
if (getenv('APP_ENV') === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Define constants
define('DS', DIRECTORY_SEPARATOR);
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . DS . 'app');
define('PUBLIC_PATH', ROOT_PATH . DS . 'public');
define('CONFIG_PATH', ROOT_PATH . DS . 'config');
define('STORAGE_PATH', ROOT_PATH . DS . 'storage');

// Load autoloader
require_once APP_PATH . DS . 'core' . DS . 'Autoloader.php';
Autoloader::register();

// Load configuration
$config = require CONFIG_PATH . DS . 'config.php';

// Set timezone
date_default_timezone_set($config['app']['timezone']);

// Security Headers - Best Practice
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

// Add Content Security Policy in production
if ($config['app']['env'] === 'production') {
    header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.ckeditor.com https://challenges.cloudflare.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data: https:; connect-src 'self'");
}

// Performance: Enable GZIP compression if not already enabled by server
if ($config['performance']['gzip_compression'] && !ob_get_level()) {
    ob_start('ob_gzhandler');
}

// Error and Exception Handling
set_error_handler(function($errno, $errstr, $errfile, $errline) use ($config) {
    $error = [
        'type' => 'Error',
        'message' => $errstr,
        'file' => $errfile,
        'line' => $errline,
        'time' => date('Y-m-d H:i:s')
    ];
    
    // Log error
    if ($config['logging']['enabled']) {
        error_log(json_encode($error) . PHP_EOL, 3, $config['logging']['path'] . '/error.log');
    }
    
    // Display error only in development
    if ($config['app']['debug']) {
        echo "<h3>Error</h3>";
        echo "<p><strong>Message:</strong> {$errstr}</p>";
        echo "<p><strong>File:</strong> {$errfile}</p>";
        echo "<p><strong>Line:</strong> {$errline}</p>";
    } else {
        echo "<h3>An error occurred</h3>";
        echo "<p>Please try again later.</p>";
    }
    
    return true;
});

set_exception_handler(function($exception) use ($config) {
    $error = [
        'type' => 'Exception',
        'message' => $exception->getMessage(),
        'file' => $exception->getFile(),
        'line' => $exception->getLine(),
        'trace' => $exception->getTraceAsString(),
        'time' => date('Y-m-d H:i:s')
    ];
    
    // Log exception
    if ($config['logging']['enabled']) {
        error_log(json_encode($error) . PHP_EOL, 3, $config['logging']['path'] . '/exception.log');
    }
    
    http_response_code(500);
    
    // Display exception only in development
    if ($config['app']['debug']) {
        echo "<h3>Exception</h3>";
        echo "<p><strong>Message:</strong> {$exception->getMessage()}</p>";
        echo "<p><strong>File:</strong> {$exception->getFile()}</p>";
        echo "<p><strong>Line:</strong> {$exception->getLine()}</p>";
        echo "<pre>{$exception->getTraceAsString()}</pre>";
    } else {
        if (file_exists(APP_PATH . '/views/errors/500.php')) {
            require_once APP_PATH . '/views/errors/500.php';
        } else {
            echo "<h3>Internal Server Error</h3>";
            echo "<p>Something went wrong. Please try again later.</p>";
        }
    }
});

// Load routes
require_once ROOT_PATH . DS . 'routes' . DS . 'web.php';

// Dispatch router
try {
    Router::dispatch();
} catch (Exception $e) {
    if ($config['app']['debug']) {
        echo "<h3>Routing Error</h3>";
        echo "<p>{$e->getMessage()}</p>";
    } else {
        http_response_code(500);
        echo "<h3>Error</h3><p>Something went wrong.</p>";
    }
}

// Flush output buffer
ob_end_flush();
