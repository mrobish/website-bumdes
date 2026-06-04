# 🏢 BUMDes Management System

> **Sistem Manajemen Keuangan BUMDes Berbasis Web**
> 
> *"Input Sederhana oleh Manusia, Perhitungan Kompleks oleh Sistem"*

[![Laravel](https://img.shields.io/badge/Laravel-10-FF2D20?style=flat&logo=laravel&logoColor=white)](https://laravel.com)
[![Filament](https://img.shields.io/badge/Filament-3-FFCC00?style=flat&logo=filament&logoColor=black)](https://filamentphp.com)
[![PHP](https://img.shields.io/badge/PHP-8.1-777BB4?style=flat&logo=php&logoColor=white)](https://php.net)
[![MariaDB](https://img.shields.io/badge/MariaDB-10-003545?style=flat&logo=mariadb&logoColor=white)](https://mariadb.org)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind-3-06B6D4?style=flat&logo=tailwindcss&logoColor=white)](https://tailwindcss.com)
[![License](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)

---

## ✨ Fitur Utama

### 💰 Akuntansi Double-Entry (SAK ETAP)
- Jurnal umum otomatis
- Buku besar
- Neraca saldo
- Laba/rugi
- Neraca (posisi keuangan)

### 📊 Laporan Keuangan (PDF & Excel)
- **5 Laporan PDF** profesional dengan kop surat
- **3 Template Excel** siap import
- Filter bulanan atau tahunan
- Export ke PDF/Excel

### 🏢 Multi Unit Usaha
- BUMDes Induk (Pusat)
- Unit usaha dengan RAK
- Konsolidasi otomatis
- Transfer antar unit

### 💸 Piutang & Hutang
- Piutang usaha (Accounts Receivable)
- Hutang usaha (Accounts Payable)
- Cicilan bertahap
- Lampiran dokumen (KTP, surat perjanjian)

### 📱 Mobile-First & PWA
- Responsif di semua device
- Install sebagai aplikasi
- Mode offline

### 🔐 Multi User & Role
- **5 Role:** Admin, Bendahara Pusat, Bendahara Unit, Manajer Unit, Pengawas
- Audit log lengkap
- Session timeout 30 menit

---

## 🚀 Instalasi Cepat

### Download & Install

```bash
# 1. Download package
wget https://github.com/mrobish/website-bumdes/releases/download/v2.0.0/bumdes-v2.0.0.zip

# 2. Extract ke folder web
unzip bumdes-v2.0.0.zip -d /var/www/bumdes/

# 3. Set permissions
chmod -R 755 storage bootstrap/cache

# 4. Buka browser → wizard otomatis!
```

### Persyaratan Sistem

| Komponen | Minimum | Rekomendasi |
|----------|---------|-------------|
| PHP | 8.1 | 8.1+ |
| Database | MySQL 5.7 / MariaDB 10.3 | MariaDB 10.6+ |
| RAM | 1 GB | 2 GB |
| Storage | 2 GB | 5 GB |

**PHP Extensions Wajib:**
```
openssl, pdo, pdo_mysql, mbstring, tokenizer, xml, 
ctype, json, bcmath, fileinfo, gd, curl, zip
```

📖 **Panduan lengkap:** [README-INSTALL.md](README-INSTALL.md)

---

## 📸 Screenshot

### Dashboard Admin
```
┌─────────────────────────────────────────────────────────────────┐
│  📊 Dashboard Keuangan BUMDes                                   │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐          │
│  │ 💰 Kas   │ │ 📈 Laba  │ │ 📉 Rugi  │ │ 🏦 Bank  │          │
│  │ Rp 50jt  │ │ Rp 25jt  │ │ Rp 10jt  │ │ Rp 100jt │          │
│  └──────────┘ └──────────┘ └──────────┘ └──────────┘          │
│                                                                 │
│  📊 Grafik Bulanan          📋 Transaksi Terakhir              │
│  ┌─────────────────┐       ┌─────────────────────────┐        │
│  │ ▆▆▆▆▆▆▆▆▆▆▆▆▆  │       │ 01/06 - Pemasukan Rp 5jt│        │
│  │ ▆▆▆▆▆▆▆▆▆▆▆▆▆  │       │ 02/06 - Pengeluaran 2jt │        │
│  └─────────────────┘       └─────────────────────────┘        │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### Input Transaksi (Mobile)
```
┌─────────────────────────┐
│  📝 Input Transaksi     │
├─────────────────────────┤
│                         │
│  Tanggal                │
│  ┌─────────────────┐   │
│  │ 04/06/2026      │   │
│  └─────────────────┘   │
│                         │
│  Tipe Transaksi         │
│  ┌─────────────────┐   │
│  │ Pemasukan    ▼  │   │
│  └─────────────────┘   │
│                         │
│  Jumlah (Rp)            │
│  ┌─────────────────┐   │
│  │ 5.000.000       │   │
│  └─────────────────┘   │
│                         │
│  Keterangan             │
│  ┌─────────────────┐   │
│  │ Sewa tempat     │   │
│  └─────────────────┘   │
│                         │
│  [💾 Simpan Transaksi]  │
│                         │
└─────────────────────────┘
```

---

## 🛠️ Tech Stack

| Teknologi | Versi | Kegunaan |
|-----------|-------|----------|
| **Laravel** | 10 | PHP Framework |
| **Filament** | 3 | Admin Panel |
| **PHP** | 8.1 | Backend |
| **MariaDB** | 10.6 | Database |
| **Tailwind CSS** | 3 | Styling |
| **Livewire** | 3 | Reactive UI |
| **DomPDF** | 2 | PDF Generation |
| **PhpSpreadsheet** | 1.29 | Excel Export |

---

## 📁 Struktur Folder

```
bumdes/
├── app/
│   ├── Http/Controllers/    # Controller
│   ├── Models/              # Eloquent Models
│   ├── Services/            # Business Logic
│   └── Filament/            # Admin Panel
├── database/
│   ├── migrations/          # Database Schema
│   └── seeders/             # Data Default
├── resources/views/         # Blade Templates
├── routes/                  # Route Definitions
├── storage/                 # Logs & Cache
└── public/                  # Public Assets
```

---

## 🔧 Konfigurasi

### Environment (.env)
```env
APP_NAME="BUMDes Management System"
APP_ENV=production
APP_KEY=base64:...
APP_DEBUG=false
APP_URL=https://bumdes.example.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bumdes
DB_USERNAME=bumdes_user
DB_PASSWORD=password
```

### Web Server (Nginx)
```nginx
server {
    listen 80;
    server_name bumdes.example.com;
    root /var/www/bumdes/public;
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

---

## 📊 Database Schema

### Tabel Utama
- `accounts` - Chart of Accounts (30 SAK ETAP)
- `transactions` - Transaksi keuangan
- `journal_entries` - Jurnal double-entry
- `piutangs` - Piutang usaha
- `hutangs` - Hutang usaha
- `budgets` - Anggaran per kategori
- `business_units` - Unit usaha
- `users` - Pengguna sistem

### Relasi
```
transactions (1) ──→ (N) journal_entries
accounts (1) ──→ (N) journal_entries
piutangs (1) ──→ (N) piutang_payments
hutangs (1) ──→ (N) hutang_payments
```

---

## 🎯 Roadmap

### ✅ Selesai (v2.0)
- [x] Akuntansi double-entry SAK ETAP
- [x] 5 Laporan PDF profesional
- [x] Piutang & Hutang
- [x] Multi unit usaha + konsolidasi
- [x] Multi user & role
- [x] Install wizard
- [x] Mobile-first PWA

### 🔜 Akan Datang (v2.1+)
- [ ] Telegram Bot notifications
- [ ] RESTful API (20+ endpoints)
- [ ] Marketplace produk BUMDes
- [ ] Payroll/Gaji karyawan
- [ ] Inventori/Stok barang
- [ ] Bank reconciliation
- [ ] Export ke SIPD Kemendagri

---

## 🤝 Kontribusi

1. Fork repository
2. Buat branch (`git checkout -b feature/xxx`)
3. Commit (`git commit -m 'feat: add xxx'`)
4. Push (`git push origin feature/xxx`)
5. Buat Pull Request

---

## 📄 License

**MIT License** - © 2026 mrobis

Bebas digunakan, dimodifikasi, dan didistribusikan.

---

## 🙏 Credits

Dibuat oleh **[mrobis](https://github.com/mrobish)**

- 🌐 **Website:** [bumdes.ondesa.id](https://bumdes.ondesa.id)
- 📧 **GitHub:** [github.com/mrobish](https://github.com/mrobish)
- 📦 **Download:** [Releases](https://github.com/mrobish/website-bumdes/releases)

---

<div align="center">

**Dibuat dengan ❤️ untuk Kemajuan Desa Indonesia**

![PHP](https://img.shields.io/badge/PHP-8.1-777BB4?style=flat&logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-10-FF2D20?style=flat&logo=laravel&logoColor=white)
![Filament](https://img.shields.io/badge/Filament-3-FFCC00?style=flat&logo=filament&logoColor=black)

</div>
