<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama template
            $table->string('slug')->unique(); // jurnal-harian, neraca, etc
            $table->text('description')->nullable();
            $table->enum('type', ['jurnal', 'neraca', 'laba_rugi', 'arus_kas', 'modal', 'realisasi_anggaran', 'buku_besar', 'cat']);
            $table->enum('frequency', ['harian', 'bulanan', 'tahunan', 'sekali'])->default('bulanan');
            $table->json('columns'); // Kolom template (header Excel)
            $table->json('sample_data')->nullable(); // Contoh data
            $table->json('validation_rules')->nullable(); // Aturan validasi
            $table->string('file_path')->nullable(); // Path file template Excel
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_templates');
    }
};
