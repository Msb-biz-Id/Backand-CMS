<!-- Category Archive (Dynamic) -->
<div class="hero-section" style="padding: 3rem 0; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container">
        <h1 class="text-white mb-2">
            <i class="ri-folder-line me-2"></i>
            <?php echo htmlspecialchars($category['name']); ?>
        </h1>
        <?php if ($category['description']): ?>
            <p class="text-white mb-0"><?php echo htmlspecialchars($category['description']); ?></p>
        <?php endif; ?>
        <p class="text-white mt-2 mb-0">
            <small><?php echo count($posts); ?> post<?php echo count($posts) !== 1 ? 's' : ''; ?> in this category</small>
        </p>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Posts -->
            <div class="col-lg-8">
                <?php if (!empty($posts)): ?>
                    <div class="row">
                        <?php foreach ($posts as $post): ?>
                            <div class="col-md-6 mb-4">
                                <article class="card h-100 shadow-sm hover-lift">
                                    <?php if ($post['featured_image']): ?>
                                        <a href="/post/<?php echo $post['slug']; ?>">
                                            <img src="<?php echo $post['featured_image']; ?>" 
                                                 class="card-img-top" 
                                                 alt="<?php echo htmlspecialchars($post['title']); ?>"
                                                 style="height: 200px; object-fit: cover;">
                                        </a>
                                    <?php endif; ?>
                                    
                                    <div class="card-body">
                                        <h2 class="h5 card-title">
                                            <a href="/post/<?php echo $post['slug']; ?>" class="text-dark text-decoration-none">
                                                <?php echo htmlspecialchars($post['title']); ?>
                                            </a>
                                        </h2>
                                        
                                        <p class="card-text text-muted small">
                                            <?php 
                                            $excerpt = $post['excerpt'] ?? strip_tags($post['content']);
                                            echo htmlspecialchars(substr($excerpt, 0, 120)) . '...';
                                            ?>
                                        </p>
                                        
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                <?php echo date('M d, Y', strtotime($post['published_at'] ?? $post['created_at'])); ?>
                                            </small>
                                            <a href="/post/<?php echo $post['slug']; ?>" class="btn btn-sm btn-outline-primary">
                                                Read More
                                            </a>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="ri-folder-open-line" style="font-size: 5rem; color: #ccc;"></i>
                        <h3 class="mt-3">No posts in this category yet</h3>
                        <p class="text-muted">Check back soon for new content</p>
                        <a href="/posts" class="btn btn-primary mt-2">Browse All Posts</a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- All Categories -->
                <?php if (!empty($categories)): ?>
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3">
                            <i class="ri-folder-line me-2"></i> All Categories
                        </h5>
                        <ul class="list-unstyled mb-0">
                            <?php foreach ($categories as $cat): ?>
                                <li class="mb-2">
                                    <a href="/category/<?php echo $cat['slug']; ?>" 
                                       class="text-decoration-none d-flex justify-content-between <?php echo $cat['id'] == $category['id'] ? 'fw-bold text-primary' : ''; ?>">
                                        <span><?php echo htmlspecialchars($cat['name']); ?></span>
                                        <span class="badge bg-light text-dark"><?php echo $cat['post_count'] ?? 0; ?></span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-body text-center">
                        <i class="ri-arrow-left-line fs-3 text-primary mb-3"></i>
                        <h5>Browse More</h5>
                        <p class="text-muted small">Explore other categories and topics</p>
                        <a href="/posts" class="btn btn-primary btn-sm">All Posts</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.hover-lift {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.hover-lift:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.15);
}
</style>
