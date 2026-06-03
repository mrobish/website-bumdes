<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Account;

class SeedCOASeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            // === 1xxx ASET ===
            // Aset Lancar
            ['code' => '1101', 'name' => 'Kas', 'type' => 'asset', 'normal_balance' => 'debit', 'is_system' => true, 'description' => 'Uang tunai di kas'],
            ['code' => '1102', 'name' => 'Bank', 'type' => 'asset', 'normal_balance' => 'debit', 'is_system' => true, 'description' => 'Saldo rekening bank'],
            ['code' => '1103', 'name' => 'Piutang Usaha', 'type' => 'asset', 'normal_balance' => 'debit', 'is_system' => true, 'description' => 'Piutang dari pelanggan'],
            ['code' => '1104', 'name' => 'Persediaan', 'type' => 'asset', 'normal_balance' => 'debit', 'is_system' => true, 'description' => 'Barang persediaan'],
            
            // Aset Tetap
            ['code' => '1201', 'name' => 'Tanah', 'type' => 'asset', 'normal_balance' => 'debit', 'is_system' => true, 'description' => 'Tanah milik BUMDes'],
            ['code' => '1202', 'name' => 'Bangunan', 'type' => 'asset', 'normal_balance' => 'debit', 'is_system' => true, 'description' => 'Gedung/bangunan'],
            ['code' => '1203', 'name' => 'Kendaraan', 'type' => 'asset', 'normal_balance' => 'debit', 'is_system' => true, 'description' => 'Kendaraan operasional'],
            ['code' => '1204', 'name' => 'Peralatan', 'type' => 'asset', 'normal_balance' => 'debit', 'is_system' => true, 'description' => 'Peralatan kerja'],
            
            // Akumulasi Penyusutan (Contra Asset)
            ['code' => '1291', 'name' => 'Akumulasi Penyusutan Bangunan', 'type' => 'asset', 'normal_balance' => 'credit', 'is_system' => true, 'description' => 'Akumulasi penyusutan bangunan'],
            ['code' => '1292', 'name' => 'Akumulasi Penyusutan Kendaraan', 'type' => 'asset', 'normal_balance' => 'credit', 'is_system' => true, 'description' => 'Akumulasi penyusutan kendaraan'],
            ['code' => '1293', 'name' => 'Akumulasi Penyusutan Peralatan', 'type' => 'asset', 'normal_balance' => 'credit', 'is_system' => true, 'description' => 'Akumulasi penyusutan peralatan'],

            // === 2xxx KEWAJIBAN ===
            ['code' => '2101', 'name' => 'Hutang Usaha', 'type' => 'liability', 'normal_balance' => 'credit', 'is_system' => true, 'description' => 'Hutang kepada pemasok'],
            ['code' => '2102', 'name' => 'Hutang Bank', 'type' => 'liability', 'normal_balance' => 'credit', 'is_system' => true, 'description' => 'Pinjaman bank'],
            ['code' => '2103', 'name' => 'Pendapatan Diterima Dimuka', 'type' => 'liability', 'normal_balance' => 'credit', 'is_system' => true, 'description' => 'Uang muka dari pelanggan'],

            // === 3xxx EKUITAS ===
            ['code' => '3101', 'name' => 'Modal BUMDes', 'type' => 'equity', 'normal_balance' => 'credit', 'is_system' => true, 'description' => 'Modal awal BUMDes'],
            ['code' => '3102', 'name' => 'Laba Ditahan', 'type' => 'equity', 'normal_balance' => 'credit', 'is_system' => true, 'description' => 'Laba yang belum dibagikan'],
            ['code' => '3103', 'name' => 'Penyertaan Modal Pemerintah', 'type' => 'equity', 'normal_balance' => 'credit', 'is_system' => true, 'description' => 'Penyertaan modal dari pemerintah'],
            ['code' => '3104', 'name' => 'Penyertaan Modal Lain', 'type' => 'equity', 'normal_balance' => 'credit', 'is_system' => true, 'description' => 'Penyertaan modal dari pihak lain'],

            // === 4xxx PENDAPATAN ===
            ['code' => '4101', 'name' => 'Pendapatan Usaha', 'type' => 'revenue', 'normal_balance' => 'credit', 'is_system' => true, 'description' => 'Pendapatan dari kegiatan usaha utama'],
            ['code' => '4102', 'name' => 'Pendapatan Jasa', 'type' => 'revenue', 'normal_balance' => 'credit', 'is_system' => true, 'description' => 'Pendapatan dari jasa'],
            ['code' => '4103', 'name' => 'Pendapatan Sewa', 'type' => 'revenue', 'normal_balance' => 'credit', 'is_system' => true, 'description' => 'Pendapatan dari sewa'],
            ['code' => '4201', 'name' => 'Pendapatan Lain-lain', 'type' => 'revenue', 'normal_balance' => 'credit', 'is_system' => true, 'description' => 'Pendapatan non-usaha'],

            // === 5xxx BEBAN / HARGA POKOK PENJUALAN ===
            ['code' => '5101', 'name' => 'Harga Pokok Penjualan', 'type' => 'expense', 'normal_balance' => 'debit', 'is_system' => true, 'description' => 'Biaya langsung penjualan'],
            ['code' => '5102', 'name' => 'Beban Bahan Baku', 'type' => 'expense', 'normal_balance' => 'debit', 'is_system' => true, 'description' => 'Biaya bahan baku'],

            // === 6xxx BEBAN OPERASIONAL ===
            ['code' => '6101', 'name' => 'Beban Gaji', 'type' => 'expense', 'normal_balance' => 'debit', 'is_system' => true, 'description' => 'Gaji karyawan'],
            ['code' => '6102', 'name' => 'Beban Sewa', 'type' => 'expense', 'normal_balance' => 'debit', 'is_system' => true, 'description' => 'Biaya sewa'],
            ['code' => '6103', 'name' => 'Beban Utilitas', 'type' => 'expense', 'normal_balance' => 'debit', 'is_system' => true, 'description' => 'Listrik, air, telepon'],
            ['code' => '6104', 'name' => 'Beban Penyusutan', 'type' => 'expense', 'normal_balance' => 'debit', 'is_system' => true, 'description' => 'Penyusutan aset tetap'],
            ['code' => '6105', 'name' => 'Beban Operasional Lain', 'type' => 'expense', 'normal_balance' => 'debit', 'is_system' => true, 'description' => 'Beban operasional lainnya'],
            ['code' => '6106', 'name' => 'Beban Perbaikan', 'type' => 'expense', 'normal_balance' => 'debit', 'is_system' => true, 'description' => 'Biaya perbaikan aset'],
        ];

        foreach ($accounts as $account) {
            Account::updateOrCreate(
                ['code' => $account['code']],
                $account
            );
        }

        $this->command->info('✅ COASeeder: ' . count($accounts) . ' akun default ditambahkan');
    }
}
