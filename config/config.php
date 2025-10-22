<?php
/**
 * Configuration File - CMS Application
 * Best Practice: Centralized configuration management
 */

return [
    // Application Settings
    'app' => [
        'name' => 'Advanced CMS',
        'version' => '1.0.0',
        'env' => getenv('APP_ENV') ?: 'production', // development, staging, production
        'debug' => getenv('APP_DEBUG') === 'true' ? true : false,
        'timezone' => 'Asia/Jakarta',
        'locale' => 'id_ID',
        'url' => getenv('APP_URL') ?: 'http://localhost',
        'admin_path' => 'admin', // Backend admin path
    ],

    // Database Configuration
    'database' => [
        'default' => 'mysql',
        'connections' => [
            'mysql' => [
                'driver' => 'mysql',
                'host' => getenv('DB_HOST') ?: 'localhost',
                'port' => getenv('DB_PORT') ?: '3306',
                'database' => getenv('DB_NAME') ?: 'cms_database',
                'username' => getenv('DB_USER') ?: 'root',
                'password' => getenv('DB_PASS') ?: '',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => 'cms_',
                'strict' => true,
            ]
        ],
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false, // Security: Real prepared statements
            PDO::ATTR_PERSISTENT => false, // Connection pooling
        ]
    ],

    // Security Settings
    'security' => [
        'csrf_token_name' => 'csrf_token',
        'csrf_token_expire' => 3600, // 1 hour
        'password_hash_algo' => PASSWORD_DEFAULT, // Compatible dengan PHP 7.4+ (bcrypt/argon2)
        'password_min_length' => 8,
        'max_login_attempts' => 5,
        'login_lockout_time' => 900, // 15 minutes
        'session_lifetime' => 7200, // 2 hours
        'session_regenerate_interval' => 300, // 5 minutes
        'enable_2fa' => true,
        'allowed_file_types' => [
            'image' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'],
            'document' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'],
            'video' => ['mp4', 'webm', 'ogg', 'avi', 'mov'],
            'audio' => ['mp3', 'wav', 'ogg'],
        ],
        'max_upload_size' => 10485760, // 10MB in bytes
        'encryption_key' => getenv('ENCRYPTION_KEY') ?: bin2hex(random_bytes(32)),
    ],

    // Session Configuration
    'session' => [
        'driver' => 'file', // file, database, redis
        'lifetime' => 7200, // 2 hours
        'expire_on_close' => false,
        'encrypt' => true,
        'cookie_name' => 'cms_session',
        'cookie_path' => '/',
        'cookie_domain' => null,
        'cookie_secure' => getenv('APP_ENV') === 'production', // HTTPS only in production
        'cookie_httponly' => true, // Security: Prevent XSS
        'cookie_samesite' => 'Lax', // Security: CSRF protection
    ],

    // Cache Configuration
    'cache' => [
        'driver' => 'file', // file, redis, memcached
        'path' => __DIR__ . '/../storage/cache',
        'prefix' => 'cms_cache_',
        'ttl' => 3600, // 1 hour default
        'enabled' => true,
    ],

    // SEO Configuration
    'seo' => [
        'meta' => [
            'default_title' => 'Advanced CMS - Modern Content Management',
            'title_separator' => ' | ',
            'default_description' => 'Modern CMS with advanced SEO features',
            'default_keywords' => 'cms, seo, content management',
            'robots' => 'index, follow',
            'og_type' => 'website',
        ],
        'sitemap' => [
            'enabled' => true,
            'cache_duration' => 86400, // 24 hours
            'priority' => [
                'home' => 1.0,
                'posts' => 0.8,
                'pages' => 0.9,
                'products' => 0.7,
                'jobs' => 0.6,
            ],
            'changefreq' => [
                'home' => 'daily',
                'posts' => 'weekly',
                'pages' => 'monthly',
                'products' => 'weekly',
                'jobs' => 'weekly',
            ],
        ],
        'schema' => [
            'organization' => [
                '@type' => 'Organization',
                'name' => 'Advanced CMS',
                'logo' => '/assets/images/logo.png',
            ],
        ],
        'canonical_url' => true,
        'auto_meta_description' => true,
        'meta_description_length' => 160,
    ],

    // Email Configuration
    'mail' => [
        'driver' => 'smtp', // smtp, sendmail, mail
        'host' => getenv('MAIL_HOST') ?: 'localhost',
        'port' => getenv('MAIL_PORT') ?: 587,
        'username' => getenv('MAIL_USERNAME') ?: '',
        'password' => getenv('MAIL_PASSWORD') ?: '',
        'encryption' => 'tls', // tls, ssl
        'from' => [
            'address' => getenv('MAIL_FROM_ADDRESS') ?: 'noreply@example.com',
            'name' => getenv('MAIL_FROM_NAME') ?: 'Advanced CMS',
        ],
    ],

    // Cloudflare Configuration
    'cloudflare' => [
        'enabled' => true,
        'zone_id' => getenv('CLOUDFLARE_ZONE_ID') ?: '',
        'api_token' => getenv('CLOUDFLARE_API_TOKEN') ?: '',
        'turnstile' => [
            'site_key' => getenv('CLOUDFLARE_TURNSTILE_SITE_KEY') ?: '',
            'secret_key' => getenv('CLOUDFLARE_TURNSTILE_SECRET_KEY') ?: '',
            'enabled' => true,
        ],
    ],

    // API Configuration
    'api' => [
        'enabled' => true,
        'rate_limit' => [
            'enabled' => true,
            'max_requests' => 100,
            'window' => 60, // seconds
        ],
        'cors' => [
            'enabled' => true,
            'allowed_origins' => ['*'],
            'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
            'allowed_headers' => ['Content-Type', 'Authorization', 'X-Requested-With'],
        ],
        'authentication' => [
            'type' => 'bearer', // bearer, api_key
            'token_expire' => 3600, // 1 hour
        ],
    ],

    // Multi-language Configuration
    'localization' => [
        'default' => 'id',
        'fallback' => 'en',
        'available' => ['id', 'en', 'es', 'fr', 'de', 'ja', 'zh'],
        'auto_detect' => true,
    ],

    // Media Configuration
    'media' => [
        'upload_path' => __DIR__ . '/../public/uploads',
        'allowed_types' => [
            'image/jpeg', 'image/jpg', 'image/png', 'image/gif', 
            'image/webp', 'image/svg+xml',
            'application/pdf',
            'video/mp4', 'video/webm',
        ],
        'max_size' => 10485760, // 10MB
        'image_sizes' => [
            'thumbnail' => ['width' => 150, 'height' => 150],
            'medium' => ['width' => 300, 'height' => 300],
            'large' => ['width' => 1024, 'height' => 768],
        ],
        'optimize' => true,
        'lazy_load' => true,
    ],

    // Shipping Configuration (Raja Ongkir)
    'shipping' => [
        'rajaongkir_api_key' => getenv('RAJAONGKIR_API_KEY') ?: '',
        'default_origin_city' => '153', // Jakarta
        'available_couriers' => ['jne', 'pos', 'tiki'],
    ],

    // AI Configuration (Google Gemini)
    'ai' => [
        'gemini_api_key' => getenv('GEMINI_API_KEY') ?: '',
        'auto_generate_enabled' => false,
        'default_style' => 'professional',
        'default_length' => 'medium',
    ],

    // E-Commerce Configuration
    'ecommerce' => [
        'whatsapp_number' => getenv('WHATSAPP_NUMBER') ?: '',
        'currency_symbol' => 'Rp',
        'currency_code' => 'IDR',
        'enable_cod' => true,
        'minimum_order' => 50000,
        'tax_rate' => 0, // 0 = no tax
    ],

    // Performance Configuration
    'performance' => [
        'minify_html' => true,
        'minify_css' => true,
        'minify_js' => true,
        'gzip_compression' => true,
        'lazy_loading' => true,
        'cdn_enabled' => false,
        'cdn_url' => '',
    ],

    // Logging Configuration
    'logging' => [
        'enabled' => true,
        'level' => 'debug', // debug, info, warning, error, critical
        'path' => __DIR__ . '/../storage/logs',
        'max_files' => 30,
        'log_queries' => getenv('APP_ENV') === 'development',
    ],

    // Backup Configuration
    'backup' => [
        'enabled' => true,
        'path' => __DIR__ . '/../storage/backups',
        'schedule' => 'daily', // hourly, daily, weekly, monthly
        'keep_backups' => 7, // Keep last 7 backups
        'include_database' => true,
        'include_uploads' => true,
    ],
];
