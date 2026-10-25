@extends('layouts.app')

@section('title', 'Edit Master Ruangan - ' . $room->name . ' - FEB UNJ')
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
                        <i data-lucide="pencil" class="w-5 h-5"></i>
                    </div>
                    <span>Edit Master Ruangan</span>
                </h1>
                <p class="text-xs sm:text-sm text-gray-500 mt-1 pl-13">
                    Perbarui informasi ruangan <strong>{{ $room->name }}</strong>, kapasitas kursi, alokasi grup gedung/lantai, atau kelengkapan fasilitas FEB UNJ.
                </p>
            </div>

            <div class="flex items-center gap-2.5 self-start sm:self-auto pl-13 sm:pl-0">
                <a href="{{ route('master.rooms.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 text-xs sm:text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-200 rounded-full shadow-2xs hover:shadow-xs transition-all active:scale-[0.98]">
                    <i data-lucide="arrow-left" class="w-4 h-4 text-gray-500"></i>
                    <span>Kembali ke Ruangan</span>
                </a>
            </div>
        </div>

        {{-- 2-Column macOS Studio Layout --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-7 items-start">
            
            {{-- LEFT COLUMN: Main Form & Danger Zone (7 Cols) --}}
            <div class="lg:col-span-7 space-y-6">

                {{-- Edit Form Card --}}
                <div class="mac-card p-6 sm:p-7">
                    <form id="room-edit-form" method="POST" action="{{ route('master.rooms.update', $room) }}" novalidate>
                        @csrf
                        @method('PUT')

                        <div class="flex items-center justify-between gap-3.5 mb-6 pb-4 border-b border-gray-100">
                            <div class="flex items-center gap-3.5">
                                 <div class="mac-step-badge">
                                     <i data-lucide="door-open" class="w-4 h-4 text-[#FF4D00]"></i>
                                 </div>
                                 <div>
                                     <h3 class="text-base font-bold text-gray-900 tracking-tight">
                                         Perbarui Informasi Ruangan
                                     </h3>
                                     <p class="text-xs text-gray-500 mt-0.5">
                                         Sesuaikan nama, kapasitas, penempatan grup, serta status aktif ruangan.
                                     </p>
                                 </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold {{ $room->is_active ? 'bg-emerald-100/90 text-emerald-800 border border-emerald-200' : 'bg-gray-100 text-gray-700 border border-gray-200' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $room->is_active ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                    {{ $room->is_active ? 'Ruangan Aktif' : 'Ruangan Nonaktif' }}
                                </span>
                                <span class="hidden sm:inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-xs font-semibold bg-gray-100 text-gray-600 border border-gray-200">
                                    <i data-lucide="hash" class="w-3.5 h-3.5 text-orange-500"></i>
                                    ID #{{ $room->id }}
                                </span>
                            </div>
                        </div>

                        <div class="space-y-5">
                            {{-- Field 1: Nama Ruangan --}}
                            <div class="space-y-1.5">
                                <label for="name" class="text-xs font-semibold text-gray-700 flex items-center gap-1.5">
                                    <i data-lucide="box" class="w-3.5 h-3.5 text-gray-400"></i>
                                    Nama Ruangan <span class="text-destructive">*</span>
                                </label>
                                <div class="relative group">
                                    <input type="text" name="name" id="name" required value="{{ old('name', $room->name) }}"
                                        class="w-full h-11 px-3.5 text-xs sm:text-sm bg-gray-50/70 hover:bg-gray-50 focus:bg-white border @error('name') border-red-400 ring-2 ring-red-400/20 @else border-gray-200/90 @enderror focus:border-[#FF4D00] rounded-xl focus:outline-none focus:ring-4 focus:ring-orange-500/10 shadow-2xs transition-all font-medium placeholder:text-gray-400"
                                        placeholder="Misal: Ruang Sidang Utama Gd M"
                                        autocomplete="off">
                                </div>

                                {{-- Quick Presets for Room Name --}}
                                <div class="pt-1.5 space-y-1.5">
                                    <div class="flex items-center gap-1 text-[11px] text-gray-500">
                                        <i data-lucide="sparkles" class="w-3 h-3 text-amber-500"></i>
                                        <span>Pintasan Nama Cepat:</span>
                                    </div>
                                    <div class="flex flex-wrap gap-1.5">
                                        <button type="button" class="preset-chip" onclick="fillRoomName('Ruang Sidang Utama')">Ruang Sidang Utama</button>
                                        <button type="button" class="preset-chip" onclick="fillRoomName('Ruang Rapat Senat')">Ruang Rapat Senat</button>
                                        <button type="button" class="preset-chip" onclick="fillRoomName('Lab Komputer Akuntansi')">Lab Komputer</button>
                                        <button type="button" class="preset-chip" onclick="fillRoomName('Auditorium Gd M')">Auditorium Gd M</button>
                                        <button type="button" class="preset-chip" onclick="fillRoomName('Ruang Kelas 301')">Ruang Kelas 301</button>
                                    </div>
                                </div>

                                @error('name')
                                    <p class="text-xs text-destructive flex items-center gap-1 mt-1">
                                        <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Field 2: Grup Ruangan (Lantai / Gedung) --}}
                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <label for="room_group_id" class="text-xs font-semibold text-gray-700 flex items-center gap-1.5">
                                        <i data-lucide="folder" class="w-3.5 h-3.5 text-gray-400"></i>
                                        Grup Ruangan / Lokasi Gedung
                                    </label>
                                    <a href="{{ route('master.room-groups.create') }}" target="_blank"
                                        class="text-[11px] font-semibold text-[#FF4D00] hover:underline flex items-center gap-1">
                                        <i data-lucide="plus" class="w-3 h-3"></i>
                                        <span>Buat Grup Baru</span>
                                    </a>
                                </div>
                                <div>
                                    <select id="room_group_id" name="room_group_id" class="select2 w-full @error('room_group_id') is-invalid @enderror">
                                        <option value="">-- Tanpa Grup (Lainnya) --</option>
                                        @foreach($roomGroups as $group)
                                            <option value="{{ $group->id }}" data-name="{{ $group->name }}" {{ old('room_group_id', $room->room_group_id) == $group->id ? 'selected' : '' }}>
                                                {{ $group->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <p class="text-[11px] text-gray-500">
                                    Pilih lantai atau blok gedung untuk mempermudah pemilahan saat reservasi dan tampilan public display.
                                </p>
                                @error('room_group_id')
                                    <p class="text-xs text-destructive flex items-center gap-1 mt-1">
                                        <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Field 3: Kapasitas Kursi & Status Ruangan (Grid 2 Kolom) --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                {{-- Kapasitas Kursi --}}
                                <div class="space-y-1.5">
                                    <label for="capacity" class="text-xs font-semibold text-gray-700 flex items-center gap-1.5">
                                        <i data-lucide="users" class="w-3.5 h-3.5 text-gray-400"></i>
                                        Kapasitas (Orang/Kursi)
                                    </label>
                                    <input type="number" name="capacity" id="capacity" min="1" value="{{ old('capacity', $room->capacity) }}"
                                        class="w-full h-11 px-3.5 text-xs sm:text-sm font-mono bg-gray-50/70 hover:bg-gray-50 focus:bg-white border @error('capacity') border-red-400 ring-2 ring-red-400/20 @else border-gray-200/90 @enderror focus:border-[#FF4D00] rounded-xl focus:outline-none focus:ring-4 focus:ring-orange-500/10 shadow-2xs transition-all placeholder:text-gray-400"
                                        placeholder="Misal: 40">
                                    
                                    {{-- Quick Capacity Chips --}}
                                    <div class="pt-1 flex flex-wrap gap-1">
                                        <button type="button" class="preset-chip text-[10px]" onclick="fillCapacity(15)">15</button>
                                        <button type="button" class="preset-chip text-[10px]" onclick="fillCapacity(30)">30</button>
                                        <button type="button" class="preset-chip text-[10px]" onclick="fillCapacity(50)">50</button>
                                        <button type="button" class="preset-chip text-[10px]" onclick="fillCapacity(80)">80</button>
                                        <button type="button" class="preset-chip text-[10px]" onclick="fillCapacity(150)">150</button>
                                    </div>

                                    @error('capacity')
                                        <p class="text-xs text-destructive flex items-center gap-1 mt-1">
                                            <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                {{-- Status Ketersediaan --}}
                                <div class="space-y-1.5">
                                    <label for="is_active" class="text-xs font-semibold text-gray-700 flex items-center gap-1.5">
                                        <i data-lucide="activity" class="w-3.5 h-3.5 text-gray-400"></i>
                                        Status Ruangan <span class="text-destructive">*</span>
                                    </label>
                                    <div>
                                        <select id="is_active" name="is_active" class="select2 w-full @error('is_active') is-invalid @enderror" required>
                                            <option value="1" {{ old('is_active', $room->is_active ? '1' : '0') == '1' ? 'selected' : '' }}>Aktif (Tersedia Dipinjam)</option>
                                            <option value="0" {{ old('is_active', $room->is_active ? '1' : '0') == '0' ? 'selected' : '' }}>Nonaktif (Renovasi / Tertutup)</option>
                                        </select>
                                    </div>
                                    <p class="text-[11px] text-gray-500">
                                        Ruangan nonaktif tidak akan ditampilkan pada peminjaman baru.
                                    </p>
                                    @error('is_active')
                                        <p class="text-xs text-destructive flex items-center gap-1 mt-1">
                                            <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Field 4: Deskripsi & Fasilitas Ruangan --}}
                            <div class="space-y-1.5">
                                <label for="description" class="text-xs font-semibold text-gray-700 flex items-center gap-1.5">
                                    <i data-lucide="info" class="w-3.5 h-3.5 text-gray-400"></i>
                                    Deskripsi &amp; Fasilitas Lengkap (Opsional)
                                </label>
                                <textarea id="description" name="description" rows="3"
                                    class="w-full p-3 text-xs sm:text-sm bg-gray-50/70 hover:bg-gray-50 focus:bg-white border @error('description') border-red-400 ring-2 ring-red-400/20 @else border-gray-200/90 @enderror focus:border-[#FF4D00] rounded-xl focus:outline-none focus:ring-4 focus:ring-orange-500/10 shadow-2xs transition-all placeholder:text-gray-400"
                                    placeholder="Contoh: Proyektor HD, AC Central, Sound System & 2 Mic, Podium, Papan Tulis, Smart TV">{{ old('description', $room->description) }}</textarea>
                                
                                {{-- Quick Facilities Adder --}}
                                <div class="pt-1 space-y-1">
                                    <div class="flex items-center gap-1 text-[11px] text-gray-500">
                                        <i data-lucide="plus-circle" class="w-3 h-3 text-emerald-600"></i>
                                        <span>Klik untuk menambah fasilitas:</span>
                                    </div>
                                    <div class="flex flex-wrap gap-1.5">
                                        <button type="button" class="preset-chip text-[10px]" onclick="appendFacility('Proyektor HD')">+ Proyektor HD</button>
                                        <button type="button" class="preset-chip text-[10px]" onclick="appendFacility('AC Central')">+ AC Central</button>
                                        <button type="button" class="preset-chip text-[10px]" onclick="appendFacility('Sound & Mic Wireless')">+ Sound &amp; Mic</button>
                                        <button type="button" class="preset-chip text-[10px]" onclick="appendFacility('Smart TV 65 Inch')">+ Smart TV</button>
                                        <button type="button" class="preset-chip text-[10px]" onclick="appendFacility('Kamera Video Conference')">+ Video Conf</button>
                                        <button type="button" class="preset-chip text-[10px]" onclick="appendFacility('Whiteboard & Spidol')">+ Whiteboard</button>
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
                                    <p class="font-bold">Sinkronisasi Jadwal &amp; Peminjaman Aktif</p>
                                    <p class="text-orange-900/80 leading-relaxed text-[11px]">
                                        Perubahan nama atau fasilitas ruangan akan langsung terdistribusi ke seluruh jadwal peminjaman yang terdaftar pada sistem FEB UNJ.
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Action Buttons inside Form Card --}}
                        <div class="flex flex-col-reverse sm:flex-row items-center justify-between gap-3 pt-6 mt-6 border-t border-gray-100">
                            <a href="{{ route('master.rooms.index') }}"
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 text-xs sm:text-sm font-semibold text-gray-700 hover:text-gray-900 bg-white hover:bg-gray-100/90 rounded-xl border border-gray-200/90 shadow-2xs hover:shadow-xs transition-all active:scale-[0.98]">
                                <i data-lucide="x-circle" class="w-4 h-4 text-gray-500"></i>
                                <span>Batal</span>
                            </a>
                            <button type="submit" id="btn_submit_edit_room"
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-8 py-3 text-xs sm:text-sm font-bold text-white bg-gradient-to-r from-[#FF4D00] to-[#ff6a00] hover:from-[#e04400] hover:to-[#eb5e00] rounded-xl shadow-[0_4px_16px_rgba(255,77,0,0.32)] hover:shadow-[0_6px_22px_rgba(255,77,0,0.42)] active:scale-[0.98] transition-all cursor-pointer">
                                <i data-lucide="check-circle" class="w-4 h-4"></i>
                                <span>Simpan Perubahan</span>
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Administrative / Danger Zone Section --}}
                @if (auth()->user()->isAdmin())
                    <div class="mac-card p-5 border-rose-200/80 bg-gradient-to-r from-rose-50/60 to-orange-50/30">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-xl bg-rose-500/10 text-rose-700 flex items-center justify-center">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </div>
                                    <h4 class="text-sm font-bold text-gray-900">Hapus Ruangan dari Sistem</h4>
                                </div>
                                <p class="text-xs text-gray-600 leading-relaxed max-w-xl">
                                    Data master ruangan akan dihapus dari sistem FEB UNJ. Jika ruangan memiliki histori peminjaman, sistem akan memblokir penghapusan demi menjaga integritas data reservasi.
                                </p>
                            </div>
                            <button type="button" 
                                    onclick="openDeleteModal()"
                                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 text-xs sm:text-sm font-bold text-rose-800 bg-rose-100 hover:bg-rose-200/90 border border-rose-300 rounded-xl transition-all active:scale-[0.98] cursor-pointer shrink-0">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                <span>Hapus Ruangan</span>
                            </button>
                        </div>
                    </div>
                @endif

            </div>

            {{-- RIGHT COLUMN: Live Simulator & Metadata Inspector (5 Cols, Sticky) --}}
            <div class="lg:col-span-5 space-y-5 lg:sticky lg:top-20">

                {{-- 🌟 CARD 1: Live Studio Inspector (Apple Studio Style) --}}
                <div class="simulator-orange-card p-5 sm:p-6">
                    <div class="flex items-center justify-between mb-4 pb-3 border-b border-orange-200/80">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-xl bg-orange-500 text-white flex items-center justify-center shadow-xs">
                                <i data-lucide="monitor" class="w-4 h-4"></i>
                            </div>
                            <span class="text-xs font-extrabold text-gray-900 tracking-tight">Pratinjau Kartu Ruangan FEB</span>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-orange-100/90 text-orange-800 border border-orange-300/80">
                            <span class="w-1.5 h-1.5 rounded-full bg-orange-500 animate-pulse"></span>
                            Live Sync
                        </span>
                    </div>

                    {{-- Simulated Official Room Card --}}
                    <div class="p-4 rounded-2xl bg-white border-2 border-orange-300/90 mb-4 shadow-xs">
                        <div class="flex items-center justify-between text-[11px] font-semibold text-gray-500 mb-2.5">
                            <span>Tampilan Terkini Display TV</span>
                            <span id="sim_status_badge" class="font-bold px-2 py-0.5 rounded-md text-[10px] {{ $room->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-700 border border-slate-200' }}">
                                {{ $room->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>
                        
                        {{-- Card Detail --}}
                        <div class="p-3.5 rounded-xl bg-gray-50/70 border border-gray-200/80 text-left space-y-3">
                            <div class="flex items-center gap-3">
                                <div id="sim_avatar" class="w-10 h-10 rounded-xl bg-gradient-to-br from-orange-500 to-amber-500 text-white font-bold text-xs flex items-center justify-center shadow-xs flex-shrink-0">
                                    <i data-lucide="door-open" class="w-5 h-5"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p id="sim_name_display" class="text-sm font-bold text-gray-900 truncate">
                                        {{ $room->name }}
                                    </p>
                                    <p id="sim_group_display" class="text-[11px] text-gray-500 flex items-center gap-1">
                                        <i data-lucide="folder" class="w-3 h-3 text-orange-500"></i>
                                        <span>{{ $room->roomGroup ? $room->roomGroup->name : 'Tanpa Grup Ruangan' }}</span>
                                    </p>
                                </div>
                            </div>

                            {{-- Capacity & Facilities Breakdown --}}
                            <div class="pt-2 border-t border-gray-200/70 space-y-2 text-xs">
                                <div class="flex items-center justify-between bg-white p-2 rounded-lg border border-gray-200/60">
                                    <span class="text-gray-500 flex items-center gap-1.5">
                                        <i data-lucide="users" class="w-3.5 h-3.5 text-gray-400"></i>
                                        Kapasitas Kursi:
                                    </span>
                                    <span id="sim_capacity_display" class="font-mono font-extrabold text-orange-600">
                                        {{ $room->capacity ? $room->capacity . ' Kursi / Orang' : '- Kursi' }}
                                    </span>
                                </div>

                                <div class="p-2.5 rounded-lg bg-white border border-gray-200/60 space-y-1">
                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Fasilitas &amp; Catatan:</span>
                                    <p id="sim_desc_display" class="text-[11px] text-gray-600 line-clamp-2 leading-relaxed">
                                        {{ $room->description ?: 'Belum ada catatan fasilitas ruangan.' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Token Breakdown Pills --}}
                    <div class="space-y-2 text-xs">
                        {{-- Token Nama --}}
                        <div class="flex items-center justify-between py-2 px-3 rounded-xl bg-white/90 border border-orange-200/70 shadow-2xs">
                            <span class="text-gray-600 font-medium flex items-center gap-2">
                                <i data-lucide="box" class="w-3.5 h-3.5 text-orange-500"></i>
                                Nama Ruangan
                            </span>
                            <span id="token_name" class="font-bold text-gray-900 truncate max-w-[150px]">
                                {{ $room->name }}
                            </span>
                        </div>

                        {{-- Token Grup --}}
                        <div class="flex items-center justify-between py-2 px-3 rounded-xl bg-white/90 border border-orange-200/70 shadow-2xs">
                            <span class="text-gray-600 font-medium flex items-center gap-2">
                                <i data-lucide="folder" class="w-3.5 h-3.5 text-orange-500"></i>
                                Grup Lokasi
                            </span>
                            <span id="token_group" class="font-bold text-gray-900 truncate max-w-[150px]">
                                {{ $room->roomGroup ? $room->roomGroup->name : 'Tanpa Grup' }}
                            </span>
                        </div>

                        {{-- Token Kapasitas --}}
                        <div class="flex items-center justify-between py-2 px-3 rounded-xl bg-white/90 border border-orange-200/70 shadow-2xs">
                            <span class="text-gray-600 font-medium flex items-center gap-2">
                                <i data-lucide="users" class="w-3.5 h-3.5 text-orange-500"></i>
                                Daya Tampung
                            </span>
                            <span id="token_capacity" class="font-bold text-gray-900 font-mono">
                                {{ $room->capacity ? $room->capacity . ' Orang' : '-' }}
                            </span>
                        </div>

                        {{-- Token Status --}}
                        <div class="flex items-center justify-between py-2 px-3 rounded-xl bg-white/90 border border-orange-200/70 shadow-2xs">
                            <span class="text-gray-600 font-medium flex items-center gap-2">
                                <i data-lucide="activity" class="w-3.5 h-3.5 text-orange-500"></i>
                                Status
                            </span>
                            <span id="token_status" class="font-bold {{ $room->is_active ? 'text-emerald-700' : 'text-slate-600' }}">
                                {{ $room->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- 🌟 CARD 2: Detail Metadata & Riwayat Reservasi --}}
                <div class="mac-card p-5 space-y-3.5">
                    <div class="flex items-center gap-2.5 pb-2.5 border-b border-gray-100">
                        <div class="w-7 h-7 rounded-lg bg-orange-100 text-[#FF4D00] flex items-center justify-center flex-shrink-0 shadow-2xs">
                            <i data-lucide="database" class="w-4 h-4"></i>
                        </div>
                        <h4 class="text-xs font-bold text-gray-900 uppercase tracking-wider">
                            Metadata &amp; Statistik Ruangan
                        </h4>
                    </div>

                    <div class="space-y-2 text-xs">
                        <div class="flex items-center justify-between py-1.5 border-b border-gray-50">
                            <span class="text-gray-500">Dibuat Pada:</span>
                            <span class="font-medium text-gray-800">
                                {{ $room->created_at ? $room->created_at->translatedFormat('d M Y, H:i') : '-' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between py-1.5 border-b border-gray-50">
                            <span class="text-gray-500">Pembaruan Terakhir:</span>
                            <span class="font-medium text-gray-800">
                                {{ $room->updated_at ? $room->updated_at->diffForHumans() : '-' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between py-1.5">
                            <span class="text-gray-500">Total Histori Agenda:</span>
                            <span class="inline-flex items-center gap-1 font-bold text-orange-600 bg-orange-50 px-2 py-0.5 rounded-md border border-orange-200/60">
                                <i data-lucide="calendar" class="w-3 h-3 text-orange-500"></i>
                                {{ $room->reservations()->count() }} Agenda
                            </span>
                        </div>
                    </div>
                </div>

            </div>

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
                Apakah Anda yakin ingin menghapus ruangan <strong class="text-gray-900">{{ $room->name }}</strong> ({{ $room->capacity ?? '-' }} Kursi)?
            </p>

            <div class="mt-3 p-3 rounded-xl bg-amber-50 border border-amber-200/80 text-[11px] text-amber-800 flex items-start gap-2">
                <i data-lucide="info" class="w-4 h-4 text-amber-600 flex-shrink-0 mt-0.5"></i>
                <span>Pastikan ruangan ini tidak pernah digunakan dalam agenda peminjaman. Data yang sudah dihapus tidak dapat dipulihkan.</span>
            </div>

            <div class="mt-6 flex items-center justify-end gap-2.5">
                <button type="button" onclick="closeDeleteModal()"
                    class="px-4 py-2 text-xs sm:text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-all cursor-pointer">
                    Batal
                </button>
                <form method="POST" action="{{ route('master.rooms.destroy', $room) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="px-4.5 py-2 text-xs sm:text-sm font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl shadow-xs transition-all cursor-pointer">
                        Ya, Hapus Ruangan
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Live Synchronizer Manager
    function updateRoomSimulator() {
        const nameInput = document.getElementById('name');
        const groupSelect = document.getElementById('room_group_id');
        const capacityInput = document.getElementById('capacity');
        const statusSelect = document.getElementById('is_active');
        const descInput = document.getElementById('description');

        // Elements to update
        const simName = document.getElementById('sim_name_display');
        const simGroup = document.getElementById('sim_group_display');
        const simCapacity = document.getElementById('sim_capacity_display');
        const simStatusBadge = document.getElementById('sim_status_badge');
        const simDesc = document.getElementById('sim_desc_display');

        // Tokens
        const tokenName = document.getElementById('token_name');
        const tokenGroup = document.getElementById('token_group');
        const tokenCapacity = document.getElementById('token_capacity');
        const tokenStatus = document.getElementById('token_status');

        // Update Name
        const nameVal = nameInput ? nameInput.value.trim() : '';
        if (nameVal) {
            simName.textContent = nameVal;
            tokenName.textContent = nameVal;
        } else {
            simName.textContent = 'Nama Ruangan';
            tokenName.textContent = '(Belum diisi)';
        }

        // Update Group
        let groupText = 'Tanpa Grup Ruangan';
        if (groupSelect && groupSelect.selectedIndex > 0) {
            const opt = groupSelect.options[groupSelect.selectedIndex];
            groupText = opt.getAttribute('data-name') || opt.text.trim();
        }
        simGroup.innerHTML = `<i data-lucide="folder" class="w-3 h-3 text-orange-500"></i><span>${groupText}</span>`;
        tokenGroup.textContent = groupText;

        // Update Capacity
        const capVal = capacityInput && capacityInput.value ? parseInt(capacityInput.value) : 0;
        const capFormatted = capVal > 0 ? `${capVal} Kursi / Orang` : '- Kursi';
        simCapacity.textContent = capFormatted;
        tokenCapacity.textContent = capVal > 0 ? `${capVal} Orang` : '-';

        // Update Status
        const isActive = statusSelect && statusSelect.value === '1';
        if (isActive) {
            simStatusBadge.className = 'font-bold px-2 py-0.5 rounded-md text-[10px] bg-emerald-50 text-emerald-700 border border-emerald-200';
            simStatusBadge.textContent = 'Aktif';
            tokenStatus.className = 'font-bold text-emerald-700';
            tokenStatus.textContent = 'Aktif';
        } else {
            simStatusBadge.className = 'font-bold px-2 py-0.5 rounded-md text-[10px] bg-slate-100 text-slate-700 border border-slate-200';
            simStatusBadge.textContent = 'Nonaktif';
            tokenStatus.className = 'font-bold text-slate-600';
            tokenStatus.textContent = 'Nonaktif';
        }

        // Update Description
        const descVal = descInput ? descInput.value.trim() : '';
        simDesc.textContent = descVal ? descVal : 'Belum ada catatan fasilitas ruangan.';

        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }

    // Quick Fill Helpers
    function fillRoomName(val) {
        const input = document.getElementById('name');
        if (input) {
            input.value = val;
            input.dispatchEvent(new Event('input'));
            animateToken('token_name');
            showToast(`Nama "${val}" dipilih`, 'success');
        }
    }

    function fillCapacity(val) {
        const input = document.getElementById('capacity');
        if (input) {
            input.value = val;
            input.dispatchEvent(new Event('input'));
            animateToken('token_capacity');
            showToast(`Kapasitas ${val} kursi dipilih`, 'success');
        }
    }

    function appendFacility(val) {
        const input = document.getElementById('description');
        if (input) {
            let current = input.value.trim();
            if (current.includes(val)) {
                showToast(`Fasilitas "${val}" sudah tercantum`, 'info');
                return;
            }
            input.value = current ? `${current}, ${val}` : val;
            input.dispatchEvent(new Event('input'));
            showToast(`Fasilitas "${val}" ditambahkan`, 'success');
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

    // Modal Manager
    function openDeleteModal() {
        const backdrop = document.getElementById('deleteModalBackdrop');
        if (backdrop) {
            backdrop.classList.add('show');
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }
    }

    function closeDeleteModal() {
        const backdrop = document.getElementById('deleteModalBackdrop');
        if (backdrop) backdrop.classList.remove('show');
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
            $('#room_group_id').select2({
                placeholder: '-- Tanpa Grup (Lainnya) --',
                allowClear: true,
                width: '100%',
                minimumResultsForSearch: 0
            }).on('change', function() {
                updateRoomSimulator();
            });

            $('#is_active').select2({
                placeholder: 'Pilih Status Ruangan',
                width: '100%',
                minimumResultsForSearch: 0
            }).on('change', function() {
                updateRoomSimulator();
            });

            // Auto focus search field on open
            $(document).on('select2:open', () => {
                const searchField = document.querySelector('.select2-container--open .select2-search__field');
                if (searchField) searchField.focus();
            });
        }

        const nameInput = document.getElementById('name');
        const groupSelect = document.getElementById('room_group_id');
        const capacityInput = document.getElementById('capacity');
        const statusSelect = document.getElementById('is_active');
        const descInput = document.getElementById('description');

        if (nameInput) nameInput.addEventListener('input', updateRoomSimulator);
        if (groupSelect) groupSelect.addEventListener('change', updateRoomSimulator);
        if (capacityInput) capacityInput.addEventListener('input', updateRoomSimulator);
        if (statusSelect) statusSelect.addEventListener('change', updateRoomSimulator);
        if (descInput) descInput.addEventListener('input', updateRoomSimulator);

        updateRoomSimulator();

        // Modal backdrop click
        document.addEventListener('click', function(e) {
            const backdrop = document.getElementById('deleteModalBackdrop');
            if (backdrop && e.target === backdrop) {
                closeDeleteModal();
            }
        });

        // Esc key to dismiss modal
        window.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeDeleteModal();
            }
        });

        @if(session('success'))
            showToast("{{ session('success') }}", 'success');
        @endif
        @if(session('error'))
            showToast("{{ session('error') }}", 'error');
        @endif
    });
</script>
@endpush
