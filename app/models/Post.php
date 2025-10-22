<?php

/**
 * Post Model
 * Best Practice: Complete content management with SEO and scheduling
 */
class Post extends Model {
    protected $table = 'posts';
    protected $primaryKey = 'id';
    protected $fillable = [
        'user_id', 'title', 'slug', 'excerpt', 'content', 'featured_image',
        'status', 'visibility', 'password', 'published_at', 'scheduled_at',
        'auto_archive_at', 'reading_time', 'allow_comments', 'is_featured',
        'language', 'translation_group_id'
    ];
    protected $timestamps = true;
    protected $softDelete = true;
    
    /**
     * Get published posts
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getPublished($limit = 15, $offset = 0) {
        $sql = "SELECT p.*, u.username, u.first_name, u.last_name
                FROM {$this->table} p
                LEFT JOIN cms_users u ON p.user_id = u.id
                WHERE p.status = 'published' 
                AND p.published_at <= NOW()
                AND p.deleted_at IS NULL
                ORDER BY p.published_at DESC
                LIMIT ? OFFSET ?";
        
        return $this->db->select($sql, [$limit, $offset]);
    }
    
    /**
     * Get post by slug
     * @param string $slug
     * @return array|null
     */
    public function getBySlug($slug) {
        $sql = "SELECT p.*, u.username, u.first_name, u.last_name
                FROM {$this->table} p
                LEFT JOIN cms_users u ON p.user_id = u.id
                WHERE p.slug = ? AND p.deleted_at IS NULL";
        
        return $this->db->selectOne($sql, [$slug]);
    }
    
    /**
     * Get scheduled posts
     * @return array
     */
    public function getScheduled() {
        return $this->findAll([
            'status' => 'scheduled'
        ], 'scheduled_at ASC');
    }
    
    /**
     * Publish scheduled posts
     * @return int Number of posts published
     */
    public function publishScheduled() {
        $sql = "UPDATE {$this->table}
                SET status = 'published', published_at = NOW(), updated_at = NOW()
                WHERE status = 'scheduled'
                AND scheduled_at <= NOW()
                AND deleted_at IS NULL";
        
        $stmt = $this->db->query($sql);
        return $stmt->rowCount();
    }
    
    /**
     * Auto archive old posts
     * @return int Number of posts archived
     */
    public function autoArchive() {
        $sql = "UPDATE {$this->table}
                SET status = 'archived', updated_at = NOW()
                WHERE status = 'published'
                AND auto_archive_at IS NOT NULL
                AND auto_archive_at <= NOW()
                AND deleted_at IS NULL";
        
        $stmt = $this->db->query($sql);
        return $stmt->rowCount();
    }
    
    /**
     * Increment views
     * @param int $postId
     * @return int
     */
    public function incrementViews($postId) {
        $sql = "UPDATE {$this->table} SET views = views + 1 WHERE id = ?";
        $stmt = $this->db->query($sql, [$postId]);
        return $stmt->rowCount();
    }
    
    /**
     * Get post with categories and tags
     * @param int $postId
     * @return array|null
     */
    public function getWithRelations($postId) {
        $post = $this->find($postId);
        
        if (!$post) {
            return null;
        }
        
        // Get categories
        $sql = "SELECT c.* FROM cms_categories c
                INNER JOIN cms_post_categories pc ON c.id = pc.category_id
                WHERE pc.post_id = ?";
        $post['categories'] = $this->db->select($sql, [$postId]);
        
        // Get tags
        $sql = "SELECT t.* FROM cms_tags t
                INNER JOIN cms_post_tags pt ON t.id = pt.tag_id
                WHERE pt.post_id = ?";
        $post['tags'] = $this->db->select($sql, [$postId]);
        
        // Get SEO data
        $sql = "SELECT * FROM cms_post_seo WHERE post_id = ?";
        $post['seo'] = $this->db->selectOne($sql, [$postId]);
        
        return $post;
    }
    
    /**
     * Search posts
     * @param string $query
     * @return array
     */
    public function search($query) {
        $sql = "SELECT p.*, u.username, u.first_name, u.last_name
                FROM {$this->table} p
                LEFT JOIN cms_users u ON p.user_id = u.id
                WHERE (p.title LIKE ? OR p.content LIKE ?)
                AND p.status = 'published'
                AND p.deleted_at IS NULL
                ORDER BY p.published_at DESC";
        
        $searchTerm = "%{$query}%";
        return $this->db->select($sql, [$searchTerm, $searchTerm]);
    }
    
    /**
     * Get posts by category
     * @param int $categoryId
     * @param int $limit
     * @return array
     */
    public function getByCategory($categoryId, $limit = 15) {
        $sql = "SELECT p.*, u.username, u.first_name, u.last_name
                FROM {$this->table} p
                LEFT JOIN cms_users u ON p.user_id = u.id
                INNER JOIN cms_post_categories pc ON p.id = pc.post_id
                WHERE pc.category_id = ?
                AND p.status = 'published'
                AND p.deleted_at IS NULL
                ORDER BY p.published_at DESC
                LIMIT ?";
        
        return $this->db->select($sql, [$categoryId, $limit]);
    }
    
    /**
     * Get posts by tag
     * @param int $tagId
     * @param int $limit
     * @return array
     */
    public function getByTag($tagId, $limit = 15) {
        $sql = "SELECT p.*, u.username, u.first_name, u.last_name
                FROM {$this->table} p
                LEFT JOIN cms_users u ON p.user_id = u.id
                INNER JOIN cms_post_tags pt ON p.id = pt.post_id
                WHERE pt.tag_id = ?
                AND p.status = 'published'
                AND p.deleted_at IS NULL
                ORDER BY p.published_at DESC
                LIMIT ?";
        
        return $this->db->select($sql, [$tagId, $limit]);
    }
    
    /**
     * Get featured posts
     * @param int $limit
     * @return array
     */
    public function getFeatured($limit = 5) {
        $sql = "SELECT p.*, u.username, u.first_name, u.last_name
                FROM {$this->table} p
                LEFT JOIN cms_users u ON p.user_id = u.id
                WHERE p.is_featured = 1
                AND p.status = 'published'
                AND p.deleted_at IS NULL
                ORDER BY p.published_at DESC
                LIMIT ?";
        
        return $this->db->select($sql, [$limit]);
    }
    
    /**
     * Get post statistics
     * @return array
     */
    public function getStatistics() {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'published' THEN 1 ELSE 0 END) as published,
                    SUM(CASE WHEN status = 'draft' THEN 1 ELSE 0 END) as draft,
                    SUM(CASE WHEN status = 'scheduled' THEN 1 ELSE 0 END) as scheduled,
                    SUM(views) as total_views,
                    AVG(views) as avg_views
                FROM {$this->table}
                WHERE deleted_at IS NULL";
        
        return $this->db->selectOne($sql);
    }
}
