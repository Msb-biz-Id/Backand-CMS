<!-- Blog Listing Page (Dynamic) -->
<div class="hero-section" style="padding: 3rem 0; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container">
        <h1 class="text-white mb-3">Our Blog</h1>
        <p class="text-white">Latest articles, news, and insights</p>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Search Results Info -->
                <?php if (!empty($search)): ?>
                    <div class="alert alert-info mb-4">
                        <i class="ri-search-line me-2"></i>
                        Search results for: <strong>"<?php echo htmlspecialchars($search); ?>"</strong>
                        <a href="/posts" class="float-end">Clear Search</a>
                    </div>
                <?php endif; ?>

                <!-- Posts Grid -->
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
                                    <?php else: ?>
                                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                            <i class="ri-image-line" style="font-size: 3rem; color: #ccc;"></i>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="card-body">
                                        <!-- Category Badge -->
                                        <?php if (!empty($post['category_name'])): ?>
                                            <span class="badge bg-primary mb-2"><?php echo htmlspecialchars($post['category_name']); ?></span>
                                        <?php endif; ?>
                                        
                                        <h2 class="h5 card-title">
                                            <a href="/post/<?php echo $post['slug']; ?>" class="text-dark text-decoration-none">
                                                <?php echo htmlspecialchars($post['title']); ?>
                                            </a>
                                        </h2>
                                        
                                        <p class="card-text text-muted small mb-3">
                                            <?php 
                                            $excerpt = $post['excerpt'] ?? strip_tags($post['content']);
                                            echo htmlspecialchars(substr($excerpt, 0, 120)) . '...';
                                            ?>
                                        </p>
                                        
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                <i class="ri-calendar-line me-1"></i>
                                                <?php echo date('M d, Y', strtotime($post['published_at'] ?? $post['created_at'])); ?>
                                            </small>
                                            
                                            <a href="/post/<?php echo $post['slug']; ?>" class="btn btn-sm btn-outline-primary">
                                                Read More <i class="ri-arrow-right-line ms-1"></i>
                                            </a>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if (isset($pagination) && $pagination['last_page'] > 1): ?>
                    <nav class="mt-4">
                        <ul class="pagination justify-content-center">
                            <!-- Previous -->
                            <?php if ($pagination['current_page'] > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $pagination['current_page'] - 1; ?>">
                                        <i class="ri-arrow-left-line"></i> Previous
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <!-- Pages -->
                            <?php for ($i = max(1, $pagination['current_page'] - 2); $i <= min($pagination['last_page'], $pagination['current_page'] + 2); $i++): ?>
                                <li class="page-item <?php echo $i == $pagination['current_page'] ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <!-- Next -->
                            <?php if ($pagination['current_page'] < $pagination['last_page']): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $pagination['current_page'] + 1; ?>">
                                        Next <i class="ri-arrow-right-line"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                    <?php endif; ?>
                    
                <?php else: ?>
                    <!-- Empty State -->
                    <div class="text-center py-5">
                        <i class="ri-file-list-line" style="font-size: 5rem; color: #ccc;"></i>
                        <h3 class="mt-3">No posts found</h3>
                        <p class="text-muted">
                            <?php echo $search ? 'Try a different search term' : 'Check back soon for new content'; ?>
                        </p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Search -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3">
                            <i class="ri-search-line me-2"></i> Search Posts
                        </h5>
                        <form action="/posts" method="GET">
                            <div class="input-group">
                                <input type="text" name="s" class="form-control" placeholder="Search..." value="<?php echo htmlspecialchars($search ?? ''); ?>">
                                <button class="btn btn-primary" type="submit">
                                    <i class="ri-search-line"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Categories -->
                <?php if (!empty($categories)): ?>
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3">
                            <i class="ri-folder-line me-2"></i> Categories
                        </h5>
                        <ul class="list-unstyled mb-0">
                            <?php foreach ($categories as $cat): ?>
                                <li class="mb-2">
                                    <a href="/category/<?php echo $cat['slug']; ?>" class="text-decoration-none d-flex justify-content-between">
                                        <span><?php echo htmlspecialchars($cat['name']); ?></span>
                                        <span class="badge bg-light text-dark"><?php echo $cat['post_count'] ?? 0; ?></span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Popular Posts -->
                <?php if (!empty($popularPosts)): ?>
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3">
                            <i class="ri-fire-line me-2"></i> Popular Posts
                        </h5>
                        <div class="list-group list-group-flush">
                            <?php foreach ($popularPosts as $popular): ?>
                                <a href="/post/<?php echo $popular['slug']; ?>" class="list-group-item list-group-item-action border-0 px-0">
                                    <div class="d-flex gap-3">
                                        <?php if ($popular['featured_image']): ?>
                                            <img src="<?php echo $popular['featured_image']; ?>" alt="" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                                        <?php endif; ?>
                                        <div class="flex-fill">
                                            <h6 class="mb-1"><?php echo htmlspecialchars($popular['title']); ?></h6>
                                            <small class="text-muted">
                                                <?php echo date('M d, Y', strtotime($popular['published_at'] ?? $popular['created_at'])); ?>
                                            </small>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
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
.page-link {
    color: var(--primary-color);
}
.page-item.active .page-link {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}
</style>
