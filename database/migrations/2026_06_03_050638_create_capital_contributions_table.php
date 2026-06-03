<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('capital_contributions', function (Blueprint $table) {
            $table->id();
            $table->string('contribution_number', 30)->unique(); // e.g., 'PM-2026-001'
            
            // Informasi Sumber
            $table->enum('source_type', [
                'desa',                    // Penyertaan modal dari desa
                'pemda_kab_kot',          // Bantuan keuangan dari Pemda Kabupaten/Kota
                'pemprov',                // Bantuan dari Pemerintah Provinsi
                'kementerian',            // Kementerian (Kemendes, dll)
                'lembaga_negara',         // Lembaga negara lainnya
                'lembaga_swasta',         // CSR / lembaga swasta
                'perorangan',             // Donasi perorangan
                'bantuan_luar_negeri',    // Bantuan dari luar negeri (international)
                'lainnya',                // Lainnya
            ]);
            $table->string('source_name'); // Nama spesifik sumber (e.g., 'Kemendes PDTT', 'PT Telkom')
            $table->text('source_address')->nullable(); // Alamat sumber
            $table->string('source_contact')->nullable(); // Kontak person
            $table->string('source_phone')->nullable(); // Telepon
            $table->string('source_email')->nullable(); // Email
            
            // Informasi Penyertaan
            $table->enum('contribution_type', [
                'hibah',           // Hibah (grant) - tidak perlu dikembalikan
                'pinjaman',        // Pinjaman (loan) - perlu dikembalikan
                'pinjaman_bunga',  // Pinjaman berbunga
                'penyertaan_saham', // Penyertaan saham/kekayaan
            ]);
            $table->enum('form', [
                'uang',            // Uang tunai
                'barang',          // Barang berupa
                'uang_dan_barang', // Uang dan barang
                'jasa',            // Jasa/layanan
            ]);
            
            // Nilai
            $table->decimal('amount', 15, 2)->default(0); // Nilai uang
            $table->text('goods_description')->nullable(); // Deskripsi barang (jika bentuk barang)
            $table->decimal('goods_value', 15, 2)->default(0); // Nilai barang
            $table->decimal('total_value', 15, 2)->default(0); // Total nilai (uang + barang)
            
            // Waktu
            $table->year('contribution_year'); // Tahun penyertaan
            $table->date('disbursement_date')->nullable(); // Tanggal pencairan
            $table->date('received_date')->nullable(); // Tanggal diterima
            
            // Untuk Pinjaman
            $table->decimal('interest_rate', 5, 2)->nullable(); // Suku bunga (% per tahun)
            $table->integer('loan_term_months')->nullable(); // Jangka waktu (bulan)
            $table->date('first_payment_date')->nullable(); // Tanggal pembayaran pertama
            $table->decimal('monthly_payment', 15, 2)->nullable(); // Cicilan per bulan
            $table->text('repayment_terms')->nullable(); // Syarat pengembalian
            
            // Penggunaan Dana
            $table->string('purpose')->nullable(); // Tujuan penyertaan
            $table->text('restrictions')->nullable(); // Pembatasan penggunaan dana
            $table->boolean('is_restricted')->default(false); // Apakah ada pembatasan
            
            // Status
            $table->enum('status', [
                'pending',      // Menunggu proses
                'approved',     // Disetujui
                'disbursed',    // Sudah dicairkan
                'received',     // Sudah diterima
                'active',       // Aktif (untuk pinjaman)
                'completed',    // Selesai (pinjaman lunas)
                'cancelled',    // Dibatalkan
                'rejected',     // Ditolak
            ])->default('pending');
            
            // Dokumen
            $table->string('agreement_number')->nullable(); // Nomor perjanjian
            $table->date('agreement_date')->nullable(); // Tanggal perjanjian
            $table->json('attachments')->nullable(); // Dokumen (SK, Perjanjian, Bukti Transfer)
            
            // Tracking
            $table->foreignId('business_unit_id')->nullable()->constrained('business_units'); // Unit penerima
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            
            // Catatan
            $table->text('notes')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('capital_contributions');
    }
};
