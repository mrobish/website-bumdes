<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('financial_uploads', function (Blueprint $table) {
            $table->json('imported_data')->nullable()->after('notes');
            $table->integer('imported_count')->default(0)->after('imported_data');
            $table->string('import_status')->default('pending')->after('imported_count'); // pending, imported, error
            $table->text('import_error')->nullable()->after('import_status');
        });
    }

    public function down(): void
    {
        Schema::table('financial_uploads', function (Blueprint $table) {
            $table->dropColumn(['imported_data', 'imported_count', 'import_status', 'import_error']);
        });
    }
};
