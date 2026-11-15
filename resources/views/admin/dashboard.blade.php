@extends('layouts.app')

@section('title', 'Dashboard Pengaturan Sistem - FEB UNJ')

@push('styles')
    <style>
        .mac-card {
            background: #ffffff;
            border-radius: 1.25rem;
            border: 1px solid rgba(226, 232, 240, 0.85);
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.03), 0 2px 6px -1px rgba(0, 0, 0, 0.02);
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .mac-card:hover {
            border-color: rgba(254, 215, 170, 0.85);
            box-shadow: 0 12px 28px -6px rgba(255, 77, 0, 0.06), 0 4px 10px -2px rgba(0, 0, 0, 0.02);
            transform: translateY(-2px);
        }
    </style>
@endpush

@section('content')
<div class="space-y-6 pb-12">
    {{-- Page Header (Apple macOS Toolbar Style) --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-gray-200/80">
        <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-[#FF4D00] to-amber-500 flex items-center justify-center text-white shadow-xs flex-shrink-0">
                <i data-lucide="settings" class="w-5 h-5"></i>
            </div>
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">Pengaturan Sistem</h1>
                <p class="text-xs sm:text-sm text-gray-500">
                    Selamat datang, <span class="font-semibold text-gray-700">{{ auth()->user()->name }}</span>. Kelola pengguna, hak akses modul, serta pantau log aktivitas &amp; error sistem.
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2.5">
            <span class="inline-flex items-center gap-2 px-3.5 py-2 rounded-full text-xs font-semibold bg-white border border-gray-200/90 text-gray-600 shadow-2xs">
                <i data-lucide="clock" class="w-3.5 h-3.5 text-[#FF4D00]"></i>
                <span>{{ now()->translatedFormat('d M Y') }} &bull; {{ now()->format('H:i') }} WIB</span>
            </span>
        </div>
    </div>

    {{-- ── Apple Bento Grid: 3 Key System Cards ── --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        {{-- Card 1: Total Pengguna --}}
        <div class="mac-card p-6 flex flex-col justify-between group">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-[#FF4D00] to-amber-500 flex items-center justify-center text-white shadow-xs group-hover:scale-105 transition-transform duration-200">
                        <i data-lucide="users" class="w-5 h-5"></i>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-orange-50 text-orange-700 border border-orange-200/70">
                        Akses &amp; Peran
                    </span>
                </div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Pengguna</p>
                <h3 class="text-3xl font-extrabold text-gray-900 tracking-tight mt-1">
                    {{ number_format($totalUsers ?? 0, 0, ',', '.') }}
                </h3>
                <p class="text-xs text-gray-500 mt-1.5">Akun terdaftar dengan hak akses modul SIADMIN FEB</p>
            </div>

            <div class="mt-5 pt-4 border-t border-gray-100 flex items-center justify-between">
                <a href="{{ route('master.users.index') }}"
                    class="inline-flex items-center gap-1.5 text-xs font-bold text-[#FF4D00] hover:text-[#e04400] transition-colors">
                    <span>Kelola Pengguna</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform"></i>
                </a>
                <a href="{{ route('master.users.create') }}"
                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-orange-50 text-orange-700 hover:bg-orange-100 transition-colors">
                    <i data-lucide="user-plus" class="w-3 h-3"></i>
                    <span>Tambah</span>
                </a>
            </div>
        </div>

        {{-- Card 2: Activity Logs --}}
        <div class="mac-card p-6 flex flex-col justify-between group">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center text-white shadow-xs group-hover:scale-105 transition-transform duration-200">
                        <i data-lucide="activity" class="w-5 h-5"></i>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/70">
                        Audit Trail
                    </span>
                </div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Activity Logs</p>
                <h3 class="text-3xl font-extrabold text-gray-900 tracking-tight mt-1">
                    {{ number_format($totalActivityLogs ?? 0, 0, ',', '.') }}
                </h3>
                <p class="text-xs text-gray-500 mt-1.5">Rekam jejak aktivitas pengguna, perubahan data &amp; ekspor</p>
            </div>

            <div class="mt-5 pt-4 border-t border-gray-100 flex items-center justify-between">
                <a href="{{ route('admin.activity-logs.index') }}"
                    class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-600 hover:text-emerald-700 transition-colors">
                    <span>Lihat Activity Logs</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform"></i>
                </a>
                <span class="text-[11px] font-medium text-gray-400">Real-time</span>
            </div>
        </div>

        {{-- Card 3: Error Logs --}}
        <div class="mac-card p-6 flex flex-col justify-between group">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-rose-500 to-red-600 flex items-center justify-center text-white shadow-xs group-hover:scale-105 transition-transform duration-200">
                        <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200/70">
                        Diagnostik
                    </span>
                </div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Error Logs</p>
                <h3 class="text-3xl font-extrabold text-gray-900 tracking-tight mt-1">
                    {{ number_format($totalErrorLogs ?? 0, 0, ',', '.') }}
                </h3>
                <p class="text-xs text-gray-500 mt-1.5">Catatan pengecualian sistem &amp; status penyelesaiannya</p>
            </div>

            <div class="mt-5 pt-4 border-t border-gray-100 flex items-center justify-between">
                <a href="{{ route('admin.error-logs.index') }}"
                    class="inline-flex items-center gap-1.5 text-xs font-bold text-rose-600 hover:text-rose-700 transition-colors">
                    <span>Lihat Error Logs</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform"></i>
                </a>
                <a href="{{ route('admin.error-logs.statistics') }}"
                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-rose-50 text-rose-700 hover:bg-rose-100 transition-colors">
                    <i data-lucide="bar-chart-2" class="w-3 h-3"></i>
                    <span>Statistik</span>
                </a>
            </div>
        </div>
    </div>

    {{-- ── Quick Actions Card ── --}}
    <div class="mac-card p-6">
        <div class="flex items-center gap-2.5 pb-4 mb-4 border-b border-gray-100">
            <div class="w-8 h-8 rounded-xl bg-slate-100 border border-slate-200/80 flex items-center justify-center text-slate-600">
                <i data-lucide="zap" class="w-4 h-4"></i>
            </div>
            <div>
                <h2 class="text-sm font-bold text-gray-900">Akses Cepat Administrator</h2>
                <p class="text-[11px] text-gray-500">Pintasan menu yang paling sering digunakan dalam pengelolaan sistem</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <a href="{{ route('master.users.index') }}"
                class="flex items-center gap-3 p-3.5 rounded-2xl bg-gray-50/80 hover:bg-orange-50/60 border border-gray-200/70 hover:border-orange-200 transition-all group">
                <div class="w-9 h-9 rounded-xl bg-white border border-gray-200/80 text-[#FF4D00] flex items-center justify-center shadow-2xs group-hover:scale-105 transition-transform">
                    <i data-lucide="users" class="w-4 h-4"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-900">Daftar Pengguna</p>
                    <p class="text-[11px] text-gray-500">Atur role &amp; akses modul</p>
                </div>
            </a>

            <a href="{{ route('master.users.create') }}"
                class="flex items-center gap-3 p-3.5 rounded-2xl bg-gray-50/80 hover:bg-orange-50/60 border border-gray-200/70 hover:border-orange-200 transition-all group">
                <div class="w-9 h-9 rounded-xl bg-white border border-gray-200/80 text-amber-600 flex items-center justify-center shadow-2xs group-hover:scale-105 transition-transform">
                    <i data-lucide="user-plus" class="w-4 h-4"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-900">Tambah Pengguna</p>
                    <p class="text-[11px] text-gray-500">Buat akun pegawai baru</p>
                </div>
            </a>

            <a href="{{ route('admin.activity-logs.index') }}"
                class="flex items-center gap-3 p-3.5 rounded-2xl bg-gray-50/80 hover:bg-emerald-50/60 border border-gray-200/70 hover:border-emerald-200 transition-all group">
                <div class="w-9 h-9 rounded-xl bg-white border border-gray-200/80 text-emerald-600 flex items-center justify-center shadow-2xs group-hover:scale-105 transition-transform">
                    <i data-lucide="history" class="w-4 h-4"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-900">Riwayat Aktivitas</p>
                    <p class="text-[11px] text-gray-500">Audit tindakan pengguna</p>
                </div>
            </a>

            <a href="{{ route('admin.error-logs.statistics') }}"
                class="flex items-center gap-3 p-3.5 rounded-2xl bg-gray-50/80 hover:bg-rose-50/60 border border-gray-200/70 hover:border-rose-200 transition-all group">
                <div class="w-9 h-9 rounded-xl bg-white border border-gray-200/80 text-rose-600 flex items-center justify-center shadow-2xs group-hover:scale-105 transition-transform">
                    <i data-lucide="bar-chart-3" class="w-4 h-4"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-900">Statistik Error</p>
                    <p class="text-[11px] text-gray-500">Analisis tren &amp; keparahan</p>
                </div>
            </a>

            <a href="{{ route('admin.gdrive.index') }}"
                class="flex items-center gap-3 p-3.5 rounded-2xl bg-gray-50/80 hover:bg-blue-50/60 border border-gray-200/70 hover:border-blue-200 transition-all group sm:col-span-2 lg:col-span-4">
                <div class="w-9 h-9 rounded-xl bg-white border border-gray-200/80 text-blue-600 flex items-center justify-center shadow-2xs group-hover:scale-105 transition-transform">
                    <i data-lucide="hard-drive" class="w-4 h-4"></i>
                </div>
                <div class="flex-1">
                    <p class="text-xs font-bold text-gray-900">Integrasi Google Drive (Arsip Satu Pintu)</p>
                    <p class="text-[11px] text-gray-500">Hubungkan akun Google 5 TB, atur Root Folder, cek kuota &amp; koneksi</p>
                </div>
                <i data-lucide="arrow-right" class="w-4 h-4 text-blue-500 group-hover:translate-x-1 transition-transform"></i>
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
@endpush
