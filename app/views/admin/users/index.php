<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">User Management</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item active">Users</li>
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
                        <p class="text-uppercase fw-medium text-muted mb-0">Total Users</p>
                    </div>
                    <div class="flex-shrink-0">
                        <h5 class="text-muted fs-14 mb-0"><i class="ri-user-line"></i></h5>
                    </div>
                </div>
                <div class="d-flex align-items-end justify-content-between mt-2">
                    <div>
                        <h4 class="fs-22 fw-semibold ff-secondary mb-2"><?php echo $stats['total'] ?? 0; ?></h4>
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
                        <p class="text-uppercase fw-medium text-muted mb-0">Active</p>
                    </div>
                    <div class="flex-shrink-0">
                        <h5 class="text-success fs-14 mb-0"><i class="ri-checkbox-circle-line"></i></h5>
                    </div>
                </div>
                <div class="d-flex align-items-end justify-content-between mt-2">
                    <div>
                        <h4 class="fs-22 fw-semibold ff-secondary mb-2 text-success"><?php echo $stats['active'] ?? 0; ?></h4>
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
                        <p class="text-uppercase fw-medium text-muted mb-0">Inactive</p>
                    </div>
                    <div class="flex-shrink-0">
                        <h5 class="text-warning fs-14 mb-0"><i class="ri-close-circle-line"></i></h5>
                    </div>
                </div>
                <div class="d-flex align-items-end justify-content-between mt-2">
                    <div>
                        <h4 class="fs-22 fw-semibold ff-secondary mb-2 text-warning"><?php echo $stats['inactive'] ?? 0; ?></h4>
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
                        <p class="text-uppercase fw-medium text-muted mb-0">Admins</p>
                    </div>
                    <div class="flex-shrink-0">
                        <h5 class="text-primary fs-14 mb-0"><i class="ri-shield-user-line"></i></h5>
                    </div>
                </div>
                <div class="d-flex align-items-end justify-content-between mt-2">
                    <div>
                        <h4 class="fs-22 fw-semibold ff-secondary mb-2 text-primary"><?php echo $stats['admins'] ?? 0; ?></h4>
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
                        <a href="/admin/users/create" class="btn btn-primary">
                            <i class="ri-add-line me-1"></i> Add New User
                        </a>
                    </div>
                    <div class="col-sm-6">
                        <form method="GET" class="d-flex gap-2">
                            <select name="role" class="form-select" style="width: 150px;">
                                <option value="">All Roles</option>
                                <option value="admin" <?php echo $role === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                <option value="editor" <?php echo $role === 'editor' ? 'selected' : ''; ?>>Editor</option>
                                <option value="author" <?php echo $role === 'author' ? 'selected' : ''; ?>>Author</option>
                                <option value="contributor" <?php echo $role === 'contributor' ? 'selected' : ''; ?>>Contributor</option>
                                <option value="subscriber" <?php echo $role === 'subscriber' ? 'selected' : ''; ?>>Subscriber</option>
                            </select>
                            <select name="status" class="form-select" style="width: 150px;">
                                <option value="">All Status</option>
                                <option value="active" <?php echo $status === 'active' ? 'selected' : ''; ?>>Active</option>
                                <option value="inactive" <?php echo $status === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                <option value="suspended" <?php echo $status === 'suspended' ? 'selected' : ''; ?>>Suspended</option>
                            </select>
                            <input type="search" name="search" class="form-control" placeholder="Search users..." value="<?php echo htmlspecialchars($search ?? ''); ?>">
                            <button type="submit" class="btn btn-secondary">Filter</button>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>User</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Registered</th>
                                <th>Last Login</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($users)): ?>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-xs me-3">
                                                    <div class="avatar-title bg-soft-primary text-primary rounded-circle">
                                                        <?php echo strtoupper(substr($user['first_name'], 0, 1)); ?>
                                                    </div>
                                                </div>
                                                <div>
                                                    <strong><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></strong>
                                                    <br>
                                                    <small class="text-muted">@<?php echo htmlspecialchars($user['username']); ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo $user['role'] === 'admin' ? 'danger' : 
                                                    ($user['role'] === 'editor' ? 'warning' : 
                                                    ($user['role'] === 'author' ? 'info' : 'secondary')); 
                                            ?>">
                                                <?php echo ucfirst($user['role']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo $user['status'] === 'active' ? 'success' : 
                                                    ($user['status'] === 'suspended' ? 'danger' : 'secondary'); 
                                            ?>">
                                                <?php echo ucfirst($user['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                        <td>
                                            <?php if ($user['last_login_at']): ?>
                                                <?php echo date('M d, Y H:i', strtotime($user['last_login_at'])); ?>
                                            <?php else: ?>
                                                <span class="text-muted">Never</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="/admin/users/<?php echo $user['id']; ?>/edit" class="btn btn-sm btn-primary">
                                                    <i class="ri-edit-line"></i>
                                                </a>
                                                <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                                    <form method="POST" action="/admin/users/<?php echo $user['id']; ?>/delete" class="d-inline" onsubmit="return confirm('Delete this user?');">
                                                        <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                            <i class="ri-delete-bin-line"></i>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <i class="ri-user-line display-4 text-muted"></i>
                                        <p class="text-muted mt-2">No users found</p>
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
