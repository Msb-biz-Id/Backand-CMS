<!-- Product Detail -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <!-- Product Image -->
                <div class="product-image-main">
                    <?php if ($product['featured_image']): ?>
                        <img src="<?php echo $product['featured_image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="img-fluid" style="border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                    <?php else: ?>
                        <div class="bg-light d-flex align-items-center justify-content-center" style="height: 400px; border-radius: 12px;">
                            <i class="ri-image-line" style="font-size: 5rem; color: #ccc;"></i>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="col-md-6">
                <!-- Product Info -->
                <?php if (!empty($product['brand_name'])): ?>
                    <p class="text-muted mb-2">
                        <i class="ri-award-line me-1"></i>
                        <strong><?php echo htmlspecialchars($product['brand_name']); ?></strong>
                    </p>
                <?php endif; ?>
                
                <h1 class="mb-3"><?php echo htmlspecialchars($product['name']); ?></h1>
                
                <?php if ($product['short_description']): ?>
                    <p class="lead text-muted mb-4"><?php echo htmlspecialchars($product['short_description']); ?></p>
                <?php endif; ?>
                
                <!-- Price -->
                <div class="mb-4">
                    <?php if ($product['is_on_sale']): ?>
                        <span class="product-price-old" style="font-size: 1.5rem;">Rp <?php echo number_format($product['price']); ?></span>
                        <br>
                        <span class="product-price" style="font-size: 2.5rem; color: #dc3545;">
                            Rp <?php echo number_format($product['sale_price']); ?>
                        </span>
                        <span class="badge bg-danger ms-2">
                            Save <?php echo round((($product['price'] - $product['sale_price']) / $product['price']) * 100); ?>%
                        </span>
                    <?php else: ?>
                        <span class="product-price" style="font-size: 2.5rem;">
                            Rp <?php echo number_format($product['price']); ?>
                        </span>
                    <?php endif; ?>
                </div>
                
                <!-- Stock Status -->
                <div class="mb-4">
                    <?php if ($product['stock_status'] === 'in_stock'): ?>
                        <span class="badge bg-success">
                            <i class="ri-checkbox-circle-line me-1"></i>
                            In Stock (<?php echo $product['stock_quantity']; ?> available)
                        </span>
                    <?php else: ?>
                        <span class="badge bg-danger">
                            <i class="ri-close-circle-line me-1"></i>
                            Out of Stock
                        </span>
                    <?php endif; ?>
                </div>
                
                <!-- Add to Cart Form -->
                <form id="add-to-cart-form" class="mb-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Quantity:</label>
                        <div class="d-flex align-items-center gap-2">
                            <input type="number" name="quantity" value="1" min="1" max="<?php echo $product['stock_quantity']; ?>" class="form-control" style="width: 100px;">
                            
                            <?php if ($product['stock_status'] === 'in_stock'): ?>
                                <button type="button" onclick="addToCart(<?php echo $product['id']; ?>, document.querySelector('[name=quantity]').value)" class="btn btn-primary">
                                    <i class="ri-shopping-cart-line me-1"></i> Add to Cart
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>
                
                <!-- WhatsApp Order -->
                <div class="mb-4">
                    <a href="<?php echo WhatsAppHelper::contactLink($GLOBALS['config']['ecommerce']['whatsapp_number'], $product['name']); ?>" 
                       target="_blank" 
                       class="btn btn-success">
                        <i class="ri-whatsapp-line me-1"></i> Order via WhatsApp
                    </a>
                </div>
                
                <!-- Product Meta -->
                <div class="product-meta">
                    <p><strong>SKU:</strong> <?php echo htmlspecialchars($product['sku']); ?></p>
                    <?php if ($product['weight']): ?>
                        <p><strong>Weight:</strong> <?php echo $product['weight']; ?> gram</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Product Description -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h3 class="mb-3">Product Description</h3>
                        <div class="product-description">
                            <?php echo $product['description']; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Related Products -->
        <?php if (!empty($relatedProducts)): ?>
        <div class="row mt-5">
            <div class="col-12">
                <h3 class="mb-4">Related Products</h3>
            </div>
            
            <?php foreach ($relatedProducts as $related): ?>
                <div class="col-md-3">
                    <div class="card product-card">
                        <a href="/product/<?php echo $related['slug']; ?>">
                            <?php if ($related['featured_image']): ?>
                                <img src="<?php echo $related['featured_image']; ?>" alt="<?php echo htmlspecialchars($related['name']); ?>" class="card-img-top">
                            <?php else: ?>
                                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 150px;">
                                    <i class="ri-image-line fs-4 text-muted"></i>
                                </div>
                            <?php endif; ?>
                        </a>
                        
                        <div class="card-body">
                            <h5 class="card-title">
                                <a href="/product/<?php echo $related['slug']; ?>" style="color: inherit;">
                                    <?php echo htmlspecialchars($related['name']); ?>
                                </a>
                            </h5>
                            <div class="product-price">
                                Rp <?php echo number_format($related['sale_price'] ?? $related['price']); ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<style>
.badge {
    display: inline-block;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 600;
}

.bg-success {
    background-color: #28a745;
    color: #fff;
}

.bg-danger {
    background-color: #dc3545;
    color: #fff;
}

.bg-warning {
    background-color: #ffc107;
    color: #333;
}

.form-control {
    padding: 0.5rem 1rem;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 1rem;
}

.form-label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.fw-bold {
    font-weight: 700;
}

.lead {
    font-size: 1.25rem;
}

.btn-success {
    background-color: #25D366;
    color: #fff;
}

.btn-success:hover {
    background-color: #1ea952;
    color: #fff;
}

.img-fluid {
    max-width: 100%;
    height: auto;
}
</style>
