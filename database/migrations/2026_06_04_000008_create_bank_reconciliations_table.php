<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::dropIfExists("bank_reconciliations");
        Schema::create('bank_reconciliations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->nullable()->constrained()->nullOnDelete();
            $table->date('bank_date');
            $table->decimal('bank_amount', 15, 2);
            $table->string('bank_description')->nullable();
            $table->enum('match_status', ['matched', 'unmatched'])->default('unmatched');
            $table->foreignId('reconciled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reconciled_at')->nullable();
            $table->timestamps();
            $table->index('match_status');
            $table->index('bank_date');
        });
    }
    public function down(): void { Schema::dropIfExists('bank_reconciliations'); }
};
