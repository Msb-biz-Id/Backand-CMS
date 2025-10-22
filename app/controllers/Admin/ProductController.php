<?php

/**
 * Admin Product Controller - E-Commerce
 * Complete product management
 */
class ProductController extends Controller {
    private $productModel;
    private $brandModel;
    private $categoryModel;
    
    public function __construct() {
        parent::__construct();
        $this->productModel = new Product();
        $this->brandModel = new Brand();
        $this->categoryModel = new Category();
    }
    
    /**
     * List all products
     */
    public function index() {
        $this->requireAuth();
        
        $page = $this->input('page', 1);
        $search = $this->input('search');
        $brand = $this->input('brand');
        $status = $this->input('status');
        
        // Build filters
        $filters = [];
        if ($brand) $filters['brand_id'] = $brand;
        if ($status) $filters['status'] = $status;
        
        if ($search) {
            $products = $this->productModel->search($search, $filters);
            $pagination = null;
        } else {
            $result = $this->productModel->paginate($page, 20, $filters, 'created_at DESC');
            $products = $result['data'];
            $pagination = $result['pagination'];
        }
        
        // Get brands for filter
        $brands = $this->brandModel->getAll();
        
        // Get statistics
        $stats = $this->productModel->getStatistics();
        
        $this->seo->setTitle('Products');
        $this->seo->setRobots('noindex, nofollow');
        
        $this->view('admin/products/index', [
            'products' => $products,
            'pagination' => $pagination,
            'brands' => $brands,
            'stats' => $stats,
            'search' => $search,
            'brandFilter' => $brand,
            'statusFilter' => $status
        ]);
    }
    
    /**
     * Create product form
     */
    public function create() {
        $this->requireAuth();
        
        $brands = $this->brandModel->getAll();
        $categories = $this->categoryModel->getByType('product');
        
        $this->seo->setTitle('Add New Product');
        $this->seo->setRobots('noindex, nofollow');
        
        $this->view('admin/products/create', [
            'brands' => $brands,
            'categories' => $categories,
            'extraJs' => ['https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js']
        ]);
    }
    
    /**
     * Store new product
     */
    public function store() {
        $this->requireAuth();
        
        try {
            $data = $this->validate([
                'name' => 'required|min:3|max:255',
                'slug' => 'required|slug|unique:products,slug',
                'sku' => 'required|unique:products,sku',
                'brand_id' => 'numeric',
                'price' => 'required|numeric',
                'sale_price' => 'numeric',
                'cost' => 'numeric',
                'stock_quantity' => 'required|numeric',
                'weight' => 'numeric',
                'description' => 'required',
                'short_description' => '',
                'status' => 'required|in:draft,published',
                'is_featured' => '',
                'featured_image' => ''
            ]);
            
            $data['is_featured'] = isset($data['is_featured']) ? 1 : 0;
            $data['is_on_sale'] = !empty($data['sale_price']) && $data['sale_price'] < $data['price'] ? 1 : 0;
            $data['stock_status'] = $data['stock_quantity'] > 0 ? 'in_stock' : 'out_of_stock';
            
            $productId = $this->productModel->create($data);
            
            if (!$productId) {
                throw new Exception('Failed to create product');
            }
            
            // Add categories
            $categories = $this->input('categories', []);
            foreach ($categories as $categoryId) {
                $this->db->insert('product_categories', [
                    'product_id' => $productId,
                    'category_id' => $categoryId
                ]);
            }
            
            $this->logActivity('create', 'product', $productId, 'Created product: ' . $data['name']);
            
            $this->setFlash('success', 'Product created successfully!');
            $this->redirect('/admin/products');
            
        } catch (Exception $e) {
            $this->setFlash('error', $e->getMessage());
            $this->back();
        }
    }
    
    /**
     * Edit product form
     */
    public function edit($id) {
        $this->requireAuth();
        
        $product = $this->productModel->find($id);
        
        if (!$product) {
            $this->setFlash('error', 'Product not found');
            $this->redirect('/admin/products');
        }
        
        $brands = $this->brandModel->getAll();
        $categories = $this->categoryModel->getByType('product');
        
        // Get product categories
        $sql = "SELECT category_id FROM cms_product_categories WHERE product_id = ?";
        $productCategories = $this->db->select($sql, [$id]);
        $product['categories'] = array_column($productCategories, 'category_id');
        
        $this->seo->setTitle('Edit Product: ' . $product['name']);
        $this->seo->setRobots('noindex, nofollow');
        
        $this->view('admin/products/edit', [
            'product' => $product,
            'brands' => $brands,
            'categories' => $categories,
            'extraJs' => ['https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js']
        ]);
    }
    
    /**
     * Update product
     */
    public function update($id) {
        $this->requireAuth();
        
        try {
            $product = $this->productModel->find($id);
            
            if (!$product) {
                throw new Exception('Product not found');
            }
            
            $data = $this->validate([
                'name' => 'required|min:3|max:255',
                'slug' => 'required|slug',
                'sku' => 'required',
                'brand_id' => 'numeric',
                'price' => 'required|numeric',
                'sale_price' => 'numeric',
                'cost' => 'numeric',
                'stock_quantity' => 'required|numeric',
                'weight' => 'numeric',
                'description' => 'required',
                'short_description' => '',
                'status' => 'required|in:draft,published',
                'is_featured' => '',
                'featured_image' => ''
            ]);
            
            $data['is_featured'] = isset($data['is_featured']) ? 1 : 0;
            $data['is_on_sale'] = !empty($data['sale_price']) && $data['sale_price'] < $data['price'] ? 1 : 0;
            $data['stock_status'] = $data['stock_quantity'] > 0 ? 'in_stock' : 'out_of_stock';
            
            $this->productModel->update($id, $data);
            
            // Update categories
            $this->db->delete('product_categories', ['product_id' => $id]);
            $categories = $this->input('categories', []);
            foreach ($categories as $categoryId) {
                $this->db->insert('product_categories', [
                    'product_id' => $id,
                    'category_id' => $categoryId
                ]);
            }
            
            $this->logActivity('update', 'product', $id, 'Updated product: ' . $data['name']);
            
            $this->setFlash('success', 'Product updated successfully!');
            $this->redirect('/admin/products');
            
        } catch (Exception $e) {
            $this->setFlash('error', $e->getMessage());
            $this->back();
        }
    }
    
    /**
     * Delete product
     */
    public function delete($id) {
        $this->requireAuth();
        
        try {
            $product = $this->productModel->find($id);
            
            if (!$product) {
                throw new Exception('Product not found');
            }
            
            $this->productModel->delete($id);
            
            $this->logActivity('delete', 'product', $id, 'Deleted product: ' . $product['name']);
            
            $this->setFlash('success', 'Product deleted successfully!');
            $this->redirect('/admin/products');
            
        } catch (Exception $e) {
            $this->setFlash('error', $e->getMessage());
            $this->back();
        }
    }
    
    /**
     * Log activity
     */
    private function logActivity($action, $subjectType, $subjectId, $description) {
        $this->db->insert('activity_log', [
            'user_id' => $_SESSION['user_id'] ?? null,
            'action' => $action,
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'description' => $description,
            'ip_address' => $this->security->getClientIP(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    }
}
