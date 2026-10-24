@extends('layouts.app')

@section('title', 'Buat Surat Baru')
@section('disable_layout_alerts', true)

@push('styles')
    <style>
        /* Scoped macOS / iOS Form Enhancements */
        .mac-card {
            background: #ffffff;
            border-radius: 1.5rem;
            border: 1px solid rgba(226, 232, 240, 0.85);
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.03), 0 2px 6px -1px rgba(0, 0, 0, 0.02);
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .mac-card:hover {
            border-color: rgba(254, 215, 170, 0.8);
            box-shadow: 0 10px 25px -4px rgba(255, 77, 0, 0.04), 0 4px 10px -2px rgba(0, 0, 0, 0.03);
        }

        /* macOS Inset Step Badge */
        .mac-step-badge {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%);
            border: 1px solid #fed7aa;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ea580c;
            font-weight: 700;
            font-size: 0.925rem;
            box-shadow: 0 2px 6px rgba(255, 77, 0, 0.08);
            flex-shrink: 0;
        }

        /* Live Simulator Warm Orange Apple Card */
        .simulator-orange-card {
            background: linear-gradient(145deg, #fff7ed 0%, #fffaf5 50%, #ffedd5 100%);
            border-radius: 1.5rem;
            border: 2px solid #fed7aa;
            box-shadow: 0 8px 24px -4px rgba(255, 77, 0, 0.08), 0 2px 6px -1px rgba(255, 77, 0, 0.04);
        }

        /* Pulse glow animation on preview update */
        @keyframes tokenPulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.03); filter: drop-shadow(0 2px 8px rgba(255, 77, 0, 0.2)); }
            100% { transform: scale(1); }
        }
        .token-updated {
            animation: tokenPulse 0.35s ease-out;
        }

        /* Stepper buttons */
        .stepper-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #334155;
            font-weight: 700;
            font-size: 1.15rem;
            transition: all 0.15s ease;
            cursor: pointer;
            user-select: none;
        }
        .stepper-btn:hover {
            background: #ffffff;
            border-color: #cbd5e1;
            color: #0f172a;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }
        .stepper-btn:active {
            transform: scale(0.94);
            background: #f1f5f9;
        }

        /* Quick Preset Chips */
        .qty-preset-chip {
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 600;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #64748b;
            transition: all 0.15s ease;
            cursor: pointer;
        }
        .qty-preset-chip:hover, .qty-preset-chip.active {
            background: #fff7ed;
            border-color: #fed7aa;
            color: #c2410c;
        }

        /* Select2 macOS Styling Override */
        .select2-container--default .select2-selection--single {
            height: 42px !important;
            border: 1.5px solid #e2e8f0 !important;
            border-radius: 0.75rem !important;
            padding: 0.45rem 0.85rem !important;
            font-size: 0.875rem !important;
            background-color: #ffffff !important;
            transition: all 0.15s ease-in-out !important;
        }
        .select2-container--default .select2-selection--single:hover {
            border-color: #cbd5e1 !important;
            background-color: #fafafa !important;
        }
        .select2-container--default.select2-container--focus .select2-selection--single,
        .select2-container--default.select2-container--open .select2-selection--single {
            border-color: #FF4D00 !important;
            box-shadow: 0 0 0 3.5px rgba(255, 77, 0, 0.12) !important;
            background-color: #ffffff !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 26px !important;
            color: #1e293b !important;
            font-weight: 500 !important;
        }
        .select2-dropdown {
            border: 1px solid #e2e8f0 !important;
            border-radius: 0.85rem !important;
            box-shadow: 0 15px 35px -5px rgba(0, 0, 0, 0.1), 0 5px 15px -3px rgba(0, 0, 0, 0.05) !important;
            overflow: hidden !important;
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
    <div class="max-w-7xl mx-auto space-y-6 pb-12 relative">
        {{-- Floating Toast Notification (Auto Dismiss) --}}
        <div id="toastPopup" class="toast-popup fixed top-6 left-1/2 z-50 flex items-center gap-3 px-5 py-3 rounded-2xl bg-white/95 backdrop-blur-md border border-gray-200 shadow-xl text-xs sm:text-sm font-medium">
            <div id="toastIconWrap" class="w-7 h-7 rounded-xl flex items-center justify-center flex-shrink-0 bg-red-100 text-red-600">
                <i id="toastIcon" data-lucide="alert-circle" class="w-4 h-4"></i>
            </div>
            <span id="toastMessage" class="text-gray-800"></span>
            <button type="button" onclick="hideToast()" class="text-gray-400 hover:text-gray-600 ml-2 p-0.5 rounded-lg transition-colors cursor-pointer">
                <i data-lucide="x" class="w-3.5 h-3.5"></i>
            </button>
        </div>
        {{-- macOS Top Header Bar --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-gray-200/60">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-[#FF4D00] to-amber-500 text-white flex items-center justify-center shadow-xs">
                        <i data-lucide="file-plus-2" class="w-5 h-5"></i>
                    </div>
                    Registrasi Surat Baru
                </h1>
                <p class="text-xs sm:text-sm text-gray-500 mt-1 pl-13">
                    Lengkapi isian di bawah untuk membuat surat dinas baru dan menerbitkan nomor resmi.
                </p>
            </div>

            <div class="flex items-center gap-2.5 self-start sm:self-auto pl-13 sm:pl-0">
                <a href="{{ route('letters.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 text-xs sm:text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-200 rounded-full shadow-2xs hover:shadow-xs transition-all active:scale-[0.98]">
                    <i data-lucide="arrow-left" class="w-4 h-4 text-gray-500"></i>
                    Kembali ke Daftar
                </a>
            </div>
        </div>

        {{-- Form & Live Inspector 2-Column macOS Studio Layout --}}
        <form id="letter-form" method="POST" action="{{ route('letters.store') }}" novalidate>
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-7 items-start">
                
                {{-- LEFT COLUMN: Main Form Steps (8 Cols) --}}
                <div class="lg:col-span-8 space-y-6">

                    {{-- STEP 1: Karakteristik & Klasifikasi Surat --}}
                    <div class="mac-card p-6 sm:p-7">
                        <div class="flex items-center gap-3.5 mb-5 pb-4 border-b border-gray-100">
                            <div class="mac-step-badge">1</div>
                            <div>
                                <h3 class="text-base font-bold text-gray-900 tracking-tight flex items-center gap-2">
                                    Informasi Dasar & Klasifikasi
                                    <span class="text-[11px] font-normal text-gray-400">• Step 1/3</span>
                                </h3>
                                <p class="text-xs text-gray-500 mt-0.5">Tentukan tingkat keamanan, sasaran surat, jenis surat, dan kode klasifikasi kearsipan.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            {{-- Klasifikasi Keamanan --}}
                            <div class="space-y-1.5">
                                <label for="security_classification" class="text-xs font-semibold text-gray-700 flex items-center justify-between">
                                    <span class="flex items-center gap-1.5">
                                        <i data-lucide="shield" class="w-3.5 h-3.5 text-gray-400"></i>
                                        Klasifikasi Keamanan <span class="text-destructive">*</span>
                                    </span>
                                    <span class="text-[10px] text-gray-400 font-mono">Kode SEC</span>
                                </label>
                                <select id="security_classification" name="security_classification"
                                    class="select @error('security_classification') !border-red-500 @enderror" required>
                                    <option value="">-- Pilih Klasifikasi Keamanan --</option>
                                    @foreach ($securityClassifications as $code => $name)
                                        <option value="{{ $code }}"
                                            {{ old('security_classification', 'B') == $code ? 'selected' : '' }}>
                                            {{ $name }} ({{ $code }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('security_classification')
                                    <p class="text-xs text-destructive flex items-center gap-1 mt-1">
                                        <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Sasaran Surat --}}
                            <div class="space-y-1.5">
                                <label for="letter_target" class="text-xs font-semibold text-gray-700 flex items-center gap-1.5">
                                    <i data-lucide="target" class="w-3.5 h-3.5 text-gray-400"></i>
                                    Sasaran Surat <span class="text-destructive">*</span>
                                </label>
                                <select id="letter_target" name="letter_target"
                                    class="select @error('letter_target') !border-red-500 @enderror" required>
                                    <option value="">-- Pilih Sasaran Surat --</option>
                                    @foreach ($letterTargets as $code => $name)
                                        <option value="{{ $code }}"
                                            {{ old('letter_target', 'internal') == $code ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('letter_target')
                                    <p class="text-xs text-destructive flex items-center gap-1 mt-1">
                                        <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Jenis Surat --}}
                            <div class="space-y-1.5">
                                <label for="letter_type_id" class="text-xs font-semibold text-gray-700 flex items-center gap-1.5">
                                    <i data-lucide="file-text" class="w-3.5 h-3.5 text-gray-400"></i>
                                    Jenis Surat <span class="text-destructive">*</span>
                                </label>
                                <select id="letter_type_id" name="letter_type_id"
                                    class="select @error('letter_type_id') !border-red-500 @enderror" required>
                                    <option value="">-- Pilih Jenis Surat --</option>
                                    @foreach ($letterTypes as $letterType)
                                        <option value="{{ $letterType->id }}"
                                            {{ old('letter_type_id') == $letterType->id ? 'selected' : '' }}>
                                            {{ $letterType->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('letter_type_id')
                                    <p class="text-xs text-destructive flex items-center gap-1 mt-1">
                                        <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Klasifikasi Surat --}}
                            <div class="space-y-1.5">
                                <label for="classification_id" class="text-xs font-semibold text-gray-700 flex items-center gap-1.5">
                                    <i data-lucide="folder" class="w-3.5 h-3.5 text-gray-400"></i>
                                    Klasifikasi Surat <span class="text-destructive">*</span>
                                </label>
                                <select id="classification_id" name="classification_id"
                                    class="select @error('classification_id') !border-red-500 @enderror" required>
                                    <option value="">-- Pilih Klasifikasi --</option>
                                    @foreach ($classifications as $classification)
                                        <option value="{{ $classification->id }}"
                                            {{ old('classification_id') == $classification->id ? 'selected' : '' }}>
                                            {{ $classification->name }} ({{ $classification->code }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('classification_id')
                                    <p class="text-xs text-destructive flex items-center gap-1 mt-1">
                                        <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- STEP 2: Detail Otorisasi & Kuantitas Surat --}}
                    <div class="mac-card p-6 sm:p-7">
                        <div class="flex items-center gap-3.5 mb-5 pb-4 border-b border-gray-100">
                            <div class="mac-step-badge">2</div>
                            <div>
                                <h3 class="text-base font-bold text-gray-900 tracking-tight flex items-center gap-2">
                                    Detail Otorisasi & Jumlah Surat
                                    <span class="text-[11px] font-normal text-gray-400">• Step 2/3</span>
                                </h3>
                                <p class="text-xs text-gray-500 mt-0.5">Tentukan pejabat penandatangan, tanggal penetapan surat, serta kuantitas surat.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            {{-- Penandatangan --}}
                            <div class="space-y-1.5">
                                <label for="signatory_id" class="text-xs font-semibold text-gray-700 flex items-center gap-1.5">
                                    <i data-lucide="user-check" class="w-3.5 h-3.5 text-gray-400"></i>
                                    Pejabat Penandatangan <span class="text-destructive">*</span>
                                </label>
                                <select id="signatory_id" name="signatory_id"
                                    class="select @error('signatory_id') !border-red-500 @enderror" required>
                                    <option value="">-- Pilih Penandatangan --</option>
                                    @foreach ($signatories as $signatory)
                                        <option value="{{ $signatory->id }}"
                                            {{ old('signatory_id') == $signatory->id ? 'selected' : '' }}>
                                            {{ $signatory->name }} - {{ $signatory->position }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('signatory_id')
                                    <p class="text-xs text-destructive flex items-center gap-1 mt-1">
                                        <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Tanggal Surat --}}
                            <div class="space-y-1.5">
                                <label for="letter_date" class="text-xs font-semibold text-gray-700 flex items-center gap-1.5">
                                    <i data-lucide="calendar" class="w-3.5 h-3.5 text-gray-400"></i>
                                    Tanggal Penetapan Surat <span class="text-destructive">*</span>
                                </label>
                                <div class="relative">
                                    <input type="text" id="letter_date" name="letter_date"
                                        value="{{ old('letter_date', now()->format('Y-m-d')) }}"
                                        class="input flatpickr-input pr-10 @error('letter_date') !border-red-500 @enderror"
                                        placeholder="Pilih tanggal" readonly required>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                                        <i data-lucide="calendar-days" class="w-4 h-4"></i>
                                    </div>
                                </div>
                                @error('letter_date')
                                    <p class="text-xs text-destructive flex items-center gap-1 mt-1">
                                        <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Interactive Apple Stepper for Quantity --}}
                            <div class="space-y-2 md:col-span-2 pt-2">
                                <div class="flex items-center justify-between">
                                    <label for="quantity" class="text-xs font-semibold text-gray-700 flex items-center gap-1.5">
                                        <i data-lucide="copy" class="w-3.5 h-3.5 text-gray-400"></i>
                                        Jumlah Surat yang Diterbitkan Sekaligus
                                    </label>
                                    <span class="text-[11px] text-gray-500">Maksimal 50 surat berurutan</span>
                                </div>

                                <div class="flex flex-wrap items-center gap-3">
                                    {{-- Tactile Stepper --}}
                                    <div class="inline-flex items-center p-1 bg-gray-100/80 border border-gray-200/90 rounded-2xl shadow-2xs">
                                        <button type="button" id="btn_qty_minus" class="stepper-btn" title="Kurangi 1">
                                            <i data-lucide="minus" class="w-4 h-4"></i>
                                        </button>
                                        <input type="number" id="quantity" name="quantity" value="{{ old('quantity', 1) }}"
                                            min="1" max="50"
                                            class="w-20 text-center font-bold text-base text-gray-800 bg-transparent border-0 focus:ring-0 p-0" placeholder="1">
                                        <button type="button" id="btn_qty_plus" class="stepper-btn" title="Tambah 1">
                                            <i data-lucide="plus" class="w-4 h-4"></i>
                                        </button>
                                    </div>

                                    {{-- Quick Preset Chips --}}
                                    <div class="flex items-center gap-1.5">
                                        <button type="button" class="qty-preset-chip" data-qty="1">1 Surat</button>
                                        <button type="button" class="qty-preset-chip" data-qty="2">2</button>
                                        <button type="button" class="qty-preset-chip" data-qty="5">5</button>
                                        <button type="button" class="qty-preset-chip" data-qty="10">10</button>
                                        <button type="button" class="qty-preset-chip" data-qty="25">25</button>
                                    </div>
                                </div>
                                @error('quantity')
                                    <p class="text-xs text-destructive flex items-center gap-1 mt-1">
                                        <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                                        {{ $message }}
                                    </p>
                                @enderror

                                {{-- Mode Selection for Multiple Letters (Apple Segmented Style) --}}
                                <div id="multiple_mode_selection" class="mt-4 p-4 sm:p-5 bg-gradient-to-br from-orange-50/70 to-amber-50/30 rounded-2xl border border-orange-200/70 shadow-2xs" style="display: none;">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center gap-2 text-xs font-bold text-orange-950 uppercase tracking-wider">
                                            <i data-lucide="sliders" class="w-3.5 h-3.5 text-orange-600"></i>
                                            Pilihan Mode Pengisian Surat Multipel
                                        </div>
                                        <span class="text-[11px] font-bold text-orange-700 bg-orange-100/80 px-2 py-0.5 rounded-full" id="multiple_badge_count">
                                            Kuantitas > 1
                                        </span>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <label class="relative flex items-start gap-3 p-3.5 rounded-xl border border-orange-200/80 bg-white/95 hover:bg-white hover:border-orange-400 cursor-pointer transition-all shadow-2xs group">
                                            <input type="radio" name="multiple_mode" value="same" class="mt-0.5 w-4 h-4 text-orange-600 focus:ring-orange-500 accent-orange-600" checked>
                                            <div class="text-xs">
                                                <div class="flex items-center gap-1.5 font-bold text-gray-900 group-hover:text-orange-600 transition-colors">
                                                    <span>Sama Semua</span>
                                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-orange-100 text-orange-800">Rekomendasi</span>
                                                </div>
                                                <p class="text-gray-500 mt-1 leading-relaxed">Perihal dan tujuan sama persis untuk seluruh surat yang digenerate berurutan.</p>
                                            </div>
                                        </label>

                                        <label class="relative flex items-start gap-3 p-3.5 rounded-xl border border-orange-200/80 bg-white/95 hover:bg-white hover:border-orange-400 cursor-pointer transition-all shadow-2xs group">
                                            <input type="radio" name="multiple_mode" value="different" class="mt-0.5 w-4 h-4 text-orange-600 focus:ring-orange-500 accent-orange-600">
                                            <div class="text-xs">
                                                <div class="font-bold text-gray-900 group-hover:text-orange-600 transition-colors">
                                                    Berbeda Tiap Surat
                                                </div>
                                                <p class="text-gray-500 mt-1 leading-relaxed">Isi perihal dan tujuan secara spesifik satu per satu untuk masing-masing surat.</p>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- STEP 3: Isi & Substansi Surat --}}
                    <div class="mac-card p-6 sm:p-7">
                        <div class="flex items-center gap-3.5 mb-5 pb-4 border-b border-gray-100">
                            <div class="mac-step-badge">3</div>
                            <div>
                                <h3 class="text-base font-bold text-gray-900 tracking-tight flex items-center gap-2">
                                    Perihal & Tujuan Surat
                                    <span class="text-[11px] font-normal text-gray-400">• Step 3/3</span>
                                </h3>
                                <p class="text-xs text-gray-500 mt-0.5" id="section3_description">Masukkan perihal surat dan nama instansi/jabatan tujuan.</p>
                            </div>
                        </div>

                        {{-- Single Mode (Default) --}}
                        <div id="single_letter_content" class="space-y-5">
                            {{-- Perihal --}}
                            <div class="space-y-1.5">
                                <label for="subject" class="text-xs font-semibold text-gray-700 flex items-center justify-between">
                                    <span class="flex items-center gap-1.5">
                                        <i data-lucide="align-left" class="w-3.5 h-3.5 text-gray-400"></i>
                                        Perihal / Hal Surat <span class="text-destructive">*</span>
                                    </span>
                                    <span class="text-[11px] text-gray-400" id="subject_char_count">0 karakter</span>
                                </label>
                                <input type="text" id="subject" name="subject" value="{{ old('subject') }}"
                                    class="input @error('subject') !border-red-500 @enderror text-sm font-medium"
                                    placeholder="Contoh: Permohonan Izin Penelitian Lapangan Mahasiswa" required>
                                
                                {{-- Quick Subject Template Suggestions --}}
                                <div class="flex flex-wrap items-center gap-1.5 pt-1">
                                    <span class="text-[10px] text-gray-400 font-medium">Contoh Cepat:</span>
                                    <button type="button" class="btn-subject-preset text-[11px] px-2 py-0.5 rounded-md bg-gray-50 hover:bg-orange-50 text-gray-600 hover:text-orange-700 border border-gray-200/60 transition-colors">
                                        Permohonan Izin Penelitian
                                    </button>
                                    <button type="button" class="btn-subject-preset text-[11px] px-2 py-0.5 rounded-md bg-gray-50 hover:bg-orange-50 text-gray-600 hover:text-orange-700 border border-gray-200/60 transition-colors">
                                        Surat Keterangan Aktif Kuliah
                                    </button>
                                    <button type="button" class="btn-subject-preset text-[11px] px-2 py-0.5 rounded-md bg-gray-50 hover:bg-orange-50 text-gray-600 hover:text-orange-700 border border-gray-200/60 transition-colors">
                                        Undangan Rapat Koordinasi
                                    </button>
                                </div>

                                @error('subject')
                                    <p class="text-xs text-destructive flex items-center gap-1 mt-1">
                                        <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Ditujukan Kepada --}}
                            <div class="space-y-1.5">
                                <label for="recipient" class="text-xs font-semibold text-gray-700 flex items-center justify-between">
                                    <span class="flex items-center gap-1.5">
                                        <i data-lucide="send" class="w-3.5 h-3.5 text-gray-400"></i>
                                        Ditujukan Kepada <span class="text-destructive">*</span>
                                    </span>
                                    <span class="text-[11px] text-gray-400">Instansi / Pejabat</span>
                                </label>
                                <input type="text" id="recipient" name="recipient" value="{{ old('recipient') }}"
                                    class="input @error('recipient') !border-red-500 @enderror text-sm font-medium"
                                    placeholder="Contoh: Dekan Fakultas Ekonomi dan Bisnis Universitas Negeri Jakarta" required>
                                
                                {{-- Quick Recipient Suggestions --}}
                                <div class="flex flex-wrap items-center gap-1.5 pt-1">
                                    <span class="text-[10px] text-gray-400 font-medium">Contoh Cepat:</span>
                                    <button type="button" class="btn-recipient-preset text-[11px] px-2 py-0.5 rounded-md bg-gray-50 hover:bg-orange-50 text-gray-600 hover:text-orange-700 border border-gray-200/60 transition-colors">
                                        Dekan FEB UNJ
                                    </button>
                                    <button type="button" class="btn-recipient-preset text-[11px] px-2 py-0.5 rounded-md bg-gray-50 hover:bg-orange-50 text-gray-600 hover:text-orange-700 border border-gray-200/60 transition-colors">
                                        Wakil Rektor Bidang Akademik UNJ
                                    </button>
                                    <button type="button" class="btn-recipient-preset text-[11px] px-2 py-0.5 rounded-md bg-gray-50 hover:bg-orange-50 text-gray-600 hover:text-orange-700 border border-gray-200/60 transition-colors">
                                        Pimpinan Instansi / Perusahaan Terkait
                                    </button>
                                </div>

                                @error('recipient')
                                    <p class="text-xs text-destructive flex items-center gap-1 mt-1">
                                        <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                        {{-- Multiple Different Mode (Hidden by default) --}}
                        <div id="multiple_letter_content" class="space-y-4" style="display: none;">
                            {{-- Info Banner --}}
                            <div class="p-4 bg-orange-50/60 rounded-2xl border border-orange-200/70 shadow-2xs">
                                <div class="flex items-start gap-3">
                                    <div class="w-8 h-8 rounded-xl bg-orange-100 flex items-center justify-center flex-shrink-0 text-orange-600 shadow-2xs">
                                        <i data-lucide="list-ordered" class="w-4 h-4"></i>
                                    </div>
                                    <div class="text-xs">
                                        <p class="font-bold text-orange-950">Mode Isian Berbeda untuk Setiap Surat</p>
                                        <p class="text-orange-800/80 mt-0.5">Isi rincian perihal dan tujuan di bawah. Seluruh nomor surat akan diterbitkan berurutan oleh sistem.</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Validation Errors for Multiple Mode --}}
                            @if ($errors->has('subjects') || $errors->has('subjects.*') || $errors->has('recipients') || $errors->has('recipients.*'))
                                <div class="p-4 bg-red-50/90 border border-red-200 rounded-2xl">
                                    <div class="flex items-start gap-2.5">
                                        <i data-lucide="alert-triangle" class="w-4 h-4 text-red-500 flex-shrink-0 mt-0.5"></i>
                                        <div>
                                            <p class="text-xs font-bold text-red-800">Terdapat kesalahan isian surat:</p>
                                            <ul class="mt-1.5 text-xs text-red-700 list-disc list-inside space-y-1">
                                                @foreach ($errors->all() as $error)
                                                    @if (str_contains($error, 'Perihal') || str_contains($error, 'Tujuan') || str_contains($error, 'subjects') || str_contains($error, 'recipients'))
                                                        <li>{{ $error }}</li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Dynamic Letter Items Container --}}
                            <div id="letter_items_container" class="space-y-3.5">
                                {{-- Dynamically generated --}}
                            </div>

                            {{-- Quick Actions --}}
                            <div class="pt-2 flex flex-wrap items-center gap-2.5">
                                <button type="button" id="btn_copy_first_to_all"
                                    class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-bold text-gray-700 bg-white hover:bg-gray-50 border border-gray-200 rounded-xl shadow-2xs hover:shadow-xs transition-all active:scale-[0.98]">
                                    <i data-lucide="copy" class="w-3.5 h-3.5 text-gray-500"></i>
                                    Salin Surat 1 ke Semua
                                </button>
                                <button type="button" id="btn_clear_all"
                                    class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-bold text-red-600 bg-white hover:bg-red-50/60 border border-red-200 rounded-xl shadow-2xs hover:shadow-xs transition-all active:scale-[0.98]">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5 text-red-500"></i>
                                    Kosongkan Semua
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- STEP 4: Informasi Tambahan (Conditional) --}}
                    <div id="additional_section" style="display: none;">
                        <div class="mac-card p-6 sm:p-7 border-orange-200/80 bg-gradient-to-br from-white to-orange-50/20">
                            <div class="flex items-center gap-3.5 mb-5 pb-4 border-b border-orange-100">
                                <div class="mac-step-badge bg-orange-100 border-orange-300 text-orange-700">4</div>
                                <div>
                                    <h3 class="text-base font-bold text-gray-900 tracking-tight flex items-center gap-2">
                                        Informasi Tambahan Khusus
                                        <span class="text-[11px] font-semibold text-orange-600 bg-orange-100/70 px-2 py-0.5 rounded-full">Wajib untuk Jenis Surat Ini</span>
                                    </h3>
                                    <p class="text-xs text-gray-500 mt-0.5">Lengkapi data keperluan spesifik dan nama mahasiswa yang bersangkutan.</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                {{-- Keperluan Surat --}}
                                <div id="letter_purpose_field" class="space-y-1.5" style="display: none;">
                                    <label for="letter_purpose_id" class="text-xs font-semibold text-gray-700 flex items-center gap-1.5">
                                        <i data-lucide="clipboard-list" class="w-3.5 h-3.5 text-gray-400"></i>
                                        Keperluan Surat <span class="text-destructive purpose-required">*</span>
                                    </label>
                                    <select id="letter_purpose_id" name="letter_purpose_id"
                                        class="select @error('letter_purpose_id') !border-red-500 @enderror">
                                        <option value="">-- Pilih Keperluan --</option>
                                        @foreach ($letterPurposes as $purpose)
                                            <option value="{{ $purpose->id }}"
                                                {{ old('letter_purpose_id') == $purpose->id ? 'selected' : '' }}>
                                                {{ $purpose->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('letter_purpose_id')
                                        <p class="text-xs text-destructive flex items-center gap-1 mt-1">
                                            <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                {{-- Nama Mahasiswa --}}
                                <div id="student_name_field" class="space-y-1.5" style="display: none;">
                                    <label for="student_name" class="text-xs font-semibold text-gray-700 flex items-center gap-1.5">
                                        <i data-lucide="user" class="w-3.5 h-3.5 text-gray-400"></i>
                                        Nama Lengkap Mahasiswa <span class="text-destructive student-required">*</span>
                                    </label>
                                    <input type="text" id="student_name" name="student_name"
                                        value="{{ old('student_name') }}"
                                        class="input @error('student_name') !border-red-500 @enderror text-sm font-medium"
                                        placeholder="Masukkan nama lengkap mahasiswa">
                                    @error('student_name')
                                        <p class="text-xs text-destructive flex items-center gap-1 mt-1">
                                            <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- macOS Bottom Action Dock --}}
                    <div class="mac-card p-5 flex flex-col-reverse sm:flex-row items-center justify-between gap-3 bg-gradient-to-r from-gray-50/70 to-white">
                        <a href="{{ route('letters.index') }}"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 text-xs sm:text-sm font-semibold text-gray-700 hover:text-gray-900 bg-white hover:bg-gray-100/90 rounded-xl border border-gray-200/90 shadow-2xs hover:shadow-xs transition-all active:scale-[0.98]">
                            <i data-lucide="arrow-left" class="w-4 h-4 text-gray-500"></i>
                            Kembali ke Daftar
                        </a>
                        <button type="submit" id="btn_submit_letter"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-8 py-3 text-xs sm:text-sm font-bold text-white bg-gradient-to-r from-[#FF4D00] to-[#ff6a00] hover:from-[#e04400] hover:to-[#eb5e00] rounded-xl shadow-[0_4px_16px_rgba(255,77,0,0.32)] hover:shadow-[0_6px_22px_rgba(255,77,0,0.42)] active:scale-[0.98] transition-all">
                            <i data-lucide="check-circle" class="w-4 h-4"></i>
                            Simpan & Generate Nomor Resmi
                        </button>
                    </div>

                </div>

                {{-- RIGHT COLUMN: Live Simulator & Progress Inspector (4 Cols, Sticky) --}}
                <div class="lg:col-span-4 space-y-5 lg:sticky lg:top-20">

                    {{-- 🌟 CARD 1: Live Letter Number Simulator (Warm Orange Apple Card Theme) --}}
                    <div class="simulator-orange-card p-5 sm:p-6">
                        <div class="flex items-center justify-between mb-4 pb-3 border-b border-orange-200/80">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-xl bg-orange-500 text-white flex items-center justify-center shadow-xs">
                                    <i data-lucide="hash" class="w-4 h-4"></i>
                                </div>
                                <span class="text-xs font-extrabold text-gray-900 tracking-tight">Pratinjau Format Nomor</span>
                            </div>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-orange-100/90 text-orange-800 border border-orange-300/80">
                                <span class="w-1.5 h-1.5 rounded-full bg-orange-500 animate-pulse"></span>
                                Live Sync
                            </span>
                        </div>

                        {{-- Main Simulated Number Result Box (Clean White Inset) --}}
                        <div class="p-3.5 rounded-2xl bg-white border-2 border-orange-300/90 mb-4 shadow-xs">
                            <div class="flex items-center justify-between text-[11px] font-semibold text-gray-500 mb-1.5">
                                <span>Format Nomor Resmi</span>
                                <span id="sim_badge_qty" class="text-orange-600 font-bold bg-orange-50 px-2 py-0.5 rounded-md border border-orange-200/70 text-[10px]">
                                    1 Surat
                                </span>
                            </div>
                            <div id="simulated_full_number" class="font-mono font-bold text-xs sm:text-sm text-[#FF4D00] tracking-tight break-all leading-normal py-0.5">
                                B / [NO. URUT] / UN39.11 / PK.01 / {{ now()->year }}
                            </div>
                            <div id="sim_running_info" class="mt-2 pt-2 border-t border-gray-100 text-[11px] text-gray-600 flex items-center gap-1.5">
                                <i data-lucide="info" class="w-3.5 h-3.5 text-orange-500 flex-shrink-0"></i>
                                <span id="sim_running_text">Pilih Jenis Surat untuk melihat nomor urut berikutnya.</span>
                            </div>
                        </div>

                        {{-- Exploded Token Breakdown Pills --}}
                        <div class="space-y-2 text-xs">
                            <div class="flex items-center justify-between py-2 px-3 rounded-xl bg-white/90 border border-orange-200/70 shadow-2xs">
                                <span class="text-gray-600 font-medium flex items-center gap-2">
                                    <i data-lucide="shield" class="w-3.5 h-3.5 text-orange-500"></i>
                                    Sifat Keamanan
                                </span>
                                <span id="sim_token_sec" class="font-mono font-bold text-orange-700 px-2 py-0.5 rounded-md bg-orange-50 border border-orange-200">
                                    B
                                </span>
                            </div>

                            <div class="flex items-center justify-between py-2 px-3 rounded-xl bg-white/90 border border-orange-200/70 shadow-2xs">
                                <span class="text-gray-600 font-medium flex items-center gap-2">
                                    <i data-lucide="hash" class="w-3.5 h-3.5 text-blue-500"></i>
                                    Nomor Urut
                                </span>
                                <span id="sim_token_running" class="font-mono font-bold text-blue-700 px-2 py-0.5 rounded-md bg-blue-50 border border-blue-200">
                                    [No. Urut]
                                </span>
                            </div>

                            <div class="flex items-center justify-between py-2 px-3 rounded-xl bg-white/90 border border-orange-200/70 shadow-2xs">
                                <span class="text-gray-600 font-medium flex items-center gap-2">
                                    <i data-lucide="target" class="w-3.5 h-3.5 text-emerald-500"></i>
                                    Sasaran & Pejabat
                                </span>
                                <span id="sim_token_signatory" class="font-mono font-bold text-emerald-700 px-2 py-0.5 rounded-md bg-emerald-50 border border-emerald-200 max-w-[150px] truncate">
                                    UN39.11
                                </span>
                            </div>

                            <div class="flex items-center justify-between py-2 px-3 rounded-xl bg-white/90 border border-orange-200/70 shadow-2xs">
                                <span class="text-gray-600 font-medium flex items-center gap-2">
                                    <i data-lucide="folder" class="w-3.5 h-3.5 text-purple-500"></i>
                                    Klasifikasi Berkas
                                </span>
                                <span id="sim_token_class" class="font-mono font-bold text-purple-700 px-2 py-0.5 rounded-md bg-purple-50 border border-purple-200 max-w-[150px] truncate">
                                    ...
                                </span>
                            </div>

                            <div class="flex items-center justify-between py-2 px-3 rounded-xl bg-white/90 border border-orange-200/70 shadow-2xs">
                                <span class="text-gray-600 font-medium flex items-center gap-2">
                                    <i data-lucide="calendar" class="w-3.5 h-3.5 text-amber-500"></i>
                                    Tahun Anggaran
                                </span>
                                <span id="sim_token_year" class="font-mono font-bold text-amber-700 px-2 py-0.5 rounded-md bg-amber-50 border border-amber-200">
                                    {{ now()->year }}
                                </span>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-t border-orange-200/70 text-[11px] text-gray-500 flex items-start gap-2">
                            <i data-lucide="shield-check" class="w-3.5 h-3.5 text-orange-600 flex-shrink-0 mt-0.5"></i>
                            <span>Nomor urut resmi dikelola otomatis per jenis surat & tahun anggaran untuk mencegah duplikasi.</span>
                        </div>
                    </div>

                    {{-- 🌟 CARD 2: Form Completeness Progress Tracker --}}
                    <div class="mac-card p-5">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-xs font-bold text-gray-900 tracking-tight flex items-center gap-1.5">
                                <i data-lucide="check-circle-2" class="w-4 h-4 text-orange-600"></i>
                                Kelengkapan Formulir
                            </h4>
                            <span id="progress_percentage" class="text-xs font-extrabold text-orange-600">
                                40%
                            </span>
                        </div>

                        {{-- Progress Bar --}}
                        <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden mb-4 shadow-inner">
                            <div id="progress_bar_fill"
                                class="h-2 rounded-full bg-gradient-to-r from-[#FF4D00] to-amber-500 transition-all duration-300"
                                style="width: 40%">
                            </div>
                        </div>

                        {{-- Interactive Checklist --}}
                        <div class="space-y-2 text-xs">
                            <div id="chk_step1" class="flex items-center justify-between text-gray-600 py-1">
                                <span class="flex items-center gap-2">
                                    <i data-lucide="circle-dashed" class="w-3.5 h-3.5 text-gray-400 chk-icon"></i>
                                    Klasifikasi & Sasaran
                                </span>
                                <span class="chk-status text-[11px] font-medium text-gray-400">Belum</span>
                            </div>

                            <div id="chk_step2" class="flex items-center justify-between text-gray-600 py-1">
                                <span class="flex items-center gap-2">
                                    <i data-lucide="circle-dashed" class="w-3.5 h-3.5 text-gray-400 chk-icon"></i>
                                    Penandatangan & Tanggal
                                </span>
                                <span class="chk-status text-[11px] font-medium text-gray-400">Belum</span>
                            </div>

                            <div id="chk_step3" class="flex items-center justify-between text-gray-600 py-1">
                                <span class="flex items-center gap-2">
                                    <i data-lucide="circle-dashed" class="w-3.5 h-3.5 text-gray-400 chk-icon"></i>
                                    Perihal / Pokok Surat
                                </span>
                                <span class="chk-status text-[11px] font-medium text-gray-400">Belum</span>
                            </div>

                            <div id="chk_step4" class="flex items-center justify-between text-gray-600 py-1">
                                <span class="flex items-center gap-2">
                                    <i data-lucide="circle-dashed" class="w-3.5 h-3.5 text-gray-400 chk-icon"></i>
                                    Instansi / Pejabat Tujuan
                                </span>
                                <span class="chk-status text-[11px] font-medium text-gray-400">Belum</span>
                            </div>
                        </div>
                    </div>

                    {{-- 🌟 CARD 3: Panduan Singkat Penomoran Surat FEB UNJ --}}
                    <div class="mac-card p-5 bg-gradient-to-br from-white to-gray-50/60">
                        <div class="flex items-center gap-2 text-xs font-bold text-gray-900 mb-3">
                            <i data-lucide="book-open" class="w-4 h-4 text-orange-600"></i>
                            Panduan Penomoran Surat FEB UNJ
                        </div>
                        <ul class="text-xs text-gray-600 space-y-2 leading-relaxed">
                            <li class="flex items-start gap-2">
                                <i data-lucide="check" class="w-3.5 h-3.5 text-emerald-600 mt-0.5 flex-shrink-0"></i>
                                <span><strong>Sasaran Eksternal:</strong> Ditujukan ke luar universitas. Kode "UN39." disisipkan otomatis.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i data-lucide="check" class="w-3.5 h-3.5 text-emerald-600 mt-0.5 flex-shrink-0"></i>
                                <span><strong>Penerbitan Batch:</strong> Cocok untuk surat tugas atau permohonan berantai hingga 50 surat.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i data-lucide="check" class="w-3.5 h-3.5 text-emerald-600 mt-0.5 flex-shrink-0"></i>
                                <span><strong>Tahun Anggaran:</strong> Diambil otomatis dari tanggal penetapan yang dipilih.</span>
                            </li>
                        </ul>
                    </div>

                </div>

            </div>
        </form>
    </div>

    @push('scripts')
        <script src="https://unpkg.com/lucide@latest"></script>
        <script>
            lucide.createIcons();

            // Real Sequence mappings passed from Backend LetterSequence
            const letterTypeSequences = @json($sequences ?? []);

            // JSON Mappings for Live Simulator
            const signatoriesData = {
                @foreach ($signatories as $sig)
                    {{ $sig->id }}: {
                        name: @json($sig->name),
                        code: @json($sig->code ?? ''),
                        position: @json($sig->position ?? '')
                    },
                @endforeach
            };

            const classificationsData = {
                @foreach ($classifications as $cls)
                    {{ $cls->id }}: {
                        name: @json($cls->name),
                        code: @json($cls->code ?? '')
                    },
                @endforeach
            };

            // Letter types data with requires_purpose flag
            const letterTypesData = {
                @foreach ($letterTypes as $type)
                    {{ $type->id }}: {
                        requires_purpose: {{ $type->requires_purpose ? 'true' : 'false' }}
                    },
                @endforeach
            };

            // State management
            let currentQuantity = 1;
            let currentMode = 'same';

            // Function to update the Live Letter Number Simulator
            function updateSimulator() {
                const sec = document.getElementById('security_classification').value || 'B';
                const target = document.getElementById('letter_target').value || 'internal';
                const letterTypeId = document.getElementById('letter_type_id').value;
                const signatoryId = document.getElementById('signatory_id').value;
                const classId = document.getElementById('classification_id').value;
                const dateVal = document.getElementById('letter_date').value;
                const quantity = parseInt(document.getElementById('quantity').value) || 1;

                // Signatory Code
                let sigCode = '...';
                if (signatoryId && signatoriesData[signatoryId]) {
                    sigCode = signatoriesData[signatoryId].code || '...';
                }

                // Target code
                let targetCode = '';
                if (target === 'external' && !sigCode.includes('UN39')) {
                    targetCode = 'UN39.';
                }
                const combinedSig = targetCode + sigCode;

                // Classification Code
                let classCode = '...';
                if (classId && classificationsData[classId]) {
                    classCode = classificationsData[classId].code || '...';
                }

                // Year
                let year = (new Date()).getFullYear();
                if (dateVal) {
                    const parsedYear = new Date(dateVal).getFullYear();
                    if (!isNaN(parsedYear)) year = parsedYear;
                }

                // Accurate running number from database sequence
                let runningStr = '[NO. URUT]';
                let runningToken = '[No. Urut]';
                let runningInfo = 'Pilih Jenis Surat untuk melihat nomor urut berikutnya.';

                if (letterTypeId && letterTypeSequences[letterTypeId] !== undefined) {
                    const nextNum = parseInt(letterTypeSequences[letterTypeId]) || 1;
                    if (quantity > 1) {
                        const startRange = String(nextNum).padStart(3, '0');
                        const endRange = String(nextNum + quantity - 1).padStart(3, '0');
                        runningStr = `${startRange} — ${endRange}`;
                        runningToken = `${startRange} — ${endRange}`;
                        runningInfo = `Nomor urut berikutnya untuk jenis ini: #${startRange} s/d #${endRange} (Estimasi Nyata).`;
                    } else {
                        const formattedNum = String(nextNum).padStart(3, '0');
                        runningStr = formattedNum;
                        runningToken = formattedNum;
                        runningInfo = `Nomor urut berikutnya untuk jenis ini: #${formattedNum} (Estimasi Nyata).`;
                    }
                }

                // Full simulated number
                const fullSimulated = `${sec} / ${runningStr} / ${combinedSig} / ${classCode} / ${year}`;

                // Update DOM elements
                const fullEl = document.getElementById('simulated_full_number');
                if (fullEl) {
                    fullEl.textContent = fullSimulated;
                    fullEl.classList.add('token-updated');
                    setTimeout(() => fullEl.classList.remove('token-updated'), 350);
                }

                const simSec = document.getElementById('sim_token_sec');
                if (simSec) simSec.textContent = sec;

                const simRun = document.getElementById('sim_token_running');
                if (simRun) simRun.textContent = runningToken;

                const simSig = document.getElementById('sim_token_signatory');
                if (simSig) simSig.textContent = combinedSig;

                const simCls = document.getElementById('sim_token_class');
                if (simCls) simCls.textContent = classCode;

                const simYr = document.getElementById('sim_token_year');
                if (simYr) simYr.textContent = year;

                const badgeQty = document.getElementById('sim_badge_qty');
                if (badgeQty) badgeQty.textContent = `${quantity} Surat`;

                const simRunningText = document.getElementById('sim_running_text');
                if (simRunningText) simRunningText.textContent = runningInfo;

                // Update progress tracker
                updateProgress();
            }

            // Function to update Form Progress Checklist
            function updateProgress() {
                let score = 0;
                const total = 4;

                const sec = document.getElementById('security_classification').value;
                const target = document.getElementById('letter_target').value;
                const letterType = document.getElementById('letter_type_id').value;
                const classId = document.getElementById('classification_id').value;
                const signatoryId = document.getElementById('signatory_id').value;
                const dateVal = document.getElementById('letter_date').value;
                const subject = document.getElementById('subject').value.trim();
                const recipient = document.getElementById('recipient').value.trim();

                // Step 1: Security + Target + Type + Class
                const chk1 = document.getElementById('chk_step1');
                if (sec && target && letterType && classId) {
                    score++;
                    setCheckItem(chk1, true);
                } else {
                    setCheckItem(chk1, false);
                }

                // Step 2: Signatory + Date
                const chk2 = document.getElementById('chk_step2');
                if (signatoryId && dateVal) {
                    score++;
                    setCheckItem(chk2, true);
                } else {
                    setCheckItem(chk2, false);
                }

                // Step 3: Subject
                const chk3 = document.getElementById('chk_step3');
                if (subject.length > 2) {
                    score++;
                    setCheckItem(chk3, true);
                } else {
                    setCheckItem(chk3, false);
                }

                // Step 4: Recipient
                const chk4 = document.getElementById('chk_step4');
                if (recipient.length > 2) {
                    score++;
                    setCheckItem(chk4, true);
                } else {
                    setCheckItem(chk4, false);
                }

                const percentage = Math.round((score / total) * 100);
                const percentEl = document.getElementById('progress_percentage');
                const barFillEl = document.getElementById('progress_bar_fill');

                if (percentEl) percentEl.textContent = `${percentage}% Selesai`;
                if (barFillEl) barFillEl.style.width = `${percentage}%`;
            }

            function setCheckItem(itemEl, isComplete) {
                if (!itemEl) return;
                const icon = itemEl.querySelector('.chk-icon');
                const status = itemEl.querySelector('.chk-status');

                if (isComplete) {
                    itemEl.classList.remove('text-gray-600');
                    itemEl.classList.add('text-emerald-700', 'font-semibold');
                    if (icon) {
                        icon.setAttribute('data-lucide', 'check-circle');
                        icon.classList.remove('text-gray-400');
                        icon.classList.add('text-emerald-500');
                    }
                    if (status) {
                        status.textContent = 'Lengkap';
                        status.classList.remove('text-gray-400');
                        status.classList.add('text-emerald-600', 'font-bold');
                    }
                } else {
                    itemEl.classList.remove('text-emerald-700', 'font-semibold');
                    itemEl.classList.add('text-gray-600');
                    if (icon) {
                        icon.setAttribute('data-lucide', 'circle-dashed');
                        icon.classList.remove('text-emerald-500');
                        icon.classList.add('text-gray-400');
                    }
                    if (status) {
                        status.textContent = 'Belum';
                        status.classList.remove('text-emerald-600', 'font-bold');
                        status.classList.add('text-gray-400');
                    }
                }
                lucide.createIcons();
            }

            // Function to toggle conditional fields
            function toggleConditionalFields(letterTypeId) {
                const additionalSection = document.getElementById('additional_section');
                const letterPurposeField = document.getElementById('letter_purpose_field');
                const studentNameField = document.getElementById('student_name_field');
                const letterPurposeSelect = document.getElementById('letter_purpose_id');
                const studentNameInput = document.getElementById('student_name');

                if (letterTypeId && letterTypesData[letterTypeId] && letterTypesData[letterTypeId].requires_purpose) {
                    additionalSection.style.display = 'block';
                    letterPurposeField.style.display = 'block';
                    studentNameField.style.display = 'block';
                    letterPurposeSelect.setAttribute('required', 'required');
                    studentNameInput.setAttribute('required', 'required');
                } else {
                    additionalSection.style.display = 'none';
                    letterPurposeField.style.display = 'none';
                    studentNameField.style.display = 'none';
                    letterPurposeSelect.removeAttribute('required');
                    studentNameInput.removeAttribute('required');
                    letterPurposeSelect.value = '';
                    studentNameInput.value = '';
                }
                updateSimulator();
            }

            // Function to create a letter item card (Apple macOS card aesthetic)
            function createLetterItemCard(index) {
                const cardNumber = index + 1;
                return `
                    <div class="letter-item-card bg-gray-50/60 hover:bg-white border border-gray-200/90 hover:border-orange-300 rounded-2xl p-4 sm:p-5 transition-all duration-200 shadow-2xs hover:shadow-xs" data-index="${index}">
                        <div class="flex items-center justify-between gap-3 mb-3 pb-2.5 border-b border-gray-200/60">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-xl bg-gradient-to-br from-[#FF4D00] to-orange-500 text-white text-xs font-extrabold flex items-center justify-center shadow-xs">
                                    ${cardNumber}
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-gray-800 tracking-tight">Surat ke-${cardNumber}</h4>
                                    <p class="text-[10px] text-gray-400">Nomor resmi di-generate otomatis</p>
                                </div>
                            </div>
                            <button type="button" class="btn-copy-previous inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-semibold text-gray-600 hover:text-orange-600 bg-white hover:bg-orange-50 border border-gray-200 rounded-lg transition-all" title="Salin dari surat sebelumnya" ${index === 0 ? 'style="display:none"' : ''}>
                                <i data-lucide="copy" class="w-3 h-3 text-gray-400"></i>
                                Salin Surat Sebelumnya
                            </button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="text-[11px] font-semibold text-gray-700 flex items-center gap-1">
                                    <i data-lucide="align-left" class="w-3 h-3 text-gray-400"></i>
                                    Perihal Surat <span class="text-destructive">*</span>
                                </label>
                                <input type="text" 
                                    name="subjects[${index}]" 
                                    class="input input-sm text-xs font-medium" 
                                    placeholder="Masukkan perihal surat ke-${cardNumber}"
                                    required>
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-[11px] font-semibold text-gray-700 flex items-center gap-1">
                                    <i data-lucide="send" class="w-3 h-3 text-gray-400"></i>
                                    Ditujukan Kepada <span class="text-destructive">*</span>
                                </label>
                                <input type="text" 
                                    name="recipients[${index}]" 
                                    class="input input-sm text-xs font-medium" 
                                    placeholder="Masukkan instansi / pejabat tujuan surat ke-${cardNumber}"
                                    required>
                            </div>
                        </div>
                    </div>
                `;
            }

            // Function to generate letter items based on quantity
            function generateLetterItems(quantity) {
                const container = document.getElementById('letter_items_container');
                container.innerHTML = '';
                
                for (let i = 0; i < quantity; i++) {
                    container.insertAdjacentHTML('beforeend', createLetterItemCard(i));
                }
                
                lucide.createIcons();
                
                document.querySelectorAll('#letter_items_container input').forEach(input => {
                    input.setAttribute('required', 'required');
                });
                
                // Add copy listener
                document.querySelectorAll('.btn-copy-previous').forEach((btn, index) => {
                    btn.addEventListener('click', function() {
                        if (index > 0) {
                            const prevCard = document.querySelector(`.letter-item-card[data-index="${index - 1}"]`);
                            const currentCard = document.querySelector(`.letter-item-card[data-index="${index}"]`);
                            
                            if (prevCard && currentCard) {
                                const prevSubject = prevCard.querySelector('input[name^="subjects"]').value;
                                const prevRecipient = prevCard.querySelector('input[name^="recipients"]').value;
                                
                                currentCard.querySelector('input[name^="subjects"]').value = prevSubject;
                                currentCard.querySelector('input[name^="recipients"]').value = prevRecipient;
                                
                                currentCard.classList.add('ring-2', 'ring-orange-500/40');
                                setTimeout(() => {
                                    currentCard.classList.remove('ring-2', 'ring-orange-500/40');
                                }, 500);
                            }
                        }
                    });
                });
            }

            // Function to update UI based on quantity and mode
            function updateMultipleLetterUI() {
                const quantity = parseInt(document.getElementById('quantity').value) || 1;
                const modeSelection = document.getElementById('multiple_mode_selection');
                const singleContent = document.getElementById('single_letter_content');
                const multipleContent = document.getElementById('multiple_letter_content');
                const subjectInput = document.getElementById('subject');
                const recipientInput = document.getElementById('recipient');
                const section3Desc = document.getElementById('section3_description');
                const badgeCount = document.getElementById('multiple_badge_count');

                currentQuantity = quantity;

                // Sync preset chip styles
                document.querySelectorAll('.qty-preset-chip').forEach(chip => {
                    const val = parseInt(chip.dataset.qty);
                    if (val === quantity) {
                        chip.classList.add('active');
                    } else {
                        chip.classList.remove('active');
                    }
                });

                if (quantity > 1) {
                    modeSelection.style.display = 'block';
                    if (badgeCount) badgeCount.textContent = `${quantity} Surat Diterbitkan`;
                    
                    const selectedMode = document.querySelector('input[name="multiple_mode"]:checked').value;
                    currentMode = selectedMode;
                    
                    if (selectedMode === 'different') {
                        singleContent.style.display = 'none';
                        multipleContent.style.display = 'block';
                        
                        subjectInput.removeAttribute('required');
                        recipientInput.removeAttribute('required');
                        
                        generateLetterItems(quantity);
                        section3Desc.textContent = `Masukkan perihal dan tujuan khusus untuk ${quantity} surat`;
                    } else {
                        document.querySelectorAll('#letter_items_container input').forEach(input => {
                            input.removeAttribute('required');
                        });
                        
                        singleContent.style.display = 'block';
                        multipleContent.style.display = 'none';
                        
                        subjectInput.setAttribute('required', 'required');
                        recipientInput.setAttribute('required', 'required');
                        
                        section3Desc.textContent = `Perihal dan tujuan yang sama akan diterapkan untuk ${quantity} surat`;
                    }
                } else {
                    modeSelection.style.display = 'none';
                    singleContent.style.display = 'block';
                    multipleContent.style.display = 'none';
                    
                    document.querySelectorAll('#letter_items_container input').forEach(input => {
                        input.removeAttribute('required');
                    });
                    
                    subjectInput.setAttribute('required', 'required');
                    recipientInput.setAttribute('required', 'required');
                    
                    section3Desc.textContent = 'Masukkan perihal pokok surat dan nama instansi/jabatan tujuan.';
                }

                updateSimulator();
            }

            // Floating Toast Popup Manager
            let toastTimer = null;

            function showToast(message, type = 'error') {
                const toast = document.getElementById('toastPopup');
                const toastMsg = document.getElementById('toastMessage');
                const toastIconWrap = document.getElementById('toastIconWrap');
                const toastIcon = document.getElementById('toastIcon');

                if (!toast || !toastMsg) return;

                toastMsg.textContent = message;

                if (type === 'error') {
                    toast.className = 'toast-popup show fixed top-6 left-1/2 z-50 flex items-center gap-3 px-4.5 py-3 rounded-2xl bg-white/95 backdrop-blur-md border border-red-200/90 text-gray-800 shadow-xl shadow-red-500/10 text-xs sm:text-sm font-medium';
                    toastIconWrap.className = 'w-7 h-7 rounded-xl bg-red-100 text-red-600 flex items-center justify-center flex-shrink-0';
                    toastIcon.setAttribute('data-lucide', 'alert-circle');
                } else {
                    toast.className = 'toast-popup show fixed top-6 left-1/2 z-50 flex items-center gap-3 px-4.5 py-3 rounded-2xl bg-white/95 backdrop-blur-md border border-emerald-200/90 text-gray-800 shadow-xl shadow-emerald-500/10 text-xs sm:text-sm font-medium';
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

            // Handle form submission with friendly non-technical validation
            const form = document.getElementById('letter-form');
            form.addEventListener('submit', function(e) {
                const sec = $('#security_classification').val();
                const target = $('#letter_target').val();
                const letterTypeId = $('#letter_type_id').val();
                const classId = $('#classification_id').val();
                const sigId = $('#signatory_id').val();
                const dateVal = document.getElementById('letter_date')?.value?.trim();
                const quantityInput = document.getElementById('quantity');
                const quantity = parseInt(quantityInput?.value || '1', 10);
                const multipleModeEl = document.querySelector('input[name="multiple_mode"]:checked');
                const multipleMode = multipleModeEl ? multipleModeEl.value : 'same';

                if (!sec) {
                    e.preventDefault();
                    $('#security_classification').select2('open');
                    showToast('Mohon tentukan Klasifikasi Keamanan surat.', 'error');
                    return false;
                }
                if (!target) {
                    e.preventDefault();
                    $('#letter_target').select2('open');
                    showToast('Mohon pilih Sasaran Surat (Internal / Eksternal).', 'error');
                    return false;
                }
                if (!letterTypeId) {
                    e.preventDefault();
                    $('#letter_type_id').select2('open');
                    showToast('Mohon pilih Jenis Surat yang ingin diterbitkan.', 'error');
                    return false;
                }
                if (!classId) {
                    e.preventDefault();
                    $('#classification_id').select2('open');
                    showToast('Mohon pilih Klasifikasi Surat untuk penomoran resmi.', 'error');
                    return false;
                }
                if (!sigId) {
                    e.preventDefault();
                    $('#signatory_id').select2('open');
                    showToast('Mohon pilih Pejabat Penandatangan surat.', 'error');
                    return false;
                }
                if (!dateVal) {
                    e.preventDefault();
                    document.getElementById('letter_date')?.focus();
                    showToast('Mohon tentukan Tanggal Surat.', 'error');
                    return false;
                }

                // Check subject and recipient based on mode
                if (quantity > 1 && multipleMode === 'different') {
                    let missingCard = null;
                    let missingType = null;
                    document.querySelectorAll('.letter-item-card').forEach((card, idx) => {
                        if (missingCard) return;
                        const sub = card.querySelector('input[name^="subjects"]')?.value?.trim();
                        const rec = card.querySelector('input[name^="recipients"]')?.value?.trim();
                        if (!sub) {
                            missingCard = card;
                            missingType = `Perihal pada surat ke-${idx + 1}`;
                        } else if (!rec) {
                            missingCard = card;
                            missingType = `Tujuan pada surat ke-${idx + 1}`;
                        }
                    });
                    if (missingCard) {
                        e.preventDefault();
                        const targetInput = missingType.startsWith('Perihal') 
                            ? missingCard.querySelector('input[name^="subjects"]') 
                            : missingCard.querySelector('input[name^="recipients"]');
                        if (targetInput) {
                            targetInput.focus();
                            targetInput.classList.add('!border-red-500');
                        }
                        showToast(`Mohon lengkapi ${missingType} sebelum menerbitkan nomor surat.`, 'error');
                        return false;
                    }
                } else {
                    const subjectVal = document.getElementById('subject')?.value?.trim();
                    const recipientVal = document.getElementById('recipient')?.value?.trim();
                    if (!subjectVal) {
                        e.preventDefault();
                        const subEl = document.getElementById('subject');
                        if (subEl) { subEl.focus(); subEl.classList.add('!border-red-500'); }
                        showToast('Mohon lengkapi Perihal Surat sebelum menerbitkan nomor.', 'error');
                        return false;
                    }
                    if (!recipientVal) {
                        e.preventDefault();
                        const recEl = document.getElementById('recipient');
                        if (recEl) { recEl.focus(); recEl.classList.add('!border-red-500'); }
                        showToast('Mohon lengkapi Tujuan / Penerima Surat.', 'error');
                        return false;
                    }
                }

                // Conditional purpose fields
                const purposeSection = document.getElementById('conditional_fields_section');
                if (purposeSection && window.getComputedStyle(purposeSection).display !== 'none') {
                    const purposeId = $('#letter_purpose_id').val();
                    const studentEl = document.getElementById('student_name');
                    const studentVal = studentEl ? studentEl.value.trim() : '';

                    if (!purposeId) {
                        e.preventDefault();
                        $('#letter_purpose_id').select2('open');
                        showToast('Mohon pilih Keperluan Surat Keterangan.', 'error');
                        return false;
                    }
                    if (!studentVal) {
                        e.preventDefault();
                        if (studentEl) { studentEl.focus(); studentEl.classList.add('!border-red-500'); }
                        showToast('Mohon lengkapi Nama Mahasiswa yang bersangkutan.', 'error');
                        return false;
                    }
                }

                // Clean hidden required inputs before native submission
                const inputs = document.querySelectorAll('input[required], select[required], textarea[required]');
                inputs.forEach(input => {
                    let isHidden = false;
                    let element = input;
                    while (element && !isHidden) {
                        if (element.offsetParent === null || window.getComputedStyle(element).display === 'none') {
                            isHidden = true;
                        }
                        element = element.parentElement;
                    }
                    if (isHidden && input.hasAttribute('required')) {
                        input.removeAttribute('required');
                    }
                });

                // Loading button feedback
                const submitBtn = document.getElementById('btn_submit_create') || document.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i> Menerbitkan Nomor Resmi...';
                    lucide.createIcons();
                }
            });

            // Initialize Select2, Stepper, and Listeners
            $(document).ready(function() {
                // Initialize Select2 dropdowns
                $('#letter_type_id').select2({
                    placeholder: '-- Pilih Jenis Surat --',
                    allowClear: false,
                    width: '100%'
                }).on('change', function() {
                    toggleConditionalFields(this.value);
                    updateSimulator();
                });

                $('#classification_id').select2({
                    placeholder: '-- Pilih Klasifikasi --',
                    allowClear: false,
                    width: '100%'
                }).on('change', function() {
                    updateSimulator();
                });

                $('#security_classification').select2({
                    placeholder: '-- Pilih Klasifikasi Keamanan --',
                    allowClear: false,
                    width: '100%'
                }).on('change', function() {
                    updateSimulator();
                });

                $('#letter_target').select2({
                    placeholder: '-- Pilih Sasaran Surat --',
                    allowClear: false,
                    width: '100%'
                }).on('change', function() {
                    updateSimulator();
                });

                $('#signatory_id').select2({
                    placeholder: '-- Pilih Penandatangan --',
                    allowClear: false,
                    width: '100%'
                }).on('change', function() {
                    updateSimulator();
                });

                $('#letter_purpose_id').select2({
                    placeholder: '-- Pilih Keperluan --',
                    allowClear: true,
                    width: '100%'
                }).on('change', function() {
                    updateProgress();
                });

                // Flatpickr for letter date
                flatpickr("#letter_date", {
                    dateFormat: "Y-m-d",
                    locale: "id",
                    altInput: true,
                    altFormat: "d F Y",
                    allowInput: true,
                    disableMobile: true,
                    defaultDate: "{{ old('letter_date', now()->format('Y-m-d')) }}",
                    onChange: function() {
                        updateSimulator();
                    }
                });

                // Apple Stepper Buttons
                const qtyInput = document.getElementById('quantity');
                const btnMinus = document.getElementById('btn_qty_minus');
                const btnPlus = document.getElementById('btn_qty_plus');

                btnMinus.addEventListener('click', function() {
                    let val = parseInt(qtyInput.value) || 1;
                    if (val > 1) {
                        qtyInput.value = val - 1;
                        updateMultipleLetterUI();
                    }
                });

                btnPlus.addEventListener('click', function() {
                    let val = parseInt(qtyInput.value) || 1;
                    if (val < 50) {
                        qtyInput.value = val + 1;
                        updateMultipleLetterUI();
                    }
                });

                // Quantity Preset Chips
                document.querySelectorAll('.qty-preset-chip').forEach(chip => {
                    chip.addEventListener('click', function() {
                        const targetQty = parseInt(this.dataset.qty);
                        if (targetQty) {
                            qtyInput.value = targetQty;
                            updateMultipleLetterUI();
                        }
                    });
                });

                // Quantity manual input & wheel listener
                qtyInput.addEventListener('input', function() {
                    let val = parseInt(this.value);
                    if (val < 1) this.value = 1;
                    if (val > 50) this.value = 50;
                    updateMultipleLetterUI();
                });

                qtyInput.addEventListener('wheel', function(e) {
                    e.preventDefault();
                }, { passive: false });

                // Mode change handler
                document.querySelectorAll('input[name="multiple_mode"]').forEach(radio => {
                    radio.addEventListener('change', function() {
                        updateMultipleLetterUI();
                    });
                });

                // Subject character counter & live update
                const subjectInput = document.getElementById('subject');
                const charCountEl = document.getElementById('subject_char_count');
                subjectInput.addEventListener('input', function() {
                    if (charCountEl) charCountEl.textContent = `${this.value.length} karakter`;
                    updateProgress();
                });

                // Recipient live update
                const recipientInput = document.getElementById('recipient');
                recipientInput.addEventListener('input', function() {
                    updateProgress();
                });

                // Quick Subject Presets
                document.querySelectorAll('.btn-subject-preset').forEach(btn => {
                    btn.addEventListener('click', function() {
                        subjectInput.value = this.textContent.trim();
                        if (charCountEl) charCountEl.textContent = `${subjectInput.value.length} karakter`;
                        updateProgress();
                        subjectInput.focus();
                    });
                });

                // Quick Recipient Presets
                document.querySelectorAll('.btn-recipient-preset').forEach(btn => {
                    btn.addEventListener('click', function() {
                        recipientInput.value = this.textContent.trim();
                        updateProgress();
                        recipientInput.focus();
                    });
                });

                // Copy first to all button
                document.getElementById('btn_copy_first_to_all').addEventListener('click', function() {
                    const firstCard = document.querySelector('.letter-item-card[data-index="0"]');
                    if (!firstCard) return;
                    
                    const firstSubject = firstCard.querySelector('input[name^="subjects"]').value;
                    const firstRecipient = firstCard.querySelector('input[name^="recipients"]').value;
                    
                    document.querySelectorAll('.letter-item-card').forEach((card, index) => {
                        if (index > 0) {
                            card.querySelector('input[name^="subjects"]').value = firstSubject;
                            card.querySelector('input[name^="recipients"]').value = firstRecipient;
                        }
                    });
                    
                    const origHtml = this.innerHTML;
                    this.innerHTML = '<i data-lucide="check" class="w-3.5 h-3.5 text-emerald-600"></i> <span class="text-emerald-700">Tersalin ke Semua!</span>';
                    this.classList.add('bg-emerald-50', 'border-emerald-300');
                    lucide.createIcons();
                    
                    setTimeout(() => {
                        this.innerHTML = origHtml;
                        this.classList.remove('bg-emerald-50', 'border-emerald-300');
                        lucide.createIcons();
                    }, 2000);
                });

                // Clear all button
                document.getElementById('btn_clear_all').addEventListener('click', function() {
                    if (confirm('Kosongkan semua isian perihal dan tujuan surat batch?')) {
                        document.querySelectorAll('.letter-item-card input').forEach(input => {
                            input.value = '';
                        });
                    }
                });

                // Validation error indicators
                @if ($errors->has('letter_type_id'))
                    $('#letter_type_id').next('.select2-container').addClass('select2-error');
                @endif
                @if ($errors->has('classification_id'))
                    $('#classification_id').next('.select2-container').addClass('select2-error');
                @endif
                @if ($errors->has('security_classification'))
                    $('#security_classification').next('.select2-container').addClass('select2-error');
                @endif
                @if ($errors->has('letter_target'))
                    $('#letter_target').next('.select2-container').addClass('select2-error');
                @endif
                @if ($errors->has('signatory_id'))
                    $('#signatory_id').next('.select2-container').addClass('select2-error');
                @endif
                @if ($errors->has('letter_purpose_id'))
                    $('#letter_purpose_id').next('.select2-container').addClass('select2-error');
                @endif

                // Old input handling
                @if (old('letter_type_id'))
                    toggleConditionalFields({{ old('letter_type_id') }});
                @endif
                @if (old('quantity', 1) > 1)
                    updateMultipleLetterUI();
                @endif

                // Initial Simulator & Progress Calculation
                updateSimulator();
                updateProgress();

                // Clear red border on input
                ['subject', 'recipient', 'student_name'].forEach(id => {
                    const el = document.getElementById(id);
                    if (el) {
                        el.addEventListener('input', () => el.classList.remove('!border-red-500'));
                    }
                });

                // Server-rendered Session Messages
                @if (session('success'))
                    showToast("{{ session('success') }}", 'success');
                @elseif (session('error'))
                    showToast("{{ session('error') }}", 'error');
                @elseif ($errors->any())
                    showToast("{{ $errors->first() }}", 'error');
                @endif
            });
        </script>
    @endpush
@endsection
