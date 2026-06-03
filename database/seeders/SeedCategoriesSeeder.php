<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class SeedCategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // PENDAPATAN
            ['name' => 'Penjualan Produk', 'type' => 'income', 'default_account_code' => '4101'],
            ['name' => 'Jasa Layanan', 'type' => 'income', 'default_account_code' => '4102'],
            ['name' => 'Sewa Tempat', 'type' => 'income', 'default_account_code' => '4103'],
            ['name' => 'Tiket Wisata', 'type' => 'income', 'default_account_code' => '4101'],
            ['name' => 'Hasil Panen', 'type' => 'income', 'default_account_code' => '4101'],
            ['name' => 'Jasa Pengolahan Sampah', 'type' => 'income', 'default_account_code' => '4102'],
            ['name' => 'Pendapatan Bunga Bank', 'type' => 'income', 'default_account_code' => '4201'],
            ['name' => 'Pendapatan Lain-lain', 'type' => 'income', 'default_account_code' => '4201'],

            // PENGELUARAN
            ['name' => 'Beli Bahan Baku', 'type' => 'expense', 'default_account_code' => '5102'],
            ['name' => 'Gaji Karyawan', 'type' => 'expense', 'default_account_code' => '6101'],
            ['name' => 'Sewa Tempat', 'type' => 'expense', 'default_account_code' => '6102'],
            ['name' => 'Listrik & Air', 'type' => 'expense', 'default_account_code' => '6103'],
            ['name' => 'Perbaikan Peralatan', 'type' => 'expense', 'default_account_code' => '6106'],
            ['name' => 'Beli Pupuk & Bibit', 'type' => 'expense', 'default_account_code' => '5102'],
            ['name' => 'Operasional Harian', 'type' => 'expense', 'default_account_code' => '6105'],
            ['name' => 'Transport & Perjalanan', 'type' => 'expense', 'default_account_code' => '6105'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['name' => $category['name'], 'type' => $category['type']],
                $category
            );
        }

        $this->command->info('✅ CategoriesSeeder: ' . count($categories) . ' kategori ditambahkan');
    }
}
