@extends('layouts.app')

@section('title', 'Detail Surat - ' . $letter->letter_number)
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
        $tgl = $letter->letter_date;
        $tglFormatted = $tgl ? ($tgl->format('j') . ' ' . ($bulanIndo[(int)$tgl->format('n')] ?? $tgl->format('F')) . ' ' . $tgl->format('Y')) : '-';

        $securityLabels = [
            'B' => 'Biasa',
            'T' => 'Terbatas',
            'R' => 'Rahasia',
            'SR' => 'Sangat Rahasia'
        ];

        $canManage = auth()->user()->isAdmin() || (auth()->user()->isOperator() && $letter->created_by === auth()->id());
    @endphp

    <div class="max-w-7xl mx-auto space-y-6 pb-12 relative">
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
                    <i data-lucide="file-text" class="w-5 h-5"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">Detail Surat</h1>
                    <p class="text-xs sm:text-sm text-gray-500">Informasi lengkap surat yang dipilih</p>
                </div>
            </div>

            <div class="flex items-center gap-2.5">
                <a href="{{ route('letters.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 text-xs sm:text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-200 rounded-full shadow-2xs hover:shadow-xs transition-all active:scale-[0.98]">
                    <i data-lucide="arrow-left" class="w-4 h-4 text-gray-500"></i>
                    <span>Kembali</span>
                </a>
            </div>
        </div>

        {{-- NOMOR SURAT CARD - Highlight dengan Copy Button (Warm Orange Apple Theme) --}}
        <div class="simulator-orange-card p-5 sm:p-6">
            {{-- Header Row: Label & Status Terdaftar --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-3.5 pb-3 border-b border-orange-200/80">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-white shadow-xs border border-orange-200/80 flex items-center justify-center text-[#FF4D00] flex-shrink-0">
                        <i data-lucide="hash" class="w-4.5 h-4.5"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-xs font-bold text-orange-800 uppercase tracking-wider">Nomor Surat Resmi</h2>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-white text-orange-800 border border-orange-200/90 shadow-2xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                Terdaftar
                            </span>
                        </div>
                        <p class="text-[11px] text-orange-600/80">Diterbitkan secara otomatis oleh sistem</p>
                    </div>
                </div>

                <div class="text-[11px] text-orange-700/80 font-medium hidden sm:flex items-center gap-1.5">
                    <i data-lucide="shield-check" class="w-3.5 h-3.5 text-orange-500"></i>
                    <span>Arsip Resmi Fakultas Ekonomi dan Bisnis</span>
                </div>
            </div>

            {{-- Main Letter Number Box with Integrated Actions --}}
            <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-xs border border-orange-200/80 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex-1 min-w-0">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">Format Nomor Surat</p>
                    <p id="letterNumber" class="text-xl sm:text-2xl lg:text-3xl font-extrabold font-mono tracking-tight text-[#FF4D00] break-all select-all">
                        {{ $letter->letter_number }}
                    </p>
                </div>

                {{-- Action Buttons: Copy Nomor & Copy Info Surat --}}
                <div class="flex flex-wrap sm:flex-nowrap items-center gap-2.5 flex-shrink-0 pt-2 md:pt-0 border-t md:border-t-0 border-gray-100">
                    <button type="button" onclick="copyLetterNumber()"
                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-gradient-to-r from-[#FF4D00] to-amber-500 hover:from-[#e04400] hover:to-[#d97706] text-white font-bold text-xs sm:text-sm rounded-xl shadow-xs hover:shadow-md transition-all active:scale-[0.98] cursor-pointer"
                        title="Salin Nomor Surat Resmi">
                        <i data-lucide="copy" class="w-4 h-4"></i>
                        <span>Copy Nomor</span>
                    </button>
                    <button type="button" onclick="copyFullLetterText()"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-orange-50/90 hover:bg-orange-100/80 border border-orange-200/90 text-orange-800 font-bold text-xs sm:text-sm rounded-xl transition-all active:scale-[0.98] cursor-pointer"
                        title="Salin Rangkuman Data Surat">
                        <i data-lucide="clipboard-check" class="w-4 h-4 text-orange-600"></i>
                        <span>Copy Info Surat</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- 2-COLUMN GRID LAYOUT --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- SECTION 1: Informasi Utama (2 Kolom) --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- CARD 1: Informasi Dasar --}}
                <div class="mac-card overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="mac-step-badge">1</div>
                            <h3 class="text-base font-bold text-gray-900 tracking-tight">Informasi Dasar</h3>
                        </div>
                        <span class="text-xs text-gray-400">Atribut Surat</span>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            {{-- Tanggal Surat --}}
                            <div class="flex items-start gap-3.5">
                                <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-200/80 text-emerald-600 flex items-center justify-center flex-shrink-0">
                                    <i data-lucide="calendar" class="w-5 h-5"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-0.5">Tanggal Surat</p>
                                    <p class="text-sm font-bold text-gray-900">{{ $tglFormatted }}</p>
                                </div>
                            </div>

                            {{-- Status --}}
                            <div class="flex items-start gap-3.5">
                                <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-200/80 text-blue-600 flex items-center justify-center flex-shrink-0">
                                    <i data-lucide="check-circle" class="w-5 h-5"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-0.5">Status</p>
                                    <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold
                                        @if ($letter->status == 'final') bg-emerald-50 text-emerald-800 border border-emerald-200
                                        @elseif($letter->status == 'draft') bg-amber-50 text-amber-800 border border-amber-200
                                        @else bg-red-50 text-red-800 border border-red-200 @endif">
                                        @if ($letter->status == 'final')
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        @elseif($letter->status == 'draft')
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        @else
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                        @endif
                                        {{ ucfirst($letter->status) }}
                                    </span>
                                </div>
                            </div>

                            {{-- Jenis Surat --}}
                            <div class="flex items-start gap-3.5">
                                <div class="w-10 h-10 rounded-xl bg-purple-50 border border-purple-200/80 text-purple-600 flex items-center justify-center flex-shrink-0">
                                    <i data-lucide="file-type" class="w-5 h-5"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-0.5">Jenis Surat</p>
                                    <p class="text-sm font-bold text-gray-900 truncate">{{ $letter->letterType->name }}</p>
                                </div>
                            </div>

                            {{-- Klasifikasi --}}
                            <div class="flex items-start gap-3.5">
                                <div class="w-10 h-10 rounded-xl bg-amber-50 border border-amber-200/80 text-amber-600 flex items-center justify-center flex-shrink-0">
                                    <i data-lucide="folder" class="w-5 h-5"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-0.5">Klasifikasi</p>
                                    <p class="text-sm font-bold text-gray-900 truncate" title="{{ $letter->classification->name }}">{{ $letter->classification->name }}</p>
                                    <p class="text-xs text-amber-700 font-mono font-bold mt-0.5">{{ $letter->classification->code }}</p>
                                </div>
                            </div>

                            {{-- Klasifikasi Keamanan --}}
                            <div class="flex items-start gap-3.5">
                                <div class="w-10 h-10 rounded-xl bg-red-50 border border-red-200/80 text-red-600 flex items-center justify-center flex-shrink-0">
                                    <i data-lucide="shield" class="w-5 h-5"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-0.5">Klasifikasi Keamanan</p>
                                    <p class="text-sm font-bold text-gray-900">{{ $letter->security_classification }} - {{ $securityLabels[$letter->security_classification] ?? 'Biasa' }}</p>
                                </div>
                            </div>

                            {{-- Sasaran Surat --}}
                            <div class="flex items-start gap-3.5">
                                <div class="w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-200/80 text-indigo-600 flex items-center justify-center flex-shrink-0">
                                    <i data-lucide="target" class="w-5 h-5"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-0.5">Sasaran Surat</p>
                                    <p class="text-sm font-bold text-gray-900">{{ ucfirst($letter->letter_target) }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        {{ $letter->letter_target === 'internal' ? 'Sirkulasi internal FEB UNJ' : 'Tujuan instansi eksternal' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CARD 2: Isi Surat (Apple Clean Inspector Layout) --}}
                <div class="mac-card overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="mac-step-badge">2</div>
                            <h3 class="text-base font-bold text-gray-900 tracking-tight">Isi Surat</h3>
                        </div>
                        <span class="text-xs text-gray-400 font-medium">Substansi &amp; Penerima</span>
                    </div>

                    <div class="p-6 space-y-5">
                        {{-- Perihal / Hal --}}
                        @if ($letter->subject)
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-2">
                                        <i data-lucide="align-left" class="w-4 h-4 text-orange-500"></i>
                                        <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Perihal / Hal</h4>
                                    </div>
                                    <button type="button" onclick="copyToClipboard('{{ addslashes($letter->subject) }}', 'Perihal surat berhasil disalin!')"
                                        class="inline-flex items-center gap-1 text-[11px] font-medium text-gray-400 hover:text-orange-600 transition-colors cursor-pointer"
                                        title="Salin Perihal">
                                        <i data-lucide="copy" class="w-3 h-3"></i>
                                        <span>Salin</span>
                                    </button>
                                </div>
                                <div class="bg-gray-50/80 hover:bg-gray-50/95 rounded-2xl p-4.5 border border-gray-200/80 shadow-2xs transition-colors">
                                    <p class="text-sm sm:text-base font-semibold text-gray-900 leading-relaxed">{{ $letter->subject }}</p>
                                </div>
                            </div>
                        @endif

                        {{-- Ditujukan Kepada --}}
                        @if ($letter->recipient)
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-2">
                                        <i data-lucide="send" class="w-4 h-4 text-blue-500"></i>
                                        <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Ditujukan Kepada</h4>
                                    </div>
                                    @if ($letter->letter_target)
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $letter->letter_target === 'internal' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-indigo-50 text-indigo-700 border border-indigo-200' }}">
                                            {{ ucfirst($letter->letter_target) }}
                                        </span>
                                    @endif
                                </div>
                                <div class="bg-gray-50/80 hover:bg-gray-50/95 rounded-2xl p-4.5 border border-gray-200/80 shadow-2xs transition-colors">
                                    <p class="text-sm sm:text-base font-semibold text-gray-900 leading-relaxed">{{ $letter->recipient }}</p>
                                </div>
                            </div>
                        @endif

                        {{-- Keperluan Surat --}}
                        @if ($letter->letterPurpose)
                            <div>
                                <div class="flex items-center gap-2 mb-2">
                                    <i data-lucide="clipboard-list" class="w-4 h-4 text-purple-500"></i>
                                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Keperluan</h4>
                                </div>
                                <div class="bg-gray-50/80 hover:bg-gray-50/95 rounded-2xl p-4 border border-gray-200/80 shadow-2xs transition-colors">
                                    <p class="text-sm font-medium text-gray-900 leading-relaxed">{{ $letter->letterPurpose->name }}</p>
                                </div>
                            </div>
                        @endif

                        {{-- Nama Mahasiswa --}}
                        @if ($letter->student_name)
                            <div>
                                <div class="flex items-center gap-2 mb-2">
                                    <i data-lucide="user" class="w-4 h-4 text-emerald-500"></i>
                                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Mahasiswa</h4>
                                </div>
                                <div class="bg-gray-50/80 hover:bg-gray-50/95 rounded-2xl p-4 border border-gray-200/80 shadow-2xs transition-colors">
                                    <p class="text-sm font-semibold text-gray-900 leading-relaxed">{{ $letter->student_name }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

            </div>

            {{-- SECTION 2: Sidebar Info (1 Kolom) --}}
            <div class="space-y-6">

                {{-- Quick Actions Card --}}
                @if ($canManage)
                    <div class="mac-card p-5">
                        <h3 class="text-xs font-bold text-gray-900 uppercase tracking-wider mb-3.5 flex items-center gap-2">
                            <i data-lucide="zap" class="w-4 h-4 text-[#FF4D00]"></i>
                            Aksi Cepat
                        </h3>
                        <div class="space-y-2.5">
                            {{-- Edit Button (Soft Tinted Amber Theme - Apple Style) --}}
                            <a href="{{ route('letters.edit', $letter) }}"
                                class="flex items-center gap-3.5 px-4 py-3 bg-amber-50 hover:bg-amber-100/80 border border-amber-200/90 text-amber-950 rounded-xl shadow-2xs hover:shadow-xs transition-all active:scale-[0.98] group">
                                <div class="w-9 h-9 rounded-xl bg-amber-100 border border-amber-200/80 text-amber-700 flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
                                    <i data-lucide="edit-3" class="w-4.5 h-4.5"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs sm:text-sm font-bold text-amber-950 tracking-tight">Edit Surat</p>
                                    <p class="text-[11px] text-amber-700/80 truncate">Perbarui rincian data surat</p>
                                </div>
                                <i data-lucide="chevron-right" class="w-4 h-4 text-amber-400 group-hover:text-amber-600 group-hover:translate-x-0.5 transition-all"></i>
                            </a>

                            {{-- Nonaktifkan Button (Soft Tinted Red Theme - Apple Danger Style) --}}
                            <form id="deleteForm" method="POST" action="{{ route('letters.destroy', $letter) }}">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="showDeleteModal()"
                                    class="w-full flex items-center gap-3.5 px-4 py-3 bg-red-50 hover:bg-red-100/80 border border-red-200/90 text-red-950 rounded-xl shadow-2xs hover:shadow-xs transition-all active:scale-[0.98] group cursor-pointer text-left">
                                    <div class="w-9 h-9 rounded-xl bg-red-100 border border-red-200/80 text-red-600 flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
                                        <i data-lucide="trash-2" class="w-4.5 h-4.5"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs sm:text-sm font-bold text-red-950 tracking-tight">Nonaktifkan Surat</p>
                                        <p class="text-[11px] text-red-700/80 truncate">Arsipkan &amp; nonaktifkan nomor</p>
                                    </div>
                                    <i data-lucide="chevron-right" class="w-4 h-4 text-red-400 group-hover:text-red-600 group-hover:translate-x-0.5 transition-all"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @endif

                {{-- CARD ARSIP DIGITAL (Google Drive) - Hanya untuk jenis surat yang membutuhkan arsip Drive --}}
                @if ($letter->requires_drive_archive)
                <div class="mac-card overflow-hidden border {{ $letter->is_archived ? 'border-emerald-200/90 bg-emerald-50/20' : 'border-gray-200/90' }}">
                    <div class="px-5 py-3.5 border-b {{ $letter->is_archived ? 'border-emerald-100 bg-emerald-50/60' : 'border-gray-100 bg-gray-50/50' }} flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <i data-lucide="cloud" class="w-4.5 h-4.5 {{ $letter->is_archived ? 'text-emerald-600' : 'text-gray-400' }}"></i>
                            <h3 class="text-xs font-bold text-gray-900 uppercase tracking-wider">Arsip Digital</h3>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $letter->is_archived ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-gray-100 text-gray-600 border border-gray-200' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $letter->is_archived ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                            {{ $letter->is_archived ? 'Terarsip di GDrive' : 'Belum Diarsipkan' }}
                        </span>
                    </div>

                    <div class="p-5 space-y-4">
                        @if ($letter->is_archived)
                            <div class="p-3 bg-white rounded-xl border border-emerald-200/80 shadow-2xs space-y-2">
                                <div class="flex items-start gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center flex-shrink-0">
                                        <i data-lucide="file-check" class="w-4 h-4"></i>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs font-bold text-gray-900">Dokumen Digital Tersedia</p>
                                        <p class="text-[11px] text-gray-500 truncate" title="{{ $letter->drive_url }}">{{ $letter->drive_url }}</p>
                                    </div>
                                </div>
                                @if ($letter->archived_at)
                                    <p class="text-[10px] text-gray-400 pt-1 border-t border-gray-100">
                                        Diarsipkan pada {{ $letter->archived_at->format('d/m/Y H:i') }}
                                        @if ($letter->archivedBy)
                                            oleh {{ $letter->archivedBy->name }}
                                        @endif
                                    </p>
                                @endif
                            </div>

                            <div class="space-y-2">
                                @if ($letter->drive_preview_url)
                                    <button type="button" onclick="openPreviewModal('{{ $letter->drive_preview_url }}', '{{ $letter->letter_number }}')"
                                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-xs transition-all active:scale-[0.98] cursor-pointer">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                        <span>Pratinjau Dokumen</span>
                                    </button>
                                @endif

                                <a href="{{ $letter->drive_url }}" target="_blank" rel="noopener noreferrer"
                                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-white hover:bg-gray-50 border border-gray-200 text-gray-700 font-semibold text-xs rounded-xl shadow-2xs transition-all active:scale-[0.98]">
                                    <i data-lucide="external-link" class="w-4 h-4 text-emerald-600"></i>
                                    <span>Buka di Google Drive</span>
                                </a>

                                @if ($canManage)
                                    <button type="button" onclick="openArchiveModal({{ $letter->id }}, '{{ addslashes($letter->letter_number) }}', '{{ addslashes($letter->drive_url ?? '') }}')"
                                        class="w-full inline-flex items-center justify-center gap-1.5 py-1 text-[11px] font-semibold text-gray-500 hover:text-emerald-700 transition-colors cursor-pointer">
                                        <i data-lucide="edit-2" class="w-3 h-3"></i>
                                        <span>Ubah Tautan Arsip</span>
                                    </button>
                                @endif
                            </div>
                        @else
                            <div class="text-center py-2 space-y-2">
                                <div class="w-10 h-10 rounded-xl bg-gray-100 text-gray-400 flex items-center justify-center mx-auto">
                                    <i data-lucide="file-question" class="w-5 h-5"></i>
                                </div>
                                <p class="text-xs text-gray-500">Berkas digital belum ditautkan ke Google Drive.</p>
                            </div>

                            @if ($canManage)
                                <button type="button" onclick="openArchiveModal({{ $letter->id }}, '{{ addslashes($letter->letter_number) }}', '')"
                                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-xs rounded-xl shadow-xs transition-all active:scale-[0.98] cursor-pointer">
                                    <i data-lucide="link" class="w-4 h-4"></i>
                                    <span>Tautkan Berkas Google Drive</span>
                                </button>
                            @endif
                        @endif
                    </div>
                </div>
                @endif

                {{-- Card Penandatangan --}}
                <div class="mac-card overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-gray-100 bg-gray-50/50 flex items-center gap-2.5">
                        <i data-lucide="user-check" class="w-4.5 h-4.5 text-cyan-600"></i>
                        <h3 class="text-xs font-bold text-gray-900 uppercase tracking-wider">Penandatangan</h3>
                    </div>

                    <div class="p-6 text-center">
                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-cyan-100 to-blue-200 flex items-center justify-center mx-auto mb-3.5 shadow-sm text-cyan-700">
                            <i data-lucide="pen-tool" class="w-8 h-8"></i>
                        </div>
                        <p class="text-base font-bold text-gray-900">{{ $letter->signatory->name ?? '-' }}</p>
                        <p class="text-xs text-gray-600 mt-0.5">{{ $letter->signatory->position ?? '-' }}</p>
                        @if ($letter->signatory && $letter->signatory->nip)
                            <p class="text-[11px] font-mono text-gray-400 mt-1">NIP. {{ $letter->signatory->nip }}</p>
                        @endif

                        <div class="mt-4 pt-3 border-t border-gray-100">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-cyan-50 rounded-full border border-cyan-200 text-cyan-700 text-xs font-semibold">
                                <i data-lucide="shield-check" class="w-3.5 h-3.5 text-cyan-600"></i>
                                Verified
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Card Informasi Lainnya --}}
                <div class="mac-card overflow-hidden">
                    <div class="px-5 py-3.5 border-b border-gray-100 bg-gray-50/50 flex items-center gap-2.5">
                        <i data-lucide="info" class="w-4.5 h-4.5 text-gray-500"></i>
                        <h3 class="text-xs font-bold text-gray-900 uppercase tracking-wider">Informasi Lainnya</h3>
                    </div>

                    <div class="p-5 space-y-4">
                        {{-- Dibuat oleh --}}
                        <div class="flex items-start gap-3 pb-3 border-b border-gray-100">
                            <div class="w-8 h-8 rounded-lg bg-indigo-50 border border-indigo-200/70 text-indigo-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i data-lucide="user" class="w-4 h-4"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-0.5">Dibuat Oleh</p>
                                <p class="text-xs sm:text-sm font-bold text-gray-900 truncate">{{ $letter->creator->name ?? 'Administrator' }}</p>
                            </div>
                        </div>

                        {{-- Tanggal dibuat --}}
                        <div class="flex items-start gap-3 pb-3 border-b border-gray-100">
                            <div class="w-8 h-8 rounded-lg bg-emerald-50 border border-emerald-200/70 text-emerald-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i data-lucide="clock" class="w-4 h-4"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-0.5">Tanggal Dibuat</p>
                                <p class="text-xs sm:text-sm font-bold text-gray-900">{{ $letter->created_at->format('d/m/Y') }}</p>
                                <p class="text-[11px] text-gray-500 mt-0.5">{{ $letter->created_at->format('H:i') }} WIB</p>
                            </div>
                        </div>

                        {{-- Terakhir diupdate --}}
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-amber-50 border border-amber-200/70 text-amber-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i data-lucide="history" class="w-4 h-4"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-0.5">Terakhir Diupdate</p>
                                <p class="text-xs sm:text-sm font-bold text-gray-900">{{ $letter->updated_at->format('d/m/Y') }}</p>
                                <p class="text-[11px] text-gray-500 mt-0.5">{{ $letter->updated_at->format('H:i') }} WIB</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

    {{-- Delete Confirmation Modal (Apple Style Dialog) --}}
    <div id="deleteModal"
        class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/40 backdrop-blur-md">
        <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full border border-gray-200 overflow-hidden transform transition-all scale-95 opacity-0"
            id="deleteModalContent">
            {{-- Modal Body --}}
            <div class="p-6 text-center space-y-4">
                <div class="w-14 h-14 rounded-2xl bg-red-50 text-red-600 border border-red-200 flex items-center justify-center mx-auto shadow-2xs">
                    <i data-lucide="alert-triangle" class="w-7 h-7"></i>
                </div>

                <div class="space-y-1">
                    <h3 class="text-lg font-bold text-gray-900">Konfirmasi Nonaktifkan Surat</h3>
                    <p class="text-xs text-gray-500 leading-relaxed">
                        Apakah Anda yakin ingin menonaktifkan surat ini? Data nomor surat tetap tersimpan di dalam arsip.
                    </p>
                </div>

                <div class="bg-gray-50 border border-gray-200/90 rounded-2xl p-3.5 text-left">
                    <p class="text-xs text-gray-600 flex items-center gap-2">
                        <i data-lucide="hash" class="w-3.5 h-3.5 text-orange-500 flex-shrink-0"></i>
                        <span>Nomor Surat: <strong class="font-mono text-gray-900">{{ $letter->letter_number }}</strong></span>
                    </p>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center gap-3 pt-2">
                    <button type="button" onclick="hideDeleteModal()"
                        class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200/80 text-gray-700 font-semibold text-xs rounded-xl transition-all active:scale-[0.98] border border-gray-200 cursor-pointer">
                        <span>Batal</span>
                    </button>
                    <button type="button" onclick="confirmDelete()"
                        class="flex-1 px-4 py-2.5 bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 text-white font-bold text-xs rounded-xl transition-all shadow-md shadow-red-600/20 active:scale-[0.98] cursor-pointer">
                        <span>Ya, Nonaktifkan</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Google Drive Document Preview Modal --}}
    <div id="previewModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6 bg-gray-900/60 backdrop-blur-md">
        <div class="bg-white rounded-3xl shadow-2xl max-w-4xl w-full h-[88vh] border border-gray-200 overflow-hidden flex flex-col transform transition-all scale-95 opacity-0" id="previewModalContent">
            {{-- Header --}}
            <div class="px-5 py-3.5 border-b border-gray-200 flex items-center justify-between bg-gray-50/80">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center flex-shrink-0">
                        <i data-lucide="file-text" class="w-4 h-4"></i>
                    </div>
                    <div class="min-w-0">
                        <h4 class="text-xs sm:text-sm font-bold text-gray-900 truncate" id="previewModalTitle">Pratinjau Dokumen</h4>
                        <p class="text-[11px] text-gray-500">Google Drive Embedded Viewer</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a id="previewExternalLink" href="#" target="_blank" rel="noopener noreferrer"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-gray-700 bg-white hover:bg-gray-100 border border-gray-200 rounded-xl transition-colors">
                        <i data-lucide="external-link" class="w-3.5 h-3.5 text-emerald-600"></i>
                        <span class="hidden sm:inline">Buka di Tab Baru</span>
                    </a>
                    <button type="button" onclick="hidePreviewModal()" class="w-8 h-8 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-500 hover:text-gray-700 flex items-center justify-center transition-colors cursor-pointer">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>

            {{-- Viewer Iframe --}}
            <div class="flex-1 bg-gray-100 relative">
                <iframe id="previewIframe" src="" class="w-full h-full border-0" allow="autoplay"></iframe>
            </div>
        </div>
    </div>

    {{-- Quick Tautkan / Ubah Link Arsip Modal --}}
    <div id="archiveModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/40 backdrop-blur-md">
        <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full border border-gray-200 overflow-hidden transform transition-all scale-95 opacity-0" id="archiveModalContent">
            <form id="archiveForm" method="POST" action="{{ route('letters.update-archive-link', $letter) }}" onsubmit="submitArchiveLink(event)">
                @csrf
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shadow-2xs">
                                <i data-lucide="folder-symlink" class="w-4.5 h-4.5"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-gray-900">Tautkan Berkas Google Drive</h3>
                                <p class="text-[11px] text-gray-500 font-mono">{{ $letter->letter_number }}</p>
                            </div>
                        </div>
                        <button type="button" onclick="hideArchiveModal()" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>

                    <div class="space-y-1.5">
                        <label for="modal_drive_url" class="text-xs font-semibold text-gray-700 flex items-center gap-1.5">
                            <i data-lucide="link" class="w-3.5 h-3.5 text-gray-400"></i>
                            URL / Link Google Drive
                        </label>
                        <input type="url" id="modal_drive_url" name="drive_url" value="{{ $letter->drive_url }}"
                            class="input text-xs font-medium"
                            placeholder="https://drive.google.com/file/d/.../view?usp=sharing">
                        <p class="text-[11px] text-gray-400">Kosongkan link jika ingin menghapus berkas arsip.</p>
                    </div>

                    <div class="flex items-center gap-2 pt-2">
                        <button type="button" onclick="hideArchiveModal()"
                            class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-xs rounded-xl transition-all cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" id="btnSubmitArchive"
                            class="flex-1 px-4 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-xs rounded-xl shadow-xs transition-all cursor-pointer">
                            Simpan Tautan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            // Toast Notification Manager
            let toastTimer = null;

            function showToast(message, type = 'success') {
                const toast = document.getElementById('toastPopup');
                const toastMsg = document.getElementById('toastMessage');
                const toastIconWrap = document.getElementById('toastIconWrap');
                const toastIcon = document.getElementById('toastIcon');

                if (!toast || !toastMsg) return;

                toastMsg.textContent = message;

                if (type === 'error') {
                    toast.className = 'toast-popup show fixed top-5 left-1/2 z-[9999] flex items-center gap-3 px-4.5 py-3 rounded-2xl bg-white/95 backdrop-blur-md border border-red-200/90 text-gray-800 shadow-xl shadow-red-500/10 text-xs sm:text-sm font-medium';
                    toastIconWrap.className = 'w-7 h-7 rounded-xl bg-red-100 text-red-600 flex items-center justify-center flex-shrink-0';
                    toastIcon.setAttribute('data-lucide', 'alert-circle');
                } else {
                    toast.className = 'toast-popup show fixed top-5 left-1/2 z-[9999] flex items-center gap-3 px-4.5 py-3 rounded-2xl bg-white/95 backdrop-blur-md border border-emerald-200/90 text-gray-800 shadow-xl shadow-emerald-500/10 text-xs sm:text-sm font-medium';
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

            function copyToClipboard(text, successMessage) {
                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(text).then(function() {
                        showToast(successMessage, 'success');
                    }).catch(function() {
                        fallbackCopyText(text, successMessage);
                    });
                } else {
                    fallbackCopyText(text, successMessage);
                }
            }

            function fallbackCopyText(text, successMessage) {
                try {
                    const temp = document.createElement('textarea');
                    temp.value = text;
                    temp.style.position = 'fixed';
                    temp.style.top = '-9999px';
                    temp.style.left = '-9999px';
                    document.body.appendChild(temp);
                    temp.focus();
                    temp.select();
                    const successful = document.execCommand('copy');
                    document.body.removeChild(temp);
                    if (successful) {
                        showToast(successMessage, 'success');
                    } else {
                        showToast('Gagal menyalin ke clipboard.', 'error');
                    }
                } catch (e) {
                    showToast('Gagal menyalin ke clipboard.', 'error');
                }
            }

            // Copy Letter Number to Clipboard
            function copyLetterNumber() {
                const letterNumber = document.getElementById('letterNumber').textContent.trim();
                copyToClipboard(letterNumber, 'Nomor surat berhasil disalin ke clipboard!');
            }

            // Copy Full Letter Information
            function copyFullLetterText() {
                const text = `Nomor: {{ $letter->letter_number }}\nTanggal: {{ $tglFormatted }}\nPerihal: {{ $letter->subject }}\nKepada: {{ $letter->recipient }}\nPenandatangan: {{ $letter->signatory->name ?? '-' }}`;
                copyToClipboard(text, 'Informasi surat berhasil disalin ke clipboard!');
            }

            // Show delete modal with smooth scale animation
            function showDeleteModal() {
                const modal = document.getElementById('deleteModal');
                const modalContent = document.getElementById('deleteModalContent');
                if (!modal || !modalContent) return;

                modal.classList.remove('hidden');
                setTimeout(() => {
                    modalContent.classList.remove('scale-95', 'opacity-0');
                    modalContent.classList.add('scale-100', 'opacity-100');
                }, 10);
                lucide.createIcons();
            }

            // Hide delete modal
            function hideDeleteModal() {
                const modal = document.getElementById('deleteModal');
                const modalContent = document.getElementById('deleteModalContent');
                if (!modal || !modalContent) return;

                modalContent.classList.remove('scale-100', 'opacity-100');
                modalContent.classList.add('scale-95', 'opacity-0');

                setTimeout(() => {
                    modal.classList.add('hidden');
                }, 200);
            }

            // Confirm delete and submit form
            function confirmDelete() {
                document.getElementById('deleteForm').submit();
            }

            // Close modal when clicking outside
            document.getElementById('deleteModal')?.addEventListener('click', function(e) {
                if (e.target === this) {
                    hideDeleteModal();
                }
            });

            // Close modal with ESC key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    hideDeleteModal();
                }
            });

            // Trigger session toast on page load
            $(document).ready(function() {
                @if (session('success'))
                    showToast("{{ session('success') }}", 'success');
                @elseif (session('error'))
                    showToast("{{ session('error') }}", 'error');
                @elseif ($errors->any())
                    showToast("{{ $errors->first() }}", 'error');
                @endif
            });

            // Preview Modal Manager
            function openPreviewModal(previewUrl, title) {
                const modal = document.getElementById('previewModal');
                const modalContent = document.getElementById('previewModalContent');
                const iframe = document.getElementById('previewIframe');
                const titleEl = document.getElementById('previewModalTitle');
                const externalLink = document.getElementById('previewExternalLink');

                if (!modal || !modalContent || !iframe) return;

                titleEl.textContent = title ? 'Pratinjau Surat: ' + title : 'Pratinjau Dokumen';
                iframe.src = previewUrl;
                externalLink.href = previewUrl.replace('/preview', '/view');

                modal.classList.remove('hidden');
                setTimeout(() => {
                    modalContent.classList.remove('scale-95', 'opacity-0');
                    modalContent.classList.add('scale-100', 'opacity-100');
                }, 10);
                lucide.createIcons();
            }

            function hidePreviewModal() {
                const modal = document.getElementById('previewModal');
                const modalContent = document.getElementById('previewModalContent');
                const iframe = document.getElementById('previewIframe');
                if (!modal || !modalContent) return;

                modalContent.classList.remove('scale-100', 'opacity-100');
                modalContent.classList.add('scale-95', 'opacity-0');

                setTimeout(() => {
                    modal.classList.add('hidden');
                    if (iframe) iframe.src = '';
                }, 200);
            }

            // Archive Link Modal Manager
            function openArchiveModal(letterId, letterNumber, currentUrl) {
                const modal = document.getElementById('archiveModal');
                const modalContent = document.getElementById('archiveModalContent');
                const input = document.getElementById('modal_drive_url');
                if (!modal || !modalContent) return;

                if (input) input.value = currentUrl || '';

                modal.classList.remove('hidden');
                setTimeout(() => {
                    modalContent.classList.remove('scale-95', 'opacity-0');
                    modalContent.classList.add('scale-100', 'opacity-100');
                    if (input) input.focus();
                }, 10);
                lucide.createIcons();
            }

            function hideArchiveModal() {
                const modal = document.getElementById('archiveModal');
                const modalContent = document.getElementById('archiveModalContent');
                if (!modal || !modalContent) return;

                modalContent.classList.remove('scale-100', 'opacity-100');
                modalContent.classList.add('scale-95', 'opacity-0');

                setTimeout(() => {
                    modal.classList.add('hidden');
                }, 200);
            }

            // AJAX submit for archive link
            async function submitArchiveLink(e) {
                e.preventDefault();
                const form = document.getElementById('archiveForm');
                const btn = document.getElementById('btnSubmitArchive');
                const url = form.action;
                const driveUrl = document.getElementById('modal_drive_url').value;

                btn.disabled = true;
                btn.innerHTML = '<span class="inline-block animate-spin mr-1">⏳</span> Menyimpan...';

                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ drive_url: driveUrl })
                    });

                    const data = await response.json();
                    if (data.success) {
                        hideArchiveModal();
                        showToast(data.message, 'success');
                        setTimeout(() => window.location.reload(), 800);
                    } else {
                        showToast(data.message || 'Gagal menyimpan tautan.', 'error');
                        btn.disabled = false;
                        btn.innerHTML = 'Simpan Tautan';
                    }
                } catch (err) {
                    showToast('Terjadi kesalahan jaringan.', 'error');
                    btn.disabled = false;
                    btn.innerHTML = 'Simpan Tautan';
                }
            }
        </script>
    @endpush
@endsection
