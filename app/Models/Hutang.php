<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hutang extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'hutang_number', 'hutang_date', 'supplier_name', 'supplier_phone',
        'supplier_address', 'supplier_npwp', 'category_id', 'unit_id',
        'amount', 'paid_amount', 'remaining', 'due_date', 'status',
        'description', 'reference_number', 'attachment_path',
        'agreement_letter_path', 'transaction_id', 'created_by', 'notes',
    ];

    protected $casts = [
        'hutang_date' => 'date',
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
            if (!$model->hutang_number) {
                $year = date('Y');
                $last = static::whereYear('hutang_date', $year)->orderBy('id', 'desc')->first();
                $num = $last ? intval(substr($last->hutang_number, -3)) + 1 : 1;
                $model->hutang_number = 'HTG-' . $year . '-' . str_pad($num, 3, '0', STR_PAD_LEFT);
            }
        });
    }

    // Relationships
    public function payments()
    {
        return $this->hasMany(HutangPayment::class)->orderBy('payment_date');
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
