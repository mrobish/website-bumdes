<x-filament-panels::page>
    <style>
        .jurnal-form { max-width: 100%; }
        .jurnal-form .fi-fo-field-wrp { margin-bottom: 0.75rem !important; }
        .jurnal-form input, .jurnal-form select {
            font-size: 16px !important; /* Prevent iOS zoom */
            padding: 12px !important;
            min-height: 48px !important;
        }
        .jurnal-form .fi-fo-select-input, .jurnal-form .fi-fo-text-input {
            border-radius: 12px !important;
        }
        .submit-btn {
            width: 100%;
            padding: 16px !important;
            font-size: 18px !important;
            font-weight: 700 !important;
            border-radius: 12px !important;
            margin-top: 0.5rem;
        }
        .today-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 1rem;
        }
    </style>

    <div class="jurnal-form">
        <div class="today-badge">
            📊 Hari ini: <strong>{{ $this->todayCount }}</strong> transaksi tercatat
        </div>

        <form wire:submit="submit">
            {!! $this->form !!}

            <x-filament::button type="submit" class="submit-btn" icon="heroicon-o-check-circle">
                💾 SIMPAN
            </x-filament::button>
        </form>
    </div>
</x-filament-panels::page>
