@extends('layouts.app')

@section('title', 'Edit Data Legalisir #' . str_pad($legalization->running_number, 3, '0', STR_PAD_LEFT) . ' - FEB UNJ')
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

        /* macOS Inset Step Badge */
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
            font-size: 0.925rem;
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
    @php
        $runningNumberPadded = str_pad($legalization->running_number, 3, '0', STR_PAD_LEFT);
    @endphp

    <div class="max-w-4xl mx-auto space-y-6 pb-12 relative">
        {{-- Floating Toast Notification (Auto Dismiss) --}}
        <div id="toastPopup" class="toast-popup fixed top-5 left-1/2 z-[9999] flex items-center gap-3 px-5 py-3 rounded-2xl bg-white/95 backdrop-blur-md border border-gray-200 shadow-xl text-xs sm:text-sm font-medium">
            <div id="toastIconWrap" class="w-7 h-7 rounded-xl flex items-center justify-center flex-shrink-0 bg-emerald-100 text-emerald-600">
                <i id="toastIcon" data-lucide="check" class="w-4 h-4"></i>
            </div>
            <span id="toastMessage" class="text-gray-800"></span>
            <button type="button" onclick="hideToast()" class="text-gray-400 hover:text-gray-600 ml-2 p-0.5 rounded-lg transition-colors cursor-pointer">
                <i data-lucide="x" class="w-3.5 h-3.5"></i>
            </button>
        </div>

        {{-- Page Header (Apple macOS Toolbar Style) --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-gray-200/80">
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-[#FF4D00] to-amber-500 flex items-center justify-center text-white shadow-xs flex-shrink-0">
                    <i data-lucide="edit-3" class="w-5 h-5"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">Edit Data Legalisir</h1>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-mono font-bold bg-orange-50 text-orange-700 border border-orange-200/70">
                            #{{ $runningNumberPadded }}
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm text-gray-500">Perbarui rincian permohonan legalisasi ijazah & transkrip nilai alumni</p>
                </div>
            </div>

            <div class="flex items-center gap-2.5">
                <a href="{{ route('legalizations.show', $legalization) }}"
                    class="inline-flex items-center gap-2 px-4 py-2 text-xs sm:text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-200 rounded-full shadow-2xs hover:shadow-xs transition-all active:scale-[0.98]">
                    <i data-lucide="arrow-left" class="w-4 h-4 text-gray-500"></i>
                    <span>Kembali ke Detail</span>
                </a>
            </div>
        </div>

        {{-- Notice Banner (Running Number Preservation) --}}
        <div class="p-4 rounded-2xl bg-amber-50/70 border border-amber-200/80 flex items-start gap-3 text-xs text-amber-800">
            <div class="w-7 h-7 rounded-xl bg-amber-100 flex items-center justify-center text-amber-700 flex-shrink-0 mt-0.5">
                <i data-lucide="info" class="w-4 h-4"></i>
            </div>
            <div>
                <p class="font-bold text-amber-900">Ketentuan Nomor Seri Berjalan</p>
                <p class="text-[11px] text-amber-700/90 mt-0.5 leading-relaxed">
                    Nomor seri berjalan <strong class="font-mono text-amber-900">#{{ $runningNumberPadded }}</strong> dipertahankan secara otomatis dan tidak dapat dimodifikasi guna menjamin integritas urutan pengarsipan resmi.
                </p>
            </div>
        </div>

        {{-- Main Form Card --}}
        <div class="mac-card overflow-hidden">
            <form method="POST" action="{{ route('legalizations.update', $legalization) }}" id="formLegalisir" class="p-6 sm:p-8 space-y-8">
                @csrf
                @method('PUT')

                {{-- ── SECTION 1: Detail Alumni & Waktu ── --}}
                <div class="space-y-5">
                    <div class="flex items-center gap-3 pb-3 border-b border-gray-100">
                        <div class="mac-step-badge">1</div>
                        <div>
                            <h2 class="text-base font-bold text-gray-900 tracking-tight">Detail Alumni & Tanggal</h2>
                            <p class="text-xs text-gray-500">Informasi identitas pemohon dan waktu pencatatan transaksi</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                        {{-- Tanggal Transaksi --}}
                        <div class="space-y-1.5">
                            <label for="date" class="text-xs font-semibold text-gray-700 flex items-center gap-1.5">
                                <i data-lucide="calendar" class="w-3.5 h-3.5 text-gray-400"></i>
                                Tanggal Permohonan <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" id="date" name="date"
                                value="{{ old('date', $legalization->date ? $legalization->date->format('Y-m-d') : '') }}"
                                class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-orange-500/10 focus:border-[#FF4D00] shadow-2xs transition-all placeholder:text-gray-400 cursor-pointer @error('date') !border-rose-500 !ring-rose-500/10 @enderror"
                                placeholder="Pilih tanggal" readonly required>
                            @error('date')
                                <p class="text-[11px] text-rose-600 flex items-center gap-1 mt-1">
                                    <i data-lucide="alert-circle" class="w-3 h-3"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Tahun Kelulusan --}}
                        <div class="space-y-1.5">
                            <label for="graduation_year" class="text-xs font-semibold text-gray-700 flex items-center gap-1.5">
                                <i data-lucide="graduation-cap" class="w-3.5 h-3.5 text-gray-400"></i>
                                Tahun Kelulusan <span class="text-rose-500">*</span>
                            </label>
                            <input type="number" id="graduation_year" name="graduation_year"
                                value="{{ old('graduation_year', $legalization->graduation_year) }}" min="1950" max="{{ date('Y') + 1 }}"
                                class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-orange-500/10 focus:border-[#FF4D00] shadow-2xs transition-all placeholder:text-gray-400 @error('graduation_year') !border-rose-500 !ring-rose-500/10 @enderror"
                                placeholder="Contoh: 2024" required>
                            @error('graduation_year')
                                <p class="text-[11px] text-rose-600 flex items-center gap-1 mt-1">
                                    <i data-lucide="alert-circle" class="w-3 h-3"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Nama Lengkap Alumni --}}
                        <div class="sm:col-span-2 space-y-1.5">
                            <label for="alumni_name" class="text-xs font-semibold text-gray-700 flex items-center gap-1.5">
                                <i data-lucide="user" class="w-3.5 h-3.5 text-gray-400"></i>
                                Nama Lengkap Alumni <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" id="alumni_name" name="alumni_name"
                                value="{{ old('alumni_name', $legalization->alumni_name) }}"
                                class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-orange-500/10 focus:border-[#FF4D00] shadow-2xs transition-all placeholder:text-gray-400 @error('alumni_name') !border-rose-500 !ring-rose-500/10 @enderror"
                                placeholder="Masukkan nama lengkap alumni..." required>
                            @error('alumni_name')
                                <p class="text-[11px] text-rose-600 flex items-center gap-1 mt-1">
                                    <i data-lucide="alert-circle" class="w-3 h-3"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- ── SECTION 2: Dokumen & Perhitungan Biaya ── --}}
                <div class="space-y-5">
                    <div class="flex items-center gap-3 pb-3 border-b border-gray-100">
                        <div class="mac-step-badge">2</div>
                        <div>
                            <h2 class="text-base font-bold text-gray-900 tracking-tight">Jenjang Pendidikan & Lembar Dokumen</h2>
                            <p class="text-xs text-gray-500">Sesuaikan jenjang studi atau kuantitas lembar untuk memperbarui tagihan</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5 items-start">
                        {{-- Jenjang Pendidikan --}}
                        <div class="space-y-1.5">
                            <label for="education_level_id" class="text-xs font-semibold text-gray-700 flex items-center gap-1.5">
                                <i data-lucide="book-open" class="w-3.5 h-3.5 text-gray-400"></i>
                                Jenjang Pendidikan <span class="text-rose-500">*</span>
                            </label>
                            <select id="education_level_id" name="education_level_id"
                                class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-orange-500/10 focus:border-[#FF4D00] shadow-2xs transition-all cursor-pointer @error('education_level_id') !border-rose-500 @enderror"
                                required onchange="calculateTotal()">
                                <option value="">-- Pilih Jenjang Pendidikan --</option>
                                @foreach ($educationLevels as $level)
                                    <option value="{{ $level->id }}" data-price="{{ $level->price_per_page }}"
                                        {{ old('education_level_id', $legalization->education_level_id) == $level->id ? 'selected' : '' }}>
                                        {{ $level->name }} — Rp {{ number_format($level->price_per_page, 0, ',', '.') }}/lembar
                                    </option>
                                @endforeach
                            </select>
                            @error('education_level_id')
                                <p class="text-[11px] text-rose-600 flex items-center gap-1 mt-1">
                                    <i data-lucide="alert-circle" class="w-3 h-3"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Jumlah Lembar Dokumen --}}
                        <div class="space-y-1.5">
                            <label for="page_count" class="text-xs font-semibold text-gray-700 flex items-center justify-between">
                                <span class="flex items-center gap-1.5">
                                    <i data-lucide="files" class="w-3.5 h-3.5 text-gray-400"></i>
                                    Jumlah Lembar Dokumen <span class="text-rose-500">*</span>
                                </span>
                                <span class="text-[11px] text-gray-400 font-normal">Min. 1 lembar</span>
                            </label>

                            <div class="flex items-center gap-2">
                                <button type="button" class="stepper-btn" onclick="stepPageCount(-1)">−</button>
                                <input type="number" id="page_count" name="page_count"
                                    value="{{ old('page_count', $legalization->page_count) }}"
                                    min="1" class="flex-1 text-center font-mono font-bold px-3.5 py-2 text-sm bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-orange-500/10 focus:border-[#FF4D00] shadow-2xs transition-all @error('page_count') !border-rose-500 @enderror"
                                    required oninput="calculateTotal()">
                                <button type="button" class="stepper-btn" onclick="stepPageCount(1)">+</button>
                            </div>

                            {{-- Quick Preset Chips --}}
                            <div class="flex items-center gap-1.5 pt-1">
                                <span class="text-[10px] text-gray-400">Preset:</span>
                                @foreach ([1, 2, 5, 10, 20] as $preset)
                                    <button type="button" class="qty-preset-chip" onclick="setPreset({{ $preset }})">
                                        {{ $preset }}
                                    </button>
                                @endforeach
                            </div>

                            @error('page_count')
                                <p class="text-[11px] text-rose-600 flex items-center gap-1 mt-1">
                                    <i data-lucide="alert-circle" class="w-3 h-3"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    {{-- Live Calculation Card (Apple Warm Orange Studio) --}}
                    <div class="simulator-orange-card p-5 sm:p-6 mt-4">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-white shadow-xs border border-orange-200/80 flex items-center justify-center text-[#FF4D00] flex-shrink-0">
                                    <i data-lucide="receipt" class="w-6 h-6"></i>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h3 class="text-xs font-bold text-orange-800 uppercase tracking-wider">Total Biaya Legalisir</h3>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-white text-orange-700 border border-orange-200 shadow-2xs font-mono">
                                            PNBP
                                        </span>
                                    </div>
                                    <p class="text-xs text-orange-700/80 mt-0.5" id="formula_preview">
                                        Kalkulasi: 0 lembar × Rp 0
                                    </p>
                                </div>
                            </div>

                            <div class="text-left sm:text-right border-t sm:border-t-0 pt-3 sm:pt-0 border-orange-200/70">
                                <p class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight font-mono" id="total_price_display">
                                    Rp 0
                                </p>
                                <p class="text-[11px] text-gray-500 mt-0.5">Dihitung otomatis secara langsung</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Form Actions Bar --}}
                <div class="flex items-center justify-between pt-6 border-t border-gray-100">
                    <a href="{{ route('legalizations.show', $legalization) }}"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-white hover:bg-gray-100 border border-gray-200 text-gray-700 font-semibold text-xs sm:text-sm rounded-xl shadow-2xs transition-all active:scale-[0.98]">
                        <i data-lucide="arrow-left" class="w-4 h-4"></i>
                        <span>Batal</span>
                    </a>

                    <button type="submit" id="btnSubmit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-[#FF4D00] to-amber-500 hover:from-[#e04400] hover:to-[#d97706] text-white font-bold text-xs sm:text-sm rounded-xl shadow-xs hover:shadow-md transition-all active:scale-[0.98] cursor-pointer">
                        <i data-lucide="check" class="w-4 h-4"></i>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Live Total Price Calculation
    function calculateTotal() {
        const select = document.getElementById('education_level_id');
        const pageCountInput = document.getElementById('page_count');
        const display = document.getElementById('total_price_display');
        const formula = document.getElementById('formula_preview');

        let price = 0;
        let levelName = '';
        let count = parseInt(pageCountInput.value) || 0;
        if (count < 1) count = 1;

        if (select && select.selectedIndex > 0) {
            const option = select.options[select.selectedIndex];
            price = parseFloat(option.getAttribute('data-price')) || 0;
            levelName = option.text.split('—')[0].trim();
        }

        const total = price * count;

        if (display) {
            display.textContent = 'Rp ' + total.toLocaleString('id-ID');
        }

        if (formula) {
            if (price > 0) {
                formula.textContent = `${count} lembar × Rp ${price.toLocaleString('id-ID')} (${levelName || 'Jenjang'})`;
            } else {
                formula.textContent = 'Pilih jenjang pendidikan untuk melihat tarif.';
            }
        }
    }

    // Step input +/- helper
    function stepPageCount(delta) {
        const input = document.getElementById('page_count');
        if (!input) return;
        let current = parseInt(input.value) || 1;
        current += delta;
        if (current < 1) current = 1;
        input.value = current;
        calculateTotal();
    }

    // Quick Preset Chip Helper
    function setPreset(val) {
        const input = document.getElementById('page_count');
        if (!input) return;
        input.value = val;
        calculateTotal();
    }

    // Floating Toast Notification
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
        } else if (type === 'error') {
            toastIconWrap.className = 'w-7 h-7 rounded-xl flex items-center justify-center flex-shrink-0 bg-rose-100 text-rose-600';
            toastIcon.setAttribute('data-lucide', 'alert-circle');
        } else {
            toastIconWrap.className = 'w-7 h-7 rounded-xl flex items-center justify-center flex-shrink-0 bg-blue-100 text-blue-600';
            toastIcon.setAttribute('data-lucide', 'info');
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

    // On Page Ready
    document.addEventListener('DOMContentLoaded', function() {
        calculateTotal();

        // Flatpickr for Date Input
        if (typeof flatpickr !== 'undefined') {
            flatpickr("#date", {
                dateFormat: "Y-m-d",
                locale: "id",
                altInput: true,
                altFormat: "d F Y",
                allowInput: true,
                defaultDate: "{{ old('date', $legalization->date ? $legalization->date->format('Y-m-d') : date('Y-m-d')) }}"
            });
        }

        // Prevent Double Submit
        const form = document.getElementById('formLegalisir');
        if (form) {
            form.addEventListener('submit', function() {
                const btn = document.getElementById('btnSubmit');
                if (btn) {
                    btn.disabled = true;
                    btn.classList.add('opacity-75', 'cursor-not-allowed');
                    btn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i><span>Menyimpan...</span>';
                    if (typeof lucide !== 'undefined') lucide.createIcons();
                }
            });
        }

        @if(session('success'))
            showToast("{{ session('success') }}", 'success');
        @elseif(session('error'))
            showToast("{{ session('error') }}", 'error');
        @endif

        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
@endpush
