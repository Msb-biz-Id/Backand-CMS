<?php

/**
 * Cache Helper Class
 * Best Practice: File-based caching with TTL support
 * Performance optimization for database queries and rendered views
 */
class Cache {
    private static $instance = null;
    private $config;
    private $cachePath;
    
    private function __construct() {
        $this->config = require __DIR__ . '/../../config/config.php';
        $this->cachePath = $this->config['cache']['path'];
        
        // Create cache directory if not exists
        if (!is_dir($this->cachePath)) {
            mkdir($this->cachePath, 0755, true);
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Get cache item
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null) {
        if (!$this->config['cache']['enabled']) {
            return $default;
        }
        
        $filename = $this->getCacheFilename($key);
        
        if (!file_exists($filename)) {
            return $default;
        }
        
        $data = unserialize(file_get_contents($filename));
        
        // Check if expired
        if ($data['expire'] !== 0 && $data['expire'] < time()) {
            $this->forget($key);
            return $default;
        }
        
        return $data['value'];
    }
    
    /**
     * Store cache item
     * @param string $key
     * @param mixed $value
     * @param int $ttl (seconds, 0 = forever)
     * @return bool
     */
    public function set($key, $value, $ttl = null) {
        if (!$this->config['cache']['enabled']) {
            return false;
        }
        
        $ttl = $ttl ?? $this->config['cache']['ttl'];
        $expire = $ttl === 0 ? 0 : time() + $ttl;
        
        $data = [
            'value' => $value,
            'expire' => $expire,
            'created' => time()
        ];
        
        $filename = $this->getCacheFilename($key);
        return file_put_contents($filename, serialize($data)) !== false;
    }
    
    /**
     * Check if cache exists and is valid
     * @param string $key
     * @return bool
     */
    public function has($key) {
        if (!$this->config['cache']['enabled']) {
            return false;
        }
        
        $filename = $this->getCacheFilename($key);
        
        if (!file_exists($filename)) {
            return false;
        }
        
        $data = unserialize(file_get_contents($filename));
        
        // Check if expired
        if ($data['expire'] !== 0 && $data['expire'] < time()) {
            $this->forget($key);
            return false;
        }
        
        return true;
    }
    
    /**
     * Delete cache item
     * @param string $key
     * @return bool
     */
    public function forget($key) {
        $filename = $this->getCacheFilename($key);
        
        if (file_exists($filename)) {
            return unlink($filename);
        }
        
        return false;
    }
    
    /**
     * Clear all cache
     * @return bool
     */
    public function flush() {
        $files = glob($this->cachePath . '/*');
        
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        
        return true;
    }
    
    /**
     * Get cache item or execute callback and store result
     * @param string $key
     * @param callable $callback
     * @param int $ttl
     * @return mixed
     */
    public function remember($key, $callback, $ttl = null) {
        if ($this->has($key)) {
            return $this->get($key);
        }
        
        $value = $callback();
        $this->set($key, $value, $ttl);
        
        return $value;
    }
    
    /**
     * Get cache filename for key
     * @param string $key
     * @return string
     */
    private function getCacheFilename($key) {
        $prefix = $this->config['cache']['prefix'];
        $hash = md5($prefix . $key);
        return $this->cachePath . '/' . $hash . '.cache';
    }
    
    /**
     * Clean expired cache files
     * @return int Number of deleted files
     */
    public function cleanExpired() {
        $files = glob($this->cachePath . '/*.cache');
        $deleted = 0;
        
        foreach ($files as $file) {
            $data = unserialize(file_get_contents($file));
            
            if ($data['expire'] !== 0 && $data['expire'] < time()) {
                unlink($file);
                $deleted++;
            }
        }
        
        return $deleted;
    }
    
    /**
     * Get cache statistics
     * @return array
     */
    public function stats() {
        $files = glob($this->cachePath . '/*.cache');
        $totalSize = 0;
        $count = 0;
        $expired = 0;
        
        foreach ($files as $file) {
            $count++;
            $totalSize += filesize($file);
            
            $data = unserialize(file_get_contents($file));
            if ($data['expire'] !== 0 && $data['expire'] < time()) {
                $expired++;
            }
        }
        
        return [
            'count' => $count,
            'size' => $totalSize,
            'size_formatted' => $this->formatBytes($totalSize),
            'expired' => $expired,
            'path' => $this->cachePath
        ];
    }
    
    /**
     * Format bytes to human readable format
     * @param int $bytes
     * @return string
     */
    private function formatBytes($bytes) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
