@extends('layouts.app')

@section('title', 'Dashboard Peminjaman Ruang - FEB UNJ')
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
            50% { opacity: 0.4; transform: scale(1.25); }
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

        /* Apple Modal Sheet Transition */
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

        /* FullCalendar Apple Styling */
        .fc {
            --fc-page-bg-color: #ffffff;
            --fc-neutral-bg-color: #f8fafc;
            --fc-neutral-text-color: #64748b;
            --fc-border-color: #f1f5f9;
            --fc-button-text-color: #1e293b;
            --fc-button-bg-color: #ffffff;
            --fc-button-border-color: #e2e8f0;
            --fc-button-hover-bg-color: #f8fafc;
            --fc-button-hover-border-color: #cbd5e1;
            --fc-button-active-bg-color: #f1f5f9;
            --fc-button-active-border-color: #cbd5e1;
            --fc-event-bg-color: #ea580c;
            --fc-event-border-color: #c2410c;
            --fc-event-text-color: #ffffff;
            --fc-today-bg-color: rgba(255, 77, 0, 0.04);
        }

        .fc .fc-toolbar {
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-bottom: 1.25rem !important;
        }

        .fc .fc-toolbar-title {
            font-size: 1.15rem !important;
            font-weight: 800 !important;
            color: #0f172a;
            letter-spacing: -0.025em;
        }

        .fc .fc-button {
            border-radius: 0.75rem;
            padding: 0.45rem 0.9rem;
            font-size: 0.8rem;
            font-weight: 700;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.04);
            transition: all 0.15s ease;
        }

        .fc .fc-button-primary:not(:disabled):hover {
            background-color: #fff7ed !important;
            border-color: #fed7aa !important;
            color: #c2410c !important;
        }

        .fc .fc-button-primary:not(:disabled).fc-button-active, 
        .fc .fc-button-primary:not(:disabled):active {
            background-color: #ffedd5 !important;
            border-color: #fed7aa !important;
            color: #ea580c !important;
        }

        .fc-theme-standard td, .fc-theme-standard th {
            border-color: #f1f5f9;
        }

        .fc-theme-standard .fc-scrollgrid {
            border-color: #f1f5f9;
            border-radius: 1rem;
            overflow: hidden;
            border: 1px solid #f1f5f9;
        }

        .fc .fc-col-header-cell-cushion {
            color: #64748b;
            font-weight: 700;
            font-size: 0.75rem;
            padding: 10px 6px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            text-decoration: none !important;
        }

        .fc .fc-col-header-cell {
            background-color: #f8fafc;
        }

        .fc .fc-daygrid-day-number {
            color: #475569;
            font-weight: 700;
            font-size: 0.8rem;
            padding: 4px;
            text-decoration: none !important;
            width: 1.75rem;
            height: 1.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 4px;
            border-radius: 9999px;
            transition: all 0.15s ease;
        }

        .fc .fc-daygrid-day:hover .fc-daygrid-day-number {
            background-color: #f1f5f9;
            color: #0f172a;
        }

        .fc .fc-daygrid-day.fc-day-today .fc-daygrid-day-number {
            background: linear-gradient(135deg, #FF4D00, #f97316);
            color: #ffffff;
            box-shadow: 0 2px 6px rgba(255, 77, 0, 0.3);
        }

        .fc .fc-daygrid-day:hover {
            background-color: #fafaf9;
            cursor: pointer;
        }

        .fc .fc-event {
            border-radius: 8px;
            padding: 2.5px 6px;
            font-size: 0.72rem;
            font-weight: 700;
            border: none;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            transition: all 0.2s ease;
            margin-bottom: 2px !important;
        }

        .fc .fc-event:hover {
            transform: translateY(-1px) scale(1.02);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            cursor: pointer;
            z-index: 10;
        }

        .fc .fc-daygrid-more-link {
            font-weight: 700;
            font-size: 0.72rem;
            color: #ea580c;
            background-color: #fff7ed;
            border: 1px solid #fed7aa;
            padding: 2px 6px;
            border-radius: 6px;
            display: inline-block;
            margin: 2px 4px;
            text-decoration: none !important;
        }

        .fc .fc-daygrid-more-link:hover {
            background-color: #ffedd5;
            color: #c2410c;
        }

        /* Custom Scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f8fafc;
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
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

    {{-- ── 0. macOS Top Header Toolbar (Consistent with Legalisir & Master Views) ── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-gray-200/80">
        <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-[#FF4D00] to-amber-500 flex items-center justify-center text-white shadow-xs flex-shrink-0">
                <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
            </div>
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">Dashboard Peminjaman Ruang</h1>
                <p class="text-xs sm:text-sm text-gray-500">Pusat monitoring jadwal peminjaman ruang rapat & sidang, agenda kegiatan, serta ketersediaan fasilitas FEB UNJ</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            {{-- Public Display Timegrid --}}
            <a href="{{ route('room-reservations.public-display.timegrid') }}" target="_blank"
                class="inline-flex items-center gap-2 px-3.5 py-2 text-xs sm:text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-200 rounded-full shadow-2xs hover:shadow-xs transition-all active:scale-[0.98]">
                <i data-lucide="monitor" class="w-4 h-4 text-blue-500"></i>
                <span>Public Display</span>
            </a>

            {{-- Catalog / List --}}
            <a href="{{ route('room-reservations.index') }}"
                class="inline-flex items-center gap-2 px-3.5 py-2 text-xs sm:text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-200 rounded-full shadow-2xs hover:shadow-xs transition-all active:scale-[0.98]">
                <i data-lucide="list" class="w-4 h-4 text-gray-500"></i>
                <span>Daftar Peminjaman</span>
            </a>

            {{-- Create Button --}}
            <a href="{{ route('room-reservations.create') }}"
                class="inline-flex items-center gap-2 px-4.5 py-2 text-xs sm:text-sm font-bold text-white bg-gradient-to-r from-[#FF4D00] to-amber-500 hover:from-[#e04400] hover:to-[#d97706] rounded-full shadow-xs hover:shadow-md transition-all active:scale-[0.98]">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Buat Peminjaman</span>
            </a>
        </div>
    </div>

    {{-- ── 1. Apple Bento Grid: 4 Key Statistics Cards ── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-5">
        {{-- Card 1: Hari Ini (Orange) --}}
        <div class="mac-card p-5 group cursor-default">
            <div class="flex items-center justify-between mb-3.5">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-orange-500 to-amber-500 flex items-center justify-center text-white shadow-xs group-hover:scale-105 transition-transform duration-200">
                    <i data-lucide="calendar-check" class="w-5 h-5"></i>
                </div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-orange-50 text-orange-700 border border-orange-200/70">
                    Hari Ini
                </span>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Peminjaman Hari Ini</p>
                <h3 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight mt-1">
                    {{ number_format($totalToday, 0, ',', '.') }}
                </h3>
                <p class="text-[11px] text-gray-500 mt-2 flex items-center justify-between">
                    <span class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 live-dot"></span>
                        <span>Agenda Terjadwal:</span>
                    </span>
                    <span class="font-bold text-orange-700 bg-orange-50 px-2 py-0.5 rounded-md border border-orange-200/70 font-mono">
                        {{ $todayReservations->count() }} Kegiatan
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
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Bulan Ini</p>
                <h3 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight mt-1">
                    {{ number_format($totalThisMonth, 0, ',', '.') }}
                </h3>
                <p class="text-[11px] text-gray-500 mt-2 flex items-center justify-between">
                    <span>Aktivitas Bulanan:</span>
                    <span class="font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200/70 font-mono">
                        {{ $totalAll > 0 ? round(($totalThisMonth / $totalAll) * 100, 1) : 0 }}% total arsip
                    </span>
                </p>
            </div>
        </div>

        {{-- Card 3: Total Ruangan Aktif (Azure Blue) --}}
        <div class="mac-card p-5 group cursor-default">
            <div class="flex items-center justify-between mb-3.5">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-500 flex items-center justify-center text-white shadow-xs group-hover:scale-105 transition-transform duration-200">
                    <i data-lucide="door-open" class="w-5 h-5"></i>
                </div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200/70">
                    Fasilitas FEB
                </span>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Ruangan</p>
                <h3 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight mt-1">
                    {{ number_format($totalRooms, 0, ',', '.') }}
                </h3>
                <p class="text-[11px] text-gray-500 mt-2 flex items-center justify-between">
                    <span>Status Fasilitas:</span>
                    <span class="font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-md border border-blue-200/70 font-mono">
                        Siap Digunakan
                    </span>
                </p>
            </div>
        </div>

        {{-- Card 4: Total Seluruh Agenda (Purple) --}}
        <div class="mac-card p-5 group cursor-default">
            <div class="flex items-center justify-between mb-3.5">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-purple-500 to-violet-600 flex items-center justify-center text-white shadow-xs group-hover:scale-105 transition-transform duration-200">
                    <i data-lucide="layers" class="w-5 h-5"></i>
                </div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-purple-50 text-purple-700 border border-purple-200/70">
                    Akumulasi
                </span>
            </div>
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Permohonan</p>
                <h3 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight mt-1">
                    {{ number_format($totalAll, 0, ',', '.') }}
                </h3>
                <p class="text-[11px] text-gray-500 mt-2 flex items-center justify-between">
                    <span>Mendatang:</span>
                    <span class="font-bold text-purple-700 bg-purple-50 px-2 py-0.5 rounded-md border border-purple-200/70 font-mono">
                        {{ number_format($totalUpcoming, 0, ',', '.') }} agenda
                    </span>
                </p>
            </div>
        </div>
    </div>

    {{-- ── 2. Bento Section: Kalender Interaktif & Agenda Hari Ini ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        {{-- Left: Kalender Penjadwalan Ruang (8 Cols) --}}
        <div class="lg:col-span-8 mac-card p-5 sm:p-6 flex flex-col justify-between">
            <div>
                {{-- Calendar Header --}}
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-3.5 mb-4 border-b border-gray-100 gap-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-orange-50 border border-orange-200 flex items-center justify-center text-orange-600 shadow-2xs">
                            <i data-lucide="calendar-days" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-gray-900">Kalender Penjadwalan Ruang</h3>
                            <p class="text-[11px] text-gray-500">Jadwal pemakaian ruang rapat & sidang FEB UNJ</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 self-start sm:self-auto">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-gray-50 text-gray-600 border border-gray-200/70">
                            <i data-lucide="info" class="w-3.5 h-3.5 text-orange-500"></i>
                            <span>Klik agenda / tanggal untuk detail</span>
                        </span>
                    </div>
                </div>

                {{-- Room Legend Chips --}}
                @if (isset($rooms) && $rooms->count() > 0)
                    <div class="flex flex-wrap items-center gap-1.5 mb-4 pb-3 border-b border-gray-100 text-xs">
                        <span class="text-gray-400 font-semibold text-[11px] mr-1 flex items-center gap-1">
                            <i data-lucide="palette" class="w-3 h-3 text-gray-400"></i>
                            Ruangan:
                        </span>
                        @foreach ($rooms as $room)
                            @php
                                $rColor = $roomColors[$room->id] ?? '#ea580c';
                            @endphp
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg text-[11px] font-semibold bg-gray-50 border border-gray-200/80 text-gray-700">
                                <span class="w-2 h-2 rounded-full" style="background-color: {{ $rColor }};"></span>
                                <span class="truncate max-w-[120px]">{{ $room->name }}</span>
                            </span>
                        @endforeach
                    </div>
                @endif

                {{-- Calendar Container --}}
                <div class="p-1">
                    <div id="calendar" class="min-h-[480px]"></div>
                </div>
            </div>
        </div>

        {{-- Right: Side Bento Stack (Agenda Hari Ini & Ruangan Terpopuler) (4 Cols) --}}
        <div class="lg:col-span-4 space-y-6">

            {{-- 🌟 Card A: Agenda Hari Ini --}}
            <div class="mac-card p-5 sm:p-6">
                <div class="flex items-center justify-between pb-3.5 mb-4 border-b border-gray-100">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-orange-50 border border-orange-200 flex items-center justify-center text-orange-600 shadow-2xs">
                            <i data-lucide="clock" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-gray-900">Agenda Hari Ini</h3>
                            <p class="text-[11px] text-gray-500">{{ now()->translatedFormat('l, d F Y') }}</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-orange-50 text-orange-800 border border-orange-200/80">
                        <span class="w-1.5 h-1.5 rounded-full bg-orange-500 live-dot"></span>
                        Live
                    </span>
                </div>

                @if ($todayReservations->count() > 0)
                    <div class="space-y-3 max-h-[360px] overflow-y-auto pr-1 custom-scrollbar">
                        @foreach ($todayReservations as $todayRes)
                            @php
                                $rColor = $roomColors[$todayRes->room_id] ?? '#ea580c';
                            @endphp
                            <div onclick="openEventDetailById({{ $todayRes->id }})"
                                class="p-3 rounded-2xl bg-gray-50/70 border border-gray-100 hover:border-orange-200 hover:bg-orange-50/20 transition-all cursor-pointer group">
                                <div class="flex items-center justify-between mb-1.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md font-mono text-[11px] font-bold bg-white text-orange-700 border border-orange-200 shadow-2xs">
                                        {{ \Carbon\Carbon::parse($todayRes->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($todayRes->end_time)->format('H:i') }}
                                    </span>
                                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold px-2 py-0.5 rounded-md bg-white border border-gray-200 text-gray-700">
                                        <span class="w-2 h-2 rounded-full" style="background-color: {{ $rColor }};"></span>
                                        <span class="truncate max-w-[100px]">{{ $todayRes->room->name ?? 'Ruangan' }}</span>
                                    </span>
                                </div>
                                <h4 class="text-xs font-bold text-gray-900 group-hover:text-orange-600 transition-colors line-clamp-1">
                                    {{ $todayRes->event_name }}
                                </h4>
                                <div class="mt-2 flex items-center justify-between text-[11px] text-gray-500 pt-1.5 border-t border-gray-100">
                                    <span class="truncate flex items-center gap-1">
                                        <i data-lucide="user" class="w-3 h-3 text-gray-400"></i>
                                        <span>{{ $todayRes->requester_name }}</span>
                                    </span>
                                    <span class="text-orange-600 font-bold group-hover:translate-x-0.5 transition-transform flex items-center gap-0.5">
                                        <span>Detail</span>
                                        <i data-lucide="chevron-right" class="w-3 h-3"></i>
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-7 px-4 rounded-2xl bg-gray-50/50 border border-dashed border-gray-200">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-2.5">
                            <i data-lucide="check-circle-2" class="w-5 h-5"></i>
                        </div>
                        <h4 class="text-xs font-bold text-gray-800">Semua Ruangan Tersedia</h4>
                        <p class="text-[11px] text-gray-500 mt-1 max-w-xs mx-auto">Tidak ada jadwal peminjaman ruang yang aktif untuk hari ini.</p>
                        <div class="mt-3">
                            <a href="{{ route('room-reservations.create') }}"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-white bg-gradient-to-r from-[#FF4D00] to-amber-500 rounded-full shadow-2xs hover:shadow-xs transition-all active:scale-[0.98]">
                                <i data-lucide="plus" class="w-3 h-3"></i>
                                <span>Buat Peminjaman</span>
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            {{-- 🌟 Card B: Ruangan Terpopuler --}}
            <div class="mac-card p-5 sm:p-6">
                <div class="flex items-center justify-between pb-3.5 mb-4 border-b border-gray-100">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-orange-50 border border-orange-200 flex items-center justify-center text-orange-600 shadow-2xs">
                            <i data-lucide="sparkles" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-gray-900">Ruangan Terpopuler</h3>
                            <p class="text-[11px] text-gray-500">Frekuensi pemakaian ruang tertinggi</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10.5px] font-semibold bg-gray-100 text-gray-600">
                        Top 5
                    </span>
                </div>

                @if (isset($topRooms) && $topRooms->count() > 0)
                    @php
                        $maxReservations = max(1, $topRooms->max('reservations_count'));
                    @endphp
                    <div class="space-y-3">
                        @foreach ($topRooms as $topRoom)
                            @php
                                $pct = round(($topRoom->reservations_count / $maxReservations) * 100);
                                $rColor = $roomColors[$topRoom->id] ?? '#ea580c';
                            @endphp
                            <div class="space-y-1">
                                <div class="flex items-center justify-between text-xs">
                                    <div class="flex items-center gap-1.5 min-w-0">
                                        <span class="w-2 h-2 rounded-full flex-shrink-0" style="background-color: {{ $rColor }};"></span>
                                        <span class="font-bold text-gray-800 truncate">{{ $topRoom->name }}</span>
                                    </div>
                                    <span class="font-mono font-bold text-gray-900 bg-gray-100 px-2 py-0.5 rounded-md text-[11px]">
                                        {{ $topRoom->reservations_count }}x Dipinjam
                                    </span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden shadow-inner">
                                    <div class="h-full rounded-full transition-all duration-500"
                                         style="width: {{ $pct }}%; background-color: {{ $rColor }};"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6">
                        <p class="text-xs text-gray-400">Belum ada data peminjaman ruangan.</p>
                    </div>
                @endif
            </div>

        </div>

    </div>

    {{-- ── 3. Main Activity Table: Aktivitas Peminjaman Terbaru ── --}}
    <div class="mac-card p-5 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-3.5 mb-4 border-b border-gray-100 gap-2">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-orange-50 border border-orange-200 flex items-center justify-center text-orange-600 shadow-2xs">
                    <i data-lucide="activity" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-gray-900">Aktivitas Peminjaman Terbaru</h3>
                    <p class="text-[11px] text-gray-500">Pencatatan permohonan reservasi ruangan terkini dalam sistem</p>
                </div>
            </div>
            <a href="{{ route('room-reservations.index') }}"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold text-orange-600 hover:text-orange-700 bg-orange-50 hover:bg-orange-100/80 border border-orange-200/80 transition-colors self-start sm:self-auto">
                <span>Buka Semua Daftar</span>
                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
            </a>
        </div>

        <div class="overflow-x-auto">
            @if ($recentReservations->count() > 0)
                <table class="min-w-full divide-y divide-gray-100 text-xs text-left">
                    <thead>
                        <tr class="bg-gray-50/60 border-b border-gray-100">
                            <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider w-36">Jadwal & Waktu</th>
                            <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider min-w-[160px]">Ruangan</th>
                            <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider min-w-[220px]">Nama Kegiatan</th>
                            <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider min-w-[180px]">Penanggung Jawab</th>
                            <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider">Pembuat</th>
                            <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider text-center w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @foreach ($recentReservations as $reservation)
                            @php
                                $rColor = $roomColors[$reservation->room_id] ?? '#ea580c';
                            @endphp
                            <tr class="hover:bg-orange-50/20 transition-colors group">
                                {{-- Tanggal & Waktu --}}
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <div class="font-bold text-gray-900 font-mono text-[11.5px]">
                                        {{ \Carbon\Carbon::parse($reservation->reservation_date)->format('d/m/Y') }}
                                    </div>
                                    <div class="text-[11px] text-orange-600 font-mono font-semibold mt-0.5">
                                        {{ \Carbon\Carbon::parse($reservation->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($reservation->end_time)->format('H:i') }} WIB
                                    </div>
                                </td>

                                {{-- Ruangan --}}
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background-color: {{ $rColor }};"></span>
                                        <span class="font-bold text-gray-900 text-xs">
                                            {{ $reservation->room->name ?? 'Ruangan Dihapus' }}
                                        </span>
                                    </div>
                                    @if(isset($reservation->room->capacity) && $reservation->room->capacity > 0)
                                        <p class="text-[10.5px] text-gray-400 pl-4.5 mt-0.5">
                                            Kapasitas {{ $reservation->room->capacity }} orang
                                        </p>
                                    @endif
                                </td>

                                {{-- Nama Kegiatan --}}
                                <td class="px-4 py-3.5">
                                    <p class="font-bold text-gray-900 line-clamp-1 text-xs">
                                        {{ $reservation->event_name }}
                                    </p>
                                    @if($reservation->remarks)
                                        <p class="text-[11px] text-gray-400 line-clamp-1 mt-0.5">
                                            {{ $reservation->remarks }}
                                        </p>
                                    @endif
                                </td>

                                {{-- Penanggung Jawab --}}
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-lg bg-orange-100 text-orange-700 font-bold text-[10px] flex items-center justify-center flex-shrink-0">
                                            {{ strtoupper(substr($reservation->requester_name, 0, 2)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-bold text-gray-800 truncate text-xs">
                                                {{ $reservation->requester_name }}
                                            </p>
                                            @if($reservation->contact_person)
                                                <p class="text-[10.5px] text-gray-400 font-mono">
                                                    {{ $reservation->contact_person }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Pembuat --}}
                                <td class="px-4 py-3.5 whitespace-nowrap text-gray-600 text-xs">
                                    <span class="inline-flex items-center gap-1 font-medium">
                                        <i data-lucide="shield-check" class="w-3.5 h-3.5 text-blue-500"></i>
                                        {{ $reservation->creator->name ?? 'Sistem' }}
                                    </span>
                                </td>

                                {{-- Detail Aksi --}}
                                <td class="px-4 py-3.5 whitespace-nowrap text-center">
                                    <a href="{{ route('room-reservations.show', $reservation) }}"
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
                        <i data-lucide="calendar-x" class="w-6 h-6"></i>
                    </div>
                    <h4 class="text-sm font-semibold text-gray-800">Belum ada aktivitas peminjaman</h4>
                    <p class="text-xs text-gray-400 mt-1 max-w-sm mx-auto">Data reservasi ruangan yang didaftarkan akan otomatis terakumulasi dalam tabel ini.</p>
                    <div class="mt-4">
                        <a href="{{ route('room-reservations.create') }}"
                            class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold text-white bg-gradient-to-r from-[#FF4D00] to-amber-500 rounded-full shadow-xs hover:shadow-md transition-all active:scale-[0.98]">
                            <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                            <span>Buat Peminjaman Pertama</span>
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- ── 4. Apple Sheet Modal: Rincian Peminjaman Ruang ── --}}
<div id="reservationDetailModalBackdrop" class="mac-modal-backdrop fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/40 backdrop-blur-xs">
    <div class="mac-modal-content bg-white rounded-3xl p-6 sm:p-7 text-left shadow-2xl border border-gray-100 sm:max-w-md w-full">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-2xl bg-orange-100 text-orange-600 flex items-center justify-center">
                    <i data-lucide="calendar-clock" class="w-5 h-5"></i>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-orange-600">Rincian Reservasi</span>
                    <h3 id="modalEventName" class="text-base font-bold text-gray-900 line-clamp-1 leading-snug">
                        Nama Kegiatan
                    </h3>
                </div>
            </div>
            <button type="button" onclick="closeReservationModal()" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 flex items-center justify-center transition-colors cursor-pointer">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <div class="space-y-3 text-xs">
            {{-- Ruangan --}}
            <div class="flex items-center justify-between py-1.5 px-3 rounded-xl bg-gray-50/80 border border-gray-100">
                <span class="text-gray-500 font-medium flex items-center gap-1.5">
                    <i data-lucide="door-open" class="w-3.5 h-3.5 text-blue-500"></i>
                    Ruangan
                </span>
                <span id="modalRoomName" class="font-bold text-gray-900">
                    -
                </span>
            </div>

            {{-- Waktu & Tanggal --}}
            <div class="flex items-center justify-between py-1.5 px-3 rounded-xl bg-gray-50/80 border border-gray-100">
                <span class="text-gray-500 font-medium flex items-center gap-1.5">
                    <i data-lucide="clock" class="w-3.5 h-3.5 text-orange-500"></i>
                    Waktu Pelaksanaan
                </span>
                <span id="modalTimeRange" class="font-mono font-bold text-orange-600">
                    -
                </span>
            </div>

            {{-- Penanggung Jawab --}}
            <div class="flex items-center justify-between py-1.5 px-3 rounded-xl bg-gray-50/80 border border-gray-100">
                <span class="text-gray-500 font-medium flex items-center gap-1.5">
                    <i data-lucide="user" class="w-3.5 h-3.5 text-purple-500"></i>
                    Penanggung Jawab
                </span>
                <span id="modalRequesterName" class="font-bold text-gray-900">
                    -
                </span>
            </div>

            {{-- Kontak --}}
            <div class="flex items-center justify-between py-1.5 px-3 rounded-xl bg-gray-50/80 border border-gray-100">
                <span class="text-gray-500 font-medium flex items-center gap-1.5">
                    <i data-lucide="phone" class="w-3.5 h-3.5 text-emerald-500"></i>
                    Kontak Person
                </span>
                <span id="modalContact" class="font-mono font-medium text-gray-800">
                    -
                </span>
            </div>

            {{-- Fasilitas --}}
            <div id="modalFacilitiesWrap" class="p-3 rounded-xl bg-gray-50/80 border border-gray-100 space-y-1">
                <span class="text-gray-500 font-medium flex items-center gap-1.5 text-[11px]">
                    <i data-lucide="box" class="w-3.5 h-3.5 text-amber-500"></i>
                    Kebutuhan Fasilitas
                </span>
                <p id="modalFacilities" class="text-gray-700 text-xs pl-5 font-medium leading-relaxed">-</p>
            </div>

            {{-- Dibuat Oleh --}}
            <div class="flex items-center justify-between text-[11px] text-gray-400 pt-1">
                <span>Pencatat / Operator:</span>
                <span id="modalCreatorName" class="font-medium text-gray-600">-</span>
            </div>
        </div>

        <div class="mt-6 flex items-center justify-end gap-2.5 pt-3 border-t border-gray-100">
            <button type="button" onclick="closeReservationModal()"
                class="px-4 py-2 text-xs sm:text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-all cursor-pointer">
                Tutup
            </button>
            <a id="modalDetailBtn" href="#"
                class="px-4.5 py-2 text-xs sm:text-sm font-bold text-white bg-gradient-to-r from-[#FF4D00] to-amber-500 rounded-xl shadow-xs transition-all active:scale-[0.98]">
                Buka Halaman Lengkap
            </a>
        </div>
    </div>
</div>

{{-- ── 5. Apple Sheet Modal: Agenda Tanggal Tertentu ── --}}
<div id="dateAgendaModalBackdrop" class="mac-modal-backdrop fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/40 backdrop-blur-xs">
    <div class="mac-modal-content bg-white rounded-3xl p-6 sm:p-7 text-left shadow-2xl border border-gray-100 sm:max-w-lg w-full">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-2xl bg-orange-100 text-orange-600 flex items-center justify-center">
                    <i data-lucide="calendar" class="w-5 h-5"></i>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-orange-600">Agenda Tanggal</span>
                    <h3 id="dateModalTitle" class="text-base font-bold text-gray-900">
                        Tanggal
                    </h3>
                </div>
            </div>
            <button type="button" onclick="closeDateModal()" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 flex items-center justify-center transition-colors cursor-pointer">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        {{-- Dynamic Events Container --}}
        <div id="dateModalEventsList" class="space-y-3 max-h-[320px] overflow-y-auto pr-1 custom-scrollbar text-xs">
            {{-- Injected dynamically --}}
        </div>

        <div class="mt-6 flex items-center justify-between pt-3 border-t border-gray-100">
            <button type="button" onclick="closeDateModal()"
                class="px-4 py-2 text-xs sm:text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-all cursor-pointer">
                Tutup
            </button>
            <a id="dateModalCreateBtn" href="{{ route('room-reservations.create') }}"
                class="inline-flex items-center gap-1.5 px-4.5 py-2 text-xs sm:text-sm font-bold text-white bg-gradient-to-r from-[#FF4D00] to-amber-500 rounded-xl shadow-xs transition-all active:scale-[0.98]">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Buat Peminjaman di Tanggal Ini</span>
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/lucide@latest"></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
<script>
    const calendarEvents = {!! json_encode($calendarEvents) !!};

    document.addEventListener('DOMContentLoaded', function() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        const calendarEl = document.getElementById('calendar');

        if (calendarEl) {
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'id',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth'
                },
                buttonText: {
                    today: 'Hari Ini',
                    month: 'Bulan'
                },
                events: calendarEvents,
                dayMaxEvents: 2,
                eventTimeFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    meridiem: false,
                    hour12: false
                },
                eventClick: function(info) {
                    info.jsEvent.preventDefault();
                    openReservationModal(info.event.extendedProps);
                },
                dateClick: function(info) {
                    const clickedDate = info.dateStr;
                    handleDateClick(clickedDate);
                },
                themeSystem: 'standard',
                height: 560
            });

            calendar.render();
        }

        @if(session('success'))
            showToast("{{ session('success') }}", 'success');
        @endif
        @if(session('error'))
            showToast("{{ session('error') }}", 'error');
        @endif
    });

    // Open Reservation Detail Modal
    function openReservationModal(props) {
        const backdrop = document.getElementById('reservationDetailModalBackdrop');
        if (!backdrop) return;

        document.getElementById('modalEventName').textContent = props.event_name || 'Tanpa Nama';
        document.getElementById('modalRoomName').textContent = props.room_name || '-';
        document.getElementById('modalTimeRange').textContent = props.time_range || '-';
        document.getElementById('modalRequesterName').textContent = props.requester_name || '-';
        document.getElementById('modalContact').textContent = props.contact_person || '-';
        document.getElementById('modalFacilities').textContent = props.facilities && props.facilities !== '-' ? props.facilities : 'Tidak ada fasilitas khusus yang diminta';
        document.getElementById('modalCreatorName').textContent = props.creator_name || 'Sistem';

        const detailBtn = document.getElementById('modalDetailBtn');
        if (detailBtn && props.detail_url) {
            detailBtn.href = props.detail_url;
        }

        backdrop.classList.add('show');
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function closeReservationModal() {
        const backdrop = document.getElementById('reservationDetailModalBackdrop');
        if (backdrop) {
            backdrop.classList.remove('show');
        }
    }

    // Open by ID from list
    function openEventDetailById(id) {
        const match = calendarEvents.find(e => e.id == id);
        if (match && match.extendedProps) {
            openReservationModal(match.extendedProps);
        }
    }

    // Handle Calendar Date Click
    function handleDateClick(clickedDateStr) {
        const backdrop = document.getElementById('dateAgendaModalBackdrop');
        const titleEl = document.getElementById('dateModalTitle');
        const listEl = document.getElementById('dateModalEventsList');
        const createBtn = document.getElementById('dateModalCreateBtn');

        const eventsOnDate = calendarEvents.filter(e => e.start.startsWith(clickedDateStr));
        const dateObj = new Date(clickedDateStr);
        const formattedDate = dateObj.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });

        titleEl.textContent = formattedDate;
        createBtn.href = `{{ route('room-reservations.create') }}?reservation_date=${clickedDateStr}`;

        if (eventsOnDate.length === 0) {
            listEl.innerHTML = `
                <div class="text-center py-8">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-2.5">
                        <i data-lucide="check-circle-2" class="w-6 h-6"></i>
                    </div>
                    <h4 class="text-sm font-bold text-gray-800">Semua Ruangan Kosong</h4>
                    <p class="text-xs text-gray-500 mt-1 max-w-xs mx-auto">Tidak ada jadwal peminjaman ruang pada ${formattedDate}. Fasilitas siap digunakan.</p>
                </div>
            `;
        } else {
            let html = '<div class="space-y-2.5">';
            eventsOnDate.forEach(ev => {
                const ep = ev.extendedProps;
                html += `
                    <div onclick="openEventDetailById(${ev.id})"
                        class="p-3 rounded-2xl bg-gray-50 hover:bg-orange-50/40 border border-gray-200/80 hover:border-orange-200 transition-all cursor-pointer group">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md font-mono text-[11px] font-bold bg-white text-orange-700 border border-orange-200 shadow-2xs">
                                ${ep.time_range}
                            </span>
                            <span class="font-bold text-xs text-gray-800 truncate max-w-[150px]">
                                ${ep.room_name}
                            </span>
                        </div>
                        <h4 class="text-xs font-bold text-gray-900 group-hover:text-orange-600 transition-colors line-clamp-1">
                            ${ep.event_name}
                        </h4>
                        <div class="mt-1.5 flex items-center justify-between text-[11px] text-gray-400">
                            <span>PJ: ${ep.requester_name}</span>
                            <span class="text-orange-600 font-bold flex items-center gap-0.5">
                                Detail <i data-lucide="chevron-right" class="w-3 h-3"></i>
                            </span>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            listEl.innerHTML = html;
        }

        backdrop.classList.add('show');
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function closeDateModal() {
        const backdrop = document.getElementById('dateAgendaModalBackdrop');
        if (backdrop) {
            backdrop.classList.remove('show');
        }
    }

    // Modal click outside to dismiss
    document.addEventListener('click', function(e) {
        const modalBackdrop = document.getElementById('reservationDetailModalBackdrop');
        if (modalBackdrop && e.target === modalBackdrop) {
            closeReservationModal();
        }
        const dateBackdrop = document.getElementById('dateAgendaModalBackdrop');
        if (dateBackdrop && e.target === dateBackdrop) {
            closeDateModal();
        }
    });

    // Escape key
    window.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeReservationModal();
            closeDateModal();
        }
    });

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
        } else {
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
</script>
@endpush
