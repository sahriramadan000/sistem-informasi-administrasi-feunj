@extends('layouts.app')

@section('title', 'Error Logs - FEB UNJ')

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
            transition: all 0.2s ease;
        }
        .kpi-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 22px -4px rgba(0, 0, 0, 0.05);
        }
    </style>
@endpush

@section('content')
<div class="space-y-6 pb-12">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-gray-200/80">
        <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-rose-500 to-red-600 flex items-center justify-center text-white shadow-xs flex-shrink-0">
                <i data-lucide="alert-triangle" class="w-5 h-5"></i>
            </div>
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">Error Logs</h1>
                <p class="text-xs sm:text-sm text-gray-500">Monitor dan telusuri pengecualian sistem (14 hari terakhir)</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('admin.dashboard') }}"
                class="inline-flex items-center gap-2 px-4 py-2 text-xs sm:text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-200 rounded-full shadow-2xs hover:shadow-xs transition-all active:scale-[0.98]">
                <i data-lucide="arrow-left" class="w-4 h-4 text-gray-500"></i>
                <span>Pengaturan</span>
            </a>
            <a href="{{ route('admin.error-logs.statistics') }}"
                class="inline-flex items-center gap-2 px-4.5 py-2 text-xs sm:text-sm font-bold text-white bg-gradient-to-r from-[#FF4D00] to-amber-500 hover:from-[#e04400] hover:to-[#d97706] rounded-full shadow-xs hover:shadow-md transition-all active:scale-[0.98]">
                <i data-lucide="bar-chart-3" class="w-4 h-4"></i>
                <span>Lihat Statistik</span>
            </a>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <div class="kpi-card p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Total Error</span>
                <div class="w-7 h-7 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <i data-lucide="activity" class="w-3.5 h-3.5"></i>
                </div>
            </div>
            <p class="text-2xl font-extrabold text-gray-900">{{ $stats['total'] }}</p>
        </div>

        <div class="kpi-card p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Critical</span>
                <div class="w-7 h-7 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                    <i data-lucide="alert-octagon" class="w-3.5 h-3.5"></i>
                </div>
            </div>
            <p class="text-2xl font-extrabold text-rose-600">{{ $stats['critical'] }}</p>
        </div>

        <div class="kpi-card p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Warnings</span>
                <div class="w-7 h-7 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <i data-lucide="alert-triangle" class="w-3.5 h-3.5"></i>
                </div>
            </div>
            <p class="text-2xl font-extrabold text-amber-600">{{ $stats['warning'] }}</p>
        </div>

        <div class="kpi-card p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Tipe</span>
                <div class="w-7 h-7 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                    <i data-lucide="code" class="w-3.5 h-3.5"></i>
                </div>
            </div>
            <p class="text-2xl font-extrabold text-purple-600">{{ $stats['unique_types'] }}</p>
        </div>

        <div class="kpi-card p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">User</span>
                <div class="w-7 h-7 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i data-lucide="users" class="w-3.5 h-3.5"></i>
                </div>
            </div>
            <p class="text-2xl font-extrabold text-emerald-600">{{ $stats['unique_users'] }}</p>
        </div>

        <div class="kpi-card p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Rata-rata</span>
                <div class="w-7 h-7 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <i data-lucide="trending-up" class="w-3.5 h-3.5"></i>
                </div>
            </div>
            <p class="text-2xl font-extrabold text-indigo-600">{{ $stats['avg_per_day'] }}</p>
        </div>
    </div>

    {{-- Filter + Table Card --}}
    <div class="mac-card overflow-hidden">
        {{-- Collapsible Filter --}}
        <div class="border-b border-gray-100">
            <button type="button" onclick="toggleFilter()"
                class="w-full flex items-center justify-between px-6 py-4 text-left hover:bg-gray-50/70 transition-colors cursor-pointer">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-orange-50 border border-orange-200/70 flex items-center justify-center text-[#FF4D00]">
                        <i data-lucide="sliders-horizontal" class="w-4 h-4"></i>
                    </div>
                    <h3 class="text-sm font-bold text-gray-900">Filter Pencarian Error</h3>
                    @if(request()->hasAny(['search', 'exception_type', 'date_from', 'date_to', 'context']))
                        <span class="inline-flex items-center gap-1 rounded-full bg-orange-50 border border-orange-200/70 px-2.5 py-0.5 text-[11px] font-bold text-orange-700">
                            <i data-lucide="filter" class="w-3 h-3"></i>
                            Aktif
                        </span>
                    @endif
                </div>
                <i data-lucide="chevron-down" id="filter-icon" class="w-4 h-4 text-gray-400 transition-transform duration-200"></i>
            </button>

            <div id="filter-content" class="hidden border-t border-gray-100 bg-gray-50/40 p-6">
                <form method="GET" action="{{ route('admin.error-logs.index') }}">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 items-end">
                        <div class="space-y-1.5">
                            <label for="search" class="text-xs font-semibold text-gray-700 block">Cari Error ID / Pesan</label>
                            <input type="text" id="search" name="search" value="{{ request('search') }}"
                                placeholder="ERR_... atau pesan error"
                                class="w-full px-3.5 py-2 text-xs sm:text-sm bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-orange-500/10 focus:border-[#FF4D00]">
                        </div>

                        <div class="space-y-1.5">
                            <label for="exception_type" class="text-xs font-semibold text-gray-700 block">Tipe Exception</label>
                            <select id="exception_type" name="exception_type"
                                class="w-full px-3.5 py-2 text-xs sm:text-sm bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-orange-500/10 focus:border-[#FF4D00]">
                                <option value="">Semua Tipe</option>
                                @foreach ($exceptionTypes as $type)
                                    <option value="{{ $type }}" {{ request('exception_type') === $type ? 'selected' : '' }}>
                                        {{ $type }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-1.5">
                            <label for="context" class="text-xs font-semibold text-gray-700 block">Context</label>
                            <input type="text" id="context" name="context" value="{{ request('context') }}"
                                placeholder="misal: LetterController.store"
                                class="w-full px-3.5 py-2 text-xs sm:text-sm bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-orange-500/10 focus:border-[#FF4D00]">
                        </div>

                        <div class="space-y-1.5">
                            <label for="date_from" class="text-xs font-semibold text-gray-700 block">Tanggal Dari</label>
                            <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}"
                                class="w-full px-3.5 py-2 text-xs sm:text-sm bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-orange-500/10 focus:border-[#FF4D00]">
                        </div>

                        <div class="space-y-1.5">
                            <label for="date_to" class="text-xs font-semibold text-gray-700 block">Tanggal Sampai</label>
                            <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}"
                                class="w-full px-3.5 py-2 text-xs sm:text-sm bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-orange-500/10 focus:border-[#FF4D00]">
                        </div>

                        <div class="flex items-center gap-2.5 sm:col-span-2 lg:col-span-3">
                            <button type="submit"
                                class="inline-flex items-center justify-center gap-1.5 px-5 py-2.5 bg-gradient-to-r from-[#FF4D00] to-amber-500 hover:from-[#e04400] hover:to-[#d97706] text-white text-xs font-bold rounded-xl shadow-xs transition-all cursor-pointer">
                                <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                                <span>Terapkan Filter</span>
                            </button>
                            <a href="{{ route('admin.error-logs.index') }}"
                                class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 bg-white hover:bg-gray-100 border border-gray-200 text-gray-700 text-xs font-semibold rounded-xl shadow-2xs transition-all">
                                <i data-lucide="x" class="w-3.5 h-3.5"></i>
                                <span>Reset</span>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Per Page Bar --}}
        <div class="border-b border-gray-100 px-6 py-3 flex items-center justify-between text-xs text-gray-500">
            <div class="flex items-center gap-2">
                <span>Tampilkan:</span>
                <form method="GET" action="{{ route('admin.error-logs.index') }}" class="inline-block m-0">
                    @if (request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                    @if (request('exception_type')) <input type="hidden" name="exception_type" value="{{ request('exception_type') }}"> @endif
                    @if (request('context')) <input type="hidden" name="context" value="{{ request('context') }}"> @endif
                    @if (request('date_from')) <input type="hidden" name="date_from" value="{{ request('date_from') }}"> @endif
                    @if (request('date_to')) <input type="hidden" name="date_to" value="{{ request('date_to') }}"> @endif

                    <select name="per_page" id="per_page"
                        class="px-2.5 py-1 text-xs font-semibold border border-gray-200 rounded-lg bg-gray-50/70 hover:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-[#FF4D00] cursor-pointer"
                        onchange="this.form.submit()">
                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page', 10) == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page', 10) == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page', 10) == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </form>
                <span>data per halaman</span>
            </div>
            <span>Total: <span class="font-bold text-gray-800">{{ $stats['total'] }}</span> error</span>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            @if (count($errors) > 0)
                <table class="min-w-full divide-y divide-gray-100 text-left">
                    <thead>
                        <tr class="bg-gray-50/60 border-b border-gray-100">
                            <th scope="col" class="pl-6 pr-3 py-3 text-center text-[11px] font-bold text-gray-500 uppercase tracking-wider w-12">No</th>
                            <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider">Error ID</th>
                            <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider">Tipe</th>
                            <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider hidden lg:table-cell">Context</th>
                            <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider">Pesan</th>
                            <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider hidden xl:table-cell">Pengguna</th>
                            <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider hidden lg:table-cell">Waktu</th>
                            <th scope="col" class="pl-4 pr-6 py-3 text-right text-[11px] font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @foreach ($errors as $index => $error)
                            @php
                                $isCritical = in_array($error['exception_type'], ['FatalError', 'ParseError', 'TypeError']);
                            @endphp
                            <tr class="hover:bg-rose-50/20 transition-colors">
                                <td class="pl-6 pr-3 py-3.5 text-center text-xs font-semibold text-gray-400">
                                    {{ ($currentPage - 1) * request('per_page', 10) + $index + 1 }}
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <code class="text-xs bg-gray-100 px-2.5 py-1 rounded-lg text-gray-800 font-mono border border-gray-200/60">
                                        {{ $error['error_id'] }}
                                    </code>
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    @if ($isCritical)
                                        <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200/70">
                                            <i data-lucide="alert-octagon" class="w-3 h-3"></i>
                                            {{ $error['exception_type'] }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200/70">
                                            <i data-lucide="alert-triangle" class="w-3 h-3"></i>
                                            {{ $error['exception_type'] }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap text-xs font-mono text-gray-600 hidden lg:table-cell">
                                    {{ $error['context'] }}
                                </td>
                                <td class="px-4 py-3.5 text-xs text-gray-700 max-w-xs">
                                    <span class="truncate block max-w-[230px]" title="{{ $error['message'] }}">
                                        {{ Str::limit($error['message'], 55) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap text-xs hidden xl:table-cell">
                                    @if ($error['user_id'])
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-gray-100 text-gray-800">
                                            {{ $error['user_name'] }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-gray-100 text-gray-400 italic">
                                            Sistem
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap text-xs text-gray-500 hidden lg:table-cell font-mono">
                                    {{ \Carbon\Carbon::parse($error['timestamp'])->format('d/m/Y H:i') }}
                                </td>
                                <td class="pl-4 pr-6 py-3.5 whitespace-nowrap text-right">
                                    <a href="{{ route('admin.error-logs.show', $error['error_id']) }}"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-[11px] font-bold text-gray-700 bg-gray-100/90 hover:bg-gray-900 hover:text-white transition-colors">
                                        <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                        <span>Detail</span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Pagination --}}
                @if ($totalPages > 1)
                    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/40 flex items-center justify-between text-xs">
                        <p class="text-gray-500">
                            Halaman <span class="font-bold text-gray-800">{{ $currentPage }}</span> dari
                            <span class="font-bold text-gray-800">{{ $totalPages }}</span>
                        </p>
                        <nav class="flex items-center gap-1.5">
                            @if ($currentPage > 1)
                                <a href="{{ request()->fullUrlWithQuery(['page' => 1]) }}"
                                    class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-3 py-1.5 font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
                                    Pertama
                                </a>
                                <a href="{{ request()->fullUrlWithQuery(['page' => $currentPage - 1]) }}"
                                    class="inline-flex items-center rounded-xl border border-gray-200 bg-white p-1.5 text-gray-700 hover:bg-gray-50 transition-colors">
                                    <i data-lucide="chevron-left" class="w-4 h-4"></i>
                                </a>
                            @endif
                            @if ($currentPage < $totalPages)
                                <a href="{{ request()->fullUrlWithQuery(['page' => $currentPage + 1]) }}"
                                    class="inline-flex items-center rounded-xl border border-gray-200 bg-white p-1.5 text-gray-700 hover:bg-gray-50 transition-colors">
                                    <i data-lucide="chevron-right" class="w-4 h-4"></i>
                                </a>
                                <a href="{{ request()->fullUrlWithQuery(['page' => $totalPages]) }}"
                                    class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-3 py-1.5 font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
                                    Terakhir
                                </a>
                            @endif
                        </nav>
                    </div>
                @endif
            @else
                <div class="text-center py-14 px-6">
                    <div class="w-14 h-14 mx-auto rounded-2xl bg-gray-100 flex items-center justify-center text-gray-400 mb-3">
                        <i data-lucide="check-circle-2" class="w-7 h-7 text-emerald-500"></i>
                    </div>
                    <h3 class="text-sm font-bold text-gray-900">Tidak ada error ditemukan</h3>
                    <p class="mt-1 text-xs text-gray-500">Belum ada error yang tercatat sesuai filter yang dipilih.</p>
                    <div class="mt-5">
                        <a href="{{ route('admin.error-logs.index') }}"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-200 bg-white text-xs font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
                            <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i>
                            <span>Reset Filter</span>
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    function toggleFilter() {
        const content = document.getElementById('filter-content');
        const icon = document.getElementById('filter-icon');
        content.classList.toggle('hidden');
        icon.style.transform = content.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
    }

    document.addEventListener('DOMContentLoaded', function () {
        @if(request()->hasAny(['search', 'exception_type', 'date_from', 'date_to', 'context']))
            const content = document.getElementById('filter-content');
            const icon = document.getElementById('filter-icon');
            content.classList.remove('hidden');
            icon.style.transform = 'rotate(180deg)';
        @endif

        if (typeof $ !== 'undefined' && $('#exception_type').length) {
            $('#exception_type').select2({ placeholder: 'Semua Tipe', allowClear: true, width: '100%' });
        }

        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
@endpush
@endsection
