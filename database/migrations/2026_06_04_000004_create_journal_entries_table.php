<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained()->cascadeOnDelete();
            $table->date('entry_date');
            $table->enum('entry_type', ['opening_balance', 'normal', 'closing', 'reversal', 'adjustment', 'depreciation']);
            $table->string('account_code', 10);
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('credit', 15, 2)->default(0);
            $table->foreignId('unit_id')->nullable()->constrained('business_units')->nullOnDelete();
            $table->text('description')->nullable();
            $table->integer('fiscal_year');
            $table->boolean('is_closing_entry')->default(false);
            $table->timestamps();
            $table->index('transaction_id');
            $table->index('account_code');
            $table->index('entry_date');
            $table->index('unit_id');
            $table->index('fiscal_year');
            $table->index('entry_type');
        });
    }
    public function down(): void { Schema::dropIfExists('journal_entries'); }
};
