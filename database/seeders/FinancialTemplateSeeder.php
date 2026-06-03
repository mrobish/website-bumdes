<?php

namespace Database\Seeders;

use App\Services\FinancialTemplateService;
use Illuminate\Database\Seeder;

class FinancialTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $service = app(FinancialTemplateService::class);
        $service->seedDefaultTemplates();

        echo "✓ 8 Financial Templates seeded\n";
    }
}
