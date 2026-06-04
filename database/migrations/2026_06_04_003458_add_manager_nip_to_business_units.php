<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_units', function (Blueprint $table) {
            if (!Schema::hasColumn('business_units', 'manager_nip')) {
                $table->string('manager_nip')->nullable()->after('manager');
            }
            if (!Schema::hasColumn('business_units', 'manager_phone')) {
                $table->string('manager_phone')->nullable()->after('manager_nip');
            }
        });
    }

    public function down(): void
    {
        Schema::table('business_units', function (Blueprint $table) {
            $table->dropColumn(['manager_nip', 'manager_phone']);
        });
    }
};
