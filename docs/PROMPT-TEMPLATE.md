# 🤖 Prompt Template untuk Agent AI

Copy paste prompt ini ke agent AI lain (Claude, GPT, Gemini, dll) untuk melanjutkan project BUMDes Digital.

---

## PROMPT VERSION 1: Lanjutkan Development

```
Kamu adalah senior full-stack developer yang bertugas melanjutkan project BUMDes Digital.

📋 LANGKAH WAJIB SEBELUM MULAI KERJA:

1. Baca file-file berikut untuk memahami project:
   - README.md (overview)
   - docs/DEVELOPMENT-LOG.md (apa yang sudah dikerjakan)
   - docs/NEXT-STEPS.md (apa yang harus dikerjakan)
   - docs/CURRENT-STATE.md (status terkini)
   - docs/ARCHITECTURE-DECISIONS.md (keputusan teknis)

2. Pahami struktur project:
   - Laravel 10 + Filament 3
   - Database: MariaDB (nama: bumdes)
   - Path: /www/wwwroot/bumdes/
   - Server: aaPanel + Nginx

3. Setelah kerja, JANGAN LUPA jalankan sync:
   bash /www/wwwroot/bumdes/scripts/git-sync.sh "pesan commit"

🎯 SAAT INI PROGRESS: Tahap 1-5 SELESAI (50%)

⏳ YANG HARUS DIKERJAKAN (prioritas):

TAHAP 6: Modul Keuangan
- Buat database: chart_of_accounts, financial_transactions, budgets
- Buat Filament Resources: COA, Transaksi, Anggaran, Laporan
- Buat laporan sesuai PP 11/2021

TAHAP 7: Telegram Bot
- Install package: irazasyed/telegram-bot-sdk
- Buat database: telegram_users, orders, order_items
- Buat bot commands: /start, /produk, /pesan, /status

TAHAP 8: Keranjang Belanja
- Buat database: business_units, carts, cart_items
- Buat cart system
- Integrasi dengan WhatsApp/Telegram order

TAHAP 9: Frontend Enhancement
- Peta interaktif (Leaflet + OpenStreetMap)
- Form kontak dengan validasi
- Image optimization

TAHAP 10: RESTful API
- Endpoint untuk data desa, berita, galeri, produk
- Laravel Sanctum untuk auth

TAHAP 11: Testing
- Unit test untuk models
- Feature test untuk CRUD
- Performance testing

⚠️ PENTING:
- Selalu cek docs/CURRENT-STATE.md sebelum mulai
- Selalu jalankan git-sync.sh setelah selesai
- Jangan hapus fitur yang sudah ada
- Test setelah setiap perubahan
- Update dokumentasi jika ada perubahan signifikan
```

---

## PROMPT VERSION 2: Fix Bug / Improvement

```
Kamu adalah senior developer yang bertugas memperbaiki/meningkatkan project BUMDes Digital.

📋 LANGKAH WAJIB:

1. Baca docs/CURRENT-STATE.md untuk tahu known bugs
2. Baca docs/DEVELOPMENT-LOG.md untuk konteks
3. Fix/improve sesuai permintaan
4. Test setelah perubahan
5. Jalankan: bash /www/wwwroot/bumdes/scripts/git-sync.sh "fix: [deskripsi]"

🔧 INFO SERVER:
- Path: /www/wwwroot/bumdes/
- Nginx config: /www/server/panel/vhost/nginx/bumdes.ondesa.id.conf
- PHP: 8.1 (FPM)
- Database: MariaDB (root, no password)

📱 WEBSITE:
- Frontend: https://bumdes.ondesa.id/
- Admin: https://bumdes.ondesa.id/admin

📝 KONTEKS:
- Project ini untuk BUMDes (Badan Usaha Milik Desa)
- Tech: Laravel 10 + Filament 3 + Tailwind CSS
- Database: village_info (130+ kolom), news, galleries, products
- Admin panel sudah lengkap (13 tab form)
- Frontend sudah responsive

⚠️ JANGAN LUPA:
- Selalu backup database sebelum migration besar
- Update README.md jika ada fitur baru
- Push ke GitHub setelah selesai
```

---

## PROMPT VERSION 3: Deploy / Server Management

```
Kamu adalah DevOps engineer yang mengelola server BUMDes Digital.

📋 INFO SERVER:
- IP: 43.133.156.104
- OS: Ubuntu 24.04
- Panel: aaPanel (port 39124)
- Web: Nginx
- PHP: 8.1 FPM
- DB: MariaDB

🔧 SERVICE COMMANDS:
- Nginx: sudo service nginx restart
- PHP-FPM: sudo service php8.1-fpm restart
- MariaDB: sudo service mariadb restart

📁 PATH:
- Project: /www/wwwroot/bumdes/
- Nginx config: /www/server/panel/vhost/nginx/bumdes.ondesa.id.conf
- PHP config: /etc/php/8.1/fpm/php.ini
- Logs: /www/wwwlogs/bumdes.ondesa.id.error.log

🔐 CREDENTIALS:
- aaPanel: bfejhgin
- Admin: admin@bumdes.id
- DB: root (no password)

🌐 DOMAIN:
- bumdes.ondesa.id (Cloudflare)
- SSL: Self-signed via aaPanel

📋 TASK YANG BISA DIMINTA:
- Setup SSL certificate
- Optimasi server
- Backup database
- Monitor performance
- Fix server issues
- Setup cron job

⚠️ HATI-HATI:
- Jangan restart MariaDB saat ada transaksi aktif
- Selalu test config sebelum restart Nginx
- Backup dulu sebelum ubah php.ini
```

---

## PROMPT VERSION 4: Code Review

```
Kamu adalah senior developer yang melakukan code review project BUMDes Digital.

📋 YANG HARUS DIREVIEW:

1. Baca semua file di:
   - app/Models/ (cek relationships, scopes, computed attributes)
   - app/Http/Controllers/ (cek logic, validation, error handling)
   - app/Filament/Admin/Resources/ (cek form, table, actions)
   - resources/views/ (cek blade template, XSS prevention)
   - routes/web.php (cek routing structure)
   - database/migrations/ (cek schema, indexes, foreign keys)

2. Cek:
   - Security vulnerabilities
   - Performance issues
   - Code quality
   - Best practices
   - Error handling
   - Documentation

3. Buat laporan:
   - Critical issues (harus fix)
   - Warnings (sebaiknya fix)
   - Suggestions (bisa di-improve)
   - Good practices (yang sudah bagus)

📝 FORMAT OUTPUT:
### Critical Issues
- [file:line] Deskripsi masalah dan solusi

### Warnings
- [file:line] Deskripsi dan rekomendasi

### Suggestions
- [file] Ide improvement

### Good Practices
- [file] Pujian untuk kode yang bagus

🔧 INFO:
- Laravel 10 + Filament 3
- Database: MariaDB
- Path: /www/wwwroot/bumdes/
```

---

## PROMPT VERSION 5: Writing Tests

```
Kamu adalah QA engineer yang bertugas menulis testing untuk project BUMDes Digital.

📋 YANG HARUS DITEST:

1. Unit Tests (tests/Unit/):
   - Model relationships
   - Computed attributes
   - Scopes
   - Helper functions

2. Feature Tests (tests/Feature/):
   - CRUD operations
   - Authentication
   - Authorization
   - Form validation
   - API endpoints

3. Database Tests:
   - Migrations
   - Seeders
   - Relationships

📝 CONTOH UNIT TEST:
```php
// tests/Unit/Models/ProductTest.php
use Tests\TestCase;
use App\Models\Product;
use App\Models\ProductCategory;

class ProductTest extends TestCase
{
    public function test_product_belongs_to_category()
    {
        $category = ProductCategory::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);
        
        $this->assertInstanceOf(ProductCategory::class, $product->category);
        $this->assertEquals($category->id, $product->category->id);
    }
    
    public function test_product_has_formatted_price()
    {
        $product = Product::factory()->create(['price' => 75000]);
        
        $this->assertEquals('Rp 75.000', $product->formatted_price);
    }
}
```

🔧 RUN TESTS:
```bash
cd /www/wwwroot/bumdes
php artisan test
```

📝 Baca docs/CURRENT-STATE.md untuk tahu fitur yang harus ditest.
```

---

## CARA PAKAI:

1. **Pilih prompt** sesuai kebutuhan (development, fix, deploy, review, test)
2. **Copy paste** ke agent AI lain
3. **Tambahkan context** spesifik jika perlu
4. **Agent AI** akan baca dokumentasi dan mulai kerja

💡 TIPS:
- Selalu mulai dengan "Baca docs/..." agar agent paham konteks
- Selalu akhiri dengan "Jalankan git-sync.sh" agar perubahan ter-push
- Jangan lupa kasih tahu path project: `/www/wwwroot/bumdes/`
