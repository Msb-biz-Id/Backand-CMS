<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Media Library</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item active">Media</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<!-- end page title -->

<!-- Stats -->
<div class="row mb-3">
    <div class="col-md-3">
        <div class="card mini-stats-wid">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <p class="text-muted fw-medium mb-2">Total Files</p>
                        <h4 class="mb-0"><?php echo count($media); ?></h4>
                    </div>
                    <div class="avatar-sm rounded-circle bg-primary align-self-center mini-stat-icon">
                        <span class="avatar-title rounded-circle bg-primary">
                            <i class="ri-file-line font-size-24"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card mini-stats-wid">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <p class="text-muted fw-medium mb-2">Storage Used</p>
                        <h4 class="mb-0"><?php echo $this->formatBytes($totalStorage); ?></h4>
                    </div>
                    <div class="avatar-sm rounded-circle bg-info align-self-center mini-stat-icon">
                        <span class="avatar-title rounded-circle bg-info">
                            <i class="ri-database-2-line font-size-24"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-sm-6">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
                            <i class="ri-upload-cloud-line align-middle me-1"></i> Upload Files
                        </button>
                    </div>
                    <div class="col-sm-6">
                        <form method="GET" class="d-flex gap-2">
                            <select name="type" class="form-select" style="max-width: 150px;">
                                <option value="">All Types</option>
                                <option value="image" <?php echo $type === 'image' ? 'selected' : ''; ?>>Images</option>
                                <option value="document" <?php echo $type === 'document' ? 'selected' : ''; ?>>Documents</option>
                                <option value="video" <?php echo $type === 'video' ? 'selected' : ''; ?>>Videos</option>
                                <option value="audio" <?php echo $type === 'audio' ? 'selected' : ''; ?>>Audio</option>
                            </select>
                            <input type="search" name="search" class="form-control" placeholder="Search..." value="<?php echo htmlspecialchars($search ?? ''); ?>">
                            <button type="submit" class="btn btn-secondary">Filter</button>
                        </form>
                    </div>
                </div>

                <!-- Media Grid -->
                <div class="row" id="mediaGrid">
                    <?php if (!empty($media)): ?>
                        <?php foreach ($media as $item): ?>
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
                                <div class="card media-item h-100" data-id="<?php echo $item['id']; ?>">
                                    <div class="position-relative">
                                        <?php if ($item['type'] === 'image'): ?>
                                            <img src="<?php echo htmlspecialchars($item['file_url']); ?>" 
                                                 class="card-img-top" 
                                                 alt="<?php echo htmlspecialchars($item['alt_text'] ?? $item['filename']); ?>"
                                                 style="height: 150px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 150px;">
                                                <i class="ri-file-line display-4 text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="position-absolute top-0 end-0 p-2">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    <i class="ri-more-2-fill"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="#" onclick="editMedia(<?php echo $item['id']; ?>)">Edit</a></li>
                                                    <li><a class="dropdown-item" href="<?php echo htmlspecialchars($item['file_url']); ?>" target="_blank">View</a></li>
                                                    <li><a class="dropdown-item" href="<?php echo htmlspecialchars($item['file_url']); ?>" download>Download</a></li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li><a class="dropdown-item text-danger" href="#" onclick="deleteMedia(<?php echo $item['id']; ?>)">Delete</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body p-2">
                                        <p class="card-title text-truncate mb-1" title="<?php echo htmlspecialchars($item['original_filename']); ?>">
                                            <small><?php echo htmlspecialchars($item['original_filename']); ?></small>
                                        </p>
                                        <p class="card-text mb-0">
                                            <small class="text-muted">
                                                <?php echo strtoupper($item['file_extension']); ?> · 
                                                <?php echo $this->formatBytes($item['file_size']); ?>
                                                <?php if ($item['type'] === 'image' && $item['width']): ?>
                                                    · <?php echo $item['width']; ?>×<?php echo $item['height']; ?>
                                                <?php endif; ?>
                                            </small>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12 text-center py-5">
                            <i class="ri-image-line display-1 text-muted"></i>
                            <p class="text-muted mt-3">No media files found. Upload your first file!</p>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if (isset($pagination) && $pagination['last_page'] > 1): ?>
                <div class="row mt-4">
                    <div class="col-12">
                        <nav>
                            <ul class="pagination justify-content-center">
                                <li class="page-item <?php echo $pagination['current_page'] <= 1 ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $pagination['current_page'] - 1; ?><?php echo $type ? '&type='.$type : ''; ?>">Previous</a>
                                </li>
                                
                                <?php for ($i = 1; $i <= $pagination['last_page']; $i++): ?>
                                    <li class="page-item <?php echo $i == $pagination['current_page'] ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?><?php echo $type ? '&type='.$type : ''; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>
                                
                                <li class="page-item <?php echo $pagination['current_page'] >= $pagination['last_page'] ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $pagination['current_page'] + 1; ?><?php echo $type ? '&type='.$type : ''; ?>">Next</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Files</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="/admin/media/upload" class="dropzone" id="mediaDropzone">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                    <div class="dz-message needsclick">
                        <div class="mb-3">
                            <i class="display-4 text-muted ri-upload-cloud-2-line"></i>
                        </div>
                        <h4>Drop files here or click to upload.</h4>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editForm">
                <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                <input type="hidden" name="media_id" id="edit_media_id">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Media</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" class="form-control" name="title" id="edit_title">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alt Text</label>
                        <input type="text" class="form-control" name="alt_text" id="edit_alt_text">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Caption</label>
                        <input type="text" class="form-control" name="caption" id="edit_caption">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="edit_description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Initialize Dropzone
Dropzone.options.mediaDropzone = {
    paramName: "file",
    maxFilesize: 10, // MB
    acceptedFiles: "image/*,application/pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,video/*,audio/*",
    headers: {
        'X-CSRF-TOKEN': '<?php echo $csrfToken; ?>'
    },
    init: function() {
        this.on("success", function(file, response) {
            if (response.success) {
                setTimeout(() => location.reload(), 1000);
            }
        });
        this.on("error", function(file, errorMessage) {
            alert('Upload error: ' + errorMessage);
        });
    }
};

function deleteMedia(id) {
    if (!confirm('Are you sure you want to delete this media?')) return;
    
    fetch(`/admin/media/${id}/delete`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?php echo $csrfToken; ?>'
        },
        body: JSON.stringify({ _method: 'DELETE' })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message);
        }
    });
}

function editMedia(id) {
    // TODO: Load media data and show edit modal
    alert('Edit feature: Load media ID ' + id);
}
</script>

<?php
// Helper function for formatting bytes
if (!function_exists('formatBytes')) {
    function formatBytes($bytes, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
?>
