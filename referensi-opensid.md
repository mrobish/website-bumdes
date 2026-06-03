# Referensi OpenSID untuk BUMDes Website

## 📊 Struktur Database OpenSID (data_coba)

### Tabel Utama: `config`
Data identitas desa:
- `nama_desa` - Nama desa (contoh: "Keude Bakongan")
- `kode_desa` - Kode desa (contoh: "1101012001")
- `kode_desa_bps` - Kode BPS (contoh: "1103020002")
- `kode_pos` - Kode pos (contoh: 77777)
- `nama_kecamatan` - Nama kecamatan
- `kode_kecamatan` - Kode kecamatan
- `nama_kepala_camat` - Nama camat
- `nip_kepala_camat` - NIP camat
- `nama_kabupaten` - Nama kabupaten
- `kode_kabupaten` - Kode kabupaten
- `nama_propinsi` - Nama provinsi
- `kode_propinsi` - Kode provinsi
- `logo` - Logo desa
- `lat`, `lng`, `zoom` - Koordinat peta
- `alamat_kantor` - Alamat kantor desa
- `email_desa` - Email desa
- `telepon` - Telepon
- `website` - Website
- `warna` - Warna tema
- `nama_kontak`, `hp_kontak`, `jabatan_kontak` - Kontak person

### Tabel: `profil_desa`
Data profil lengkap desa (key-value):
- **Ekologi**: jenis_tanah, topografi, sumber_daya_alam, flora_fauna, rawan_bencana, kearifan_lokal
- **Internet**: jenis_jaringan, provider_internet, cakupan_wilayah, kecepatan_internet, akses_publik
- **Adat**: status_desa, lembaga_adat, struktur_adat, wilayah_adat, peraturan_adat

### Tabel: `artikel`
Berita/ Artikel:
- `judul`, `isi`, `gambar`, `slug`
- `id_kategori`, `headline`, `slider`
- `tipe` (dinamis, statis)
- `hit` (views), `enabled`

### Tabel: `keuangan`
Keuangan desa:
- `template_uuid`, `tahun`
- `anggaran`, `realisasi`

### Tabel: `inventaris_asset`
Inventaris/ Aset desa:
- `nama_barang`, `kode_barang`, `register`
- `jenis`, `jumlah`, `tahun_pengadaan`
- `asal`, `harga`, `keterangan`
- `status`, `visible`

---

## 🎨 Desain Frontend OpenSID

### Technology Stack:
- **CSS Framework**: Bootstrap 3.3.7
- **Admin Panel**: AdminLTE
- **Icons**: Font Awesome, Ionicons
- **Maps**: Leaflet + OpenStreetMap
- **Charts**: Highcharts
- **Tables**: DataTables
- **Forms**: Select2, Bootstrap Datepicker, Colorpicker
- **Gallery**: Swiper, Colorbox
- **Alerts**: SweetAlert2, Toastr

### Halaman Frontend:
1. **Beranda** - Slider berita, statistik, widget
2. **Profil Desa** - Data umum, geografis, demografis
3. **Berita/Artikel** - Daftar berita dengan kategori
4. **Galeri** - Foto dan video
5. **Layanan** - Surat, pengaduan
6. **Kontak** - Form kontak, peta lokasi

---

## 📋 Data yang Harus Diisi untuk BUMDes

### 1. Data Umum Desa
- Nama Desa, Kode Desa, Kode Pos
- Nama Kecamatan, Kabupaten, Provinsi
- Alamat Lengkap Kantor Desa
- Email, Telepon, Website
- Logo Desa

### 2. Data Pemerintahan
- Nama Kepala Desa, NIP
- Nama Sekretaris Desa
- Nama Bendahara Desa
- Nama Kaur Keuangan
- Nama Kaur Umum
- Struktur Organisasi

### 3. Data Geografis
- Luas Wilayah (hektar)
- Batas Utara, Selatan, Timur, Barat
- Ketinggian dari permukaan laut
- Topografi (datar, berbukit, pegunungan)
- Jenis Tanah
- Iklim / Curah Hujan

### 4. Data Demografis
- Jumlah Penduduk (Laki-laki, Perempuan)
- Jumlah KK (Kepala Keluarga)
- Jumlah Dusun/ Dukuh
- Jumlah RT/ RW
- Kepadatan Penduduk

### 5. Data Ekonomi
- Mata Pencaharian Utama
- Jumlah Unit Usaha
- Jumlah UMKM/ IKM
- Rata-rata Penghasilan
- Potensi Ekonomi Desa

### 6. Data Sosial
- Jumlah Penduduk Miskin
- Jumlah Lansia
- Jumlah Anak Yatim
- Program Bantuan Sosial
- Kemiskinan

### 7. Data Infrastruktur
- Jalan (Kondisi: Baik/Rusak)
- Jembatan
- Saluran Air
- Listrik (Cakupan)
- Air Bersih (Cakupan)
- Telekomunikasi (Sinyal HP, Internet)

### 8. Data Keuangan Desa
- APBDes (Anggaran)
- Realisasi Anggaran
- Sumber Pendapatan (Dana Desa, PAD, dll)
- Belanja (Operasional, Pembangunan, Bantuan)

### 9. Data BUMDes
- Nama BUMDes
- Tahun Berdiri
- Akta Pendirian
- Nama Pengurus
- Modal Awal
- Jenis Usaha
- Omzet Bulanan
- Jumlah Karyawan

### 10. Data Potensi Desa
- Potensi Alam (Pertanian, Perkebunan, Perikanan, Kehutanan)
- Potensi Pariwisata
- Potensi Budaya
- Potensi Sumber Daya Manusia

### 11. Data Media
- Logo Desa (PNG/JPG)
- Foto Kantor
- Foto Kepala Desa
- Foto Pemandangan/Alam
- Video Profil Desa

### 12. Data Kontak
- Alamat Lengkap
- Telepon/ HP
- Email
- WhatsApp
- Facebook
- Instagram
- YouTube
- TikTok

### 13. Data SEO
- Meta Title
- Meta Description
- Meta Keywords
- URL Website
- Google Analytics ID

---

## 🔧 Template Form Admin (Filament)

### Tab 1: Data Umum
- Nama Desa (text, required)
- Kode Desa (text)
- Kode Desa BPS (text)
- Kode Pos (text)
- Nama Kecamatan (text, required)
- Kode Kecamatan (text)
- Nama Kabupaten (text, required)
- Kode Kabupaten (text)
- Nama Provinsi (text, required)
- Kode Provinsi (text)
- Alamat Kantor (textarea)
- Email (email)
- Telepon (text)
- Website (url)
- Logo (file upload)

### Tab 2: Data Pemerintahan
- Nama Kepala Desa (text)
- NIP Kepala Desa (text)
- Nama Sekretaris (text)
- Nama Bendahara (text)
- Nama Kaur Keuangan (text)
- Nama Kaur Umum (text)
- Struktur Organisasi (file upload)
- Visi Desa (textarea)
- Misi Desa (textarea)

### Tab 3: Data Geografis
- Luas Wilayah (number, hectares)
- Batas Utara (text)
- Batas Selatan (text)
- Batas Timur (text)
- Batas Barat (text)
- Ketinggian (number, mdpl)
- Topografi (select: Datar/Berbukit/Pegunungan)
- Jenis Tanah (text)
- Curah Hujan (text)
- Suhu Rata-rata (text)

### Tab 4: Data Demografis
- Jumlah Penduduk Laki-laki (number)
- Jumlah Penduduk Perempuan (number)
- Jumlah KK (number)
- Jumlah Dusun (number)
- Jumlah RT (number)
- Jumlah RW (number)

### Tab 5: Data Ekonomi
- Mata Pencaharian Utama (text)
- Jumlah Unit Usaha (number)
- Jumlah UMKM (number)
- Rata-rata Penghasilan (number)
- Potensi Ekonomi (textarea)

### Tab 6: Data Sosial
- Jumlah Miskin (number)
- Jumlah Lansia (number)
- Jumlah Yatim (number)
- Program Bantuan (textarea)

### Tab 7: Data Infrastruktur
- Kondisi Jalan (textarea)
- Jumlah Jembatan (number)
- Cakupan Listrik (text, %)
- Cakupan Air Bersih (text, %)
- Sinyal HP (text)
- Internet (text)

### Tab 8: Data Keuangan
- APBDes (number)
- Realisasi (number)
- Sumber Pendapatan (textarea)
- Belanja (textarea)

### Tab 9: Data BUMDes
- Nama BUMDes (text)
- Tahun Berdiri (year)
- Akta Pendirian (text)
- Nama Pengurus (textarea)
- Modal Awal (number)
- Jenis Usaha (textarea)
- Omzet Bulanan (number)
- Jumlah Karyawan (number)

### Tab 10: Data Potensi
- Potensi Alam (textarea)
- Potensi Pariwisata (textarea)
- Potensi Budaya (textarea)
- Potensi SDM (textarea)

### Tab 11: Data Media & SEO
- Foto Kantor (file)
- Foto Kepala Desa (file)
- Foto Pemandangan (file)
- Video Profil (text, YouTube URL)
- Meta Title (text)
- Meta Description (textarea)
- Meta Keywords (text)
- Google Analytics (text)

### Tab 12: Data Kontak & Sosial
- WhatsApp (text)
- Facebook (text)
- Instagram (text)
- YouTube (text)
- TikTok (text)

### Tab 13: Pengaturan
- Aktifkan Website (toggle)
- Tampilkan Statistik (toggle)
- Tampilkan Peta (toggle)
- Tampilkan Berita (toggle)
- Tampilkan Galeri (toggle)

---

## 🎯 Best Practices dari OpenSID

### 1. Database Design
- Gunakan **key-value** untuk data fleksibel (profil_desa)
- Pisahkan data **identitas** (config) dari **profil** (profil_desa)
- Gunakan **relasi** untuk data terstruktur (artikel, keuangan)

### 2. Frontend Design
- **Mobile-first** design
- **Fast loading** - optimasi gambar, lazy load
- **Accessible** - WCAG compliant
- **SEO friendly** - meta tags, structured data

### 3. Admin Panel
- **Role-based access** (Admin, Editor, Viewer)
- **Form validation** server-side & client-side
- **File upload** with preview
- **WYSIWYG editor** for rich text

### 4. Security
- **CSRF protection** (Laravel built-in)
- **XSS prevention** (escape output)
- **SQL injection prevention** (Eloquent ORM)
- **File upload validation** (type, size)
- **Rate limiting** for API

### 5. Performance
- **Query optimization** (eager loading)
- **Caching** (Redis/Memcached)
- **CDN** for assets
- **Image optimization** (WebP, lazy load)
- **Minification** (CSS, JS)
