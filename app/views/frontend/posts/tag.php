<!-- Tag Archive (Dynamic) -->
<div class="hero-section" style="padding: 3rem 0; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
    <div class="container text-center">
        <h1 class="text-white mb-2">
            <i class="ri-price-tag-3-line me-2"></i>
            #<?php echo htmlspecialchars($tag['name']); ?>
        </h1>
        <p class="text-white mb-0">
            <?php echo count($posts); ?> post<?php echo count($posts) !== 1 ? 's' : ''; ?> tagged with this topic
        </p>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <?php if (!empty($posts)): ?>
            <div class="row">
                <?php foreach ($posts as $post): ?>
                    <div class="col-md-4 mb-4">
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
                                    echo htmlspecialchars(substr($excerpt, 0, 100)) . '...';
                                    ?>
                                </p>
                                
                                <small class="text-muted">
                                    <?php echo date('M d, Y', strtotime($post['published_at'] ?? $post['created_at'])); ?>
                                </small>
                            </div>
                        </article>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="ri-price-tag-3-line" style="font-size: 5rem; color: #ccc;"></i>
                <h3 class="mt-3">No posts with this tag yet</h3>
                <p class="text-muted">Check back soon for new content</p>
                <a href="/posts" class="btn btn-primary mt-2">Browse All Posts</a>
            </div>
        <?php endif; ?>
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
