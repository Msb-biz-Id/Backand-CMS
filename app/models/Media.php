<?php

/**
 * Media Model
 * Untuk manajemen media library (images, videos, documents)
 */
class Media extends Model {
    protected $table = 'media';
    protected $primaryKey = 'id';
    protected $fillable = [
        'user_id', 'filename', 'original_filename', 'file_path', 'file_url',
        'mime_type', 'file_size', 'file_extension', 'type',
        'width', 'height', 'duration', 'title', 'alt_text', 
        'caption', 'description', 'folder', 'is_optimized'
    ];
    protected $timestamps = true;
    protected $softDelete = true;
    
    /**
     * Get media by type
     */
    public function getByType($type, $limit = 50) {
        return $this->findAll(['type' => $type], 'created_at DESC', $limit);
    }
    
    /**
     * Get media by user
     */
    public function getByUser($userId, $limit = 50) {
        return $this->findAll(['user_id' => $userId], 'created_at DESC', $limit);
    }
    
    /**
     * Search media
     */
    public function search($query) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE (filename LIKE ? OR title LIKE ? OR alt_text LIKE ? OR description LIKE ?)
                AND deleted_at IS NULL
                ORDER BY created_at DESC";
        
        $searchTerm = "%{$query}%";
        return $this->db->select($sql, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
    }
    
    /**
     * Get total storage used
     */
    public function getTotalStorage($userId = null) {
        $sql = "SELECT SUM(file_size) as total FROM {$this->table} WHERE deleted_at IS NULL";
        $params = [];
        
        if ($userId) {
            $sql .= " AND user_id = ?";
            $params[] = $userId;
        }
        
        $result = $this->db->selectOne($sql, $params);
        return $result['total'] ?? 0;
    }
    
    /**
     * Get media statistics
     */
    public function getStatistics() {
        $sql = "SELECT 
                    type,
                    COUNT(*) as count,
                    SUM(file_size) as total_size
                FROM {$this->table}
                WHERE deleted_at IS NULL
                GROUP BY type";
        
        return $this->db->select($sql);
    }
    
    /**
     * Attach media to content
     */
    public function attachTo($mediaId, $type, $id, $fieldName = null) {
        return $this->db->insert('media_relationships', [
            'media_id' => $mediaId,
            'mediable_type' => $type,
            'mediable_id' => $id,
            'field_name' => $fieldName
        ]);
    }
    
    /**
     * Get attached media
     */
    public function getAttached($type, $id, $fieldName = null) {
        $sql = "SELECT m.* FROM {$this->table} m
                INNER JOIN cms_media_relationships mr ON m.id = mr.media_id
                WHERE mr.mediable_type = ? AND mr.mediable_id = ?";
        
        $params = [$type, $id];
        
        if ($fieldName) {
            $sql .= " AND mr.field_name = ?";
            $params[] = $fieldName;
        }
        
        $sql .= " ORDER BY mr.order ASC";
        
        return $this->db->select($sql, $params);
    }
}
