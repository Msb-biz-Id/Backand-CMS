<!-- Hero Section -->
<div class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold mb-4">Welcome to Advanced CMS</h1>
                <p class="lead mb-4">
                    Modern content management system dengan fitur SEO canggih, keamanan tingkat tinggi, 
                    dan performa optimal. Dibangun dengan PHP Native OOP/MVC.
                </p>
                <div class="d-flex gap-3">
                    <a href="/admin" class="btn btn-light btn-lg">Get Started</a>
                    <a href="#features" class="btn btn-outline-light btn-lg">Learn More</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Features Section -->
<section id="features" class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Powerful Features</h2>
            <p class="text-muted">Everything you need to manage your content effectively</p>
        </div>
        
        <div class="row">
            <div class="col-md-4">
                <div class="feature-box text-center">
                    <div class="mb-3">
                        <i class="ri-search-line display-4 text-primary"></i>
                    </div>
                    <h4>Advanced SEO</h4>
                    <p class="text-muted">
                        Built-in SEO tools mirip RankMath dengan schema markup, 
                        meta tags, dan SEO score calculator.
                    </p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="feature-box text-center">
                    <div class="mb-3">
                        <i class="ri-shield-check-line display-4 text-success"></i>
                    </div>
                    <h4>High Security</h4>
                    <p class="text-muted">
                        Multi-layer security dengan CSRF protection, XSS prevention, 
                        SQL injection protection, dan encryption.
                    </p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="feature-box text-center">
                    <div class="mb-3">
                        <i class="ri-speed-line display-4 text-warning"></i>
                    </div>
                    <h4>Fast Performance</h4>
                    <p class="text-muted">
                        Optimized dengan caching, lazy loading, image optimization, 
                        dan GZIP compression.
                    </p>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="feature-box text-center">
                    <div class="mb-3">
                        <i class="ri-file-edit-line display-4 text-info"></i>
                    </div>
                    <h4>Content Management</h4>
                    <p class="text-muted">
                        Manage posts, pages, products, and jobs dengan 
                        WYSIWYG editor dan media library.
                    </p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="feature-box text-center">
                    <div class="mb-3">
                        <i class="ri-line-chart-line display-4 text-danger"></i>
                    </div>
                    <h4>Analytics</h4>
                    <p class="text-muted">
                        Built-in analytics dashboard untuk tracking views, 
                        visitors, dan content performance.
                    </p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="feature-box text-center">
                    <div class="mb-3">
                        <i class="ri-global-line display-4 text-primary"></i>
                    </div>
                    <h4>Multi-language</h4>
                    <p class="text-muted">
                        Support untuk multiple languages dengan 
                        translation management system.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if (!empty($recentPosts)): ?>
<!-- Recent Posts -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Latest Posts</h2>
            <p class="text-muted">Check out our recent blog posts</p>
        </div>
        
        <div class="row">
            <?php foreach (array_slice($recentPosts, 0, 3) as $post): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <?php if ($post['featured_image']): ?>
                            <img src="<?php echo htmlspecialchars($post['featured_image']); ?>" 
                                 class="card-img-top" 
                                 alt="<?php echo htmlspecialchars($post['title']); ?>">
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title">
                                <a href="/post/<?php echo htmlspecialchars($post['slug']); ?>" class="text-decoration-none">
                                    <?php echo htmlspecialchars($post['title']); ?>
                                </a>
                            </h5>
                            <p class="card-text text-muted">
                                <?php echo htmlspecialchars(substr($post['excerpt'] ?? $post['content'], 0, 150)); ?>...
                            </p>
                            <a href="/post/<?php echo htmlspecialchars($post['slug']); ?>" class="btn btn-sm btn-primary">
                                Read More
                            </a>
                        </div>
                        <div class="card-footer bg-transparent">
                            <small class="text-muted">
                                <i class="ri-time-line"></i> <?php echo date('M d, Y', strtotime($post['published_at'])); ?>
                            </small>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-4">
            <a href="/posts" class="btn btn-primary">View All Posts</a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CTA Section -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h2 class="fw-bold mb-4">Ready to Get Started?</h2>
                <p class="lead text-muted mb-4">
                    Login ke admin panel dan mulai mengelola konten Anda dengan mudah.
                </p>
                <a href="/admin" class="btn btn-primary btn-lg">
                    <i class="ri-dashboard-line me-2"></i> Go to Admin Panel
                </a>
            </div>
        </div>
    </div>
</section>
