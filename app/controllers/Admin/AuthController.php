<?php

/**
 * Admin Authentication Controller
 * Handles login, logout, registration, password reset
 */
class AuthController extends Controller {
    private $userModel;
    
    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
    }
    
    /**
     * Show login form
     */
    public function loginForm() {
        // If already logged in, redirect to dashboard
        if ($this->isAuthenticated()) {
            $this->redirect('/' . $this->config['app']['admin_path'] . '/dashboard');
        }
        
        $this->view('admin/auth/login', [], 'auth');
    }
    
    /**
     * Handle login
     */
    public function login() {
        try {
            // Validate input
            $data = $this->validate([
                'email' => 'required|email',
                'password' => 'required|min:6',
                'remember' => ''
            ]);
            
            // Check login attempts
            $this->security->checkLoginAttempts($data['email']);
            
            // Verify credentials
            $user = $this->userModel->verifyCredentials($data['email'], $data['password']);
            
            if (!$user) {
                $this->security->recordFailedLogin($data['email']);
                throw new Exception('Invalid email or password');
            }
            
            // Reset login attempts
            $this->security->resetLoginAttempts($data['email']);
            
            // Update last login
            $this->userModel->updateLastLogin($user['id'], $this->security->getClientIP());
            
            // Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user'] = $user;
            $_SESSION['last_activity'] = time();
            
            // Handle remember me
            if (isset($data['remember']) && $data['remember']) {
                $token = $this->security->generateToken();
                $_SESSION['remember_token'] = $token;
                setcookie('remember_token', $token, time() + (86400 * 30), '/'); // 30 days
            }
            
            // Log activity
            $this->logActivity('login', 'user', $user['id'], 'User logged in');
            
            // Redirect
            $redirectUrl = $_SESSION['intended_url'] ?? '/' . $this->config['app']['admin_path'] . '/dashboard';
            unset($_SESSION['intended_url']);
            
            if ($this->security->isAjax()) {
                $this->success(['redirect' => $redirectUrl], 'Login successful');
            } else {
                $this->setFlash('success', 'Welcome back, ' . $user['first_name'] . '!');
                $this->redirect($redirectUrl);
            }
            
        } catch (Exception $e) {
            if ($this->security->isAjax()) {
                $this->error($e->getMessage(), 401);
            } else {
                $this->setFlash('error', $e->getMessage());
                $this->back();
            }
        }
    }
    
    /**
     * Handle logout
     */
    public function logout() {
        $userId = $_SESSION['user_id'] ?? null;
        
        // Log activity
        if ($userId) {
            $this->logActivity('logout', 'user', $userId, 'User logged out');
        }
        
        // Destroy session
        session_unset();
        session_destroy();
        
        // Remove remember cookie
        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', time() - 3600, '/');
        }
        
        $this->setFlash('success', 'You have been logged out successfully');
        $this->redirect('/' . $this->config['app']['admin_path'] . '/login');
    }
    
    /**
     * Show registration form
     */
    public function registerForm() {
        // Check if registration is allowed
        if (!$this->config['allow_registration']) {
            $this->error('Registration is currently disabled', 403);
        }
        
        $this->view('admin/auth/register', [], 'auth');
    }
    
    /**
     * Handle registration
     */
    public function register() {
        try {
            // Check if registration is allowed
            if (!$this->config['allow_registration']) {
                throw new Exception('Registration is currently disabled');
            }
            
            // Validate input
            $data = $this->validate([
                'username' => 'required|min:3|max:50|alphanumeric|unique:users,username',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:8',
                'password_confirm' => 'required',
                'first_name' => 'required|max:50',
                'last_name' => 'required|max:50'
            ]);
            
            // Check password confirmation
            if ($data['password'] !== $data['password_confirm']) {
                throw new Exception('Passwords do not match');
            }
            
            // Create user
            $userId = $this->userModel->createUser([
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => $data['password'],
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'role' => $this->config['default_user_role'] ?? 'subscriber'
            ]);
            
            if (!$userId) {
                throw new Exception('Failed to create user account');
            }
            
            // Log activity
            $this->logActivity('register', 'user', $userId, 'New user registered');
            
            if ($this->security->isAjax()) {
                $this->success(null, 'Registration successful. Please login.');
            } else {
                $this->setFlash('success', 'Registration successful! Please login to continue.');
                $this->redirect('/' . $this->config['app']['admin_path'] . '/login');
            }
            
        } catch (Exception $e) {
            if ($this->security->isAjax()) {
                $this->error($e->getMessage(), 400);
            } else {
                $this->setFlash('error', $e->getMessage());
                $this->back();
            }
        }
    }
    
    /**
     * Show forgot password form
     */
    public function forgotPassword() {
        $this->view('admin/auth/forgot-password', [], 'auth');
    }
    
    /**
     * Send password reset link
     */
    public function sendResetLink() {
        try {
            $data = $this->validate([
                'email' => 'required|email'
            ]);
            
            // Find user
            $user = $this->userModel->findByEmail($data['email']);
            
            if (!$user) {
                // Don't reveal if email exists (security)
                throw new Exception('If the email exists, a reset link will be sent.');
            }
            
            // Generate reset token
            $token = $this->security->generateToken();
            $expiry = date('Y-m-d H:i:s', time() + 3600); // 1 hour
            
            // Store token (you might want to create a password_resets table)
            $_SESSION['password_reset_' . $user['id']] = [
                'token' => $token,
                'expiry' => $expiry
            ];
            
            // TODO: Send email with reset link
            // $resetLink = $this->config['app']['url'] . '/admin/reset-password?token=' . $token;
            
            // Log activity
            $this->logActivity('password_reset_request', 'user', $user['id'], 'Password reset requested');
            
            if ($this->security->isAjax()) {
                $this->success(null, 'If the email exists, a reset link will be sent.');
            } else {
                $this->setFlash('success', 'If the email exists, a reset link has been sent.');
                $this->back();
            }
            
        } catch (Exception $e) {
            if ($this->security->isAjax()) {
                $this->error($e->getMessage(), 400);
            } else {
                $this->setFlash('error', $e->getMessage());
                $this->back();
            }
        }
    }
    
    /**
     * Log user activity
     */
    private function logActivity($action, $subjectType, $subjectId, $description) {
        $db = Database::getInstance();
        $db->insert('activity_log', [
            'user_id' => $_SESSION['user_id'] ?? null,
            'action' => $action,
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'description' => $description,
            'ip_address' => $this->security->getClientIP(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    }
}
