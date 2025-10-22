<?php

/**
 * Cart Model - E-Commerce
 * Shopping cart management
 */
class Cart extends Model {
    protected $table = 'cart';
    protected $primaryKey = 'id';
    protected $fillable = [
        'user_id', 'session_id', 'product_id', 'variant_id',
        'quantity', 'price', 'options'
    ];
    protected $timestamps = true;
    
    /**
     * Get cart items
     */
    public function getCartItems($userId = null, $sessionId = null) {
        $sql = "SELECT c.*, p.name as product_name, p.slug, p.featured_image,
                       p.stock_quantity, p.stock_status
                FROM {$this->table} c
                INNER JOIN cms_products p ON c.product_id = p.id
                WHERE ";
        
        if ($userId) {
            $sql .= "c.user_id = ?";
            $params = [$userId];
        } else {
            $sql .= "c.session_id = ?";
            $params = [$sessionId];
        }
        
        $sql .= " ORDER BY c.created_at DESC";
        
        return $this->db->select($sql, $params);
    }
    
    /**
     * Add to cart
     */
    public function addToCart($data) {
        // Check if item already exists
        $existing = $this->findOne([
            'user_id' => $data['user_id'] ?? null,
            'session_id' => $data['session_id'] ?? null,
            'product_id' => $data['product_id'],
            'variant_id' => $data['variant_id'] ?? null
        ]);
        
        if ($existing) {
            // Update quantity
            return $this->update($existing['id'], [
                'quantity' => $existing['quantity'] + $data['quantity']
            ]);
        } else {
            // Add new item
            return $this->create($data);
        }
    }
    
    /**
     * Update cart item quantity
     */
    public function updateQuantity($cartId, $quantity) {
        return $this->update($cartId, ['quantity' => $quantity]);
    }
    
    /**
     * Remove from cart
     */
    public function removeItem($cartId) {
        return $this->db->delete($this->table, ['id' => $cartId]);
    }
    
    /**
     * Clear cart
     */
    public function clearCart($userId = null, $sessionId = null) {
        if ($userId) {
            return $this->db->delete($this->table, ['user_id' => $userId]);
        } else {
            return $this->db->delete($this->table, ['session_id' => $sessionId]);
        }
    }
    
    /**
     * Get cart total
     */
    public function getCartTotal($userId = null, $sessionId = null) {
        $items = $this->getCartItems($userId, $sessionId);
        $total = 0;
        
        foreach ($items as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        
        return $total;
    }
    
    /**
     * Get cart count
     */
    public function getCartCount($userId = null, $sessionId = null) {
        $sql = "SELECT SUM(quantity) as count FROM {$this->table} WHERE ";
        
        if ($userId) {
            $sql .= "user_id = ?";
            $params = [$userId];
        } else {
            $sql .= "session_id = ?";
            $params = [$sessionId];
        }
        
        $result = $this->db->selectOne($sql, $params);
        return $result['count'] ?? 0;
    }
}
