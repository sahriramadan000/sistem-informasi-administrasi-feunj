@extends('layouts.app')

@section('title', 'Daftar Peminjaman Ruang - FEB UNJ')
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

        /* Table Scrollbar */
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

        /* Hide Scrollbar Utility */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
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
        if (request('date') && request('date') !== today()->toDateString()) $activeFiltersCount++;
        if (request('status')) $activeFiltersCount++;
        if (request('search')) $activeFiltersCount++;

        $canManage = auth()->user()->can_access_room_reservations || auth()->user()->isAdmin();
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
                    <i data-lucide="calendar-days" class="w-5 h-5"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">Daftar Peminjaman Ruang</h1>
                    <p class="text-xs sm:text-sm text-gray-500">Kelola, pantau, dan telusuri jadwal peminjaman ruang rapat &amp; fasilitas FEB UNJ</p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                {{-- Shortcut ke Dashboard Peminjaman Ruang --}}
                <a href="{{ route('room-reservations.dashboard') }}"
                    class="inline-flex items-center gap-2 px-3.5 py-2 text-xs sm:text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-200 rounded-full shadow-2xs hover:shadow-xs transition-all active:scale-[0.98]"
                    title="Buka Dashboard Peminjaman">
                    <i data-lucide="layout-dashboard" class="w-4 h-4 text-gray-500"></i>
                    <span>Dashboard</span>
                </a>

                {{-- Shortcut ke Public Display --}}
                <a href="{{ route('room-reservations.public-display') }}" target="_blank"
                    class="inline-flex items-center gap-2 px-3.5 py-2 text-xs sm:text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-200 rounded-full shadow-2xs hover:shadow-xs transition-all active:scale-[0.98]"
                    title="Buka Public Display Layar TV">
                    <i data-lucide="monitor" class="w-4 h-4 text-blue-500"></i>
                    <span>Public Display</span>
                </a>

                @if ($canManage)
                    <a href="{{ route('room-reservations.create') }}"
                        class="inline-flex items-center gap-2 px-4.5 py-2 text-xs sm:text-sm font-bold text-white bg-gradient-to-r from-[#FF4D00] to-amber-500 hover:from-[#e04400] hover:to-[#d97706] rounded-full shadow-xs hover:shadow-md transition-all active:scale-[0.98]">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        <span>Buat Peminjaman</span>
                    </a>
                @endif
            </div>
        </div>

        {{-- Apple Segmented Pill Tabs with Navigation Arrows (Room Filter) --}}
        <div class="flex items-center gap-2 w-full">
            {{-- Left Navigation Button --}}
            <button type="button" id="tabScrollLeft" onclick="scrollRoomTabs('left')"
                class="w-9 h-9 sm:w-10 sm:h-10 shrink-0 flex items-center justify-center rounded-2xl bg-white hover:bg-orange-50 text-gray-700 hover:text-[#FF4D00] border border-gray-200 shadow-2xs hover:shadow-xs transition-all active:scale-95 cursor-pointer"
                title="Geser tab ke kiri" aria-label="Geser tab ke kiri">
                <i data-lucide="chevron-left" class="w-5 h-5"></i>
            </button>

            {{-- Tab List Container (No Scrollbar, Draggable, Smooth Scroll) --}}
            <div class="flex-1 min-w-0 bg-gray-100/70 p-1.5 rounded-2xl border border-gray-200/60 backdrop-blur-xs relative overflow-hidden">
                <nav id="room-tab-nav" class="flex items-center gap-1.5 overflow-x-auto no-scrollbar scroll-smooth w-full px-0.5 cursor-grab select-none" aria-label="Tabs Ruangan">
                    <a href="{{ route('room-reservations.index', array_merge(request()->except(['room_id', 'page']))) }}"
                       class="tab-item shrink-0 whitespace-nowrap px-4 py-2 rounded-xl text-xs sm:text-sm transition-all flex items-center gap-2 {{ !request('room_id') ? 'mac-tab-active text-[#FF4D00]' : 'mac-tab-inactive' }}">
                        <i data-lucide="layers" class="w-4 h-4"></i>
                        <span>Semua Ruangan</span>
                    </a>
                    @foreach($rooms as $room)
                        <a href="{{ route('room-reservations.index', array_merge(request()->except(['room_id', 'page']), ['room_id' => $room->id])) }}"
                           class="tab-item shrink-0 whitespace-nowrap px-4 py-2 rounded-xl text-xs sm:text-sm transition-all flex items-center gap-2 {{ request('room_id') == $room->id ? 'mac-tab-active text-[#FF4D00]' : 'mac-tab-inactive' }}">
                            <i data-lucide="door-open" class="w-4 h-4"></i>
                            <span>{{ $room->name }}</span>
                        </a>
                    @endforeach
                </nav>
            </div>

            {{-- Right Navigation Button --}}
            <button type="button" id="tabScrollRight" onclick="scrollRoomTabs('right')"
                class="w-9 h-9 sm:w-10 sm:h-10 shrink-0 flex items-center justify-center rounded-2xl bg-white hover:bg-orange-50 text-gray-700 hover:text-[#FF4D00] border border-gray-200 shadow-2xs hover:shadow-xs transition-all active:scale-95 cursor-pointer"
                title="Geser tab ke kanan" aria-label="Geser tab ke kanan">
                <i data-lucide="chevron-right" class="w-5 h-5"></i>
            </button>
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
                        {{-- Active date indicator pill --}}
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold {{ $activeDate === today()->toDateString() ? 'bg-blue-50 text-blue-700 border border-blue-200/80' : 'bg-amber-50 text-amber-700 border border-amber-200/80' }} shadow-2xs">
                            <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                            <span>{{ $activeDate === today()->toDateString() ? 'Hari Ini' : \Carbon\Carbon::parse($activeDate)->translatedFormat('d M Y') }}</span>
                        </span>

                        @if ($activeFiltersCount > 0 || (request('date') && request('date') !== today()->toDateString()))
                            <a href="{{ route('room-reservations.index', request()->has('room_id') ? ['room_id' => request('room_id')] : []) }}"
                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold text-gray-500 hover:text-red-600 bg-white border border-gray-200 shadow-2xs hover:border-red-200 transition-all">
                                <i data-lucide="rotate-ccw" class="w-3 h-3"></i>
                                <span>Reset Filter</span>
                            </a>
                        @endif
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-white border border-gray-200 text-gray-700 shadow-2xs">
                            <i data-lucide="hash" class="w-3.5 h-3.5 text-orange-500"></i>
                            Total: {{ number_format($reservations->total(), 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                {{-- Filter Drawer Form --}}
                <div id="filter-content" class="hidden border-t border-gray-100 bg-gray-50/30 p-5 sm:p-6 transition-all duration-300">
                    <form method="GET" action="{{ route('room-reservations.index') }}">
                        @if (request('room_id'))
                            <input type="hidden" name="room_id" value="{{ request('room_id') }}">
                        @endif
                        @if (request('per_page'))
                            <input type="hidden" name="per_page" value="{{ request('per_page') }}">
                        @endif

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 items-end">
                            {{-- Date Filter --}}
                            <div class="space-y-1.5">
                                <label for="date" class="text-xs font-semibold text-gray-700 flex items-center gap-1.5">
                                    <i data-lucide="calendar" class="w-3.5 h-3.5 text-gray-400"></i>
                                    Tanggal Booking
                                </label>
                                <input type="text" id="date" name="date" value="{{ $activeDate }}"
                                    class="w-full px-3 py-2 text-xs bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-[#FF4D00] shadow-2xs transition-all placeholder:text-gray-400"
                                    placeholder="Pilih tanggal...">
                            </div>

                            {{-- Status Filter --}}
                            <div class="space-y-1.5">
                                <label for="status" class="text-xs font-semibold text-gray-700 flex items-center gap-1.5">
                                    <i data-lucide="check-circle" class="w-3.5 h-3.5 text-gray-400"></i>
                                    Status Peminjaman
                                </label>
                                <select id="status" name="status" class="w-full px-3 py-2 text-xs bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-[#FF4D00] shadow-2xs transition-all text-gray-700">
                                    <option value="">Semua Status</option>
                                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui (Approved)</option>
                                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu Persetujuan (Pending)</option>
                                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak (Rejected)</option>
                                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan (Cancelled)</option>
                                </select>
                            </div>

                            {{-- Search Keyword Filter --}}
                            <div class="space-y-1.5">
                                <label for="search" class="text-xs font-semibold text-gray-700 flex items-center gap-1.5">
                                    <i data-lucide="search" class="w-3.5 h-3.5 text-gray-400"></i>
                                    Cari Kegiatan / Pemohon
                                </label>
                                <input type="text" id="search" name="search" value="{{ request('search') }}"
                                    class="w-full px-3 py-2 text-xs bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-[#FF4D00] shadow-2xs transition-all placeholder:text-gray-400"
                                    placeholder="Ketik nama kegiatan atau pemohon...">
                            </div>

                            {{-- Submit & Reset Action Buttons --}}
                            <div class="flex items-center gap-2 col-span-full pt-2">
                                <button type="submit"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-[#FF4D00] to-amber-500 hover:from-[#e04400] hover:to-[#d97706] text-white font-bold text-xs rounded-xl shadow-xs transition-all active:scale-[0.98] cursor-pointer">
                                    <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                                    <span>Terapkan Filter</span>
                                </button>
                                <a href="{{ route('room-reservations.index', request()->has('room_id') ? ['room_id' => request('room_id')] : []) }}"
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
                    <form method="GET" action="{{ route('room-reservations.index') }}" class="inline-block">
                        @if (request('room_id')) <input type="hidden" name="room_id" value="{{ request('room_id') }}"> @endif
                        <input type="hidden" name="date" value="{{ $activeDate }}">
                        @if (request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
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
                    <span>permohonan per halaman</span>
                </div>

                <div class="flex items-center gap-2">
                    <span>Halaman {{ $reservations->currentPage() }} dari {{ $reservations->lastPage() }}</span>
                </div>
            </div>

            {{-- Table View --}}
            <div class="overflow-x-auto table-scrollbar">
                @if ($reservations->count() > 0)
                    <table class="min-w-full divide-y divide-gray-100 text-left">
                        <thead>
                            <tr class="bg-gray-50/60 border-b border-gray-100">
                                <th scope="col" class="px-3.5 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider w-12 text-center">No</th>
                                <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider min-w-[190px]">Tanggal &amp; Waktu</th>
                                <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider min-w-[210px]">Ruangan</th>
                                <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider min-w-[260px]">Kegiatan / Acara</th>
                                <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider min-w-[200px]">Penanggung Jawab</th>
                                <th scope="col" class="px-3.5 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider min-w-[125px]">Status</th>
                                <th scope="col" class="px-3.5 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider min-w-[120px] hidden xl:table-cell">Dibuat Oleh</th>
                                <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider text-center w-28">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach ($reservations as $index => $reservation)
                                @php
                                    $statusPill = match ($reservation->status) {
                                        'approved' => ['bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200/90', 'dot' => 'bg-emerald-500', 'label' => 'Disetujui'],
                                        'pending' => ['bg' => 'bg-amber-50 text-amber-700 border-amber-200/90', 'dot' => 'bg-amber-500', 'label' => 'Menunggu'],
                                        'rejected' => ['bg' => 'bg-rose-50 text-rose-700 border-rose-200/90', 'dot' => 'bg-rose-500', 'label' => 'Ditolak'],
                                        'cancelled' => ['bg' => 'bg-slate-100 text-slate-700 border-slate-200/90', 'dot' => 'bg-slate-400', 'label' => 'Dibatalkan'],
                                        default => ['bg' => 'bg-gray-100 text-gray-700 border-gray-200/90', 'dot' => 'bg-gray-400', 'label' => ucfirst($reservation->status ?? 'Draft')],
                                    };
                                @endphp
                                <tr class="hover:bg-orange-50/30 transition-colors group">
                                    {{-- No --}}
                                    <td class="px-3.5 py-4 text-center text-xs font-semibold text-gray-400">
                                        {{ ($reservations->currentPage() - 1) * $reservations->perPage() + $index + 1 }}
                                    </td>

                                    {{-- Tanggal & Waktu --}}
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-1.5 text-xs font-bold text-gray-900 mb-1">
                                            <i data-lucide="calendar" class="w-3.5 h-3.5 text-gray-400"></i>
                                            <span>{{ \Carbon\Carbon::parse($reservation->reservation_date)->translatedFormat('d M Y') }}</span>
                                        </div>
                                        <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md font-mono text-[11px] font-bold bg-gray-50 border border-gray-200/80 text-orange-700 shadow-2xs">
                                            <i data-lucide="clock" class="w-3 h-3 text-orange-500"></i>
                                            <span>{{ \Carbon\Carbon::parse($reservation->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($reservation->end_time)->format('H:i') }}</span>
                                        </div>
                                    </td>

                                    {{-- Ruangan --}}
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-8 rounded-xl bg-orange-50 border border-orange-100 flex items-center justify-center text-[#FF4D00] flex-shrink-0">
                                                <i data-lucide="door-open" class="w-4 h-4"></i>
                                            </div>
                                            <div>
                                                <h4 class="text-xs sm:text-[13px] font-bold text-gray-900 group-hover:text-[#FF4D00] transition-colors leading-tight">
                                                    {{ $reservation->room->name ?? '-' }}
                                                </h4>
                                                @if ($reservation->room && $reservation->room->capacity)
                                                    <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mt-0.5 block">
                                                        Kapasitas: {{ $reservation->room->capacity }} Kursi
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Kegiatan / Acara --}}
                                    <td class="px-4 py-4">
                                        <a href="{{ route('room-reservations.show', $reservation) }}"
                                            class="text-xs sm:text-[13px] font-bold text-gray-900 hover:text-[#FF4D00] transition-colors leading-relaxed block break-words"
                                            title="Lihat Detail Peminjaman">
                                            {{ $reservation->event_name }}
                                        </a>
                                        @if ($reservation->facilities)
                                            <div class="flex items-center gap-1 mt-1 text-[11px] text-gray-500 line-clamp-1" title="Fasilitas: {{ $reservation->facilities }}">
                                                <i data-lucide="sparkles" class="w-3 h-3 text-amber-500 flex-shrink-0"></i>
                                                <span class="truncate">{{ $reservation->facilities }}</span>
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Penanggung Jawab / Pemohon --}}
                                    <td class="px-4 py-4">
                                        <div class="text-xs sm:text-[13px] font-semibold text-gray-800 break-words">
                                            {{ $reservation->requester_name }}
                                        </div>
                                        @if ($reservation->contact_person)
                                            <div class="flex items-center gap-1.5 mt-1">
                                                <span class="font-mono text-[11px] text-gray-500">{{ $reservation->contact_person }}</span>
                                                <button type="button" onclick="copyToClipboard('{{ addslashes($reservation->contact_person) }}', 'Kontak PJ berhasil disalin!')"
                                                    class="p-1 text-gray-400 hover:text-orange-600 transition-colors rounded-md hover:bg-orange-50 cursor-pointer"
                                                    title="Salin Nomor Kontak">
                                                    <i data-lucide="copy" class="w-3 h-3"></i>
                                                </button>
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Status --}}
                                    <td class="px-3.5 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold border shadow-2xs {{ $statusPill['bg'] }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $statusPill['dot'] }}"></span>
                                            <span>{{ $statusPill['label'] }}</span>
                                        </span>
                                    </td>

                                    {{-- Dibuat Oleh --}}
                                    <td class="px-3.5 py-4 whitespace-nowrap hidden xl:table-cell">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700 border border-gray-200/80">
                                            <i data-lucide="user" class="w-3 h-3 text-gray-400"></i>
                                            {{ $reservation->creator?->name ?? 'Sistem' }}
                                        </span>
                                    </td>

                                    {{-- Aksi --}}
                                    <td class="px-4 py-4 text-center whitespace-nowrap">
                                        <div class="flex items-center justify-center gap-1.5">
                                            {{-- Detail --}}
                                            <a href="{{ route('room-reservations.show', $reservation) }}"
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-orange-50 hover:bg-orange-100 text-[#FF4D00] hover:text-orange-700 font-bold text-xs rounded-full border border-orange-200/80 shadow-2xs transition-all active:scale-[0.96]"
                                                title="Lihat Detail Peminjaman">
                                                <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                                <span>Detail</span>
                                            </a>

                                            @if(auth()->user()->isAdmin() || auth()->id() == $reservation->created_by)
                                                {{-- Edit --}}
                                                <a href="{{ route('room-reservations.edit', $reservation) }}"
                                                    class="p-1.5 rounded-lg bg-gray-50 hover:bg-amber-50 text-gray-500 hover:text-amber-600 border border-gray-200/80 hover:border-amber-200 transition-all shadow-2xs"
                                                    title="Edit Peminjaman">
                                                    <i data-lucide="edit-2" class="w-3.5 h-3.5"></i>
                                                </a>

                                                {{-- Hapus --}}
                                                <button type="button"
                                                    onclick="openDeleteModal('{{ route('room-reservations.destroy', $reservation) }}', '{{ addslashes($reservation->event_name) }}')"
                                                    class="p-1.5 rounded-lg bg-gray-50 hover:bg-rose-50 text-gray-500 hover:text-rose-600 border border-gray-200/80 hover:border-rose-200 transition-all shadow-2xs cursor-pointer"
                                                    title="Hapus Peminjaman">
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
                        {{ $reservations->appends(request()->query())->links() }}
                    </div>
                @else
                    {{-- Empty State (Apple Style) --}}
                    <div class="text-center py-16 px-4">
                        <div class="w-16 h-16 rounded-3xl bg-orange-50 border border-orange-200/80 flex items-center justify-center mx-auto mb-4 text-[#FF4D00] shadow-2xs">
                            <i data-lucide="calendar-x" class="w-8 h-8"></i>
                        </div>
                        <h3 class="text-base font-bold text-gray-900">Tidak Ada Data Peminjaman</h3>
                        <p class="text-xs sm:text-sm text-gray-500 mt-1 max-w-sm mx-auto">
                            Belum ada jadwal peminjaman ruangan yang tercatat sesuai filter yang dipilih saat ini.
                        </p>
                        <div class="mt-5 flex items-center justify-center gap-3">
                            @if ($activeFiltersCount > 0 || (request('date') && request('date') !== today()->toDateString()))
                                <a href="{{ route('room-reservations.index', request()->has('room_id') ? ['room_id' => request('room_id')] : []) }}"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-white hover:bg-gray-50 border border-gray-200 text-gray-700 font-semibold text-xs rounded-full shadow-2xs transition-all">
                                    <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                                    <span>Reset Filter</span>
                                </a>
                            @endif
                            @if ($canManage)
                                <a href="{{ route('room-reservations.create') }}"
                                    class="inline-flex items-center gap-2 px-5 py-2 bg-gradient-to-r from-[#FF4D00] to-amber-500 hover:from-[#e04400] hover:to-[#d97706] text-white font-bold text-xs rounded-full shadow-xs transition-all">
                                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                                    <span>Buat Peminjaman Baru</span>
                                </a>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- Delete Confirmation Modal (macOS Sheet Dialog) --}}
    <div id="deleteModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/40 backdrop-blur-md">
        <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full border border-gray-200 overflow-hidden transform transition-all scale-95 opacity-0"
            id="deleteModalContent">
            <div class="p-6 space-y-4">
                <div class="flex items-start gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-rose-50 border border-rose-200/80 text-rose-600 flex items-center justify-center flex-shrink-0 shadow-2xs">
                        <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900 tracking-tight">Hapus Peminjaman Ruang</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Tindakan ini tidak dapat dibatalkan. Jadwal peminjaman akan dihapus dari sistem.</p>
                    </div>
                </div>

                <div class="p-3.5 bg-gray-50 rounded-2xl border border-gray-200/80 text-xs text-gray-700 font-medium">
                    <span class="text-gray-400 block text-[10px] uppercase font-bold tracking-wider mb-0.5">Nama Kegiatan:</span>
                    <span id="deleteEventTitle" class="font-bold text-gray-900 break-words"></span>
                </div>

                <form id="deleteForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <div class="flex items-center gap-3 pt-2">
                        <button type="button" onclick="closeDeleteModal()"
                            class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200/80 text-gray-700 font-semibold text-xs rounded-xl transition-all border border-gray-200 cursor-pointer">
                            <span>Batal</span>
                        </button>
                        <button type="submit"
                            class="flex-1 px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-xl transition-all shadow-xs cursor-pointer">
                            <span>Hapus Permanen</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

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
                        showToast('Gagal menyalin kontak.', 'error');
                    }
                } catch (e) {
                    showToast('Gagal menyalin kontak.', 'error');
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

            // Delete Modal Manager
            function openDeleteModal(actionUrl, eventTitle) {
                const modal = document.getElementById('deleteModal');
                const content = document.getElementById('deleteModalContent');
                const form = document.getElementById('deleteForm');
                const titleEl = document.getElementById('deleteEventTitle');

                if (!modal || !content || !form) return;

                form.action = actionUrl;
                titleEl.textContent = eventTitle || 'Peminjaman Ruang';

                modal.classList.remove('hidden');
                setTimeout(() => {
                    content.classList.remove('scale-95', 'opacity-0');
                    content.classList.add('scale-100', 'opacity-100');
                }, 10);
                lucide.createIcons();
            }

            function closeDeleteModal() {
                const modal = document.getElementById('deleteModal');
                const content = document.getElementById('deleteModalContent');
                if (!modal || !content) return;

                content.classList.remove('scale-100', 'opacity-100');
                content.classList.add('scale-95', 'opacity-0');
                setTimeout(() => {
                    modal.classList.add('hidden');
                }, 200);
            }

            // Room Tabs Scroll & Drag Manager
            function scrollRoomTabs(direction) {
                const nav = document.getElementById('room-tab-nav');
                if (!nav) return;
                const scrollAmount = 280;
                nav.scrollBy({
                    left: direction === 'left' ? -scrollAmount : scrollAmount,
                    behavior: 'smooth'
                });
            }

            function updateTabScrollState() {
                const nav = document.getElementById('room-tab-nav');
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

            function initTabDragToScroll() {
                const nav = document.getElementById('room-tab-nav');
                if (!nav) return;

                let isDown = false;
                let startX = 0;
                let scrollStart = 0;
                let isDragging = false;

                nav.addEventListener('mousedown', (e) => {
                    if (e.button !== 0) return; // Only main left click
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

                // Prevent link navigation if user was dragging
                nav.querySelectorAll('a').forEach(link => {
                    link.addEventListener('click', (e) => {
                        if (isDragging) {
                            e.preventDefault();
                            e.stopPropagation();
                        }
                    });
                });

                // Update arrows on scroll & resize
                nav.addEventListener('scroll', updateTabScrollState, { passive: true });
                window.addEventListener('resize', updateTabScrollState);

                // Auto-scroll active tab into view
                const activeTab = nav.querySelector('.mac-tab-active');
                if (activeTab) {
                    activeTab.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
                }

                // Initial arrow state
                setTimeout(updateTabScrollState, 50);
            }

            // On Page Ready
            document.addEventListener('DOMContentLoaded', function () {
                initTabDragToScroll();

                @if ($activeFiltersCount > 0)
                    const content = document.getElementById('filter-content');
                    const icon = document.getElementById('filter-icon');
                    if (content && icon) {
                        content.classList.remove('hidden');
                        icon.style.transform = 'rotate(180deg)';
                    }
                @endif

                if (typeof flatpickr !== 'undefined') {
                    flatpickr("#date", {
                        dateFormat: "Y-m-d",
                        altInput: true,
                        altFormat: "d M Y",
                        locale: "id",
                        placeholder: "Pilih tanggal..."
                    });
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
