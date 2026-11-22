@extends('layouts.app')

@section('title', 'Studio Sertifikat - ' . $letter->letter_number)
@section('disable_layout_alerts', true)

@push('styles')
    <style>
        .mac-card {
            background: #ffffff;
            border-radius: 1.25rem;
            border: 1px solid rgba(226, 232, 240, 0.85);
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.03), 0 2px 6px -1px rgba(0, 0, 0, 0.02);
        }
        .studio-stage-bg {
            background-color: #f1f5f9;
            background-image: radial-gradient(#cbd5e1 1px, transparent 1px);
            background-size: 18px 18px;
        }
        .checkerboard-mini {
            background-color: #f8fafc;
            background-image:
                linear-gradient(45deg, #e2e8f0 25%, transparent 25%),
                linear-gradient(-45deg, #e2e8f0 25%, transparent 25%),
                linear-gradient(45deg, transparent 75%, #e2e8f0 75%),
                linear-gradient(-45deg, transparent 75%, #e2e8f0 75%);
            background-size: 10px 10px;
            background-position: 0 0, 0 5px, 5px -5px, -5px 0px;
        }
        .shortcut-chip {
            transition: all 0.16s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .shortcut-chip:hover {
            transform: translateY(-1px);
        }
        .shortcut-chip:active {
            transform: scale(0.97);
        }
    </style>
@endpush

@section('content')
<div class="space-y-4 pb-10 relative">
    {{-- Floating Toast --}}
    <div id="studioToast" class="fixed top-5 left-1/2 -translate-x-1/2 z-[9999] hidden items-center gap-2.5 px-4 py-2.5 rounded-2xl bg-slate-900/95 text-white text-xs font-semibold shadow-2xl backdrop-blur-md">
        <i id="studioToastIcon" data-lucide="check-circle-2" class="w-4 h-4 text-emerald-400"></i>
        <span id="studioToastText"></span>
    </div>

    {{-- ── TOP STUDIO BAR ── --}}
    <div class="mac-card p-3.5 sm:p-4 flex flex-col xl:flex-row xl:items-center justify-between gap-3">
        <div class="flex items-center gap-3 min-w-0">
            <a href="{{ route('letters.show', $letter) }}"
                class="w-9 h-9 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 flex items-center justify-center transition-colors flex-shrink-0"
                title="Kembali ke Detail Surat">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
            </a>
            <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-indigo-600 to-blue-600 text-white flex items-center justify-center shadow-xs flex-shrink-0">
                <i data-lucide="award" class="w-5 h-5"></i>
            </div>
            <div class="min-w-0">
                <div class="flex flex-wrap items-center gap-2">
                    <h1 class="text-base sm:text-lg font-extrabold text-gray-900 tracking-tight">Studio Sertifikat</h1>
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-mono font-bold bg-orange-50 text-[#FF4D00] border border-orange-200/80">
                        {{ $letter->letter_number }}
                    </span>
                </div>
                <p class="text-[11px] text-gray-500 truncate max-w-md" title="{{ $letter->subject ?: $letter->recipient }}">
                    {{ $letter->letterType?->name ?? 'Sertifikat' }} &bull; {{ $letter->subject ?: ($letter->recipient ?: 'Dokumen Resmi') }}
                </p>
            </div>
        </div>

        {{-- Presets + Export Actions --}}
        <div class="flex flex-wrap items-center gap-2">
            {{-- Preset Picker --}}
            <div class="flex items-center gap-1.5 bg-gray-50 border border-gray-200/90 rounded-xl p-1">
                <select id="presetSelect" class="bg-transparent text-xs font-semibold text-gray-700 px-2 py-1 focus:outline-none max-w-[165px]">
                    <option value="">-- Pilih Preset Posisi --</option>
                    @foreach ($presets as $preset)
                        <option value="{{ $preset->id }}">{{ $preset->name }}</option>
                    @endforeach
                </select>
                <button type="button" onclick="applySelectedPreset()"
                    class="px-2.5 py-1 rounded-lg bg-white hover:bg-indigo-50 text-indigo-700 border border-gray-200 text-[11px] font-bold transition-colors cursor-pointer"
                    title="Terapkan posisi preset ke kanvas">
                    Terapkan
                </button>
                <button type="button" onclick="openPresetModal()"
                    class="px-2.5 py-1 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-[11px] font-bold transition-colors cursor-pointer"
                    title="Simpan tata letak saat ini sebagai preset">
                    + Simpan Preset
                </button>
                <button type="button" onclick="deleteSelectedPreset()"
                    class="p-1.5 rounded-lg text-gray-400 hover:text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer"
                    title="Hapus preset terpilih">
                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                </button>
            </div>

            {{-- Export Dropdown / Pills --}}
            <div class="flex flex-wrap items-center gap-1.5">
                <button type="button" onclick="exportCertificate('png')"
                    class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-bold text-gray-700 bg-white hover:bg-gray-50 border border-gray-200 rounded-xl shadow-2xs transition-all cursor-pointer">
                    <i data-lucide="image" class="w-3.5 h-3.5 text-emerald-600"></i>
                    <span>PNG</span>
                </button>
                <button type="button" onclick="exportCertificate('jpg')"
                    class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-bold text-gray-700 bg-white hover:bg-gray-50 border border-gray-200 rounded-xl shadow-2xs transition-all cursor-pointer">
                    <i data-lucide="file-image" class="w-3.5 h-3.5 text-amber-600"></i>
                    <span>JPG</span>
                </button>
                <button type="button" onclick="exportCertificate('pdf')"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-bold text-white bg-gray-900 hover:bg-gray-800 rounded-xl shadow-xs transition-all cursor-pointer">
                    <i data-lucide="file-down" class="w-3.5 h-3.5"></i>
                    <span>Export PDF</span>
                </button>
                <button type="button" id="btnArchiveGdrive" onclick="exportAndArchiveToGdrive()"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-bold text-white bg-gradient-to-r from-[#FF4D00] to-amber-500 hover:from-[#e04400] hover:to-[#d97706] rounded-xl shadow-xs transition-all cursor-pointer"
                    title="{{ $gdriveReady ? 'Generate PDF lalu langsung simpan ke folder Arsip Google Drive' : 'Google Drive belum terhubung' }}">
                    <i data-lucide="cloud-upload" class="w-3.5 h-3.5"></i>
                    <span>Simpan &amp; Arsipkan GDrive</span>
                </button>
            </div>
        </div>
    </div>

    {{-- ── 3-COLUMN STUDIO WORKSPACE ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-start">

        {{-- LEFT SIDEBAR: Shortcut Components & Assets (3 cols) --}}
        <div class="lg:col-span-3 space-y-3.5">

            {{-- 1. Upload Base Sertifikat --}}
            <div class="mac-card p-4 space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-orange-50 border border-orange-200 text-[#FF4D00] text-xs font-bold flex items-center justify-center">1</span>
                        <h2 class="text-xs font-extrabold text-gray-900 uppercase tracking-wider">Base Sertifikat</h2>
                    </div>
                    <span id="baseDimsBadge" class="hidden text-[10px] font-mono font-semibold text-gray-500 bg-gray-100 px-2 py-0.5 rounded-md"></span>
                </div>

                <label for="baseFileInput" id="baseDropZone"
                    class="flex flex-col items-center justify-center gap-1.5 p-3.5 border-2 border-dashed border-gray-200 hover:border-[#FF4D00] rounded-2xl bg-gray-50/70 hover:bg-orange-50/40 transition-all cursor-pointer text-center">
                    <div class="w-8 h-8 rounded-xl bg-white border border-gray-200 flex items-center justify-center text-[#FF4D00] shadow-2xs">
                        <i data-lucide="upload-cloud" class="w-4 h-4"></i>
                    </div>
                    <p id="baseFileNameLabel" class="text-xs font-bold text-gray-800 truncate max-w-full">Upload Base Sertifikat</p>
                    <p class="text-[10px] text-gray-400">PDF, PNG, JPG, JPEG (Resolusi Tinggi)</p>
                    <input type="file" id="baseFileInput" accept=".pdf,.png,.jpg,.jpeg" class="hidden" onchange="handleBaseFileUpload(this.files[0])">
                </label>

                {{-- PDF Page Switcher (if multi-page PDF) --}}
                <div id="pdfPageControl" class="hidden flex items-center justify-between bg-indigo-50/70 border border-indigo-200/70 rounded-xl px-3 py-1.5 text-xs">
                    <span class="font-semibold text-indigo-800">Halaman PDF:</span>
                    <div class="flex items-center gap-1.5">
                        <button type="button" onclick="changePdfPage(-1)" class="w-6 h-6 rounded-lg bg-white border border-indigo-200 text-indigo-700 font-bold hover:bg-indigo-100 cursor-pointer">-</button>
                        <span id="pdfPageIndicator" class="font-mono font-bold text-indigo-900">1 / 1</span>
                        <button type="button" onclick="changePdfPage(1)" class="w-6 h-6 rounded-lg bg-white border border-indigo-200 text-indigo-700 font-bold hover:bg-indigo-100 cursor-pointer">+</button>
                    </div>
                </div>
            </div>

            {{-- 2. Shortcut Komponen Cepat --}}
            <div class="mac-card p-4 space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-indigo-50 border border-indigo-200 text-indigo-600 text-xs font-bold flex items-center justify-center">2</span>
                        <h2 class="text-xs font-extrabold text-gray-900 uppercase tracking-wider">Shortcut Komponen</h2>
                    </div>
                </div>

                {{-- Smart Combo 1-Click --}}
                <button type="button" onclick="smartAutoCompose()"
                    class="shortcut-chip w-full flex items-center justify-between gap-2 px-3.5 py-2.5 rounded-2xl bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white text-xs font-bold shadow-xs cursor-pointer">
                    <span class="flex items-center gap-2">
                        <i data-lucide="wand-2" class="w-4 h-4"></i>
                        <span>Susun Otomatis Semua</span>
                    </span>
                    <span class="text-[10px] font-normal bg-white/20 px-2 py-0.5 rounded-full">1 Klik</span>
                </button>

                {{-- Nomor Sertifikat Shortcuts --}}
                <div class="p-3 rounded-2xl bg-gray-50/80 border border-gray-200/70 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-gray-700 flex items-center gap-1.5">
                            <i data-lucide="hash" class="w-3.5 h-3.5 text-[#FF4D00]"></i>
                            Nomor Surat Resmi
                        </span>
                        <button type="button" onclick="addNumberLayer('No. ' + LETTER_DATA.number)"
                            class="text-[11px] font-bold text-[#FF4D00] hover:underline cursor-pointer">
                            + Tempel
                        </button>
                    </div>

                    <div class="flex flex-wrap gap-1.5">
                        <button type="button" onclick="addOrReplaceNumberText('No. ' + LETTER_DATA.number)"
                            class="shortcut-chip px-2.5 py-1 rounded-lg bg-white border border-gray-200 hover:border-orange-300 text-[11px] font-semibold text-gray-700 cursor-pointer">
                            No. {{ Str::limit($letter->letter_number, 18) }}
                        </button>
                        <button type="button" onclick="addOrReplaceNumberText(LETTER_DATA.number)"
                            class="shortcut-chip px-2.5 py-1 rounded-lg bg-white border border-gray-200 hover:border-orange-300 text-[11px] font-semibold text-gray-700 cursor-pointer">
                            Tanpa "No."
                        </button>
                        <button type="button" onclick="snapNumberUnderTitle()"
                            class="shortcut-chip px-2.5 py-1 rounded-lg bg-orange-50 border border-orange-200 text-[10.5px] font-bold text-[#FF4D00] cursor-pointer"
                            title="Posisikan nomor tepat di tengah di bawah judul Sertifikat">
                            Posisi Bawah Judul
                        </button>
                    </div>
                </div>
            </div>

            {{-- 3. Shortcut Tanda Tangan (TTD) --}}
            <div class="mac-card p-4 space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-600 text-xs font-bold flex items-center justify-center">3</span>
                        <h2 class="text-xs font-extrabold text-gray-900 uppercase tracking-wider">Tanda Tangan</h2>
                    </div>
                    <button type="button" onclick="openQuickUploadModal('signature', {{ $letter->signatory_id ?? 'null' }}, '{{ addslashes($letter->signatory?->name ?? '') }}')"
                        class="text-[11px] font-bold text-emerald-700 hover:underline cursor-pointer">
                        + Upload TTD
                    </button>
                </div>

                @if ($letter->signatory)
                    <div class="p-3 rounded-2xl bg-emerald-50/50 border border-emerald-200/80 space-y-2">
                        <div class="flex items-center justify-between gap-2">
                            <div class="min-w-0">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700">Penandatangan Surat</span>
                                <p class="text-xs font-bold text-gray-900 truncate">{{ $letter->signatory->name }}</p>
                                <p class="text-[10.5px] text-gray-500 truncate">{{ $letter->signatory->position }}</p>
                            </div>
                        </div>

                        <div id="primarySigSlot">
                            @if ($letter->signatory->signature_path)
                                <button type="button"
                                    onclick="addImageLayer('signature', '{{ $letter->signatory->signature_url }}', '{{ addslashes($letter->signatory->name) }}', 100)"
                                    class="shortcut-chip w-full flex items-center gap-2.5 p-2 rounded-xl bg-white border border-emerald-200 hover:border-emerald-400 shadow-2xs cursor-pointer">
                                    <div class="w-14 h-9 checkerboard-mini rounded-lg flex items-center justify-center overflow-hidden p-1">
                                        <img src="{{ $letter->signatory->signature_url }}" alt="TTD" class="max-h-full max-w-full object-contain">
                                    </div>
                                    <span class="text-xs font-bold text-emerald-800 flex-1 text-left">Tempel TTD</span>
                                    <i data-lucide="plus-circle" class="w-4 h-4 text-emerald-600"></i>
                                </button>
                            @else
                                <button type="button"
                                    onclick="openQuickUploadModal('signature', {{ $letter->signatory->id }}, '{{ addslashes($letter->signatory->name) }}')"
                                    class="w-full flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl bg-white border border-dashed border-emerald-300 text-[11px] font-bold text-emerald-700 hover:bg-emerald-50 cursor-pointer">
                                    <i data-lucide="upload" class="w-3.5 h-3.5"></i>
                                    <span>Upload Spesimen TTD Pejabat Ini</span>
                                </button>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Other signatories with uploaded TTD --}}
                @php
                    $otherSigs = $signatories->filter(fn($s) => $s->id !== $letter->signatory_id && !empty($s->signature_path));
                @endphp
                @if ($otherSigs->count() > 0)
                    <div class="space-y-1.5 max-h-36 overflow-y-auto pr-1" id="otherSignaturesList">
                        @foreach ($otherSigs as $sig)
                            <button type="button"
                                onclick="addImageLayer('signature', '{{ $sig->signature_url }}', '{{ addslashes($sig->name) }}', 100)"
                                class="shortcut-chip w-full flex items-center gap-2 p-2 rounded-xl bg-gray-50 hover:bg-emerald-50 border border-gray-200/80 text-left cursor-pointer">
                                <img src="{{ $sig->signature_url }}" alt="{{ $sig->name }}" class="w-10 h-6 object-contain bg-white rounded border border-gray-100 p-0.5">
                                <div class="min-w-0 flex-1">
                                    <p class="text-[11px] font-bold text-gray-800 truncate">{{ $sig->name }}</p>
                                    <p class="text-[10px] text-gray-400 truncate">{{ $sig->position }}</p>
                                </div>
                                <i data-lucide="plus" class="w-3.5 h-3.5 text-gray-400"></i>
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- 4. Shortcut Stempel --}}
            <div class="mac-card p-4 space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-purple-50 border border-purple-200 text-purple-600 text-xs font-bold flex items-center justify-center">4</span>
                        <h2 class="text-xs font-extrabold text-gray-900 uppercase tracking-wider">Stempel</h2>
                    </div>
                    <button type="button" onclick="openQuickUploadModal('stamp')"
                        class="text-[11px] font-bold text-purple-700 hover:underline cursor-pointer">
                        + Upload Stempel
                    </button>
                </div>

                <div id="stampsGallery" class="grid grid-cols-2 gap-2">
                    @forelse ($stamps as $stamp)
                        <button type="button"
                            onclick="addImageLayer('stamp', '{{ $stamp->file_url }}', '{{ addslashes($stamp->name) }}', {{ $stamp->default_opacity }})"
                            class="shortcut-chip flex flex-col items-center gap-1.5 p-2.5 rounded-2xl bg-gray-50 hover:bg-purple-50/70 border border-gray-200/80 hover:border-purple-300 text-center cursor-pointer"
                            title="Klik untuk menempelkan {{ $stamp->name }}">
                            <div class="w-12 h-12 checkerboard-mini rounded-xl flex items-center justify-center p-1">
                                <img src="{{ $stamp->file_url }}" alt="{{ $stamp->name }}" class="max-h-full max-w-full object-contain" style="opacity: {{ $stamp->default_opacity / 100 }}">
                            </div>
                            <span class="text-[11px] font-bold text-gray-800 truncate w-full">{{ $stamp->name }}</span>
                        </button>
                    @empty
                        <div class="col-span-2 text-center py-3 px-2 rounded-2xl bg-gray-50 border border-dashed border-gray-200 text-[11px] text-gray-500 space-y-2">
                            <p>Belum ada master stempel aktif.</p>
                            <button type="button" onclick="openQuickUploadModal('stamp')"
                                class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-purple-600 text-white text-[10.5px] font-bold cursor-pointer">
                                <i data-lucide="plus" class="w-3 h-3"></i>
                                <span>Upload Stempel</span>
                            </button>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- CENTER PANEL: Interactive Canvas Stage (6 cols) --}}
        <div class="lg:col-span-6 space-y-3">
            {{-- Stage Control Bar --}}
            <div class="mac-card px-4 py-2.5 flex flex-wrap items-center justify-between gap-2 text-xs">
                <div class="flex items-center gap-1.5">
                    <button type="button" onclick="undoCanvas()" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold cursor-pointer" title="Undo (Ctrl+Z)">
                        <i data-lucide="undo-2" class="w-3.5 h-3.5"></i>
                        <span class="hidden sm:inline">Undo</span>
                    </button>
                    <button type="button" onclick="centerSelectedLayer()" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold cursor-pointer" title="Rata Tengah Horizontal (Ctrl+E)">
                        <i data-lucide="align-center-horizontal" class="w-3.5 h-3.5"></i>
                        <span class="hidden sm:inline">Center X</span>
                    </button>
                    <label class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-gray-50 border border-gray-200 text-gray-700 font-semibold cursor-pointer">
                        <input type="checkbox" id="snapToggle" checked class="rounded border-gray-300 text-indigo-600">
                        <span>Snap Guide</span>
                    </label>
                </div>

                <div class="flex items-center gap-1.5">
                    <button type="button" onclick="adjustZoom(-0.1)" class="w-7 h-7 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-700 font-bold cursor-pointer">-</button>
                    <button type="button" onclick="resetZoom()" id="zoomBadge" class="px-2.5 py-1 rounded-lg bg-gray-50 border border-gray-200 font-mono text-[11px] font-bold text-gray-700 cursor-pointer" title="Klik untuk Fit ke layar">Fit</button>
                    <button type="button" onclick="adjustZoom(0.1)" class="w-7 h-7 rounded-lg bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-700 font-bold cursor-pointer">+</button>
                </div>
            </div>

            {{-- Canvas Stage Container --}}
            <div class="mac-card p-3 sm:p-4 studio-stage-bg min-h-[520px] flex items-center justify-center overflow-auto relative" id="stageWrapper">
                {{-- Empty State Before Uploading Base --}}
                <div id="emptyStageState" class="text-center max-w-sm space-y-3 py-16">
                    <div class="w-16 h-16 rounded-3xl bg-white border border-gray-200 text-[#FF4D00] flex items-center justify-center mx-auto shadow-sm">
                        <i data-lucide="file-badge" class="w-8 h-8"></i>
                    </div>
                    <h3 class="text-base font-bold text-gray-900">Mulai dengan Upload Base Sertifikat</h3>
                    <p class="text-xs text-gray-500 leading-relaxed">
                        Unggah desain sertifikat polos dalam format <strong>PDF, PNG, JPG, atau JPEG</strong> di panel kiri, lalu klik komponen Nomor Surat, TTD, dan Stempel untuk mengatur posisinya.
                    </p>
                    <button type="button" onclick="document.getElementById('baseFileInput').click()"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-gradient-to-r from-[#FF4D00] to-amber-500 text-white text-xs font-bold shadow-xs cursor-pointer">
                        <i data-lucide="upload" class="w-4 h-4"></i>
                        <span>Pilih File Sertifikat</span>
                    </button>
                </div>

                {{-- Interactive Canvas --}}
                <canvas id="studioCanvas" class="hidden rounded-xl shadow-2xl bg-white touch-none select-none"></canvas>
            </div>

            {{-- Keyboard Shortcut Legend --}}
            <div class="flex flex-wrap items-center justify-center gap-x-4 gap-y-1 text-[11px] text-gray-400">
                <span><kbd class="px-1.5 py-0.5 bg-white border border-gray-200 rounded text-gray-600 font-mono">Panah</kbd> Geser 1px</span>
                <span><kbd class="px-1.5 py-0.5 bg-white border border-gray-200 rounded text-gray-600 font-mono">Shift+Panah</kbd> Geser 10px</span>
                <span><kbd class="px-1.5 py-0.5 bg-white border border-gray-200 rounded text-gray-600 font-mono">Del</kbd> Hapus Layer</span>
                <span><kbd class="px-1.5 py-0.5 bg-white border border-gray-200 rounded text-gray-600 font-mono">Ctrl+Z</kbd> Undo</span>
                <span><kbd class="px-1.5 py-0.5 bg-white border border-gray-200 rounded text-gray-600 font-mono">Ctrl+E</kbd> Rata Tengah</span>
            </div>
        </div>

        {{-- RIGHT PANEL: Layer Stack & Context Inspector (3 cols) --}}
        <div class="lg:col-span-3 space-y-3.5">

            {{-- Context Inspector --}}
            <div class="mac-card p-4 space-y-4">
                <div class="flex items-center justify-between pb-2.5 border-b border-gray-100">
                    <h2 class="text-xs font-extrabold text-gray-900 uppercase tracking-wider flex items-center gap-1.5">
                        <i data-lucide="sliders-horizontal" class="w-3.5 h-3.5 text-[#FF4D00]"></i>
                        <span>Properti Layer</span>
                    </h2>
                    <span id="selectedLayerTypeBadge" class="hidden px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200"></span>
                </div>

                {{-- No Layer Selected --}}
                <div id="inspectorEmpty" class="py-6 text-center space-y-2">
                    <i data-lucide="mouse-pointer-click" class="w-7 h-7 text-gray-300 mx-auto"></i>
                    <p class="text-xs text-gray-400">Klik elemen Nomor, TTD, atau Stempel di kanvas untuk mengatur ukuran, warna, transparansi, &amp; posisi.</p>
                </div>

                {{-- Text Inspector (Nomor Surat) --}}
                <div id="inspectorText" class="hidden space-y-3.5">
                    <div>
                        <label class="block text-[11px] font-bold text-gray-600 mb-1">Teks Nomor</label>
                        <input type="text" id="propTextValue" oninput="updateSelectedTextProp('text', this.value)"
                            class="w-full px-3 py-2 text-xs font-semibold border border-gray-200 rounded-xl focus:outline-none focus:border-[#FF4D00]">
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-600 mb-1">Font</label>
                            <select id="propFontFamily" onchange="updateSelectedTextProp('fontFamily', this.value)"
                                class="w-full px-2.5 py-2 text-xs border border-gray-200 rounded-xl bg-white focus:outline-none">
                                <option value="Times New Roman, Liberation Serif, serif">Times New Roman</option>
                                <option value="Georgia, serif">Georgia Serif</option>
                                <option value="'Plus Jakarta Sans', sans-serif">Plus Jakarta Sans</option>
                                <option value="Arial, sans-serif">Arial / Sans</option>
                                <option value="'Courier New', monospace">Courier Mono</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-600 mb-1">Ukuran (<span id="propFontSizeLabel">48</span>px)</label>
                            <input type="number" id="propFontSize" min="12" max="260" value="48" oninput="updateSelectedTextProp('fontSize', parseInt(this.value || 48))"
                                class="w-full px-2.5 py-2 text-xs font-mono border border-gray-200 rounded-xl focus:outline-none">
                        </div>
                    </div>

                    <input type="range" id="propFontSizeSlider" min="14" max="180" value="48"
                        oninput="document.getElementById('propFontSize').value=this.value; updateSelectedTextProp('fontSize', parseInt(this.value))"
                        class="w-full accent-[#FF4D00]">

                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-1">
                            <button type="button" id="btnPropBold" onclick="toggleSelectedTextStyle('bold')" class="px-2.5 py-1.5 text-xs font-bold border border-gray-200 rounded-lg cursor-pointer">B</button>
                            <button type="button" id="btnPropItalic" onclick="toggleSelectedTextStyle('italic')" class="px-2.5 py-1.5 text-xs italic font-serif border border-gray-200 rounded-lg cursor-pointer">I</button>
                        </div>
                        <div class="flex items-center gap-1">
                            <button type="button" onclick="updateSelectedTextProp('align', 'left')" class="p-1.5 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer" title="Rata Kiri"><i data-lucide="align-left" class="w-3.5 h-3.5"></i></button>
                            <button type="button" onclick="updateSelectedTextProp('align', 'center')" class="p-1.5 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer" title="Rata Tengah"><i data-lucide="align-center" class="w-3.5 h-3.5"></i></button>
                            <button type="button" onclick="updateSelectedTextProp('align', 'right')" class="p-1.5 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer" title="Rata Kanan"><i data-lucide="align-right" class="w-3.5 h-3.5"></i></button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-gray-600 mb-1.5">Warna Teks</label>
                        <div class="flex items-center gap-2">
                            <input type="color" id="propTextColor" value="#111827" oninput="updateSelectedTextProp('color', this.value)"
                                class="w-8 h-8 rounded-lg border border-gray-200 cursor-pointer p-0.5">
                            @foreach (['#111827', '#000000', '#1e3a8a', '#7c2d12', '#14532d'] as $hex)
                                <button type="button" onclick="setTextColorPreset('{{ $hex }}')"
                                    class="w-6 h-6 rounded-full border border-gray-300 cursor-pointer" style="background: {{ $hex }}"></button>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Image Inspector (TTD / Stamp) --}}
                <div id="inspectorImage" class="hidden space-y-3.5">
                    <div>
                        <div class="flex items-center justify-between text-[11px] font-bold text-gray-600 mb-1">
                            <span>Lebar Elemen</span>
                            <span id="propImgScaleLabel" class="font-mono text-gray-900">18%</span>
                        </div>
                        <input type="range" id="propImgScale" min="4" max="65" step="0.5" value="18"
                            oninput="updateSelectedImageScale(parseFloat(this.value))"
                            class="w-full accent-indigo-600">
                    </div>

                    <div>
                        <div class="flex items-center justify-between text-[11px] font-bold text-gray-600 mb-1">
                            <span>Rotasi</span>
                            <span id="propImgRotationLabel" class="font-mono text-gray-900">0°</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="range" id="propImgRotation" min="-45" max="45" step="1" value="0"
                                oninput="updateSelectedProp('rotation', parseFloat(this.value))"
                                class="flex-1 accent-indigo-600">
                            <button type="button" onclick="updateSelectedProp('rotation', 0)" class="px-2 py-0.5 text-[10px] font-bold bg-gray-100 rounded-md cursor-pointer">0°</button>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between text-[11px] font-bold text-gray-600 mb-1">
                            <span>Transparansi (Opacity)</span>
                            <span id="propImgOpacityLabel" class="font-mono text-gray-900">100%</span>
                        </div>
                        <input type="range" id="propImgOpacity" min="15" max="100" step="1" value="100"
                            oninput="updateSelectedProp('opacity', parseInt(this.value))"
                            class="w-full accent-purple-600">
                    </div>

                    {{-- Magic White Background Remover --}}
                    <div class="p-3 rounded-2xl bg-indigo-50/60 border border-indigo-200/70 space-y-2">
                        <label class="flex items-center justify-between gap-2 cursor-pointer">
                            <span class="text-[11px] font-bold text-indigo-950 flex items-center gap-1.5">
                                <i data-lucide="eraser" class="w-3.5 h-3.5 text-indigo-600"></i>
                                Hapus Background Putih
                            </span>
                            <input type="checkbox" id="propRemoveBg" onchange="toggleSelectedRemoveBg(this.checked)"
                                class="rounded border-indigo-300 text-indigo-600">
                        </label>
                        <div id="propThresholdWrap" class="hidden space-y-1">
                            <div class="flex justify-between text-[10px] text-indigo-800 font-semibold">
                                <span>Sensitivitas Putih</span>
                                <span id="propBgThresholdLabel">225</span>
                            </div>
                            <input type="range" id="propBgThreshold" min="175" max="250" value="225"
                                oninput="updateSelectedBgThreshold(parseInt(this.value))"
                                class="w-full accent-indigo-600">
                        </div>
                    </div>
                </div>

                {{-- Common Layer Actions --}}
                <div id="inspectorCommon" class="hidden pt-3 border-t border-gray-100 space-y-2">
                    <div class="grid grid-cols-3 gap-1.5">
                        <button type="button" onclick="bringSelectedForward()" class="px-2 py-1.5 text-[11px] font-semibold bg-gray-100 hover:bg-gray-200 rounded-xl cursor-pointer">Naik</button>
                        <button type="button" onclick="sendSelectedBackward()" class="px-2 py-1.5 text-[11px] font-semibold bg-gray-100 hover:bg-gray-200 rounded-xl cursor-pointer">Turun</button>
                        <button type="button" onclick="centerSelectedLayer()" class="px-2 py-1.5 text-[11px] font-semibold bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-xl cursor-pointer">Tengah X</button>
                    </div>
                    <button type="button" onclick="deleteSelectedLayer()"
                        class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-bold text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-200/70 rounded-xl cursor-pointer">
                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                        <span>Hapus Elemen Terpilih</span>
                    </button>
                </div>
            </div>

            {{-- Active Layers List --}}
            <div class="mac-card p-4 space-y-2.5">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-extrabold text-gray-900 uppercase tracking-wider">Daftar Layer</h3>
                    <span id="layerCountBadge" class="text-[11px] font-bold text-gray-400">0 elemen</span>
                </div>
                <div id="layerListContainer" class="space-y-1.5 max-h-48 overflow-y-auto text-xs text-gray-500">
                    <p class="text-[11px] text-gray-400 italic">Belum ada elemen ditambahkan.</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal: Quick Upload TTD / Stempel --}}
<div id="quickAssetModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/45 backdrop-blur-sm">
    <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full border border-slate-200 overflow-hidden">
        <form id="quickAssetForm" onsubmit="submitQuickAsset(event)">
            @csrf
            <input type="hidden" id="qa_type" name="asset_type" value="stamp">
            <input type="hidden" id="qa_signatory_id" name="signatory_id" value="">
            <div class="p-6 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <h3 id="qaModalTitle" class="text-base font-bold text-gray-900">Upload Aset</h3>
                    <button type="button" onclick="closeQuickUploadModal()" class="p-1.5 text-gray-400 hover:text-gray-600 rounded-xl cursor-pointer">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>

                <div class="space-y-3">
                    <div>
                        <label id="qaNameLabel" class="block text-xs font-bold text-gray-700 mb-1">Nama Aset</label>
                        <input type="text" id="qa_name" name="name" required
                            class="w-full px-3.5 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-[#FF4D00]">
                    </div>
                    <div id="qaOpacityWrap" class="hidden">
                        <label class="block text-xs font-bold text-gray-700 mb-1">Opacity Default Stempel (%)</label>
                        <input type="number" id="qa_opacity" name="default_opacity" min="10" max="100" value="88"
                            class="w-full px-3.5 py-2 text-sm font-mono border border-gray-200 rounded-xl">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">File Gambar (PNG/JPG/WEBP, maks. 5 MB)</label>
                        <input type="file" id="qa_file" name="asset_file" accept=".png,.jpg,.jpeg,.webp" required
                            class="block w-full text-xs text-gray-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-indigo-600 file:text-white cursor-pointer">
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-gray-100">
                    <button type="button" onclick="closeQuickUploadModal()" class="px-4 py-2 text-xs font-semibold bg-gray-100 rounded-xl cursor-pointer">Batal</button>
                    <button type="submit" id="qaSubmitBtn" class="px-5 py-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl cursor-pointer">Upload &amp; Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Modal: Save Preset --}}
<div id="presetModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/45 backdrop-blur-sm">
    <div class="bg-white rounded-3xl shadow-2xl max-w-sm w-full border border-slate-200 overflow-hidden">
        <form onsubmit="submitSavePreset(event)">
            <div class="p-6 space-y-4">
                <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                    <h3 class="text-sm font-bold text-gray-900">Simpan Preset Posisi</h3>
                    <button type="button" onclick="closePresetModal()" class="p-1 text-gray-400 hover:text-gray-600 cursor-pointer"><i data-lucide="x" class="w-4 h-4"></i></button>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Nama Preset</label>
                    <input type="text" id="presetNameInput" required placeholder="Misal: Sertifikat Dekan Landscape"
                        class="w-full px-3.5 py-2 text-xs border border-gray-200 rounded-xl focus:outline-none focus:border-indigo-500">
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closePresetModal()" class="px-4 py-2 text-xs font-semibold bg-gray-100 rounded-xl cursor-pointer">Batal</button>
                    <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl cursor-pointer">Simpan Preset</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
{{-- PDF.js & jsPDF CDNs --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<script>
    const LETTER_DATA = {
        id: {{ $letter->id }},
        number: @json($letter->letter_number),
        letterTypeId: {{ $letter->letter_type_id ?? 'null' }},
        primarySigName: @json($letter->signatory?->name ?? ''),
        primarySigUrl: @json($letter->signatory?->signature_url),
        archiveUrl: @json(route('letters.update-archive-link', $letter)),
        logExportUrl: @json(route('letters.certificate-studio.log-export', $letter)),
        quickAssetUrl: @json(route('certificate-studio.quick-asset')),
        savePresetUrl: @json(route('certificate-studio.presets.store')),
        defaultStampUrl: @json($stamps->first()?->file_url),
        defaultStampName: @json($stamps->first()?->name ?? 'Stempel FEB UNJ'),
        defaultStampOpacity: {{ $stamps->first()?->default_opacity ?? 88 }},
        gdriveReady: {{ $gdriveReady ? 'true' : 'false' }},
    };

    const PRESETS_DATA = @json($presets->keyBy('id'));

    if (typeof pdfjsLib !== 'undefined') {
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    }

    // ── Studio State ──
    const canvas = document.getElementById('studioCanvas');
    const ctx = canvas.getContext('2d');

    let baseImage = null;
    let baseWidth = 1754;
    let baseHeight = 1240;
    let pdfDoc = null;
    let currentPdfPage = 1;

    let displayScale = 1;
    let manualZoom = 1;
    let layers = [];
    let selectedLayerId = null;
    let historyStack = [];

    let dragState = null; // { mode: 'move'|'resize'|'rotate', layerId, handle, startX, startY, orig... }
    let snapGuides = { x: null, y: null };
    const imageCache = new Map();
    const cleanedCache = new Map();

    function showStudioToast(msg, isError = false) {
        const el = document.getElementById('studioToast');
        const txt = document.getElementById('studioToastText');
        const icon = document.getElementById('studioToastIcon');
        if (!el || !txt) return;

        txt.textContent = msg;
        if (icon) {
            icon.setAttribute('data-lucide', isError ? 'alert-circle' : 'check-circle-2');
            icon.className = isError ? 'w-4 h-4 text-rose-400' : 'w-4 h-4 text-emerald-400';
        }
        el.classList.remove('hidden');
        el.classList.add('flex');
        if (typeof lucide !== 'undefined') lucide.createIcons();

        clearTimeout(window.__studioToastTimer);
        window.__studioToastTimer = setTimeout(() => {
            el.classList.add('hidden');
            el.classList.remove('flex');
        }, 3200);
    }

    function pushHistory() {
        const snapshot = JSON.stringify(layers);
        if (historyStack.length === 0 || historyStack[historyStack.length - 1] !== snapshot) {
            historyStack.push(snapshot);
            if (historyStack.length > 30) historyStack.shift();
        }
    }

    function undoCanvas() {
        if (historyStack.length <= 1) return;
        historyStack.pop();
        layers = JSON.parse(historyStack[historyStack.length - 1]);
        if (!layers.find(l => l.id === selectedLayerId)) {
            selectedLayerId = layers[layers.length - 1]?.id || null;
        }
        preloadLayerImages().then(() => {
            renderCanvas();
            syncInspector();
            renderLayerList();
        });
    }

    // ── Base Upload (PDF or Image) ──
    async function handleBaseFileUpload(file) {
        if (!file) return;
        const nameLabel = document.getElementById('baseFileNameLabel');
        if (nameLabel) nameLabel.textContent = file.name;

        const ext = file.name.split('.').pop().toLowerCase();

        if (ext === 'pdf') {
            try {
                const buffer = await file.arrayBuffer();
                pdfDoc = await pdfjsLib.getDocument({ data: buffer }).promise;
                currentPdfPage = 1;
                await renderPdfPageToBase(currentPdfPage);

                const pageCtrl = document.getElementById('pdfPageControl');
                if (pdfDoc.numPages > 1) {
                    pageCtrl.classList.remove('hidden');
                    document.getElementById('pdfPageIndicator').textContent = `1 / ${pdfDoc.numPages}`;
                } else {
                    pageCtrl.classList.add('hidden');
                }
                showStudioToast(`Base PDF "${file.name}" dimuat dengan resolusi tinggi.`);
            } catch (err) {
                console.error(err);
                showStudioToast('Gagal membaca file PDF. Pastikan PDF tidak terenkripsi password.', true);
            }
        } else {
            pdfDoc = null;
            document.getElementById('pdfPageControl')?.classList.add('hidden');
            const reader = new FileReader();
            reader.onload = (e) => {
                const img = new Image();
                img.onload = () => {
                    baseImage = img;
                    baseWidth = img.naturalWidth || img.width;
                    baseHeight = img.naturalHeight || img.height;
                    activateStage();
                    showStudioToast(`Base gambar "${file.name}" siap diedit.`);
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    }

    async function renderPdfPageToBase(pageNum) {
        if (!pdfDoc) return;
        const page = await pdfDoc.getPage(pageNum);
        const viewport = page.getViewport({ scale: 3.0 }); // ~300 DPI crisp render
        const off = document.createElement('canvas');
        off.width = Math.round(viewport.width);
        off.height = Math.round(viewport.height);
        await page.render({ canvasContext: off.getContext('2d'), viewport }).promise;

        const img = new Image();
        img.onload = () => {
            baseImage = img;
            baseWidth = off.width;
            baseHeight = off.height;
            activateStage();
        };
        img.src = off.toDataURL('image/png');
    }

    async function changePdfPage(delta) {
        if (!pdfDoc) return;
        const next = currentPdfPage + delta;
        if (next < 1 || next > pdfDoc.numPages) return;
        currentPdfPage = next;
        document.getElementById('pdfPageIndicator').textContent = `${currentPdfPage} / ${pdfDoc.numPages}`;
        await renderPdfPageToBase(currentPdfPage);
    }

    function activateStage() {
        document.getElementById('emptyStageState')?.classList.add('hidden');
        canvas.classList.remove('hidden');

        const badge = document.getElementById('baseDimsBadge');
        if (badge) {
            badge.textContent = `${baseWidth}×${baseHeight}`;
            badge.classList.remove('hidden');
        }

        fitCanvasToWrapper();
        if (historyStack.length === 0) pushHistory();
        renderCanvas();
    }

    function fitCanvasToWrapper() {
        const wrapper = document.getElementById('stageWrapper');
        const availW = Math.max(320, (wrapper?.clientWidth || 720) - 36);
        const availH = Math.max(420, window.innerHeight * 0.68);
        const fitScale = Math.min(availW / baseWidth, availH / baseHeight, 1);
        displayScale = fitScale * manualZoom;

        canvas.width = baseWidth;
        canvas.height = baseHeight;
        canvas.style.width = Math.round(baseWidth * displayScale) + 'px';
        canvas.style.height = Math.round(baseHeight * displayScale) + 'px';
    }

    function adjustZoom(delta) {
        manualZoom = Math.max(0.4, Math.min(2.5, +(manualZoom + delta).toFixed(2)));
        document.getElementById('zoomBadge').textContent = Math.round(manualZoom * 100) + '%';
        if (baseImage) fitCanvasToWrapper();
    }

    function resetZoom() {
        manualZoom = 1;
        document.getElementById('zoomBadge').textContent = 'Fit';
        if (baseImage) fitCanvasToWrapper();
    }

    window.addEventListener('resize', () => {
        if (baseImage) fitCanvasToWrapper();
    });

    // ── Layer Management ──
    function uid() {
        return 'lyr_' + Date.now().toString(36) + '_' + Math.random().toString(36).slice(2, 6);
    }

    function ensureBaseReady() {
        if (!baseImage) {
            showStudioToast('Upload base sertifikat (PDF/PNG/JPG) terlebih dahulu!', true);
            return false;
        }
        return true;
    }

    function addNumberLayer(textValue = 'No. ' + LETTER_DATA.number, xPct = 0.5, yPct = 0.308) {
        if (!ensureBaseReady()) return;
        const fontSize = Math.max(22, Math.round(baseHeight * 0.025));
        const layer = {
            id: uid(),
            type: 'text',
            role: 'number',
            name: 'Nomor Sertifikat',
            text: textValue,
            x: baseWidth * xPct,
            y: baseHeight * yPct,
            fontSize: fontSize,
            fontFamily: 'Times New Roman, Liberation Serif, serif',
            bold: false,
            italic: false,
            color: '#111827',
            align: 'center',
            opacity: 100,
            rotation: 0,
        };
        layers.push(layer);
        selectedLayerId = layer.id;
        pushHistory();
        renderCanvas();
        syncInspector();
        renderLayerList();
    }

    function addOrReplaceNumberText(textValue) {
        const existing = layers.find(l => l.type === 'text' && l.role === 'number');
        if (existing) {
            existing.text = textValue;
            selectedLayerId = existing.id;
            pushHistory();
            renderCanvas();
            syncInspector();
            renderLayerList();
        } else {
            addNumberLayer(textValue);
        }
    }

    function snapNumberUnderTitle() {
        if (!ensureBaseReady()) return;
        const existing = layers.find(l => l.type === 'text' && l.role === 'number');
        if (!existing) {
            addNumberLayer('No. ' + LETTER_DATA.number, 0.5, 0.308);
            return;
        }
        existing.x = baseWidth * 0.5;
        existing.y = baseHeight * 0.308;
        existing.align = 'center';
        selectedLayerId = existing.id;
        pushHistory();
        renderCanvas();
        syncInspector();
    }

    async function loadCachedImage(url) {
        if (imageCache.has(url)) return imageCache.get(url);
        return new Promise((resolve, reject) => {
            const img = new Image();
            img.crossOrigin = 'anonymous';
            img.onload = () => {
                imageCache.set(url, img);
                resolve(img);
            };
            img.onerror = reject;
            img.src = url;
        });
    }

    async function preloadLayerImages() {
        const urls = [...new Set(layers.filter(l => l.type === 'image').map(l => l.url))];
        await Promise.all(urls.map(u => loadCachedImage(u).catch(() => null)));
    }

    async function addImageLayer(role, url, label, opacity = 100, customPct = null) {
        if (!ensureBaseReady()) return;
        if (!url) {
            showStudioToast('Gambar belum tersedia. Silakan upload terlebih dahulu.', true);
            return;
        }

        try {
            const img = await loadCachedImage(url);
            const aspect = (img.naturalHeight || 1) / (img.naturalWidth || 1);

            const defaultWidthPct = role === 'stamp' ? 0.165 : 0.21;
            const defaultXPct = role === 'stamp' ? 0.425 : 0.55;
            const defaultYPct = role === 'stamp' ? 0.74 : 0.745;

            const widthPx = baseWidth * (customPct?.wPct ?? defaultWidthPct);
            const heightPx = widthPx * aspect;

            const layer = {
                id: uid(),
                type: 'image',
                role: role,
                name: label || (role === 'stamp' ? 'Stempel' : 'Tanda Tangan'),
                url: url,
                x: baseWidth * (customPct?.xPct ?? defaultXPct),
                y: baseHeight * (customPct?.yPct ?? defaultYPct),
                width: widthPx,
                height: heightPx,
                aspect: aspect,
                rotation: 0,
                opacity: opacity,
                removeBg: false,
                bgThreshold: 225,
            };

            layers.push(layer);
            selectedLayerId = layer.id;
            pushHistory();
            renderCanvas();
            syncInspector();
            renderLayerList();
        } catch (err) {
            showStudioToast('Gagal memuat gambar aset.', true);
        }
    }

    async function smartAutoCompose() {
        if (!ensureBaseReady()) return;

        if (!layers.find(l => l.type === 'text' && l.role === 'number')) {
            addNumberLayer('No. ' + LETTER_DATA.number, 0.5, 0.308);
        }

        if (LETTER_DATA.primarySigUrl && !layers.find(l => l.role === 'signature')) {
            await addImageLayer('signature', LETTER_DATA.primarySigUrl, LETTER_DATA.primarySigName || 'TTD Pejabat', 100, {
                wPct: 0.21, xPct: 0.54, yPct: 0.745
            });
        }

        if (LETTER_DATA.defaultStampUrl && !layers.find(l => l.role === 'stamp')) {
            await addImageLayer('stamp', LETTER_DATA.defaultStampUrl, LETTER_DATA.defaultStampName, LETTER_DATA.defaultStampOpacity, {
                wPct: 0.165, xPct: 0.425, yPct: 0.74
            });
        }

        showStudioToast('Komponen Nomor, TTD & Stempel disusun ke posisi default!');
    }

    // ── Magic White Background Remover ──
    function getProcessedImage(layer) {
        const rawImg = imageCache.get(layer.url);
        if (!rawImg) return null;
        if (!layer.removeBg) return rawImg;

        const key = `${layer.url}|${layer.bgThreshold}`;
        if (cleanedCache.has(key)) return cleanedCache.get(key);

        const off = document.createElement('canvas');
        off.width = rawImg.naturalWidth || rawImg.width;
        off.height = rawImg.naturalHeight || rawImg.height;
        const octx = off.getContext('2d');
        octx.drawImage(rawImg, 0, 0);

        const imgData = octx.getImageData(0, 0, off.width, off.height);
        const d = imgData.data;
        const th = layer.bgThreshold || 225;

        for (let i = 0; i < d.length; i += 4) {
            const r = d[i], g = d[i + 1], b = d[i + 2];
            if (r >= th && g >= th && b >= th) {
                d[i + 3] = 0;
            }
        }
        octx.putImageData(imgData, 0, 0);
        cleanedCache.set(key, off);
        return off;
    }

    // ── Rendering Engine ──
    function getTextBoxMetrics(layer) {
        ctx.save();
        const stylePrefix = `${layer.italic ? 'italic ' : ''}${layer.bold ? 'bold ' : ''}`;
        ctx.font = `${stylePrefix}${layer.fontSize}px ${layer.fontFamily}`;
        const metrics = ctx.measureText(layer.text || '');
        ctx.restore();

        const w = Math.max(40, metrics.width + 16);
        const h = Math.max(24, layer.fontSize * 1.25);

        let left = layer.x - w / 2;
        if (layer.align === 'left') left = layer.x;
        if (layer.align === 'right') left = layer.x - w;

        return { left, top: layer.y - h / 2, width: w, height: h };
    }

    function getLayerBounds(layer) {
        if (layer.type === 'text') {
            const box = getTextBoxMetrics(layer);
            return {
                cx: box.left + box.width / 2,
                cy: box.top + box.height / 2,
                w: box.width,
                h: box.height,
            };
        }
        return {
            cx: layer.x,
            cy: layer.y,
            w: layer.width,
            h: layer.height,
        };
    }

    function renderCanvas(forExport = false) {
        if (!baseImage) return;
        ctx.clearRect(0, 0, baseWidth, baseHeight);
        ctx.drawImage(baseImage, 0, 0, baseWidth, baseHeight);

        for (const layer of layers) {
            ctx.save();
            ctx.globalAlpha = Math.max(0.05, Math.min(1, (layer.opacity ?? 100) / 100));

            if (layer.type === 'text') {
                const stylePrefix = `${layer.italic ? 'italic ' : ''}${layer.bold ? 'bold ' : ''}`;
                ctx.font = `${stylePrefix}${layer.fontSize}px ${layer.fontFamily}`;
                ctx.fillStyle = layer.color || '#111827';
                ctx.textAlign = layer.align || 'center';
                ctx.textBaseline = 'middle';
                ctx.translate(layer.x, layer.y);
                if (layer.rotation) ctx.rotate((layer.rotation * Math.PI) / 180);
                ctx.fillText(layer.text || '', 0, 0);
            } else if (layer.type === 'image') {
                const imgSource = getProcessedImage(layer);
                if (imgSource) {
                    ctx.translate(layer.x, layer.y);
                    if (layer.rotation) ctx.rotate((layer.rotation * Math.PI) / 180);
                    ctx.drawImage(imgSource, -layer.width / 2, -layer.height / 2, layer.width, layer.height);
                }
            }

            ctx.restore();
        }

        if (forExport) return;

        // Draw Snap Guides
        ctx.save();
        ctx.strokeStyle = '#ec4899';
        ctx.lineWidth = Math.max(1.5, 2 / displayScale);
        ctx.setLineDash([8 / displayScale, 6 / displayScale]);
        if (snapGuides.x !== null) {
            ctx.beginPath();
            ctx.moveTo(snapGuides.x, 0);
            ctx.lineTo(snapGuides.x, baseHeight);
            ctx.stroke();
        }
        if (snapGuides.y !== null) {
            ctx.beginPath();
            ctx.moveTo(0, snapGuides.y);
            ctx.lineTo(baseWidth, snapGuides.y);
            ctx.stroke();
        }
        ctx.restore();

        // Draw Selection Box & Handles
        const sel = layers.find(l => l.id === selectedLayerId);
        if (sel) {
            const b = getLayerBounds(sel);
            const handleR = Math.max(6, 7 / displayScale);

            ctx.save();
            ctx.translate(b.cx, b.cy);
            if (sel.rotation) ctx.rotate((sel.rotation * Math.PI) / 180);

            ctx.strokeStyle = '#2563eb';
            ctx.lineWidth = Math.max(1.5, 2 / displayScale);
            ctx.strokeRect(-b.w / 2, -b.h / 2, b.w, b.h);

            // Corner Resize Handles
            const corners = [
                [-b.w / 2, -b.h / 2],
                [b.w / 2, -b.h / 2],
                [b.w / 2, b.h / 2],
                [-b.w / 2, b.h / 2],
            ];
            for (const [hx, hy] of corners) {
                ctx.fillStyle = '#ffffff';
                ctx.beginPath();
                ctx.arc(hx, hy, handleR, 0, Math.PI * 2);
                ctx.fill();
                ctx.stroke();
            }

            // Top Rotate Handle for Images
            if (sel.type === 'image') {
                const rotY = -b.h / 2 - (26 / displayScale);
                ctx.beginPath();
                ctx.moveTo(0, -b.h / 2);
                ctx.lineTo(0, rotY);
                ctx.stroke();

                ctx.fillStyle = '#2563eb';
                ctx.beginPath();
                ctx.arc(0, rotY, handleR, 0, Math.PI * 2);
                ctx.fill();
            }
            ctx.restore();
        }
    }

    // ── Canvas Pointer Interaction ──
    function toCanvasPoint(evt) {
        const rect = canvas.getBoundingClientRect();
        const clientX = evt.touches ? evt.touches[0].clientX : evt.clientX;
        const clientY = evt.touches ? evt.touches[0].clientY : evt.clientY;
        return {
            x: (clientX - rect.left) * (canvas.width / rect.width),
            y: (clientY - rect.top) * (canvas.height / rect.height),
        };
    }

    function toLocalPoint(pt, cx, cy, deg) {
        const rad = (-deg * Math.PI) / 180;
        const dx = pt.x - cx;
        const dy = pt.y - cy;
        return {
            x: dx * Math.cos(rad) - dy * Math.sin(rad),
            y: dx * Math.sin(rad) + dy * Math.cos(rad),
        };
    }

    function hitTest(pt) {
        const hitPad = Math.max(12, 14 / displayScale);
        const sel = layers.find(l => l.id === selectedLayerId);

        if (sel) {
            const b = getLayerBounds(sel);
            const lp = toLocalPoint(pt, b.cx, b.cy, sel.rotation || 0);

            if (sel.type === 'image') {
                const rotY = -b.h / 2 - (26 / displayScale);
                if (Math.hypot(lp.x, lp.y - rotY) <= hitPad) {
                    return { layer: sel, mode: 'rotate' };
                }
            }

            const corners = [
                { name: 'nw', x: -b.w / 2, y: -b.h / 2 },
                { name: 'ne', x: b.w / 2, y: -b.h / 2 },
                { name: 'se', x: b.w / 2, y: b.h / 2 },
                { name: 'sw', x: -b.w / 2, y: b.h / 2 },
            ];
            for (const c of corners) {
                if (Math.hypot(lp.x - c.x, lp.y - c.y) <= hitPad) {
                    return { layer: sel, mode: 'resize', handle: c.name };
                }
            }
        }

        for (let i = layers.length - 1; i >= 0; i--) {
            const l = layers[i];
            const b = getLayerBounds(l);
            const lp = toLocalPoint(pt, b.cx, b.cy, l.rotation || 0);
            if (Math.abs(lp.x) <= b.w / 2 + 8 && Math.abs(lp.y) <= b.h / 2 + 8) {
                return { layer: l, mode: 'move' };
            }
        }
        return null;
    }

    function onPointerDown(evt) {
        if (!baseImage) return;
        const pt = toCanvasPoint(evt);
        const hit = hitTest(pt);

        if (!hit) {
            selectedLayerId = null;
            renderCanvas();
            syncInspector();
            renderLayerList();
            return;
        }

        evt.preventDefault();
        selectedLayerId = hit.layer.id;
        const b = getLayerBounds(hit.layer);
        dragState = {
            mode: hit.mode,
            layerId: hit.layer.id,
            startPt: pt,
            origX: hit.layer.x,
            origY: hit.layer.y,
            origW: hit.layer.width || 0,
            origH: hit.layer.height || 0,
            origFontSize: hit.layer.fontSize || 48,
            origDist: Math.max(10, Math.hypot(pt.x - b.cx, pt.y - b.cy)),
            cx: b.cx,
            cy: b.cy,
        };
        renderCanvas();
        syncInspector();
        renderLayerList();
    }

    function onPointerMove(evt) {
        if (!dragState) return;
        evt.preventDefault();
        const pt = toCanvasPoint(evt);
        const layer = layers.find(l => l.id === dragState.layerId);
        if (!layer) return;

        snapGuides = { x: null, y: null };

        if (dragState.mode === 'move') {
            let nx = dragState.origX + (pt.x - dragState.startPt.x);
            let ny = dragState.origY + (pt.y - dragState.startPt.y);

            if (document.getElementById('snapToggle')?.checked) {
                const tol = baseWidth * 0.012;
                if (Math.abs(nx - baseWidth / 2) < tol) {
                    nx = baseWidth / 2;
                    snapGuides.x = baseWidth / 2;
                }
                if (Math.abs(ny - baseHeight / 2) < tol) {
                    ny = baseHeight / 2;
                    snapGuides.y = baseHeight / 2;
                }
            }

            layer.x = nx;
            layer.y = ny;
        } else if (dragState.mode === 'resize') {
            const newDist = Math.max(15, Math.hypot(pt.x - dragState.cx, pt.y - dragState.cy));
            const ratio = newDist / dragState.origDist;
            if (layer.type === 'image') {
                layer.width = Math.max(30, dragState.origW * ratio);
                layer.height = layer.width * (layer.aspect || 1);
            } else {
                layer.fontSize = Math.max(14, Math.min(260, Math.round(dragState.origFontSize * ratio)));
            }
            syncInspector();
        } else if (dragState.mode === 'rotate') {
            const angle = Math.atan2(pt.y - dragState.cy, pt.x - dragState.cx) * (180 / Math.PI) + 90;
            layer.rotation = Math.round(angle);
            syncInspector();
        }

        renderCanvas();
    }

    function onPointerUp() {
        if (!dragState) return;
        dragState = null;
        snapGuides = { x: null, y: null };
        pushHistory();
        renderCanvas();
    }

    canvas.addEventListener('mousedown', onPointerDown);
    window.addEventListener('mousemove', onPointerMove);
    window.addEventListener('mouseup', onPointerUp);
    canvas.addEventListener('touchstart', onPointerDown, { passive: false });
    window.addEventListener('touchmove', onPointerMove, { passive: false });
    window.addEventListener('touchend', onPointerUp);

    // ── Inspector & Layer Controls ──
    function syncInspector() {
        const layer = layers.find(l => l.id === selectedLayerId);
        const empty = document.getElementById('inspectorEmpty');
        const txt = document.getElementById('inspectorText');
        const img = document.getElementById('inspectorImage');
        const common = document.getElementById('inspectorCommon');
        const badge = document.getElementById('selectedLayerTypeBadge');

        if (!layer) {
            empty.classList.remove('hidden');
            txt.classList.add('hidden');
            img.classList.add('hidden');
            common.classList.add('hidden');
            badge.classList.add('hidden');
            return;
        }

        empty.classList.add('hidden');
        common.classList.remove('hidden');
        badge.classList.remove('hidden');
        badge.textContent = layer.type === 'text' ? 'Nomor Teks' : (layer.role === 'stamp' ? 'Stempel' : 'TTD');

        if (layer.type === 'text') {
            txt.classList.remove('hidden');
            img.classList.add('hidden');
            document.getElementById('propTextValue').value = layer.text || '';
            document.getElementById('propFontFamily').value = layer.fontFamily;
            document.getElementById('propFontSize').value = layer.fontSize;
            document.getElementById('propFontSizeSlider').value = Math.min(180, layer.fontSize);
            document.getElementById('propFontSizeLabel').textContent = layer.fontSize;
            document.getElementById('propTextColor').value = layer.color || '#111827';
            document.getElementById('btnPropBold').classList.toggle('bg-gray-900', !!layer.bold);
            document.getElementById('btnPropBold').classList.toggle('text-white', !!layer.bold);
            document.getElementById('btnPropItalic').classList.toggle('bg-gray-900', !!layer.italic);
            document.getElementById('btnPropItalic').classList.toggle('text-white', !!layer.italic);
        } else {
            txt.classList.add('hidden');
            img.classList.remove('hidden');
            const pct = +((layer.width / baseWidth) * 100).toFixed(1);
            document.getElementById('propImgScale').value = pct;
            document.getElementById('propImgScaleLabel').textContent = pct + '%';
            document.getElementById('propImgRotation').value = layer.rotation || 0;
            document.getElementById('propImgRotationLabel').textContent = (layer.rotation || 0) + '°';
            document.getElementById('propImgOpacity').value = layer.opacity ?? 100;
            document.getElementById('propImgOpacityLabel').textContent = (layer.opacity ?? 100) + '%';
            document.getElementById('propRemoveBg').checked = !!layer.removeBg;
            document.getElementById('propThresholdWrap').classList.toggle('hidden', !layer.removeBg);
            document.getElementById('propBgThreshold').value = layer.bgThreshold || 225;
            document.getElementById('propBgThresholdLabel').textContent = layer.bgThreshold || 225;
        }
    }

    function updateSelectedTextProp(key, val) {
        const layer = layers.find(l => l.id === selectedLayerId);
        if (!layer || layer.type !== 'text') return;
        layer[key] = val;
        if (key === 'fontSize') {
            document.getElementById('propFontSizeLabel').textContent = val;
        }
        renderCanvas();
        renderLayerList();
    }

    function toggleSelectedTextStyle(styleKey) {
        const layer = layers.find(l => l.id === selectedLayerId);
        if (!layer || layer.type !== 'text') return;
        layer[styleKey] = !layer[styleKey];
        pushHistory();
        syncInspector();
        renderCanvas();
    }

    function setTextColorPreset(hex) {
        document.getElementById('propTextColor').value = hex;
        updateSelectedTextProp('color', hex);
        pushHistory();
    }

    function updateSelectedImageScale(pct) {
        const layer = layers.find(l => l.id === selectedLayerId);
        if (!layer || layer.type !== 'image') return;
        layer.width = baseWidth * (pct / 100);
        layer.height = layer.width * (layer.aspect || 1);
        document.getElementById('propImgScaleLabel').textContent = pct + '%';
        renderCanvas();
    }

    function updateSelectedProp(key, val) {
        const layer = layers.find(l => l.id === selectedLayerId);
        if (!layer) return;
        layer[key] = val;
        if (key === 'rotation') {
            document.getElementById('propImgRotation').value = val;
            document.getElementById('propImgRotationLabel').textContent = val + '°';
        }
        if (key === 'opacity') {
            document.getElementById('propImgOpacityLabel').textContent = val + '%';
        }
        renderCanvas();
    }

    function toggleSelectedRemoveBg(checked) {
        const layer = layers.find(l => l.id === selectedLayerId);
        if (!layer || layer.type !== 'image') return;
        layer.removeBg = checked;
        document.getElementById('propThresholdWrap').classList.toggle('hidden', !checked);
        pushHistory();
        renderCanvas();
    }

    function updateSelectedBgThreshold(val) {
        const layer = layers.find(l => l.id === selectedLayerId);
        if (!layer || layer.type !== 'image') return;
        layer.bgThreshold = val;
        document.getElementById('propBgThresholdLabel').textContent = val;
        renderCanvas();
    }

    function centerSelectedLayer() {
        const layer = layers.find(l => l.id === selectedLayerId);
        if (!layer) return;
        layer.x = baseWidth / 2;
        if (layer.type === 'text') layer.align = 'center';
        pushHistory();
        renderCanvas();
        showStudioToast('Elemen diratakan ke tengah horizontal.');
    }

    function bringSelectedForward() {
        const idx = layers.findIndex(l => l.id === selectedLayerId);
        if (idx === -1 || idx >= layers.length - 1) return;
        [layers[idx], layers[idx + 1]] = [layers[idx + 1], layers[idx]];
        pushHistory();
        renderCanvas();
        renderLayerList();
    }

    function sendSelectedBackward() {
        const idx = layers.findIndex(l => l.id === selectedLayerId);
        if (idx <= 0) return;
        [layers[idx], layers[idx - 1]] = [layers[idx - 1], layers[idx]];
        pushHistory();
        renderCanvas();
        renderLayerList();
    }

    function deleteSelectedLayer() {
        if (!selectedLayerId) return;
        layers = layers.filter(l => l.id !== selectedLayerId);
        selectedLayerId = layers[layers.length - 1]?.id || null;
        pushHistory();
        renderCanvas();
        syncInspector();
        renderLayerList();
    }

    function renderLayerList() {
        const container = document.getElementById('layerListContainer');
        const countBadge = document.getElementById('layerCountBadge');
        if (countBadge) countBadge.textContent = `${layers.length} elemen`;
        if (!container) return;

        if (layers.length === 0) {
            container.innerHTML = '<p class="text-[11px] text-gray-400 italic">Belum ada elemen ditambahkan.</p>';
            return;
        }

        container.innerHTML = [...layers].reverse().map(l => {
            const active = l.id === selectedLayerId;
            const title = l.type === 'text' ? (l.text || 'Teks') : l.name;
            const badge = l.type === 'text' ? 'No' : (l.role === 'stamp' ? 'Cap' : 'TTD');
            return `
                <button type="button" onclick="selectLayerById('${l.id}')"
                    class="w-full flex items-center justify-between gap-2 px-2.5 py-2 rounded-xl border text-left transition-all cursor-pointer ${active ? 'bg-blue-50 border-blue-300 text-blue-900 font-bold' : 'bg-gray-50 border-gray-200/70 text-gray-700 hover:bg-gray-100'}">
                    <span class="truncate text-[11px]">${title}</span>
                    <span class="px-1.5 py-0.5 rounded bg-white border border-gray-200 text-[9.5px] font-mono font-bold text-gray-500 flex-shrink-0">${badge}</span>
                </button>
            `;
        }).join('');
    }

    function selectLayerById(id) {
        selectedLayerId = id;
        renderCanvas();
        syncInspector();
        renderLayerList();
    }

    // Keyboard Shortcuts
    document.addEventListener('keydown', (e) => {
        if (['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement?.tagName)) return;
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'z') {
            e.preventDefault();
            undoCanvas();
            return;
        }
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'e') {
            e.preventDefault();
            centerSelectedLayer();
            return;
        }
        if (!selectedLayerId) return;
        const layer = layers.find(l => l.id === selectedLayerId);
        if (!layer) return;

        const step = e.shiftKey ? 10 : 1;
        if (e.key === 'ArrowLeft') { e.preventDefault(); layer.x -= step; renderCanvas(); }
        else if (e.key === 'ArrowRight') { e.preventDefault(); layer.x += step; renderCanvas(); }
        else if (e.key === 'ArrowUp') { e.preventDefault(); layer.y -= step; renderCanvas(); }
        else if (e.key === 'ArrowDown') { e.preventDefault(); layer.y += step; renderCanvas(); }
        else if (e.key === 'Delete' || e.key === 'Backspace') { e.preventDefault(); deleteSelectedLayer(); }
    });

    // ── Presets ──
    function openPresetModal() {
        if (!layers.length) {
            showStudioToast('Tambahkan minimal 1 elemen sebelum menyimpan preset.', true);
            return;
        }
        document.getElementById('presetModal').classList.remove('hidden');
        document.getElementById('presetNameInput').focus();
    }
    function closePresetModal() {
        document.getElementById('presetModal').classList.add('hidden');
    }

    function serializeLayersAsPreset() {
        return layers.map(l => ({
            type: l.type,
            role: l.role,
            name: l.name,
            xPct: l.x / baseWidth,
            yPct: l.y / baseHeight,
            fontSizePct: l.fontSize ? l.fontSize / baseHeight : null,
            fontFamily: l.fontFamily || null,
            bold: !!l.bold,
            italic: !!l.italic,
            color: l.color || null,
            align: l.align || 'center',
            widthPct: l.width ? l.width / baseWidth : null,
            rotation: l.rotation || 0,
            opacity: l.opacity ?? 100,
            removeBg: !!l.removeBg,
            bgThreshold: l.bgThreshold || 225,
            url: l.url || null,
            usePrefixNo: l.type === 'text' && (l.text || '').startsWith('No.'),
        }));
    }

    async function submitSavePreset(e) {
        e.preventDefault();
        const name = document.getElementById('presetNameInput').value.trim();
        if (!name) return;

        const resp = await fetch(LETTER_DATA.savePresetUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                name,
                letter_type_id: LETTER_DATA.letterTypeId,
                config: { items: serializeLayersAsPreset() },
            }),
        });
        const data = await resp.json();
        if (data.success && data.preset) {
            PRESETS_DATA[data.preset.id] = data.preset;
            const sel = document.getElementById('presetSelect');
            const opt = new Option(data.preset.name, data.preset.id, true, true);
            sel.add(opt);
            closePresetModal();
            showStudioToast(data.message);
        } else {
            showStudioToast(data.message || 'Gagal menyimpan preset.', true);
        }
    }

    async function applySelectedPreset() {
        if (!ensureBaseReady()) return;
        const id = document.getElementById('presetSelect').value;
        const preset = PRESETS_DATA[id];
        if (!preset || !preset.config?.items) {
            showStudioToast('Pilih preset posisi terlebih dahulu.', true);
            return;
        }

        const newLayers = [];
        for (const item of preset.config.items) {
            if (item.type === 'text') {
                newLayers.push({
                    id: uid(),
                    type: 'text',
                    role: item.role || 'number',
                    name: item.name || 'Nomor Sertifikat',
                    text: item.usePrefixNo ? ('No. ' + LETTER_DATA.number) : LETTER_DATA.number,
                    x: baseWidth * (item.xPct ?? 0.5),
                    y: baseHeight * (item.yPct ?? 0.31),
                    fontSize: Math.max(18, Math.round(baseHeight * (item.fontSizePct ?? 0.025))),
                    fontFamily: item.fontFamily || 'Times New Roman, Liberation Serif, serif',
                    bold: !!item.bold,
                    italic: !!item.italic,
                    color: item.color || '#111827',
                    align: item.align || 'center',
                    opacity: item.opacity ?? 100,
                    rotation: item.rotation || 0,
                });
            } else if (item.type === 'image') {
                let targetUrl = item.url;
                if (item.role === 'signature' && LETTER_DATA.primarySigUrl) {
                    targetUrl = LETTER_DATA.primarySigUrl;
                }
                if (!targetUrl) continue;
                try {
                    const img = await loadCachedImage(targetUrl);
                    const aspect = (img.naturalHeight || 1) / (img.naturalWidth || 1);
                    const w = baseWidth * (item.widthPct ?? 0.18);
                    newLayers.push({
                        id: uid(),
                        type: 'image',
                        role: item.role,
                        name: item.name || 'Aset',
                        url: targetUrl,
                        x: baseWidth * (item.xPct ?? 0.5),
                        y: baseHeight * (item.yPct ?? 0.74),
                        width: w,
                        height: w * aspect,
                        aspect,
                        rotation: item.rotation || 0,
                        opacity: item.opacity ?? 100,
                        removeBg: !!item.removeBg,
                        bgThreshold: item.bgThreshold || 225,
                    });
                } catch {}
            }
        }

        layers = newLayers;
        selectedLayerId = layers[0]?.id || null;
        pushHistory();
        renderCanvas();
        syncInspector();
        renderLayerList();
        showStudioToast(`Preset "${preset.name}" diterapkan.`);
    }

    async function deleteSelectedPreset() {
        const sel = document.getElementById('presetSelect');
        const id = sel.value;
        if (!id || !confirm('Hapus preset posisi yang dipilih?')) return;

        const resp = await fetch(`/certificate-studio/presets/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        });
        const data = await resp.json();
        if (data.success) {
            delete PRESETS_DATA[id];
            sel.remove(sel.selectedIndex);
            showStudioToast(data.message);
        } else {
            showStudioToast(data.message || 'Gagal menghapus preset.', true);
        }
    }

    // ── Quick Asset Upload Modal ──
    function openQuickUploadModal(type, signatoryId = null, signatoryName = '') {
        document.getElementById('qa_type').value = type;
        document.getElementById('qa_signatory_id').value = signatoryId || '';
        document.getElementById('qa_file').value = '';
        document.getElementById('qaModalTitle').textContent = type === 'signature' ? 'Upload TTD Digital' : 'Upload Stempel Sertifikat';
        document.getElementById('qaNameLabel').textContent = type === 'signature' ? 'Nama Pejabat / Label TTD' : 'Nama Stempel';
        document.getElementById('qa_name').value = signatoryName || '';
        document.getElementById('qaOpacityWrap').classList.toggle('hidden', type !== 'stamp');
        document.getElementById('quickAssetModal').classList.remove('hidden');
    }

    function closeQuickUploadModal() {
        document.getElementById('quickAssetModal').classList.add('hidden');
    }

    async function submitQuickAsset(e) {
        e.preventDefault();
        const btn = document.getElementById('qaSubmitBtn');
        btn.disabled = true;
        btn.textContent = 'Mengunggah...';

        try {
            const fd = new FormData(document.getElementById('quickAssetForm'));
            const resp = await fetch(LETTER_DATA.quickAssetUrl, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: fd,
            });
            const data = await resp.json();
            if (data.success && data.asset) {
                if (data.asset.type === 'signature' && data.asset.signatory_id == {{ $letter->signatory_id ?? 'null' }}) {
                    LETTER_DATA.primarySigUrl = data.asset.url;
                }
                closeQuickUploadModal();
                showStudioToast(data.message);
                if (baseImage) {
                    await addImageLayer(data.asset.type, data.asset.url, data.asset.name, data.asset.default_opacity || 100);
                } else {
                    setTimeout(() => window.location.reload(), 700);
                }
            } else {
                showStudioToast(data.message || 'Upload gagal.', true);
            }
        } catch (err) {
            showStudioToast('Terjadi kesalahan saat mengunggah aset.', true);
        } finally {
            btn.disabled = false;
            btn.textContent = 'Upload & Simpan';
        }
    }

    // ── Export & Archive ──
    function safeFilename(ext) {
        const cleanNum = (LETTER_DATA.number || 'Sertifikat').replace(/[^A-Za-z0-9\-]+/g, '-');
        return `Sertifikat_${cleanNum}.${ext}`;
    }

    function buildPdfBlob() {
        renderCanvas(true);
        const imgData = canvas.toDataURL('image/jpeg', 0.96);
        const orientation = baseWidth >= baseHeight ? 'landscape' : 'portrait';
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF({
            orientation,
            unit: 'px',
            format: [baseWidth, baseHeight],
            hotfixes: ['px_scaling'],
        });
        pdf.addImage(imgData, 'JPEG', 0, 0, baseWidth, baseHeight);
        renderCanvas(false);
        return pdf;
    }

    async function exportCertificate(format) {
        if (!ensureBaseReady()) return;

        if (format === 'pdf') {
            const pdf = buildPdfBlob();
            pdf.save(safeFilename('pdf'));
        } else {
            renderCanvas(true);
            const mime = format === 'jpg' ? 'image/jpeg' : 'image/png';
            const dataUrl = canvas.toDataURL(mime, 0.96);
            renderCanvas(false);

            const link = document.createElement('a');
            link.download = safeFilename(format);
            link.href = dataUrl;
            link.click();
        }

        fetch(LETTER_DATA.logExportUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ format }),
        }).catch(() => {});

        showStudioToast(`Sertifikat berhasil di-export (${format.toUpperCase()})!`);
    }

    async function exportAndArchiveToGdrive() {
        if (!ensureBaseReady()) return;
        if (!LETTER_DATA.gdriveReady) {
            showStudioToast('Google Drive belum terhubung. Hubungi Admin untuk menghubungkan di Pengaturan Sistem.', true);
            return;
        }

        const btn = document.getElementById('btnArchiveGdrive');
        const origHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="inline-block animate-spin">⏳</span><span>Mengarsipkan...</span>';

        try {
            const pdf = buildPdfBlob();
            const blob = pdf.output('blob');
            const fd = new FormData();
            fd.append('archive_file', blob, safeFilename('pdf'));

            const resp = await fetch(LETTER_DATA.archiveUrl, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: fd,
            });
            const data = await resp.json();
            if (data.success) {
                showStudioToast(data.message || 'Sertifikat berhasil disimpan & diarsipkan ke Google Drive!');
            } else {
                showStudioToast(data.message || 'Gagal mengarsipkan ke Google Drive.', true);
            }
        } catch (err) {
            showStudioToast('Terjadi kesalahan jaringan saat mengarsipkan sertifikat.', true);
        } finally {
            btn.disabled = false;
            btn.innerHTML = origHtml;
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        if (typeof lucide !== 'undefined') lucide.createIcons();

        const drop = document.getElementById('baseDropZone');
        if (drop) {
            ['dragenter', 'dragover'].forEach(e => drop.addEventListener(e, ev => {
                ev.preventDefault();
                drop.classList.add('border-[#FF4D00]', 'bg-orange-50/60');
            }));
            ['dragleave', 'drop'].forEach(e => drop.addEventListener(e, ev => {
                ev.preventDefault();
                drop.classList.remove('border-[#FF4D00]', 'bg-orange-50/60');
            }));
            drop.addEventListener('drop', ev => {
                if (ev.dataTransfer?.files?.[0]) {
                    handleBaseFileUpload(ev.dataTransfer.files[0]);
                }
            });
        }
    });
</script>
@endpush
