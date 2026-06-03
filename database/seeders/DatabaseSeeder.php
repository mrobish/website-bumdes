<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            VillageInfoSeeder::class,
            BusinessUnitSeeder::class,
            FinancialReportTemplateSeeder::class,
            BumdesSettingSeeder::class,
            BumdesStructureSeeder::class,
        ]);
    }
}
