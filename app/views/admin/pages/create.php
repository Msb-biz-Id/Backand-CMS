<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Add New Page</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="/admin/pages">Pages</a></li>
                    <li class="breadcrumb-item active">Add New</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<form method="POST" action="/admin/pages">
    <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
    
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Page Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="title" id="title" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Slug <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="slug" id="slug" required>
                        <small class="text-muted">URL-friendly version of the title</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Content <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="content" id="content" rows="15"></textarea>
                    </div>
                </div>
            </div>

            <!-- SEO Section -->
            <div class="card">
                <div class="card-header">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="seo_enabled" id="seo_enabled" value="1" checked>
                        <label class="form-check-label fw-bold" for="seo_enabled">
                            <i class="ri-search-line me-1"></i> SEO Settings
                        </label>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Meta Title</label>
                        <input type="text" class="form-control" name="meta_title" maxlength="60">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Meta Description</label>
                        <textarea class="form-control" name="meta_description" rows="2" maxlength="160"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Meta Keywords</label>
                        <input type="text" class="form-control" name="meta_keywords">
                    </div>
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
                        <select class="form-select" name="status" required>
                            <option value="draft">Draft</option>
                            <option value="published" selected>Published</option>
                            <option value="private">Private</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Parent Page</label>
                        <select class="form-select" name="parent_id">
                            <option value="">None (Top Level)</option>
                            <?php if (!empty($allPages)): ?>
                                <?php foreach ($allPages as $p): ?>
                                    <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['title']); ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Template</label>
                        <select class="form-select" name="template">
                            <option value="default">Default</option>
                            <option value="full-width">Full Width</option>
                            <option value="sidebar-left">Sidebar Left</option>
                            <option value="sidebar-right">Sidebar Right</option>
                        </select>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="show_in_menu" value="1" id="show_in_menu" checked>
                        <label class="form-check-label" for="show_in_menu">
                            Show in Menu
                        </label>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Menu Order</label>
                        <input type="number" class="form-control" name="menu_order" value="0">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Page Order</label>
                        <input type="number" class="form-control" name="order" value="0">
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-save-line me-1"></i> Publish Page
                        </button>
                        <a href="/admin/pages" class="btn btn-secondary">Cancel</a>
                    </div>
                </div>
            </div>

            <!-- Featured Image -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Featured Image</h5>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <img src="/assets/images/placeholder.jpg" class="img-fluid" alt="Preview" style="max-height: 200px;">
                    </div>
                    <input type="hidden" name="featured_image" id="featured_image">
                    <button type="button" class="btn btn-sm btn-primary mt-2 w-100">
                        <i class="ri-image-add-line me-1"></i> Set Featured Image
                    </button>
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

// Initialize CKEditor
ClassicEditor
    .create(document.querySelector('#content'), {
        toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 
                  'blockQuote', 'insertTable', '|', 'imageUpload', '|', 'undo', 'redo']
    })
    .catch(error => {
        console.error(error);
    });
</script>
