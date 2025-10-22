<?php

/**
 * Tag Model
 * Untuk tagging posts, products, jobs
 */
class Tag extends Model {
    protected $table = 'tags';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name', 'slug', 'description', 'type',
        'meta_title', 'meta_description', 'language'
    ];
    protected $timestamps = true;
    protected $softDelete = true;
    
    /**
     * Get tags by type
     */
    public function getByType($type = 'post') {
        return $this->findAll(['type' => $type], 'name ASC');
    }
    
    /**
     * Get tag by slug
     */
    public function getBySlug($slug, $type = 'post') {
        return $this->findOne(['slug' => $slug, 'type' => $type]);
    }
    
    /**
     * Get popular tags
     */
    public function getPopular($type = 'post', $limit = 10) {
        $sql = "SELECT t.*, COUNT(pt.post_id) as posts_count
                FROM {$this->table} t
                LEFT JOIN cms_post_tags pt ON t.id = pt.tag_id
                WHERE t.type = ? AND t.deleted_at IS NULL
                GROUP BY t.id
                ORDER BY posts_count DESC
                LIMIT ?";
        
        return $this->db->select($sql, [$type, $limit]);
    }
    
    /**
     * Get posts count for tag
     */
    public function getPostsCount($tagId) {
        $sql = "SELECT COUNT(*) as count FROM cms_post_tags WHERE tag_id = ?";
        $result = $this->db->selectOne($sql, [$tagId]);
        return $result['count'] ?? 0;
    }
    
    /**
     * Search tags
     */
    public function search($query, $type = 'post') {
        $sql = "SELECT * FROM {$this->table}
                WHERE (name LIKE ? OR description LIKE ?)
                AND type = ?
                AND deleted_at IS NULL
                ORDER BY name ASC";
        
        $searchTerm = "%{$query}%";
        return $this->db->select($sql, [$searchTerm, $searchTerm, $type]);
    }
}
