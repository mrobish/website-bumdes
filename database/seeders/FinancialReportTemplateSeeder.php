<?php

namespace Database\Seeders;

use App\Models\FinancialReportTemplate;
use Illuminate\Database\Seeder;

class FinancialReportTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Neraca SAK EMKM',
                'code' => 'NERACA',
                'type' => 'balance_sheet',
                'description' => 'Laporan Posisi Keuangan sesuai SAK EMKM.',
                'structure' => json_encode([
                    'rows' => [
                        ['label' => 'ASET', 'type' => 'header'],
                        ['label' => 'Aset Lancar', 'type' => 'section'],
                        ['label' => 'Kas dan Ekivalen Kas', 'type' => 'item', 'account_codes' => ['1101', '1102']],
                        ['label' => 'Piutang', 'type' => 'item', 'account_codes' => ['1103']],
                        ['label' => 'Persediaan', 'type' => 'item', 'account_codes' => ['1104']],
                        ['label' => 'Total Aset Lancar', 'type' => 'subtotal'],
                        ['label' => 'Aset Tidak Lancar', 'type' => 'section'],
                        ['label' => 'Tanah', 'type' => 'item', 'account_codes' => ['1201']],
                        ['label' => 'Bangunan', 'type' => 'item', 'account_codes' => ['1202']],
                        ['label' => 'Peralatan', 'type' => 'item', 'account_codes' => ['1203']],
                        ['label' => 'Kendaraan', 'type' => 'item', 'account_codes' => ['1204']],
                        ['label' => 'Total Aset Tidak Lancar', 'type' => 'subtotal'],
                        ['label' => 'JUMLAH ASET', 'type' => 'total'],
                        ['label' => '', 'type' => 'spacer'],
                        ['label' => 'KEWAJIBAN DAN EKUITAS', 'type' => 'header'],
                        ['label' => 'Kewajiban', 'type' => 'section'],
                        ['label' => 'Kewajiban Lancar', 'type' => 'item', 'account_codes' => ['2101', '2102']],
                        ['label' => 'Total Kewajiban', 'type' => 'subtotal'],
                        ['label' => '', 'type' => 'spacer'],
                        ['label' => 'Ekuitas', 'type' => 'section'],
                        ['label' => 'Modal Setoran', 'type' => 'item', 'account_codes' => ['3101']],
                        ['label' => 'Laba Ditahan', 'type' => 'item', 'account_codes' => ['3102']],
                        ['label' => 'Laba Tahun Berjalan', 'type' => 'item', 'account_codes' => ['3103']],
                        ['label' => 'Total Ekuitas', 'type' => 'subtotal'],
                        ['label' => 'JUMLAH KEWAJIBAN DAN EKUITAS', 'type' => 'total'],
                    ],
                ]),
                'is_default' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Laporan Laba/Rugi Konsolidasi',
                'code' => 'LR_KONSOL',
                'type' => 'income_statement',
                'description' => 'Laporan Laba/Rugi yang menggabungkan seluruh performa BUMDes.',
                'structure' => json_encode([
                    'rows' => [
                        ['label' => 'PENDAPATAN', 'type' => 'header'],
                        ['label' => 'Pendapatan Usaha', 'type' => 'section'],
                        ['label' => 'Penjualan Produk', 'type' => 'item', 'account_codes' => ['4101']],
                        ['label' => 'Pendapatan Jasa', 'type' => 'item', 'account_codes' => ['4102']],
                        ['label' => 'Total Pendapatan Usaha', 'type' => 'subtotal'],
                        ['label' => 'Pendapatan Lain-lain', 'type' => 'section'],
                        ['label' => 'Pendapatan Bunga', 'type' => 'item', 'account_codes' => ['4201']],
                        ['label' => 'Pendapatan Sewa', 'type' => 'item', 'account_codes' => ['4202']],
                        ['label' => 'Total Pendapatan Lain-lain', 'type' => 'subtotal'],
                        ['label' => 'JUMLAH PENDAPATAN', 'type' => 'total'],
                        ['label' => '', 'type' => 'spacer'],
                        ['label' => 'BEBAN', 'type' => 'header'],
                        ['label' => 'Beban Usaha', 'type' => 'section'],
                        ['label' => 'Beban Gaji', 'type' => 'item', 'account_codes' => ['5101']],
                        ['label' => 'Beban Sewa', 'type' => 'item', 'account_codes' => ['5102']],
                        ['label' => 'Beban Listrik', 'type' => 'item', 'account_codes' => ['5103']],
                        ['label' => 'Beban ATK', 'type' => 'item', 'account_codes' => ['5104']],
                        ['label' => 'Total Beban Usaha', 'type' => 'subtotal'],
                        ['label' => 'JUMLAH BEBAN', 'type' => 'total'],
                        ['label' => '', 'type' => 'spacer'],
                        ['label' => 'LABA BERSIH', 'type' => 'total_highlight'],
                    ],
                ]),
                'is_default' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Laporan Arus Kas',
                'code' => 'ARUS_KAS',
                'type' => 'cash_flow',
                'description' => 'Laporan Arus Kas sesuai SAK EMKM.',
                'structure' => json_encode([
                    'rows' => [
                        ['label' => 'ARUS KAS DARI AKTIVITAS OPERASIONAL', 'type' => 'header'],
                        ['label' => 'Penerimaan dari pelanggan', 'type' => 'item'],
                        ['label' => 'Pembayaran kepada pemasok', 'type' => 'item', 'negative' => true],
                        ['label' => 'Pembayaran gaji', 'type' => 'item', 'negative' => true],
                        ['label' => 'Kas Bersih dari Aktivitas Operasional', 'type' => 'subtotal'],
                        ['label' => '', 'type' => 'spacer'],
                        ['label' => 'ARUS KAS DARI AKTIVITAS INVESTASI', 'type' => 'header'],
                        ['label' => 'Pembelian aset tetap', 'type' => 'item', 'negative' => true],
                        ['label' => 'Penjualan aset tetap', 'type' => 'item'],
                        ['label' => 'Kas Bersih dari Aktivitas Investasi', 'type' => 'subtotal'],
                        ['label' => '', 'type' => 'spacer'],
                        ['label' => 'ARUS KAS DARI AKTIVITAS PENDANAAN', 'type' => 'header'],
                        ['label' => 'Penyertaan modal dari desa', 'type' => 'item'],
                        ['label' => 'Pembayaran dividen', 'type' => 'item', 'negative' => true],
                        ['label' => 'Kas Bersih dari Aktivitas Pendanaan', 'type' => 'subtotal'],
                        ['label' => '', 'type' => 'spacer'],
                        ['label' => 'PENAMBAHAN (PENGURANGAN) KAS BERSIH', 'type' => 'total_highlight'],
                        ['label' => 'Saldo Kas Awal', 'type' => 'item'],
                        ['label' => 'SALDO KAS AKHIR', 'type' => 'total'],
                    ],
                ]),
                'is_default' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Catatan Atas Laporan Keuangan (CALK)',
                'code' => 'CALK',
                'type' => 'notes',
                'description' => 'Rincian penjelasan angka-angka di dalam laporan keuangan.',
                'structure' => json_encode([
                    'rows' => [
                        ['label' => '1. Umum', 'type' => 'header'],
                        ['label' => '1.1 Bentuk Badan Hukum', 'type' => 'item'],
                        ['label' => '1.2 Bidang Usaha', 'type' => 'item'],
                        ['label' => '1.3 Periode Pelaporan', 'type' => 'item'],
                        ['label' => '2. Ikhtisar Laporan Keuangan', 'type' => 'header'],
                        ['label' => '2.1 Ikhtisar Posisi Keuangan', 'type' => 'item'],
                        ['label' => '2.2 Ikhtisar Hasil Usaha', 'type' => 'item'],
                        ['label' => '3. Ikhtisar Posisi Keuangan', 'type' => 'header'],
                        ['label' => '3.1 Kas dan Ekivalen Kas', 'type' => 'item'],
                        ['label' => '3.2 Piutang', 'type' => 'item'],
                        ['label' => '3.3 Aset Tetap', 'type' => 'item'],
                        ['label' => '3.4 Kewajiban', 'type' => 'item'],
                        ['label' => '3.5 Ekuitas', 'type' => 'item'],
                        ['label' => '4. Ikhtisar Hasil Usaha', 'type' => 'header'],
                        ['label' => '4.1 Pendapatan', 'type' => 'item'],
                        ['label' => '4.2 Beban', 'type' => 'item'],
                    ],
                ]),
                'is_default' => true,
                'is_active' => true,
            ],
        ];

        foreach ($templates as $template) {
            FinancialReportTemplate::updateOrCreate(
                ['code' => $template['code']],
                $template
            );
        }

        $this->command->info('Financial report templates seeded: ' . count($templates) . ' templates');
    }
}
