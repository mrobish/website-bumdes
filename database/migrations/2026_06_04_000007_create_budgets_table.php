<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Drop old budgets table if exists (old schema)
        Schema::dropIfExists('budgets');
        
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained('business_units')->nullOnDelete();
            $table->integer('year');
            $table->integer('month');
            $table->decimal('amount', 15, 2)->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['category_id', 'unit_id', 'year', 'month']);
            $table->index('year');
            $table->index('month');
        });
    }
    public function down(): void { Schema::dropIfExists('budgets'); }
};
