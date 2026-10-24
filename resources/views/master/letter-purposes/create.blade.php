@extends('layouts.app')

@section('title', 'Tambah Keperluan Surat - FEB UNJ')
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
        .purpose-preset-chip {
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
        .purpose-preset-chip:hover {
            background: #fff7ed;
            border-color: #fed7aa;
            color: #c2410c;
            transform: translateY(-1px);
        }
        .purpose-preset-chip:active {
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
                        <i data-lucide="bookmark-plus" class="w-5 h-5"></i>
                    </div>
                    <span>Tambah Keperluan Surat</span>
                </h1>
                <p class="text-xs sm:text-sm text-gray-500 mt-1 pl-13">
                    Definisikan opsi keperluan baru untuk kebutuhan persuratan akademik mahasiswa FEB UNJ.
                </p>
            </div>

            <div class="flex items-center gap-2.5 self-start sm:self-auto pl-13 sm:pl-0">
                <a href="{{ route('master.letter-purposes.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 text-xs sm:text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-200 rounded-full shadow-2xs hover:shadow-xs transition-all active:scale-[0.98]">
                    <i data-lucide="arrow-left" class="w-4 h-4 text-gray-500"></i>
                    <span>Kembali ke Keperluan</span>
                </a>
            </div>
        </div>

        {{-- 2-Column macOS Studio Layout --}}
        <form id="letter-purpose-form" method="POST" action="{{ route('master.letter-purposes.store') }}" novalidate>
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-7 items-start">
                
                {{-- LEFT COLUMN: Main Form Card (7 Cols) --}}
                <div class="lg:col-span-7 space-y-6">

                    {{-- Form Card --}}
                    <div class="mac-card p-6 sm:p-7">
                        <div class="flex items-center gap-3.5 mb-6 pb-4 border-b border-gray-100">
                            <div class="mac-step-badge">
                                <i data-lucide="bookmark" class="w-4 h-4 text-[#FF4D00]"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-gray-900 tracking-tight">
                                    Informasi Keperluan Surat
                                </h3>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    Isi nama keperluan yang jelas dan spesifik serta tentukan status keaktifannya.
                                </p>
                            </div>
                        </div>

                        <div class="space-y-5">
                            {{-- Field: Nama Keperluan --}}
                            <div class="space-y-1.5">
                                <label for="name" class="text-xs font-semibold text-gray-700 flex items-center gap-1.5">
                                    <i data-lucide="bookmark-check" class="w-3.5 h-3.5 text-gray-400"></i>
                                    Nama Keperluan Surat <span class="text-destructive">*</span>
                                </label>
                                <input type="text" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name') }}" 
                                       class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-900 placeholder:text-gray-400 focus:outline-none focus:border-[#FF4D00] focus:ring-4 focus:ring-orange-500/10 transition-all @error('name') !border-red-500 !ring-red-500/10 @enderror" 
                                       placeholder="Contoh: Magang Mandiri / Observasi Skripsi"
                                       maxlength="255"
                                       required>

                                {{-- Quick Preset Chips for inspiration --}}
                                <div class="pt-1">
                                    <div class="text-[11px] text-gray-500 flex items-center gap-1.5 mb-1.5">
                                        <span>Rekomendasi Keperluan Mahasiswa:</span>
                                    </div>
                                    <div class="flex flex-wrap gap-1.5">
                                        <button type="button" class="purpose-preset-chip" onclick="fillPreset('Magang Mandiri')">Magang Mandiri</button>
                                        <button type="button" class="purpose-preset-chip" onclick="fillPreset('Magang Kelompok')">Magang Kelompok</button>
                                        <button type="button" class="purpose-preset-chip" onclick="fillPreset('Observasi Skripsi')">Observasi Skripsi</button>
                                        <button type="button" class="purpose-preset-chip" onclick="fillPreset('Observasi Mandiri')">Observasi Mandiri</button>
                                        <button type="button" class="purpose-preset-chip" onclick="fillPreset('PKL Mandiri')">PKL Mandiri</button>
                                        <button type="button" class="purpose-preset-chip" onclick="fillPreset('Penelitian Skripsi')">Penelitian Skripsi</button>
                                    </div>
                                </div>

                                <p class="text-[11px] text-gray-500">
                                    Nama resmi keperluan yang akan muncul pada dropdown pembuatan surat mahasiswa.
                                </p>
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
                                          placeholder="Tambahkan catatan singkat peruntukan keperluan ini (opsional)...">{{ old('description') }}</textarea>
                                @error('description')
                                    <p class="text-xs text-destructive flex items-center gap-1 mt-1">
                                        <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Field: Status Aktif (Apple iOS Style Toggle Switch) --}}
                            <div class="pt-2">
                                <div class="p-4 rounded-2xl bg-gray-50/80 border border-gray-200/80 hover:bg-gray-50 transition-colors">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="space-y-1">
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs font-bold text-gray-900">Status Keperluan</span>
                                                <span id="statusIndicatorBadge" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                    Aktif
                                                </span>
                                            </div>
                                            <p id="statusDescriptionText" class="text-xs text-gray-500 leading-relaxed">
                                                Keperluan surat aktif dan langsung tersedia di pilihan dropdown pembuatan surat dinas mahasiswa.
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
                        <a href="{{ route('master.letter-purposes.index') }}"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 text-xs sm:text-sm font-semibold text-gray-700 hover:text-gray-900 bg-white hover:bg-gray-100/90 rounded-xl border border-gray-200/90 shadow-2xs hover:shadow-xs transition-all active:scale-[0.98]">
                            <i data-lucide="x-circle" class="w-4 h-4 text-gray-500"></i>
                            <span>Batal</span>
                        </a>
                        <button type="submit" id="btn_submit_letter_purpose"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-8 py-3 text-xs sm:text-sm font-bold text-white bg-gradient-to-r from-[#FF4D00] to-[#ff6a00] hover:from-[#e04400] hover:to-[#eb5e00] rounded-xl shadow-[0_4px_16px_rgba(255,77,0,0.32)] hover:shadow-[0_6px_22px_rgba(255,77,0,0.42)] active:scale-[0.98] transition-all cursor-pointer">
                            <i data-lucide="check-circle" class="w-4 h-4"></i>
                            <span>Simpan Keperluan</span>
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
                                    <i data-lucide="bookmark" class="w-4 h-4"></i>
                                </div>
                                <span class="text-xs font-extrabold text-gray-900 tracking-tight">Pratinjau Keperluan Surat</span>
                            </div>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-orange-100/90 text-orange-800 border border-orange-300/80">
                                <span class="w-1.5 h-1.5 rounded-full bg-orange-500 animate-pulse"></span>
                                Live Sync
                            </span>
                        </div>

                        {{-- Simulated Dropdown Option Box --}}
                        <div class="p-3.5 rounded-2xl bg-white border-2 border-orange-300/90 mb-4 shadow-xs">
                            <div class="flex items-center justify-between text-[11px] font-semibold text-gray-500 mb-1.5">
                                <span>Pilihan Dropdown Surat Mahasiswa</span>
                                <span class="text-orange-600 font-bold bg-orange-50 px-2 py-0.5 rounded-md border border-orange-200/70 text-[10px]">
                                    Keperluan
                                </span>
                            </div>
                            
                            {{-- Simulated Item Capsule --}}
                            <div class="p-2.5 rounded-xl bg-gray-50 border border-gray-200/80 flex items-center gap-2.5">
                                <div class="w-6 h-6 rounded-lg bg-orange-100 text-[#FF4D00] flex items-center justify-center flex-shrink-0">
                                    <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                </div>
                                <span id="sim_name_display" class="text-xs sm:text-sm font-bold text-gray-900 truncate">
                                    Nama Keperluan Surat
                                </span>
                            </div>

                            <div class="mt-2.5 pt-2 border-t border-gray-100 text-[11px] text-gray-600 flex items-center gap-1.5">
                                <i data-lucide="info" class="w-3.5 h-3.5 text-orange-500 flex-shrink-0"></i>
                                <span>Tampil otomatis saat operator memilih jenis surat mahasiswa.</span>
                            </div>
                        </div>

                        {{-- Token Breakdown Pills --}}
                        <div class="space-y-2 text-xs">
                            {{-- Token Nama --}}
                            <div class="flex items-center justify-between py-2 px-3 rounded-xl bg-white/90 border border-orange-200/70 shadow-2xs">
                                <span class="text-gray-600 font-medium flex items-center gap-2">
                                    <i data-lucide="bookmark" class="w-3.5 h-3.5 text-orange-500"></i>
                                    Kategori Keperluan
                                </span>
                                <span id="sim_token_name_badge" class="font-bold text-gray-900 truncate max-w-[170px]" title="Nama keperluan">
                                    [BELUM DIISI]
                                </span>
                            </div>

                            {{-- Token Sasaran --}}
                            <div class="flex items-center justify-between py-2 px-3 rounded-xl bg-white/90 border border-orange-200/70 shadow-2xs">
                                <span class="text-gray-600 font-medium flex items-center gap-2">
                                    <i data-lucide="users" class="w-3.5 h-3.5 text-blue-500"></i>
                                    Target Sasaran
                                </span>
                                <span class="font-semibold text-blue-700 px-2 py-0.5 rounded-md bg-blue-50 border border-blue-200">
                                    Mahasiswa & Akademik
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

                    {{-- 🌟 CARD 2: Pedoman & Ketentuan Sistem --}}
                    <div class="mac-card p-5 sm:p-6 bg-gradient-to-br from-white to-amber-50/20">
                        <div class="flex items-center gap-2 mb-3 text-xs font-bold text-gray-900 uppercase tracking-wider">
                            <i data-lucide="sparkles" class="w-4 h-4 text-amber-500"></i>
                            Panduan Keperluan Surat FEB UNJ
                        </div>
                        <ul class="text-xs text-gray-600 space-y-2.5">
                            <li class="flex items-start gap-2">
                                <i data-lucide="check" class="w-3.5 h-3.5 text-emerald-600 flex-shrink-0 mt-0.5"></i>
                                <span><strong>Keterkaitan Jenis Surat:</strong> Keperluan ini akan ditampilkan pada form surat ketika jenis surat mewajibkan pengisian keperluan (misal: Surat Tugas, SIPERMAWA).</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i data-lucide="check" class="w-3.5 h-3.5 text-emerald-600 flex-shrink-0 mt-0.5"></i>
                                <span><strong>Standarisasi Nama:</strong> Gunakan format nama baku seperti <code class="font-mono text-gray-800 bg-gray-100 px-1 py-0.5 rounded">Magang Mandiri</code> atau <code class="font-mono text-gray-800 bg-gray-100 px-1 py-0.5 rounded">Observasi Skripsi</code> untuk kemudahan rekapitulasi data.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i data-lucide="check" class="w-3.5 h-3.5 text-emerald-600 flex-shrink-0 mt-0.5"></i>
                                <span><strong>Status Aktif:</strong> Keperluan yang aktif otomatis dapat dipilih langsung oleh staf saat pembuatan surat baru.</span>
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

        const nameInput = document.getElementById('name');
        const simNameDisplay = document.getElementById('sim_name_display');
        const simTokenNameBadge = document.getElementById('sim_token_name_badge');
        const form = document.getElementById('letter-purpose-form');

        // Update Live Simulation from input
        function updateLivePreview() {
            let nameVal = nameInput.value.trim();

            if (nameVal.length > 0) {
                simNameDisplay.textContent = nameVal;
                simTokenNameBadge.textContent = nameVal;
                simTokenNameBadge.title = nameVal;
                simNameDisplay.classList.add('token-updated');
                setTimeout(() => simNameDisplay.classList.remove('token-updated'), 350);
            } else {
                simNameDisplay.textContent = 'Nama Keperluan Surat';
                simTokenNameBadge.textContent = '[BELUM DIISI]';
                simTokenNameBadge.title = '';
            }
        }

        nameInput.addEventListener('input', updateLivePreview);

        // Initial preview call
        updateLivePreview();

        // Form validation feedback
        form.addEventListener('submit', function(e) {
            const name = nameInput.value.trim();

            if (!name) {
                e.preventDefault();
                showToast('Nama keperluan surat wajib diisi.', 'error');
                nameInput.focus();
                return;
            }
        });
    });

    // Preset Chip Click Helper
    function fillPreset(name) {
        const nameInput = document.getElementById('name');
        nameInput.value = name;
        nameInput.dispatchEvent(new Event('input'));
        showToast(`Preset "${name}" dipilih`, 'success');
        nameInput.focus();
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
            statusDescriptionText.textContent = 'Keperluan surat aktif dan langsung tersedia di pilihan dropdown pembuatan surat dinas mahasiswa.';

            simTokenStatusBadge.className = 'font-bold text-emerald-700 px-2 py-0.5 rounded-md bg-emerald-50 border border-emerald-200';
            simTokenStatusBadge.textContent = 'Aktif';
        } else {
            statusIndicatorBadge.className = 'inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-700 border border-gray-300';
            statusIndicatorBadge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> Nonaktif';
            statusDescriptionText.textContent = 'Keperluan surat dinonaktifkan dan disembunyikan dari pilihan registrasi surat baru.';

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
