# Advanced CMS - Installation Guide

## System Requirements

- **Web Server**: Apache 2.4+ or Nginx 1.18+
- **PHP**: 7.4+ (8.0+ recommended)
- **Database**: MySQL 5.7+ or MariaDB 10.3+
- **PHP Extensions**:
  - PDO
  - pdo_mysql
  - mbstring
  - openssl
  - json
  - gd or imagick (for image processing)
  - fileinfo
  - zip

## Installation Steps

### 1. Download and Extract

Extract the CMS files to your web server directory:

```bash
cd /var/www/html
# Files should be in /var/www/html/workspace/
```

### 2. Configure Web Server

#### Apache Configuration

The `.htaccess` file is already included in the `public` directory. Ensure `mod_rewrite` is enabled:

```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

Set document root to `public` directory:

```apache
<VirtualHost *:80>
    ServerName your-domain.com
    DocumentRoot /var/www/html/workspace/public
    
    <Directory /var/www/html/workspace/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

#### Nginx Configuration

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/html/workspace/public;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
}
```

### 3. Set Permissions

```bash
cd /var/www/html/workspace

# Set ownership
sudo chown -R www-data:www-data .

# Set directory permissions
sudo find . -type d -exec chmod 755 {} \;

# Set file permissions
sudo find . -type f -exec chmod 644 {} \;

# Make writable directories
sudo chmod -R 775 storage/
sudo chmod -R 775 public/uploads/
```

### 4. Configure Environment

```bash
# Copy environment file
cp .env.example .env

# Edit configuration
nano .env
```

Configure your database credentials in `.env`:

```ini
APP_ENV=development
APP_DEBUG=true
APP_URL=http://your-domain.com

DB_HOST=localhost
DB_PORT=3306
DB_NAME=cms_database
DB_USER=root
DB_PASS=your_password

# Generate a random encryption key (32 characters)
ENCRYPTION_KEY=your_random_32_character_key_here
```

### 5. Create Database

Create a MySQL database:

```bash
mysql -u root -p
```

```sql
CREATE DATABASE cms_database CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

### 6. Run Database Installation

```bash
cd /var/www/html/workspace
php database/install.php
```

This will:
- Create database if not exists
- Create all required tables
- Insert default admin user and settings

### 7. Access Your CMS

- **Frontend**: http://your-domain.com
- **Admin Panel**: http://your-domain.com/admin

**Default Admin Credentials:**
- Email: `admin@example.com`
- Password: `admin123`

⚠️ **IMPORTANT**: Change the default password immediately after first login!

## Post-Installation Configuration

### 1. Update Admin Password

1. Login to admin panel
2. Go to Profile Settings
3. Change password

### 2. Configure Site Settings

Navigate to **Settings > General** and configure:
- Site Name
- Site Description
- Logo & Favicon
- Timezone
- Default Language

### 3. Configure SEO Settings

Navigate to **SEO > Settings**:
- Default Meta Title & Description
- Social Media Tags
- Google Analytics ID
- Sitemap Settings

### 4. Configure Cloudflare (Optional)

If using Cloudflare Turnstile:

1. Get your Cloudflare credentials
2. Update `.env`:
```ini
CLOUDFLARE_ZONE_ID=your_zone_id
CLOUDFLARE_API_TOKEN=your_api_token
CLOUDFLARE_TURNSTILE_SITE_KEY=your_site_key
CLOUDFLARE_TURNSTILE_SECRET_KEY=your_secret_key
```

### 5. Configure Email (Optional)

Update SMTP settings in `.env`:

```ini
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_FROM_ADDRESS=noreply@your-domain.com
MAIL_FROM_NAME="Advanced CMS"
```

## Security Checklist

- [ ] Change default admin password
- [ ] Update `.env` file with secure credentials
- [ ] Generate secure `ENCRYPTION_KEY`
- [ ] Remove or secure `install.php` after installation
- [ ] Enable HTTPS/SSL certificate
- [ ] Configure firewall rules
- [ ] Set up regular backups
- [ ] Keep PHP and dependencies updated
- [ ] Review file permissions
- [ ] Configure Content Security Policy headers

## Troubleshooting

### Permission Issues

```bash
sudo chown -R www-data:www-data storage/ public/uploads/
sudo chmod -R 775 storage/ public/uploads/
```

### Database Connection Failed

- Verify database credentials in `.env`
- Ensure MySQL service is running: `sudo systemctl status mysql`
- Check if database exists: `SHOW DATABASES;`

### 404 Errors

- Ensure `mod_rewrite` is enabled (Apache)
- Check `.htaccess` file exists in `public/` directory
- Verify document root points to `public/` directory

### PHP Extension Missing

Install required extensions:

```bash
# Ubuntu/Debian
sudo apt-get install php-pdo php-mysql php-mbstring php-json php-gd

# Restart web server
sudo systemctl restart apache2
```

### Clear Cache

```bash
# Manual cache clear
rm -rf storage/cache/*
rm -rf storage/logs/*
```

## Maintenance

### Backup Database

```bash
mysqldump -u username -p cms_database > backup.sql
```

### Update CMS

1. Backup database and files
2. Download new version
3. Replace files (keep `.env` and `uploads/`)
4. Run migrations if any
5. Clear cache

## Support

For issues and support:
- Check documentation
- Review error logs: `storage/logs/`
- Contact support team

## Features Overview

### Content Management
- Posts with scheduling & auto-archive
- Pages with hierarchy
- Products with inventory
- Jobs/Career listings
- Media library with optimization

### SEO Features (RankMath-like)
- Meta tags management
- Schema.org JSON-LD
- Sitemap generation
- SEO score calculator
- Focus keyword analysis
- Open Graph & Twitter Cards

### Security
- CSRF protection
- SQL injection prevention
- XSS protection
- Password hashing (Argon2id)
- Rate limiting
- Brute force protection
- 2FA support

### Performance
- File-based caching
- Image optimization
- Lazy loading
- GZIP compression
- Minification (HTML/CSS/JS)

### Advanced Features
- Multi-language support
- Role-based access control
- A/B testing
- Content versioning
- Analytics dashboard
- API with authentication
- Social media integration
- Ads management

## Next Steps

1. Explore the admin panel
2. Create your first post
3. Configure SEO settings
4. Upload media files
5. Customize site settings
6. Set up backup schedule

Enjoy your new Advanced CMS! 🚀
