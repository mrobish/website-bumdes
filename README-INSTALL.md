# 📋 BUMDes Management System - Panduan Instalasi Lengkap

> **Versi:** 2.0.0  
> **Developer:** mrobis  
> **GitHub:** https://github.com/mrobis/website-bumdes  
> **License:** MIT License

---

## 📑 Daftar Isi

1. [Persyaratan Sistem](#-persyaratan-sistem)
2. [Software yang Dibutuhkan](#-software-yang-dibutuhkan)
3. [Persiapan Server](#-persiapan-server)
4. [Instalasi Step-by-Step](#-instalasi-step-by-step)
5. [Konfigurasi Web Server](#-konfigurasi-web-server)
6. [Post-Instalasi](#-post-instalasi)
7. [Troubleshooting](#-troubleshooting)
8. [FAQ](#-faq)

---

## 💻 Persyaratan Sistem

### Minimum Requirements

| Komponen | Minimum | Rekomendasi |
|----------|---------|-------------|
| **OS** | Ubuntu 20.04 / CentOS 7 / Debian 10 | Ubuntu 22.04 / 24.04 LTS |
| **RAM** | 1 GB | 2 GB atau lebih |
| **CPU** | 1 Core | 2 Core atau lebih |
| **Storage** | 2 GB free space | 5 GB atau lebih |
| **PHP** | 8.1.0 | 8.1.x atau 8.2.x |
| **Database** | MySQL 5.7 / MariaDB 10.3 | MariaDB 10.6+ |
| **Web Server** | Apache 2.4 / Nginx 1.18 | Nginx 1.22+ |

### PHP Version

```
Minimum: PHP 8.1.0
Recommended: PHP 8.1.x atau 8.2.x
```

**Cek versi PHP:**
```bash
php -v
```

### PHP Extensions yang Dibutuhkan

| Extension | Fungsi | Wajib? |
|-----------|--------|--------|
| `openssl` | Enkripsi & security | ✅ Ya |
| `pdo` | Database connection | ✅ Ya |
| `pdo_mysql` | MySQL driver | ✅ Ya |
| `mbstring` | Multi-byte string | ✅ Ya |
| `tokenizer` | Token parsing | ✅ Ya |
| `xml` | XML processing | ✅ Ya |
| `ctype` | Character type | ✅ Ya |
| `json` | JSON handling | ✅ Ya |
| `bcmath` | Math precision | ✅ Ya |
| `fileinfo` | File detection | ✅ Ya |
| `gd` | Image processing | ✅ Ya |
| `curl` | HTTP requests | ✅ Ya |
| `zip` | ZIP compression | ✅ Ya |
| `intl` | Internationalization | ⚪ Opsional |
| `exif` | Image metadata | ⚪ Opsional |

**Cek extensions yang sudah terinstall:**
```bash
php -m
```

**Cek extension spesifik:**
```bash
php -m | grep -E "openssl|pdo|mbstring|gd|curl|zip"
```

---

## 🛠️ Software yang Dibutuhkan

### 1. Web Server (Pilih Salah Satu)

#### Option A: Apache 2.4+
```bash
# Ubuntu/Debian
sudo apt update
sudo apt install apache2

# CentOS/RHEL
sudo yum install httpd
# atau
sudo dnf install httpd
```

**Module yang dibutuhkan:**
```bash
# Ubuntu/Debian
sudo a2enmod rewrite
sudo a2enmod ssl
sudo a2enmod headers

# Restart Apache
sudo systemctl restart apache2
```

#### Option B: Nginx 1.18+ (Direkomendasikan)
```bash
# Ubuntu/Debian
sudo apt update
sudo apt install nginx

# CentOS/RHEL
sudo yum install nginx
# atau
sudo dnf install nginx
```

### 2. PHP 8.1+

#### Instalasi PHP 8.1 (Ubuntu/Debian)
```bash
# Tambah repository PHP
sudo apt install software-properties-common
sudo add-apt-repository ppa:ondrej/php
sudo apt update

# Install PHP 8.1 + extensions
sudo apt install php8.1 php8.1-fpm php8.1-mysql php8.1-cli \
    php8.1-common php8.1-mbstring php8.1-xml php8.1-curl \
    php8.1-zip php8.1-gd php8.1-bcmath php8.1-fileinfo \
    php8.1-tokenizer php8.1-ctype php8.1-json php8.1-opcache

# Restart PHP-FPM (jika pakai Nginx)
sudo systemctl restart php8.1-fpm

# Restart Apache (jika pakai Apache)
sudo systemctl restart apache2
```

#### Instalasi PHP 8.1 (CentOS/RHEL)
```bash
# Install EPEL repository
sudo yum install epel-release

# Install Remi repository
sudo yum install https://rpms.remirepo.net/enterprise/remi-release-8.rpm

# Enable PHP 8.1
sudo dnf module reset php
sudo dnf module enable php:remi-8.1

# Install PHP 8.1 + extensions
sudo dnf install php php-fpm php-mysqlnd php-cli php-common \
    php-mbstring php-xml php-curl php-zip php-gd php-bcmath \
    php-fileinfo php-tokenizer php ctype php-json php-opcache

# Start PHP-FPM
sudo systemctl start php-fpm
sudo systemctl enable php-fpm
```

### 3. Database Server (Pilih Salah Satu)

#### Option A: MariaDB 10.3+ (Direkomendasikan)
```bash
# Ubuntu/Debian
sudo apt install mariadb-server mariadb-client

# CentOS/RHEL
sudo yum install mariadb-server mariadb
# atau
sudo dnf install mariadb-server mariadb

# Start MariaDB
sudo systemctl start mariadb
sudo systemctl enable mariadb

# Secure installation
sudo mysql_secure_installation
```

#### Option B: MySQL 5.7+
```bash
# Ubuntu/Debian
sudo apt install mysql-server mysql-client

# CentOS/RHEL
sudo yum install mysql-server mysql
# atau
sudo dnf install mysql-server mysql

# Start MySQL
sudo systemctl start mysqld
sudo systemctl enable mysqld

# Secure installation
sudo mysql_secure_installation
```

### 4. Composer 2.x
```bash
# Download installer
cd ~
curl -sS https://getcomposer.org/installer -o composer-setup.php

# Install globally
sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer

# Verify
composer --version

# Cleanup
rm composer-setup.php
```

### 5. Node.js 18+ & NPM (Opsional - untuk build frontend)
```bash
# Install Node.js 18 LTS
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install nodejs

# Verify
node --version
npm --version
```

### 6. Git (Opsional)
```bash
# Ubuntu/Debian
sudo apt install git

# CentOS/RHEL
sudo yum install git
# atau
sudo dnf install git

# Verify
git --version
```

---

## 🖥️ Persiapan Server

### Untuk Shared Hosting (cPanel/Plesk)

**Persyaratan:**
- PHP 8.1+ dengan extensions di atas
- MySQL 5.7+ atau MariaDB 10.3+
- SSH access (opsional tapi direkomendasikan)
- Composer support

**Langkah:**
1. Login ke cPanel/Plesk
2. Buat database baru di MySQL Databases
3. Buat database user dan assign ke database
4. Upload file ZIP via File Manager
5. Extract di folder `public_html` atau subfolder
6. Lanjut ke [Instalasi Step-by-Step](#-instalasi-step-by-step)

### Untuk VPS/Cloud Server

**Contoh Provider:**
- DigitalOcean ($4-6/bulan)
- Vultr ($3.50-6/bulan)
- Linode/Akamai ($5/bulan)
- AWS EC2 (t2.micro free tier)
- Google Cloud (e2-micro free tier)
- Hetzner ($4.50/bulan)
- IDCloudHost / Biznet Gio / Rumahweb (Indonesia)

**Setup Server Baru:**
```bash
# 1. Update system
sudo apt update && sudo apt upgrade -y

# 2. Install required packages
sudo apt install -y software-properties-common curl wget unzip

# 3. Install LEMP Stack (Linux, Nginx, MariaDB, PHP)
# Ikuti instruksi di atas untuk masing-masing software

# 4. Create database
sudo mysql -u root -p
```

```sql
-- Di dalam MySQL shell
CREATE DATABASE bumdes CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'bumdes_user'@'localhost' IDENTIFIED BY 'password_kuat_disini';
GRANT ALL PRIVILEGES ON bumdes.* TO 'bumdes_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Untuk aaPanel (Seperti Server Ini)

```bash
# 1. Login aaPanel (http://your-ip:8888)
# 2. Install LNMP stack via aaPanel:
#    - Nginx 1.22+
#    - MySQL 5.7+ atau MariaDB 10.6+
#    - PHP 8.1+
# 3. Buat website baru
# 4. Buat database baru
# 5. Upload file
```

---

## 🚀 Instalasi Step-by-Step

### Step 1: Download Aplikasi

#### Option A: Download ZIP dari GitHub
```bash
cd /www/wwwroot  # atau /var/www/html atau folder web Anda

# Download latest release
wget https://github.com/mrobis/website-bumdes/archive/refs/heads/main.zip -O bumdes.zip

# Extract
unzip bumdes.zip
mv website-bumdes-main bumdes
cd bumdes
```

#### Option B: Git Clone
```bash
cd /www/wwwroot
git clone https://github.com/mrobis/website-bumdes.git bumdes
cd bumdes
```

#### Option C: Upload via File Manager (cPanel)
1. Download ZIP dari https://github.com/mrobis/website-bumdes/archive/refs/heads/main.zip
2. Login ke cPanel → File Manager
3. Navigate ke `public_html`
4. Upload ZIP file
5. Extract ZIP
6. Rename folder menjadi `bumdes` (opsional)

### Step 2: Install Dependencies

```bash
cd /www/wwwroot/bumdes

# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Jika error memory limit:
php -d memory_limit=-1 /usr/local/bin/composer install --no-dev --optimize-autoloader
```

### Step 3: Set Permissions

```bash
# Set ownership (sesuaikan dengan web server user)
# Untuk Nginx/Apache di Ubuntu/Debian:
sudo chown -R www-data:www-data /www/wwwroot/bumdes

# Untuk aaPanel:
sudo chown -R www:www /www/wwwroot/bumdes

# Set folder permissions
sudo chmod -R 755 /www/wwwroot/bumdes
sudo chmod -R 775 /www/wwwroot/bumdes/storage
sudo chmod -R 775 /www/wwwroot/bumdes/bootstrap/cache

# Jika masih error:
sudo chmod -R 777 /www/wwwroot/bumdes/storage
sudo chmod -R 777 /www/wwwroot/bumdes/bootstrap/cache
```

### Step 4: Buka Browser

Akses website Anda:
```
http://domain-anda.com
```
atau
```
http://ip-server/install
```

**Wizard instalasi akan muncul secara otomatis!**

### Step 5: Ikuti Wizard Instalasi

#### 🔍 Step 1: System Check
- Sistem akan otomatis memeriksa:
  - ✅ PHP version >= 8.1
  - ✅ Required extensions
  - ✅ Writable directories
- Jika semua ✅ hijau, klik **"Lanjut ke Database"**

#### 🗄️ Step 2: Konfigurasi Database
Isi form:
```
Database Host: 127.0.0.1 (atau localhost)
Port: 3306
Database Name: bumdes
Username: bumdes_user (atau root)
Password: (password database Anda)
```

Klik **"🔌 Test Koneksi"** untuk verifikasi.

Jika berhasil ✅, klik **"Lanjut ke Admin"**

#### 👤 Step 3: Buat Akun Admin
```
Nama Lengkap: Administrator
Email: admin@bumdes.id
Password: (minimal 6 karakter)
Konfirmasi Password: (ulangi password)
```

Klik **"Lanjut ke Identitas BUMDes"**

#### 🏢 Step 4: Identitas BUMDes
```
Nama BUMDes: BUMDes Karya Mekar
Nama Desa: Karangmekar
Alamat: Jl. Raya Desa No. 1, Kecamatan, Kabupaten
No. Telepon: 08xxxxxxxxxx
Email: info@bumdes.id (opsional)
```

Klik **"Lanjut ke Selesai"**

#### ✨ Step 5: Instalasi Otomatis
Sistem akan otomatis:
- ✅ Menulis konfigurasi .env
- ✅ Menjalankan migrasi database
- ✅ Membuat data default (COA, kategori, dll)
- ✅ Membuat akun admin
- ✅ Menyimpan identitas BUMDes
- ✅ Membersihkan cache

**Tunggu hingga selesai!**

### Step 6: Login ke Dashboard

Setelah instalasi berhasil:
1. Klik **"🚀 Masuk ke Dashboard"**
2. Login dengan email dan password yang dibuat tadi
3. **Selesai!** 🎉

---

## 🌐 Konfigurasi Web Server

### Apache (.htaccess)

Sudah disediakan di folder `public/`. Pastikan `mod_rewrite` aktif:

```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

**Virtual Host (Opsional):**
```apache
<VirtualHost *:80>
    ServerName bumdes.example.com
    DocumentRoot /www/wwwroot/bumdes/public
    
    <Directory /www/wwwroot/bumdes/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/bumdes_error.log
    CustomLog ${APACHE_LOG_DIR}/bumdes_access.log combined
</VirtualHost>
```

### Nginx (Direkomendasikan)

**Konfigurasi Nginx:**
```nginx
server {
    listen 80;
    server_name bumdes.example.com;  # Ganti dengan domain Anda
    root /www/wwwroot/bumdes/public;  # Sesuaikan path
    index index.php index.html;
    
    # Charset
    charset utf-8;
    
    # Logging
    access_log /var/log/nginx/bumdes_access.log;
    error_log /var/log/nginx/bumdes_error.log;
    
    # Max upload size
    client_max_body_size 20M;
    
    # Location blocks
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;  # Sesuaikan versi PHP
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }
    
    location ~ /\.(?!well-known).* {
        deny all;
    }
    
    # Cache static files
    location ~* \.(css|js|jpg|jpeg|png|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
    }
}
```

**Restart Nginx:**
```bash
sudo nginx -t  # Test config
sudo systemctl restart nginx
```

### SSL/HTTPS (Direkomendasikan)

#### Option A: Let's Encrypt (Gratis)
```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx  # Untuk Nginx
sudo apt install certbot python3-certbot-apache  # Untuk Apache

# Generate SSL certificate
sudo certbot --nginx -d bumdes.example.com  # Untuk Nginx
sudo certbot --apache -d bumdes.example.com  # Untuk Apache

# Auto-renewal
sudo certbot renew --dry-run
```

#### Option B: Cloudflare (Seperti Server Ini)
1. Daftar di https://cloudflare.com
2. Tambah domain
3. Update nameserver di registrar
4. Enable SSL: **Full** atau **Full (Strict)**
5. Enable **Always Use HTTPS**

#### Option C: aaPanel
1. Login aaPanel
2. Menu: Website → Settings → SSL
3. Pilih Let's Encrypt atau upload custom certificate

---

## ⚙️ Post-Instalasi

### 1. Konfigurasi Cron Job

Tambah cron job untuk task scheduler Laravel:

```bash
crontab -e
```

Tambah baris ini:
```
* * * * * cd /www/wwwroot/bumdes && php artisan schedule:run >> /dev/null 2>&1
```

### 2. Optimasi Performa (Production)

```bash
cd /www/wwwroot/bumdes

# Cache config
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize
php artisan optimize
```

### 3. Backup Database (Otomatis)

Buat script backup:
```bash
cat > /www/wwwroot/bumdes/backup.sh << 'EOF'
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/www/wwwroot/bumdes/storage/app/backups"
mkdir -p $BACKUP_DIR

mysqldump -u bumdes_user -p'password' bumdes | gzip > $BACKUP_DIR/backup_$DATE.sql.gz

# Hapus backup lebih dari 30 hari
find $BACKUP_DIR -name "backup_*.sql.gz" -mtime +30 -delete
EOF

chmod +x /www/wwwroot/bumdes/backup.sh
```

Tambah cron untuk backup otomatis:
```bash
crontab -e
```
```
0 2 * * * /www/wwwroot/bumdes/backup.sh
```

### 4. Update Aplikasi

```bash
cd /www/wwwroot/bumdes

# Backup dulu
./backup.sh

# Pull update terbaru
git pull origin main

# Install dependencies
composer install --no-dev --optimize-autoloader

# Run migrations
php artisan migrate

# Clear cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set permissions
sudo chown -R www:www storage bootstrap/cache
```

### 5. Setup Email (Opsional)

Edit `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=email@gmail.com
MAIL_PASSWORD=app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="info@bumdes.id"
MAIL_FROM_NAME="${APP_NAME}"
```

---

## 🔧 Troubleshooting

### Error 500 (Internal Server Error)

**Penyebab & Solusi:**

1. **Permission denied**
   ```bash
   sudo chmod -R 775 storage bootstrap/cache
   sudo chown -R www:www storage bootstrap/cache
   ```

2. **.env tidak ada**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **APP_KEY kosong**
   ```bash
   php artisan key:generate
   ```

4. **Cek error log**
   ```bash
   tail -f storage/logs/laravel.log
   ```

### Database Connection Failed

```bash
# Cek MySQL/MariaDB running
sudo systemctl status mysql

# Cek database exists
mysql -u root -p -e "SHOW DATABASES;"

# Cek user privileges
mysql -u root -p -e "SHOW GRANTS FOR 'bumdes_user'@'localhost';"

# Test connection
php artisan tinker
# >>> DB::connection()->getPdo();
```

### Blank Page

```bash
# Enable debug mode
# Edit .env, set APP_DEBUG=true

# Clear cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Cek PHP error log
tail -f /var/log/php8.1-fpm.log
```

### 404 Not Found (Nginx)

```bash
# Pastikan root folder benar
# root /www/wwwroot/bumdes/public;  (harus ke folder public!)

# Restart Nginx
sudo nginx -t
sudo systemctl restart nginx
```

### Upload Limit

```bash
# Edit php.ini
sudo nano /etc/php/8.1/fpm/php.ini

# Cari dan ubah:
upload_max_filesize = 20M
post_max_size = 25M
memory_limit = 256M
max_execution_time = 60

# Restart PHP-FPM
sudo systemctl restart php8.1-fpm
```

### Storage Link Error

```bash
php artisan storage:link

# Jika error "link already exists":
rm public/storage
php artisan storage:link
```

---

## ❓ FAQ

### Q: Apakah bisa di shared hosting?
**A:** Bisa! Upload ZIP via File Manager, extract, lalu buka browser untuk wizard instalasi. Pastikan support PHP 8.1+ dan MySQL.

### Q: Berapa biaya server?
**A:** 
- Shared Hosting: Rp 10.000 - 50.000/bulan
- VPS: Rp 50.000 - 150.000/bulan
- Cloud: Tergantung provider

### Q: Apakah bisa di localhost?
**A:** Bisa! Gunakan Laragon, XAMPP, atau Docker untuk development lokal.

### Q: Bagaimana cara update?
**A:** 
```bash
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate
php artisan config:cache
```

### Q: Apakah data bisa diexport?
**A:** Ya! Semua laporan bisa diexport ke PDF dan Excel.

### Q: Berapa user yang bisa dibuat?
**A:** Tidak ada batasan. Tersedia 5 role: admin, bendahara_pusat, bendahara_unit, manajer_unit, pengawas.

### Q: Apakah bisa multi-BUMDes?
**A:** Satu instalasi untuk satu BUMDes. Untuk multi-BUMDes, install di subdomain berbeda.

### Q: Bagaimana cara backup?
**A:** 
```bash
# Database
mysqldump -u root -p bumdes > backup.sql

# Files
tar -czf bumdes-backup.tar.gz /www/wwwroot/bumdes
```

### Q: Apakah aman?
**A:** Ya! Menggunakan:
- CSRF protection
- SQL injection prevention (Eloquent ORM)
- XSS protection
- Password hashing (bcrypt)
- Session encryption

---

## 📞 Support

- **GitHub Issues:** https://github.com/mrobis/website-bumdes/issues
- **Email:** info@bumdeskeudebakongan.id

---

## 📄 License

MIT License - © 2026 mrobis

---

**Dibuat dengan ❤️ untuk Kemajuan Desa Indonesia**

**By [mrobis](https://github.com/mrobis)**
