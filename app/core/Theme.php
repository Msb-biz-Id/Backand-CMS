<?php

/**
 * Theme System
 * 
 * BERBEDA DARI WORDPRESS:
 * - WordPress: Theme = Template files (PHP)
 * - Advanced CMS: Theme = Visual customization only
 * - Logic tetap di core, theme hanya CSS/JS/assets
 */
class Theme {
    private $activeTheme;
    private $themesPath;
    private $config;
    
    public function __construct() {
        global $config;
        $this->config = $config;
        $this->themesPath = dirname(__DIR__, 2) . '/public/themes/';
        $this->activeTheme = $this->getActiveTheme();
    }
    
    /**
     * Get active theme from database
     */
    public function getActiveTheme() {
        $db = Database::getInstance();
        $setting = $db->selectOne("SELECT value FROM cms_settings WHERE `key` = 'active_theme'");
        
        return $setting['value'] ?? 'default';
    }
    
    /**
     * Get theme assets path
     */
    public function getAssetPath($file = '') {
        return '/themes/' . $this->activeTheme . ($file ? '/' . $file : '');
    }
    
    /**
     * Get theme CSS
     */
    public function getCss() {
        return $this->getAssetPath('assets/css/theme.css');
    }
    
    /**
     * Get theme JS
     */
    public function getJs() {
        return $this->getAssetPath('assets/js/theme.js');
    }
    
    /**
     * Get theme config
     */
    public function getConfig() {
        $configFile = $this->themesPath . $this->activeTheme . '/theme.json';
        
        if (file_exists($configFile)) {
            return json_decode(file_get_contents($configFile), true);
        }
        
        return [];
    }
    
    /**
     * Get customization settings
     */
    public function getCustomization($key = null) {
        $db = Database::getInstance();
        $setting = $db->selectOne("SELECT value FROM cms_settings WHERE `key` = 'theme_customization'");
        
        $customization = $setting ? json_decode($setting['value'], true) : [];
        
        if ($key) {
            return $customization[$key] ?? null;
        }
        
        return $customization;
    }
    
    /**
     * Set customization
     */
    public function setCustomization($key, $value) {
        $db = Database::getInstance();
        
        $current = $this->getCustomization();
        $current[$key] = $value;
        
        $exists = $db->selectOne("SELECT id FROM cms_settings WHERE `key` = 'theme_customization'");
        
        if ($exists) {
            $db->update('settings', ['value' => json_encode($current)], ['key' => 'theme_customization']);
        } else {
            $db->insert('settings', [
                'key' => 'theme_customization',
                'value' => json_encode($current),
                'group' => 'theme'
            ]);
        }
        
        return true;
    }
    
    /**
     * Get available themes
     */
    public function getAvailableThemes() {
        $themes = [];
        
        if (!is_dir($this->themesPath)) {
            return $themes;
        }
        
        $dirs = scandir($this->themesPath);
        
        foreach ($dirs as $dir) {
            if ($dir === '.' || $dir === '..') {
                continue;
            }
            
            $themePath = $this->themesPath . $dir;
            
            if (is_dir($themePath)) {
                $configFile = $themePath . '/theme.json';
                
                if (file_exists($configFile)) {
                    $config = json_decode(file_get_contents($configFile), true);
                    $config['directory'] = $dir;
                    $themes[$dir] = $config;
                }
            }
        }
        
        return $themes;
    }
    
    /**
     * Activate theme
     */
    public function activate($themeName) {
        $db = Database::getInstance();
        
        // Check if theme exists
        $themePath = $this->themesPath . $themeName;
        
        if (!is_dir($themePath)) {
            throw new Exception('Theme not found');
        }
        
        $exists = $db->selectOne("SELECT id FROM cms_settings WHERE `key` = 'active_theme'");
        
        if ($exists) {
            $db->update('settings', ['value' => $themeName], ['key' => 'active_theme']);
        } else {
            $db->insert('settings', [
                'key' => 'active_theme',
                'value' => $themeName,
                'group' => 'theme'
            ]);
        }
        
        $this->activeTheme = $themeName;
        
        return true;
    }
    
    /**
     * Get primary color
     */
    public function getPrimaryColor() {
        return $this->getCustomization('primary_color') ?? '#007bff';
    }
    
    /**
     * Get secondary color
     */
    public function getSecondaryColor() {
        return $this->getCustomization('secondary_color') ?? '#6c757d';
    }
    
    /**
     * Get font family
     */
    public function getFontFamily() {
        return $this->getCustomization('font_family') ?? 'Arial, sans-serif';
    }
}
