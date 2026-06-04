<?php

namespace App\Services;

use App\Models\ChartOfAccount;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Service untuk auto-generate laporan keuangan dari data Jurnal Umum.
 * Semua laporan (Buku Besar, Laba Rugi, Neraca, Arus Kas, Perubahan Modal)
 * dihitung otomatis dari transaksi di tabel financial_transactions.
 */
class FinancialAutoCalculateService
{
    /**
     * Hitung Buku Besar per akun per bulan
     */
    public function getBukuBesar(string $bulan): array
    {
        $start = Carbon::parse($bulan . '-01')->startOfMonth();
        $end = $start->copy()->endOfMonth();

        // Saldo awal = total semua transaksi sebelum bulan ini
        $saldoAwalQuery = DB::table('financial_transactions')
            ->join('chart_of_accounts', 'financial_transactions.account_id', '=', 'chart_of_accounts.id')
            ->where('financial_transactions.transaction_date', '<', $start->toDateString())
            ->whereIn('financial_transactions.status', ['published', 'approved'])
            ->where('chart_of_accounts.is_group', false)
            ->select(
                'chart_of_accounts.code as kode',
                'chart_of_accounts.name as nama',
                DB::raw("SUM(CASE WHEN financial_transactions.type = 'debit' THEN financial_transactions.amount ELSE 0 END) as total_debet_awal"),
                DB::raw("SUM(CASE WHEN financial_transactions.type = 'credit' THEN financial_transactions.amount ELSE 0 END) as total_kredit_awal")
            )
            ->groupBy('chart_of_accounts.code', 'chart_of_accounts.name')
            ->get();

        // Transaksi bulan ini
        $transaksiQuery = DB::table('financial_transactions')
            ->join('chart_of_accounts', 'financial_transactions.account_id', '=', 'chart_of_accounts.id')
            ->whereBetween('financial_transactions.transaction_date', [$start->toDateString(), $end->toDateString()])
            ->whereIn('financial_transactions.status', ['published', 'approved'])
            ->where('chart_of_accounts.is_group', false)
            ->select(
                'chart_of_accounts.code as kode',
                'chart_of_accounts.name as nama',
                DB::raw("SUM(CASE WHEN financial_transactions.type = 'debit' THEN financial_transactions.amount ELSE 0 END) as total_debet"),
                DB::raw("SUM(CASE WHEN financial_transactions.type = 'credit' THEN financial_transactions.amount ELSE 0 END) as total_kredit")
            )
            ->groupBy('chart_of_accounts.code', 'chart_of_accounts.name')
            ->get();

        // Gabung
        $all = [];
        foreach ($saldoAwalQuery as $row) {
            $all[$row->kode] = [
                'kode' => $row->kode,
                'nama' => $row->nama,
                'saldo_awal' => $row->total_debet_awal - $row->total_kredit_awal,
                'total_debet' => 0,
                'total_kredit' => 0,
                'saldo_akhir' => 0,
            ];
        }
        foreach ($transaksiQuery as $row) {
            if (!isset($all[$row->kode])) {
                $all[$row->kode] = [
                    'kode' => $row->kode,
                    'nama' => $row->nama,
                    'saldo_awal' => 0,
                    'total_debet' => 0,
                    'total_kredit' => 0,
                    'saldo_akhir' => 0,
                ];
            }
            $all[$row->kode]['total_debet'] = $row->total_debet;
            $all[$row->kode]['total_kredit'] = $row->total_kredit;
        }

        // Hitung saldo akhir
        foreach ($all as &$item) {
            $item['saldo_akhir'] = $item['saldo_awal'] + $item['total_debet'] - $item['total_kredit'];
        }

        // Sort by code
        uasort($all, fn($a, $b) => strcmp($a['kode'], $b['kode']));

        return [
            'bulan' => $start->format('F Y'),
            'data' => array_values($all),
            'total_debet' => array_sum(array_column($all, 'total_debet')),
            'total_kredit' => array_sum(array_column($all, 'total_kredit')),
        ];
    }

    /**
     * Hitung Laba Rugi (SAK EMKM)
     */
    public function getLabaRugi(string $bulan): array
    {
        $start = Carbon::parse($bulan . '-01')->startOfMonth();
        $end = $start->copy()->endOfMonth();

        // Ambil semua akun yang transaksi di bulan ini
        $data = DB::table('financial_transactions')
            ->join('chart_of_accounts', 'financial_transactions.account_id', '=', 'chart_of_accounts.id')
            ->whereBetween('financial_transactions.transaction_date', [$start->toDateString(), $end->toDateString()])
            ->whereIn('financial_transactions.status', ['published', 'approved'])
            ->where('chart_of_accounts.is_group', false)
            ->whereIn('chart_of_accounts.type', ['pendapatan', 'beban'])
            ->select(
                'chart_of_accounts.code as kode',
                'chart_of_accounts.name as nama',
                'chart_of_accounts.type as tipe',
                DB::raw("SUM(CASE WHEN financial_transactions.type = 'debit' THEN financial_transactions.amount ELSE 0 END) as total_debet"),
                DB::raw("SUM(CASE WHEN financial_transactions.type = 'credit' THEN financial_transactions.amount ELSE 0 END) as total_kredit")
            )
            ->groupBy('chart_of_accounts.code', 'chart_of_accounts.name', 'chart_of_accounts.type')
            ->get();

        $pendapatan = [];
        $beban = [];
        $totalPendapatan = 0;
        $totalBeban = 0;

        foreach ($data as $row) {
            // Pendapatan: credit > debit (normal balance credit)
            $jumlah = $row->total_kredit - $row->total_debet;
            $item = [
                'kode' => $row->kode,
                'nama' => $row->nama,
                'jumlah' => $jumlah,
            ];

            if ($row->tipe === 'pendapatan') {
                $pendapatan[] = $item;
                $totalPendapatan += $jumlah;
            } else {
                // Beban: debit > credit (normal balance debit)
                $jumlah = $row->total_debet - $row->total_kredit;
                $item['jumlah'] = $jumlah;
                $beban[] = $item;
                $totalBeban += $jumlah;
            }
        }

        return [
            'bulan' => $start->format('F Y'),
            'pendapatan' => $pendapatan,
            'beban' => $beban,
            'total_pendapatan' => $totalPendapatan,
            'total_beban' => $totalBeban,
            'laba_bersih' => $totalPendapatan - $totalBeban,
        ];
    }

    /**
     * Hitung Neraca (Posisi Keuangan)
     */
    public function getNeraca(string $bulan): array
    {
        $end = Carbon::parse($bulan . '-01')->endOfMonth();

        // Ambil saldo semua akun sampai akhir bulan
        $data = DB::table('financial_transactions')
            ->join('chart_of_accounts', 'financial_transactions.account_id', '=', 'chart_of_accounts.id')
            ->where('financial_transactions.transaction_date', '<=', $end->toDateString())
            ->whereIn('financial_transactions.status', ['published', 'approved'])
            ->where('chart_of_accounts.is_group', false)
            ->select(
                'chart_of_accounts.code as kode',
                'chart_of_accounts.name as nama',
                'chart_of_accounts.type as tipe',
                DB::raw("SUM(CASE WHEN financial_transactions.type = 'debit' THEN financial_transactions.amount ELSE 0 END) as total_debet"),
                DB::raw("SUM(CASE WHEN financial_transactions.type = 'credit' THEN financial_transactions.amount ELSE 0 END) as total_kredit")
            )
            ->groupBy('chart_of_accounts.code', 'chart_of_accounts.name', 'chart_of_accounts.type')
            ->get();

        $aset = [];
        $kewajiban = [];
        $ekuitas = [];
        $totalAset = 0;
        $totalKewajiban = 0;
        $totalEkuitas = 0;

        foreach ($data as $row) {
            // Normal balance: aset = debit, kewajiban/ekuitas = credit
            $saldo = in_array($row->tipe, ['kewajiban', 'ekuitas'])
                ? $row->total_kredit - $row->total_debet
                : $row->total_debet - $row->total_kredit;

            $kategori = match($row->tipe) {
                'aset' => ($row->kode[0] == '1' && $row->kode[1] == '1') ? 'Aset Lancar' : 'Aset Tetap',
                'kewajiban' => 'Kewajiban',
                'ekuitas' => 'Ekuitas',
                default => 'Lainnya',
            };

            $item = [
                'kode' => $row->kode,
                'nama' => $row->nama,
                'kategori' => $kategori,
                'jumlah' => $saldo,
            ];

            if ($row->tipe === 'aset') {
                $aset[] = $item;
                $totalAset += $saldo;
            } elseif ($row->tipe === 'kewajiban') {
                $kewajiban[] = $item;
                $totalKewajiban += $saldo;
            } elseif ($row->tipe === 'ekuitas') {
                $ekuitas[] = $item;
                $totalEkuitas += $saldo;
            }
        }

        return [
            'bulan' => $end->format('F Y'),
            'aset' => $aset,
            'kewajiban' => $kewajiban,
            'ekuitas' => $ekuitas,
            'total_aset' => $totalAset,
            'total_kewajiban' => $totalKewajiban,
            'total_ekuitas' => $totalEkuitas,
            'selisih' => $totalAset - ($totalKewajiban + $totalEkuitas),
        ];
    }

    /**
     * Hitung Arus Kas
     */
    public function getArusKas(string $bulan): array
    {
        $start = Carbon::parse($bulan . '-01')->startOfMonth();
        $end = $start->copy()->endOfMonth();

        // Kelompokkan berdasarkan jenis akun
        $data = DB::table('financial_transactions')
            ->join('chart_of_accounts', 'financial_transactions.account_id', '=', 'chart_of_accounts.id')
            ->whereBetween('financial_transactions.transaction_date', [$start->toDateString(), $end->toDateString()])
            ->whereIn('financial_transactions.status', ['published', 'approved'])
            ->where('chart_of_accounts.code', 'like', '11%') // Hanya akun kas
            ->select(
                'chart_of_accounts.code as kode',
                'chart_of_accounts.name as nama',
                DB::raw("SUM(CASE WHEN financial_transactions.type = 'debit' THEN financial_transactions.amount ELSE 0 END) as penerimaan"),
                DB::raw("SUM(CASE WHEN financial_transactions.type = 'credit' THEN financial_transactions.amount ELSE 0 END) as pengeluaran")
            )
            ->groupBy('chart_of_accounts.code', 'chart_of_accounts.name')
            ->get();

        $operasi = [];
        $investasi = [];
        $pendanaan = [];
        $totalOperasi = 0;
        $totalInvestasi = 0;
        $totalPendanaan = 0;

        foreach ($data as $row) {
            $bersih = $row->penerimaan - $row->pengeluaran;

            // Klasifikasi berdasarkan kode akun
            $kategori = match(true) {
                substr($row->kode, 0, 2) === '11' => 'Operasi',
                substr($row->kode, 0, 2) === '12' => 'Investasi',
                default => 'Operasi',
            };

            $item = [
                'keterangan' => $row->nama,
                'kategori' => $kategori,
                'jumlah' => $bersih,
            ];

            if ($kategori === 'Operasi') {
                $operasi[] = $item;
                $totalOperasi += $bersih;
            } else {
                $investasi[] = $item;
                $totalInvestasi += $bersih;
            }
        }

        return [
            'bulan' => $start->format('F Y'),
            'operasi' => $operasi,
            'investasi' => $investasi,
            'pendanaan' => $pendanaan,
            'total_operasi' => $totalOperasi,
            'total_investasi' => $totalInvestasi,
            'total_pendanaan' => $totalPendanaan,
            'kas_bersih' => $totalOperasi + $totalInvestasi + $totalPendanaan,
        ];
    }

    /**
     * Hitung Perubahan Modal
     */
    public function getPerubahanModal(string $bulan): array
    {
        $start = Carbon::parse($bulan . '-01')->startOfMonth();
        $end = $start->copy()->endOfMonth();

        // Ambil akun modal (3xxx)
        $data = DB::table('financial_transactions')
            ->join('chart_of_accounts', 'financial_transactions.account_id', '=', 'chart_of_accounts.id')
            ->whereBetween('financial_transactions.transaction_date', [$start->toDateString(), $end->toDateString()])
            ->whereIn('financial_transactions.status', ['published', 'approved'])
            ->where('chart_of_accounts.type', 'ekuitas')
            ->select(
                'chart_of_accounts.code as kode',
                'chart_of_accounts.name as nama',
                DB::raw("SUM(CASE WHEN financial_transactions.type = 'credit' THEN financial_transactions.amount ELSE 0 END) - SUM(CASE WHEN financial_transactions.type = 'debit' THEN financial_transactions.amount ELSE 0 END) as jumlah")
            )
            ->groupBy('chart_of_accounts.code', 'chart_of_accounts.name')
            ->get();

        $modal = [];
        $totalModal = 0;

        foreach ($data as $row) {
            $modal[] = [
                'keterangan' => $row->nama . ' (' . $row->kode . ')',
                'jumlah' => $row->jumlah,
            ];
            $totalModal += $row->jumlah;
        }

        return [
            'bulan' => $start->format('F Y'),
            'modal' => $modal,
            'total_modal' => $totalModal,
        ];
    }

    /**
     * Hitung Realisasi Anggaran
     */
    public function getRealisasiAnggaran(string $bulan): array
    {
        $start = Carbon::parse($bulan . '-01')->startOfMonth();
        $end = $start->copy()->endOfMonth();

        // Ambil data anggaran dari upload
        $uploads = DB::table('financial_uploads')
            ->join('financial_templates', 'financial_uploads.template_id', '=', 'financial_templates.id')
            ->where('financial_templates.slug', 'realisasi-anggaran')
            ->where('financial_uploads.period', $start->format('Y-m'))
            ->where('financial_uploads.import_status', 'imported')
            ->select('financial_uploads.imported_data')
            ->get();

        if ($uploads->isEmpty()) {
            return [
                'bulan' => $start->format('F Y'),
                'data' => [],
                'total_anggaran' => 0,
                'total_realisasi' => 0,
                'persentase' => 0,
            ];
        }

        $importedData = json_decode($uploads->first()->imported_data, true);
        $details = $importedData['details'] ?? [];

        return [
            'bulan' => $start->format('F Y'),
            'data' => $details,
            'total_anggaran' => $importedData['total_anggaran'] ?? 0,
            'total_realisasi' => $importedData['total_realisasi'] ?? 0,
            'persentase' => $importedData['persentase_total'] ?? 0,
        ];
    }

    /**
     * Hitung CAT (Catatan Atas Laporan Keuangan)
     */
    public function getCAT(string $bulan): array
    {
        $start = Carbon::parse($bulan . '-01')->startOfMonth();

        $uploads = DB::table('financial_uploads')
            ->join('financial_templates', 'financial_uploads.template_id', '=', 'financial_templates.id')
            ->where('financial_templates.slug', 'cat-laporan')
            ->where('financial_uploads.period', $start->format('Y-m'))
            ->where('financial_uploads.import_status', 'imported')
            ->select('financial_uploads.imported_data')
            ->get();

        if ($uploads->isEmpty()) {
            return ['bulan' => $start->format('F Y'), 'data' => []];
        }

        $importedData = json_decode($uploads->first()->imported_data, true);

        return [
            'bulan' => $start->format('F Y'),
            'data' => $importedData['details'] ?? [],
        ];
    }

    /**
     * Ringkasan semua laporan untuk dashboard
     */
    public function getDashboard(string $bulan): array
    {
        return [
            'buku_besar' => $this->getBukuBesar($bulan),
            'laba_rugi' => $this->getLabaRugi($bulan),
            'neraca' => $this->getNeraca($bulan),
            'arus_kas' => $this->getArusKas($bulan),
            'perubahan_modal' => $this->getPerubahanModal($bulan),
            'realisasi_anggaran' => $this->getRealisasiAnggaran($bulan),
            'cat' => $this->getCAT($bulan),
        ];
    }
}
