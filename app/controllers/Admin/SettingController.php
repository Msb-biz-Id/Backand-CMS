<?php

/**
 * Admin Setting Controller
 * System configuration management
 */
class SettingController extends Controller {
    private $settingModel;
    
    public function __construct() {
        parent::__construct();
        $this->settingModel = new Setting();
    }
    
    /**
     * Settings index
     */
    public function index() {
        $this->requireRole(['admin']);
        
        // Get active tab
        $activeTab = $this->input('tab', 'general');
        
        // Get settings by group
        $groups = ['general', 'seo', 'analytics', 'integrations', 'performance', 'security'];
        $settings = [];
        
        foreach ($groups as $group) {
            $settings[$group] = $this->settingModel->getByGroup($group);
        }
        
        // SEO
        $this->seo->setTitle('Settings');
        $this->seo->setRobots('noindex, nofollow');
        
        $this->view('admin/settings/index', [
            'settings' => $settings,
            'activeTab' => $activeTab
        ]);
    }
    
    /**
     * Update settings
     */
    public function update() {
        $this->requireRole(['admin']);
        
        try {
            $data = $this->input();
            
            // Remove CSRF token and method
            unset($data['csrf_token'], $data['_method']);
            
            // Begin transaction
            $this->db->beginTransaction();
            
            // Update each setting
            foreach ($data as $key => $value) {
                $this->settingModel->set($key, $value);
            }
            
            // Commit transaction
            $this->db->commit();
            
            // Clear cache
            $this->cache->forget('settings');
            
            // Log activity
            $this->logActivity('update', 'settings', 0, 'Updated system settings');
            
            $this->setFlash('success', 'Settings updated successfully!');
            $this->back();
            
        } catch (Exception $e) {
            $this->db->rollback();
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
