<?php

/**
 * User Model
 * Best Practice: Complete user management with security features
 */
class User extends Model {
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $fillable = [
        'username', 'email', 'password', 'first_name', 'last_name', 
        'avatar', 'role', 'status', 'two_factor_secret', 'two_factor_enabled'
    ];
    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];
    protected $timestamps = true;
    protected $softDelete = true;
    
    /**
     * Find user by email
     * @param string $email
     * @return array|null
     */
    public function findByEmail($email) {
        return $this->findOne(['email' => $email]);
    }
    
    /**
     * Find user by username
     * @param string $username
     * @return array|null
     */
    public function findByUsername($username) {
        return $this->findOne(['username' => $username]);
    }
    
    /**
     * Create new user
     * @param array $data
     * @return int|false
     */
    public function createUser($data) {
        // Hash password
        if (isset($data['password'])) {
            $data['password'] = $this->security->hashPassword($data['password']);
        }
        
        // Set default role if not provided
        if (!isset($data['role'])) {
            $data['role'] = 'subscriber';
        }
        
        // Set default status
        if (!isset($data['status'])) {
            $data['status'] = 'active';
        }
        
        return $this->create($data);
    }
    
    /**
     * Update user password
     * @param int $userId
     * @param string $newPassword
     * @return int
     */
    public function updatePassword($userId, $newPassword) {
        $hashedPassword = $this->security->hashPassword($newPassword);
        return $this->update($userId, ['password' => $hashedPassword]);
    }
    
    /**
     * Verify user credentials
     * @param string $identifier (email or username)
     * @param string $password
     * @return array|false
     */
    public function verifyCredentials($identifier, $password) {
        // Find user by email or username
        $user = $this->findByEmail($identifier);
        if (!$user) {
            $user = $this->findByUsername($identifier);
        }
        
        if (!$user) {
            return false;
        }
        
        // Check if user is active
        if ($user['status'] !== 'active') {
            throw new Exception('Your account is not active. Please contact administrator.');
        }
        
        // Verify password
        if (!$this->security->verifyPassword($password, $user['password'])) {
            return false;
        }
        
        // Remove password from returned data
        unset($user['password']);
        
        return $user;
    }
    
    /**
     * Update last login
     * @param int $userId
     * @param string $ipAddress
     * @return int
     */
    public function updateLastLogin($userId, $ipAddress) {
        return $this->update($userId, [
            'last_login_at' => date('Y-m-d H:i:s'),
            'last_login_ip' => $ipAddress
        ]);
    }
    
    /**
     * Get users by role
     * @param string $role
     * @return array
     */
    public function getUsersByRole($role) {
        return $this->findAll(['role' => $role]);
    }
    
    /**
     * Count users by status
     * @param string $status
     * @return int
     */
    public function countByStatus($status = 'active') {
        return $this->count(['status' => $status]);
    }
    
    /**
     * Search users
     * @param string $query
     * @return array
     */
    public function search($query) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE (username LIKE ? OR email LIKE ? OR first_name LIKE ? OR last_name LIKE ?)
                AND deleted_at IS NULL";
        
        $searchTerm = "%{$query}%";
        return $this->db->select($sql, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
    }
    
    /**
     * Get user statistics
     * @return array
     */
    public function getStatistics() {
        $sql = "SELECT 
                    role,
                    COUNT(*) as count,
                    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_count,
                    SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) as today_count
                FROM {$this->table}
                WHERE deleted_at IS NULL
                GROUP BY role";
        
        return $this->db->select($sql);
    }
}
