<?php

/**
 * Comment Model
 * For comment management
 */
class Comment extends Model {
    protected $table = 'comments';
    protected $primaryKey = 'id';
    protected $fillable = [
        'post_id', 'parent_id', 'user_id', 'author_name',
        'author_email', 'author_website', 'author_ip',
        'content', 'status', 'user_agent'
    ];
    protected $timestamps = true;
    protected $softDelete = true;
    
    /**
     * Get comments for a post
     */
    public function getByPost($postId, $status = 'approved') {
        $sql = "SELECT c.*, u.username, u.first_name, u.last_name
                FROM {$this->table} c
                LEFT JOIN cms_users u ON c.user_id = u.id
                WHERE c.post_id = ? AND c.status = ? AND c.deleted_at IS NULL
                ORDER BY c.created_at DESC";
        
        return $this->db->select($sql, [$postId, $status]);
    }
    
    /**
     * Get comments with post info
     */
    public function getWithPostInfo($limit = null) {
        $sql = "SELECT c.*, p.title as post_title, p.slug as post_slug,
                       u.username, u.first_name, u.last_name
                FROM {$this->table} c
                LEFT JOIN cms_posts p ON c.post_id = p.id
                LEFT JOIN cms_users u ON c.user_id = u.id
                WHERE c.deleted_at IS NULL
                ORDER BY c.created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT ?";
            return $this->db->select($sql, [$limit]);
        }
        
        return $this->db->select($sql);
    }
    
    /**
     * Get pending comments
     */
    public function getPending() {
        return $this->findAll(['status' => 'pending'], 'created_at DESC');
    }
    
    /**
     * Get comment count by status
     */
    public function countByStatus($status) {
        return $this->count(['status' => $status]);
    }
    
    /**
     * Approve comment
     */
    public function approve($id) {
        return $this->update($id, ['status' => 'approved']);
    }
    
    /**
     * Reject comment
     */
    public function reject($id) {
        return $this->update($id, ['status' => 'rejected']);
    }
    
    /**
     * Mark as spam
     */
    public function markAsSpam($id) {
        return $this->update($id, ['status' => 'spam']);
    }
    
    /**
     * Get comment statistics
     */
    public function getStatistics() {
        return [
            'total' => $this->count([]),
            'approved' => $this->countByStatus('approved'),
            'pending' => $this->countByStatus('pending'),
            'spam' => $this->countByStatus('spam')
        ];
    }
    
    /**
     * Build comment tree (threaded comments)
     */
    public function buildCommentTree($comments, $parentId = null) {
        $branch = [];
        
        foreach ($comments as $comment) {
            if ($comment['parent_id'] == $parentId) {
                $children = $this->buildCommentTree($comments, $comment['id']);
                if ($children) {
                    $comment['children'] = $children;
                }
                $branch[] = $comment;
            }
        }
        
        return $branch;
    }
}
