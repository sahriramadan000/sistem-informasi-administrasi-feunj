@extends('layouts.app')

@section('title', 'Detail Legalisir #' . str_pad($legalization->running_number, 3, '0', STR_PAD_LEFT) . ' - FEB UNJ')
@section('disable_layout_alerts', true)

@push('styles')
    <style>
        /* iOS / macOS Subtle Cards */
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

        /* Warm Orange Apple Studio Card */
        .simulator-orange-card {
            background: linear-gradient(145deg, #fff7ed 0%, #fffaf5 50%, #ffedd5 100%);
            border-radius: 1.25rem;
            border: 1.5px solid #fed7aa;
            box-shadow: 0 8px 24px -4px rgba(255, 77, 0, 0.08), 0 2px 6px -1px rgba(255, 77, 0, 0.04);
        }

        /* Number Step Badge */
        .mac-step-badge {
            width: 32px;
            height: 32px;
            border-radius: 10px;
            background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%);
            border: 1px solid #fed7aa;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ea580c;
            font-weight: 700;
            font-size: 0.875rem;
            flex-shrink: 0;
        }

        /* Floating Toast Notification (Auto Dismiss) */
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
        $bulanIndo = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $tgl = $legalization->date;
        $tglFormatted = $tgl ? ($tgl->format('j') . ' ' . ($bulanIndo[(int)$tgl->format('n')] ?? $tgl->format('F')) . ' ' . $tgl->format('Y')) : '-';
        $runningNumberPadded = str_pad($legalization->running_number, 3, '0', STR_PAD_LEFT);
        $canManage = auth()->user()->isAdmin() || auth()->user()->isOperator();

        $alumniName = $legalization->alumni_name;
        $initials = collect(explode(' ', $alumniName))
            ->filter()
            ->take(2)
            ->map(fn($w) => strtoupper(substr($w, 0, 1)))
            ->join('');
        if (empty($initials)) {
            $initials = 'AL';
        }
    @endphp

    <div class="max-w-5xl mx-auto space-y-6 pb-12 relative">
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

        {{-- Page Header - Modern iOS / macOS Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-gray-200/80">
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-[#FF4D00] to-amber-500 flex items-center justify-center text-white shadow-xs flex-shrink-0">
                    <i data-lucide="stamp" class="w-5 h-5"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">Detail Legalisir</h1>
                    <p class="text-xs sm:text-sm text-gray-500">Informasi lengkap transaksi legalisasi ijazah & transkrip nilai alumni FEB UNJ</p>
                </div>
            </div>

            <div class="flex items-center gap-2.5">
                <a href="{{ route('legalizations.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 text-xs sm:text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-200 rounded-full shadow-2xs hover:shadow-xs transition-all active:scale-[0.98]">
                    <i data-lucide="arrow-left" class="w-4 h-4 text-gray-500"></i>
                    <span>Kembali ke Daftar</span>
                </a>
            </div>
        </div>

        {{-- Highlight Card: Nomor Seri Legalisir (Warm Orange Apple Studio) --}}
        <div class="simulator-orange-card p-5 sm:p-6">
            {{-- Header Row: Label & Status Terdaftar --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-3.5 pb-3 border-b border-orange-200/80">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-white shadow-xs border border-orange-200/80 flex items-center justify-center text-[#FF4D00] flex-shrink-0">
                        <i data-lucide="hash" class="w-4.5 h-4.5"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-xs font-bold text-orange-800 uppercase tracking-wider">Nomor Seri Legalisir</h2>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-white text-orange-800 border border-orange-200/90 shadow-2xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                Terdaftar Resmi
                            </span>
                        </div>
                        <p class="text-[11px] text-orange-600/80">Nomor urut registrasi transaksi legalisir dokumen dinas</p>
                    </div>
                </div>

                <div class="text-[11px] text-orange-700/80 font-medium hidden sm:flex items-center gap-1.5">
                    <i data-lucide="shield-check" class="w-3.5 h-3.5 text-orange-500"></i>
                    <span>Arsip Sah Fakultas Ekonomi dan Bisnis</span>
                </div>
            </div>

            {{-- Main Number Box with Integrated Actions --}}
            <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-xs border border-orange-200/80 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    <div class="p-2 sm:p-3 bg-orange-50 rounded-xl border border-orange-200/60 font-mono text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">
                        #{{ $runningNumberPadded }}
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Tahun Arsip: {{ $legalization->year }}</p>
                        <p class="text-xs sm:text-sm font-semibold text-gray-700 mt-0.5">
                            {{ $legalization->alumni_name }} (Lulusan Th. {{ $legalization->graduation_year }})
                        </p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <button type="button"
                        onclick="copyToClipboard('#{{ $runningNumberPadded }}', 'Nomor legalisir #{{ $runningNumberPadded }} berhasil disalin')"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-bold text-orange-700 bg-orange-50 hover:bg-orange-100 border border-orange-200 rounded-xl transition-all active:scale-[0.98] cursor-pointer">
                        <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                        <span>Salin Nomor</span>
                    </button>

                    @if ($canManage)
                        <a href="{{ route('legalizations.edit', $legalization) }}"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-bold text-amber-700 bg-amber-50 hover:bg-amber-100 border border-amber-200 rounded-xl transition-all active:scale-[0.98]">
                            <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                            <span>Edit</span>
                        </a>

                        <button type="button"
                            onclick="openDeactivateModal('{{ route('legalizations.destroy', $legalization) }}', '#{{ $runningNumberPadded }}', '{{ addslashes($legalization->alumni_name) }}')"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-xl transition-all active:scale-[0.98] cursor-pointer">
                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                            <span>Nonaktifkan</span>
                        </button>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── Bento Grid: Detailed Information (2 Columns) ── --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left Column (2 Cols on lg): Pemohon & Dokumen --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Card 1: Informasi Pemohon --}}
                <div class="mac-card p-6">
                    <div class="flex items-center gap-3 pb-4 border-b border-gray-100 mb-5">
                        <div class="mac-step-badge">1</div>
                        <div>
                            <h3 class="text-base font-bold text-gray-900 tracking-tight">Informasi Alumni & Permohonan</h3>
                            <p class="text-xs text-gray-500">Data identitas lulusan dan waktu transaksi</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        {{-- Nama Alumni --}}
                        <div class="flex items-start gap-3.5">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-100 to-orange-100 text-[#FF4D00] border border-orange-200/60 flex items-center justify-center font-bold text-sm flex-shrink-0 shadow-2xs mt-0.5">
                                {{ $initials }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Nama Lengkap Alumni</p>
                                <p class="text-sm font-bold text-gray-900 mt-0.5">{{ $legalization->alumni_name }}</p>
                            </div>
                        </div>

                        {{-- Tahun Kelulusan --}}
                        <div class="flex items-start gap-3.5">
                            <div class="w-10 h-10 rounded-xl bg-orange-50 border border-orange-200/70 text-[#FF4D00] flex items-center justify-center flex-shrink-0 shadow-2xs mt-0.5">
                                <i data-lucide="graduation-cap" class="w-5 h-5"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Tahun Kelulusan</p>
                                <p class="text-sm font-bold text-gray-900 mt-0.5">Angkatan / Lulus {{ $legalization->graduation_year }}</p>
                            </div>
                        </div>

                        {{-- Tanggal Permohonan --}}
                        <div class="flex items-start gap-3.5">
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-200/70 text-emerald-600 flex items-center justify-center flex-shrink-0 shadow-2xs mt-0.5">
                                <i data-lucide="calendar" class="w-5 h-5"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Tanggal Transaksi</p>
                                <p class="text-sm font-bold text-gray-900 mt-0.5">{{ $tglFormatted }}</p>
                            </div>
                        </div>

                        {{-- Jenjang Pendidikan --}}
                        <div class="flex items-start gap-3.5">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-200/70 text-blue-600 flex items-center justify-center flex-shrink-0 shadow-2xs mt-0.5">
                                <i data-lucide="book-open" class="w-5 h-5"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Jenjang Pendidikan</p>
                                <div class="mt-0.5">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200 shadow-2xs">
                                        {{ $legalization->educationLevel->name ?? '-' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card 2: Rincian Biaya Legalisir --}}
                <div class="mac-card p-6">
                    <div class="flex items-center gap-3 pb-4 border-b border-gray-100 mb-5">
                        <div class="mac-step-badge">2</div>
                        <div>
                            <h3 class="text-base font-bold text-gray-900 tracking-tight">Rincian Dokumen & Tagihan PNBP</h3>
                            <p class="text-xs text-gray-500">Perhitungan tarif dan akumulasi tagihan penerimaan negara</p>
                        </div>
                    </div>

                    <div class="bg-gray-50/70 rounded-2xl p-4 sm:p-5 border border-gray-200/80 divide-y divide-gray-200/70 text-xs sm:text-sm">
                        <div class="flex items-center justify-between pb-3">
                            <span class="text-gray-500 font-medium">Jenjang Dokumen:</span>
                            <span class="font-bold text-gray-900">{{ $legalization->educationLevel->name ?? '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between py-3">
                            <span class="text-gray-500 font-medium">Tarif Satuan per Lembar:</span>
                            <span class="font-mono font-bold text-gray-800">
                                Rp {{ number_format($legalization->educationLevel->price_per_page ?? 0, 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between py-3">
                            <span class="text-gray-500 font-medium">Jumlah Lembar Legalisir:</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-mono font-bold bg-amber-50 text-amber-800 border border-amber-200/70">
                                {{ $legalization->page_count }} Lembar
                            </span>
                        </div>
                        <div class="flex items-center justify-between pt-3">
                            <span class="text-sm font-bold text-gray-900">Total Tagihan (PNBP):</span>
                            <span class="text-lg sm:text-xl font-extrabold text-[#FF4D00] font-mono">
                                Rp {{ number_format($legalization->total_price, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column (1 Col on lg): Audit & Info Sistem --}}
            <div class="space-y-6">
                {{-- Card 3: Informasi Sistem & Audit --}}
                <div class="mac-card p-6 space-y-4">
                    <div class="flex items-center gap-2 pb-3 border-b border-gray-100">
                        <div class="w-8 h-8 rounded-xl bg-gray-100 flex items-center justify-center text-gray-600">
                            <i data-lucide="cpu" class="w-4 h-4"></i>
                        </div>
                        <h3 class="text-xs font-bold text-gray-800 uppercase tracking-wider">Audit & Sistem</h3>
                    </div>

                    <div class="space-y-3.5 text-xs">
                        <div>
                            <p class="text-gray-400 font-semibold uppercase text-[10px]">Dibuat Oleh</p>
                            <div class="flex items-center gap-2 mt-1">
                                <div class="w-6 h-6 rounded-full bg-orange-100 text-[#FF4D00] flex items-center justify-center text-[10px] font-bold">
                                    {{ strtoupper(substr($legalization->creator->name ?? 'A', 0, 1)) }}
                                </div>
                                <span class="font-bold text-gray-800">{{ $legalization->creator->name ?? 'Sistem' }}</span>
                            </div>
                        </div>

                        <div>
                            <p class="text-gray-400 font-semibold uppercase text-[10px]">Waktu Pencatatan</p>
                            <p class="font-semibold text-gray-700 mt-0.5">
                                {{ $legalization->created_at ? $legalization->created_at->translatedFormat('d F Y, H:i') : '-' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-gray-400 font-semibold uppercase text-[10px]">Terakhir Diperbarui</p>
                            <p class="font-semibold text-gray-700 mt-0.5">
                                {{ $legalization->updated_at ? $legalization->updated_at->translatedFormat('d F Y, H:i') : '-' }}
                            </p>
                        </div>

                        <div class="pt-2 border-t border-gray-100">
                            <p class="text-gray-400 font-semibold uppercase text-[10px]">Status Berkas</p>
                            <div class="mt-1">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Aktif Terdaftar
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Quick Actions Card --}}
                <div class="mac-card p-5 bg-gradient-to-br from-gray-50/70 to-orange-50/20 space-y-2.5">
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">Aksi Cepat</p>

                    <a href="{{ route('legalizations.index') }}"
                        class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl bg-white hover:bg-gray-50 border border-gray-200 text-xs font-bold text-gray-700 shadow-2xs hover:shadow-xs transition-all active:scale-[0.98]">
                        <span class="flex items-center gap-2">
                            <i data-lucide="files" class="w-4 h-4 text-gray-500"></i>
                            <span>Buka Katalog Legalisir</span>
                        </span>
                        <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400"></i>
                    </a>

                    <a href="{{ route('legalizations.dashboard') }}"
                        class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl bg-white hover:bg-gray-50 border border-gray-200 text-xs font-bold text-gray-700 shadow-2xs hover:shadow-xs transition-all active:scale-[0.98]">
                        <span class="flex items-center gap-2">
                            <i data-lucide="layout-dashboard" class="w-4 h-4 text-gray-500"></i>
                            <span>Dashboard Legalisir</span>
                        </span>
                        <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400"></i>
                    </a>

                    @if ($canManage)
                        <a href="{{ route('legalizations.create') }}"
                            class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl bg-gradient-to-r from-[#FF4D00] to-amber-500 hover:from-[#e04400] hover:to-[#d97706] text-xs font-bold text-white shadow-xs hover:shadow-md transition-all active:scale-[0.98]">
                            <span class="flex items-center gap-2">
                                <i data-lucide="plus" class="w-4 h-4"></i>
                                <span>Buat Transaksi Baru</span>
                            </span>
                            <i data-lucide="chevron-right" class="w-4 h-4 text-white/80"></i>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Deactivate Confirmation Modal (Apple Sheet Style) --}}
    <div id="deactivateModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/40 backdrop-blur-md">
        <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full border border-gray-200 overflow-hidden transform transition-all scale-95 opacity-0"
            id="deactivateModalContent">
            <div class="p-6">
                <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center mb-4 shadow-2xs">
                    <i data-lucide="alert-triangle" class="w-6 h-6"></i>
                </div>

                <h3 class="text-base sm:text-lg font-bold text-gray-900 tracking-tight" id="modal-deactivate-title">
                    Konfirmasi Nonaktifkan Data
                </h3>

                <p class="text-xs sm:text-sm text-gray-600 leading-relaxed mt-2">
                    Apakah Anda yakin ingin menonaktifkan data legalisir <strong id="modalRunningNumber" class="text-gray-900 font-mono"></strong> atas nama <strong id="modalAlumniName" class="text-gray-900"></strong>?
                </p>

                <div class="mt-3 p-3 rounded-xl bg-amber-50 border border-amber-200/80 text-[11px] text-amber-800 flex items-start gap-2">
                    <i data-lucide="info" class="w-4 h-4 text-amber-600 flex-shrink-0 mt-0.5"></i>
                    <span>Nomor legalisir ini akan diarsipkan dan tidak akan muncul dalam pelaporan aktif. Riwayat transaksi tetap tercatat secara aman.</span>
                </div>

                <div class="mt-6 flex items-center justify-end gap-2.5">
                    <button type="button" onclick="closeDeactivateModal()"
                        class="px-4 py-2 text-xs sm:text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-all cursor-pointer">
                        Batal
                    </button>
                    <form id="deactivateForm" method="POST" action="">
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
    </div>
@endsection

@push('scripts')
<script>
    // Copy to Clipboard with Feedback
    function copyToClipboard(text, successMsg = 'Teks disalin ke clipboard') {
        if (!navigator.clipboard) {
            const textarea = document.createElement('textarea');
            textarea.value = text;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
            showToast(successMsg, 'success');
            return;
        }

        navigator.clipboard.writeText(text).then(() => {
            showToast(successMsg, 'success');
        }).catch(err => {
            console.error('Failed to copy: ', err);
            showToast('Gagal menyalin teks', 'error');
        });
    }

    // Modal Helpers: Deactivate Modal
    function openDeactivateModal(actionUrl, runningNumber, alumniName) {
        const modal = document.getElementById('deactivateModal');
        const content = document.getElementById('deactivateModalContent');
        const form = document.getElementById('deactivateForm');
        const numElem = document.getElementById('modalRunningNumber');
        const nameElem = document.getElementById('modalAlumniName');

        if (modal && content && form) {
            form.action = actionUrl;
            if (numElem) numElem.textContent = runningNumber;
            if (nameElem) nameElem.textContent = alumniName;

            modal.classList.remove('hidden');
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }
    }

    function closeDeactivateModal() {
        const modal = document.getElementById('deactivateModal');
        const content = document.getElementById('deactivateModalContent');
        if (!modal || !content) return;

        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 200);
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

    document.addEventListener('DOMContentLoaded', function() {
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
