<!-- Single Page (Dynamic) -->
<article class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Page Header -->
                <header class="mb-5 text-center">
                    <h1 class="display-4 fw-bold mb-3"><?php echo htmlspecialchars($page['title']); ?></h1>
                    
                    <?php if (!empty($page['excerpt'])): ?>
                        <p class="lead text-muted"><?php echo htmlspecialchars($page['excerpt']); ?></p>
                    <?php endif; ?>
                </header>

                <!-- Featured Image -->
                <?php if ($page['featured_image']): ?>
                    <div class="mb-5">
                        <img src="<?php echo $page['featured_image']; ?>" 
                             alt="<?php echo htmlspecialchars($page['title']); ?>" 
                             class="img-fluid rounded shadow"
                             style="width: 100%;">
                    </div>
                <?php endif; ?>

                <!-- Page Content -->
                <div class="page-content mb-5">
                    <?php echo $page['content']; ?>
                </div>

                <!-- Child Pages -->
                <?php if (!empty($childPages)): ?>
                    <div class="child-pages mt-5 pt-5 border-top">
                        <h3 class="mb-4">
                            <i class="ri-pages-line me-2"></i> Related Pages
                        </h3>
                        <div class="row">
                            <?php foreach ($childPages as $child): ?>
                                <div class="col-md-4 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h5 class="card-title">
                                                <a href="/page/<?php echo $child['slug']; ?>" class="text-decoration-none">
                                                    <?php echo htmlspecialchars($child['title']); ?>
                                                </a>
                                            </h5>
                                            <?php if ($child['excerpt']): ?>
                                                <p class="card-text text-muted small">
                                                    <?php echo htmlspecialchars(substr($child['excerpt'], 0, 100)) . '...'; ?>
                                                </p>
                                            <?php endif; ?>
                                            <a href="/page/<?php echo $child['slug']; ?>" class="btn btn-sm btn-outline-primary">
                                                Read More <i class="ri-arrow-right-line ms-1"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</article>

<style>
.page-content {
    font-size: 1.125rem;
    line-height: 1.8;
}

.page-content img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    margin: 1.5rem 0;
}

.page-content h2,
.page-content h3,
.page-content h4 {
    margin-top: 2rem;
    margin-bottom: 1rem;
    font-weight: 600;
}

.page-content p {
    margin-bottom: 1.5rem;
}

.page-content ul,
.page-content ol {
    margin-bottom: 1.5rem;
    padding-left: 2rem;
}

.page-content blockquote {
    border-left: 4px solid var(--primary-color);
    padding-left: 1.5rem;
    margin: 2rem 0;
    font-style: italic;
    color: #666;
}

.page-content table {
    width: 100%;
    margin: 1.5rem 0;
    border-collapse: collapse;
}

.page-content table th,
.page-content table td {
    padding: 0.75rem;
    border: 1px solid #dee2e6;
}

.page-content table th {
    background-color: #f8f9fa;
    font-weight: 600;
}
</style>
