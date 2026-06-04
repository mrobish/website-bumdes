<?php

namespace App\Filament\Admin\Pages;

use App\Models\Transaction;
use App\Models\Category;
use App\Models\BusinessUnit;
use App\Models\FiscalYear;
use App\Services\AutoJournalService;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

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
        $this->todayCount = Transaction::whereDate('transaction_date', today())->where('is_void', false)->count();
        $this->form->fill([
            'transaction_date' => now()->format('Y-m-d'),
            'type' => 'income',
            'category_id' => null,
            'unit_id' => null,
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

                // Tipe
                Select::make('type')
                    ->label('💰 Tipe')
                    ->options([
                        'income' => '✅ Pemasukan (Uang Masuk)',
                        'expense' => '❌ Pengeluaran (Uang Keluar)',
                    ])
                    ->required()
                    ->default('income')
                    ->live(),

                // Kategori (berdasarkan tipe)
                Select::make('category_id')
                    ->label('📋 Kategori')
                    ->options(fn (callable $get) => Category::where('type', $get('type') ?? 'income')
                        ->pluck('name', 'id'))
                    ->searchable()
                    ->required(),

                // Unit Usaha (opsional)
                Select::make('unit_id')
                    ->label('🏢 Unit Usaha (opsional)')
                    ->options(BusinessUnit::pluck('name', 'id'))
                    ->searchable()
                    ->nullable(),

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
        if (empty($data['category_id']) || empty($data['amount']) || $data['amount'] <= 0) {
            Notification::make()->title('Lengkapi Data!')->danger()->send();
            return;
        }

        DB::transaction(function () use ($data) {
            // Generate transaction number
            $date = \Carbon\Carbon::parse($data['transaction_date']);
            $prefix = strtoupper(substr($data['type'], 0, 3)) . '-' . $date->format('Ymd');
            $count = Transaction::where('transaction_number', 'like', $prefix . '%')->count();
            $number = $prefix . '-' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);

            // Create transaction (new system)
            $transaction = Transaction::create([
                'transaction_number' => $number,
                'transaction_date' => $data['transaction_date'],
                'type' => $data['type'],
                'category_id' => $data['category_id'],
                'unit_id' => $data['unit_id'],
                'amount' => $data['amount'],
                'description' => $data['description'],
                'fiscal_year' => $date->year,
                'created_by' => auth()->id() ?? 1,
            ]);

            // Auto journal (double-entry)
            match ($data['type']) {
                'income' => AutoJournalService::recordIncome($transaction),
                'expense' => AutoJournalService::recordExpense($transaction),
            };

            // Verify balance
            if (!AutoJournalService::verifyBalance($transaction)) {
                throw new \Exception('Jurnal tidak seimbang!');
            }
        });

        $this->todayCount++;

        $tipe = $data['type'] === 'income' ? '💰 Pemasukan' : '💸 Pengeluaran';
        $kategori = Category::find($data['category_id'])?->name ?? '-';

        Notification::make()
            ->title("✅ Tersimpan!")
            ->body("$tipe Rp " . number_format($data['amount'], 0, ',', '.') . " — $kategori")
            ->success()
            ->duration(2000)
            ->send();

        // Reset form untuk input berikutnya
        $this->form->fill([
            'transaction_date' => $data['transaction_date'],
            'type' => $data['type'],
            'category_id' => null,
            'unit_id' => null,
            'amount' => null,
            'description' => '',
        ]);
    }
}
