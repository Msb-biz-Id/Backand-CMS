<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Dashboard</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Advanced CMS</a></li>
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </div>

        </div>
    </div>
</div>
<!-- end page title -->

<div class="row">
    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <p class="text-truncate font-size-14 mb-2">Total Posts</p>
                        <h4 class="mb-2"><?php echo $stats['posts']['total'] ?? 0; ?></h4>
                        <p class="text-muted mb-0"><span class="text-success fw-bold font-size-12 me-2"><i class="ri-arrow-right-up-line me-1 align-middle"></i><?php echo $stats['posts']['today'] ?? 0; ?></span>Today</p>
                    </div>
                    <div class="avatar-sm">
                        <span class="avatar-title bg-light text-primary rounded-3">
                            <i class="ri-file-text-line font-size-24"></i>  
                        </span>
                    </div>
                </div>                                              
            </div><!-- end cardbody -->
        </div><!-- end card -->
    </div><!-- end col -->

    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <p class="text-truncate font-size-14 mb-2">Total Pages</p>
                        <h4 class="mb-2"><?php echo $stats['pages']['total'] ?? 0; ?></h4>
                        <p class="text-muted mb-0"><span class="text-success fw-bold font-size-12 me-2"><?php echo $stats['pages']['published'] ?? 0; ?></span>Published</p>
                    </div>
                    <div class="avatar-sm">
                        <span class="avatar-title bg-light text-success rounded-3">
                            <i class="ri-file-copy-line font-size-24"></i>  
                        </span>
                    </div>
                </div>                                              
            </div><!-- end cardbody -->
        </div><!-- end card -->
    </div><!-- end col -->

    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <p class="text-truncate font-size-14 mb-2">Total Products</p>
                        <h4 class="mb-2"><?php echo $stats['products']['total'] ?? 0; ?></h4>
                        <p class="text-muted mb-0"><span class="text-danger fw-bold font-size-12 me-2"><?php echo $stats['products']['out_of_stock'] ?? 0; ?></span>Out of Stock</p>
                    </div>
                    <div class="avatar-sm">
                        <span class="avatar-title bg-light text-info rounded-3">
                            <i class="ri-shopping-cart-line font-size-24"></i>  
                        </span>
                    </div>
                </div>                                              
            </div><!-- end cardbody -->
        </div><!-- end card -->
    </div><!-- end col -->

    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex">
                    <div class="flex-grow-1">
                        <p class="text-truncate font-size-14 mb-2">Total Users</p>
                        <h4 class="mb-2"><?php echo $stats['users']['total'] ?? 0; ?></h4>
                        <p class="text-muted mb-0"><span class="text-success fw-bold font-size-12 me-2"><i class="ri-arrow-right-up-line me-1 align-middle"></i><?php echo $stats['users']['today'] ?? 0; ?></span>Today</p>
                    </div>
                    <div class="avatar-sm">
                        <span class="avatar-title bg-light text-warning rounded-3">
                            <i class="ri-group-line font-size-24"></i>  
                        </span>
                    </div>
                </div>                                              
            </div><!-- end cardbody -->
        </div><!-- end card -->
    </div><!-- end col -->
</div><!-- end row -->

<div class="row">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-body">
                <div class="d-sm-flex flex-wrap">
                    <h4 class="card-title mb-4">Analytics Overview</h4>
                </div>

                <div class="row text-center">
                    <div class="col-4">
                        <div class="mt-4">
                            <p class="text-muted mb-1">Total Views</p>
                            <h5><?php echo number_format($stats['analytics']['total_views'] ?? 0); ?></h5>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="mt-4">
                            <p class="text-muted mb-1">Unique Visitors</p>
                            <h5><?php echo number_format($stats['analytics']['unique_views'] ?? 0); ?></h5>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="mt-4">
                            <p class="text-muted mb-1">Avg. Time</p>
                            <h5><?php echo round($stats['analytics']['avg_time'] ?? 0); ?>s</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Comments</h4>
                
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-xs me-3">
                        <span class="avatar-title rounded-circle bg-primary bg-soft text-primary font-size-18">
                            <i class="ri-chat-3-line"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="mb-0"><?php echo $stats['comments']['total'] ?? 0; ?></h5>
                        <p class="text-muted mb-0">Total Comments</p>
                    </div>
                </div>

                <div class="d-flex align-items-center">
                    <div class="avatar-xs me-3">
                        <span class="avatar-title rounded-circle bg-warning bg-soft text-warning font-size-18">
                            <i class="ri-time-line"></i>
                        </span>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="mb-0"><?php echo $stats['comments']['pending'] ?? 0; ?></h5>
                        <p class="text-muted mb-0">Pending Approval</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end row -->

<div class="row">
    <div class="col-xl-6">
        <div class="card">
            <div class="card-body">
                <div class="d-sm-flex flex-wrap">
                    <h4 class="card-title mb-4">Recent Posts</h4>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recentPosts)): ?>
                                <?php foreach ($recentPosts as $post): ?>
                                    <tr>
                                        <td><a href="/admin/posts/<?php echo $post['id']; ?>/edit"><?php echo htmlspecialchars($post['title']); ?></a></td>
                                        <td><?php echo htmlspecialchars($post['first_name'] . ' ' . $post['last_name']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $post['status'] === 'published' ? 'success' : 'warning'; ?>">
                                                <?php echo ucfirst($post['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M d, Y', strtotime($post['created_at'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center">No posts yet</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-6">
        <div class="card">
            <div class="card-body">
                <div class="d-sm-flex flex-wrap">
                    <h4 class="card-title mb-4">Recent Activity</h4>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Action</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recentActivity)): ?>
                                <?php foreach ($recentActivity as $activity): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($activity['username'] ?? 'System'); ?></td>
                                        <td><?php echo htmlspecialchars($activity['description']); ?></td>
                                        <td><?php echo date('M d, H:i', strtotime($activity['created_at'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center">No recent activity</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end row -->

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">System Information</h4>
                
                <div class="row">
                    <div class="col-lg-3 col-md-6">
                        <div class="mt-4">
                            <p class="text-muted mb-1">PHP Version</p>
                            <h5><?php echo $systemInfo['php_version'] ?? 'Unknown'; ?></h5>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="mt-4">
                            <p class="text-muted mb-1">Database</p>
                            <h5><?php echo $systemInfo['database_version'] ?? 'Unknown'; ?></h5>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="mt-4">
                            <p class="text-muted mb-1">Storage Used</p>
                            <h5><?php echo $systemInfo['storage_used'] ?? '0 B'; ?></h5>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="mt-4">
                            <p class="text-muted mb-1">Cache Status</p>
                            <h5><?php echo $systemInfo['cache_enabled'] ? 'Enabled' : 'Disabled'; ?></h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
