<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BumdesStructure;
use App\Models\BusinessUnit;

class BumdesStructureSeeder extends Seeder
{
    public function run(): void
    {
        // Pengurus BUMDes
        $pengurus = [
            [
                'position' => 'Penasehat',
                'position_group' => 'pengurus',
                'name' => 'Kepala Desa',
                'is_village_head' => true,
                'is_active' => true,
                'sort_order' => 1,
                'notes' => 'Otomatis dijabat oleh Kepala Desa',
            ],
            [
                'position' => 'Direktur',
                'position_group' => 'pengurus',
                'name' => 'Direktur BUMDes',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'position' => 'Sekretaris',
                'position_group' => 'pengurus',
                'name' => 'Sekretaris BUMDes',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'position' => 'Bendahara',
                'position_group' => 'pengurus',
                'name' => 'Bendahara BUMDes',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'position' => 'Pengawas',
                'position_group' => 'pengawas',
                'name' => 'Ketua BPD',
                'is_active' => true,
                'sort_order' => 5,
            ],
        ];

        foreach ($pengurus as $item) {
            BumdesStructure::updateOrCreate(
                ['position' => $item['position'], 'position_group' => $item['position_group']],
                $item
            );
        }

        // Pengelola Unit Usaha
        $units = BusinessUnit::all();
        $unitPositions = [
            'Unit Ketahanan Pangan' => 'Pengelola Unit Pangan',
            'Unit Pariwisata' => 'Pengelola Unit Wisata',
            'Unit Pengelolaan Sampah' => 'Pengelola Unit Sampah',
            'Unit Jaringan Internet' => 'Pengelola Unit Internet',
        ];

        $sortOrder = 10;
        foreach ($units as $unit) {
            if ($unit->type === 'unit_usaha') {
                $positionName = $unitPositions[$unit->name] ?? 'Pengelola ' . $unit->name;
                BumdesStructure::updateOrCreate(
                    ['position' => $positionName, 'position_group' => 'unit'],
                    [
                        'position' => $positionName,
                        'position_group' => 'unit',
                        'name' => $positionName,
                        'business_unit_id' => $unit->id,
                        'is_active' => true,
                        'sort_order' => $sortOrder++,
                    ]
                );
            }
        }
    }
}
