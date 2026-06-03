# Rencana Rombak Total Sistem Keuangan BUMDes

## Status: Phase 1 — Foundation

---

## Overview

Rombak total sistem keuangan dari model sederhana ke **double-entry accounting** lengkap sesuai SAK ETAP.

**Backup tersimpan:**
- Database: `/www/wwwroot/bumdes/backups/20260603/bumdes-20260603-2226.sql`
- Code: `/tmp/bumdes-code-backup-20260603-2226.tar.gz`

---

## Tabel yang DIHAPUS ( tidak dipakai lagi )

| Tabel Alasan | Alasan |
|---|---|
| `financial_transactions` | Diganti `transactions` + `journal_entries` |
| `chart_of_accounts` | Diganti `accounts` (lebih lengkap) |
| `inter_account_transfers` | Diganti `inter_unit_transfers` |
| `consolidated_reports` | Dihitung otomatis, tidak perlu tabel |
| `financial_report_templates` | Dihitung otomatis dari journal_entries |
| `financial_templates` | Template Excel lama |
| `financial_uploads` | Upload lama |
| `budgets` | Diganti `budgets` baru |
| `capital_contributions` | Diganti ke dalam `transactions` |
| `village_info` | Diganti `bumdes_settings` (sudah ada) |

---

## Tabel BARU yang dibuat

| Tabel | Fungsi |
|---|---|
| `fiscal_years` | Tahun fiskal (open/closed) |
| `accounts` | COA lengkap (1xxx-6xxx) |
| `categories` | Kategori pemasukan/pengeluaran |
| `category_account_overrides` | Override akun per unit |
| `transactions` | Semua transaksi (income/expense/transfer/etc) |
| `journal_entries` | Double-entry journal (debit/credit) |
| `assets` | Aset tetap (update dari lama) |
| `depreciation_logs` | Log depresiasi |
| `budgets` | Budget per kategori |
| `bank_reconciliations` | Reconcile bank |
| `audit_log` | Jejak audit |
| `inter_unit_transfers` | Transfer antar unit (RAK) |
| `settings` | Pengaturan sistem |

---

## Tabel yang DIPERTAHANKAN

| Tabel | Catatan |
|---|---|
| `users` | Update: tambah last_login_at, last_login_ip |
| `roles` | Pertahankan (5 role) |
| `business_units` | Rename jadi `units` |
| `bumdes_settings` | Pertahankan (identitas BUMDes) |
| `bumdes_structures` | Pertahankan (struktur kepengurusan) |
| `news` | Pertahankan (berita) |
| `galleries` | Pertahankan (galeri) |
| `products` | Pertahankan (produk) |
| `product_categories` | Pertahankan (kategori produk) |

---

## Phase 1 — Foundation (Sedang Dikerjakan)

### 1.1 Migration Baru
- [x] Backup database
- [x] Backup code
- [ ] Buat migration: `fiscal_years`
- [ ] Buat migration: `accounts` (ganti chart_of_accounts)
- [ ] Buat migration: `categories`
- [ ] Buat migration: `category_account_overrides`
- [ ] Buat migration: `transactions` (ganti financial_transactions)
- [ ] Buat migration: `journal_entries`
- [ ] Buat migration: `assets` (update)
- [ ] Buat migration: `depreciation_logs`
- [ ] Buat migration: `budgets` (baru)
- [ ] Buat migration: `bank_reconciliations`
- [ ] Buat migration: `audit_log`
- [ ] Buat migration: `inter_unit_transfers` (ganti inter_account_transfers)
- [ ] Buat migration: `settings`

### 1.2 Models & Relationships
- [ ] FiscalYear model
- [ ] Account model (ganti ChartOfAccount)
- [ ] Category model
- [ ] CategoryAccountOverride model
- [ ] Transaction model (ganti FinancialTransaction)
- [ ] JournalEntry model
- [ ] Asset model (update)
- [ ] DepreciationLog model
- [ ] Budget model (baru)
- [ ] BankReconciliation model
- [ ] AuditLog model
- [ ] InterUnitTransfer model (ganti InterAccountTransfer)
- [ ] Setting model

### 1.3 Seeders
- [ ] SeedCOASeeder — 28+ akun default SAK ETAP
- [ ] SeedCategoriesSeeder — kategori pemasukan/pengeluaran
- [ ] SeedFiscalYearSeeder — tahun fiskal aktif
- [ ] SeedRolesSeeder — 5 role dengan permission

### 1.4 Services
- [ ] AutoJournalService — double-entry engine
- [ ] AuditLogService — logging setiap perubahan

### 1.5 Filament Resources
- [ ] FiscalYearResource
- [ ] AccountResource
- [ ] CategoryResource
- [ ] TransactionResource (sederhana, mobile-first)
- [ ] JournalEntryResource (read-only)
- [ ] UnitResource (ganti BusinessUnitResource)

---

## Phase 2 — Accounting Engine

- [ ] General Ledger view (Buku Besar)
- [ ] Trial Balance (Neraca Saldo)
- [ ] Income Statement per unit (Laba/Rugi)
- [ ] Balance Sheet per unit (Neraca)
- [ ] Cash Flow per unit (Arus Kas)
- [ ] Multi-period comparison

---

## Phase 3 — Advanced Features

- [ ] Internal transfer + RAK elimination
- [ ] Consolidated reports
- [ ] Asset management + auto-depreciation
- [ ] Adjustment form
- [ ] Excel import/export
- [ ] PDF report generation

---

## Phase 4 — Operations

- [ ] Year-end closing
- [ ] Budget management
- [ ] Dashboard with charts
- [ ] Bank reconciliation
- [ ] Audit log viewer
- [ ] Backup/restore

---

## Catatan Penting

1. **Jangan hapus tabel lama sampai migration selesai dan data ter-migrate**
2. **Setiap perubahan, clear cache + restart PHP-FPM**
3. **Setiap perubahan, commit & push ke GitHub**
4. **Test setelah setiap phase selesai**
5. **Gunakan ACL untuk permissions: setfacl -R -m u:www:rwx [folder]**
