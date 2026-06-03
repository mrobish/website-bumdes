<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_uploads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained('financial_templates')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('file_name'); // Nama file asli
            $table->string('file_path'); // Path file yang di-upload
            $table->string('file_size'); // Size file
            $table->string('period')->nullable(); // Periode (2026-01, 2026-Q1, 2026)
            $table->enum('status', ['pending', 'validated', 'approved', 'rejected'])->default('pending');
            $table->text('notes')->nullable();
            $table->text('validation_errors')->nullable(); // JSON error validasi
            $table->json('parsed_data')->nullable(); // Data yang sudah di-parse
            $table->timestamp('validated_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->index(['template_id', 'period']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_uploads');
    }
};
