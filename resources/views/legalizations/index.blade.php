@extends('layouts.app')

@section('title', 'Daftar Legalisir - FEB UNJ')
@section('disable_layout_alerts', true)

@push('styles')
    <style>
        /* iOS / macOS Subtle Cards */
        .mac-card {
            background: #ffffff;
            border-radius: 1.25rem;
            border: 1px solid rgba(226, 232, 240, 0.85);
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.03), 0 2px 6px -1px rgba(0, 0, 0, 0.02);
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
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



        /* Custom Scrollbar */
        .table-scrollbar::-webkit-scrollbar {
            height: 6px;
        }
        .table-scrollbar::-webkit-scrollbar-track {
            background: #f8fafc;
        }
        .table-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }
        .table-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
@endpush

@section('content')
    @php
        $activeFiltersCount = 0;
        if (request('date_range')) $activeFiltersCount++;
        if (request('education_level_id')) $activeFiltersCount++;
        if (request('search')) $activeFiltersCount++;

        $canManage = auth()->user()->isAdmin() || auth()->user()->isOperator();
    @endphp

    <div class="w-full space-y-6 pb-12 relative">
        {{-- Floating Toast Notification (Auto Dismiss) --}}
        <div id="toastPopup" class="toast-popup fixed top-5 left-1/2 z-[9999] flex items-center gap-3 px-5 py-3 rounded-2xl bg-white/95 backdrop-blur-md border border-gray-200 shadow-xl text-xs sm:text-sm font-medium">
            <div id="toastIconWrap" class="w-7 h-7 rounded-xl flex items-center justify-center flex-shrink-0 bg-emerald-100 text-emerald-600">
                <i id="toastIcon" data-lucide="check" class="w-4 h-4"></i>
            </div>
            <span id="toastMessage" class="text-gray-800"></span>
            <button type="button" onclick="hideToast()" class="text-gray-400 hover:text-gray-600 ml-2 p-0.5 rounded-lg transition-colors cursor-pointer">
                <i data-lucide="x" class="w-3.5 h-3.5"></i>
            </button>
        </div>

        {{-- Page Header (Apple macOS Toolbar Style) --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-gray-200/80">
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-[#FF4D00] to-amber-500 flex items-center justify-center text-white shadow-xs flex-shrink-0">
                    <i data-lucide="files" class="w-5 h-5"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">Daftar Legalisir</h1>
                    <p class="text-xs sm:text-sm text-gray-500">Kelola, pantau, dan telusuri seluruh transaksi legalisasi ijazah & transkrip nilai alumni FEB UNJ</p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                {{-- Shortcut ke Dashboard Legalisir --}}
                <a href="{{ route('legalizations.dashboard') }}"
                    class="inline-flex items-center gap-2 px-3.5 py-2 text-xs sm:text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-200 rounded-full shadow-2xs hover:shadow-xs transition-all active:scale-[0.98]"
                    title="Buka Dashboard Legalisir">
                    <i data-lucide="layout-dashboard" class="w-4 h-4 text-gray-500"></i>
                    <span>Dashboard</span>
                </a>

                {{-- Export Excel Rekon --}}
                <button type="button" onclick="openExportModal()"
                    class="inline-flex items-center gap-2 px-3.5 py-2 text-xs sm:text-sm font-semibold text-gray-700 bg-white hover:bg-emerald-50 hover:text-emerald-700 border border-gray-200 hover:border-emerald-200 rounded-full shadow-2xs hover:shadow-xs transition-all active:scale-[0.98] cursor-pointer"
                    title="Export Laporan Rekon Excel (.xlsx)">
                    <i data-lucide="file-spreadsheet" class="w-4 h-4 text-emerald-600"></i>
                    <span>Export Excel</span>
                </button>

                @if ($canManage)
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('legalizations.import.template') }}"
                            class="inline-flex items-center gap-2 px-3.5 py-2 text-xs sm:text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-200 rounded-full shadow-2xs hover:shadow-xs transition-all active:scale-[0.98]"
                            title="Unduh Template Excel">
                            <i data-lucide="download" class="w-4 h-4 text-gray-500"></i>
                            <span>Template</span>
                        </a>
                        <button type="button" onclick="openImportModal()"
                            class="inline-flex items-center gap-2 px-3.5 py-2 text-xs sm:text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-200 rounded-full shadow-2xs hover:shadow-xs transition-all active:scale-[0.98] cursor-pointer"
                            title="Import Transaksi dari Excel">
                            <i data-lucide="upload" class="w-4 h-4 text-gray-500"></i>
                            <span>Import Excel</span>
                        </button>
                    @endif

                    <a href="{{ route('legalizations.create') }}"
                        class="inline-flex items-center gap-2 px-4.5 py-2 text-xs sm:text-sm font-bold text-white bg-gradient-to-r from-[#FF4D00] to-amber-500 hover:from-[#e04400] hover:to-[#d97706] rounded-full shadow-xs hover:shadow-md transition-all active:scale-[0.98]">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        <span>Buat Legalisir</span>
                    </a>
                @endif
            </div>
        </div>



        {{-- MAIN DATA CONTAINER (Filter + Toolbar + Table) --}}
        <div class="mac-card overflow-hidden">
            
            {{-- Filter Collapsible Header --}}
            <div class="border-b border-gray-100">
                <div class="flex items-center justify-between px-5 py-3.5 bg-gray-50/50">
                    <button type="button" onclick="toggleFilter()"
                        class="flex items-center gap-2.5 text-left text-xs sm:text-sm font-bold text-gray-800 hover:text-gray-900 transition-colors cursor-pointer group">
                        <div class="w-7 h-7 rounded-lg bg-white border border-gray-200/80 shadow-2xs flex items-center justify-center text-gray-600 group-hover:text-[#FF4D00] group-hover:border-orange-200 transition-colors">
                            <i data-lucide="sliders-horizontal" class="w-3.5 h-3.5"></i>
                        </div>
                        <span>Filter &amp; Pencarian</span>

                        @if ($activeFiltersCount > 0)
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-orange-100 text-orange-800 border border-orange-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span>
                                {{ $activeFiltersCount }} Filter Aktif
                            </span>
                        @endif

                        <i data-lucide="chevron-down" id="filter-icon"
                            class="w-4 h-4 text-gray-400 group-hover:text-gray-600 transition-transform duration-200 ml-1"></i>
                    </button>

                    <div class="flex items-center gap-2">
                        @if ($activeFiltersCount > 0)
                            <a href="{{ route('legalizations.index') }}"
                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold text-gray-500 hover:text-red-600 bg-white border border-gray-200 shadow-2xs hover:border-red-200 transition-all">
                                <i data-lucide="rotate-ccw" class="w-3 h-3"></i>
                                <span>Reset Filter</span>
                            </a>
                        @endif
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-white border border-gray-200 text-gray-700 shadow-2xs">
                            <i data-lucide="hash" class="w-3.5 h-3.5 text-orange-500"></i>
                            Total: {{ number_format($legalizations->total(), 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                {{-- Filter Drawer Form --}}
                <div id="filter-content" class="hidden border-t border-gray-100 bg-gray-50/30 p-5 sm:p-6 transition-all duration-300">
                    <form method="GET" action="{{ route('legalizations.index') }}" id="filterForm">
                        @if (request('per_page'))
                            <input type="hidden" name="per_page" value="{{ request('per_page') }}">
                        @endif

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 items-end">
                            {{-- Date Range Filter --}}
                            <div class="space-y-1.5">
                                <label for="date_range" class="text-xs font-semibold text-gray-700 flex items-center gap-1.5">
                                    <i data-lucide="calendar" class="w-3.5 h-3.5 text-gray-400"></i>
                                    Tanggal Legalisir
                                </label>
                                <input type="text" id="date_range" name="date_range" value="{{ request('date_range') }}"
                                    class="w-full px-3 py-2 text-xs bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-[#FF4D00] shadow-2xs transition-all placeholder:text-gray-400 cursor-pointer"
                                    placeholder="Pilih rentang tanggal...">
                            </div>

                            {{-- Education Level Filter --}}
                            <div class="space-y-1.5">
                                <label for="filter_education_level_id" class="text-xs font-semibold text-gray-700 flex items-center gap-1.5">
                                    <i data-lucide="graduation-cap" class="w-3.5 h-3.5 text-gray-400"></i>
                                    Jenjang Pendidikan
                                </label>
                                <select id="filter_education_level_id" name="education_level_id" class="w-full px-3 py-2 text-xs bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-[#FF4D00] shadow-2xs transition-all cursor-pointer">
                                    <option value="">Semua Jenjang</option>
                                    @foreach ($educationLevels as $level)
                                        <option value="{{ $level->id }}" {{ request('education_level_id') == $level->id ? 'selected' : '' }}>
                                            {{ $level->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Search Alumni Name --}}
                            <div class="space-y-1.5">
                                <label for="search" class="text-xs font-semibold text-gray-700 flex items-center gap-1.5">
                                    <i data-lucide="search" class="w-3.5 h-3.5 text-gray-400"></i>
                                    Cari Nama Alumni
                                </label>
                                <input type="text" id="search" name="search" value="{{ request('search') }}"
                                    class="w-full px-3 py-2 text-xs bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-[#FF4D00] shadow-2xs transition-all placeholder:text-gray-400"
                                    placeholder="Ketik nama alumni...">
                            </div>

                            {{-- Submit & Reset Action Buttons --}}
                            <div class="flex items-center gap-2 col-span-full pt-2">
                                <button type="submit"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-[#FF4D00] to-amber-500 hover:from-[#e04400] hover:to-[#d97706] text-white font-bold text-xs rounded-xl shadow-xs transition-all active:scale-[0.98] cursor-pointer">
                                    <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                                    <span>Terapkan Filter</span>
                                </button>
                                <a href="{{ route('legalizations.index') }}"
                                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-white hover:bg-gray-100 border border-gray-200 text-gray-700 font-semibold text-xs rounded-xl shadow-2xs transition-all active:scale-[0.98]">
                                    <i data-lucide="x" class="w-3.5 h-3.5"></i>
                                    <span>Reset</span>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Table Control Bar (Per Page & Page Info) --}}
            <div class="px-5 py-3 border-b border-gray-100 bg-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs text-gray-500">
                <div class="flex items-center gap-2">
                    <span>Tampilkan:</span>
                    <form method="GET" action="{{ route('legalizations.index') }}" class="inline-block">
                        @if (request('education_level_id')) <input type="hidden" name="education_level_id" value="{{ request('education_level_id') }}"> @endif
                        @if (request('date_range')) <input type="hidden" name="date_range" value="{{ request('date_range') }}"> @endif
                        @if (request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif

                        <select name="per_page" id="per_page"
                            class="px-2.5 py-1 text-xs border border-gray-200 rounded-lg bg-gray-50/50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-[#FF4D00] transition-colors cursor-pointer"
                            onchange="this.form.submit()">
                            <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ request('per_page', 10) == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page', 10) == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page', 10) == 100 ? 'selected' : '' }}>100</option>
                        </select>
                    </form>
                    <span>data per halaman</span>
                </div>

                <div class="flex items-center gap-2">
                    <span>Halaman {{ $legalizations->currentPage() }} dari {{ $legalizations->lastPage() }}</span>
                </div>
            </div>

            {{-- Table View --}}
            <div class="overflow-x-auto table-scrollbar">
                @if ($legalizations->count() > 0)
                    <table class="min-w-full divide-y divide-gray-100 text-left">
                        <thead>
                            <tr class="bg-gray-50/60 border-b border-gray-100">
                                <th scope="col" class="px-3 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider w-10 text-center">No</th>
                                <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider min-w-[150px]">No. Legalisir</th>
                                <th scope="col" class="px-3.5 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider min-w-[110px]">Tanggal</th>
                                <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider min-w-[220px]">Nama Alumni</th>
                                <th scope="col" class="px-3.5 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider min-w-[130px] hidden sm:table-cell">Jenjang</th>
                                <th scope="col" class="px-3.5 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider text-center min-w-[90px]">Lembar</th>
                                <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider min-w-[130px]">Total Biaya</th>
                                <th scope="col" class="px-3.5 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider min-w-[120px] hidden lg:table-cell">Dibuat Oleh</th>
                                <th scope="col" class="px-3.5 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider text-center w-28">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach ($legalizations as $index => $legalization)
                                @php
                                    $alumniName = $legalization->alumni_name;
                                    $initials = collect(explode(' ', $alumniName))
                                        ->filter()
                                        ->take(2)
                                        ->map(fn($w) => strtoupper(substr($w, 0, 1)))
                                        ->join('');
                                    if (empty($initials)) {
                                        $initials = 'AL';
                                    }
                                    $runningNumberPadded = str_pad($legalization->running_number, 3, '0', STR_PAD_LEFT);
                                @endphp
                                <tr class="hover:bg-orange-50/30 transition-colors group">
                                    {{-- No --}}
                                    <td class="px-4 py-4 text-center text-xs font-semibold text-gray-400">
                                        {{ ($legalizations->currentPage() - 1) * $legalizations->perPage() + $index + 1 }}
                                    </td>

                                    {{-- No. Legalisir --}}
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('legalizations.show', $legalization) }}"
                                                class="font-mono text-[13.5px] sm:text-sm font-bold text-gray-900 group-hover:text-[#FF4D00] transition-colors leading-normal tracking-tight"
                                                title="Klik untuk melihat detail legalisir">
                                                #{{ $runningNumberPadded }}
                                            </a>
                                            <button type="button" onclick="copyToClipboard('{{ $runningNumberPadded }}', 'Nomor legalisir #{{ $runningNumberPadded }} disalin!')"
                                                class="opacity-0 group-hover:opacity-100 p-1.5 text-gray-400 hover:text-orange-600 transition-all rounded-lg hover:bg-orange-50 cursor-pointer flex-shrink-0"
                                                title="Salin Nomor">
                                                <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                                            </button>
                                        </div>
                                    </td>

                                    {{-- Tanggal --}}
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-1.5 text-xs font-semibold text-gray-700">
                                            <i data-lucide="calendar" class="w-3.5 h-3.5 text-gray-400"></i>
                                            <span>{{ $legalization->date ? $legalization->date->format('d/m/Y') : '-' }}</span>
                                        </div>
                                    </td>

                                    {{-- Nama Alumni --}}
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-amber-100 to-orange-100 text-[#FF4D00] border border-orange-200/60 flex items-center justify-center font-bold text-xs flex-shrink-0 shadow-2xs">
                                                {{ $initials }}
                                            </div>
                                            <div class="min-w-0">
                                                <a href="{{ route('legalizations.show', $legalization) }}"
                                                    class="text-xs sm:text-[13px] font-bold text-gray-900 hover:text-[#FF4D00] transition-colors leading-relaxed block break-words"
                                                    title="Klik untuk melihat detail alumni">
                                                    {{ $legalization->alumni_name }}
                                                </a>
                                                <p class="text-[11px] text-gray-400 flex items-center gap-1 mt-0.5">
                                                    <span>Lulusan Th.</span>
                                                    <span class="font-semibold text-gray-600">{{ $legalization->graduation_year }}</span>
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Jenjang Pendidikan --}}
                                    <td class="px-4 py-4 whitespace-nowrap hidden sm:table-cell">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold border bg-gray-100 text-gray-700 border-gray-200/80">
                                            <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span>
                                            {{ $legalization->educationLevel->name ?? '-' }}
                                        </span>
                                    </td>

                                    {{-- Lembar --}}
                                    <td class="px-4 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200/70 font-mono">
                                            {{ $legalization->page_count }} Lembar
                                        </span>
                                    </td>

                                    {{-- Total Biaya --}}
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <span class="font-mono text-xs sm:text-sm font-bold text-gray-900">
                                            Rp {{ number_format($legalization->total_price, 0, ',', '.') }}
                                        </span>
                                    </td>

                                    {{-- Dibuat Oleh --}}
                                    <td class="px-4 py-4 whitespace-nowrap hidden lg:table-cell">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700 border border-gray-200/80">
                                            <i data-lucide="user" class="w-3 h-3 text-gray-400"></i>
                                            {{ $legalization->creator?->name ?? 'Sistem' }}
                                        </span>
                                    </td>

                                    {{-- Aksi --}}
                                    <td class="px-3.5 py-3.5 text-center whitespace-nowrap">
                                        <div class="inline-flex items-center gap-1.5">
                                            <a href="{{ route('legalizations.show', $legalization) }}"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-orange-50 hover:bg-orange-100 text-[#FF4D00] hover:text-orange-700 font-bold text-xs rounded-full border border-orange-200/80 shadow-2xs transition-all active:scale-[0.96]"
                                                title="Lihat Detail Legalisir">
                                                <i data-lucide="eye" class="w-3.5 h-3.5 text-[#FF4D00]"></i>
                                                <span>Detail</span>
                                            </a>
                                            @if ($canManage)
                                                <a href="{{ route('legalizations.edit', $legalization) }}"
                                                    class="p-1.5 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors"
                                                    title="Edit Data">
                                                    <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                                </a>
                                                <button type="button"
                                                    onclick="openDeactivateModal('{{ route('legalizations.destroy', $legalization) }}', '#{{ $runningNumberPadded }}', '{{ addslashes($legalization->alumni_name) }}')"
                                                    class="p-1.5 text-gray-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors cursor-pointer"
                                                    title="Nonaktifkan Data">
                                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{-- Modern Pagination Footer --}}
                    <div class="border-t border-gray-100">
                        {{ $legalizations->appends(request()->query())->links() }}
                    </div>
                @else
                    {{-- Empty State (Apple Style) --}}
                    <div class="text-center py-16 px-4">
                        <div class="w-16 h-16 rounded-3xl bg-orange-50 border border-orange-200/80 flex items-center justify-center mx-auto mb-4 text-[#FF4D00] shadow-2xs">
                            <i data-lucide="inbox" class="w-8 h-8"></i>
                        </div>
                        <h3 class="text-base font-bold text-gray-900">Tidak Ada Data Legalisir</h3>
                        <p class="text-xs sm:text-sm text-gray-500 mt-1 max-w-sm mx-auto">
                            Belum ada permohonan legalisir yang tercatat sesuai filter yang dipilih saat ini.
                        </p>
                        <div class="mt-5 flex items-center justify-center gap-3">
                            @if ($activeFiltersCount > 0)
                                <a href="{{ route('legalizations.index') }}"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-white hover:bg-gray-50 border border-gray-200 text-gray-700 font-semibold text-xs rounded-full shadow-2xs transition-all">
                                    <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                                    <span>Reset Filter</span>
                                </a>
                            @endif
                            @if ($canManage)
                                <a href="{{ route('legalizations.create') }}"
                                    class="inline-flex items-center gap-2 px-5 py-2 bg-gradient-to-r from-[#FF4D00] to-amber-500 hover:from-[#e04400] hover:to-[#d97706] text-white font-bold text-xs rounded-full shadow-xs transition-all">
                                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                                    <span>Buat Legalisir</span>
                                </a>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- Import Excel Modal (Apple macOS Sheet Dialog) --}}
    <div id="importModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/40 backdrop-blur-md">
        <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full border border-gray-200 overflow-hidden transform transition-all scale-95 opacity-0"
            id="importModalContent">
            {{-- Modal Header --}}
            <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-orange-50 border border-orange-200/80 text-[#FF4D00] flex items-center justify-center shadow-2xs">
                        <i data-lucide="upload" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900 tracking-tight">Import Data Legalisir</h3>
                        <p class="text-xs text-gray-500">Unggah file Excel (.xls, .xlsx) transaksi legalisir</p>
                    </div>
                </div>
                <button type="button" onclick="closeImportModal()"
                    class="p-1 rounded-xl text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors cursor-pointer">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            {{-- Modal Body --}}
            <form action="{{ route('legalizations.import') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                <div class="space-y-2">
                    <label for="file_excel" class="text-xs font-bold uppercase tracking-wider text-gray-500">File Excel</label>
                    <div class="border-2 border-dashed border-gray-200 hover:border-orange-300 rounded-2xl p-6 text-center transition-colors bg-gray-50/50 hover:bg-orange-50/30">
                        <i data-lucide="file-spreadsheet" class="w-10 h-10 text-emerald-600 mx-auto mb-2"></i>
                        <input type="file" name="file_excel" id="file_excel" accept=".xls,.xlsx" required
                            class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100 cursor-pointer">
                        <p class="text-[11px] text-gray-400 mt-2">Pastikan struktur kolom sesuai dengan format template yang disediakan.</p>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center gap-3 pt-2">
                    <button type="button" onclick="closeImportModal()"
                        class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200/80 text-gray-700 font-semibold text-xs rounded-xl transition-all border border-gray-200 cursor-pointer">
                        <span>Batal</span>
                    </button>
                    <button type="submit"
                        class="flex-1 px-4 py-2.5 bg-gradient-to-r from-[#FF4D00] to-amber-500 hover:from-[#e04400] hover:to-[#d97706] text-white font-bold text-xs rounded-xl transition-all shadow-xs cursor-pointer">
                        <span>Unggah &amp; Import</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Export Excel Rekon Modal (Apple Sheet Style) --}}
    <div id="exportModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/40 backdrop-blur-md">
        <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full border border-gray-200 overflow-hidden transform transition-all scale-95 opacity-0"
            id="exportModalContent">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/70">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center shadow-2xs">
                        <i data-lucide="file-spreadsheet" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="text-sm sm:text-base font-bold text-gray-900 tracking-tight">Export Laporan Rekon Excel</h3>
                        <p class="text-[11px] text-gray-500">Unduh laporan transaksi legalisir terstruktur (.xlsx)</p>
                    </div>
                </div>
                <button type="button" onclick="closeExportModal()" class="text-gray-400 hover:text-gray-600 p-1.5 rounded-xl hover:bg-gray-100 transition-colors cursor-pointer">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <form method="GET" action="{{ route('legalizations.export') }}" onsubmit="handleExportSubmit()" class="p-6 space-y-4">
                @if (request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif

                {{-- Preset Periode Cepat --}}
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-gray-700 block">Pilih Cepat Periode Rekon</label>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" onclick="setExportPreset('current')"
                            class="px-3 py-1.5 text-xs font-semibold rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 transition-all cursor-pointer">
                            Sesuai Filter Halaman
                        </button>
                        <button type="button" onclick="setExportPreset('this_month')"
                            class="px-3 py-1.5 text-xs font-semibold rounded-xl border border-gray-200 bg-white hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-200 text-gray-700 transition-all cursor-pointer">
                            Bulan Ini
                        </button>
                        <button type="button" onclick="setExportPreset('last_month')"
                            class="px-3 py-1.5 text-xs font-semibold rounded-xl border border-gray-200 bg-white hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-200 text-gray-700 transition-all cursor-pointer">
                            Bulan Lalu
                        </button>
                        <button type="button" onclick="setExportPreset('all')"
                            class="px-3 py-1.5 text-xs font-semibold rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 transition-all cursor-pointer">
                            Semua Periode
                        </button>
                    </div>
                </div>

                {{-- Rentang Tanggal --}}
                <div class="space-y-1.5">
                    <label for="export_date_range" class="text-xs font-semibold text-gray-700 flex items-center gap-1.5">
                        <i data-lucide="calendar" class="w-3.5 h-3.5 text-gray-400"></i>
                        Rentang Tanggal Transaksi
                    </label>
                    <input type="text" id="export_date_range" name="date_range" value="{{ request('date_range') }}"
                        class="w-full px-3.5 py-2.5 text-xs bg-gray-50 hover:bg-white focus:bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all cursor-pointer font-medium text-gray-800"
                        placeholder="Kosongkan untuk semua periode, atau pilih rentang tanggal...">
                </div>

                {{-- Jenjang Pendidikan --}}
                <div class="space-y-1.5">
                    <label for="export_education_level_id" class="text-xs font-semibold text-gray-700 flex items-center gap-1.5">
                        <i data-lucide="graduation-cap" class="w-3.5 h-3.5 text-gray-400"></i>
                        Filter Jenjang Pendidikan
                    </label>
                    <select id="export_education_level_id" name="education_level_id"
                        class="w-full px-3.5 py-2.5 text-xs bg-gray-50 hover:bg-white focus:bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all cursor-pointer font-medium text-gray-800">
                        <option value="">Semua Jenjang Pendidikan</option>
                        @foreach ($educationLevels as $level)
                            <option value="{{ $level->id }}" {{ request('education_level_id') == $level->id ? 'selected' : '' }}>
                                {{ $level->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Info Template Rekon --}}
                <div class="p-3.5 rounded-2xl bg-emerald-50/70 border border-emerald-200/80 text-[11.5px] text-emerald-900 flex items-start gap-2.5">
                    <i data-lucide="sparkles" class="w-4 h-4 text-emerald-600 flex-shrink-0 mt-0.5"></i>
                    <div class="space-y-0.5">
                        <p class="font-bold">Format Siap Rekonsiliasi (2 Sheet):</p>
                        <p class="text-emerald-800/90 leading-relaxed">
                            File otomatis berisi <strong>Rincian Transaksi</strong> (lengkap dengan formula SUM, format Rupiah, kolom cek rekon &amp; tanda tangan) dan <strong>Rekapitulasi per Jenjang</strong>.
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="button" onclick="closeExportModal()"
                        class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200/80 text-gray-700 font-semibold text-xs rounded-xl transition-all border border-gray-200 cursor-pointer">
                        <span>Batal</span>
                    </button>
                    <button type="submit"
                        class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl transition-all shadow-xs cursor-pointer">
                        <i data-lucide="download" class="w-3.5 h-3.5"></i>
                        <span>Unduh Laporan (.xlsx)</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Deactivate Confirmation Modal (Apple Sheet Style) --}}
    <div id="deactivateModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/40 backdrop-blur-md">
        <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full border border-gray-200 overflow-hidden transform transition-all scale-95 opacity-0"
            id="deactivateModalContent">
            <div class="p-6">
                <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center mb-4 shadow-2xs">
                    <i data-lucide="alert-triangle" class="w-6 h-6"></i>
                </div>

                <h3 class="text-base sm:text-lg font-bold text-gray-900 tracking-tight" id="modal-deactivate-title">
                    Konfirmasi Nonaktifkan Data
                </h3>

                <p class="text-xs sm:text-sm text-gray-600 leading-relaxed mt-2">
                    Apakah Anda yakin ingin menonaktifkan data legalisir <strong id="modalRunningNumber" class="text-gray-900 font-mono"></strong> atas nama <strong id="modalAlumniName" class="text-gray-900"></strong>?
                </p>

                <div class="mt-3 p-3 rounded-xl bg-amber-50 border border-amber-200/80 text-[11px] text-amber-800 flex items-start gap-2">
                    <i data-lucide="info" class="w-4 h-4 text-amber-600 flex-shrink-0 mt-0.5"></i>
                    <span>Nomor legalisir ini akan diarsipkan dan tidak akan muncul dalam pelaporan aktif. Riwayat transaksi tetap tercatat secara aman.</span>
                </div>

                <div class="mt-6 flex items-center justify-end gap-2.5">
                    <button type="button" onclick="closeDeactivateModal()"
                        class="px-4 py-2 text-xs sm:text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-all cursor-pointer">
                        Batal
                    </button>
                    <form id="deactivateForm" method="POST" action="">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="px-4.5 py-2 text-xs sm:text-sm font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl shadow-xs transition-all cursor-pointer">
                            Ya, Nonaktifkan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Error Modal (Import Errors Detail) --}}
    @if (session('import_errors') && count(session('import_errors')) > 0)
        <div id="errorModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-md">
            <div class="bg-white rounded-3xl p-6 sm:p-7 text-left overflow-hidden shadow-2xl border border-gray-200 max-w-2xl w-full">
                <div class="flex items-start gap-3.5 pb-4 border-b border-gray-100">
                    <div class="w-11 h-11 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center flex-shrink-0 shadow-2xs">
                        <i data-lucide="alert-circle" class="w-6 h-6"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-base sm:text-lg font-bold text-gray-900">
                            Import Data Belum Sempurna
                        </h3>
                        <p class="text-xs sm:text-sm text-gray-500 mt-0.5">
                            Ditemukan <span class="font-bold text-rose-600">{{ count(session('import_errors')) }} baris</span> dengan format data yang memerlukan perbaikan.
                        </p>
                    </div>
                    <button type="button" onclick="closeErrorModal()" class="text-gray-400 hover:text-gray-600 p-1 rounded-xl transition-colors cursor-pointer">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                {{-- Error List --}}
                <div class="mt-4 max-h-80 overflow-y-auto space-y-3 pr-1 table-scrollbar">
                    @foreach (session('import_errors') as $error)
                        <div class="p-4 border border-rose-200/80 bg-rose-50/60 rounded-2xl">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="inline-flex items-center rounded-full bg-rose-100 px-2.5 py-0.5 text-xs font-bold text-rose-800">
                                    Baris {{ $error['row'] }}
                                </span>
                                <span class="text-xs sm:text-sm font-bold text-gray-900">
                                    Kolom: {{ $error['field'] }}
                                </span>
                            </div>

                            <div class="flex items-start gap-2 mb-2">
                                <i data-lucide="x-circle" class="h-4 w-4 text-rose-600 mt-0.5 flex-shrink-0"></i>
                                <p class="text-xs sm:text-sm font-medium text-rose-700">
                                    {{ $error['message'] }}
                                </p>
                            </div>

                            @if ($error['value'])
                                <div class="ml-6 mb-2">
                                    <span class="text-[11px] text-gray-500">Nilai yang diinput:</span>
                                    <code class="block mt-0.5 bg-white px-2.5 py-1.5 rounded-lg border border-rose-200 text-xs font-mono text-gray-800">
                                        {{ $error['value'] }}
                                    </code>
                                </div>
                            @endif

                            @if ($error['suggestions'])
                                <div class="ml-6 mt-2 p-2.5 bg-emerald-50/80 border border-emerald-200/70 rounded-xl flex items-start gap-2">
                                    <i data-lucide="lightbulb" class="h-4 w-4 text-emerald-600 mt-0.5 flex-shrink-0"></i>
                                    <div class="text-xs text-emerald-800">
                                        <span class="font-bold">Saran Perbaikan:</span>
                                        <span>{{ $error['suggestions'] }}</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                {{-- Tips Footer --}}
                <div class="mt-4 pt-4 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <a href="{{ route('legalizations.import.template') }}"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-bold text-gray-700 bg-white hover:bg-gray-50 border border-gray-200 rounded-xl shadow-2xs transition-all">
                        <i data-lucide="download" class="w-3.5 h-3.5 text-gray-500"></i>
                        <span>Unduh Template Ulang</span>
                    </a>

                    <button type="button" onclick="closeErrorModal()"
                        class="px-4.5 py-2 text-xs font-bold text-white bg-gray-900 hover:bg-gray-800 rounded-xl shadow-xs transition-all cursor-pointer">
                        Tutup &amp; Perbaiki
                    </button>
                </div>
            </div>
        </div>
    @endif

@endsection

@push('scripts')
<script>
    // Filter Drawer Toggle with Rotation Animation
    function toggleFilter() {
        const content = document.getElementById('filter-content');
        const icon = document.getElementById('filter-icon');
        if (!content || !icon) return;

        if (content.classList.contains('hidden')) {
            content.classList.remove('hidden');
            icon.style.transform = 'rotate(180deg)';
        } else {
            content.classList.add('hidden');
            icon.style.transform = 'rotate(0deg)';
        }
    }

    // Modal Helpers: Import Modal with smooth scale/opacity animation
    function openImportModal() {
        const modal = document.getElementById('importModal');
        const content = document.getElementById('importModalContent');
        if (!modal || !content) return;

        modal.classList.remove('hidden');
        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function closeImportModal() {
        const modal = document.getElementById('importModal');
        const content = document.getElementById('importModalContent');
        if (!modal || !content) return;

        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 200);
    }

    // Modal Helpers: Deactivate Modal
    function openDeactivateModal(actionUrl, runningNumber, alumniName) {
        const modal = document.getElementById('deactivateModal');
        const content = document.getElementById('deactivateModalContent');
        const form = document.getElementById('deactivateForm');
        const numElem = document.getElementById('modalRunningNumber');
        const nameElem = document.getElementById('modalAlumniName');

        if (modal && content && form) {
            form.action = actionUrl;
            if (numElem) numElem.textContent = runningNumber;
            if (nameElem) nameElem.textContent = alumniName;

            modal.classList.remove('hidden');
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }
    }

    function closeDeactivateModal() {
        const modal = document.getElementById('deactivateModal');
        const content = document.getElementById('deactivateModalContent');
        if (!modal || !content) return;

        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 200);
    }

    function closeErrorModal() {
        const modal = document.getElementById('errorModal');
        if (modal) modal.classList.add('hidden');
    }

    // Copy to Clipboard with Feedback
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

    // Floating Toast Notification
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
        } else if (type === 'error') {
            toastIconWrap.className = 'w-7 h-7 rounded-xl flex items-center justify-center flex-shrink-0 bg-rose-100 text-rose-600';
            toastIcon.setAttribute('data-lucide', 'alert-circle');
        } else {
            toastIconWrap.className = 'w-7 h-7 rounded-xl flex items-center justify-center flex-shrink-0 bg-blue-100 text-blue-600';
            toastIcon.setAttribute('data-lucide', 'info');
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

    let exportDatePicker = null;
    const currentPageDateRange = @json(request('date_range', ''));

    function openExportModal() {
        const modal = document.getElementById('exportModal');
        const content = document.getElementById('exportModalContent');
        if (modal && content) {
            modal.classList.remove('hidden');
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }
    }

    function closeExportModal() {
        const modal = document.getElementById('exportModal');
        const content = document.getElementById('exportModalContent');
        if (!modal || !content) return;

        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 200);
    }

    function setExportPreset(preset) {
        const input = document.getElementById('export_date_range');
        if (!input) return;

        const now = new Date();
        const pad = n => String(n).padStart(2, '0');
        const formatYmd = d => `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;

        if (preset === 'current') {
            if (exportDatePicker) {
                if (currentPageDateRange) {
                    const sep = currentPageDateRange.includes(' to ') ? ' to ' : ' - ';
                    exportDatePicker.setDate(currentPageDateRange.split(sep).map(s => s.trim()), true);
                } else {
                    exportDatePicker.clear();
                }
            } else {
                input.value = currentPageDateRange;
            }
        } else if (preset === 'this_month') {
            const start = new Date(now.getFullYear(), now.getMonth(), 1);
            const end = new Date(now.getFullYear(), now.getMonth() + 1, 0);
            if (exportDatePicker) {
                exportDatePicker.setDate([start, end], true);
            } else {
                input.value = `${formatYmd(start)} to ${formatYmd(end)}`;
            }
        } else if (preset === 'last_month') {
            const start = new Date(now.getFullYear(), now.getMonth() - 1, 1);
            const end = new Date(now.getFullYear(), now.getMonth(), 0);
            if (exportDatePicker) {
                exportDatePicker.setDate([start, end], true);
            } else {
                input.value = `${formatYmd(start)} to ${formatYmd(end)}`;
            }
        } else if (preset === 'all') {
            if (exportDatePicker) {
                exportDatePicker.clear();
            } else {
                input.value = '';
            }
        }
    }

    function handleExportSubmit() {
        showToast('Menyiapkan laporan Excel rekon...', 'info');
        setTimeout(() => {
            closeExportModal();
        }, 600);
    }

    // On Page Ready
    document.addEventListener('DOMContentLoaded', function () {
        @if ($activeFiltersCount > 0)
            const content = document.getElementById('filter-content');
            const icon = document.getElementById('filter-icon');
            if (content && icon) {
                content.classList.remove('hidden');
                icon.style.transform = 'rotate(180deg)';
            }
        @endif

        if (typeof flatpickr !== 'undefined') {
            flatpickr("#date_range", {
                mode: "range",
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d M Y",
                locale: "id",
                placeholder: "Pilih tanggal start - end"
            });

            exportDatePicker = flatpickr("#export_date_range", {
                mode: "range",
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d M Y",
                locale: "id",
                placeholder: "Kosongkan untuk semua periode..."
            });
        }

        // Keyboard Shortcut: '/' to focus search input
        window.addEventListener('keydown', function(e) {
            if (e.key === '/' && !['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement.tagName)) {
                e.preventDefault();
                const searchInput = document.getElementById('search');
                if (searchInput) {
                    searchInput.focus();
                    searchInput.select();
                }
            }
        });

        @if (session('success'))
            showToast("{{ session('success') }}", 'success');
        @elseif (session('error'))
            showToast("{{ session('error') }}", 'error');
        @endif

        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
@endpush
