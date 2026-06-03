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
    protected static ?string $navigationGroup = '💰 Keuangan';
    protected static ?string $navigationLabel = 'Input Jurnal';
    protected static ?string $title = 'Input Jurnal';
    protected static ?int $navigationSort = 1;

    public ?array $data = [];
    public int $todayCount = 0;

    public function mount(): void
    {
        $this->todayCount = FinancialTransaction::whereDate('transaction_date', today())->count();
        $this->form->fill([
            'transaction_date' => now()->format('Y-m-d'),
            'type' => 'pemasukan',
            'account_code' => '',
            'amount' => null,
            'description' => '',
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // Tanggal
                TextInput::make('transaction_date')
                    ->label('📅 Tanggal')
                    ->type('date')
                    ->required()
                    ->default(now()->format('Y-m-d'))
                    ->live(),

                // Tipe — toggle besar
                Select::make('type')
                    ->label('💰 Tipe')
                    ->options([
                        'pemasukan' => '✅ Pemasukan (Uang Masuk)',
                        'pengeluaran' => '❌ Pengeluaran (Uang Keluar)',
                    ])
                    ->required()
                    ->default('pemasukan')
                    ->live(),

                // Kode Akun — search & pilih
                Select::make('account_code')
                    ->label('📋 Kode Akun')
                    ->options(fn () => ChartOfAccount::pluck('name', 'code')->map(fn ($name, $code) => "$code — $name")->toArray())
                    ->searchable()
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn ($state, $set) => $set('account_name_display', ChartOfAccount::where('code', $state)->value('name') ?? '')),

                // Jumlah
                TextInput::make('amount')
                    ->label('💵 Jumlah (Rp)')
                    ->numeric()
                    ->prefix('Rp')
                    ->placeholder('0')
                    ->required()
                    ->minValue(1)
                    ->autofocus(),

                // Keterangan
                TextInput::make('description')
                    ->label('📝 Keterangan')
                    ->placeholder('Contoh: Beli beras 5kg')
                    ->required()
                    ->maxLength(255),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $data = $this->form->getState();

        // Validasi
        if (empty($data['account_code']) || empty($data['amount']) || $data['amount'] <= 0) {
            Notification::make()->title('Lengkapi Data!')->danger()->send();
            return;
        }

        // Generate nomor transaksi
        $date = \Carbon\Carbon::parse($data['transaction_date']);
        $prefix = 'J-' . $date->format('Ymd');
        $count = FinancialTransaction::where('transaction_number', 'like', $prefix . '%')->count();
        $number = $prefix . '-' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);

        // Simpan langsung
        FinancialTransaction::create([
            'transaction_number' => $number,
            'transaction_date' => $data['transaction_date'],
            'type' => $data['type'],
            'amount' => $data['amount'],
            'description' => $data['description'],
            'account_code' => $data['account_code'],
            'status' => 'published',
            'created_by' => auth()->id() ?? 1,
        ]);

        $this->todayCount++;

        $tipe = $data['type'] === 'pemasukan' ? '💰 Pemasukan' : '💸 Pengeluaran';
        $namaAkun = ChartOfAccount::where('code', $data['account_code'])->value('name');

        Notification::make()
            ->title("✅ Tersimpan!")
            ->body("$tipe Rp " . number_format($data['amount'], 0, ',', '.') . " — $namaAkun")
            ->success()
            ->duration(2000)
            ->send();

        // Reset form untuk input berikutnya
        $this->form->fill([
            'transaction_date' => $data['transaction_date'],
            'type' => $data['type'],
            'account_code' => '',
            'amount' => null,
            'description' => '',
        ]);
    }
}
