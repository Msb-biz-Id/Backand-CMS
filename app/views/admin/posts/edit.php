<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Edit Post</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="/admin/posts">Posts</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<!-- end page title -->

<form method="POST" action="/admin/posts/<?php echo $post['id']; ?>" id="postForm">
    <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
    <input type="hidden" name="_method" value="PUT">
    
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="title" id="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Slug <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="slug" id="slug" value="<?php echo htmlspecialchars($post['slug']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Content <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="content" id="content" rows="15"><?php echo htmlspecialchars($post['content']); ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Excerpt</label>
                        <textarea class="form-control" name="excerpt" rows="3"><?php echo htmlspecialchars($post['excerpt'] ?? ''); ?></textarea>
                    </div>
                </div>
            </div>

            <!-- SEO Section -->
            <div class="card">
                <div class="card-header">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="seo_enabled" id="seo_enabled" value="1" <?php echo !empty($post['seo']) ? 'checked' : ''; ?>>
                        <label class="form-check-label fw-bold" for="seo_enabled">
                            <i class="ri-search-line me-1"></i> SEO Settings
                        </label>
                    </div>
                </div>
                <div class="card-body" id="seo_section">
                    <div class="mb-3">
                        <label class="form-label">Meta Title</label>
                        <input type="text" class="form-control" name="meta_title" value="<?php echo htmlspecialchars($post['seo']['meta_title'] ?? ''); ?>" maxlength="60">
                        <small class="text-muted">Optimal: 50-60 characters</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Meta Description</label>
                        <textarea class="form-control" name="meta_description" rows="2" maxlength="160"><?php echo htmlspecialchars($post['seo']['meta_description'] ?? ''); ?></textarea>
                        <small class="text-muted">Optimal: 120-160 characters</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Focus Keyword</label>
                        <input type="text" class="form-control" name="focus_keyword" value="<?php echo htmlspecialchars($post['seo']['focus_keyword'] ?? ''); ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Meta Keywords</label>
                        <input type="text" class="form-control" name="meta_keywords" value="<?php echo htmlspecialchars($post['seo']['meta_keywords'] ?? ''); ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Canonical URL</label>
                        <input type="url" class="form-control" name="canonical_url" value="<?php echo htmlspecialchars($post['seo']['canonical_url'] ?? ''); ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Robots Meta</label>
                        <select class="form-select" name="robots">
                            <?php $robots = $post['seo']['robots'] ?? 'index, follow'; ?>
                            <option value="index, follow" <?php echo $robots === 'index, follow' ? 'selected' : ''; ?>>Index, Follow</option>
                            <option value="noindex, follow" <?php echo $robots === 'noindex, follow' ? 'selected' : ''; ?>>No Index, Follow</option>
                            <option value="index, nofollow" <?php echo $robots === 'index, nofollow' ? 'selected' : ''; ?>>Index, No Follow</option>
                            <option value="noindex, nofollow" <?php echo $robots === 'noindex, nofollow' ? 'selected' : ''; ?>>No Index, No Follow</option>
                        </select>
                    </div>

                    <?php if (!empty($post['seo']['seo_score'])): ?>
                    <div class="alert alert-info">
                        <strong>SEO Score:</strong> <?php echo $post['seo']['seo_score']; ?>/100
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Publish Box -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Publish</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status" id="status" required>
                            <option value="draft" <?php echo $post['status'] === 'draft' ? 'selected' : ''; ?>>Draft</option>
                            <option value="published" <?php echo $post['status'] === 'published' ? 'selected' : ''; ?>>Published</option>
                            <option value="scheduled" <?php echo $post['status'] === 'scheduled' ? 'selected' : ''; ?>>Scheduled</option>
                            <option value="archived" <?php echo $post['status'] === 'archived' ? 'selected' : ''; ?>>Archived</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Publish Date</label>
                        <input type="datetime-local" class="form-control" name="published_at" value="<?php echo $post['published_at'] ? date('Y-m-d\TH:i', strtotime($post['published_at'])) : ''; ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Auto Archive Date</label>
                        <input type="datetime-local" class="form-control" name="auto_archive_at" value="<?php echo $post['auto_archive_at'] ? date('Y-m-d\TH:i', strtotime($post['auto_archive_at'])) : ''; ?>">
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="is_featured" <?php echo $post['is_featured'] ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="is_featured">
                            Mark as Featured
                        </label>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="allow_comments" value="1" id="allow_comments" <?php echo $post['allow_comments'] ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="allow_comments">
                            Allow Comments
                        </label>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted">
                            <strong>Views:</strong> <?php echo number_format($post['views']); ?><br>
                            <strong>Created:</strong> <?php echo date('M d, Y H:i', strtotime($post['created_at'])); ?><br>
                            <strong>Modified:</strong> <?php echo date('M d, Y H:i', strtotime($post['updated_at'])); ?>
                        </small>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-save-line me-1"></i> Update Post
                        </button>
                        <a href="/admin/posts" class="btn btn-secondary">Cancel</a>
                        <a href="/post/<?php echo htmlspecialchars($post['slug']); ?>" target="_blank" class="btn btn-info">
                            <i class="ri-eye-line me-1"></i> Preview
                        </a>
                    </div>
                </div>
            </div>

            <!-- Featured Image -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Featured Image</h5>
                </div>
                <div class="card-body">
                    <div class="text-center" id="image_preview">
                        <img src="<?php echo $post['featured_image'] ?? '/assets/images/placeholder.jpg'; ?>" class="img-fluid" alt="Preview" style="max-height: 200px;">
                    </div>
                    <input type="hidden" name="featured_image" id="featured_image" value="<?php echo htmlspecialchars($post['featured_image'] ?? ''); ?>">
                    <button type="button" class="btn btn-sm btn-primary mt-2 w-100">
                        <i class="ri-image-edit-line me-1"></i> Change Featured Image
                    </button>
                </div>
            </div>

            <!-- Categories -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Categories</h5>
                </div>
                <div class="card-body">
                    <select class="form-select" name="categories[]" multiple size="5">
                        <?php
                        $postCategories = array_column($post['categories'] ?? [], 'id');
                        $allCategories = [
                            ['id' => 1, 'name' => 'Technology'],
                            ['id' => 2, 'name' => 'Business'],
                            ['id' => 3, 'name' => 'Lifestyle'],
                            ['id' => 4, 'name' => 'Health'],
                            ['id' => 5, 'name' => 'Travel']
                        ];
                        foreach ($allCategories as $cat):
                        ?>
                            <option value="<?php echo $cat['id']; ?>" <?php echo in_array($cat['id'], $postCategories) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Tags -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Tags</h5>
                </div>
                <div class="card-body">
                    <select class="form-select select2" name="tags[]" multiple>
                        <?php
                        $postTags = array_column($post['tags'] ?? [], 'id');
                        $allTags = [
                            ['id' => 1, 'name' => 'PHP'],
                            ['id' => 2, 'name' => 'JavaScript'],
                            ['id' => 3, 'name' => 'CMS'],
                            ['id' => 4, 'name' => 'SEO'],
                            ['id' => 5, 'name' => 'Tutorial']
                        ];
                        foreach ($allTags as $tag):
                        ?>
                            <option value="<?php echo $tag['id']; ?>" <?php echo in_array($tag['id'], $postTags) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($tag['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
// Auto-generate slug from title
document.getElementById('title').addEventListener('input', function() {
    const slug = this.value
        .toLowerCase()
        .replace(/[^\w\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/--+/g, '-');
    document.getElementById('slug').value = slug;
});

// Toggle SEO section
document.getElementById('seo_enabled').addEventListener('change', function() {
    document.getElementById('seo_section').style.display = this.checked ? 'block' : ''; 
});

// Initialize CKEditor
ClassicEditor
    .create(document.querySelector('#content'), {
        toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'blockQuote', 'insertTable', '|', 'undo', 'redo']
    })
    .catch(error => {
        console.error(error);
    });

// Initialize Select2
$(document).ready(function() {
    $('.select2').select2({
        tags: true,
        placeholder: 'Select or add tags'
    });
});
</script>
