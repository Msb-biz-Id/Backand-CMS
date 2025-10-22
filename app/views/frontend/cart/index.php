<!-- Cart Page -->
<section class="py-5">
    <div class="container">
        <h1 class="mb-4">Shopping Cart</h1>
        
        <?php if (!empty($cartItems)): ?>
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <table class="cart-table" style="width: 100%;">
                            <thead>
                                <tr style="border-bottom: 2px solid #e9ecef;">
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cartItems as $item): ?>
                                    <tr style="border-bottom: 1px solid #e9ecef;">
                                        <td>
                                            <div class="d-flex align-items-center gap-3" style="padding: 1rem 0;">
                                                <?php if ($item['featured_image']): ?>
                                                    <img src="<?php echo $item['featured_image']; ?>" alt="" style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px;">
                                                <?php endif; ?>
                                                <div>
                                                    <strong><?php echo htmlspecialchars($item['product_name']); ?></strong>
                                                    <br>
                                                    <small class="text-muted">SKU: <?php echo $item['sku'] ?? '-'; ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>Rp <?php echo number_format($item['price']); ?></td>
                                        <td>
                                            <input type="number" 
                                                   value="<?php echo $item['quantity']; ?>" 
                                                   min="1" 
                                                   max="<?php echo $item['stock_quantity']; ?>"
                                                   class="form-control" 
                                                   style="width: 80px;"
                                                   onchange="updateCartQuantity(<?php echo $item['id']; ?>, this.value)">
                                        </td>
                                        <td><strong>Rp <?php echo number_format($item['price'] * $item['quantity']); ?></strong></td>
                                        <td>
                                            <button onclick="removeFromCart(<?php echo $item['id']; ?>)" class="btn-icon-danger">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h4 class="mb-3">Cart Summary</h4>
                        
                        <div class="cart-summary">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <strong>Rp <?php echo number_format($total); ?></strong>
                            </div>
                            <div class="d-flex justify-content-between mb-3" style="padding-bottom: 1rem; border-bottom: 1px solid #e9ecef;">
                                <span>Shipping:</span>
                                <span class="text-muted">Calculated at checkout</span>
                            </div>
                            <div class="d-flex justify-content-between mb-4">
                                <strong style="font-size: 1.25rem;">Total:</strong>
                                <strong style="font-size: 1.25rem; color: var(--primary-color);">Rp <?php echo number_format($total); ?></strong>
                            </div>
                            
                            <a href="/checkout" class="btn btn-primary w-100 mb-2">
                                <i class="ri-shopping-bag-line me-1"></i> Proceed to Checkout
                            </a>
                            
                            <a href="/products" class="btn btn-outline w-100">
                                <i class="ri-arrow-left-line me-1"></i> Continue Shopping
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="text-center py-5">
            <i class="ri-shopping-cart-line" style="font-size: 5rem; color: #ccc;"></i>
            <h3 class="mt-3">Your cart is empty</h3>
            <p class="text-muted mb-4">Start adding products to your cart</p>
            <a href="/products" class="btn btn-primary">
                <i class="ri-shopping-bag-line me-1"></i> Browse Products
            </a>
        </div>
        <?php endif; ?>
    </div>
</section>

<style>
.cart-table th {
    padding: 1rem;
    font-weight: 600;
    text-align: left;
}

.cart-table td {
    padding: 1rem;
    vertical-align: middle;
}

.btn-icon-danger {
    background: transparent;
    border: none;
    color: #dc3545;
    font-size: 1.25rem;
    cursor: pointer;
    padding: 0.5rem;
}

.btn-icon-danger:hover {
    color: #a71d2a;
}

.gap-3 {
    gap: 1rem;
}

.justify-content-between {
    justify-content: space-between;
}

.text-muted {
    color: #6c757d;
}
</style>
