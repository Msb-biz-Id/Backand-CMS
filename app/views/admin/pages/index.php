<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">All Pages</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item active">Pages</li>
                </ol>
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
                        <a href="/admin/pages/create" class="btn btn-primary">
                            <i class="ri-add-line me-1"></i> Add New Page
                        </a>
                    </div>
                    <div class="col-sm-6">
                        <form method="GET" class="d-flex gap-2">
                            <input type="search" name="search" class="form-control" placeholder="Search pages..." value="<?php echo htmlspecialchars($search ?? ''); ?>">
                            <button type="submit" class="btn btn-secondary">Search</button>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Title</th>
                                <th>Slug</th>
                                <th>Template</th>
                                <th>Status</th>
                                <th>Menu</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($pages)): ?>
                                <?php foreach ($pages as $page): ?>
                                    <tr>
                                        <td>
                                            <a href="/admin/pages/<?php echo $page['id']; ?>/edit" class="text-dark fw-medium">
                                                <?php echo htmlspecialchars($page['title']); ?>
                                            </a>
                                        </td>
                                        <td><code><?php echo htmlspecialchars($page['slug']); ?></code></td>
                                        <td><?php echo htmlspecialchars($page['template'] ?? 'default'); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $page['status'] === 'published' ? 'success' : 'secondary'; ?>">
                                                <?php echo ucfirst($page['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($page['show_in_menu']): ?>
                                                <i class="ri-check-line text-success"></i>
                                            <?php else: ?>
                                                <i class="ri-close-line text-muted"></i>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo date('M d, Y', strtotime($page['created_at'])); ?></td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="/admin/pages/<?php echo $page['id']; ?>/edit" class="btn btn-sm btn-primary">
                                                    <i class="ri-edit-line"></i>
                                                </a>
                                                <a href="/page/<?php echo htmlspecialchars($page['slug']); ?>" target="_blank" class="btn btn-sm btn-info">
                                                    <i class="ri-eye-line"></i>
                                                </a>
                                                <form method="POST" action="/admin/pages/<?php echo $page['id']; ?>/delete" class="d-inline" onsubmit="return confirm('Delete this page?');">
                                                    <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="ri-delete-bin-line"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <i class="ri-file-line display-4 text-muted"></i>
                                        <p class="text-muted mt-2">No pages found. <a href="/admin/pages/create">Create your first page</a></p>
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
