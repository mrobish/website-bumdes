<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('depreciation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->date('run_date');
            $table->decimal('depreciation_amount', 15, 2);
            $table->foreignId('journal_entry_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('fiscal_year');
            $table->timestamps();
            $table->index('asset_id');
            $table->index('fiscal_year');
        });
    }
    public function down(): void { Schema::dropIfExists('depreciation_logs'); }
};
