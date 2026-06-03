<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consolidated_reports', function (Blueprint $table) {
            $table->id();
            $table->string('report_number', 30)->unique(); // e.g., 'KONSOL-2026-001'
            $table->string('period'); // e.g., '2026-01', '2026-Q1', '2026'
            $table->enum('period_type', ['monthly', 'quarterly', 'yearly']);
            
            // Report Data (JSON for flexibility)
            $table->json('balance_sheet'); // Neraca
            $table->json('income_statement'); // Laba/Rugi
            $table->json('cash_flow'); // Arus Kas
            $table->json('notes')->nullable(); // CALK
            
            // Per-unit breakdown
            $table->json('unit_breakdown'); // Detail per unit
            
            // Totals
            $table->decimal('total_assets', 15, 2)->default(0);
            $table->decimal('total_liabilities', 15, 2)->default(0);
            $table->decimal('total_equity', 15, 2)->default(0);
            $table->decimal('total_revenue', 15, 2)->default(0);
            $table->decimal('total_expenses', 15, 2)->default(0);
            $table->decimal('net_profit', 15, 2)->default(0);
            
            // Metadata
            $table->enum('status', ['draft', 'final', 'archived'])->default('draft');
            $table->foreignId('prepared_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->string('pdf_path')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consolidated_reports');
    }
};
