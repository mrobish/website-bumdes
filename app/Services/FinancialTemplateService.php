<?php

namespace App\Services;

use App\Models\BumdesSetting;
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
        $akunItems = $coaList->filter(fn($a) => !$a->is_group);

        // ── Ambil data BUMDes dari settings ──
        $bumdes = BumdesSetting::first();
        $namaBumdes = strtoupper($bumdes->bumdes_name ?? $bumdes->village_name ?? 'BUMDes');
        $alamat = trim(($bumdes->bumdes_address ?? '') . ', ' . ($bumdes->bumdes_village ?? '') . ', Kec. ' . ($bumdes->bumdes_district ?? '') . ', Kab. ' . ($bumdes->bumdes_regency ?? '') . ', Prov. ' . ($bumdes->bumdes_province ?? ''));
        $email = $bumdes->email ?? '';
        $columns = is_string($template->columns) ? json_decode($template->columns, true) : ($template->columns ?? []);
        $lastCol = $this->getColumnLetter(count($columns));
        $headerRow = 5;

        // ── KOP SURAT: Nama BUMDes, Alamat, Email ──
        $sheet->setCellValue('A1', $namaBumdes);
        $sheet->getStyle('A1')->applyFromArray(['font' => ['bold' => true, 'size' => 14], 'alignment' => ['horizontal' => 'center']]);
        $sheet->mergeCells("A1:{$lastCol}1");

        $sheet->setCellValue('A2', $alamat);
        $sheet->getStyle('A2')->applyFromArray(['font' => ['size' => 10], 'alignment' => ['horizontal' => 'center']]);
        $sheet->mergeCells("A2:{$lastCol}2");

        $sheet->setCellValue('A3', $email);
        $sheet->getStyle('A3')->applyFromArray(['font' => ['size' => 10], 'color' => ['rgb' => '3498DB'], 'alignment' => ['horizontal' => 'center']]);
        $sheet->mergeCells("A3:{$lastCol}3");

        // Garis pemisah
        $sheet->setCellValue('A4', str_repeat('─', 80));
        $sheet->getStyle('A4')->applyFromArray(['font' => ['size' => 8], 'alignment' => ['horizontal' => 'center']]);
        $sheet->mergeCells("A4:{$lastCol}4");

        // ── JUDUL LAPORAN ──
        // (sebelumnya ada judul di row 1-2, sekarang row 6 jadi header kolom)

        // ── Header Style ──
        $headerStyle = [
            'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '2C3E50']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center', 'wrap_text' => true],
            'borders' => ['allBorders' => ['borderStyle' => 'thin']],
        ];
        $dataStyle = ['borders' => ['allBorders' => ['borderStyle' => 'thin']], 'alignment' => ['vertical' => 'center']];
        $currencyFmt = '#,##0';
        $sampleData = is_string($template->sample_data) ? json_decode($template->sample_data, true) : ($template->sample_data ?? []);

        // ── Tulis Header Kolom ──
        foreach ($columns as $col => $header) {
            $cell = $this->getColumnLetter($col + 1) . $headerRow;
            $sheet->setCellValue($cell, $header['name'] ?? $header);
            $sheet->getStyle($cell)->applyFromArray($headerStyle);
            $sheet->getColumnDimension($this->getColumnLetter($col + 1))->setWidth($header['width'] ?? 20);
        }

        // ── RefCOA Sheet (hidden) ──
        $coaSheet = $spreadsheet->createSheet();
        $coaSheet->setTitle('RefCOA');
        $coaSheet->setCellValue('A1', 'Kode');
        $coaSheet->setCellValue('B1', 'Nama Akun');
        $coaSheet->setCellValue('C1', 'Kategori');
        $coaSheet->getColumnDimension('A')->setWidth(12);
        $coaSheet->getColumnDimension('B')->setWidth(35);
        $coaSheet->getColumnDimension('C')->setWidth(20);
        $coaRow = 2;
        $kodeOptions = [];
        foreach ($akunItems as $a) {
            $coaSheet->setCellValue("A{$coaRow}", $a->code);
            $coaSheet->setCellValue("B{$coaRow}", $a->name);
            // Map kode ke kategori otomatis
            $kategori = $this->mapKodeToKategori($a->code, $a->type);
            $coaSheet->setCellValue("C{$coaRow}", $kategori);
            $kodeOptions[] = $a->code;
            $coaRow++;
        }
        $coaLastRow = $coaRow - 1;
        $coaSheet->setSheetState(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::SHEETSTATE_VERYHIDDEN);
        $kodeList = implode(',', $kodeOptions);

        // ── Sample Data ──
        $sampleRow = $headerRow + 1;
        $sampleStyle = ['fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'ECF0F1']]];
        if (!empty($sampleData)) {
            foreach ($sampleData as $row) {
                foreach ($columns as $col => $header) {
                    $cell = $this->getColumnLetter($col + 1) . $sampleRow;
                    $key = $header['key'] ?? $col;
                    $val = $row[$key] ?? '';
                    // Tulis nilai sebagai string agar tidak error formula
                    if (is_numeric($val)) {
                        $sheet->setCellValue($cell, (float)$val);
                    } else {
                        $sheet->setCellValue($cell, (string)$val);
                    }
                    $sheet->getStyle($cell)->applyFromArray(array_merge($dataStyle, $sampleStyle));
                    if (isset($header['type']) && $header['type'] === 'number') {
                        $sheet->getStyle($cell)->getNumberFormat()->setFormatCode($currencyFmt);
                    }
                }
                $sampleRow++;
            }
        }

        // ── Data Rows with Dropdown + VLOOKUP + Formulas ──
        $dataStart = $sampleRow;
        $dataEnd = $dataStart + 49; // 50 baris kosong
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
        $jumlahIdx = $this->findColIndex($columns, 'jumlah');
        $debetIdx = $this->findColIndex($columns, 'debet');
        $kreditIdx = $this->findColIndex($columns, 'kredit');

        for ($row = $dataStart; $row <= $dataEnd; $row++) {
            // Style semua kolom
            foreach ($columns as $col => $header) {
                $cell = $this->getColumnLetter($col + 1) . $row;
                $sheet->getStyle($cell)->applyFromArray($dataStyle);
                if (isset($header['type']) && $header['type'] === 'number') {
                    $sheet->getStyle($cell)->getNumberFormat()->setFormatCode($currencyFmt);
                }
            }

            // ── KODE AKUN: Dropdown ──
            if ($kodeColIdx !== false) {
                $kodeCell = $this->getColumnLetter($kodeColIdx + 1) . $row;
                $this->addDropdown($sheet, $kodeCell, $kodeList, 'Pilih Kode Akun');
            }

            // ── NAMA AKUN: VLOOKUP otomatis dari Kode Akun ──
            if ($namaColIdx !== false && $kodeColIdx !== false) {
                $namaCell = $this->getColumnLetter($namaColIdx + 1) . $row;
                $kodeCell = $this->getColumnLetter($kodeColIdx + 1) . $row;
                // VLOOKUP: cari kode di RefCOA!A:B, ambil kolom 2 (nama)
                $sheet->setCellValue($namaCell, "=IFERROR(VLOOKUP({$kodeCell},RefCOA!\$A\$2:\$B\${$coaLastRow},2,FALSE),\"\")");
                $sheet->getStyle($namaCell)->getFont()->setItalic(true);
                $sheet->getStyle($namaCell)->getFont()->getColor()->setRGB('7F8C8D');
            }

            // ── KATEGORI: VLOOKUP otomatis dari Kode Akun ──
            if ($kategoriColIdx !== false && $kodeColIdx !== false) {
                $katCell = $this->getColumnLetter($kategoriColIdx + 1) . $row;
                $kodeCell = $this->getColumnLetter($kodeColIdx + 1) . $row;
                $sheet->setCellValue($katCell, "=IFERROR(VLOOKUP({$kodeCell},RefCOA!\$A\$2:\$C\${$coaLastRow},3,FALSE),\"\")");
                $sheet->getStyle($katCell)->getFont()->setItalic(true);
                $sheet->getStyle($katCell)->getFont()->getColor()->setRGB('7F8C8D');
            }

            // ── KATEGORI: Dropdown manual (untuk arus_kas, perubahan_modal) ──
            if ($kategoriColIdx !== false && $kodeColIdx === false) {
                $katList = match($template->type) {
                    'arus_kas' => 'Operasi,Investasi,Pendanaan',
                    'modal' => 'Saldo Awal,Penyertaan Modal,Laba/Tahun Berjalan,Dividen',
                    default => null,
                };
                if ($katList) {
                    $katCell = $this->getColumnLetter($kategoriColIdx + 1) . $row;
                    $this->addDropdown($sheet, $katCell, $katList, 'Pilih Kategori');
                }
            }

            // ── FORMULAS PER TIPE ──
            $this->addFormulas($sheet, $template->type, $row, [
                'sa' => $saColIdx, 'sd' => $sdColIdx, 'sk' => $skColIdx,
                'sa_akhir' => $saAkhirIdx, 'anggaran' => $anggaranIdx,
                'realisasi' => $realisasiIdx, 'persen' => $persenIdx,
                'selisih' => $selisihIdx, 'jumlah' => $jumlahIdx,
                'debet' => $debetIdx, 'kredit' => $kreditIdx,
            ], $currencyFmt);
        }

        // ── TOTAL ROW ──
        $sumRow = $dataEnd + 2;
        $this->addTotalRow($sheet, $columns, $template, $dataStart, $dataEnd, $sumRow, $currencyFmt, $coaLastRow);

        // ── Footer info ──
        $footerRow = $sumRow + 3;
        $footerText = ($bumdes->bumdes_village ?? '................') . ', ' . date('d/m/Y');
        $footerText2 = 'Mengetahui,';
        $footerText3 = $bumdes->pdf_footer_text ?? ($bumdes->bumdes_village ?? '................');
        $sheet->setCellValue("A{$footerRow}", $footerText);
        $sheet->getStyle("A{$footerRow}")->getAlignment()->setHorizontal('right');
        $sheet->mergeCells("A{$footerRow}:D{$footerRow}");
        $sheet->setCellValue("A" . ($footerRow + 4), "_________________________");
        $sheet->getStyle("A" . ($footerRow + 4))->getAlignment()->setHorizontal('center');
        $sheet->mergeCells("A" . ($footerRow + 4) . ":D" . ($footerRow + 4));
        $sheet->setCellValue("A" . ($footerRow + 5), $footerText3);
        $sheet->getStyle("A" . ($footerRow + 5))->getAlignment()->setHorizontal('center');
        $sheet->mergeCells("A" . ($footerRow + 5) . ":D" . ($footerRow + 5));

        // ── Petunjuk Sheet ──
        $this->createPetunjukSheet($spreadsheet, $template, $akunItems);

        // ── Save ──
        $fileName = 'templates/' . $template->slug . '-template.xlsx';
        $filePath = storage_path('app/' . $fileName);
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);
        @chmod($filePath, 0666);
        return $fileName;
    }

    /**
     * Map kode akun ke kategori untuk auto-fill
     * 11xx = Aset Lancar, 12xx = Aset Tetap, 2xxx = Kewajiban,
     * 3xxx = Ekuitas, 4xxx = Pendapatan, 5xxx = Beban
     */
    protected function mapKodeToKategori(string $code, string $type): string
    {
        $prefix = (int)substr($code, 0, 2);
        return match(true) {
            $prefix === 11 => 'Aset Lancar',
            $prefix === 12 => 'Aset Tetap',
            $prefix === 13 => 'Aset Lainnya',
            $prefix >= 20 && $prefix < 30 => 'Kewajiban',
            $prefix >= 30 && $prefix < 40 => 'Ekuitas',
            $prefix >= 40 && $prefix < 50 => 'Pendapatan',
            $prefix >= 50 && $prefix < 60 => 'Beban',
            default => ucfirst($type),
        };
    }

    /**
     * Tambah formula otomatis per baris sesuai tipe template
     */
    protected function addFormulas($sheet, string $type, int $row, array $idx, string $fmt): void
    {
        // ── BUKU BESAR: Saldo Akhir = Saldo Awal + Debet - Kredit ──
        if ($type === 'buku_besar' && $idx['sa'] !== false && $idx['sd'] !== false && $idx['sk'] !== false && $idx['sa_akhir'] !== false) {
            $sa = $this->getColumnLetter($idx['sa'] + 1);
            $sd = $this->getColumnLetter($idx['sd'] + 1);
            $sk = $this->getColumnLetter($idx['sk'] + 1);
            $sak = $this->getColumnLetter($idx['sa_akhir'] + 1);
            $sheet->setCellValue("{$sak}{$row}", "=IFERROR({$sa}{$row}+{$sd}{$row}-{$sk}{$row},0)");
        }

        // ── REALISASI ANGGARAN: Persentase + Selisih ──
        if ($type === 'realisasi_anggaran') {
            if ($idx['anggaran'] !== false && $idx['realisasi'] !== false && $idx['persen'] !== false) {
                $ang = $this->getColumnLetter($idx['anggaran'] + 1);
                $rea = $this->getColumnLetter($idx['realisasi'] + 1);
                $per = $this->getColumnLetter($idx['persen'] + 1);
                $sheet->setCellValue("{$per}{$row}", "=IFERROR(IF({$ang}{$row}>0,({$rea}{$row}/{$ang}{$row})*100,0),0)");
                $sheet->getStyle("{$per}{$row}")->getNumberFormat()->setFormatCode('0.00');
            }
            if ($idx['anggaran'] !== false && $idx['realisasi'] !== false && $idx['selisih'] !== false) {
                $ang = $this->getColumnLetter($idx['anggaran'] + 1);
                $rea = $this->getColumnLetter($idx['realisasi'] + 1);
                $sel = $this->getColumnLetter($idx['selisih'] + 1);
                $sheet->setCellValue("{$sel}{$row}", "=IFERROR({$ang}{$row}-{$rea}{$row},0)");
            }
        }
    }

    /**
     * Tambah baris TOTAL di bawah data
     */
    protected function addTotalRow($sheet, array $columns, FinancialTemplate $template, int $dataStart, int $dataEnd, int $sumRow, string $fmt, int $coaLastRow): void
    {
        $debetIdx = $this->findColIndex($columns, 'debet');
        $kreditIdx = $this->findColIndex($columns, 'kredit');
        $jumlahIdx = $this->findColIndex($columns, 'jumlah');
        $anggaranIdx = $this->findColIndex($columns, 'anggaran');
        $realisasiIdx = $this->findColIndex($columns, 'realisasi');
        $lastCol = $this->getColumnLetter(count($columns));
        $totalStyle = ['font' => ['bold' => true, 'size' => 11], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'D5F5E3']], 'borders' => ['allBorders' => ['borderStyle' => 'thin']]];

        // ── JURNAL: Total Debet, Total Kredit, Selisih ──
        if ($template->type === 'jurnal' && $debetIdx !== false && $kreditIdx !== false) {
            $dCol = $this->getColumnLetter($debetIdx + 1);
            $kCol = $this->getColumnLetter($kreditIdx + 1);
            $sheet->setCellValue("A{$sumRow}", 'TOTAL');
            $sheet->getStyle("A{$sumRow}")->applyFromArray(['font' => ['bold' => true, 'size' => 12]]);

            $sheet->setCellValue("{$dCol}{$sumRow}", "=SUM({$dCol}{$dataStart}:{$dCol}{$dataEnd})");
            $sheet->getStyle("{$dCol}{$sumRow}")->applyFromArray($totalStyle);
            $sheet->getStyle("{$dCol}{$sumRow}")->getNumberFormat()->setFormatCode($fmt);

            $sheet->setCellValue("{$kCol}{$sumRow}", "=SUM({$kCol}{$dataStart}:{$kCol}{$dataEnd})");
            $sheet->getStyle("{$kCol}{$sumRow}")->applyFromArray($totalStyle);
            $sheet->getStyle("{$kCol}{$sumRow}")->getNumberFormat()->setFormatCode($fmt);

            // Selisih = Debet - Kredit (harus = 0)
            $selCol = $this->getColumnLetter(count($columns) + 1);
            $sheet->setCellValue("{$selCol}1", 'Selisih');
            $sheet->getStyle("{$selCol}1")->applyFromArray(['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'F39C12']]]);
            $sheet->setCellValue("{$selCol}{$sumRow}", "=IFERROR({$dCol}{$sumRow}-{$kCol}{$sumRow},0)");
            $sheet->getStyle("{$selCol}{$sumRow}")->applyFromArray(['font' => ['bold' => true, 'color' => ['rgb' => 'E74C3C'], 'size' => 12]]);
            $sheet->getStyle("{$selCol}{$sumRow}")->getNumberFormat()->setFormatCode($fmt);
        }

        // ── BUKU BESAR: Total Saldo Awal, Debet, Kredit, Saldo Akhir ──
        if ($template->type === 'buku_besar') {
            $cols = ['saldo_awal', 'total_debet', 'total_kredit', 'saldo_akhir'];
            $sheet->setCellValue("A{$sumRow}", 'TOTAL');
            $sheet->getStyle("A{$sumRow}")->applyFromArray(['font' => ['bold' => true, 'size' => 12]]);
            foreach ($cols as $ck) {
                $ci = $this->findColIndex($columns, $ck);
                if ($ci !== false) {
                    $cl = $this->getColumnLetter($ci + 1);
                    $sheet->setCellValue("{$cl}{$sumRow}", "=SUM({$cl}{$dataStart}:{$cl}{$dataEnd})");
                    $sheet->getStyle("{$cl}{$sumRow}")->applyFromArray($totalStyle);
                    $sheet->getStyle("{$cl}{$sumRow}")->getNumberFormat()->setFormatCode($fmt);
                }
            }
        }

        // ── LABA RUGI: Total Pendapatan, Total Beban, Laba Bersih ──
        if ($template->type === 'laba_rugi' && $jumlahIdx !== false) {
            $jCol = $this->getColumnLetter($jumlahIdx + 1);
            $katCol = $this->getColumnLetter($this->findColIndex($columns, 'kategori') + 1);

            // Total Pendapatan (SUMIF kategori = "Pendapatan")
            $sheet->setCellValue("A{$sumRow}", 'TOTAL PENDAPATAN');
            $sheet->getStyle("A{$sumRow}")->applyFromArray(['font' => ['bold' => true]]);
            $sheet->setCellValue("{$jCol}{$sumRow}", "=IFERROR(SUMIF({$katCol}{$dataStart}:{$katCol}{$dataEnd},\"Pendapatan\",{$jCol}{$dataStart}:{$jCol}{$dataEnd}),0)");
            $sheet->getStyle("{$jCol}{$sumRow}")->applyFromArray($totalStyle);
            $sheet->getStyle("{$jCol}{$sumRow}")->getNumberFormat()->setFormatCode($fmt);

            // Total Beban
            $sumRow2 = $sumRow + 1;
            $sheet->setCellValue("A{$sumRow2}", 'TOTAL BEBAN');
            $sheet->getStyle("A{$sumRow2}")->applyFromArray(['font' => ['bold' => true]]);
            $sheet->setCellValue("{$jCol}{$sumRow2}", "=IFERROR(SUMIF({$katCol}{$dataStart}:{$katCol}{$dataEnd},\"Beban\",{$jCol}{$dataStart}:{$jCol}{$dataEnd}),0)");
            $sheet->getStyle("{$jCol}{$sumRow2}")->applyFromArray($totalStyle);
            $sheet->getStyle("{$jCol}{$sumRow2}")->getNumberFormat()->setFormatCode($fmt);

            // Laba Bersih = Total Pendapatan - Total Beban
            $sumRow3 = $sumRow + 2;
            $sheet->setCellValue("A{$sumRow3}", 'LABA BERSIH');
            $sheet->getStyle("A{$sumRow3}")->applyFromArray(['font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '27AE60']]]);
            $sheet->setCellValue("{$jCol}{$sumRow3}", "=IFERROR({$jCol}{$sumRow}-{$jCol}{$sumRow2},0)");
            $sheet->getStyle("{$jCol}{$sumRow3}")->applyFromArray(['font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '27AE60']]]);
            $sheet->getStyle("{$jCol}{$sumRow3}")->getNumberFormat()->setFormatCode($fmt);
        }

        // ── NERACA: Total Aset, Total Kewajiban+Ekuitas, Selisih ──
        if ($template->type === 'neraca' && $jumlahIdx !== false) {
            $jCol = $this->getColumnLetter($jumlahIdx + 1);
            $katCol = $this->getColumnLetter($this->findColIndex($columns, 'kategori') + 1);

            // Total Aset (Lancar + Tetap + Lainnya)
            $sheet->setCellValue("A{$sumRow}", 'TOTAL ASET');
            $sheet->getStyle("A{$sumRow}")->applyFromArray(['font' => ['bold' => true]]);
            $sheet->setCellValue("{$jCol}{$sumRow}", "=IFERROR(SUMPRODUCT(({\$katCol}{\$dataStart}:{\$katCol}{\$dataEnd}=\"Aset Lancar\")*({\$jCol}{\$dataStart}:{\$jCol}{\$dataEnd}))+SUMPRODUCT(({\$katCol}{\$dataStart}:{\$katCol}{\$dataEnd}=\"Aset Tetap\")*({\$jCol}{\$dataStart}:{\$jCol}{\$dataEnd}))+SUMPRODUCT(({\$katCol}{\$dataStart}:{\$katCol}{\$dataEnd}=\"Aset Lainnya\")*({\$jCol}{\$dataStart}:{\$jCol}{\$dataEnd})),0)");
            // Fix: use proper cell references
            $sheet->setCellValue("{$jCol}{$sumRow}", "=IFERROR(SUMIF({$katCol}{$dataStart}:{$katCol}{$dataEnd},\"Aset Lancar\",{$jCol}{$dataStart}:{$jCol}{$dataEnd})+SUMIF({$katCol}{$dataStart}:{$katCol}{$dataEnd},\"Aset Tetap\",{$jCol}{$dataStart}:{$jCol}{$dataEnd})+SUMIF({$katCol}{$dataStart}:{$katCol}{$dataEnd},\"Aset Lainnya\",{$jCol}{$dataStart}:{$jCol}{$dataEnd}),0)");
            $sheet->getStyle("{$jCol}{$sumRow}")->applyFromArray($totalStyle);
            $sheet->getStyle("{$jCol}{$sumRow}")->getNumberFormat()->setFormatCode($fmt);

            // Total Kewajiban + Ekuitas
            $sumRow2 = $sumRow + 1;
            $sheet->setCellValue("A{$sumRow2}", 'TOTAL KEWAJIBAN + EKUITAS');
            $sheet->getStyle("A{$sumRow2}")->applyFromArray(['font' => ['bold' => true]]);
            $sheet->setCellValue("{$jCol}{$sumRow2}", "=IFERROR(SUMIF({$katCol}{$dataStart}:{$katCol}{$dataEnd},\"Kewajiban\",{$jCol}{$dataStart}:{$jCol}{$dataEnd})+SUMIF({$katCol}{$dataStart}:{$katCol}{$dataEnd},\"Ekuitas\",{$jCol}{$dataStart}:{$jCol}{$dataEnd}),0)");
            $sheet->getStyle("{$jCol}{$sumRow2}")->applyFromArray($totalStyle);
            $sheet->getStyle("{$jCol}{$sumRow2}")->getNumberFormat()->setFormatCode($fmt);

            // Selisih = Total Aset - (Kewajiban + Ekuitas) — harus = 0
            $sumRow3 = $sumRow + 2;
            $sheet->setCellValue("A{$sumRow3}", 'SELISIH (harus = 0)');
            $sheet->getStyle("A{$sumRow3}")->applyFromArray(['font' => ['bold' => true, 'color' => ['rgb' => 'E74C3C']]]);
            $sheet->setCellValue("{$jCol}{$sumRow3}", "=IFERROR({$jCol}{$sumRow}-{$jCol}{$sumRow2},0)");
            $sheet->getStyle("{$jCol}{$sumRow3}")->applyFromArray(['font' => ['bold' => true, 'color' => ['rgb' => 'E74C3C'], 'size' => 12]]);
            $sheet->getStyle("{$jCol}{$sumRow3}")->getNumberFormat()->setFormatCode($fmt);
        }

        // ── ARUS KAS: Total per Kategori ──
        if ($template->type === 'arus_kas' && $jumlahIdx !== false) {
            $jCol = $this->getColumnLetter($jumlahIdx + 1);
            $katCol = $this->getColumnLetter($this->findColIndex($columns, 'kategori') + 1);

            $sheet->setCellValue("A{$sumRow}", 'TOTAL ARUS KAS OPERASI');
            $sheet->getStyle("A{$sumRow}")->applyFromArray(['font' => ['bold' => true]]);
            $sheet->setCellValue("{$jCol}{$sumRow}", "=IFERROR(SUMIF({$katCol}{$dataStart}:{$katCol}{$dataEnd},\"Operasi\",{$jCol}{$dataStart}:{$jCol}{$dataEnd}),0)");
            $sheet->getStyle("{$jCol}{$sumRow}")->applyFromArray($totalStyle);
            $sheet->getStyle("{$jCol}{$sumRow}")->getNumberFormat()->setFormatCode($fmt);

            $sumRow2 = $sumRow + 1;
            $sheet->setCellValue("A{$sumRow2}", 'TOTAL ARUS KAS INVESTASI');
            $sheet->getStyle("A{$sumRow2}")->applyFromArray(['font' => ['bold' => true]]);
            $sheet->setCellValue("{$jCol}{$sumRow2}", "=IFERROR(SUMIF({$katCol}{$dataStart}:{$katCol}{$dataEnd},\"Investasi\",{$jCol}{$dataStart}:{$jCol}{$dataEnd}),0)");
            $sheet->getStyle("{$jCol}{$sumRow2}")->applyFromArray($totalStyle);
            $sheet->getStyle("{$jCol}{$sumRow2}")->getNumberFormat()->setFormatCode($fmt);

            $sumRow3 = $sumRow + 2;
            $sheet->setCellValue("A{$sumRow3}", 'TOTAL ARUS KAS PENDANAAN');
            $sheet->getStyle("A{$sumRow3}")->applyFromArray(['font' => ['bold' => true]]);
            $sheet->setCellValue("{$jCol}{$sumRow3}", "=IFERROR(SUMIF({$katCol}{$dataStart}:{$katCol}{$dataEnd},\"Pendanaan\",{$jCol}{$dataStart}:{$jCol}{$dataEnd}),0)");
            $sheet->getStyle("{$jCol}{$sumRow3}")->applyFromArray($totalStyle);
            $sheet->getStyle("{$jCol}{$sumRow3}")->getNumberFormat()->setFormatCode($fmt);

            $sumRow4 = $sumRow + 3;
            $sheet->setCellValue("A{$sumRow4}", 'KENAIKAN/PENURUNAN BERSIH KAS');
            $sheet->getStyle("A{$sumRow4}")->applyFromArray(['font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '27AE60']]]);
            $sheet->setCellValue("{$jCol}{$sumRow4}", "=IFERROR({$jCol}{$sumRow}+{$jCol}{$sumRow2}+{$jCol}{$sumRow3},0)");
            $sheet->getStyle("{$jCol}{$sumRow4}")->applyFromArray(['font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '27AE60']]]);
            $sheet->getStyle("{$jCol}{$sumRow4}")->getNumberFormat()->setFormatCode($fmt);
        }

        // ── PERUBAHAN MODAL: Total ──
        if ($template->type === 'modal' && $jumlahIdx !== false) {
            $jCol = $this->getColumnLetter($jumlahIdx + 1);
            $sheet->setCellValue("A{$sumRow}", 'TOTAL PERUBAHAN MODAL');
            $sheet->getStyle("A{$sumRow}")->applyFromArray(['font' => ['bold' => true, 'size' => 12]]);
            $sheet->setCellValue("{$jCol}{$sumRow}", "=SUM({$jCol}{$dataStart}:{$jCol}{$dataEnd})");
            $sheet->getStyle("{$jCol}{$sumRow}")->applyFromArray($totalStyle);
            $sheet->getStyle("{$jCol}{$sumRow}")->getNumberFormat()->setFormatCode($fmt);
        }

        // ── REALISASI ANGGARAN: Total Anggaran + Realisasi ──
        if ($template->type === 'realisasi_anggaran' && $anggaranIdx !== false && $realisasiIdx !== false) {
            $aCol = $this->getColumnLetter($anggaranIdx + 1);
            $rCol = $this->getColumnLetter($realisasiIdx + 1);
            $sheet->setCellValue("A{$sumRow}", 'TOTAL');
            $sheet->getStyle("A{$sumRow}")->applyFromArray(['font' => ['bold' => true, 'size' => 12]]);

            $sheet->setCellValue("{$aCol}{$sumRow}", "=SUM({$aCol}{$dataStart}:{$aCol}{$dataEnd})");
            $sheet->getStyle("{$aCol}{$sumRow}")->applyFromArray($totalStyle);
            $sheet->getStyle("{$aCol}{$sumRow}")->getNumberFormat()->setFormatCode($fmt);

            $sheet->setCellValue("{$rCol}{$sumRow}", "=SUM({$rCol}{$dataStart}:{$rCol}{$dataEnd})");
            $sheet->getStyle("{$rCol}{$sumRow}")->applyFromArray($totalStyle);
            $sheet->getStyle("{$rCol}{$sumRow}")->getNumberFormat()->setFormatCode($fmt);

            // Total Selisih
            if ($this->findColIndex($columns, 'selisih') !== false) {
                $selCol = $this->getColumnLetter($this->findColIndex($columns, 'selisih') + 1);
                $sheet->setCellValue("{$selCol}{$sumRow}", "=IFERROR({$aCol}{$sumRow}-{$rCol}{$sumRow},0)");
                $sheet->getStyle("{$selCol}{$sumRow}")->applyFromArray($totalStyle);
                $sheet->getStyle("{$selCol}{$sumRow}")->getNumberFormat()->setFormatCode($fmt);
            }

            // Total Persentase
            if ($this->findColIndex($columns, 'persentase') !== false) {
                $perCol = $this->getColumnLetter($this->findColIndex($columns, 'persentase') + 1);
                $sheet->setCellValue("{$perCol}{$sumRow}", "=IFERROR(IF({$aCol}{$sumRow}>0,({$rCol}{$sumRow}/{$aCol}{$sumRow})*100,0),0)");
                $sheet->getStyle("{$perCol}{$sumRow}")->getNumberFormat()->setFormatCode('0.00');
                $sheet->getStyle("{$perCol}{$sumRow}")->applyFromArray($totalStyle);
            }
        }
    }

    /**
     * Tambah Data Validation (dropdown)
     * NOTE: setShowDropDown(false) = TAMPILKAN arrow dropdown
     */
    protected function addDropdown($sheet, string $cell, string $list, string $title): void
    {
        $v = new DataValidation();
        $v->setType(DataValidation::TYPE_LIST);
        $v->setAllowBlank(true);
        $v->setShowDropDown(false); // false = TAMPILKAN dropdown arrow
        $v->setFormula1('"' . $list . '"');
        $v->setShowErrorMessage(true);
        $v->setErrorTitle($title);
        $v->setError('Pilih dari dropdown. Jangan ketik manual.');
        $v->setShowInputMessage(true);
        $v->setPromptTitle($title);
        $v->setPrompt('Pilih dari daftar yang tersedia.');
        $sheet->setDataValidation($cell, $v);
    }

    /**
     * Buat sheet Petunjuk
     */
    protected function createPetunjukSheet(Spreadsheet $spreadsheet, FinancialTemplate $template, $akunItems): void
    {
        $bumdes = BumdesSetting::first();
        $inst = $spreadsheet->createSheet();
        $inst->setTitle('Petunjuk');
        $inst->getColumnDimension('A')->setWidth(5);
        $inst->getColumnDimension('B')->setWidth(65);
        $inst->getColumnDimension('C')->setWidth(30);

        $row = 1;
        $inst->setCellValue("B{$row}", 'PETUNJUK PENGISIAN ' . strtoupper($template->name));
        $inst->getStyle("B{$row}")->applyFromArray(['font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '2C3E50']]]);

        $row = 3;
        $inst->setCellValue("B{$row}", 'CARA PENGISIAN');
        $inst->getStyle("B{$row}")->applyFromArray(['font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => '2C3E50']]]);
        $steps = [
            '1. Download template ini',
            '2. Buka dengan Microsoft Excel atau LibreOffice Calc',
            '3. Isi data pada sheet "' . $template->name . '"',
            '4. Pilih Kode Akun dari DROPDOWN (kolom akan otomatis terisi: Nama Akun & Kategori)',
            '5. Baris ABU-ABU = CONTOH, boleh dihapus',
            '6. JANGAN ubah baris header (kop surat + nama kolom)',
            '7. Rumus sudah terisi otomatis — JANGAN edit kolom yang ada rumus',
            '8. Kolom KETERINGAN/URAIAN diisi manual',
            '9. Simpan dalam format .xlsx, lalu upload ke aplikasi',
        ];
        foreach ($steps as $s) { $row++; $inst->setCellValue("B{$row}", $s); }

        $row += 2;
        $inst->setCellValue("B{$row}", 'KOLOM OTOMATIS (tidak perlu diisi)');
        $inst->getStyle("B{$row}")->applyFromArray(['font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => '27AE60']]]);
        $autoCols = [
            '• Nama Akun — otomatis terisi setelah pilih Kode Akun',
            '• Kategori — otomatis terisi dari kode akun (Aset Lancar, Pendapatan, dll)',
            '• Saldo Akhir (Buku Besar) — rumus: Saldo Awal + Debet - Kredit',
            '• Persentase (Realisasi) — rumus: (Realisasi / Anggaran) × 100',
            '• Selisih — rumus: Anggaran - Realisasi',
            '• Total — dihitung otomatis di baris TOTAL',
            '• Laba Bersih (Laba Rugi) — rumus: Total Pendapatan - Total Beban',
            '• Selisih Neraca — harus = 0 (Aset = Kewajiban + Ekuitas)',
        ];
        foreach ($autoCols as $c) { $row++; $inst->setCellValue("B{$row}", $c); $inst->getStyle("B{$row}")->applyFromArray(['font' => ['color' => ['rgb' => '27AE60']]]); }

        $row += 2;
        $inst->setCellValue("B{$row}", 'KOLOM MANUAL (isi sendiri)');
        $inst->getStyle("B{$row}")->applyFromArray(['font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => 'E67E22']]]);
        $manualCols = [
            '• Tanggal — isi DD/MM/YYYY',
            '• No. Ref — nomor referensi transaksi',
            '• Keterangan / Uraian — deskripsi transaksi',
            '• Debet / Kredit / Jumlah / Anggaran / Realisasi — input angka',
        ];
        foreach ($manualCols as $c) { $row++; $inst->setCellValue("B{$row}", $c); $inst->getStyle("B{$row}")->applyFromArray(['font' => ['color' => ['rgb' => 'E67E22']]]); }

        $row += 2;
        $inst->setCellValue("B{$row}", 'ATURAN PENTING');
        $inst->getStyle("B{$row}")->applyFromArray(['font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => 'E74C3C']]]);
        $rules = [
            '❌ JANGAN ketik Kode Akun manual — gunakan DROPDOWN',
            '❌ JANGAN edit kolom yang sudah ada rumus (abu-abu/hijau)',
            '❌ JANGAN hapus/ubah baris kop surat (baris 1-5)',
            '✅ Jika kode akun belum ada, buat dulu di Pengaturan Keuangan > COA',
            '✅ Format angka: tanpa titik/koma pemisah (5000000, bukan 5.000.000)',
            '✅ Simpan sebagai .xlsx sebelum upload',
        ];
        foreach ($rules as $r) { $row++; $inst->setCellValue("B{$row}", $r); }

        $row += 2;
        $inst->setCellValue("B{$row}", 'DAFTAR KODE AKUN');
        $inst->getStyle("B{$row}")->applyFromArray(['font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => '2C3E50']]]);
        $row++;
        $inst->setCellValue("B{$row}", 'Jika kode belum ada, buat dulu di Pengaturan Keuangan > Chart of Accounts');
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
        $inst->setCellValue("B{$row}", 'KONTAK');
        $inst->getStyle("B{$row}")->applyFromArray(['font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => '2C3E50']]]);
        $row++;
        $inst->setCellValue("B{$row}", 'Email: ' . ($bumdes->email ?? 'admin@bumdes.id'));
        $row++;
        $inst->setCellValue("B{$row}", 'Telp: ' . ($bumdes->phone ?? '-'));
        $row++;
        $inst->setCellValue("B{$row}", 'Website: ' . ($bumdes->website ?? '-'));
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
        $highestRow = $sheet->getHighestRow();
        // Data mulai dari baris 6 (setelah kop 4 baris + header 1 baris)
        for ($row = 6; $row <= $highestRow; $row++) {
            $rowData = [];
            $isEmpty = true;
            foreach ($columns as $col => $header) {
                $cell = $this->getColumnLetter($col + 1) . $row;
                $value = $sheet->getCell($cell)->getCalculatedValue();
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

    public function seedDefaultTemplates(): void
    {
        $templates = [
            ['name' => 'Jurnal Umum', 'slug' => 'jurnal-umum', 'description' => 'Jurnal umum untuk mencatat transaksi harian', 'type' => 'jurnal', 'frequency' => 'harian',
             'columns' => json_encode([
                ['key' => 'tanggal', 'name' => 'Tanggal (*)', 'type' => 'date', 'width' => 15, 'rules' => ['required']],
                ['key' => 'no_ref', 'name' => 'No. Ref', 'type' => 'text', 'width' => 12],
                ['key' => 'kode_akun', 'name' => 'Kode Akun (*)', 'type' => 'text', 'width' => 15, 'rules' => ['required']],
                ['key' => 'nama_akun', 'name' => 'Nama Akun', 'type' => 'text', 'width' => 25],
                ['key' => 'keterangan', 'name' => 'Keterangan (*)', 'type' => 'text', 'width' => 35, 'rules' => ['required']],
                ['key' => 'debet', 'name' => 'Debet (Rp)', 'type' => 'number', 'width' => 18],
                ['key' => 'kredit', 'name' => 'Kredit (Rp)', 'type' => 'number', 'width' => 18],
             ]),
             'sample_data' => json_encode([
                ['tanggal' => '01/01/2026', 'no_ref' => 'J-001', 'kode_akun' => '1101', 'nama_akun' => '', 'keterangan' => 'Setoran modal dari Desa', 'debet' => 50000000, 'kredit' => 0],
                ['tanggal' => '01/01/2026', 'no_ref' => 'J-001', 'kode_akun' => '3101', 'nama_akun' => '', 'keterangan' => 'Setoran modal dari Desa', 'debet' => 0, 'kredit' => 50000000],
                ['tanggal' => '05/01/2026', 'no_ref' => 'J-002', 'kode_akun' => '1101', 'nama_akun' => '', 'keterangan' => 'Penjualan produk UMKM', 'debet' => 10000000, 'kredit' => 0],
                ['tanggal' => '05/01/2026', 'no_ref' => 'J-002', 'kode_akun' => '4101', 'nama_akun' => '', 'keterangan' => 'Penjualan produk UMKM', 'debet' => 0, 'kredit' => 10000000],
             ]), 'sort_order' => 1],

            ['name' => 'Buku Besar', 'slug' => 'buku-besar', 'description' => 'Rekapitulasi per akun per bulan', 'type' => 'buku_besar', 'frequency' => 'bulanan',
             'columns' => json_encode([
                ['key' => 'kode_akun', 'name' => 'Kode Akun (*)', 'type' => 'text', 'width' => 15, 'rules' => ['required']],
                ['key' => 'nama_akun', 'name' => 'Nama Akun', 'type' => 'text', 'width' => 25],
                ['key' => 'bulan', 'name' => 'Bulan (*)', 'type' => 'text', 'width' => 15, 'rules' => ['required']],
                ['key' => 'saldo_awal', 'name' => 'Saldo Awal (Rp)', 'type' => 'number', 'width' => 18],
                ['key' => 'total_debet', 'name' => 'Total Debet (Rp)', 'type' => 'number', 'width' => 18],
                ['key' => 'total_kredit', 'name' => 'Total Kredit (Rp)', 'type' => 'number', 'width' => 18],
                ['key' => 'saldo_akhir', 'name' => 'Saldo Akhir (Rp)', 'type' => 'number', 'width' => 18],
             ]),
             'sample_data' => json_encode([
                ['kode_akun' => '1101', 'nama_akun' => '', 'bulan' => 'Januari 2026', 'saldo_awal' => 0, 'total_debet' => 60000000, 'total_kredit' => 8000000, 'saldo_akhir' => 0],
             ]), 'sort_order' => 2],

            ['name' => 'Laba Rugi', 'slug' => 'laba-rugi', 'description' => 'Laporan laba rugi SAK EMKM', 'type' => 'laba_rugi', 'frequency' => 'bulanan',
             'columns' => json_encode([
                ['key' => 'kode_akun', 'name' => 'Kode Akun (*)', 'type' => 'text', 'width' => 15, 'rules' => ['required']],
                ['key' => 'nama_akun', 'name' => 'Nama Akun', 'type' => 'text', 'width' => 30],
                ['key' => 'kategori', 'name' => 'Kategori', 'type' => 'text', 'width' => 15],
                ['key' => 'jumlah', 'name' => 'Jumlah (Rp) (*)', 'type' => 'number', 'width' => 20, 'rules' => ['required', 'numeric']],
             ]),
             'sample_data' => json_encode([
                ['kode_akun' => '4101', 'nama_akun' => '', 'kategori' => '', 'jumlah' => 100000000],
                ['kode_akun' => '4102', 'nama_akun' => '', 'kategori' => '', 'jumlah' => 25000000],
                ['kode_akun' => '5101', 'nama_akun' => '', 'kategori' => '', 'jumlah' => 15000000],
                ['kode_akun' => '5103', 'nama_akun' => '', 'kategori' => '', 'jumlah' => 2000000],
             ]), 'sort_order' => 3],

            ['name' => 'Neraca', 'slug' => 'neraca', 'description' => 'Laporan posisi keuangan', 'type' => 'neraca', 'frequency' => 'bulanan',
             'columns' => json_encode([
                ['key' => 'kode_akun', 'name' => 'Kode Akun (*)', 'type' => 'text', 'width' => 15, 'rules' => ['required']],
                ['key' => 'nama_akun', 'name' => 'Nama Akun', 'type' => 'text', 'width' => 30],
                ['key' => 'kategori', 'name' => 'Kategori', 'type' => 'text', 'width' => 20],
                ['key' => 'jumlah', 'name' => 'Jumlah (Rp) (*)', 'type' => 'number', 'width' => 20, 'rules' => ['required', 'numeric']],
             ]),
             'sample_data' => json_encode([
                ['kode_akun' => '1101', 'nama_akun' => '', 'kategori' => '', 'jumlah' => 45000000],
                ['kode_akun' => '1103', 'nama_akun' => '', 'kategori' => '', 'jumlah' => 5000000],
                ['kode_akun' => '1203', 'nama_akun' => '', 'kategori' => '', 'jumlah' => 25000000],
                ['kode_akun' => '2101', 'nama_akun' => '', 'kategori' => '', 'jumlah' => 8000000],
                ['kode_akun' => '3101', 'nama_akun' => '', 'kategori' => '', 'jumlah' => 50000000],
                ['kode_akun' => '3103', 'nama_akun' => '', 'kategori' => '', 'jumlah' => 17000000],
             ]), 'sort_order' => 4],

            ['name' => 'Arus Kas', 'slug' => 'arus-kas', 'description' => 'Laporan arus kas', 'type' => 'arus_kas', 'frequency' => 'bulanan',
             'columns' => json_encode([
                ['key' => 'keterangan', 'name' => 'Keterangan (*)', 'type' => 'text', 'width' => 40, 'rules' => ['required']],
                ['key' => 'kategori', 'name' => 'Kategori (*)', 'type' => 'text', 'width' => 20, 'rules' => ['required']],
                ['key' => 'jumlah', 'name' => 'Jumlah (Rp) (*)', 'type' => 'number', 'width' => 20, 'rules' => ['required', 'numeric']],
             ]),
             'sample_data' => json_encode([
                ['keterangan' => 'Penerimaan dari penjualan produk', 'kategori' => 'Operasi', 'jumlah' => 100000000],
                ['keterangan' => 'Pembayaran gaji karyawan', 'kategori' => 'Operasi', 'jumlah' => -15000000],
                ['keterangan' => 'Pembelian peralatan baru', 'kategori' => 'Investasi', 'jumlah' => -25000000],
             ]), 'sort_order' => 5],

            ['name' => 'Perubahan Modal', 'slug' => 'perubahan-modal', 'description' => 'Laporan perubahan modal', 'type' => 'modal', 'frequency' => 'tahunan',
             'columns' => json_encode([
                ['key' => 'keterangan', 'name' => 'Keterangan (*)', 'type' => 'text', 'width' => 40, 'rules' => ['required']],
                ['key' => 'jumlah', 'name' => 'Jumlah (Rp) (*)', 'type' => 'number', 'width' => 20, 'rules' => ['required', 'numeric']],
             ]),
             'sample_data' => json_encode([
                ['keterangan' => 'Saldo Awal Modal', 'jumlah' => 50000000],
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
                ['kode_akun' => '5101', 'uraian' => 'Beban gaji karyawan', 'anggaran' => 15000000, 'realisasi' => 15000000, 'persentase' => 0, 'selisih' => 0],
                ['kode_akun' => '5103', 'uraian' => 'Beban listrik', 'anggaran' => 2400000, 'realisasi' => 2000000, 'persentase' => 0, 'selisih' => 0],
             ]), 'sort_order' => 7],

            ['name' => 'CAT (Catatan Atas Laporan Keuangan)', 'slug' => 'cat-laporan', 'description' => 'Catatan atas laporan keuangan SAK EMKM', 'type' => 'cat', 'frequency' => 'tahunan',
             'columns' => json_encode([
                ['key' => 'no', 'name' => 'No. (*)', 'type' => 'text', 'width' => 8, 'rules' => ['required']],
                ['key' => 'uraian', 'name' => 'Uraian (*)', 'type' => 'text', 'width' => 50, 'rules' => ['required']],
                ['key' => 'nilai', 'name' => 'Nilai (Rp)', 'type' => 'number', 'width' => 20],
                ['key' => 'keterangan', 'name' => 'Keterangan', 'type' => 'text', 'width' => 35],
             ]),
             'sample_data' => json_encode([
                ['no' => '1', 'uraian' => 'Bentuk Usaha', 'nilai' => '', 'keterangan' => 'Badan Usaha Milik Desa (BUMDes)'],
                ['no' => '2', 'uraian' => 'Kebijakan Akuntansi', 'nilai' => '', 'keterangan' => 'K basis akrual, SAK EMKM'],
                ['no' => '3', 'uraian' => 'Aset Tetap', 'nilai' => '', 'keterangan' => 'Depresiasi garis lurus, umur ekonomis 5 tahun'],
                ['no' => '4', 'uraian' => 'Modal Disetor', 'nilai' => 50000000, 'keterangan' => 'Dari Pemerintah Desa'],
             ]), 'sort_order' => 8],
        ];
        foreach ($templates as $data) {
            FinancialTemplate::updateOrCreate(['slug' => $data['slug']], $data);
        }
    }
}
