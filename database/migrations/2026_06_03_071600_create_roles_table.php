<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama role (Super Admin, Admin, etc)
            $table->string('slug')->unique(); // super_admin, admin, etc
            $table->text('description')->nullable();
            $table->json('permissions'); // JSON array of permissions
            $table->boolean('is_default')->default(false); // Role default system
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Insert default roles
        DB::table('roles')->insert([
            [
                'name' => 'Super Admin',
                'slug' => 'super_admin',
                'description' => 'Akses penuh ke semua fitur',
                'permissions' => json_encode(['*']),
                'is_default' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Akses ke semua fitur kecuali manajemen user',
                'permissions' => json_encode([
                    'dashboard', 'berita', 'galeri', 'produk', 'unit_usaha',
                    'keuangan', 'aset', 'penyertaan_modal', 'rak', 'konsolidasi',
                    'laporan', 'identitas_bumdes', 'struktur', 'system_info'
                ]),
                'is_default' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bendahara',
                'slug' => 'bendahara',
                'description' => 'Akses ke fitur keuangan dan laporan',
                'permissions' => json_encode([
                    'dashboard', 'keuangan', 'aset', 'penyertaan_modal',
                    'rak', 'konsolidasi', 'laporan', 'identitas_bumdes'
                ]),
                'is_default' => true,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Operator',
                'slug' => 'operator',
                'description' => 'Akses ke konten dan produk',
                'permissions' => json_encode([
                    'dashboard', 'berita', 'galeri', 'produk', 'unit_usaha',
                    'identitas_bumdes'
                ]),
                'is_default' => true,
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Viewer',
                'slug' => 'viewer',
                'description' => 'Hanya bisa melihat dashboard',
                'permissions' => json_encode(['dashboard']),
                'is_default' => true,
                'sort_order' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
