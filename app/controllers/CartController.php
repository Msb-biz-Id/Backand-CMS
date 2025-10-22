<?php

/**
 * Frontend Cart Controller
 * Shopping cart management
 */
class CartController extends Controller {
    private $cartModel;
    private $productModel;
    
    public function __construct() {
        parent::__construct();
        $this->cartModel = new Cart();
        $this->productModel = new Product();
    }
    
    /**
     * View cart
     */
    public function index() {
        $userId = $_SESSION['user_id'] ?? null;
        $sessionId = session_id();
        
        $cartItems = $this->cartModel->getCartItems($userId, $sessionId);
        $total = $this->cartModel->getCartTotal($userId, $sessionId);
        
        $this->seo->setTitle('Shopping Cart');
        $this->seo->setRobots('noindex, nofollow');
        
        $this->view('frontend/cart/index', [
            'cartItems' => $cartItems,
            'total' => $total
        ], 'frontend');
    }
    
    /**
     * Add to cart (AJAX)
     */
    public function add() {
        try {
            $productId = $this->input('product_id');
            $quantity = $this->input('quantity', 1);
            $variantId = $this->input('variant_id');
            
            // Get product
            $product = $this->productModel->find($productId);
            
            if (!$product) {
                throw new Exception('Product not found');
            }
            
            // Check stock
            if ($product['stock_quantity'] < $quantity) {
                throw new Exception('Insufficient stock');
            }
            
            $userId = $_SESSION['user_id'] ?? null;
            $sessionId = session_id();
            
            $data = [
                'user_id' => $userId,
                'session_id' => $sessionId,
                'product_id' => $productId,
                'variant_id' => $variantId,
                'quantity' => $quantity,
                'price' => $product['sale_price'] ?? $product['price']
            ];
            
            $this->cartModel->addToCart($data);
            
            $this->json([
                'success' => true,
                'message' => 'Product added to cart',
                'count' => $this->cartModel->getCartCount($userId, $sessionId)
            ]);
            
        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
    
    /**
     * Update cart quantity
     */
    public function update($cartId) {
        try {
            $quantity = $this->input('quantity', 1);
            
            $this->cartModel->updateQuantity($cartId, $quantity);
            
            $this->json([
                'success' => true,
                'message' => 'Cart updated'
            ]);
            
        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
    
    /**
     * Remove from cart
     */
    public function remove($cartId) {
        try {
            $this->cartModel->removeItem($cartId);
            
            $this->json([
                'success' => true,
                'message' => 'Item removed from cart'
            ]);
            
        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
    
    /**
     * Get cart count (AJAX)
     */
    public function count() {
        $userId = $_SESSION['user_id'] ?? null;
        $sessionId = session_id();
        
        $count = $this->cartModel->getCartCount($userId, $sessionId);
        
        $this->json([
            'success' => true,
            'count' => $count
        ]);
    }
}
