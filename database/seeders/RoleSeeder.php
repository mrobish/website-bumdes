<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Super Admin',
                'slug' => 'super_admin',
                'permissions' => json_encode(['*']),
                'description' => 'Akses penuh ke semua fitur',
            ],
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'permissions' => json_encode([
                    'dashboard', 'berita', 'galeri', 'produk', 'unit_usaha',
                    'keuangan', 'aset', 'penyertaan_modal', 'rak', 'konsolidasi',
                    'laporan', 'identitas_bumdes', 'struktur', 'manajemen_user',
                    'manajemen_role', 'informasi_system', 'error_log'
                ]),
                'description' => 'Admin sistem dengan akses luas',
            ],
            [
                'name' => 'Bendahara',
                'slug' => 'bendahara',
                'permissions' => json_encode([
                    'dashboard', 'keuangan', 'aset', 'penyertaan_modal',
                    'laporan', 'identitas_bumdes'
                ]),
                'description' => 'Bendahara yang mengelola keuangan',
            ],
            [
                'name' => 'Operator',
                'slug' => 'operator',
                'permissions' => json_encode([
                    'dashboard', 'berita', 'galeri', 'produk', 'keuangan'
                ]),
                'description' => 'Operator yang input data',
            ],
            [
                'name' => 'Viewer',
                'slug' => 'viewer',
                'permissions' => json_encode([
                    'dashboard', 'laporan'
                ]),
                'description' => 'Pengguna yang hanya melihat laporan',
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['slug' => $role['slug']],
                $role
            );
        }

        $this->command->info('✅ RoleSeeder: ' . count($roles) . ' role ditambahkan');
    }
}
