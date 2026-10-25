@extends('layouts.app')

@section('title', 'Master Data Ruangan - FEB UNJ')
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

        /* Hide Scrollbar Utility */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* Scoped Search & Filter Bar Layout */
        .search-input-col {
            flex: 1 1 auto;
            min-width: 0;
            width: 100%;
        }
        .status-select-col {
            width: 100%;
            flex: 0 0 auto;
        }
        @media (min-width: 640px) {
            .search-input-col {
                flex: 1 1 0%;
                min-width: 240px;
                width: auto;
            }
            .status-select-col {
                width: 155px !important;
                min-width: 145px !important;
                max-width: 165px !important;
                flex: 0 0 155px !important;
            }
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

        /* macOS Modal Transition */
        .mac-modal-backdrop {
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.25s ease-out;
        }
        .mac-modal-backdrop.show {
            opacity: 1;
            pointer-events: auto;
        }
        .mac-modal-content {
            transform: scale(0.95) translateY(10px);
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .mac-modal-backdrop.show .mac-modal-content {
            transform: scale(1) translateY(0);
        }
    </style>
@endpush

@section('content')
    @php
        $canManage = auth()->user()->isAdmin() || auth()->user()->isOperator();
        $selectedGroup = isset($roomGroups) ? $roomGroups->firstWhere('id', request('room_group_id')) : null;
        $activeFiltersCount = 0;
        if (request('search')) $activeFiltersCount++;
        if (request('room_group_id')) $activeFiltersCount++;
        if (request('status')) $activeFiltersCount++;
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
                    <i data-lucide="door-open" class="w-5 h-5"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">Master Data Ruangan</h1>
                    <p class="text-xs sm:text-sm text-gray-500">Kelola master ruangan, kapasitas kursi, grup lantai/gedung, serta status ketersediaan peminjaman FEB UNJ</p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                {{-- Shortcut ke Master Grup Ruangan --}}
                <a href="{{ route('master.room-groups.index') }}"
                    class="inline-flex items-center gap-2 px-3.5 py-2 text-xs sm:text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-200 rounded-full shadow-2xs hover:shadow-xs transition-all active:scale-[0.98]"
                    title="Buka Master Grup Ruangan">
                    <i data-lucide="folder" class="w-4 h-4 text-gray-500"></i>
                    <span>Master Grup</span>
                </a>

                @if ($canManage)
                    <a href="{{ route('master.rooms.create') }}"
                        class="inline-flex items-center gap-2 px-4.5 py-2 text-xs sm:text-sm font-bold text-white bg-gradient-to-r from-[#FF4D00] to-amber-500 hover:from-[#e04400] hover:to-[#d97706] rounded-full shadow-xs hover:shadow-md transition-all active:scale-[0.98]">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        <span>Tambah Ruangan</span>
                    </a>
                @endif
            </div>
        </div>

        {{-- SELECTION BY GROUP: Apple Segmented Pill Tabs with Navigation Arrows --}}
        @if (isset($roomGroups) && $roomGroups->count() > 0)
            <div class="space-y-2.5">
                <div class="flex items-center justify-between px-1">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-lg bg-orange-100 text-[#FF4D00] flex items-center justify-center text-xs font-bold shadow-2xs">
                            <i data-lucide="folder-tree" class="w-3.5 h-3.5"></i>
                        </div>
                        <h2 class="text-xs sm:text-sm font-bold text-gray-800 tracking-tight">Pilih Grup / Lokasi Ruangan</h2>
                        @if ($selectedGroup)
                            <span class="text-[11px] font-semibold text-gray-500 hidden sm:inline-flex items-center gap-1">
                                &bull; Menampilkan grup: <strong class="text-gray-900">{{ $selectedGroup->name }}</strong>
                            </span>
                        @endif
                    </div>
                    @if (request('room_group_id'))
                        <a href="{{ route('master.rooms.index', array_merge(request()->except(['room_group_id', 'page']))) }}"
                            class="inline-flex items-center gap-1 text-[11px] font-semibold text-[#FF4D00] hover:text-orange-700 hover:underline transition-colors cursor-pointer">
                            <i data-lucide="layers" class="w-3 h-3"></i>
                            <span>Tampilkan Semua Ruangan</span>
                        </a>
                    @endif
                </div>

                <div class="flex items-center gap-2 w-full">
                    {{-- Left Navigation Button --}}
                    <button type="button" id="tabScrollLeft" onclick="scrollGroupTabs('left')"
                        class="w-9 h-9 sm:w-10 sm:h-10 shrink-0 flex items-center justify-center rounded-2xl bg-white hover:bg-orange-50 text-gray-700 hover:text-[#FF4D00] border border-gray-200 shadow-2xs hover:shadow-xs transition-all active:scale-95 cursor-pointer"
                        title="Geser grup ke kiri" aria-label="Geser grup ke kiri">
                        <i data-lucide="chevron-left" class="w-5 h-5"></i>
                    </button>

                    {{-- Tab List Container (No Scrollbar, Draggable, Smooth Scroll) --}}
                    <div class="flex-1 min-w-0 bg-gray-100/70 p-1.5 rounded-2xl border border-gray-200/60 backdrop-blur-xs relative overflow-hidden">
                        <nav id="group-tab-nav" class="flex items-center gap-1.5 overflow-x-auto no-scrollbar scroll-smooth w-full px-0.5 cursor-grab select-none" aria-label="Tabs Grup Ruangan">
                            {{-- Tab: Semua Ruangan --}}
                            <a href="{{ route('master.rooms.index', array_merge(request()->except(['room_group_id', 'page']))) }}"
                               class="tab-item shrink-0 whitespace-nowrap px-4 py-2 rounded-xl text-xs sm:text-sm transition-all flex items-center gap-2 {{ !request('room_group_id') ? 'mac-tab-active text-[#FF4D00]' : 'mac-tab-inactive' }}">
                                <i data-lucide="layers" class="w-4 h-4"></i>
                                <span>Semua Ruangan</span>
                                <span class="text-[11px] font-bold px-1.5 py-0.5 rounded-full {{ !request('room_group_id') ? 'bg-orange-100 text-[#FF4D00]' : 'bg-gray-200/80 text-gray-600' }}">
                                    {{ $totalRoomsCount ?? \App\Models\Room::count() }}
                                </span>
                            </a>

                            {{-- Tabs: Setiap RoomGroup --}}
                            @foreach ($roomGroups as $group)
                                <a href="{{ route('master.rooms.index', array_merge(request()->except(['room_group_id', 'page']), ['room_group_id' => $group->id])) }}"
                                   class="tab-item shrink-0 whitespace-nowrap px-4 py-2 rounded-xl text-xs sm:text-sm transition-all flex items-center gap-2 {{ request('room_group_id') == $group->id ? 'mac-tab-active text-[#FF4D00]' : 'mac-tab-inactive' }}">
                                    <i data-lucide="folder" class="w-4 h-4"></i>
                                    <span>{{ $group->name }}</span>
                                    <span class="text-[11px] font-bold px-1.5 py-0.5 rounded-full {{ request('room_group_id') == $group->id ? 'bg-orange-100 text-[#FF4D00]' : 'bg-gray-200/80 text-gray-600' }}">
                                        {{ $group->rooms_count ?? $group->rooms()->count() }}
                                    </span>
                                </a>
                            @endforeach
                        </nav>
                    </div>

                    {{-- Right Navigation Button --}}
                    <button type="button" id="tabScrollRight" onclick="scrollGroupTabs('right')"
                        class="w-9 h-9 sm:w-10 sm:h-10 shrink-0 flex items-center justify-center rounded-2xl bg-white hover:bg-orange-50 text-gray-700 hover:text-[#FF4D00] border border-gray-200 shadow-2xs hover:shadow-xs transition-all active:scale-95 cursor-pointer"
                        title="Geser grup ke kanan" aria-label="Geser grup ke kanan">
                        <i data-lucide="chevron-right" class="w-5 h-5"></i>
                    </button>
                </div>
            </div>
        @endif

        {{-- SEARCH BY TEXT & FILTERS: Apple Spotlight Box --}}
        <div class="mac-card p-4 sm:p-5 relative overflow-hidden">
            <form method="GET" action="{{ route('master.rooms.index') }}" class="space-y-3.5" id="searchForm">
                {{-- Preserve Group Filter & Per Page --}}
                @if (request('room_group_id'))
                    <input type="hidden" name="room_group_id" value="{{ request('room_group_id') }}">
                @endif
                @if (request('per_page'))
                    <input type="hidden" name="per_page" value="{{ request('per_page') }}">
                @endif

                {{-- Spotlight Input Bar --}}
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 w-full">
                    {{-- 1. Search Text Input (Lebar & Dominan) --}}
                    <div class="search-input-col relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-[#FF4D00] transition-colors">
                            <i data-lucide="search" class="w-5 h-5"></i>
                        </div>
                        <input type="text" name="search" id="searchInput" value="{{ request('search') }}"
                            class="w-full h-12 pl-12 pr-24 text-xs sm:text-sm bg-gray-50/80 hover:bg-gray-50 focus:bg-white border border-gray-200/90 focus:border-[#FF4D00] rounded-2xl focus:outline-none focus:ring-4 focus:ring-orange-500/10 shadow-2xs transition-all font-medium placeholder:text-gray-400 placeholder:font-normal"
                            placeholder="Cari berdasarkan nama ruangan, nomor lantai, gedung, fasilitas, atau deskripsi..."
                            autocomplete="off">
                        
                        {{-- Clear Button & Shortcut Badge --}}
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center gap-1.5">
                            @if (request('search'))
                                <a href="{{ route('master.rooms.index', array_merge(request()->except(['search', 'page']))) }}"
                                    class="w-6 h-6 rounded-full bg-gray-200/80 hover:bg-gray-300 text-gray-600 flex items-center justify-center transition-all cursor-pointer"
                                    title="Hapus pencarian teks">
                                    <i data-lucide="x" class="w-3.5 h-3.5"></i>
                                </a>
                            @endif
                            <kbd class="hidden md:inline-flex items-center px-2 py-0.5 text-[10px] font-mono font-medium text-gray-400 bg-white border border-gray-200 rounded-md shadow-2xs" title="Tekan / pada keyboard untuk mencari">
                                /
                            </kbd>
                        </div>
                    </div>

                    {{-- 2. Status Filter Dropdown (Ringkas / Tidak Terlalu Lebar) --}}
                    <div class="status-select-col relative shrink-0">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                            <i data-lucide="sliders-horizontal" class="w-3.5 h-3.5"></i>
                        </div>
                        <select name="status" onchange="this.form.submit()"
                            class="w-full h-12 pl-9 pr-8 text-xs sm:text-sm font-medium bg-gray-50/80 hover:bg-gray-50 focus:bg-white border border-gray-200/90 focus:border-[#FF4D00] rounded-2xl focus:outline-none focus:ring-4 focus:ring-orange-500/10 shadow-2xs transition-all text-gray-700 cursor-pointer appearance-none">
                            <option value="">Semua Status</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400">
                            <i data-lucide="chevron-down" class="w-4 h-4"></i>
                        </div>
                    </div>

                    {{-- 3. Tombol Submit Cari --}}
                    <button type="submit"
                        class="w-full sm:w-auto h-12 px-6 shrink-0 inline-flex items-center justify-center gap-2 bg-gradient-to-r from-[#FF4D00] to-amber-500 hover:from-[#e04400] hover:to-[#d97706] text-white text-xs sm:text-sm font-bold rounded-2xl shadow-xs hover:shadow-md transition-all active:scale-[0.98] cursor-pointer">
                        <i data-lucide="search" class="w-4 h-4"></i>
                        <span>Cari</span>
                    </button>
                </div>

                {{-- Quick Preset Chips & Summary Row --}}
                <div class="flex flex-wrap items-center justify-between gap-3 pt-0.5 text-xs">
                    {{-- Quick Keywords --}}
                    <div class="flex flex-wrap items-center gap-1.5">
                        <span class="text-gray-400 font-medium flex items-center gap-1">
                            <i data-lucide="sparkles" class="w-3.5 h-3.5 text-amber-500"></i>
                            Pintasan Kategori:
                        </span>
                        @php
                            $quickPresets = ['Rapat', 'Sidang', 'Lab', 'Kelas', 'Lobby'];
                        @endphp
                        @foreach ($quickPresets as $preset)
                            <a href="{{ route('master.rooms.index', array_merge(request()->except(['search', 'page']), ['search' => $preset])) }}"
                                class="inline-flex items-center gap-1 px-3 py-1 rounded-xl text-xs font-semibold {{ request('search') == $preset ? 'bg-orange-100 text-[#FF4D00] border border-orange-300 shadow-2xs' : 'bg-gray-100/80 hover:bg-orange-50 text-gray-600 hover:text-[#FF4D00] border border-gray-200/60 hover:border-orange-200' }} transition-all active:scale-[0.96]"
                                title="Cari ruangan {{ $preset }}">
                                <span>{{ $preset }}</span>
                            </a>
                        @endforeach
                    </div>

                    {{-- Active Filter Tags & Count --}}
                    <div class="flex flex-wrap items-center gap-2">
                        @if (request('room_group_id') && $selectedGroup)
                            <a href="{{ route('master.rooms.index', array_merge(request()->except(['room_group_id', 'page']))) }}"
                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-orange-50 text-orange-800 border border-orange-200 hover:bg-orange-100 transition-colors"
                                title="Hapus filter grup {{ $selectedGroup->name }}">
                                <i data-lucide="folder" class="w-3 h-3 text-orange-600"></i>
                                <span>Grup: <strong>{{ $selectedGroup->name }}</strong></span>
                                <i data-lucide="x" class="w-3 h-3 text-orange-500"></i>
                            </a>
                        @endif

                        @if (request('status'))
                            <a href="{{ route('master.rooms.index', array_merge(request()->except(['status', 'page']))) }}"
                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-orange-50 text-orange-800 border border-orange-200 hover:bg-orange-100 transition-colors"
                                title="Hapus filter status">
                                <span>Status: <strong>{{ request('status') === 'active' ? 'Aktif' : 'Nonaktif' }}</strong></span>
                                <i data-lucide="x" class="w-3 h-3 text-orange-500"></i>
                            </a>
                        @endif

                        @if ($activeFiltersCount > 0)
                            <a href="{{ route('master.rooms.index') }}"
                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold text-gray-500 hover:text-red-600 bg-white border border-gray-200 hover:border-red-200 shadow-2xs transition-all">
                                <i data-lucide="rotate-ccw" class="w-3 h-3"></i>
                                <span>Reset Filter</span>
                            </a>
                        @endif

                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-white border border-gray-200 text-gray-700 shadow-2xs">
                            <i data-lucide="hash" class="w-3.5 h-3.5 text-orange-500"></i>
                            Total: {{ number_format($rooms->total(), 0, ',', '.') }} Ruangan
                        </span>
                    </div>
                </div>
            </form>
        </div>

        {{-- MAIN DATA CONTAINER (Table View) --}}
        <div class="mac-card overflow-hidden">
            {{-- Table Control Bar (Per Page & Status Info) --}}
            <div class="px-5 py-3 border-b border-gray-100 bg-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs text-gray-500">
                <div class="flex items-center gap-2">
                    <span>Tampilkan:</span>
                    <form method="GET" action="{{ route('master.rooms.index') }}" class="inline-block m-0">
                        @if (request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                        @if (request('room_group_id')) <input type="hidden" name="room_group_id" value="{{ request('room_group_id') }}"> @endif
                        @if (request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif

                        <select name="per_page" id="per_page"
                            class="px-2.5 py-1 text-xs border border-gray-200 rounded-lg bg-gray-50/50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-[#FF4D00] transition-colors cursor-pointer"
                            onchange="this.form.submit()">
                            <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ request('per_page', 10) == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page', 10) == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page', 10) == 100 ? 'selected' : '' }}>100</option>
                        </select>
                    </form>
                    <span>ruangan per halaman</span>
                </div>

                <div class="flex items-center gap-2">
                    @if (request('room_group_id') && $selectedGroup)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-orange-50 text-orange-800 border border-orange-200/80">
                            <i data-lucide="folder" class="w-3.5 h-3.5 text-[#FF4D00]"></i>
                            Grup: "<strong>{{ $selectedGroup->name }}</strong>"
                        </span>
                    @endif
                    @if (request('search'))
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-orange-50 text-orange-800 border border-orange-200/80">
                            <i data-lucide="filter" class="w-3.5 h-3.5 text-[#FF4D00]"></i>
                            Pencarian: "<strong>{{ trim(request('search')) }}</strong>"
                        </span>
                    @endif
                    <span>Halaman {{ $rooms->currentPage() }} dari {{ $rooms->lastPage() }}</span>
                </div>
            </div>

            {{-- Table View --}}
            <div class="overflow-x-auto">
                @if ($rooms->count() > 0)
                    <table class="min-w-full divide-y divide-gray-100 text-left">
                        <thead>
                            <tr class="bg-gray-50/60 border-b border-gray-100">
                                <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider w-12 text-center">No</th>
                                <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider min-w-[240px]">Nama Ruangan</th>
                                <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider min-w-[170px]">Grup / Lantai</th>
                                <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider min-w-[150px]">Kapasitas</th>
                                <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider min-w-[220px] hidden lg:table-cell">Deskripsi &amp; Fasilitas</th>
                                <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider min-w-[120px] text-center">Status</th>
                                <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider min-w-[140px] text-center hidden sm:table-cell">Total Peminjaman</th>
                                @if ($canManage)
                                    <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider text-center w-36">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach ($rooms as $index => $room)
                                <tr class="hover:bg-orange-50/30 transition-colors group">
                                    {{-- No --}}
                                    <td class="px-4 py-4 text-center text-xs font-semibold text-gray-400">
                                        {{ ($rooms->currentPage() - 1) * $rooms->perPage() + $index + 1 }}
                                    </td>

                                    {{-- Nama Ruangan --}}
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-3">
                                            {{-- Icon Avatar Badge --}}
                                            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-orange-100 to-amber-100 text-[#FF4D00] border border-orange-200/80 flex items-center justify-center font-bold text-xs shadow-2xs flex-shrink-0">
                                                <i data-lucide="door-open" class="w-4 h-4"></i>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div class="flex items-center gap-1.5">
                                                    <p class="text-xs sm:text-sm font-bold text-gray-900 leading-snug truncate">
                                                        {{ $room->name }}
                                                    </p>
                                                    <button type="button" onclick="copyToClipboard('{{ addslashes($room->name) }}', 'Nama ruangan disalin!')"
                                                        class="opacity-0 group-hover:opacity-100 p-1 text-gray-400 hover:text-orange-600 transition-all rounded-md hover:bg-orange-50 cursor-pointer flex-shrink-0"
                                                        title="Salin Nama Ruangan">
                                                        <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                                                    </button>
                                                </div>
                                                <p class="text-[11px] text-gray-400 mt-0.5">
                                                    Fasilitas Ruang Kampus FEB UNJ
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Grup Ruangan --}}
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        @if ($room->roomGroup)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-200/80 shadow-2xs">
                                                <i data-lucide="folder" class="w-3.5 h-3.5 text-gray-500"></i>
                                                <span>{{ $room->roomGroup->name }}</span>
                                            </span>
                                        @else
                                            <span class="text-xs text-gray-400 italic">Tanpa Grup</span>
                                        @endif
                                    </td>

                                    {{-- Kapasitas Kursi --}}
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div>
                                            <span class="font-mono text-xs sm:text-sm font-extrabold text-gray-900">
                                                {{ $room->capacity ?? '-' }}
                                            </span>
                                            <span class="text-xs text-gray-400 font-medium">Kursi / Orang</span>
                                        </div>
                                        <p class="text-[11px] text-gray-400 font-medium mt-0.5">Daya Tampung Maksimal</p>
                                    </td>

                                    {{-- Deskripsi & Fasilitas --}}
                                    <td class="px-4 py-4 hidden lg:table-cell">
                                        <p class="text-xs text-gray-600 line-clamp-2 max-w-sm leading-relaxed" title="{{ $room->description }}">
                                            {{ $room->description ?: '-' }}
                                        </p>
                                    </td>

                                    {{-- Status Aktif --}}
                                    <td class="px-4 py-4 text-center whitespace-nowrap">
                                        @if ($room->is_active)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/90 shadow-2xs">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                <span>Aktif</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200/90 shadow-2xs">
                                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                                <span>Nonaktif</span>
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Total Peminjaman --}}
                                    <td class="px-4 py-4 text-center whitespace-nowrap hidden sm:table-cell">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-gray-100/90 text-gray-700 border border-gray-200/80">
                                            <i data-lucide="calendar" class="w-3 h-3 text-orange-500"></i>
                                            {{ $room->reservations_count ?? $room->reservations()->count() }} Agenda
                                        </span>
                                    </td>

                                    {{-- Aksi --}}
                                    @if ($canManage)
                                        <td class="px-4 py-4 text-center whitespace-nowrap">
                                            <div class="flex items-center justify-center gap-2">
                                                {{-- Edit Button --}}
                                                <a href="{{ route('master.rooms.edit', $room) }}"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-orange-50 hover:bg-orange-100 text-[#FF4D00] hover:text-orange-700 font-bold text-xs rounded-full border border-orange-200/80 shadow-2xs transition-all active:scale-[0.96]"
                                                    title="Edit Data Ruangan">
                                                    <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                                                    <span>Edit</span>
                                                </a>

                                                {{-- Delete Button --}}
                                                <button type="button"
                                                    onclick="openDeleteModal('{{ route('master.rooms.destroy', $room) }}', '{{ addslashes($room->name) }}', '{{ $room->capacity ?? '-' }} Kursi')"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 hover:text-rose-800 font-bold text-xs rounded-full border border-rose-200/80 shadow-2xs transition-all active:scale-[0.96] cursor-pointer"
                                                    title="Hapus Ruangan">
                                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                                    <span>Hapus</span>
                                                </button>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    {{-- Empty State (Apple Style) --}}
                    <div class="p-12 text-center">
                        <div class="w-14 h-14 mx-auto rounded-2xl bg-orange-50 text-[#FF4D00] flex items-center justify-center mb-3.5 shadow-2xs border border-orange-100">
                            <i data-lucide="door-closed" class="w-7 h-7"></i>
                        </div>
                        <h3 class="text-base font-bold text-gray-900 tracking-tight">Tidak Ada Data Ruangan Ditemukan</h3>
                        <p class="text-xs sm:text-sm text-gray-500 max-w-sm mx-auto mt-1 leading-relaxed">
                            @if (request('search') || request('room_group_id') || request('status'))
                                Tidak ada ruangan yang cocok dengan filter pencarian yang diterapkan. Silakan sesuaikan kata kunci Anda.
                            @else
                                Belum ada data master ruangan yang terdaftar di sistem FEB UNJ.
                            @endif
                        </p>
                        <div class="mt-5 flex items-center justify-center gap-2.5">
                            @if ($activeFiltersCount > 0)
                                <a href="{{ route('master.rooms.index') }}"
                                    class="inline-flex items-center gap-2 px-4 py-2 text-xs sm:text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-200 rounded-full shadow-2xs transition-all">
                                    <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                                    <span>Reset Filter</span>
                                </a>
                            @endif
                            @if ($canManage)
                                <a href="{{ route('master.rooms.create') }}"
                                    class="inline-flex items-center gap-2 px-4.5 py-2 text-xs sm:text-sm font-bold text-white bg-gradient-to-r from-[#FF4D00] to-amber-500 rounded-full shadow-xs hover:shadow-md transition-all active:scale-[0.98]">
                                    <i data-lucide="plus" class="w-4 h-4"></i>
                                    <span>Tambah Ruangan</span>
                                </a>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            {{-- Compact Apple Style Pagination --}}
            @if ($rooms->hasPages())
                <div class="px-5 py-4 border-t border-gray-100 bg-gray-50/40">
                    {{ $rooms->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Delete Confirmation Modal (Apple Sheet Style) --}}
    <div id="deleteModalBackdrop" class="mac-modal-backdrop fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/40 backdrop-blur-xs">
        <div class="mac-modal-content bg-white rounded-3xl p-6 sm:p-7 text-left shadow-2xl border border-gray-100 sm:max-w-md w-full">
            <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center mb-4 shadow-2xs">
                <i data-lucide="alert-triangle" class="w-6 h-6"></i>
            </div>

            <h3 class="text-base sm:text-lg font-bold text-gray-900 tracking-tight">
                Konfirmasi Hapus Ruangan
            </h3>

            <p class="text-xs sm:text-sm text-gray-600 leading-relaxed mt-2">
                Apakah Anda yakin ingin menghapus ruangan <strong id="modalRoomName" class="text-gray-900"></strong> (<span id="modalRoomCapacity" class="text-gray-700 font-mono"></span>)?
            </p>

            <div class="mt-3 p-3 rounded-xl bg-amber-50 border border-amber-200/80 text-[11px] text-amber-800 flex items-start gap-2">
                <i data-lucide="info" class="w-4 h-4 text-amber-600 flex-shrink-0 mt-0.5"></i>
                <span>Pastikan ruangan ini belum pernah digunakan dalam jadwal peminjaman aktif. Data yang sudah dihapus tidak dapat dipulihkan kembali.</span>
            </div>

            <div class="mt-6 flex items-center justify-end gap-2.5">
                <button type="button" onclick="closeDeleteModal()"
                    class="px-4 py-2 text-xs sm:text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-all cursor-pointer">
                    Batal
                </button>
                <form id="deleteForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="px-4.5 py-2 text-xs sm:text-sm font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl shadow-xs transition-all cursor-pointer">
                        Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Group Tabs Scroll & Drag Manager
    function scrollGroupTabs(direction) {
        const nav = document.getElementById('group-tab-nav');
        if (!nav) return;
        const scrollAmount = 260;
        nav.scrollBy({
            left: direction === 'left' ? -scrollAmount : scrollAmount,
            behavior: 'smooth'
        });
    }

    function updateGroupTabScrollState() {
        const nav = document.getElementById('group-tab-nav');
        const btnLeft = document.getElementById('tabScrollLeft');
        const btnRight = document.getElementById('tabScrollRight');

        if (!nav) return;

        const { scrollLeft, scrollWidth, clientWidth } = nav;
        const canScroll = scrollWidth > clientWidth;

        if (!canScroll) {
            if (btnLeft) {
                btnLeft.classList.add('opacity-40', 'cursor-not-allowed');
                btnLeft.classList.remove('cursor-pointer');
            }
            if (btnRight) {
                btnRight.classList.add('opacity-40', 'cursor-not-allowed');
                btnRight.classList.remove('cursor-pointer');
            }
            return;
        }

        // Left arrow state
        const atStart = scrollLeft <= 4;
        if (btnLeft) {
            btnLeft.classList.toggle('opacity-40', atStart);
            btnLeft.classList.toggle('cursor-not-allowed', atStart);
            btnLeft.classList.toggle('cursor-pointer', !atStart);
        }

        // Right arrow state
        const atEnd = scrollLeft >= (scrollWidth - clientWidth - 4);
        if (btnRight) {
            btnRight.classList.toggle('opacity-40', atEnd);
            btnRight.classList.toggle('cursor-not-allowed', atEnd);
            btnRight.classList.toggle('cursor-pointer', !atEnd);
        }
    }

    function initGroupTabDragToScroll() {
        const nav = document.getElementById('group-tab-nav');
        if (!nav) return;

        let isDown = false;
        let startX = 0;
        let scrollStart = 0;
        let isDragging = false;

        nav.addEventListener('mousedown', (e) => {
            if (e.button !== 0) return;
            isDown = true;
            isDragging = false;
            nav.classList.add('cursor-grabbing');
            nav.classList.remove('cursor-grab');
            startX = e.pageX - nav.offsetLeft;
            scrollStart = nav.scrollLeft;
        });

        window.addEventListener('mouseup', () => {
            if (!isDown) return;
            isDown = false;
            nav.classList.remove('cursor-grabbing');
            nav.classList.add('cursor-grab');
            setTimeout(() => { isDragging = false; }, 60);
        });

        nav.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            const x = e.pageX - nav.offsetLeft;
            const walk = (x - startX) * 1.5;
            if (Math.abs(walk) > 4) {
                isDragging = true;
            }
            nav.scrollLeft = scrollStart - walk;
        });

        // Prevent click navigation during dragging
        nav.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', (e) => {
                if (isDragging) {
                    e.preventDefault();
                    e.stopPropagation();
                }
            });
        });

        nav.addEventListener('scroll', updateGroupTabScrollState, { passive: true });
        window.addEventListener('resize', updateGroupTabScrollState);

        // Auto-scroll active tab into center view
        const activeTab = nav.querySelector('.mac-tab-active');
        if (activeTab) {
            activeTab.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
        }

        setTimeout(updateGroupTabScrollState, 50);
    }

    document.addEventListener('DOMContentLoaded', function() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        // Initialize group tabs drag & scroll
        initGroupTabDragToScroll();

        // Keyboard Shortcut: '/' to focus search input
        window.addEventListener('keydown', function(e) {
            if (e.key === '/' && !['INPUT', 'TEXTAREA'].includes(document.activeElement.tagName)) {
                e.preventDefault();
                const searchInput = document.getElementById('searchInput');
                if (searchInput) {
                    searchInput.focus();
                    searchInput.select();
                }
            }
        });

        // Flash message from session
        @if(session('success'))
            showToast("{{ session('success') }}", 'success');
        @endif
        @if(session('error'))
            showToast("{{ session('error') }}", 'error');
        @endif
    });

    // Copy to Clipboard
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

    // Delete Confirmation Modal Manager
    function openDeleteModal(actionUrl, roomName, capacity) {
        const backdrop = document.getElementById('deleteModalBackdrop');
        const form = document.getElementById('deleteForm');
        const nameEl = document.getElementById('modalRoomName');
        const capacityEl = document.getElementById('modalRoomCapacity');

        if (!backdrop || !form) return;

        form.action = actionUrl;
        nameEl.textContent = roomName;
        capacityEl.textContent = capacity;

        backdrop.classList.add('show');

        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }

    function closeDeleteModal() {
        const backdrop = document.getElementById('deleteModalBackdrop');
        if (backdrop) {
            backdrop.classList.remove('show');
        }
    }

    // Modal click outside to dismiss
    document.addEventListener('click', function(e) {
        const backdrop = document.getElementById('deleteModalBackdrop');
        if (backdrop && e.target === backdrop) {
            closeDeleteModal();
        }
    });

    // Escape key listener for modal
    window.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDeleteModal();
        }
    });
</script>
@endpush
