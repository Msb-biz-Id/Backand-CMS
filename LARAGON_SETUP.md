# Setup Advanced CMS di Laragon (Windows)

## Prerequisites

- Laragon Full versi terbaru (sudah include Apache, PHP, MySQL)
- Download dari: https://laragon.org/download/

## Step-by-Step Installation

### 1. Install Laragon

1. Download Laragon Full
2. Install Laragon di `C:\laragon` (default)
3. Jalankan Laragon sebagai Administrator
4. Start All Services (Apache + MySQL)

### 2. Extract CMS ke Laragon

```bash
# Copy folder workspace ke:
C:\laragon\www\advancedcms\
```

Struktur folder:
```
C:\laragon\www\advancedcms\
├── app/
├── config/
├── database/
├── public/
├── routes/
└── storage/
```

### 3. Setup Virtual Host

**Opsi A: Menggunakan Menu Laragon (Recommended)**

1. Klik kanan Laragon tray icon
2. **Apache > sites** > **Add Virtual Host**
3. Nama: `advancedcms.test`
4. Document Root: `C:\laragon\www\advancedcms\public`
5. Laragon akan otomatis:
   - Buat virtual host
   - Update hosts file
   - Restart Apache

**Opsi B: Manual Configuration**

1. Edit hosts file:
```bash
# C:\Windows\System32\drivers\etc\hosts
127.0.0.1  advancedcms.test
```

2. Edit Apache vhost:
```bash
# C:\laragon\etc\apache2\sites-enabled\advancedcms.test.conf
```

```apache
<VirtualHost *:80>
    DocumentRoot "C:/laragon/www/advancedcms/public"
    ServerName advancedcms.test
    ServerAlias *.advancedcms.test
    
    <Directory "C:/laragon/www/advancedcms/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

3. Restart Apache via Laragon

### 4. Setup Database

1. **Buka HeidiSQL** (included dengan Laragon)
   - Host: 127.0.0.1
   - User: root
   - Password: (kosongkan)
   - Port: 3306

2. **Buat Database**
```sql
CREATE DATABASE cms_database CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 5. Konfigurasi Environment

1. Copy file environment:
```bash
cd C:\laragon\www\advancedcms
copy .env.example .env
```

2. Edit `.env`:
```ini
APP_ENV=development
APP_DEBUG=true
APP_URL=http://advancedcms.test

DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=cms_database
DB_USER=root
DB_PASS=

# Generate random 32 character key
ENCRYPTION_KEY=your_random_32_character_key_here
```

### 6. Set Permissions (Penting!)

```bash
# Buka Command Prompt sebagai Administrator
cd C:\laragon\www\advancedcms

# Beri permission untuk storage & uploads
icacls storage /grant Users:F /T
icacls public\uploads /grant Users:F /T
```

### 7. Install Database

**Menggunakan Command Line:**

```bash
# Buka Laragon Terminal (Menu > Terminal)
cd C:\laragon\www\advancedcms
php database\install.php
```

**Atau menggunakan HeidiSQL:**

1. Buka HeidiSQL
2. Pilih database `cms_database`
3. Tools > Load SQL file
4. Pilih setiap file di `database/migrations/` secara berurutan
5. Execute

### 8. Akses CMS

1. **Frontend**: http://advancedcms.test
2. **Admin Panel**: http://advancedcms.test/admin

**Login Credentials:**
- Email: `admin@example.com`
- Password: `admin123`

⚠️ **PENTING**: Ganti password setelah login pertama kali!

## Testing

### Test 1: Apache & PHP
```bash
# Buat file test.php di public/
<?php phpinfo(); ?>
```
Akses: http://advancedcms.test/test.php

### Test 2: Database Connection
```bash
php -r "new PDO('mysql:host=127.0.0.1', 'root', '');"
```

### Test 3: Rewrite Rules
Akses: http://advancedcms.test/admin
Harus redirect ke login page.

## Troubleshooting

### Error: "Page Not Found"

**Solusi:**
1. Pastikan DocumentRoot mengarah ke folder `public/`
2. Pastikan file `.htaccess` ada di folder `public/`
3. Pastikan `mod_rewrite` enabled

```bash
# Check mod_rewrite di Laragon
# Buka: C:\laragon\bin\apache\httpd.conf
# Pastikan line ini tidak di-comment:
LoadModule rewrite_module modules/mod_rewrite.so
```

### Error: "Database Connection Failed"

**Solusi:**
1. Check MySQL service berjalan (Laragon > MySQL > Start)
2. Test connection via HeidiSQL
3. Verifikasi credentials di `.env`

### Error: "Permission Denied"

**Solusi:**
```bash
# Reset permissions
icacls storage /reset /T
icacls storage /grant Users:F /T
icacls public\uploads /reset /T
icacls public\uploads /grant Users:F /T
```

### Error: "Call to undefined function..."

**Solusi:**
1. Check PHP version (minimal 7.4)
2. Enable required extensions di `php.ini`:

```ini
extension=pdo_mysql
extension=mbstring
extension=openssl
extension=fileinfo
extension=gd2
```

Restart Apache setelah edit `php.ini`

### Assets (CSS/JS) Tidak Load

**Solusi:**
1. Check path di browser console
2. Pastikan folder `public/assets/` ada
3. Clear browser cache (Ctrl + Shift + Del)

## Development Tips

### 1. Enable Error Display

Edit `php.ini`:
```ini
display_errors = On
error_reporting = E_ALL
```

### 2. Increase Upload Limit

Edit `php.ini`:
```ini
upload_max_filesize = 20M
post_max_size = 20M
max_execution_time = 300
```

### 3. Enable Query Log

Edit `config/config.php`:
```php
'logging' => [
    'log_queries' => true,
]
```

View logs di: `storage/logs/`

### 4. Clear Cache

```bash
# Via browser
http://advancedcms.test/admin/tools/cache

# Via command line
cd C:\laragon\www\advancedcms
rd /s /q storage\cache
```

## Quick Commands

```bash
# Start Laragon Services
# Via Laragon GUI: Start All

# Stop Services
# Via Laragon GUI: Stop All

# Restart Apache
# Via Laragon GUI: Apache > Reload

# Open Terminal
# Via Laragon GUI: Menu > Terminal

# Access MySQL
# Via Laragon GUI: Menu > MySQL > mysql
```

## Directory Structure di Laragon

```
C:\laragon\
├── www\
│   └── advancedcms\      # Your CMS
├── bin\
│   ├── apache\           # Apache config
│   ├── php\              # PHP versions
│   └── mysql\            # MySQL data
├── etc\
│   ├── apache2\
│   │   └── sites-enabled\  # Virtual hosts
│   └── php\              # php.ini files
└── data\
    └── mysql\            # Database files
```

## Backup & Restore

### Backup

```bash
# Database backup
cd C:\laragon\www\advancedcms
php database\backup.php

# Manual backup via HeidiSQL
# Select database > Tools > Export database as SQL
```

### Restore

```bash
# Via HeidiSQL
# Tools > Load SQL file > Select backup file
```

## Performance Tips

1. **Enable OPcache**
```ini
# php.ini
opcache.enable=1
opcache.memory_consumption=128
```

2. **Enable APCu** (optional)
```ini
extension=apcu
apc.enabled=1
```

3. **Increase PHP Memory**
```ini
memory_limit = 256M
```

## Next Steps

1. ✅ Change default admin password
2. ✅ Configure site settings
3. ✅ Test all features
4. ✅ Setup backup schedule
5. ✅ Review security settings

## Support

Jika ada masalah:

1. Check error logs: `storage/logs/error.log`
2. Check Apache error log: `C:\laragon\logs\error.log`
3. Check PHP error log: `C:\laragon\logs\php_errors.log`

## Production Migration

Setelah testing di Laragon, siap untuk deploy ke cPanel:

1. Export database via HeidiSQL
2. Zip seluruh folder `advancedcms`
3. Follow `CPANEL_DEPLOYMENT.md` untuk deployment

---

**Selamat! CMS Anda siap digunakan di Laragon! 🚀**
