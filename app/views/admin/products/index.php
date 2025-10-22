<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Products</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item active">Products</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row">
    <div class="col-md-3">
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="text-uppercase fw-medium text-muted mb-0">Total Products</p>
                    </div>
                </div>
                <div class="d-flex align-items-end justify-content-between mt-2">
                    <div>
                        <h4 class="fs-22 fw-semibold ff-secondary mb-2"><?php echo $stats['total']; ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="text-uppercase fw-medium text-muted mb-0">Published</p>
                    </div>
                </div>
                <div class="d-flex align-items-end justify-content-between mt-2">
                    <div>
                        <h4 class="fs-22 fw-semibold ff-secondary mb-2 text-success"><?php echo $stats['published']; ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="text-uppercase fw-medium text-muted mb-0">Draft</p>
                    </div>
                </div>
                <div class="d-flex align-items-end justify-content-between mt-2">
                    <div>
                        <h4 class="fs-22 fw-semibold ff-secondary mb-2 text-warning"><?php echo $stats['draft']; ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="text-uppercase fw-medium text-muted mb-0">Out of Stock</p>
                    </div>
                </div>
                <div class="d-flex align-items-end justify-content-between mt-2">
                    <div>
                        <h4 class="fs-22 fw-semibold ff-secondary mb-2 text-danger"><?php echo $stats['out_of_stock']; ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <a href="/admin/products/create" class="btn btn-primary">
                            <i class="ri-add-line me-1"></i> Add New Product
                        </a>
                    </div>
                    <div class="col-sm-6">
                        <form method="GET" class="d-flex gap-2">
                            <select name="brand" class="form-select" style="width: 150px;">
                                <option value="">All Brands</option>
                                <?php foreach ($brands as $brand): ?>
                                    <option value="<?php echo $brand['id']; ?>" <?php echo $brandFilter == $brand['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($brand['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <select name="status" class="form-select" style="width: 150px;">
                                <option value="">All Status</option>
                                <option value="published" <?php echo $statusFilter === 'published' ? 'selected' : ''; ?>>Published</option>
                                <option value="draft" <?php echo $statusFilter === 'draft' ? 'selected' : ''; ?>>Draft</option>
                            </select>
                            <input type="search" name="search" class="form-control" placeholder="Search products..." value="<?php echo htmlspecialchars($search ?? ''); ?>">
                            <button type="submit" class="btn btn-secondary">Filter</button>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Product</th>
                                <th>SKU</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($products)): ?>
                                <?php foreach ($products as $product): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <?php if ($product['featured_image']): ?>
                                                    <img src="<?php echo $product['featured_image']; ?>" alt="" class="avatar-sm rounded me-2">
                                                <?php else: ?>
                                                    <div class="avatar-sm bg-light rounded me-2 d-flex align-items-center justify-content-center">
                                                        <i class="ri-image-line fs-4 text-muted"></i>
                                                    </div>
                                                <?php endif; ?>
                                                <div>
                                                    <a href="/admin/products/<?php echo $product['id']; ?>/edit" class="text-dark fw-medium">
                                                        <?php echo htmlspecialchars($product['name']); ?>
                                                    </a>
                                                    <?php if (!empty($product['brand_name'])): ?>
                                                        <br><small class="text-muted"><?php echo htmlspecialchars($product['brand_name']); ?></small>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td><code><?php echo htmlspecialchars($product['sku']); ?></code></td>
                                        <td>
                                            <?php if ($product['is_on_sale']): ?>
                                                <span class="text-muted text-decoration-line-through">Rp <?php echo number_format($product['price']); ?></span>
                                                <br>
                                                <strong class="text-danger">Rp <?php echo number_format($product['sale_price']); ?></strong>
                                            <?php else: ?>
                                                <strong>Rp <?php echo number_format($product['price']); ?></strong>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo $product['stock_quantity'] > 0 ? 'success' : 'danger'; ?>">
                                                <?php echo $product['stock_quantity']; ?> pcs
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo $product['status'] === 'published' ? 'success' : 'secondary'; ?>">
                                                <?php echo ucfirst($product['status']); ?>
                                            </span>
                                            <?php if ($product['is_featured']): ?>
                                                <span class="badge bg-warning">Featured</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="/admin/products/<?php echo $product['id']; ?>/edit" class="btn btn-sm btn-primary">
                                                    <i class="ri-edit-line"></i>
                                                </a>
                                                <a href="/product/<?php echo $product['slug']; ?>" target="_blank" class="btn btn-sm btn-info">
                                                    <i class="ri-eye-line"></i>
                                                </a>
                                                <form method="POST" action="/admin/products/<?php echo $product['id']; ?>/delete" class="d-inline" onsubmit="return confirm('Delete this product?');">
                                                    <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="ri-delete-bin-line"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <i class="ri-shopping-bag-line display-4 text-muted"></i>
                                        <p class="text-muted mt-2">No products found. <a href="/admin/products/create">Add your first product</a></p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if (isset($pagination) && $pagination['last_page'] > 1): ?>
                <div class="row mt-4">
                    <div class="col-12">
                        <nav>
                            <ul class="pagination justify-content-end">
                                <li class="page-item <?php echo $pagination['current_page'] <= 1 ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $pagination['current_page'] - 1; ?>">Previous</a>
                                </li>
                                <?php for ($i = 1; $i <= $pagination['last_page']; $i++): ?>
                                    <li class="page-item <?php echo $i == $pagination['current_page'] ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>
                                <li class="page-item <?php echo $pagination['current_page'] >= $pagination['last_page'] ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $pagination['current_page'] + 1; ?>">Next</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
