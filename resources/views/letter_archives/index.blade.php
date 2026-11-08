@extends('layouts.app')

@section('title', 'Arsip Digital - FEB UNJ')
@section('disable_layout_alerts', true)

@push('styles')
    <style>
        /* Apple macOS Window Container Frame */
        .mac-window {
            background: #ffffff;
            border-radius: 1.5rem;
            border: 1px solid rgba(226, 232, 240, 0.95);
            box-shadow: 0 4px 24px -4px rgba(0, 0, 0, 0.05), 0 1px 3px 0 rgba(0, 0, 0, 0.02);
        }

        /* Folders Card Surface */
        .mac-surface {
            background: #ffffff;
            border-radius: 1.15rem;
            border: 1px solid rgba(226, 232, 240, 0.95);
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.02), 0 1px 2px -1px rgba(0, 0, 0, 0.02);
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            user-select: none;
        }
        .mac-surface:hover {
            transform: translateY(-2px);
            border-color: #cbd5e1;
            box-shadow: 0 8px 20px -4px rgba(15, 23, 42, 0.06), 0 2px 6px -1px rgba(15, 23, 42, 0.02);
        }
        .mac-surface.selected {
            border-color: #94a3b8 !important;
            background: #f8fafc !important;
            box-shadow: 0 0 0 2px rgba(148, 163, 184, 0.25), 0 4px 12px -2px rgba(15, 23, 42, 0.04) !important;
        }
        .mac-surface.selected .folder-indicator {
            border-color: #64748b !important;
        }
        .mac-surface.selected .folder-indicator span {
            background-color: #64748b !important;
        }

        /* Rectangular File Card (Apple Finder / iOS Files style) */
        .mac-file-card {
            background: #ffffff;
            border-radius: 1.15rem;
            border: 1px solid rgba(226, 232, 240, 0.95);
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.02), 0 1px 2px -1px rgba(0, 0, 0, 0.02);
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            user-select: none;
        }
        .mac-file-card:hover {
            transform: translateY(-2px);
            border-color: #cbd5e1;
            box-shadow: 0 8px 20px -4px rgba(15, 23, 42, 0.06), 0 2px 6px -1px rgba(15, 23, 42, 0.02);
        }
        .mac-file-card.selected {
            border-color: #94a3b8 !important;
            background: #f8fafc !important;
            box-shadow: 0 0 0 2px rgba(148, 163, 184, 0.25), 0 4px 12px -2px rgba(15, 23, 42, 0.04) !important;
        }

        /* Apple Segmented Control Pill */
        .mac-segmented-track {
            background: #f1f5f9;
            padding: 3px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            border: 1px solid rgba(226, 232, 240, 0.85);
        }
        .mac-segmented-btn {
            padding: 5px 12px;
            border-radius: 9px;
            font-size: 12px;
            font-weight: 500;
            color: #64748b;
            transition: all 0.15s ease;
            white-space: nowrap;
        }
        .mac-segmented-btn:hover {
            color: #0f172a;
        }
        .mac-segmented-btn.active {
            background: #ffffff;
            color: #0f172a;
            font-weight: 600;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06), 0 1px 2px rgba(0, 0, 0, 0.04);
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

        /* Line Clamp Utilities */
        .line-clamp-1 {
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
@endpush

@section('content')
    @php
        // Filter aktif HANYA dihitung dari filter eksplisit pengguna, BUKAN dari navigasi folder (year, letter_type_id, classification_id)
        $activeFiltersCount = 0;
        if (request('signatory_id')) $activeFiltersCount++;
        if (request('security_classification')) $activeFiltersCount++;
        if (request('letter_target')) $activeFiltersCount++;
        if (request('search')) $activeFiltersCount++;

        $isReadOnlyPortal = $isReadOnlyPortal ?? false;
        $baseRouteName = $baseRouteName ?? ($isReadOnlyPortal ? 'archives.index' : 'letter-archives.index');
        $canManage = !$isReadOnlyPortal && (auth()->user()->isAdmin() || auth()->user()->isOperator());

        // Label helper for active filter tags
        $activeSignatory = request('signatory_id') ? $signatories->firstWhere('id', request('signatory_id')) : null;
        $activeSecClassLabel = request('security_classification') ? ($securityClassifications[request('security_classification')] ?? request('security_classification')) : null;
        $activeTargetLabel = request('letter_target') ? ($letterTargets[request('letter_target')] ?? ucfirst(request('letter_target'))) : null;
    @endphp

    <div class="w-full pb-16 relative">
        {{-- Floating Toast Notification --}}
        <div id="toastPopup" class="toast-popup fixed top-5 left-1/2 z-[9999] flex items-center gap-3 px-5 py-3 rounded-2xl bg-white/95 backdrop-blur-md border border-gray-200 shadow-xl text-xs sm:text-sm font-medium">
            <div id="toastIconWrap" class="w-7 h-7 rounded-xl flex items-center justify-center flex-shrink-0 bg-slate-100 text-slate-800 border border-slate-200">
                <i id="toastIcon" data-lucide="check" class="w-4 h-4"></i>
            </div>
            <span id="toastMessage" class="text-gray-800"></span>
            <button type="button" onclick="hideToast()" class="text-gray-400 hover:text-gray-600 ml-2 p-0.5 rounded-lg transition-colors cursor-pointer">
                <i data-lucide="x" class="w-3.5 h-3.5"></i>
            </button>
        </div>

        {{-- ── UNIFIED MODERN ARCHIVE CONTAINER CARD ── --}}
        <div class="mac-window overflow-hidden">
            @php
                if ($activeClassification) {
                    $currentFolderTitle = $activeClassification->code . ' - ' . $activeClassification->name;
                } elseif ($activeLetterType) {
                    $currentFolderTitle = $activeLetterType->name;
                } elseif ($currentYear) {
                    $currentFolderTitle = 'Tahun ' . $currentYear;
                } else {
                    $currentFolderTitle = $isReadOnlyPortal ? 'Modul Arsip Digital' : 'Arsip Digital';
                }
            @endphp

            {{-- Modern Single Card Header (Clean & Seamless, no 3 dots, no double path) --}}
            <div class="px-5 sm:px-6 py-4 border-b border-slate-200/90 bg-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-3.5 min-w-0">
                    {{-- Navigation Action / Back Button --}}
                    @if ($parentUrl)
                        <a href="{{ $parentUrl }}"
                            class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition-all active:scale-95 flex-shrink-0 group shadow-2xs"
                            title="Kembali ke {{ $parentName }}">
                            <i data-lucide="arrow-left" class="w-4 h-4 text-slate-600 group-hover:text-slate-900 group-hover:-translate-x-0.5 transition-transform"></i>
                        </a>
                    @else
                        <div class="w-9 h-9 rounded-xl bg-slate-900 text-white flex items-center justify-center shadow-xs flex-shrink-0">
                            <i data-lucide="{{ $showFiles ? 'folder-check' : 'folder-archive' }}" class="w-4.5 h-4.5"></i>
                        </div>
                    @endif

                    <div class="min-w-0">
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <h2 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight truncate max-w-xl">
                                {{ $currentFolderTitle }}
                            </h2>
                        </div>

                        {{-- Interactive Breadcrumb Path --}}
                        <nav aria-label="Breadcrumb" class="flex items-center flex-wrap gap-1.5 text-xs text-slate-400 font-medium mt-1">
                            @if ($isReadOnlyPortal)
                                <a href="{{ route('switcher') }}" class="hover:text-slate-700 transition-colors flex items-center gap-1" title="Pilih Modul">
                                    <i data-lucide="layout-grid" class="w-3.5 h-3.5"></i>
                                    <span>Portal</span>
                                </a>
                                <i data-lucide="chevron-right" class="w-3 h-3 text-slate-300"></i>
                                <a href="{{ route('archives.index') }}" class="{{ !request('year') ? 'text-slate-900 font-semibold' : 'hover:text-slate-700' }} transition-colors">
                                    Arsip Digital
                                </a>
                            @else
                                <a href="{{ route('dashboard') }}" class="hover:text-slate-700 transition-colors flex items-center gap-1" title="Dashboard Persuratan">
                                    <i data-lucide="home" class="w-3.5 h-3.5"></i>
                                </a>
                                <i data-lucide="chevron-right" class="w-3 h-3 text-slate-300"></i>
                                <a href="{{ route('letters.index') }}" class="hover:text-slate-700 transition-colors">Persuratan</a>
                                <i data-lucide="chevron-right" class="w-3 h-3 text-slate-300"></i>
                                <a href="{{ route('letter-archives.index') }}" class="{{ !request('year') ? 'text-slate-900 font-semibold' : 'hover:text-slate-700' }} transition-colors">
                                    Arsip Digital
                                </a>
                            @endif

                            @if ($currentYear)
                                <i data-lucide="chevron-right" class="w-3 h-3 text-slate-300"></i>
                                <a href="{{ route($baseRouteName, array_merge(request()->except(['page', 'classification_id', 'letter_type_id']), ['year' => $currentYear])) }}"
                                    class="{{ !$activeLetterType ? 'text-slate-900 font-semibold' : 'hover:text-slate-700' }} transition-colors">
                                    Tahun {{ $currentYear }}
                                </a>
                            @endif

                            @if ($activeLetterType)
                                <i data-lucide="chevron-right" class="w-3 h-3 text-slate-300"></i>
                                <a href="{{ route($baseRouteName, array_merge(request()->except(['page', 'classification_id']), ['year' => $currentYear, 'letter_type_id' => $activeLetterType->id])) }}"
                                    class="{{ !$activeClassification ? 'text-slate-900 font-semibold' : 'hover:text-slate-700' }} transition-colors">
                                    {{ $activeLetterType->name }}
                                </a>
                            @endif

                            @if ($activeClassification)
                                <i data-lucide="chevron-right" class="w-3 h-3 text-slate-300"></i>
                                <span class="text-slate-900 font-bold font-mono">
                                    {{ $activeClassification->code }}
                                </span>
                            @endif
                        </nav>
                    </div>
                </div>

                {{-- Right: View Switcher (Grid / List) --}}
                <div class="flex items-center gap-2.5 self-start sm:self-center flex-shrink-0">
                    @if ($showFiles)
                        <div class="inline-flex p-1 bg-slate-100 rounded-xl border border-slate-200/80 shadow-2xs">
                            <a href="{{ route($baseRouteName, array_merge(request()->all(), ['view_mode' => 'grid'])) }}"
                                class="p-1.5 sm:p-2 rounded-lg transition-all {{ $viewMode === 'grid' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-400 hover:text-slate-700' }}"
                                title="Tampilan Grid Kartu">
                                <i data-lucide="layout-grid" class="w-4 h-4"></i>
                            </a>
                            <a href="{{ route($baseRouteName, array_merge(request()->all(), ['view_mode' => 'list'])) }}"
                                class="p-1.5 sm:p-2 rounded-lg transition-all {{ $viewMode === 'list' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-400 hover:text-slate-700' }}"
                                title="Tampilan List Tabel">
                                <i data-lucide="list" class="w-4 h-4"></i>
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- 3. macOS Window Content Canvas --}}
            <div class="p-5 sm:p-6 bg-[#fafbfc] space-y-5">
                {{-- Toolbar Controls: Segmented Archive Status + Filter + Spotlight Search --}}
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    {{-- Left: Filter Controls --}}
                    <div class="flex flex-wrap items-center gap-2.5">
                        {{-- Status Arsip Segmented Control --}}
                        @if ($isReadOnlyPortal)
                            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200/90 text-xs font-bold shadow-2xs">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span>Dokumen Terarsip ({{ $totalArchivedLetters }})</span>
                            </div>
                        @else
                            <div class="mac-segmented-track shadow-2xs bg-slate-100/90">
                                <a href="{{ route($baseRouteName, array_merge(request()->except(['archive_status', 'page']), ['archive_status' => 'archived'])) }}"
                                    class="mac-segmented-btn {{ $archiveStatus === 'archived' ? 'active' : '' }}">
                                    Terarsip ({{ $totalArchivedLetters }})
                                </a>
                                <a href="{{ route($baseRouteName, array_merge(request()->except(['archive_status', 'page']), ['archive_status' => 'all'])) }}"
                                    class="mac-segmented-btn {{ $archiveStatus === 'all' ? 'active' : '' }}">
                                    Semua ({{ $totalAllLetters }})
                                </a>
                                <a href="{{ route($baseRouteName, array_merge(request()->except(['archive_status', 'page']), ['archive_status' => 'unarchived'])) }}"
                                    class="mac-segmented-btn {{ $archiveStatus === 'unarchived' ? 'active' : '' }}">
                                    Belum ({{ $totalUnarchivedLetters }})
                                </a>
                            </div>
                        @endif

                        {{-- Filter Lanjutan Button (HANYA menghitung filter spesifik, bukan navigasi folder) --}}
                        <button type="button" onclick="toggleAdvancedFilter()"
                            class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-slate-700 hover:text-slate-900 bg-white hover:bg-slate-50 border border-slate-200 rounded-xl transition-all cursor-pointer shadow-2xs">
                            <i data-lucide="sliders-horizontal" class="w-3.5 h-3.5 text-slate-500"></i>
                            <span>Filter</span>
                            @if ($activeFiltersCount > 0)
                                <span class="w-4.5 h-4.5 rounded-full bg-slate-900 text-white text-[10px] font-bold flex items-center justify-center">
                                    {{ $activeFiltersCount }}
                                </span>
                            @endif
                        </button>

                        @if ($activeFiltersCount > 0)
                            <a href="{{ route($baseRouteName, array_filter(['year' => request('year'), 'letter_type_id' => request('letter_type_id'), 'classification_id' => request('classification_id'), 'view_mode' => $viewMode, 'archive_status' => $archiveStatus, 'portal' => $isReadOnlyPortal ? 1 : null])) }}"
                                class="text-xs text-slate-400 hover:text-slate-700 font-medium px-2 py-1 transition-colors">
                                Reset Filter
                            </a>
                        @endif
                    </div>

                    {{-- Right: Spotlight Search Bar --}}
                    <form method="GET" action="{{ route($baseRouteName) }}" class="w-full md:w-72">
                        @if ($isReadOnlyPortal) <input type="hidden" name="portal" value="1"> @endif
                        <input type="hidden" name="archive_status" value="{{ $archiveStatus }}">
                        <input type="hidden" name="view_mode" value="{{ $viewMode }}">
                        @if (request('year')) <input type="hidden" name="year" value="{{ request('year') }}"> @endif
                        @if (request('letter_type_id')) <input type="hidden" name="letter_type_id" value="{{ request('letter_type_id') }}"> @endif
                        @if (request('classification_id')) <input type="hidden" name="classification_id" value="{{ request('classification_id') }}"> @endif
                        @if (request('signatory_id')) <input type="hidden" name="signatory_id" value="{{ request('signatory_id') }}"> @endif
                        @if (request('security_classification')) <input type="hidden" name="security_classification" value="{{ request('security_classification') }}"> @endif
                        @if (request('letter_target')) <input type="hidden" name="letter_target" value="{{ request('letter_target') }}"> @endif

                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Cari nomor, perihal, tujuan..."
                                class="w-full pl-9 pr-8 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-400/20 focus:border-slate-400 transition-all placeholder:text-slate-400 shadow-2xs font-medium">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <i data-lucide="search" class="w-3.5 h-3.5"></i>
                            </div>
                            @if (request('search'))
                                <a href="{{ route('letter-archives.index', request()->except(['search', 'page'])) }}"
                                    class="absolute inset-y-0 right-0 pr-2.5 flex items-center text-slate-400 hover:text-slate-600">
                                    <i data-lucide="x" class="w-3.5 h-3.5"></i>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                {{-- ── ACTIVE FILTER CHIPS (HANYA tampil untuk filter spesifik yang dipilih pengguna) ── --}}
                @if ($activeFiltersCount > 0)
                    <div class="flex flex-wrap items-center gap-1.5 pt-0.5">
                        <span class="text-[11px] font-semibold text-slate-400 mr-1">Filter Aktif:</span>

                        @if ($activeSignatory)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-xs font-medium text-slate-700 shadow-2xs">
                                <i data-lucide="user" class="w-3 h-3 text-slate-500"></i>
                                <span>{{ $activeSignatory->position }}</span>
                                <a href="{{ route('letter-archives.index', request()->except(['signatory_id', 'page'])) }}" class="hover:text-red-500 transition-colors">
                                    <i data-lucide="x" class="w-3 h-3"></i>
                                </a>
                            </span>
                        @endif

                        @if ($activeSecClassLabel)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-xs font-medium text-slate-700 shadow-2xs">
                                <i data-lucide="shield" class="w-3 h-3 text-slate-500"></i>
                                <span>Keamanan: {{ $activeSecClassLabel }}</span>
                                <a href="{{ route('letter-archives.index', request()->except(['security_classification', 'page'])) }}" class="hover:text-red-500 transition-colors">
                                    <i data-lucide="x" class="w-3 h-3"></i>
                                </a>
                            </span>
                        @endif

                        @if ($activeTargetLabel)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-xs font-medium text-slate-700 shadow-2xs">
                                <i data-lucide="target" class="w-3 h-3 text-slate-500"></i>
                                <span>Sasaran: {{ $activeTargetLabel }}</span>
                                <a href="{{ route('letter-archives.index', request()->except(['letter_target', 'page'])) }}" class="hover:text-red-500 transition-colors">
                                    <i data-lucide="x" class="w-3 h-3"></i>
                                </a>
                            </span>
                        @endif

                        @if (request('search'))
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-white border border-slate-300 text-xs font-medium text-slate-800 shadow-2xs">
                                <i data-lucide="search" class="w-3 h-3 text-slate-500"></i>
                                <span>"{{ request('search') }}"</span>
                                <a href="{{ route('letter-archives.index', request()->except(['search', 'page'])) }}" class="hover:text-red-500 transition-colors">
                                    <i data-lucide="x" class="w-3 h-3"></i>
                                </a>
                            </span>
                        @endif
                    </div>
                @endif

                {{-- ── ADVANCED FILTER DRAWER (Apple macOS Clean Floating Panel) ── --}}
                <div id="advancedFilterDrawer" class="{{ (request('signatory_id') || request('security_classification') || request('letter_target')) ? '' : 'hidden' }} bg-white rounded-2xl border border-slate-200/90 p-4.5 shadow-sm transition-all">
                    <form method="GET" action="{{ route($baseRouteName) }}">
                        @if ($isReadOnlyPortal) <input type="hidden" name="portal" value="1"> @endif
                        <input type="hidden" name="archive_status" value="{{ $archiveStatus }}">
                        <input type="hidden" name="view_mode" value="{{ $viewMode }}">
                        @if (request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                        @if (request('year')) <input type="hidden" name="year" value="{{ request('year') }}"> @endif
                        @if (request('letter_type_id')) <input type="hidden" name="letter_type_id" value="{{ request('letter_type_id') }}"> @endif
                        @if (request('classification_id')) <input type="hidden" name="classification_id" value="{{ request('classification_id') }}"> @endif

                        <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-100">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center">
                                    <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                                </div>
                                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Filter Lanjutan Dokumen</h3>
                            </div>
                            <button type="button" onclick="toggleAdvancedFilter()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                                <i data-lucide="x" class="w-3.5 h-3.5"></i>
                            </button>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 items-end">
                            <div class="space-y-1">
                                <label for="signatory_id" class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Penandatangan</label>
                                <select id="signatory_id" name="signatory_id" class="w-full px-3 py-2 text-xs bg-slate-50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-400/20 focus:border-slate-400 transition-all font-medium text-slate-700">
                                    <option value="">Semua Penandatangan</option>
                                    @foreach ($signatories as $sig)
                                        <option value="{{ $sig->id }}" {{ request('signatory_id') == $sig->id ? 'selected' : '' }}>
                                            {{ $sig->position }} - {{ $sig->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="space-y-1">
                                <label for="security_classification" class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Klasifikasi Keamanan</label>
                                <select id="security_classification" name="security_classification" class="w-full px-3 py-2 text-xs bg-slate-50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-400/20 focus:border-slate-400 transition-all font-medium text-slate-700">
                                    <option value="">Semua Keamanan</option>
                                    @foreach ($securityClassifications as $code => $label)
                                        <option value="{{ $code }}" {{ request('security_classification') === $code ? 'selected' : '' }}>
                                            {{ $code }} - {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="space-y-1">
                                <label for="letter_target" class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Sasaran Surat</label>
                                <select id="letter_target" name="letter_target" class="w-full px-3 py-2 text-xs bg-slate-50 hover:bg-white focus:bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-400/20 focus:border-slate-400 transition-all font-medium text-slate-700">
                                    <option value="">Semua Sasaran</option>
                                    @foreach ($letterTargets as $targetKey => $targetLabel)
                                        <option value="{{ $targetKey }}" {{ request('letter_target') === $targetKey ? 'selected' : '' }}>
                                            {{ $targetLabel }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-span-full pt-2 flex items-center justify-between border-t border-slate-100">
                                <a href="{{ route($baseRouteName, array_filter(['year' => request('year'), 'letter_type_id' => request('letter_type_id'), 'classification_id' => request('classification_id'), 'view_mode' => $viewMode, 'archive_status' => $archiveStatus, 'portal' => $isReadOnlyPortal ? 1 : null])) }}"
                                    class="text-xs font-semibold text-slate-400 hover:text-slate-700 transition-colors">
                                    Bersihkan Filter
                                </a>

                                <div class="flex items-center gap-2">
                                    <button type="button" onclick="toggleAdvancedFilter()"
                                        class="px-3.5 py-1.5 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-all">
                                        Batal
                                    </button>
                                    <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-4 py-1.5 bg-slate-900 text-white font-semibold text-xs rounded-xl hover:bg-slate-800 transition-all shadow-xs">
                                        <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                        <span>Terapkan Filter</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- ── 4. SECTION: FOLDERS (Hierarchical: Tahun -> Jenis Surat -> Klasifikasi) ── --}}
                @if ($explorerFolders->count() > 0)
                    <div class="space-y-3">
                        <div class="flex items-center justify-between px-0.5">
                            <div class="flex items-center gap-2">
                                <h3 class="text-sm font-bold text-slate-800 tracking-tight">Folders</h3>
                                <span class="text-[11px] text-slate-400 font-medium">
                                    @if ($folderLevel === 'root')
                                        (Pilih Tahun Arsip)
                                    @elseif ($folderLevel === 'year')
                                        (Pilih Jenis Surat - Tahun {{ $currentYear }})
                                    @elseif ($folderLevel === 'type')
                                        (Pilih Klasifikasi - {{ $activeLetterType->name }})
                                    @endif
                                </span>
                            </div>

                            <span class="text-[11px] text-slate-400 font-medium hidden sm:inline">
                                Double-click folder untuk membuka
                            </span>
                        </div>

                        {{-- Folders Grid (Matches exact macOS reference layout 5 columns) --}}
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-5 gap-3.5">
                            @foreach ($explorerFolders as $folder)
                                <div class="mac-surface p-4 flex flex-col justify-between aspect-[1.12/1] cursor-pointer group"
                                    id="folder-item-{{ $loop->index }}"
                                    onclick="selectCard(this)"
                                    ondblclick="window.location.href='{{ $folder['url'] }}'"
                                    title="Double-click untuk membuka folder {{ $folder['name'] }}">
                                    
                                    {{-- Top Row: Folder Indicator (Left) & Action Menu (Right) --}}
                                    <div class="flex items-center justify-between">
                                        <div class="folder-indicator w-4 h-4 rounded-full border border-slate-300 group-hover:border-slate-400 transition-colors flex items-center justify-center">
                                            <span class="w-1.5 h-1.5 rounded-full bg-transparent group-hover:bg-slate-400 transition-colors"></span>
                                        </div>

                                        <div class="relative dropdown-container">
                                            <button type="button" onclick="toggleCardDropdown(event, 'folder-menu-{{ $loop->index }}')"
                                                class="text-slate-300 hover:text-slate-600 p-0.5 -mr-1 rounded transition-colors cursor-pointer" title="Menu Folder">
                                                <i data-lucide="more-vertical" class="w-3.5 h-3.5 text-slate-400"></i>
                                            </button>

                                            <div id="folder-menu-{{ $loop->index }}"
                                                class="hidden absolute right-0 top-full mt-1 w-36 bg-white rounded-xl border border-slate-200 shadow-lg py-1 z-30 text-xs font-medium text-slate-700">
                                                <a href="{{ $folder['url'] }}"
                                                    class="w-full text-left px-3 py-1.5 hover:bg-slate-50 flex items-center gap-2">
                                                    <i data-lucide="folder-open" class="w-3.5 h-3.5 text-slate-500"></i>
                                                    <span>Buka Folder</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Center: Clean Golden Folder Icon (Matching reference image) --}}
                                    <div class="flex flex-col items-center justify-center text-center my-auto py-0.5">
                                        <div class="w-13 h-10.5 relative flex items-center justify-center group-hover:scale-105 transition-transform duration-200">
                                            <svg viewBox="0 0 52 42" fill="none" class="w-full h-full drop-shadow-[0_1px_2px_rgba(0,0,0,0.06)]" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M4 8C4 5.79086 5.79086 4 8 4H19.5858C20.6467 4 21.6641 4.42143 22.4142 5.17157L24.8284 7.58579C25.5786 8.33593 26.596 8.75736 27.6569 8.75736H44C46.2091 8.75736 48 10.5482 48 12.7574V34C48 36.2091 46.2091 38 44 38H8C5.79086 38 4 36.2091 4 34V8Z" fill="#FBBF24"/>
                                                <path d="M4 14C4 11.7909 5.79086 10 8 10H44C46.2091 10 48 11.7909 48 14V34C48 36.2091 46.2091 38 44 38H8C5.79086 38 4 36.2091 4 34V14Z" fill="#F59E0B"/>
                                                <path d="M4 15.5C4 13.2909 5.79086 11.5 8 11.5H44C46.2091 11.5 48 13.2909 48 15.5V34C48 36.2091 46.2091 38 44 38H8C5.79086 38 4 36.2091 4 34V15.5Z" fill="#FCD34D" fill-opacity="0.35"/>
                                            </svg>
                                        </div>
                                        <h4 class="text-xs font-semibold text-slate-800 tracking-tight mt-2 truncate max-w-full">
                                            {{ $folder['name'] }}
                                        </h4>
                                    </div>

                                    {{-- Bottom Row: File Count (Left) & Short Label (Right) --}}
                                    <div class="flex items-center justify-between text-[10.5px] text-slate-500 font-medium pt-1.5 border-t border-slate-100">
                                        <span>{{ $folder['total'] }} berkas</span>
                                        <span class="font-mono text-slate-400 truncate max-w-[60px]">{{ $folder['short_name'] }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- ── 5. SECTION: FILES (Berkas Surat - Ditampilkan ketika sudah mencapai level terdalam / pencarian) ── --}}
                @if ($showFiles)
                    <div class="space-y-3 pt-1">
                        <div class="flex items-center justify-between px-0.5">
                            <div class="flex items-center gap-2">
                                <h3 class="text-sm font-bold text-slate-800 tracking-tight">Files</h3>
                                @if ($activeClassification)
                                    <span class="text-[11px] text-slate-500 font-medium">dalam {{ $activeClassification->code }} - {{ $activeClassification->name }}</span>
                                @elseif ($activeLetterType)
                                    <span class="text-[11px] text-slate-500 font-medium">dalam {{ $activeLetterType->name }}</span>
                                @elseif ($currentYear)
                                    <span class="text-[11px] text-slate-500 font-medium">dalam Tahun {{ $currentYear }}</span>
                                @elseif (request('search'))
                                    <span class="text-[11px] text-slate-500 font-medium">Hasil Pencarian "{{ request('search') }}"</span>
                                @endif
                            </div>

                            <div class="flex items-center gap-3 text-xs text-slate-400 font-medium">
                                <span class="hidden sm:inline">Double-click berkas untuk pratinjau</span>
                                <span>Halaman {{ $letters->currentPage() }} dari {{ $letters->lastPage() }}</span>
                            </div>
                        </div>

                        @if ($letters->isEmpty())
                            <div class="mac-surface p-10 text-center">
                                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                    <i data-lucide="file-question" class="w-6 h-6 text-slate-400"></i>
                                </div>
                                <h4 class="text-sm font-semibold text-slate-800">Folder ini masih kosong</h4>
                                <p class="text-xs text-slate-400 max-w-sm mx-auto mt-0.5">
                                    Belum ada dokumen surat yang terarsip di dalam kategori ini.
                                </p>
                                @if ($parentUrl)
                                    <div class="mt-4">
                                        <a href="{{ $parentUrl }}"
                                            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition-all">
                                            <i data-lucide="arrow-left" class="w-3.5 h-3.5 text-slate-500"></i>
                                            <span>Kembali ke {{ $parentName }}</span>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @else
                            @if ($viewMode === 'grid')
                                {{-- ── RECTANGULAR FILE CARDS GRID (Persegi Panjang, Monochrome Apple Design) ── --}}
                                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3.5">
                                    @foreach ($letters as $letter)
                                        @php
                                            $tgl = $letter->letter_date;
                                            $tglFormatted = $tgl ? $tgl->format('d.m.y') : '-';
                                            $isArchived = $letter->is_archived;
                                            $targetDisplay = $letter->recipient ?: ($letter->letter_target ? ucfirst($letter->letter_target) : '-');
                                        @endphp

                                        <div class="mac-file-card p-4 sm:p-4.5 flex flex-col justify-between relative group select-none cursor-pointer"
                                            id="file-item-{{ $letter->id }}"
                                            onclick="selectCard(this)"
                                            ondblclick="{{ $isArchived ? "openPreviewModal('{$letter->drive_preview_url}', '" . addslashes($letter->letter_number) . "')" : ($canManage ? "openArchiveModal({$letter->id}, '" . addslashes($letter->letter_number) . "', '')" : '') }}"
                                            title="Double-click untuk pratinjau dokumen">

                                            {{-- 1. TOP ROW: Document Sheet Icon + Nomor Surat & Meta + Status Badge & Menu --}}
                                            <div class="flex items-start justify-between gap-3">
                                                <div class="flex items-start gap-3 min-w-0">
                                                    {{-- Minimalist Monochrome Document Sheet Icon --}}
                                                    <div class="w-10 h-13 bg-white rounded-lg border border-slate-200 shadow-2xs relative overflow-hidden flex flex-col items-center justify-between p-1.5 flex-shrink-0 group-hover:scale-105 transition-transform duration-200">
                                                        <div class="absolute top-0 right-0 w-2.5 h-2.5 bg-slate-100 border-b border-l border-slate-200 rounded-bl"></div>
                                                        <div class="w-full space-y-1 mt-0.5 pr-1.5">
                                                            <div class="h-0.5 bg-slate-200 rounded-full w-full"></div>
                                                            <div class="h-0.5 bg-slate-200 rounded-full w-4/5"></div>
                                                            <div class="h-0.5 bg-slate-200 rounded-full w-2/3"></div>
                                                        </div>
                                                        @if ($isArchived)
                                                            <span class="text-[8.5px] font-bold px-1.5 py-0.2 rounded bg-slate-800 text-white font-mono tracking-wider">PDF</span>
                                                        @else
                                                            <span class="text-[8.5px] font-medium px-1.5 py-0.2 rounded bg-slate-100 text-slate-500 font-mono tracking-wider">DOC</span>
                                                        @endif
                                                    </div>

                                                    {{-- Nomor Surat & Quick Meta --}}
                                                    <div class="min-w-0">
                                                        <div class="flex items-center gap-1.5">
                                                            <span class="text-xs sm:text-[13px] font-bold text-slate-900 font-mono tracking-tight truncate group-hover:text-slate-700 transition-colors"
                                                                title="{{ $letter->letter_number }}">
                                                                {{ $letter->letter_number }}
                                                            </span>
                                                            <button type="button" onclick="event.stopPropagation(); copyToClipboard('{{ addslashes($letter->letter_number) }}', 'Nomor surat disalin!')"
                                                                class="opacity-0 group-hover:opacity-100 p-0.5 text-slate-400 hover:text-slate-700 rounded transition-all cursor-pointer flex-shrink-0"
                                                                title="Salin Nomor">
                                                                <i data-lucide="copy" class="w-3 h-3 text-slate-500"></i>
                                                            </button>
                                                        </div>

                                                        <div class="flex items-center gap-1.5 text-[11px] text-slate-400 font-medium mt-0.5">
                                                            <span class="font-mono text-slate-500">{{ $tglFormatted }}</span>
                                                            <span>•</span>
                                                            <span class="font-mono text-slate-600 truncate max-w-[120px]" title="{{ $letter->classification->code ?? '-' }}">
                                                                {{ $letter->classification->code ?? ($letter->letterType->name ?? 'Surat') }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Top Right: Status Badge & Kebab Dropdown --}}
                                                <div class="flex items-center gap-1 flex-shrink-0">
                                                    @if ($isArchived)
                                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10.5px] font-semibold bg-slate-100 text-slate-800 border border-slate-200/90 shadow-2xs">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-slate-800"></span>
                                                            Terarsip
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-slate-50 text-slate-400 border border-slate-200/60">
                                                            Belum
                                                        </span>
                                                    @endif

                                                    {{-- Kebab Menu Trigger --}}
                                                    <div class="relative dropdown-container">
                                                        <button type="button" onclick="toggleCardDropdown(event, 'dropdown-{{ $letter->id }}')"
                                                            class="text-slate-400 hover:text-slate-700 p-1 -mr-1 rounded-lg transition-colors cursor-pointer" title="Menu Aksi">
                                                            <i data-lucide="more-vertical" class="w-3.5 h-3.5 text-slate-400"></i>
                                                        </button>

                                                        {{-- Dropdown Popup --}}
                                                        <div id="dropdown-{{ $letter->id }}"
                                                            class="hidden absolute right-0 top-full mt-1 w-44 bg-white rounded-xl border border-slate-200 shadow-lg py-1 z-30 text-xs font-medium text-slate-700">
                                                            @if ($isArchived)
                                                                <button type="button" onclick="openPreviewModal('{{ $letter->drive_preview_url }}', '{{ addslashes($letter->letter_number) }}')"
                                                                    class="w-full text-left px-3 py-1.5 hover:bg-slate-50 flex items-center gap-2 cursor-pointer">
                                                                    <i data-lucide="eye" class="w-3.5 h-3.5 text-slate-500"></i>
                                                                    <span>Pratinjau Dokumen</span>
                                                                </button>
                                                                <a href="{{ $letter->drive_url }}" target="_blank" rel="noopener noreferrer"
                                                                    class="w-full text-left px-3 py-1.5 hover:bg-slate-50 flex items-center gap-2">
                                                                    <i data-lucide="external-link" class="w-3.5 h-3.5 text-slate-500"></i>
                                                                    <span>Buka di Google Drive</span>
                                                                </a>
                                                            @endif

                                                            <button type="button" onclick="copyToClipboard('{{ addslashes($letter->letter_number) }}', 'Nomor surat disalin!')"
                                                                class="w-full text-left px-3 py-1.5 hover:bg-slate-50 flex items-center gap-2 cursor-pointer">
                                                                <i data-lucide="copy" class="w-3.5 h-3.5 text-slate-500"></i>
                                                                <span>Salin Nomor</span>
                                                            </button>

                                                            @if ($canManage)
                                                                <button type="button" onclick="openArchiveModal({{ $letter->id }}, '{{ addslashes($letter->letter_number) }}', '{{ addslashes($letter->drive_url ?? '') }}')"
                                                                    class="w-full text-left px-3 py-1.5 hover:bg-slate-50 text-slate-900 font-semibold flex items-center gap-2 cursor-pointer">
                                                                    <i data-lucide="link" class="w-3.5 h-3.5 text-slate-600"></i>
                                                                    <span>{{ $isArchived ? 'Ubah Link GDrive' : 'Tautkan ke GDrive' }}</span>
                                                                </button>
                                                            @endif

                                                            @if (!$isReadOnlyPortal)
                                                                <div class="border-t border-slate-100 my-1"></div>
                                                                <a href="{{ route('letters.show', $letter) }}"
                                                                    class="w-full text-left px-3 py-1.5 hover:bg-slate-50 text-slate-500 flex items-center gap-2">
                                                                    <i data-lucide="file-text" class="w-3.5 h-3.5 text-slate-400"></i>
                                                                    <span>Detail Surat</span>
                                                                </a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- 2. MIDDLE SECTION: Information Details (Perihal, Tujuan, Mahasiswa) --}}
                                            <div class="mt-3.5 space-y-2">
                                                {{-- Perihal --}}
                                                <div>
                                                    <div class="text-[10.5px] font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                                                        <i data-lucide="file-text" class="w-3 h-3 text-slate-400"></i>
                                                        <span>Perihal</span>
                                                    </div>
                                                    <p class="text-xs font-semibold text-slate-800 line-clamp-2 leading-relaxed mt-0.5" title="{{ $letter->subject }}">
                                                        {{ $letter->subject ?: '-' }}
                                                    </p>
                                                </div>

                                                {{-- Tujuan & Mahasiswa Grid / Row --}}
                                                <div class="pt-2 border-t border-slate-100 grid grid-cols-1 {{ $letter->student_name ? 'sm:grid-cols-2' : '' }} gap-2 text-xs">
                                                    {{-- Tujuan --}}
                                                    <div class="min-w-0">
                                                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1">
                                                            <i data-lucide="send" class="w-3 h-3 text-slate-400"></i>
                                                            <span>Tujuan</span>
                                                        </div>
                                                        <p class="text-slate-700 font-medium truncate mt-0.5" title="{{ $targetDisplay }}">
                                                            {{ $targetDisplay ?: '-' }}
                                                        </p>
                                                    </div>

                                                    {{-- Mahasiswa (Jika Ada) --}}
                                                    @if ($letter->student_name)
                                                        <div class="min-w-0">
                                                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1">
                                                                <i data-lucide="graduation-cap" class="w-3 h-3 text-slate-400"></i>
                                                                <span>Mahasiswa</span>
                                                            </div>
                                                            <p class="text-slate-900 font-semibold truncate mt-0.5" title="{{ $letter->student_name }}">
                                                                <span class="bg-slate-100 border border-slate-200/80 px-2 py-0.5 rounded-md inline-block">
                                                                    {{ $letter->student_name }}
                                                                </span>
                                                            </p>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- 3. BOTTOM SECTION: Footer Meta (Signatory) & Quick Action Icons --}}
                                            <div class="mt-3.5 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
                                                <div class="flex items-center gap-1.5 truncate text-slate-500 font-medium">
                                                    <i data-lucide="user" class="w-3 h-3 text-slate-400 flex-shrink-0"></i>
                                                    <span class="truncate" title="{{ $letter->signatory->position ?? '-' }}">
                                                        {{ $letter->signatory->position ?? '-' }}
                                                    </span>
                                                </div>

                                                {{-- Quick Actions (Monochrome neutral icons) --}}
                                                <div class="flex items-center gap-1 flex-shrink-0">
                                                    @if ($isArchived)
                                                        <button type="button" onclick="event.stopPropagation(); openPreviewModal('{{ $letter->drive_preview_url }}', '{{ addslashes($letter->letter_number) }}')"
                                                            class="p-1.5 rounded-lg text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition-colors cursor-pointer"
                                                            title="Pratinjau Dokumen">
                                                            <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                                        </button>
                                                        <a href="{{ $letter->drive_url }}" target="_blank" rel="noopener noreferrer" onclick="event.stopPropagation();"
                                                            class="p-1.5 rounded-lg text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition-colors"
                                                            title="Buka di Google Drive">
                                                            <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                                                        </a>
                                                    @elseif ($canManage)
                                                        <button type="button" onclick="event.stopPropagation(); openArchiveModal({{ $letter->id }}, '{{ addslashes($letter->letter_number) }}', '')"
                                                            class="p-1.5 rounded-lg text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition-colors cursor-pointer"
                                                            title="Tautkan ke Google Drive">
                                                            <i data-lucide="link" class="w-3.5 h-3.5"></i>
                                                        </button>
                                                    @endif
                                                    @if (!$isReadOnlyPortal)
                                                        <a href="{{ route('letters.show', $letter) }}" onclick="event.stopPropagation();"
                                                            class="p-1.5 rounded-lg text-slate-400 hover:text-slate-900 hover:bg-slate-100 transition-colors"
                                                            title="Detail Surat">
                                                            <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                {{-- ── LIST VIEW (Konsisten dengan Tabel Surat Utama) ── --}}
                                <div class="bg-white rounded-2xl border border-gray-200/80 shadow-xs overflow-hidden">
                                    <div class="overflow-x-auto">
                                        <table class="w-full text-left border-collapse">
                                            <thead>
                                                <tr class="border-b border-gray-200/80 bg-gray-50/75">
                                                    <th scope="col" class="px-4 py-3 text-center text-[11px] font-bold text-gray-500 uppercase tracking-wider w-12">No</th>
                                                    <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider min-w-[200px]">
                                                        <div class="flex items-center gap-1.5" title="Klik nomor surat untuk membuka pratinjau/detail">
                                                            <span>Nomor Surat</span>
                                                            <i data-lucide="copy" class="w-3 h-3 text-gray-400"></i>
                                                        </div>
                                                    </th>
                                                    <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider min-w-[110px]">Tanggal</th>
                                                    <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider min-w-[130px] hidden md:table-cell">Jenis Surat</th>
                                                    <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider min-w-[150px] hidden lg:table-cell">Penandatangan</th>
                                                    <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider min-w-[240px]">
                                                        <div class="flex items-center gap-1.5" title="Klik perihal untuk menyalin">
                                                            <span>Perihal</span>
                                                            <i data-lucide="copy" class="w-3 h-3 text-gray-400"></i>
                                                        </div>
                                                    </th>
                                                    <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider min-w-[180px]">
                                                        <div class="flex items-center gap-1.5" title="Klik tujuan untuk menyalin">
                                                            <span>Tujuan</span>
                                                            <i data-lucide="copy" class="w-3 h-3 text-gray-400"></i>
                                                        </div>
                                                    </th>
                                                    <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider min-w-[180px]">
                                                        <div class="flex items-center gap-1.5" title="Klik nama mahasiswa untuk menyalin">
                                                            <span>Mahasiswa</span>
                                                            <i data-lucide="copy" class="w-3 h-3 text-gray-400"></i>
                                                        </div>
                                                    </th>
                                                    <th scope="col" class="px-3.5 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider text-center min-w-[110px]">Status</th>
                                                    <th scope="col" class="px-3.5 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider text-center w-28">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-100 bg-white">
                                                @php
                                                    $typeColorMap = [
                                                        'sertifikat' => 'bg-purple-50 text-purple-700 border-purple-200/80',
                                                        'mou' => 'bg-blue-50 text-blue-700 border-blue-200/80',
                                                        'ia' => 'bg-indigo-50 text-indigo-700 border-indigo-200/80',
                                                        'sipermawa' => 'bg-emerald-50 text-emerald-700 border-emerald-200/80',
                                                        'tugas' => 'bg-amber-50 text-amber-700 border-amber-200/80',
                                                        'keputusan' => 'bg-indigo-50 text-indigo-700 border-indigo-200/80',
                                                        'undangan' => 'bg-rose-50 text-rose-700 border-rose-200/80',
                                                        'umum' => 'bg-gray-100 text-gray-700 border-gray-200/80',
                                                    ];
                                                @endphp

                                                @foreach ($letters as $index => $letter)
                                                    @php
                                                        $typeName = strtolower($letter->letterType->name ?? '');
                                                        $typeClass = 'bg-gray-100 text-gray-700 border-gray-200/80';
                                                        foreach ($typeColorMap as $key => $cls) {
                                                            if (str_contains($typeName, $key)) {
                                                                $typeClass = $cls;
                                                                break;
                                                            }
                                                        }
                                                        $isArchived = $letter->is_archived;
                                                    @endphp
                                                    <tr class="hover:bg-slate-50/80 transition-colors group">
                                                        {{-- No --}}
                                                        <td class="px-4 py-4 text-center text-xs font-semibold text-gray-400">
                                                            {{ ($letters->currentPage() - 1) * $letters->perPage() + $index + 1 }}
                                                        </td>

                                                        {{-- Nomor Surat --}}
                                                        <td class="px-4 py-4">
                                                            <div class="flex items-center gap-2">
                                                                @if ($isReadOnlyPortal)
                                                                    @if ($isArchived)
                                                                        <button type="button" onclick="openPreviewModal('{{ $letter->drive_preview_url }}', '{{ addslashes($letter->letter_number) }}')"
                                                                            class="font-mono text-[13.5px] sm:text-sm font-bold text-gray-900 group-hover:text-slate-900 group-hover:underline transition-colors leading-normal tracking-tight text-left cursor-pointer"
                                                                            title="Klik untuk pratinjau dokumen">
                                                                            {{ $letter->letter_number }}
                                                                        </button>
                                                                    @else
                                                                        <span class="font-mono text-[13.5px] sm:text-sm font-bold text-gray-900 leading-normal tracking-tight">
                                                                            {{ $letter->letter_number }}
                                                                        </span>
                                                                    @endif
                                                                @else
                                                                    <a href="{{ route('letters.show', $letter) }}"
                                                                        class="font-mono text-[13.5px] sm:text-sm font-bold text-gray-900 group-hover:text-slate-900 group-hover:underline transition-colors leading-normal tracking-tight"
                                                                        title="Klik untuk melihat detail surat">
                                                                        {{ $letter->letter_number }}
                                                                    </a>
                                                                @endif
                                                                <button type="button" onclick="copyToClipboard('{{ addslashes($letter->letter_number) }}', 'Nomor surat disalin!')"
                                                                    class="opacity-0 group-hover:opacity-100 p-1.5 text-slate-400 hover:text-slate-700 transition-all rounded-lg hover:bg-slate-100 cursor-pointer flex-shrink-0"
                                                                    title="Salin Nomor">
                                                                    <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                                                                </button>
                                                            </div>
                                                            @if ($letter->classification)
                                                                <p class="text-[11px] text-gray-400 font-mono mt-0.5">{{ $letter->classification->code }} - {{ $letter->classification->name }}</p>
                                                            @endif
                                                        </td>

                                                        {{-- Tanggal Surat --}}
                                                        <td class="px-4 py-4 whitespace-nowrap">
                                                            <div class="flex items-center gap-1.5 text-xs font-semibold text-gray-700">
                                                                <i data-lucide="calendar" class="w-3.5 h-3.5 text-gray-400"></i>
                                                                <span>{{ $letter->letter_date ? $letter->letter_date->format('d/m/Y') : '-' }}</span>
                                                            </div>
                                                        </td>

                                                        {{-- Jenis Surat --}}
                                                        <td class="px-4 py-4 whitespace-nowrap hidden md:table-cell">
                                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $typeClass }}">
                                                                <span class="w-1.5 h-1.5 rounded-full bg-current opacity-70"></span>
                                                                {{ $letter->letterType->name ?? '-' }}
                                                            </span>
                                                        </td>

                                                        {{-- Penandatangan --}}
                                                        <td class="px-4 py-4 whitespace-nowrap hidden lg:table-cell">
                                                            <div class="text-xs">
                                                                <span class="font-bold text-gray-900">{{ $letter->signatory->position ?? '-' }}</span>
                                                                @if ($letter->signatory)
                                                                    <p class="text-[11px] text-gray-500 truncate max-w-[150px]">{{ $letter->signatory->name }}</p>
                                                                @endif
                                                            </div>
                                                        </td>

                                                        {{-- Perihal --}}
                                                        <td class="px-4 py-4">
                                                            @if ($letter->subject)
                                                                <div role="button" tabindex="0"
                                                                    data-copy="{{ $letter->subject }}"
                                                                    onclick="copyToClipboard(this.dataset.copy, 'Perihal surat berhasil disalin!')"
                                                                    onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();this.click();}"
                                                                    class="group/copy flex items-start justify-between gap-2 text-xs sm:text-[13px] font-semibold text-gray-900 hover:text-slate-900 transition-colors leading-relaxed cursor-pointer break-words rounded-xl p-1 -m-1 hover:bg-slate-100/70 active:scale-[0.99]"
                                                                    title="Klik untuk menyalin perihal">
                                                                    <span class="break-words flex-1">{{ $letter->subject }}</span>
                                                                    <span class="opacity-0 group-hover/copy:opacity-100 transition-opacity p-0.5 text-gray-400 group-hover/copy:text-slate-600 flex-shrink-0 mt-0.5">
                                                                        <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                                                                    </span>
                                                                </div>
                                                            @else
                                                                <span class="text-xs text-gray-300 font-mono">-</span>
                                                            @endif
                                                        </td>

                                                        {{-- Tujuan / Penerima --}}
                                                        <td class="px-4 py-4">
                                                            @if ($letter->recipient)
                                                                <div role="button" tabindex="0"
                                                                    data-copy="{{ $letter->recipient }}"
                                                                    onclick="copyToClipboard(this.dataset.copy, 'Tujuan surat berhasil disalin!')"
                                                                    onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();this.click();}"
                                                                    class="group/copy flex items-start justify-between gap-2 text-xs sm:text-[13px] font-medium text-gray-800 hover:text-slate-900 transition-colors cursor-pointer rounded-xl p-1 -m-1 hover:bg-slate-100/70 active:scale-[0.99]"
                                                                    title="Klik untuk menyalin tujuan">
                                                                    <div class="flex items-start gap-2 flex-1 min-w-0">
                                                                        <i data-lucide="send" class="w-3.5 h-3.5 text-slate-400 flex-shrink-0 mt-0.5 group-hover/copy:scale-110 transition-transform"></i>
                                                                        <span class="break-words leading-relaxed flex-1">{{ $letter->recipient }}</span>
                                                                    </div>
                                                                    <span class="opacity-0 group-hover/copy:opacity-100 transition-opacity p-0.5 text-gray-400 group-hover/copy:text-slate-600 flex-shrink-0 mt-0.5">
                                                                        <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                                                                    </span>
                                                                </div>
                                                            @else
                                                                <span class="text-xs text-gray-300 font-mono pl-3">-</span>
                                                            @endif
                                                        </td>

                                                        {{-- Mahasiswa Terkait --}}
                                                        <td class="px-4 py-4">
                                                            @if ($letter->student_name)
                                                                <div role="button" tabindex="0"
                                                                    data-copy="{{ $letter->student_name }}"
                                                                    onclick="copyToClipboard(this.dataset.copy, 'Nama mahasiswa berhasil disalin!')"
                                                                    onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();this.click();}"
                                                                    class="group/copy inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs sm:text-[12.5px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200/90 shadow-2xs max-w-full hover:bg-emerald-100 hover:border-emerald-300 transition-all cursor-pointer active:scale-[0.98]"
                                                                    title="Klik untuk menyalin nama mahasiswa">
                                                                    <i data-lucide="graduation-cap" class="w-4 h-4 text-emerald-600 flex-shrink-0"></i>
                                                                    <span class="break-words leading-snug flex-1">{{ $letter->student_name }}</span>
                                                                    <i data-lucide="copy" class="w-3.5 h-3.5 opacity-0 group-hover/copy:opacity-100 transition-opacity text-emerald-700 flex-shrink-0"></i>
                                                                </div>
                                                            @else
                                                                <span class="text-xs text-gray-300 font-mono pl-3">-</span>
                                                            @endif
                                                        </td>

                                                        {{-- Status Arsip --}}
                                                        <td class="px-3.5 py-4 text-center whitespace-nowrap">
                                                            @if ($isArchived)
                                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/90 shadow-2xs">
                                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                                    Terarsip
                                                                </span>
                                                            @else
                                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200/80">
                                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                                                    Belum
                                                                </span>
                                                            @endif
                                                        </td>

                                                        {{-- Aksi --}}
                                                        <td class="px-3.5 py-4 text-center whitespace-nowrap">
                                                            <div class="flex items-center justify-center gap-1.5">
                                                                @if ($isArchived)
                                                                    <button type="button" onclick="openPreviewModal('{{ $letter->drive_preview_url }}', '{{ addslashes($letter->letter_number) }}')"
                                                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-bold bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200/90 shadow-2xs transition-all active:scale-95 cursor-pointer"
                                                                        title="Pratinjau Dokumen">
                                                                        <i data-lucide="eye" class="w-3.5 h-3.5 text-emerald-600"></i>
                                                                        <span>Lihat</span>
                                                                    </button>
                                                                    <a href="{{ $letter->drive_url }}" target="_blank" rel="noopener noreferrer"
                                                                        class="p-1.5 rounded-lg text-gray-400 hover:text-emerald-700 hover:bg-emerald-50 border border-transparent hover:border-emerald-200 transition-colors"
                                                                        title="Buka Dokumen di Google Drive">
                                                                        <i data-lucide="external-link" class="w-4 h-4"></i>
                                                                    </a>
                                                                @else
                                                                    @if ($canManage)
                                                                        <button type="button" onclick="openArchiveModal({{ $letter->id }}, '{{ addslashes($letter->letter_number) }}', '')"
                                                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-gray-50 hover:bg-emerald-50 text-gray-600 hover:text-emerald-700 border border-gray-200 hover:border-emerald-200 transition-colors cursor-pointer"
                                                                            title="Tautkan link Google Drive">
                                                                            <i data-lucide="link" class="w-3.5 h-3.5 text-gray-400"></i>
                                                                            <span>+ Tautkan</span>
                                                                        </button>
                                                                    @else
                                                                        <span class="text-xs text-gray-300 font-mono">-</span>
                                                                    @endif
                                                                @endif

                                                                @if (!$isReadOnlyPortal)
                                                                    <a href="{{ route('letters.show', $letter) }}"
                                                                        class="p-1.5 rounded-lg text-gray-400 hover:text-slate-900 hover:bg-slate-100 border border-transparent hover:border-slate-200 transition-colors"
                                                                        title="Detail Surat">
                                                                        <i data-lucide="chevron-right" class="w-4 h-4"></i>
                                                                    </a>
                                                                @endif
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif

                            {{-- Pagination Bar --}}
                            <div class="mt-4 rounded-xl border border-slate-200/80 overflow-hidden bg-white shadow-2xs">
                                {{ $letters->appends(request()->query())->links() }}
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ── 6. MODALS (Google Drive Document Viewer & Quick Tautkan Link) ── --}}
    {{-- Google Drive Document Preview Modal --}}
    <div id="previewModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6 bg-slate-900/50 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full h-[88vh] border border-slate-200 overflow-hidden flex flex-col transform transition-all scale-95 opacity-0" id="previewModalContent">
            {{-- Header --}}
            <div class="px-5 py-3 border-b border-slate-200 flex items-center justify-between bg-slate-50/80">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-7 h-7 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="file-text" class="w-4 h-4 text-slate-600"></i>
                    </div>
                    <div class="min-w-0">
                        <h4 class="text-xs sm:text-sm font-semibold text-slate-900 truncate" id="previewModalTitle">Pratinjau Dokumen</h4>
                        <p class="text-[11px] text-slate-400">Google Drive Embedded Viewer</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a id="previewExternalLink" href="#" target="_blank" rel="noopener noreferrer"
                        class="inline-flex items-center gap-1 px-3 py-1 text-xs font-medium text-slate-700 bg-white hover:bg-slate-100 border border-slate-200 rounded-lg transition-colors">
                        <i data-lucide="external-link" class="w-3 h-3 text-slate-500"></i>
                        <span class="hidden sm:inline">Buka Tab Baru</span>
                    </a>
                    <button type="button" onclick="hidePreviewModal()" class="w-7 h-7 rounded-lg text-slate-400 hover:text-slate-600 flex items-center justify-center transition-colors cursor-pointer">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>

            {{-- Viewer Iframe --}}
            <div class="flex-1 bg-slate-100 relative">
                <iframe id="previewIframe" src="" class="w-full h-full border-0" allow="autoplay"></iframe>
            </div>
        </div>
    </div>

    @if (!$isReadOnlyPortal)
    {{-- Quick Tautkan / Ubah Link Arsip Modal --}}
    <div id="archiveModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full border border-slate-200 overflow-hidden transform transition-all scale-95 opacity-0" id="archiveModalContent">
            <form id="archiveForm" method="POST" action="" onsubmit="submitArchiveLink(event)">
                @csrf
                <div class="p-5 space-y-4">
                    <div class="flex items-center justify-between pb-2.5 border-b border-slate-100">
                        <div>
                            <h3 class="text-sm font-semibold text-slate-900">Tautkan Berkas Google Drive</h3>
                            <p class="text-[11px] text-slate-400 font-mono" id="archiveModalLetterNumber">-</p>
                        </div>
                        <button type="button" onclick="hideArchiveModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>

                    <div class="space-y-1.5">
                        <label for="modal_drive_url" class="text-xs font-medium text-slate-600">
                            URL / Link Google Drive
                        </label>
                        <input type="url" id="modal_drive_url" name="drive_url" value=""
                            class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-400/20 focus:border-slate-400 font-medium"
                            placeholder="https://drive.google.com/file/d/.../view?usp=sharing">
                        <p class="text-[11px] text-slate-400">Kosongkan link jika ingin menghapus tautan berkas arsip.</p>
                    </div>

                    <div class="flex items-center gap-2 pt-2">
                        <button type="button" onclick="hideArchiveModal()"
                            class="flex-1 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs rounded-xl transition-all cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" id="btnSubmitArchive"
                            class="flex-1 px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs rounded-xl shadow-xs transition-all cursor-pointer">
                            Simpan Tautan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endif

    @push('scripts')
        <script>
            let toastTimer = null;

            function showToast(message, type = 'success') {
                const toast = document.getElementById('toastPopup');
                const toastMsg = document.getElementById('toastMessage');
                const toastIconWrap = document.getElementById('toastIconWrap');
                const toastIcon = document.getElementById('toastIcon');

                if (!toast || !toastMsg) return;

                toastMsg.textContent = message;

                if (type === 'error') {
                    toast.className = 'toast-popup show fixed top-5 left-1/2 z-[9999] flex items-center gap-3 px-4 py-2.5 rounded-2xl bg-white/95 backdrop-blur-md border border-red-200 text-slate-800 shadow-lg text-xs font-medium';
                    toastIconWrap.className = 'w-6 h-6 rounded-lg bg-red-50 text-red-600 flex items-center justify-center flex-shrink-0';
                    toastIcon.setAttribute('data-lucide', 'alert-circle');
                } else {
                    toast.className = 'toast-popup show fixed top-5 left-1/2 z-[9999] flex items-center gap-3 px-4 py-2.5 rounded-2xl bg-white/95 backdrop-blur-md border border-slate-200 text-slate-800 shadow-lg text-xs font-medium';
                    toastIconWrap.className = 'w-6 h-6 rounded-lg bg-slate-100 text-slate-800 flex items-center justify-center flex-shrink-0';
                    toastIcon.setAttribute('data-lucide', 'check');
                }

                lucide.createIcons();

                clearTimeout(toastTimer);
                toastTimer = setTimeout(() => {
                    hideToast();
                }, 4000);
            }

            function hideToast() {
                const toast = document.getElementById('toastPopup');
                if (toast) {
                    toast.classList.remove('show');
                }
            }

            function copyToClipboard(text, successMessage) {
                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(text).then(function() {
                        showToast(successMessage, 'success');
                    }).catch(function() {
                        fallbackCopyText(text, successMessage);
                    });
                } else {
                    fallbackCopyText(text, successMessage);
                }
            }

            function fallbackCopyText(text, successMessage) {
                try {
                    const temp = document.createElement('textarea');
                    temp.value = text;
                    temp.style.position = 'fixed';
                    temp.style.top = '-9999px';
                    temp.style.left = '-9999px';
                    document.body.appendChild(temp);
                    temp.focus();
                    temp.select();
                    const successful = document.execCommand('copy');
                    document.body.removeChild(temp);
                    if (successful) {
                        showToast(successMessage, 'success');
                    } else {
                        showToast('Gagal menyalin.', 'error');
                    }
                } catch (e) {
                    showToast('Gagal menyalin.', 'error');
                }
            }

            function selectCard(cardEl) {
                document.querySelectorAll('.mac-file-card, .mac-surface').forEach(el => {
                    if (el !== cardEl) el.classList.remove('selected');
                });
                cardEl.classList.toggle('selected');
            }

            function applyFilter(key, value) {
                const url = new URL(window.location.href);
                if (value) {
                    url.searchParams.set(key, value);
                } else {
                    url.searchParams.delete(key);
                }
                url.searchParams.delete('page');
                window.location.href = url.toString();
            }

            function toggleAdvancedFilter() {
                const drawer = document.getElementById('advancedFilterDrawer');
                if (!drawer) return;
                drawer.classList.toggle('hidden');
            }

            function toggleCardDropdown(event, dropdownId) {
                event.stopPropagation();
                const dropdown = document.getElementById(dropdownId);
                if (!dropdown) return;

                document.querySelectorAll('.dropdown-container > div').forEach(el => {
                    if (el.id !== dropdownId) el.classList.add('hidden');
                });

                dropdown.classList.toggle('hidden');
            }

            document.addEventListener('click', function(e) {
                if (!e.target.closest('.dropdown-container')) {
                    document.querySelectorAll('.dropdown-container > div').forEach(el => {
                        el.classList.add('hidden');
                    });
                }
            });

            function openPreviewModal(previewUrl, title) {
                const modal = document.getElementById('previewModal');
                const modalContent = document.getElementById('previewModalContent');
                const iframe = document.getElementById('previewIframe');
                const titleEl = document.getElementById('previewModalTitle');
                const externalLink = document.getElementById('previewExternalLink');

                if (!modal || !modalContent || !iframe) return;

                titleEl.textContent = title ? title : 'Pratinjau Dokumen';
                iframe.src = previewUrl;
                externalLink.href = previewUrl.replace('/preview', '/view');

                modal.classList.remove('hidden');
                setTimeout(() => {
                    modalContent.classList.remove('scale-95', 'opacity-0');
                    modalContent.classList.add('scale-100', 'opacity-100');
                }, 10);
                lucide.createIcons();
            }

            function hidePreviewModal() {
                const modal = document.getElementById('previewModal');
                const modalContent = document.getElementById('previewModalContent');
                const iframe = document.getElementById('previewIframe');
                if (!modal || !modalContent) return;

                modalContent.classList.remove('scale-100', 'opacity-100');
                modalContent.classList.add('scale-95', 'opacity-0');

                setTimeout(() => {
                    modal.classList.add('hidden');
                    if (iframe) iframe.src = '';
                }, 200);
            }

            function openArchiveModal(letterId, letterNumber, currentUrl) {
                const modal = document.getElementById('archiveModal');
                const modalContent = document.getElementById('archiveModalContent');
                const form = document.getElementById('archiveForm');
                const input = document.getElementById('modal_drive_url');
                const titleEl = document.getElementById('archiveModalLetterNumber');

                if (!modal || !modalContent || !form) return;

                form.action = `/letters/${letterId}/archive-link`;
                if (titleEl) titleEl.textContent = letterNumber || '-';
                if (input) input.value = currentUrl || '';

                modal.classList.remove('hidden');
                setTimeout(() => {
                    modalContent.classList.remove('scale-95', 'opacity-0');
                    modalContent.classList.add('scale-100', 'opacity-100');
                    if (input) input.focus();
                }, 10);
                lucide.createIcons();
            }

            function hideArchiveModal() {
                const modal = document.getElementById('archiveModal');
                const modalContent = document.getElementById('archiveModalContent');
                if (!modal || !modalContent) return;

                modalContent.classList.remove('scale-100', 'opacity-100');
                modalContent.classList.add('scale-95', 'opacity-0');

                setTimeout(() => {
                    modal.classList.add('hidden');
                }, 200);
            }

            async function submitArchiveLink(e) {
                e.preventDefault();
                const form = document.getElementById('archiveForm');
                const btn = document.getElementById('btnSubmitArchive');
                const url = form.action;
                const driveUrl = document.getElementById('modal_drive_url').value;

                btn.disabled = true;
                btn.innerHTML = '<span class="inline-block animate-spin mr-1">⏳</span> Menyimpan...';

                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ drive_url: driveUrl })
                    });

                    const data = await response.json();
                    if (data.success) {
                        hideArchiveModal();
                        showToast(data.message, 'success');
                        setTimeout(() => window.location.reload(), 700);
                    } else {
                        showToast(data.message || 'Gagal menyimpan tautan.', 'error');
                        btn.disabled = false;
                        btn.innerHTML = 'Simpan Tautan';
                    }
                } catch (err) {
                    showToast('Terjadi kesalahan jaringan.', 'error');
                    btn.disabled = false;
                    btn.innerHTML = 'Simpan Tautan';
                }
            }

            document.addEventListener('DOMContentLoaded', function () {
                @if (session('success'))
                    showToast("{{ session('success') }}", 'success');
                @elseif (session('error'))
                    showToast("{{ session('error') }}", 'error');
                @endif

                lucide.createIcons();
            });
        </script>
    @endpush
@endsection
