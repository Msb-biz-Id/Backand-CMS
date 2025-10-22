<?php

/**
 * Category Model
 * Untuk kategorisasi posts, products, jobs
 */
class Category extends Model {
    protected $table = 'categories';
    protected $primaryKey = 'id';
    protected $fillable = [
        'parent_id', 'name', 'slug', 'description', 'image',
        'type', 'order', 'meta_title', 'meta_description', 'language'
    ];
    protected $timestamps = true;
    protected $softDelete = true;
    
    /**
     * Get categories by type
     */
    public function getByType($type = 'post') {
        return $this->findAll(['type' => $type], '`order` ASC, name ASC');
    }
    
    /**
     * Get category by slug
     */
    public function getBySlug($slug, $type = 'post') {
        return $this->findOne(['slug' => $slug, 'type' => $type]);
    }
    
    /**
     * Get parent categories (no parent_id)
     */
    public function getParentCategories($type = 'post') {
        return $this->findAll(['parent_id' => null, 'type' => $type], '`order` ASC, name ASC');
    }
    
    /**
     * Get child categories
     */
    public function getChildren($parentId) {
        return $this->findAll(['parent_id' => $parentId], '`order` ASC, name ASC');
    }
    
    /**
     * Get category tree
     */
    public function getCategoryTree($type = 'post', $parentId = null) {
        $categories = $this->findAll(['parent_id' => $parentId, 'type' => $type], '`order` ASC');
        
        foreach ($categories as &$category) {
            $category['children'] = $this->getCategoryTree($type, $category['id']);
        }
        
        return $categories;
    }
    
    /**
     * Get posts count for category
     */
    public function getPostsCount($categoryId) {
        $sql = "SELECT COUNT(*) as count FROM cms_post_categories WHERE category_id = ?";
        $result = $this->db->selectOne($sql, [$categoryId]);
        return $result['count'] ?? 0;
    }
}
