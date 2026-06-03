<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FinancialTransaction;
use App\Models\BusinessUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncController extends Controller
{
    /**
     * Sync offline data to server
     */
    public function sync(Request $request)
    {
        try {
            $request->validate([
                'type' => 'required|string|in:transaction',
                'data' => 'required|array',
                'offline_id' => 'required|integer'
            ]);

            $type = $request->input('type');
            $data = $request->input('data');
            $offlineId = $request->input('offline_id');

            DB::beginTransaction();

            switch ($type) {
                case 'transaction':
                    $result = $this->syncTransaction($data, $offlineId);
                    break;
                
                default:
                    throw new \Exception("Unknown sync type: {$type}");
            }

            DB::commit();

            Log::info("Sync completed", [
                'type' => $type,
                'offline_id' => $offlineId,
                'server_id' => $result['id'] ?? null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disinkronkan',
                'data' => $result
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error("Sync failed", [
                'type' => $request->input('type'),
                'offline_id' => $request->input('offline_id'),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal sinkronisasi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sync single transaction
     */
    protected function syncTransaction(array $data, int $offlineId)
    {
        // Validate required fields
        if (empty($data['transaction_date']) || empty($data['type']) || empty($data['amount'])) {
            throw new \Exception('Missing required transaction fields');
        }

        // Check if already synced (by offline_id in reference)
        $existing = FinancialTransaction::where('reference', "OFFLINE-{$offlineId}")->first();
        if ($existing) {
            return [
                'id' => $existing->id,
                'status' => 'already_synced'
            ];
        }

        // Create transaction
        $transaction = FinancialTransaction::create([
            'transaction_date' => $data['transaction_date'],
            'type' => $data['type'],
            'business_unit_id' => $data['business_unit_id'] ?? 1, // Default to Induk
            'account_id' => $data['account_id'] ?? null,
            'counter_account_id' => $data['counter_account_id'] ?? null,
            'amount' => $data['amount'],
            'description' => $data['description'] ?? '',
            'reference' => "OFFLINE-{$offlineId}",
            'status' => 'pending', // Offline transactions need approval
            'notes' => ($data['notes'] ?? '') . " [Disync dari offline ID: {$offlineId}]",
            'created_by' => auth()->id() ?? 1,
        ]);

        return [
            'id' => $transaction->id,
            'transaction_number' => $transaction->transaction_number,
            'status' => 'synced'
        ];
    }

    /**
     * Get sync status
     */
    public function status()
    {
        $pendingTransactions = FinancialTransaction::where('reference', 'like', 'OFFLINE-%')
            ->where('status', 'pending')
            ->count();

        $syncedToday = FinancialTransaction::where('reference', 'like', 'OFFLINE-%')
            ->whereDate('created_at', today())
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'pending_approval' => $pendingTransactions,
                'synced_today' => $syncedToday,
                'last_sync' => FinancialTransaction::where('reference', 'like', 'OFFLINE-%')
                    ->latest()
                    ->value('updated_at')
            ]
        ]);
    }
}
