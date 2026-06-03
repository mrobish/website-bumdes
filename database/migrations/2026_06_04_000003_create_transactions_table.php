<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number', 30)->unique();
            $table->date('transaction_date');
            $table->foreignId('unit_id')->nullable()->constrained('business_units')->nullOnDelete();
            $table->enum('type', ['income', 'expense', 'transfer', 'asset_purchase', 'opening_balance', 'adjustment', 'void']);
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 15, 2);
            $table->text('description');
            $table->string('reference_number', 50)->nullable();
            $table->string('attachment_path')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->integer('fiscal_year');
            $table->boolean('is_void')->default(false);
            $table->text('void_reason')->nullable();
            $table->foreignId('voided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('voided_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->boolean('is_locked')->default(false);
            $table->timestamps();
            $table->softDeletes();
            $table->index('transaction_date');
            $table->index('unit_id');
            $table->index('type');
            $table->index('fiscal_year');
            $table->index('is_void');
        });
    }
    public function down(): void { Schema::dropIfExists('transactions'); }
};
