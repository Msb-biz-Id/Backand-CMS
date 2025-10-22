<!-- Single Post Page (Dynamic) -->
<article class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Post Header -->
                <header class="mb-4">
                    <!-- Categories -->
                    <?php if (!empty($categories)): ?>
                        <div class="mb-3">
                            <?php foreach ($categories as $cat): ?>
                                <a href="/category/<?php echo $cat['slug']; ?>" class="badge bg-primary text-decoration-none me-1">
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Title -->
                    <h1 class="display-5 fw-bold mb-3"><?php echo htmlspecialchars($post['title']); ?></h1>

                    <!-- Meta -->
                    <div class="d-flex align-items-center gap-3 text-muted mb-4">
                        <span>
                            <i class="ri-user-line me-1"></i>
                            <?php echo htmlspecialchars($post['author_name'] ?? 'Admin'); ?>
                        </span>
                        <span>
                            <i class="ri-calendar-line me-1"></i>
                            <?php echo date('F d, Y', strtotime($post['published_at'] ?? $post['created_at'])); ?>
                        </span>
                        <?php if (!empty($post['views'])): ?>
                            <span>
                                <i class="ri-eye-line me-1"></i>
                                <?php echo number_format($post['views']); ?> views
                            </span>
                        <?php endif; ?>
                        <span>
                            <i class="ri-time-line me-1"></i>
                            <?php echo ceil(str_word_count(strip_tags($post['content'])) / 200); ?> min read
                        </span>
                    </div>

                    <!-- Featured Image -->
                    <?php if ($post['featured_image']): ?>
                        <div class="mb-4">
                            <img src="<?php echo $post['featured_image']; ?>" 
                                 alt="<?php echo htmlspecialchars($post['title']); ?>" 
                                 class="img-fluid rounded shadow"
                                 style="width: 100%; max-height: 500px; object-fit: cover;">
                        </div>
                    <?php endif; ?>
                </header>

                <!-- Post Content -->
                <div class="post-content mb-5">
                    <?php echo $post['content']; ?>
                </div>

                <!-- Tags -->
                <?php if (!empty($tags)): ?>
                    <div class="post-tags mb-4 pb-4 border-bottom">
                        <strong class="me-2"><i class="ri-price-tag-3-line me-1"></i> Tags:</strong>
                        <?php foreach ($tags as $tag): ?>
                            <a href="/tag/<?php echo $tag['slug']; ?>" class="badge bg-light text-dark text-decoration-none me-1">
                                #<?php echo htmlspecialchars($tag['name']); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Share Buttons -->
                <div class="share-buttons mb-5 pb-4 border-bottom">
                    <strong class="me-3"><i class="ri-share-line me-1"></i> Share:</strong>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>" 
                       target="_blank" 
                       class="btn btn-sm btn-outline-primary me-2">
                        <i class="ri-facebook-fill"></i> Facebook
                    </a>
                    <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>&text=<?php echo urlencode($post['title']); ?>" 
                       target="_blank" 
                       class="btn btn-sm btn-outline-info me-2">
                        <i class="ri-twitter-fill"></i> Twitter
                    </a>
                    <a href="https://wa.me/?text=<?php echo urlencode($post['title'] . ' ' . $_SERVER['REQUEST_URI']); ?>" 
                       target="_blank" 
                       class="btn btn-sm btn-outline-success me-2">
                        <i class="ri-whatsapp-fill"></i> WhatsApp
                    </a>
                    <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>&title=<?php echo urlencode($post['title']); ?>" 
                       target="_blank" 
                       class="btn btn-sm btn-outline-primary">
                        <i class="ri-linkedin-fill"></i> LinkedIn
                    </a>
                </div>

                <!-- Author Box -->
                <?php if (!empty($post['author_bio'])): ?>
                <div class="author-box card mb-5">
                    <div class="card-body">
                        <div class="d-flex gap-3">
                            <div>
                                <?php if (!empty($post['author_avatar'])): ?>
                                    <img src="<?php echo $post['author_avatar']; ?>" alt="" class="rounded-circle" style="width: 80px; height: 80px;">
                                <?php else: ?>
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 2rem;">
                                        <?php echo strtoupper(substr($post['author_name'] ?? 'A', 0, 1)); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="flex-fill">
                                <h5 class="mb-1"><?php echo htmlspecialchars($post['author_name'] ?? 'Admin'); ?></h5>
                                <p class="text-muted mb-0"><?php echo htmlspecialchars($post['author_bio']); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Comments Section -->
                <?php include __DIR__ . '/../partials/comments.php'; ?>
            </div>
        </div>

        <!-- Related Posts -->
        <?php if (!empty($relatedPosts)): ?>
        <div class="row mt-5 pt-5 border-top">
            <div class="col-12">
                <h3 class="mb-4">
                    <i class="ri-article-line me-2"></i> Related Posts
                </h3>
            </div>
            
            <?php foreach ($relatedPosts as $related): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm hover-lift">
                        <?php if ($related['featured_image']): ?>
                            <a href="/post/<?php echo $related['slug']; ?>">
                                <img src="<?php echo $related['featured_image']; ?>" 
                                     class="card-img-top" 
                                     alt="<?php echo htmlspecialchars($related['title']); ?>"
                                     style="height: 200px; object-fit: cover;">
                            </a>
                        <?php endif; ?>
                        
                        <div class="card-body">
                            <h5 class="card-title">
                                <a href="/post/<?php echo $related['slug']; ?>" class="text-dark text-decoration-none">
                                    <?php echo htmlspecialchars($related['title']); ?>
                                </a>
                            </h5>
                            <p class="card-text text-muted small">
                                <?php echo htmlspecialchars(substr($related['excerpt'] ?? strip_tags($related['content']), 0, 100)) . '...'; ?>
                            </p>
                            <small class="text-muted">
                                <i class="ri-calendar-line me-1"></i>
                                <?php echo date('M d, Y', strtotime($related['published_at'] ?? $related['created_at'])); ?>
                            </small>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</article>

<style>
.post-content {
    font-size: 1.125rem;
    line-height: 1.8;
}

.post-content img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    margin: 1.5rem 0;
}

.post-content h2,
.post-content h3,
.post-content h4 {
    margin-top: 2rem;
    margin-bottom: 1rem;
}

.post-content p {
    margin-bottom: 1.5rem;
}

.post-content ul,
.post-content ol {
    margin-bottom: 1.5rem;
    padding-left: 2rem;
}

.post-content blockquote {
    border-left: 4px solid var(--primary-color);
    padding-left: 1.5rem;
    margin: 2rem 0;
    font-style: italic;
    color: #666;
}

.hover-lift {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.hover-lift:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.15);
}
</style>
