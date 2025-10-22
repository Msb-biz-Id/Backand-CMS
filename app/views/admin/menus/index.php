<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Menus</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item active">Menus</li>
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
                        <a href="/admin/menus/create" class="btn btn-primary">
                            <i class="ri-add-line me-1"></i> Create New Menu
                        </a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Slug</th>
                                <th>Location</th>
                                <th>Items</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($menus)): ?>
                                <?php foreach ($menus as $menu): ?>
                                    <tr>
                                        <td>
                                            <a href="/admin/menus/<?php echo $menu['id']; ?>/builder" class="text-dark fw-medium">
                                                <?php echo htmlspecialchars($menu['name']); ?>
                                            </a>
                                        </td>
                                        <td><code><?php echo htmlspecialchars($menu['slug']); ?></code></td>
                                        <td>
                                            <?php if ($menu['location']): ?>
                                                <span class="badge bg-info">
                                                    <?php echo $locations[$menu['location']] ?? $menu['location']; ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">Not assigned</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                            $sql = "SELECT COUNT(*) as count FROM cms_menu_items WHERE menu_id = ? AND deleted_at IS NULL";
                                            $result = $this->db->selectOne($sql, [$menu['id']]);
                                            $count = $result['count'] ?? 0;
                                            ?>
                                            <span class="badge bg-secondary"><?php echo $count; ?> items</span>
                                        </td>
                                        <td><?php echo date('M d, Y', strtotime($menu['created_at'])); ?></td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="/admin/menus/<?php echo $menu['id']; ?>/builder" class="btn btn-sm btn-primary" title="Menu Builder">
                                                    <i class="ri-list-settings-line"></i>
                                                </a>
                                                <a href="/admin/menus/<?php echo $menu['id']; ?>/edit" class="btn btn-sm btn-info" title="Edit">
                                                    <i class="ri-edit-line"></i>
                                                </a>
                                                <form method="POST" action="/admin/menus/<?php echo $menu['id']; ?>/delete" class="d-inline" onsubmit="return confirm('Delete this menu and all its items?');">
                                                    <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                        <i class="ri-delete-bin-line"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <i class="ri-menu-line display-4 text-muted"></i>
                                        <p class="text-muted mt-2">No menus found. <a href="/admin/menus/create">Create your first menu</a></p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Menu Locations Info -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Available Menu Locations</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($locations as $key => $label): ?>
                        <div class="col-md-6 mb-3">
                            <div class="border rounded p-3">
                                <h6 class="mb-2"><?php echo $label; ?></h6>
                                <small class="text-muted">Location: <code><?php echo $key; ?></code></small>
                                <?php
                                $assignedMenu = null;
                                foreach ($menus as $menu) {
                                    if ($menu['location'] === $key) {
                                        $assignedMenu = $menu;
                                        break;
                                    }
                                }
                                ?>
                                <?php if ($assignedMenu): ?>
                                    <p class="mb-0 mt-2">
                                        <span class="badge bg-success">Active</span>
                                        <strong><?php echo htmlspecialchars($assignedMenu['name']); ?></strong>
                                    </p>
                                <?php else: ?>
                                    <p class="mb-0 mt-2">
                                        <span class="badge bg-secondary">No menu assigned</span>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
