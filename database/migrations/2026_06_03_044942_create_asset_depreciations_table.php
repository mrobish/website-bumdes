<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_depreciations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('assets');
            
            // Periode
            $table->date('depreciation_date'); // Tanggal penyusutan
            $table->string('period', 7); // e.g., '2026-01'
            
            // Perhitungan
            $table->decimal('beginning_book_value', 15, 2); // Nilai buku awal
            $table->decimal('depreciation_amount', 15, 2); // Penyusutan bulan ini
            $table->decimal('accumulated_depreciation', 15, 2); // Akumulasi penyusutan
            $table->decimal('ending_book_value', 15, 2); // Nilai buku akhir
            
            // Status
            $table->enum('status', ['draft', 'posted', 'reversed'])->default('draft');
            $table->foreignId('journal_entry_id')->nullable(); // Link ke jurnal
            
            $table->timestamps();
            
            $table->unique(['asset_id', 'period']); // 1 aset = 1 penyusutan per bulan
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_depreciations');
    }
};
