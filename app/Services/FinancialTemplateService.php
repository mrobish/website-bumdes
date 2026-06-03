<?php

namespace App\Services;

use App\Models\FinancialTemplate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
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

        // Title style
        $titleStyle = [
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => 'center'],
        ];

        // Header style
        $headerStyle = [
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '2C3E50']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            'borders' => [
                'allBorders' => ['borderStyle' => 'thin'],
            ],
        ];

        // Data style
        $dataStyle = [
            'borders' => [
                'allBorders' => ['borderStyle' => 'thin'],
            ],
            'alignment' => ['vertical' => 'center'],
        ];

        // Currency format
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
        $sheet->getStyle('A3')->applyFromArray(['horizontal' => 'center']);
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

        // Add sample data if exists
        $sampleData = is_string($template->sample_data) ? json_decode($template->sample_data, true) : ($template->sample_data ?? []);
        if (!empty($sampleData)) {
            $sampleRow = $headerRow + 1;
            foreach ($sampleData as $row) {
                foreach ($columns as $col => $header) {
                    $cell = $this->getColumnLetter($col + 1) . $sampleRow;
                    $value = $row[$header['key'] ?? $col] ?? '';
                    $sheet->setCellValue($cell, $value);
                    $sheet->getStyle($cell)->applyFromArray($dataStyle);

                    // Apply currency format for amount columns
                    if (isset($header['type']) && $header['type'] === 'number') {
                        $sheet->getStyle($cell)->getNumberFormat()->setFormatCode($currencyFormat);
                    }
                }
                // Style sample row as gray
                $sheet->getStyle('A' . $sampleRow . ':' . $this->getLastColumn($template) . $sampleRow)
                    ->applyFromArray([
                        'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'ECF0F1']],
                    ]);
                $sampleRow++;
            }
        }

        // Add empty rows for data entry
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

        // Add instructions sheet
        $instructions = $spreadsheet->createSheet('Petunjuk');
        $instructions->setCellValue('A1', 'PETUNJUK PENGISIAN');
        $instructions->getStyle('A1')->applyFromArray(['font' => ['bold' => true, 'size' => 14]]);
        $instructions->setCellValue('A3', '1. Download template ini');
        $instructions->setCellValue('A4', '2. Buka dengan Microsoft Excel atau LibreOffice Calc');
        $instructions->setCellValue('A5', '3. Isi data pada sheet "' . $template->name . '"');
        $instructions->setCellValue('A6', '4. Baris berwarna abu-abu adalah CONTOH, bisa dihapus');
        $instructions->setCellValue('A7', '5. JANGAN mengubah format header (baris 5)');
        $instructions->setCellValue('A8', '6. Simpan file');
        $instructions->setCellValue('A9', '7. Upload kembali ke aplikasi');
        $instructions->setCellValue('A11', 'CATATAN PENTING:');
        $instructions->setCellValue('A12', '- Format tanggal: DD/MM/YYYY');
        $instructions->setCellValue('A13', '- Format angka: tanpa tanda baca (contoh: 1000000)');
        $instructions->setCellValue('A14', '- Kolom bertanda (*) wajib diisi');
        $instructions->setCellValue('A15', '- Pastikan total Debet = total Kredit untuk Jurnal');
        $instructions->getColumnDimension('A')->setWidth(60);

        // Save to storage
        $fileName = 'templates/' . $template->slug . '-template.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save(storage_path('app/' . $fileName));

        return $fileName;
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
        $startRow = 6; // Skip header rows

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

            // Skip empty rows
            if (!$isEmpty) {
                // Validate row
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

    /**
     * Validate a single row
     */
    protected function validateRow(array $rowData, array $columns, int $row): array
    {
        $errors = [];

        foreach ($columns as $col => $header) {
            $key = $header['key'] ?? 'col_' . $col;
            $value = $rowData[$key] ?? null;
            $rules = $header['rules'] ?? [];

            // Required check
            if (in_array('required', $rules) && empty($value)) {
                $errors[] = "Baris $row: Kolom '{$header['name']}' wajib diisi";
            }

            // Numeric check
            if (in_array('numeric', $rules) && !empty($value) && !is_numeric($value)) {
                $errors[] = "Baris $row: Kolom '{$header['name']}' harus berupa angka";
            }
        }

        return $errors;
    }

    /**
     * Get column letter from number (1=A, 2=B, etc)
     */
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

    /**
     * Get last column letter for template
     */
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
                    ['tanggal' => '01/01/2026', 'no_ref' => 'J-001', 'kode_akun' => '1101', 'nama_akun' => 'Kas', 'keterangan' => 'Setoran modal dari desa', 'debet' => 50000000, 'kredit' => 0],
                    ['tanggal' => '01/01/2026', 'no_ref' => 'J-001', 'kode_akun' => '3101', 'nama_akun' => 'Modal Disetor', 'keterangan' => 'Setoran modal dari desa', 'debet' => 0, 'kredit' => 50000000],
                ]),
                'sort_order' => 1,
            ],
            [
                'name' => 'Buku Besar',
                'slug' => 'buku-besar',
                'description' => 'Rekapitulasi transaksi per kode akun',
                'type' => 'buku_besar',
                'frequency' => 'bulanan',
                'columns' => json_encode([
                    ['key' => 'kode_akun', 'name' => 'Kode Akun (*)', 'type' => 'text', 'width' => 15, 'rules' => ['required']],
                    ['key' => 'nama_akun', 'name' => 'Nama Akun (*)', 'type' => 'text', 'width' => 25, 'rules' => ['required']],
                    ['key' => 'bulan', 'name' => 'Bulan (*)', 'type' => 'text', 'width' => 12, 'rules' => ['required']],
                    ['key' => 'saldo_awal', 'name' => 'Saldo Awal', 'type' => 'number', 'width' => 18],
                    ['key' => 'total_debet', 'name' => 'Total Debet', 'type' => 'number', 'width' => 18],
                    ['key' => 'total_kredit', 'name' => 'Total Kredit', 'type' => 'number', 'width' => 18],
                    ['key' => 'saldo_akhir', 'name' => 'Saldo Akhir', 'type' => 'number', 'width' => 18],
                ]),
                'sample_data' => json_encode([
                    ['kode_akun' => '1101', 'nama_akun' => 'Kas', 'bulan' => 'Januari', 'saldo_awal' => 0, 'total_debet' => 50000000, 'total_kredit' => 5000000, 'saldo_akhir' => 45000000],
                ]),
                'sort_order' => 2,
            ],
            [
                'name' => 'Laba Rugi',
                'slug' => 'laba-rugi',
                'description' => 'Laporan laba rugi periode berjalan (SAK EMKM)',
                'type' => 'laba_rugi',
                'frequency' => 'bulanan',
                'columns' => json_encode([
                    ['key' => 'kode_akun', 'name' => 'Kode Akun (*)', 'type' => 'text', 'width' => 15, 'rules' => ['required']],
                    ['key' => 'nama_akun', 'name' => 'Nama Akun (*)', 'type' => 'text', 'width' => 30, 'rules' => ['required']],
                    ['key' => 'kategori', 'name' => 'Kategori (*)', 'type' => 'text', 'width' => 15, 'rules' => ['required']],
                    ['key' => 'jumlah', 'name' => 'Jumlah (Rp) (*)', 'type' => 'number', 'width' => 20, 'rules' => ['required', 'numeric']],
                ]),
                'sample_data' => json_encode([
                    ['kode_akun' => '4101', 'nama_akun' => 'Pendapatan Penjualan', 'kategori' => 'Pendapatan', 'jumlah' => 100000000],
                    ['kode_akun' => '5101', 'nama_akun' => 'Harga Pokok Penjualan', 'kategori' => 'Beban', 'jumlah' => 60000000],
                    ['kode_akun' => '5201', 'nama_akun' => 'Beban Gaji', 'kategori' => 'Beban', 'jumlah' => 10000000],
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
                    ['kode_akun' => '1201', 'nama_akun' => 'Piutang Usaha', 'kategori' => 'Aset Lancar', 'jumlah' => 15000000],
                    ['kode_akun' => '2101', 'nama_akun' => 'Utang Usaha', 'kategori' => 'Kewajiban', 'jumlah' => 10000000],
                    ['kode_akun' => '3101', 'nama_akun' => 'Modal Disetor', 'kategori' => 'Ekuitas', 'jumlah' => 50000000],
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
                    ['keterangan' => 'Penerimaan dari penjualan', 'kategori' => 'Operasi', 'jumlah' => 100000000],
                    ['keterangan' => 'Pembayaran ke supplier', 'kategori' => 'Operasi', 'jumlah' => -60000000],
                    ['keterangan' => 'Pembelian aset tetap', 'kategori' => 'Investasi', 'jumlah' => -25000000],
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
                    ['key' => 'keterangan', 'name' => 'Keterangan (*)', 'type' => 'text', 'width' => 35, 'rules' => ['required']],
                    ['key' => 'jumlah', 'name' => 'Jumlah (Rp) (*)', 'type' => 'number', 'width' => 20, 'rules' => ['required', 'numeric']],
                ]),
                'sample_data' => json_encode([
                    ['keterangan' => 'Saldo Awal Modal', 'jumlah' => 50000000],
                    ['keterangan' => 'Penyertaan Modal', 'jumlah' => 10000000],
                    ['keterangan' => 'Laba Tahun Berjalan', 'jumlah' => 25000000],
                    ['keterangan' => 'Penarikan Modal', 'jumlah' => -5000000],
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
                    ['kode_akun' => '5101', 'uraian' => 'Pembelian bahan baku', 'anggaran' => 30000000, 'realisasi' => 28000000, 'persentase' => 93.33, 'selisih' => 2000000],
                    ['kode_akun' => '5201', 'uraian' => 'Beban gaji karyawan', 'anggaran' => 15000000, 'realisasi' => 15000000, 'persentase' => 100, 'selisih' => 0],
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
                    ['no' => '1', 'uraian' => 'Kebijakan Akuntansi', 'nilai' => '', 'keterangan' => 'Metode: Akrual'],
                    ['no' => '2', 'uraian' => 'Aset Tetap', 'nilai' => '', 'keterangan' => 'Metode depresiasi: Garis lurus'],
                    ['no' => '3', 'uraian' => 'Modal Disetor', 'nilai' => 50000000, 'keterangan' => 'Dari Pemerintah Desa'],
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
