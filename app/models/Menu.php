<?php

/**
 * Menu Model
 * For navigation menu management
 */
class Menu extends Model {
    protected $table = 'menus';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name', 'slug', 'location', 'description'
    ];
    protected $timestamps = true;
    protected $softDelete = true;
    
    /**
     * Get menu by location
     */
    public function getByLocation($location) {
        return $this->findOne(['location' => $location]);
    }
    
    /**
     * Get all menu locations
     */
    public function getLocations() {
        return [
            'primary' => 'Primary Menu',
            'footer' => 'Footer Menu',
            'sidebar' => 'Sidebar Menu',
            'mobile' => 'Mobile Menu'
        ];
    }
    
    /**
     * Get menu with items
     */
    public function getMenuWithItems($menuId) {
        $menu = $this->find($menuId);
        
        if (!$menu) {
            return null;
        }
        
        // Get menu items
        $sql = "SELECT * FROM cms_menu_items 
                WHERE menu_id = ? AND deleted_at IS NULL
                ORDER BY `order` ASC";
        
        $menu['items'] = $this->db->select($sql, [$menuId]);
        
        // Build hierarchy
        $menu['items'] = $this->buildMenuTree($menu['items']);
        
        return $menu;
    }
    
    /**
     * Build menu tree (hierarchical structure)
     */
    private function buildMenuTree($items, $parentId = null) {
        $branch = [];
        
        foreach ($items as $item) {
            if ($item['parent_id'] == $parentId) {
                $children = $this->buildMenuTree($items, $item['id']);
                if ($children) {
                    $item['children'] = $children;
                }
                $branch[] = $item;
            }
        }
        
        return $branch;
    }
    
    /**
     * Get flat menu items
     */
    public function getMenuItems($menuId) {
        $sql = "SELECT * FROM cms_menu_items 
                WHERE menu_id = ? AND deleted_at IS NULL
                ORDER BY `order` ASC";
        
        return $this->db->select($sql, [$menuId]);
    }
}
