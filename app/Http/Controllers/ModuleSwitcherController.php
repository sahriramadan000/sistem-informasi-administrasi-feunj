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

        $accessCount = 0;
        if ($user->can_access_letters) $accessCount++;
        if ($user->can_access_legalizations) $accessCount++;
        if ($user->isAdmin()) $accessCount++;

        // Redirect langsung jika user hanya punya 1 akses
        if ($accessCount === 1) {
            if ($user->can_access_letters) {
                return redirect()->route('dashboard'); // Dashboard Persuratan
            }
            if ($user->can_access_legalizations) {
                return redirect()->route('legalizations.dashboard'); // Dashboard Legalisir
            }
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard'); // Pengaturan Sistem
            }
        }

        // Tampilkan halaman switcher jika punya >= 2 akses
        if ($accessCount >= 2) {
            return view('module_switcher');
        }

        // Tidak punya akses
        abort(403, 'Anda tidak memiliki akses ke modul manapun. Hubungi Administrator.');
    }
}
