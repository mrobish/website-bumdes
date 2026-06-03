<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialUpload extends Model
{
    protected $fillable = [
        'template_id',
        'user_id',
        'file_name',
        'file_path',
        'file_size',
        'period',
        'status',
        'notes',
        'validation_errors',
        'parsed_data',
        'validated_at',
        'approved_at',
    ];

    protected $casts = [
        'validation_errors' => 'array',
        'parsed_data' => 'array',
        'validated_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(FinancialTemplate::class, 'template_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getStatusLabel(): string
    {
        return match($this->status) {
            'pending' => 'Menunggu Validasi',
            'validated' => 'Sah',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default => $this->status,
        };
    }

    public function getStatusColor(): string
    {
        return match($this->status) {
            'pending' => 'warning',
            'validated' => 'info',
            'approved' => 'success',
            'rejected' => 'danger',
            default => 'gray',
        };
    }
}
