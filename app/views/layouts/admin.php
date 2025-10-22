<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>Advanced CMS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Advanced CMS - Modern Content Management System" />
    <meta name="author" content="Advanced CMS" />
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?php echo $csrfToken ?? ''; ?>">
    
    <!-- App favicon -->
    <link rel="shortcut icon" href="/assets/images/favicon.ico">

    <!-- Bootstrap Css -->
    <link href="/assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="/assets/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />
    
    <?php if (isset($extraCss)): ?>
        <?php foreach ($extraCss as $css): ?>
            <link href="<?php echo $css; ?>" rel="stylesheet" type="text/css" />
        <?php endforeach; ?>
    <?php endif; ?>

</head>

<body data-sidebar="dark">

    <!-- Begin page -->
    <div id="layout-wrapper">

        <!-- ========== Top Bar ========== -->
        <header id="page-topbar">
            <div class="navbar-header">
                <div class="d-flex">
                    <!-- LOGO -->
                    <div class="navbar-brand-box">
                        <a href="/admin/dashboard" class="logo logo-dark">
                            <span class="logo-sm">
                                <img src="/assets/images/logo-sm.png" alt="" height="22">
                            </span>
                            <span class="logo-lg">
                                <img src="/assets/images/logo-dark.png" alt="" height="20">
                            </span>
                        </a>

                        <a href="/admin/dashboard" class="logo logo-light">
                            <span class="logo-sm">
                                <img src="/assets/images/logo-sm.png" alt="" height="22">
                            </span>
                            <span class="logo-lg">
                                <img src="/assets/images/logo-light.png" alt="" height="20">
                            </span>
                        </a>
                    </div>

                    <button type="button" class="btn btn-sm px-3 font-size-16 header-item waves-effect vertical-menu-btn">
                        <i class="fa fa-fw fa-bars"></i>
                    </button>
                </div>

                <div class="d-flex">
                    <!-- User -->
                    <div class="dropdown d-inline-block">
                        <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img class="rounded-circle header-profile-user" src="/assets/images/users/avatar-1.jpg"
                                alt="Header Avatar">
                            <span class="d-none d-xl-inline-block ms-1 fw-medium font-size-15">
                                <?php echo htmlspecialchars($_SESSION['user']['first_name'] ?? 'Admin'); ?>
                            </span>
                            <i class="uil-angle-down d-none d-xl-inline-block font-size-15"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <!-- item-->
                            <a class="dropdown-item" href="/admin/profile"><i class="uil uil-user-circle font-size-18 align-middle text-muted me-1"></i> <span class="align-middle">Profile</span></a>
                            <a class="dropdown-item" href="/admin/settings"><i class="uil uil-cog font-size-18 align-middle me-1 text-muted"></i> <span class="align-middle">Settings</span></a>
                            <a class="dropdown-item" href="/admin/logout"><i class="uil uil-sign-out-alt font-size-18 align-middle me-1 text-muted"></i> <span class="align-middle">Logout</span></a>
                        </div>
                    </div>

                </div>
            </div>
        </header>

        <!-- ========== Left Sidebar Start ========== -->
        <div class="vertical-menu">

            <div data-simplebar class="h-100">

                <!--- Sidemenu -->
                <div id="sidebar-menu">
                    <!-- Left Menu Start -->
                    <ul class="metismenu list-unstyled" id="side-menu">
                        <li class="menu-title">Menu</li>

                        <li>
                            <a href="/admin/dashboard" class="waves-effect">
                                <i class="uil-home-alt"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>

                        <li>
                            <a href="javascript: void(0);" class="has-arrow waves-effect">
                                <i class="uil-file-alt"></i>
                                <span>Posts</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                <li><a href="/admin/posts">All Posts</a></li>
                                <li><a href="/admin/posts/create">Add New</a></li>
                                <li><a href="/admin/categories?type=post">Categories</a></li>
                                <li><a href="/admin/tags?type=post">Tags</a></li>
                            </ul>
                        </li>

                        <li>
                            <a href="javascript: void(0);" class="has-arrow waves-effect">
                                <i class="uil-file"></i>
                                <span>Pages</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                <li><a href="/admin/pages">All Pages</a></li>
                                <li><a href="/admin/pages/create">Add New</a></li>
                            </ul>
                        </li>

                        <li>
                            <a href="javascript: void(0);" class="has-arrow waves-effect">
                                <i class="uil-shopping-cart"></i>
                                <span>Products</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                <li><a href="/admin/products">All Products</a></li>
                                <li><a href="/admin/products/create">Add New</a></li>
                                <li><a href="/admin/categories?type=product">Categories</a></li>
                            </ul>
                        </li>

                        <li>
                            <a href="javascript: void(0);" class="has-arrow waves-effect">
                                <i class="uil-briefcase"></i>
                                <span>Jobs</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                <li><a href="/admin/jobs">All Jobs</a></li>
                                <li><a href="/admin/jobs/create">Add New</a></li>
                                <li><a href="/admin/jobs/applications">Applications</a></li>
                            </ul>
                        </li>

                        <li>
                            <a href="/admin/media" class="waves-effect">
                                <i class="uil-images"></i>
                                <span>Media Library</span>
                            </a>
                        </li>

                        <li>
                            <a href="/admin/ads" class="waves-effect">
                                <i class="uil-ad"></i>
                                <span>Advertisements</span>
                            </a>
                        </li>

                        <li class="menu-title">Analytics & SEO</li>

                        <li>
                            <a href="/admin/analytics" class="waves-effect">
                                <i class="uil-chart"></i>
                                <span>Analytics</span>
                            </a>
                        </li>

                        <li>
                            <a href="/admin/seo" class="waves-effect">
                                <i class="uil-search"></i>
                                <span>SEO Settings</span>
                            </a>
                        </li>

                        <li class="menu-title">System</li>

                        <li>
                            <a href="/admin/users" class="waves-effect">
                                <i class="uil-users-alt"></i>
                                <span>Users</span>
                            </a>
                        </li>

                        <li>
                            <a href="/admin/settings" class="waves-effect">
                                <i class="uil-cog"></i>
                                <span>Settings</span>
                            </a>
                        </li>

                        <li>
                            <a href="javascript: void(0);" class="has-arrow waves-effect">
                                <i class="uil-wrench"></i>
                                <span>Tools</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                <li><a href="/admin/tools/backup">Backup</a></li>
                                <li><a href="/admin/tools/cache">Cache</a></li>
                            </ul>
                        </li>

                    </ul>
                </div>
                <!-- Sidebar -->
            </div>
        </div>
        <!-- Left Sidebar End -->

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">

            <div class="page-content">
                <div class="container-fluid">

                    <?php if (isset($_SESSION['flash'])): ?>
                        <?php foreach ($_SESSION['flash'] as $type => $message): ?>
                            <div class="alert alert-<?php echo $type === 'error' ? 'danger' : $type; ?> alert-dismissible fade show" role="alert">
                                <?php echo htmlspecialchars($message); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <?php unset($_SESSION['flash'][$type]); ?>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <!-- Page Content -->
                    <?php include $viewPath; ?>

                </div> <!-- container-fluid -->
            </div>
            <!-- End Page-content -->

            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <?php echo date('Y'); ?> © Advanced CMS.
                        </div>
                        <div class="col-sm-6">
                            <div class="text-sm-end d-none d-sm-block">
                                Crafted with <i class="mdi mdi-heart text-danger"></i> by Advanced CMS Team
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
        <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->

    <!-- JAVASCRIPT -->
    <script src="/assets/libs/jquery/jquery.min.js"></script>
    <script src="/assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/libs/metismenu/metisMenu.min.js"></script>
    <script src="/assets/libs/simplebar/simplebar.min.js"></script>
    <script src="/assets/libs/node-waves/waves.min.js"></script>

    <!-- App js -->
    <script src="/assets/js/app.js"></script>

    <?php if (isset($extraJs)): ?>
        <?php foreach ($extraJs as $js): ?>
            <script src="<?php echo $js; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>

    <script>
        // CSRF Token for AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>

</body>
</html>
