<?php

/**
 * Product Model - E-Commerce
 * Complete product management with variants, brands, options
 */
class Product extends Model {
    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $fillable = [
        'brand_id', 'name', 'slug', 'sku', 'description',
        'short_description', 'price', 'sale_price', 'cost',
        'stock_quantity', 'stock_status', 'featured_image',
        'gallery_images', 'is_featured', 'is_on_sale',
        'weight', 'length', 'width', 'height',
        'status', 'meta_title', 'meta_description'
    ];
    protected $timestamps = true;
    protected $softDelete = true;
    
    /**
     * Get published products
     */
    public function getPublished($limit = null) {
        $sql = "SELECT p.*, b.name as brand_name
                FROM {$this->table} p
                LEFT JOIN cms_brands b ON p.brand_id = b.id
                WHERE p.status = 'published' AND p.deleted_at IS NULL
                ORDER BY p.created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT ?";
            return $this->db->select($sql, [$limit]);
        }
        
        return $this->db->select($sql);
    }
    
    /**
     * Get product by slug
     */
    public function getBySlug($slug) {
        $sql = "SELECT p.*, b.name as brand_name, b.slug as brand_slug
                FROM {$this->table} p
                LEFT JOIN cms_brands b ON p.brand_id = b.id
                WHERE p.slug = ? AND p.deleted_at IS NULL";
        
        return $this->db->selectOne($sql, [$slug]);
    }
    
    /**
     * Get featured products
     */
    public function getFeatured($limit = 10) {
        $sql = "SELECT * FROM {$this->table}
                WHERE is_featured = 1 AND status = 'published'
                AND deleted_at IS NULL
                ORDER BY created_at DESC
                LIMIT ?";
        
        return $this->db->select($sql, [$limit]);
    }
    
    /**
     * Get products by category
     */
    public function getByCategory($categoryId, $limit = null) {
        $sql = "SELECT p.*, pc.category_id
                FROM {$this->table} p
                INNER JOIN cms_product_categories pc ON p.id = pc.product_id
                WHERE pc.category_id = ? AND p.status = 'published'
                AND p.deleted_at IS NULL
                ORDER BY p.created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT ?";
            return $this->db->select($sql, [$categoryId, $limit]);
        }
        
        return $this->db->select($sql, [$categoryId]);
    }
    
    /**
     * Get products by brand
     */
    public function getByBrand($brandId, $limit = null) {
        $sql = "SELECT p.*, b.name as brand_name
                FROM {$this->table} p
                LEFT JOIN cms_brands b ON p.brand_id = b.id
                WHERE p.brand_id = ? AND p.status = 'published'
                AND p.deleted_at IS NULL
                ORDER BY p.created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT ?";
            return $this->db->select($sql, [$brandId, $limit]);
        }
        
        return $this->db->select($sql, [$brandId]);
    }
    
    /**
     * Get product variants
     */
    public function getVariants($productId) {
        $sql = "SELECT * FROM cms_product_variants
                WHERE product_id = ? AND deleted_at IS NULL
                ORDER BY `order` ASC";
        
        return $this->db->select($sql, [$productId]);
    }
    
    /**
     * Get product options
     */
    public function getOptions($productId) {
        $sql = "SELECT po.*, pov.id as value_id, pov.value
                FROM cms_product_options po
                LEFT JOIN cms_product_option_values pov ON po.id = pov.option_id
                WHERE po.product_id = ?
                ORDER BY po.order, pov.order";
        
        return $this->db->select($sql, [$productId]);
    }
    
    /**
     * Search products
     */
    public function search($query, $filters = []) {
        $sql = "SELECT p.*, b.name as brand_name
                FROM {$this->table} p
                LEFT JOIN cms_brands b ON p.brand_id = b.id
                WHERE (p.name LIKE ? OR p.description LIKE ? OR p.sku LIKE ?)
                AND p.deleted_at IS NULL";
        
        $params = ["%{$query}%", "%{$query}%", "%{$query}%"];
        
        // Add filters
        if (!empty($filters['brand_id'])) {
            $sql .= " AND p.brand_id = ?";
            $params[] = $filters['brand_id'];
        }
        
        if (!empty($filters['min_price'])) {
            $sql .= " AND p.price >= ?";
            $params[] = $filters['min_price'];
        }
        
        if (!empty($filters['max_price'])) {
            $sql .= " AND p.price <= ?";
            $params[] = $filters['max_price'];
        }
        
        $sql .= " ORDER BY p.created_at DESC";
        
        return $this->db->select($sql, $params);
    }
    
    /**
     * Update stock
     */
    public function updateStock($productId, $quantity) {
        $product = $this->find($productId);
        
        if (!$product) {
            return false;
        }
        
        $newStock = $product['stock_quantity'] - $quantity;
        $stockStatus = $newStock > 0 ? 'in_stock' : 'out_of_stock';
        
        return $this->update($productId, [
            'stock_quantity' => $newStock,
            'stock_status' => $stockStatus
        ]);
    }
    
    /**
     * Get product statistics
     */
    public function getStatistics() {
        $stats = [];
        
        $stats['total'] = $this->count([]);
        $stats['published'] = $this->count(['status' => 'published']);
        $stats['draft'] = $this->count(['status' => 'draft']);
        $stats['out_of_stock'] = $this->count(['stock_status' => 'out_of_stock']);
        
        return $stats;
    }
}
