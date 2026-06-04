<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bumdes_settings', function (Blueprint $table) {
            // Pelaksana Operasional
            if (!Schema::hasColumn('bumdes_settings', 'sekretaris_name')) {
                $table->string('sekretaris_name')->nullable()->after('direktur_phone');
                $table->string('sekretaris_nip')->nullable()->after('sekretaris_name');
            }
            if (!Schema::hasColumn('bumdes_settings', 'bendahara_umum_name')) {
                $table->string('bendahara_umum_name')->nullable()->after('sekretaris_nip');
                $table->string('bendahara_umum_nip')->nullable()->after('bendahara_umum_name');
            }
            if (!Schema::hasColumn('bumdes_settings', 'kepala_unit_name')) {
                $table->string('kepala_unit_name')->nullable()->after('bendahara_umum_nip');
                $table->string('kepala_unit_nip')->nullable()->after('kepala_unit_name');
                $table->string('kepala_unit_usaha')->nullable()->after('kepala_unit_nip');
            }
            // Pengawas - rename asal field if not exists
            if (!Schema::hasColumn('bumdes_settings', 'pengawas1_asal')) {
                $table->string('pengawas1_asal')->nullable()->after('pengawas1_nip');
            }
            if (!Schema::hasColumn('bumdes_settings', 'pengawas2_asal')) {
                $table->string('pengawas2_asal')->nullable()->after('pengawas2_nip');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bumdes_settings', function (Blueprint $table) {
            $table->dropColumn([
                'sekretaris_name', 'sekretaris_nip',
                'bendahara_umum_name', 'bendahara_umum_nip',
                'kepala_unit_name', 'kepala_unit_nip', 'kepala_unit_usaha',
                'pengawas1_asal', 'pengawas2_asal',
            ]);
        });
    }
};
