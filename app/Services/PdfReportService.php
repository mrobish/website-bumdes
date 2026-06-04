<?php

namespace App\Services;

use App\Models\BumdesSetting;
use App\Models\FiscalYear;
use App\Models\Account;
use App\Models\JournalEntry;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfReportService
{
    protected $setting;
    protected $primaryColor = '#1E3A5F';
    protected $accentColor = '#D4A843';
    protected $lightBg = '#F8F9FA';

    public function __construct()
    {
        $this->setting = BumdesSetting::first();
        if ($this->setting && $this->setting->primary_color) {
            $this->primaryColor = $this->setting->primary_color;
        }
        if ($this->setting && $this->setting->secondary_color) {
            $this->accentColor = $this->setting->secondary_color;
        }
    }

    /**
     * Generate Neraca Saldo PDF
     */
    public function neracaSaldo($fiscalYearId, $month = null)
    {
        $fy = FiscalYear::findOrFail($fiscalYearId);
        $accounts = Account::orderBy('code')->get();
        $data = [];
        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($accounts as $acc) {
            $query = JournalEntry::whereHas('transaction', function ($q) use ($fy) {
                $q->where('fiscal_year_id', $fy->id)->where('status', 'posted');
            })->where('account_id', $acc->id);

            if ($month) {
                $query->whereHas('transaction', function ($q) use ($fy, $month) {
                    $q->whereYear('transaction_date', $fy->year)
                      ->whereMonth('transaction_date', $month);
                });
            }

            $debit = $query->sum('debit');
            $credit = $query->sum('credit');

            $balance = $debit - $credit;
            if ($balance != 0) {
                $data[] = [
                    'code' => $acc->code,
                    'name' => $acc->name,
                    'debit' => $balance > 0 ? abs($balance) : 0,
                    'credit' => $balance < 0 ? abs($balance) : 0,
                ];
                $totalDebit += $balance > 0 ? abs($balance) : 0;
                $totalCredit += $balance < 0 ? abs($balance) : 0;
            }
        }

        $period = $month ? date('F', mktime(0, 0, 0, $month, 1)) . ' ' . $fy->year : 'Per ' . $fy->year;

        return Pdf::loadView('pdf.neraca-saldo', [
            'setting' => $this->setting,
            'data' => $data,
            'period' => $period,
            'totalDebit' => $totalDebit,
            'totalCredit' => $totalCredit,
            'primaryColor' => $this->primaryColor,
            'accentColor' => $this->accentColor,
        ])->stream('neraca-saldo-' . $fy->year . '.pdf');
    }

    /**
     * Generate Laba Rugi PDF
     */
    public function labaRugi($fiscalYearId, $month = null)
    {
        $fy = FiscalYear::findOrFail($fiscalYearId);

        $pendapatan = Account::where('code', 'like', '4%')->orderBy('code')->get();
        $beban = Account::where('code', 'like', '5%')
            ->orWhere('code', 'like', '6%')
            ->orderBy('code')->get();

        $totalPendapatan = 0;
        $totalBeban = 0;
        $pendapatanData = [];
        $bebanData = [];

        foreach ($pendapatan as $acc) {
            $query = JournalEntry::whereHas('transaction', function ($q) use ($fy) {
                $q->where('fiscal_year_id', $fy->id)->where('status', 'posted');
            })->where('account_id', $acc->id);

            if ($month) {
                $query->whereHas('transaction', function ($q) use ($fy, $month) {
                    $q->whereYear('transaction_date', $fy->year)
                      ->whereMonth('transaction_date', $month);
                });
            }

            $amount = $query->sum('credit') - $query->sum('debit');
            if ($amount != 0) {
                $pendapatanData[] = ['code' => $acc->code, 'name' => $acc->name, 'amount' => $amount];
                $totalPendapatan += $amount;
            }
        }

        foreach ($beban as $acc) {
            $query = JournalEntry::whereHas('transaction', function ($q) use ($fy) {
                $q->where('fiscal_year_id', $fy->id)->where('status', 'posted');
            })->where('account_id', $acc->id);

            if ($month) {
                $query->whereHas('transaction', function ($q) use ($fy, $month) {
                    $q->whereYear('transaction_date', $fy->year)
                      ->whereMonth('transaction_date', $month);
                });
            }

            $amount = $query->sum('debit') - $query->sum('credit');
            if ($amount != 0) {
                $bebanData[] = ['code' => $acc->code, 'name' => $acc->name, 'amount' => $amount];
                $totalBeban += $amount;
            }
        }

        $labaBersih = $totalPendapatan - $totalBeban;
        $period = $month ? date('F', mktime(0, 0, 0, $month, 1)) . ' ' . $fy->year : 'Per ' . $fy->year;

        return Pdf::loadView('pdf.laba-rugi', [
            'setting' => $this->setting,
            'pendapatanData' => $pendapatanData,
            'bebanData' => $bebanData,
            'totalPendapatan' => $totalPendapatan,
            'totalBeban' => $totalBeban,
            'labaBersih' => $labaBersih,
            'period' => $period,
            'primaryColor' => $this->primaryColor,
            'accentColor' => $this->accentColor,
        ])->stream('laba-rugi-' . $fy->year . '.pdf');
    }

    /**
     * Generate Neraca PDF
     */
    public function neraca($fiscalYearId, $month = null)
    {
        $fy = FiscalYear::findOrFail($fiscalYearId);

        $aset = Account::where('code', 'like', '1%')->orderBy('code')->get();
        $kewajiban = Account::where('code', 'like', '2%')->orderBy('code')->get();
        $ekuitas = Account::where('code', 'like', '3%')->orderBy('code')->get();

        $totalAset = 0;
        $totalKewajiban = 0;
        $totalEkuitas = 0;
        $asetData = [];
        $kewajibanData = [];
        $ekuitasData = [];

        $getBalance = function ($acc) use ($fy, $month) {
            $query = JournalEntry::whereHas('transaction', function ($q) use ($fy) {
                $q->where('fiscal_year_id', $fy->id)->where('status', 'posted');
            })->where('account_id', $acc->id);

            if ($month) {
                $query->whereHas('transaction', function ($q) use ($fy, $month) {
                    $q->whereYear('transaction_date', $fy->year)
                      ->whereMonth('transaction_date', $month);
                });
            }

            return $query->sum('debit') - $query->sum('credit');
        };

        foreach ($aset as $acc) {
            $bal = $getBalance($acc);
            if ($bal != 0) {
                $asetData[] = ['code' => $acc->code, 'name' => $acc->name, 'amount' => $bal];
                $totalAset += $bal;
            }
        }

        foreach ($kewajiban as $acc) {
            $bal = $getBalance($acc);
            if ($bal != 0) {
                $kewajibanData[] = ['code' => $acc->code, 'name' => $acc->name, 'amount' => abs($bal)];
                $totalKewajiban += abs($bal);
            }
        }

        foreach ($ekuitas as $acc) {
            $bal = $getBalance($acc);
            if ($bal != 0) {
                $ekuitasData[] = ['code' => $acc->code, 'name' => $acc->name, 'amount' => abs($bal)];
                $totalEkuitas += abs($bal);
            }
        }

        // Calculate retained earnings
        $pendapatanTotal = Account::where('code', 'like', '4%')->get()->sum(function ($acc) use ($getBalance) {
            return $getBalance($acc);
        });
        $bebanTotal = Account::where('code', 'like', '5%')
            ->orWhere('code', 'like', '6%')
            ->get()->sum(function ($acc) use ($getBalance) {
                return $getBalance($acc);
            });

        $labaDitahan = $pendapatanTotal - abs($bebanTotal);
        $period = $month ? date('F', mktime(0, 0, 0, $month, 1)) . ' ' . $fy->year : 'Per ' . $fy->year;

        return Pdf::loadView('pdf.neraca', [
            'setting' => $this->setting,
            'asetData' => $asetData,
            'kewajibanData' => $kewajibanData,
            'ekuitasData' => $ekuitasData,
            'totalAset' => $totalAset,
            'totalKewajiban' => $totalKewajiban,
            'totalEkuitas' => $totalEkuitas,
            'labaDitahan' => $labaDitahan,
            'period' => $period,
            'primaryColor' => $this->primaryColor,
            'accentColor' => $this->accentColor,
        ])->stream('neraca-' . $fy->year . '.pdf');
    }

    /**
     * Generate Jurnal Umum PDF
     */
    public function jurnalUmum($fiscalYearId, $month = null)
    {
        $fy = FiscalYear::findOrFail($fiscalYearId);

        $query = \App\Models\Transaction::where('fiscal_year_id', $fy->id)
            ->where('status', 'posted')
            ->orderBy('transaction_date')
            ->orderBy('id');

        if ($month) {
            $query->whereYear('transaction_date', $fy->year)
                  ->whereMonth('transaction_date', $month);
        }

        $transactions = $query->with('journalEntries.account', 'category')->get();
        $period = $month ? date('F', mktime(0, 0, 0, $month, 1)) . ' ' . $fy->year : 'Tahun ' . $fy->year;

        return Pdf::loadView('pdf.jurnal-umum', [
            'setting' => $this->setting,
            'transactions' => $transactions,
            'period' => $period,
            'primaryColor' => $this->primaryColor,
            'accentColor' => $this->accentColor,
        ])->stream('jurnal-umum-' . $fy->year . '.pdf');
    }

    /**
     * Format currency
     */
    public static function formatRupiah($amount)
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}
