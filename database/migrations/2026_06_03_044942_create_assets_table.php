<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_code', 20)->unique(); // e.g., 'AST-2026-001'
            $table->string('name');
            $table->text('description')->nullable();
            
            // Klasifikasi
            $table->enum('category', ['tanah', 'bangunan', 'peralatan', 'kendaraan', 'inventaris', 'lainnya']);
            $table->foreignId('business_unit_id')->constrained('business_units');
            $table->foreignId('account_id')->constrained('chart_of_accounts'); // Akun aset
            
            // Nilai
            $table->decimal('purchase_price', 15, 2); // Harga beli
            $table->decimal('salvage_value', 15, 2)->default(0); // Nilai sisa
            $table->date('purchase_date');
            
            // Penyusutan
            $table->integer('useful_life_months'); // Umur ekonomis (bulan)
            $table->enum('depreciation_method', ['straight_line', 'declining_balance'])->default('straight_line');
            $table->decimal('depreciation_rate', 5, 2)->nullable(); // Rate per tahun (opsional)
            
            // Status
            $table->enum('status', ['active', 'fully_depreciated', 'disposed', 'sold'])->default('active');
            $table->date('disposal_date')->nullable();
            $table->decimal('disposal_price', 15, 2)->nullable();
            
            // Bukti
            $table->string('location')->nullable(); // Lokasi aset
            $table->json('attachments')->nullable(); // Foto bukti
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
