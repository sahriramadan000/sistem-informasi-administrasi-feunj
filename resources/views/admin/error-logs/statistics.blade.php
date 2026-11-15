@extends('layouts.app')

@section('title', 'Statistik Error Logs - FEB UNJ')

@push('styles')
    <style>
        .mac-card {
            background: #ffffff;
            border-radius: 1.5rem;
            border: 1px solid rgba(226, 232, 240, 0.85);
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.03), 0 2px 6px -1px rgba(0, 0, 0, 0.02);
        }
        .kpi-card {
            background: #ffffff;
            border-radius: 1.25rem;
            border: 1px solid rgba(226, 232, 240, 0.85);
            box-shadow: 0 4px 16px -2px rgba(0, 0, 0, 0.03);
        }
    </style>
@endpush

@section('content')
<div class="space-y-6 pb-12">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-gray-200/80">
        <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-[#FF4D00] to-amber-500 flex items-center justify-center text-white shadow-xs flex-shrink-0">
                <i data-lucide="bar-chart-3" class="w-5 h-5"></i>
            </div>
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">Statistik Error</h1>
                <p class="text-xs sm:text-sm text-gray-500">Ringkasan dan tren error sistem dalam 14 hari terakhir</p>
            </div>
        </div>

        <a href="{{ route('admin.error-logs.index') }}"
            class="inline-flex items-center gap-2 px-4 py-2 text-xs sm:text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-200 rounded-full shadow-2xs transition-all self-start sm:self-auto">
            <i data-lucide="arrow-left" class="w-4 h-4 text-gray-500"></i>
            <span>Kembali ke Error Logs</span>
        </a>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <div class="kpi-card p-4">
            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Total Error</p>
            <p class="text-2xl font-extrabold text-gray-900 mt-1">{{ $stats['total'] }}</p>
            <p class="text-[11px] text-gray-400 mt-1">14 hari terakhir</p>
        </div>

        <div class="kpi-card p-4">
            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Critical</p>
            <p class="text-2xl font-extrabold text-rose-600 mt-1">{{ $stats['critical'] }}</p>
            <p class="text-[11px] text-gray-400 mt-1">{{ $stats['total'] > 0 ? round($stats['critical'] / $stats['total'] * 100, 1) : 0 }}% dari total</p>
        </div>

        <div class="kpi-card p-4">
            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Warnings</p>
            <p class="text-2xl font-extrabold text-amber-600 mt-1">{{ $stats['warning'] }}</p>
            <p class="text-[11px] text-gray-400 mt-1">{{ $stats['total'] > 0 ? round($stats['warning'] / $stats['total'] * 100, 1) : 0 }}% dari total</p>
        </div>

        <div class="kpi-card p-4">
            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Tipe Exception</p>
            <p class="text-2xl font-extrabold text-purple-600 mt-1">{{ $stats['unique_types'] }}</p>
            <p class="text-[11px] text-gray-400 mt-1">jenis unik</p>
        </div>

        <div class="kpi-card p-4">
            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">User Terdampak</p>
            <p class="text-2xl font-extrabold text-emerald-600 mt-1">{{ $stats['unique_users'] }}</p>
            <p class="text-[11px] text-gray-400 mt-1">pengguna unik</p>
        </div>

        <div class="kpi-card p-4">
            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Rata-rata/Hari</p>
            <p class="text-2xl font-extrabold text-indigo-600 mt-1">{{ $stats['avg_per_day'] }}</p>
            <p class="text-[11px] text-gray-400 mt-1">error per hari</p>
        </div>
    </div>

    {{-- Error Trend Chart --}}
    <div class="mac-card p-6">
        <div class="flex items-center gap-2.5 pb-4 mb-5 border-b border-gray-100">
            <div class="w-8 h-8 rounded-xl bg-orange-50 border border-orange-200/70 flex items-center justify-center text-[#FF4D00]">
                <i data-lucide="bar-chart-2" class="w-4 h-4"></i>
            </div>
            <div>
                <h2 class="text-sm font-bold text-gray-900">Tren Error Harian</h2>
                <p class="text-[11px] text-gray-500">Jumlah error per hari dalam 14 hari terakhir</p>
            </div>
        </div>

        @php $maxCount = count($errorsByDay) > 0 ? max($errorsByDay) : 1; @endphp
        <div class="flex items-end gap-2 h-44">
            @foreach ($errorsByDay as $date => $count)
                @php
                    $heightPct = $maxCount > 0 ? ($count / $maxCount * 100) : 0;
                    $dateObj   = \Carbon\Carbon::parse($date);
                    $isToday   = $dateObj->isToday();
                    $barColor  = $isToday ? 'bg-[#FF4D00]' : ($count >= ($maxCount * 0.75) ? 'bg-rose-400' : 'bg-gray-300');
                @endphp
                <div class="flex-1 flex flex-col items-center gap-1 group cursor-default" title="{{ $dateObj->format('d M Y') }}: {{ $count }} error">
                    <span class="text-[11px] font-bold text-gray-600 opacity-0 group-hover:opacity-100 transition-opacity">
                        {{ $count }}
                    </span>
                    <div class="w-full rounded-t-lg {{ $barColor }} transition-all"
                         style="height: {{ max($heightPct, 4) }}%"></div>
                    <p class="text-[11px] text-gray-500 text-center leading-tight">
                        {{ $dateObj->format('d') }}<br>
                        <span class="text-gray-400">{{ $dateObj->format('M') }}</span>
                    </p>
                </div>
            @endforeach
        </div>

        <div class="flex flex-wrap items-center gap-4 mt-4 pt-4 border-t border-gray-100 text-xs text-gray-500">
            <span class="inline-flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-[#FF4D00]"></span> Hari ini</span>
            <span class="inline-flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-rose-400"></span> Volume tinggi (&ge;75% maks)</span>
            <span class="inline-flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-gray-300"></span> Normal</span>
        </div>
    </div>

    {{-- Top Errors + Top Users --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="mac-card p-6">
            <div class="flex items-center gap-2.5 pb-4 mb-4 border-b border-gray-100">
                <div class="w-8 h-8 rounded-xl bg-rose-50 border border-rose-200/70 flex items-center justify-center text-rose-600">
                    <i data-lucide="alert-octagon" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-gray-900">Error Terbanyak</h3>
                    <p class="text-[11px] text-gray-500">Top 5 tipe error yang paling sering muncul</p>
                </div>
            </div>

            @if (count($topErrors) > 0)
                <div class="space-y-4">
                    @foreach ($topErrors as $error => $count)
                        @php $percentage = $stats['total'] > 0 ? round($count / $stats['total'] * 100, 1) : 0; @endphp
                        <div>
                            <div class="flex justify-between items-center mb-1.5">
                                <p class="text-xs font-semibold text-gray-800 truncate max-w-[70%]" title="{{ $error }}">{{ $error }}</p>
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    <span class="text-[11px] text-gray-400">{{ $percentage }}%</span>
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200/70">{{ $count }}</span>
                                </div>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2">
                                <div class="bg-rose-500 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-xs text-gray-400">Tidak ada error tercatat</div>
            @endif
        </div>

        <div class="mac-card p-6">
            <div class="flex items-center gap-2.5 pb-4 mb-4 border-b border-gray-100">
                <div class="w-8 h-8 rounded-xl bg-blue-50 border border-blue-200/70 flex items-center justify-center text-blue-600">
                    <i data-lucide="users" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-gray-900">User Terdampak</h3>
                    <p class="text-[11px] text-gray-500">Top 5 pengguna dengan error terbanyak</p>
                </div>
            </div>

            @if (count($topUsers) > 0)
                <div class="space-y-4">
                    @foreach ($topUsers as $user => $count)
                        @php $percentage = $stats['total'] > 0 ? round($count / $stats['total'] * 100, 1) : 0; @endphp
                        <div>
                            <div class="flex justify-between items-center mb-1.5">
                                <p class="text-xs font-semibold text-gray-800 truncate" title="{{ $user }}">{{ $user }}</p>
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    <span class="text-[11px] text-gray-400">{{ $percentage }}%</span>
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200/70">{{ $count }}</span>
                                </div>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2">
                                <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-xs text-gray-400">Tidak ada error pada pengguna</div>
            @endif
        </div>
    </div>

    {{-- Error Distribution --}}
    <div class="mac-card p-6">
        <div class="flex items-center gap-2.5 pb-4 mb-4 border-b border-gray-100">
            <div class="w-8 h-8 rounded-xl bg-slate-100 border border-slate-200/80 flex items-center justify-center text-slate-600">
                <i data-lucide="pie-chart" class="w-4 h-4"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-gray-900">Distribusi Error</h3>
                <p class="text-[11px] text-gray-500">Komposisi error berdasarkan tingkat keparahan</p>
            </div>
        </div>

        @php
            $other = $stats['total'] - $stats['critical'] - $stats['warning'];
            $criticalPct = $stats['total'] > 0 ? round($stats['critical'] / $stats['total'] * 100, 1) : 0;
            $warningPct  = $stats['total'] > 0 ? round($stats['warning']  / $stats['total'] * 100, 1) : 0;
            $otherPct    = $stats['total'] > 0 ? round($other             / $stats['total'] * 100, 1) : 0;
            $rows = [
                ['label' => 'Critical Errors', 'count' => $stats['critical'], 'pct' => $criticalPct, 'bar' => 'bg-rose-500',  'badge' => 'bg-rose-50 text-rose-700 border-rose-200/70'],
                ['label' => 'Warning Errors',  'count' => $stats['warning'],  'pct' => $warningPct,  'bar' => 'bg-amber-400', 'badge' => 'bg-amber-50 text-amber-700 border-amber-200/70'],
                ['label' => 'Other Errors',    'count' => $other,             'pct' => $otherPct,    'bar' => 'bg-blue-500',  'badge' => 'bg-blue-50 text-blue-700 border-blue-200/70'],
            ];
        @endphp

        <div class="space-y-4">
            @foreach ($rows as $row)
                <div>
                    <div class="flex items-center justify-between mb-1.5 text-xs">
                        <span class="font-semibold text-gray-800">{{ $row['label'] }}</span>
                        <div class="flex items-center gap-2">
                            <span class="text-gray-400">{{ $row['pct'] }}%</span>
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[11px] font-bold border {{ $row['badge'] }}">{{ $row['count'] }}</span>
                        </div>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2.5">
                        <div class="{{ $row['bar'] }} h-2.5 rounded-full" style="width: {{ $row['pct'] }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
@endpush
@endsection
