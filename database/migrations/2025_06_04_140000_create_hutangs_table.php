<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Hutang (payables to suppliers/vendors)
        Schema::create('hutangs', function (Blueprint $table) {
            $table->id();
            $table->string('hutang_number')->unique(); // HTG-2025-001
            $table->date('hutang_date');
            $table->string('supplier_name'); // nama supplier/vendor
            $table->string('supplier_phone')->nullable();
            $table->string('supplier_address')->nullable();
            $table->string('supplier_npwp')->nullable(); // NPWP supplier
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained('business_units')->nullOnDelete();
            $table->decimal('amount', 15, 2); // total hutang
            $table->decimal('paid_amount', 15, 2)->default(0); // sudah dibayar
            $table->decimal('remaining', 15, 2)->default(0); // sisa
            $table->date('due_date')->nullable(); // jatuh tempo
            $table->enum('status', ['belum_lunas', 'sebagian', 'lunas', 'macet'])->default('belum_lunas');
            $table->text('description')->nullable();
            $table->string('reference_number')->nullable(); // no invoice/faktur
            $table->string('attachment_path')->nullable(); // foto invoice
            $table->string('agreement_letter_path')->nullable(); // surat perjanjian
            $table->foreignId('transaction_id')->nullable()->constrained('transactions')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Pembayaran hutang (installments)
        Schema::create('hutang_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hutang_id')->constrained('hutangs')->cascadeOnDelete();
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
        Schema::dropIfExists('hutang_payments');
        Schema::dropIfExists('hutangs');
    }
};
