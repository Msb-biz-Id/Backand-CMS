<?php

/**
 * Admin Dashboard Controller
 * Main dashboard with statistics and analytics
 */
class DashboardController extends Controller {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Dashboard index
     */
    public function index() {
        $this->requireAuth();
        
        // Get statistics
        $stats = $this->getStatistics();
        
        // Get recent activity
        $recentActivity = $this->getRecentActivity();
        
        // Get recent posts
        $recentPosts = $this->getRecentPosts();
        
        // Get system info
        $systemInfo = $this->getSystemInfo();
        
        // SEO: Set page title and meta
        $this->seo->setTitle('Dashboard', false);
        $this->seo->setDescription('Admin dashboard with statistics and analytics');
        $this->seo->setRobots('noindex, nofollow'); // Don't index admin pages
        
        $this->view('admin/dashboard/index', [
            'stats' => $stats,
            'recentActivity' => $recentActivity,
            'recentPosts' => $recentPosts,
            'systemInfo' => $systemInfo
        ]);
    }
    
    /**
     * Get dashboard statistics
     */
    private function getStatistics() {
        $db = Database::getInstance();
        
        // Posts statistics
        $postStats = $db->selectOne("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'published' THEN 1 ELSE 0 END) as published,
                SUM(CASE WHEN status = 'draft' THEN 1 ELSE 0 END) as draft,
                SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) as today
            FROM cms_posts 
            WHERE deleted_at IS NULL
        ");
        
        // Pages statistics
        $pageStats = $db->selectOne("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'published' THEN 1 ELSE 0 END) as published
            FROM cms_pages 
            WHERE deleted_at IS NULL
        ");
        
        // Products statistics
        $productStats = $db->selectOne("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'published' THEN 1 ELSE 0 END) as published,
                SUM(CASE WHEN in_stock = 0 THEN 1 ELSE 0 END) as out_of_stock
            FROM cms_products 
            WHERE deleted_at IS NULL
        ");
        
        // Users statistics
        $userStats = $db->selectOne("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active,
                SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) as today
            FROM cms_users 
            WHERE deleted_at IS NULL
        ");
        
        // Comments statistics
        $commentStats = $db->selectOne("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending
            FROM cms_comments
        ");
        
        // Analytics statistics (last 30 days)
        $analyticsStats = $db->selectOne("
            SELECT 
                SUM(page_views) as total_views,
                SUM(unique_views) as unique_views,
                AVG(avg_time_on_page) as avg_time
            FROM cms_analytics_summary
            WHERE date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
        ");
        
        // Media statistics
        $mediaStats = $db->selectOne("
            SELECT 
                COUNT(*) as total,
                SUM(file_size) as total_size
            FROM cms_media 
            WHERE deleted_at IS NULL
        ");
        
        return [
            'posts' => $postStats,
            'pages' => $pageStats,
            'products' => $productStats,
            'users' => $userStats,
            'comments' => $commentStats,
            'analytics' => $analyticsStats,
            'media' => $mediaStats
        ];
    }
    
    /**
     * Get recent activity
     */
    private function getRecentActivity($limit = 10) {
        $db = Database::getInstance();
        
        $activity = $db->select("
            SELECT 
                a.*,
                u.username,
                u.first_name,
                u.last_name,
                u.avatar
            FROM cms_activity_log a
            LEFT JOIN cms_users u ON a.user_id = u.id
            ORDER BY a.created_at DESC
            LIMIT ?
        ", [$limit]);
        
        return $activity;
    }
    
    /**
     * Get recent posts
     */
    private function getRecentPosts($limit = 5) {
        $db = Database::getInstance();
        
        $posts = $db->select("
            SELECT 
                p.*,
                u.username,
                u.first_name,
                u.last_name
            FROM cms_posts p
            LEFT JOIN cms_users u ON p.user_id = u.id
            WHERE p.deleted_at IS NULL
            ORDER BY p.created_at DESC
            LIMIT ?
        ", [$limit]);
        
        return $posts;
    }
    
    /**
     * Get system information
     */
    private function getSystemInfo() {
        return [
            'php_version' => PHP_VERSION,
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'database_version' => $this->getDatabaseVersion(),
            'storage_used' => $this->getStorageUsed(),
            'cache_enabled' => $this->config['cache']['enabled'],
            'cache_stats' => $this->cache->stats()
        ];
    }
    
    /**
     * Get database version
     */
    private function getDatabaseVersion() {
        $db = Database::getInstance();
        $result = $db->selectOne("SELECT VERSION() as version");
        return $result['version'] ?? 'Unknown';
    }
    
    /**
     * Get storage used
     */
    private function getStorageUsed() {
        $uploadPath = $this->config['media']['upload_path'];
        
        if (!is_dir($uploadPath)) {
            return 0;
        }
        
        $size = 0;
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($uploadPath, RecursiveDirectoryIterator::SKIP_DOTS)
        );
        
        foreach ($files as $file) {
            $size += $file->getSize();
        }
        
        return $this->formatBytes($size);
    }
    
    /**
     * Format bytes to human readable
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
