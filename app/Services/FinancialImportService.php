<?php

namespace App\Services;

use App\Models\ChartOfAccount;
use App\Models\FinancialTransaction;
use App\Models\FinancialUpload;
use App\Models\FinancialTemplate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\IOFactory;

class FinancialImportService
{
    /**
     * Import data dari uploaded Excel ke database
     */
    public function import(FinancialUpload $upload): array
    {
        $template = $upload->template;
        $filePath = storage_path('app/' . $upload->file_path);

        if (!file_exists($filePath)) {
            return ['success' => false, 'error' => 'File tidak ditemukan: ' . $upload->file_path];
        }

        try {
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getSheet(0);
            $columns = is_string($template->columns) ? json_decode($template->columns, true) : ($template->columns ?? []);

            // Baca semua data dari Excel (mulai baris 5 = setelah kop + header)
            $data = [];
            $highestRow = $sheet->getHighestRow();
            for ($row = 5; $row <= $highestRow; $row++) {
                $rowData = [];
                $isEmpty = true;
                foreach ($columns as $col => $header) {
                    $cell = $this->getColumnLetter($col + 1) . $row;
                    $value = $sheet->getCell($cell)->getCalculatedValue();
                    $key = $header['key'] ?? $col;
                    $rowData[$key] = $value;
                    if (!empty($value) && $value !== null) $isEmpty = false;
                }
                if (!$isEmpty) {
                    $data[] = $rowData;
                }
            }

            if (empty($data)) {
                return ['success' => false, 'error' => 'Tidak ada data ditemukan dalam file'];
            }

            // Import berdasarkan tipe template
            $result = match($template->type) {
                'jurnal' => $this->importJurnal($data, $upload),
                'buku_besar' => $this->importBukuBesar($data, $upload),
                'laba_rugi' => $this->importLabaRugi($data, $upload),
                'neraca' => $this->importNeraca($data, $upload),
                'arus_kas' => $this->importArusKas($data, $upload),
                'modal' => $this->importModal($data, $upload),
                'realisasi_anggaran' => $this->importRealisasi($data, $upload),
                'cat' => $this->importCAT($data, $upload),
                default => ['success' => false, 'error' => 'Tipe template tidak dikenali: ' . $template->type],
            };

            return $result;

        } catch (\Exception $e) {
            return ['success' => false, 'error' => 'Gagal membaca file: ' . $e->getMessage()];
        }
    }

    /**
     * Import Jurnal Umum → financial_transactions
     */
    protected function importJurnal(array $data, FinancialUpload $upload): array
    {
        $imported = 0;
        $errors = [];
        $coaMap = $this->getCOAMap();

        DB::beginTransaction();
        try {
            foreach ($data as $idx => $row) {
                $kodeAkun = trim($row['kode_akun'] ?? '');
                $keterangan = trim($row['keterangan'] ?? '');
                $debet = (float)($row['debet'] ?? 0);
                $kredit = (float)($row['kredit'] ?? 0);
                $tanggal = $this->parseDate($row['tanggal'] ?? '');
                $noRef = trim($row['no_ref'] ?? '');

                // Validasi
                if (empty($kodeAkun)) {
                    $errors[] = "Baris " . ($idx + 5) . ": Kode Akun kosong";
                    continue;
                }
                if (!isset($coaMap[$kodeAkun])) {
                    $errors[] = "Baris " . ($idx + 5) . ": Kode Akun {$kodeAkun} tidak ditemukan di COA";
                    continue;
                }
                if ($debet == 0 && $kredit == 0) {
                    $errors[] = "Baris " . ($idx + 5) . ": Debet dan Kredit keduanya 0";
                    continue;
                }
                if (empty($tanggal)) {
                    $errors[] = "Baris " . ($idx + 5) . ": Tanggal tidak valid";
                    continue;
                }

                // Cari atau buat transaction number
                $txNumber = $noRef ?: ('IMP-' . $upload->id . '-' . str_pad($imported + 1, 4, '0', STR_PAD_LEFT));

                // Insert transaction
                FinancialTransaction::create([
                    'transaction_number' => $txNumber,
                    'transaction_date' => $tanggal,
                    'type' => $kredit > 0 ? 'credit' : 'debit',
                    'account_id' => $coaMap[$kodeAkun],
                    'amount' => max($debet, $kredit),
                    'description' => $keterangan,
                    'reference' => $noRef,
                    'created_by' => Auth::id() ?? 1,
                    'status' => 'approved',
                    'notes' => "Imported from upload #{$upload->id}",
                ]);

                // Jika ada debet DAN kredit di baris yang sama (double entry)
                if ($debet > 0 && $kredit > 0) {
                    // Cari akun lawan (biasanya kas untuk debet, pendapatan untuk kredit)
                    // Untuk jurnal umum sederhana, kita skip baris ini
                    // Karena sudah ada di baris terpisah
                }

                $imported++;
            }

            DB::commit();

            // Update upload record
            $upload->update([
                'imported_data' => $data,
                'imported_count' => $imported,
                'import_status' => $imported > 0 ? 'imported' : 'error',
                'import_error' => !empty($errors) ? implode("\n", $errors) : null,
            ]);

            return [
                'success' => $imported > 0,
                'imported' => $imported,
                'errors' => $errors,
                'message' => "{$imported} transaksi berhasil diimport" . (!empty($errors) ? ". " . count($errors) . " error" : ''),
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            $upload->update([
                'import_status' => 'error',
                'import_error' => $e->getMessage(),
            ]);
            return ['success' => false, 'error' => 'Gagal import: ' . $e->getMessage()];
        }
    }

    /**
     * Import Buku Besar → financial_transactions (summary)
     */
    protected function importBukuBesar(array $data, FinancialUpload $upload): array
    {
        $imported = 0;
        $errors = [];
        $coaMap = $this->getCOAMap();

        DB::beginTransaction();
        try {
            foreach ($data as $idx => $row) {
                $kodeAkun = trim($row['kode_akun'] ?? '');
                $bulan = trim($row['bulan'] ?? '');
                $saldoAwal = (float)($row['saldo_awal'] ?? 0);
                $totalDebet = (float)($row['total_debet'] ?? 0);
                $totalKredit = (float)($row['total_kredit'] ?? 0);

                if (empty($kodeAkun) || !isset($coaMap[$kodeAkun])) {
                    $errors[] = "Baris " . ($idx + 5) . ": Kode Akun {$kodeAkun} tidak valid";
                    continue;
                }

                // Create summary transaction
                $txNumber = 'BB-' . $upload->id . '-' . str_pad($imported + 1, 4, '0', STR_PAD_LEFT);
                $tanggal = $this->parseBulan($bulan);

                if ($totalDebet > 0) {
                    FinancialTransaction::create([
                        'transaction_number' => $txNumber . '-D',
                        'transaction_date' => $tanggal,
                        'type' => 'debit',
                        'account_id' => $coaMap[$kodeAkun],
                        'amount' => $totalDebet,
                        'description' => "Buku Besar {$bulan} - Total Debet",
                        'created_by' => Auth::id() ?? 1,
                        'status' => 'approved',
                        'notes' => "Imported from upload #{$upload->id}",
                    ]);
                }

                if ($totalKredit > 0) {
                    FinancialTransaction::create([
                        'transaction_number' => $txNumber . '-K',
                        'transaction_date' => $tanggal,
                        'type' => 'credit',
                        'account_id' => $coaMap[$kodeAkun],
                        'amount' => $totalKredit,
                        'description' => "Buku Besar {$bulan} - Total Kredit",
                        'created_by' => Auth::id() ?? 1,
                        'status' => 'approved',
                        'notes' => "Imported from upload #{$upload->id}",
                    ]);
                }

                $imported++;
            }

            DB::commit();
            $upload->update([
                'imported_data' => $data,
                'imported_count' => $imported,
                'import_status' => $imported > 0 ? 'imported' : 'error',
                'import_error' => !empty($errors) ? implode("\n", $errors) : null,
            ]);

            return [
                'success' => $imported > 0,
                'imported' => $imported,
                'errors' => $errors,
                'message' => "{$imported} akun buku besar berhasil diimport" . (!empty($errors) ? ". " . count($errors) . " error" : ''),
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            $upload->update(['import_status' => 'error', 'import_error' => $e->getMessage()]);
            return ['success' => false, 'error' => 'Gagal import: ' . $e->getMessage()];
        }
    }

    /**
     * Import Laba Rugi → simpan sebagai JSON (summary report)
     */
    protected function importLabaRugi(array $data, FinancialUpload $upload): array
    {
        $imported = 0;
        $totalPendapatan = 0;
        $totalBeban = 0;
        $details = [];

        foreach ($data as $idx => $row) {
            $kodeAkun = trim($row['kode_akun'] ?? '');
            $kategori = trim($row['kategori'] ?? '');
            $jumlah = (float)($row['jumlah'] ?? 0);

            if (empty($kodeAkun)) continue;

            $details[] = [
                'kode_akun' => $kodeAkun,
                'kategori' => $kategori,
                'jumlah' => $jumlah,
            ];

            if ($kategori === 'Pendapatan') $totalPendapatan += $jumlah;
            elseif ($kategori === 'Beban') $totalBeban += $jumlah;

            $imported++;
        }

        $labaBersih = $totalPendapatan - $totalBeban;

        $upload->update([
            'imported_data' => [
                'type' => 'laba_rugi',
                'total_pendapatan' => $totalPendapatan,
                'total_beban' => $totalBeban,
                'laba_bersih' => $labaBersih,
                'details' => $details,
            ],
            'imported_count' => $imported,
            'import_status' => 'imported',
        ]);

        return [
            'success' => true,
            'imported' => $imported,
            'message' => "Laba Rugi diimport: Pendapatan Rp " . number_format($totalPendapatan) . ", Beban Rp " . number_format($totalBeban) . ", Laba Bersih Rp " . number_format($labaBersih),
        ];
    }

    /**
     * Import Neraca → simpan sebagai JSON
     */
    protected function importNeraca(array $data, FinancialUpload $upload): array
    {
        $imported = 0;
        $totalAset = 0;
        $totalKewajiban = 0;
        $totalEkuitas = 0;
        $details = [];

        foreach ($data as $idx => $row) {
            $kodeAkun = trim($row['kode_akun'] ?? '');
            $kategori = trim($row['kategori'] ?? '');
            $jumlah = (float)($row['jumlah'] ?? 0);

            if (empty($kodeAkun)) continue;

            $details[] = [
                'kode_akun' => $kodeAkun,
                'kategori' => $kategori,
                'jumlah' => $jumlah,
            ];

            if (str_starts_with($kategori, 'Aset')) $totalAset += $jumlah;
            elseif ($kategori === 'Kewajiban') $totalKewajiban += $jumlah;
            elseif ($kategori === 'Ekuitas') $totalEkuitas += $jumlah;

            $imported++;
        }

        $selisih = $totalAset - ($totalKewajiban + $totalEkuitas);

        $upload->update([
            'imported_data' => [
                'type' => 'neraca',
                'total_aset' => $totalAset,
                'total_kewajiban' => $totalKewajiban,
                'total_ekuitas' => $totalEkuitas,
                'selisih' => $selisih,
                'details' => $details,
            ],
            'imported_count' => $imported,
            'import_status' => 'imported',
        ]);

        return [
            'success' => true,
            'imported' => $imported,
            'message' => "Neraca diimport: Aset Rp " . number_format($totalAset) . ", Kewajiban+Ekuitas Rp " . number_format($totalKewajiban + $totalEkuitas) . ", Selisih Rp " . number_format($selisih),
        ];
    }

    /**
     * Import Arus Kas → simpan sebagai JSON
     */
    protected function importArusKas(array $data, FinancialUpload $upload): array
    {
        $imported = 0;
        $kasOperasi = 0;
        $kasInvestasi = 0;
        $kasPendanaan = 0;
        $details = [];

        foreach ($data as $idx => $row) {
            $keterangan = trim($row['keterangan'] ?? '');
            $kategori = trim($row['kategori'] ?? '');
            $jumlah = (float)($row['jumlah'] ?? 0);

            if (empty($keterangan)) continue;

            $details[] = [
                'keterangan' => $keterangan,
                'kategori' => $kategori,
                'jumlah' => $jumlah,
            ];

            if ($kategori === 'Operasi') $kasOperasi += $jumlah;
            elseif ($kategori === 'Investasi') $kasInvestasi += $jumlah;
            elseif ($kategori === 'Pendanaan') $kasPendanaan += $jumlah;

            $imported++;
        }

        $kasBersih = $kasOperasi + $kasInvestasi + $kasPendanaan;

        $upload->update([
            'imported_data' => [
                'type' => 'arus_kas',
                'kas_operasi' => $kasOperasi,
                'kas_investasi' => $kasInvestasi,
                'kas_pendanaan' => $kasPendanaan,
                'kas_bersih' => $kasBersih,
                'details' => $details,
            ],
            'imported_count' => $imported,
            'import_status' => 'imported',
        ]);

        return [
            'success' => true,
            'imported' => $imported,
            'message' => "Arus Kas diimport: Operasi Rp " . number_format($kasOperasi) . ", Investasi Rp " . number_format($kasInvestasi) . ", Pendanaan Rp " . number_format($kasPendanaan),
        ];
    }

    /**
     * Import Perubahan Modal → simpan sebagai JSON
     */
    protected function importModal(array $data, FinancialUpload $upload): array
    {
        $imported = 0;
        $totalModal = 0;
        $details = [];

        foreach ($data as $idx => $row) {
            $keterangan = trim($row['keterangan'] ?? '');
            $jumlah = (float)($row['jumlah'] ?? 0);

            if (empty($keterangan)) continue;

            $details[] = [
                'keterangan' => $keterangan,
                'jumlah' => $jumlah,
            ];
            $totalModal += $jumlah;
            $imported++;
        }

        $upload->update([
            'imported_data' => [
                'type' => 'modal',
                'total_modal' => $totalModal,
                'details' => $details,
            ],
            'imported_count' => $imported,
            'import_status' => 'imported',
        ]);

        return [
            'success' => true,
            'imported' => $imported,
            'message' => "Perubahan Modal diimport: Total Rp " . number_format($totalModal),
        ];
    }

    /**
     * Import Realisasi Anggaran → simpan sebagai JSON
     */
    protected function importRealisasi(array $data, FinancialUpload $upload): array
    {
        $imported = 0;
        $totalAnggaran = 0;
        $totalRealisasi = 0;
        $details = [];

        foreach ($data as $idx => $row) {
            $kodeAkun = trim($row['kode_akun'] ?? '');
            $uraian = trim($row['uraian'] ?? '');
            $anggaran = (float)($row['anggaran'] ?? 0);
            $realisasi = (float)($row['realisasi'] ?? 0);

            if (empty($uraian)) continue;

            $details[] = [
                'kode_akun' => $kodeAkun,
                'uraian' => $uraian,
                'anggaran' => $anggaran,
                'realisasi' => $realisasi,
                'persentase' => $anggaran > 0 ? round(($realisasi / $anggaran) * 100, 2) : 0,
                'selisih' => $anggaran - $realisasi,
            ];

            $totalAnggaran += $anggaran;
            $totalRealisasi += $realisasi;
            $imported++;
        }

        $upload->update([
            'imported_data' => [
                'type' => 'realisasi_anggaran',
                'total_anggaran' => $totalAnggaran,
                'total_realisasi' => $totalRealisasi,
                'persentase_total' => $totalAnggaran > 0 ? round(($totalRealisasi / $totalAnggaran) * 100, 2) : 0,
                'details' => $details,
            ],
            'imported_count' => $imported,
            'import_status' => 'imported',
        ]);

        return [
            'success' => true,
            'imported' => $imported,
            'message' => "Realisasi Anggaran diimport: Anggaran Rp " . number_format($totalAnggaran) . ", Realisasi Rp " . number_format($totalRealisasi),
        ];
    }

    /**
     * Import CAT → simpan sebagai JSON
     */
    protected function importCAT(array $data, FinancialUpload $upload): array
    {
        $imported = 0;
        $details = [];

        foreach ($data as $idx => $row) {
            $no = trim($row['no'] ?? '');
            $uraian = trim($row['uraian'] ?? '');
            $nilai = $row['nilai'] ?? '';
            $keterangan = trim($row['keterangan'] ?? '');

            if (empty($uraian)) continue;

            $details[] = [
                'no' => $no,
                'uraian' => $uraian,
                'nilai' => is_numeric($nilai) ? (float)$nilai : $nilai,
                'keterangan' => $keterangan,
            ];
            $imported++;
        }

        $upload->update([
            'imported_data' => [
                'type' => 'cat',
                'details' => $details,
            ],
            'imported_count' => $imported,
            'import_status' => 'imported',
        ]);

        return [
            'success' => true,
            'imported' => $imported,
            'message' => "CAT diimport: {$imported} catatan",
        ];
    }

    /**
     * Get COA map: code => id
     */
    protected function getCOAMap(): array
    {
        return DB::table('chart_of_accounts')
            ->where('is_group', false)
            ->pluck('id', 'code')
            ->toArray();
    }

    /**
     * Parse tanggal dari berbagai format
     */
    protected function parseDate($value): ?string
    {
        if (empty($value)) return null;

        // Jika sudah format YYYY-MM-DD
        if (preg_match('/^\d{4}-\d{2}-\d{2}/', $value)) {
            return substr($value, 0, 10);
        }

        // Format DD/MM/YYYY
        if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $value, $m)) {
            return "{$m[3]}-{$m[2]}-{$m[1]}";
        }

        // Format DD-MM-YYYY
        if (preg_match('/^(\d{1,2})-(\d{1,2})-(\d{4})$/', $value, $m)) {
            return "{$m[3]}-{$m[2]}-{$m[1]}";
        }

        // Coba PHP parse
        $ts = strtotime($value);
        return $ts ? date('Y-m-d', $ts) : null;
    }

    /**
     * Parse bulan "Januari 2026" → "2026-01-01"
     */
    protected function parseBulan(string $bulan): string
    {
        $months = [
            'januari' => '01', 'februari' => '02', 'maret' => '03', 'april' => '04',
            'mei' => '05', 'juni' => '06', 'juli' => '07', 'agustus' => '08',
            'september' => '09', 'oktober' => '10', 'november' => '11', 'desember' => '12',
        ];

        $bulan = strtolower(trim($bulan));
        foreach ($months as $name => $num) {
            if (str_contains($bulan, $name)) {
                $year = preg_replace('/[^0-9]/', '', $bulan);
                return ($year ?: date('Y')) . "-{$num}-01";
            }
        }

        return date('Y-m-d');
    }

    protected function getColumnLetter(int $column): string
    {
        $letter = '';
        while ($column > 0) { $column--; $letter = chr(65 + ($column % 26)) . $letter; $column = intdiv($column, 26); }
        return $letter;
    }
}
