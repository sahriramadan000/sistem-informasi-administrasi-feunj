@extends('layouts.app')

@section('title', 'Error Statistics')

@section('content')
<div class="space-y-6">

    {{-- ============================================================ --}}
    {{-- Page Header --}}
    {{-- ============================================================ --}}
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i data-lucide="bar-chart-3" class="w-6 h-6 text-brand"></i>
                Error Statistics
            </h2>
            <p class="mt-1 text-sm text-gray-500">Ringkasan dan tren error sistem dalam 14 hari terakhir</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.error-logs.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Kembali
            </a>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- Statistics Cards --}}
    {{-- ============================================================ --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">

        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 border-l-4 border-l-blue-500">
            <div class="flex items-center gap-2 mb-1">
                <i data-lucide="activity" class="h-4 w-4 text-blue-400"></i>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Error</p>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['total'] }}</p>
            <p class="text-xs text-gray-400 mt-1">14 hari terakhir</p>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 border-l-4 border-l-red-500">
            <div class="flex items-center gap-2 mb-1">
                <i data-lucide="alert-octagon" class="h-4 w-4 text-red-400"></i>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Critical</p>
            </div>
            <p class="text-3xl font-bold text-red-600">{{ $stats['critical'] }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ $stats['total'] > 0 ? round($stats['critical'] / $stats['total'] * 100, 1) : 0 }}% dari total</p>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 border-l-4 border-l-yellow-500">
            <div class="flex items-center gap-2 mb-1">
                <i data-lucide="alert-triangle" class="h-4 w-4 text-yellow-400"></i>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Warnings</p>
            </div>
            <p class="text-3xl font-bold text-yellow-600">{{ $stats['warning'] }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ $stats['total'] > 0 ? round($stats['warning'] / $stats['total'] * 100, 1) : 0 }}% dari total</p>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 border-l-4 border-l-purple-500">
            <div class="flex items-center gap-2 mb-1">
                <i data-lucide="code" class="h-4 w-4 text-purple-400"></i>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Tipe Exception</p>
            </div>
            <p class="text-3xl font-bold text-purple-600">{{ $stats['unique_types'] }}</p>
            <p class="text-xs text-gray-400 mt-1">jenis unik</p>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 border-l-4 border-l-green-500">
            <div class="flex items-center gap-2 mb-1">
                <i data-lucide="users" class="h-4 w-4 text-green-400"></i>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">User Terdampak</p>
            </div>
            <p class="text-3xl font-bold text-green-600">{{ $stats['unique_users'] }}</p>
            <p class="text-xs text-gray-400 mt-1">pengguna unik</p>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 border-l-4 border-l-indigo-500">
            <div class="flex items-center gap-2 mb-1">
                <i data-lucide="trending-up" class="h-4 w-4 text-indigo-400"></i>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Rata-rata/Hari</p>
            </div>
            <p class="text-3xl font-bold text-indigo-600">{{ $stats['avg_per_day'] }}</p>
            <p class="text-xs text-gray-400 mt-1">error per hari</p>
        </div>

    </div>

    {{-- ============================================================ --}}
    {{-- Error Trend Chart --}}
    {{-- ============================================================ --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">

        <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-100">
                <i data-lucide="bar-chart-2" class="h-5 w-5 text-gray-600"></i>
            </div>
            <div>
                <h3 class="text-base font-semibold text-gray-900">Tren Error Harian</h3>
                <p class="text-sm text-gray-500">Jumlah error per hari dalam 14 hari terakhir</p>
            </div>
        </div>

        <div class="p-6">
            @php $maxCount = count($errorsByDay) > 0 ? max($errorsByDay) : 1; @endphp
            <div class="flex items-end gap-2 h-40">
                @foreach ($errorsByDay as $date => $count)
                    @php
                        $heightPct = $maxCount > 0 ? ($count / $maxCount * 100) : 0;
                        $dateObj   = \Carbon\Carbon::parse($date);
                        $isToday   = $dateObj->isToday();
                        $barColor  = $isToday ? 'bg-brand' : ($count >= ($maxCount * 0.75) ? 'bg-red-400' : 'bg-gray-300');
                        $hoverColor = $isToday ? 'hover:bg-orange-600' : ($count >= ($maxCount * 0.75) ? 'hover:bg-red-500' : 'hover:bg-gray-400');
                    @endphp
                    <div class="flex-1 flex flex-col items-center gap-1 group cursor-default" title="{{ $dateObj->format('d M Y') }}: {{ $count }} error">
                        {{-- Count label --}}
                        <span class="text-xs font-semibold text-gray-600 opacity-0 group-hover:opacity-100 transition-opacity">
                            {{ $count }}
                        </span>
                        {{-- Bar --}}
                        <div class="w-full rounded-t-sm {{ $barColor }} {{ $hoverColor }} transition-colors"
                             style="height: {{ max($heightPct, 4) }}%"></div>
                        {{-- Date label --}}
                        <p class="text-xs text-gray-500 text-center leading-tight">
                            {{ $dateObj->format('d') }}<br>
                            <span class="text-gray-400">{{ $dateObj->format('M') }}</span>
                        </p>
                        @if ($isToday)
                            <span class="text-xs text-brand font-semibold">Hari ini</span>
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- Legend --}}
            <div class="flex items-center gap-4 mt-4 pt-4 border-t border-gray-100">
                <div class="flex items-center gap-1.5">
                    <div class="w-3 h-3 rounded-sm bg-brand"></div>
                    <span class="text-xs text-gray-500">Hari ini</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <div class="w-3 h-3 rounded-sm bg-red-400"></div>
                    <span class="text-xs text-gray-500">Volume tinggi (≥75% maks)</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <div class="w-3 h-3 rounded-sm bg-gray-300"></div>
                    <span class="text-xs text-gray-500">Normal</span>
                </div>
            </div>
        </div>

    </div>

    {{-- ============================================================ --}}
    {{-- Top Errors + Top Users (2 kolom) --}}
    {{-- ============================================================ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Top 5 Most Occurring Errors --}}
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-red-50">
                    <i data-lucide="alert-octagon" class="h-5 w-5 text-red-500"></i>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-gray-900">Error Terbanyak</h3>
                    <p class="text-sm text-gray-500">Top 5 tipe error yang paling sering muncul</p>
                </div>
            </div>

            <div class="p-6">
                @if (count($topErrors) > 0)
                    <div class="space-y-4">
                        @foreach ($topErrors as $error => $count)
                            @php
                                $percentage = $stats['total'] > 0 ? round($count / $stats['total'] * 100, 1) : 0;
                            @endphp
                            <div>
                                <div class="flex justify-between items-center mb-1.5">
                                    <p class="text-sm font-medium text-gray-700 truncate max-w-[70%]" title="{{ $error }}">
                                        {{ $error }}
                                    </p>
                                    <div class="flex items-center gap-2 flex-shrink-0">
                                        <span class="text-xs text-gray-400">{{ $percentage }}%</span>
                                        <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-semibold ring-1 ring-inset bg-red-100 text-red-800 ring-red-600/20">
                                            {{ $count }}
                                        </span>
                                    </div>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2">
                                    <div class="bg-red-500 h-2 rounded-full transition-all" style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="flex flex-col items-center py-8 text-gray-400">
                        <i data-lucide="check-circle" class="h-10 w-10 mb-2 text-green-400"></i>
                        <p class="text-sm text-gray-500">Tidak ada error tercatat</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Top 5 Users with Errors --}}
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-50">
                    <i data-lucide="users" class="h-5 w-5 text-blue-500"></i>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-gray-900">User Terdampak</h3>
                    <p class="text-sm text-gray-500">Top 5 pengguna dengan error terbanyak</p>
                </div>
            </div>

            <div class="p-6">
                @if (count($topUsers) > 0)
                    <div class="space-y-4">
                        @foreach ($topUsers as $user => $count)
                            @php
                                $percentage = $stats['total'] > 0 ? round($count / $stats['total'] * 100, 1) : 0;
                            @endphp
                            <div>
                                <div class="flex justify-between items-center mb-1.5">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <div class="w-6 h-6 rounded-full bg-brand-lighter flex items-center justify-center flex-shrink-0">
                                            <i data-lucide="user" class="h-3 w-3 text-brand"></i>
                                        </div>
                                        <p class="text-sm font-medium text-gray-700 truncate" title="{{ $user }}">
                                            {{ $user }}
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-2 flex-shrink-0">
                                        <span class="text-xs text-gray-400">{{ $percentage }}%</span>
                                        <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-semibold ring-1 ring-inset bg-blue-100 text-blue-800 ring-blue-600/20">
                                            {{ $count }}
                                        </span>
                                    </div>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2">
                                    <div class="bg-blue-500 h-2 rounded-full transition-all" style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="flex flex-col items-center py-8 text-gray-400">
                        <i data-lucide="user-check" class="h-10 w-10 mb-2 text-green-400"></i>
                        <p class="text-sm text-gray-500">Tidak ada error pada pengguna</p>
                    </div>
                @endif
            </div>
        </div>

    </div>

    {{-- ============================================================ --}}
    {{-- Error Distribution --}}
    {{-- ============================================================ --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">

        <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-100">
                <i data-lucide="pie-chart" class="h-5 w-5 text-gray-600"></i>
            </div>
            <div>
                <h3 class="text-base font-semibold text-gray-900">Distribusi Error</h3>
                <p class="text-sm text-gray-500">Komposisi error berdasarkan tingkat keparahan</p>
            </div>
        </div>

        <div class="p-6">
            @php
                $other = $stats['total'] - $stats['critical'] - $stats['warning'];
                $criticalPct = $stats['total'] > 0 ? round($stats['critical'] / $stats['total'] * 100, 1) : 0;
                $warningPct  = $stats['total'] > 0 ? round($stats['warning']  / $stats['total'] * 100, 1) : 0;
                $otherPct    = $stats['total'] > 0 ? round($other             / $stats['total'] * 100, 1) : 0;
                $rows = [
                    ['label' => 'Critical Errors', 'count' => $stats['critical'], 'pct' => $criticalPct, 'bar' => 'bg-red-500',    'badge' => 'bg-red-100 text-red-800 ring-red-600/20',    'icon' => 'alert-octagon', 'icon_color' => 'text-red-500'],
                    ['label' => 'Warning Errors',  'count' => $stats['warning'],  'pct' => $warningPct,  'bar' => 'bg-yellow-400',  'badge' => 'bg-yellow-100 text-yellow-800 ring-yellow-600/20', 'icon' => 'alert-triangle','icon_color' => 'text-yellow-500'],
                    ['label' => 'Other Errors',    'count' => $other,             'pct' => $otherPct,    'bar' => 'bg-blue-500',    'badge' => 'bg-blue-100 text-blue-800 ring-blue-600/20',    'icon' => 'info',          'icon_color' => 'text-blue-500'],
                ];
            @endphp

            <div class="space-y-5">
                @foreach ($rows as $row)
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <i data-lucide="{{ $row['icon'] }}" class="h-4 w-4 {{ $row['icon_color'] }}"></i>
                                <span class="text-sm font-medium text-gray-700">{{ $row['label'] }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-xs text-gray-400">{{ $row['pct'] }}%</span>
                                <span class="inline-flex items-center rounded-md px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset {{ $row['badge'] }}">
                                    {{ $row['count'] }}
                                </span>
                            </div>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-3">
                            <div class="{{ $row['bar'] }} h-3 rounded-full transition-all" style="width: {{ $row['pct'] }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

    {{-- ============================================================ --}}
    {{-- Info Box (pola Edit Surat) --}}
    {{-- ============================================================ --}}
    <div class="bg-gradient-to-r from-blue-50 to-cyan-50 border-l-4 border-blue-500 rounded-r-lg p-5">
        <div class="flex gap-4">
            <div class="flex-shrink-0">
                <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center shadow-sm">
                    <i data-lucide="info" class="w-5 h-5 text-blue-500"></i>
                </div>
            </div>
            <div>
                <h6 class="font-semibold text-gray-900 mb-2">Panduan Membaca Statistik</h6>
                <ul class="text-sm text-gray-700 space-y-1.5">
                    <li class="flex items-start gap-2">
                        <i data-lucide="alert-octagon" class="w-4 h-4 text-red-500 mt-0.5 flex-shrink-0"></i>
                        <span><strong>Critical Errors</strong> — Fatal error, parse error, dan type error yang perlu segera ditangani</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i data-lucide="alert-triangle" class="w-4 h-4 text-yellow-500 mt-0.5 flex-shrink-0"></i>
                        <span><strong>Warning Errors</strong> — Notice dan deprecation yang mengindikasikan potensi masalah</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i data-lucide="bar-chart-2" class="w-4 h-4 text-blue-500 mt-0.5 flex-shrink-0"></i>
                        <span><strong>Tren Error</strong> — Bar merah menandakan hari dengan volume tinggi (≥75% dari nilai maksimum)</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i data-lucide="users" class="w-4 h-4 text-blue-500 mt-0.5 flex-shrink-0"></i>
                        <span><strong>Top Users</strong> — Pengguna yang paling sering memicu error, berguna untuk investigasi</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        lucide.createIcons();
    });
</script>
@endpush
@endsection