<?php

namespace App\Filament\Pages;

use App\Models\FiscalYear;
use App\Services\YearEndClosingService;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class YearEndClosingPage extends Page
{
    protected static string $view = 'filament.pages.year-end-closing';
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = '💰 Keuangan';
    protected static ?string $navigationLabel = 'Tutup Buku';
    protected static ?string $title = 'Tutup Buku Tahunan';
    protected static ?int $navigationSort = 7;

    // Step management
    public int $currentStep = 1;
    public bool $processing = false;

    // Step 1: Select fiscal year
    public ?int $selectedFiscalYearId = null;
    public array $openFiscalYears = [];

    // Step 2: Trial balance preview
    public array $trialBalance = [];
    public float $tbTotalDebit = 0;
    public float $tbTotalCredit = 0;
    public bool $tbBalanced = false;

    // Step 3: Income statement preview
    public array $incomeRevenues = [];
    public array $incomeExpenses = [];
    public float $incomeTotalRevenue = 0;
    public float $incomeTotalExpense = 0;
    public float $incomeNetIncome = 0;

    // Pre-check
    public bool $canClose = false;
    public string $canCloseReason = '';

    // Step 5: Result
    public array $closingResult = [];

    public function mount(): void
    {
        $this->loadOpenFiscalYears();
    }

    public function loadOpenFiscalYears(): void
    {
        $this->openFiscalYears = FiscalYear::where('status', 'open')
            ->orderBy('year', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * Step 1 → 2: Load trial balance for selected year
     */
    public function goToStep2(): void
    {
        if (!$this->selectedFiscalYearId) {
            Notification::make()
                ->title('Pilih Tahun Fiskal')
                ->warning()
                ->send();
            return;
        }

        // Run pre-check
        $check = YearEndClosingService::canCloseYear($this->selectedFiscalYearId);
        $this->canClose = $check['can'];
        $this->canCloseReason = $check['reason'];

        // Load trial balance
        $tb = YearEndClosingService::getTrialBalance($this->selectedFiscalYearId);
        $this->trialBalance = $tb['accounts'];
        $this->tbTotalDebit = $tb['totalDebit'];
        $this->tbTotalCredit = $tb['totalCredit'];
        $this->tbBalanced = $tb['balanced'];

        $this->currentStep = 2;
    }

    /**
     * Step 2 → 3: Load income statement
     */
    public function goToStep3(): void
    {
        $is = YearEndClosingService::getIncomeStatement($this->selectedFiscalYearId);
        $this->incomeRevenues = $is['revenues'];
        $this->incomeExpenses = $is['expenses'];
        $this->incomeTotalRevenue = $is['totalRevenue'];
        $this->incomeTotalExpense = $is['totalExpense'];
        $this->incomeNetIncome = $is['netIncome'];

        $this->currentStep = 3;
    }

    /**
     * Step 3 → 4: Confirm page
     */
    public function goToStep4(): void
    {
        $this->currentStep = 4;
    }

    /**
     * Step 4 → Execute closing
     */
    public function executeClosing(): void
    {
        if (!$this->canClose) {
            Notification::make()
                ->title('Tidak Dapat Menutup Tahun')
                ->body($this->canCloseReason)
                ->danger()
                ->send();
            return;
        }

        $this->processing = true;

        try {
            $result = YearEndClosingService::closeYear($this->selectedFiscalYearId);
            $this->closingResult = $result;

            Notification::make()
                ->title('Tutup Buku Berhasil')
                ->body($result['message'])
                ->success()
                ->send();

            $this->currentStep = 5;

            // Refresh the list
            $this->loadOpenFiscalYears();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Gagal Menutup Buku')
                ->body($e->getMessage())
                ->danger()
                ->send();
        } finally {
            $this->processing = false;
        }
    }

    /**
     * Go back to step 1
     */
    public function resetWizard(): void
    {
        $this->currentStep = 1;
        $this->selectedFiscalYearId = null;
        $this->trialBalance = [];
        $this->incomeRevenues = [];
        $this->incomeExpenses = [];
        $this->closingResult = [];
        $this->canClose = false;
        $this->canCloseReason = '';
        $this->loadOpenFiscalYears();
    }

    /**
     * Go back one step
     */
    public function goBack(): void
    {
        if ($this->currentStep > 1 && $this->currentStep < 5) {
            $this->currentStep--;
        }
    }

    public function getFiscalYearLabel(int $year): string
    {
        return "Tahun Fiskal {$year}";
    }

    public function getSelectedYear(): ?int
    {
        if (!$this->selectedFiscalYearId) return null;
        $fy = FiscalYear::find($this->selectedFiscalYearId);
        return $fy?->year;
    }
}
