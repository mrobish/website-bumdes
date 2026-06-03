<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bumdes_settings', function (Blueprint $table) {
            // Motto / Tagline
            $table->string('motto', 500)->nullable()->after('about')->comment('Motto/Tagline BUMDes');

            // Informasi Bank
            $table->string('bank_name')->nullable()->after('whatsapp')->comment('Nama Bank');
            $table->string('bank_account_number', 50)->nullable()->after('bank_name')->comment('Nomor Rekening');
            $table->string('bank_account_name')->nullable()->after('bank_account_number')->comment('Nama Pemegang Rekening');

            // NPWP
            $table->string('npwp', 50)->nullable()->after('nomor_ahu')->comment('NPWP BUMDes');

            // Pejabat Desa (untuk kop surat & tanda tangan)
            $table->string('kepala_desa_name')->nullable()->after('npwp')->comment('Nama Kepala Desa');
            $table->string('kepala_desa_nip', 50)->nullable()->after('kepala_desa_name')->comment('NIP Kepala Desa');
            $table->string('kaur_keuangan_name')->nullable()->after('kepala_desa_nip')->comment('Nama Kaur Keuangan');
            $table->string('kaur_keuangan_nip', 50)->nullable()->after('kaur_keuangan_name')->comment('NIP Kaur Keuangan');
            $table->string('bendahara_name')->nullable()->after('kaur_keuangan_nip')->comment('Nama Bendahara');
            $table->string('bendahara_nip', 50)->nullable()->after('bendahara_name')->comment('NIP Bendahara');

            // Warna Branding
            $table->string('motto_color', 20)->nullable()->after('motto')->comment('Warna Motto (hex)');
            $table->string('primary_color', 20)->nullable()->after('motto_color')->comment('Warna Utama Branding (hex)');
            $table->string('secondary_color', 20)->nullable()->after('primary_color')->comment('Warna Sekunder Branding (hex)');
        });
    }

    public function down(): void
    {
        Schema::table('bumdes_settings', function (Blueprint $table) {
            $table->dropColumn([
                'motto',
                'bank_name',
                'bank_account_number',
                'bank_account_name',
                'npwp',
                'kepala_desa_name',
                'kepala_desa_nip',
                'kaur_keuangan_name',
                'kaur_keuangan_nip',
                'bendahara_name',
                'bendahara_nip',
                'motto_color',
                'primary_color',
                'secondary_color',
            ]);
        });
    }
};
