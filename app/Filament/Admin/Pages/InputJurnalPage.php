<?php

namespace App\Filament\Admin\Pages;

use App\Models\ChartOfAccount;
use App\Models\FinancialTransaction;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class InputJurnalPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.pages.input-jurnal';
    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';
    protected static ?string $navigationGroup = 'Keuangan';
    protected static ?string $navigationLabel = 'Input Jurnal';
    protected static ?string $title = 'Input Jurnal Harian';
    protected static ?int $navigationSort = 1;

    public ?array $data = [];
    public int $month;
    public int $year;

    public function mount(): void
    {
        $this->month = (int) now()->month;
        $this->year = (int) now()->year;
        $this->form->fill([
            'month' => $this->month,
            'year' => $this->year,
            'rows' => [],
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('📅 Pengaturan Periode')
                    ->description('Pilih bulan dan tahun transaksi jurnal')
                    ->icon('heroicon-o-calendar')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('month')
                                ->label('Bulan')
                                ->options([
                                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
                                    4 => 'April', 5 => 'Mei', 6 => 'Juni',
                                    7 => 'Juli', 8 => 'Agustus', 9 => 'September',
                                    10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                                ])
                                ->default(now()->month)
                                ->required(),
                            Select::make('year')
                                ->label('Tahun')
                                ->options(collect(array_reverse(range(now()->year - 3, now()->year + 1)))->mapWithKeys(fn ($y) => [$y => $y]))
                                ->default(now()->year)
                                ->required(),
                        ]),
                    ])->columns(2),

                Section::make('📝 Daftar Transaksi')
                    ->description('Klik "+ Tambah Baris" untuk menambah transaksi. Semua baris disimpan sebagai DRAFT.')
                    ->icon('heroicon-o-table-cells')
                    ->schema([
                        \Filament\Forms\Components\Repeater::make('rows')
                            ->schema([
                                Grid::make(6)->schema([
                                    TextInput::make('transaction_date')
                                        ->label('Tanggal')
                                        ->type('date')
                                        ->required()
                                        ->columnSpan(1),
                                    Select::make('account_code')
                                        ->label('Kode Akun')
                                        ->options(fn () => ChartOfAccount::pluck('code', 'code')->toArray())
                                        ->searchable()
                                        ->required()
                                        ->reactive()
                                        ->afterStateUpdated(fn ($state, $set) => $set('account_name', ChartOfAccount::where('code', $state)->value('name') ?? ''))
                                        ->columnSpan(1),
                                    TextInput::make('account_name')
                                        ->label('Nama Akun')
                                        ->disabled()
                                        ->dehydrated(false)
                                        ->columnSpan(1),
                                    Select::make('type')
                                        ->label('Tipe')
                                        ->options([
                                            'pemasukan' => '💰 Pemasukan',
                                            'pengeluaran' => '💸 Pengeluaran',
                                        ])
                                        ->required()
                                        ->columnSpan(1),
                                    TextInput::make('amount')
                                        ->label('Jumlah (Rp)')
                                        ->numeric()
                                        ->prefix('Rp')
                                        ->required()
                                        ->minValue(0)
                                        ->columnSpan(1),
                                    TextInput::make('description')
                                        ->label('Keterangan')
                                        ->required()
                                        ->columnSpan(1),
                                ])->columns(6),
                            ])
                            ->defaultItems(1)
                            ->addActionLabel('+ Tambah Baris')
                            ->removeActionLabel('✕')
                            ->reorderable(false)
                            ->collapsible(false)
                            ->itemLabel(fn (array $state): ?string =>
                                !empty($state['account_code'])
                                    ? "{$state['account_code']} - {$state['description']}"
                                    : null
                            ),
                    ]),
            ])
            ->statePath('data');
    }

    public function submitJurnal(): void
    {
        $data = $this->form->getState();
        $rows = $data['rows'] ?? [];
        $month = $data['month'];
        $year = $data['year'];

        $validRows = array_filter($rows, fn ($r) =>
            !empty($r['account_code']) && !empty($r['amount']) && (float) $r['amount'] > 0
        );

        if (empty($validRows)) {
            Notification::make()->title('Error')->body('Tidak ada transaksi yang valid!')->danger()->send();
            return;
        }

        $totalPemasukan = 0;
        $totalPengeluaran = 0;
        foreach ($validRows as $row) {
            if (($row['type'] ?? '') === 'pemasukan') {
                $totalPemasukan += (float) $row['amount'];
            } else {
                $totalPengeluaran += (float) $row['amount'];
            }
        }

        if (abs($totalPemasukan - $totalPengeluaran) > 0.01) {
            Notification::make()
                ->title('Jurnal Tidak Seimbang!')
                ->body("Pemasukan Rp " . number_format($totalPemasukan, 0, ',', '.') . " ≠ Pengeluaran Rp " . number_format($totalPengeluaran, 0, ',', '.'))
                ->danger()->send();
            return;
        }

        $prefix = 'J-' . $year . str_pad($month, 2, '0', STR_PAD_LEFT);
        $lastNum = FinancialTransaction::where('transaction_number', 'like', $prefix . '%')->count();
        $counter = $lastNum;
        $saved = 0;

        foreach ($validRows as $row) {
            $counter++;
            $d = \Carbon\Carbon::parse($row['transaction_date']);
            if ((int) $d->month !== $month || (int) $d->year !== $year) continue;

            FinancialTransaction::create([
                'transaction_number' => $prefix . '-' . str_pad($counter, 4, '0', STR_PAD_LEFT),
                'transaction_date' => $row['transaction_date'],
                'type' => $row['type'],
                'amount' => $row['amount'],
                'description' => $row['description'],
                'account_code' => $row['account_code'],
                'status' => 'draft',
                'created_by' => auth()->id() ?? 1,
            ]);
            $saved++;
        }

        if ($saved > 0) {
            $namaBulan = \Carbon\Carbon::create()->month($month)->translatedFormat('F');
            Notification::make()
                ->title("✅ $saved Transaksi Tersimpan!")
                ->body("Jurnal bulan $namaBulan $year disimpan sebagai DRAFT.")
                ->success()->send();

            $this->form->fill([
                'month' => $this->month,
                'year' => $this->year,
                'rows' => [],
            ]);
        } else {
            Notification::make()->title('Error')->body('Gagal menyimpan!')->danger()->send();
        }
    }
}
