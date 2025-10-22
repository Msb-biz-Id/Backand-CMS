<?php

/**
 * MenuItem Model
 * For menu item management
 */
class MenuItem extends Model {
    protected $table = 'menu_items';
    protected $primaryKey = 'id';
    protected $fillable = [
        'menu_id', 'parent_id', 'title', 'url', 'target',
        'icon', 'css_class', 'order', 'type', 'object_id'
    ];
    protected $timestamps = true;
    protected $softDelete = true;
    
    /**
     * Create menu item
     */
    public function createItem($data) {
        // Get max order for this menu
        $sql = "SELECT MAX(`order`) as max_order FROM {$this->table} 
                WHERE menu_id = ? AND parent_id = ?";
        
        $result = $this->db->selectOne($sql, [
            $data['menu_id'],
            $data['parent_id'] ?? null
        ]);
        
        $data['order'] = ($result['max_order'] ?? 0) + 1;
        
        return $this->create($data);
    }
    
    /**
     * Reorder menu items
     */
    public function reorder($menuId, $items) {
        foreach ($items as $order => $itemId) {
            $this->update($itemId, ['order' => $order + 1]);
        }
        
        return true;
    }
    
    /**
     * Get item with children count
     */
    public function getWithChildrenCount($id) {
        $item = $this->find($id);
        
        if (!$item) {
            return null;
        }
        
        $sql = "SELECT COUNT(*) as count FROM {$this->table} 
                WHERE parent_id = ? AND deleted_at IS NULL";
        
        $result = $this->db->selectOne($sql, [$id]);
        $item['children_count'] = $result['count'] ?? 0;
        
        return $item;
    }
}
