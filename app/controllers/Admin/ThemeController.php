<?php

/**
 * Admin Theme Controller
 * Theme management & customization
 */
class ThemeController extends Controller {
    private $theme;
    
    public function __construct() {
        parent::__construct();
        $this->theme = new Theme();
    }
    
    /**
     * Theme manager index
     */
    public function index() {
        $this->requireAuth();
        $this->requireRole('admin');
        
        $themes = $this->theme->getAvailableThemes();
        $activeTheme = $this->theme->getActiveTheme();
        $customization = $this->theme->getCustomization();
        
        $this->seo->setTitle('Theme Manager');
        $this->seo->setRobots('noindex, nofollow');
        
        $this->view('admin/themes/index', [
            'themes' => $themes,
            'activeTheme' => $activeTheme,
            'customization' => $customization
        ]);
    }
    
    /**
     * Activate theme
     */
    public function activate() {
        $this->requireAuth();
        $this->requireRole('admin');
        
        try {
            $themeName = $this->input('theme');
            
            if (!$themeName) {
                throw new Exception('Theme name is required');
            }
            
            $this->theme->activate($themeName);
            
            // Clear cache
            $this->cache->flush();
            
            $this->logActivity('activate', 'theme', null, 'Activated theme: ' . $themeName);
            
            $this->setFlash('success', 'Theme activated successfully!');
            $this->redirect('/admin/themes');
            
        } catch (Exception $e) {
            $this->setFlash('error', $e->getMessage());
            $this->back();
        }
    }
    
    /**
     * Customize theme
     */
    public function customize() {
        $this->requireAuth();
        $this->requireRole('admin');
        
        try {
            $data = $this->input();
            
            // Save each customization
            foreach ($data as $key => $value) {
                if ($key !== 'csrf_token') {
                    $this->theme->setCustomization($key, $value);
                }
            }
            
            // Clear cache
            $this->cache->flush();
            
            $this->logActivity('customize', 'theme', null, 'Customized theme settings');
            
            $this->setFlash('success', 'Theme customization saved!');
            $this->redirect('/admin/themes');
            
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
