<?php

/**
 * Admin User Controller
 * User Management CRUD
 */
class UserController extends Controller {
    private $userModel;
    
    public function __construct() {
        parent::__construct();
        $this->userModel = new User();
    }
    
    /**
     * List all users
     */
    public function index() {
        $this->requireAuth();
        $this->requireRole('admin');
        
        $page = $this->input('page', 1);
        $search = $this->input('search');
        $role = $this->input('role');
        $status = $this->input('status');
        
        // Build filters
        $filters = [];
        if ($role) {
            $filters['role'] = $role;
        }
        if ($status) {
            $filters['status'] = $status;
        }
        
        if ($search) {
            $users = $this->userModel->search($search);
            $pagination = null;
        } else {
            $result = $this->userModel->paginate($page, 15, $filters, 'created_at DESC');
            $users = $result['data'];
            $pagination = $result['pagination'];
        }
        
        // Get statistics
        $stats = $this->userModel->getStatistics();
        
        $this->seo->setTitle('User Management');
        $this->seo->setRobots('noindex, nofollow');
        
        $this->view('admin/users/index', [
            'users' => $users,
            'pagination' => $pagination,
            'search' => $search,
            'role' => $role,
            'status' => $status,
            'stats' => $stats
        ]);
    }
    
    /**
     * Show create form
     */
    public function create() {
        $this->requireAuth();
        $this->requireRole('admin');
        
        $this->seo->setTitle('Add New User');
        $this->seo->setRobots('noindex, nofollow');
        
        $this->view('admin/users/create');
    }
    
    /**
     * Store new user
     */
    public function store() {
        $this->requireAuth();
        $this->requireRole('admin');
        
        try {
            $data = $this->validate([
                'username' => 'required|min:3|max:50|unique:users,username',
                'email' => 'required|email|unique:users,email',
                'first_name' => 'required|min:2|max:50',
                'last_name' => 'required|min:2|max:50',
                'password' => 'required|min:8',
                'role' => 'required|in:admin,editor,author,contributor,subscriber',
                'status' => 'required|in:active,inactive,suspended'
            ]);
            
            $userId = $this->userModel->createUser($data);
            
            if (!$userId) {
                throw new Exception('Failed to create user');
            }
            
            $this->logActivity('create', 'user', $userId, 'Created user: ' . $data['username']);
            
            $this->setFlash('success', 'User created successfully!');
            $this->redirect('/admin/users');
            
        } catch (Exception $e) {
            $this->setFlash('error', $e->getMessage());
            $this->back();
        }
    }
    
    /**
     * Show edit form
     */
    public function edit($id) {
        $this->requireAuth();
        $this->requireRole('admin');
        
        $user = $this->userModel->find($id);
        
        if (!$user) {
            $this->setFlash('error', 'User not found');
            $this->redirect('/admin/users');
        }
        
        $this->seo->setTitle('Edit User: ' . $user['username']);
        $this->seo->setRobots('noindex, nofollow');
        
        $this->view('admin/users/edit', [
            'user' => $user
        ]);
    }
    
    /**
     * Update user
     */
    public function update($id) {
        $this->requireAuth();
        $this->requireRole('admin');
        
        try {
            $user = $this->userModel->find($id);
            
            if (!$user) {
                throw new Exception('User not found');
            }
            
            $data = $this->validate([
                'username' => 'required|min:3|max:50',
                'email' => 'required|email',
                'first_name' => 'required|min:2|max:50',
                'last_name' => 'required|min:2|max:50',
                'role' => 'required|in:admin,editor,author,contributor,subscriber',
                'status' => 'required|in:active,inactive,suspended',
                'bio' => '',
                'website' => ''
            ]);
            
            // Update password if provided
            if ($this->input('password')) {
                $this->userModel->updatePassword($id, $this->input('password'));
            }
            
            $this->userModel->update($id, $data);
            
            $this->logActivity('update', 'user', $id, 'Updated user: ' . $data['username']);
            
            $this->setFlash('success', 'User updated successfully!');
            $this->redirect('/admin/users');
            
        } catch (Exception $e) {
            $this->setFlash('error', $e->getMessage());
            $this->back();
        }
    }
    
    /**
     * Delete user
     */
    public function delete($id) {
        $this->requireAuth();
        $this->requireRole('admin');
        
        try {
            // Don't allow deleting yourself
            if ($id == $_SESSION['user_id']) {
                throw new Exception('You cannot delete your own account');
            }
            
            $user = $this->userModel->find($id);
            
            if (!$user) {
                throw new Exception('User not found');
            }
            
            $this->userModel->delete($id);
            
            $this->logActivity('delete', 'user', $id, 'Deleted user: ' . $user['username']);
            
            $this->setFlash('success', 'User deleted successfully!');
            $this->redirect('/admin/users');
            
        } catch (Exception $e) {
            $this->setFlash('error', $e->getMessage());
            $this->back();
        }
    }
    
    /**
     * User profile
     */
    public function profile() {
        $this->requireAuth();
        
        $user = $this->userModel->find($_SESSION['user_id']);
        
        if (!$user) {
            $this->setFlash('error', 'User not found');
            $this->redirect('/admin/dashboard');
        }
        
        $this->seo->setTitle('My Profile');
        $this->seo->setRobots('noindex, nofollow');
        
        $this->view('admin/users/profile', [
            'user' => $user
        ]);
    }
    
    /**
     * Update profile
     */
    public function updateProfile() {
        $this->requireAuth();
        
        try {
            $userId = $_SESSION['user_id'];
            
            $data = $this->validate([
                'first_name' => 'required|min:2|max:50',
                'last_name' => 'required|min:2|max:50',
                'email' => 'required|email',
                'bio' => '',
                'website' => ''
            ]);
            
            $this->userModel->update($userId, $data);
            
            // Update password if provided
            if ($this->input('current_password') && $this->input('new_password')) {
                $user = $this->userModel->find($userId);
                
                if (!$this->security->verifyPassword($this->input('current_password'), $user['password_hash'])) {
                    throw new Exception('Current password is incorrect');
                }
                
                $this->userModel->updatePassword($userId, $this->input('new_password'));
            }
            
            $this->setFlash('success', 'Profile updated successfully!');
            $this->redirect('/admin/profile');
            
        } catch (Exception $e) {
            $this->setFlash('error', $e->getMessage());
            $this->back();
        }
    }
    
    /**
     * Log activity
     */
    private function logActivity($action, $subjectType, $subjectId, $description) {
        $this->db->insert('activity_log', [
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
