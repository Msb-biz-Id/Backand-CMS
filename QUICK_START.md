# Quick Start Guide - Advanced CMS

## 🚀 5 Menit Setup (Laragon)

### 1. Persiapan

✅ Download & Install Laragon: https://laragon.org/download/  
✅ Extract CMS ke: `C:\laragon\www\advancedcms\`

### 2. Virtual Host

**Klik kanan Laragon icon > Apache > sites > Add Virtual Host**
- Name: `advancedcms.test`
- Path: `C:\laragon\www\advancedcms\public`

### 3. Database

**Buka HeidiSQL (via Laragon menu)**
```sql
CREATE DATABASE cms_database CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 4. Configuration

```bash
# Copy .env
cd C:\laragon\www\advancedcms
copy .env.example .env

# Edit .env
DB_NAME=cms_database
DB_USER=root
DB_PASS=
```

### 5. Install

```bash
# Buka Laragon Terminal
php database\install.php
```

### 6. Akses

**Frontend:** http://advancedcms.test  
**Admin:** http://advancedcms.test/admin

**Login:**
- Email: `admin@example.com`
- Password: `admin123`

## ⚡ Yang Sudah Berjalan

✅ **Core System**
- MVC Architecture
- Database Connection
- Routing System
- Security Layer (CSRF, XSS, SQL Injection)
- SEO System
- Caching System

✅ **Authentication**
- Login/Logout
- Session Management
- Role-based Access Control

✅ **Admin Panel**
- Dashboard dengan statistics
- Modern UI (Qovex template)
- Responsive design

✅ **Frontend**
- Homepage dengan hero section
- Feature showcase
- Responsive layout

## 📝 Test Checklist

- [ ] Homepage load: http://advancedcms.test
- [ ] Admin login: http://advancedcms.test/admin
- [ ] Dashboard load setelah login
- [ ] Logout berfungsi
- [ ] CSS/JS load dengan benar

## 🔧 Jika Ada Masalah

### Error: "Page Not Found"
```bash
# Check mod_rewrite di Apache
C:\laragon\bin\apache\httpd.conf
# Cari dan uncomment:
LoadModule rewrite_module modules/mod_rewrite.so

# Restart Apache via Laragon
```

### Error: "Database Connection Failed"
```bash
# Check MySQL service running di Laragon
# Verify credentials di .env file
```

### Error: "Permission Denied"
```bash
# Buka CMD as Administrator
cd C:\laragon\www\advancedcms
icacls storage /grant Users:F /T
icacls public\uploads /grant Users:F /T
```

## 📚 Dokumentasi Lengkap

- **Setup Laragon:** `LARAGON_SETUP.md`
- **Deploy cPanel:** `CPANEL_DEPLOYMENT.md`
- **Installation:** `INSTALLATION.md`
- **Security:** `SECURITY.md`
- **API:** `API_DOCUMENTATION.md`

## 🎯 Default Credentials

**⚠️ GANTI SETELAH LOGIN PERTAMA!**

```
Email: admin@example.com
Password: admin123
```

## 📊 Status Features

### ✅ Completed (Siap Pakai)
- Core Framework (MVC, Router, Database)
- Security Layer (CSRF, XSS, SQL Injection)
- SEO System (Meta, Schema, Sitemap)
- Authentication (Login/Logout)
- Admin Layout
- Frontend Layout
- Dashboard (dengan statistics)

### ⏳ Ready for Development
- Posts CRUD (model ready, need controller/view)
- Pages Management
- Products Catalog
- Jobs Listings
- Media Library
- Settings Management

## 🔑 Default Login Flow

1. Buka: http://advancedcms.test/admin
2. Login dengan credentials di atas
3. Akan redirect ke Dashboard
4. Ganti password: Profile > Change Password

## 🎨 Customization

### Change Site Name
```php
// config/config.php
'app' => [
    'name' => 'Your Site Name',
]
```

### Change Admin Path
```php
// config/config.php
'app' => [
    'admin_path' => 'backend', // was 'admin'
]
```

### Enable Cache
```php
// config/config.php
'cache' => [
    'enabled' => true,
    'ttl' => 3600,
]
```

## 🚀 Next Steps

1. ✅ Login ke admin panel
2. ✅ Ganti password default
3. ✅ Update site settings
4. ✅ Test all features
5. ✅ Create your first post
6. ✅ Deploy to production (follow CPANEL_DEPLOYMENT.md)

## 💡 Tips

**Development Mode:**
```ini
# .env
APP_ENV=development
APP_DEBUG=true
```

**Production Mode:**
```ini
# .env
APP_ENV=production
APP_DEBUG=false
```

**Clear Cache:**
```bash
# Via browser
http://advancedcms.test/admin/tools/cache

# Via CMD
cd C:\laragon\www\advancedcms
rd /s /q storage\cache
```

## 📞 Need Help?

1. Check error logs: `storage/logs/error.log`
2. Check documentation files
3. Review `IMPLEMENTATION_STATUS.md` untuk progress

---

**Selamat! CMS Anda siap digunakan! 🎉**
