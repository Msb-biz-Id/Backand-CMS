<?php

/**
 * Autoloader Class
 * Best Practice: PSR-4 compliant autoloading
 */
class Autoloader {
    private static $directories = [
        'core' => __DIR__,
        'controllers' => __DIR__ . '/../controllers',
        'models' => __DIR__ . '/../models',
        'helpers' => __DIR__ . '/../helpers',
        'middleware' => __DIR__ . '/../middleware',
        'libraries' => __DIR__ . '/../libraries',
    ];
    
    /**
     * Register autoloader
     */
    public static function register() {
        spl_autoload_register([self::class, 'load']);
    }
    
    /**
     * Load class file
     * @param string $className
     */
    private static function load($className) {
        foreach (self::$directories as $directory) {
            $file = $directory . '/' . $className . '.php';
            
            if (file_exists($file)) {
                require_once $file;
                return;
            }
        }
    }
    
    /**
     * Add custom directory
     * @param string $name
     * @param string $path
     */
    public static function addDirectory($name, $path) {
        self::$directories[$name] = $path;
    }
}
