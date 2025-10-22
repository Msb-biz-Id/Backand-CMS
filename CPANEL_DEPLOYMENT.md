# Deploy Advanced CMS ke cPanel (Production)

## Prerequisites

- Akses cPanel hosting
- PHP 7.4+ support
- MySQL database
- Minimal 500MB disk space
- SSL Certificate (recommended)

## Step-by-Step Deployment

### 1. Persiapan File

**Di Komputer Local (setelah testing di Laragon):**

```bash
# 1. Pastikan aplikasi berjalan normal di Laragon
# 2. Update .env ke production mode

# 3. Buat zip file (EXCLUDE beberapa folder)
```

**Folder yang TIDAK perlu di-upload:**
- `Qovex/` (template sudah dicopy ke public/assets/)
- `storage/cache/*` (akan regenerate)
- `storage/logs/*` (akan regenerate)
- `.git/` (jika ada)
- `node_modules/` (jika ada)

**Cara membuat ZIP:**
```bash
# Opsi A: Via Windows Explorer
1. Masuk ke folder advancedcms
2. Select semua folder KECUALI yang disebutkan di atas
3. Klik kanan > Send to > Compressed (zipped) folder
4. Rename: advancedcms.zip

# Opsi B: Via 7-Zip
7z a -tzip advancedcms.zip * -xr!Qovex -xr!storage/cache/* -xr!storage/logs/* -xr!.git
```

### 2. Upload ke cPanel

**Via File Manager (Recommended for first time):**

1. Login ke cPanel
2. File Manager
3. Navigate ke `public_html/` atau `domains/your-domain.com/public_html/`
4. Upload `advancedcms.zip`
5. Extract: Klik kanan file > Extract
6. Files akan terextract ke dalam folder

**Via FTP (Alternative):**

1. Download FileZilla Client
2. Connect ke FTP server:
   - Host: ftp.your-domain.com
   - Username: your_cpanel_username
   - Password: your_cpanel_password
   - Port: 21
3. Upload seluruh folder `advancedcms/`

### 3. Setup Database di cPanel

1. **Buat Database**
   - cPanel > MySQL Databases
   - Create Database: `cpanel_cms_db`
   - Klik "Create Database"

2. **Buat Database User**
   - MySQL Users section
   - Username: `cpanel_cms_user`
   - Password: (generate strong password)
   - Klik "Create User"

3. **Assign User ke Database**
   - Add User to Database section
   - User: cpanel_cms_user
   - Database: cpanel_cms_db
   - Privileges: ALL PRIVILEGES
   - Klik "Make Changes"

**Catat informasi ini:**
```
Database Name: cpanel_cms_db
Database User: cpanel_cms_user
Database Password: xxxxxx
Database Host: localhost (biasanya)
```

### 4. Konfigurasi Environment

1. **Edit .env file**

Via cPanel File Manager:
```
1. Navigate ke folder advancedcms/
2. Find file .env.example
3. Copy dan rename menjadi .env
4. Edit file .env
```

```ini
# Production Configuration
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database (dari step 3)
DB_HOST=localhost
DB_PORT=3306
DB_NAME=cpanel_cms_db
DB_USER=cpanel_cms_user
DB_PASS=your_database_password

# Security - Generate random key
ENCRYPTION_KEY=your_32_character_random_key_here

# Email (Optional - configure later)
MAIL_HOST=mail.your-domain.com
MAIL_PORT=587
MAIL_USERNAME=noreply@your-domain.com
MAIL_PASSWORD=your_email_password
MAIL_FROM_ADDRESS=noreply@your-domain.com

# Cloudflare (Optional)
CLOUDFLARE_TURNSTILE_SITE_KEY=
CLOUDFLARE_TURNSTILE_SECRET_KEY=
```

### 5. Set Permissions

Via cPanel File Manager:

```
1. Select folder: storage/
2. Permissions > Change Permissions (chmod)
3. Set to: 755 (atau 775 jika 755 tidak work)
4. Check "Recurse into subdirectories"
5. Klik "Change Permissions"

Ulangi untuk:
- storage/cache/
- storage/logs/
- storage/sessions/
- storage/backups/
- public/uploads/
```

**Via SSH (jika tersedia):**
```bash
cd /home/username/public_html/advancedcms
chmod -R 755 storage/
chmod -R 755 public/uploads/
```

### 6. Install Database

**Opsi A: Via Terminal/SSH (Recommended)**

```bash
# SSH ke server
ssh username@your-domain.com

# Navigate ke folder
cd public_html/advancedcms

# Run installer
php database/install.php
```

**Opsi B: Via phpMyAdmin**

1. cPanel > phpMyAdmin
2. Select database `cpanel_cms_db`
3. Import tab
4. Upload setiap file dari `database/migrations/` secara berurutan:
   - 001_create_users_table.sql
   - 002_create_posts_table.sql
   - ... (semua file)
5. Execute

### 7. Setup Document Root

**PENTING:** Website harus mengarah ke folder `public/`

**Opsi A: Subdomain Deployment**

Jika deploy di subdomain (misal: cms.your-domain.com):

1. cPanel > Domains > Create a New Domain
2. Domain: cms.your-domain.com
3. Document Root: `/home/username/public_html/advancedcms/public`
4. Create Domain

**Opsi B: Main Domain**

Jika deploy di domain utama:

1. **Cara 1: Symlink (Recommended)**
```bash
# Via SSH
cd /home/username/public_html
rm -rf * # HATI-HATI! Backup dulu jika ada file penting
ln -s /home/username/advancedcms/public/* .
```

2. **Cara 2: .htaccess Redirect**
```apache
# public_html/.htaccess
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ advancedcms/public/$1 [L]
</IfModule>
```

3. **Cara 3: Pindahkan Semua File**
```bash
# Pindahkan isi folder advancedcms ke public_html/
mv advancedcms/* ./
mv advancedcms/.* ./ 2>/dev/null
rmdir advancedcms/

# Update paths di config.php jika perlu
```

### 8. Setup SSL (HTTPS)

1. **Free SSL via cPanel**
   - cPanel > SSL/TLS Status
   - Select domain
   - Run AutoSSL
   - Wait for installation

2. **Let's Encrypt (Alternative)**
   - cPanel > SSL/TLS
   - Install SSL Certificate
   - Choose Let's Encrypt
   - Install

3. **Force HTTPS**

Edit `public/.htaccess`, uncomment line ini:
```apache
# Force HTTPS
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

### 9. Test Installation

1. **Akses Website**
   - https://your-domain.com/admin
   - Harus redirect ke login page

2. **Test Login**
   - Email: admin@example.com
   - Password: admin123

3. **Test Dashboard**
   - Setelah login, harus masuk ke dashboard
   - Check semua menu

4. **Change Admin Password**
   - Profile > Change Password
   - Set password baru yang kuat

### 10. Post-Deployment Configuration

1. **Update Site Settings**
   - Admin > Settings > General
   - Update Site Name, Description
   - Upload Logo & Favicon

2. **Configure Email**
   - Admin > Settings > Email
   - Test email sending

3. **Setup Backup Schedule**
   - cPanel > Cron Jobs
   - Add daily backup job:
   ```bash
   0 2 * * * cd /home/username/public_html/advancedcms && php artisan backup:run
   ```

4. **Configure Cloudflare (Optional)**
   - Add site to Cloudflare
   - Update nameservers
   - Configure firewall rules

## Troubleshooting

### Error 500 - Internal Server Error

**Solusi:**

1. Check error logs:
```bash
# Via cPanel
Error Log viewer: cPanel > Metrics > Errors

# Via File Manager
public_html/advancedcms/storage/logs/error.log
```

2. Common fixes:
```bash
# Check permissions
chmod -R 755 storage/
chmod -R 755 public/uploads/

# Check .htaccess
# Pastikan file .htaccess ada di public/

# Check PHP version
# cPanel > Select PHP Version > 7.4 atau higher
```

### Database Connection Error

**Solusi:**

1. Verify credentials di `.env`
2. Test connection via phpMyAdmin
3. Check if database user has privileges
4. Try `127.0.0.1` instead of `localhost` untuk DB_HOST

### White Screen / Blank Page

**Solusi:**

1. Enable error display sementara:
```php
// public/index.php - tambahkan di line paling atas
ini_set('display_errors', 1);
error_reporting(E_ALL);
```

2. Check PHP error log:
```bash
tail -f /home/username/public_html/advancedcms/storage/logs/php_errors.log
```

### Assets (CSS/JS) Not Loading

**Solusi:**

1. Check path di browser console (F12)
2. Verify file exists: public/assets/css/bootstrap.min.css
3. Check permissions: 644 untuk files, 755 untuk folders
4. Clear browser cache

### Permission Denied Errors

**Solusi:**

```bash
# Via SSH
cd /home/username/public_html/advancedcms
find . -type f -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;
chmod -R 775 storage/
chmod -R 775 public/uploads/
```

### 404 Errors on Admin Pages

**Solusi:**

1. Check if .htaccess exists di public/
2. Verify mod_rewrite enabled:
   - Ask hosting support
   - Or check via php.ini

3. Test .htaccess:
```apache
# public/.htaccess
# Add this at the top to test
# If you see "IT WORKS", mod_rewrite is ON
# RewriteEngine On
# RewriteRule ^test$ - [R=200,L]
```

## Performance Optimization

### 1. Enable OPcache

cPanel > Select PHP Version > Extensions:
```
✓ opcache
```

### 2. PHP Configuration

cPanel > MultiPHP INI Editor:
```ini
memory_limit = 256M
upload_max_filesize = 20M
post_max_size = 20M
max_execution_time = 300
max_input_time = 300
```

### 3. Enable Caching

```php
// config/config.php
'cache' => [
    'enabled' => true,
    'ttl' => 3600,
]
```

### 4. Setup Cloudflare

1. Add site to Cloudflare
2. Configure caching rules
3. Enable minification (HTML, CSS, JS)
4. Setup firewall rules

## Security Checklist

- [ ] SSL/HTTPS enabled
- [ ] Admin password changed
- [ ] .env file protected (not accessible via browser)
- [ ] Debug mode OFF (`APP_DEBUG=false`)
- [ ] File permissions correct (755/644)
- [ ] Database user has limited privileges
- [ ] Backup configured
- [ ] Cloudflare configured (optional)
- [ ] Regular updates scheduled

## Backup Strategy

### Manual Backup

1. **Database Backup**
```bash
# Via SSH
mysqldump -u cpanel_cms_user -p cpanel_cms_db > backup.sql

# Via phpMyAdmin
# Export > Go
```

2. **Files Backup**
```bash
# Via SSH
cd /home/username
tar -czf advancedcms_backup_$(date +%Y%m%d).tar.gz public_html/advancedcms/

# Via cPanel File Manager
# Select folder > Compress > Download
```

### Automated Backup

**Via cPanel Cron Jobs:**

```bash
# Daily database backup at 2 AM
0 2 * * * mysqldump -u cpanel_cms_user -pYOURPASS cpanel_cms_db | gzip > /home/username/backups/cms_$(date +\%Y\%m\%d).sql.gz

# Weekly files backup every Sunday at 3 AM
0 3 * * 0 cd /home/username && tar -czf backups/files_$(date +\%Y\%m\%d).tar.gz public_html/advancedcms/
```

## Monitoring

### Setup Uptime Monitoring

1. UptimeRobot.com (Free)
2. Pingdom.com
3. StatusCake.com

Monitor:
- Main site: https://your-domain.com
- Admin: https://your-domain.com/admin

### Log Monitoring

Check regularly:
- Error logs: `storage/logs/error.log`
- Access logs: Via cPanel > Raw Access
- Activity logs: Via Admin Panel

## Maintenance

### Weekly Tasks

- [ ] Check error logs
- [ ] Verify backups
- [ ] Check disk space
- [ ] Review security logs

### Monthly Tasks

- [ ] Update PHP version (if available)
- [ ] Review and clean old backups
- [ ] Check performance metrics
- [ ] Test backup restoration

## Migration from Laragon

**Checklist:**

1. ✅ Export database dari Laragon
2. ✅ Upload files via FTP/File Manager
3. ✅ Import database ke cPanel
4. ✅ Update .env dengan production settings
5. ✅ Set correct permissions
6. ✅ Point document root to public/
7. ✅ Enable SSL
8. ✅ Test all functionality
9. ✅ Change admin password
10. ✅ Configure backups

## Support

Jika ada masalah:

1. Check documentation
2. Review error logs
3. Contact hosting support
4. Check cPanel knowledge base

## Success! 🎉

Website Anda sekarang LIVE di production!

**Next Steps:**
1. Configure Google Analytics
2. Setup sitemap submission
3. Configure email notifications
4. Create content
5. Promote website

---

**Production URL**: https://your-domain.com  
**Admin Panel**: https://your-domain.com/admin
