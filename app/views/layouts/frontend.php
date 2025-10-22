<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <?php echo $seoData['meta_tags'] ?? ''; ?>
    
    <!-- Theme CSS -->
    <link rel="stylesheet" href="/themes/default/assets/css/theme.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Remix Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css">
    
    <?php if (isset($extraCss)): ?>
        <?php foreach ($extraCss as $css): ?>
            <link rel="stylesheet" href="<?php echo $css; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>

    <!-- Header -->
    <header class="site-header">
        <div class="container">
            <div class="site-logo">
                <a href="/">Advanced CMS</a>
            </div>
            
            <nav class="main-nav">
                <ul>
                    <li><a href="/">Home</a></li>
                    <li><a href="/posts">Blog</a></li>
                    <li><a href="/products">Products</a></li>
                    <li><a href="/page/about">About</a></li>
                    <li><a href="/page/contact">Contact</a></li>
                    <li>
                        <a href="/cart" class="cart-icon">
                            <i class="ri-shopping-cart-line"></i>
                            <span class="cart-badge">0</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Flash Messages -->
    <?php if (isset($_SESSION['flash'])): ?>
        <div class="container mt-3">
            <?php foreach ($_SESSION['flash'] as $type => $message): ?>
                <div class="alert alert-<?php echo $type; ?>" role="alert">
                    <?php echo $message; ?>
                </div>
                <?php unset($_SESSION['flash'][$type]); ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Main Content -->
    <main class="site-main">
        <?php 
        $viewPath = __DIR__ . '/../' . $view . '.php';
        if (file_exists($viewPath)) {
            include $viewPath;
        }
        ?>
    </main>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <div class="footer-widget">
                        <h4>About Us</h4>
                        <p>Advanced CMS - Modern content management system with powerful features.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="footer-widget">
                        <h4>Quick Links</h4>
                        <ul>
                            <li><a href="/">Home</a></li>
                            <li><a href="/posts">Blog</a></li>
                            <li><a href="/products">Products</a></li>
                            <li><a href="/page/about">About</a></li>
                            <li><a href="/page/contact">Contact</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="footer-widget">
                        <h4>Contact Info</h4>
                        <ul>
                            <li><i class="ri-mail-line"></i> info@example.com</li>
                            <li><i class="ri-phone-line"></i> +62 812-3456-7890</li>
                            <li><i class="ri-map-pin-line"></i> Jakarta, Indonesia</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> Advanced CMS. All rights reserved. Built with ❤️ + 🤖 AI</p>
            </div>
        </div>
    </footer>

    <!-- Theme JS -->
    <script src="/themes/default/assets/js/theme.js"></script>
    
    <?php if (isset($extraJs)): ?>
        <?php foreach ($extraJs as $js): ?>
            <script src="<?php echo $js; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <?php echo $seoData['schema_markup'] ?? ''; ?>
</body>
</html>
