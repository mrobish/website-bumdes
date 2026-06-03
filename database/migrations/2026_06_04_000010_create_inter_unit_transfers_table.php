<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('inter_unit_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_unit_id')->constrained('business_units');
            $table->foreignId('to_unit_id')->constrained('business_units');
            $table->decimal('amount', 15, 2);
            $table->text('description');
            $table->enum('status', ['pending', 'confirmed'])->default('pending');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('confirmed_at')->nullable();
            $table->foreignId('journal_entry_sender_id')->nullable()->constrained('journal_entries')->nullOnDelete();
            $table->foreignId('journal_entry_receiver_id')->nullable()->constrained('journal_entries')->nullOnDelete();
            $table->timestamps();
            $table->index('from_unit_id');
            $table->index('to_unit_id');
            $table->index('status');
        });
    }
    public function down(): void { Schema::dropIfExists('inter_unit_transfers'); }
};
