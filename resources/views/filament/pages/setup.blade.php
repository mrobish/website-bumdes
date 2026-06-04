<x-filament-panels::page>
    <style>
        .setup-container { max-width: 800px; margin: 0 auto; }
        .setup-header { text-align: center; padding: 40px 20px; background: linear-gradient(135deg, #1e3a5f, #2563eb); border-radius: 16px; margin-bottom: 24px; color: white; }
        .setup-header h1 { font-size: 28px; font-weight: 800; margin-bottom: 8px; }
        .setup-header p { font-size: 16px; opacity: 0.9; }
        .setup-header .icon { font-size: 48px; margin-bottom: 12px; }
        .setup-footer { text-align: center; padding: 20px; margin-top: 24px; }
    </style>

    <div class="setup-container">
        <div class="setup-header">
            <div class="icon">🏢</div>
            <h1>Setup BUMDes</h1>
            <p>Lengkapi identitas BUMDes Anda untuk mulai menggunakan sistem</p>
        </div>

        <x-filament-panels::form wire:submit="save">
            {{ $this->form }}

            <div class="setup-footer">
                <x-filament::button type="submit" size="lg" icon="heroicon-o-check-circle">
                    💾 Simpan dan Mulai Menggunakan Sistem
                </x-filament::button>
            </div>
        </x-filament-panels::form>
    </div>
</x-filament-panels::page>
