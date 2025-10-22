<?php

/**
 * Page Model
 * Untuk static pages (About, Contact, etc)
 */
class Page extends Model {
    protected $table = 'pages';
    protected $primaryKey = 'id';
    protected $fillable = [
        'user_id', 'parent_id', 'title', 'slug', 'content',
        'template', 'featured_image', 'status', 'order',
        'show_in_menu', 'menu_order', 'language', 'translation_group_id'
    ];
    protected $timestamps = true;
    protected $softDelete = true;
    
    /**
     * Get published pages
     */
    public function getPublished($limit = null) {
        $sql = "SELECT * FROM {$this->table}
                WHERE status = 'published'
                AND deleted_at IS NULL
                ORDER BY `order` ASC, title ASC";
        
        if ($limit) {
            $sql .= " LIMIT ?";
            return $this->db->select($sql, [$limit]);
        }
        
        return $this->db->select($sql);
    }
    
    /**
     * Get page by slug
     */
    public function getBySlug($slug) {
        $sql = "SELECT p.*, u.username, u.first_name, u.last_name
                FROM {$this->table} p
                LEFT JOIN cms_users u ON p.user_id = u.id
                WHERE p.slug = ? AND p.deleted_at IS NULL";
        
        return $this->db->selectOne($sql, [$slug]);
    }
    
    /**
     * Get page with SEO data
     */
    public function getWithSEO($id) {
        $page = $this->find($id);
        
        if (!$page) {
            return null;
        }
        
        // Get SEO data
        $sql = "SELECT * FROM cms_page_seo WHERE page_id = ?";
        $page['seo'] = $this->db->selectOne($sql, [$id]);
        
        // Get parent page if exists
        if ($page['parent_id']) {
            $page['parent'] = $this->find($page['parent_id']);
        }
        
        return $page;
    }
    
    /**
     * Get child pages
     */
    public function getChildren($parentId) {
        return $this->findAll(['parent_id' => $parentId], '`order` ASC, title ASC');
    }
    
    /**
     * Get menu pages
     */
    public function getMenuPages() {
        return $this->findAll(['show_in_menu' => 1], 'menu_order ASC, title ASC');
    }
    
    /**
     * Get page hierarchy (tree structure)
     */
    public function getPageTree($parentId = null) {
        $pages = $this->findAll(['parent_id' => $parentId], '`order` ASC');
        
        foreach ($pages as &$page) {
            $page['children'] = $this->getPageTree($page['id']);
        }
        
        return $pages;
    }
    
    /**
     * Search pages
     */
    public function search($query) {
        $sql = "SELECT p.*, u.username
                FROM {$this->table} p
                LEFT JOIN cms_users u ON p.user_id = u.id
                WHERE (p.title LIKE ? OR p.content LIKE ?)
                AND p.deleted_at IS NULL
                ORDER BY p.created_at DESC";
        
        $searchTerm = "%{$query}%";
        return $this->db->select($sql, [$searchTerm, $searchTerm]);
    }
}
