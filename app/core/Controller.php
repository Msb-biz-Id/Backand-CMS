<?php

/**
 * Base Controller Class
 * Best Practice: All controllers should extend this class
 */
abstract class Controller {
    protected $db;
    protected $security;
    protected $config;
    protected $seo;
    protected $cache;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->security = Security::getInstance();
        $this->config = require __DIR__ . '/../../config/config.php';
        $this->seo = SEO::getInstance();
        $this->cache = Cache::getInstance();
        
        // Start session if not started
        if (session_status() === PHP_SESSION_NONE) {
            $this->startSecureSession();
        }
    }
    
    /**
     * Start secure session
     * Security: Secure session configuration
     */
    protected function startSecureSession() {
        $sessionConfig = $this->config['session'];
        
        ini_set('session.cookie_httponly', $sessionConfig['cookie_httponly']);
        ini_set('session.cookie_secure', $sessionConfig['cookie_secure']);
        ini_set('session.cookie_samesite', $sessionConfig['cookie_samesite']);
        ini_set('session.use_strict_mode', 1);
        ini_set('session.use_only_cookies', 1);
        
        session_name($sessionConfig['cookie_name']);
        session_set_cookie_params([
            'lifetime' => $sessionConfig['lifetime'],
            'path' => $sessionConfig['cookie_path'],
            'domain' => $sessionConfig['cookie_domain'],
            'secure' => $sessionConfig['cookie_secure'],
            'httponly' => $sessionConfig['cookie_httponly'],
            'samesite' => $sessionConfig['cookie_samesite']
        ]);
        
        session_start();
        
        // Regenerate session ID periodically (Security: Session fixation prevention)
        if (!isset($_SESSION['last_regeneration'])) {
            $_SESSION['last_regeneration'] = time();
        } elseif (time() - $_SESSION['last_regeneration'] > $this->config['security']['session_regenerate_interval']) {
            session_regenerate_id(true);
            $_SESSION['last_regeneration'] = time();
        }
    }
    
    /**
     * Load view
     * @param string $view
     * @param array $data
     * @param string $layout
     */
    protected function view($view, $data = [], $layout = 'admin') {
        // Extract data to variables
        extract($data);
        
        // SEO data
        $seoData = $this->seo->getSEOData();
        
        // CSRF token
        $csrfToken = $this->security->generateCSRFToken();
        
        // Load layout
        $layoutPath = __DIR__ . "/../views/layouts/{$layout}.php";
        $viewPath = __DIR__ . "/../views/{$view}.php";
        
        if (file_exists($layoutPath) && file_exists($viewPath)) {
            require_once $layoutPath;
        } else {
            throw new Exception("View or layout not found: {$view}");
        }
    }
    
    /**
     * Load view without layout
     * @param string $view
     * @param array $data
     */
    protected function viewOnly($view, $data = []) {
        extract($data);
        $viewPath = __DIR__ . "/../views/{$view}.php";
        
        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            throw new Exception("View not found: {$view}");
        }
    }
    
    /**
     * JSON response
     * @param mixed $data
     * @param int $statusCode
     */
    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    /**
     * Success JSON response
     * @param mixed $data
     * @param string $message
     */
    protected function success($data = null, $message = 'Success') {
        $this->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], 200);
    }
    
    /**
     * Error JSON response
     * @param string $message
     * @param int $statusCode
     * @param mixed $errors
     */
    protected function error($message = 'Error', $statusCode = 400, $errors = null) {
        $this->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], $statusCode);
    }
    
    /**
     * Redirect
     * @param string $url
     * @param int $statusCode
     */
    protected function redirect($url, $statusCode = 302) {
        header("Location: {$url}", true, $statusCode);
        exit;
    }
    
    /**
     * Redirect back
     */
    protected function back() {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        $this->redirect($referer);
    }
    
    /**
     * Get request input
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    protected function input($key = null, $default = null) {
        $input = array_merge($_GET, $_POST, json_decode(file_get_contents('php://input'), true) ?? []);
        
        if ($key === null) {
            return $input;
        }
        
        return $input[$key] ?? $default;
    }
    
    /**
     * Get sanitized input
     * @param string $key
     * @param string $type
     * @param mixed $default
     * @return mixed
     */
    protected function sanitizeInput($key, $type = 'string', $default = null) {
        $value = $this->input($key, $default);
        return $this->security->sanitize($value, $type);
    }
    
    /**
     * Validate request
     * @param array $rules
     * @return array
     */
    protected function validate($rules) {
        $data = $this->input();
        $validation = $this->security->validate($data, $rules);
        
        if (!$validation['valid']) {
            if ($this->security->isAjax()) {
                $this->error('Validation failed', 422, $validation['errors']);
            } else {
                $_SESSION['errors'] = $validation['errors'];
                $_SESSION['old_input'] = $data;
                $this->back();
            }
        }
        
        return $data;
    }
    
    /**
     * Check if user is authenticated
     * @return bool
     */
    protected function isAuthenticated() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
    
    /**
     * Get authenticated user
     * @return array|null
     */
    protected function getUser() {
        if (!$this->isAuthenticated()) {
            return null;
        }
        
        return $_SESSION['user'] ?? null;
    }
    
    /**
     * Check if user has role
     * @param string|array $roles
     * @return bool
     */
    protected function hasRole($roles) {
        if (!$this->isAuthenticated()) {
            return false;
        }
        
        $userRole = $_SESSION['user']['role'] ?? null;
        
        if (is_array($roles)) {
            return in_array($userRole, $roles);
        }
        
        return $userRole === $roles;
    }
    
    /**
     * Require authentication
     */
    protected function requireAuth() {
        if (!$this->isAuthenticated()) {
            if ($this->security->isAjax()) {
                $this->error('Unauthorized', 401);
            } else {
                $_SESSION['intended_url'] = $_SERVER['REQUEST_URI'];
                $this->redirect('/' . $this->config['app']['admin_path'] . '/login');
            }
        }
    }
    
    /**
     * Require role
     * @param string|array $roles
     */
    protected function requireRole($roles) {
        $this->requireAuth();
        
        if (!$this->hasRole($roles)) {
            if ($this->security->isAjax()) {
                $this->error('Forbidden', 403);
            } else {
                $this->redirect('/403');
            }
        }
    }
    
    /**
     * Upload file
     * @param array $file
     * @param string $path
     * @param string $type
     * @return array
     */
    protected function uploadFile($file, $path = 'media', $type = 'image') {
        // Validate file
        $validation = $this->security->validateFileUpload($file, $type);
        
        if (!$validation['valid']) {
            throw new Exception($validation['error']);
        }
        
        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '_' . time() . '.' . $extension;
        $uploadPath = $this->config['media']['upload_path'] . '/' . $path;
        
        // Create directory if not exists
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }
        
        $destination = $uploadPath . '/' . $filename;
        
        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new Exception('Failed to upload file');
        }
        
        // Optimize image if enabled
        if ($type === 'image' && $this->config['media']['optimize']) {
            $this->optimizeImage($destination);
        }
        
        return [
            'filename' => $filename,
            'path' => '/' . $path . '/' . $filename,
            'full_path' => $destination,
            'size' => filesize($destination),
            'type' => mime_content_type($destination)
        ];
    }
    
    /**
     * Optimize image
     * @param string $path
     */
    protected function optimizeImage($path) {
        $info = getimagesize($path);
        $mime = $info['mime'];
        
        switch ($mime) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($path);
                imagejpeg($image, $path, 85);
                break;
            case 'image/png':
                $image = imagecreatefrompng($path);
                imagepng($image, $path, 8);
                break;
            case 'image/gif':
                $image = imagecreatefromgif($path);
                imagegif($image, $path);
                break;
        }
        
        if (isset($image)) {
            imagedestroy($image);
        }
    }
    
    /**
     * Set flash message
     * @param string $key
     * @param mixed $value
     */
    protected function setFlash($key, $value) {
        $_SESSION['flash'][$key] = $value;
    }
    
    /**
     * Get flash message
     * @param string $key
     * @return mixed
     */
    protected function getFlash($key) {
        $value = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $value;
    }
}
