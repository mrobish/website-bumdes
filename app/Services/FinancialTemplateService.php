<?php

namespace App\Services;

use App\Models\FinancialTemplate;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class FinancialTemplateService
{
    public function generateTemplate(FinancialTemplate $template): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $coaList = DB::table('chart_of_accounts')->orderBy('code')->get();
        $akunItems = $coaList->filter(fn($a) => !str_ends_with($a->code, '00'));
        $headerStyle = [
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '2C3E50']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            'borders' => ['allBorders' => ['borderStyle' => 'thin']],
        ];
        $dataStyle = ['borders' => ['allBorders' => ['borderStyle' => 'thin']], 'alignment' => ['vertical' => 'center']];
        $currencyFmt = '#,##0';
        $columns = is_string($template->columns) ? json_decode($template->columns, true) : ($template->columns ?? []);
        $lastCol = $this->getLastColumn($template);
        $headerRow = 5;

        $sheet->setCellValue('A1', 'BUMDes Keude Bakongan');
        $sheet->getStyle('A1')->applyFromArray(['font' => ['bold' => true, 'size' => 14], 'alignment' => ['horizontal' => 'center']]);
        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->setCellValue('A2', $template->name);
        $sheet->getStyle('A2')->applyFromArray(['font' => ['bold' => true, 'size' => 12], 'alignment' => ['horizontal' => 'center']]);
        $sheet->mergeCells("A2:{$lastCol}2");
        $sheet->setCellValue('A3', 'GUNAKAN DROPDOWN untuk kode/nama akun. Jangan ketik manual!');
        $sheet->getStyle('A3')->applyFromArray(['font' => ['bold' => true, 'color' => ['rgb' => 'E74C3C'], 'size' => 10], 'alignment' => ['horizontal' => 'center']]);
        $sheet->mergeCells("A3:{$lastCol}3");

        foreach ($columns as $col => $header) {
            $cell = $this->getColumnLetter($col + 1) . $headerRow;
            $sheet->setCellValue($cell, $header['name'] ?? $header);
            $sheet->getStyle($cell)->applyFromArray($headerStyle);
            $sheet->getColumnDimension($this->getColumnLetter($col + 1))->setWidth($header['width'] ?? 20);
        }

        // COA Reference Sheet (hidden)
        $coaSheet = $spreadsheet->createSheet();
        $coaSheet->setTitle('RefCOA');
        $coaSheet->setCellValue('A1', 'Kode');
        $coaSheet->setCellValue('B1', 'Nama');
        $coaSheet->getColumnDimension('A')->setWidth(10);
        $coaSheet->getColumnDimension('B')->setWidth(30);
        $coaRow = 2;
        $kodeOptions = [];
        $namaOptions = [];
        foreach ($akunItems as $a) {
            $coaSheet->setCellValue("A{$coaRow}", $a->code);
            $coaSheet->setCellValue("B{$coaRow}", $a->name);
            $kodeOptions[] = $a->code;
            $namaOptions[] = $a->name;
            $coaRow++;
        }
        $coaSheet->setSheetState(\PhpOffice\PhpSpreadsheet\Spreadsheet::SHEETSTATE_VERYHIDDEN);
        $kodeList = implode(',', $kodeOptions);
        $namaList = implode(',', $namaOptions);

        // Sample data
        $sampleData = is_string($template->sample_data) ? json_decode($template->sample_data, true) : ($template->sample_data ?? []);
        $sampleRow = $headerRow + 1;
        if (!empty($sampleData)) {
            foreach ($sampleData as $row) {
                foreach ($columns as $col => $header) {
                    $cell = $this->getColumnLetter($col + 1) . $sampleRow;
                    $sheet->setCellValue($cell, $row[$header['key'] ?? $col] ?? '');
                    $sheet->getStyle($cell)->applyFromArray($dataStyle);
                    if (isset($header['type']) && $header['type'] === 'number') {
                        $sheet->getStyle($cell)->getNumberFormat()->setFormatCode($currencyFmt);
                    }
                }
                $sheet->getStyle("A{$sampleRow}:{$lastCol}{$sampleRow}")->applyFromArray(['fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'ECF0F1']]]);
                $sampleRow++;
            }
        }

        // Data rows with dropdowns + formulas
        $dataStart = $sampleRow;
        $dataEnd = $dataStart + 49;
        $kodeColIdx = $this->findColIndex($columns, 'kode_akun');
        $namaColIdx = $this->findColIndex($columns, 'nama_akun');
        $kategoriColIdx = $this->findColIndex($columns, 'kategori');
        $saColIdx = $this->findColIndex($columns, 'saldo_awal');
        $sdColIdx = $this->findColIndex($columns, 'total_debet');
        $skColIdx = $this->findColIndex($columns, 'total_kredit');
        $saAkhirIdx = $this->findColIndex($columns, 'saldo_akhir');
        $anggaranIdx = $this->findColIndex($columns, 'anggaran');
        $realisasiIdx = $this->findColIndex($columns, 'realisasi');
        $persenIdx = $this->findColIndex($columns, 'persentase');
        $selisihIdx = $this->findColIndex($columns, 'selisih');

        for ($row = $dataStart; $row <= $dataEnd; $row++) {
            foreach ($columns as $col => $header) {
                $cell = $this->getColumnLetter($col + 1) . $row;
                $sheet->getStyle($cell)->applyFromArray($dataStyle);
                if (isset($header['type']) && $header['type'] === 'number') {
                    $sheet->getStyle($cell)->getNumberFormat()->setFormatCode($currencyFmt);
                }
            }
            if ($kodeColIdx !== false) {
                $this->addDropdown($sheet, $this->getColumnLetter($kodeColIdx + 1) . $row, $kodeList, 'Pilih Kode Akun');
            }
            if ($namaColIdx !== false) {
                $this->addDropdown($sheet, $this->getColumnLetter($namaColIdx + 1) . $row, $namaList, 'Pilih Nama Akun');
            }
            if ($kategoriColIdx !== false) {
                $katList = match($template->type) {
                    'neraca' => 'Aset Lancar,Aset Tetap,Kewajiban,Ekuitas',
                    'laba_rugi' => 'Pendapatan,Beban',
                    'arus_kas' => 'Operasi,Investasi,Pendanaan',
                    default => null,
                };
                if ($katList) {
                    $this->addDropdown($sheet, $this->getColumnLetter($kategoriColIdx + 1) . $row, $katList, 'Pilih Kategori');
                }
            }
            // Formulas
            if ($template->type === 'buku_besar' && $saColIdx !== false && $sdColIdx !== false && $skColIdx !== false && $saAkhirIdx !== false) {
                $sa = $this->getColumnLetter($saColIdx + 1);
                $sd = $this->getColumnLetter($sdColIdx + 1);
                $sk = $this->getColumnLetter($skColIdx + 1);
                $sak = $this->getColumnLetter($saAkhirIdx + 1);
                $sheet->setCellValue("{$sak}{$row}", "={$sa}{$row}+{$sd}{$row}-{$sk}{$row}");
            }
            if ($template->type === 'realisasi_anggaran' && $anggaranIdx !== false && $realisasiIdx !== false && $persenIdx !== false) {
                $ang = $this->getColumnLetter($anggaranIdx + 1);
                $rea = $this->getColumnLetter($realisasiIdx + 1);
                $per = $this->getColumnLetter($persenIdx + 1);
                $sheet->setCellValue("{$per}{$row}", "=IF({$ang}{$row}>0,({$rea}{$row}/{$ang}{$row})*100,0)");
                $sheet->getStyle("{$per}{$row}")->getNumberFormat()->setFormatCode('0.00');
            }
            if ($template->type === 'realisasi_anggaran' && $anggaranIdx !== false && $realisasiIdx !== false && $selisihIdx !== false) {
                $ang = $this->getColumnLetter($anggaranIdx + 1);
                $rea = $this->getColumnLetter($realisasiIdx + 1);
                $sel = $this->getColumnLetter($selisihIdx + 1);
                $sheet->setCellValue("{$sel}{$row}", "={$ang}{$row}-{$rea}{$row}");
            }
        }

        // Total row
        $sumRow = $dataEnd + 2;
        $this->addTotalRow($sheet, $columns, $template, $dataStart, $dataEnd, $sumRow, $currencyFmt);

        // Petunjuk sheet
        $this->createPetunjukSheet($spreadsheet, $template, $akunItems);

        $fileName = 'templates/' . $template->slug . '-template.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save(storage_path('app/' . $fileName));
        return $fileName;
    }

    protected function addDropdown($sheet, string $cell, string $list, string $title): void
    {
        $v = new DataValidation();
        $v->setType(DataValidation::TYPE_LIST);
        $v->setAllowBlank(true);
        $v->setShowDropDown(true);
        $v->setFormula1('"' . $list . '"');
        $v->setShowErrorMessage(true);
        $v->setErrorTitle($title);
        $v->setError('Pilih dari dropdown. Jangan ketik manual.');
        $v->setShowInputMessage(true);
        $v->setPromptTitle($title);
        $v->setPrompt('Pilih dari dropdown.');
        $sheet->addDataValidation($v);
        $v->addCell($cell);
    }

    protected function addTotalRow($sheet, array $columns, FinancialTemplate $template, int $dataStart, int $dataEnd, int $sumRow, string $fmt): void
    {
        $debetIdx = $this->findColIndex($columns, 'debet');
        $kreditIdx = $this->findColIndex($columns, 'kredit');
        $jumlahIdx = $this->findColIndex($columns, 'jumlah');
        $anggaranIdx = $this->findColIndex($columns, 'anggaran');
        $realisasiIdx = $this->findColIndex($columns, 'realisasi');

        if ($debetIdx !== false && $kreditIdx !== false) {
            $dCol = $this->getColumnLetter($debetIdx + 1);
            $kCol = $this->getColumnLetter($kreditIdx + 1);
            $sheet->setCellValue("A{$sumRow}", 'TOTAL');
            $sheet->getStyle("A{$sumRow}")->applyFromArray(['font' => ['bold' => true, 'size' => 12]]);
            $sheet->setCellValue("{$dCol}{$sumRow}", "=SUM({$dCol}{$dataStart}:{$dCol}{$dataEnd})");
            $sheet->getStyle("{$dCol}{$sumRow}")->applyFromArray(['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'D5F5E3']]]);
            $sheet->getStyle("{$dCol}{$sumRow}")->getNumberFormat()->setFormatCode($fmt);
            $sheet->setCellValue("{$kCol}{$sumRow}", "=SUM({$kCol}{$dataStart}:{$kCol}{$dataEnd})");
            $sheet->getStyle("{$kCol}{$sumRow}")->applyFromArray(['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'D5F5E3']]]);
            $sheet->getStyle("{$kCol}{$sumRow}")->getNumberFormat()->setFormatCode($fmt);
            $selCol = $this->getColumnLetter(count($columns) + 1);
            $sheet->setCellValue("{$selCol}{$sumRow}", "={}{$dCol}{$sumRow}-{}{$kCol}{$sumRow}");
            $sheet->getStyle("{$selCol}{$sumRow}")->applyFromArray(['font' => ['bold' => true, 'color' => ['rgb' => 'E74C3C']]]);
            $sheet->getStyle("{$selCol}{$sumRow}")->getNumberFormat()->setFormatCode($fmt);
        }
        if ($template->type === 'laba_rugi' && $jumlahIdx !== false) {
            $jCol = $this->getColumnLetter($jumlahIdx + 1);
            $sheet->setCellValue("A{$sumRow}", 'TOTAL PENDAPATAN');
            $sheet->getStyle("A{$sumRow}")->applyFromArray(['font' => ['bold' => true]]);
            $sheet->setCellValue("A" . ($sumRow + 1), 'TOTAL BEBAN');
            $sheet->getStyle("A" . ($sumRow + 1))->applyFromArray(['font' => ['bold' => true]]);
            $sheet->setCellValue("A" . ($sumRow + 2), 'LABA BERSIH');
            $sheet->getStyle("A" . ($sumRow + 2))->applyFromArray(['font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '27AE60']]]);
            $sheet->setCellValue("{$jCol}" . ($sumRow + 2), "={}{$jCol}{$sumRow}-{}{$jCol}" . ($sumRow + 1));
            $sheet->getStyle("{$jCol}" . ($sumRow + 2))->getNumberFormat()->setFormatCode($fmt);
        }
        if ($template->type === 'neraca' && $jumlahIdx !== false) {
            $jCol = $this->getColumnLetter($jumlahIdx + 1);
            $sheet->setCellValue("A{$sumRow}", 'TOTAL ASET');
            $sheet->getStyle("A{$sumRow}")->applyFromArray(['font' => ['bold' => true]]);
            $sheet->setCellValue("A" . ($sumRow + 1), 'TOTAL KEWAJIBAN + EKUITAS');
            $sheet->getStyle("A" . ($sumRow + 1))->applyFromArray(['font' => ['bold' => true]]);
            $sheet->setCellValue("A" . ($sumRow + 2), 'SELISIH (harus = 0)');
            $sheet->getStyle("A" . ($sumRow + 2))->applyFromArray(['font' => ['bold' => true, 'color' => ['rgb' => 'E74C3C']]]);
            $sheet->setCellValue("{$jCol}" . ($sumRow + 2), "={}{$jCol}{$sumRow}-{}{$jCol}" . ($sumRow + 1));
            $sheet->getStyle("{$jCol}" . ($sumRow + 2))->getNumberFormat()->setFormatCode($fmt);
        }
        if ($template->type === 'realisasi_anggaran' && $anggaranIdx !== false && $realisasiIdx !== false) {
            $aCol = $this->getColumnLetter($anggaranIdx + 1);
            $rCol = $this->getColumnLetter($realisasiIdx + 1);
            $sheet->setCellValue("A{$sumRow}", 'TOTAL');
            $sheet->getStyle("A{$sumRow}")->applyFromArray(['font' => ['bold' => true, 'size' => 12]]);
            $sheet->setCellValue("{$aCol}{$sumRow}", "=SUM({$aCol}{$dataStart}:{$aCol}{$dataEnd})");
            $sheet->setCellValue("{$rCol}{$sumRow}", "=SUM({$rCol}{$dataStart}:{$rCol}{$dataEnd})");
            $sheet->getStyle("{$aCol}{$sumRow}")->applyFromArray(['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'D5F5E3']]]);
            $sheet->getStyle("{$rCol}{$sumRow}")->applyFromArray(['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'D5F5E3']]]);
        }
    }

    protected function createPetunjukSheet(Spreadsheet $spreadsheet, FinancialTemplate $template, $akunItems): void
    {
        $inst = $spreadsheet->createSheet();
        $inst->setTitle('Petunjuk');
        $inst->getColumnDimension('A')->setWidth(5);
        $inst->getColumnDimension('B')->setWidth(65);
        $inst->getColumnDimension('C')->setWidth(30);
        $row = 1;
        $inst->setCellValue("B{$row}", 'PETUNJUK - ' . strtoupper($template->name));
        $inst->getStyle("B{$row}")->applyFromArray(['font' => ['bold' => true, 'size' => 16]]);

        $row = 3;
        $inst->setCellValue("B{$row}", 'CARA PENGISIAN');
        $inst->getStyle("B{$row}")->applyFromArray(['font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => '2C3E50']]]);
        $steps = [
            '1. Download template ini',
            '2. Buka dengan Microsoft Excel atau LibreOffice Calc',
            '3. Isi data pada sheet "' . $template->name . '"',
            '4. GUNAKAN DROPDOWN untuk kode & nama akun (jangan ketik manual!)',
            '5. Baris ABU-ABU = CONTOH, boleh dihapus',
            '6. JANGAN ubah header (baris 1-5)',
            '7. Rumus sudah terisi otomatis (Saldo Akhir, Total, dll)',
            '8. Simpan dalam format .xlsx',
            '9. Upload kembali ke aplikasi',
        ];
        foreach ($steps as $s) { $row++; $inst->setCellValue("B{$row}", $s); }

        $row += 2;
        $inst->setCellValue("B{$row}", 'FORMAT DATA');
        $inst->getStyle("B{$row}")->applyFromArray(['font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => '2C3E50']]]);
        $fmts = [
            'Tanggal  : DD/MM/YYYY',
            'Angka    : Tanpa titik/koma (5000000, bukan 5.000.000)',
            'Kode Akun: PILIH dari dropdown',
            'Kolom (*): WAJIB diisi',
        ];
        foreach ($fmts as $f) { $row++; $inst->setCellValue("B{$row}", $f); $inst->getStyle("B{$row}")->applyFromArray(['font' => ['name' => 'Consolas', 'size' => 10]]); }

        $row += 2;
        $inst->setCellValue("B{$row}", 'RUMUS OTOMATIS');
        $inst->getStyle("B{$row}")->applyFromArray(['font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => '2C3E50']]]);
        $formulas = match($template->type) {
            'buku_besar' => ['Saldo Akhir = Saldo Awal + Debet - Kredit (otomatis)'],
            'jurnal' => ['Total Debet & Kredit dihitung otomatis, Selisih harus = 0'],
            'laba_rugi' => ['Laba Bersih = Total Pendapatan - Total Beban (otomatis)'],
            'neraca' => ['Selisih = Total Aset - (Kewajiban + Ekuitas), harus = 0'],
            'realisasi_anggaran' => ['Persentase = (Realisasi/Anggaran)x100, Selisih = Anggaran-Realisasi'],
            default => ['Rumus sudah terisi otomatis'],
        };
        foreach ($formulas as $f) { $row++; $inst->setCellValue("B{$row}", $f); $inst->getStyle("B{$row}")->applyFromArray(['font' => ['name' => 'Consolas', 'size' => 10, 'color' => ['rgb' => '27AE60']]]); }

        $row += 2;
        $inst->setCellValue("B{$row}", 'DAFTAR KODE AKUN');
        $inst->getStyle("B{$row}")->applyFromArray(['font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => '2C3E50']]]);
        $row++;
        $inst->setCellValue("B{$row}", 'Jika kode belum ada, buat dulu di Pengaturan Keuangan > COA');
        $inst->getStyle("B{$row}")->applyFromArray(['font' => ['italic' => true, 'color' => ['rgb' => 'E74C3C']]]);

        $row += 2;
        $inst->setCellValue("B{$row}", 'Kode');
        $inst->setCellValue("C{$row}", 'Nama Akun');
        $inst->getStyle("B{$row}")->applyFromArray(['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '3498DB']]]);
        $inst->getStyle("C{$row}")->applyFromArray(['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '3498DB']]]);

        $currentType = '';
        foreach ($akunItems as $a) {
            $typeName = strtoupper($a->type);
            if ($a->type !== $currentType) {
                $row++;
                $inst->setCellValue("B{$row}", "-- {$typeName} --");
                $inst->getStyle("B{$row}")->applyFromArray(['font' => ['bold' => true, 'color' => ['rgb' => '8E44AD']]]);
                $currentType = $a->type;
            }
            $row++;
            $inst->setCellValue("B{$row}", $a->code);
            $inst->setCellValue("C{$row}", $a->name);
            $inst->getStyle("B{$row}")->applyFromArray(['font' => ['name' => 'Consolas', 'size' => 10]]);
        }

        $row += 2;
        $inst->setCellValue("B{$row}", 'ATURAN');
        $inst->getStyle("B{$row}")->applyFromArray(['font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => 'E74C3C']]]);
        $rules = ['JANGAN ketik kode/nama akun manual, gunakan DROPDOWN', 'Jika kode belum ada, buat dulu di COA', 'Kolom (*) wajib diisi', 'Simpan format .xlsx'];
        foreach ($rules as $r) { $row++; $inst->setCellValue("B{$row}", "- " . $r); }

        $row += 2;
        $inst->setCellValue("B{$row}", 'KONTAK: admin@bumdeskeude.id | bendahara@bumdeskeude.id');
    }

    protected function parseUploadedFile(string $filePath, FinancialTemplate $template): array
    {
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(storage_path('app/' . $filePath));
        $sheet = $spreadsheet->getActiveSheet();
        $columns = is_string($template->columns) ? json_decode($template->columns, true) : ($template->columns ?? []);
        $data = [];
        $errors = [];
        $highestRow = $sheet->getHighestRow();
        for ($row = 6; $row <= $highestRow; $row++) {
            $rowData = [];
            $isEmpty = true;
            foreach ($columns as $col => $header) {
                $cell = $this->getColumnLetter($col + 1) . $row;
                $value = $sheet->getCell($cell)->getValue();
                $key = $header['key'] ?? 'col_' . $col;
                $rowData[$key] = $value;
                if (!empty($value) && $value !== null) $isEmpty = false;
            }
            if (!$isEmpty) {
                foreach ($columns as $col => $header) {
                    $key = $header['key'] ?? 'col_' . $col;
                    if (in_array('required', $header['rules'] ?? []) && empty($rowData[$key] ?? null)) {
                        $errors[] = "Baris $row: Kolom '{$header['name']}' wajib diisi";
                    }
                }
                $data[] = $rowData;
            }
        }
        return ['data' => $data, 'errors' => $errors, 'total_rows' => count($data)];
    }

    protected function findColIndex(array $columns, string $key): int|false
    {
        foreach ($columns as $idx => $col) {
            if (($col['key'] ?? '') === $key) return $idx;
        }
        return false;
    }

    protected function getColumnLetter(int $column): string
    {
        $letter = '';
        while ($column > 0) { $column--; $letter = chr(65 + ($column % 26)) . $letter; $column = intdiv($column, 26); }
        return $letter;
    }

    protected function getLastColumn(FinancialTemplate $template): string
    {
        $columns = is_string($template->columns) ? json_decode($template->columns, true) : ($template->columns ?? []);
        return $this->getColumnLetter(count($columns));
    }

    public function seedDefaultTemplates(): void
    {
        $templates = [
            ['name' => 'Jurnal Umum', 'slug' => 'jurnal-umum', 'description' => 'Jurnal umum untuk mencatat transaksi', 'type' => 'jurnal', 'frequency' => 'harian',
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
                ['tanggal' => '01/01/2026', 'no_ref' => 'J-001', 'kode_akun' => '1101', 'nama_akun' => 'Kas', 'keterangan' => 'Setoran modal dari Desa', 'debet' => 50000000, 'kredit' => 0],
                ['tanggal' => '01/01/2026', 'no_ref' => 'J-001', 'kode_akun' => '3101', 'nama_akun' => 'Modal Setoran', 'keterangan' => 'Setoran modal dari Desa', 'debet' => 0, 'kredit' => 50000000],
                ['tanggal' => '05/01/2026', 'no_ref' => 'J-002', 'kode_akun' => '1101', 'nama_akun' => 'Kas', 'keterangan' => 'Penjualan produk', 'debet' => 10000000, 'kredit' => 0],
                ['tanggal' => '05/01/2026', 'no_ref' => 'J-002', 'kode_akun' => '4101', 'nama_akun' => 'Penjualan Produk', 'keterangan' => 'Penjualan produk', 'debet' => 0, 'kredit' => 10000000],
             ]), 'sort_order' => 1],
            ['name' => 'Buku Besar', 'slug' => 'buku-besar', 'description' => 'Rekapitulasi per akun per bulan', 'type' => 'buku_besar', 'frequency' => 'bulanan',
             'columns' => json_encode([
                ['key' => 'kode_akun', 'name' => 'Kode Akun (*)', 'type' => 'text', 'width' => 15, 'rules' => ['required']],
                ['key' => 'nama_akun', 'name' => 'Nama Akun (*)', 'type' => 'text', 'width' => 25, 'rules' => ['required']],
                ['key' => 'bulan', 'name' => 'Bulan (*)', 'type' => 'text', 'width' => 15, 'rules' => ['required']],
                ['key' => 'saldo_awal', 'name' => 'Saldo Awal (Rp)', 'type' => 'number', 'width' => 18],
                ['key' => 'total_debet', 'name' => 'Total Debet (Rp)', 'type' => 'number', 'width' => 18],
                ['key' => 'total_kredit', 'name' => 'Total Kredit (Rp)', 'type' => 'number', 'width' => 18],
                ['key' => 'saldo_akhir', 'name' => 'Saldo Akhir (Rp)', 'type' => 'number', 'width' => 18],
             ]),
             'sample_data' => json_encode([
                ['kode_akun' => '1101', 'nama_akun' => 'Kas', 'bulan' => 'Januari 2026', 'saldo_awal' => 0, 'total_debet' => 60000000, 'total_kredit' => 8000000, 'saldo_akhir' => 52000000],
             ]), 'sort_order' => 2],
            ['name' => 'Laba Rugi', 'slug' => 'laba-rugi', 'description' => 'Laporan laba rugi SAK EMKM', 'type' => 'laba_rugi', 'frequency' => 'bulanan',
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
                ['kode_akun' => '5103', 'nama_akun' => 'Beban Listrik', 'kategori' => 'Beban', 'jumlah' => 2000000],
             ]), 'sort_order' => 3],
            ['name' => 'Neraca', 'slug' => 'neraca', 'description' => 'Laporan posisi keuangan', 'type' => 'neraca', 'frequency' => 'bulanan',
             'columns' => json_encode([
                ['key' => 'kode_akun', 'name' => 'Kode Akun (*)', 'type' => 'text', 'width' => 15, 'rules' => ['required']],
                ['key' => 'nama_akun', 'name' => 'Nama Akun (*)', 'type' => 'text', 'width' => 30, 'rules' => ['required']],
                ['key' => 'kategori', 'name' => 'Kategori (*)', 'type' => 'text', 'width' => 20, 'rules' => ['required']],
                ['key' => 'jumlah', 'name' => 'Jumlah (Rp) (*)', 'type' => 'number', 'width' => 20, 'rules' => ['required', 'numeric']],
             ]),
             'sample_data' => json_encode([
                ['kode_akun' => '1101', 'nama_akun' => 'Kas', 'kategori' => 'Aset Lancar', 'jumlah' => 45000000],
                ['kode_akun' => '1103', 'nama_akun' => 'Piutang', 'kategori' => 'Aset Lancar', 'jumlah' => 5000000],
                ['kode_akun' => '1203', 'nama_akun' => 'Peralatan', 'kategori' => 'Aset Tetap', 'jumlah' => 25000000],
                ['kode_akun' => '2101', 'nama_akun' => 'Hutang Usaha', 'kategori' => 'Kewajiban', 'jumlah' => 8000000],
                ['kode_akun' => '3101', 'nama_akun' => 'Modal Setoran', 'kategori' => 'Ekuitas', 'jumlah' => 50000000],
                ['kode_akun' => '3103', 'nama_akun' => 'Laba Tahun Berjalan', 'kategori' => 'Ekuitas', 'jumlah' => 17000000],
             ]), 'sort_order' => 4],
            ['name' => 'Arus Kas', 'slug' => 'arus-kas', 'description' => 'Laporan arus kas', 'type' => 'arus_kas', 'frequency' => 'bulanan',
             'columns' => json_encode([
                ['key' => 'keterangan', 'name' => 'Keterangan (*)', 'type' => 'text', 'width' => 40, 'rules' => ['required']],
                ['key' => 'kategori', 'name' => 'Kategori (*)', 'type' => 'text', 'width' => 20, 'rules' => ['required']],
                ['key' => 'jumlah', 'name' => 'Jumlah (Rp) (*)', 'type' => 'number', 'width' => 20, 'rules' => ['required', 'numeric']],
             ]),
             'sample_data' => json_encode([
                ['keterangan' => 'Penerimaan dari penjualan', 'kategori' => 'Operasi', 'jumlah' => 100000000],
                ['keterangan' => 'Pembayaran gaji', 'kategori' => 'Operasi', 'jumlah' => -15000000],
                ['keterangan' => 'Pembelian peralatan', 'kategori' => 'Investasi', 'jumlah' => -25000000],
             ]), 'sort_order' => 5],
            ['name' => 'Perubahan Modal', 'slug' => 'perubahan-modal', 'description' => 'Laporan perubahan modal', 'type' => 'modal', 'frequency' => 'tahunan',
             'columns' => json_encode([
                ['key' => 'keterangan', 'name' => 'Keterangan (*)', 'type' => 'text', 'width' => 40, 'rules' => ['required']],
                ['key' => 'jumlah', 'name' => 'Jumlah (Rp) (*)', 'type' => 'number', 'width' => 20, 'rules' => ['required', 'numeric']],
             ]),
             'sample_data' => json_encode([
                ['keterangan' => 'Saldo Awal Modal (3101)', 'jumlah' => 50000000],
                ['keterangan' => 'Penyertaan Modal dari Desa', 'jumlah' => 10000000],
                ['keterangan' => 'Laba Tahun Berjalan', 'jumlah' => 35000000],
             ]), 'sort_order' => 6],
            ['name' => 'Realisasi Anggaran', 'slug' => 'realisasi-anggaran', 'description' => 'Realisasi anggaran per program', 'type' => 'realisasi_anggaran', 'frequency' => 'bulanan',
             'columns' => json_encode([
                ['key' => 'kode_akun', 'name' => 'Kode Akun', 'type' => 'text', 'width' => 15],
                ['key' => 'uraian', 'name' => 'Uraian (*)', 'type' => 'text', 'width' => 35, 'rules' => ['required']],
                ['key' => 'anggaran', 'name' => 'Anggaran (Rp) (*)', 'type' => 'number', 'width' => 20, 'rules' => ['required', 'numeric']],
                ['key' => 'realisasi', 'name' => 'Realisasi (Rp) (*)', 'type' => 'number', 'width' => 20, 'rules' => ['required', 'numeric']],
                ['key' => 'persentase', 'name' => 'Persentase (%)', 'type' => 'number', 'width' => 15],
                ['key' => 'selisih', 'name' => 'Selisih (Rp)', 'type' => 'number', 'width' => 18],
             ]),
             'sample_data' => json_encode([
                ['kode_akun' => '5101', 'uraian' => 'Beban gaji', 'anggaran' => 15000000, 'realisasi' => 15000000, 'persentase' => 100, 'selisih' => 0],
                ['kode_akun' => '5103', 'uraian' => 'Beban listrik', 'anggaran' => 2400000, 'realisasi' => 2000000, 'persentase' => 83.33, 'selisih' => 400000],
             ]), 'sort_order' => 7],
            ['name' => 'CAT (Catatan Atas Laporan Keuangan)', 'slug' => 'cat-laporan', 'description' => 'Catatan atas laporan keuangan SAK EMKM', 'type' => 'cat', 'frequency' => 'tahunan',
             'columns' => json_encode([
                ['key' => 'no', 'name' => 'No. (*)', 'type' => 'text', 'width' => 8, 'rules' => ['required']],
                ['key' => 'uraian', 'name' => 'Uraian (*)', 'type' => 'text', 'width' => 50, 'rules' => ['required']],
                ['key' => 'nilai', 'name' => 'Nilai (Rp)', 'type' => 'number', 'width' => 20],
                ['key' => 'keterangan', 'name' => 'Keterangan', 'type' => 'text', 'width' => 35],
             ]),
             'sample_data' => json_encode([
                ['no' => '1', 'uraian' => 'Bentuk Usaha', 'nilai' => '', 'keterangan' => 'BUMDes'],
                ['no' => '2', 'uraian' => 'Kebijakan Akuntansi', 'nilai' => '', 'keterangan' => 'Akrual, SAK EMKM'],
                ['no' => '3', 'uraian' => 'Aset Tetap', 'nilai' => '', 'keterangan' => 'Depresiasi garis lurus 5 tahun'],
                ['no' => '4', 'uraian' => 'Modal Disetor', 'nilai' => 50000000, 'keterangan' => 'Dari Pemerintah Desa'],
             ]), 'sort_order' => 8],
        ];
        foreach ($templates as $data) {
            FinancialTemplate::updateOrCreate(['slug' => $data['slug']], $data);
        }
    }
}
