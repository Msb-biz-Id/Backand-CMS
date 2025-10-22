<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>Advanced CMS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Advanced CMS - Modern Content Management System" />
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?php echo $csrfToken ?? ''; ?>">
    
    <!-- App favicon -->
    <link rel="shortcut icon" href="/assets/images/favicon.ico">

    <!-- Bootstrap Css -->
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="/assets/css/app.min.css" rel="stylesheet" type="text/css" />

</head>

<body class="authentication-bg">

    <div class="account-pages my-5 pt-sm-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    
                    <?php if (isset($_SESSION['flash'])): ?>
                        <?php foreach ($_SESSION['flash'] as $type => $message): ?>
                            <div class="alert alert-<?php echo $type === 'error' ? 'danger' : $type; ?> alert-dismissible fade show" role="alert">
                                <?php echo htmlspecialchars($message); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <?php unset($_SESSION['flash'][$type]); ?>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <?php include $viewPath; ?>

                    <div class="mt-5 text-center">
                        <p>© <?php echo date('Y'); ?> Advanced CMS. Crafted with <i class="mdi mdi-heart text-danger"></i></p>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT -->
    <script src="/assets/libs/jquery/jquery.min.js"></script>
    <script src="/assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/libs/metismenu/metisMenu.min.js"></script>
    <script src="/assets/libs/simplebar/simplebar.min.js"></script>
    <script src="/assets/libs/node-waves/waves.min.js"></script>

    <!-- App js -->
    <script src="/assets/js/app.js"></script>

</body>
</html>
