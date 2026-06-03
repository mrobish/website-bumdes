<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_report_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., 'Neraca SAK EMKM', 'Laba Rugi Konsolidasi'
            $table->string('code', 20)->unique(); // e.g., 'NERACA', 'LR_KONSOL', 'ARUS_KAS', 'CALK'
            $table->enum('type', ['balance_sheet', 'income_statement', 'cash_flow', 'notes']);
            $table->text('description')->nullable();
            
            // Template structure (JSON)
            $table->json('structure'); // Defines rows, columns, formulas
            $table->json('settings')->nullable(); // Additional settings
            
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_report_templates');
    }
};
