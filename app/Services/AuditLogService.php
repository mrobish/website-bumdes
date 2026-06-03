<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogService
{
    /**
     * Log a create action
     */
    public static function logCreate(string $table, int $recordId, array $newValues, ?Request $request = null): void
    {
        self::log('create', $table, $recordId, null, $newValues, $request);
    }

    /**
     * Log an update action
     */
    public static function logUpdate(string $table, int $recordId, array $oldValues, array $newValues, ?Request $request = null): void
    {
        self::log('update', $table, $recordId, $oldValues, $newValues, $request);
    }

    /**
     * Log a void action
     */
    public static function logVoid(string $table, int $recordId, array $oldValues, string $reason, ?Request $request = null): void
    {
        self::log('void', $table, $recordId, $oldValues, ['void_reason' => $reason], $request);
    }

    /**
     * Log an approval action
     */
    public static function logApproval(string $table, int $recordId, ?Request $request = null): void
    {
        self::log('approve', $table, $recordId, null, ['approved_at' => now()], $request);
    }

    /**
     * Core logging method
     */
    protected static function log(string $action, string $table, int $recordId, ?array $oldValues, ?array $newValues, ?Request $request): void
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'table_name' => $table,
            'record_id' => $recordId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => $request?->ip() ?? request()->ip(),
        ]);
    }

    /**
     * Get audit trail for a record
     */
    public static function getTrail(string $table, int $recordId): \Illuminate\Database\Eloquent\Collection
    {
        return AuditLog::where('table_name', $table)
            ->where('record_id', $recordId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get recent activity
     */
    public static function getRecent(int $limit = 20): \Illuminate\Database\Eloquent\Collection
    {
        return AuditLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
