<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bumdes_settings', function (Blueprint $table) {
            $table->id();
            
            // Identitas BUMDes
            $table->string('bumdes_name')->comment('Nama BUMDes');
            $table->string('bumdes_nib')->nullable()->comment('Nomor Induk Berusaha');
            $table->string('bumdes_npwd')->nullable()->comment('Nomor Pokok Wajib Daftar');
            $table->text('bumdes_address')->comment('Alamat BUMDes');
            $table->string('bumdes_village')->comment('Nama Desa');
            $table->string('bumdes_district')->comment('Kecamatan');
            $table->string('bumdes_regency')->comment('Kabupaten');
            $table->string('bumdes_province')->comment('Provinsi');
            $table->string('bumdes_postal_code', 10)->nullable()->comment('Kode Pos');
            
            // Kontak
            $table->string('phone')->comment('Nomor Telepon/HP');
            $table->string('whatsapp')->nullable()->comment('Nomor WhatsApp');
            $table->string('email')->nullable()->comment('Email');
            $table->string('website')->nullable()->comment('Website');
            
            // Visi & Misi
            $table->text('vision')->nullable()->comment('Visi BUMDes');
            $table->text('mission')->nullable()->comment('Misi BUMDes');
            
            // Tentang BUMDes
            $table->longText('about')->nullable()->comment('Tentang BUMDes');
            $table->date('established_date')->nullable()->comment('Tanggal Berdiri');
            
            // Logo & Foto
            $table->string('logo_path')->nullable()->comment('Logo BUMDes');
            $table->string('logo_white_path')->nullable()->comment('Logo BUMDes (Putih)');
            $table->string('kantor_photo_path')->nullable()->comment('Foto Kantor (untuk background website)');
            $table->string('kegiatan_photo_path')->nullable()->comment('Foto Kegiatan (untuk background login)');
            $table->string('signature_photo_path')->nullable()->comment('Foto Tangan (untuk tanda tangan digital)');
            
            // Data Kop PDF
            $table->string('pdf_header_line1')->nullable()->comment('Baris 1 Kop PDF (mis: PEMERINTAH KAB. ACEH SELATAN)');
            $table->string('pdf_header_line2')->nullable()->comment('Baris 2 Kop PDF (mis: KECAMATAN BAKONGAN)');
            $table->string('pdf_header_line3')->nullable()->comment('Baris 3 Kop PDF (mis: DESA KEUDE BAKONGAN)');
            $table->string('pdf_footer_text')->nullable()->comment('Footer PDF');
            $table->string('pdf_stamp_path')->nullable()->comment('Stempel/Base64 untuk PDF');
            
            // Media Sosial
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('youtube')->nullable();
            $table->string('tiktok')->nullable();
            $table->string('twitter')->nullable();
            
            // Pengaturan Website
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bumdes_settings');
    }
};
