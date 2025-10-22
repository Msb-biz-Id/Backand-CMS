<?php

/**
 * Frontend Product Controller
 * E-Commerce product display
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
     * Product listing page
     */
    public function index() {
        $page = $this->input('page', 1);
        $brand = $this->input('brand');
        $category = $this->input('category');
        $sort = $this->input('sort', 'newest');
        
        $filters = [];
        if ($brand) $filters['brand_id'] = $brand;
        if ($category) {
            // Get products by category
            $products = $this->productModel->getByCategory($category, null);
            $pagination = null;
        } else {
            $result = $this->productModel->paginate($page, 12, $filters, $this->getSortOrder($sort));
            $products = $result['data'];
            $pagination = $result['pagination'];
        }
        
        // Get brands for filter
        $brands = $this->brandModel->getAll();
        $categories = $this->categoryModel->getByType('product');
        
        $this->seo->setTitle('Products');
        $this->seo->setDescription('Browse our complete product catalog');
        
        $this->view('frontend/products/index', [
            'products' => $products,
            'pagination' => $pagination,
            'brands' => $brands,
            'categories' => $categories,
            'selectedBrand' => $brand,
            'selectedCategory' => $category,
            'sort' => $sort
        ], 'frontend');
    }
    
    /**
     * Single product page
     */
    public function show($slug) {
        $product = $this->productModel->getBySlug($slug);
        
        if (!$product) {
            header("HTTP/1.0 404 Not Found");
            $this->view('errors/404', [], 'frontend');
            return;
        }
        
        // Get related products
        $relatedProducts = $this->productModel->getPublished(4);
        
        // SEO
        $this->seo->setTitle($product['name']);
        $this->seo->setDescription($product['short_description'] ?? substr(strip_tags($product['description']), 0, 160));
        
        // Product Schema
        $this->seo->addSchema('Product', [
            'name' => $product['name'],
            'description' => $product['short_description'] ?? '',
            'image' => $product['featured_image'] ?? '',
            'sku' => $product['sku'],
            'offers' => [
                '@type' => 'Offer',
                'price' => $product['sale_price'] ?? $product['price'],
                'priceCurrency' => 'IDR',
                'availability' => $product['stock_status'] === 'in_stock' ? 'InStock' : 'OutOfStock'
            ]
        ]);
        
        $this->view('frontend/products/show', [
            'product' => $product,
            'relatedProducts' => $relatedProducts
        ], 'frontend');
    }
    
    /**
     * Products by category
     */
    public function category($slug) {
        $categoryModel = new Category();
        $category = $categoryModel->getBySlug($slug, 'product');
        
        if (!$category) {
            header("HTTP/1.0 404 Not Found");
            $this->view('errors/404', [], 'frontend');
            return;
        }
        
        $products = $this->productModel->getByCategory($category['id']);
        
        $this->seo->setTitle('Products: ' . $category['name']);
        $this->seo->setDescription($category['description'] ?? '');
        
        $this->view('frontend/products/category', [
            'category' => $category,
            'products' => $products
        ], 'frontend');
    }
    
    /**
     * Get sort order SQL
     */
    private function getSortOrder($sort) {
        switch ($sort) {
            case 'price_low':
                return 'price ASC';
            case 'price_high':
                return 'price DESC';
            case 'name_az':
                return 'name ASC';
            case 'name_za':
                return 'name DESC';
            case 'newest':
            default:
                return 'created_at DESC';
        }
    }
}
