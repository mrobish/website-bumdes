<?php

namespace App\Services;

use App\Models\FinancialTemplate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Storage;

class FinancialTemplateService
{
    /**
     * Generate Excel template for download
     */
    public function generateTemplate(FinancialTemplate $template): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $titleStyle = [
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => 'center'],
        ];

        $headerStyle = [
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '2C3E50']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            'borders' => ['allBorders' => ['borderStyle' => 'thin']],
        ];

        $dataStyle = [
            'borders' => ['allBorders' => ['borderStyle' => 'thin']],
            'alignment' => ['vertical' => 'center'],
        ];

        $currencyFormat = '#,##0';

        // Set title
        $sheet->setCellValue('A1', 'BUMDes Keude Bakongan');
        $sheet->getStyle('A1')->applyFromArray($titleStyle);
        $sheet->mergeCells('A1:' . $this->getLastColumn($template) . '1');

        $sheet->setCellValue('A2', $template->name);
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12],
            'alignment' => ['horizontal' => 'center'],
        ]);
        $sheet->mergeCells('A2:' . $this->getLastColumn($template) . '2');

        $sheet->setCellValue('A3', 'Isi data pada baris yang kosong. Baris berwarna abu-abu adalah contoh.');
        $sheet->getStyle('A3')->applyFromArray(['alignment' => ['horizontal' => 'center']]);
        $sheet->mergeCells('A3:' . $this->getLastColumn($template) . '3');

        // Set headers
        $columns = is_string($template->columns) ? json_decode($template->columns, true) : ($template->columns ?? []);
        $headerRow = 5;
        foreach ($columns as $col => $header) {
            $cell = $this->getColumnLetter($col + 1) . $headerRow;
            $sheet->setCellValue($cell, $header['name'] ?? $header);
            $sheet->getStyle($cell)->applyFromArray($headerStyle);
            $sheet->getColumnDimension($this->getColumnLetter($col + 1))->setWidth($header['width'] ?? 20);
        }

        // Add sample data
        $sampleData = is_string($template->sample_data) ? json_decode($template->sample_data, true) : ($template->sample_data ?? []);
        if (!empty($sampleData)) {
            $sampleRow = $headerRow + 1;
            foreach ($sampleData as $row) {
                foreach ($columns as $col => $header) {
                    $cell = $this->getColumnLetter($col + 1) . $sampleRow;
                    $value = $row[$header['key'] ?? $col] ?? '';
                    $sheet->setCellValue($cell, $value);
                    $sheet->getStyle($cell)->applyFromArray($dataStyle);
                    if (isset($header['type']) && $header['type'] === 'number') {
                        $sheet->getStyle($cell)->getNumberFormat()->setFormatCode($currencyFormat);
                    }
                }
                $sheet->getStyle('A' . $sampleRow . ':' . $this->getLastColumn($template) . $sampleRow)
                    ->applyFromArray(['fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'ECF0F1']]]);
                $sampleRow++;
            }
        }

        // Add empty rows
        $startRow = $headerRow + count($sampleData) + 2;
        for ($i = 0; $i < 50; $i++) {
            $row = $startRow + $i;
            foreach ($columns as $col => $header) {
                $cell = $this->getColumnLetter($col + 1) . $row;
                $sheet->getStyle($cell)->applyFromArray($dataStyle);
                if (isset($header['type']) && $header['type'] === 'number') {
                    $sheet->getStyle($cell)->getNumberFormat()->setFormatCode($currencyFormat);
                }
            }
        }

        // ===== SHEET PETUNJUK DETAIL =====
        $inst = $spreadsheet->createSheet();
        $inst->setTitle('Petunjuk');
        $inst->getColumnDimension('A')->setWidth(5);
        $inst->getColumnDimension('B')->setWidth(65);
        $inst->getColumnDimension('C')->setWidth(30);

        $row = 1;
        $inst->setCellValue("A{$row}", '');
        $inst->setCellValue("B{$row}", 'PETUNJUK PENGISIAN - ' . strtoupper($template->name));
        $inst->getStyle("B{$row}")->applyFromArray(['font' => ['bold' => true, 'size' => 16]]);

        $row = 3;
        $inst->setCellValue("B{$row}", '═══════════════════════════════════════════════════');
        $inst->getStyle("B{$row}")->applyFromArray(['font' => ['color' => ['rgb' => '2C3E50']]]);

        // --- CARA PENGISIAN ---
        $row = 4;
        $inst->setCellValue("B{$row}", '📋 CARA PENGISIAN');
        $inst->getStyle("B{$row}")->applyFromArray(['font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => '2C3E50']]]);

        $instructions = [
            '1. Download template ini dengan klik tombol "Download" di aplikasi',
            '2. Buka file dengan Microsoft Excel 2016+ atau LibreOffice Calc',
            '3. Aktifkan mode "Edit" jika ada notifikasi Protected View',
            '4. Isi data pada sheet "' . $template->name . '" (sheet pertama)',
            '5. Baris BERWARNA ABU-ABU adalah CONTOH, bisa dihapus atau diubah',
            '6. JANGAN mengubah header/baris judul (baris 1-5)',
            '7. Simpan file (Ctrl+S) dalam format .xlsx',
            '8. Upload kembali ke aplikasi melalui menu "Upload Laporan"',
        ];
        foreach ($instructions as $i => $text) {
            $row++;
            $inst->setCellValue("B{$row}", $text);
        }

        // --- FORMAT DATA ---
        $row += 2;
        $inst->setCellValue("B{$row}", '📝 FORMAT DATA');
        $inst->getStyle("B{$row}")->applyFromArray(['font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => '2C3E50']]]);

        $formats = [
            'Tanggal     : DD/MM/YYYY (contoh: 15/01/2026)',
            'Angka       : Tanpa titik/koma, langsung angka (contoh: 5000000, bukan 5.000.000)',
            'Kode Akun   : Harus sesuai dengan daftar kode akun di bawah',
            'Kolom (*)   : WAJIB diisi, tidak boleh kosong',
            'Kolom tanpa *: Boleh dikosongkan jika tidak relevan',
        ];
        foreach ($formats as $text) {
            $row++;
            $inst->setCellValue("B{$row}", $text);
            $inst->getStyle("B{$row}")->applyFromArray(['font' => ['name' => 'Consolas', 'size' => 10]]);
        }

        // --- DAFTAR KODE AKUN ---
        $row += 2;
        $inst->setCellValue("B{$row}", '📊 DAFTAR KODE AKUN YANG BERLAKU');
        $inst->getStyle("B{$row}")->applyFromArray(['font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => '2C3E50']]]);

        $row++;
        $inst->setCellValue("B{$row}", 'Gunakan HANYA kode akun di bawah ini. Jika kode tidak ada, konsultasikan ke admin.');
        $inst->getStyle("B{$row}")->applyFromArray(['font' => ['italic' => true, 'color' => ['rgb' => 'E74C3C']]]);

        // Header tabel akun
        $row += 2;
        $inst->setCellValue("A{$row}", '');
        $inst->setCellValue("B{$row}", 'Kode');
        $inst->setCellValue("C{$row}", 'Nama Akun');
        $inst->getStyle("B{$row}")->applyFromArray(['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '3498DB']], 'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']]]);
        $inst->getStyle("C{$row}")->applyFromArray(['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '3498DB']], 'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']]]);

        // ASET LANCAR
        $row++;
        $inst->setCellValue("B{$row}", '── ASET LANCAR ──');
        $inst->getStyle("B{$row}")->applyFromArray(['font' => ['bold' => true, 'color' => ['rgb' => '27AE60']]]);

        $asetLancar = [
            ['1100', 'Aset Lancar (Induk)'],
            ['1101', 'Kas'],
            ['1102', 'Bank'],
            ['1103', 'Piutang'],
            ['1104', 'Persediaan'],
        ];
        foreach ($asetLancar as $a) {
            $row++;
            $inst->setCellValue("B{$row}", $a[0]);
            $inst->setCellValue("C{$row}", $a[1]);
            $inst->getStyle("B{$row}")->applyFromArray(['font' => ['name' => 'Consolas', 'size' => 10]]);
        }

        // ASET TETAP
        $row++;
        $inst->setCellValue("B{$row}", '── ASET TETAP ──');
        $inst->getStyle("B{$row}")->applyFromArray(['font' => ['bold' => true, 'color' => ['rgb' => '27AE60']]]);

        $asetTetap = [
            ['1200', 'Aset Tetap (Induk)'],
            ['1201', 'Tanah'],
            ['1202', 'Bangunan'],
            ['1203', 'Peralatan'],
            ['1204', 'Kendaraan'],
        ];
        foreach ($asetTetap as $a) {
            $row++;
            $inst->setCellValue("B{$row}", $a[0]);
            $inst->setCellValue("C{$row}", $a[1]);
            $inst->getStyle("B{$row}")->applyFromArray(['font' => ['name' => 'Consolas', 'size' => 10]]);
        }

        // KEWAJIBAN
        $row++;
        $inst->setCellValue("B{$row}", '── KEWAJIBAN ──');
        $inst->getStyle("B{$row}")->applyFromArray(['font' => ['bold' => true, 'color' => ['rgb' => 'E74C3C']]]);

        $kewajiban = [
            ['2100', 'Kewajiban Lancar (Induk)'],
            ['2101', 'Hutang Usaha'],
            ['2102', 'Hutang Bank'],
        ];
        foreach ($kewajiban as $a) {
            $row++;
            $inst->setCellValue("B{$row}", $a[0]);
            $inst->setCellValue("C{$row}", $a[1]);
            $inst->getStyle("B{$row}")->applyFromArray(['font' => ['name' => 'Consolas', 'size' => 10]]);
        }

        // MODAL
        $row++;
        $inst->setCellValue("B{$row}", '── MODAL ──');
        $inst->getStyle("B{$row}")->applyFromArray(['font' => ['bold' => true, 'color' => ['rgb' => '8E44AD']]]);

        $modal = [
            ['3100', 'Modal (Induk)'],
            ['3101', 'Modal Setoran'],
            ['3102', 'Laba Ditahan'],
            ['3103', 'Laba Tahun Berjalan'],
        ];
        foreach ($modal as $a) {
            $row++;
            $inst->setCellValue("B{$row}", $a[0]);
            $inst->setCellValue("C{$row}", $a[1]);
            $inst->getStyle("B{$row}")->applyFromArray(['font' => ['name' => 'Consolas', 'size' => 10]]);
        }

        // PENDAPATAN
        $row++;
        $inst->setCellValue("B{$row}", '── PENDAPATAN ──');
        $inst->getStyle("B{$row}")->applyFromArray(['font' => ['bold' => true, 'color' => ['rgb' => 'F39C12']]]);

        $pendapatan = [
            ['4100', 'Pendapatan Usaha (Induk)'],
            ['4101', 'Penjualan Produk'],
            ['4102', 'Pendapatan Jasa'],
            ['4200', 'Pendapatan Lain (Induk)'],
            ['4201', 'Pendapatan Bunga'],
            ['4202', 'Pendapatan Sewa'],
        ];
        foreach ($pendapatan as $a) {
            $row++;
            $inst->setCellValue("B{$row}", $a[0]);
            $inst->setCellValue("C{$row}", $a[1]);
            $inst->getStyle("B{$row}")->applyFromArray(['font' => ['name' => 'Consolas', 'size' => 10]]);
        }

        // BEBAN
        $row++;
        $inst->setCellValue("B{$row}", '── BEBAN ──');
        $inst->getStyle("B{$row}")->applyFromArray(['font' => ['bold' => true, 'color' => ['rgb' => 'E74C3C']]]);

        $beban = [
            ['5100', 'Beban Usaha (Induk)'],
            ['5101', 'Beban Gaji'],
            ['5102', 'Beban Sewa'],
            ['5103', 'Beban Listrik'],
            ['5104', 'Beban ATK'],
            ['5200', 'Beban Lain (Induk)'],
            ['5201', 'Beban Administrasi Bank'],
            ['5202', 'Beban Pajak'],
        ];
        foreach ($beban as $a) {
            $row++;
            $inst->setCellValue("B{$row}", $a[0]);
            $inst->setCellValue("C{$row}", $a[1]);
            $inst->getStyle("B{$row}")->applyFromArray(['font' => ['name' => 'Consolas', 'size' => 10]]);
        }

        // --- CONTOH TRANSAKSI ---
        $row += 2;
        $inst->setCellValue("B{$row}", '💡 CONTOH TRANSAKSI (untuk referensi)');
        $inst->getStyle("B{$row}")->applyFromArray(['font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => '2C3E50']]]);

        $examples = $this->getTransactionExamples($template->type);
        foreach ($examples as $ex) {
            $row++;
            $inst->setCellValue("B{$row}", $ex);
            $inst->getStyle("B{$row}")->applyFromArray(['font' => ['name' => 'Consolas', 'size' => 9]]);
        }

        // --- ATURAN PENTING ---
        $row += 2;
        $inst->setCellValue("B{$row}", '⚠️  ATURAN PENTING');
        $inst->getStyle("B{$row}")->applyFromArray(['font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => 'E74C3C']]]);

        $rules = $this->getTemplateRules($template->type);
        foreach ($rules as $r) {
            $row++;
            $inst->setCellValue("B{$row}", $r);
        }

        // --- KONTAK ---
        $row += 2;
        $inst->setCellValue("B{$row}", '📞 KONTAK & BANTUAN');
        $inst->getStyle("B{$row}")->applyFromArray(['font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => '2C3E50']]]);

        $contacts = [
            'Admin BUMDes  : admin@bumdeskeude.id',
            'Bendahara     : bendahara@bumdeskeude.id',
            'Website       : https://bumdes.ondesa.id',
        ];
        foreach ($contacts as $c) {
            $row++;
            $inst->setCellValue("B{$row}", $c);
            $inst->getStyle("B{$row}")->applyFromArray(['font' => ['name' => 'Consolas', 'size' => 10]]);
        }

        // Save
        $fileName = 'templates/' . $template->slug . '-template.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save(storage_path('app/' . $fileName));

        return $fileName;
    }

    /**
     * Get transaction examples based on template type
     */
    protected function getTransactionExamples(string $type): array
    {
        return match($type) {
            'jurnal' => [
                'Contoh 1: Setoran modal dari desa',
                '  Tanggal: 01/01/2026 | Ref: J-001',
                '  Debet  : 1101 Kas ................. 50.000.000',
                '  Kredit : 3101 Modal Setoran ........ 50.000.000',
                '',
                'Contoh 2: Penjualan produk',
                '  Tanggal: 05/01/2026 | Ref: J-002',
                '  Debet  : 1101 Kas ................. 10.000.000',
                '  Kredit : 4101 Penjualan Produk ..... 10.000.000',
                '',
                'Contoh 3: Beli bahan baku',
                '  Tanggal: 10/01/2026 | Ref: J-003',
                '  Debet  : 1104 Persediaan ........... 5.000.000',
                '  Kredit : 1101 Kas .................. 5.000.000',
                '',
                'Contoh 4: Bayar gaji karyawan',
                '  Tanggal: 28/01/2026 | Ref: J-004',
                '  Debet  : 5101 Beban Gaji ........... 3.000.000',
                '  Kredit : 1101 Kas .................. 3.000.000',
                '',
                'Contoh 5: Beli peralatan (aset tetap)',
                '  Tanggal: 15/02/2026 | Ref: J-010',
                '  Debet  : 1203 Peralatan ............ 25.000.000',
                '  Kredit : 1102 Bank .................. 25.000.000',
            ],
            'buku_besar' => [
                'Isi rekapitulasi per akun per bulan.',
                'Contoh: Akun 1101 Kas bulan Januari',
                '  Saldo Awal : 0',
                '  Total Debet: 60.000.000 (total semua penerimaan)',
                '  Total Kredit: 8.000.000 (total semua pengeluaran)',
                '  Saldo Akhir: 52.000.000',
                '',
                'Rumus: Saldo Awal + Total Debet - Total Kredit = Saldo Akhir',
            ],
            'laba_rugi' => [
                'Pendapatan (kode 4xxx):',
                '  4101 Penjualan Produk',
                '  4102 Pendapatan Jasa',
                '  4201 Pendapatan Bunga',
                '  4202 Pendapatan Sewa',
                '',
                'Beban (kode 5xxx):',
                '  5101 Beban Gaji',
                '  5102 Beban Sewa',
                '  5103 Beban Listrik',
                '  5104 Beban ATK',
                '  5201 Beban Administrasi Bank',
                '  5202 Beban Pajak',
                '',
                'Rumus: Total Pendapatan - Total Beban = Laba Bersih',
            ],
            'neraca' => [
                'Aset Lancar (kode 11xx):',
                '  1101 Kas, 1102 Bank, 1103 Piutang, 1104 Persediaan',
                '',
                'Aset Tetap (kode 12xx):',
                '  1201 Tanah, 1202 Bangunan, 1203 Peralatan, 1204 Kendaraan',
                '',
                'Kewajiban (kode 2xxx):',
                '  2101 Hutang Usaha, 2102 Hutang Bank',
                '',
                'Ekuitas/Modal (kode 3xxx):',
                '  3101 Modal Setoran, 3102 Laba Ditahan, 3103 Laba Tahun Berjalan',
                '',
                'Rumus: Total Aset = Total Kewajiban + Total Ekuitas',
            ],
            'arus_kas' => [
                'Kategori OPERASI (kas dari operasi harian):',
                '  Penerimaan dari penjualan, pembayaran supplier, gaji, sewa',
                '',
                'Kategori INVESTASI (kas dari beli/jual aset):',
                '  Pembelian aset tetap, penjualan aset',
                '',
                'Kategori PENDANAAN (kas dari pinjaman/modal):',
                '  Setoran modal, pinjaman bank, pembayaran hutang',
                '',
                'Gunakan angka NEGATIF untuk pengeluaran.',
            ],
            'modal' => [
                'Isi perubahan modal selama periode:',
                '  1. Saldo Awal Modal (dari periode sebelumnya)',
                '  2. Penyertaan Modal (tambahan dari desa/pihak lain)',
                '  3. Laba Tahun Berjalan (dari Laba Rugi)',
                '  4. Penarikan Modal (jika ada)',
                '',
                'Rumus: Saldo Awal + Penyertaan + Laba - Penarikan = Saldo Akhir',
            ],
            'realisasi_anggaran' => [
                'Isi per program/kegiatan:',
                '  Kolom Anggaran: rencana awal dari APBDes',
                '  Kolom Realisasi: pengeluaran aktual',
                '  Kolom Persentase: (Realisasi / Anggaran) x 100',
                '  Kolom Selisih: Anggaran - Realisasi',
                '',
                'Contoh:',
                '  Anggaran belanja bahan: 30.000.000',
                '  Realisasi: 28.000.000',
                '  Persentase: 93.33%',
                '  Selisih: 2.000.000 (sisa)',
            ],
            'cat' => [
                'CAT berisi penjelasan atas laporan keuangan:',
                '  1. Kebijakan Akuntansi (metode yang digunakan)',
                '  2. Aset Tetap & Depresiasi',
                '  3. Modal & Penyertaan',
                '  4. Sistem Pengendalian Internal',
                '  5. Risiko Usaha',
                '  6. Peristiwa Setelah Tanggal Laporan',
                '',
                'Isi kolom "Nilai" hanya jika ada angka.',
            ],
            default => [],
        };
    }

    /**
     * Get template-specific rules
     */
    protected function getTemplateRules(string $type): array
    {
        return match($type) {
            'jurnal' => [
                '✗  Total Debet HARUS sama dengan Total Kredit',
                '✗  Setiap transaksi minimal 2 baris (1 debet, 1 kredit)',
                '✗  Kode akun harus sesuai daftar di atas',
                '✗  Tanggal harus dalam format DD/MM/YYYY',
                '✗  Angka tanpa titik/koma (5000000, bukan 5.000.000)',
                '✓  Ref boleh kosong, akan diisi otomatis oleh sistem',
            ],
            'neraca' => [
                '✗  Total Aset HARUS sama dengan Total Kewajiban + Ekuitas',
                '✗  Kode akun harus sesuai daftar di atas',
                '✗  Angka saldo harus POSITIF',
                '✓  Jika ada koreksi, gunakan angka negatif',
            ],
            'laba_rugi' => [
                '✗  Kode 4xxx = Pendapatan (angka positif)',
                '✗  Kode 5xxx = Beban (angka positif, jangan negatif)',
                '✗  Laba Bersih = Total Pendapatan - Total Beban',
            ],
            default => [
                '✗  Kode akun harus sesuai daftar di atas',
                '✗  Kolom bertanda (*) wajib diisi',
                '✗  Angka tanpa titik/koma',
                '✓  Simpan dalam format .xlsx',
            ],
        };
    }

    /**
     * Parse uploaded Excel file
     */
    public function parseUploadedFile(string $filePath, FinancialTemplate $template): array
    {
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(storage_path('app/' . $filePath));
        $sheet = $spreadsheet->getActiveSheet();

        $columns = is_string($template->columns) ? json_decode($template->columns, true) : ($template->columns ?? []);
        $data = [];
        $errors = [];
        $startRow = 6;

        $highestRow = $sheet->getHighestRow();

        for ($row = $startRow; $row <= $highestRow; $row++) {
            $rowData = [];
            $isEmpty = true;

            foreach ($columns as $col => $header) {
                $cell = $this->getColumnLetter($col + 1) . $row;
                $value = $sheet->getCell($cell)->getValue();
                $key = $header['key'] ?? 'col_' . $col;

                $rowData[$key] = $value;

                if (!empty($value) && $value !== null) {
                    $isEmpty = false;
                }
            }

            if (!$isEmpty) {
                $rowErrors = $this->validateRow($rowData, $columns, $row);
                if (!empty($rowErrors)) {
                    $errors = array_merge($errors, $rowErrors);
                }
                $data[] = $rowData;
            }
        }

        return [
            'data' => $data,
            'errors' => $errors,
            'total_rows' => count($data),
        ];
    }

    protected function validateRow(array $rowData, array $columns, int $row): array
    {
        $errors = [];

        foreach ($columns as $col => $header) {
            $key = $header['key'] ?? 'col_' . $col;
            $value = $rowData[$key] ?? null;
            $rules = $header['rules'] ?? [];

            if (in_array('required', $rules) && empty($value)) {
                $errors[] = "Baris $row: Kolom '{$header['name']}' wajib diisi";
            }

            if (in_array('numeric', $rules) && !empty($value) && !is_numeric($value)) {
                $errors[] = "Baris $row: Kolom '{$header['name']}' harus berupa angka";
            }
        }

        return $errors;
    }

    protected function getColumnLetter(int $column): string
    {
        $letter = '';
        while ($column > 0) {
            $column--;
            $letter = chr(65 + ($column % 26)) . $letter;
            $column = intdiv($column, 26);
        }
        return $letter;
    }

    protected function getLastColumn(FinancialTemplate $template): string
    {
        $columns = is_string($template->columns) ? json_decode($template->columns, true) : ($template->columns ?? []);
        return $this->getColumnLetter(count($columns));
    }

    /**
     * Seed default templates
     */
    public function seedDefaultTemplates(): void
    {
        $templates = [
            [
                'name' => 'Jurnal Umum',
                'slug' => 'jurnal-umum',
                'description' => 'Template jurnal umum untuk mencatat seluruh transaksi keuangan',
                'type' => 'jurnal',
                'frequency' => 'harian',
                'columns' => json_encode([
                    ['key' => 'tanggal', 'name' => 'Tanggal (*)', 'type' => 'date', 'width' => 15, 'rules' => ['required']],
                    ['key' => 'no_ref', 'name' => 'No. Ref', 'type' => 'text', 'width' => 15],
                    ['key' => 'kode_akun', 'name' => 'Kode Akun (*)', 'type' => 'text', 'width' => 15, 'rules' => ['required']],
                    ['key' => 'nama_akun', 'name' => 'Nama Akun (*)', 'type' => 'text', 'width' => 25, 'rules' => ['required']],
                    ['key' => 'keterangan', 'name' => 'Keterangan (*)', 'type' => 'text', 'width' => 35, 'rules' => ['required']],
                    ['key' => 'debet', 'name' => 'Debet (Rp)', 'type' => 'number', 'width' => 18],
                    ['key' => 'kredit', 'name' => 'Kredit (Rp)', 'type' => 'number', 'width' => 18],
                ]),
                'sample_data' => json_encode([
                    ['tanggal' => '01/01/2026', 'no_ref' => 'J-001', 'kode_akun' => '1101', 'nama_akun' => 'Kas', 'keterangan' => 'Setoran modal dari Pemerintah Desa', 'debet' => 50000000, 'kredit' => 0],
                    ['tanggal' => '01/01/2026', 'no_ref' => 'J-001', 'kode_akun' => '3101', 'nama_akun' => 'Modal Setoran', 'keterangan' => 'Setoran modal dari Pemerintah Desa', 'debet' => 0, 'kredit' => 50000000],
                    ['tanggal' => '05/01/2026', 'no_ref' => 'J-002', 'kode_akun' => '1101', 'nama_akun' => 'Kas', 'keterangan' => 'Penjualan produk pertama', 'debet' => 10000000, 'kredit' => 0],
                    ['tanggal' => '05/01/2026', 'no_ref' => 'J-002', 'kode_akun' => '4101', 'nama_akun' => 'Penjualan Produk', 'keterangan' => 'Penjualan produk pertama', 'debet' => 0, 'kredit' => 10000000],
                ]),
                'sort_order' => 1,
            ],
            [
                'name' => 'Buku Besar',
                'slug' => 'buku-besar',
                'description' => 'Rekapitulasi transaksi per kode akun per bulan',
                'type' => 'buku_besar',
                'frequency' => 'bulanan',
                'columns' => json_encode([
                    ['key' => 'kode_akun', 'name' => 'Kode Akun (*)', 'type' => 'text', 'width' => 15, 'rules' => ['required']],
                    ['key' => 'nama_akun', 'name' => 'Nama Akun (*)', 'type' => 'text', 'width' => 25, 'rules' => ['required']],
                    ['key' => 'bulan', 'name' => 'Bulan (*)', 'type' => 'text', 'width' => 12, 'rules' => ['required']],
                    ['key' => 'saldo_awal', 'name' => 'Saldo Awal (Rp)', 'type' => 'number', 'width' => 18],
                    ['key' => 'total_debet', 'name' => 'Total Debet (Rp)', 'type' => 'number', 'width' => 18],
                    ['key' => 'total_kredit', 'name' => 'Total Kredit (Rp)', 'type' => 'number', 'width' => 18],
                    ['key' => 'saldo_akhir', 'name' => 'Saldo Akhir (Rp)', 'type' => 'number', 'width' => 18],
                ]),
                'sample_data' => json_encode([
                    ['kode_akun' => '1101', 'nama_akun' => 'Kas', 'bulan' => 'Januari 2026', 'saldo_awal' => 0, 'total_debet' => 60000000, 'total_kredit' => 8000000, 'saldo_akhir' => 52000000],
                    ['kode_akun' => '1102', 'nama_akun' => 'Bank', 'bulan' => 'Januari 2026', 'saldo_awal' => 0, 'total_debet' => 0, 'total_kredit' => 0, 'saldo_akhir' => 0],
                    ['kode_akun' => '4101', 'nama_akun' => 'Penjualan Produk', 'bulan' => 'Januari 2026', 'saldo_awal' => 0, 'total_debet' => 0, 'total_kredit' => 10000000, 'saldo_akhir' => 10000000],
                ]),
                'sort_order' => 2,
            ],
            [
                'name' => 'Laba Rugi',
                'slug' => 'laba-rugi',
                'description' => 'Laporan laba rugi periode berjalan sesuai SAK EMKM',
                'type' => 'laba_rugi',
                'frequency' => 'bulanan',
                'columns' => json_encode([
                    ['key' => 'kode_akun', 'name' => 'Kode Akun (*)', 'type' => 'text', 'width' => 15, 'rules' => ['required']],
                    ['key' => 'nama_akun', 'name' => 'Nama Akun (*)', 'type' => 'text', 'width' => 30, 'rules' => ['required']],
                    ['key' => 'kategori', 'name' => 'Kategori (*)', 'type' => 'text', 'width' => 15, 'rules' => ['required']],
                    ['key' => 'jumlah', 'name' => 'Jumlah (Rp) (*)', 'type' => 'number', 'width' => 20, 'rules' => ['required', 'numeric']],
                ]),
                'sample_data' => json_encode([
                    ['kode_akun' => '4101', 'nama_akun' => 'Penjualan Produk', 'kategori' => 'Pendapatan', 'jumlah' => 100000000],
                    ['kode_akun' => '4102', 'nama_akun' => 'Pendapatan Jasa', 'kategori' => 'Pendapatan', 'jumlah' => 25000000],
                    ['kode_akun' => '5101', 'nama_akun' => 'Beban Gaji', 'kategori' => 'Beban', 'jumlah' => 15000000],
                    ['kode_akun' => '5102', 'nama_akun' => 'Beban Sewa', 'kategori' => 'Beban', 'jumlah' => 5000000],
                    ['kode_akun' => '5103', 'nama_akun' => 'Beban Listrik', 'kategori' => 'Beban', 'jumlah' => 2000000],
                    ['kode_akun' => '5104', 'nama_akun' => 'Beban ATK', 'kategori' => 'Beban', 'jumlah' => 1000000],
                    ['kode_akun' => '5201', 'nama_akun' => 'Beban Administrasi Bank', 'kategori' => 'Beban', 'jumlah' => 500000],
                ]),
                'sort_order' => 3,
            ],
            [
                'name' => 'Neraca',
                'slug' => 'neraca',
                'description' => 'Laporan posisi keuangan (Balance Sheet) per akhir periode',
                'type' => 'neraca',
                'frequency' => 'bulanan',
                'columns' => json_encode([
                    ['key' => 'kode_akun', 'name' => 'Kode Akun (*)', 'type' => 'text', 'width' => 15, 'rules' => ['required']],
                    ['key' => 'nama_akun', 'name' => 'Nama Akun (*)', 'type' => 'text', 'width' => 30, 'rules' => ['required']],
                    ['key' => 'kategori', 'name' => 'Kategori (*)', 'type' => 'text', 'width' => 20, 'rules' => ['required']],
                    ['key' => 'jumlah', 'name' => 'Jumlah (Rp) (*)', 'type' => 'number', 'width' => 20, 'rules' => ['required', 'numeric']],
                ]),
                'sample_data' => json_encode([
                    ['kode_akun' => '1101', 'nama_akun' => 'Kas', 'kategori' => 'Aset Lancar', 'jumlah' => 45000000],
                    ['kode_akun' => '1102', 'nama_akun' => 'Bank', 'kategori' => 'Aset Lancar', 'jumlah' => 10000000],
                    ['kode_akun' => '1103', 'nama_akun' => 'Piutang', 'kategori' => 'Aset Lancar', 'jumlah' => 5000000],
                    ['kode_akun' => '1104', 'nama_akun' => 'Persediaan', 'kategori' => 'Aset Lancar', 'jumlah' => 8000000],
                    ['kode_akun' => '1203', 'nama_akun' => 'Peralatan', 'kategori' => 'Aset Tetap', 'jumlah' => 25000000],
                    ['kode_akun' => '2101', 'nama_akun' => 'Hutang Usaha', 'kategori' => 'Kewajiban', 'jumlah' => 8000000],
                    ['kode_akun' => '3101', 'nama_akun' => 'Modal Setoran', 'kategori' => 'Ekuitas', 'jumlah' => 50000000],
                    ['kode_akun' => '3103', 'nama_akun' => 'Laba Tahun Berjalan', 'kategori' => 'Ekuitas', 'jumlah' => 35000000],
                ]),
                'sort_order' => 4,
            ],
            [
                'name' => 'Arus Kas',
                'slug' => 'arus-kas',
                'description' => 'Laporan arus kas (Cash Flow Statement)',
                'type' => 'arus_kas',
                'frequency' => 'bulanan',
                'columns' => json_encode([
                    ['key' => 'keterangan', 'name' => 'Keterangan (*)', 'type' => 'text', 'width' => 40, 'rules' => ['required']],
                    ['key' => 'kategori', 'name' => 'Kategori (*)', 'type' => 'text', 'width' => 20, 'rules' => ['required']],
                    ['key' => 'jumlah', 'name' => 'Jumlah (Rp) (*)', 'type' => 'number', 'width' => 20, 'rules' => ['required', 'numeric']],
                ]),
                'sample_data' => json_encode([
                    ['keterangan' => 'Penerimaan dari penjualan produk', 'kategori' => 'Operasi', 'jumlah' => 100000000],
                    ['keterangan' => 'Penerimaan pendapatan jasa', 'kategori' => 'Operasi', 'jumlah' => 25000000],
                    ['keterangan' => 'Pembayaran gaji karyawan', 'kategori' => 'Operasi', 'jumlah' => -15000000],
                    ['keterangan' => 'Pembayaran sewa tempat', 'kategori' => 'Operasi', 'jumlah' => -5000000],
                    ['keterangan' => 'Pembayaran listrik & air', 'kategori' => 'Operasi', 'jumlah' => -2000000],
                    ['keterangan' => 'Pembelian peralatan baru', 'kategori' => 'Investasi', 'jumlah' => -25000000],
                    ['keterangan' => 'Setoran modal dari desa', 'kategori' => 'Pendanaan', 'jumlah' => 50000000],
                    ['keterangan' => 'Pembayaran hutang bank', 'kategori' => 'Pendanaan', 'jumlah' => -10000000],
                ]),
                'sort_order' => 5,
            ],
            [
                'name' => 'Perubahan Modal',
                'slug' => 'perubahan-modal',
                'description' => 'Laporan perubahan modal/ekuitas',
                'type' => 'modal',
                'frequency' => 'tahunan',
                'columns' => json_encode([
                    ['key' => 'keterangan', 'name' => 'Keterangan (*)', 'type' => 'text', 'width' => 40, 'rules' => ['required']],
                    ['key' => 'jumlah', 'name' => 'Jumlah (Rp) (*)', 'type' => 'number', 'width' => 20, 'rules' => ['required', 'numeric']],
                ]),
                'sample_data' => json_encode([
                    ['keterangan' => 'Saldo Awal Modal (3101 Modal Setoran)', 'jumlah' => 50000000],
                    ['keterangan' => 'Penyertaan Modal dari Pemerintah Desa', 'jumlah' => 10000000],
                    ['keterangan' => 'Laba Tahun Berjalan (dari Laba Rugi)', 'jumlah' => 35000000],
                    ['keterangan' => 'Penarikan Modal (jika ada)', 'jumlah' => -5000000],
                ]),
                'sort_order' => 6,
            ],
            [
                'name' => 'Realisasi Anggaran',
                'slug' => 'realisasi-anggaran',
                'description' => 'Laporan realisasi anggaran per program/kegiatan',
                'type' => 'realisasi_anggaran',
                'frequency' => 'bulanan',
                'columns' => json_encode([
                    ['key' => 'kode_akun', 'name' => 'Kode Akun', 'type' => 'text', 'width' => 15],
                    ['key' => 'uraian', 'name' => 'Uraian (*)', 'type' => 'text', 'width' => 35, 'rules' => ['required']],
                    ['key' => 'anggaran', 'name' => 'Anggaran (Rp) (*)', 'type' => 'number', 'width' => 20, 'rules' => ['required', 'numeric']],
                    ['key' => 'realisasi', 'name' => 'Realisasi (Rp) (*)', 'type' => 'number', 'width' => 20, 'rules' => ['required', 'numeric']],
                    ['key' => 'persentase', 'name' => 'Persentase (%)', 'type' => 'number', 'width' => 15],
                    ['key' => 'selisih', 'name' => 'Selisih (Rp)', 'type' => 'number', 'width' => 18],
                ]),
                'sample_data' => json_encode([
                    ['kode_akun' => '5101', 'uraian' => 'Beban gaji karyawan', 'anggaran' => 15000000, 'realisasi' => 15000000, 'persentase' => 100, 'selisih' => 0],
                    ['kode_akun' => '5102', 'uraian' => 'Beban sewa tempat', 'anggaran' => 5000000, 'realisasi' => 5000000, 'persentase' => 100, 'selisih' => 0],
                    ['kode_akun' => '5103', 'uraian' => 'Beban listrik & air', 'anggaran' => 2400000, 'realisasi' => 2000000, 'persentase' => 83.33, 'selisih' => 400000],
                    ['kode_akun' => '5104', 'uraian' => 'Beban ATK & perlengkapan', 'anggaran' => 1200000, 'realisasi' => 800000, 'persentase' => 66.67, 'selisih' => 400000],
                ]),
                'sort_order' => 7,
            ],
            [
                'name' => 'CAT (Catatan Atas Laporan Keuangan)',
                'slug' => 'cat-laporan',
                'description' => 'Catatan atas laporan keuangan sesuai SAK EMKM',
                'type' => 'cat',
                'frequency' => 'tahunan',
                'columns' => json_encode([
                    ['key' => 'no', 'name' => 'No. (*)', 'type' => 'text', 'width' => 8, 'rules' => ['required']],
                    ['key' => 'uraian', 'name' => 'Uraian (*)', 'type' => 'text', 'width' => 50, 'rules' => ['required']],
                    ['key' => 'nilai', 'name' => 'Nilai (Rp)', 'type' => 'number', 'width' => 20],
                    ['key' => 'keterangan', 'name' => 'Keterangan', 'type' => 'text', 'width' => 35],
                ]),
                'sample_data' => json_encode([
                    ['no' => '1', 'uraian' => 'Bentuk Usaha', 'nilai' => '', 'keterangan' => 'Badan Usaha Milik Desa (BUMDes)'],
                    ['no' => '2', 'uraian' => 'Kebijakan Akuntansi', 'nilai' => '', 'keterangan' => 'Metode akrual, SAK EMKM'],
                    ['no' => '3', 'uraian' => 'Aset Tetap', 'nilai' => '', 'keterangan' => 'Depresiasi: Garis lurus, umur ekonomis 5 tahun'],
                    ['no' => '4', 'uraian' => 'Modal Disetor', 'nilai' => 50000000, 'keterangan' => 'Dari Pemerintah Desa Keude Bakongan'],
                    ['no' => '5', 'uraian' => 'Sistem Pengendalian Internal', 'nilai' => '', 'keterangan' => 'Pisah tugas: Direktur, Sekretaris, Bendahara'],
                ]),
                'sort_order' => 8,
            ],
        ];

        foreach ($templates as $data) {
            FinancialTemplate::updateOrCreate(
                ['slug' => $data['slug']],
                $data
            );
        }
    }
}
