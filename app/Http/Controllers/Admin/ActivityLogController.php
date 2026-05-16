<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

/**
 * Controller untuk admin activity log dashboard
 */
class ActivityLogController extends Controller
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
     * Menampilkan dashboard activity logs dengan filter
     */
    public function index(Request $request)
    {
        try {
            $query = AuditLog::with('user');

            // Filter by action type
            if ($request->filled('action')) {
                $query->where('action', $request->action);
            }

            // Filter by model
            if ($request->filled('model')) {
                $query->where('model', $request->model);
            }

            // Filter by user
            if ($request->filled('user_id')) {
                $query->where('user_id', $request->user_id);
            }

            // Filter by date range
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            // Search by user name or model ID
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('user_name', 'like', "%{$search}%")
                      ->orWhere('model_id', 'like', "%{$search}%")
                      ->orWhereHas('user', function ($userQuery) use ($search) {
                          $userQuery->where('name', 'like', "%{$search}%")
                                    ->orWhere('email', 'like', "%{$search}%");
                      });
                });
            }

            // Paginate
            $perPage = $request->input('per_page', 25);
            $allowedPerPage = [10, 25, 50, 100];
            if (!in_array($perPage, $allowedPerPage)) {
                $perPage = 25;
            }

            $activityLogs = $query->orderBy('created_at', 'desc')->paginate($perPage);

            // Statistics
            $stats = [
                'total_today' => AuditLog::whereDate('created_at', today())->count(),
                'total_week' => AuditLog::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
                'total_month' => AuditLog::whereMonth('created_at', now()->month)->count(),
                'creates' => AuditLog::where('action', 'create')->count(),
                'updates' => AuditLog::where('action', 'update')->count(),
                'deletes' => AuditLog::where('action', 'delete')->count(),
            ];

            // Get unique models and users for filters
            $models = AuditLog::distinct()->pluck('model')->sort()->values();
            $users = User::orderBy('name')->get();

            return view('admin.activity-logs.index', [
                'activityLogs' => $activityLogs,
                'stats' => $stats,
                'models' => $models,
                'users' => $users,
            ]);
        } catch (\Throwable $e) {
            return $this->handleError($e, 'ActivityLogController.index', 'Gagal memuat activity logs.');
        }
    }

    /**
     * Menampilkan detail activity log tertentu
     */
    public function show($id)
    {
        try {
            $activityLog = AuditLog::with('user')->findOrFail($id);

            // Get related activities (same user, same model, within 1 hour)
            $relatedActivities = AuditLog::where('user_id', $activityLog->user_id)
                ->where('model', $activityLog->model)
                ->where('model_id', $activityLog->model_id)
                ->whereBetween('created_at', [
                    $activityLog->created_at->copy()->subHour(),
                    $activityLog->created_at->copy()->addHour(),
                ])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            return view('admin.activity-logs.show', compact('activityLog', 'relatedActivities'));
        } catch (\Throwable $e) {
            return $this->handleError($e, 'ActivityLogController.show', 'Gagal memuat detail activity log.');
        }
    }
}
