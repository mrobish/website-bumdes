<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number', 20)->unique();
            $table->date('transaction_date');
            $table->enum('type', ['pemasukan', 'pengeluaran']);
            $table->foreignId('account_id')->constrained('chart_of_accounts');
            $table->foreignId('counter_account_id')->nullable()->constrained('chart_of_accounts')->nullOnDelete();
            $table->decimal('amount', 15, 2);
            $table->text('description');
            $table->string('reference', 100)->nullable();
            $table->string('attachment', 255)->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected'])->default('draft');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('transaction_date');
            $table->index('type');
            $table->index('account_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_transactions');
    }
};
