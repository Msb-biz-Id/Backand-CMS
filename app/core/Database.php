<?php

/**
 * Database Core Class
 * Best Practice: Singleton pattern for database connection
 * Security: Prepared statements, SQL injection prevention
 */
class Database {
    private static $instance = null;
    private $connection;
    private $config;
    private $queryLog = [];
    
    /**
     * Private constructor to prevent direct instantiation (Singleton)
     */
    private function __construct() {
        $this->config = require __DIR__ . '/../../config/config.php';
        $this->connect();
    }
    
    /**
     * Get singleton instance
     * @return Database
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Establish database connection
     * Security: Secure connection with proper error handling
     */
    private function connect() {
        try {
            $dbConfig = $this->config['database']['connections'][$this->config['database']['default']];
            $options = $this->config['database']['options'];
            
            $dsn = sprintf(
                "%s:host=%s;port=%s;dbname=%s;charset=%s",
                $dbConfig['driver'],
                $dbConfig['host'],
                $dbConfig['port'],
                $dbConfig['database'],
                $dbConfig['charset']
            );
            
            $this->connection = new PDO(
                $dsn,
                $dbConfig['username'],
                $dbConfig['password'],
                $options
            );
            
            // Set timezone
            $this->connection->exec("SET time_zone = '+00:00'");
            
        } catch (PDOException $e) {
            // Security: Don't expose database credentials in error messages
            if ($this->config['app']['debug']) {
                throw new Exception("Database Connection Error: " . $e->getMessage());
            } else {
                error_log("Database Connection Error: " . $e->getMessage());
                throw new Exception("Database connection failed. Please contact administrator.");
            }
        }
    }
    
    /**
     * Get PDO connection
     * @return PDO
     */
    public function getConnection() {
        return $this->connection;
    }
    
    /**
     * Execute a query with prepared statements (Security: SQL Injection Prevention)
     * @param string $sql
     * @param array $params
     * @return PDOStatement
     */
    public function query($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            
            // Bind parameters with appropriate data types
            if (!empty($params)) {
                foreach ($params as $key => $value) {
                    $param = is_numeric($key) ? $key + 1 : $key;
                    $type = $this->getPDOType($value);
                    $stmt->bindValue($param, $value, $type);
                }
            }
            
            $stmt->execute();
            
            // Log query for debugging (only in development)
            if ($this->config['logging']['log_queries'] && $this->config['app']['env'] === 'development') {
                $this->queryLog[] = [
                    'sql' => $sql,
                    'params' => $params,
                    'time' => microtime(true)
                ];
            }
            
            return $stmt;
        } catch (PDOException $e) {
            $this->handleQueryError($e, $sql, $params);
        }
    }
    
    /**
     * Get PDO parameter type
     * @param mixed $value
     * @return int
     */
    private function getPDOType($value) {
        if (is_int($value)) {
            return PDO::PARAM_INT;
        } elseif (is_bool($value)) {
            return PDO::PARAM_BOOL;
        } elseif (is_null($value)) {
            return PDO::PARAM_NULL;
        }
        return PDO::PARAM_STR;
    }
    
    /**
     * Select query
     * @param string $sql
     * @param array $params
     * @return array
     */
    public function select($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    /**
     * Select single row
     * @param string $sql
     * @param array $params
     * @return array|null
     */
    public function selectOne($sql, $params = []) {
        $stmt = $this->query($sql, $params);
        $result = $stmt->fetch();
        return $result ?: null;
    }
    
    /**
     * Insert query
     * @param string $table
     * @param array $data
     * @return int|false Last insert ID
     */
    public function insert($table, $data) {
        $table = $this->sanitizeTableName($table);
        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');
        
        $sql = sprintf(
            "INSERT INTO %s (%s) VALUES (%s)",
            $table,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );
        
        $this->query($sql, array_values($data));
        return $this->connection->lastInsertId();
    }
    
    /**
     * Update query
     * @param string $table
     * @param array $data
     * @param array $where
     * @return int Affected rows
     */
    public function update($table, $data, $where) {
        $table = $this->sanitizeTableName($table);
        $set = [];
        $params = [];
        
        foreach ($data as $column => $value) {
            $set[] = "$column = ?";
            $params[] = $value;
        }
        
        $whereClause = [];
        foreach ($where as $column => $value) {
            $whereClause[] = "$column = ?";
            $params[] = $value;
        }
        
        $sql = sprintf(
            "UPDATE %s SET %s WHERE %s",
            $table,
            implode(', ', $set),
            implode(' AND ', $whereClause)
        );
        
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }
    
    /**
     * Delete query
     * @param string $table
     * @param array $where
     * @return int Affected rows
     */
    public function delete($table, $where) {
        $table = $this->sanitizeTableName($table);
        $whereClause = [];
        $params = [];
        
        foreach ($where as $column => $value) {
            $whereClause[] = "$column = ?";
            $params[] = $value;
        }
        
        $sql = sprintf(
            "DELETE FROM %s WHERE %s",
            $table,
            implode(' AND ', $whereClause)
        );
        
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }
    
    /**
     * Begin transaction
     */
    public function beginTransaction() {
        return $this->connection->beginTransaction();
    }
    
    /**
     * Commit transaction
     */
    public function commit() {
        return $this->connection->commit();
    }
    
    /**
     * Rollback transaction
     */
    public function rollback() {
        return $this->connection->rollback();
    }
    
    /**
     * Sanitize table name (Security: Prevent SQL injection in table names)
     * @param string $table
     * @return string
     */
    private function sanitizeTableName($table) {
        $prefix = $this->config['database']['connections'][$this->config['database']['default']]['prefix'];
        // Remove any non-alphanumeric characters except underscore
        $table = preg_replace('/[^a-zA-Z0-9_]/', '', $table);
        return $prefix . $table;
    }
    
    /**
     * Handle query errors
     * @param PDOException $e
     * @param string $sql
     * @param array $params
     */
    private function handleQueryError($e, $sql, $params) {
        $error = [
            'message' => $e->getMessage(),
            'sql' => $sql,
            'params' => $params,
            'trace' => $e->getTraceAsString(),
            'time' => date('Y-m-d H:i:s')
        ];
        
        // Log error
        error_log(json_encode($error));
        
        if ($this->config['app']['debug']) {
            throw new Exception("Query Error: " . $e->getMessage() . "\nSQL: " . $sql);
        } else {
            throw new Exception("A database error occurred. Please try again.");
        }
    }
    
    /**
     * Get query log
     * @return array
     */
    public function getQueryLog() {
        return $this->queryLog;
    }
    
    /**
     * Prevent cloning (Singleton)
     */
    private function __clone() {}
    
    /**
     * Prevent unserialization (Singleton)
     */
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}
