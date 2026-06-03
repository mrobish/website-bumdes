<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FiscalYear;

class SeedFiscalYearSeeder extends Seeder
{
    public function run(): void
    {
        $currentYear = (int) date('Y');
        
        FiscalYear::updateOrCreate(
            ['year' => $currentYear],
            ['status' => 'open']
        );

        FiscalYear::updateOrCreate(
            ['year' => $currentYear - 1],
            ['status' => 'closed']
        );

        $this->command->info('✅ FiscalYearSeeder: Tahun ' . $currentYear . ' (open) & ' . ($currentYear - 1) . ' (closed)');
    }
}
