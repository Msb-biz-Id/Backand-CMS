<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Add New Post</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="/admin/posts">Posts</a></li>
                    <li class="breadcrumb-item active">Add New</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<!-- end page title -->

<form method="POST" action="/admin/posts" id="postForm">
    <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
    
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
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

                    <div class="mb-3">
                        <label class="form-label">Excerpt</label>
                        <textarea class="form-control" name="excerpt" rows="3" placeholder="Brief description (optional - will be auto-generated if empty)"></textarea>
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
                <div class="card-body" id="seo_section">
                    <div class="mb-3">
                        <label class="form-label">Meta Title</label>
                        <input type="text" class="form-control" name="meta_title" maxlength="60">
                        <small class="text-muted">Optimal: 50-60 characters</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Meta Description</label>
                        <textarea class="form-control" name="meta_description" rows="2" maxlength="160"></textarea>
                        <small class="text-muted">Optimal: 120-160 characters</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Focus Keyword</label>
                        <input type="text" class="form-control" name="focus_keyword" placeholder="Main keyword for this post">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Meta Keywords</label>
                        <input type="text" class="form-control" name="meta_keywords" placeholder="keyword1, keyword2, keyword3">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Canonical URL</label>
                        <input type="url" class="form-control" name="canonical_url" placeholder="https://example.com/post-url">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Robots Meta</label>
                        <select class="form-select" name="robots">
                            <option value="index, follow">Index, Follow (Default)</option>
                            <option value="noindex, follow">No Index, Follow</option>
                            <option value="index, nofollow">Index, No Follow</option>
                            <option value="noindex, nofollow">No Index, No Follow</option>
                        </select>
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
                        <select class="form-select" name="status" id="status" required>
                            <option value="draft">Draft</option>
                            <option value="published" selected>Published</option>
                            <option value="scheduled">Scheduled</option>
                        </select>
                    </div>

                    <div class="mb-3" id="published_at_field">
                        <label class="form-label">Publish Date</label>
                        <input type="datetime-local" class="form-control" name="published_at" value="<?php echo date('Y-m-d\TH:i'); ?>">
                    </div>

                    <div class="mb-3 d-none" id="scheduled_at_field">
                        <label class="form-label">Schedule Date</label>
                        <input type="datetime-local" class="form-control" name="scheduled_at">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Auto Archive Date</label>
                        <input type="datetime-local" class="form-control" name="auto_archive_at">
                        <small class="text-muted">Automatically archive this post</small>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="is_featured">
                        <label class="form-check-label" for="is_featured">
                            Mark as Featured
                        </label>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="allow_comments" value="1" id="allow_comments" checked>
                        <label class="form-check-label" for="allow_comments">
                            Allow Comments
                        </label>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-save-line me-1"></i> Publish Post
                        </button>
                        <a href="/admin/posts" class="btn btn-secondary">Cancel</a>
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
                        <img src="/assets/images/placeholder.jpg" class="img-fluid" alt="Preview" style="max-height: 200px;">
                    </div>
                    <input type="hidden" name="featured_image" id="featured_image">
                    <button type="button" class="btn btn-sm btn-primary mt-2 w-100">
                        <i class="ri-image-add-line me-1"></i> Set Featured Image
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
                        <option value="1">Technology</option>
                        <option value="2">Business</option>
                        <option value="3">Lifestyle</option>
                        <option value="4">Health</option>
                        <option value="5">Travel</option>
                    </select>
                    <small class="text-muted">Hold Ctrl to select multiple</small>
                </div>
            </div>

            <!-- Tags -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Tags</h5>
                </div>
                <div class="card-body">
                    <select class="form-select select2" name="tags[]" multiple>
                        <option value="1">PHP</option>
                        <option value="2">JavaScript</option>
                        <option value="3">CMS</option>
                        <option value="4">SEO</option>
                        <option value="5">Tutorial</option>
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

// Handle status change
document.getElementById('status').addEventListener('change', function() {
    const published_field = document.getElementById('published_at_field');
    const scheduled_field = document.getElementById('scheduled_at_field');
    
    if (this.value === 'scheduled') {
        published_field.classList.add('d-none');
        scheduled_field.classList.remove('d-none');
    } else {
        published_field.classList.remove('d-none');
        scheduled_field.classList.add('d-none');
    }
});

// Toggle SEO section
document.getElementById('seo_enabled').addEventListener('change', function() {
    document.getElementById('seo_section').style.display = this.checked ? 'block' : 'none';
});

// Initialize CKEditor
ClassicEditor
    .create(document.querySelector('#content'), {
        toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'blockQuote', 'insertTable', '|', 'undo', 'redo']
    })
    .catch(error => {
        console.error(error);
    });

// Initialize Select2 for tags
$(document).ready(function() {
    $('.select2').select2({
        tags: true,
        placeholder: 'Select or add tags'
    });
});
</script>
