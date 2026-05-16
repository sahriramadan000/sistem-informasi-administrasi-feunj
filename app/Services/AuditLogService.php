<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuditLogService
{
    /**
     * Log a user action for audit trail
     * 
     * @param string $action - action type (create, update, delete)
     * @param string $model - model name (Letter, User, etc.)
     * @param int|null $modelId - ID of the affected model
     * @param array $data - changes made
     * @return AuditLog
     */
    public static function log(string $action, string $model, ?int $modelId = null, array $data = []): AuditLog
    {
        $userId = Auth::id();
        $userName = Auth::user()?->name ?? 'System';

        $auditLog = AuditLog::create([
            'user_id' => $userId,
            'user_name' => $userName,
            'action' => $action,
            'model' => $model,
            'model_id' => $modelId,
            'data' => $data ?: null,
            'request_url' => request()->url(),
            'request_method' => request()->method(),
            'request_ip' => request()->ip(),
        ]);

        Log::info("[AUDIT] $action on $model", [
            'audit_log_id' => $auditLog->id,
            'user_id' => $userId,
            'model_id' => $modelId,
        ]);

        return $auditLog;
    }

    /**
     * Get audit logs for a specific model
     */
    public static function getModelHistory(string $model, int $modelId): array
    {
        return AuditLog::where('model', $model)
            ->where('model_id', $modelId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * Get audit logs for a specific user
     */
    public static function getUserHistory(int $userId, int $limit = 50): array
    {
        return AuditLog::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Get recent audit logs (for dashboard)
     */
    public static function getRecent(int $limit = 100): array
    {
        return AuditLog::orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }
}
