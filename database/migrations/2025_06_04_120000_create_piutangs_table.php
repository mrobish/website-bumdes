<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Piutang (receivables from masyarakat)
        Schema::create('piutangs', function (Blueprint $table) {
            $table->id();
            $table->string('piutang_number')->unique(); // PIU-2025-001
            $table->date('piutang_date');
            $table->string('customer_name'); // nama masyarakat
            $table->string('customer_phone')->nullable();
            $table->string('customer_address')->nullable();
            $table->string('customer_nik')->nullable(); // NIK untuk identifikasi
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained('business_units')->nullOnDelete();
            $table->decimal('amount', 15, 2); // total piutang
            $table->decimal('paid_amount', 15, 2)->default(0); // sudah dibayar
            $table->decimal('remaining', 15, 2)->default(0); // sisa
            $table->date('due_date')->nullable(); // jatuh tempo
            $table->enum('status', ['belum_lunas', 'sebagian', 'lunas', 'macet'])->default('belum_lunas');
            $table->text('description')->nullable();
            $table->string('reference_number')->nullable(); // nota/kwitansi
            $table->string('attachment_path')->nullable(); // foto nota
            $table->foreignId('transaction_id')->nullable()->constrained('transactions')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Pembayaran piutang (installments)
        Schema::create('piutang_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('piutang_id')->constrained('piutangs')->cascadeOnDelete();
            $table->date('payment_date');
            $table->decimal('amount', 15, 2);
            $table->string('payment_method')->default('tunai'); // tunai, transfer, qris
            $table->string('reference_number')->nullable(); // bukti bayar
            $table->text('notes')->nullable();
            $table->foreignId('transaction_id')->nullable()->constrained('transactions')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('piutang_payments');
        Schema::dropIfExists('piutangs');
    }
};
