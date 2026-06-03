<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('financial_transactions', function (Blueprint $table) {
            $table->foreignId('business_unit_id')->nullable()->after('id')->constrained('business_units');
            $table->json('attachments')->nullable()->after('notes'); // Bukti foto nota/struk
        });
    }

    public function down(): void
    {
        Schema::table('financial_transactions', function (Blueprint $table) {
            $table->dropForeign(['business_unit_id']);
            $table->dropColumn(['business_unit_id', 'attachments']);
        });
    }
};
