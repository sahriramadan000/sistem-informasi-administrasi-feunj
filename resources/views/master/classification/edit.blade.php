@extends('layouts.app')

@section('title', 'Edit Klasifikasi Surat - FEB UNJ')
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
                        <i data-lucide="pencil" class="w-5 h-5"></i>
                    </div>
                    <span>Edit Klasifikasi Surat</span>
                </h1>
                <p class="text-xs sm:text-sm text-gray-500 mt-1 pl-13">
                    Perbarui data kode, nama klasifikasi kearsipan, atau kelola status keaktifan.
                </p>
            </div>

            <div class="flex items-center gap-2.5 self-start sm:self-auto pl-13 sm:pl-0">
                <a href="{{ route('master.classification-letters.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 text-xs sm:text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-200 rounded-full shadow-2xs hover:shadow-xs transition-all active:scale-[0.98]">
                    <i data-lucide="arrow-left" class="w-4 h-4 text-gray-500"></i>
                    <span>Kembali ke Klasifikasi</span>
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
                                <i data-lucide="file-pen" class="w-4 h-4 text-[#FF4D00]"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-gray-900 tracking-tight">
                                    Informasi Klasifikasi Surat
                                </h3>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    Perubahan kode akan langsung berpengaruh pada format surat baru yang menggunakan kode ini.
                                </p>
                            </div>
                        </div>

                        {{-- Current Status Pill --}}
                        <div>
                            @if ($classificationLetter->is_active)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100/90 text-emerald-800 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-700 border border-gray-300">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                    Nonaktif
                                </span>
                            @endif
                        </div>
                    </div>

                    <form id="edit-classification-form" method="POST" action="{{ route('master.classification-letters.update', $classificationLetter) }}" novalidate>
                        @csrf
                        @method('PUT')

                        <div class="space-y-5">
                            {{-- Field: Kode Klasifikasi --}}
                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <label for="code" class="text-xs font-semibold text-gray-700 flex items-center gap-1.5">
                                        <i data-lucide="hash" class="w-3.5 h-3.5 text-gray-400"></i>
                                        Kode Klasifikasi <span class="text-destructive">*</span>
                                    </label>
                                    <span class="text-[10px] text-gray-400 font-mono">Format Unik (Kapital)</span>
                                </div>
                                <div class="relative">
                                    <input type="text" 
                                           id="code" 
                                           name="code" 
                                           value="{{ old('code', $classificationLetter->code) }}" 
                                           class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-mono font-bold tracking-wider text-gray-900 placeholder:text-gray-400 placeholder:font-normal placeholder:tracking-normal focus:outline-none focus:border-[#FF4D00] focus:ring-4 focus:ring-orange-500/10 transition-all uppercase @error('code') !border-red-500 !ring-red-500/10 @enderror" 
                                           placeholder="Contoh: KM.00 atau VAL-ZJ"
                                           maxlength="50"
                                           autocomplete="off"
                                           required>
                                    <div class="absolute right-3 top-1/2 -translate-y-1/2">
                                        <span id="codeLengthBadge" class="text-[10px] font-mono font-semibold text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded">{{ strlen($classificationLetter->code) }}/50</span>
                                    </div>
                                </div>

                                <p class="text-[11px] text-gray-500">
                                    Kode unik ini digunakan dalam struktur nomor surat dinas resmi FEB UNJ.
                                </p>
                                @error('code')
                                    <p class="text-xs text-destructive flex items-center gap-1 mt-1">
                                        <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Field: Nama Klasifikasi --}}
                            <div class="space-y-1.5">
                                <label for="name" class="text-xs font-semibold text-gray-700 flex items-center gap-1.5">
                                    <i data-lucide="file-text" class="w-3.5 h-3.5 text-gray-400"></i>
                                    Nama Klasifikasi <span class="text-destructive">*</span>
                                </label>
                                <input type="text" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name', $classificationLetter->name) }}" 
                                       class="w-full px-3.5 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-900 placeholder:text-gray-400 focus:outline-none focus:border-[#FF4D00] focus:ring-4 focus:ring-orange-500/10 transition-all @error('name') !border-red-500 !ring-red-500/10 @enderror" 
                                       placeholder="Contoh: Validasi Berkas Ijazah"
                                       maxlength="150"
                                       required>
                                <p class="text-[11px] text-gray-500">Nama lengkap peruntukan klasifikasi surat agar mudah dikenali staf.</p>
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
                                          placeholder="Tambahkan keterangan peruntukan klasifikasi ini...">{{ old('description', $classificationLetter->description) }}</textarea>
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
                                                <span class="text-xs font-bold text-gray-900">Status Klasifikasi</span>
                                                <span id="statusIndicatorBadge" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold {{ old('is_active', $classificationLetter->is_active) ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-gray-100 text-gray-700 border border-gray-300' }}">
                                                    <span class="w-1.5 h-1.5 rounded-full {{ old('is_active', $classificationLetter->is_active) ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                                    {{ old('is_active', $classificationLetter->is_active) ? 'Aktif' : 'Nonaktif' }}
                                                </span>
                                            </div>
                                            <p id="statusDescriptionText" class="text-xs text-gray-500 leading-relaxed">
                                                {{ old('is_active', $classificationLetter->is_active) ? 'Klasifikasi ini aktif dan dapat langsung dipilih saat membuat surat dinas baru.' : 'Klasifikasi nonaktif dan disembunyikan dari dropdown registrasi surat baru.' }}
                                            </p>
                                        </div>

                                        {{-- Custom iOS Switch Control --}}
                                        <label class="relative inline-flex items-center cursor-pointer flex-shrink-0 mt-0.5">
                                            <input type="hidden" name="is_active" value="0">
                                            <input type="checkbox" 
                                                   id="is_active" 
                                                   name="is_active" 
                                                   value="1" 
                                                   {{ old('is_active', $classificationLetter->is_active) ? 'checked' : '' }} 
                                                   class="sr-only peer"
                                                   onchange="handleStatusToggle(this)">
                                            <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500 shadow-inner"></div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- macOS Bottom Action Dock inside card --}}
                        <div class="flex flex-col-reverse sm:flex-row items-center justify-between gap-3 pt-6 mt-6 border-t border-gray-100">
                            <a href="{{ route('master.classification-letters.index') }}"
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 text-xs sm:text-sm font-semibold text-gray-700 hover:text-gray-900 bg-white hover:bg-gray-100/90 rounded-xl border border-gray-200/90 shadow-2xs hover:shadow-xs transition-all active:scale-[0.98]">
                                <i data-lucide="x-circle" class="w-4 h-4 text-gray-500"></i>
                                <span>Batal</span>
                            </a>
                            <button type="submit" id="btn_submit_edit_classification"
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-8 py-3 text-xs sm:text-sm font-bold text-white bg-gradient-to-r from-[#FF4D00] to-[#ff6a00] hover:from-[#e04400] hover:to-[#eb5e00] rounded-xl shadow-[0_4px_16px_rgba(255,77,0,0.32)] hover:shadow-[0_6px_22px_rgba(255,77,0,0.42)] active:scale-[0.98] transition-all cursor-pointer">
                                <i data-lucide="check-circle" class="w-4 h-4"></i>
                                <span>Simpan Perubahan</span>
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Administrative / Danger Zone Section --}}
                @if (auth()->user()->isAdmin())
                    @if ($classificationLetter->is_active)
                        <div class="mac-card p-5 border-amber-200/80 bg-gradient-to-r from-amber-50/60 to-orange-50/30">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-xl bg-amber-500/10 text-amber-700 flex items-center justify-center">
                                            <i data-lucide="power" class="w-4 h-4"></i>
                                        </div>
                                        <h4 class="text-sm font-bold text-gray-900">Nonaktifkan Klasifikasi Surat</h4>
                                    </div>
                                    <p class="text-xs text-gray-600 leading-relaxed max-w-xl">
                                        Klasifikasi ini tidak akan dihapus permanen agar seluruh surat historis tetap valid. Klasifikasi hanya akan disembunyikan dari dropdown registrasi surat dinas baru.
                                    </p>
                                </div>
                                <form method="POST" 
                                      action="{{ route('master.classification-letters.destroy', $classificationLetter) }}"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menonaktifkan klasifikasi {{ $classificationLetter->code }}? Klasifikasi tidak akan dihapus permanen namun disembunyikan dari daftar registrasi surat baru.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 text-xs sm:text-sm font-bold text-amber-800 bg-amber-100 hover:bg-amber-200/90 border border-amber-300 rounded-xl transition-all active:scale-[0.98] cursor-pointer">
                                        <i data-lucide="power" class="w-4 h-4"></i>
                                        <span>Nonaktifkan</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="mac-card p-5 border-gray-200 bg-gray-50/80">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl bg-gray-200 text-gray-600 flex items-center justify-center flex-shrink-0">
                                    <i data-lucide="shield-alert" class="w-4 h-4"></i>
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-gray-900">Klasifikasi Saat Ini Berstatus Nonaktif</h4>
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        Untuk mengaktifkan kembali, Anda cukup menyalakan sakelar <strong class="text-gray-700">Status Klasifikasi</strong> di atas lalu tekan tombol <strong class="text-[#FF4D00]">Simpan Perubahan</strong>.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif

            </div>

            {{-- RIGHT COLUMN: Live Simulator & Metadata Inspector (5 Cols, Sticky) --}}
            <div class="lg:col-span-5 space-y-5 lg:sticky lg:top-20">

                {{-- 🌟 CARD 1: Live Letter Number Simulator (Apple Studio Style) --}}
                <div class="simulator-orange-card p-5 sm:p-6">
                    <div class="flex items-center justify-between mb-4 pb-3 border-b border-orange-200/80">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-xl bg-orange-500 text-white flex items-center justify-center shadow-xs">
                                <i data-lucide="hash" class="w-4 h-4"></i>
                            </div>
                            <span class="text-xs font-extrabold text-gray-900 tracking-tight">Pratinjau Penggunaan Nomor</span>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-orange-100/90 text-orange-800 border border-orange-300/80">
                            <span class="w-1.5 h-1.5 rounded-full bg-orange-500 animate-pulse"></span>
                            Live Sync
                        </span>
                    </div>

                    {{-- Simulated Official Letter Number Display --}}
                    <div class="p-3.5 rounded-2xl bg-white border-2 border-orange-300/90 mb-4 shadow-xs">
                        <div class="flex items-center justify-between text-[11px] font-semibold text-gray-500 mb-1.5">
                            <span>Format Nomor Resmi</span>
                            <span class="text-orange-600 font-bold bg-orange-50 px-2 py-0.5 rounded-md border border-orange-200/70 text-[10px]">
                                Terkini
                            </span>
                        </div>
                        <div class="font-mono font-bold text-xs sm:text-sm text-gray-800 tracking-tight break-all leading-relaxed py-1">
                            B / 001 / UN39.11 / <span id="sim_code_token" class="text-[#FF4D00] font-black px-1.5 py-0.5 rounded-md bg-orange-50 border border-orange-200/80 underline decoration-orange-400">{{ $classificationLetter->code }}</span> / {{ now()->year }}
                        </div>
                        <div class="mt-2 pt-2 border-t border-gray-100 text-[11px] text-gray-600 flex items-center gap-1.5">
                            <i data-lucide="info" class="w-3.5 h-3.5 text-orange-500 flex-shrink-0"></i>
                            <span>Segmen nomor di atas akan otomatis disesuaikan secara real-time.</span>
                        </div>
                    </div>

                    {{-- Token Breakdown Pills --}}
                    <div class="space-y-2 text-xs">
                        {{-- Token Kode --}}
                        <div class="flex items-center justify-between py-2 px-3 rounded-xl bg-white/90 border border-orange-200/70 shadow-2xs">
                            <span class="text-gray-600 font-medium flex items-center gap-2">
                                <i data-lucide="tag" class="w-3.5 h-3.5 text-orange-500"></i>
                                Kode Klasifikasi
                            </span>
                            <span id="sim_token_code_badge" class="font-mono font-bold text-orange-700 px-2 py-0.5 rounded-md bg-orange-50 border border-orange-200">
                                {{ $classificationLetter->code }}
                            </span>
                        </div>

                        {{-- Token Nama --}}
                        <div class="flex items-center justify-between py-2 px-3 rounded-xl bg-white/90 border border-orange-200/70 shadow-2xs">
                            <span class="text-gray-600 font-medium flex items-center gap-2">
                                <i data-lucide="file-text" class="w-3.5 h-3.5 text-blue-500"></i>
                                Nama Terdaftar
                            </span>
                            <span id="sim_token_name_badge" class="font-semibold text-gray-800 truncate max-w-[170px]" title="{{ $classificationLetter->name }}">
                                {{ $classificationLetter->name }}
                            </span>
                        </div>

                        {{-- Token Status --}}
                        <div class="flex items-center justify-between py-2 px-3 rounded-xl bg-white/90 border border-orange-200/70 shadow-2xs">
                            <span class="text-gray-600 font-medium flex items-center gap-2">
                                <i data-lucide="activity" class="w-3.5 h-3.5 text-emerald-500"></i>
                                Status Penggunaan
                            </span>
                            <span id="sim_token_status_badge" class="font-bold {{ $classificationLetter->is_active ? 'text-emerald-700 bg-emerald-50 border-emerald-200' : 'text-gray-600 bg-gray-100 border-gray-300' }} px-2 py-0.5 rounded-md border">
                                {{ $classificationLetter->is_active ? 'Aktif' : 'Nonaktif' }}
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
                            <span class="text-gray-500">Total Surat Terkait</span>
                            <span class="font-bold text-gray-900 bg-gray-100 px-2 py-0.5 rounded-md font-mono">
                                {{ $classificationLetter->letters()->count() }} Surat
                            </span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Tanggal Dibuat</span>
                            <span class="font-medium text-gray-800">
                                {{ $classificationLetter->created_at ? $classificationLetter->created_at->translatedFormat('d M Y, H:i') : '-' }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Terakhir Diperbarui</span>
                            <span class="font-medium text-gray-800">
                                {{ $classificationLetter->updated_at ? $classificationLetter->updated_at->translatedFormat('d M Y, H:i') : '-' }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- 🌟 CARD 3: Pedoman Ketentuan Penomoran --}}
                <div class="mac-card p-5 sm:p-6 bg-gradient-to-br from-white to-amber-50/20">
                    <div class="flex items-center gap-2 mb-3 text-xs font-bold text-gray-900 uppercase tracking-wider">
                        <i data-lucide="sparkles" class="w-4 h-4 text-amber-500"></i>
                        Catatan Penting
                    </div>
                    <ul class="text-xs text-gray-600 space-y-2.5">
                        <li class="flex items-start gap-2">
                            <i data-lucide="check" class="w-3.5 h-3.5 text-emerald-600 flex-shrink-0 mt-0.5"></i>
                            <span>Perubahan nama atau kode klasifikasi akan berlaku untuk surat dinas yang dibuat setelah perubahan disimpan.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <i data-lucide="check" class="w-3.5 h-3.5 text-emerald-600 flex-shrink-0 mt-0.5"></i>
                            <span>Nomor surat dinas yang telah terbit sebelumnya tidak akan berubah otomatis guna menjamin keabsahan hukum arsip.</span>
                        </li>
                    </ul>
                </div>

            </div>

        </div>
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
        const simTokenCodeBadge = document.getElementById('sim_token_code_badge');
        const simTokenNameBadge = document.getElementById('sim_token_name_badge');
        const form = document.getElementById('edit-classification-form');

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
                simTokenCodeBadge.textContent = '[KOSONG]';
            }

            if (nameVal.length > 0) {
                simTokenNameBadge.textContent = nameVal;
                simTokenNameBadge.title = nameVal;
            } else {
                simTokenNameBadge.textContent = '-';
                simTokenNameBadge.title = '';
            }
        }

        codeInput.addEventListener('input', updateLivePreview);
        nameInput.addEventListener('input', updateLivePreview);

        // Initial preview call
        updateLivePreview();

        // Form validation feedback
        form.addEventListener('submit', function(e) {
            const code = codeInput.value.trim();
            const name = nameInput.value.trim();

            if (!code) {
                e.preventDefault();
                showToast('Kode klasifikasi wajib diisi.', 'error');
                codeInput.focus();
                return;
            }

            if (!name) {
                e.preventDefault();
                showToast('Nama klasifikasi wajib diisi.', 'error');
                nameInput.focus();
                return;
            }
        });
    });

    // iOS Toggle Switch Handler
    function handleStatusToggle(checkbox) {
        const isChecked = checkbox.checked;
        const statusIndicatorBadge = document.getElementById('statusIndicatorBadge');
        const statusDescriptionText = document.getElementById('statusDescriptionText');
        const simTokenStatusBadge = document.getElementById('sim_token_status_badge');

        if (isChecked) {
            statusIndicatorBadge.className = 'inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200';
            statusIndicatorBadge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif';
            statusDescriptionText.textContent = 'Klasifikasi ini aktif dan dapat langsung dipilih saat membuat surat dinas baru.';

            simTokenStatusBadge.className = 'font-bold text-emerald-700 px-2 py-0.5 rounded-md bg-emerald-50 border border-emerald-200';
            simTokenStatusBadge.textContent = 'Aktif';
        } else {
            statusIndicatorBadge.className = 'inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-700 border border-gray-300';
            statusIndicatorBadge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> Nonaktif';
            statusDescriptionText.textContent = 'Klasifikasi nonaktif dan disembunyikan dari dropdown registrasi surat baru.';

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
