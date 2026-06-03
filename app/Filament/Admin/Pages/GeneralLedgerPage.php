<?php

namespace App\Filament\Admin\Pages;

use App\Models\Account;
use App\Models\JournalEntry;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;

class GeneralLedgerPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.pages.general-ledger';
    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationGroup = '📊 Laporan';
    protected static ?string $navigationLabel = 'Buku Besar';
    protected static ?string $title = 'Buku Besar';
    protected static ?int $navigationSort = 1;

    public ?array $data = [];
    public array $entries = [];
    public float $totalDebit = 0;
    public float $totalCredit = 0;
    public float $balance = 0;

    public function mount(): void
    {
        $this->form->fill([
            'account_code' => '',
            'date_from' => now()->startOfYear()->format('Y-m-d'),
            'date_to' => now()->format('Y-m-d'),
            'unit_id' => '',
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(4)->schema([
                    Select::make('account_code')
                        ->label('Akun')
                        ->options(fn () => Account::active()->pluck('name', 'code')->map(fn ($name, $code) => "$code — $name")->toArray())
                        ->searchable()
                        ->required(),
                    DatePicker::make('date_from')
                        ->label('Dari')
                        ->required(),
                    DatePicker::make('date_to')
                        ->label('Sampai')
                        ->required(),
                    Select::make('unit_id')
                        ->label('Unit')
                        ->options(fn () => ['' => 'Semua Unit'] + \App\Models\BusinessUnit::pluck('name', 'id')->toArray())
                        ->default(''),
                ])->columns(4),
            ])
            ->statePath('data');
    }

    public function loadData(): void
    {
        $data = $this->form->getState();
        
        $query = JournalEntry::query()
            ->where('account_code', $data['account_code'])
            ->whereBetween('entry_date', [$data['date_from'], $data['date_to']]);

        if (!empty($data['unit_id'])) {
            $query->where('unit_id', $data['unit_id']);
        }

        $this->entries = $query->with('transaction', 'unit')
            ->orderBy('entry_date')
            ->get()
            ->toArray();

        $this->totalDebit = collect($this->entries)->sum('debit');
        $this->totalCredit = collect($this->entries)->sum('credit');
        
        $account = Account::where('code', $data['account_code'])->first();
        if ($account && $account->normal_balance === 'debit') {
            $this->balance = $this->totalDebit - $this->totalCredit;
        } else {
            $this->balance = $this->totalCredit - $this->totalDebit;
        }
    }
}
