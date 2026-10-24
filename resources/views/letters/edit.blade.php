@extends('layouts.app')

@section('title', 'Edit Surat')
@section('disable_layout_alerts', true)

@push('styles')
    <style>
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
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-gray-200/60">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-[#FF4D00] to-amber-500 text-white flex items-center justify-center shadow-xs">
                        <i data-lucide="file-pen" class="w-5 h-5"></i>
                    </div>
                    Edit Surat
                </h1>
                <p class="text-xs sm:text-sm text-gray-500 mt-1 pl-13">
                    Perbarui data informasi, klasifikasi kearsipan, atau sasaran tujuan surat.
                </p>
            </div>

            <div class="flex items-center gap-2.5 self-start sm:self-auto pl-13 sm:pl-0">
                <a href="{{ route('letters.show', $letter) }}"
                    class="inline-flex items-center gap-2 px-4 py-2 text-xs sm:text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-200 rounded-full shadow-2xs hover:shadow-xs transition-all active:scale-[0.98]">
                    <i data-lucide="arrow-left" class="w-4 h-4 text-gray-500"></i>
                    Kembali ke Detail
                </a>
            </div>
        </div>

        {{-- Form & Inspector 2-Column macOS Studio Layout --}}
        <form id="edit-letter-form" method="POST" action="{{ route('letters.update', $letter) }}" novalidate>
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-7 items-start">
                
                {{-- LEFT COLUMN: Main Form Steps (8 Cols) --}}
                <div class="lg:col-span-8 space-y-6">

                    {{-- STEP 1: Informasi Dasar Surat --}}
                    <div class="mac-card p-6 sm:p-7">
                        <div class="flex items-center justify-between gap-3 mb-5 pb-4 border-b border-gray-100">
                            <div class="flex items-center gap-3.5">
                                <div class="mac-step-badge">1</div>
                                <div>
                                    <h3 class="text-base font-bold text-gray-900 tracking-tight flex items-center gap-2">
                                        Informasi Dasar & Klasifikasi
                                        <span class="text-[11px] font-normal text-gray-400">• Step 1/3</span>
                                    </h3>
                                    <p class="text-xs text-gray-500 mt-0.5">Klasifikasi surat, penandatangan, dan sasaran surat.</p>
                                </div>
                            </div>
                            <span class="hidden sm:inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-gray-100 text-gray-600 border border-gray-200">
                                <i data-lucide="lock" class="w-3 h-3 text-gray-400"></i>
                                Terkunci Sebagian
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            {{-- Nomor Surat (Read-Only) --}}
                            <div class="space-y-1.5">
                                <label class="text-xs font-semibold text-gray-700 flex items-center justify-between">
                                    <span class="flex items-center gap-1.5">
                                        <i data-lucide="hash" class="w-3.5 h-3.5 text-gray-400"></i>
                                        Nomor Surat Terdaftar
                                    </span>
                                    <span class="text-[10px] text-gray-400 uppercase font-mono tracking-wider">Terkunci</span>
                                </label>
                                <div class="relative">
                                    <input type="text" value="{{ $letter->letter_number }}"
                                        class="input bg-gray-50/80 border-gray-200/90 font-mono text-xs font-bold text-gray-700 cursor-not-allowed pr-9" readonly>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                                        <i data-lucide="lock" class="w-3.5 h-3.5"></i>
                                    </div>
                                </div>
                            </div>

                            {{-- Jenis Surat (Read-Only) --}}
                            <div class="space-y-1.5">
                                <label class="text-xs font-semibold text-gray-700 flex items-center justify-between">
                                    <span class="flex items-center gap-1.5">
                                        <i data-lucide="file-text" class="w-3.5 h-3.5 text-gray-400"></i>
                                        Jenis Surat
                                    </span>
                                    <span class="text-[10px] text-gray-400 uppercase font-mono tracking-wider">Terkunci</span>
                                </label>
                                <div class="relative">
                                    <input type="text" value="{{ $letter->letterType->name }}"
                                        class="input bg-gray-50/80 border-gray-200/90 text-xs font-semibold text-gray-700 cursor-not-allowed pr-9" readonly>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400">
                                        <i data-lucide="lock" class="w-3.5 h-3.5"></i>
                                    </div>
                                </div>
                            </div>

                            {{-- Klasifikasi Surat (Editable) --}}
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
                                            {{ old('classification_id', $letter->classification_id) == $classification->id ? 'selected' : '' }}>
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

                            {{-- Penandatangan (Editable) --}}
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
                                            {{ old('signatory_id', $letter->signatory_id) == $signatory->id ? 'selected' : '' }}>
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

                            {{-- Klasifikasi Keamanan (Editable) --}}
                            <div class="space-y-1.5">
                                <label for="security_classification" class="text-xs font-semibold text-gray-700 flex items-center gap-1.5">
                                    <i data-lucide="shield" class="w-3.5 h-3.5 text-gray-400"></i>
                                    Klasifikasi Keamanan <span class="text-destructive">*</span>
                                </label>
                                <select id="security_classification" name="security_classification"
                                    class="select @error('security_classification') !border-red-500 @enderror" required>
                                    <option value="">-- Pilih Klasifikasi Keamanan --</option>
                                    @foreach ($securityClassifications as $code => $name)
                                        <option value="{{ $code }}"
                                            {{ old('security_classification', $letter->security_classification) == $code ? 'selected' : '' }}>
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

                            {{-- Sasaran Surat (Editable) --}}
                            <div class="space-y-1.5">
                                <label for="letter_target" class="text-xs font-semibold text-gray-700 flex items-center gap-1.5">
                                    <i data-lucide="target" class="w-3.5 h-3.5 text-gray-400"></i>
                                    Sasaran Surat <span class="text-destructive">*</span>
                                </label>
                                <select id="letter_target" name="letter_target"
                                    class="select @error('letter_target') !border-red-500 @enderror" required>
                                    <option value="">-- Pilih Sasaran Surat --</option>
                                    <option value="internal"
                                        {{ old('letter_target', $letter->letter_target) === 'internal' ? 'selected' : '' }}>
                                        Internal
                                    </option>
                                    <option value="external"
                                        {{ old('letter_target', $letter->letter_target) === 'external' ? 'selected' : '' }}>
                                        External
                                    </option>
                                </select>
                                @error('letter_target')
                                    <p class="text-xs text-destructive flex items-center gap-1 mt-1">
                                        <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- STEP 2: Detail Administrasi --}}
                    <div class="mac-card p-6 sm:p-7">
                        <div class="flex items-center gap-3.5 mb-5 pb-4 border-b border-gray-100">
                            <div class="mac-step-badge">2</div>
                            <div>
                                <h3 class="text-base font-bold text-gray-900 tracking-tight flex items-center gap-2">
                                    Detail Administrasi & Tanggal
                                    <span class="text-[11px] font-normal text-gray-400">• Step 2/3</span>
                                </h3>
                                <p class="text-xs text-gray-500 mt-0.5">Perbarui tanggal penetapan surat.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            {{-- Tanggal Surat --}}
                            <div class="space-y-1.5">
                                <label for="letter_date" class="text-xs font-semibold text-gray-700 flex items-center gap-1.5">
                                    <i data-lucide="calendar" class="w-3.5 h-3.5 text-gray-400"></i>
                                    Tanggal Penetapan Surat <span class="text-destructive">*</span>
                                </label>
                                <div class="relative">
                                    <input type="text" id="letter_date" name="letter_date"
                                        value="{{ old('letter_date', $letter->letter_date->format('Y-m-d')) }}"
                                        class="input flatpickr-input pr-10 @error('letter_date') !border-red-500 @enderror font-medium"
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
                                <p class="text-xs text-gray-500 mt-0.5">Perbarui perihal dan instansi tujuan surat.</p>
                            </div>
                        </div>

                        <div class="space-y-5">
                            {{-- Perihal --}}
                            <div class="space-y-1.5">
                                <label for="subject" class="text-xs font-semibold text-gray-700 flex items-center gap-1.5">
                                    <i data-lucide="align-left" class="w-3.5 h-3.5 text-gray-400"></i>
                                    Perihal / Hal Surat <span class="text-destructive">*</span>
                                </label>
                                <input type="text" id="subject" name="subject"
                                    value="{{ old('subject', $letter->subject) }}"
                                    class="input @error('subject') !border-red-500 @enderror text-sm font-medium"
                                    placeholder="Contoh: Permohonan Izin Penelitian Lapangan" required>
                                <p class="text-[11px] text-gray-500 flex items-center gap-1.5 pt-0.5">
                                    <i data-lucide="lightbulb" class="w-3.5 h-3.5 text-amber-500 flex-shrink-0"></i>
                                    <span>Jelaskan secara ringkas dan jelas maksud pokok surat</span>
                                </p>
                                @error('subject')
                                    <p class="text-xs text-destructive flex items-center gap-1 mt-1">
                                        <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Ditujukan Kepada --}}
                            <div class="space-y-1.5">
                                <label for="recipient" class="text-xs font-semibold text-gray-700 flex items-center gap-1.5">
                                    <i data-lucide="send" class="w-3.5 h-3.5 text-gray-400"></i>
                                    Ditujukan Kepada <span class="text-destructive">*</span>
                                </label>
                                <input type="text" id="recipient" name="recipient"
                                    value="{{ old('recipient', $letter->recipient) }}"
                                    class="input @error('recipient') !border-red-500 @enderror text-sm font-medium"
                                    placeholder="Contoh: Dekan Fakultas Ekonomi dan Bisnis" required>
                                <p class="text-[11px] text-gray-500 flex items-center gap-1.5 pt-0.5">
                                    <i data-lucide="lightbulb" class="w-3.5 h-3.5 text-amber-500 flex-shrink-0"></i>
                                    <span>Sebutkan nama jabatan, instansi, atau mitra penerima surat</span>
                                </p>
                                @error('recipient')
                                    <p class="text-xs text-destructive flex items-center gap-1 mt-1">
                                        <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- STEP 4: Informasi Tambahan (Conditional) --}}
                    @if ($letter->letterType->requires_purpose)
                        <div class="mac-card p-6 sm:p-7 border-orange-200/80 bg-gradient-to-br from-white to-orange-50/20">
                            <div class="flex items-center gap-3.5 mb-5 pb-4 border-b border-orange-100">
                                <div class="mac-step-badge bg-orange-100 border-orange-300 text-orange-700">4</div>
                                <div>
                                    <h3 class="text-base font-bold text-gray-900 tracking-tight flex items-center gap-2">
                                        Informasi Tambahan Khusus
                                        <span class="text-[11px] font-semibold text-orange-600 bg-orange-100/70 px-2 py-0.5 rounded-full">Wajib untuk Jenis Surat Ini</span>
                                    </h3>
                                    <p class="text-xs text-gray-500 mt-0.5">Data keperluan dan nama mahasiswa terkait.</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                {{-- Keperluan Surat --}}
                                <div class="space-y-1.5">
                                    <label for="letter_purpose_id" class="text-xs font-semibold text-gray-700 flex items-center gap-1.5">
                                        <i data-lucide="clipboard-list" class="w-3.5 h-3.5 text-gray-400"></i>
                                        Keperluan Surat <span class="text-destructive">*</span>
                                    </label>
                                    <select id="letter_purpose_id" name="letter_purpose_id"
                                        class="select @error('letter_purpose_id') !border-red-500 @enderror" required>
                                        <option value="">-- Pilih Keperluan --</option>
                                        @foreach ($letterPurposes as $purpose)
                                            <option value="{{ $purpose->id }}"
                                                {{ old('letter_purpose_id', $letter->letter_purpose_id) == $purpose->id ? 'selected' : '' }}>
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
                                <div class="space-y-1.5">
                                    <label for="student_name" class="text-xs font-semibold text-gray-700 flex items-center gap-1.5">
                                        <i data-lucide="user" class="w-3.5 h-3.5 text-gray-400"></i>
                                        Nama Lengkap Mahasiswa <span class="text-destructive">*</span>
                                    </label>
                                    <input type="text" id="student_name" name="student_name"
                                        value="{{ old('student_name', $letter->student_name) }}"
                                        class="input @error('student_name') !border-red-500 @enderror text-sm font-medium"
                                        placeholder="Masukkan nama lengkap mahasiswa" required>
                                    @error('student_name')
                                        <p class="text-xs text-destructive flex items-center gap-1 mt-1">
                                            <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- macOS Bottom Action Dock --}}
                    <div class="mac-card p-5 flex flex-col-reverse sm:flex-row items-center justify-between gap-3 bg-gradient-to-r from-gray-50/70 to-white">
                        <a href="{{ route('letters.show', $letter) }}"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 text-xs sm:text-sm font-semibold text-gray-700 hover:text-gray-900 bg-white hover:bg-gray-100/90 rounded-xl border border-gray-200/90 shadow-2xs hover:shadow-xs transition-all active:scale-[0.98]">
                            <i data-lucide="x" class="w-4 h-4 text-gray-500"></i>
                            Batalkan Perubahan
                        </a>
                        <button type="submit" id="btn_submit_edit"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-8 py-3 text-xs sm:text-sm font-bold text-white bg-gradient-to-r from-[#FF4D00] to-[#ff6a00] hover:from-[#e04400] hover:to-[#eb5e00] rounded-xl shadow-[0_4px_16px_rgba(255,77,0,0.32)] hover:shadow-[0_6px_22px_rgba(255,77,0,0.42)] active:scale-[0.98] transition-all">
                            <i data-lucide="check" class="w-4 h-4"></i>
                            Simpan Perubahan Surat
                        </button>
                    </div>

                </div>

                {{-- RIGHT COLUMN: Active Letter Inspector (4 Cols, Sticky) --}}
                <div class="lg:col-span-4 space-y-5 lg:sticky lg:top-20">

                    {{-- 🌟 CARD 1: Pratinjau Nomor Surat Resmi (Warm Orange Apple Theme) --}}
                    <div class="simulator-orange-card p-5 sm:p-6">
                        <div class="flex items-center justify-between mb-4 pb-3 border-b border-orange-200/80">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-xl bg-orange-500 text-white flex items-center justify-center shadow-xs">
                                    <i data-lucide="hash" class="w-4 h-4"></i>
                                </div>
                                <span class="text-xs font-extrabold text-gray-900 tracking-tight">Format Nomor Surat</span>
                            </div>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-orange-100/90 text-orange-800 border border-orange-300/80">
                                <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span>
                                Terdaftar
                            </span>
                        </div>

                        {{-- Main Simulated Number Result Box (Clean White Inset) --}}
                        <div class="p-3.5 rounded-2xl bg-white border-2 border-orange-300/90 mb-4 shadow-xs">
                            <div class="flex items-center justify-between text-[11px] font-semibold text-gray-500 mb-1.5">
                                <span>Nomor Surat Resmi</span>
                                <span class="text-orange-600 font-bold bg-orange-50 px-2 py-0.5 rounded-md border border-orange-200/70 text-[10px]">
                                    No. #{{ str_pad($letter->running_number, 3, '0', STR_PAD_LEFT) }}
                                </span>
                            </div>
                            <div id="simulated_edit_number" class="font-mono font-bold text-xs sm:text-sm text-[#FF4D00] tracking-tight break-all leading-normal py-0.5">
                                {{ $letter->letter_number }}
                            </div>
                            <div class="mt-2 pt-2 border-t border-gray-100 text-[11px] text-gray-600 flex items-center gap-1.5">
                                <i data-lucide="info" class="w-3.5 h-3.5 text-orange-500 flex-shrink-0"></i>
                                <span>Perubahan klasifikasi / penandatangan otomatis memperbarui nomor.</span>
                            </div>
                        </div>

                        {{-- Exploded Token Breakdown Pills --}}
                        <div class="space-y-2 text-xs">
                            <div class="flex items-center justify-between py-2 px-3 rounded-xl bg-white/90 border border-orange-200/70 shadow-2xs">
                                <span class="text-gray-600 font-medium flex items-center gap-2">
                                    <i data-lucide="shield" class="w-3.5 h-3.5 text-orange-500"></i>
                                    Sifat Keamanan
                                </span>
                                <span id="edit_token_sec" class="font-mono font-bold text-orange-700 px-2 py-0.5 rounded-md bg-orange-50 border border-orange-200">
                                    {{ $letter->security_classification }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between py-2 px-3 rounded-xl bg-white/90 border border-orange-200/70 shadow-2xs">
                                <span class="text-gray-600 font-medium flex items-center gap-2">
                                    <i data-lucide="hash" class="w-3.5 h-3.5 text-blue-500"></i>
                                    Nomor Urut
                                </span>
                                <span class="font-mono font-bold text-blue-700 px-2 py-0.5 rounded-md bg-blue-50 border border-blue-200">
                                    {{ str_pad($letter->running_number, 3, '0', STR_PAD_LEFT) }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between py-2 px-3 rounded-xl bg-white/90 border border-orange-200/70 shadow-2xs">
                                <span class="text-gray-600 font-medium flex items-center gap-2">
                                    <i data-lucide="target" class="w-3.5 h-3.5 text-emerald-500"></i>
                                    Sasaran & Pejabat
                                </span>
                                <span id="edit_token_signatory" class="font-mono font-bold text-emerald-700 px-2 py-0.5 rounded-md bg-emerald-50 border border-emerald-200 max-w-[150px] truncate">
                                    {{ $letter->signatory ? ($letter->letter_target === 'external' && !str_contains($letter->signatory->code, 'UN39') ? 'UN39.' : '') . $letter->signatory->code : '...' }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between py-2 px-3 rounded-xl bg-white/90 border border-orange-200/70 shadow-2xs">
                                <span class="text-gray-600 font-medium flex items-center gap-2">
                                    <i data-lucide="folder" class="w-3.5 h-3.5 text-purple-500"></i>
                                    Klasifikasi Berkas
                                </span>
                                <span id="edit_token_class" class="font-mono font-bold text-purple-700 px-2 py-0.5 rounded-md bg-purple-50 border border-purple-200 max-w-[150px] truncate">
                                    {{ $letter->classification->code ?? '...' }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between py-2 px-3 rounded-xl bg-white/90 border border-orange-200/70 shadow-2xs">
                                <span class="text-gray-600 font-medium flex items-center gap-2">
                                    <i data-lucide="calendar" class="w-3.5 h-3.5 text-amber-500"></i>
                                    Tahun Anggaran
                                </span>
                                <span id="edit_token_year" class="font-mono font-bold text-amber-700 px-2 py-0.5 rounded-md bg-amber-50 border border-amber-200">
                                    {{ $letter->year }}
                                </span>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-t border-orange-200/70 text-[11px] text-gray-500 flex items-start gap-2">
                            <i data-lucide="shield-check" class="w-3.5 h-3.5 text-orange-600 flex-shrink-0 mt-0.5"></i>
                            <span>Nomor surat resmi dijamin unik dan valid sesuai penomoran surat FEB UNJ.</span>
                        </div>
                    </div>

                    {{-- 🌟 CARD 2: Apple Notice Callout --}}
                    <div class="mac-card p-5 bg-gradient-to-br from-amber-50/70 to-orange-50/30 border-amber-200/80">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-xl bg-amber-100 border border-amber-200 flex items-center justify-center text-amber-700 flex-shrink-0 shadow-2xs">
                                <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                            </div>
                            <div class="text-xs">
                                <h4 class="font-bold text-amber-950 mb-1">Ketentuan Regenerasi Nomor</h4>
                                <p class="text-amber-900/80 leading-relaxed">
                                    Jika Anda mengubah <strong>Klasifikasi Surat</strong> atau <strong>Pejabat Penandatangan</strong>, sistem akan meregenerasi susunan nomor surat agar tetap valid sesuai format resmi.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- 🌟 CARD 3: Audit Information --}}
                    <div class="mac-card p-5">
                        <div class="flex items-center gap-2 text-xs font-bold text-gray-900 mb-3">
                            <i data-lucide="history" class="w-4 h-4 text-orange-600"></i>
                            Riwayat Surat
                        </div>
                        <div class="space-y-2 text-xs text-gray-500">
                            <div class="flex items-center justify-between py-1 border-b border-gray-100">
                                <span>Terdaftar Pada</span>
                                <span class="font-medium text-gray-800">{{ $letter->created_at->format('d M Y, H:i') }} WIB</span>
                            </div>
                            <div class="flex items-center justify-between py-1 border-b border-gray-100">
                                <span>Pembaruan Terakhir</span>
                                <span class="font-medium text-gray-800">{{ $letter->updated_at->format('d M Y, H:i') }} WIB</span>
                            </div>
                            <div class="flex items-center justify-between py-1">
                                <span>Dibuat Oleh</span>
                                <span class="font-medium text-gray-800">{{ $letter->creator->name ?? 'Administrator' }}</span>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </form>
    </div>

    @push('scripts')
        <script src="https://unpkg.com/lucide@latest"></script>
        <script>
            lucide.createIcons();

            // Floating Toast Notification Logic
            let toastTimer = null;
            function showToast(message, type = 'error') {
                const toast = document.getElementById('toastPopup');
                const toastMsg = document.getElementById('toastMessage');
                const toastIconWrap = document.getElementById('toastIconWrap');
                const toastIcon = document.getElementById('toastIcon');

                if (!toast || !toastMsg) return;

                toastMsg.textContent = message;

                if (type === 'error') {
                    toast.className = 'toast-popup show fixed top-5 left-1/2 z-[9999] flex items-center gap-3 px-5 py-3 rounded-2xl bg-white/95 backdrop-blur-md border border-red-200/90 text-gray-800 shadow-xl shadow-red-500/10 text-xs sm:text-sm font-medium';
                    toastIconWrap.className = 'w-7 h-7 rounded-xl bg-red-100 text-red-600 flex items-center justify-center flex-shrink-0';
                    toastIcon.setAttribute('data-lucide', 'alert-circle');
                } else {
                    toast.className = 'toast-popup show fixed top-5 left-1/2 z-[9999] flex items-center gap-3 px-5 py-3 rounded-2xl bg-white/95 backdrop-blur-md border border-emerald-200/90 text-gray-800 shadow-xl shadow-emerald-500/10 text-xs sm:text-sm font-medium';
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

            // JSON Mappings for Live Simulator in Edit
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

            const runningNumber = "{{ str_pad($letter->running_number, 3, '0', STR_PAD_LEFT) }}";

            function updateEditSimulator() {
                const sec = document.getElementById('security_classification').value || 'B';
                const target = document.getElementById('letter_target').value || 'internal';
                const signatoryId = document.getElementById('signatory_id').value;
                const classId = document.getElementById('classification_id').value;
                const dateVal = document.getElementById('letter_date').value;

                let sigCode = '...';
                if (signatoryId && signatoriesData[signatoryId]) {
                    sigCode = signatoriesData[signatoryId].code || '...';
                }

                let targetCode = '';
                if (target === 'external' && !sigCode.includes('UN39')) {
                    targetCode = 'UN39.';
                }
                const combinedSig = targetCode + sigCode;

                let classCode = '...';
                if (classId && classificationsData[classId]) {
                    classCode = classificationsData[classId].code || '...';
                }

                let year = (new Date()).getFullYear();
                if (dateVal) {
                    const parsedYear = new Date(dateVal).getFullYear();
                    if (!isNaN(parsedYear)) year = parsedYear;
                }

                const fullNumber = `${sec} / ${runningNumber} / ${combinedSig} / ${classCode} / ${year}`;
                const fullEl = document.getElementById('simulated_edit_number');
                if (fullEl) {
                    fullEl.textContent = fullNumber;
                }

                const simSec = document.getElementById('edit_token_sec');
                if (simSec) simSec.textContent = sec;

                const simSig = document.getElementById('edit_token_signatory');
                if (simSig) simSig.textContent = combinedSig;

                const simCls = document.getElementById('edit_token_class');
                if (simCls) simCls.textContent = classCode;

                const simYr = document.getElementById('edit_token_year');
                if (simYr) simYr.textContent = year;
            }

            $(document).ready(function() {
                $('#letter_target').select2({
                    placeholder: '-- Pilih Sasaran Surat --',
                    allowClear: false,
                    width: '100%'
                }).on('change', function() {
                    updateEditSimulator();
                });

                $('#classification_id').select2({
                    placeholder: '-- Pilih Klasifikasi --',
                    allowClear: false,
                    width: '100%'
                }).on('change', function() {
                    updateEditSimulator();
                });

                $('#signatory_id').select2({
                    placeholder: '-- Pilih Penandatangan --',
                    allowClear: false,
                    width: '100%'
                }).on('change', function() {
                    updateEditSimulator();
                });

                $('#security_classification').select2({
                    placeholder: '-- Pilih Klasifikasi Keamanan --',
                    allowClear: false,
                    width: '100%'
                }).on('change', function() {
                    updateEditSimulator();
                });

                @if ($letter->letterType->requires_purpose)
                    $('#letter_purpose_id').select2({
                        placeholder: '-- Pilih Keperluan --',
                        allowClear: true,
                        width: '100%'
                    });
                @endif

                flatpickr("#letter_date", {
                    dateFormat: "Y-m-d",
                    locale: "id",
                    altInput: true,
                    altFormat: "d F Y",
                    allowInput: true,
                    disableMobile: true,
                    defaultDate: "{{ old('letter_date', $letter->letter_date->format('Y-m-d')) }}",
                    onChange: function() {
                        updateEditSimulator();
                    }
                });

                // Client-side Validation Interception with Friendly Pop-up
                const form = document.getElementById('edit-letter-form');
                form.addEventListener('submit', function(e) {
                    const sec = $('#security_classification').val();
                    const target = $('#letter_target').val();
                    const classId = $('#classification_id').val();
                    const sigId = $('#signatory_id').val();
                    const dateVal = document.getElementById('letter_date')?.value?.trim();
                    const subjectVal = document.getElementById('subject')?.value?.trim();
                    const recipientVal = document.getElementById('recipient')?.value?.trim();

                    if (!sec) {
                        e.preventDefault();
                        $('#security_classification').select2('open');
                        showToast('Mohon pilih Klasifikasi Keamanan surat.', 'error');
                        return false;
                    }
                    if (!target) {
                        e.preventDefault();
                        $('#letter_target').select2('open');
                        showToast('Mohon pilih Sasaran Surat (Internal / Eksternal).', 'error');
                        return false;
                    }
                    if (!classId) {
                        e.preventDefault();
                        $('#classification_id').select2('open');
                        showToast('Mohon pilih Klasifikasi Surat sebelum menyimpan perubahan.', 'error');
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
                    if (!subjectVal) {
                        e.preventDefault();
                        const subEl = document.getElementById('subject');
                        if (subEl) { subEl.focus(); subEl.classList.add('!border-red-500'); }
                        showToast('Mohon lengkapi Perihal Surat sebelum menyimpan perubahan.', 'error');
                        return false;
                    }
                    if (!recipientVal) {
                        e.preventDefault();
                        const recEl = document.getElementById('recipient');
                        if (recEl) { recEl.focus(); recEl.classList.add('!border-red-500'); }
                        showToast('Mohon lengkapi Tujuan / Penerima Surat.', 'error');
                        return false;
                    }

                    @if ($letter->letterType->requires_purpose)
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
                    @endif

                    // Button loading state
                    const btn = document.getElementById('btn_submit_edit');
                    if (btn) {
                        btn.disabled = true;
                        btn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i> Menyimpan Perubahan...';
                        lucide.createIcons();
                    }
                });

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
