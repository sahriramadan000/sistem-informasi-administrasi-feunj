@extends('layouts.app')

@section('title', 'Edit Jenjang Pendidikan - FEB UNJ')
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
                    <span>Edit Jenjang Pendidikan</span>
                </h1>
                <p class="text-xs sm:text-sm text-gray-500 mt-1 pl-13">
                    Perbarui nama jenjang studi atau sesuaikan besaran tarif PNBP per lembar untuk layanan legalisir FEB UNJ.
                </p>
            </div>

            <div class="flex items-center gap-2.5 self-start sm:self-auto pl-13 sm:pl-0">
                <a href="{{ route('master.education-levels.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 text-xs sm:text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-200 rounded-full shadow-2xs hover:shadow-xs transition-all active:scale-[0.98]">
                    <i data-lucide="arrow-left" class="w-4 h-4 text-gray-500"></i>
                    <span>Kembali ke Jenjang</span>
                </a>
            </div>
        </div>

        {{-- 2-Column macOS Studio Layout --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-7 items-start">
            
            {{-- LEFT COLUMN: Main Form & Danger Zone (7 Cols) --}}
            <div class="lg:col-span-7 space-y-6">

                {{-- Edit Form Card --}}
                <div class="mac-card p-6 sm:p-7">
                    <div class="flex items-center justify-between gap-3.5 mb-6 pb-4 border-b border-gray-100">
                        <div class="flex items-center gap-3.5">
                            <div class="mac-step-badge">
                                <i data-lucide="layers" class="w-4 h-4 text-[#FF4D00]"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-gray-900 tracking-tight">
                                    Informasi Jenjang & Tarif Legalisir
                                </h3>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    Perubahan data akan langsung tersinkronisasi pada permohonan legalisir baru.
                                </p>
                            </div>
                        </div>

                        {{-- Current Status Pill --}}
                        <div>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100/90 text-emerald-800 border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Terdaftar Aktif
                            </span>
                        </div>
                    </div>

                    <form id="edit-education-level-form" method="POST" action="{{ route('master.education-levels.update', $educationLevel) }}" novalidate>
                        @csrf
                        @method('PUT')

                        <div class="space-y-5">
                            {{-- Field 1: Nama Jenjang Pendidikan --}}
                            <div class="space-y-1.5">
                                <label for="name" class="text-xs font-semibold text-gray-700 flex items-center gap-1.5">
                                    <i data-lucide="graduation-cap" class="w-3.5 h-3.5 text-gray-400"></i>
                                    Nama Jenjang Pendidikan <span class="text-destructive">*</span>
                                </label>
                                <input type="text" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name', $educationLevel->name) }}" 
                                       class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-semibold text-gray-900 placeholder:text-gray-400 focus:outline-none focus:border-[#FF4D00] focus:ring-4 focus:ring-orange-500/10 transition-all @error('name') !border-red-500 !ring-red-500/10 @enderror" 
                                       placeholder="Contoh: Sarjana (S1)"
                                       maxlength="100"
                                       required>

                                {{-- Quick Presets for Education Level Name --}}
                                <div class="pt-1">
                                    <div class="text-[11px] text-gray-500 flex items-center gap-1.5 mb-1.5">
                                        <span>Pilihan Jenjang Lazim FEB UNJ:</span>
                                    </div>
                                    <div class="flex flex-wrap gap-1.5">
                                        <button type="button" class="preset-chip" onclick="fillName('Sarjana (S1)')">Sarjana (S1)</button>
                                        <button type="button" class="preset-chip" onclick="fillName('Magister (S2)')">Magister (S2)</button>
                                        <button type="button" class="preset-chip" onclick="fillName('Doktor (S3)')">Doktor (S3)</button>
                                        <button type="button" class="preset-chip" onclick="fillName('Diploma Tiga (D3)')">Diploma Tiga (D3)</button>
                                        <button type="button" class="preset-chip" onclick="fillName('Profesi Akuntan')">Profesi Akuntan</button>
                                    </div>
                                </div>

                                <p class="text-[11px] text-gray-500">
                                    Nama jenjang akan tampil pada formulir permohonan legalisir dan tercatat pada kuitansi pembayaran resmi.
                                </p>
                                @error('name')
                                    <p class="text-xs text-destructive flex items-center gap-1 mt-1">
                                        <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Field 2: Tarif per Lembar (Rp) --}}
                            <div class="space-y-1.5">
                                <label for="price_per_page" class="text-xs font-semibold text-gray-700 flex items-center gap-1.5">
                                    <i data-lucide="banknote" class="w-3.5 h-3.5 text-gray-400"></i>
                                    Tarif per Lembar (Rp) <span class="text-destructive">*</span>
                                </label>

                                <div class="relative rounded-xl shadow-2xs">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                        <span class="text-xs font-bold text-gray-500">Rp</span>
                                    </div>
                                    <input type="number" 
                                           id="price_per_page" 
                                           name="price_per_page" 
                                           value="{{ old('price_per_page', $educationLevel->price_per_page) }}" 
                                           min="0"
                                           step="500"
                                           class="w-full pl-11 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-mono font-bold text-gray-900 placeholder:text-gray-400 focus:outline-none focus:border-[#FF4D00] focus:ring-4 focus:ring-orange-500/10 transition-all @error('price_per_page') !border-red-500 !ring-red-500/10 @enderror" 
                                           placeholder="Contoh: 5000"
                                           required>
                                </div>

                                {{-- Quick Presets for Price --}}
                                <div class="pt-1">
                                    <div class="text-[11px] text-gray-500 flex items-center gap-1.5 mb-1.5">
                                        <span>Pintasan Tarif PNBP:</span>
                                    </div>
                                    <div class="flex flex-wrap gap-1.5">
                                        <button type="button" class="preset-chip font-mono" onclick="fillPrice(0)">Rp 0 (Gratis)</button>
                                        <button type="button" class="preset-chip font-mono" onclick="fillPrice(2500)">Rp 2.500</button>
                                        <button type="button" class="preset-chip font-mono" onclick="fillPrice(5000)">Rp 5.000</button>
                                        <button type="button" class="preset-chip font-mono" onclick="fillPrice(10000)">Rp 10.000</button>
                                        <button type="button" class="preset-chip font-mono" onclick="fillPrice(15000)">Rp 15.000</button>
                                    </div>
                                </div>

                                <p class="text-[11px] text-gray-500">
                                    Besaran tarif PNBP per lembar dokumen legalisir. Isi angka <strong>0</strong> jika layanan tidak dipungut biaya.
                                </p>
                                @error('price_per_page')
                                    <p class="text-xs text-destructive flex items-center gap-1 mt-1">
                                        <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Notice Card --}}
                            <div class="p-4 rounded-2xl bg-orange-50/60 border border-orange-200/70 text-xs text-orange-950 flex items-start gap-3">
                                <div class="w-6 h-6 rounded-lg bg-orange-500 text-white flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
                                </div>
                                <div class="space-y-0.5">
                                    <p class="font-bold">Otomatisasi Tagihan Legalisir</p>
                                    <p class="text-orange-900/80 leading-relaxed text-[11px]">
                                        Ketika staf atau alumni mengajukan legalisir dokumen, total tagihan akan otomatis dikalkulasikan: <code class="font-mono bg-white/80 px-1 py-0.5 rounded border border-orange-200 font-bold">Jumlah Lembar × Tarif per Lembar</code>.
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- macOS Bottom Action Dock inside card --}}
                        <div class="flex flex-col-reverse sm:flex-row items-center justify-between gap-3 pt-6 mt-6 border-t border-gray-100">
                            <a href="{{ route('master.education-levels.index') }}"
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 text-xs sm:text-sm font-semibold text-gray-700 hover:text-gray-900 bg-white hover:bg-gray-100/90 rounded-xl border border-gray-200/90 shadow-2xs hover:shadow-xs transition-all active:scale-[0.98]">
                                <i data-lucide="x-circle" class="w-4 h-4 text-gray-500"></i>
                                <span>Batal</span>
                            </a>
                            <button type="submit" id="btn_submit_edit_education_level"
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
                                    <h4 class="text-sm font-bold text-gray-900">Nonaktifkan Jenjang Pendidikan</h4>
                                </div>
                                <p class="text-xs text-gray-600 leading-relaxed max-w-xl">
                                    Jenjang pendidikan akan dinonaktifkan (soft delete). Data transaksi legalisir masa lalu yang menggunakan jenjang ini tetap aman dan tidak akan terhapus.
                                </p>
                            </div>
                            <button type="button" 
                                    onclick="openDeactivateModal()"
                                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 text-xs sm:text-sm font-bold text-rose-800 bg-rose-100 hover:bg-rose-200/90 border border-rose-300 rounded-xl transition-all active:scale-[0.98] cursor-pointer">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                <span>Nonaktifkan</span>
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
                                <i data-lucide="calculator" class="w-4 h-4"></i>
                            </div>
                            <span class="text-xs font-extrabold text-gray-900 tracking-tight">Pratinjau Tarif & Tagihan PNBP</span>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-orange-100/90 text-orange-800 border border-orange-300/80">
                            <span class="w-1.5 h-1.5 rounded-full bg-orange-500 animate-pulse"></span>
                            Live Sync
                        </span>
                    </div>

                    {{-- Simulated Official Bill / Tariffs Box --}}
                    <div class="p-4 rounded-2xl bg-white border-2 border-orange-300/90 mb-4 shadow-xs">
                        <div class="flex items-center justify-between text-[11px] font-semibold text-gray-500 mb-2.5">
                            <span>Simulasi Kartu Legalisir FEB UNJ</span>
                            <span class="text-orange-600 font-bold bg-orange-50 px-2 py-0.5 rounded-md border border-orange-200/70 text-[10px]">
                                Terkini
                            </span>
                        </div>
                        
                        {{-- Card Detail --}}
                        <div class="p-3.5 rounded-xl bg-gray-50/70 border border-gray-200/80 text-left space-y-3">
                            <div class="flex items-center gap-3">
                                <div id="sim_avatar" class="w-10 h-10 rounded-xl bg-gradient-to-br from-orange-500 to-amber-500 text-white font-bold text-xs flex items-center justify-center shadow-xs flex-shrink-0">
                                    {{ collect(explode(' ', $educationLevel->name))->take(2)->map(fn($w) => strtoupper(substr($w, 0, 1)))->join('') ?: 'JP' }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p id="sim_name_display" class="text-sm font-bold text-gray-900 truncate">
                                        {{ $educationLevel->name }}
                                    </p>
                                    <p class="text-[11px] text-gray-500">
                                        Strata Pendidikan Resmi FEB UNJ
                                    </p>
                                </div>
                            </div>

                            {{-- Fee Breakdown --}}
                            <div class="pt-2 border-t border-gray-200/70 space-y-1.5 text-xs">
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-500">Tarif per Lembar:</span>
                                    <span id="sim_price_display" class="font-mono font-extrabold text-orange-600">
                                        Rp {{ number_format($educationLevel->price_per_page, 0, ',', '.') }}
                                    </span>
                                </div>

                                <div class="flex items-center justify-between text-[11px] text-gray-600 bg-white p-2 rounded-lg border border-gray-200/60">
                                    <span>Contoh 5 Lembar:</span>
                                    <span id="sim_calc_5" class="font-mono font-bold text-gray-900">
                                        Rp {{ number_format($educationLevel->price_per_page * 5, 0, ',', '.') }}
                                    </span>
                                </div>

                                <div class="flex items-center justify-between text-[11px] text-gray-600 bg-white p-2 rounded-lg border border-gray-200/60">
                                    <span>Contoh 10 Lembar:</span>
                                    <span id="sim_calc_10" class="font-mono font-bold text-gray-900">
                                        Rp {{ number_format($educationLevel->price_per_page * 10, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Token Breakdown Pills --}}
                    <div class="space-y-2 text-xs">
                        {{-- Token Nama --}}
                        <div class="flex items-center justify-between py-2 px-3 rounded-xl bg-white/90 border border-orange-200/70 shadow-2xs">
                            <span class="text-gray-600 font-medium flex items-center gap-2">
                                <i data-lucide="graduation-cap" class="w-3.5 h-3.5 text-orange-500"></i>
                                Nama Jenjang
                            </span>
                            <span id="sim_token_name_badge" class="font-bold text-orange-600 truncate max-w-[170px]">
                                {{ $educationLevel->name }}
                            </span>
                        </div>

                        {{-- Token Tarif --}}
                        <div class="flex items-center justify-between py-2 px-3 rounded-xl bg-white/90 border border-orange-200/70 shadow-2xs">
                            <span class="text-gray-600 font-medium flex items-center gap-2">
                                <i data-lucide="tag" class="w-3.5 h-3.5 text-blue-500"></i>
                                Tarif Satuan
                            </span>
                            <span id="sim_token_price_badge" class="font-mono font-bold text-gray-900">
                                Rp {{ number_format($educationLevel->price_per_page, 0, ',', '.') }} / lembar
                            </span>
                        </div>

                        {{-- Token Status --}}
                        <div class="flex items-center justify-between py-2 px-3 rounded-xl bg-white/90 border border-orange-200/70 shadow-2xs">
                            <span class="text-gray-600 font-medium flex items-center gap-2">
                                <i data-lucide="check-circle" class="w-3.5 h-3.5 text-emerald-500"></i>
                                Status Penggunaan
                            </span>
                            <span class="font-bold text-emerald-700 px-2 py-0.5 rounded-md bg-emerald-50 border border-emerald-200">
                                Terdaftar Aktif
                            </span>
                        </div>
                    </div>
                </div>

                {{-- 🌟 CARD 2: Metadata & Audit Inspector --}}
                <div class="mac-card p-5 sm:p-6 space-y-4">
                    <div class="flex items-center gap-2 text-xs font-bold text-gray-900 uppercase tracking-wider pb-3 border-b border-gray-100">
                        <i data-lucide="file-clock" class="w-4 h-4 text-gray-500"></i>
                        Informasi Riwayat & Audit
                    </div>

                    <div class="space-y-3 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Total Transaksi Legalisir</span>
                            <span class="font-bold text-gray-900 bg-gray-100 px-2 py-0.5 rounded-md font-mono">
                                {{ $educationLevel->legalizations()->count() }} Berkas
                            </span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Tanggal Didaftarkan</span>
                            <span class="font-medium text-gray-800">
                                {{ $educationLevel->created_at ? $educationLevel->created_at->translatedFormat('d M Y, H:i') : '-' }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Terakhir Diperbarui</span>
                            <span class="font-medium text-gray-800">
                                {{ $educationLevel->updated_at ? $educationLevel->updated_at->translatedFormat('d M Y, H:i') : '-' }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- 🌟 CARD 3: Pedoman Ketentuan Sistem --}}
                <div class="mac-card p-5 sm:p-6 bg-gradient-to-br from-white to-amber-50/20">
                    <div class="flex items-center gap-2 mb-3 text-xs font-bold text-gray-900 uppercase tracking-wider">
                        <i data-lucide="sparkles" class="w-4 h-4 text-amber-500"></i>
                        Catatan Penting Keabsahan Tarif
                    </div>
                    <ul class="text-xs text-gray-600 space-y-2.5">
                        <li class="flex items-start gap-2">
                            <i data-lucide="check" class="w-3.5 h-3.5 text-emerald-600 flex-shrink-0 mt-0.5"></i>
                            <span>Perubahan tarif per lembar hanya berlaku pada permohonan legalisir yang baru didaftarkan.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i data-lucide="check" class="w-3.5 h-3.5 text-emerald-600 flex-shrink-0 mt-0.5"></i>
                            <span>Kuitansi pembayaran dan riwayat keuangan legalisir yang telah terbit sebelumnya tidak akan berubah nilainya.</span>
                        </li>
                    </ul>
                </div>

            </div>

        </div>
    </div>

    {{-- Deactivate Confirmation Modal (Apple Sheet Style) --}}
    @if (auth()->user()->isAdmin())
        <div id="deactivateModalBackdrop" class="mac-modal-backdrop fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/40 backdrop-blur-xs">
            <div class="mac-modal-content bg-white rounded-3xl p-6 sm:p-7 text-left shadow-2xl border border-gray-100 sm:max-w-md w-full">
                <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center mb-4 shadow-2xs">
                    <i data-lucide="alert-triangle" class="w-6 h-6"></i>
                </div>

                <h3 class="text-base sm:text-lg font-bold text-gray-900 tracking-tight">
                    Konfirmasi Nonaktifkan Jenjang
                </h3>

                <p class="text-xs sm:text-sm text-gray-600 leading-relaxed mt-2">
                    Apakah Anda yakin ingin menonaktifkan master jenjang <strong class="text-gray-900">{{ $educationLevel->name }}</strong>?
                </p>

                <div class="mt-3 p-3 rounded-xl bg-amber-50 border border-amber-200/80 text-[11px] text-amber-800 flex items-start gap-2">
                    <i data-lucide="info" class="w-4 h-4 text-amber-600 flex-shrink-0 mt-0.5"></i>
                    <span>Sebanyak <strong>{{ $educationLevel->legalizations()->count() }} berkas legalisir</strong> yang pernah tercatat menggunakan jenjang ini akan tetap aman di database.</span>
                </div>

                <div class="mt-6 flex items-center justify-end gap-2.5">
                    <button type="button" onclick="closeDeactivateModal()"
                        class="px-4 py-2 text-xs sm:text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-all cursor-pointer">
                        Batal
                    </button>
                    <form method="POST" action="{{ route('master.education-levels.destroy', $educationLevel) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="px-4.5 py-2 text-xs sm:text-sm font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl shadow-xs transition-all cursor-pointer">
                            Ya, Nonaktifkan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
<script src="https://unpkg.com/lucide@latest"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        const nameInput = document.getElementById('name');
        const priceInput = document.getElementById('price_per_page');

        const simNameDisplay = document.getElementById('sim_name_display');
        const simPriceDisplay = document.getElementById('sim_price_display');
        const simAvatar = document.getElementById('sim_avatar');
        const simCalc5 = document.getElementById('sim_calc_5');
        const simCalc10 = document.getElementById('sim_calc_10');

        const simTokenNameBadge = document.getElementById('sim_token_name_badge');
        const simTokenPriceBadge = document.getElementById('sim_token_price_badge');

        const form = document.getElementById('edit-education-level-form');

        function formatRupiah(number) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(number);
        }

        function getInitials(text) {
            if (!text) return 'JP';
            const words = text.trim().split(/\s+/).filter(Boolean);
            if (words.length === 1) return words[0].substring(0, 2).toUpperCase();
            return (words[0][0] + words[1][0]).toUpperCase();
        }

        function updateLivePreview() {
            const nameVal = nameInput.value.trim();
            const priceVal = parseInt(priceInput.value, 10) || 0;

            // Name & Avatar
            if (nameVal.length > 0) {
                simNameDisplay.textContent = nameVal;
                simTokenNameBadge.textContent = nameVal;
                simAvatar.textContent = getInitials(nameVal);
            } else {
                simNameDisplay.textContent = 'Nama Jenjang Pendidikan';
                simTokenNameBadge.textContent = '[KOSONG]';
                simAvatar.textContent = 'JP';
            }

            // Price & Calculations
            simPriceDisplay.textContent = formatRupiah(priceVal);
            simTokenPriceBadge.textContent = `${formatRupiah(priceVal)} / lembar`;
            simCalc5.textContent = formatRupiah(priceVal * 5);
            simCalc10.textContent = formatRupiah(priceVal * 10);

            // Trigger animation
            simPriceDisplay.classList.add('token-updated');
            setTimeout(() => simPriceDisplay.classList.remove('token-updated'), 350);
        }

        nameInput.addEventListener('input', updateLivePreview);
        priceInput.addEventListener('input', updateLivePreview);

        // Initial preview call
        updateLivePreview();

        // Form submit validation
        form.addEventListener('submit', function(e) {
            const name = nameInput.value.trim();
            const price = priceInput.value.trim();

            if (!name) {
                e.preventDefault();
                showToast('Nama jenjang pendidikan wajib diisi.', 'error');
                nameInput.focus();
                return;
            }
            if (price === '' || isNaN(price) || Number(price) < 0) {
                e.preventDefault();
                showToast('Tarif per lembar harus berupa angka valid (minimal 0).', 'error');
                priceInput.focus();
                return;
            }
        });
    });

    // Preset Name Helper
    function fillName(name) {
        const nameInput = document.getElementById('name');
        nameInput.value = name;
        nameInput.dispatchEvent(new Event('input'));
        showToast(`Jenjang "${name}" dipilih`, 'success');
        nameInput.focus();
    }

    // Preset Price Helper
    function fillPrice(price) {
        const priceInput = document.getElementById('price_per_page');
        priceInput.value = price;
        priceInput.dispatchEvent(new Event('input'));
        showToast(`Tarif "Rp ${new Intl.NumberFormat('id-ID').format(price)}" dipilih`, 'success');
        priceInput.focus();
    }

    // Modal Handlers (Apple Style)
    function openDeactivateModal() {
        const backdrop = document.getElementById('deactivateModalBackdrop');
        if (backdrop) {
            backdrop.classList.add('show');
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }
    }

    function closeDeactivateModal() {
        const backdrop = document.getElementById('deactivateModalBackdrop');
        if (backdrop) {
            backdrop.classList.remove('show');
        }
    }

    // Close on backdrop click
    document.addEventListener('click', function(e) {
        const backdrop = document.getElementById('deactivateModalBackdrop');
        if (backdrop && e.target === backdrop) {
            closeDeactivateModal();
        }
    });

    // Close on Escape key
    window.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDeactivateModal();
        }
    });

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
