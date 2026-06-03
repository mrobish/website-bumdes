# 📋 Development Log - BUMDes Digital

Catatan lengkap semua aktivitas pengembangan project BUMDes Digital.

## 📅 Timeline

### 2026-06-03: Tahap 1-5 (Server Setup → Konten)

#### Tahap 1: Server Setup ✅
**Yang dikerjakan:**
- Install aaPanel di Ubuntu 24.04 (VPS 2 core, 2GB RAM)
- IP Server: 43.133.156.104
- Install PHP 8.1 + Tidy extension (compile dari source)
- Setup Nginx virtual host untuk `bumdes.ondesa.id`
- Konfigurasi SSL/HTTPS via aaPanel (self-signed cert)
- Optimasi: OPcache, PHP-FPM (max_children 30→10), MariaDB, Nginx buffers

**Keputusan:**
- Nginx dipilih over Apache karena lebih hemat memory di 2GB RAM
- SSL mode "Full" karena pakai self-signed cert di origin
- Cloudflare dipasang sebagai CDN/reverse proxy

#### Tahap 2: Laravel + Filament Install ✅
**Yang dikerjakan:**
- Install Laravel 10.50.2 via Composer 2.10.0
- Path: `/www/wwwroot/bumdes/`
- Install Filament 3.3
- Publish assets Livewire ke public/
- Buat admin user: `admin@bumdes.id`

**Issues yang dihadapi:**
1. `putenv()` disabled → edit php.ini untuk enable
2. Composer 2.0.0 incompatible dengan Laravel 10 → update ke 2.10.0
3. Livewire.js 404 via Cloudflare → user harus purge cache
4. `Filament\Forms\Components\Tab` not found → fix import ke `Tabs\Tab`

**Keputusan:**
- Database bumdes dipisah dari OpenSID (data_coba)
- Root user untuk database bumdes (development)

#### Tahap 3: Database Setup ✅
**Yang dikerjakan:**
- Buat database `bumdes` di MariaDB
- Buat tabel `village_info` dengan 130+ kolom
- Design database berdasarkan referensi OpenSID (tabel config + profil_desa)

**Referensi dari OpenSID:**
- Tabel `config`: nama_desa, kode_desa, kecamatan, kabupaten, provinsi, alamat, kontak
- Tabel `profil_desa`: key-value untuk data fleksibel (ekologi, internet, adat)
- Struktur ini diadaptasi dan diperluas untuk BUMDes

#### Tahap 4: Template Info Desa ✅
**Yang dikerjakan:**
- Buat form admin Filament 13 tab:
  1. Data Umum (identitas desa, kontak)
  2. Data Pemerintahan (kepala desa, sekretaris, perangkat)
  3. Data Geografis (luas, batas, topografi, iklim)
  4. Data Demografis (penduduk, KK, dusun, RT/RW)
  5. Data Ekonomi (UMKM, penghasilan, kemiskinan)
  6. Data Sosial (sekolah, kesehatan, masjid)
  7. Data Infrastruktur (jalan, listrik, air, internet)
  8. Data Keuangan (APBDes, realisasi)
  9. Data BUMDes (nama, modal, omzet, karyawan)
  10. Data Potensi (wisata, pertanian, perikanan, budaya)
  11. Data Media & SEO (foto, video, meta tags)
  12. Data Kontak & Sosial (WA, FB, IG, YouTube)
  13. Pengaturan (toggle publikasi)

- Frontend website profesional:
  - Layout dengan top bar, navigation sticky, footer 4 kolom
  - WhatsApp floating button
  - Back to top button
  - Google Fonts (Inter + Playfair Display)
  - Mobile responsive

- Homepage: hero section, statistik, quick access, about, BUMDes highlight

#### Tahap 5: Halaman Konten ✅
**Yang dikerjakan:**

**Database (4 tabel baru):**
- `news` - Berita/artikel (title, slug, content, category, image, views)
- `galleries` - Galeri foto/video (title, image, type, category)
- `product_categories` - Kategori produk (name, slug, icon)
- `products` - Produk UMKM (name, price, stock, category, whatsapp_order)

**Models (4):**
- `News` - scope published, featured, category; computed excerpt, image_url
- `Gallery` - scope published, type, category
- `ProductCategory` - relationship ke products
- `Product` - computed formatted_price, has_discount, in_stock, whatsapp_link

**Controllers (3):**
- `NewsController` - index (paginasi 9), show (increment views, related)
- `GalleryController` - index (filter category/type), show
- `ProductController` - index (search, filter, sort), show (related)

**Filament Resources (4):**
- `NewsResource` - Form dengan rich editor, table badge kategori
- `GalleryResource` - Form foto/video, filter
- `ProductResource` - Form lengkap (harga, stok, diskon, WhatsApp)
- `ProductCategoryResource` - Form kategori

**Frontend Views (6):**
- `news/index.blade.php` - Grid berita dengan kartu
- `news/show.blade.php` - Detail + share button + related
- `galeri/index.blade.php` - Masonry grid + filter
- `galeri/show.blade.php` - Detail galeri
- `produk/index.blade.php` - Search/filter/sort + kartu produk
- `produk/show.blade.php` - Detail + WhatsApp order

**Seeder:**
- 3 berita contoh
- 3 galeri contoh
- 4 kategori produk (Kuliner, Kerajinan, Pertanian, Peternakan)
- 4 produk contoh (Kopi Gayo, Keripik Pisang, Tikar Anyaman, Beras Organik)

**Issues yang dihadapi:**
1. Migration order conflict (products before product_categories) → rename files
2. Permission denied pada file baru → chown ke www:www lalu ubuntu:ubuntu
3. `$villageInfo` undefined di views → buat ViewComposer di AppServiceProvider
4. Heredoc write issues → pakai Python script atau sudo tee

---

## 🔧 Environment Details

### Server
- **OS:** Ubuntu 24.04 LTS
- **RAM:** 2GB
- **CPU:** 2 core
- **Panel:** aaPanel
- **Web Server:** Nginx
- **PHP:** 8.1.32 (FPM)
- **Database:** MariaDB
- **Composer:** 2.10.0

### Domain & SSL
- **Domain:** bumdes.ondesa.id
- **SSL:** Self-signed via aaPanel
- **CDN:** Cloudflare (SSL mode: Full)
- **Nginx Config:** /www/server/panel/vhost/nginx/bumdes.ondesa.id.conf

### Credentials
- **aaPanel:** username `bfejhgin`
- **BUMDes Admin:** admin@bumdes.id
- **Database:** root / (empty)
- **OpenSID DB:** data_coba / (separate)

---

## 📁 File Structure Reference

```
/www/wwwroot/bumdes/           # Project root
├── app/
│   ├── Filament/Admin/        # Admin panel
│   │   ├── Resources/         # CRUD resources
│   │   └── Widgets/           # Dashboard widgets
│   ├── Http/Controllers/      # Web controllers
│   ├── Models/                # Eloquent models
│   └── Providers/             # Service providers
├── database/
│   ├── migrations/            # Database schema
│   └── seeders/               # Sample data
├── resources/views/           # Blade templates
│   ├── layouts/               # Master layout
│   ├── news/                  # Berita pages
│   ├── galeri/                # Galeri pages
│   ├── produk/                # Produk pages
│   └── village-info/          # Info desa pages
├── routes/web.php             # Web routes
└── referensi-opensid.md       # OpenSID reference
```

---

## 🎯 Keputusan Teknis

1. **Laravel + Filament** dipilih karena:
   - RBAC bawaan Filament
   - Form builder yang powerful
   - Responsive admin panel
   - Community support besar

2. **Tailwind CSS** untuk frontend:
   - Utility-first, cepat development
   - Bundle size kecil
   - Mobile-first mudah

3. **MariaDB** over PostgreSQL:
   - Sudah terinstall di server (OpenSID)
   - Kompatibel dengan MySQL
   - Lebih ringan untuk VPS kecil

4. **View Composer** untuk `$villageInfo`:
   - Share data desa ke semua view otomatis
   - Tidak perlu pass manual di setiap controller

5. **WhatsApp Order** dipilih:
   - Lebih umum di Indonesia
   - Tidak perlu integrasi payment gateway
   - User sudah familiar

---

## ⚠️ Known Issues

1. **Cloudflare Cache:** Kadang serving stale 404 untuk Livewire assets
   - Solusi: User harus purge cache di Cloudflare dashboard

2. **File Permissions:** File baru sering permission denied
   - Solusi: `sudo chown -R ubuntu:ubuntu /www/wwwroot/bumdes/`

3. **PHP putenv()** disabled by default
   - Solusi: Edit `/etc/php/8.1/fpm/php.ini` → enable putenv()

---

## 📊 Database Tables

| Table | Kolom | Deskripsi |
|-------|-------|-----------|
| village_info | 130+ | Data lengkap desa |
| news | 13 | Berita/artikel |
| galleries | 11 | Galeri foto/video |
| product_categories | 8 | Kategori produk |
| products | 22 | Produk UMKM |
| users | - | Pengguna admin (Laravel default) |
