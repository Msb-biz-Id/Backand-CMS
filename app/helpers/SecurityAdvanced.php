<?php

/**
 * Advanced Security Helper
 * Custom login URLs per user/role
 */
class SecurityAdvanced {
    
    /**
     * Generate custom login URL for user
     */
    public static function generateCustomLoginUrl($userId = null) {
        $token = bin2hex(random_bytes(16));
        
        if ($userId) {
            // Save to user meta
            $db = Database::getInstance();
            $db->insert('user_meta', [
                'user_id' => $userId,
                'meta_key' => 'custom_login_url',
                'meta_value' => $token
            ]);
        }
        
        return $token;
    }
    
    /**
     * Get custom login URL for user
     */
    public static function getCustomLoginUrl($userId) {
        $db = Database::getInstance();
        $meta = $db->selectOne("SELECT meta_value FROM cms_user_meta 
                                WHERE user_id = ? AND meta_key = 'custom_login_url'", [$userId]);
        
        return $meta['meta_value'] ?? null;
    }
    
    /**
     * Get custom login URL for role
     */
    public static function getRoleLoginUrl($role) {
        $db = Database::getInstance();
        $setting = $db->selectOne("SELECT value FROM cms_settings 
                                   WHERE `key` = ?", ['role_login_url_' . $role]);
        
        return $setting['value'] ?? null;
    }
    
    /**
     * Set custom login URL for role
     */
    public static function setRoleLoginUrl($role, $url) {
        $token = bin2hex(random_bytes(16));
        
        $db = Database::getInstance();
        $exists = $db->selectOne("SELECT id FROM cms_settings 
                                  WHERE `key` = ?", ['role_login_url_' . $role]);
        
        if ($exists) {
            $db->update('settings', 
                ['value' => $token], 
                ['key' => 'role_login_url_' . $role]
            );
        } else {
            $db->insert('settings', [
                'key' => 'role_login_url_' . $role,
                'value' => $token,
                'group' => 'security'
            ]);
        }
        
        return $token;
    }
    
    /**
     * Verify custom login URL
     */
    public static function verifyCustomLoginUrl($token) {
        $db = Database::getInstance();
        
        // Check user meta
        $userMeta = $db->selectOne("SELECT user_id FROM cms_user_meta 
                                    WHERE meta_key = 'custom_login_url' 
                                    AND meta_value = ?", [$token]);
        
        if ($userMeta) {
            return [
                'type' => 'user',
                'user_id' => $userMeta['user_id']
            ];
        }
        
        // Check role settings
        $roles = ['admin', 'editor', 'author', 'contributor', 'subscriber'];
        
        foreach ($roles as $role) {
            $setting = $db->selectOne("SELECT value FROM cms_settings 
                                       WHERE `key` = ? AND value = ?", 
                                       ['role_login_url_' . $role, $token]);
            
            if ($setting) {
                return [
                    'type' => 'role',
                    'role' => $role
                ];
            }
        }
        
        return null;
    }
    
    /**
     * Generate login page for custom URL
     */
    public static function getLoginPageUrl($identifier = null) {
        global $config;
        $baseUrl = $config['app']['url'] ?? '';
        $adminPath = $config['app']['admin_path'] ?? 'admin';
        
        if ($identifier) {
            return $baseUrl . '/' . $adminPath . '/login/' . $identifier;
        }
        
        return $baseUrl . '/' . $adminPath . '/login';
    }
    
    /**
     * Log failed login attempt
     */
    public static function logFailedLogin($identifier, $ip) {
        $db = Database::getInstance();
        $db->insert('security_log', [
            'event_type' => 'failed_login',
            'identifier' => $identifier,
            'ip_address' => $ip,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Check if IP is blocked
     */
    public static function isIpBlocked($ip) {
        $db = Database::getInstance();
        
        // Count failed attempts in last hour
        $sql = "SELECT COUNT(*) as attempts FROM cms_security_log 
                WHERE event_type = 'failed_login' 
                AND ip_address = ? 
                AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)";
        
        $result = $db->selectOne($sql, [$ip]);
        $attempts = $result['attempts'] ?? 0;
        
        global $config;
        $maxAttempts = $config['security']['max_login_attempts'] ?? 5;
        
        return $attempts >= $maxAttempts;
    }
}
