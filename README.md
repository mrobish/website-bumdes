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

### Tahap 6: Modul Keuangan ⏳
- [ ] COA fleksibel
- [ ] Transaksi keuangan
- [ ] Laporan PP 11/2021
- [ ] Audit trail

### Tahap 7: Telegram Bot ⏳
- [ ] Bot notifikasi pesanan
- [ ] Bot cek status
- [ ] Bot info produk

### Tahap 8: Unit Usaha ⏳
- [ ] Modul unit usaha
- [ ] Keranjang belanja
- [ ] Checkout WhatsApp/Telegram

### Tahap 9: Frontend ⏳
- [ ] Peta interaktif
- [ ] Form kontak
- [ ] Newsletter

### Tahap 10: API RESTful ⏳
- [ ] API data desa
- [ ] API produk
- [ ] API berita
- [ ] Authentication

### Tahap 11: Testing ⏳
- [ ] Unit testing
- [ ] Performance testing
- [ ] Security audit
- [ ] Go live

## 🚀 Quick Start

```bash
git clone https://github.com/USERNAME/bumdes-digital.git
cd bumdes-digital
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
| Potensi | /potensi |
| Berita | /berita |
| Galeri | /galeri |
| Produk | /produk |
| Kontak | /kontak |
| Admin | /admin |

## 📊 Database

- `village_info` - Data desa (130+ kolom)
- `news` - Berita/artikel
- `galleries` - Galeri foto/video
- `products` - Produk UMKM
- `product_categories` - Kategori produk

---

**Dibuat dengan ❤️ untuk BUMDes Indonesia**
