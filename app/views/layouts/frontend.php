<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- SEO Meta Tags -->
    <?php echo $this->seo->render(); ?>
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="/assets/images/favicon.ico">
    
    <!-- Bootstrap CSS -->
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <!-- Icons -->
    <link href="/assets/css/icons.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 100px 0;
            min-height: 60vh;
            display: flex;
            align-items: center;
        }
        .feature-box {
            padding: 30px;
            border-radius: 10px;
            background: #f8f9fa;
            margin-bottom: 30px;
            transition: transform 0.3s;
        }
        .feature-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
        }
    </style>
</head>
<body>

<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="/">
            <strong>Advanced CMS</strong>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/posts">Blog</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/products">Products</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/jobs">Careers</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link btn btn-primary text-white ms-2" href="/admin">Admin Panel</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Page Content -->
<?php include $viewPath; ?>

<!-- Footer -->
<footer class="bg-dark text-white py-5 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <h5>Advanced CMS</h5>
                <p>Modern content management system dengan fitur SEO canggih dan keamanan tingkat tinggi.</p>
            </div>
            <div class="col-md-4">
                <h5>Quick Links</h5>
                <ul class="list-unstyled">
                    <li><a href="/" class="text-white-50">Home</a></li>
                    <li><a href="/posts" class="text-white-50">Blog</a></li>
                    <li><a href="/products" class="text-white-50">Products</a></li>
                    <li><a href="/admin" class="text-white-50">Admin</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h5>Connect</h5>
                <div class="social-links">
                    <a href="#" class="text-white-50 me-3"><i class="ri-facebook-fill"></i></a>
                    <a href="#" class="text-white-50 me-3"><i class="ri-twitter-fill"></i></a>
                    <a href="#" class="text-white-50 me-3"><i class="ri-instagram-fill"></i></a>
                    <a href="#" class="text-white-50"><i class="ri-linkedin-fill"></i></a>
                </div>
            </div>
        </div>
        <hr class="bg-white-50 my-4">
        <div class="text-center">
            <p class="mb-0">© <?php echo date('Y'); ?> Advanced CMS. All rights reserved.</p>
        </div>
    </div>
</footer>

<!-- Scripts -->
<script src="/assets/libs/jquery/jquery.min.js"></script>
<script src="/assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>

</body>
</html>
