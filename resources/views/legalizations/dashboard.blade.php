@extends('layouts.app')

@section('title', 'Dashboard Legalisir - FEB UNJ')
@section('disable_layout_alerts', true)

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

        /* Floating Toast Notification */
        .toast-popup {
            transform: translate(-50%, -24px);
            opacity: 0;
            pointer-events: none;
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .toast-popup.show {
            transform: translate(-50%, 0);
            opacity: 1;
            pointer-events: auto;
        }
    </style>
@endpush

@section('content')
<div class="space-y-6 pb-12 relative">
    {{-- Floating Toast Notification --}}
    <div id="toastPopup" class="toast-popup fixed top-5 left-1/2 z-[9999] flex items-center gap-3 px-5 py-3 rounded-2xl bg-white/95 backdrop-blur-md border border-gray-200 shadow-xl text-xs sm:text-sm font-medium">
        <div id="toastIconWrap" class="w-7 h-7 rounded-xl flex items-center justify-center flex-shrink-0 bg-emerald-100 text-emerald-600">
            <i id="toastIcon" data-lucide="check" class="w-4 h-4"></i>
        </div>
        <span id="toastMessage" class="text-gray-800"></span>
        <button type="button" onclick="hideToast()" class="text-gray-400 hover:text-gray-600 ml-2 p-0.5 rounded-lg transition-colors cursor-pointer">
            <i data-lucide="x" class="w-3.5 h-3.5"></i>
        </button>
    </div>

    {{-- ── 1. Apple Bento Grid: 4 Key Statistics Cards ── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-5">
        {{-- Card 1: Hari Ini (Orange) --}}
        <div class="mac-card p-5 group cursor-default">
            <div class="flex items-center justify-between mb-3.5">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-orange-500 to-amber-500 flex items-center justify-center text-white shadow-xs group-hover:scale-105 transition-transform duration-200">
                    <i data-lucide="file-check-2" class="w-5 h-5"></i>
                </div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-orange-50 text-orange-700 border border-orange-200/70">
                    Hari Ini
                </span>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Legalisir Hari Ini</p>
                <h3 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight mt-1">
                    {{ number_format($stats['total_today'], 0, ',', '.') }}
                </h3>
                <p class="text-[11px] text-gray-500 mt-2 flex items-center justify-between">
                    <span>Penerimaan:</span>
                    <span class="font-bold text-orange-700 bg-orange-50 px-2 py-0.5 rounded-md border border-orange-200/70 font-mono">
                        Rp {{ number_format($stats['revenue_today'], 0, ',', '.') }}
                    </span>
                </p>
            </div>
        </div>

        {{-- Card 2: Bulan Ini (Emerald) --}}
        <div class="mac-card p-5 group cursor-default">
            <div class="flex items-center justify-between mb-3.5">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center text-white shadow-xs group-hover:scale-105 transition-transform duration-200">
                    <i data-lucide="calendar" class="w-5 h-5"></i>
                </div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/70">
                    {{ now()->translatedFormat('F Y') }}
                </span>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Legalisir Bulan Ini</p>
                <h3 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight mt-1">
                    {{ number_format($stats['total_this_month'], 0, ',', '.') }}
                </h3>
                <p class="text-[11px] text-gray-500 mt-2 flex items-center justify-between">
                    <span>Penerimaan:</span>
                    <span class="font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200/70 font-mono">
                        Rp {{ number_format($stats['revenue_this_month'], 0, ',', '.') }}
                    </span>
                </p>
            </div>
        </div>

        {{-- Card 3: Total Dokumen Akumulatif (Azure Blue) --}}
        <div class="mac-card p-5 group cursor-default">
            <div class="flex items-center justify-between mb-3.5">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-500 flex items-center justify-center text-white shadow-xs group-hover:scale-105 transition-transform duration-200">
                    <i data-lucide="layers" class="w-5 h-5"></i>
                </div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200/70">
                    Semua Arsip
                </span>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Permohonan</p>
                <h3 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight mt-1">
                    {{ number_format($stats['total_all'] ?? 0, 0, ',', '.') }}
                </h3>
                <p class="text-[11px] text-gray-500 mt-2 flex items-center justify-between">
                    <span>Total Lembar:</span>
                    <span class="font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-md border border-blue-200/70 font-mono">
                        {{ number_format($stats['total_pages'] ?? 0, 0, ',', '.') }} lembar
                    </span>
                </p>
            </div>
        </div>

        {{-- Card 4: Total Kas / Penerimaan (Purple) --}}
        <div class="mac-card p-5 group cursor-default">
            <div class="flex items-center justify-between mb-3.5">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-purple-500 to-violet-600 flex items-center justify-center text-white shadow-xs group-hover:scale-105 transition-transform duration-200">
                    <i data-lucide="wallet" class="w-5 h-5"></i>
                </div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-purple-50 text-purple-700 border border-purple-200/70">
                    Akumulasi PNBP
                </span>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Penerimaan</p>
                <h3 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight mt-1 truncate" title="Rp {{ number_format($stats['revenue_all'] ?? 0, 0, ',', '.') }}">
                    Rp {{ number_format($stats['revenue_all'] ?? 0, 0, ',', '.') }}
                </h3>
                <p class="text-[11px] text-gray-500 mt-2 flex items-center justify-between">
                    <span>Tahun {{ now()->year }}:</span>
                    <span class="font-bold text-purple-700 bg-purple-50 px-2 py-0.5 rounded-md border border-purple-200/70 font-mono">
                        {{ number_format($stats['total_this_year'] ?? 0, 0, ',', '.') }} berkas
                    </span>
                </p>
            </div>
        </div>
    </div>

    {{-- ── 2. Rekapitulasi Pendapatan Bulanan (Untuk Rekonsiliasi Keuangan) ── --}}
    <div class="mac-card overflow-hidden">
        <div class="p-5 sm:p-6 pb-5">
        {{-- Header & Filter Form --}}
        <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4 pb-4 mb-5 border-b border-gray-100">
            <div class="flex items-start sm:items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white shadow-xs flex-shrink-0">
                    <i data-lucide="calendar-range" class="w-5 h-5"></i>
                </div>
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h3 class="text-base font-bold text-gray-900 tracking-tight">Rekapitulasi Pendapatan Bulanan</h3>
                    </div>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Periode terpilih: <span class="font-semibold text-gray-700">{{ $monthlySummary['period_label'] }}</span>
                    </p>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row flex-wrap items-stretch sm:items-center gap-2.5">
                {{-- Filter Rentang Bulan --}}
                <form id="monthlyFilterForm" method="GET" action="{{ route('legalizations.dashboard') }}" class="flex flex-wrap items-center gap-2">
                    <div class="flex items-center gap-1.5 bg-gray-50 border border-gray-200/90 rounded-xl px-2.5 py-1.5">
                        <label for="start_month" class="text-[11px] font-semibold text-gray-500">Dari</label>
                        <input type="month" id="start_month" name="start_month" value="{{ $startMonth }}"
                            class="bg-transparent text-xs font-semibold text-gray-800 focus:outline-none cursor-pointer">
                    </div>
                    <span class="text-xs text-gray-400 font-medium hidden sm:inline">s.d.</span>
                    <div class="flex items-center gap-1.5 bg-gray-50 border border-gray-200/90 rounded-xl px-2.5 py-1.5">
                        <label for="end_month" class="text-[11px] font-semibold text-gray-500">Sampai</label>
                        <input type="month" id="end_month" name="end_month" value="{{ $endMonth }}"
                            class="bg-transparent text-xs font-semibold text-gray-800 focus:outline-none cursor-pointer">
                    </div>
                    <button type="submit"
                        class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 text-xs font-bold text-white bg-gray-900 hover:bg-gray-800 rounded-xl shadow-2xs transition-all active:scale-[0.98] cursor-pointer">
                        <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                        <span>Terapkan</span>
                    </button>
                </form>

                {{-- Export Excel Periode Terpilih --}}
                <a href="{{ route('legalizations.export', ['date_range' => $monthlySummary['date_range_param']]) }}"
                    onclick="showToast('Menyiapkan laporan Excel rekon periode terpilih...', 'success')"
                    class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 text-xs font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200/90 rounded-xl shadow-2xs transition-all active:scale-[0.98]">
                    <i data-lucide="file-spreadsheet" class="w-3.5 h-3.5"></i>
                    <span>Export Excel (.xlsx)</span>
                </a>
            </div>
        </div>

        {{-- Preset Cepat --}}
        <div class="flex flex-wrap items-center gap-1.5 mb-5">
            <span class="text-[11px] font-semibold text-gray-400 mr-1">Preset Cepat:</span>
            <button type="button" onclick="setMonthRangePreset('3m')"
                class="px-2.5 py-1 text-[11px] font-semibold text-gray-600 bg-gray-100/80 hover:bg-gray-200/80 rounded-full transition-colors cursor-pointer">
                3 Bulan Terakhir
            </button>
            <button type="button" onclick="setMonthRangePreset('6m')"
                class="px-2.5 py-1 text-[11px] font-semibold text-gray-600 bg-gray-100/80 hover:bg-gray-200/80 rounded-full transition-colors cursor-pointer">
                6 Bulan Terakhir
            </button>
            <button type="button" onclick="setMonthRangePreset('ytd')"
                class="px-2.5 py-1 text-[11px] font-semibold text-gray-600 bg-gray-100/80 hover:bg-gray-200/80 rounded-full transition-colors cursor-pointer">
                Tahun Ini ({{ now()->year }})
            </button>
            <button type="button" onclick="setMonthRangePreset('last_year')"
                class="px-2.5 py-1 text-[11px] font-semibold text-gray-600 bg-gray-100/80 hover:bg-gray-200/80 rounded-full transition-colors cursor-pointer">
                Tahun {{ now()->year - 1 }}
            </button>
        </div>

        {{-- 4 KPI Ringkasan Periode Terpilih --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-5">
            <div class="p-3.5 rounded-2xl bg-emerald-50/60 border border-emerald-200/70">
                <p class="text-[11px] font-semibold text-emerald-700 uppercase tracking-wider">Total Pendapatan Periode</p>
                <p class="text-lg sm:text-xl font-extrabold text-emerald-900 font-mono mt-1">
                    Rp {{ number_format($monthlySummary['total_revenue'], 0, ',', '.') }}
                </p>
            </div>
            <div class="p-3.5 rounded-2xl bg-gray-50/80 border border-gray-200/70">
                <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Volume Transaksi &amp; Lembar</p>
                <p class="text-lg sm:text-xl font-extrabold text-gray-900 mt-1">
                    {{ number_format($monthlySummary['total_transactions'], 0, ',', '.') }}
                    <span class="text-xs font-semibold text-gray-500">berkas</span>
                    <span class="text-gray-300 mx-1">·</span>
                    {{ number_format($monthlySummary['total_pages'], 0, ',', '.') }}
                    <span class="text-xs font-semibold text-gray-500">lembar</span>
                </p>
            </div>
            <div class="p-3.5 rounded-2xl bg-gray-50/80 border border-gray-200/70">
                <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Rata-rata per Bulan</p>
                <p class="text-lg sm:text-xl font-extrabold text-gray-900 font-mono mt-1">
                    Rp {{ number_format($monthlySummary['avg_revenue'], 0, ',', '.') }}
                </p>
            </div>
            <div class="p-3.5 rounded-2xl bg-orange-50/60 border border-orange-200/70">
                <p class="text-[11px] font-semibold text-orange-700 uppercase tracking-wider">Bulan Tertinggi</p>
                <p class="text-sm sm:text-base font-extrabold text-gray-900 mt-1 truncate" title="{{ $monthlySummary['peak_month_label'] }}">
                    {{ $monthlySummary['peak_month_label'] }}
                </p>
                <p class="text-[11px] font-bold text-orange-700 font-mono">
                    Rp {{ number_format($monthlySummary['peak_month_revenue'], 0, ',', '.') }}
                </p>
            </div>
        </div>
        </div>

        {{-- Tabel Rekapitulasi Bulanan (Full-bleed Apple Style ke Card) --}}
        <div class="overflow-x-auto border-t border-gray-100">
            <table class="min-w-full divide-y divide-gray-100 text-left">
                <thead>
                    <tr class="bg-gray-50/60 border-b border-gray-100">
                        <th scope="col" class="pl-5 sm:pl-6 pr-3 py-3 text-center text-[11px] font-bold text-gray-500 uppercase tracking-wider w-12">No</th>
                        <th scope="col" class="px-4 py-3 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">Bulan &amp; Tahun</th>
                        <th scope="col" class="px-4 py-3 text-right text-[11px] font-bold text-gray-500 uppercase tracking-wider">Transaksi</th>
                        <th scope="col" class="px-4 py-3 text-right text-[11px] font-bold text-gray-500 uppercase tracking-wider">Total Lembar</th>
                        <th scope="col" class="px-4 py-3 text-right text-[11px] font-bold text-gray-500 uppercase tracking-wider">Pendapatan (Rp)</th>
                        <th scope="col" class="px-4 py-3 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider hidden md:table-cell w-48">Porsi Periode</th>
                        <th scope="col" class="pl-4 pr-5 sm:pr-6 py-3 text-right text-[11px] font-bold text-gray-500 uppercase tracking-wider w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @foreach ($monthlyReport as $index => $row)
                        @php
                            $isPeak = $monthlySummary['peak_month_revenue'] > 0 && $row['total_revenue'] === $monthlySummary['peak_month_revenue'];
                            $monthBadge = strtoupper(\Carbon\Carbon::createFromFormat('Y-m', $row['ym'])->locale('id')->translatedFormat('M'));
                        @endphp
                        <tr class="hover:bg-emerald-50/30 transition-colors group">
                            <td class="pl-5 sm:pl-6 pr-3 py-3.5 text-center text-xs font-semibold text-gray-400">
                                {{ $index + 1 }}
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl flex items-center justify-center text-[10px] font-extrabold tracking-tight flex-shrink-0 {{ $row['total_revenue'] > 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200/70' : 'bg-gray-100 text-gray-400 border border-gray-200/60' }}">
                                        {{ $monthBadge }}
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs sm:text-sm font-bold text-gray-900 group-hover:text-emerald-800 transition-colors">{{ $row['label'] }}</span>
                                        @if ($isPeak)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-orange-50 text-orange-700 border border-orange-200/70">
                                                <i data-lucide="trending-up" class="w-2.5 h-2.5"></i>
                                                Tertinggi
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap text-right text-xs sm:text-sm font-semibold text-gray-700">
                                {{ number_format($row['tx_count'], 0, ',', '.') }}
                                <span class="text-[11px] font-normal text-gray-400 ml-0.5">berkas</span>
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap text-right text-xs sm:text-sm font-semibold text-gray-700">
                                {{ number_format($row['total_pages'], 0, ',', '.') }}
                                <span class="text-[11px] font-normal text-gray-400 ml-0.5">lembar</span>
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap text-right">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs sm:text-sm font-extrabold font-mono {{ $row['total_revenue'] > 0 ? 'bg-emerald-50/70 text-emerald-700' : 'text-gray-400' }}">
                                    Rp {{ number_format($row['total_revenue'], 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap hidden md:table-cell">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                                        <div class="h-full rounded-full {{ $isPeak ? 'bg-gradient-to-r from-orange-500 to-amber-500' : 'bg-emerald-500' }} transition-all duration-300"
                                            style="width: {{ min(100, $row['share_percent']) }}%"></div>
                                    </div>
                                    <span class="text-[11px] font-bold text-gray-500 w-11 text-right tabular-nums">{{ $row['share_percent'] }}%</span>
                                </div>
                            </td>
                            <td class="pl-4 pr-5 sm:pr-6 py-3.5 whitespace-nowrap text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('legalizations.index', ['date_range' => $row['start_date'] . ' to ' . $row['end_date']]) }}"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl text-[11px] font-semibold text-gray-700 bg-gray-100/80 hover:bg-gray-200/80 transition-colors"
                                        title="Lihat daftar transaksi bulan {{ $row['label'] }}">
                                        <i data-lucide="list" class="w-3 h-3"></i>
                                        <span>Detail</span>
                                    </a>
                                    <a href="{{ route('legalizations.export', ['date_range' => $row['start_date'] . ' to ' . $row['end_date']]) }}"
                                        onclick="showToast('Mengunduh rekon {{ $row['label'] }}...', 'success')"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl text-[11px] font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200/70 transition-colors"
                                        title="Export Excel rekon bulan {{ $row['label'] }}">
                                        <i data-lucide="download" class="w-3 h-3"></i>
                                        <span>Excel</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50/80 border-t border-gray-200/80">
                    <tr>
                        <td colspan="2" class="pl-5 sm:pl-6 pr-4 py-3.5 text-xs sm:text-sm font-extrabold text-gray-900">
                            TOTAL ({{ $monthlySummary['month_count'] }} Bulan)
                        </td>
                        <td class="px-4 py-3.5 text-right text-xs sm:text-sm font-extrabold text-gray-900 whitespace-nowrap">
                            {{ number_format($monthlySummary['total_transactions'], 0, ',', '.') }}
                            <span class="text-[11px] font-semibold text-gray-500 ml-0.5">berkas</span>
                        </td>
                        <td class="px-4 py-3.5 text-right text-xs sm:text-sm font-extrabold text-gray-900 whitespace-nowrap">
                            {{ number_format($monthlySummary['total_pages'], 0, ',', '.') }}
                            <span class="text-[11px] font-semibold text-gray-500 ml-0.5">lembar</span>
                        </td>
                        <td class="px-4 py-3.5 text-right whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-emerald-100/80 text-emerald-800 font-extrabold font-mono text-xs sm:text-sm">
                                Rp {{ number_format($monthlySummary['total_revenue'], 0, ',', '.') }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 hidden md:table-cell text-[11px] font-bold text-gray-500">100%</td>
                        <td class="pl-4 pr-5 sm:pr-6 py-3.5"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- ── 3. Bento Section: Analisis Jenjang Pendidikan & Tren Tahunan ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
        
        {{-- Left: Distribusi per Jenjang Pendidikan (7 Cols) --}}
        <div class="lg:col-span-7 mac-card p-5 sm:p-6 flex flex-col justify-between">
            <div>
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-3.5 mb-4 border-b border-gray-100 gap-2">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-orange-50 border border-orange-200 flex items-center justify-center text-orange-600 shadow-2xs">
                            <i data-lucide="graduation-cap" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-gray-900">Distribusi per Jenjang Pendidikan</h3>
                            <p class="text-[11px] text-gray-500">Komposisi permohonan legalisir berdasarkan jenjang lulusan</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-orange-50 text-orange-800 border border-orange-200/80 shadow-2xs self-start sm:self-auto">
                        Total: {{ number_format($stats['total_all'] ?? 0, 0, ',', '.') }} Dokumen
                    </span>
                </div>

                @if (isset($legalizationsByLevel) && $legalizationsByLevel->count() > 0)
                    @php
                        $totalDocs = max(1, $stats['total_all'] ?? $legalizationsByLevel->sum('count'));
                        $palette = ['#FF4D00', '#3b82f6', '#10b981', '#8b5cf6', '#f59e0b', '#06b6d4', '#ec4899'];
                    @endphp

                    {{-- Multi-Segment Progress Bar --}}
                    <div class="mb-4">
                        <div class="w-full bg-gray-100 rounded-full h-3 flex overflow-hidden p-0.5 gap-0.5 shadow-inner">
                            @foreach ($legalizationsByLevel as $index => $stat)
                                @php
                                    $pct = ($stat->count / $totalDocs) * 100;
                                    $color = $palette[$index % count($palette)];
                                @endphp
                                @if($pct > 0)
                                    <div class="h-full rounded-full transition-all duration-500"
                                        style="width: {{ $pct }}%; background-color: {{ $color }};"
                                        title="{{ $stat->educationLevel->name ?? 'Lainnya' }}: {{ $stat->count }} berkas ({{ round($pct, 1) }}%)">
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    {{-- Table Breakdown --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead>
                                <tr class="text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                                    <th class="pb-2.5 pl-2">Jenjang Pendidikan</th>
                                    <th class="pb-2.5 text-center">Berkas</th>
                                    <th class="pb-2.5 text-right">Penerimaan</th>
                                    <th class="pb-2.5 pr-2 hidden sm:table-cell text-center">Porsi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-xs">
                                @foreach ($legalizationsByLevel as $index => $stat)
                                    @php
                                        $percentage = round(($stat->count / $totalDocs) * 100, 1);
                                        $color = $palette[$index % count($palette)];
                                    @endphp
                                    <tr class="hover:bg-orange-50/20 transition-colors">
                                        <td class="py-2.5 pl-2 font-medium text-gray-900 flex items-center gap-2">
                                            <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background-color: {{ $color }};"></span>
                                            <span>{{ $stat->educationLevel->name ?? 'Tidak Diketahui' }}</span>
                                        </td>
                                        <td class="py-2.5 text-center font-bold text-gray-900">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-800">
                                                {{ $stat->count }}
                                            </span>
                                        </td>
                                        <td class="py-2.5 text-right font-mono font-bold text-gray-800">
                                            Rp {{ number_format($stat->revenue, 0, ',', '.') }}
                                        </td>
                                        <td class="py-2.5 pr-2 hidden sm:table-cell text-center">
                                            <span class="text-gray-600 font-mono font-medium bg-gray-50 px-2 py-0.5 rounded border border-gray-200/60">
                                                {{ $percentage }}%
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-10">
                        <div class="w-12 h-12 rounded-2xl bg-gray-100 text-gray-400 flex items-center justify-center mx-auto mb-3">
                            <i data-lucide="graduation-cap" class="w-6 h-6"></i>
                        </div>
                        <h4 class="text-sm font-semibold text-gray-800">Belum ada data transaksi legalisir</h4>
                        <p class="text-xs text-gray-400 mt-1 max-w-sm mx-auto">Dokumen yang didaftarkan akan otomatis terakumulasi dalam statistik jenjang pendidikan.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Right: Tren Tahunan Legalisir (5 Cols) --}}
        <div class="lg:col-span-5 mac-card p-5 sm:p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3.5 mb-4 border-b border-gray-100">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-orange-50 border border-orange-200 flex items-center justify-center text-orange-600 shadow-2xs">
                            <i data-lucide="bar-chart-3" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-gray-900">Rekapitulasi Tahunan</h3>
                            <p class="text-[11px] text-gray-500">Volume legalisir & pendapatan per tahun</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-gray-100 text-gray-700">
                        Multi-Tahun
                    </span>
                </div>

                @if (isset($legalizationsByYear) && $legalizationsByYear->count() > 0)
                    <div class="space-y-2.5">
                        @foreach ($legalizationsByYear as $yearStat)
                            <div class="flex items-center justify-between p-2.5 sm:p-3 rounded-2xl bg-gray-50/70 border border-gray-100 hover:bg-orange-50/30 transition-colors">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full {{ $yearStat->year == now()->year ? 'bg-orange-500' : 'bg-gray-300' }}"></span>
                                    <span class="text-xs font-bold text-gray-900">Tahun {{ $yearStat->year }}</span>
                                    @if($yearStat->year == now()->year)
                                        <span class="text-[10px] font-bold text-orange-700 bg-orange-100/80 px-2 py-0.2 rounded-full border border-orange-200/80">Tahun Ini</span>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-white text-gray-800 border border-gray-200/80 shadow-2xs">
                                        {{ number_format($yearStat->count, 0, ',', '.') }} berkas
                                    </span>
                                    <p class="text-[10px] text-gray-500 font-mono mt-0.5">
                                        Rp {{ number_format($yearStat->revenue, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Contribution Meter --}}
                    @php
                        $sumYearDocs = max(1, $legalizationsByYear->sum('count'));
                        $currentYearPercent = round((($stats['total_this_year'] ?? 0) / $sumYearDocs) * 100, 1);
                    @endphp
                    <div class="mt-5 pt-4 border-t border-gray-100">
                        <div class="flex items-center justify-between text-xs mb-1.5">
                            <span class="text-gray-500 font-medium">Porsi Th. {{ now()->year }}</span>
                            <span class="font-bold text-orange-600">{{ $currentYearPercent }}%</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden shadow-inner">
                            <div class="h-full rounded-full transition-all duration-500"
                                style="width: {{ min(100, $currentYearPercent) }}%; background: linear-gradient(to right, #FF4D00, #f97316);">
                            </div>
                        </div>
                        <p class="text-[11px] text-gray-400 mt-2 text-center">
                            {{ number_format($stats['total_this_year'] ?? 0, 0, ',', '.') }} dari {{ number_format($sumYearDocs, 0, ',', '.') }} total transaksi tercatat
                        </p>
                    </div>
                @else
                    <div class="text-center py-8">
                        <p class="text-xs text-gray-400">Belum ada rekapitulasi tahunan.</p>
                    </div>
                @endif
            </div>
        </div>

    </div>

    {{-- ── 4. Main Activity Table: Aktivitas Legalisir Terbaru ── --}}
    <div class="mac-card p-5 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-3.5 mb-4 border-b border-gray-100 gap-2">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-orange-50 border border-orange-200 flex items-center justify-center text-orange-600 shadow-2xs">
                    <i data-lucide="activity" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-gray-900">Aktivitas Legalisir Terbaru</h3>
                    <p class="text-[11px] text-gray-500">Transaksi permohonan legalisasi terkini dalam sistem</p>
                </div>
            </div>
            <a href="{{ route('legalizations.index') }}"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold text-orange-600 hover:text-orange-700 bg-orange-50 hover:bg-orange-100/80 border border-orange-200/80 transition-colors self-start sm:self-auto">
                <span>Buka Semua Katalog</span>
                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
            </a>
        </div>

        <div class="overflow-x-auto">
            @if ($recentLegalizations->count() > 0)
                <table class="min-w-full divide-y divide-gray-100 text-xs text-left">
                    <thead>
                        <tr class="bg-gray-50/60 border-b border-gray-100">
                            <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider w-24">No. Legalisir</th>
                            <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider min-w-[200px]">Nama Alumni</th>
                            <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider min-w-[140px]">Jenjang Pendidikan</th>
                            <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider text-center">Jumlah Lembar</th>
                            <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider text-right pr-4">Total Biaya</th>
                            <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider text-center w-20">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @foreach ($recentLegalizations as $legalization)
                            <tr class="hover:bg-orange-50/20 transition-colors group">
                                {{-- Nomor Urut Legalisir --}}
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <div class="flex items-center gap-1.5">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-mono font-bold bg-blue-50 text-blue-700 border border-blue-200/80 shadow-2xs">
                                            #{{ str_pad($legalization->running_number, 3, '0', STR_PAD_LEFT) }}
                                        </span>
                                        <button type="button" onclick="copyToClipboard('{{ $legalization->running_number }}', 'Nomor legalisir disalin!')"
                                            class="opacity-0 group-hover:opacity-100 p-0.5 text-gray-400 hover:text-orange-600 transition-all cursor-pointer"
                                            title="Salin Nomor">
                                            <i data-lucide="copy" class="w-3 h-3"></i>
                                        </button>
                                    </div>
                                </td>

                                {{-- Tanggal --}}
                                <td class="px-4 py-3.5 whitespace-nowrap text-gray-500 font-mono text-[11.5px]">
                                    {{ $legalization->date ? $legalization->date->format('d/m/Y') : '-' }}
                                </td>

                                {{-- Nama Alumni & Tahun Kelulusan --}}
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 rounded-lg bg-orange-100 text-orange-700 font-bold text-[10px] flex items-center justify-center flex-shrink-0">
                                            {{ strtoupper(substr($legalization->alumni_name, 0, 2)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-bold text-gray-900 truncate">
                                                {{ $legalization->alumni_name }}
                                            </p>
                                            @if($legalization->graduation_year)
                                                <p class="text-[10.5px] text-gray-400 font-medium">
                                                    Lulusan Th. {{ $legalization->graduation_year }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Jenjang Pendidikan --}}
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-gray-100 text-gray-800 border border-gray-200/80">
                                        <i data-lucide="graduation-cap" class="w-3 h-3 text-gray-500"></i>
                                        {{ $legalization->educationLevel->name ?? '-' }}
                                    </span>
                                </td>

                                {{-- Jumlah Lembar --}}
                                <td class="px-4 py-3.5 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md font-mono text-xs font-semibold bg-orange-50 text-orange-800 border border-orange-200/70">
                                        {{ $legalization->page_count }} Lembar
                                    </span>
                                </td>

                                {{-- Total Biaya --}}
                                <td class="px-4 py-3.5 whitespace-nowrap text-right pr-4 font-mono font-bold text-gray-900 text-xs">
                                    Rp {{ number_format($legalization->total_price, 0, ',', '.') }}
                                </td>

                                {{-- Detail Aksi --}}
                                <td class="px-4 py-3.5 whitespace-nowrap text-center">
                                    <a href="{{ route('legalizations.show', $legalization) }}"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-bold text-gray-700 hover:text-[#FF4D00] bg-gray-50 hover:bg-orange-50 rounded-lg border border-gray-200/80 hover:border-orange-200 transition-all"
                                        title="Lihat Detail Transaksi">
                                        <i data-lucide="eye" class="w-3 h-3"></i>
                                        <span>Detail</span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="text-center py-12">
                    <div class="w-12 h-12 rounded-2xl bg-gray-100 text-gray-400 flex items-center justify-center mx-auto mb-3">
                        <i data-lucide="inbox" class="w-6 h-6"></i>
                    </div>
                    <h4 class="text-sm font-semibold text-gray-800">Belum ada aktivitas legalisir</h4>
                    <p class="text-xs text-gray-400 mt-1 max-w-sm mx-auto">Data transaksi legalisasi ijazah & transkrip nilai alumni akan muncul di sini.</p>
                    @if (auth()->user()->isAdmin() || auth()->user()->isOperator())
                        <div class="mt-4">
                            <a href="{{ route('legalizations.create') }}"
                                class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold text-white bg-gradient-to-r from-[#FF4D00] to-amber-500 rounded-full shadow-xs hover:shadow-md transition-all active:scale-[0.98]">
                                <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                                <span>Buat Legalisir Pertama</span>
                            </a>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/lucide@latest"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        @if(session('success'))
            showToast("{{ session('success') }}", 'success');
        @endif
        @if(session('error'))
            showToast("{{ session('error') }}", 'error');
        @endif
    });

    // Copy to Clipboard Helper
    function copyToClipboard(text, successMsg = 'Teks disalin ke clipboard') {
        if (!navigator.clipboard) {
            const textarea = document.createElement('textarea');
            textarea.value = text;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
            showToast(successMsg, 'success');
            return;
        }

        navigator.clipboard.writeText(text).then(() => {
            showToast(successMsg, 'success');
        }).catch(err => {
            console.error('Failed to copy: ', err);
            showToast('Gagal menyalin teks', 'error');
        });
    }

    function setMonthRangePreset(preset) {
        const startInput = document.getElementById('start_month');
        const endInput = document.getElementById('end_month');
        const form = document.getElementById('monthlyFilterForm');
        if (!startInput || !endInput || !form) return;

        const now = new Date();
        const year = now.getFullYear();
        const month = now.getMonth(); // 0-indexed

        const formatYM = (d) => `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}`;

        let startDate, endDate;
        if (preset === '3m') {
            startDate = new Date(year, month - 2, 1);
            endDate = new Date(year, month, 1);
        } else if (preset === '6m') {
            startDate = new Date(year, month - 5, 1);
            endDate = new Date(year, month, 1);
        } else if (preset === 'ytd') {
            startDate = new Date(year, 0, 1);
            endDate = new Date(year, month, 1);
        } else if (preset === 'last_year') {
            startDate = new Date(year - 1, 0, 1);
            endDate = new Date(year - 1, 11, 1);
        }

        if (startDate && endDate) {
            startInput.value = formatYM(startDate);
            endInput.value = formatYM(endDate);
            form.submit();
        }
    }

    // Toast Notification System
    let toastTimeout;
    function showToast(msg, type = 'success') {
        const toast = document.getElementById('toastPopup');
        const toastMsg = document.getElementById('toastMessage');
        const toastIconWrap = document.getElementById('toastIconWrap');
        const toastIcon = document.getElementById('toastIcon');

        if (!toast) return;

        toastMsg.textContent = msg;

        if (type === 'success') {
            toastIconWrap.className = 'w-7 h-7 rounded-xl flex items-center justify-center flex-shrink-0 bg-emerald-100 text-emerald-600';
            toastIcon.setAttribute('data-lucide', 'check');
        } else {
            toastIconWrap.className = 'w-7 h-7 rounded-xl flex items-center justify-center flex-shrink-0 bg-red-100 text-red-600';
            toastIcon.setAttribute('data-lucide', 'alert-circle');
        }

        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        toast.classList.add('show');

        clearTimeout(toastTimeout);
        toastTimeout = setTimeout(() => {
            hideToast();
        }, 3200);
    }

    function hideToast() {
        const toast = document.getElementById('toastPopup');
        if (toast) {
            toast.classList.remove('show');
        }
    }
</script>
@endpush
