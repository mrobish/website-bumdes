<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('capital_contribution_id')->constrained('capital_contributions');
            $table->date('payment_date');
            $table->decimal('amount', 15, 2);
            $table->decimal('principal', 15, 2)->nullable();
            $table->decimal('interest', 15, 2)->nullable();
            $table->decimal('remaining_balance', 15, 2)->nullable();
            $table->string('payment_method')->default('transfer');
            $table->string('reference_number')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('paid'); // paid, pending, overdue
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_payments');
    }
};
