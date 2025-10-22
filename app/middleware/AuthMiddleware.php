<?php

/**
 * Authentication Middleware
 * Security: Verify user authentication
 */
class AuthMiddleware {
    private $security;
    private $config;
    
    public function __construct() {
        $this->security = Security::getInstance();
        $this->config = require __DIR__ . '/../../config/config.php';
    }
    
    public function handle() {
        if (!isset($_SESSION)) {
            session_start();
        }
        
        // Check if user is authenticated
        if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
            // Save intended URL for redirect after login
            $_SESSION['intended_url'] = $_SERVER['REQUEST_URI'];
            
            if ($this->security->isAjax()) {
                http_response_code(401);
                echo json_encode([
                    'success' => false,
                    'message' => 'Unauthorized. Please login.'
                ]);
                exit;
            } else {
                $adminPath = $this->config['app']['admin_path'];
                header("Location: /{$adminPath}/login");
                exit;
            }
        }
        
        // Check session validity
        if (isset($_SESSION['last_activity'])) {
            $sessionLifetime = $this->config['security']['session_lifetime'];
            
            if (time() - $_SESSION['last_activity'] > $sessionLifetime) {
                // Session expired
                session_unset();
                session_destroy();
                
                $_SESSION['error'] = 'Your session has expired. Please login again.';
                
                $adminPath = $this->config['app']['admin_path'];
                header("Location: /{$adminPath}/login");
                exit;
            }
        }
        
        // Update last activity time
        $_SESSION['last_activity'] = time();
        
        return true;
    }
}
