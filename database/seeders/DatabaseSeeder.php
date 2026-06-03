<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // Core
            RoleSeeder::class,
            UserSeeder::class,
            
            // Master Data
            SeedCOASeeder::class,
            SeedCategoriesSeeder::class,
            SeedFiscalYearSeeder::class,
            
            // Existing
            BusinessUnitSeeder::class,
            BumdesSettingSeeder::class,
            BumdesStructureSeeder::class,
        ]);
    }
}
