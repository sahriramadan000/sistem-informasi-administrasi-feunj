<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ErrorTrackingService;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

/**
 * Controller untuk admin error log dashboard
 */
class ErrorLogController extends Controller
{
    /**
     * Inisialisasi middleware
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    /**
     * Menampilkan dashboard error logs dengan filter dan statistik
     */
    public function index(Request $request)
    {
        try {
            // Get errors dari service
            $allErrors = ErrorTrackingService::getRecentErrors(14);

            // Filter by type
            if ($request->filled('exception_type')) {
                $allErrors = array_filter($allErrors, function ($error) use ($request) {
                    return $error['exception_type'] === $request->exception_type;
                });
            }

            // Filter by date
            if ($request->filled('date_from')) {
                $dateFrom = Carbon::parse($request->date_from)->startOfDay();
                $allErrors = array_filter($allErrors, function ($error) use ($dateFrom) {
                    return Carbon::parse($error['timestamp']) >= $dateFrom;
                });
            }

            if ($request->filled('date_to')) {
                $dateTo = Carbon::parse($request->date_to)->endOfDay();
                $allErrors = array_filter($allErrors, function ($error) use ($dateTo) {
                    return Carbon::parse($error['timestamp']) <= $dateTo;
                });
            }

            // Filter by context
            if ($request->filled('context')) {
                $allErrors = array_filter($allErrors, function ($error) use ($request) {
                    return stripos($error['context'], $request->context) !== false;
                });
            }

            // Filter by user
            if ($request->filled('user_id')) {
                $allErrors = array_filter($allErrors, function ($error) use ($request) {
                    return $error['user_id'] === (int) $request->user_id;
                });
            }

            // Search by error ID or message
            if ($request->filled('search')) {
                $search = strtolower($request->search);
                $allErrors = array_filter($allErrors, function ($error) use ($search) {
                    return stripos($error['error_id'], $search) !== false ||
                           stripos($error['message'], $search) !== false;
                });
            }

            // Get statistics
            $stats = $this->getErrorStatistics($allErrors);

            // Paginate manually
            $perPage = 20;
            $currentPage = $request->get('page', 1);
            $totalErrors = count($allErrors);
            $totalPages = ceil($totalErrors / $perPage);

            // Reset array keys and slice
            $allErrors = array_values($allErrors);
            $paginatedErrors = array_slice($allErrors, ($currentPage - 1) * $perPage, $perPage);

            return view('admin.error-logs.index', [
                'errors' => $paginatedErrors,
                'stats' => $stats,
                'totalErrors' => $totalErrors,
                'currentPage' => $currentPage,
                'perPage' => $perPage,
                'totalPages' => $totalPages,
                'exceptionTypes' => $this->getExceptionTypes($allErrors),
            ]);
        } catch (\Throwable $e) {
            return $this->handleError($e, 'ErrorLogController.index', 'Gagal memuat error logs.');
        }
    }

    /**
     * Menampilkan detail error tertentu
     */
    public function show($errorId)
    {
        try {
            $error = ErrorTrackingService::getErrorById($errorId);

            if (!$error) {
                return redirect()->route('admin.error-logs.index')
                    ->with('error', 'Error log tidak ditemukan.');
            }

            // Get related audit logs (same user around same time)
            $relatedAudits = [];
            if ($error['user_id']) {
                $relatedAudits = AuditLog::where('user_id', $error['user_id'])
                    ->whereBetween('created_at', [
                        Carbon::parse($error['timestamp'])->subMinutes(5),
                        Carbon::parse($error['timestamp'])->addMinutes(5),
                    ])
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
                    ->get();
            }

            return view('admin.error-logs.show', compact('error', 'relatedAudits'));
        } catch (\Throwable $e) {
            return $this->handleError($e, 'ErrorLogController.show', 'Gagal memuat detail error.');
        }
    }

    /**
     * Menampilkan statistik errors
     */
    public function statistics()
    {
        try {
            $allErrors = ErrorTrackingService::getRecentErrors(14);
            $stats = $this->getErrorStatistics($allErrors);

            // Group by hour for chart
            $errorsByHour = $this->groupErrorsByTime($allErrors, 'hour', 24);
            $errorsByDay = $this->groupErrorsByTime($allErrors, 'day', 14);

            return view('admin.error-logs.statistics', [
                'stats' => $stats,
                'errorsByHour' => $errorsByHour,
                'errorsByDay' => $errorsByDay,
                'topErrors' => $this->getTopErrors($allErrors),
                'topUsers' => $this->getTopErrorUsers($allErrors),
            ]);
        } catch (\Throwable $e) {
            return $this->handleError($e, 'ErrorLogController.statistics', 'Gagal memuat statistik error.');
        }
    }

    /**
     * Get error statistics
     */
    private function getErrorStatistics(array $errors): array
    {
        $totalErrors = count($errors);
        $criticalErrors = count(array_filter($errors, function ($e) {
            return in_array($e['exception_type'], ['FatalError', 'ParseError', 'TypeError']);
        }));
        $warningErrors = count(array_filter($errors, function ($e) {
            return in_array($e['exception_type'], ['Warning', 'Notice', 'Deprecated']);
        }));

        return [
            'total' => $totalErrors,
            'critical' => $criticalErrors,
            'warning' => $warningErrors,
            'unique_types' => count(array_unique(array_column($errors, 'exception_type'))),
            'unique_users' => count(array_unique(array_filter(array_column($errors, 'user_id')))),
            'avg_per_day' => round($totalErrors / 14),
        ];
    }

    /**
     * Get unique exception types
     */
    private function getExceptionTypes(array $errors): array
    {
        $types = array_unique(array_column($errors, 'exception_type'));
        return array_combine($types, $types);
    }

    /**
     * Group errors by time period
     */
    private function groupErrorsByTime(array $errors, string $period, int $limit): array
    {
        $grouped = [];

        foreach ($errors as $error) {
            $time = Carbon::parse($error['timestamp']);

            if ($period === 'hour') {
                $key = $time->format('Y-m-d H:00');
            } else {
                $key = $time->format('Y-m-d');
            }

            $grouped[$key] = ($grouped[$key] ?? 0) + 1;
        }

        // Fill missing dates
        $result = [];
        $start = Carbon::now()->subDays($limit)->startOfDay();

        for ($i = 0; $i < $limit; $i++) {
            if ($period === 'hour') {
                for ($h = 0; $h < 24; $h++) {
                    $key = $start->copy()->addHours($h)->format('Y-m-d H:00');
                    $result[$key] = $grouped[$key] ?? 0;
                }
                $start->addDay();
            } else {
                $key = $start->format('Y-m-d');
                $result[$key] = $grouped[$key] ?? 0;
                $start->addDay();
            }
        }

        return $result;
    }

    /**
     * Get top occurring errors
     */
    private function getTopErrors(array $errors): array
    {
        $messages = [];
        foreach ($errors as $error) {
            $msg = $error['exception_type'] . ': ' . substr($error['message'], 0, 50);
            $messages[$msg] = ($messages[$msg] ?? 0) + 1;
        }

        arsort($messages);
        return array_slice($messages, 0, 5, true);
    }

    /**
     * Get users with most errors
     */
    private function getTopErrorUsers(array $errors): array
    {
        $users = [];
        foreach ($errors as $error) {
            if ($error['user_id']) {
                $key = $error['user_name'] . ' (' . $error['user_id'] . ')';
                $users[$key] = ($users[$key] ?? 0) + 1;
            }
        }

        arsort($users);
        return array_slice($users, 0, 5, true);
    }
}
