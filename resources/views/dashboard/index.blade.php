@extends('layouts.app')

@section('title', 'Dashboard - FEB UNJ')

@push('styles')
<style>
    /* Apple macOS / iOS Card Styling */
    .mac-card {
        background: #ffffff;
        border-radius: 1.25rem;
        border: 1px solid rgba(226, 232, 240, 0.85);
        box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.03), 0 2px 6px -1px rgba(0, 0, 0, 0.02);
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .mac-card:hover {
        border-color: rgba(254, 215, 170, 0.8);
        box-shadow: 0 10px 25px -4px rgba(255, 77, 0, 0.05), 0 4px 10px -2px rgba(0, 0, 0, 0.02);
    }

    /* Live pulsing dot */
    @keyframes livePulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.4; transform: scale(1.2); }
    }
    .live-dot {
        animation: livePulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    /* Custom sleek scrollbar */
    .custom-scrollbar {
        scrollbar-width: thin;
        scrollbar-color: #cbd5e1 #f8fafc;
    }
    .custom-scrollbar::-webkit-scrollbar {
        width: 5px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f8fafc;
        border-radius: 9999px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 9999px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>
@endpush

@section('content')
<div class="space-y-6 pb-12">

    {{-- ── Apple Widgets: 4 Key Statistics Cards ── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-5">
        {{-- Card 1: Total Surat Terbit (Orange) --}}
        <div class="mac-card p-5 group cursor-default">
            <div class="flex items-center justify-between mb-3.5">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-orange-500 to-amber-500 flex items-center justify-center text-white shadow-xs group-hover:scale-105 transition-transform duration-200">
                    <i data-lucide="mail" class="w-5 h-5"></i>
                </div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-orange-50 text-orange-700 border border-orange-200/70">
                    Katalog Aktif
                </span>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Surat Terbit</p>
                <h3 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight mt-1">
                    {{ number_format($totalLetters, 0, ',', '.') }}
                </h3>
                <p class="text-[11px] text-gray-500 mt-2 flex items-center gap-1.5">
                    <i data-lucide="check-circle" class="w-3.5 h-3.5 text-emerald-500 flex-shrink-0"></i>
                    <span>Tervalidasi dalam sistem digital</span>
                </p>
            </div>
        </div>

        {{-- Card 2: Surat Tahun Berjalan (Emerald) --}}
        <div class="mac-card p-5 group cursor-default">
            <div class="flex items-center justify-between mb-3.5">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center text-white shadow-xs group-hover:scale-105 transition-transform duration-200">
                    <i data-lucide="calendar" class="w-5 h-5"></i>
                </div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/70">
                    Tahun {{ now()->year }}
                </span>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Surat Tahun Ini</p>
                <h3 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight mt-1">
                    {{ number_format($currentYearLetters, 0, ',', '.') }}
                </h3>
                <p class="text-[11px] text-gray-500 mt-2 flex items-center gap-1.5">
                    <span class="font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.2 rounded border border-emerald-200">
                        {{ $totalLetters > 0 ? round(($currentYearLetters / $totalLetters) * 100, 1) : 0 }}%
                    </span>
                    <span>dari seluruh arsip tersimpan</span>
                </p>
            </div>
        </div>

        {{-- Card 3: Surat Bulan Berjalan (Azure Blue) --}}
        <div class="mac-card p-5 group cursor-default">
            <div class="flex items-center justify-between mb-3.5">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-500 flex items-center justify-center text-white shadow-xs group-hover:scale-105 transition-transform duration-200">
                    <i data-lucide="calendar-days" class="w-5 h-5"></i>
                </div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200/70">
                    {{ now()->translatedFormat('F') }}
                </span>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Surat Bulan Ini</p>
                <h3 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight mt-1">
                    {{ number_format($currentMonthLetters, 0, ',', '.') }}
                </h3>
                <p class="text-[11px] text-gray-500 mt-2 flex items-center gap-1.5">
                    <i data-lucide="clock" class="w-3.5 h-3.5 text-blue-500 flex-shrink-0"></i>
                    <span>Aktivitas periode berjalan</span>
                </p>
            </div>
        </div>

        {{-- Card 4: Surat Dinonaktifkan (Purple) --}}
        <div class="mac-card p-5 group cursor-default">
            <div class="flex items-center justify-between mb-3.5">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-purple-500 to-violet-600 flex items-center justify-center text-white shadow-xs group-hover:scale-105 transition-transform duration-200">
                    <i data-lucide="archive" class="w-5 h-5"></i>
                </div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-purple-50 text-purple-700 border border-purple-200/70">
                    Arsip Nonaktif
                </span>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Surat Dinonaktifkan</p>
                <h3 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight mt-1">
                    {{ number_format($inactiveLetters, 0, ',', '.') }}
                </h3>
                <p class="text-[11px] text-gray-500 mt-2 flex items-center gap-1.5">
                    <span class="text-purple-700 font-medium">
                        {{ $inactiveLettersThisYear }} dinonaktifkan th. {{ now()->year }}
                    </span>
                </p>
            </div>
        </div>
    </div>

    {{-- ── 3. Apple macOS Control Center: Quick Action Tiles (Admin & Operator Only) ── --}}
    @if (auth()->user()->isAdmin() || auth()->user()->isOperator())
        <div class="mac-card p-5 sm:p-6">
            <div class="flex items-center justify-between pb-3.5 mb-4 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-xl bg-orange-50 border border-orange-200 flex items-center justify-center text-orange-600 shadow-2xs">
                        <i data-lucide="zap" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-gray-900">Aksi Cepat Manajemen Persuratan</h3>
                        <p class="text-[11px] text-gray-500">Pintasan menu master data dan administrasi naskah dinas</p>
                    </div>
                </div>
                <span class="hidden sm:inline-flex items-center gap-1 text-[11px] font-medium text-gray-400">
                    Pintasan Navigasi
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                {{-- Klasifikasi Surat --}}
                <a href="{{ route('master.classification-letters.index') }}"
                    class="flex items-center p-3.5 rounded-2xl bg-gray-50/70 hover:bg-white border border-gray-200/70 hover:border-orange-200 hover:shadow-xs transition-all duration-150 group">
                    <div class="w-10 h-10 rounded-xl bg-orange-100/80 text-orange-600 flex items-center justify-center mr-3 flex-shrink-0 group-hover:scale-105 transition-transform">
                        <i data-lucide="tags" class="w-5 h-5"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-bold text-gray-900 truncate group-hover:text-orange-600 transition-colors">Klasifikasi Surat</p>
                        <p class="text-[11px] text-gray-400 truncate">Kode &amp; penomoran arsip</p>
                    </div>
                    <i data-lucide="chevron-right" class="w-4 h-4 text-gray-300 group-hover:text-orange-500 group-hover:translate-x-0.5 transition-all"></i>
                </a>

                {{-- Jenis Surat --}}
                <a href="{{ route('master.letter-types.index') }}"
                    class="flex items-center p-3.5 rounded-2xl bg-gray-50/70 hover:bg-white border border-gray-200/70 hover:border-emerald-200 hover:shadow-xs transition-all duration-150 group">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100/80 text-emerald-600 flex items-center justify-center mr-3 flex-shrink-0 group-hover:scale-105 transition-transform">
                        <i data-lucide="file-text" class="w-5 h-5"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-bold text-gray-900 truncate group-hover:text-emerald-600 transition-colors">Jenis Surat</p>
                        <p class="text-[11px] text-gray-400 truncate">Format &amp; template dinas</p>
                    </div>
                    <i data-lucide="chevron-right" class="w-4 h-4 text-gray-300 group-hover:text-emerald-500 group-hover:translate-x-0.5 transition-all"></i>
                </a>

                {{-- Penandatangan --}}
                <a href="{{ route('master.signatories.index') }}"
                    class="flex items-center p-3.5 rounded-2xl bg-gray-50/70 hover:bg-white border border-gray-200/70 hover:border-blue-200 hover:shadow-xs transition-all duration-150 group">
                    <div class="w-10 h-10 rounded-xl bg-blue-100/80 text-blue-600 flex items-center justify-center mr-3 flex-shrink-0 group-hover:scale-105 transition-transform">
                        <i data-lucide="user-check" class="w-5 h-5"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-bold text-gray-900 truncate group-hover:text-blue-600 transition-colors">Penandatangan</p>
                        <p class="text-[11px] text-gray-400 truncate">Pejabat struktural FEB</p>
                    </div>
                    <i data-lucide="chevron-right" class="w-4 h-4 text-gray-300 group-hover:text-blue-500 group-hover:translate-x-0.5 transition-all"></i>
                </a>

                {{-- Buat Surat Baru --}}
                <a href="{{ route('letters.create') }}"
                    class="flex items-center p-3.5 rounded-2xl bg-gradient-to-r from-orange-50 to-amber-50/80 hover:bg-orange-100/60 border border-orange-200/90 hover:shadow-xs transition-all duration-150 group">
                    <div class="w-10 h-10 rounded-xl text-white flex items-center justify-center mr-3 flex-shrink-0 shadow-2xs group-hover:scale-105 transition-transform"
                        style="background: linear-gradient(135deg, #FF4D00, #ea580c);">
                        <i data-lucide="plus" class="w-5 h-5"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-bold text-orange-700 truncate">Buat Surat Baru</p>
                        <p class="text-[11px] text-orange-600/80 truncate">Registrasi surat keluar</p>
                    </div>
                    <i data-lucide="arrow-right" class="w-4 h-4 text-orange-500 group-hover:translate-x-0.5 transition-transform"></i>
                </a>
            </div>
        </div>
    @endif

    {{-- ── 4. Main 2-Column Bento Grid: Analisis & Histori (Kiri) vs Performa Kontributor (Kanan) ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        {{-- ── LEFT COLUMN (7 Cols): 3 Cards (Distribusi, Statistik per Tahun, Registrasi Surat Terbaru) ── --}}
        <div id="dashboard-left-column" class="lg:col-span-7 space-y-6">

            {{-- 🌟 CARD 1: Distribusi Surat per Jenis --}}
            <div class="mac-card p-5 sm:p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-3.5 mb-4 border-b border-gray-100 gap-2">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-orange-50 border border-orange-200 flex items-center justify-center text-orange-600 shadow-2xs">
                            <i data-lucide="pie-chart" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-gray-900">Distribusi Surat per Jenis (Tahun {{ now()->year }})</h3>
                            <p class="text-[11px] text-gray-500">Komposisi surat dinas terbit pada tahun berjalan</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-orange-50 text-orange-800 border border-orange-200/80 shadow-2xs self-start sm:self-auto">
                        Total: {{ number_format($totalLettersThisYear, 0, ',', '.') }} surat
                    </span>
                </div>

                @if ($lettersByTypeThisYear->count() > 0)
                    @php
                        $palette = ['#FF4D00', '#3b82f6', '#10b981', '#8b5cf6', '#f59e0b', '#06b6d4', '#ec4899', '#64748b'];
                    @endphp

                    {{-- Multi-Segment Apple Activity Progress Bar --}}
                    <div class="mb-4">
                        <div class="w-full bg-gray-100 rounded-full h-3 flex overflow-hidden p-0.5 gap-0.5 shadow-inner">
                            @foreach ($lettersByTypeThisYear as $index => $stat)
                                @php
                                    $pct = $totalLettersThisYear > 0 ? ($stat->count / $totalLettersThisYear) * 100 : 0;
                                    $color = $palette[$index % count($palette)];
                                @endphp
                                @if($pct > 0)
                                    <div class="h-full rounded-full transition-all duration-500"
                                        style="width: {{ $pct }}%; background-color: {{ $color }};"
                                        title="{{ $stat->letterType->name ?? 'Jenis Surat' }}: {{ $stat->count }} surat ({{ round($pct, 1) }}%)">
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    {{-- Breakdown Table --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead>
                                <tr class="text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                                    <th class="pb-2.5 pl-2">Jenis Surat</th>
                                    <th class="pb-2.5 text-center">Jumlah</th>
                                    <th class="pb-2.5 text-center">Porsi</th>
                                    <th class="pb-2.5 pr-2 hidden sm:table-cell">Visual Distribusi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-xs">
                                @foreach ($lettersByTypeThisYear as $index => $stat)
                                    @php
                                        $percentage = $totalLettersThisYear > 0 ? round(($stat->count / $totalLettersThisYear) * 100, 1) : 0;
                                        $color = $palette[$index % count($palette)];
                                    @endphp
                                    <tr class="hover:bg-orange-50/20 transition-colors">
                                        <td class="py-2.5 pl-2 font-medium text-gray-900 flex items-center gap-2">
                                            <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background-color: {{ $color }};"></span>
                                            <span class="truncate max-w-[200px]" title="{{ $stat->letterType->name ?? '-' }}">{{ $stat->letterType->name ?? '-' }}</span>
                                        </td>
                                        <td class="py-2.5 text-center font-bold text-gray-900">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-800">
                                                {{ $stat->count }}
                                            </span>
                                        </td>
                                        <td class="py-2.5 text-center text-gray-600 font-mono font-medium">
                                            {{ $percentage }}%
                                        </td>
                                        <td class="py-2.5 pr-2 hidden sm:table-cell">
                                            <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                                <div class="h-full rounded-full transition-all duration-500" style="width: {{ $percentage }}%; background-color: {{ $color }};"></div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="w-12 h-12 rounded-2xl bg-gray-100 text-gray-400 flex items-center justify-center mx-auto mb-3">
                            <i data-lucide="pie-chart" class="w-6 h-6"></i>
                        </div>
                        <h4 class="text-sm font-semibold text-gray-800">Belum ada aktivitas surat tahun ini</h4>
                        <p class="text-xs text-gray-400 mt-1 max-w-sm mx-auto">Surat yang didaftarkan pada tahun {{ now()->year }} akan langsung muncul pada grafik distribusi.</p>
                    </div>
                @endif
            </div>

            {{-- 🌟 CARD 2: Statistik per Tahun --}}
            <div class="mac-card p-5 sm:p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-3.5 mb-4 border-b border-gray-100 gap-2">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-orange-50 border border-orange-200 flex items-center justify-center text-orange-600 shadow-2xs">
                            <i data-lucide="bar-chart-3" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-gray-900">Statistik per Tahun</h3>
                            <p class="text-[11px] text-gray-500">Perbandingan volume arsip naskah dinas antar tahun buku</p>
                        </div>
                    </div>
                    @php
                        $sumLetters = max(1, $lettersByYear->sum('count'));
                        $maxYearCount = max(1, $lettersByYear->max('count'));
                    @endphp
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-orange-50 text-orange-800 border border-orange-200/80 shadow-2xs self-start sm:self-auto">
                        Total: {{ number_format($sumLetters, 0, ',', '.') }} surat
                    </span>
                </div>

                @if ($lettersByYear->count() > 0)
                    {{-- Breakdown Table --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100 text-xs">
                            <thead>
                                <tr class="text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                                    <th class="pb-2.5 pl-2">Tahun Arsip</th>
                                    <th class="pb-2.5 text-center">Volume</th>
                                    <th class="pb-2.5 text-center">Porsi</th>
                                    <th class="pb-2.5 pr-2 hidden sm:table-cell">Visual Distribusi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($lettersByYear as $stat)
                                    @php
                                        $barPct = round(($stat->count / $maxYearCount) * 100, 1);
                                        $sharePct = round(($stat->count / $sumLetters) * 100, 1);
                                        $isCurrentYear = ($stat->year == now()->year);
                                    @endphp
                                    <tr class="hover:bg-orange-50/20 transition-colors group">
                                        <td class="py-3 pl-2 font-medium text-gray-900 flex items-center gap-2">
                                            <span class="w-2.5 h-2.5 rounded-full flex-shrink-0 {{ $isCurrentYear ? 'bg-orange-500 shadow-2xs' : 'bg-gray-300' }}"></span>
                                            <span class="font-bold text-gray-900">Tahun {{ $stat->year }}</span>
                                            @if($isCurrentYear)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-orange-100/90 text-orange-800 border border-orange-200 shadow-2xs">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-orange-500 live-dot"></span>
                                                    Tahun Ini
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3 text-center font-bold text-gray-900">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $isCurrentYear ? 'bg-white text-orange-700 border border-orange-200/80 shadow-2xs' : 'bg-gray-100 text-gray-800' }}">
                                                {{ number_format($stat->count, 0, ',', '.') }}
                                            </span>
                                        </td>
                                        <td class="py-3 text-center text-gray-600 font-mono font-medium">
                                            {{ $sharePct }}%
                                        </td>
                                        <td class="py-3 pr-2 hidden sm:table-cell">
                                            <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden shadow-inner">
                                                <div class="h-full rounded-full transition-all duration-500 {{ $isCurrentYear ? 'bg-gradient-to-r from-orange-500 to-amber-500' : 'bg-gray-400' }}"
                                                    style="width: {{ $barPct }}%;">
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Subtle Footer Indicator --}}
                    <div class="pt-3 mt-3 border-t border-gray-100 flex items-center justify-between text-[11px] text-gray-400">
                        <span>Tercatat {{ $lettersByYear->count() }} tahun buku arsip</span>
                        <span class="inline-flex items-center gap-1 text-gray-500 font-medium">
                            <i data-lucide="check-circle" class="w-3.5 h-3.5 text-emerald-500"></i>
                            Sinkronisasi data arsip aktif
                        </span>
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="w-12 h-12 rounded-2xl bg-gray-100 text-gray-400 flex items-center justify-center mx-auto mb-3">
                            <i data-lucide="bar-chart-3" class="w-6 h-6"></i>
                        </div>
                        <h4 class="text-sm font-semibold text-gray-800">Belum ada statistik tahunan</h4>
                        <p class="text-xs text-gray-400 mt-1 max-w-sm mx-auto">Statistik volume naskah dinas akan terakumulasi otomatis saat surat didaftarkan.</p>
                    </div>
                @endif
            </div>

            {{-- 🌟 CARD 3: Registrasi Surat Terbaru --}}
            <div class="mac-card p-5 sm:p-6">
                <div class="flex items-center justify-between pb-3.5 mb-4 border-b border-gray-100">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-orange-50 border border-orange-200 flex items-center justify-center text-orange-600 shadow-2xs">
                            <i data-lucide="clock" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-gray-900">Registrasi Surat Terbaru</h3>
                            <p class="text-[11px] text-gray-500">5 naskah dinas terakhir yang terbit dalam sistem</p>
                        </div>
                    </div>
                    <a href="{{ route('letters.index') }}"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold text-orange-600 hover:text-orange-700 bg-orange-50 hover:bg-orange-100/80 border border-orange-200/80 transition-colors">
                        <span>Lihat Semua</span>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                    </a>
                </div>

                @if ($recentLetters->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100 text-xs">
                            <thead>
                                <tr class="text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                                    <th class="pb-2.5 pl-2">Nomor Surat</th>
                                    <th class="pb-2.5">Perihal</th>
                                    <th class="pb-2.5 hidden sm:table-cell">Penandatangan</th>
                                    <th class="pb-2.5 text-center">Tanggal</th>
                                    <th class="pb-2.5 text-right pr-2">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($recentLetters as $letter)
                                    <tr class="hover:bg-orange-50/20 transition-colors group">
                                        <td class="py-3 pl-2 font-mono whitespace-nowrap">
                                            <a href="{{ route('letters.show', $letter) }}"
                                                class="font-bold text-orange-600 hover:text-orange-700 group-hover:underline flex items-center gap-1">
                                                <span>{{ $letter->letter_number }}</span>
                                                <i data-lucide="external-link" class="w-3 h-3 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                                            </a>
                                        </td>
                                        <td class="py-3 max-w-[200px] truncate text-gray-800 font-medium" title="{{ $letter->subject }}">
                                            {{ $letter->subject }}
                                        </td>
                                        <td class="py-3 hidden sm:table-cell text-gray-500 truncate max-w-[140px]" title="{{ $letter->signatory->name ?? '-' }}">
                                            {{ $letter->signatory->name ?? '-' }}
                                        </td>
                                        <td class="py-3 text-center text-gray-500 whitespace-nowrap font-mono text-[11px]">
                                            {{ $letter->letter_date ? $letter->letter_date->format('d/m/Y') : '-' }}
                                        </td>
                                        <td class="py-3 text-right pr-2 whitespace-nowrap">
                                            @if ($letter->status === 'final')
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/80 shadow-2xs">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 live-dot"></span>
                                                    Final
                                                </span>
                                            @elseif ($letter->status === 'draft')
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200/80 shadow-2xs">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                    Draft
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-700">
                                                    {{ ucfirst($letter->status) }}
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="w-12 h-12 rounded-2xl bg-gray-100 text-gray-400 flex items-center justify-center mx-auto mb-3">
                            <i data-lucide="inbox" class="w-6 h-6"></i>
                        </div>
                        <h4 class="text-sm font-semibold text-gray-800">Belum ada arsip surat</h4>
                        <p class="text-xs text-gray-400 mt-1 max-w-sm mx-auto">Mulai daftarkan surat keluar untuk mengelola arsip naskah dinas resmi.</p>
                    </div>
                @endif
            </div>

        </div>

        {{-- ── RIGHT COLUMN (5 Cols): HANYA Performa Kontributor (Tampil Semua + Scroll Setara Bagian Kiri) ── --}}
        <div class="lg:col-span-5 min-h-0">
            <div id="dashboard-contributor-card" class="mac-card p-5 sm:p-6 flex flex-col min-h-0 overflow-hidden">
                <div class="flex items-center justify-between pb-3.5 mb-4 border-b border-gray-100 flex-shrink-0">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-purple-50 border border-purple-200 flex items-center justify-center text-purple-600 shadow-2xs">
                            <i data-lucide="users" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-gray-900">Performa Kontributor</h3>
                            <p class="text-[11px] text-gray-500">Peringkat kontribusi registrasi surat oleh staf</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200/70">
                        {{ isset($lettersByUser) ? $lettersByUser->count() : 0 }} Pengguna
                    </span>
                </div>

                @if (isset($lettersByUser) && $lettersByUser->count() > 0)
                    {{-- Scrollable List with exact max-height matching the left column --}}
                    <div id="dashboard-contributor-list" class="flex-1 min-h-0 max-h-[500px] lg:max-h-none overflow-y-auto pr-1.5 space-y-2.5 custom-scrollbar">
                        @foreach ($lettersByUser as $idx => $user)
                            <div class="flex items-center justify-between p-2.5 sm:p-3 rounded-2xl bg-gray-50/70 border border-gray-100 hover:bg-purple-50/30 hover:border-purple-200/60 transition-all">
                                <div class="flex items-center gap-3 min-w-0">
                                    {{-- Rank Badge --}}
                                    <div class="w-6 h-6 rounded-full flex items-center justify-center font-bold text-xs flex-shrink-0 {{ $idx == 0 ? 'bg-amber-100 text-amber-800 border border-amber-300 shadow-2xs' : ($idx == 1 ? 'bg-slate-200 text-slate-800 border border-slate-300' : ($idx == 2 ? 'bg-orange-100 text-orange-800 border border-orange-300' : 'bg-gray-100 text-gray-500')) }}">
                                        {{ $idx + 1 }}
                                    </div>

                                    {{-- Avatar & Info --}}
                                    <div class="w-8 h-8 rounded-xl text-white flex items-center justify-center font-bold text-[10px] shadow-2xs flex-shrink-0"
                                        style="background: linear-gradient(135deg, {{ $idx == 0 ? '#FF4D00, #F59E0B' : ($idx % 3 == 0 ? '#8b5cf6, #6366f1' : ($idx % 2 == 0 ? '#0ea5e9, #3b82f6' : '#10b981, #059669')) }});">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-gray-900 truncate" title="{{ $user->name }}">{{ $user->name }}</p>
                                        <p class="text-[10px] text-gray-400 truncate capitalize">{{ $user->role ?? 'Staf' }}</p>
                                    </div>
                                </div>

                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $user->letters_count > 0 ? 'bg-white text-orange-700 border border-orange-200/80 shadow-2xs' : 'bg-gray-100 text-gray-400 border border-gray-200' }} flex-shrink-0">
                                    {{ number_format($user->letters_count, 0, ',', '.') }} surat
                                </span>
                            </div>
                        @endforeach
                    </div>

                    {{-- Scroll Hint Footer --}}
                    <div class="pt-3 mt-3 border-t border-gray-100 flex items-center justify-between text-[11px] text-gray-400 flex-shrink-0">
                        <span>Total {{ $lettersByUser->count() }} staf terdaftar</span>
                        <span class="inline-flex items-center gap-1 text-[10px] text-gray-400">
                            <i data-lucide="mouse-pointer" class="w-3 h-3"></i>
                            Gulir untuk melihat seluruhnya
                        </span>
                    </div>
                @else
                    <p class="text-xs text-gray-400 text-center py-6">Belum ada data kontribusi pengguna.</p>
                @endif
            </div>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/lucide@latest"></script>
<script>
    function syncDashboardHeight() {
        const leftCol = document.getElementById('dashboard-left-column');
        const rightCard = document.getElementById('dashboard-contributor-card');
        const rightList = document.getElementById('dashboard-contributor-list');
        if (!leftCol || !rightCard) return;

        if (window.innerWidth >= 1024) {
            const leftHeight = leftCol.offsetHeight;
            if (leftHeight > 0) {
                rightCard.style.height = leftHeight + 'px';
                rightCard.style.maxHeight = leftHeight + 'px';

                if (rightList) {
                    const header = rightCard.querySelector('.flex-shrink-0');
                    const allShrink = rightCard.querySelectorAll('.flex-shrink-0');
                    const footer = allShrink.length > 1 ? allShrink[1] : null;
                    const headerH = header ? header.offsetHeight + 16 : 60;
                    const footerH = footer ? footer.offsetHeight + 12 : 45;
                    const cardPadding = 48; // p-5 to p-6 (24px top + 24px bottom)
                    const availableListHeight = Math.max(200, leftHeight - headerH - footerH - cardPadding);
                    rightList.style.maxHeight = availableListHeight + 'px';
                }
            }
        } else {
            rightCard.style.height = '';
            rightCard.style.maxHeight = '';
            if (rightList) {
                rightList.style.maxHeight = '';
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
        syncDashboardHeight();
        setTimeout(syncDashboardHeight, 100);
        setTimeout(syncDashboardHeight, 400);
    });

    window.addEventListener('load', syncDashboardHeight);
    window.addEventListener('resize', syncDashboardHeight);

    if (document.fonts) {
        document.fonts.ready.then(syncDashboardHeight);
    }

    if (window.ResizeObserver) {
        const leftCol = document.getElementById('dashboard-left-column');
        if (leftCol) {
            new ResizeObserver(() => {
                syncDashboardHeight();
            }).observe(leftCol);
        }
    }
</script>
@endpush
