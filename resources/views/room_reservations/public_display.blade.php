<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Ruangan - Sistem Informasi Administrasi FEB UNJ</title>
    <!-- Refresh logic is handled via AJAX in JS -->
    <link rel="icon" type="image/png" href="{{ asset('logo-unj.png') }}">

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700;800&family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700;800&display=swap" rel="stylesheet">

    <!-- Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- FullCalendar -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>

    <!-- Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        :root {
            --bg: #f8fafc;
            --panel: #ffffff;
            --hairline: #e2e8f0;
            --text: #0f172a;
            --muted: #64748b;
            --muted-dim: #94a3b8;
            --signal: #f97316;
            --live: #ea580c;
            --live-soft: rgba(234, 88, 12, 0.2);
            --amber: #f59e0b;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            overflow: hidden; 
        }

        .font-display { font-family: 'Space Grotesk', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }

        .glass-panel {
            background: var(--panel);
            border: 1px solid var(--hairline);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
        }

        /* ===== Pulse Animations ===== */
        .pulse-dot { position: relative; }
        .pulse-dot::after {
            content: '';
            position: absolute; inset: -6px;
            border-radius: 9999px;
            border: 2px solid white;
            animation: pulse-ring 2s cubic-bezier(0.4,0,0.6,1) infinite;
        }
        @keyframes pulse-ring {
            0% { transform: scale(0.8); opacity: 0.8; }
            70% { transform: scale(1.8); opacity: 0; }
            100% { opacity: 0; }
        }

        .clock-colon { animation: blink 2s steps(1) infinite; }
        @keyframes blink { 0%, 49% { opacity: 1; } 50%, 100% { opacity: 0.25; } }

        /* ===== Timeline ===== */
        .timeline-rail {
            position: absolute;
            left: 38px;
            top: 24px;
            bottom: 24px;
            width: 2px;
            background: linear-gradient(to bottom, var(--hairline) 90%, transparent);
        }
        .timeline-dot {
            width: 14px; height: 14px;
            border-radius: 9999px;
            border: 3px solid var(--panel);
            box-shadow: 0 0 0 2px var(--hairline);
            z-index: 2;
        }

        .page-fade-enter { animation: pageFadeIn 0.5s ease both; }
        @keyframes pageFadeIn {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .page-dot {
            width: 8px; height: 8px; border-radius: 9999px;
            background: var(--muted-dim);
            transition: all 0.3s ease;
        }
        .page-dot.active {
            width: 24px;
            background: var(--signal);
        }

        /* Hover animation for Next Event card */
        .hover-float {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }

        ::-webkit-scrollbar { display: none; }

        /* ===== Prodless Weekly & Mini Calendar Customization ===== */
        #weekly-calendar .fc-theme-standard td, #weekly-calendar .fc-theme-standard th {
            border-color: #f1f5f9;
        }
        #weekly-calendar .fc-timegrid-slot {
            height: 3rem;
        }
        #weekly-calendar .fc-timegrid-axis {
            font-size: 11px;
            font-weight: 600;
            color: #94a3b8;
            padding-right: 12px;
        }
        #weekly-calendar .fc-col-header-cell {
            padding: 12px 0;
            border-bottom: 2px solid #e2e8f0;
        }
        #weekly-calendar .fc-timegrid-event {
            border: none;
            background: transparent;
            box-shadow: none;
            border-radius: 0;
            margin: 0;
            padding: 1px 4px;
        }
        
        #mini-calendar .fc-header-toolbar {
            margin-bottom: 1rem !important;
        }
        #mini-calendar .fc-toolbar-title {
            font-size: 1.1rem;
            font-weight: 800;
            font-family: 'Space Grotesk', sans-serif;
            color: #1e293b;
        }
        #mini-calendar .fc-col-header-cell {
            padding: 4px 0;
            border: none !important;
            font-size: 0.7rem;
            color: #94a3b8;
        }
        #mini-calendar td, #mini-calendar th, #mini-calendar .fc-scrollgrid {
            border: none !important;
        }
        #mini-calendar .fc-daygrid-day-frame {
            min-height: 40px !important;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        #mini-calendar .fc-daygrid-day-top {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.2s;
        }
        #mini-calendar .fc-daygrid-day-top:hover {
            background-color: #f1f5f9;
        }
        #mini-calendar .active-week-highlight .fc-daygrid-day-top {
            background-color: #ffedd5;
            color: #c2410c;
            font-weight: 800;
        }
        #mini-calendar .fc-day-today .fc-daygrid-day-top {
            background-color: #0f172a;
            color: white;
        }
        #mini-calendar .fc-daygrid-day-events {
            display: none;
        }
        #mini-calendar .fc-daygrid-day-number {
            font-size: 13px;
            font-weight: 600;
            padding: 0;
            margin: 0;
        }

        /* ===== FullCalendar Modern Customization ===== */
    </style>
</head>
<body class="h-screen w-screen flex flex-col relative selection:bg-orange-500/30 selection:text-orange-900">

        <!-- Background Decoration -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none -z-10 bg-slate-50">
        <div class="absolute -top-[25%] -left-[10%] w-[50%] h-[50%] rounded-full bg-orange-100/60 blur-[130px]"></div>
        <div class="absolute top-[20%] -right-[10%] w-[40%] h-[40%] rounded-full bg-amber-100/50 blur-[100px]"></div>
    </div>

    <!-- Header -->
    <header class="border-b border-[color:var(--hairline)] bg-white/80 backdrop-blur-xl shrink-0 shadow-sm relative z-20">
        <div class="px-8 py-4 mx-auto w-full flex items-center justify-between gap-6">
            
            <!-- Left: Brand -->
            <div class="flex items-center gap-4 shrink-0">
                <img src="{{ asset('logo-unj.png') }}" alt="Logo UNJ" class="w-12 h-12 xl:w-14 xl:h-14 object-contain">
                <div class="flex flex-col justify-center">
                    <h1 class="text-2xl xl:text-2xl font-black text-slate-900 font-display uppercase leading-none mb-1">
                        Fakultas Ekonomi & Bisnis
                    </h1>
                    <span class="text-slate-500 font-bold text-[14px] uppercase">
                        Universitas Negeri Jakarta
                    </span>
                </div>
            </div>

            <!-- Right: Clock & Controls -->
            <div class="flex items-center text-right shrink-0 gap-5">
                
                <h2 id="header-title" class="text-xl xl:text-xl font-black text-slate-800 font-display uppercase ml-2">
                    Jadwal Pemakaian Ruangan
                </h2>
                <div class="w-px h-10 bg-[color:var(--hairline)]"></div>
                <!-- Date and Clock Stacked -->
                <div class="flex flex-col items-center justify-center">
                    <div id="digital-date" class="text-[color:var(--text)] font-bold text-sm xl:text-base tracking-wider uppercase flex items-center gap-2 mb-1">
                        <!-- JS injected -->
                    </div>
                    <div id="digital-clock" class="text-[color:var(--text)] font-black text-2xl xl:text-3xl tracking-tight font-mono leading-none">
                        <!-- JS injected -->
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <!-- PORTAL VIEW -->
    <main id="portal-view" class="flex-1 w-full p-6 lg:p-10 mx-auto min-h-0 relative z-10 animate-[pageFadeIn_0.5s_ease_both] overflow-y-auto">
        <div class="max-w-[1920px] mx-auto w-full flex flex-col lg:flex-row gap-10 lg:gap-16 items-start">
            
            <!-- Bagian Kiri: Header Unik (Sticky) -->
            <div class="w-full lg:w-[400px] xl:w-[450px] shrink-0 lg:sticky lg:top-8 flex flex-col relative z-10 group">
                
                <!-- Watermark Background Teks Vertikal -->
                <div class="absolute top-10 -left-12 text-[12rem] xl:text-[14rem] font-black text-slate-200/50 rotate-180 origin-center pointer-events-none tracking-tighter select-none font-display hidden xl:block" style="writing-mode: vertical-rl;">
                    UNJ
                </div>

                <!-- Premium Nested Glass Card (Bezel) -->
                <div class="relative bg-white/40 backdrop-blur-3xl rounded-[3rem] p-3 xl:p-4 shadow-[0_30px_80px_-20px_rgba(0,0,0,0.1)] border border-white/60">
                    
                    <!-- Inner Dark UI Panel -->
                    <div class="relative bg-gradient-to-br from-slate-900 to-slate-950 border border-slate-800 rounded-[2.5rem] p-8 xl:p-10 overflow-hidden text-white shadow-2xl flex flex-col h-full min-h-[400px]">
                        
                        <!-- Ambient Glowing Orbs -->
                        <div class="absolute -right-20 -top-20 w-64 h-64 bg-orange-600 rounded-full blur-[70px] opacity-30 mix-blend-screen animate-pulse"></div>
                        <div class="absolute -left-20 -bottom-20 w-64 h-64 bg-amber-500 rounded-full blur-[70px] opacity-20 mix-blend-screen"></div>
                        
                        <!-- Top Bar -->
                        <div class="flex justify-between items-start mb-14 relative z-10">
                            <div class="w-14 h-14 xl:w-16 xl:h-16 rounded-[1.25rem] bg-white/5 backdrop-blur-md flex items-center justify-center border border-white/10 shadow-xl group-hover:scale-110 group-hover:bg-white/10 transition-all duration-500">
                                <i data-lucide="layout-dashboard" class="w-7 h-7 xl:w-8 xl:h-8 text-orange-400"></i>
                            </div>
                            <div class="px-3 py-1.5 rounded-full bg-slate-800/80 border border-slate-700 text-[10px] font-bold font-mono tracking-widest uppercase flex items-center gap-2 backdrop-blur-md shadow-inner">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse shadow-[0_0_10px_rgba(16,185,129,0.5)]"></span>
                                Live Sync
                            </div>
                        </div>

                        <!-- Huge Typography -->
                        <h1 class="text-6xl xl:text-7xl font-black tracking-tight font-display mb-4 leading-[1.05] relative z-10">
                            Smart<br>
                            <span class="text-transparent bg-clip-text bg-gradient-to-br from-orange-400 to-orange-500">Display.</span>
                        </h1>
                        <p class="text-slate-400 font-medium leading-relaxed mb-8 relative z-10">
                            Ketuk ruangan untuk memantau agenda harian secara eksklusif. Jadwal tersinkronisasi <strong class="text-orange-400">otomatis</strong> secara terpusat.
                        </p>

                        <!-- Search Bar -->
                        <div class="mb-12 relative z-10 w-full">
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                                    <i data-lucide="search" class="w-5 h-5 text-slate-500 group-focus-within:text-orange-400 transition-colors"></i>
                                </div>
                                <input type="text" id="room-search" placeholder="Cari nama ruangan..." class="w-full bg-slate-900/50 backdrop-blur-md border border-slate-700 text-white text-sm rounded-2xl focus:ring-2 focus:ring-orange-500/50 focus:border-orange-500 block pl-12 p-4 shadow-inner transition-all duration-300 placeholder-slate-500 font-medium outline-none hover:border-slate-600">
                            </div>
                        </div>

                        <!-- System Info Footer (Integrated inside card) -->
                        <div class="relative z-10 mt-auto pt-8 border-t border-white/10 flex items-center justify-between">
                            <div>
                                <div class="text-[10px] font-black uppercase tracking-widest text-slate-500 mb-1">Sistem Terintegrasi</div>
                                <div class="font-bold text-base flex items-center gap-1.5 text-white">
                                    FEB UNJ
                                    <i data-lucide="check-circle-2" class="w-4 h-4 text-orange-500"></i>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="text-right">
                                    <div class="text-2xl font-black text-white leading-none font-display">{{ count($rooms) }}</div>
                                    <div class="text-[9px] font-bold uppercase tracking-widest text-orange-400 mt-1">Ruang Aktif</div>
                                </div>
                                <div class="w-10 h-10 rounded-full bg-orange-500/10 flex items-center justify-center border border-orange-500/20 shadow-inner">
                                    <i data-lucide="layout-grid" class="w-4 h-4 text-orange-400"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Bagian Kanan: Grid Ruangan -->
            <div class="flex-1 w-full pb-12 pt-4 xl:pt-8 flex flex-col">

                <div class="flex flex-wrap justify-center gap-6" id="room-grid">
                    @foreach($rooms as $room)
                    <div data-room-name="{{ $room->name }}" onclick="openRoom('{{ encrypt($room->id) }}', '{{ addslashes($room->name) }}')" class="room-card w-full sm:w-[calc(50%-12px)] lg:w-[calc(33.333%-16px)] xl:w-[calc(25%-18px)] 2xl:w-[calc(20%-19.2px)] cursor-pointer group bg-white rounded-[1.5rem] p-6 xl:p-7 border border-slate-200 shadow-sm hover:shadow-xl hover:border-slate-300 transition-all duration-300 hover:-translate-y-1 flex flex-col relative overflow-hidden h-full">
                        
                        <!-- Top Row: Icon & Action -->
                        <div class="flex justify-between items-start mb-5 relative z-10">
                            <div class="w-12 h-12 bg-slate-50 border border-slate-100 rounded-xl flex items-center justify-center transition-all duration-300 group-hover:bg-slate-800 group-hover:border-slate-800 shadow-sm">
                                <i data-lucide="door-open" class="w-6 h-6 text-slate-400 group-hover:text-white transition-colors duration-300"></i>
                            </div>
                            <div class="w-8 h-8 flex items-center justify-center text-slate-300 group-hover:text-slate-800 transition-all duration-300 -rotate-45 group-hover:rotate-0 transform">
                                <i data-lucide="arrow-right" class="w-4 h-4"></i>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="relative z-10 flex-1 flex flex-col">
                            <h3 class="text-xl font-bold text-slate-800 mb-1 leading-tight group-hover:text-slate-900 transition-colors duration-300">{{ $room->name }}</h3>
                            <p class="text-[11px] font-medium text-slate-400 mb-5">Ketuk untuk lihat detail</p>
                            
                            <!-- Bottom Info -->
                            <div class="mt-auto">
                                <div class="inline-flex items-center gap-2 text-xs font-bold text-slate-500">
                                    <i data-lucide="users" class="w-3.5 h-3.5 text-slate-400"></i>
                                    {{ $room->capacity ?? '-' }} Orang
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        </div>
    </main>
    
    <!-- CALENDAR VIEW -->
    <main id="calendar-view-container" class="hidden flex-1 w-full p-6 lg:p-8 mx-auto flex flex-col min-h-0 relative z-10 max-w-[1920px] animate-[pageFadeIn_0.5s_ease_both]">

            <div id="today-view" class="flex-1 flex flex-row gap-8 min-h-0 h-full w-full mx-auto animate-[pageFadeIn_0.5s_ease_both]">

                <!-- ===== Main Stage (Now Playing / Hero Column) ===== -->
                <div class="flex-[1.5] xl:flex-[2] flex flex-col min-h-0 h-full relative" id="main-stage">
                    <!-- JS Injected Content -->
                </div>

                <!-- ===== Sidebar Timeline (Semua Agenda Hari Ini) ===== -->
                <div class="w-[450px] xl:w-[500px] rounded-[3rem] flex flex-col overflow-hidden shrink-0 h-full border-2 border-slate-200 shadow-[0_20px_60px_rgba(0,0,0,0.08)] bg-white/90 backdrop-blur-3xl relative">
                    <!-- Subtle background decoration -->
                    <div class="absolute top-0 right-0 w-64 h-64 bg-orange-100/50 rounded-full blur-[80px] pointer-events-none"></div>
                    <div class="absolute bottom-0 left-0 w-64 h-64 bg-teal-100/40 rounded-full blur-[80px] pointer-events-none"></div>

                    <div class="flex items-center justify-between px-10 pt-10 pb-6 shrink-0 relative z-10">
                        <div class="flex items-center gap-4">
                            <div class="p-3 bg-gradient-to-br from-orange-400 to-orange-600 rounded-2xl shadow-[0_8px_16px_rgba(249,115,22,0.3)] text-white">
                                <i data-lucide="list-tree" class="w-7 h-7"></i>
                            </div>
                            <h3 class="text-2xl font-black text-slate-800 tracking-tight font-display">DAFTAR AGENDA</h3>
                        </div>
                        <div class="flex flex-col items-end">
                            <span id="agenda-count" class="text-sm font-extrabold px-4 py-2 bg-white/90 backdrop-blur-md text-orange-600 rounded-xl font-mono border border-orange-100 shadow-sm">{{ $scheduleData->count() }} ITEM</span>
                        </div>
                    </div>

                    <div class="relative flex-1 overflow-hidden">
                        <div id="timeline-container" class="absolute inset-0 px-10 py-2 flex flex-col">
                            <!-- JS Injected Content -->
                        </div>
                    </div>
                    
                    <!-- Enhanced Footer Widget for Sidebar -->
                    <div class="px-8 py-5 shrink-0 relative z-10 border-t border-slate-100/50 bg-gradient-to-b from-transparent to-slate-50/80">
                        <div class="flex flex-col gap-4">
                            <!-- Page Dots -->
                            <div class="flex justify-center">
                                <div id="page-dots" class="flex gap-2"></div>
                            </div>
                            
                            <!-- Info / Interactive Widget -->
                            <div class="relative p-3.5 rounded-[1.25rem] overflow-hidden bg-gradient-to-br from-slate-800 to-slate-900 border border-slate-700 shadow-md group">
                                <div class="absolute top-0 right-0 w-24 h-24 bg-orange-500/20 rounded-full blur-xl group-hover:bg-orange-500/30 transition-colors pointer-events-none"></div>
                                <div class="relative z-10 flex items-center gap-3">
                                    <div class="w-11 h-11 bg-white/10 rounded-xl flex items-center justify-center shrink-0 border border-white/10 backdrop-blur-md">
                                        <i data-lucide="sparkles" class="w-5 h-5 text-orange-400"></i>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-base font-extrabold text-white leading-tight">Fasilitas Ruangan</span>
                                        <span class="text-sm text-slate-400 font-medium mt-0.5">Harap menjaga kebersihan bersama</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <!-- ===== WEEKLY VIEW (Prodless Style) ===== -->
        <div id="weekly-view" class="hidden flex flex-1 flex-row gap-6 min-h-0 h-full w-full mx-auto animate-[pageFadeIn_0.5s_ease_both]">
            
            <!-- Sidebar (Mini Calendar & Event Details) -->
            <div class="w-[380px] xl:w-[420px] rounded-[2.5rem] bg-white border border-slate-200/60 shadow-[0_8px_30px_rgb(0,0,0,0.04)] flex flex-col p-6 h-full gap-6 shrink-0">
                <!-- Header Title -->
                <div class="px-2 pt-2">
                    <h2 class="text-xl font-bold text-slate-800 tracking-tight flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-slate-900 flex items-center justify-center text-white">
                            <i data-lucide="calendar" class="w-4 h-4"></i>
                        </div>
                        Calendar
                    </h2>
                </div>
                
                <!-- Mini Calendar -->
                <div class="bg-slate-50/50 rounded-3xl p-4 border border-slate-100">
                    <div id="mini-calendar" class="w-full"></div>
                </div>
                
                <!-- Separator -->
                <hr class="border-t-2 border-dashed border-slate-200/60">

                <!-- Event Detail Card -->
                <div class="flex-1 overflow-y-auto">
                    <div class="px-2 pb-2">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-[15px] font-bold text-slate-800">Detail Agenda</h3>
                            <span class="text-[10px] font-bold px-2 py-1 bg-red-50 text-red-600 rounded-full flex items-center gap-1">
                                <i data-lucide="clock" class="w-3 h-3"></i> Terpilih
                            </span>
                        </div>
                        
                        <div id="weekly-event-detail" class="space-y-4">
                            <div class="text-center py-10 text-slate-400">
                                <i data-lucide="mouse-pointer-click" class="w-10 h-10 mx-auto mb-3 opacity-20"></i>
                                <p class="text-sm font-medium">Klik salah satu agenda di kalender mingguan<br>untuk melihat detailnya.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Weekly Calendar -->
            <div class="flex-1 bg-white rounded-[2.5rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-200/60 overflow-hidden flex flex-col p-8 h-full">
                <!-- Custom Toolbar for Weekly -->
                <div class="flex items-center justify-between mb-6 shrink-0">
                    <div class="flex items-center gap-4">
                        <h2 id="weekly-title" class="text-2xl font-extrabold text-slate-800 tracking-tight">Minggu Ini</h2>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="flex items-center bg-slate-50 p-1 rounded-xl border border-slate-200 shadow-sm">
                            <button onclick="weeklyCalendar.prev()" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-500 hover:bg-white hover:shadow-sm hover:text-slate-800 transition-all"><i data-lucide="chevron-left" class="w-4 h-4"></i></button>
                            <button onclick="weeklyCalendar.today()" class="px-3 text-sm font-semibold text-slate-600 hover:text-slate-900 transition-colors">Hari Ini</button>
                            <button onclick="weeklyCalendar.next()" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-500 hover:bg-white hover:shadow-sm hover:text-slate-800 transition-all"><i data-lucide="chevron-right" class="w-4 h-4"></i></button>
                        </div>
                    </div>
                </div>
                
                <div id="weekly-calendar" class="flex-1 w-full min-h-0"></div>
            </div>
        </div>
    </main>

    <footer class="py-2.5 px-8 border-t border-slate-200 shrink-0 bg-white/80 backdrop-blur-md relative z-20 flex items-center justify-between">
        <!-- Tabs & Navigation -->
        <div id="footer-tabs" class="hidden items-center gap-2 w-[450px]">
            <button id="btn-back-to-portal" onclick="closeRoom()" class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs shadow-md transition-colors flex items-center gap-1.5">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Ganti Ruang
            </button>
            <div class="w-px h-5 bg-slate-300 mx-1"></div>
            <button id="tab-today" class="px-3 py-1.5 text-xs font-bold rounded-lg bg-orange-100 text-orange-700 shadow-sm border border-orange-200 transition-colors flex items-center gap-1.5">
                <i data-lucide="layout-dashboard" class="w-3.5 h-3.5"></i> Jadwal Hari Ini
            </button>
            <button id="tab-weekly" class="px-3 py-1.5 text-xs font-bold rounded-lg bg-slate-50 text-slate-500 hover:bg-slate-100 border border-slate-200 shadow-sm transition-colors flex items-center gap-1.5">
                <i data-lucide="calendar-days" class="w-3.5 h-3.5"></i> Kalender
            </button>
        </div>
        
        
        
        <p class="text-slate-400 text-xs font-bold tracking-wider uppercase">&copy; {{ date('Y') }} Fakultas Ekonomi dan Bisnis UNJ &middot; Sistem Informasi Administrasi</p>
        
        <!-- UI Controls (Zoom & Fullscreen) -->
        <div class="flex items-center gap-3 w-[200px] justify-end">
            <div class="flex items-center bg-slate-50 rounded-lg border border-slate-200 shadow-sm p-1">
                <button id="btn-zoom-out" class="p-1 rounded text-slate-500 hover:bg-white hover:text-slate-800 hover:shadow-sm transition-all" title="Zoom Out">
                    <i data-lucide="minus" class="w-3.5 h-3.5"></i>
                </button>
                <span id="zoom-level-text" class="px-1 text-[10px] font-bold text-slate-600 font-mono">100%</span>
                <button id="btn-zoom-in" class="p-1 rounded text-slate-500 hover:bg-white hover:text-slate-800 hover:shadow-sm transition-all" title="Zoom In">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                </button>
            </div>
            <button id="btn-fullscreen" class="p-1.5 rounded-lg bg-slate-50 text-slate-500 hover:text-orange-600 hover:bg-orange-50 border border-slate-200 shadow-sm transition-all" title="Toggle Fullscreen">
                <i data-lucide="maximize" class="w-4 h-4"></i>
            </button>
    </footer>

    <!-- Scripts -->
    <script>
        lucide.createIcons();
        
        let monthlySchedule = @json($monthlyScheduleData ?? []);

        // ===================================================
        let isTabCalendarActive = false;
        const tabToday = document.getElementById('tab-today');
        const tabWeekly = document.getElementById('tab-weekly');
        const viewToday = document.getElementById('today-view');
        const viewWeekly = document.getElementById('weekly-view');

        if(tabToday && tabWeekly && viewToday && viewWeekly) {
            tabToday.addEventListener('click', () => {
                viewToday.classList.remove('hidden');
                viewWeekly.classList.add('hidden');
                
                tabToday.className = "px-3 py-1.5 text-xs font-bold rounded-lg bg-orange-100 text-orange-700 shadow-sm border border-orange-200 transition-colors flex items-center gap-1.5";
                tabWeekly.className = "px-3 py-1.5 text-xs font-bold rounded-lg bg-slate-50 text-slate-500 hover:bg-slate-100 border border-slate-200 shadow-sm transition-colors flex items-center gap-1.5";
            });

            tabWeekly.addEventListener('click', () => {
                viewToday.classList.add('hidden');
                viewWeekly.classList.remove('hidden');
                
                tabToday.className = "px-3 py-1.5 text-xs font-bold rounded-lg bg-slate-50 text-slate-500 hover:bg-slate-100 border border-slate-200 shadow-sm transition-colors flex items-center gap-1.5";
                tabWeekly.className = "px-3 py-1.5 text-xs font-bold rounded-lg bg-orange-100 text-orange-700 shadow-sm border border-orange-200 transition-colors flex items-center gap-1.5";
                
                renderWeeklyCalendar();
            });
        }

        let weeklyCalendar = null;
        let miniCalendar = null;

        function renderWeeklyCalendar() {
            if (weeklyCalendar) {
                weeklyCalendar.updateSize();
                if(miniCalendar) miniCalendar.updateSize();
                return;
            }

            var miniEl = document.getElementById('mini-calendar');
            miniCalendar = new FullCalendar.Calendar(miniEl, {
                initialView: 'dayGridMonth',
                locale: 'id',
                headerToolbar: {
                    left: 'prev',
                    center: 'title',
                    right: 'next'
                },
                height: 350,
                contentHeight: 'auto',
                fixedWeekCount: false,
                showNonCurrentDates: false,
                events: monthlySchedule,
                dateClick: function(info) {
                    weeklyCalendar.gotoDate(info.date);
                },
                datesSet: function() {
                    // Timeout ensures cells are rendered before we attempt to highlight them
                    setTimeout(applyMiniCalendarHighlight, 10);
                }
            });
            miniCalendar.render();

            var weeklyEl = document.getElementById('weekly-calendar');
            weeklyCalendar = new FullCalendar.Calendar(weeklyEl, {
                initialView: 'timeGridWeek',
                locale: 'id',
                headerToolbar: false,
                height: '100%',
                slotMinTime: '07:00:00',
                slotMaxTime: '21:00:00',
                allDaySlot: false,
                nowIndicator: true,
                events: monthlySchedule,
                datesSet: function(info) {
                    document.getElementById('weekly-title').innerText = info.view.title;
                    if (miniCalendar) {
                        miniCalendar.gotoDate(info.view.currentStart);
                        highlightMiniCalendarWeek(info.view.currentStart);
                    }
                },
                eventClick: function(info) {
                    showWeeklyEventDetail(info.event);
                },
                eventContent: function(arg) {
                    return {
                        html: `
                        <div class="h-full w-full p-2.5 flex flex-col justify-start rounded-xl bg-orange-50/90 backdrop-blur-sm border border-orange-200/80 shadow-sm hover:shadow-[0_8px_20px_-6px_rgba(234,88,12,0.3)] hover:-translate-y-0.5 transition-all duration-300 relative group overflow-hidden cursor-pointer">
                            <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-gradient-to-b from-orange-400 to-orange-600"></div>
                            
                            <!-- Dekorasi Halus -->
                            <div class="absolute -right-6 -bottom-6 w-16 h-16 bg-orange-500/10 rounded-full blur-2xl group-hover:bg-orange-500/20 transition-colors"></div>

                            <div class="pl-1 relative z-10 flex flex-col h-full overflow-hidden">
                                <div class="text-[10px] font-black text-orange-600 tracking-wider mb-0.5 flex items-center gap-1">
                                    <i data-lucide="clock" class="w-3 h-3"></i> ${arg.timeText}
                                </div>
                                <div class="text-xs font-bold text-slate-800 leading-snug break-words line-clamp-2">${arg.event.title}</div>
                                
                                <div class="mt-auto pt-1">
                                    <div class="text-[10px] font-semibold text-slate-500 truncate flex items-center gap-1">
                                        <i data-lucide="user-round" class="w-3 h-3 text-orange-400"></i>
                                        ${arg.event.extendedProps.requester_name || '-'}
                                    </div>
                                </div>
                            </div>
                        </div>
                        `
                    };
                }
            });
            weeklyCalendar.render();
        }

        let activeWeekDates = [];

        function highlightMiniCalendarWeek(startDate) {
            activeWeekDates = [];
            // info.view.currentStart from weekly calendar is the start of the week
            let curr = new Date(startDate);
            for(let i=0; i<7; i++) {
                // Ensure date format is YYYY-MM-DD in local time
                let year = curr.getFullYear();
                let month = String(curr.getMonth() + 1).padStart(2, '0');
                let day = String(curr.getDate()).padStart(2, '0');
                activeWeekDates.push(`${year}-${month}-${day}`);
                curr.setDate(curr.getDate() + 1);
            }
            applyMiniCalendarHighlight();
        }

        function applyMiniCalendarHighlight() {
            // Remove previous highlights
            document.querySelectorAll('#mini-calendar .fc-daygrid-day').forEach(el => {
                el.classList.remove('active-week-highlight');
            });
            
            // Add new highlights
            activeWeekDates.forEach(d => {
                let cell = document.querySelector(`#mini-calendar .fc-daygrid-day[data-date="${d}"]`);
                if (cell) {
                    cell.classList.add('active-week-highlight');
                }
            });
        }

        function showWeeklyEventDetail(event) {
            const detailEl = document.getElementById('weekly-event-detail');
            const start = event.start;
            const end = event.end;
            
            const timeStr = start.toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'}) + ' - ' + (end ? end.toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'}) : '');
            const dateStr = start.toLocaleDateString('id-ID', {weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'});
            
            let html = `
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center shrink-0 border border-slate-200">
                        <i data-lucide="building" class="w-4 h-4 text-slate-500"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-800 text-lg leading-tight">${event.title}</h4>
                        <p class="text-xs text-slate-500 mt-1">${dateStr}</p>
                    </div>
                </div>
                
                <div class="mt-5 p-4 rounded-2xl bg-orange-50 border border-orange-100">
                    <div class="flex items-center gap-3 mb-2">
                        <i data-lucide="clock" class="w-4 h-4 text-orange-500"></i>
                        <span class="text-sm font-semibold text-orange-800">${timeStr}</span>
                    </div>
                    <div class="flex items-center gap-3 mb-2">
                        <i data-lucide="door-open" class="w-4 h-4 text-orange-500"></i>
                        <span class="text-sm font-semibold text-orange-800">${event.extendedProps.room_name || 'Ruangan'}</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <i data-lucide="user-round" class="w-4 h-4 text-orange-500"></i>
                        <span class="text-sm font-semibold text-orange-800">${event.extendedProps.requester_name || '-'}</span>
                    </div>
                </div>
                
                <div class="mt-4 text-sm text-slate-600">
                    Agenda ini dilaksanakan di <strong>${event.extendedProps.room_name || 'Ruangan'}</strong> pada jam yang telah ditentukan.
                </div>
            `;
            
            detailEl.innerHTML = html;
            lucide.createIcons();
        }        // Fullscreen Logic
        // ===================================================
        const btnFullscreens = document.querySelectorAll('#btn-fullscreen, #btn-fullscreen-portal');
        btnFullscreens.forEach(btn => {
            btn.addEventListener('click', () => {
                if (!document.fullscreenElement) {
                    document.documentElement.requestFullscreen().catch((err) => {
                        console.error(`Error attempting to enable fullscreen: ${err.message}`);
                    });
                } else {
                    document.exitFullscreen();
                }
            });
        });

        document.addEventListener('fullscreenchange', () => {
            const icon = document.fullscreenElement ? 'minimize' : 'maximize';
            btnFullscreens.forEach(btn => {
                btn.innerHTML = `<i data-lucide="${icon}" class="w-5 h-5"></i>`;
            });
            lucide.createIcons();
        });

        // ===================================================
        // Zoom Logic (Simulate Chrome Ctrl +/- via REM scaling)
        // ===================================================
        let currentZoom = parseFloat(localStorage.getItem('display_zoom_level')) || 1;
        const btnsZoomIn = document.querySelectorAll('#btn-zoom-in');
        const btnsZoomOut = document.querySelectorAll('#btn-zoom-out');
        const zoomLevelTexts = document.querySelectorAll('#zoom-level-text');

        function applyZoom() {
            document.documentElement.style.fontSize = (currentZoom * 16) + 'px';
            zoomLevelTexts.forEach(el => { el.innerText = Math.round(currentZoom * 100) + '%'; });
            localStorage.setItem('display_zoom_level', currentZoom);
        }
        
        applyZoom();

        btnsZoomIn.forEach(btn => {
            btn.addEventListener('click', () => {
                currentZoom += 0.1;
                applyZoom();
            });
        });
        
        btnsZoomOut.forEach(btn => {
            btn.addEventListener('click', () => {
                if (currentZoom > 0.3) {
                    currentZoom -= 0.1;
                    applyZoom();
                }
            });
        });

        // ===================================================
        // Digital Clock
        // ===================================================
        function updateClock() {
            const now = new Date();
            const h = String(now.getHours()).padStart(2, '0');
            const m = String(now.getMinutes()).padStart(2, '0');
            const s = String(now.getSeconds()).padStart(2, '0');
            document.getElementById('digital-clock').innerHTML = `${h}<span class="clock-colon opacity-75">:</span>${m}<span class="clock-colon opacity-75">:</span>${s}`;

            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            document.getElementById('digital-date').innerHTML = `${now.toLocaleDateString('id-ID', options)}`;
            
            // Only update icons if they are empty
            if(!document.querySelector('#digital-date svg')) lucide.createIcons();
        }
        updateClock();
        setInterval(updateClock, 1000);

        // ===================================================
        // SPA Logic (No Refresh Navigation)
        // ===================================================
        let schedule = @json($scheduleData ?? []);
        window.selectedRoomId = null;
        let showRoomBadge = true;

        function openRoom(roomId, roomName) {
            // Tampilkan loading state jika perlu, namun untuk seamless kita langsung sembunyikan portal
            document.getElementById('portal-view').classList.add('hidden');
            document.getElementById('calendar-view-container').classList.remove('hidden');
            document.getElementById('btn-back-to-portal').classList.remove('hidden');
            document.getElementById('footer-tabs').classList.remove('hidden');
            document.getElementById('footer-tabs').classList.add('flex');
            
            // Re-render calendar so it corrects its dimensions after being unhidden
            setTimeout(() => {
                if(typeof calendar !== 'undefined' && calendar !== null) calendar.render();
                if(typeof weeklyCalendar !== 'undefined' && weeklyCalendar !== null) weeklyCalendar.render();
                if(typeof miniCalendar !== 'undefined' && miniCalendar !== null) miniCalendar.render();
            }, 100);

            // Update URL and State
            window.selectedRoomId = roomId;
            window.selectedRoomName = roomName;
            history.pushState({ room_id: roomId }, '', '?room_id=' + roomId);
            showRoomBadge = false;

            // Fetch new data via AJAX
            fetchDataAndRender(roomId);
        }

        function closeRoom() {
            document.getElementById('calendar-view-container').classList.add('hidden');
            document.getElementById('portal-view').classList.remove('hidden');
            document.getElementById('btn-back-to-portal').classList.add('hidden');
            document.getElementById('footer-tabs').classList.add('hidden');
            document.getElementById('footer-tabs').classList.remove('flex');
            
            window.selectedRoomId = null;
            history.pushState(null, '', window.location.pathname);
        }

        window.addEventListener('popstate', function(event) {
            const params = new URLSearchParams(window.location.search);
            const roomId = params.get('room_id');
            if (roomId) {
                openRoom(roomId, "Ruangan");
            } else {
                closeRoom();
            }
        });

        // Initialize state based on URL on first load
        window.addEventListener('DOMContentLoaded', () => {
            const params = new URLSearchParams(window.location.search);
            const roomId = params.get('room_id');
            if (roomId) {
                // If loaded with room_id, open it (but we don't know the name right away unless passed from server)
                openRoom(roomId, "Ruangan Terpilih");
            }
        });

        // ===================================================
        // Data jadwal dari server & AJAX Auto-Refresh
        // ===================================================
        
        function fetchDataAndRender(roomId = null) {
            let url = '/room-reservations/public-display'; // Ensure base URL is clean
            if(roomId) url += '?room_id=' + roomId;

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if(data && data.schedule) {
                    schedule = data.schedule;
                    renderMainStage();
                    renderTimelinePage();
                }
                if(data && data.monthlySchedule) {
                    monthlySchedule = data.monthlySchedule;
                    if(typeof weeklyCalendar !== 'undefined' && weeklyCalendar !== null) {
                        weeklyCalendar.removeAllEventSources();
                        weeklyCalendar.addEventSource(monthlySchedule);
                    }
                }
            }).catch(console.error);
        }

        // Polling data setiap 60 detik (tanpa refresh halaman)
        setInterval(() => {
            fetchDataAndRender(window.selectedRoomId);
        }, 60000);

        function getStatus(item, now) {
            if (now < item.start) return 'upcoming';
            if (now >= item.start && now <= item.end) return 'active';
            return 'past';
        }

        // ===================================================
        // Render MAIN STAGE (Now Playing)
        // ===================================================
        let activeCountdownInterval = null;

        function renderMainStage() {
            const el = document.getElementById('main-stage');
            if (!el) return;
            const now = Date.now();

            if (activeCountdownInterval) {
                clearInterval(activeCountdownInterval);
                activeCountdownInterval = null;
            }

            const active = schedule.find(item => getStatus(item, now) === 'active');
            const upcoming = schedule.filter(item => getStatus(item, now) === 'upcoming')
                                      .sort((a, b) => a.start - b.start)[0];

            // Watermark templates — dua varian berdasarkan state
            const logoUrl = '{{ asset("logo-unj.png") }}';

            // Reusable decoration pieces
            const cornerSize = 40; // px panjang bracket
            const cornerW = 3;     // px tebal bracket
            const cornerOpacity = '0.18';

            // Corner bracket L-shapes (kiri-atas, kanan-atas, kiri-bawah, kanan-bawah)
            const cornerDecor = `
                <!-- Corner brackets -->
                <div style="position:absolute;top:32px;left:32px;width:${cornerSize}px;height:${cornerSize}px;
                            border-top:${cornerW}px solid rgba(255,255,255,${cornerOpacity});
                            border-left:${cornerW}px solid rgba(255,255,255,${cornerOpacity});
                            border-radius:4px 0 0 0;pointer-events:none;z-index:2;"></div>
                <div style="position:absolute;top:32px;right:32px;width:${cornerSize}px;height:${cornerSize}px;
                            border-top:${cornerW}px solid rgba(255,255,255,${cornerOpacity});
                            border-right:${cornerW}px solid rgba(255,255,255,${cornerOpacity});
                            border-radius:0 4px 0 0;pointer-events:none;z-index:2;"></div>
                <div style="position:absolute;bottom:32px;left:32px;width:${cornerSize}px;height:${cornerSize}px;
                            border-bottom:${cornerW}px solid rgba(255,255,255,${cornerOpacity});
                            border-left:${cornerW}px solid rgba(255,255,255,${cornerOpacity});
                            border-radius:0 0 0 4px;pointer-events:none;z-index:2;"></div>
                <div style="position:absolute;bottom:32px;right:32px;width:${cornerSize}px;height:${cornerSize}px;
                            border-bottom:${cornerW}px solid rgba(255,255,255,${cornerOpacity});
                            border-right:${cornerW}px solid rgba(255,255,255,${cornerOpacity});
                            border-radius:0 0 4px 0;pointer-events:none;z-index:2;"></div>

                <!-- Dot grid pattern -->
                <div style="position:absolute;inset:0;pointer-events:none;z-index:1;
                            background-image:radial-gradient(circle, rgba(255,255,255,0.12) 1px, transparent 1px);
                            background-size:28px 28px;"></div>
            `;

            // Active/Upcoming: logo tengah agak ke atas, hindari footer
            const watermarkHtmlBusy = cornerDecor + `
                <img src="${logoUrl}" alt="" aria-hidden="true"
                     style="position:absolute; left:50%; top:40%; transform:translate(-50%,-50%);
                            width:420px; height:420px; object-fit:contain;
                            opacity:0.10; filter:brightness(1) invert(1);
                            pointer-events:none; user-select:none; z-index:1;">
            `;

            // Empty/Available: logo tepat di tengah
            const watermarkHtmlFree = cornerDecor + `
                <img src="${logoUrl}" alt="" aria-hidden="true"
                     style="position:absolute; left:50%; top:50%; transform:translate(-50%,-50%);
                            width:420px; height:420px; object-fit:contain;
                            opacity:0.10; filter:brightness(1) invert(1);
                            pointer-events:none; user-select:none; z-index:1;">
            `;

            if (active) {
                el.innerHTML = `
                    <div class="relative w-full h-full rounded-[3rem] overflow-hidden shadow-[0_20px_60px_-15px_rgba(234,88,12,0.4)] flex flex-col pt-10 xl:pt-14 text-white transition-all duration-700 bg-gradient-to-br from-orange-500 via-orange-600 to-red-600 border border-orange-400/50">
                        <!-- Decorative bg shapes -->
                        <div class="absolute top-0 right-0 w-96 h-96 bg-white/20 rounded-full blur-[100px] -translate-y-1/2 translate-x-1/3 pointer-events-none"></div>
                        <div class="absolute bottom-0 left-0 w-[40rem] h-[40rem] bg-red-900/30 rounded-full blur-[120px] translate-y-1/3 -translate-x-1/3 pointer-events-none"></div>
                        ${watermarkHtmlBusy}
                        
                        <!-- Header -->
                        <div class="flex items-center gap-5 shrink-0 relative z-10 px-10 xl:px-14">
                            <div class="w-6 h-6 rounded-full bg-white/30 flex items-center justify-center shadow-[0_0_15px_rgba(255,255,255,0.5)]">
                                <div class="w-3 h-3 rounded-full bg-white pulse-dot border-none"></div>
                            </div>
                            <span class="text-xl font-bold tracking-[0.3em] uppercase text-white drop-shadow-md">Sedang Berlangsung</span>
                            <span class="ml-auto text-3xl xl:text-4xl font-extrabold font-mono text-orange-900 bg-white/90 backdrop-blur-xl px-6 py-2.5 rounded-2xl shadow-xl shadow-black/10">${active.start_label} - ${active.end_label}</span>
                        </div>

                        <!-- Content -->
                        <div class="flex flex-col justify-center relative z-10 flex-1 px-10 xl:px-14 min-h-0 py-6">
                            <h2 class="text-4xl xl:text-[4rem] font-black text-white leading-[1.15] mb-8 font-display tracking-tight drop-shadow-lg line-clamp-3">${active.event_name}</h2>
                            
                            <div class="flex flex-wrap items-center gap-4 text-white shrink-0">
                                
                                <div class="flex items-center gap-4 bg-white/15 backdrop-blur-xl px-5 py-4 rounded-2xl border border-white/25 shadow-lg">
                                    <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-white/20 border border-white/20 shrink-0">
                                        <i data-lucide="user-round" class="w-6 h-6 text-white"></i>
                                    </div>
                                    <div class="flex flex-col leading-tight">
                                        <span class="text-[11px] font-bold tracking-[0.2em] uppercase text-white/60">Penanggung Jawab</span>
                                        <span class="text-xl xl:text-2xl font-black text-white">${active.requester_name}</span>
                                    </div>
                                </div>

                                ${active.contact_person ? `
                                <div class="flex items-center gap-4 bg-white/15 backdrop-blur-xl px-5 py-4 rounded-2xl border border-white/25 shadow-lg">
                                    <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-white/20 border border-white/20 shrink-0">
                                        <i data-lucide="phone" class="w-6 h-6 text-white"></i>
                                    </div>
                                    <div class="flex flex-col leading-tight">
                                        <span class="text-[11px] font-bold tracking-[0.2em] uppercase text-white/60">Kontak</span>
                                        <span class="text-xl xl:text-2xl font-black text-white">${active.contact_person}</span>
                                    </div>
                                </div>
                                ` : ''}

                                ${showRoomBadge ? `
                                <div class="flex items-center gap-4 bg-white/15 backdrop-blur-xl px-5 py-4 rounded-2xl border border-white/25 shadow-lg">
                                    <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-white/20 border border-white/20 shrink-0">
                                        <i data-lucide="door-open" class="w-6 h-6 text-white"></i>
                                    </div>
                                    <div class="flex flex-col leading-tight">
                                        <span class="text-[11px] font-bold tracking-[0.2em] uppercase text-white/60">Ruangan</span>
                                        <span class="text-xl xl:text-2xl font-black text-white">${active.room_name}</span>
                                    </div>
                                </div>
                                ` : ''}
                            </div>
                        </div>

                        <!-- Footer Widget Docked to Bottom -->
                        <div class="mt-auto relative z-10 w-full shrink-0">
                            <div class="bg-black/15 backdrop-blur-2xl border-t border-white/20 rounded-b-[3rem] p-8 xl:p-10 shadow-[0_-10px_40px_rgba(0,0,0,0.15)] w-full flex flex-col gap-6 xl:gap-8">
                                <div class="flex items-end justify-between px-2 xl:px-4">
                                    <div class="flex flex-col gap-2">
                                        <span class="text-sm font-bold text-white/80 uppercase tracking-[0.25em] flex items-center gap-2 drop-shadow-sm"><i data-lucide="timer" class="w-5 h-5"></i> Sisa Waktu</span>
                                        <span class="text-5xl xl:text-7xl font-black font-mono tracking-tighter text-white drop-shadow-md leading-none" id="live-countdown">--:--:--</span>
                                    </div>
                                    <div class="flex flex-col items-end gap-2">
                                        <span class="text-sm font-bold text-white/80 uppercase tracking-[0.25em] drop-shadow-sm">Progres</span>
                                        <div class="flex items-baseline gap-1 leading-none">
                                            <span class="text-5xl xl:text-7xl font-black font-mono tracking-tighter text-white drop-shadow-md" id="live-progress-text">--</span>
                                            <span class="text-3xl xl:text-4xl font-bold text-white/70">%</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="w-full h-5 xl:h-6 bg-black/30 rounded-full overflow-hidden shadow-inner p-1.5 backdrop-blur-sm border border-white/10">
                                    <div id="live-progress-bar" class="h-full bg-gradient-to-r from-white/60 via-white to-white rounded-full shadow-[0_0_20px_rgba(255,255,255,1)] transition-all duration-1000 ease-linear relative overflow-hidden" style="width: 0%">
                                        <div class="absolute inset-0 bg-white/40 animate-pulse"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                // Start tick for active
                activeCountdownInterval = setInterval(() => {
                    const currentNow = Date.now();
                    const activeStart = active.start;
                    const activeEnd = active.end;
                    
                    const total = activeEnd - activeStart;
                    let elapsed = currentNow - activeStart;
                    if (elapsed < 0) elapsed = 0;
                    if (elapsed > total) elapsed = total;
                    
                    const pct = (elapsed / total) * 100;
                    const remainingMs = activeEnd - currentNow;
                    
                    const pBar = document.getElementById('live-progress-bar');
                    const pText = document.getElementById('live-progress-text');
                    const cText = document.getElementById('live-countdown');
                    
                    if(pBar) pBar.style.width = pct + '%';
                    if(pText) pText.innerText = Math.round(pct);
                    if(cText) {
                        if (remainingMs <= 0) {
                            cText.innerText = "Selesai";
                        } else {
                            let totalSec = Math.floor(remainingMs / 1000);
                            let h = Math.floor(totalSec / 3600);
                            let m = Math.floor((totalSec % 3600) / 60);
                            let s = totalSec % 60;
                            if (h > 0) {
                                cText.innerText = `${h}:${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
                            } else {
                                cText.innerText = `${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
                            }
                        }
                    }

                    // Auto-refresh layout if status changes
                    if (remainingMs <= 0 && document.visibilityState === 'visible') {
                        setTimeout(() => { renderMainStage(); renderTimelinePage(); }, 2000);
                    }
                }, 1000);

            } else if (upcoming) {
                // Upcoming
                el.innerHTML = `
                    <div class="relative w-full h-full rounded-[3rem] overflow-hidden shadow-[0_20px_60px_-15px_rgba(245,158,11,0.3)] flex flex-col pt-10 xl:pt-14 text-slate-900 transition-all duration-700 ease-out bg-gradient-to-br from-amber-300 via-amber-400 to-orange-400">
                        <!-- Decorative shapes -->
                        <div class="absolute top-0 right-0 w-[800px] h-[800px] bg-white/40 blur-[120px] rounded-full translate-x-1/3 -translate-y-1/3 pointer-events-none"></div>
                        <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-orange-600/20 blur-[100px] rounded-full -translate-x-1/3 translate-y-1/3 pointer-events-none"></div>
                        ${watermarkHtmlBusy}

                        <div class="px-10 xl:px-14 flex-1 flex flex-col justify-center relative z-10">
                            <span class="inline-flex items-center gap-2.5 px-4 py-2 rounded-2xl bg-white/40 text-amber-900 text-sm font-bold tracking-widest backdrop-blur-md mb-6 w-fit border border-white/50 shadow-sm">
                                <i data-lucide="clock-4" class="w-4 h-4 animate-bounce"></i>
                                AGENDA SELANJUTNYA
                            </span>
                            <h2 class="text-4xl xl:text-[4rem] font-black text-slate-900 leading-[1.15] mb-8 font-display tracking-tight drop-shadow-md line-clamp-3">${upcoming.event_name}</h2>
                            
                            <div class="flex flex-wrap items-center gap-4 text-slate-800 shrink-0">

                                <div class="flex items-center gap-4 bg-white/30 backdrop-blur-xl px-5 py-4 rounded-2xl border border-white/50 shadow-lg">
                                    <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-white/30 border border-white/30 shrink-0">
                                        <i data-lucide="user-round" class="w-6 h-6 text-amber-900"></i>
                                    </div>
                                    <div class="flex flex-col leading-tight">
                                        <span class="text-[11px] font-bold tracking-[0.2em] uppercase text-amber-900/60">Penanggung Jawab</span>
                                        <span class="text-xl xl:text-2xl font-black text-slate-900">${upcoming.requester_name}</span>
                                    </div>
                                </div>

                                ${upcoming.contact_person ? `
                                <div class="flex items-center gap-4 bg-white/30 backdrop-blur-xl px-5 py-4 rounded-2xl border border-white/50 shadow-lg">
                                    <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-white/30 border border-white/30 shrink-0">
                                        <i data-lucide="phone" class="w-6 h-6 text-amber-900"></i>
                                    </div>
                                    <div class="flex flex-col leading-tight">
                                        <span class="text-[11px] font-bold tracking-[0.2em] uppercase text-amber-900/60">Kontak</span>
                                        <span class="text-xl xl:text-2xl font-black text-slate-900">${upcoming.contact_person}</span>
                                    </div>
                                </div>
                                ` : ''}

                                ${showRoomBadge ? `
                                <div class="flex items-center gap-4 bg-white/30 backdrop-blur-xl px-5 py-4 rounded-2xl border border-white/50 shadow-lg">
                                    <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-white/30 border border-white/30 shrink-0">
                                        <i data-lucide="door-open" class="w-6 h-6 text-amber-900"></i>
                                    </div>
                                    <div class="flex flex-col leading-tight">
                                        <span class="text-[11px] font-bold tracking-[0.2em] uppercase text-amber-900/60">Ruangan</span>
                                        <span class="text-xl xl:text-2xl font-black text-slate-900">${upcoming.room_name}</span>
                                    </div>
                                </div>
                                ` : ''}
                            </div>
                        </div>

                        <!-- Footer Widget Docked to Bottom -->
                        <div class="mt-auto relative z-10 w-full shrink-0 pt-8">
                            <div class="bg-white/40 backdrop-blur-2xl border-t border-white/50 rounded-b-[3rem] p-8 xl:p-10 shadow-[0_-10px_40px_rgba(0,0,0,0.1)] w-full flex items-center justify-between">
                                <div class="flex flex-col gap-2">
                                    <span class="text-sm font-bold text-amber-900/70 uppercase tracking-[0.25em] flex items-center gap-2 drop-shadow-sm"><i data-lucide="calendar" class="w-5 h-5"></i> Jadwal Pelaksanaan</span>
                                    <span class="text-5xl xl:text-6xl font-black font-mono tracking-tighter text-slate-800 drop-shadow-sm leading-none">
                                        ${upcoming.start_label} - ${upcoming.end_label}
                                    </span>
                                </div>
                                <div class="flex flex-col items-end gap-2 text-right">
                                    <span class="text-sm font-bold text-amber-900/70 uppercase tracking-[0.25em] drop-shadow-sm">Dimulai Dalam</span>
                                    <div class="px-6 py-3 rounded-2xl bg-slate-900 text-amber-400 font-black text-4xl xl:text-5xl font-mono shadow-[0_4px_20px_rgba(0,0,0,0.2)]">
                                        <span id="upcoming-countdown">--:--:--</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                // Start tick for upcoming
                activeCountdownInterval = setInterval(() => {
                    const currentNow = Date.now();
                    const upcomingStart = upcoming.start;
                    const remainingMs = upcomingStart - currentNow;
                    const cText = document.getElementById('upcoming-countdown');
                    if (cText) {
                        if (remainingMs <= 0) {
                            cText.innerText = "Sekarang!";
                            if (document.visibilityState === 'visible') {
                                setTimeout(() => { renderMainStage(); renderTimelinePage(); }, 2000);
                            }
                        } else {
                            let totalSec = Math.floor(remainingMs / 1000);
                            let h = Math.floor(totalSec / 3600);
                            let m = Math.floor((totalSec % 3600) / 60);
                            let s = totalSec % 60;
                            if (h > 0) {
                                cText.innerText = `${h}:${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
                            } else {
                                cText.innerText = `${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
                            }
                        }
                    }
                }, 1000);

            } else {
                // Empty State
                el.innerHTML = `
                    <div class="relative w-full h-full rounded-[3rem] overflow-hidden shadow-[0_20px_60px_-15px_rgba(16,185,129,0.3)] flex flex-col justify-center items-center text-center p-12 transition-all duration-700 bg-gradient-to-br from-emerald-500 via-teal-500 to-cyan-600 border border-emerald-400">
                        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyMCIgaGVpZ2h0PSIyMCI+PHBhdGggZD0iTTAgMGgyMHYyMEgwem0xMCAxMGEyIDIgMCAxIDAgMC00IDIgMiAwIDAgMCAwIDRaIiBmaWxsPSJyZ2JhKDI1NSwyNTUsMjU1LDAuMDUpIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiLz48L3N2Zz4=')] opacity-30 mix-blend-overlay"></div>
                        
                        <!-- Decorative shapes -->
                        <div class="absolute top-0 right-0 w-[800px] h-[800px] bg-white/30 blur-[120px] rounded-full translate-x-1/3 -translate-y-1/3 pointer-events-none"></div>
                        <div class="absolute bottom-0 left-0 w-[600px] h-[600px] bg-cyan-900/30 blur-[100px] rounded-full -translate-x-1/3 translate-y-1/3 pointer-events-none"></div>
                        ${watermarkHtmlFree}

                        <div class="hover-float flex items-center justify-center w-36 h-36 rounded-[3rem] bg-white/20 backdrop-blur-2xl shadow-2xl text-white mb-10 border border-white/40 relative z-10 transition-transform">
                            <i data-lucide="door-open" class="w-16 h-16 drop-shadow-md"></i>
                        </div>
                        
                        <span class="inline-flex items-center gap-2.5 px-5 py-2.5 rounded-2xl bg-emerald-800/40 text-emerald-50 text-sm font-bold tracking-[0.2em] backdrop-blur-md mb-8 border border-white/20 uppercase relative z-10 shadow-inner">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            Ruangan Tersedia
                        </span>

                        <h2 class="text-5xl xl:text-[5.5rem] font-black text-white mb-8 font-display tracking-tight relative z-10 drop-shadow-lg leading-tight px-10">
                            ${window.selectedRoomName ? window.selectedRoomName : 'Ruangan FEB UNJ'}
                        </h2>
                        
                        <p class="text-2xl xl:text-3xl text-emerald-50 max-w-3xl relative z-10 font-medium leading-relaxed drop-shadow-sm px-10">
                            Belum ada agenda lebih lanjut yang dijadwalkan.
                        </p>
                    </div>
                `;
            }
            lucide.createIcons();
        }

        // ===================================================
        // Render TIMELINE dengan auto-pagination
        // ===================================================
        const PAGE_SIZE = 3; // Maksimal 3 data per slide
        const ROTATE_INTERVAL = 5000; // ms
        let currentPage = 0;
        let rotateTimer = null;

        function statusMeta(status) {
            if (status === 'active') return { color: 'var(--live)', label: 'BERLANGSUNG', dim: false };
            if (status === 'upcoming') return { color: 'var(--amber)', label: 'AKAN DATANG', dim: false };
            return { color: 'var(--muted-dim)', label: 'SELESAI', dim: true };
        }

        function renderTimelinePage() {
            const container = document.getElementById('timeline-container');
            if (!container) return;
            
            const agendaCountEl = document.getElementById('agenda-count');
            if(agendaCountEl) {
                agendaCountEl.innerText = schedule.length + ' ITEM';
            }
            
            const now = Date.now();
            const pages = Math.ceil(schedule.length / PAGE_SIZE) || 1;
            currentPage = currentPage % pages;
            const items = schedule.slice(currentPage * PAGE_SIZE, currentPage * PAGE_SIZE + PAGE_SIZE);

            if (schedule.length === 0) {
                const emptyTitle = "Belum Ada Aktivitas";
                const emptyDesc = !showRoomBadge 
                    ? "Jadwal peminjaman untuk ruangan ini masih kosong hari ini." 
                    : "Belum ada data peminjaman yang tercatat untuk seluruh ruangan.";
                    
                container.innerHTML = `
                    <div class="flex-1 flex flex-col items-center justify-center text-center p-6 h-full page-fade-enter">
                        <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-4 border border-slate-100 shadow-inner">
                            <i data-lucide="inbox" class="w-10 h-10 text-slate-300"></i>
                        </div>
                        <h4 class="text-xl font-bold text-slate-700 mb-2 font-display">${emptyTitle}</h4>
                        <p class="text-sm font-medium text-slate-500 max-w-[250px] leading-relaxed">
                            ${emptyDesc}
                        </p>
                    </div>
                `;
                lucide.createIcons();
                renderPageDots(0);
                return;
            }

            const rows = items.map((item, index) => {
                const status = getStatus(item, now);
                const meta = statusMeta(status);
                const isActive = status === 'active';
                const isPast = status === 'past';
                
                return `
                    <div class="relative pl-10 pb-5 last:pb-0 ${isActive ? 'scale-[1.02] transition-transform origin-left z-10' : 'z-0'}">
                        <!-- Connecting Line -->
                        ${index !== items.length - 1 ? `<div class="absolute left-[11px] top-[48px] bottom-[-24px] w-0.5 bg-slate-200 rounded-full"></div>` : ''}
                        
                        <!-- Timeline Dot -->
                        <div class="absolute left-0 top-6 w-6 h-6 rounded-full bg-white border-[4px] shadow-sm flex items-center justify-center z-10" style="border-color: ${meta.color}; ${isActive ? 'box-shadow: 0 0 0 6px var(--live-soft)' : ''}">
                            ${isActive ? '<div class="w-2 h-2 rounded-full bg-orange-600 animate-ping"></div>' : ''}
                        </div>

                        <!-- Card -->
                        <div class="bg-white rounded-3xl p-5 border ${isActive ? 'border-orange-400 shadow-[0_15px_40px_rgba(249,115,22,0.2)] ring-4 ring-orange-500/10 bg-gradient-to-br from-white to-orange-50/50' : 'border-slate-200 shadow-[0_10px_30px_rgba(0,0,0,0.08)] hover:shadow-xl hover:border-slate-300'} transition-all duration-300 relative z-20">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-mono font-bold text-base px-3 py-1 rounded-lg border shadow-sm ${isActive ? 'text-orange-700 bg-orange-100 border-orange-200' : 'text-slate-700 bg-slate-100 border-slate-300'}">${item.start_label} - ${item.end_label}</span>
                                ${isActive 
                                    ? `<span class="text-[10px] font-black tracking-widest px-3 py-1.5 rounded-xl bg-gradient-to-r from-orange-500 to-red-500 text-white shadow-md animate-pulse">LIVE</span>` 
                                    : `<span class="text-[10px] font-bold tracking-wider px-3 py-1.5 rounded-xl bg-slate-100 text-slate-500">${meta.label}</span>`}
                            </div>
                            <h4 class="text-lg font-bold text-slate-800 leading-tight mb-3 line-clamp-2">${item.event_name}</h4>
                            <div class="flex flex-col gap-2.5 text-xs font-semibold text-slate-600">
                                <span class="flex items-center gap-3">
                                    <div class="w-7 h-7 flex items-center justify-center bg-slate-100 rounded-full border border-slate-200">
                                        <i data-lucide="user" class="w-3.5 h-3.5 text-slate-500"></i>
                                    </div>
                                    ${item.requester_name}
                                </span>
                                ${showRoomBadge ? `
                                <span class="flex items-center gap-3">
                                    <div class="w-7 h-7 flex items-center justify-center bg-orange-50 rounded-full border border-orange-100">
                                        <i data-lucide="door-open" class="w-3.5 h-3.5 text-orange-500"></i>
                                    </div>
                                    ${item.room_name}
                                </span>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                `;
            }).join('');

            container.innerHTML = `<div class="page-fade-enter">${rows}</div>`;
            lucide.createIcons();
            renderPageDots(pages);
        }

        function renderPageDots(pages) {
            const dotsEl = document.getElementById('page-dots');
            if (!dotsEl) return;
            if (pages <= 1) { dotsEl.innerHTML = ''; return; }
            dotsEl.innerHTML = Array.from({ length: pages }).map((_, i) =>
                `<div class="h-1.5 rounded-full transition-all duration-500 ${i === currentPage ? 'bg-orange-500 w-12 shadow-[0_0_10px_rgba(249,115,22,0.5)]' : 'bg-slate-300 w-4'}"></div>`
            ).join('');
        }

        function startRotation() {
            const pages = Math.ceil(schedule.length / PAGE_SIZE) || 1;
            if (pages <= 1) return;
            if (rotateTimer) clearInterval(rotateTimer);
            rotateTimer = setInterval(() => {
                currentPage += 1;
                renderTimelinePage();
            }, ROTATE_INTERVAL);
        }


        // Equalize Room Cards Height
        function equalizeCardHeights() {
            const cards = document.querySelectorAll('.room-card');
            if (!cards.length) return;
            // Reset
            cards.forEach(card => card.style.height = 'auto');
            // Find max height
            let maxHeight = 0;
            cards.forEach(card => {
                // Ensure element is visible before calculating height
                if (card.style.display !== 'none') {
                    const h = card.offsetHeight;
                    if (h > maxHeight) maxHeight = h;
                }
            });
            // Apply max height
            if (maxHeight > 0) {
                cards.forEach(card => card.style.height = maxHeight + 'px');
            }
        }

        // Init + refresh berkala
        // ===================================================
        function initDisplay() {
            renderMainStage();
            renderTimelinePage();
            startRotation();
            // Allow DOM to fully paint before calculating height
            setTimeout(equalizeCardHeights, 100);
        }
        initDisplay();
        
        window.addEventListener('resize', () => {
            setTimeout(equalizeCardHeights, 100);
        });
        
        // Cek secara berkala (30s) hanya jika perlu redraw keseluruhan 
        // yang tidak tertangkap oleh ticking timer (misal: refresh data)
        setInterval(() => {
            renderTimelinePage();
            // renderMainStage tidak perlu dipanggil manual di sini karena 
            // timer sudah melakukan auto-refresh layout saat state berubah (lihat setInterval countdown).
        }, 30000);
        
        // Search Filter Logic for Room Grid
        const searchInput = document.getElementById('room-search');
        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                const term = e.target.value.toLowerCase();
                const cards = document.querySelectorAll('.room-card');
                cards.forEach(card => {
                    const name = card.getAttribute('data-room-name').toLowerCase();
                    if (name.includes(term)) {
                        card.style.display = '';
                    } else {
                        card.style.display = 'none';
                    }
                });
                equalizeCardHeights();
            });
        }
    </script>
</body>
</html>