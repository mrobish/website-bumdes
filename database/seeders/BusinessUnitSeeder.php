<?php

namespace Database\Seeders;

use App\Models\BusinessUnit;
use Illuminate\Database\Seeder;

class BusinessUnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            [
                'code' => 'INDUK',
                'name' => 'BUMDes Induk (Pusat)',
                'description' => 'Pusat pengelolaan BUMDes. Menangani transaksi manajerial umum, penyertaan modal dari desa, operasional kantor BUMDes.',
                'type' => 'induk',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'code' => 'PANG',
                'name' => 'Unit Ketahanan Pangan',
                'description' => 'Pengelolaan lahan pertanian, produksi pangan, dan distribusi hasil pertanian.',
                'type' => 'unit_usaha',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'code' => 'WIS',
                'name' => 'Unit Pariwisata',
                'description' => 'Pengelolaan objek wisata, homestay, dan jasa pariwisata desa.',
                'type' => 'unit_usaha',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'code' => 'SAM',
                'name' => 'Unit Pengelolaan Sampah',
                'description' => 'Pengelolaan sampah desa, daur ulang, dan bank sampah.',
                'type' => 'unit_usaha',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'code' => 'NET',
                'name' => 'Unit Jaringan Internet',
                'description' => 'Pengelolaan jaringan internet desa (RT/RW Net).',
                'type' => 'unit_usaha',
                'is_active' => true,
                'sort_order' => 5,
            ],
        ];

        foreach ($units as $unit) {
            BusinessUnit::updateOrCreate(
                ['code' => $unit['code']],
                $unit
            );
        }

        $this->command->info('Business units seeded: ' . count($units) . ' units');
    }
}
