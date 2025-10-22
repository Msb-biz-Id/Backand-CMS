<?php

/**
 * Job Model
 * Handles job/career postings
 */
class Job extends Model {
    protected $table = 'jobs';
    protected $primaryKey = 'id';
    protected $fillable = [
        'title', 'slug', 'company', 'location', 'job_type', 
        'salary_min', 'salary_max', 'description', 'requirements', 
        'benefits', 'status', 'deadline', 'meta_title', 'meta_description'
    ];
    protected $softDelete = true;
    
    /**
     * Get active jobs
     */
    public function getActive($limit = null) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE status = 'active' 
                AND (deadline IS NULL OR deadline >= CURDATE())
                AND deleted_at IS NULL
                ORDER BY created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT $limit";
        }
        
        return $this->db->select($sql);
    }
    
    /**
     * Get job by slug
     */
    public function getBySlug($slug) {
        return $this->findOne(['slug' => $slug]);
    }
    
    /**
     * Get jobs by type
     */
    public function getByType($type, $limit = null) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE job_type = ? 
                AND status = 'active'
                AND (deadline IS NULL OR deadline >= CURDATE())
                AND deleted_at IS NULL
                ORDER BY created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT $limit";
        }
        
        return $this->db->select($sql, [$type]);
    }
    
    /**
     * Get jobs by location
     */
    public function getByLocation($location, $limit = null) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE location LIKE ?
                AND status = 'active'
                AND deleted_at IS NULL
                ORDER BY created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT $limit";
        }
        
        return $this->db->select($sql, ["%$location%"]);
    }
    
    /**
     * Search jobs
     */
    public function search($query) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE (title LIKE ? OR description LIKE ? OR company LIKE ? OR location LIKE ?)
                AND status = 'active'
                AND (deadline IS NULL OR deadline >= CURDATE())
                AND deleted_at IS NULL
                ORDER BY created_at DESC";
        
        $searchTerm = "%$query%";
        return $this->db->select($sql, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
    }
    
    /**
     * Get statistics
     */
    public function getStatistics() {
        $total = $this->count(['status' => 'active']);
        $fullTime = $this->count(['status' => 'active', 'job_type' => 'full-time']);
        $partTime = $this->count(['status' => 'active', 'job_type' => 'part-time']);
        $remote = $this->count(['status' => 'active', 'job_type' => 'remote']);
        
        return [
            'total' => $total,
            'full_time' => $fullTime,
            'part_time' => $partTime,
            'remote' => $remote
        ];
    }
    
    /**
     * Increment views
     */
    public function incrementViews($id) {
        $sql = "UPDATE {$this->table} SET views = views + 1 WHERE id = ?";
        return $this->db->query($sql, [$id]);
    }
}
