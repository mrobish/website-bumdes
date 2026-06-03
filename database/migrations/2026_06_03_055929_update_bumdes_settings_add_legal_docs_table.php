<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bumdes_settings', function (Blueprint $table) {
            // Hapus field lama yang tidak dipakai
            $table->dropColumn([
                'bumdes_npwd',  // Ganti dengan nomor_ahu
            ]);
            
            // Tambah field baru
            $table->string('village_name')->nullable()->after('bumdes_name')->comment('Nama Desa');
            $table->string('village_code')->nullable()->after('village_name')->comment('Kode Desa');
            $table->string('nomor_ahu')->nullable()->after('bumdes_npwd')->comment('Nomor AHU (Jika sudah terbit SK Kemenkumham)');
            $table->string('nomor_perdes')->nullable()->after('established_date')->comment('Nomor Perdes Pendirian');
            $table->date('tanggal_perdes')->nullable()->after('nomor_perdes')->comment('Tanggal Perdes Pendirian');
            $table->string('file_perdes_path')->nullable()->after('tanggal_perdes')->comment('File Perdes Pendirian (PDF)');
            $table->string('file_adart_path')->nullable()->after('file_perdes_path')->comment('File AD/ART BUMDes (PDF)');
        });
    }

    public function down(): void
    {
        Schema::table('bumdes_settings', function (Blueprint $table) {
            $table->string('bumdes_npwd')->nullable();
            $table->dropColumn([
                'village_name',
                'village_code',
                'nomor_ahu',
                'nomor_perdes',
                'tanggal_perdes',
                'file_perdes_path',
                'file_adart_path',
            ]);
        });
    }
};
