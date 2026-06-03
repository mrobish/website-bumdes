# 🚀 Next Steps - Panduan Lanjutan untuk Agent AI

File ini berisi panduan lengkap untuk agent AI lain yang mau melanjutkan project BUMDes Digital.

## 📌 Status Saat Ini

**Progress:** Tahap 1-5 selesai (50%)
**Website:** https://bumdes.ondesa.id (aktif)
**Admin:** https://bumdes.ondesa.id/admin

### Yang Sudah Jadi:
✅ Server setup (aaPanel + Nginx + PHP 8.1 + MariaDB)
✅ Laravel 10 + Filament 3 installed
✅ Database `village_info` (130+ kolom)
✅ Admin panel dengan 13 tab form
✅ Frontend website profesional (Tailwind CSS)
✅ Modul Berita (CRUD + frontend)
✅ Modul Galeri (CRUD + frontend)
✅ Modul Produk UMKM (CRUD + frontend + WhatsApp order)
✅ Seeder data contoh

### Yang Belum Dikerjakan:
⏳ Modul Keuangan (COA, transaksi, laporan PP 11/2021)
⏳ Telegram Bot (notifikasi pesanan, info produk)
⏳ Unit Usaha management
⏳ Keranjang belanja & checkout
⏳ Peta interaktif (Leaflet)
⏳ RESTful API
⏳ Testing

---

## 🎯 Tahap 6: Modul Keuangan (Prioritas Tinggi)

### Database yang Perlu Dibuat:

```sql
-- COA (Chart of Accounts) - Fleksibel per BUMDes
CREATE TABLE chart_of_accounts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    type ENUM('aset', 'kewajiban', 'modal', 'pendapatan', 'beban') NOT NULL,
    parent_id BIGINT UNSIGNED NULL,
    is_group BOOLEAN DEFAULT FALSE,
    description TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- Transaksi Keuangan
CREATE TABLE financial_transactions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    transaction_date DATE NOT NULL,
    type ENUM('pemasukan', 'pengeluaran') NOT NULL,
    account_id BIGINT UNSIGNED NOT NULL,
    counter_account_id BIGINT UNSIGNED NULL,
    amount DECIMAL(15,2) NOT NULL,
    description TEXT NOT NULL,
    reference VARCHAR(100) NULL,
    attachment VARCHAR(255) NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    approved_by BIGINT UNSIGNED NULL,
    status ENUM('draft', 'pending', 'approved', 'rejected') DEFAULT 'draft',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);

-- Anggaran
CREATE TABLE budgets (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    account_id BIGINT UNSIGNED NOT NULL,
    year YEAR NOT NULL,
    planned_amount DECIMAL(15,2) NOT NULL DEFAULT 0,
    actual_amount DECIMAL(15,2) NOT NULL DEFAULT 0,
    notes TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

### Filament Resources yang Perlu Dibuat:
1. `ChartOfAccountResource` - CRUD COA
2. `FinancialTransactionResource` - CRUD transaksi
3. `BudgetResource` - CRUD anggaran
4. `FinanceReportPage` - Halaman laporan keuangan

### Laporan yang Wajib Ada (PP 11/2021):
1. Laporan Realisasi Anggaran
2. Laporan Posisi Keuangan
3. Laporan Arus Kas
4. Laporan Perubahan Modal
5. Catatan Atas Laporan Keuangan

---

## 🎯 Tahap 7: Telegram Bot

### Database yang Perlu Dibuat:

```sql
-- Telegram Users
CREATE TABLE telegram_users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    chat_id BIGINT NOT NULL UNIQUE,
    username VARCHAR(100) NULL,
    first_name VARCHAR(100) NULL,
    last_name VARCHAR(100) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- Orders
CREATE TABLE orders (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(20) NOT NULL UNIQUE,
    telegram_user_id BIGINT UNSIGNED NULL,
    customer_name VARCHAR(255) NOT NULL,
    customer_phone VARCHAR(20) NOT NULL,
    customer_address TEXT NOT NULL,
    total_amount DECIMAL(15,2) NOT NULL,
    status ENUM('pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    notes TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- Order Items
CREATE TABLE order_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    price DECIMAL(15,2) NOT NULL,
    subtotal DECIMAL(15,2) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

### Bot Features:
1. `/start` - Welcome message + menu
2. `/produk` - Lihat katalog produk
3. `/pesan [produk]` - Pesan produk
4. `/status [nomor]` - Cek status pesanan
5. `/kontak` - Hubungi admin
6. Notifikasi otomatis saat ada pesanan baru

### Package yang Perlu Diinstall:
```bash
composer require irazasyed/telegram-bot-sdk
```

---

## 🎯 Tahap 8: Unit Usaha + Keranjang Belanja

### Database:
```sql
-- Unit Usaha
CREATE TABLE business_units (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT NULL,
    manager VARCHAR(255) NULL,
    phone VARCHAR(20) NULL,
    address TEXT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- Keranjang Belanja
CREATE TABLE carts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(255) NOT NULL,
    user_id BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

-- Cart Items
CREATE TABLE cart_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cart_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

---

## 🎯 Tahap 9: Frontend Enhancement

### Yang Perlu Ditambah:
1. Peta interaktif (Leaflet + OpenStreetMap) di halaman profil
2. Form kontak dengan validasi
3. Newsletter subscription
4. Animasi scroll (AOS.js)
5. Image lazy loading
6. Dark mode toggle

### Package:
```bash
npm install leaflet aos
```

---

## 🎯 Tahap 10: RESTful API

### Endpoint yang Perlu Dibuat:

```
GET /api/v1/desa          - Data desa
GET /api/v1/berita        - Daftar berita
GET /api/v1/berita/{id}   - Detail berita
GET /api/v1/galeri        - Daftar galeri
GET /api/v1/produk        - Daftar produk
GET /api/v1/produk/{slug} - Detail produk
GET /api/v1/kategori      - Kategori produk
POST /api/v1/pesan        - Buat pesanan
GET /api/v1/pesan/{id}    - Cek status pesanan
```

### Auth:
- Laravel Sanctum untuk API authentication
- Token-based auth untuk mobile app

---

## 🎯 Tahap 11: Testing

### Yang Perlu Di-Test:
1. **Unit Testing:**
   - Model relationships
   - Computed attributes
   - Scopes

2. **Feature Testing:**
   - CRUD operations
   - Authentication
   - Authorization

3. **Performance Testing:**
   - Query optimization
   - Caching strategy
   - Load testing

4. **Security Testing:**
   - SQL injection
   - XSS prevention
   - CSRF protection
   - File upload validation

---

## 🔧 Environment Setup untuk Agent Baru

### 1. Clone Repository
```bash
git clone https://github.com/mrobish/website-bumdes.git
cd website-bumdes
```

### 2. Install Dependencies
```bash
composer install
```

### 3. Setup Environment
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configure Database
Edit `.env`:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bumdes
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Run Migrations
```bash
php artisan migrate
php artisan db:seed
```

### 6. Create Admin User
```bash
php artisan make:filament-user
```

### 7. Link Storage
```bash
php artisan storage:link
```

### 8. Run Development Server
```bash
php artisan serve
```

---

## 📞 Server Access

### SSH
```bash
ssh ubuntu@43.133.156.104
```

### aaPanel
- URL: https://43.133.156.104:39124/48088562
- Username: bfejhgin
- Password: [redacted]

### Database
```bash
mysql -u root bumdes
```

### Restart Services
```bash
# PHP-FPM
sudo service php8.1-fpm restart

# Nginx
sudo service nginx restart

# MariaDB
sudo service mariadb restart
```

---

## ⚠️ Pitfalls & Tips

1. **Permission Issues:** Selalu `sudo chown -R ubuntu:ubuntu /www/wwwroot/bumdes/` setelah buat file baru

2. **Cloudflare Cache:** Jika ada assets yang 404, minta user purge cache di Cloudflare

3. **PHP putenv():** Harus enable di php.ini untuk Composer/Laravel

4. **View Composer:** `$villageInfo` di-share otomatis ke semua view via AppServiceProvider

5. **Filament Tab Import:** Gunakan `Filament\Forms\Components\Tabs\Tab` (bukan `Tab`)

6. **Migration Order:** Pastikan tabel parent dibuat sebelum tabel child (foreign key)

7. **Storage Link:** Harus dijalankan untuk file upload bisa diakses

8. **Queue:** Belum dikonfigurasi. Untuk email/notification nanti perlu setup queue driver
