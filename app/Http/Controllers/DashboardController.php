<?php

namespace App\Http\Controllers;

use App\Models\Letter;
use App\Models\Master\LetterType;

/**
 * Controller untuk halaman dashboard
 */
class DashboardController extends Controller
{
    /**
     * Inisialisasi middleware
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Menampilkan halaman dashboard dengan statistik
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Statistik dasar
        $totalLetters = Letter::count();
        $currentYearLetters = Letter::where('year', date('Y'))->count();
        $currentMonthLetters = Letter::whereYear('created_at', date('Y'))
            ->whereMonth('created_at', date('m'))
            ->count();

        // Surat terbaru
        $recentLetters = Letter::with(['signatory', 'classification'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Statistik per tahun
        $lettersByYear = Letter::selectRaw('year, COUNT(*) as count')
            ->groupBy('year')
            ->orderBy('year', 'desc')
            ->limit(5)
            ->get();

        // Statistik surat per jenis (untuk semua waktu)
        $lettersByType = Letter::selectRaw('letter_type_id, COUNT(*) as count')
            ->with('letterType')
            ->groupBy('letter_type_id')
            ->orderBy('count', 'desc')
            ->get();

        // Statistik surat per jenis tahun ini
        $lettersByTypeThisYear = Letter::selectRaw('letter_type_id, COUNT(*) as count')
            ->with('letterType')
            ->where('year', date('Y'))
            ->groupBy('letter_type_id')
            ->orderBy('count', 'desc')
            ->get();

        // Hitung total surat untuk percentage
        $totalLettersThisYear = $lettersByTypeThisYear->sum('count');

        return view('dashboard.index', compact(
            'totalLetters',
            'currentYearLetters',
            'currentMonthLetters',
            'recentLetters',
            'lettersByYear',
            'lettersByType',
            'lettersByTypeThisYear',
            'totalLettersThisYear'
        ));
    }
}
