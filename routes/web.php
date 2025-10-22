<?php

/**
 * Web Routes
 * Best Practice: Organized routing with middleware
 */

// Register middleware
Router::middleware('auth', 'AuthMiddleware');
Router::middleware('admin', 'AdminMiddleware');
Router::middleware('csrf', 'CSRFMiddleware');

// Frontend Routes
Router::get('/', 'HomeController@index');
Router::get('/about', 'PageController@show');
Router::get('/contact', 'PageController@contact');
Router::post('/contact', 'PageController@sendContact', ['csrf']);

// Blog/Posts
Router::get('/posts', 'PostController@index');
Router::get('/post/{slug}', 'PostController@show');
Router::get('/category/{slug}', 'PostController@category');
Router::get('/tag/{slug}', 'PostController@tag');

// Comments
Router::post('/comments/submit', 'CommentController@submit', ['csrf']);

// Products (E-Commerce)
Router::get('/products', 'ProductController@index');
Router::get('/product/{slug}', 'ProductController@show');
Router::get('/products/category/{slug}', 'ProductController@category');
Router::get('/products/brand/{slug}', 'ProductController@brand');

// Shopping Cart
Router::get('/cart', 'CartController@index');
Router::post('/api/cart/add', 'CartController@add');
Router::post('/api/cart/update/{id}', 'CartController@update');
Router::post('/api/cart/remove/{id}', 'CartController@remove');
Router::get('/api/cart/count', 'CartController@count');

// Checkout
Router::get('/checkout', 'CheckoutController@index', ['csrf']);
Router::post('/checkout/process', 'CheckoutController@process', ['csrf']);
Router::get('/checkout/success/{orderId}', 'CheckoutController@success');

// Jobs/Loker
Router::get('/jobs', 'JobController@index');
Router::get('/job/{slug}', 'JobController@show');
Router::post('/job/{id}/apply', 'JobController@apply', ['csrf']);

// Pages
Router::get('/page/{slug}', 'PageController@show');

// Search
Router::get('/search', 'SearchController@index');

// Sitemap & SEO
Router::get('/sitemap.xml', 'SitemapController@index');
Router::get('/robots.txt', 'RobotsController@index');

// API Routes
Router::get('/api/posts', 'API\PostController@index');
Router::get('/api/posts/{id}', 'API\PostController@show');
Router::get('/api/products', 'API\ProductController@index');
Router::get('/api/products/{id}', 'API\ProductController@show');

// Admin Routes
$adminPath = $GLOBALS['config']['app']['admin_path'] ?? 'admin';

// Admin - Authentication
Router::get("/{$adminPath}/login", 'Admin\AuthController@loginForm');
Router::post("/{$adminPath}/login", 'Admin\AuthController@login', ['csrf']);
Router::get("/{$adminPath}/logout", 'Admin\AuthController@logout', ['auth']);
Router::get("/{$adminPath}/register", 'Admin\AuthController@registerForm');
Router::post("/{$adminPath}/register", 'Admin\AuthController@register', ['csrf']);
Router::get("/{$adminPath}/forgot-password", 'Admin\AuthController@forgotPassword');
Router::post("/{$adminPath}/forgot-password", 'Admin\AuthController@sendResetLink', ['csrf']);

// Admin - Dashboard
Router::get("/{$adminPath}", 'Admin\DashboardController@index', ['auth', 'admin']);
Router::get("/{$adminPath}/dashboard", 'Admin\DashboardController@index', ['auth', 'admin']);

// Admin - Posts
Router::get("/{$adminPath}/posts", 'Admin\PostController@index', ['auth']);
Router::get("/{$adminPath}/posts/create", 'Admin\PostController@create', ['auth']);
Router::post("/{$adminPath}/posts", 'Admin\PostController@store', ['auth', 'csrf']);
Router::get("/{$adminPath}/posts/{id}/edit", 'Admin\PostController@edit', ['auth']);
Router::post("/{$adminPath}/posts/{id}", 'Admin\PostController@update', ['auth', 'csrf']);
Router::post("/{$adminPath}/posts/{id}/delete", 'Admin\PostController@delete', ['auth', 'csrf']);

// Admin - Pages
Router::get("/{$adminPath}/pages", 'Admin\PageController@index', ['auth']);
Router::get("/{$adminPath}/pages/create", 'Admin\PageController@create', ['auth']);
Router::post("/{$adminPath}/pages", 'Admin\PageController@store', ['auth', 'csrf']);
Router::get("/{$adminPath}/pages/{id}/edit", 'Admin\PageController@edit', ['auth']);
Router::post("/{$adminPath}/pages/{id}", 'Admin\PageController@update', ['auth', 'csrf']);
Router::post("/{$adminPath}/pages/{id}/delete", 'Admin\PageController@delete', ['auth', 'csrf']);

// Admin - Products
Router::get("/{$adminPath}/products", 'Admin\ProductController@index', ['auth']);
Router::get("/{$adminPath}/products/create", 'Admin\ProductController@create', ['auth']);
Router::post("/{$adminPath}/products", 'Admin\ProductController@store', ['auth', 'csrf']);
Router::get("/{$adminPath}/products/{id}/edit", 'Admin\ProductController@edit', ['auth']);
Router::post("/{$adminPath}/products/{id}", 'Admin\ProductController@update', ['auth', 'csrf']);
Router::post("/{$adminPath}/products/{id}/delete", 'Admin\ProductController@delete', ['auth', 'csrf']);

// Admin - Jobs/Loker
Router::get("/{$adminPath}/jobs", 'Admin\JobController@index', ['auth']);
Router::get("/{$adminPath}/jobs/create", 'Admin\JobController@create', ['auth']);
Router::post("/{$adminPath}/jobs", 'Admin\JobController@store', ['auth', 'csrf']);
Router::get("/{$adminPath}/jobs/{id}/edit", 'Admin\JobController@edit', ['auth']);
Router::post("/{$adminPath}/jobs/{id}", 'Admin\JobController@update', ['auth', 'csrf']);
Router::post("/{$adminPath}/jobs/{id}/delete", 'Admin\JobController@delete', ['auth', 'csrf']);

// Admin - Media
Router::get("/{$adminPath}/media", 'Admin\MediaController@index', ['auth']);
Router::post("/{$adminPath}/media/upload", 'Admin\MediaController@upload', ['auth', 'csrf']);
Router::post("/{$adminPath}/media/{id}/delete", 'Admin\MediaController@delete', ['auth', 'csrf']);

// Admin - Categories
Router::get("/{$adminPath}/categories", 'Admin\CategoryController@index', ['auth']);
Router::post("/{$adminPath}/categories", 'Admin\CategoryController@store', ['auth', 'csrf']);
Router::post("/{$adminPath}/categories/{id}", 'Admin\CategoryController@update', ['auth', 'csrf']);
Router::post("/{$adminPath}/categories/{id}/delete", 'Admin\CategoryController@delete', ['auth', 'csrf']);

// Admin - Tags
Router::get("/{$adminPath}/tags", 'Admin\TagController@index', ['auth']);
Router::post("/{$adminPath}/tags", 'Admin\TagController@store', ['auth', 'csrf']);
Router::post("/{$adminPath}/tags/{id}", 'Admin\TagController@update', ['auth', 'csrf']);
Router::post("/{$adminPath}/tags/{id}/delete", 'Admin\TagController@delete', ['auth', 'csrf']);

// Admin - Ads Management
Router::get("/{$adminPath}/ads", 'Admin\AdController@index', ['auth', 'admin']);
Router::get("/{$adminPath}/ads/create", 'Admin\AdController@create', ['auth', 'admin']);
Router::post("/{$adminPath}/ads", 'Admin\AdController@store', ['auth', 'admin', 'csrf']);
Router::get("/{$adminPath}/ads/{id}/edit", 'Admin\AdController@edit', ['auth', 'admin']);
Router::post("/{$adminPath}/ads/{id}", 'Admin\AdController@update', ['auth', 'admin', 'csrf']);
Router::post("/{$adminPath}/ads/{id}/delete", 'Admin\AdController@delete', ['auth', 'admin', 'csrf']);

// Admin - Users & Roles
Router::get("/{$adminPath}/users", 'Admin\UserController@index', ['auth', 'admin']);
Router::get("/{$adminPath}/users/create", 'Admin\UserController@create', ['auth', 'admin']);
Router::post("/{$adminPath}/users", 'Admin\UserController@store', ['auth', 'admin', 'csrf']);
Router::get("/{$adminPath}/users/{id}/edit", 'Admin\UserController@edit', ['auth', 'admin']);
Router::post("/{$adminPath}/users/{id}", 'Admin\UserController@update', ['auth', 'admin', 'csrf']);
Router::post("/{$adminPath}/users/{id}/delete", 'Admin\UserController@delete', ['auth', 'admin', 'csrf']);

// Admin - Menus
Router::get("/{$adminPath}/menus", 'Admin\MenuController@index', ['auth']);
Router::get("/{$adminPath}/menus/create", 'Admin\MenuController@create', ['auth']);
Router::post("/{$adminPath}/menus", 'Admin\MenuController@store', ['auth', 'csrf']);
Router::get("/{$adminPath}/menus/{id}/builder", 'Admin\MenuController@builder', ['auth']);
Router::get("/{$adminPath}/menus/{id}/edit", 'Admin\MenuController@edit', ['auth']);
Router::post("/{$adminPath}/menus/{id}", 'Admin\MenuController@update', ['auth', 'csrf']);
Router::post("/{$adminPath}/menus/{id}/delete", 'Admin\MenuController@delete', ['auth', 'csrf']);
Router::post("/{$adminPath}/menus/{id}/add-item", 'Admin\MenuController@addItem', ['auth', 'csrf']);
Router::post("/{$adminPath}/menus/{id}/reorder", 'Admin\MenuController@reorder', ['auth', 'csrf']);
Router::post("/{$adminPath}/menus/item/{id}", 'Admin\MenuController@updateItem', ['auth', 'csrf']);
Router::post("/{$adminPath}/menus/item/{id}/delete", 'Admin\MenuController@deleteItem', ['auth', 'csrf']);

// Admin - Comments
Router::get("/{$adminPath}/comments", 'Admin\CommentController@index', ['auth']);
Router::post("/{$adminPath}/comments/{id}/approve", 'Admin\CommentController@approve', ['auth', 'csrf']);
Router::post("/{$adminPath}/comments/{id}/reject", 'Admin\CommentController@reject', ['auth', 'csrf']);
Router::post("/{$adminPath}/comments/{id}/spam", 'Admin\CommentController@spam', ['auth', 'csrf']);
Router::post("/{$adminPath}/comments/{id}/delete", 'Admin\CommentController@delete', ['auth', 'csrf']);
Router::post("/{$adminPath}/comments/bulk", 'Admin\CommentController@bulkAction', ['auth', 'csrf']);

// Admin - Profile
Router::get("/{$adminPath}/profile", 'Admin\UserController@profile', ['auth']);
Router::post("/{$adminPath}/profile", 'Admin\UserController@updateProfile', ['auth', 'csrf']);

// Admin - Settings
Router::get("/{$adminPath}/settings", 'Admin\SettingController@index', ['auth', 'admin']);
Router::post("/{$adminPath}/settings", 'Admin\SettingController@update', ['auth', 'admin', 'csrf']);

// Admin - SEO Settings
Router::get("/{$adminPath}/seo", 'Admin\SEOController@index', ['auth', 'admin']);
Router::post("/{$adminPath}/seo", 'Admin\SEOController@update', ['auth', 'admin', 'csrf']);

// Admin - Tools
Router::get("/{$adminPath}/tools/backup", 'Admin\ToolController@backup', ['auth', 'admin']);
Router::post("/{$adminPath}/tools/backup/create", 'Admin\ToolController@createBackup', ['auth', 'admin', 'csrf']);
Router::get("/{$adminPath}/tools/cache", 'Admin\ToolController@cache', ['auth', 'admin']);
Router::post("/{$adminPath}/tools/cache/clear", 'Admin\ToolController@clearCache', ['auth', 'admin', 'csrf']);

// Admin - Analytics
Router::get("/{$adminPath}/analytics", 'Admin\AnalyticsController@index', ['auth']);

// Admin - AI Generator
Router::get("/{$adminPath}/ai/generator", 'Admin\AIGeneratorController@index', ['auth']);
Router::post("/{$adminPath}/ai/generate", 'Admin\AIGeneratorController@generate', ['auth', 'csrf']);
Router::post("/{$adminPath}/ai/save", 'Admin\AIGeneratorController@saveAsPost', ['auth', 'csrf']);

// Admin - Themes
Router::get("/{$adminPath}/themes", 'Admin\ThemeController@index', ['auth', 'admin']);
Router::post("/{$adminPath}/themes/activate", 'Admin\ThemeController@activate', ['auth', 'admin', 'csrf']);
Router::post("/{$adminPath}/themes/customize", 'Admin\ThemeController@customize', ['auth', 'admin', 'csrf']);
