![Laravel](https://img.shields.io/badge/Laravel-10-FF2D20?style=flat&logo=laravel&logoColor=white)
![Filament](https://img.shields.io/badge/Filament-3-FFCC00?style=flat&logo=filament&logoColor=black)
![PHP](https://img.shields.io/badge/PHP-8.1-777BB4?style=flat&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MariaDB-10-003545?style=flat&logo=mariadb&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind-3-06B6D4?style=flat&logo=tailwindcss&logoColor=white)
![PWA](https://img.shields.io/badge/PWA-Ready-5A0FC8?style=flat&logo=pwa&logoColor=white)
![SAK ETAP](https://img.shields.io/badge/SAK--ETAP-Compliant-4CAF50?style=flat)
![PP 11/2021](https://img.shields.io/badge/PP%2011%2F2021-Compliant-2196F3?style=flat)
![License](https://img.shields.io/badge/License-MIT-blue.svg)

---

# 🏢 BUMDes Management System v2.0

> **"Input Sederhana oleh Manusia, Perhitungan Kompleks oleh Sistem"**

Sistem manajemen keuangan BUMDes berbasis web dengan **akuntansi double-entry** sesuai standar **SAK ETAP** dan **PP 11/2021**. Desa fokus input transaksi sederhana, sistem hitung jurnal, buku besar, dan laporan otomatis.

🌐 **Live Demo:** [bumdes.ondesa.id](https://bumdes.ondesa.id)

---

## 📸 Screenshots

### 🏠 Halaman Utama (Beranda)
```
┌─────────────────────────────────────────────────────────────────┐
│  [Logo]  BUMDes Karya Mekar            Berita  Profil  Unit ▼  │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│            🏢 BUM DESA KARYA MEKAR KARANGMEKAR                 │
│           Badan Usaha Milik Desa Karangmekar                    │
│                                                                 │
│           [📊 Tentang Kami]  [📰 Berita Terkini]               │
│                                                                 │
├─────────────────────────────────────────────────────────────────┤
│  📊 Tentang BUMDes                                              │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐          │
│  │ 4 Unit   │ │ Rp 1.2M  │ │ 15+      │ │ SAK ETAP │          │
│  │ Usaha    │ │ Aset     │ │ Produk   │ │ Standar  │          │
│  └──────────┘ └──────────┘ └──────────┘ └──────────┘          │
│                                                                 │
│  🏢 Unit Usaha                                                  │
│  ┌────────────┐ ┌────────────┐ ┌────────────┐                  │
│  │ 🌾 Pertanian│ │ 🗑️ Sampah  │ │ 🏪 Retail  │                  │
│  └────────────┘ └────────────┘ └────────────┘                  │
│                                                                 │
│  👥 Pelaksana Operasional BUMDes                                │
│  ┌──────┐ ┌──────┐ ┌──────┐ ┌──────┐ ┌──────┐                │
│  │ Direkt│ │ Sekre│ │ Bend │ │ Peng1│ │ Peng2│                │
│  │ foto  │ │ taris│ │ ahara│ │ awas │ │ awas │                │
│  └──────┘ └──────┘ └──────┘ └──────┘ └──────┘                 │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### 📊 Dashboard Admin (Filament)
```
┌─────────────────────────────────────────────────────────────────┐
│  [Logo]  BUMDes Karya Mekar                     🔔 👤 Admin    │
├────────────┬────────────────────────────────────────────────────┤
│ 📊 Dashboard│   Dashboard Keuangan                              │
│ ⚙️ Master   │   ┌─────────────┬─────────────┬─────────────┐    │
│  Data       │   │ 💰 Saldo    │ 📈 Aset     │ 📉 Bulan    │    │
│ 💰 Keuangan │   │ Rp 1.234.567│ Rp 567.890  │ Rp 123.456  │    │
│ 📊 Laporan  │   └─────────────┴─────────────┴─────────────┘    │
│ 🏢 Manajemen│                                                  │
│ 📰 Konten   │   [Chart Pemasukan/Pengeluaran Bulanan]          │
│ 👤 User     │   ┌─────────────────────────────────────────┐    │
│ ⚙️ Pengaturan│   │ Jan Feb Mar Apr Mei Jun Jul Aug Sep Oct │    │
│ 🔧 Sistem   │   │ ██  ██  ██  ██  ██  ░░  ░░  ░░  ░░  ░░ │    │
│             │   └─────────────────────────────────────────┘    │
│             │                                                  │
│             │   [Donut Chart Kategori]  [Top 5 Pengeluaran]    │
│             │                                                  │
└────────────┴────────────────────────────────────────────────────┘
```

### 📝 Input Transaksi (Mobile-First)
```
┌─────────────────────┐
│ ≡  Input Transaksi  │
├─────────────────────┤
│                     │
│ Jenis Transaksi *   │
│ ┌─────────────────┐ │
│ │ 📥 Pemasukan  ▼ │ │
│ └─────────────────┘ │
│                     │
│ Kategori *          │
│ ┌─────────────────┐ │
│ │ Tiket Wisata  ▼ │ │
│ └─────────────────┘ │
│                     │
│ Tanggal *           │
│ ┌─────────────────┐ │
│ │ 01/06/2025    📅│ │
│ └─────────────────┘ │
│                     │
│ Jumlah (Rp) *       │
│ ┌─────────────────┐ │
│ │ 500.000         │ │
│ └─────────────────┘ │
│                     │
│ Keterangan          │
│ ┌─────────────────┐ │
│ │ Penjualan tiket │ │
│ │ hari ini        │ │
│ └─────────────────┘ │
│                     │
│ 📎 Upload Bukti     │
│ ┌─────────────────┐ │
│ │ Pilih File...   │ │
│ └─────────────────┘ │
│                     │
│ [ 💾 Simpan ]       │
│                     │
└─────────────────────┘
```

### 📊 Contoh PDF yang Dihasilkan
```
┌─────────────────────────────────────────┐
│                                         │
│  ┌─────┐                                │
│  │Logo │  PEMERINTAH KABUPATEN TASIK... │
│  └─────┘  BUM DESA KARYA MEKAR         │
│           Desa Karangmekar, Kec. ...    │
│  ─────────────────────────────────────  │
│                                         │
│        NERACA SALDO                     │
│         Juni 2025                       │
│                                         │
│  ┌─────────────────────────────────┐    │
│  │ Kode │ Nama Akun    │ D    │ K  │    │
│  ├─────────────────────────────────┤    │
│  │ 1101 │ Kas          │ 500K │    │    │
│  │ 1201 │ Aset Tetap   │ 400K │    │    │
│  │ 2101 │ Utang Usaha  │      │ 50K│    │
│  │ 3101 │ Modal Disetor│      │ 400K│   │
│  │ 4101 │ Pendapatan   │      │ 200K│   │
│  │ 5101 │ Beban Gaji   │ 100K │    │    │
│  ├─────────────────────────────────┤    │
│  │ TOTAL│              │1000K │650K│    │
│  └─────────────────────────────────┘    │
│                                         │
│  ✅ SEIMBANG                            │
│                                         │
│  Karangmekar, 01 Juni 2025             │
│                                         │
│  ┌──────────┐ ┌──────────┐ ┌────────┐  │
│  │ Direktur │ │Sekretaris│ │Bendahara│ │
│  │    ttd   │ │    ttd   │ │   ttd  │  │
│  └──────────┘ └──────────┘ └────────┘  │
│                                         │
│  ┌───────────────┐ ┌───────────────┐    │
│  │ Mengetahui,   │ │ Mengetahui,   │    │
│  │  Pengawas     │ │ Kepala Desa   │    │
│  └───────────────┘ └───────────────┘    │
│                                         │
└─────────────────────────────────────────┘
```

---

## ✨ Fitur Utama

### 🏠 Frontend (Publik)
- ✅ **Homepage** — Hero section, about, unit usaha, pejabat, berita, kontak
- ✅ **Profil Desa** — Visi misi, struktur organisasi, kontak
- ✅ **Berita** — CRUD berita dengan featured image
- ✅ **Galeri** — Album foto dengan lightbox
- ✅ **Produk** — Katalog produk UMKM
- ✅ **Unit Usaha** — Halaman per unit dengan produk
- ✅ **Custom Theme** — Warna dinamis dari pengaturan BUMDes
- ✅ **PWA** — Install sebagai app, mode offline
- ✅ **Mobile-First** — Responsive di semua device

### 💰 Sistem Keuangan (SAK ETAP + PP 11/2021)
- ✅ **Double-Entry Accounting** — Setiap transaksi = jurnal debit+kredit
- ✅ **Input Sederhana** — Form biasa, tanpa istilah akuntansi
- ✅ **Auto Jurnal** — Sistem generate jurnal otomatis
- ✅ **Buku Besar (GL)** — Per akun dengan saldo berjalan
- ✅ **Neraca Saldo** — Trial balance + status seimbang
- ✅ **Laba/Rugi** — Income statement per periode
- ✅ **Neraca** — Balance sheet: Aset = Kewajiban + Ekuitas
- ✅ **Arus Kas** — Cash flow statement
- ✅ **Input Jurnal Manual** — Untuk penyesuaian
- ✅ **Void Transaksi** — Dengan reversing entry (audit trail)
- ✅ **Tutup Buku** — Year-end closing wizard 5 langkah
- ✅ **Budget per Kategori** — Tracking + alert threshold
- ✅ **Depresiasi Aset** — Straight-line otomatis per bulan
- ✅ **Transfer Antar Unit** — RAK + konfirmasi penerima
- ✅ **Konsolidasi** — Laporan gabungan pusat + unit

### 📄 Laporan PDF (Baru!)
- ✅ **Neraca Saldo PDF** — Kop surat + tabel + tanda tangan
- ✅ **Laba/Rugi PDF** — Format profesional
- ✅ **Neraca PDF** — Aset = Kewajiban + Ekuitas
- ✅ **Jurnal Umum PDF** — Semua transaksi
- ✅ **Buku Besar PDF** — Per akun + saldo berjalan
- ✅ **Filter Bulanan** — Per bulan atau tahunan
- ✅ **Kop Surat** — Logo + teks dari pengaturan BUMDes
- ✅ **Tanda Tangan** — 5 pejabat (Pengawas, Penasihat, Direktur, Sekretaris, Bendahara)

### 📊 Excel Templates
- ✅ **Jurnal Umum** — Input per bulan, auto-sort tanggal
- ✅ **Realisasi Anggaran** — 3 kolom: Realisasi, Anggaran, Selisih
- ✅ **CAT (Cash Analysis Tool)** — Arus kas detail 7 kolom
- ✅ **Template Otomatis** — Kop surat + logo dari pengaturan
- ✅ **Dropdown + VLOOKUP** — Kode akun terkunci, nama auto-fill
- ✅ **Petunjuk Pengisian** — 28 kode COA + contoh per template

### ⚙️ Pengaturan BUMDes
- ✅ **Identitas BUMDes** — Nama, alamat, kontak, NPWP, motto
- ✅ **Pejabat BUMDes** — PP 11/2021: Penasihat, Direktur, Sekretaris, Bendahara, Pengawas
- ✅ **Foto per Pejabat** — Upload foto per posisi jabatan
- ✅ **Rekening Bank** — Nama bank, no rek, atas nama
- ✅ **Custom Theme** — Warna primer/sekunder, logo, CSS kustom
- ✅ **Kop Surat** — 3 baris teks + logo untuk PDF
- ✅ **Dark Mode** — Toggle dark/light mode

### 🔧 Sistem
- ✅ **User Management** — CRUD user dengan avatar
- ✅ **Role & Akses** — 5 role dengan permission JSON
- ✅ **Audit Log** — Jejak semua perubahan data
- ✅ **Backup & Restore** — Database backup manual
- ✅ **System Info** — Server info, PHP info, storage
- ✅ **Error Log Viewer** — Lihat log error aplikasi
- ✅ **Auto-Sync** — Git post-commit hook ke GitHub

---

## 🏗️ Arsitektur

### Struktur Database
```
┌─────────────────┐     ┌──────────────────┐     ┌─────────────────┐
│  transactions   │────▶│  journal_entries  │◀────│    accounts     │
│                 │     │                  │     │   (30 COA)     │
│ id              │     │ id               │     │                 │
│ transaction_no  │     │ transaction_id   │     │ code            │
│ transaction_date│     │ entry_date       │     │ name            │
│ unit_id         │     │ account_code     │     │ type            │
│ type            │     │ debit            │     │ normal_balance  │
│ category_id     │     │ credit           │     │ is_active       │
│ amount          │     │ unit_id          │     └─────────────────┘
│ description     │     │ description      │
│ fiscal_year     │     │ fiscal_year      │     ┌─────────────────┐
│ is_void         │     └──────────────────┘     │   categories    │
└─────────────────┘                               │                 │
                                                  │ id              │
┌─────────────────┐     ┌──────────────────┐     │ name            │
│ bumdes_settings │     │ business_units   │     │ type            │
│                 │     │                  │     │ default_account │
│ bumdes_name     │     │ id               │     └─────────────────┘
│ logo_path       │     │ name             │
│ primary_color   │     │ manager          │     ┌─────────────────┐
│ direktur_name   │     │ manager_nip      │     │ fiscal_years    │
│ direktur_photo  │     │ code             │     │                 │
│ bendahara_name  │     │ parent_unit_id   │     │ id              │
│ bendahara_photo │     └──────────────────┘     │ year            │
│ ...             │                               │ status          │
└─────────────────┘                               └─────────────────┘
```

### COA Default (SAK ETAP)
```
1xxx  ASET
├── 1101  Kas
├── 1102  Bank
├── 1201  Aset Tetap
├── 1202  Akumulasi Depresiasi
└── 1301  Piutang Usaha

2xxx  KEWAJIBAN
├── 2101  Utang Usaha
└── 2201  Utang Bank

3xxx  EKUITAS
├── 3101  Modal Disetor
└── 3201  Laba Ditahan

4xxx  PENDAPATAN
├── 4101  Pendapatan Operasional
└── 4201  Pendapatan Non-Operasional

5xxx  BEBAN / COS
├── 5101  Beban Gaji
├── 5201  Beban Operasional
└── 5301  Beban Perlengkapan

6xxx  BEBAN LAIN
├── 6101  Beban Depresiasi
└── 6201  Beban Bunga
```

### Alur Transaksi (Double-Entry)
```
  Input User          Sistem                  Database
  ──────────          ──────                  ────────
  
  [Pemasukan]    ──▶  Auto Journal       ──▶  journal_entries:
  Rp 500.000          DR Kas (1101) 500K       DR 1101  500.000
  Tiket Wisata        CR Pendapatan (4101)     CR 4101  500.000
  
  [Pengeluaran]  ──▶  Auto Journal       ──▶  journal_entries:
  Rp 100.000          DR Beban (5101) 100K     DR 5101  100.000
  Beli Pupuk          CR Kas (1101) 100K       CR 1101  100.000
  
  [Beli Aset]    ──▶  Auto Journal       ──▶  journal_entries:
  Rp 5.000.000        DR Aset (1201) 5000K     DR 1201  5.000.000
  Sepeda Motor        CR Kas (1101) 5000K      CR 1101  5.000.000
  
  [Void]         ──▶  Reversing Entry    ──▶  journal_entries:
                      DR/CR kebalikan          Semua dibalik
                      + is_void = true         is_void = true
```

---

## 🚀 Instalasi

### Prerequisites
- PHP 8.1+
- MariaDB 10.6+
- Composer 2.x
- aaPanel (opsional)

### Langkah Instalasi

```bash
# 1. Clone repository
git clone https://github.com/mrobish/website-bumdes.git
cd website-bumdes

# 2. Install dependencies
composer install

# 3. Copy .env
cp .env.example .env

# 4. Generate key
php artisan key:generate

# 5. Edit .env - sesuaikan database
nano .env

# 6. Jalankan migrasi + seeder
php artisan migrate --seed

# 7. Buat symbolic link storage
php artisan storage:link

# 8. Set permissions
chmod -R 775 storage bootstrap/cache
chown -R www:www storage bootstrap/cache

# 9. Seed data default
php artisan db:seed --class=AccountSeeder
php artisan db:seed --class=CategorySeeder
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=BumdesSettingSeeder
php artisan db:seed --class=FinancialTemplateSeeder

# 10. Clear cache
php artisan optimize:clear
```

### Login Default
| Role | Email | Password |
|------|-------|----------|
| Super Admin | admin@bumdes.id | password |
| Bendahara | bendahara@bumdeskeude.id | password |
| Operator | operator@bumdeskeude.id | password |
| Viewer | viewer@bumdeskeude.id | password |

---

## 📁 Struktur Folder

```
bumdes/
├── app/
│   ├── Filament/
│   │   └── Admin/
│   │       ├── Pages/          # Dashboard, Laporan, Settings
│   │       ├── Resources/      # CRUD Master Data
│   │       └── Widgets/        # Dashboard widgets
│   ├── Http/Controllers/       # Web controllers
│   ├── Models/                 # Eloquent models
│   └── Services/               # Business logic
│       ├── AutoJournalService  # Double-entry engine
│       ├── DepreciationService # Aset depresiasi
│       ├── PdfReportService    # Generate PDF
│       └── FinancialTemplateService # Excel templates
├── database/
│   ├── migrations/             # Database schema
│   └── seeders/                # Default data
├── resources/views/
│   ├── filament/pages/         # Filament page views
│   ├── pdf/                    # PDF templates
│   └── welcome.blade.php       # Homepage
├── storage/app/templates/      # Excel template files
└── public/
    └── storage/                # Uploaded files (symlink)
```

---

## 📊 Progress Pengembangan

### ✅ Selesai (100%)
| Modul | Status |
|-------|--------|
| Homepage & Frontend | ✅ 100% |
| Profil Desa | ✅ 100% |
| Berita & Galeri | ✅ 100% |
| Produk & Unit Usaha | ✅ 100% |
| PWA & Offline Mode | ✅ 100% |
| Custom Theme | ✅ 100% |
| Identitas BUMDes | ✅ 100% |
| Pejabat BUMDes (PP 11/2021) | ✅ 100% |
| User Management | ✅ 100% |
| Role & Permission | ✅ 100% |
| COA (Chart of Accounts) | ✅ 100% |
| Kategori Transaksi | ✅ 100% |
| Input Transaksi | ✅ 100% |
| Auto Journal (Double-Entry) | ✅ 100% |
| Buku Besar (GL) | ✅ 100% |
| Neraca Saldo | ✅ 100% |
| Laba/Rugi | ✅ 100% |
| Neraca | ✅ 100% |
| Arus Kas | ✅ 100% |
| Void & Reversing Entry | ✅ 100% |
| Tutup Buku (Year-End Closing) | ✅ 100% |
| Budget per Kategori | ✅ 100% |
| Depresiasi Aset | ✅ 100% |
| Transfer Antar Unit (RAK) | ✅ 100% |
| Konsolidasi Laporan | ✅ 100% |
| Excel Templates (3 template) | ✅ 100% |
| Import Excel | ✅ 100% |
| Dashboard Charts | ✅ 100% |
| Audit Log | ✅ 100% |
| Backup & Restore | ✅ 100% |
| PDF Reports (5 laporan) | ✅ 100% |
| Filter Bulanan | ✅ 100% |
| Logo & Branding | ✅ 100% |

### 🔜 Rencana Pengembangan (v2.1+)
| Fitur | Prioritas |
|-------|-----------|
| 📱 Telegram Bot | ⭐⭐⭐ |
| 🔗 RESTful API (20+ endpoints) | ⭐⭐⭐ |
| 🛒 Keranjang Belanja / Marketplace | ⭐⭐ |
| 🔐 RBAC per Unit Usaha | ⭐⭐ |
| 📊 Multi-Period Comparison | ⭐⭐ |
| 🔔 Notification System | ⭐ |
| 🏛️ Export ke SIPD | ⭐ |

---

## 🛠️ Tech Stack

| Layer | Technology |
|-------|------------|
| **Backend** | Laravel 10.50 |
| **Admin Panel** | Filament 3.3 |
| **Frontend** | Tailwind CSS 3.x, Alpine.js |
| **Database** | MariaDB 10.6 |
| **PDF** | DomPDF 3.x |
| **Excel** | PhpSpreadsheet 5.7 |
| **Charts** | Chart.js 4.x |
| **Maps** | Leaflet.js |
| **Animations** | AOS.js |
| **Server** | Ubuntu 24.04, Nginx, PHP 8.1 |
| **Panel** | aaPanel |
| **CDN** | Cloudflare |

---

## 📚 Dokumentasi

| Dokumen | Deskripsi |
|---------|-----------|
| [README-DEVELOPMENT.md](README-DEVELOPMENT.md) | Setup development & troubleshooting |
| [README-NEW-SYSTEM.md](README-NEW-SYSTEM.md) | Arsitektur sistem keuangan baru |
| [README-UPGRADE.md](README-UPGRADE.md) | Panduan upgrade dari sistem lama |
| [docs/ARCHITECTURE-DECISIONS.md](docs/ARCHITECTURE-DECISIONS.md) | Keputusan arsitektur (ADR) |
| [docs/DEVELOPMENT-LOG.md](docs/DEVELOPMENT-LOG.md) | Log pengembangan |
| [docs/CURRENT-STATE.md](docs/CURRENT-STATE.md) | Status terkini |
| [docs/DEPLOYMENT.md](docs/DEPLOYMENT.md) | Panduan deploy ke server |
| [docs/USER-MANUAL.md](docs/USER-MANUAL.md) | Manual pengguna |
| [bumdes-finance-prompt-v2.md](bumdes-finance-prompt-v2.md) | Spesifikasi sistem keuangan |

---

## 👥 Struktur Organisasi BUMDes (PP 11/2021)

```
                    ┌─────────────┐
                    │   MUSDES    │
                    │(Musyawarah  │
                    │   Desa)     │
                    └──────┬──────┘
                           │
              ┌────────────┼────────────┐
              │                         │
     ┌────────┴────────┐     ┌─────────┴─────────┐
     │   PENASIHAT     │     │    PENGAWAS        │
     │ (Kepala Desa    │     │ (BPD + Tokoh       │
     │  ex officio)    │     │  Masyarakat)       │
     └─────────────────┘     └─────────────────────┘

                    ┌─────────────┐
                    │  PELAKSANA  │
                    │ OPERASIONAL │
                    └──────┬──────┘
                           │
        ┌──────────┬───────┴───────┬──────────┐
        │          │               │          │
  ┌─────┴─────┐ ┌──┴───┐ ┌───────┴──┐ ┌─────┴─────┐
  │ DIREKTUR  │ │SEKRET│ │ BENDAHARA│ │ KEPALA    │
  │           │ │ARIS  │ │          │ │ UNIT USAHA│
  └───────────┘ └──────┘ └──────────┘ └───────────┘
```

---

## 🤝 Kontribusi

1. Fork repository
2. Buat branch baru (`git checkout -b feature/xxx`)
3. Commit perubahan (`git commit -m 'feat: add xxx'`)
4. Push ke branch (`git push origin feature/xxx`)
5. Buat Pull Request

---

## 📄 License

MIT License - Silakan gunakan dan modifikasi sesuai kebutuhan.

**© {{ YEAR }} mrobis** - [github.com/mrobis](https://github.com/mrobis)

---

## 🙏 Acknowledgments

- [Laravel](https://laravel.com/) - Framework PHP
- [Filament](https://filamentphp.com/) - Admin Panel
- [Tailwind CSS](https://tailwindcss.com/) - CSS Framework
- [SAK ETAP](https://iaiglobal.or.id/) - Standar Akuntansi
- [PP 11/2021](https://peraturan.bpk.go.id/) - Peraturan BUMDes

---

<div align="center">

**Dibuat dengan ❤️ untuk Kemajuan Desa**

**By [mrobis](https://github.com/mrobis)**

🌐 [bumdes.ondesa.id](https://bumdes.ondesa.id) | 📧 [info@bumdeskeudebakongan.id](mailto:info@bumdeskeudebakongan.id) | 📱 [WhatsApp](https://wa.me/6285157520432)

</div>
