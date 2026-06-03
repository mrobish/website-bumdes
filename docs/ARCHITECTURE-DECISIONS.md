# 🏗️ Architecture Decision Records

Catatan keputusan arsitektur yang diambil selama pengembangan.

## ADR-001: Laravel + Filament vs WordPress

**Status:** Accepted

**Konteks:**
- Butuh website untuk BUMDes dengan RBAC, form management, dan customisasi tinggi
- OpenSID sudah ada tapi untuk pemerintahan desa, bukan BUMDes

**Keputusan:**
- Laravel 10 + Filament 3 dipilih

**Alasan:**
- RBAC bawaan Filament (Admin, Editor, Viewer)
- Form builder powerful dengan validasi
- Responsive admin panel
- Lebih secure dari WordPress
- Performance lebih baik di VPS kecil

**Konsekuensi:**
- Perlu custom development untuk semua fitur
- Learning curve lebih tinggi dari WordPress
- Maintenance lebih kompleks

---

## ADR-002: Database Terpisah dari OpenSID

**Status:** Accepted

**Konteks:**
- Server sudah menjalankan OpenSID dengan database `data_coba`
- BUMDes butuh database sendiri

**Keputusan:**
- Buat database baru `bumdes` di MariaDB yang sama

**Alasan:**
- Isolasi data antara pemerintahan desa dan BUMDes
- Backup/restore independen
- Performance tidak terganggu

**Konsekuensi:**
- Tidak ada integrasi langsung dengan OpenSID
- Perlu sync data manual jika diperlukan

---

## ADR-003: Tailwind CSS vs Bootstrap

**Status:** Accepted

**Konteks:**
- Frontend BUMDes perlu desain modern dan responsive

**Keputusan:**
- Tailwind CSS via CDN

**Alasan:**
- Utility-first, cepat development
- Bundle size lebih kecil
- Custom design lebih mudah
- Mobile-first approach

**Konsekuensi:**
- Tidak ada component library bawaan
- Perlu buat komponen sendiri

---

## ADR-004: WhatsApp Order vs Payment Gateway

**Status:** Accepted

**Konteks:**
- BUMDes butuh sistem pemesanan produk

**Keputusan:**
- Order via WhatsApp/Telegram, bukan payment gateway

**Alasan:**
- Lebih umum di Indonesia rural area
- Tidak perlu integrasi payment gateway
- User sudah familiar
- Transaksi offline/cash masih dominan

**Konsekuensi:**
- Tidak ada pembayaran online
- Order perlu konfirmasi manual
- Tidak ada automated shipping

---

## ADR-005: View Composer untuk Global Data

**Status:** Accepted

**Konteks:**
- `$villageInfo` dibutuhkan di semua view (layout, footer, dll)

**Keputusan:**
- Gunakan View Composer di AppServiceProvider

**Alasan:**
- Otomatis share data ke semua view
- Tidak perlu pass manual di setiap controller
- Lebih clean dan maintainable

**Konsekuensi:**
- Query ke database setiap request (perlu cache)
- Semua view dapat akses ke villageInfo

---

## ADR-006: Nginx vs Apache

**Status:** Accepted

**Konteks:**
- Server 2GB RAM, perlu web server yang hemat memory

**Keputusan:**
- Nginx dipilih

**Alasan:**
- Event-driven, lebih hemat memory
- Better performance untuk static files
- Reverse proxy yang baik untuk Cloudflare
- aaPanel support lebih baik

**Konsekuensi:**
- Konfigurasi berbeda dari Apache (.htaccess tidak bisa dipakai)
- Perlu konfigurasi ulang untuk rewrite rules

---

## ADR-007: Self-Signed SSL Certificate

**Status:** Accepted

**Konteks:**
- Website perlu HTTPS
- Cloudflare SSL mode "Full"

**Keputusan:**
- Gunakan self-signed certificate dari aaPanel

**Alasan:**
- Gratis
- Sudah terintegrasi dengan aaPanel
- Cloudflare handle SSL untuk user

**Konsekuensi:**
- Certificate warning jika akses langsung ke IP
- Perlu renew manual

---

## ADR-008: Seeder untuk Data Contoh

**Status:** Accepted

**Konteks:**
- Perlu data contoh untuk testing dan demo

**Keputusan:**
- Buat seeder dengan data contoh lengkap

**Alasan:**
- Mempercepat development
- Memudahkan testing
- Contoh realistis untuk user

**Konsekuensi:**
- Data contoh perlu dihapus/diupdate di production
- Seeder perlu diupdate saat schema berubah
