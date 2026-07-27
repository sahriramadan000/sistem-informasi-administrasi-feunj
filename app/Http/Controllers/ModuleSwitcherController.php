<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ModuleSwitcherController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();

        // Tampilkan halaman switcher jika punya >= 2 akses
        if ($user->hasMultipleModules()) {
            return view('module_switcher');
        }

        // Redirect langsung jika user hanya punya 1 akses
        if ($user->can_access_letters) {
            return redirect()->route('dashboard'); // Dashboard Persuratan
        }
        if ($user->can_access_legalizations) {
            return redirect()->route('legalizations.dashboard'); // Dashboard Legalisir
        }
        if ($user->can_access_room_reservations) {
            return redirect()->route('room-reservations.dashboard'); // Dashboard Peminjaman Ruang
        }
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard'); // Pengaturan Sistem
        }

        // Tidak punya akses
        abort(403, 'Anda tidak memiliki akses ke modul manapun. Hubungi Administrator.');
    }
}
