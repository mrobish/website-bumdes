<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bumdes_structures', function (Blueprint $table) {
            $table->id();
            
            // Jabatan
            $table->string('position')->comment('Nama Jabatan');
            $table->string('position_group')->comment('Kelompok: pengurus, unit, pengawas');
            $table->integer('sort_order')->default(0)->comment('Urutan tampil');
            
            // Pengisi Jabatan
            $table->string('name')->comment('Nama Pejabat');
            $table->string('nip')->nullable()->comment('NIP (jika ada)');
            $table->string('photo_path')->nullable()->comment('Foto Pejabat');
            $table->string('phone')->nullable()->comment('Nomor HP');
            $table->string('email')->nullable()->comment('Email');
            
            // Status
            $table->boolean('is_active')->default(true);
            $table->boolean('is_village_head')->default(false)->comment('Jika ini Kepala Desa (otomatis Penasehat)');
            
            // Relasi ke Unit Usaha (jika jabatan terkait unit)
            $table->unsignedBigInteger('business_unit_id')->nullable()->comment('Unit Usaha terkait');
            
            // Periode
            $table->date('start_date')->nullable()->comment('Mulai jabatan');
            $table->date('end_date')->nullable()->comment('Akhir jabatan');
            
            // Catatan
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Foreign key
            $table->foreign('business_unit_id')->references('id')->on('business_units')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bumdes_structures');
    }
};
