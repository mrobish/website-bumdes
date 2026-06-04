<?php

namespace App\Services;

use App\Models\Account;
use App\Models\FiscalYear;
use App\Models\JournalEntry;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfReportService
{
    protected $setting;
    protected $primaryColor;
    protected $accentColor;

    public function __construct()
    {
        $this->setting = \App\Models\BumdesSetting::first();
        $this->primaryColor = $this->setting->primary_color ?? '#1E3A5F';
        $this->accentColor = $this->setting->secondary_color ?? '#D4A843';
    }

    private function getAccountBalance($account, $fy)
    {
        $debit = JournalEntry::where('account_code', $account->code)
            ->whereHas('transaction', function ($q) use ($fy) {
                $q->where('fiscal_year', $fy->year)->where('is_void', 0);
            })->sum('debit');
        
        $credit = JournalEntry::where('account_code', $account->code)
            ->whereHas('transaction', function ($q) use ($fy) {
                $q->where('fiscal_year', $fy->year)->where('is_void', 0);
            })->sum('credit');
        
        return ['debit' => $debit, 'credit' => $credit, 'balance' => $debit - $credit];
    }

    /**
     * Generate Neraca Saldo PDF
     */
    public function neracaSaldo($fiscalYearId)
    {
        $fy = FiscalYear::findOrFail($fiscalYearId);
        $accounts = Account::active()->orderBy('code')->get();
        
        $data = [];
        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($accounts as $acc) {
            $bal = $this->getAccountBalance($acc, $fy);
            if ($bal['debit'] != 0 || $bal['credit'] != 0) {
                $data[] = ['code' => $acc->code, 'name' => $acc->name, 'debit' => $bal['debit'], 'credit' => $bal['credit']];
                $totalDebit += $bal['debit'];
                $totalCredit += $bal['credit'];
            }
        }

        return Pdf::loadView('pdf.neraca-saldo', [
            'data' => $data,
            'totalDebit' => $totalDebit,
            'totalCredit' => $totalCredit,
            'period' => 'Tahun ' . $fy->year,
            'setting' => $this->setting,
            'primaryColor' => $this->primaryColor,
            'accentColor' => $this->accentColor,
        ])->stream('neraca-saldo-' . $fy->year . '.pdf');
    }

    /**
     * Generate Laba Rugi PDF
     */
    public function labaRugi($fiscalYearId)
    {
        $fy = FiscalYear::findOrFail($fiscalYearId);
        $pendapatan = Account::where('code', 'like', '4%')->active()->orderBy('code')->get();
        $beban = Account::where(function ($q) {
            $q->where('code', 'like', '5%')->orWhere('code', 'like', '6%');
        })->active()->orderBy('code')->get();

        $pendapatanData = [];
        $totalPendapatan = 0;
        foreach ($pendapatan as $acc) {
            $bal = $this->getAccountBalance($acc, $fy);
            if ($bal['balance'] != 0) {
                $pendapatanData[] = ['code' => $acc->code, 'name' => $acc->name, 'amount' => $bal['credit'] - $bal['debit']];
                $totalPendapatan += $bal['credit'] - $bal['debit'];
            }
        }

        $bebanData = [];
        $totalBeban = 0;
        foreach ($beban as $acc) {
            $bal = $this->getAccountBalance($acc, $fy);
            if ($bal['balance'] != 0) {
                $bebanData[] = ['code' => $acc->code, 'name' => $acc->name, 'amount' => $bal['debit'] - $bal['credit']];
                $totalBeban += $bal['debit'] - $bal['credit'];
            }
        }

        $labaBersih = $totalPendapatan - $totalBeban;

        return Pdf::loadView('pdf.laba-rugi', [
            'pendapatanData' => $pendapatanData,
            'bebanData' => $bebanData,
            'totalPendapatan' => $totalPendapatan,
            'totalBeban' => $totalBeban,
            'labaBersih' => $labaBersih,
            'period' => 'Tahun ' . $fy->year,
            'setting' => $this->setting,
            'primaryColor' => $this->primaryColor,
            'accentColor' => $this->accentColor,
        ])->stream('laba-rugi-' . $fy->year . '.pdf');
    }

    /**
     * Generate Neraca PDF
     */
    public function neraca($fiscalYearId)
    {
        $fy = FiscalYear::findOrFail($fiscalYearId);
        
        $aset = Account::where('code', 'like', '1%')->active()->orderBy('code')->get();
        $kewajiban = Account::where('code', 'like', '2%')->active()->orderBy('code')->get();
        $ekuitas = Account::where('code', 'like', '3%')->active()->orderBy('code')->get();

        $asetData = [];
        $totalAset = 0;
        foreach ($aset as $acc) {
            $bal = $this->getAccountBalance($acc, $fy);
            if ($bal['balance'] != 0) {
                $asetData[] = ['code' => $acc->code, 'name' => $acc->name, 'amount' => $bal['balance']];
                $totalAset += $bal['balance'];
            }
        }

        $kewajibanData = [];
        $totalKewajiban = 0;
        foreach ($kewajiban as $acc) {
            $bal = $this->getAccountBalance($acc, $fy);
            if ($bal['balance'] != 0) {
                $kewajibanData[] = ['code' => $acc->code, 'name' => $acc->name, 'amount' => $bal['credit'] - $bal['debit']];
                $totalKewajiban += $bal['credit'] - $bal['debit'];
            }
        }

        $ekuitasData = [];
        $totalEkuitas = 0;
        foreach ($ekuitas as $acc) {
            $bal = $this->getAccountBalance($acc, $fy);
            if ($bal['balance'] != 0) {
                $ekuitasData[] = ['code' => $acc->code, 'name' => $acc->name, 'amount' => $bal['credit'] - $bal['debit']];
                $totalEkuitas += $bal['credit'] - $bal['debit'];
            }
        }

        // Laba ditahan dari Laba Rugi
        $pendapatan = JournalEntry::whereHas('account', function ($q) { $q->where('code', 'like', '4%'); })
            ->whereHas('transaction', function ($q) use ($fy) { $q->where('fiscal_year', $fy->year)->where('is_void', 0); })->sum('credit');
        $beban = JournalEntry::whereHas('account', function ($q) { $q->where('code', 'like', '5%')->orWhere('code', 'like', '6%'); })
            ->whereHas('transaction', function ($q) use ($fy) { $q->where('fiscal_year', $fy->year)->where('is_void', 0); })->sum('debit');
        $labaDitahan = $pendapatan - $beban;

        return Pdf::loadView('pdf.neraca', [
            'asetData' => $asetData,
            'kewajibanData' => $kewajibanData,
            'ekuitasData' => $ekuitasData,
            'totalAset' => $totalAset,
            'totalKewajiban' => $totalKewajiban,
            'totalEkuitas' => $totalEkuitas,
            'labaDitahan' => $labaDitahan,
            'period' => 'Tahun ' . $fy->year,
            'setting' => $this->setting,
            'primaryColor' => $this->primaryColor,
            'accentColor' => $this->accentColor,
        ])->stream('neraca-' . $fy->year . '.pdf');
    }

    /**
     * Generate Jurnal Umum PDF
     */
    public function jurnalUmum($fiscalYearId)
    {
        $fy = FiscalYear::findOrFail($fiscalYearId);

        $transactions = \App\Models\Transaction::where('fiscal_year', $fy->year)
            ->where('is_void', 0)
            ->orderBy('transaction_date')
            ->orderBy('id')
            ->with('journalEntries.account', 'category')
            ->get();

        return Pdf::loadView('pdf.jurnal-umum', [
            'transactions' => $transactions,
            'period' => 'Tahun ' . $fy->year,
            'setting' => $this->setting,
            'primaryColor' => $this->primaryColor,
            'accentColor' => $this->accentColor,
        ])->stream('jurnal-umum-' . $fy->year . '.pdf');
    }

    /**
     * Generate Buku Besar PDF (per akun dengan saldo berjalan)
     */
    public function bukuBesar($accountCode, $dateFrom = null, $dateTo = null)
    {
        $account = Account::where('code', $accountCode)->firstOrFail();
        
        $query = JournalEntry::where('account_code', $accountCode)
            ->orderBy('entry_date')
            ->orderBy('id');

        if ($dateFrom) {
            $query->where('entry_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->where('entry_date', '<=', $dateTo);
        }

        $entries = $query->with('transaction')->get();
        
        $runningBalance = 0;
        $totalDebit = 0;
        $totalCredit = 0;
        $ledgerData = [];

        foreach ($entries as $e) {
            $runningBalance += $e->debit - $e->credit;
            $totalDebit += $e->debit;
            $totalCredit += $e->credit;
            
            $ledgerData[] = [
                'date' => $e->entry_date,
                'ref' => $e->transaction->transaction_number ?? '-',
                'description' => $e->description ?? '',
                'debit' => $e->debit,
                'credit' => $e->credit,
                'balance' => $runningBalance,
            ];
        }

        $period = $dateFrom ? date('d/m/Y', strtotime($dateFrom)) . ' - ' . date('d/m/Y', strtotime($dateTo)) : 'Semua Periode';

        return Pdf::loadView('pdf.buku-besar', [
            'account' => $account,
            'ledgerData' => $ledgerData,
            'totalDebit' => $totalDebit,
            'totalCredit' => $totalCredit,
            'balance' => $runningBalance,
            'period' => $period,
            'setting' => $this->setting,
            'primaryColor' => $this->primaryColor,
            'accentColor' => $this->accentColor,
        ])->stream('buku-besar-' . $account->code . '.pdf');
    }

    /**
     * Format currency
     */
    public static function formatRupiah($amount)
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}
