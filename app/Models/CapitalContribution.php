<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CapitalContribution extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'contribution_number', 'source_type', 'source_name', 'source_address',
        'source_contact', 'source_phone', 'source_email', 'contribution_type',
        'form', 'amount', 'goods_description', 'goods_value', 'total_value',
        'contribution_year', 'disbursement_date', 'received_date', 'interest_rate',
        'loan_term_months', 'first_payment_date', 'monthly_payment', 'repayment_terms',
        'purpose', 'restrictions', 'is_restricted', 'status', 'agreement_number',
        'agreement_date', 'attachments', 'business_unit_id', 'created_by',
        'approved_by', 'approved_at', 'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'goods_value' => 'decimal:2',
        'total_value' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'monthly_payment' => 'decimal:2',
        'disbursement_date' => 'date',
        'received_date' => 'date',
        'first_payment_date' => 'date',
        'agreement_date' => 'date',
        'approved_at' => 'date',
        'is_restricted' => 'boolean',
        'attachments' => 'array',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->contribution_number)) {
                $year = date('Y');
                $last = self::whereYear('created_at', $year)->count() + 1;
                $model->contribution_number = 'PM-' . $year . '-' . str_pad($last, 3, '0', STR_PAD_LEFT);
            }
            // Auto-calculate total value
            $model->total_value = $model->amount + $model->goods_value;
        });
    }

    // Relationships
    public function businessUnit(): BelongsTo { return $this->belongsTo(BusinessUnit::class); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function approver(): BelongsTo { return $this->belongsTo(User::class, 'approved_by'); }

    // Scopes
    public function scopeActive($query) { return $query->whereIn('status', ['active', 'received', 'disbursed']); }
    public function scopeHibah($query) { return $query->where('contribution_type', 'hibah'); }
    public function scopePinjaman($query) { return $query->whereIn('contribution_type', ['pinjaman', 'pinjaman_bunga']); }
    public function scopeForYear($query, int $year) { return $query->where('contribution_year', $year); }
    public function scopeFromDesa($query) { return $query->where('source_type', 'desa'); }

    // Helpers
    public function isHibah(): bool { return $this->contribution_type === 'hibah'; }
    public function isPinjaman(): bool { return in_array($this->contribution_type, ['pinjaman', 'pinjaman_bunga']); }
    public function isUang(): bool { return $this->form === 'uang'; }
    public function isBarang(): bool { return $this->form === 'barang'; }
    public function isCompleted(): bool { return $this->status === 'completed'; }

    // Sisa pinjaman
    public function getRemainingLoan(): float
    {
        if (!$this->isPinjaman()) return 0;
        $paid = $this->loanPayments()->sum('amount');
        return $this->total_value - $paid;
    }

    // Total cicilan yang sudah dibayar
    public function getPaidAmount(): float
    {
        return $this->loanPayments()->sum('amount');
    }

    // Formatted attributes
    public function getSourceTypeLabelAttribute(): string
    {
        return match($this->source_type) {
            'desa' => 'Desa',
            'pemda_kab_kot' => 'Pemda Kab/Kota',
            'pemprov' => 'Pemprov',
            'kementerian' => 'Kementerian',
            'lembaga_negara' => 'Lembaga Negara',
            'lembaga_swasta' => 'Lembaga Swasta / CSR',
            'perorangan' => 'Perorangan',
            'bantuan_luar_negeri' => 'Bantuan Luar Negeri',
            'lainnya' => 'Lainnya',
        };
    }

    public function getContributionTypeLabelAttribute(): string
    {
        return match($this->contribution_type) {
            'hibah' => 'Hibah (Grant)',
            'pinjaman' => 'Pinjaman (Loan)',
            'pinjaman_bunga' => 'Pinjaman Berbunga',
            'penyertaan_saham' => 'Penyertaan Saham',
        };
    }

    public function getFormLabelAttribute(): string
    {
        return match($this->form) {
            'uang' => 'Uang Tunai',
            'barang' => 'Barang Berupa',
            'uang_dan_barang' => 'Uang dan Barang',
            'jasa' => 'Jasa/Layanan',
        };
    }

    public function getFormattedAmountAttribute(): string { return 'Rp ' . number_format($this->amount, 0, ',', '.'); }
    public function getFormattedTotalAttribute(): string { return 'Rp ' . number_format($this->total_value, 0, ',', '.'); }
}
