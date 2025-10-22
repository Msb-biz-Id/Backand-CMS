<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Comments</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item active">Comments</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row">
    <div class="col-md-3">
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="text-uppercase fw-medium text-muted mb-0">Total Comments</p>
                    </div>
                </div>
                <div class="d-flex align-items-end justify-content-between mt-2">
                    <div>
                        <h4 class="fs-22 fw-semibold ff-secondary mb-2"><?php echo $stats['total']; ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="text-uppercase fw-medium text-muted mb-0">Approved</p>
                    </div>
                </div>
                <div class="d-flex align-items-end justify-content-between mt-2">
                    <div>
                        <h4 class="fs-22 fw-semibold ff-secondary mb-2 text-success"><?php echo $stats['approved']; ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="text-uppercase fw-medium text-muted mb-0">Pending</p>
                    </div>
                </div>
                <div class="d-flex align-items-end justify-content-between mt-2">
                    <div>
                        <h4 class="fs-22 fw-semibold ff-secondary mb-2 text-warning"><?php echo $stats['pending']; ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card card-animate">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="text-uppercase fw-medium text-muted mb-0">Spam</p>
                    </div>
                </div>
                <div class="d-flex align-items-end justify-content-between mt-2">
                    <div>
                        <h4 class="fs-22 fw-semibold ff-secondary mb-2 text-danger"><?php echo $stats['spam']; ?></h4>
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
                        <form method="POST" action="/admin/comments/bulk" id="bulk-form">
                            <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                            <div class="d-flex gap-2">
                                <select name="action" class="form-select" style="width: 150px;">
                                    <option value="">Bulk Actions</option>
                                    <option value="approve">Approve</option>
                                    <option value="reject">Reject</option>
                                    <option value="spam">Mark as Spam</option>
                                    <option value="delete">Delete</option>
                                </select>
                                <button type="submit" class="btn btn-secondary" onclick="return confirm('Apply bulk action?');">Apply</button>
                            </div>
                        </form>
                    </div>
                    <div class="col-sm-6">
                        <form method="GET" class="d-flex gap-2">
                            <select name="status" class="form-select" style="width: 150px;">
                                <option value="">All Status</option>
                                <option value="approved" <?php echo $status === 'approved' ? 'selected' : ''; ?>>Approved</option>
                                <option value="pending" <?php echo $status === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="spam" <?php echo $status === 'spam' ? 'selected' : ''; ?>>Spam</option>
                                <option value="rejected" <?php echo $status === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                            </select>
                            <input type="search" name="search" class="form-control" placeholder="Search comments..." value="<?php echo htmlspecialchars($search ?? ''); ?>">
                            <button type="submit" class="btn btn-secondary">Filter</button>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="30">
                                    <input type="checkbox" id="select-all" class="form-check-input">
                                </th>
                                <th>Author</th>
                                <th>Comment</th>
                                <th>Post</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($comments)): ?>
                                <?php foreach ($comments as $comment): ?>
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="comment_ids[]" value="<?php echo $comment['id']; ?>" class="form-check-input comment-checkbox" form="bulk-form">
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($comment['author_name'] ?? 'Anonymous'); ?></strong>
                                            <br>
                                            <small class="text-muted"><?php echo htmlspecialchars($comment['author_email'] ?? ''); ?></small>
                                        </td>
                                        <td>
                                            <div style="max-width: 400px;">
                                                <?php echo htmlspecialchars(substr($comment['content'], 0, 150)); ?>
                                                <?php if (strlen($comment['content']) > 150): ?>...<?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if (!empty($comment['post_slug'])): ?>
                                                <a href="/post/<?php echo $comment['post_slug']; ?>" target="_blank">
                                                    <?php echo htmlspecialchars($comment['post_title']); ?>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">Post deleted</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo $comment['status'] === 'approved' ? 'success' : 
                                                    ($comment['status'] === 'pending' ? 'warning' : 
                                                    ($comment['status'] === 'spam' ? 'danger' : 'secondary')); 
                                            ?>">
                                                <?php echo ucfirst($comment['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M d, Y H:i', strtotime($comment['created_at'])); ?></td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    Actions
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <?php if ($comment['status'] !== 'approved'): ?>
                                                    <li>
                                                        <form method="POST" action="/admin/comments/<?php echo $comment['id']; ?>/approve" class="d-inline">
                                                            <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                                                            <button type="submit" class="dropdown-item text-success">
                                                                <i class="ri-check-line me-1"></i> Approve
                                                            </button>
                                                        </form>
                                                    </li>
                                                    <?php endif; ?>
                                                    <li>
                                                        <form method="POST" action="/admin/comments/<?php echo $comment['id']; ?>/reject" class="d-inline">
                                                            <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                                                            <button type="submit" class="dropdown-item text-warning">
                                                                <i class="ri-close-line me-1"></i> Reject
                                                            </button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <form method="POST" action="/admin/comments/<?php echo $comment['id']; ?>/spam" class="d-inline">
                                                            <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                                                            <button type="submit" class="dropdown-item text-danger">
                                                                <i class="ri-spam-line me-1"></i> Mark as Spam
                                                            </button>
                                                        </form>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <form method="POST" action="/admin/comments/<?php echo $comment['id']; ?>/delete" class="d-inline" onsubmit="return confirm('Delete this comment?');">
                                                            <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                                                            <button type="submit" class="dropdown-item text-danger">
                                                                <i class="ri-delete-bin-line me-1"></i> Delete
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <i class="ri-chat-3-line display-4 text-muted"></i>
                                        <p class="text-muted mt-2">No comments found</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if (isset($pagination) && $pagination['last_page'] > 1): ?>
                <div class="row mt-4">
                    <div class="col-12">
                        <nav>
                            <ul class="pagination justify-content-end">
                                <li class="page-item <?php echo $pagination['current_page'] <= 1 ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $pagination['current_page'] - 1; ?>">Previous</a>
                                </li>
                                <?php for ($i = 1; $i <= $pagination['last_page']; $i++): ?>
                                    <li class="page-item <?php echo $i == $pagination['current_page'] ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>
                                <li class="page-item <?php echo $pagination['current_page'] >= $pagination['last_page'] ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $pagination['current_page'] + 1; ?>">Next</a>
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

<script>
// Select all checkboxes
document.getElementById('select-all')?.addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.comment-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});
</script>
