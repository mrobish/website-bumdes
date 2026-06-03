# 🏘️ BUMDes Digital - Website Badan Usaha Milik Desa

Platform digital untuk BUMDes (Badan Usaha Milik Desa) yang dibangun dengan **Laravel 10** + **Filament 3**.

## 🎯 Visi

Menjadi platform digital yang memudahkan BUMDes di seluruh Indonesia untuk:
- Mengelola data desa secara digital
- Memasarkan produk UMKM secara online
- Menyampaikan informasi ke warga
- Mengelola keuangan desa secara transparan

## 📋 Technology Stack

| Komponen | Teknologi |
|----------|-----------|
| Backend | Laravel 10.50 |
| Admin Panel | Filament 3.3 |
| Frontend | Tailwind CSS + Blade |
| Database | MariaDB |
| Server | aaPanel + Nginx |
| PHP | 8.1 |
| CDN | Cloudflare |

## 📊 Progress Development

### Tahap 1: Server Setup ✅
- [x] Install aaPanel di Ubuntu 24.04
- [x] Install PHP 8.1 + Tidy extension
- [x] Setup Nginx virtual host
- [x] Konfigurasi SSL/HTTPS
- [x] Optimasi OPcache, PHP-FPM, MariaDB, Nginx

### Tahap 2: Laravel + Filament Install ✅
- [x] Install Laravel 10 via Composer
- [x] Install Filament 3
- [x] Setup admin panel
- [x] Buat admin user

### Tahap 3: Database Setup ✅
- [x] Buat database bumdes
- [x] Buat tabel village_info (130+ kolom)
- [x] Import data desa dari OpenSID
- [x] Seeder data contoh

### Tahap 4: Template Info Desa ✅
- [x] Form admin 13 tab
- [x] Frontend website profesional
- [x] Responsive design (mobile-first)
- [x] WhatsApp floating button

### Tahap 5: Halaman Konten ✅
- [x] Berita/Artikel
- [x] Galeri Foto/Video
- [x] Produk UMKM
- [x] Kategori Produk

### Tahap 6: Modul Keuangan ✅
- [x] COA fleksibel (25 akun default)
- [x] Transaksi keuangan
- [x] Anggaran (budgeting)
- [x] Business Units (Induk + 4 Unit Usaha)
- [x] RAK (Rekening Antar Kantor)
- [x] Laporan Konsolidasi
- [x] Template Laporan SAK EMKM
- [x] Penyusutan Aset (Depresiasi)
- [x] Penyertaan Modal (9 sumber, 4 jenis)

### Tahap 7: PWA & Offline Mode ✅
- [x] Service Worker (sw.js)
- [x] IndexedDB untuk offline storage
- [x] Auto-sync saat online
- [x] Form input transaksi offline
- [x] Connection status bar

### Tahap 8: Unit Usaha Frontend ✅
- [x] Halaman listing semua unit usaha
- [x] Halaman detail per unit usaha
- [x] Produk & layanan per unit
- [x] Berita terkait per unit
- [x] Integration dengan navigation menu

### Tahap 9: Telegram Bot ⏳
- [ ] Bot notifikasi pesanan
- [ ] Bot cek status
- [ ] Bot info produk

### Tahap 10: Frontend Enhancement ⏳
- [ ] Peta interaktif
- [ ] Form kontak
- [ ] Newsletter
- [ ] SEO optimization

### Tahap 11: API RESTful ⏳
- [ ] API data desa
- [ ] API produk
- [ ] API berita
- [ ] Authentication

### Tahap 12: Testing & Launch ⏳
- [ ] Unit testing
- [ ] Performance testing
- [ ] Security audit
- [ ] Go live

## 🚀 Quick Start

```bash
git clone https://github.com/mrobish/website-bumdes.git
cd website-bumdes
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan make:filament-user
php artisan storage:link
php artisan serve
```

## 📱 Halaman Website

| Halaman | URL |
|---------|-----|
| Beranda | / |
| Profil Desa | /profil-desa |
| BUMDes | /bumdes |
| Unit Usaha | /unit-usaha |
| Unit Usaha Detail | /unit-usaha/{slug} |
| Potensi | /potensi |
| Berita | /berita |
| Galeri | /galeri |
| Produk | /produk |
| Kontak | /kontak |
| Offline Transaksi | /offline-transaksi |
| Admin | /admin |

## 📊 Database

### Core Tables
- `users` - User management
- `village_info` - Data desa (130+ kolom)

### Content Tables
- `news` - Berita/artikel
- `galleries` - Galeri foto/video
- `products` - Produk UMKM
- `product_categories` - Kategori produk

### Financial Tables
- `chart_of_accounts` - COA (25 akun default)
- `financial_transactions` - Transaksi keuangan
- `budgets` - Anggaran
- `business_units` - Unit usaha (Induk + 4 unit)
- `inter_account_transfers` - RAK (Rekening Antar Kantor)
- `consolidated_reports` - Laporan konsolidasi
- `financial_report_templates` - Template SAK EMKM
- `assets` - Aset tetap
- `asset_depreciations` - Penyusutan aset
- `capital_contributions` - Penyertaan modal

## 🏢 Arsitektur Holding Company

```
BUMDes Keude Bakongan
├── BUMDes Induk (Pusat)
│   ├── Unit Ketahanan Pangan
│   ├── Unit Pariwisata
│   ├── Unit Pengelolaan Sampah
│   └── Unit Jaringan Internet
```

## 📈 Fitur Utama

### Admin Panel (Filament 3)
- Dashboard dengan statistik
- Manajemen data desa
- Manajemen berita & galeri
- Manajemen produk UMKM
- Modul keuangan lengkap
- Laporan keuangan SAK EMKM
- Manajemen unit usaha

### Frontend Website
- Responsive design (mobile-first)
- WhatsApp floating button
- Galeri foto interaktif
- Pencarian produk
- Halaman unit usaha

### PWA (Progressive Web App)
- Installable di手机
- Offline mode
- Auto-sync data

---

**Dibuat dengan ❤️ untuk BUMDes Indonesia**
