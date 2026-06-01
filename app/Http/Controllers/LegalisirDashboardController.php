<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Legalization;
use Carbon\Carbon;

class LegalisirDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // Pastikan user memiliki akses
        if (!auth()->user()->can_access_legalizations) {
            abort(403, 'Anda tidak memiliki akses ke modul ini.');
        }

        $today = Carbon::today();
        $thisMonth = Carbon::now()->month;
        $thisYear = Carbon::now()->year;

        $stats = [
            'total_today' => Legalization::whereDate('date', $today)->count(),
            'total_this_month' => Legalization::whereMonth('date', $thisMonth)
                                            ->whereYear('date', $thisYear)
                                            ->count(),
            'revenue_today' => Legalization::whereDate('date', $today)->sum('total_price'),
            'revenue_this_month' => Legalization::whereMonth('date', $thisMonth)
                                              ->whereYear('date', $thisYear)
                                              ->sum('total_price'),
        ];

        $recentLegalizations = Legalization::with(['educationLevel', 'creator'])
                                         ->orderBy('created_at', 'desc')
                                         ->take(5)
                                         ->get();

        return view('legalizations.dashboard', compact('stats', 'recentLegalizations'));
    }
}
