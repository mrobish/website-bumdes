<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inter_account_transfers', function (Blueprint $table) {
            $table->id();
            $table->string('transfer_number', 30)->unique(); // e.g., 'RAK-2026-0001'
            $table->date('transfer_date');
            
            // Source
            $table->foreignId('from_business_unit_id')->constrained('business_units');
            $table->foreignId('from_account_id')->constrained('chart_of_accounts');
            
            // Destination
            $table->foreignId('to_business_unit_id')->constrained('business_units');
            $table->foreignId('to_account_id')->constrained('chart_of_accounts');
            
            $table->decimal('amount', 15, 2);
            $table->text('description');
            $table->text('notes')->nullable();
            
            // Status
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            
            // Attachments (bukti transfer)
            $table->json('attachments')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inter_account_transfers');
    }
};
