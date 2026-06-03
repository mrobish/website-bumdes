<?php

namespace App\Filament\Pages;

use App\Models\AuditLog;
use App\Models\User;
use Filament\Pages\Page;
use Illuminate\Pagination\LengthAwarePaginator;

class AuditLogPage extends Page
{
    protected static string $view = 'filament.pages.audit-log';
    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationGroup = '👤 User & Akses';
    protected static ?string $navigationLabel = 'Audit Log';
    protected static ?string $title = 'Jejak Audit';
    protected static ?int $navigationSort = 3;

    // Filters
    public string $dateFrom = '';
    public string $dateTo = '';
    public ?int $filterUserId = null;
    public string $filterAction = '';
    public string $filterTable = '';
    public string $search = '';

    // Data
    public int $totalRecords = 0;
    public bool $loaded = false;
    public int $currentPage = 1;

    // Expandable row
    public ?int $expandedLogId = null;

    // Internal paginator
    protected ?LengthAwarePaginator $paginator = null;

    public function mount(): void
    {
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
        $this->loadData();
    }

    public function loadData(): void
    {
        $query = AuditLog::with('user')->orderBy('created_at', 'desc');

        if ($this->dateFrom) {
            $query->where('created_at', '>=', $this->dateFrom . ' 00:00:00');
        }
        if ($this->dateTo) {
            $query->where('created_at', '<=', $this->dateTo . ' 23:59:59');
        }
        if ($this->filterUserId) {
            $query->where('user_id', $this->filterUserId);
        }
        if ($this->filterAction) {
            $query->where('action', $this->filterAction);
        }
        if ($this->filterTable) {
            $query->where('table_name', 'like', "%{$this->filterTable}%");
        }
        if ($this->search) {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('table_name', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        $this->totalRecords = $query->count();
        $this->paginator = $query->paginate(25, ['*'], 'page', $this->currentPage);
        $this->loaded = true;
    }

    public function getLogs(): array
    {
        return $this->paginator ? $this->paginator->items() : [];
    }

    public function getLogsPaginator(): ?LengthAwarePaginator
    {
        return $this->paginator;
    }

    public function goToPage(int $page): void
    {
        $this->currentPage = $page;
        $this->loadData();
    }

    public function toggleExpand(int $logId): void
    {
        $this->expandedLogId = ($this->expandedLogId === $logId) ? null : $logId;
    }

    public function resetFilters(): void
    {
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
        $this->filterUserId = null;
        $this->filterAction = '';
        $this->filterTable = '';
        $this->search = '';
        $this->currentPage = 1;
        $this->loaded = false;
        $this->loadData();
    }

    public function getUsers(): array
    {
        return User::orderBy('name')->pluck('name', 'id')->toArray();
    }

    public function getActionLabel(string $action): string
    {
        return match ($action) {
            'create' => 'Buat',
            'update' => 'Ubah',
            'delete' => 'Hapus',
            'void' => 'Void',
            'approve' => 'Setuju',
            'login' => 'Login',
            default => ucfirst($action),
        };
    }

    public function getActionColor(string $action): string
    {
        return match ($action) {
            'create' => 'success',
            'update' => 'warning',
            'delete' => 'danger',
            'void' => 'danger',
            'approve' => 'success',
            'login' => 'info',
            default => 'gray',
        };
    }

    public function getTotalPages(): int
    {
        return $this->paginator ? $this->paginator->lastPage() : 1;
    }
}
