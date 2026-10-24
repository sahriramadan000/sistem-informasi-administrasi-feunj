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
            'total_all' => Legalization::count(),
            'revenue_all' => Legalization::sum('total_price'),
            'total_pages' => Legalization::sum('page_count'),
            'total_this_year' => Legalization::whereYear('date', $thisYear)->count(),
        ];

        // Breakdown per jenjang pendidikan
        $legalizationsByLevel = Legalization::selectRaw('education_level_id, COUNT(*) as count, SUM(total_price) as revenue')
            ->with('educationLevel')
            ->groupBy('education_level_id')
            ->orderBy('count', 'desc')
            ->get();

        // Tren per tahun
        $legalizationsByYear = Legalization::selectRaw('year, COUNT(*) as count, SUM(total_price) as revenue')
            ->groupBy('year')
            ->orderBy('year', 'desc')
            ->get();

        $recentLegalizations = Legalization::with(['educationLevel', 'creator'])
            ->orderBy('date', 'desc')
            ->orderBy('running_number', 'desc')
            ->take(6)
            ->get();

        return view('legalizations.dashboard', compact(
            'stats',
            'recentLegalizations',
            'legalizationsByLevel',
            'legalizationsByYear'
        ));
    }
}
