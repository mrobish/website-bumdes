<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FinancialTemplate extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'type',
        'frequency',
        'columns',
        'sample_data',
        'validation_rules',
        'file_path',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'columns' => 'array',
        'sample_data' => 'array',
        'validation_rules' => 'array',
        'is_active' => 'boolean',
    ];

    public function uploads(): HasMany
    {
        return $this->hasMany(FinancialUpload::class, 'template_id');
    }

    public function getTypeLabel(): string
    {
        return match($this->type) {
            'jurnal' => 'Jurnal',
            'neraca' => 'Neraca',
            'laba_rugi' => 'Laba/Rugi',
            'arus_kas' => 'Arus Kas',
            'modal' => 'Perubahan Modal',
            'realisasi_anggaran' => 'Realisasi Anggaran',
            'buku_besar' => 'Buku Besar',
            'cat' => 'CAT',
            default => $this->type,
        };
    }

    public function getFrequencyLabel(): string
    {
        return match($this->frequency) {
            'harian' => 'Harian',
            'bulanan' => 'Bulanan',
            'tahunan' => 'Tahunan',
            'sekali' => 'Sekali',
            default => $this->frequency,
        };
    }

    public function getTypeColor(): string
    {
        return match($this->type) {
            'jurnal' => 'info',
            'neraca' => 'success',
            'laba_rugi' => 'warning',
            'arus_kas' => 'primary',
            'modal' => 'gray',
            'realisasi_anggaran' => 'danger',
            'buku_besar' => 'info',
            'cat' => 'gray',
            default => 'gray',
        };
    }
}
