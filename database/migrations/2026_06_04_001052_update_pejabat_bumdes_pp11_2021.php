<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bumdes_settings', function (Blueprint $table) {
            // === PEJABAT SESUAI PP 11/2021 ===
            // Pembina (sudah ada: kepala_desa_name/nip)

            // Pengawas 1
            $table->string('pengawas1_name')->nullable()->after('bendahara_nip');
            $table->string('pengawas1_nip')->nullable()->after('pengawas1_name');
            $table->string('pengawas1_position')->nullable()->after('pengawas1_nip'); // jabatan asal

            // Pengawas 2
            $table->string('pengawas2_name')->nullable()->after('pengawas1_position');
            $table->string('pengawas2_nip')->nullable()->after('pengawas2_name');
            $table->string('pengawas2_position')->nullable()->after('pengawas2_nip'); // jabatan asal

            // Direktur BUMDes (pengurus harian)
            $table->string('direktur_name')->nullable()->after('pengawas2_position');
            $table->string('direktur_nip')->nullable()->after('direktur_name');
            $table->string('direktur_phone')->nullable()->after('direktur_nip');
        });
    }

    public function down(): void
    {
        Schema::table('bumdes_settings', function (Blueprint $table) {
            $table->dropColumn([
                'pengawas1_name', 'pengawas1_nip', 'pengawas1_position',
                'pengawas2_name', 'pengawas2_nip', 'pengawas2_position',
                'direktur_name', 'direktur_nip', 'direktur_phone',
            ]);
        });
    }
};
