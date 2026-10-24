@extends('layouts.app')

@section('title', 'Tambah Jenis Surat - FEB UNJ')
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

        /* Quick Code Preset Chips */
        .code-preset-chip {
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
        .code-preset-chip:hover {
            background: #fff7ed;
            border-color: #fed7aa;
            color: #c2410c;
            transform: translateY(-1px);
        }
        .code-preset-chip:active {
            transform: translateY(0);
        }

        /* Pulse glow animation on preview update */
        @keyframes tokenPulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.04); filter: drop-shadow(0 2px 8px rgba(255, 77, 0, 0.25)); }
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
                        <i data-lucide="file-plus-2" class="w-5 h-5"></i>
                    </div>
                    <span>Tambah Jenis Surat</span>
                </h1>
                <p class="text-xs sm:text-sm text-gray-500 mt-1 pl-13">
                    Definisikan jenis surat dinas baru, kode penomoran, serta konfigurasi keperluan surat FEB UNJ.
                </p>
            </div>

            <div class="flex items-center gap-2.5 self-start sm:self-auto pl-13 sm:pl-0">
                <a href="{{ route('master.letter-types.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 text-xs sm:text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-200 rounded-full shadow-2xs hover:shadow-xs transition-all active:scale-[0.98]">
                    <i data-lucide="arrow-left" class="w-4 h-4 text-gray-500"></i>
                    <span>Kembali ke Jenis Surat</span>
                </a>
            </div>
        </div>

        {{-- 2-Column macOS Studio Layout --}}
        <form id="letter-type-form" method="POST" action="{{ route('master.letter-types.store') }}" novalidate>
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-7 items-start">
                
                {{-- LEFT COLUMN: Main Form Card (7 Cols) --}}
                <div class="lg:col-span-7 space-y-6">

                    {{-- Form Card --}}
                    <div class="mac-card p-6 sm:p-7">
                        <div class="flex items-center gap-3.5 mb-6 pb-4 border-b border-gray-100">
                            <div class="mac-step-badge">
                                <i data-lucide="layers" class="w-4 h-4 text-[#FF4D00]"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-gray-900 tracking-tight">
                                    Informasi & Konfigurasi Jenis Surat
                                </h3>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    Lengkapi kode jenis, nama resmi, dan tentukan opsi keperluan surat mahasiswa.
                                </p>
                            </div>
                        </div>

                        <div class="space-y-5">
                            {{-- Field: Kode Jenis Surat --}}
                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <label for="code" class="text-xs font-semibold text-gray-700 flex items-center gap-1.5">
                                        <i data-lucide="hash" class="w-3.5 h-3.5 text-gray-400"></i>
                                        Kode Jenis Surat
                                        <span class="text-[10px] text-gray-400 font-normal">(Opsional)</span>
                                    </label>
                                    <span class="text-[10px] text-gray-400 font-mono">Format Huruf Kapital</span>
                                </div>
                                <div class="relative">
                                    <input type="text" 
                                           id="code" 
                                           name="code" 
                                           value="{{ old('code') }}" 
                                           class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-mono font-bold tracking-wider text-gray-900 placeholder:text-gray-400 placeholder:font-normal placeholder:tracking-normal focus:outline-none focus:border-[#FF4D00] focus:ring-4 focus:ring-orange-500/10 transition-all uppercase @error('code') !border-red-500 !ring-red-500/10 @enderror" 
                                           placeholder="Contoh: ST, SK, SU, UND"
                                           maxlength="50"
                                           autocomplete="off">
                                    <div class="absolute right-3 top-1/2 -translate-y-1/2">
                                        <span id="codeLengthBadge" class="text-[10px] font-mono font-semibold text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded">0/50</span>
                                    </div>
                                </div>

                                {{-- Quick Preset Chips for inspiration --}}
                                <div class="pt-1">
                                    <div class="text-[11px] text-gray-500 flex items-center gap-1.5 mb-1.5">
                                        <span>Pintasan Contoh Kode:</span>
                                    </div>
                                    <div class="flex flex-wrap gap-1.5">
                                        <button type="button" class="code-preset-chip" onclick="fillPreset('ST', 'Surat Tugas', true)">ST (Surat Tugas)</button>
                                        <button type="button" class="code-preset-chip" onclick="fillPreset('SK', 'Surat Keputusan', false)">SK (Surat Keputusan)</button>
                                        <button type="button" class="code-preset-chip" onclick="fillPreset('SU', 'Surat Umum', false)">SU (Surat Umum)</button>
                                        <button type="button" class="code-preset-chip" onclick="fillPreset('UND', 'Surat Undangan', false)">UND (Undangan)</button>
                                        <button type="button" class="code-preset-chip" onclick="fillPreset('SMW', 'SIPERMAWA', true)">SMW (SIPERMAWA)</button>
                                    </div>
                                </div>

                                <p class="text-[11px] text-gray-500">
                                    Kode singkatan unik untuk pengkategorian jenis surat dinas (opsional namun disarankan).
                                </p>
                                @error('code')
                                    <p class="text-xs text-destructive flex items-center gap-1 mt-1">
                                        <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Field: Nama Jenis Surat --}}
                            <div class="space-y-1.5">
                                <label for="name" class="text-xs font-semibold text-gray-700 flex items-center gap-1.5">
                                    <i data-lucide="file-text" class="w-3.5 h-3.5 text-gray-400"></i>
                                    Nama Jenis Surat <span class="text-destructive">*</span>
                                </label>
                                <input type="text" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name') }}" 
                                       class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-900 placeholder:text-gray-400 focus:outline-none focus:border-[#FF4D00] focus:ring-4 focus:ring-orange-500/10 transition-all @error('name') !border-red-500 !ring-red-500/10 @enderror" 
                                       placeholder="Contoh: Surat Tugas / Surat Keputusan Dekan"
                                       maxlength="150"
                                       required>
                                <p class="text-[11px] text-gray-500">Nama lengkap jenis surat yang akan tampil pada pilihan registrasi surat.</p>
                                @error('name')
                                    <p class="text-xs text-destructive flex items-center gap-1 mt-1">
                                        <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Field: Deskripsi (Opsional) --}}
                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <label for="description" class="text-xs font-semibold text-gray-700 flex items-center gap-1.5">
                                        <i data-lucide="align-left" class="w-3.5 h-3.5 text-gray-400"></i>
                                        Deskripsi Ruang Lingkup
                                    </label>
                                    <span class="text-[10px] text-gray-400">Opsional</span>
                                </div>
                                <textarea id="description" 
                                          name="description" 
                                          rows="3" 
                                          class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-900 placeholder:text-gray-400 focus:outline-none focus:border-[#FF4D00] focus:ring-4 focus:ring-orange-500/10 transition-all resize-none @error('description') !border-red-500 !ring-red-500/10 @enderror" 
                                          placeholder="Tambahkan catatan singkat peruntukan jenis surat ini (opsional)...">{{ old('description') }}</textarea>
                                @error('description')
                                    <p class="text-xs text-destructive flex items-center gap-1 mt-1">
                                        <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Field: Memerlukan Keperluan Surat (Apple iOS Style Toggle Switch) --}}
                            <div class="pt-2">
                                <div class="p-4 rounded-2xl bg-gray-50/80 border border-gray-200/80 hover:bg-gray-50 transition-colors">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="space-y-1">
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs font-bold text-gray-900">Keperluan Surat & Data Mahasiswa</span>
                                                <span id="purposeIndicatorBadge" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold {{ old('requires_purpose', false) ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-gray-100 text-gray-700 border border-gray-300' }}">
                                                    <span class="w-1.5 h-1.5 rounded-full {{ old('requires_purpose', false) ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                                    {{ old('requires_purpose', false) ? 'Diwajibkan' : 'Tidak Diperlukan' }}
                                                </span>
                                            </div>
                                            <p id="purposeDescriptionText" class="text-xs text-gray-500 leading-relaxed">
                                                {{ old('requires_purpose', false) ? 'Surat dengan jenis ini mewajibkan pengisian Keperluan Surat dan data Mahasiswa saat registrasi surat.' : 'Surat dengan jenis ini tidak memerlukan isian keperluan khusus atau nama mahasiswa.' }}
                                            </p>
                                        </div>

                                        {{-- iOS Switch Control --}}
                                        <label class="relative inline-flex items-center cursor-pointer flex-shrink-0 mt-0.5">
                                            <input type="hidden" name="requires_purpose" value="0">
                                            <input type="checkbox" 
                                                   id="requires_purpose" 
                                                   name="requires_purpose" 
                                                   value="1" 
                                                   {{ old('requires_purpose', false) ? 'checked' : '' }} 
                                                   class="sr-only peer"
                                                   onchange="handlePurposeToggle(this)">
                                            <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500 shadow-inner"></div>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            {{-- Field: Status Aktif (Apple iOS Style Toggle Switch) --}}
                            <div>
                                <div class="p-4 rounded-2xl bg-gray-50/80 border border-gray-200/80 hover:bg-gray-50 transition-colors">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="space-y-1">
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs font-bold text-gray-900">Status Jenis Surat</span>
                                                <span id="statusIndicatorBadge" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                    Aktif
                                                </span>
                                            </div>
                                            <p id="statusDescriptionText" class="text-xs text-gray-500 leading-relaxed">
                                                Jenis surat aktif dan langsung tersedia di menu pembuatan surat dinas baru.
                                            </p>
                                        </div>

                                        {{-- iOS Switch Control --}}
                                        <label class="relative inline-flex items-center cursor-pointer flex-shrink-0 mt-0.5">
                                            <input type="hidden" name="is_active" value="0">
                                            <input type="checkbox" 
                                                   id="is_active" 
                                                   name="is_active" 
                                                   value="1" 
                                                   {{ old('is_active', true) ? 'checked' : '' }} 
                                                   class="sr-only peer"
                                                   onchange="handleStatusToggle(this)">
                                            <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500 shadow-inner"></div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- macOS Bottom Action Dock --}}
                    <div class="mac-card p-5 flex flex-col-reverse sm:flex-row items-center justify-between gap-3 bg-gradient-to-r from-gray-50/70 to-white">
                        <a href="{{ route('master.letter-types.index') }}"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 text-xs sm:text-sm font-semibold text-gray-700 hover:text-gray-900 bg-white hover:bg-gray-100/90 rounded-xl border border-gray-200/90 shadow-2xs hover:shadow-xs transition-all active:scale-[0.98]">
                            <i data-lucide="x-circle" class="w-4 h-4 text-gray-500"></i>
                            <span>Batal</span>
                        </a>
                        <button type="submit" id="btn_submit_letter_type"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-8 py-3 text-xs sm:text-sm font-bold text-white bg-gradient-to-r from-[#FF4D00] to-[#ff6a00] hover:from-[#e04400] hover:to-[#eb5e00] rounded-xl shadow-[0_4px_16px_rgba(255,77,0,0.32)] hover:shadow-[0_6px_22px_rgba(255,77,0,0.42)] active:scale-[0.98] transition-all cursor-pointer">
                            <i data-lucide="check-circle" class="w-4 h-4"></i>
                            <span>Simpan Jenis Surat</span>
                        </button>
                    </div>

                </div>

                {{-- RIGHT COLUMN: Live Simulator & Inspector (5 Cols, Sticky) --}}
                <div class="lg:col-span-5 space-y-5 lg:sticky lg:top-20">

                    {{-- 🌟 CARD 1: Live Studio Inspector (Apple Studio Style) --}}
                    <div class="simulator-orange-card p-5 sm:p-6">
                        <div class="flex items-center justify-between mb-4 pb-3 border-b border-orange-200/80">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-xl bg-orange-500 text-white flex items-center justify-center shadow-xs">
                                    <i data-lucide="file-check" class="w-4 h-4"></i>
                                </div>
                                <span class="text-xs font-extrabold text-gray-900 tracking-tight">Pratinjau Jenis Surat</span>
                            </div>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-orange-100/90 text-orange-800 border border-orange-300/80">
                                <span class="w-1.5 h-1.5 rounded-full bg-orange-500 animate-pulse"></span>
                                Live Sync
                            </span>
                        </div>

                        {{-- Simulated Dropdown Option Box --}}
                        <div class="p-3.5 rounded-2xl bg-white border-2 border-orange-300/90 mb-4 shadow-xs">
                            <div class="flex items-center justify-between text-[11px] font-semibold text-gray-500 mb-1.5">
                                <span>Tampilan Opsi Registrasi Surat</span>
                                <span class="text-orange-600 font-bold bg-orange-50 px-2 py-0.5 rounded-md border border-orange-200/70 text-[10px]">
                                    Pilihan Form
                                </span>
                            </div>
                            
                            {{-- Simulated Item Capsule --}}
                            <div class="p-2.5 rounded-xl bg-gray-50 border border-gray-200/80 flex items-center gap-2.5">
                                <span id="sim_code_token" class="font-mono text-xs font-bold text-[#FF4D00] bg-orange-100/80 px-2 py-1 rounded-md border border-orange-200 flex-shrink-0">
                                    KODE
                                </span>
                                <span id="sim_name_display" class="text-xs sm:text-sm font-bold text-gray-900 truncate">
                                    Nama Jenis Surat
                                </span>
                            </div>

                            <div class="mt-2.5 pt-2 border-t border-gray-100 text-[11px] text-gray-600 flex items-center gap-1.5">
                                <i data-lucide="info" class="w-3.5 h-3.5 text-orange-500 flex-shrink-0"></i>
                                <span>Akan tampil di dropdown jenis surat bagi operator administrasi.</span>
                            </div>
                        </div>

                        {{-- Token Breakdown Pills --}}
                        <div class="space-y-2 text-xs">
                            {{-- Token Kode --}}
                            <div class="flex items-center justify-between py-2 px-3 rounded-xl bg-white/90 border border-orange-200/70 shadow-2xs">
                                <span class="text-gray-600 font-medium flex items-center gap-2">
                                    <i data-lucide="tag" class="w-3.5 h-3.5 text-orange-500"></i>
                                    Kode Singkatan
                                </span>
                                <span id="sim_token_code_badge" class="font-mono font-bold text-orange-700 px-2 py-0.5 rounded-md bg-orange-50 border border-orange-200">
                                    -
                                </span>
                            </div>

                            {{-- Token Keperluan Surat --}}
                            <div class="flex items-center justify-between py-2 px-3 rounded-xl bg-white/90 border border-orange-200/70 shadow-2xs">
                                <span class="text-gray-600 font-medium flex items-center gap-2">
                                    <i data-lucide="user-check" class="w-3.5 h-3.5 text-blue-500"></i>
                                    Keperluan & Mahasiswa
                                </span>
                                <span id="sim_token_purpose_badge" class="font-semibold text-gray-600 px-2 py-0.5 rounded-md bg-gray-100 border border-gray-300">
                                    Tidak Diperlukan
                                </span>
                            </div>

                            {{-- Token Status --}}
                            <div class="flex items-center justify-between py-2 px-3 rounded-xl bg-white/90 border border-orange-200/70 shadow-2xs">
                                <span class="text-gray-600 font-medium flex items-center gap-2">
                                    <i data-lucide="activity" class="w-3.5 h-3.5 text-emerald-500"></i>
                                    Status Penggunaan
                                </span>
                                <span id="sim_token_status_badge" class="font-bold text-emerald-700 px-2 py-0.5 rounded-md bg-emerald-50 border border-emerald-200">
                                    Aktif
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- 🌟 CARD 2: Pedoman & Konfigurasi Sistem --}}
                    <div class="mac-card p-5 sm:p-6 bg-gradient-to-br from-white to-amber-50/20">
                        <div class="flex items-center gap-2 mb-3 text-xs font-bold text-gray-900 uppercase tracking-wider">
                            <i data-lucide="sparkles" class="w-4 h-4 text-amber-500"></i>
                            Panduan Konfigurasi Jenis Surat
                        </div>
                        <ul class="text-xs text-gray-600 space-y-2.5">
                            <li class="flex items-start gap-2">
                                <i data-lucide="check" class="w-3.5 h-3.5 text-emerald-600 flex-shrink-0 mt-0.5"></i>
                                <span><strong>Kode Singkatan:</strong> Disarankan menggunakan 2–4 huruf kapital (misal: <code class="font-mono text-gray-800 bg-gray-100 px-1 py-0.5 rounded">ST</code>, <code class="font-mono text-gray-800 bg-gray-100 px-1 py-0.5 rounded">SK</code>) untuk kemudahan filter.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i data-lucide="check" class="w-3.5 h-3.5 text-emerald-600 flex-shrink-0 mt-0.5"></i>
                                <span><strong>Keperluan & Mahasiswa:</strong> Aktifkan jika surat membutuhkan data peruntukan dan nama lengkap mahasiswa saat diterbitkan.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i data-lucide="check" class="w-3.5 h-3.5 text-emerald-600 flex-shrink-0 mt-0.5"></i>
                                <span><strong>Status Aktif:</strong> Jenis surat yang aktif akan otomatis muncul dalam formulir pendaftaran surat dinas baru.</span>
                            </li>
                        </ul>
                    </div>

                </div>

            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script src="https://unpkg.com/lucide@latest"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        const codeInput = document.getElementById('code');
        const nameInput = document.getElementById('name');
        const codeLengthBadge = document.getElementById('codeLengthBadge');
        const simCodeToken = document.getElementById('sim_code_token');
        const simNameDisplay = document.getElementById('sim_name_display');
        const simTokenCodeBadge = document.getElementById('sim_token_code_badge');
        const form = document.getElementById('letter-type-form');

        // Update Live Simulation from inputs
        function updateLivePreview() {
            let codeVal = codeInput.value.trim().toUpperCase();
            let nameVal = nameInput.value.trim();

            // Auto uppercase in field
            if (codeInput.value !== codeVal) {
                const start = codeInput.selectionStart;
                const end = codeInput.selectionEnd;
                codeInput.value = codeVal;
                codeInput.setSelectionRange(start, end);
            }

            codeLengthBadge.textContent = `${codeVal.length}/50`;

            if (codeVal.length > 0) {
                simCodeToken.textContent = codeVal;
                simTokenCodeBadge.textContent = codeVal;
                simCodeToken.classList.add('token-updated');
                setTimeout(() => simCodeToken.classList.remove('token-updated'), 350);
            } else {
                simCodeToken.textContent = 'KODE';
                simTokenCodeBadge.textContent = '-';
            }

            if (nameVal.length > 0) {
                simNameDisplay.textContent = nameVal;
            } else {
                simNameDisplay.textContent = 'Nama Jenis Surat';
            }
        }

        codeInput.addEventListener('input', updateLivePreview);
        nameInput.addEventListener('input', updateLivePreview);

        // Initial preview call
        updateLivePreview();

        // Form validation feedback
        form.addEventListener('submit', function(e) {
            const name = nameInput.value.trim();

            if (!name) {
                e.preventDefault();
                showToast('Nama jenis surat wajib diisi.', 'error');
                nameInput.focus();
                return;
            }
        });
    });

    // Preset Chip Click Helper
    function fillPreset(code, name, requiresPurpose) {
        const codeInput = document.getElementById('code');
        const nameInput = document.getElementById('name');
        const purposeCheckbox = document.getElementById('requires_purpose');
        
        codeInput.value = code;
        if (!nameInput.value.trim()) {
            nameInput.value = name;
        }
        if (purposeCheckbox) {
            purposeCheckbox.checked = requiresPurpose;
            handlePurposeToggle(purposeCheckbox);
        }
        
        // Trigger input event
        codeInput.dispatchEvent(new Event('input'));
        nameInput.dispatchEvent(new Event('input'));
        
        showToast(`Preset jenis "${name}" (${code}) dipilih`, 'success');
        nameInput.focus();
    }

    // iOS Purpose Toggle Handler
    function handlePurposeToggle(checkbox) {
        const isChecked = checkbox.checked;
        const purposeIndicatorBadge = document.getElementById('purposeIndicatorBadge');
        const purposeDescriptionText = document.getElementById('purposeDescriptionText');
        const simTokenPurposeBadge = document.getElementById('sim_token_purpose_badge');

        if (isChecked) {
            purposeIndicatorBadge.className = 'inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200';
            purposeIndicatorBadge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Diwajibkan';
            purposeDescriptionText.textContent = 'Surat dengan jenis ini mewajibkan pengisian Keperluan Surat dan data Mahasiswa saat registrasi surat.';

            simTokenPurposeBadge.className = 'font-bold text-emerald-700 px-2 py-0.5 rounded-md bg-emerald-50 border border-emerald-200';
            simTokenPurposeBadge.textContent = 'Wajib Diisi';
        } else {
            purposeIndicatorBadge.className = 'inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-700 border border-gray-300';
            purposeIndicatorBadge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> Tidak Diperlukan';
            purposeDescriptionText.textContent = 'Surat dengan jenis ini tidak memerlukan isian keperluan khusus atau nama mahasiswa.';

            simTokenPurposeBadge.className = 'font-semibold text-gray-600 px-2 py-0.5 rounded-md bg-gray-100 border border-gray-300';
            simTokenPurposeBadge.textContent = 'Tidak Diperlukan';
        }
    }

    // iOS Status Toggle Handler
    function handleStatusToggle(checkbox) {
        const isChecked = checkbox.checked;
        const statusIndicatorBadge = document.getElementById('statusIndicatorBadge');
        const statusDescriptionText = document.getElementById('statusDescriptionText');
        const simTokenStatusBadge = document.getElementById('sim_token_status_badge');

        if (isChecked) {
            statusIndicatorBadge.className = 'inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200';
            statusIndicatorBadge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif';
            statusDescriptionText.textContent = 'Jenis surat aktif dan langsung tersedia di menu pembuatan surat dinas baru.';

            simTokenStatusBadge.className = 'font-bold text-emerald-700 px-2 py-0.5 rounded-md bg-emerald-50 border border-emerald-200';
            simTokenStatusBadge.textContent = 'Aktif';
        } else {
            statusIndicatorBadge.className = 'inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-700 border border-gray-300';
            statusIndicatorBadge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> Nonaktif';
            statusDescriptionText.textContent = 'Jenis surat dinonaktifkan dan disembunyikan dari pilihan registrasi surat baru.';

            simTokenStatusBadge.className = 'font-bold text-gray-600 px-2 py-0.5 rounded-md bg-gray-100 border border-gray-300';
            simTokenStatusBadge.textContent = 'Nonaktif';
        }
    }

    // Toast Notification System
    let toastTimeout;
    function showToast(msg, type = 'error') {
        const toast = document.getElementById('toastPopup');
        const toastMsg = document.getElementById('toastMessage');
        const toastIconWrap = document.getElementById('toastIconWrap');
        const toastIcon = document.getElementById('toastIcon');

        if (!toast) return;

        toastMsg.textContent = msg;

        if (type === 'success') {
            toastIconWrap.className = 'w-7 h-7 rounded-xl flex items-center justify-center flex-shrink-0 bg-emerald-100 text-emerald-600';
            toastIcon.setAttribute('data-lucide', 'check-circle');
        } else {
            toastIconWrap.className = 'w-7 h-7 rounded-xl flex items-center justify-center flex-shrink-0 bg-red-100 text-red-600';
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
