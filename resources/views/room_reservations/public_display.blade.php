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

            <!-- Right: Clock -->
            <div class="flex items-center text-right shrink-0 gap-5">
                <h2 class="text-xl xl:text-xl font-black text-slate-800 font-display uppercase">
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
    <main class="flex-1 w-full p-6 lg:p-8 mx-auto flex flex-col min-h-0 relative z-10 max-w-[1920px]">
        @if(!$selectedRoom)
            <div class="mb-6 p-4 glass-panel rounded-2xl flex items-center gap-4 mx-auto w-full max-w-4xl shrink-0 shadow-sm">
                <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center shrink-0">
                    <i data-lucide="settings-2" class="w-5 h-5 text-[color:var(--muted)]"></i>
                </div>
                <form action="{{ route('room-reservations.public-display') }}" method="GET" class="flex-1 flex gap-4">
                    <select name="room_id" class="flex-1 bg-white border border-[color:var(--hairline)] rounded-xl px-4 py-3 text-base text-[color:var(--text)] focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 font-medium transition-all">
                        <option value="">-- Pilih Ruangan untuk Monitor Ini --</option>
                        @foreach($rooms as $room)
                            <option value="{{ encrypt($room->id) }}" {{ $selectedRoom && $selectedRoom->id == $room->id ? 'selected' : '' }}>{{ $room->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white px-8 py-3 rounded-xl text-base font-bold transition-all shadow-[0_4px_12px_rgba(249,115,22,0.4)] hover:shadow-[0_6px_16px_rgba(249,115,22,0.5)] hover:-translate-y-0.5 flex items-center gap-2">
                        <i data-lucide="save" class="w-5 h-5"></i>
                        Simpan Pengaturan
                    </button>
                </form>
            </div>
        @endif

        @if($reservations->isEmpty())
            <!-- ===== Empty state: seluruh hari kosong ===== -->
            <div class="flex-1 flex flex-col items-center justify-center text-center">
                <div class="w-40 h-40 rounded-full flex items-center justify-center mb-10 bg-emerald-50 border border-emerald-100 shadow-[0_20px_40px_rgba(16,185,129,0.1)] relative">
                    <div class="absolute inset-0 rounded-full bg-emerald-400/20 animate-ping" style="animation-duration: 3s;"></div>
                    <i data-lucide="check-circle-2" class="w-20 h-20 text-emerald-500 relative z-10"></i>
                </div>
                <h2 class="text-5xl font-extrabold text-slate-800 mb-5 font-display tracking-tight">Ruangan Bebas Tersedia</h2>
                <p class="text-slate-500 text-2xl max-w-2xl leading-relaxed font-medium">Tidak ada jadwal peminjaman {{ $selectedRoom ? 'untuk ruangan ini' : '' }} pada hari ini. Silakan gunakan sesuai prosedur yang berlaku.</p>
            </div>
        @else


            <div class="flex-1 flex flex-row gap-8 min-h-0 h-full w-full mx-auto">

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
                            <span class="text-sm font-extrabold px-4 py-2 bg-white/90 backdrop-blur-md text-orange-600 rounded-xl font-mono border border-orange-100 shadow-sm">{{ $scheduleData->count() }} ITEM</span>
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
        @endif
    </main>

    <footer class="py-2.5 px-8 border-t border-slate-200 shrink-0 bg-white/80 backdrop-blur-md relative z-20 flex items-center justify-between">
        <!-- Spacer for balance -->
        <div class="w-[200px]"></div>
        
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
        </div>
    </footer>

    <!-- Scripts -->
    <script>
        lucide.createIcons();

        // ===================================================
        // Fullscreen Logic
        // ===================================================
        const btnFullscreen = document.getElementById('btn-fullscreen');
        if(btnFullscreen) {
            btnFullscreen.addEventListener('click', () => {
                if (!document.fullscreenElement) {
                    document.documentElement.requestFullscreen().catch((err) => {
                        console.error(`Error attempting to enable fullscreen: ${err.message}`);
                    });
                } else {
                    document.exitFullscreen();
                }
            });

            document.addEventListener('fullscreenchange', () => {
                const icon = document.fullscreenElement ? 'minimize' : 'maximize';
                btnFullscreen.innerHTML = `<i data-lucide="${icon}" class="w-6 h-6"></i>`;
                lucide.createIcons();
            });
        }

        // ===================================================
        // Zoom Logic (Simulate Chrome Ctrl +/- via REM scaling)
        // ===================================================
        let currentZoom = parseFloat(localStorage.getItem('display_zoom_level')) || 1;
        const btnZoomIn = document.getElementById('btn-zoom-in');
        const btnZoomOut = document.getElementById('btn-zoom-out');
        const zoomLevelText = document.getElementById('zoom-level-text');

        function applyZoom() {
            // Mengubah ukuran base font akan men-scale semua elemen Tailwind yang menggunakan 'rem'
            document.documentElement.style.fontSize = (currentZoom * 16) + 'px';
            if(zoomLevelText) zoomLevelText.innerText = Math.round(currentZoom * 100) + '%';
            localStorage.setItem('display_zoom_level', currentZoom);
        }
        
        // Terapkan zoom saat pertama kali dimuat
        applyZoom();

        if(btnZoomIn) {
            btnZoomIn.addEventListener('click', () => {
                currentZoom += 0.1;
                applyZoom();
            });
        }
        
        if(btnZoomOut) {
            btnZoomOut.addEventListener('click', () => {
                if (currentZoom > 0.3) {
                    currentZoom -= 0.1;
                    applyZoom();
                }
            });
        }

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
            document.getElementById('digital-date').innerHTML =
                `${now.toLocaleDateString('id-ID', options)}`;
            
            // Render icon only once ideally, but this is fine for now
            lucide.createIcons();
        }
        updateClock();
        setInterval(updateClock, 1000);

        // ===================================================
        // Data jadwal dari server & AJAX Auto-Refresh
        // ===================================================
        let schedule = @json($scheduleData ?? []);
        const showRoomBadge = {{ $selectedRoom ? 'false' : 'true' }};

        // Polling data setiap 60 detik (tanpa refresh halaman)
        setInterval(() => {
            fetch(window.location.href, {
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
            })
            .catch(err => console.error('Failed to fetch schedule updates:', err));
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
                            {{ $selectedRoom ? $selectedRoom->name : 'Ruangan FEB UNJ' }}
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
            const now = Date.now();
            const pages = Math.ceil(schedule.length / PAGE_SIZE) || 1;
            currentPage = currentPage % pages;
            const items = schedule.slice(currentPage * PAGE_SIZE, currentPage * PAGE_SIZE + PAGE_SIZE);

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

        // ===================================================
        // Init + refresh berkala
        // ===================================================
        function initDisplay() {
            if (!schedule.length) return;
            renderMainStage();
            renderTimelinePage();
            startRotation();
        }
        initDisplay();
        
        // Cek secara berkala (30s) hanya jika perlu redraw keseluruhan 
        // yang tidak tertangkap oleh ticking timer (misal: refresh data)
        setInterval(() => {
            renderTimelinePage();
            // renderMainStage tidak perlu dipanggil manual di sini karena 
            // timer sudah melakukan auto-refresh layout saat state berubah (lihat setInterval countdown).
        }, 30000);
    </script>
</body>
</html>