<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BumdesStructure extends Model
{
    use HasFactory;

    protected $fillable = [
        'position',
        'position_group',
        'sort_order',
        'name',
        'nip',
        'photo_path',
        'phone',
        'email',
        'is_active',
        'is_village_head',
        'business_unit_id',
        'start_date',
        'end_date',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_village_head' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // Relationships
    public function businessUnit(): BelongsTo
    {
        return $this->belongsTo(BusinessUnit::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePengurus($query)
    {
        return $query->where('position_group', 'pengurus');
    }

    public function scopeUnit($query)
    {
        return $query->where('position_group', 'unit');
    }

    public function scopePengawas($query)
    {
        return $query->where('position_group', 'pengawas');
    }

    // Helpers
    public function getPhotoUrlAttribute()
    {
        return $this->photo_path ? asset('storage/' . $this->photo_path) : null;
    }

    /**
     * Get position group label
     */
    public function getPositionGroupLabelAttribute()
    {
        return match($this->position_group) {
            'pengurus' => 'Pengurus BUMDes',
            'unit' => 'Pengelola Unit Usaha',
            'pengawas' => 'Pengawas',
            'lainnya' => 'Lainnya',
            default => $this->position_group,
        };
    }
}
