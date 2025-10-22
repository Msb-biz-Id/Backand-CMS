<?php

/**
 * CSRF Protection Middleware
 * Security: Prevent Cross-Site Request Forgery attacks
 */
class CSRFMiddleware {
    private $security;
    
    public function __construct() {
        $this->security = Security::getInstance();
    }
    
    public function handle() {
        if (!isset($_SESSION)) {
            session_start();
        }
        
        // Skip CSRF check for GET requests
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            return true;
        }
        
        // Get CSRF token from request
        $token = null;
        
        // Check POST data
        if (isset($_POST['csrf_token'])) {
            $token = $_POST['csrf_token'];
        }
        // Check headers (for AJAX requests)
        elseif (isset($_SERVER['HTTP_X_CSRF_TOKEN'])) {
            $token = $_SERVER['HTTP_X_CSRF_TOKEN'];
        }
        
        // Verify token
        if (!$token || !$this->security->verifyCSRFToken($token)) {
            if ($this->security->isAjax()) {
                http_response_code(419);
                echo json_encode([
                    'success' => false,
                    'message' => 'CSRF token mismatch. Please refresh the page and try again.'
                ]);
                exit;
            } else {
                http_response_code(419);
                echo '<h1>419 - CSRF Token Mismatch</h1>';
                echo '<p>Your session has expired or the request is invalid. Please refresh the page and try again.</p>';
                exit;
            }
        }
        
        return true;
    }
}
