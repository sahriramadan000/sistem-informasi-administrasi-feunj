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
        // Statistik dasar - hanya surat aktif
        $totalLetters = Letter::active()->count();
        $currentYearLetters = Letter::active()->where('year', date('Y'))->count();
        $currentMonthLetters = Letter::active()
            ->whereYear('created_at', date('Y'))
            ->whereMonth('created_at', date('m'))
            ->count();

        // Statistik surat yang dinonaktifkan
        $inactiveLetters = Letter::where('is_active', false)->count();
        $inactiveLettersThisYear = Letter::where('is_active', false)
            ->where('year', date('Y'))
            ->count();
        $inactiveLettersThisMonth = Letter::where('is_active', false)
            ->whereYear('created_at', date('Y'))
            ->whereMonth('created_at', date('m'))
            ->count();

        // Surat terbaru - hanya surat aktif
        $recentLetters = Letter::with(['signatory', 'classification'])
            ->active()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Statistik per tahun - hanya surat aktif
        $lettersByYear = Letter::selectRaw('year, COUNT(*) as count')
            ->active()
            ->groupBy('year')
            ->orderBy('year', 'desc')
            ->limit(5)
            ->get();

        // Statistik surat per jenis (untuk semua waktu) - hanya surat aktif
        $lettersByType = Letter::selectRaw('letter_type_id, COUNT(*) as count')
            ->with('letterType')
            ->active()
            ->groupBy('letter_type_id')
            ->orderBy('count', 'desc')
            ->get();

        // Statistik surat per jenis tahun ini - hanya surat aktif
        $lettersByTypeThisYear = Letter::selectRaw('letter_type_id, COUNT(*) as count')
            ->with('letterType')
            ->active()
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
            'inactiveLetters',
            'inactiveLettersThisYear',
            'inactiveLettersThisMonth',
            'recentLetters',
            'lettersByYear',
            'lettersByType',
            'lettersByTypeThisYear',
            'totalLettersThisYear'
        ));
    }
}
