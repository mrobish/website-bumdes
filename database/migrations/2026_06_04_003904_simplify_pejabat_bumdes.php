<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bumdes_settings', function (Blueprint $table) {
            // STEP 1: Drop semua NIP columns dulu
            $dropCols = [];
            foreach (['kepala_desa_nip','pengawas1_nip','pengawas2_nip','direktur_nip','sekretaris_nip','bendahara_umum_nip'] as $col) {
                if (Schema::hasColumn('bumdes_settings', $col)) $dropCols[] = $col;
            }
            // Drop position/asal fields
            foreach (['pengawas1_position','pengawas2_position','pengawas1_asal','pengawas2_asal'] as $col) {
                if (Schema::hasColumn('bumdes_settings', $col)) $dropCols[] = $col;
            }
            // Drop old kepala_unit fields if exist
            foreach (['kepala_unit_name','kepala_unit_nip','kepala_unit_usaha'] as $col) {
                if (Schema::hasColumn('bumdes_settings', $col)) $dropCols[] = $col;
            }
            if (!empty($dropCols)) $table->dropColumn($dropCols);
        });

        // STEP 2: Tambah kolom baru
        Schema::table('bumdes_settings', function (Blueprint $table) {
            $table->string('kepala_desa_phone')->nullable()->after('kepala_desa_name');
            $table->string('pengawas1_phone')->nullable()->after('pengawas1_name');
            $table->string('pengawas2_phone')->nullable()->after('pengawas2_name');
            $table->string('sekretaris_phone')->nullable()->after('sekretaris_name');
            $table->string('bendahara_umum_phone')->nullable()->after('bendahara_umum_name');
            $table->string('pejabat_foto_path')->nullable()->after('bendahara_umum_phone');
        });
    }

    public function down(): void
    {
        Schema::table('bumdes_settings', function (Blueprint $table) {
            $table->dropColumn([
                'kepala_desa_phone', 'pengawas1_phone', 'pengawas2_phone',
                'sekretaris_phone', 'bendahara_umum_phone', 'pejabat_foto_path',
            ]);
        });
    }
};
