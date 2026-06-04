<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Piutang extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'piutang_number',
        'piutang_date',
        'customer_name',
        'customer_phone',
        'customer_address',
        'customer_nik',
        'category_id',
        'unit_id',
        'amount',
        'paid_amount',
        'remaining',
        'due_date',
        'status',
        'description',
        'reference_number',
        'attachment_path',
        'transaction_id',
        'created_by',
        'notes',
    ];

    protected $casts = [
        'piutang_date' => 'date',
        'due_date' => 'date',
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining' => 'decimal:2',
    ];

    // Boot
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (!$model->remaining) {
                $model->remaining = $model->amount;
            }
            if (!$model->piutang_number) {
                $year = date('Y');
                $last = static::whereYear('piutang_date', $year)->orderBy('id', 'desc')->first();
                $num = $last ? intval(substr($last->piutang_number, -3)) + 1 : 1;
                $model->piutang_number = 'PIU-' . $year . '-' . str_pad($num, 3, '0', STR_PAD_LEFT);
            }
        });
    }

    // Relationships
    public function payments()
    {
        return $this->hasMany(PiutangPayment::class)->orderBy('payment_date');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function unit()
    {
        return $this->belongsTo(BusinessUnit::class, 'unit_id');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Accessors
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'belum_lunas' => '🔴 Belum Lunas',
            'sebagian' => '🟡 Sebagian',
            'lunas' => '🟢 Lunas',
            'macet' => '⚫ Macet',
            default => '-',
        };
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date && $this->due_date->isPast() && $this->status !== 'lunas';
    }

    public function getPercentPaidAttribute(): float
    {
        return $this->amount > 0 ? ($this->paid_amount / $this->amount) * 100 : 0;
    }

    // Methods
    public function updateStatus(): void
    {
        $this->remaining = $this->amount - $this->paid_amount;
        
        if ($this->remaining <= 0) {
            $this->status = 'lunas';
            $this->remaining = 0;
        } elseif ($this->paid_amount > 0) {
            $this->status = 'sebagian';
        } else {
            $this->status = 'belum_lunas';
        }
        
        $this->save();
    }
}
