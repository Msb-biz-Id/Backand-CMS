<!-- Hero Section -->
<div class="hero-section">
    <div class="container">
        <h1>Our Products</h1>
        <p>Browse our complete catalog of quality products</p>
    </div>
</div>

<!-- Products Section -->
<section class="py-5">
    <div class="container">
        <!-- Filters -->
        <div class="row mb-4">
            <div class="col-md-12">
                <form method="GET" class="d-flex gap-2 align-items-center flex-wrap">
                    <select name="brand" class="form-select" style="width: 200px;" onchange="this.form.submit()">
                        <option value="">All Brands</option>
                        <?php foreach ($brands as $brand): ?>
                            <option value="<?php echo $brand['id']; ?>" <?php echo $selectedBrand == $brand['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($brand['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    
                    <select name="category" class="form-select" style="width: 200px;" onchange="this.form.submit()">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>" <?php echo $selectedCategory == $category['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    
                    <select name="sort" class="form-select" style="width: 200px;" onchange="this.form.submit()">
                        <option value="newest" <?php echo $sort === 'newest' ? 'selected' : ''; ?>>Newest First</option>
                        <option value="price_low" <?php echo $sort === 'price_low' ? 'selected' : ''; ?>>Price: Low to High</option>
                        <option value="price_high" <?php echo $sort === 'price_high' ? 'selected' : ''; ?>>Price: High to Low</option>
                        <option value="name_az" <?php echo $sort === 'name_az' ? 'selected' : ''; ?>>Name: A-Z</option>
                    </select>
                </form>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="row">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <div class="col-md-4">
                        <div class="card product-card">
                            <?php if ($product['is_on_sale']): ?>
                                <span class="product-badge">SALE</span>
                            <?php endif; ?>
                            
                            <a href="/product/<?php echo $product['slug']; ?>">
                                <?php if ($product['featured_image']): ?>
                                    <img src="<?php echo $product['featured_image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="card-img-top">
                                <?php else: ?>
                                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                        <i class="ri-image-line fs-1 text-muted"></i>
                                    </div>
                                <?php endif; ?>
                            </a>
                            
                            <div class="card-body">
                                <?php if (!empty($product['brand_name'])): ?>
                                    <small class="text-muted"><?php echo htmlspecialchars($product['brand_name']); ?></small>
                                <?php endif; ?>
                                
                                <h3 class="card-title">
                                    <a href="/product/<?php echo $product['slug']; ?>" style="color: inherit; text-decoration: none;">
                                        <?php echo htmlspecialchars($product['name']); ?>
                                    </a>
                                </h3>
                                
                                <div class="product-price">
                                    <?php if ($product['is_on_sale']): ?>
                                        <span class="product-price-old">Rp <?php echo number_format($product['price']); ?></span>
                                        <span class="text-danger">Rp <?php echo number_format($product['sale_price']); ?></span>
                                    <?php else: ?>
                                        Rp <?php echo number_format($product['price']); ?>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if ($product['stock_status'] === 'in_stock'): ?>
                                    <button onclick="addToCart(<?php echo $product['id']; ?>)" class="btn btn-primary w-100">
                                        <i class="ri-shopping-cart-line me-1"></i> Add to Cart
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-secondary w-100" disabled>
                                        Out of Stock
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <i class="ri-shopping-bag-line" style="font-size: 4rem; color: #ccc;"></i>
                    <p class="text-muted mt-3">No products found</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if (isset($pagination) && $pagination['last_page'] > 1): ?>
        <div class="row mt-4">
            <div class="col-12">
                <nav>
                    <ul class="pagination justify-content-center" style="list-style: none; display: flex; gap: 0.5rem;">
                        <li class="<?php echo $pagination['current_page'] <= 1 ? 'disabled' : ''; ?>">
                            <a href="?page=<?php echo $pagination['current_page'] - 1; ?>" class="btn btn-outline">Previous</a>
                        </li>
                        <?php for ($i = 1; $i <= min($pagination['last_page'], 5); $i++): ?>
                            <li>
                                <a href="?page=<?php echo $i; ?>" class="btn <?php echo $i == $pagination['current_page'] ? 'btn-primary' : 'btn-outline'; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                        <li class="<?php echo $pagination['current_page'] >= $pagination['last_page'] ? 'disabled' : ''; ?>">
                            <a href="?page=<?php echo $pagination['current_page'] + 1; ?>" class="btn btn-outline">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<style>
.form-select {
    padding: 0.5rem 1rem;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 1rem;
}

.d-flex {
    display: flex;
}

.gap-2 {
    gap: 0.5rem;
}

.flex-wrap {
    flex-wrap: wrap;
}

.align-items-center {
    align-items: center;
}

.w-100 {
    width: 100%;
}

.text-danger {
    color: #dc3545;
}

.fs-1 {
    font-size: 3rem;
}

.bg-light {
    background-color: #f8f9fa;
}
</style>
