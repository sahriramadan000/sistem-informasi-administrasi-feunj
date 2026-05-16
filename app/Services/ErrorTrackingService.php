<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Throwable;
use Carbon\Carbon;

class ErrorTrackingService
{
    /**
     * Generate unique error ID with timestamp and hash
     * Format: ERR_YYYYMMDD_HHMMSS_HASH
     */
    public static function generateErrorId(): string
    {
        $timestamp = Carbon::now()->format('YmdHis');
        $hash = substr(md5(uniqid(rand(), true)), 0, 8);
        return "ERR_{$timestamp}_{$hash}";
    }

    /**
     * Log error to JSON file and Laravel log
     * 
     * @param Throwable $exception
     * @param string $context - context of error (e.g., 'LetterImport', 'LetterController.store')
     * @param array $additionalData - additional context data
     * @return string - error ID
     */
    public static function logError(Throwable $exception, string $context = 'Unknown', array $additionalData = []): string
    {
        $errorId = self::generateErrorId();
        $userId = Auth::id() ?? null;
        $userName = Auth::user()?->name ?? 'System';

        // Prepare error data
        $errorData = [
            'error_id' => $errorId,
            'timestamp' => Carbon::now()->toIso8601String(),
            'user_id' => $userId,
            'user_name' => $userName,
            'context' => $context,
            'exception_type' => class_basename($exception),
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'stack_trace' => $exception->getTraceAsString(),
            'request_url' => request()->url(),
            'request_method' => request()->method(),
            'request_ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'query_parameters' => request()->query(),
            'form_data' => self::sanitizeFormData(request()->all()),
            'additional_data' => $additionalData,
        ];

        // Log to Laravel log file
        Log::error("[$context] {$exception->getMessage()}", [
            'error_id' => $errorId,
            'exception' => class_basename($exception),
            'file' => $exception->getFile() . ':' . $exception->getLine(),
        ]);

        // Log to JSON file
        self::writeJsonLog($errorData);

        return $errorId;
    }

    /**
     * Log successful operation for audit trail
     * 
     * @param string $action - action type (create, update, delete)
     * @param string $model - model name (Letter, User, etc.)
     * @param int|null $modelId - ID of the model
     * @param array $data - relevant data
     * @return void
     */
    public static function logAction(string $action, string $model, ?int $modelId = null, array $data = []): void
    {
        $userId = Auth::id() ?? null;
        $userName = Auth::user()?->name ?? 'System';

        $auditData = [
            'timestamp' => Carbon::now()->toIso8601String(),
            'user_id' => $userId,
            'user_name' => $userName,
            'action' => $action,
            'model' => $model,
            'model_id' => $modelId,
            'request_url' => request()->url(),
            'request_method' => request()->method(),
            'request_ip' => request()->ip(),
            'data' => $data,
        ];

        Log::info("[AUDIT] $action on $model", [
            'user_id' => $userId,
            'model_id' => $modelId,
        ]);

        self::writeAuditLog($auditData);
    }

    /**
     * Write error to JSON file
     */
    private static function writeJsonLog(array $errorData): void
    {
        $date = Carbon::now()->format('Y-m-d');
        $logDirectory = storage_path('app/error-logs');
        
        // Create directory if it doesn't exist
        if (!file_exists($logDirectory)) {
            mkdir($logDirectory, 0755, true);
        }

        $filename = "{$logDirectory}/errors-{$date}.json";
        
        $logs = [];
        if (file_exists($filename)) {
            $content = file_get_contents($filename);
            $logs = json_decode($content, true) ?? [];
        }

        $logs[] = $errorData;

        file_put_contents($filename, json_encode($logs, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), LOCK_EX);
    }

    /**
     * Write audit log to JSON file
     */
    private static function writeAuditLog(array $auditData): void
    {
        $date = Carbon::now()->format('Y-m-d');
        $logDirectory = storage_path('app/audit-logs');
        
        // Create directory if it doesn't exist
        if (!file_exists($logDirectory)) {
            mkdir($logDirectory, 0755, true);
        }

        $filename = "{$logDirectory}/audit-{$date}.json";
        
        $logs = [];
        if (file_exists($filename)) {
            $content = file_get_contents($filename);
            $logs = json_decode($content, true) ?? [];
        }

        $logs[] = $auditData;

        file_put_contents($filename, json_encode($logs, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), LOCK_EX);
    }

    /**
     * Retrieve errors for admin dashboard (14 day retention)
     */
    public static function getRecentErrors(int $days = 14): array
    {
        $logDirectory = storage_path('app/error-logs');
        $allErrors = [];

        if (!file_exists($logDirectory)) {
            return [];
        }

        $cutoffDate = Carbon::now()->subDays($days);
        $files = glob("{$logDirectory}/errors-*.json");

        foreach ($files as $file) {
            // Extract date from filename: errors-YYYY-MM-DD.json
            preg_match('/errors-(\d{4}-\d{2}-\d{2})\.json/', $file, $matches);
            if (!isset($matches[1])) {
                continue;
            }

            $fileDate = Carbon::parse($matches[1]);
            if ($fileDate->isBefore($cutoffDate)) {
                continue;
            }

            $content = file_get_contents($file);
            $errors = json_decode($content, true) ?? [];
            $allErrors = array_merge($allErrors, $errors);
        }

        // Sort by timestamp descending
        usort($allErrors, function ($a, $b) {
            return strtotime($b['timestamp']) - strtotime($a['timestamp']);
        });

        return $allErrors;
    }

    /**
     * Get single error by ID
     */
    public static function getErrorById(string $errorId): ?array
    {
        $errors = self::getRecentErrors();
        foreach ($errors as $error) {
            if ($error['error_id'] === $errorId) {
                return $error;
            }
        }
        return null;
    }

    /**
     * Cleanup old error logs (> 14 days)
     */
    public static function cleanupOldLogs(int $days = 14): void
    {
        $logDirectory = storage_path('app/error-logs');
        
        if (!file_exists($logDirectory)) {
            return;
        }

        $cutoffDate = Carbon::now()->subDays($days);
        $files = glob("{$logDirectory}/errors-*.json");

        foreach ($files as $file) {
            preg_match('/errors-(\d{4}-\d{2}-\d{2})\.json/', $file, $matches);
            if (!isset($matches[1])) {
                continue;
            }

            $fileDate = Carbon::parse($matches[1]);
            if ($fileDate->isBefore($cutoffDate)) {
                unlink($file);
                Log::info("Deleted old error log: {$file}");
            }
        }
    }

    /**
     * Sanitize form data to remove sensitive information
     */
    private static function sanitizeFormData(array $data): array
    {
        $sensitiveFields = ['password', 'password_confirmation', 'token', 'api_key', 'secret'];
        
        foreach ($data as $key => $value) {
            if (in_array(strtolower($key), $sensitiveFields)) {
                $data[$key] = '***REDACTED***';
            }
        }

        return $data;
    }
}
