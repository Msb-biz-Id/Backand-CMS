<?php

/**
 * Brand Model - E-Commerce
 * Product brands/manufacturers
 */
class Brand extends Model {
    protected $table = 'brands';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name', 'slug', 'description', 'logo',
        'website', 'is_featured', 'order'
    ];
    protected $timestamps = true;
    protected $softDelete = true;
    
    /**
     * Get all brands
     */
    public function getAll() {
        return $this->findAll([], '`order` ASC, name ASC');
    }
    
    /**
     * Get featured brands
     */
    public function getFeatured() {
        return $this->findAll(['is_featured' => 1], '`order` ASC');
    }
    
    /**
     * Get brand with products count
     */
    public function getWithProductsCount($id) {
        $brand = $this->find($id);
        
        if (!$brand) {
            return null;
        }
        
        $sql = "SELECT COUNT(*) as count FROM cms_products 
                WHERE brand_id = ? AND deleted_at IS NULL";
        
        $result = $this->db->selectOne($sql, [$id]);
        $brand['products_count'] = $result['count'] ?? 0;
        
        return $brand;
    }
}
