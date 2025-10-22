<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">🤖 AI Article Generator</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item active">AI Generator</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-4">
        <!-- Generation Settings -->
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0 text-white">
                    <i class="ri-settings-3-line me-2"></i> Generation Settings
                </h5>
            </div>
            <div class="card-body">
                <form id="ai-generator-form">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Keyword <span class="text-danger">*</span></label>
                        <input type="text" name="keyword" class="form-control" placeholder="e.g., Digital Marketing" required>
                        <small class="text-muted">Main topic/keyword untuk artikel</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Category</label>
                        <select name="category_id" class="form-select">
                            <option value="">No category</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Writing Style <span class="text-danger">*</span></label>
                        <select name="style" class="form-select" required>
                            <option value="professional">Professional (Business)</option>
                            <option value="academic">Academic (Formal with references)</option>
                            <option value="casual">Casual (Friendly & relaxed)</option>
                            <option value="journalistic">Journalistic (Objective reporting)</option>
                            <option value="storytelling">Storytelling (Engaging narrative)</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Article Length <span class="text-danger">*</span></label>
                        <select name="length" class="form-select" required>
                            <option value="short">Short (~300 words)</option>
                            <option value="medium" selected>Medium (~600 words)</option>
                            <option value="long">Long (~1000 words)</option>
                            <option value="very_long">Very Long (~1500 words)</option>
                        </select>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="ri-information-line me-2"></i>
                        <small><strong>Note:</strong> Article generation menggunakan Google Gemini AI. Pastikan API key sudah dikonfigurasi di Settings.</small>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100" id="generate-btn">
                        <i class="ri-sparkling-line me-2"></i> Generate Article
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Tips -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="ri-lightbulb-line me-2"></i> Tips
                </h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0 small">
                    <li class="mb-2"><i class="ri-check-line text-success me-1"></i> Gunakan keyword yang specific</li>
                    <li class="mb-2"><i class="ri-check-line text-success me-1"></i> Pilih category yang relevan</li>
                    <li class="mb-2"><i class="ri-check-line text-success me-1"></i> Style professional untuk blog bisnis</li>
                    <li class="mb-2"><i class="ri-check-line text-success me-1"></i> Academic untuk artikel riset</li>
                    <li class="mb-2"><i class="ri-check-line text-success me-1"></i> Review hasil sebelum publish</li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="col-lg-8">
        <!-- Generated Content -->
        <div class="card" id="result-card" style="display: none;">
            <div class="card-header bg-success text-white">
                <h5 class="card-title mb-0 text-white">
                    <i class="ri-article-line me-2"></i> Generated Article
                </h5>
            </div>
            <div class="card-body">
                <form id="save-article-form" method="POST" action="/admin/ai/save">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                    <input type="hidden" name="keyword" id="result-keyword">
                    <input type="hidden" name="category_id" id="result-category">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Title</label>
                        <input type="text" name="title" id="result-title" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Meta Description</label>
                        <textarea name="meta_description" id="result-meta" class="form-control" rows="2"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Content</label>
                        <textarea name="content" id="result-content" class="form-control" rows="15"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Status</label>
                        <select name="status" class="form-select">
                            <option value="draft">Save as Draft</option>
                            <option value="published">Publish Immediately</option>
                        </select>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="ri-save-line me-1"></i> Save as Post
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="document.getElementById('result-card').style.display='none'">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Loading State -->
        <div class="card" id="loading-card" style="display: none;">
            <div class="card-body text-center py-5">
                <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <h5>Generating Article with AI...</h5>
                <p class="text-muted">This may take 10-30 seconds. Please wait.</p>
            </div>
        </div>
        
        <!-- Empty State -->
        <div class="card" id="empty-state">
            <div class="card-body text-center py-5">
                <i class="ri-robot-line" style="font-size: 5rem; color: #ccc;"></i>
                <h4 class="mt-3">AI Article Generator</h4>
                <p class="text-muted">Fill the form on the left and click "Generate Article" to create SEO-optimized content with Google Gemini AI.</p>
                
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="p-3 border rounded">
                            <i class="ri-sparkling-fill text-warning fs-2"></i>
                            <h6 class="mt-2">AI-Powered</h6>
                            <small class="text-muted">Menggunakan Google Gemini AI terbaru</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 border rounded">
                            <i class="ri-seo-line text-success fs-2"></i>
                            <h6 class="mt-2">SEO Optimized</h6>
                            <small class="text-muted">Auto keyword integration & meta tags</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('ai-generator-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    // Show loading
    document.getElementById('empty-state').style.display = 'none';
    document.getElementById('result-card').style.display = 'none';
    document.getElementById('loading-card').style.display = 'block';
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    
    try {
        const response = await fetch('/admin/ai/generate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Hide loading
            document.getElementById('loading-card').style.display = 'none';
            
            // Show result
            document.getElementById('result-card').style.display = 'block';
            document.getElementById('result-title').value = result.data.title;
            document.getElementById('result-meta').value = result.data.meta_description;
            document.getElementById('result-content').value = result.data.content;
            document.getElementById('result-keyword').value = data.keyword;
            document.getElementById('result-category').value = data.category_id;
        } else {
            alert('Error: ' + result.message);
            document.getElementById('loading-card').style.display = 'none';
            document.getElementById('empty-state').style.display = 'block';
        }
    } catch (error) {
        alert('Error generating article: ' + error.message);
        document.getElementById('loading-card').style.display = 'none';
        document.getElementById('empty-state').style.display = 'block';
    }
});

document.getElementById('save-article-form')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    
    try {
        const response = await fetch('/admin/ai/save', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('Article saved as post!');
            window.location.href = result.redirect;
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        alert('Error saving article: ' + error.message);
    }
});
</script>

<style>
.spinner-border {
    display: inline-block;
    width: 3rem;
    height: 3rem;
    vertical-align: text-bottom;
    border: 0.25em solid currentColor;
    border-right-color: transparent;
    border-radius: 50%;
    animation: spinner-border .75s linear infinite;
}

@keyframes spinner-border {
    to { transform: rotate(360deg); }
}

.visually-hidden {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0,0,0,0);
    white-space: nowrap;
    border: 0;
}

.fs-2 {
    font-size: 2rem;
}

.border {
    border: 1px solid #dee2e6;
}

.rounded {
    border-radius: 8px;
}

.p-3 {
    padding: 1rem;
}
</style>
