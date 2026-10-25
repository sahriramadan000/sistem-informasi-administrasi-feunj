@extends('layouts.app')

@section('title', 'Tambah Master Grup Ruangan - FEB UNJ')
@section('disable_layout_alerts', true)

@push('styles')
    <style>
        /* Scoped macOS / iOS Form Enhancements */
        .mac-card {
            background: #ffffff;
            border-radius: 1.25rem;
            border: 1px solid rgba(226, 232, 240, 0.85);
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.03), 0 2px 6px -1px rgba(0, 0, 0, 0.02);
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .mac-card:hover {
            border-color: rgba(254, 215, 170, 0.8);
            box-shadow: 0 10px 25px -4px rgba(255, 77, 0, 0.04), 0 4px 10px -2px rgba(0, 0, 0, 0.03);
        }

        /* macOS Step Badge */
        .mac-step-badge {
            width: 36px;
            height: 36px;
            border-radius: 12px;
            background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%);
            border: 1px solid #fed7aa;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ea580c;
            font-weight: 700;
            font-size: 0.9rem;
            box-shadow: 0 2px 6px rgba(255, 77, 0, 0.08);
            flex-shrink: 0;
        }

        /* Live Simulator Warm Orange Apple Card */
        .simulator-orange-card {
            background: linear-gradient(145deg, #fff7ed 0%, #fffaf5 50%, #ffedd5 100%);
            border-radius: 1.25rem;
            border: 1.5px solid #fed7aa;
            box-shadow: 0 8px 24px -4px rgba(255, 77, 0, 0.08), 0 2px 6px -1px rgba(255, 77, 0, 0.04);
        }

        /* Quick Preset Chips */
        .preset-chip {
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 600;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #475569;
            transition: all 0.15s ease;
            cursor: pointer;
            user-select: none;
        }
        .preset-chip:hover {
            background: #fff7ed;
            border-color: #fed7aa;
            color: #c2410c;
            transform: translateY(-1px);
        }
        .preset-chip:active {
            transform: translateY(0);
        }

        /* Pulse glow animation on preview update */
        @keyframes tokenPulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.03); filter: drop-shadow(0 2px 8px rgba(255, 77, 0, 0.25)); }
            100% { transform: scale(1); }
        }
        .token-updated {
            animation: tokenPulse 0.35s ease-out;
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

        /* Select2 macOS Styling Override */
        .select2-container {
            width: 100% !important;
        }
        .select2-container--default .select2-selection--single {
            height: 44px !important;
            padding: 8px 14px !important;
            border-radius: 0.75rem !important;
            border: 1px solid rgba(226, 232, 240, 0.9) !important;
            background-color: rgba(249, 250, 251, 0.7) !important;
            display: flex !important;
            align-items: center !important;
            transition: all 0.2s ease !important;
        }
        .select2-container--default .select2-selection--single:hover {
            background-color: #f9fafb !important;
            border-color: #cbd5e1 !important;
        }
        .select2-container--default.select2-container--focus .select2-selection--single,
        .select2-container--default.select2-container--open .select2-selection--single {
            background-color: #ffffff !important;
            border-color: #FF4D00 !important;
            box-shadow: 0 0 0 4px rgba(255, 77, 0, 0.1) !important;
            outline: none !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            padding-left: 0 !important;
            color: #1e293b !important;
            font-size: 0.8125rem !important;
            font-weight: 500 !important;
            line-height: normal !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 42px !important;
            right: 8px !important;
        }
        .select2-dropdown {
            border: 1px solid #e2e8f0 !important;
            border-radius: 0.85rem !important;
            box-shadow: 0 15px 35px -5px rgba(0, 0, 0, 0.1), 0 5px 15px -3px rgba(0, 0, 0, 0.05) !important;
            overflow: hidden !important;
            background: #ffffff !important;
            z-index: 9999 !important;
        }
        .select2-search--dropdown {
            padding: 8px !important;
        }
        .select2-search--dropdown .select2-search__field {
            border: 1px solid #e2e8f0 !important;
            border-radius: 0.5rem !important;
            padding: 6px 10px !important;
            font-size: 0.8125rem !important;
            outline: none !important;
        }
        .select2-search--dropdown .select2-search__field:focus {
            border-color: #FF4D00 !important;
            box-shadow: 0 0 0 2px rgba(255, 77, 0, 0.1) !important;
        }
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #FF4D00 !important;
            color: #ffffff !important;
        }
        .select2-container--default .select2-results__option[aria-selected="true"] {
            background-color: #fff7ed !important;
            color: #ea580c !important;
            font-weight: 600 !important;
        }
        .is-invalid + .select2-container .select2-selection--single {
            border-color: #f87171 !important;
            box-shadow: 0 0 0 2px rgba(248, 113, 113, 0.2) !important;
        }
    </style>
@endpush

@section('content')
    <div class="max-w-6xl mx-auto space-y-6 pb-12 relative">
        {{-- Floating Toast Notification --}}
        <div id="toastPopup" class="toast-popup fixed top-5 left-1/2 z-[9999] flex items-center gap-3 px-5 py-3 rounded-2xl bg-white/95 backdrop-blur-md border border-gray-200 shadow-xl text-xs sm:text-sm font-medium">
            <div id="toastIconWrap" class="w-7 h-7 rounded-xl flex items-center justify-center flex-shrink-0 bg-red-100 text-red-600">
                <i id="toastIcon" data-lucide="alert-circle" class="w-4 h-4"></i>
            </div>
            <span id="toastMessage" class="text-gray-800"></span>
            <button type="button" onclick="hideToast()" class="text-gray-400 hover:text-gray-600 ml-2 p-0.5 rounded-lg transition-colors cursor-pointer">
                <i data-lucide="x" class="w-3.5 h-3.5"></i>
            </button>
        </div>

        {{-- macOS Top Header Bar --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-gray-200/70">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-[#FF4D00] to-amber-500 text-white flex items-center justify-center shadow-xs flex-shrink-0">
                        <i data-lucide="folder-plus" class="w-5 h-5"></i>
                    </div>
                    <span>Tambah Master Grup Ruangan</span>
                </h1>
                <p class="text-xs sm:text-sm text-gray-500 mt-1 pl-13">
                    Daftarkan grup lokasi gedung atau lantai baru untuk mengelompokkan dan memudahkan navigasi peminjaman ruangan FEB UNJ.
                </p>
            </div>

            <div class="flex items-center gap-2.5 self-start sm:self-auto pl-13 sm:pl-0">
                <a href="{{ route('master.room-groups.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 text-xs sm:text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-200 rounded-full shadow-2xs hover:shadow-xs transition-all active:scale-[0.98]">
                    <i data-lucide="arrow-left" class="w-4 h-4 text-gray-500"></i>
                    <span>Kembali</span>
                </a>
            </div>
        </div>

        {{-- 2-Column macOS Studio Layout --}}
        <form id="group-form" method="POST" action="{{ route('master.room-groups.store') }}" novalidate>
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-7 items-start">
                
                {{-- LEFT COLUMN: Main Form Card (7 Cols) --}}
                <div class="lg:col-span-7 space-y-6">

                    {{-- Form Card --}}
                    <div class="mac-card p-6 sm:p-7">
                        <div class="flex items-center gap-3.5 mb-6 pb-4 border-b border-gray-100">
                            <div class="mac-step-badge">
                                <i data-lucide="folder" class="w-4 h-4 text-[#FF4D00]"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-gray-900 tracking-tight">
                                    Identitas Grup &amp; Penempatan Gedung
                                </h3>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    Tentukan nama pengelompokan lantai/gedung, status keaktifan, dan deskripsi area.
                                </p>
                            </div>
                        </div>

                        <div class="space-y-5">
                            {{-- Field 1: Nama Grup Ruangan --}}
                            <div class="space-y-1.5">
                                <label for="name" class="text-xs font-semibold text-gray-700 flex items-center gap-1.5">
                                    <i data-lucide="building" class="w-3.5 h-3.5 text-gray-400"></i>
                                    Nama Grup Ruangan (Gedung / Lantai) <span class="text-destructive">*</span>
                                </label>
                                <div class="relative group">
                                    <input type="text" name="name" id="name" required value="{{ old('name') }}"
                                        class="w-full h-11 px-3.5 text-xs sm:text-sm bg-gray-50/70 hover:bg-gray-50 focus:bg-white border @error('name') border-red-400 ring-2 ring-red-400/20 @else border-gray-200/90 @enderror focus:border-[#FF4D00] rounded-xl focus:outline-none focus:ring-4 focus:ring-orange-500/10 shadow-2xs transition-all font-medium placeholder:text-gray-400"
                                        placeholder="Misal: Gedung M (Dekanat) / Lantai 2"
                                        autocomplete="off">
                                </div>

                                {{-- Quick Presets for Group Name --}}
                                <div class="pt-1.5 space-y-1.5">
                                    <div class="flex items-center gap-1 text-[11px] text-gray-500">
                                        <i data-lucide="sparkles" class="w-3 h-3 text-amber-500"></i>
                                        <span>Pintasan Nama Gedung &amp; Lantai:</span>
                                    </div>
                                    <div class="flex flex-wrap gap-1.5">
                                        <button type="button" class="preset-chip" onclick="fillGroupName('Gedung M (Dekanat)')">Gedung M (Dekanat)</button>
                                        <button type="button" class="preset-chip" onclick="fillGroupName('Gedung RA Kartini')">Gedung RA Kartini</button>
                                        <button type="button" class="preset-chip" onclick="fillGroupName('Lantai 1')">Lantai 1</button>
                                        <button type="button" class="preset-chip" onclick="fillGroupName('Lantai 2')">Lantai 2</button>
                                        <button type="button" class="preset-chip" onclick="fillGroupName('Lantai 3')">Lantai 3</button>
                                        <button type="button" class="preset-chip" onclick="fillGroupName('Lantai 4')">Lantai 4</button>
                                        <button type="button" class="preset-chip" onclick="fillGroupName('Auditorium & Hall')">Auditorium &amp; Hall</button>
                                        <button type="button" class="preset-chip" onclick="fillGroupName('Laboratorium Terpadu')">Laboratorium Terpadu</button>
                                    </div>
                                </div>

                                @error('name')
                                    <p class="text-xs text-destructive flex items-center gap-1 mt-1">
                                        <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Field 2: Status Grup (Select2) --}}
                            <div class="space-y-1.5">
                                <label for="is_active" class="text-xs font-semibold text-gray-700 flex items-center gap-1.5">
                                    <i data-lucide="activity" class="w-3.5 h-3.5 text-gray-400"></i>
                                    Status Keaktifan Grup <span class="text-destructive">*</span>
                                </label>
                                <div>
                                    <select id="is_active" name="is_active" class="select2 w-full @error('is_active') is-invalid @enderror" required>
                                        <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Aktif</option>
                                        <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Nonaktif</option>
                                    </select>
                                </div>
                                <p class="text-[11px] text-gray-500">
                                    Grup aktif akan muncul sebagai tab kategori di daftar ruangan dan formulir reservasi.
                                </p>
                                @error('is_active')
                                    <p class="text-xs text-destructive flex items-center gap-1 mt-1">
                                        <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Field 3: Deskripsi / Cakupan Area --}}
                            <div class="space-y-1.5">
                                <label for="description" class="text-xs font-semibold text-gray-700 flex items-center gap-1.5">
                                    <i data-lucide="info" class="w-3.5 h-3.5 text-gray-400"></i>
                                    Deskripsi &amp; Cakupan Lokasi (Opsional)
                                </label>
                                <textarea id="description" name="description" rows="3"
                                    class="w-full p-3 text-xs sm:text-sm bg-gray-50/70 hover:bg-gray-50 focus:bg-white border @error('description') border-red-400 ring-2 ring-red-400/20 @else border-gray-200/90 @enderror focus:border-[#FF4D00] rounded-xl focus:outline-none focus:ring-4 focus:ring-orange-500/10 shadow-2xs transition-all placeholder:text-gray-400"
                                    placeholder="Contoh: Menampung seluruh ruang rapat pimpinan dekanat, ruang sidang, dan perpustakaan di sayap timur...">{{ old('description') }}</textarea>
                                
                                {{-- Quick Description Adder --}}
                                <div class="pt-1 space-y-1">
                                    <div class="flex items-center gap-1 text-[11px] text-gray-500">
                                        <i data-lucide="plus-circle" class="w-3 h-3 text-emerald-600"></i>
                                        <span>Klik untuk menambah keterangan area:</span>
                                    </div>
                                    <div class="flex flex-wrap gap-1.5">
                                        <button type="button" class="preset-chip text-[10px]" onclick="appendDesc('Sayap Barat')">+ Sayap Barat</button>
                                        <button type="button" class="preset-chip text-[10px]" onclick="appendDesc('Sayap Timur')">+ Sayap Timur</button>
                                        <button type="button" class="preset-chip text-[10px]" onclick="appendDesc('Gedung Utama FEB')">+ Gedung Utama FEB</button>
                                        <button type="button" class="preset-chip text-[10px]" onclick="appendDesc('Akses Lift & Tangga')">+ Akses Lift &amp; Tangga</button>
                                        <button type="button" class="preset-chip text-[10px]" onclick="appendDesc('Khusus Ruang Dekanat')">+ Khusus Dekanat</button>
                                        <button type="button" class="preset-chip text-[10px]" onclick="appendDesc('Zona Perkuliahan Mahasiswa')">+ Perkuliahan Mahasiswa</button>
                                    </div>
                                </div>

                                @error('description')
                                    <p class="text-xs text-destructive flex items-center gap-1 mt-1">
                                        <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Notice Card --}}
                            <div class="p-4 rounded-2xl bg-orange-50/60 border border-orange-200/70 text-xs text-orange-950 flex items-start gap-3">
                                <div class="w-6 h-6 rounded-lg bg-orange-500 text-white flex items-center justify-center flex-shrink-0 mt-0.5 shadow-2xs">
                                    <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
                                </div>
                                <div class="space-y-0.5">
                                    <p class="font-bold">Integrasi Navigasi Daftar Ruangan &amp; Reservasi</p>
                                    <p class="text-orange-900/80 leading-relaxed text-[11px]">
                                        Grup yang dibuat akan langsung menjadi tab filter horisontal pada halaman reservasi ruangan serta memudahkan alokasi ruangan saat menambah master ruangan baru.
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Action Buttons inside Form Card --}}
                        <div class="flex flex-col-reverse sm:flex-row items-center justify-between gap-3 pt-6 mt-6 border-t border-gray-100">
                            <a href="{{ route('master.room-groups.index') }}"
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 text-xs sm:text-sm font-semibold text-gray-700 hover:text-gray-900 bg-white hover:bg-gray-100/90 rounded-xl border border-gray-200/90 shadow-2xs hover:shadow-xs transition-all active:scale-[0.98]">
                                <i data-lucide="x-circle" class="w-4 h-4 text-gray-500"></i>
                                <span>Batal</span>
                            </a>
                            <button type="submit" id="btn_submit_group"
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-8 py-3 text-xs sm:text-sm font-bold text-white bg-gradient-to-r from-[#FF4D00] to-[#ff6a00] hover:from-[#e04400] hover:to-[#eb5e00] rounded-xl shadow-[0_4px_16px_rgba(255,77,0,0.32)] hover:shadow-[0_6px_22px_rgba(255,77,0,0.42)] active:scale-[0.98] transition-all cursor-pointer">
                                <i data-lucide="check-circle" class="w-4 h-4"></i>
                                <span>Simpan Grup Ruangan</span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- RIGHT COLUMN: Live Simulator Studio Card (5 Cols) --}}
                <div class="lg:col-span-5 space-y-5 lg:sticky lg:top-6">
                    
                    {{-- Apple Warm Orange Preview Card --}}
                    <div class="simulator-orange-card p-6 space-y-5">
                        
                        {{-- Component 1: Visualisasi Tab Navigasi --}}
                        <div class="space-y-2">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-orange-950/70 flex items-center gap-1.5">
                                <i data-lucide="layers" class="w-3.5 h-3.5 text-[#FF4D00]"></i>
                                Simulasi Tab Kategori Ruangan
                            </span>

                            <div class="p-3 bg-white/80 backdrop-blur-md rounded-2xl border border-orange-200/60 shadow-xs space-y-2">
                                <div class="text-[10px] text-gray-400 font-medium">Bentuk Tab di Daftar Ruangan:</div>
                                
                                <div class="flex items-center gap-2 overflow-hidden py-1">
                                    {{-- Active Simulated Tab --}}
                                    <div id="simTabActive" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-gradient-to-r from-[#FF4D00] to-[#ff6a00] text-white text-xs font-bold shadow-md shadow-orange-500/20 flex-shrink-0 transition-all">
                                        <i data-lucide="folder" class="w-3.5 h-3.5"></i>
                                        <span id="token_tab_name">Grup Baru</span>
                                        <span class="inline-flex items-center justify-center px-1.5 py-0.5 rounded-full bg-white/20 text-[10px] font-mono">0</span>
                                    </div>

                                    {{-- Mock Sibling Tab --}}
                                    <div class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-gray-100/80 text-gray-600 text-xs font-semibold flex-shrink-0 opacity-70">
                                        <span>Semua Ruangan</span>
                                        <span class="px-1.5 py-0.5 rounded-full bg-gray-200 text-[10px]">12</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Component 2: Kartu Profil Grup Ruangan --}}
                        <div class="bg-white rounded-2xl p-5 border border-orange-100 shadow-xs space-y-4">
                            <div class="flex items-center gap-3">
                                <div class="w-11 h-11 rounded-2xl bg-orange-100/80 border border-orange-200 text-[#FF4D00] flex items-center justify-center flex-shrink-0 shadow-2xs">
                                    <i data-lucide="building-2" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Grup Ruangan</span>
                                    <h4 id="token_card_name" class="text-sm sm:text-base font-extrabold text-gray-900 leading-tight">
                                        Nama Grup Belum Diisi
                                    </h4>
                                </div>
                            </div>

                            <div class="pt-2 border-t border-gray-100">
                                <div class="text-[11px] font-semibold text-gray-500 mb-1">Cakupan Area / Deskripsi:</div>
                                <p id="token_card_desc" class="text-xs text-gray-700 leading-relaxed italic bg-gray-50/70 p-2.5 rounded-xl border border-gray-100">
                                    Belum ada catatan deskripsi area gedung.
                                </p>
                            </div>
                        </div>

                        {{-- Component 3: Info Ringkasan Relasi --}}
                        <div class="grid grid-cols-2 gap-3 text-xs">
                            <div class="p-3 rounded-xl bg-white/70 border border-orange-200/50">
                                <span class="text-[10px] font-semibold text-gray-500 block">Kategori</span>
                                <span class="font-bold text-gray-900 flex items-center gap-1 mt-0.5">
                                    <i data-lucide="grid" class="w-3 h-3 text-[#FF4D00]"></i>
                                    Blok Gedung / Lantai
                                </span>
                            </div>
                            <div class="p-3 rounded-xl bg-white/70 border border-orange-200/50">
                                <span class="text-[10px] font-semibold text-gray-500 block">Total Ruangan</span>
                                <span class="font-bold text-gray-900 flex items-center gap-1 mt-0.5">
                                    <i data-lucide="door-open" class="w-3 h-3 text-[#FF4D00]"></i>
                                    0 Ruangan Terkait
                                </span>
                            </div>
                        </div>

                        {{-- Helper Footnote --}}
                        <div class="flex items-center gap-2 text-[11px] text-orange-950/70 pt-1">
                            <i data-lucide="info" class="w-3.5 h-3.5 flex-shrink-0 text-orange-500"></i>
                            <span>Setelah disimpan, Anda dapat langsung menautkan ruangan ke grup ini.</span>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    // Live Simulator Synchronizer
    function updateGroupSimulator() {
        const nameInput = document.getElementById('name');
        const statusSelect = document.getElementById('is_active');
        const descInput = document.getElementById('description');

        const tokenTabName = document.getElementById('token_tab_name');
        const tokenCardName = document.getElementById('token_card_name');
        const tokenCardStatus = document.getElementById('token_card_status');
        const tokenCardDesc = document.getElementById('token_card_desc');
        const simTabActive = document.getElementById('simTabActive');

        // Update Group Name
        const nameVal = nameInput ? nameInput.value.trim() : '';
        if (nameVal) {
            tokenTabName.textContent = nameVal;
            tokenCardName.textContent = nameVal;
        } else {
            tokenTabName.textContent = 'Grup Baru';
            tokenCardName.textContent = 'Nama Grup Belum Diisi';
        }

        // Update Status
        const isActive = statusSelect ? statusSelect.value === '1' : true;
        if (tokenCardStatus) {
            tokenCardStatus.innerHTML = '';
        }
        if (simTabActive) {
            if (isActive) {
                simTabActive.className = 'inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-gradient-to-r from-[#FF4D00] to-[#ff6a00] text-white text-xs font-bold shadow-md shadow-orange-500/20 flex-shrink-0 transition-all';
            } else {
                simTabActive.className = 'inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-gray-200 text-gray-600 text-xs font-bold flex-shrink-0 transition-all opacity-80';
            }
        }

        // Update Description
        const descVal = descInput ? descInput.value.trim() : '';
        tokenCardDesc.textContent = descVal ? descVal : 'Belum ada catatan deskripsi area gedung.';

        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }

    // Quick Fill Helpers
    function fillGroupName(val) {
        const input = document.getElementById('name');
        if (input) {
            input.value = val;
            input.dispatchEvent(new Event('input'));
            animateToken('token_card_name');
            showToast(`Nama grup "${val}" dipilih`, 'success');
        }
    }

    function appendDesc(val) {
        const input = document.getElementById('description');
        if (input) {
            let current = input.value.trim();
            if (current.includes(val)) {
                showToast(`Keterangan "${val}" sudah tercantum`, 'info');
                return;
            }
            input.value = current ? `${current}, ${val}` : val;
            input.dispatchEvent(new Event('input'));
            showToast(`Keterangan "${val}" ditambahkan`, 'success');
        }
    }

    function animateToken(id) {
        const el = document.getElementById(id);
        if (el) {
            el.classList.remove('token-updated');
            void el.offsetWidth;
            el.classList.add('token-updated');
        }
    }

    // Toast Manager
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
        } else if (type === 'info') {
            toastIconWrap.className = 'w-7 h-7 rounded-xl flex items-center justify-center flex-shrink-0 bg-blue-100 text-blue-600';
            toastIcon.setAttribute('data-lucide', 'info');
        } else {
            toastIconWrap.className = 'w-7 h-7 rounded-xl flex items-center justify-center flex-shrink-0 bg-red-100 text-red-600';
            toastIcon.setAttribute('data-lucide', 'alert-circle');
        }

        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        toast.classList.add('show');
        clearTimeout(toastTimeout);
        toastTimeout = setTimeout(hideToast, 3000);
    }

    function hideToast() {
        const toast = document.getElementById('toastPopup');
        if (toast) toast.classList.remove('show');
    }

    // Initialize Event Listeners
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        // Initialize Select2 dropdowns with search
        if (typeof $ !== 'undefined' && $.fn.select2) {
            $('#is_active').select2({
                placeholder: 'Pilih Status Grup Ruangan',
                width: '100%',
                minimumResultsForSearch: 0
            }).on('change', function() {
                updateGroupSimulator();
            });

            // Auto focus search field on open
            $(document).on('select2:open', () => {
                const searchField = document.querySelector('.select2-container--open .select2-search__field');
                if (searchField) searchField.focus();
            });
        }

        const nameInput = document.getElementById('name');
        const statusSelect = document.getElementById('is_active');
        const descInput = document.getElementById('description');

        if (nameInput) nameInput.addEventListener('input', updateGroupSimulator);
        if (statusSelect) statusSelect.addEventListener('change', updateGroupSimulator);
        if (descInput) descInput.addEventListener('input', updateGroupSimulator);

        updateGroupSimulator();

        @if(session('success'))
            showToast("{{ session('success') }}", 'success');
        @endif
        @if(session('error'))
            showToast("{{ session('error') }}", 'error');
        @endif
    });
</script>
@endpush
