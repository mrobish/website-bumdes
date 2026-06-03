<x-filament-panels::page>
    <form wire:submit="submitJurnal">
        {!! $this->form !!}
        
        <div style="margin-top: 1.5rem; display: flex; justify-content: center; gap: 1rem;">
            <x-filament::button type="submit" size="lg" icon="heroicon-o-check">
                💾 Simpan Jurnal (Draft)
            </x-filament::button>
            
            <x-filament::button tag="a" href="/admin/jurnal" size="lg" color="gray" icon="heroicon-o-arrow-left">
                ← Kembali ke Daftar
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
