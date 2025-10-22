<?php

/**
 * Admin Middleware
 * Security: Verify user has admin or editor role
 */
class AdminMiddleware {
    private $security;
    
    public function __construct() {
        $this->security = Security::getInstance();
    }
    
    public function handle() {
        if (!isset($_SESSION)) {
            session_start();
        }
        
        // Check if user is authenticated
        if (!isset($_SESSION['user']) || empty($_SESSION['user'])) {
            return false;
        }
        
        $user = $_SESSION['user'];
        $allowedRoles = ['admin', 'editor'];
        
        // Check if user has required role
        if (!in_array($user['role'], $allowedRoles)) {
            if ($this->security->isAjax()) {
                http_response_code(403);
                echo json_encode([
                    'success' => false,
                    'message' => 'Forbidden. You do not have permission to access this resource.'
                ]);
                exit;
            } else {
                http_response_code(403);
                echo '<h1>403 - Forbidden</h1>';
                echo '<p>You do not have permission to access this page.</p>';
                exit;
            }
        }
        
        return true;
    }
}
