@extends('layouts.app')

@section('title', 'Daftar Surat - FEB UNJ')
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

        /* Segmented Pill Tabs */
        .mac-tab-active {
            background: #ffffff;
            color: #111827;
            font-weight: 700;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06), 0 1px 2px rgba(0, 0, 0, 0.04);
            border: 1px solid rgba(226, 232, 240, 0.9);
        }
        .mac-tab-inactive {
            color: #64748b;
            font-weight: 500;
            background: transparent;
            border: 1px solid transparent;
        }
        .mac-tab-inactive:hover {
            color: #0f172a;
            background: rgba(255, 255, 255, 0.6);
        }

        /* Line Clamp Utilities */
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
        $activeFiltersCount = 0;
        if (request('date_range')) $activeFiltersCount++;
        if (request('signatory_id')) $activeFiltersCount++;
        if (request('created_by')) $activeFiltersCount++;
        if (request('search')) $activeFiltersCount++;
        if (request('classification_id')) $activeFiltersCount++;
        if (request('year')) $activeFiltersCount++;

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
                    <i data-lucide="mail" class="w-5 h-5"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">Daftar Surat</h1>
                    <p class="text-xs sm:text-sm text-gray-500">Kelola, pantau, dan telusuri seluruh arsip surat keluar FEB UNJ</p>
                </div>
            </div>

            @if ($canManage)
                <div class="flex flex-wrap items-center gap-2.5">
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('letters.import.template') }}"
                            class="inline-flex items-center gap-2 px-3.5 py-2 text-xs sm:text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-200 rounded-full shadow-2xs hover:shadow-xs transition-all active:scale-[0.98]"
                            title="Unduh Template Excel">
                            <i data-lucide="download" class="w-4 h-4 text-gray-500"></i>
                            <span>Template</span>
                        </a>
                        <button type="button" onclick="openImportModal()"
                            class="inline-flex items-center gap-2 px-3.5 py-2 text-xs sm:text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-200 rounded-full shadow-2xs hover:shadow-xs transition-all active:scale-[0.98] cursor-pointer"
                            title="Import Surat dari Excel">
                            <i data-lucide="upload" class="w-4 h-4 text-gray-500"></i>
                            <span>Import Excel</span>
                        </button>
                    @endif

                    <a href="{{ route('letters.create') }}"
                        class="inline-flex items-center gap-2 px-4.5 py-2 text-xs sm:text-sm font-bold text-white bg-gradient-to-r from-[#FF4D00] to-amber-500 hover:from-[#e04400] hover:to-[#d97706] rounded-full shadow-xs hover:shadow-md transition-all active:scale-[0.98]">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        <span>Buat Surat Baru</span>
                    </a>
                </div>
            @endif
        </div>

        {{-- Apple Segmented Pill Tabs (Letter Types Filter) --}}
        <div class="bg-gray-100/70 p-1.5 rounded-2xl border border-gray-200/60 backdrop-blur-xs">
            <nav class="flex items-center gap-1.5 overflow-x-auto scrollbar-none" aria-label="Tabs Jenis Surat">
                <a href="{{ route('letters.index', array_merge(request()->except(['letter_type_id', 'page']))) }}"
                   class="whitespace-nowrap px-4 py-2 rounded-xl text-xs sm:text-sm transition-all flex items-center gap-2 {{ !request('letter_type_id') ? 'mac-tab-active text-[#FF4D00]' : 'mac-tab-inactive' }}">
                    <i data-lucide="layers" class="w-4 h-4"></i>
                    <span>Semua Jenis</span>
                </a>
                @foreach($letterTypes as $type)
                    <a href="{{ route('letters.index', array_merge(request()->except(['letter_type_id', 'page']), ['letter_type_id' => $type->id])) }}"
                       class="whitespace-nowrap px-4 py-2 rounded-xl text-xs sm:text-sm transition-all flex items-center gap-2 {{ request('letter_type_id') == $type->id ? 'mac-tab-active text-[#FF4D00]' : 'mac-tab-inactive' }}">
                        <i data-lucide="file-text" class="w-4 h-4"></i>
                        <span>{{ $type->name }}</span>
                    </a>
                @endforeach
            </nav>
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
                            <a href="{{ route('letters.index', request()->has('letter_type_id') ? ['letter_type_id' => request('letter_type_id')] : []) }}"
                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold text-gray-500 hover:text-red-600 bg-white border border-gray-200 shadow-2xs hover:border-red-200 transition-all">
                                <i data-lucide="rotate-ccw" class="w-3 h-3"></i>
                                <span>Reset Filter</span>
                            </a>
                        @endif
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-white border border-gray-200 text-gray-700 shadow-2xs">
                            <i data-lucide="hash" class="w-3.5 h-3.5 text-orange-500"></i>
                            Total: {{ number_format($letters->total(), 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                {{-- Filter Drawer Form --}}
                <div id="filter-content" class="hidden border-t border-gray-100 bg-gray-50/30 p-5 sm:p-6 transition-all duration-300">
                    <form method="GET" action="{{ route('letters.index') }}">
                        @if (request('letter_type_id'))
                            <input type="hidden" name="letter_type_id" value="{{ request('letter_type_id') }}">
                        @endif
                        @if (request('per_page'))
                            <input type="hidden" name="per_page" value="{{ request('per_page') }}">
                        @endif

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                            {{-- Date Range Filter --}}
                            <div class="space-y-1.5">
                                <label for="date_range" class="text-xs font-semibold text-gray-700 flex items-center gap-1.5">
                                    <i data-lucide="calendar" class="w-3.5 h-3.5 text-gray-400"></i>
                                    Tanggal Surat
                                </label>
                                <input type="text" id="date_range" name="date_range" value="{{ request('date_range') }}"
                                    class="w-full px-3 py-2 text-xs bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-[#FF4D00] shadow-2xs transition-all placeholder:text-gray-400"
                                    placeholder="Pilih rentang tanggal...">
                            </div>

                            {{-- Signatory Filter --}}
                            <div class="space-y-1.5">
                                <label for="signatory_id" class="text-xs font-semibold text-gray-700 flex items-center gap-1.5">
                                    <i data-lucide="pen-tool" class="w-3.5 h-3.5 text-gray-400"></i>
                                    Penandatangan
                                </label>
                                <select id="signatory_id" name="signatory_id" class="select2 w-full text-xs">
                                    <option value="">Semua Penandatangan</option>
                                    @foreach ($signatories as $signatory)
                                        <option value="{{ $signatory->id }}" {{ request('signatory_id') == $signatory->id ? 'selected' : '' }}>
                                            {{ $signatory->position }} - {{ $signatory->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Search Perihal --}}
                            <div class="space-y-1.5">
                                <label for="search" class="text-xs font-semibold text-gray-700 flex items-center gap-1.5">
                                    <i data-lucide="search" class="w-3.5 h-3.5 text-gray-400"></i>
                                    Cari Perihal / Hal
                                </label>
                                <input type="text" id="search" name="search" value="{{ request('search') }}"
                                    class="w-full px-3 py-2 text-xs bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-[#FF4D00] shadow-2xs transition-all placeholder:text-gray-400"
                                    placeholder="Ketik kata kunci perihal...">
                            </div>

                            {{-- Created By Filter --}}
                            <div class="space-y-1.5">
                                <label for="created_by" class="text-xs font-semibold text-gray-700 flex items-center gap-1.5">
                                    <i data-lucide="user" class="w-3.5 h-3.5 text-gray-400"></i>
                                    Dibuat Oleh
                                </label>
                                <select id="created_by" name="created_by" class="select2 w-full text-xs">
                                    <option value="">Semua Pengguna</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}" {{ request('created_by') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Submit & Reset Action Buttons --}}
                            <div class="flex items-center gap-2 col-span-full pt-2">
                                <button type="submit"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-[#FF4D00] to-amber-500 hover:from-[#e04400] hover:to-[#d97706] text-white font-bold text-xs rounded-xl shadow-xs transition-all active:scale-[0.98] cursor-pointer">
                                    <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                                    <span>Terapkan Filter</span>
                                </button>
                                <a href="{{ route('letters.index', request()->has('letter_type_id') ? ['letter_type_id' => request('letter_type_id')] : []) }}"
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
                    <form method="GET" action="{{ route('letters.index') }}" class="inline-block">
                        @if (request('letter_type_id')) <input type="hidden" name="letter_type_id" value="{{ request('letter_type_id') }}"> @endif
                        @if (request('date_range')) <input type="hidden" name="date_range" value="{{ request('date_range') }}"> @endif
                        @if (request('signatory_id')) <input type="hidden" name="signatory_id" value="{{ request('signatory_id') }}"> @endif
                        @if (request('created_by')) <input type="hidden" name="created_by" value="{{ request('created_by') }}"> @endif
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
                    <span>surat per halaman</span>
                </div>

                <div class="flex items-center gap-2">
                    <span>Halaman {{ $letters->currentPage() }} dari {{ $letters->lastPage() }}</span>
                </div>
            </div>

            {{-- Table View --}}
            <div class="overflow-x-auto">
                @if ($letters->count() > 0)
                    <table class="min-w-full divide-y divide-gray-100 text-left">
                        <thead>
                            <tr class="bg-gray-50/60 border-b border-gray-100">
                                <th scope="col" class="px-3 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider w-10 text-center">No</th>
                                <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider min-w-[220px]">Nomor Surat</th>
                                <th scope="col" class="px-3.5 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider min-w-[100px]">Tanggal</th>
                                <th scope="col" class="px-3.5 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider min-w-[115px] hidden md:table-cell">Jenis</th>
                                <th scope="col" class="px-3.5 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider min-w-[125px] hidden lg:table-cell">Penandatangan</th>
                                <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider min-w-[260px]">Perihal</th>
                                <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider min-w-[220px]">Tujuan</th>
                                <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider min-w-[200px]">Mahasiswa</th>
                                <th scope="col" class="px-3.5 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider min-w-[110px] hidden xl:table-cell">Dibuat Oleh</th>
                                <th scope="col" class="px-3.5 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider text-center w-24">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @php
                                $typeColorMap = [
                                    'sertifikat' => 'bg-purple-50 text-purple-700 border-purple-200/80',
                                    'mou' => 'bg-blue-50 text-blue-700 border-blue-200/80',
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
                                @endphp
                                <tr class="hover:bg-orange-50/30 transition-colors group">
                                    {{-- No --}}
                                    <td class="px-4 py-4 text-center text-xs font-semibold text-gray-400">
                                        {{ ($letters->currentPage() - 1) * $letters->perPage() + $index + 1 }}
                                    </td>

                                    {{-- Nomor Surat (Ukuran Lebih Besar & Field Lebih Lebar) --}}
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('letters.show', $letter) }}"
                                                class="font-mono text-[13.5px] sm:text-sm font-bold text-gray-900 group-hover:text-[#FF4D00] transition-colors leading-normal tracking-tight"
                                                title="Klik untuk melihat detail surat">
                                                {{ $letter->letter_number }}
                                            </a>
                                            <button type="button" onclick="copyToClipboard('{{ addslashes($letter->letter_number) }}', 'Nomor surat disalin!')"
                                                class="opacity-0 group-hover:opacity-100 p-1.5 text-gray-400 hover:text-orange-600 transition-all rounded-lg hover:bg-orange-50 cursor-pointer flex-shrink-0"
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

                                    {{-- Perihal (Full Text Tanpa Batasan Truncate) --}}
                                    <td class="px-4 py-4">
                                        <a href="{{ route('letters.show', $letter) }}"
                                            class="text-xs sm:text-[13px] font-semibold text-gray-900 hover:text-[#FF4D00] transition-colors leading-relaxed block break-words"
                                            title="Klik untuk melihat detail surat">
                                            {{ $letter->subject ?: '-' }}
                                        </a>
                                    </td>

                                    {{-- Tujuan / Penerima (Dilebarkan) --}}
                                    <td class="px-4 py-4">
                                        @if ($letter->recipient)
                                            <div class="flex items-start gap-2 text-xs sm:text-[13px] font-medium text-gray-800">
                                                <i data-lucide="send" class="w-3.5 h-3.5 text-blue-500 flex-shrink-0 mt-0.5"></i>
                                                <span class="break-words leading-relaxed">{{ $letter->recipient }}</span>
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-300 font-mono pl-3">-</span>
                                        @endif
                                    </td>

                                    {{-- Mahasiswa Terkait (Dilebarkan) --}}
                                    <td class="px-4 py-4">
                                        @if ($letter->student_name)
                                            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs sm:text-[12.5px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200/90 shadow-2xs max-w-full">
                                                <i data-lucide="graduation-cap" class="w-4 h-4 text-emerald-600 flex-shrink-0"></i>
                                                <span class="break-words leading-snug">{{ $letter->student_name }}</span>
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-300 font-mono pl-3">-</span>
                                        @endif
                                    </td>

                                    {{-- Dibuat Oleh --}}
                                    <td class="px-4 py-4 whitespace-nowrap hidden xl:table-cell">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700 border border-gray-200/80">
                                            <i data-lucide="user" class="w-3 h-3 text-gray-400"></i>
                                            {{ $letter->creator?->name ?? 'Sistem' }}
                                        </span>
                                    </td>

                                    {{-- Aksi --}}
                                    <td class="px-3.5 py-3.5 text-center whitespace-nowrap">
                                        <a href="{{ route('letters.show', $letter) }}"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-orange-50 hover:bg-orange-100 text-[#FF4D00] hover:text-orange-700 font-bold text-xs rounded-full border border-orange-200/80 shadow-2xs transition-all active:scale-[0.96]"
                                            title="Lihat Detail Surat">
                                            <i data-lucide="eye" class="w-3.5 h-3.5 text-[#FF4D00]"></i>
                                            <span>Detail</span>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{-- Modern Pagination Footer (Single Unified Info & Compact Pills) --}}
                    <div class="border-t border-gray-100">
                        {{ $letters->appends(request()->query())->links() }}
                    </div>
                @else
                    {{-- Empty State (Apple Style) --}}
                    <div class="text-center py-16 px-4">
                        <div class="w-16 h-16 rounded-3xl bg-orange-50 border border-orange-200/80 flex items-center justify-center mx-auto mb-4 text-[#FF4D00] shadow-2xs">
                            <i data-lucide="inbox" class="w-8 h-8"></i>
                        </div>
                        <h3 class="text-base font-bold text-gray-900">Tidak Ada Data Surat</h3>
                        <p class="text-xs sm:text-sm text-gray-500 mt-1 max-w-sm mx-auto">
                            Belum ada surat yang terdaftar sesuai filter yang dipilih saat ini.
                        </p>
                        <div class="mt-5 flex items-center justify-center gap-3">
                            @if ($activeFiltersCount > 0)
                                <a href="{{ route('letters.index', request()->has('letter_type_id') ? ['letter_type_id' => request('letter_type_id')] : []) }}"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-white hover:bg-gray-50 border border-gray-200 text-gray-700 font-semibold text-xs rounded-full shadow-2xs transition-all">
                                    <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                                    <span>Reset Filter</span>
                                </a>
                            @endif
                            @if ($canManage)
                                <a href="{{ route('letters.create') }}"
                                    class="inline-flex items-center gap-2 px-5 py-2 bg-gradient-to-r from-[#FF4D00] to-amber-500 hover:from-[#e04400] hover:to-[#d97706] text-white font-bold text-xs rounded-full shadow-xs transition-all">
                                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                                    <span>Buat Surat Baru</span>
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
                        <h3 class="text-base font-bold text-gray-900 tracking-tight">Import Data Surat</h3>
                        <p class="text-xs text-gray-500">Unggah file Excel (.xls, .xlsx) arsip surat</p>
                    </div>
                </div>
                <button type="button" onclick="closeImportModal()"
                    class="p-1 rounded-xl text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors cursor-pointer">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            {{-- Modal Body --}}
            <form action="{{ route('letters.import') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
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

    {{-- Error Modal for Import Errors --}}
    @if (session('import_errors') && count(session('import_errors')) > 0)
        <div id="errorModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-gray-900/40 backdrop-blur-md">
            <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl max-w-2xl w-full border border-gray-200 p-6 space-y-4">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-red-100 text-red-600 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="alert-circle" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Import Gagal</h3>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Ditemukan <strong class="text-red-600">{{ count(session('import_errors')) }} baris</strong> yang memiliki ketidaksesuaian format.
                        </p>
                    </div>
                </div>

                <div class="max-h-80 overflow-y-auto space-y-2.5 p-1">
                    @foreach (session('import_errors') as $error)
                        <div class="p-3.5 bg-red-50 border border-red-200/80 rounded-2xl text-xs space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-red-800">Baris {{ $error['row'] }} &bull; {{ $error['field'] }}</span>
                            </div>
                            <p class="text-red-700">{{ $error['message'] }}</p>
                            @if (!empty($error['value']))
                                <code class="block bg-white p-2 rounded-lg border border-red-200 text-gray-800 font-mono text-[11px] mt-1">{{ $error['value'] }}</code>
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100">
                    <a href="{{ route('letters.import.template') }}"
                        class="px-4 py-2 bg-white hover:bg-gray-50 border border-gray-200 text-gray-700 font-semibold text-xs rounded-xl transition-all">
                        Unduh Template
                    </a>
                    <button type="button" onclick="document.getElementById('errorModal').classList.add('hidden')"
                        class="px-4 py-2 bg-gray-900 hover:bg-gray-800 text-white font-semibold text-xs rounded-xl transition-all">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif

    @push('scripts')
        <script>
            // Toast Notification Manager
            let toastTimer = null;

            function showToast(message, type = 'success') {
                const toast = document.getElementById('toastPopup');
                const toastMsg = document.getElementById('toastMessage');
                const toastIconWrap = document.getElementById('toastIconWrap');
                const toastIcon = document.getElementById('toastIcon');

                if (!toast || !toastMsg) return;

                toastMsg.textContent = message;

                if (type === 'error') {
                    toast.className = 'toast-popup show fixed top-5 left-1/2 z-[9999] flex items-center gap-3 px-4.5 py-3 rounded-2xl bg-white/95 backdrop-blur-md border border-red-200/90 text-gray-800 shadow-xl shadow-red-500/10 text-xs sm:text-sm font-medium';
                    toastIconWrap.className = 'w-7 h-7 rounded-xl bg-red-100 text-red-600 flex items-center justify-center flex-shrink-0';
                    toastIcon.setAttribute('data-lucide', 'alert-circle');
                } else {
                    toast.className = 'toast-popup show fixed top-5 left-1/2 z-[9999] flex items-center gap-3 px-4.5 py-3 rounded-2xl bg-white/95 backdrop-blur-md border border-emerald-200/90 text-gray-800 shadow-xl shadow-emerald-500/10 text-xs sm:text-sm font-medium';
                    toastIconWrap.className = 'w-7 h-7 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0';
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

            // Toggle Filter Drawer
            function toggleFilter() {
                const content = document.getElementById('filter-content');
                const icon = document.getElementById('filter-icon');
                if (!content || !icon) return;

                const isHidden = content.classList.contains('hidden');
                if (isHidden) {
                    content.classList.remove('hidden');
                    icon.style.transform = 'rotate(180deg)';
                } else {
                    content.classList.add('hidden');
                    icon.style.transform = 'rotate(0deg)';
                }
            }

            // Import Modal Helpers
            function openImportModal() {
                const modal = document.getElementById('importModal');
                const content = document.getElementById('importModalContent');
                if (!modal || !content) return;

                modal.classList.remove('hidden');
                setTimeout(() => {
                    content.classList.remove('scale-95', 'opacity-0');
                    content.classList.add('scale-100', 'opacity-100');
                }, 10);
                lucide.createIcons();
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
                }

                if (typeof $ !== 'undefined' && $.fn.select2) {
                    $('#signatory_id').select2({ placeholder: 'Semua Penandatangan', allowClear: true, width: '100%' });
                    $('#created_by').select2({ placeholder: 'Semua Pengguna', allowClear: true, width: '100%' });
                }

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