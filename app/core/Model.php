<?php

/**
 * Base Model Class
 * Best Practice: Active Record pattern with repository methods
 * All models should extend this class
 */
abstract class Model {
    protected $db;
    protected $security;
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $timestamps = true;
    protected $softDelete = false;
    protected $perPage = 15;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->security = Security::getInstance();
    }
    
    /**
     * Find record by ID
     * @param int $id
     * @return array|null
     */
    public function find($id) {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?";
        
        if ($this->softDelete) {
            $sql .= " AND deleted_at IS NULL";
        }
        
        return $this->db->selectOne($sql, [$id]);
    }
    
    /**
     * Find all records
     * @param array $conditions
     * @param string $orderBy
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function findAll($conditions = [], $orderBy = null, $limit = null, $offset = 0) {
        $sql = "SELECT * FROM {$this->table}";
        $params = [];
        
        if ($this->softDelete) {
            $conditions['deleted_at'] = null;
        }
        
        if (!empty($conditions)) {
            $where = [];
            foreach ($conditions as $column => $value) {
                if ($value === null) {
                    $where[] = "$column IS NULL";
                } else {
                    $where[] = "$column = ?";
                    $params[] = $value;
                }
            }
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        
        if ($orderBy) {
            $sql .= " ORDER BY $orderBy";
        }
        
        if ($limit) {
            $sql .= " LIMIT $limit";
            if ($offset) {
                $sql .= " OFFSET $offset";
            }
        }
        
        return $this->db->select($sql, $params);
    }
    
    /**
     * Find one record by conditions
     * @param array $conditions
     * @return array|null
     */
    public function findOne($conditions) {
        $sql = "SELECT * FROM {$this->table}";
        $params = [];
        
        if ($this->softDelete) {
            $conditions['deleted_at'] = null;
        }
        
        $where = [];
        foreach ($conditions as $column => $value) {
            $where[] = "$column = ?";
            $params[] = $value;
        }
        
        $sql .= " WHERE " . implode(' AND ', $where);
        
        return $this->db->selectOne($sql, $params);
    }
    
    /**
     * Create new record
     * @param array $data
     * @return int|false
     */
    public function create($data) {
        // Filter only fillable fields
        $data = $this->filterFillable($data);
        
        // Add timestamps
        if ($this->timestamps) {
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');
        }
        
        return $this->db->insert($this->table, $data);
    }
    
    /**
     * Update record
     * @param int $id
     * @param array $data
     * @return int
     */
    public function update($id, $data) {
        // Filter only fillable fields
        $data = $this->filterFillable($data);
        
        // Update timestamp
        if ($this->timestamps) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }
        
        return $this->db->update($this->table, $data, [$this->primaryKey => $id]);
    }
    
    /**
     * Delete record (soft delete if enabled)
     * @param int $id
     * @return int
     */
    public function delete($id) {
        if ($this->softDelete) {
            return $this->update($id, ['deleted_at' => date('Y-m-d H:i:s')]);
        }
        
        return $this->db->delete($this->table, [$this->primaryKey => $id]);
    }
    
    /**
     * Restore soft deleted record
     * @param int $id
     * @return int
     */
    public function restore($id) {
        if (!$this->softDelete) {
            return false;
        }
        
        return $this->db->update(
            $this->table,
            ['deleted_at' => null, 'updated_at' => date('Y-m-d H:i:s')],
            [$this->primaryKey => $id]
        );
    }
    
    /**
     * Permanent delete (even if soft delete enabled)
     * @param int $id
     * @return int
     */
    public function forceDelete($id) {
        return $this->db->delete($this->table, [$this->primaryKey => $id]);
    }
    
    /**
     * Count records
     * @param array $conditions
     * @return int
     */
    public function count($conditions = []) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table}";
        $params = [];
        
        if ($this->softDelete) {
            $conditions['deleted_at'] = null;
        }
        
        if (!empty($conditions)) {
            $where = [];
            foreach ($conditions as $column => $value) {
                if ($value === null) {
                    $where[] = "$column IS NULL";
                } else {
                    $where[] = "$column = ?";
                    $params[] = $value;
                }
            }
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        
        $result = $this->db->selectOne($sql, $params);
        return (int) $result['count'];
    }
    
    /**
     * Paginate results
     * @param int $page
     * @param int $perPage
     * @param array $conditions
     * @param string $orderBy
     * @return array
     */
    public function paginate($page = 1, $perPage = null, $conditions = [], $orderBy = null) {
        $perPage = $perPage ?? $this->perPage;
        $offset = ($page - 1) * $perPage;
        
        $total = $this->count($conditions);
        $data = $this->findAll($conditions, $orderBy, $perPage, $offset);
        
        return [
            'data' => $data,
            'pagination' => [
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => ceil($total / $perPage),
                'from' => $offset + 1,
                'to' => min($offset + $perPage, $total)
            ]
        ];
    }
    
    /**
     * Filter fillable fields
     * @param array $data
     * @return array
     */
    protected function filterFillable($data) {
        if (empty($this->fillable)) {
            // If fillable is empty, use guarded to exclude fields
            return array_diff_key($data, array_flip($this->guarded));
        }
        
        // Only allow fillable fields
        return array_intersect_key($data, array_flip($this->fillable));
    }
    
    /**
     * Begin transaction
     */
    public function beginTransaction() {
        return $this->db->beginTransaction();
    }
    
    /**
     * Commit transaction
     */
    public function commit() {
        return $this->db->commit();
    }
    
    /**
     * Rollback transaction
     */
    public function rollback() {
        return $this->db->rollback();
    }
    
    /**
     * Execute raw query
     * @param string $sql
     * @param array $params
     * @return array
     */
    public function query($sql, $params = []) {
        return $this->db->select($sql, $params);
    }
    
    /**
     * Get table name
     * @return string
     */
    public function getTable() {
        return $this->table;
    }
}
