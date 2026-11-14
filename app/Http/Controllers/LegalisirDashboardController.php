<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Legalization;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class LegalisirDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        // Pastikan user memiliki akses
        if (!auth()->user()->can_access_legalizations) {
            abort(403, 'Anda tidak memiliki akses ke modul ini.');
        }

        $today = Carbon::today();
        $thisMonth = Carbon::now()->month;
        $thisYear = Carbon::now()->year;

        // Filter Rentang Bulan untuk Rekap Pendapatan Bulanan (Default: Jan tahun ini s.d. bulan ini)
        $defaultStart = Carbon::now()->startOfYear();
        $defaultEnd = Carbon::now()->startOfMonth();

        try {
            $startDate = $request->filled('start_month')
                ? Carbon::createFromFormat('Y-m', $request->input('start_month'))->startOfMonth()
                : $defaultStart->copy();
        } catch (\Throwable $e) {
            $startDate = $defaultStart->copy();
        }

        try {
            $endDate = $request->filled('end_month')
                ? Carbon::createFromFormat('Y-m', $request->input('end_month'))->startOfMonth()
                : $defaultEnd->copy();
        } catch (\Throwable $e) {
            $endDate = $defaultEnd->copy();
        }

        if ($startDate->gt($endDate)) {
            [$startDate, $endDate] = [$endDate, $startDate];
        }

        $rangeStart = $startDate->copy()->startOfMonth();
        $rangeEnd = $endDate->copy()->endOfMonth();

        $monthlyAggregates = Legalization::selectRaw('YEAR(date) as yr, MONTH(date) as mo, COUNT(*) as tx_count, SUM(page_count) as total_pages, SUM(total_price) as total_revenue')
            ->whereBetween('date', [$rangeStart->toDateString(), $rangeEnd->toDateString()])
            ->groupByRaw('YEAR(date), MONTH(date)')
            ->get()
            ->keyBy(function ($row) {
                return sprintf('%04d-%02d', $row->yr, $row->mo);
            });

        $monthlyReport = [];
        $period = CarbonPeriod::create($rangeStart, '1 month', $endDate->copy()->startOfMonth());
        $totalRangeRevenue = 0;
        $totalRangeTx = 0;
        $totalRangePages = 0;

        foreach ($period as $monthDate) {
            $ym = $monthDate->format('Y-m');
            $agg = $monthlyAggregates->get($ym);
            $txCount = $agg ? (int) $agg->tx_count : 0;
            $pages = $agg ? (int) $agg->total_pages : 0;
            $revenue = $agg ? (int) $agg->total_revenue : 0;

            $totalRangeRevenue += $revenue;
            $totalRangeTx += $txCount;
            $totalRangePages += $pages;

            $monthlyReport[] = [
                'ym' => $ym,
                'label' => $monthDate->copy()->locale('id')->translatedFormat('F Y'),
                'short_label' => $monthDate->copy()->locale('id')->translatedFormat('M Y'),
                'start_date' => $monthDate->copy()->startOfMonth()->toDateString(),
                'end_date' => $monthDate->copy()->endOfMonth()->toDateString(),
                'tx_count' => $txCount,
                'total_pages' => $pages,
                'total_revenue' => $revenue,
                'share_percent' => 0,
            ];
        }

        $peakMonth = null;
        foreach ($monthlyReport as &$item) {
            $item['share_percent'] = $totalRangeRevenue > 0
                ? round(($item['total_revenue'] / $totalRangeRevenue) * 100, 1)
                : 0;
            if ($item['total_revenue'] > 0 && (!$peakMonth || $item['total_revenue'] > $peakMonth['total_revenue'])) {
                $peakMonth = $item;
            }
        }
        unset($item);

        $monthCount = count($monthlyReport);
        $monthlySummary = [
            'total_revenue' => $totalRangeRevenue,
            'total_transactions' => $totalRangeTx,
            'total_pages' => $totalRangePages,
            'month_count' => $monthCount,
            'avg_revenue' => $monthCount > 0 ? (int) round($totalRangeRevenue / $monthCount) : 0,
            'peak_month_label' => $peakMonth ? $peakMonth['label'] : '-',
            'peak_month_revenue' => $peakMonth ? $peakMonth['total_revenue'] : 0,
            'period_label' => $rangeStart->copy()->locale('id')->translatedFormat('F Y') . ' - ' . $rangeEnd->copy()->locale('id')->translatedFormat('F Y'),
            'date_range_param' => $rangeStart->toDateString() . ' to ' . $rangeEnd->toDateString(),
        ];

        $startMonth = $rangeStart->format('Y-m');
        $endMonth = $rangeEnd->format('Y-m');

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
            'legalizationsByYear',
            'startMonth',
            'endMonth',
            'monthlyReport',
            'monthlySummary'
        ));
    }
}
