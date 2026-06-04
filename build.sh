#!/bin/bash

# BUMDes Management System - Build Script
# Creates a distributable ZIP package

set -e

echo "=========================================="
echo " BUMDes Management System - Build Package"
echo "=========================================="
echo ""

# Configuration
PACKAGE_NAME="bumdes-management-system"
VERSION=$(git describe --tags --abbrev=0 2>/dev/null || echo "v2.0.0")
BUILD_DIR="build"
DIST_DIR="dist"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
ZIP_NAME="${PACKAGE_NAME}-${VERSION}-${TIMESTAMP}.zip"

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

print_status() {
    echo -e "${GREEN}[✓]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[!]${NC} $1"
}

print_error() {
    echo -e "${RED}[✗]${NC} $1"
}

# Step 1: Clean previous builds
echo "Step 1: Cleaning previous builds..."
rm -rf $BUILD_DIR $DIST_DIR
mkdir -p $BUILD_DIR $DIST_DIR
print_status "Build directories cleaned"

# Step 2: Install production dependencies
echo ""
echo "Step 2: Installing production dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction
print_status "Composer dependencies installed"

# Step 3: Build frontend assets
echo ""
echo "Step 3: Building frontend assets..."
npm ci
npm run build
print_status "Frontend assets built"

# Step 4: Copy files to build directory
echo ""
echo "Step 4: Copying files to build directory..."
rsync -av --progress \
    --exclude='.git' \
    --exclude='node_modules' \
    --exclude='.env' \
    --exclude='.installed' \
    --exclude='storage/app/*' \
    --exclude='storage/framework/cache/*' \
    --exclude='storage/framework/sessions/*' \
    --exclude='storage/framework/views/*' \
    --exclude='storage/logs/*' \
    --exclude='bootstrap/cache/*' \
    --exclude='.DS_Store' \
    --exclude='*.log' \
    --exclude='tests' \
    --exclude='phpunit.xml' \
    --exclude='.editorconfig' \
    --exclude='.gitignore' \
    --exclude='build.sh' \
    --exclude='dist' \
    . $BUILD_DIR/
print_status "Files copied to build directory"

# Step 5: Create .env.example
echo ""
echo "Step 5: Creating .env.example..."
cat > $BUILD_DIR/.env.example << 'EOF'
APP_NAME="BUMDes Management System"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bumdes
DB_USERNAME=root
DB_PASSWORD=

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
EOF
print_status ".env.example created"

# Step 6: Create README for distribution
echo ""
echo "Step 6: Creating distribution README..."
cat > $BUILD_DIR/README-INSTALL.md << 'EOF'
# BUMDes Management System - Installation Guide

## 📋 Persyaratan Sistem

- PHP >= 8.1
- MySQL/MariaDB >= 10.4
- Extension PHP: openssl, pdo, mbstring, tokenizer, xml, ctype, json, bcmath, fileinfo, gd, mysql, curl, zip
- Web Server: Apache/Nginx

## 🚀 Instalasi

### 1. Upload File

Upload semua file ke web server (public_html atau folder www).

### 2. Set Permissions

```bash
chmod -R 755 storage bootstrap/cache
chmod 644 .env.example
cp .env.example .env
```

### 3. Buka Browser

Akses website Anda. Wizard instalasi akan muncul secara otomatis.

### 4. Ikuti Wizard

Wizard akan memandu Anda melalui:
1. ✅ System Check - Verifikasi persyaratan sistem
2. 🗄️ Database - Konfigurasi koneksi database
3. 👤 Admin - Buat akun administrator
4. 🏢 BUMDes - Isi identitas BUMDes
5. ✨ Selesai - Sistem siap digunakan

## 🔐 Login Default

Setelah instalasi, Anda dapat login dengan:
- **Email:** admin@bumdes.id (sesuai yang diisi di wizard)
- **Password:** (sesuai yang diisi di wizard)

## 📁 Struktur Folder

```
bumdes/
├── app/              # Application code
├── bootstrap/        # Framework bootstrap
├── config/           # Configuration files
├── database/         # Database migrations & seeders
├── public/           # Public accessible files
├── resources/        # Views & assets
├── routes/           # Route definitions
├── storage/          # Logs, cache, sessions
├── vendor/           # Composer dependencies
├── .env.example      # Environment template
└── README-INSTALL.md # This file
```

## 🔧 Konfigurasi Web Server

### Apache (.htaccess sudah disediakan)

Pastikan mod_rewrite diaktifkan:
```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### Nginx

```nginx
server {
    listen 80;
    server_name bumdes.example.com;
    root /path/to/bumdes/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

## 🆘 Troubleshooting

### 500 Error
- Check file permissions (storage & bootstrap/cache)
- Check .env file exists and is configured
- Check Laravel log: storage/logs/laravel.log

### Database Connection Failed
- Verify database credentials in .env
- Make sure MySQL/MariaDB is running
- Check firewall settings

### Blank Page
- Enable APP_DEBUG=true in .env
- Check PHP error log

## 📞 Support

- GitHub: https://github.com/mrobis/website-bumdes
- Issues: https://github.com/mrobis/website-bumdes/issues

## 📄 License

MIT License - © mrobis
EOF
print_status "Distribution README created"

# Step 7: Create .htaccess for public folder
echo ""
echo "Step 7: Creating .htaccess..."
cat > $BUILD_DIR/public/.htaccess << 'EOF'
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
EOF
print_status ".htaccess created"

# Step 8: Set permissions
echo ""
echo "Step 8: Setting permissions..."
chmod -R 755 $BUILD_DIR/storage $BUILD_DIR/bootstrap/cache 2>/dev/null || true
print_status "Permissions set"

# Step 9: Create ZIP package
echo ""
echo "Step 9: Creating ZIP package..."
cd $BUILD_DIR
zip -r ../$DIST_DIR/$ZIP_NAME . -x "*.git*" "node_modules/*" ".env" ".installed"
cd ..
print_status "ZIP package created: $DIST_DIR/$ZIP_NAME"

# Step 10: Generate checksum
echo ""
echo "Step 10: Generating checksum..."
md5sum $DIST_DIR/$ZIP_NAME > $DIST_DIR/$ZIP_NAME.md5
sha256sum $DIST_DIR/$ZIP_NAME > $DIST_DIR/$ZIP_NAME.sha256
print_status "Checksums generated"

# Cleanup
echo ""
echo "Step 11: Cleaning up..."
rm -rf $BUILD_DIR
print_status "Build directory cleaned"

# Summary
echo ""
echo "=========================================="
echo " Build Complete!"
echo "=========================================="
echo ""
echo "Package: $DIST_DIR/$ZIP_NAME"
echo "Size: $(du -h $DIST_DIR/$ZIP_NAME | cut -f1)"
echo ""
echo "MD5: $(cat $DIST_DIR/$ZIP_NAME.md5 | cut -d' ' -f1)"
echo "SHA256: $(cat $DIST_DIR/$ZIP_NAME.sha256 | cut -d' ' -f1)"
echo ""
echo "Distribution files:"
ls -lh $DIST_DIR/
echo ""
echo "Ready for distribution! 🚀"
