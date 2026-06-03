<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChartOfAccount;

class ChartOfAccountSeeder extends Seeder
{
    public function run(): void
    {
        // ========== ASET ==========
        
        // Aset Lancar
        $asetLancar = ChartOfAccount::create([
            'code' => '1100',
            'name' => 'Aset Lancar',
            'type' => 'aset',
            'is_group' => true,
            'description' => 'Aset yang dapat dikonversi menjadi kas dalam waktu satu tahun',
        ]);

        ChartOfAccount::create(['code' => '1101', 'name' => 'Kas', 'type' => 'aset', 'parent_id' => $asetLancar->id, 'description' => 'Kas tunai']);
        ChartOfAccount::create(['code' => '1102', 'name' => 'Bank', 'type' => 'aset', 'parent_id' => $asetLancar->id, 'description' => 'Simpanan di bank']);
        ChartOfAccount::create(['code' => '1103', 'name' => 'Piutang', 'type' => 'aset', 'parent_id' => $asetLancar->id, 'description' => 'Piutang dari penjualan']);
        ChartOfAccount::create(['code' => '1104', 'name' => 'Persediaan', 'type' => 'aset', 'parent_id' => $asetLancar->id, 'description' => 'Barang dagang']);

        // Aset Tetap
        $asetTetap = ChartOfAccount::create([
            'code' => '1200',
            'name' => 'Aset Tetap',
            'type' => 'aset',
            'is_group' => true,
            'description' => 'Aset jangka panjang',
        ]);

        ChartOfAccount::create(['code' => '1201', 'name' => 'Tanah', 'type' => 'aset', 'parent_id' => $asetTetap->id]);
        ChartOfAccount::create(['code' => '1202', 'name' => 'Bangunan', 'type' => 'aset', 'parent_id' => $asetTetap->id]);
        ChartOfAccount::create(['code' => '1203', 'name' => 'Peralatan', 'type' => 'aset', 'parent_id' => $asetTetap->id]);
        ChartOfAccount::create(['code' => '1204', 'name' => 'Kendaraan', 'type' => 'aset', 'parent_id' => $asetTetap->id]);

        // ========== KEWAJIBAN ==========
        
        $kewajibanLancar = ChartOfAccount::create([
            'code' => '2100',
            'name' => 'Kewajiban Lancar',
            'type' => 'kewajiban',
            'is_group' => true,
        ]);

        ChartOfAccount::create(['code' => '2101', 'name' => 'Hutang Usaha', 'type' => 'kewajiban', 'parent_id' => $kewajibanLancar->id]);
        ChartOfAccount::create(['code' => '2102', 'name' => 'Hutang Bank', 'type' => 'kewajiban', 'parent_id' => $kewajibanLancar->id]);

        // ========== MODAL ==========
        
        $modal = ChartOfAccount::create([
            'code' => '3100',
            'name' => 'Modal',
            'type' => 'modal',
            'is_group' => true,
        ]);

        ChartOfAccount::create(['code' => '3101', 'name' => 'Modal Setoran', 'type' => 'modal', 'parent_id' => $modal->id]);
        ChartOfAccount::create(['code' => '3102', 'name' => 'Laba Ditahan', 'type' => 'modal', 'parent_id' => $modal->id]);
        ChartOfAccount::create(['code' => '3103', 'name' => 'Laba Tahun Berjalan', 'type' => 'modal', 'parent_id' => $modal->id]);

        // ========== PENDAPATAN ==========
        
        $pendapatanUsaha = ChartOfAccount::create([
            'code' => '4100',
            'name' => 'Pendapatan Usaha',
            'type' => 'pendapatan',
            'is_group' => true,
        ]);

        ChartOfAccount::create(['code' => '4101', 'name' => 'Penjualan Produk', 'type' => 'pendapatan', 'parent_id' => $pendapatanUsaha->id]);
        ChartOfAccount::create(['code' => '4102', 'name' => 'Pendapatan Jasa', 'type' => 'pendapatan', 'parent_id' => $pendapatanUsaha->id]);

        $pendapatanLain = ChartOfAccount::create([
            'code' => '4200',
            'name' => 'Pendapatan Lain',
            'type' => 'pendapatan',
            'is_group' => true,
        ]);

        ChartOfAccount::create(['code' => '4201', 'name' => 'Pendapatan Bunga', 'type' => 'pendapatan', 'parent_id' => $pendapatanLain->id]);
        ChartOfAccount::create(['code' => '4202', 'name' => 'Pendapatan Sewa', 'type' => 'pendapatan', 'parent_id' => $pendapatanLain->id]);

        // ========== BEBAN ==========
        
        $bebanUsaha = ChartOfAccount::create([
            'code' => '5100',
            'name' => 'Beban Usaha',
            'type' => 'beban',
            'is_group' => true,
        ]);

        ChartOfAccount::create(['code' => '5101', 'name' => 'Beban Gaji', 'type' => 'beban', 'parent_id' => $bebanUsaha->id]);
        ChartOfAccount::create(['code' => '5102', 'name' => 'Beban Sewa', 'type' => 'beban', 'parent_id' => $bebanUsaha->id]);
        ChartOfAccount::create(['code' => '5103', 'name' => 'Beban Listrik', 'type' => 'beban', 'parent_id' => $bebanUsaha->id]);
        ChartOfAccount::create(['code' => '5104', 'name' => 'Beban ATK', 'type' => 'beban', 'parent_id' => $bebanUsaha->id]);

        $bebanLain = ChartOfAccount::create([
            'code' => '5200',
            'name' => 'Beban Lain',
            'type' => 'beban',
            'is_group' => true,
        ]);

        ChartOfAccount::create(['code' => '5201', 'name' => 'Beban Administrasi Bank', 'type' => 'beban', 'parent_id' => $bebanLain->id]);
        ChartOfAccount::create(['code' => '5202', 'name' => 'Beban Pajak', 'type' => 'beban', 'parent_id' => $bebanLain->id]);
    }
}
