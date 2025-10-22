<?php

/**
 * Module System
 * 
 * BERBEDA DARI WORDPRESS PLUGIN:
 * - WordPress: Plugin = File-based dengan hooks
 * - Advanced CMS: Module = Function-based dengan dependency injection
 * - Modules are pure PHP classes dengan clear interface
 * - No file scanning, all registered explicitly
 */
class Module {
    private static $modules = [];
    private static $activeModules = [];
    
    /**
     * Register module
     */
    public static function register($name, $className, $config = []) {
        self::$modules[$name] = [
            'class' => $className,
            'config' => $config,
            'active' => false
        ];
    }
    
    /**
     * Activate module
     */
    public static function activate($name) {
        if (!isset(self::$modules[$name])) {
            throw new Exception("Module {$name} not registered");
        }
        
        $module = self::$modules[$name];
        $className = $module['class'];
        
        // Check if class exists
        if (!class_exists($className)) {
            throw new Exception("Module class {$className} not found");
        }
        
        // Instantiate module
        $instance = new $className($module['config']);
        
        // Call install method if exists
        if (method_exists($instance, 'install')) {
            $instance->install();
        }
        
        // Call activate method if exists
        if (method_exists($instance, 'activate')) {
            $instance->activate();
        }
        
        // Save to database
        $db = Database::getInstance();
        $db->insert('modules', [
            'name' => $name,
            'class' => $className,
            'config' => json_encode($module['config']),
            'active' => 1
        ]);
        
        self::$modules[$name]['active'] = true;
        self::$activeModules[$name] = $instance;
        
        return true;
    }
    
    /**
     * Deactivate module
     */
    public static function deactivate($name) {
        if (!isset(self::$activeModules[$name])) {
            return false;
        }
        
        $instance = self::$activeModules[$name];
        
        // Call deactivate method if exists
        if (method_exists($instance, 'deactivate')) {
            $instance->deactivate();
        }
        
        // Update database
        $db = Database::getInstance();
        $db->update('modules', ['active' => 0], ['name' => $name]);
        
        self::$modules[$name]['active'] = false;
        unset(self::$activeModules[$name]);
        
        return true;
    }
    
    /**
     * Get active modules
     */
    public static function getActive() {
        return self::$activeModules;
    }
    
    /**
     * Check if module is active
     */
    public static function isActive($name) {
        return isset(self::$activeModules[$name]);
    }
    
    /**
     * Get module instance
     */
    public static function get($name) {
        return self::$activeModules[$name] ?? null;
    }
    
    /**
     * Load active modules from database
     */
    public static function loadActive() {
        $db = Database::getInstance();
        $modules = $db->select("SELECT * FROM cms_modules WHERE active = 1");
        
        foreach ($modules as $module) {
            $name = $module['name'];
            $className = $module['class'];
            $config = json_decode($module['config'], true);
            
            if (class_exists($className)) {
                $instance = new $className($config);
                
                // Call init method if exists
                if (method_exists($instance, 'init')) {
                    $instance->init();
                }
                
                self::$activeModules[$name] = $instance;
            }
        }
    }
}

/**
 * Base Module Interface
 */
interface ModuleInterface {
    public function install();
    public function activate();
    public function deactivate();
    public function uninstall();
    public function init();
}
