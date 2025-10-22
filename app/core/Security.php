<?php

/**
 * Security Core Class
 * Best Practice: Comprehensive security implementation
 * Features: CSRF, XSS, SQL Injection prevention, Input validation, Sanitization
 */
class Security {
    private static $instance = null;
    private $config;
    
    private function __construct() {
        $this->config = require __DIR__ . '/../../config/config.php';
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Generate CSRF Token
     * Security: Prevent Cross-Site Request Forgery
     * @return string
     */
    public function generateCSRFToken() {
        if (!isset($_SESSION)) {
            session_start();
        }
        
        $token = bin2hex(random_bytes(32));
        $tokenName = $this->config['security']['csrf_token_name'];
        
        $_SESSION[$tokenName] = [
            'token' => $token,
            'expire' => time() + $this->config['security']['csrf_token_expire']
        ];
        
        return $token;
    }
    
    /**
     * Verify CSRF Token
     * @param string $token
     * @return bool
     */
    public function verifyCSRFToken($token) {
        if (!isset($_SESSION)) {
            session_start();
        }
        
        $tokenName = $this->config['security']['csrf_token_name'];
        
        if (!isset($_SESSION[$tokenName])) {
            return false;
        }
        
        $storedToken = $_SESSION[$tokenName];
        
        // Check if token expired
        if (time() > $storedToken['expire']) {
            unset($_SESSION[$tokenName]);
            return false;
        }
        
        // Constant time comparison to prevent timing attacks
        return hash_equals($storedToken['token'], $token);
    }
    
    /**
     * Sanitize input to prevent XSS attacks
     * Security: Cross-Site Scripting prevention
     * @param mixed $data
     * @param string $type
     * @return mixed
     */
    public function sanitize($data, $type = 'string') {
        if (is_array($data)) {
            return array_map(function($item) use ($type) {
                return $this->sanitize($item, $type);
            }, $data);
        }
        
        switch ($type) {
            case 'string':
                return htmlspecialchars(strip_tags($data), ENT_QUOTES, 'UTF-8');
            
            case 'html':
                // Allow specific HTML tags but sanitize attributes
                return $this->sanitizeHTML($data);
            
            case 'email':
                return filter_var($data, FILTER_SANITIZE_EMAIL);
            
            case 'url':
                return filter_var($data, FILTER_SANITIZE_URL);
            
            case 'int':
                return filter_var($data, FILTER_SANITIZE_NUMBER_INT);
            
            case 'float':
                return filter_var($data, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            
            case 'filename':
                // Remove any path traversal attempts
                $data = basename($data);
                return preg_replace('/[^a-zA-Z0-9._-]/', '', $data);
            
            default:
                return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        }
    }
    
    /**
     * Sanitize HTML content (for CKEditor content)
     * Security: Allow safe HTML tags while preventing XSS
     * @param string $html
     * @return string
     */
    private function sanitizeHTML($html) {
        // Allowed tags for content editor
        $allowedTags = '<p><br><strong><em><u><h1><h2><h3><h4><h5><h6><ul><ol><li><a><img><table><thead><tbody><tr><th><td><blockquote><code><pre><hr><div><span>';
        
        // Strip disallowed tags
        $html = strip_tags($html, $allowedTags);
        
        // Remove dangerous attributes
        $html = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $html);
        $html = preg_replace('/on\w+\s*=\s*["\'].*?["\']/i', '', $html); // Remove event handlers
        $html = preg_replace('/javascript:/i', '', $html); // Remove javascript: protocol
        
        return $html;
    }
    
    /**
     * Validate input data
     * @param mixed $data
     * @param array $rules
     * @return array ['valid' => bool, 'errors' => array]
     */
    public function validate($data, $rules) {
        $errors = [];
        
        foreach ($rules as $field => $ruleSet) {
            $ruleList = explode('|', $ruleSet);
            $value = isset($data[$field]) ? $data[$field] : null;
            
            foreach ($ruleList as $rule) {
                $params = [];
                
                // Extract parameters from rule (e.g., min:8)
                if (strpos($rule, ':') !== false) {
                    list($rule, $paramString) = explode(':', $rule, 2);
                    $params = explode(',', $paramString);
                }
                
                $error = $this->validateRule($field, $value, $rule, $params);
                
                if ($error !== true) {
                    $errors[$field][] = $error;
                }
            }
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    /**
     * Validate individual rule
     * @param string $field
     * @param mixed $value
     * @param string $rule
     * @param array $params
     * @return string|bool
     */
    private function validateRule($field, $value, $rule, $params = []) {
        switch ($rule) {
            case 'required':
                return !empty($value) ? true : "$field is required";
            
            case 'email':
                return filter_var($value, FILTER_VALIDATE_EMAIL) ? true : "$field must be a valid email";
            
            case 'url':
                return filter_var($value, FILTER_VALIDATE_URL) ? true : "$field must be a valid URL";
            
            case 'numeric':
                return is_numeric($value) ? true : "$field must be numeric";
            
            case 'integer':
                return filter_var($value, FILTER_VALIDATE_INT) !== false ? true : "$field must be an integer";
            
            case 'min':
                $min = $params[0];
                return strlen($value) >= $min ? true : "$field must be at least $min characters";
            
            case 'max':
                $max = $params[0];
                return strlen($value) <= $max ? true : "$field must not exceed $max characters";
            
            case 'between':
                $min = $params[0];
                $max = $params[1];
                $len = strlen($value);
                return ($len >= $min && $len <= $max) ? true : "$field must be between $min and $max characters";
            
            case 'alpha':
                return ctype_alpha($value) ? true : "$field must contain only letters";
            
            case 'alphanumeric':
                return ctype_alnum($value) ? true : "$field must contain only letters and numbers";
            
            case 'slug':
                return preg_match('/^[a-z0-9-]+$/', $value) ? true : "$field must be a valid slug";
            
            case 'date':
                return strtotime($value) !== false ? true : "$field must be a valid date";
            
            case 'in':
                return in_array($value, $params) ? true : "$field must be one of: " . implode(', ', $params);
            
            case 'unique':
                // Check database uniqueness (requires table and column)
                return $this->checkUnique($params[0], $params[1], $value) ? true : "$field already exists";
            
            default:
                return true;
        }
    }
    
    /**
     * Check if value is unique in database
     * @param string $table
     * @param string $column
     * @param mixed $value
     * @return bool
     */
    private function checkUnique($table, $column, $value) {
        $db = Database::getInstance();
        $sql = "SELECT COUNT(*) as count FROM {$table} WHERE {$column} = ?";
        $result = $db->selectOne($sql, [$value]);
        return $result['count'] == 0;
    }
    
    /**
     * Hash password securely
     * Security: Using Argon2id (best practice)
     * @param string $password
     * @return string
     */
    public function hashPassword($password) {
        return password_hash($password, $this->config['security']['password_hash_algo']);
    }
    
    /**
     * Verify password
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    
    /**
     * Encrypt data
     * Security: AES-256-GCM encryption
     * @param string $data
     * @return string
     */
    public function encrypt($data) {
        $key = $this->config['security']['encryption_key'];
        $cipher = 'aes-256-gcm';
        $ivLength = openssl_cipher_iv_length($cipher);
        $iv = openssl_random_pseudo_bytes($ivLength);
        $tag = '';
        
        $encrypted = openssl_encrypt($data, $cipher, $key, 0, $iv, $tag);
        
        // Combine encrypted data, IV, and tag
        return base64_encode($encrypted . '::' . $iv . '::' . $tag);
    }
    
    /**
     * Decrypt data
     * @param string $data
     * @return string|false
     */
    public function decrypt($data) {
        $key = $this->config['security']['encryption_key'];
        $cipher = 'aes-256-gcm';
        
        $data = base64_decode($data);
        list($encrypted, $iv, $tag) = explode('::', $data);
        
        return openssl_decrypt($encrypted, $cipher, $key, 0, $iv, $tag);
    }
    
    /**
     * Check login attempts (Brute force protection)
     * @param string $identifier (email or username)
     * @return bool
     */
    public function checkLoginAttempts($identifier) {
        if (!isset($_SESSION)) {
            session_start();
        }
        
        $key = 'login_attempts_' . md5($identifier);
        
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = [
                'attempts' => 0,
                'locked_until' => 0
            ];
        }
        
        $attempts = $_SESSION[$key];
        
        // Check if account is locked
        if ($attempts['locked_until'] > time()) {
            $remainingTime = $attempts['locked_until'] - time();
            throw new Exception("Too many failed login attempts. Please try again in " . ceil($remainingTime / 60) . " minutes.");
        }
        
        // Reset if lock time expired
        if ($attempts['locked_until'] > 0 && $attempts['locked_until'] <= time()) {
            $_SESSION[$key] = [
                'attempts' => 0,
                'locked_until' => 0
            ];
        }
        
        return true;
    }
    
    /**
     * Record failed login attempt
     * @param string $identifier
     */
    public function recordFailedLogin($identifier) {
        if (!isset($_SESSION)) {
            session_start();
        }
        
        $key = 'login_attempts_' . md5($identifier);
        
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = [
                'attempts' => 0,
                'locked_until' => 0
            ];
        }
        
        $_SESSION[$key]['attempts']++;
        
        // Lock account if max attempts reached
        if ($_SESSION[$key]['attempts'] >= $this->config['security']['max_login_attempts']) {
            $_SESSION[$key]['locked_until'] = time() + $this->config['security']['login_lockout_time'];
        }
    }
    
    /**
     * Reset login attempts
     * @param string $identifier
     */
    public function resetLoginAttempts($identifier) {
        if (!isset($_SESSION)) {
            session_start();
        }
        
        $key = 'login_attempts_' . md5($identifier);
        unset($_SESSION[$key]);
    }
    
    /**
     * Validate file upload
     * Security: File type and size validation
     * @param array $file $_FILES array element
     * @param string $type (image, document, video, audio)
     * @return array ['valid' => bool, 'error' => string]
     */
    public function validateFileUpload($file, $type = 'image') {
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return ['valid' => false, 'error' => 'No file uploaded'];
        }
        
        // Check file size
        if ($file['size'] > $this->config['security']['max_upload_size']) {
            $maxSizeMB = $this->config['security']['max_upload_size'] / 1048576;
            return ['valid' => false, 'error' => "File size must not exceed {$maxSizeMB}MB"];
        }
        
        // Check file extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedTypes = $this->config['security']['allowed_file_types'][$type] ?? [];
        
        if (!in_array($extension, $allowedTypes)) {
            return ['valid' => false, 'error' => "File type not allowed. Allowed types: " . implode(', ', $allowedTypes)];
        }
        
        // Check MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        $allowedMimes = $this->config['media']['allowed_types'];
        if (!in_array($mimeType, $allowedMimes)) {
            return ['valid' => false, 'error' => 'Invalid file type'];
        }
        
        // Additional check for image files
        if ($type === 'image') {
            $imageInfo = getimagesize($file['tmp_name']);
            if ($imageInfo === false) {
                return ['valid' => false, 'error' => 'Invalid image file'];
            }
        }
        
        return ['valid' => true, 'error' => ''];
    }
    
    /**
     * Generate secure random token
     * @param int $length
     * @return string
     */
    public function generateToken($length = 32) {
        return bin2hex(random_bytes($length));
    }
    
    /**
     * Prevent XSS in output
     * @param string $data
     * @return string
     */
    public function escape($data) {
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Check if request is AJAX
     * @return bool
     */
    public function isAjax() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
    
    /**
     * Get client IP address
     * @return string
     */
    public function getClientIP() {
        $ipKeys = ['HTTP_CF_CONNECTING_IP', 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 
                   'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'];
        
        foreach ($ipKeys as $key) {
            if (isset($_SERVER[$key])) {
                $ips = explode(',', $_SERVER[$key]);
                $ip = trim($ips[0]);
                
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
    
    /**
     * Rate limiting check
     * @param string $identifier
     * @param int $maxRequests
     * @param int $window (seconds)
     * @return bool
     */
    public function rateLimit($identifier, $maxRequests = 100, $window = 60) {
        if (!isset($_SESSION)) {
            session_start();
        }
        
        $key = 'rate_limit_' . md5($identifier);
        $now = time();
        
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = [
                'requests' => [],
                'blocked_until' => 0
            ];
        }
        
        $rateData = &$_SESSION[$key];
        
        // Check if blocked
        if ($rateData['blocked_until'] > $now) {
            throw new Exception('Rate limit exceeded. Please try again later.');
        }
        
        // Remove old requests outside the window
        $rateData['requests'] = array_filter($rateData['requests'], function($timestamp) use ($now, $window) {
            return $timestamp > ($now - $window);
        });
        
        // Check if limit exceeded
        if (count($rateData['requests']) >= $maxRequests) {
            $rateData['blocked_until'] = $now + $window;
            throw new Exception('Rate limit exceeded. Please try again later.');
        }
        
        // Record this request
        $rateData['requests'][] = $now;
        
        return true;
    }
}
