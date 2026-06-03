<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialReportTemplate extends Model
{
    protected $fillable = [
        'name',
        'code',
        'type',
        'description',
        'structure',
        'settings',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'structure' => 'array',
        'settings' => 'array',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    // Helpers
    public function getRows(): array
    {
        return $this->structure['rows'] ?? [];
    }

    public function getColumns(): array
    {
        return $this->structure['columns'] ?? [];
    }
}
