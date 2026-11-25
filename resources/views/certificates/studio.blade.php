@extends('layouts.app')

@section('title', ($studioMode === 'letter' ? 'Studio Surat' : 'Studio Sertifikat') . ' - ' . $letter->letter_number)
@section('disable_layout_alerts', true)

@push('styles')
    {{-- Google Fonts Collection for Certificates --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Alex+Brush&family=Cinzel:wght@400;600;700&family=EB+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,700&family=Great+Vibes&family=JetBrains+Mono:wght@400;600;700&family=Lora:ital,wght@0,400;0,600;0,700;1,400&family=Merriweather:ital,wght@0,400;0,700;1,400&family=Montserrat:ital,wght@0,400;0,600;0,700;1,400&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400;1,700&family=Poppins:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">

    <style>
        .studio-stage-bg {
            background-color: #EBEEF3;
            background-image: radial-gradient(#CBD5E1 0.85px, transparent 0.85px);
            background-size: 20px 20px;
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
        .studio-scroll::-webkit-scrollbar,
        .filmstrip-scroll::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }
        .studio-scroll::-webkit-scrollbar-track,
        .filmstrip-scroll::-webkit-scrollbar-track {
            background: transparent;
        }
        .studio-scroll::-webkit-scrollbar-thumb,
        .filmstrip-scroll::-webkit-scrollbar-thumb {
            background: #CBD5E1;
            border-radius: 9999px;
        }
        .studio-scroll::-webkit-scrollbar-thumb:hover,
        .filmstrip-scroll::-webkit-scrollbar-thumb:hover {
            background: #94A3B8;
        }
        .shortcut-chip {
            transition: all 0.15s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .shortcut-chip:hover {
            transform: translateY(-1px);
        }
        .shortcut-chip:active {
            transform: scale(0.97);
        }
        .rail-btn {
            color: #64748B;
            transition: all 0.15s ease;
        }
        .rail-btn:hover {
            color: #0F172A;
            background-color: #F1F5F9;
        }
        .rail-btn.active {
            color: #FF4D00;
            background-color: #FFF7ED;
            box-shadow: inset 0 0 0 1px rgba(255, 77, 0, 0.18);
        }
        .studio-toggle-pill:has(input:checked) {
            background-color: #EEF2FF;
            color: #4338CA;
            border-color: #C7D2FE;
        }
    </style>
@endpush

@section('content')
@php
    $formattedLetterDate = $letter->letter_date
        ? $letter->letter_date->locale('id')->translatedFormat('d F Y')
        : now()->locale('id')->translatedFormat('d F Y');
    $otherSigs = $signatories->filter(fn($s) => $s->id !== $letter->signatory_id && !empty($s->signature_path));
@endphp
<div class="-m-4 sm:-m-6 lg:-m-8 lg:h-[calc(100vh-4rem)] min-h-[640px] flex flex-col bg-[#EBEEF3] lg:overflow-hidden relative">
    {{-- Floating Toast --}}
    <div id="studioToast" class="fixed top-5 left-1/2 -translate-x-1/2 z-[9999] hidden items-center gap-2.5 px-4 py-2.5 rounded-2xl bg-slate-900/95 text-white text-xs font-semibold shadow-2xl backdrop-blur-md">
        <i id="studioToastIcon" data-lucide="check-circle-2" class="w-4 h-4 text-emerald-400"></i>
        <span id="studioToastText"></span>
    </div>

    {{-- ── 1. UNIFIED SLIM TOP STUDIO BAR ── --}}
    <header class="h-14 bg-white border-b border-gray-200/80 px-3 sm:px-4 flex items-center justify-between gap-2 z-20 flex-shrink-0">
        {{-- Left: Navigation, Title & Status --}}
        <div class="flex items-center gap-2.5 min-w-0">
            <a href="{{ route('letters.show', $letter) }}"
                class="w-8 h-8 rounded-lg hover:bg-gray-100 text-gray-600 hover:text-gray-900 flex items-center justify-center transition-colors flex-shrink-0"
                title="Kembali ke Detail Surat">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
            </a>
            <div class="w-px h-5 bg-gray-200 flex-shrink-0"></div>
            <div class="flex items-center gap-2 min-w-0">
                <div class="w-7 h-7 rounded-lg {{ $studioMode === 'letter' ? 'bg-gradient-to-br from-emerald-600 to-teal-600' : 'bg-gradient-to-br from-[#FF4D00] to-amber-500' }} text-white flex items-center justify-center flex-shrink-0">
                    <i data-lucide="{{ $studioMode === 'letter' ? 'file-pen-line' : 'award' }}" class="w-4 h-4"></i>
                </div>
                <span class="hidden md:inline text-sm font-extrabold text-gray-900 tracking-tight whitespace-nowrap">
                    {{ $studioMode === 'letter' ? 'Studio Surat' : 'Studio Sertifikat' }}
                </span>
                @if ($canCert && $canLetter)
                    <div class="hidden sm:inline-flex items-center p-0.5 rounded-lg bg-gray-100 border border-gray-200/80 text-[10.5px] font-bold">
                        <a href="{{ route('letters.certificate-studio', ['letter' => $letter, 'mode' => 'certificate']) }}"
                            class="px-2 py-0.5 rounded-md transition-colors {{ $studioMode === 'certificate' ? 'bg-white text-[#FF4D00] shadow-2xs' : 'text-gray-500 hover:text-gray-800' }}">
                            Sertifikat
                        </a>
                        <a href="{{ route('letters.certificate-studio', ['letter' => $letter, 'mode' => 'letter']) }}"
                            class="px-2 py-0.5 rounded-md transition-colors {{ $studioMode === 'letter' ? 'bg-white text-emerald-700 shadow-2xs' : 'text-gray-500 hover:text-gray-800' }}">
                            Surat / Kop
                        </a>
                    </div>
                @endif
                <span id="topQueueBadge" class="hidden px-2 py-0.5 rounded-md text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200/80 whitespace-nowrap"></span>
            </div>
        </div>

        {{-- Center: Canvas Tools --}}
        <div class="hidden xl:flex items-center gap-1 bg-gray-50/90 border border-gray-200/80 rounded-xl px-1.5 py-1">
            <button type="button" id="btnUndo" onclick="undoCanvas()" disabled
                class="w-7 h-7 rounded-lg hover:bg-white disabled:opacity-35 text-gray-700 flex items-center justify-center transition-colors cursor-pointer" title="Undo (Ctrl+Z)">
                <i data-lucide="undo-2" class="w-3.5 h-3.5"></i>
            </button>
            <button type="button" id="btnRedo" onclick="redoCanvas()" disabled
                class="w-7 h-7 rounded-lg hover:bg-white disabled:opacity-35 text-gray-700 flex items-center justify-center transition-colors cursor-pointer" title="Redo (Ctrl+Y)">
                <i data-lucide="redo-2" class="w-3.5 h-3.5"></i>
            </button>

            <div class="w-px h-4 bg-gray-200 mx-0.5"></div>

            <button type="button" onclick="adjustZoom(-0.1)" class="w-7 h-7 rounded-lg hover:bg-white flex items-center justify-center text-gray-700 text-xs font-bold cursor-pointer" title="Perkecil">-</button>
            <button type="button" onclick="resetZoom()" id="zoomBadge" class="px-2 h-7 rounded-lg hover:bg-white font-mono text-[11px] font-bold text-gray-700 cursor-pointer" title="Fit ke layar">Fit</button>
            <button type="button" onclick="adjustZoom(0.1)" class="w-7 h-7 rounded-lg hover:bg-white flex items-center justify-center text-gray-700 text-xs font-bold cursor-pointer" title="Perbesar">+</button>

            <div class="w-px h-4 bg-gray-200 mx-0.5"></div>

            <label class="studio-toggle-pill inline-flex items-center gap-1 px-2 h-7 rounded-lg border border-transparent text-[11px] font-semibold text-gray-600 hover:bg-white cursor-pointer select-none" title="Panduan rata tengah otomatis">
                <input type="checkbox" id="snapToggle" checked class="sr-only">
                <i data-lucide="magnet" class="w-3 h-3"></i>
                <span>Snap</span>
            </label>
            <label class="studio-toggle-pill inline-flex items-center gap-1 px-2 h-7 rounded-lg border border-transparent text-[11px] font-semibold text-gray-600 hover:bg-white cursor-pointer select-none" title="Tampilkan garis grid 3x3 & margin aman">
                <input type="checkbox" id="gridToggle" onchange="renderCanvas()" class="sr-only">
                <i data-lucide="grid-3x3" class="w-3 h-3"></i>
                <span>Grid</span>
            </label>

            <div class="w-px h-4 bg-gray-200 mx-0.5"></div>

            <button type="button" onclick="openFullscreenPreview()"
                class="inline-flex items-center gap-1.5 px-2.5 h-7 rounded-lg bg-white hover:bg-indigo-50 text-gray-700 hover:text-indigo-700 border border-gray-200/80 text-[11px] font-bold transition-colors cursor-pointer"
                title="Pratinjau bersih layar penuh">
                <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                <span>Pratinjau</span>
            </button>

            {{-- Keyboard Shortcut Help Popover --}}
            <div id="shortcutPopoverWrap" class="relative">
                <button type="button" onclick="toggleShortcutPopover()"
                    class="w-7 h-7 rounded-lg hover:bg-white text-gray-500 hover:text-gray-800 flex items-center justify-center transition-colors cursor-pointer"
                    title="Pintasan Keyboard">
                    <i data-lucide="help-circle" class="w-3.5 h-3.5"></i>
                </button>
                <div id="shortcutPopoverMenu" class="hidden absolute right-0 mt-2 w-60 bg-white rounded-xl border border-gray-200 shadow-xl p-3 z-50 space-y-2 text-[11px]">
                    <div class="font-bold text-gray-900 pb-1.5 border-b border-gray-100">Pintasan Keyboard</div>
                    <div class="flex justify-between text-gray-600"><span>Geser 1px / 10px</span><kbd class="font-mono bg-gray-100 px-1.5 py-0.5 rounded">Panah / Shift</kbd></div>
                    <div class="flex justify-between text-gray-600"><span>Tengah Horizontal</span><kbd class="font-mono bg-gray-100 px-1.5 py-0.5 rounded">Ctrl+E</kbd></div>
                    <div class="flex justify-between text-gray-600"><span>Duplikat Layer</span><kbd class="font-mono bg-gray-100 px-1.5 py-0.5 rounded">Ctrl+D</kbd></div>
                    <div class="flex justify-between text-gray-600"><span>Undo / Redo</span><kbd class="font-mono bg-gray-100 px-1.5 py-0.5 rounded">Ctrl+Z / Y</kbd></div>
                    <div class="flex justify-between text-gray-600"><span>Ganti Halaman</span><kbd class="font-mono bg-gray-100 px-1.5 py-0.5 rounded">PgUp / PgDn</kbd></div>
                </div>
            </div>
        </div>

        {{-- Right: Preset Popover, Unified Export & GDrive Save --}}
        <div class="flex items-center gap-1.5 sm:gap-2 flex-shrink-0">
            {{-- Mobile Preview Quick Trigger --}}
            <button type="button" onclick="openFullscreenPreview()"
                class="xl:hidden w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 flex items-center justify-center cursor-pointer"
                title="Pratinjau">
                <i data-lucide="eye" class="w-4 h-4"></i>
            </button>

            {{-- Preset Popover --}}
            <div id="presetPopoverWrap" class="relative">
                <button type="button" onclick="togglePresetMenu()"
                    class="inline-flex items-center gap-1.5 px-2.5 sm:px-3 h-9 text-xs font-bold text-gray-700 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-xl transition-colors cursor-pointer">
                    <i data-lucide="bookmark" class="w-3.5 h-3.5 text-indigo-600"></i>
                    <span class="hidden sm:inline">Preset</span>
                    <i data-lucide="chevron-down" class="w-3 h-3 text-gray-400"></i>
                </button>
                <div id="presetPopoverMenu" class="hidden absolute right-0 mt-2 w-64 bg-white rounded-2xl border border-gray-200 shadow-2xl p-3 z-50 space-y-2.5">
                    <div class="text-[10px] font-extrabold uppercase tracking-wider text-gray-400">Preset Tata Letak</div>
                    <select id="presetSelect" class="w-full px-2.5 py-2 text-xs font-semibold text-gray-700 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-indigo-500">
                        <option value="">-- Pilih Preset --</option>
                        @foreach ($presets as $preset)
                            <option value="{{ $preset->id }}">{{ $preset->name }}</option>
                        @endforeach
                    </select>
                    <div class="grid grid-cols-2 gap-1.5">
                        <button type="button" onclick="closePresetMenu(); applySelectedPreset()"
                            class="px-2.5 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200/80 text-[11px] font-bold transition-colors cursor-pointer">
                            Terapkan
                        </button>
                        <button type="button" onclick="closePresetMenu(); openPresetModal()"
                            class="px-2.5 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-[11px] font-bold transition-colors cursor-pointer">
                            + Simpan Baru
                        </button>
                    </div>
                    <button type="button" onclick="deleteSelectedPreset()"
                        class="w-full inline-flex items-center justify-center gap-1 px-2.5 py-1.5 rounded-xl text-rose-600 hover:bg-rose-50 text-[11px] font-semibold transition-colors cursor-pointer">
                        <i data-lucide="trash-2" class="w-3 h-3"></i>
                        <span>Hapus Preset Terpilih</span>
                    </button>
                </div>
            </div>

            {{-- Unified Export Popover --}}
            <div id="exportMenuWrap" class="relative">
                <button type="button" onclick="toggleExportMenu()"
                    class="inline-flex items-center gap-1.5 px-3 h-9 text-xs font-bold text-white bg-gray-900 hover:bg-gray-800 rounded-xl shadow-2xs transition-colors cursor-pointer">
                    <i data-lucide="download" class="w-3.5 h-3.5"></i>
                    <span id="exportBtnLabel">Export</span>
                    <i data-lucide="chevron-down" class="w-3 h-3 text-gray-400"></i>
                </button>
                <div id="exportPopoverMenu" class="hidden absolute right-0 mt-2 w-60 bg-white rounded-2xl border border-gray-200 shadow-2xl p-2 z-50 space-y-1">
                    {{-- Single Mode Section --}}
                    <div id="exportSingleSection" class="space-y-1">
                        <div class="px-2.5 py-1 text-[10px] font-extrabold uppercase tracking-wider text-gray-400">Download Sertifikat</div>
                        <button type="button" onclick="closeExportMenu(); exportSingleSlide('pdf')"
                            class="w-full flex items-center justify-between px-3 py-2 text-xs font-bold text-gray-800 hover:bg-orange-50 hover:text-[#FF4D00] rounded-xl transition-colors cursor-pointer">
                            <span class="flex items-center gap-2"><i data-lucide="file-down" class="w-4 h-4 text-[#FF4D00]"></i><span>Dokumen PDF</span></span>
                            <span class="text-[10px] font-mono bg-gray-100 px-1.5 py-0.5 rounded">PDF</span>
                        </button>
                        <button type="button" onclick="closeExportMenu(); exportSingleSlide('png')"
                            class="w-full flex items-center justify-between px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 rounded-xl transition-colors cursor-pointer">
                            <span class="flex items-center gap-2"><i data-lucide="image" class="w-4 h-4 text-emerald-600"></i><span>Gambar PNG</span></span>
                            <span class="text-[10px] font-mono bg-gray-100 px-1.5 py-0.5 rounded">PNG</span>
                        </button>
                        <button type="button" onclick="closeExportMenu(); exportSingleSlide('jpg')"
                            class="w-full flex items-center justify-between px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-amber-50 hover:text-amber-700 rounded-xl transition-colors cursor-pointer">
                            <span class="flex items-center gap-2"><i data-lucide="file-image" class="w-4 h-4 text-amber-600"></i><span>Gambar JPG</span></span>
                            <span class="text-[10px] font-mono bg-gray-100 px-1.5 py-0.5 rounded">JPG</span>
                        </button>
                    </div>

                    {{-- Batch Mode Section --}}
                    <div id="exportBatchSection" class="hidden space-y-1">
                        <div class="px-2.5 py-1 text-[10px] font-extrabold uppercase tracking-wider text-gray-400">Semua Halaman (<span class="batchCountLabel">0</span>)</div>
                        <button type="button" onclick="closeExportMenu(); exportBatchPdf()"
                            class="w-full flex items-center justify-between px-3 py-2 text-xs font-bold text-gray-800 hover:bg-orange-50 hover:text-[#FF4D00] rounded-xl transition-colors cursor-pointer text-left">
                            <span class="flex items-center gap-2"><i data-lucide="files" class="w-4 h-4 text-[#FF4D00]"></i><span>PDF Gabungan</span></span>
                            <span class="text-[10px] font-mono bg-gray-100 px-1.5 py-0.5 rounded">1 PDF</span>
                        </button>
                        <button type="button" onclick="closeExportMenu(); exportBatchZip('pdf')"
                            class="w-full flex items-center justify-between px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 rounded-xl transition-colors cursor-pointer text-left">
                            <span class="flex items-center gap-2"><i data-lucide="folder-archive" class="w-4 h-4 text-indigo-600"></i><span>ZIP — PDF Terpisah</span></span>
                            <span class="text-[10px] font-mono bg-gray-100 px-1.5 py-0.5 rounded">ZIP</span>
                        </button>
                        <button type="button" onclick="closeExportMenu(); exportBatchZip('png')"
                            class="w-full flex items-center justify-between px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 rounded-xl transition-colors cursor-pointer text-left">
                            <span class="flex items-center gap-2"><i data-lucide="images" class="w-4 h-4 text-emerald-600"></i><span>ZIP — PNG Terpisah</span></span>
                            <span class="text-[10px] font-mono bg-gray-100 px-1.5 py-0.5 rounded">ZIP</span>
                        </button>
                        <button type="button" onclick="closeExportMenu(); exportBatchZip('jpg')"
                            class="w-full flex items-center justify-between px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-amber-50 hover:text-amber-700 rounded-xl transition-colors cursor-pointer text-left">
                            <span class="flex items-center gap-2"><i data-lucide="file-image" class="w-4 h-4 text-amber-600"></i><span>ZIP — JPG Terpisah</span></span>
                            <span class="text-[10px] font-mono bg-gray-100 px-1.5 py-0.5 rounded">ZIP</span>
                        </button>
                        <div class="border-t border-gray-100 my-1"></div>
                        <div class="px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-gray-400">Halaman Aktif</div>
                        <div class="grid grid-cols-3 gap-1 px-1 pb-1">
                            <button type="button" onclick="closeExportMenu(); exportSingleSlide('pdf')" class="px-2 py-1.5 text-[11px] font-bold bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg cursor-pointer">PDF</button>
                            <button type="button" onclick="closeExportMenu(); exportSingleSlide('png')" class="px-2 py-1.5 text-[11px] font-bold bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg cursor-pointer">PNG</button>
                            <button type="button" onclick="closeExportMenu(); exportSingleSlide('jpg')" class="px-2 py-1.5 text-[11px] font-bold bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg cursor-pointer">JPG</button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- GDrive Primary CTA --}}
            <button type="button" id="btnArchiveGdrive" onclick="exportAndArchiveToGdrive()"
                class="inline-flex items-center gap-1.5 px-3 sm:px-3.5 h-9 text-xs font-bold text-white bg-gradient-to-r from-[#FF4D00] to-amber-500 hover:from-[#e04400] hover:to-[#d97706] rounded-xl shadow-xs transition-all cursor-pointer"
                title="{{ $gdriveReady ? 'Simpan PDF ke Arsip Google Drive' : 'Google Drive belum terhubung' }}">
                <i data-lucide="cloud-upload" class="w-3.5 h-3.5"></i>
                <span id="btnArchiveGdriveLabel" class="hidden sm:inline">Simpan GDrive</span>
            </button>
        </div>
    </header>

    {{-- ── 2. CANVA / PHOTOSHOP STUDIO WORKSPACE ── --}}
    <div class="flex-1 flex flex-col lg:flex-row min-h-0 overflow-hidden">

        {{-- A. LEFT VERTICAL ICON RAIL (64px) --}}
        <aside class="lg:w-[64px] bg-white border-b lg:border-b-0 lg:border-r border-gray-200/80 flex lg:flex-col items-center justify-between px-2 py-1.5 lg:py-3 z-20 flex-shrink-0">
            <nav class="flex lg:flex-col items-center gap-1.5">
                <button type="button" data-tool-tab="upload" onclick="switchLeftTool('upload')"
                    class="rail-btn active w-12 h-12 rounded-xl flex flex-col items-center justify-center gap-0.5 cursor-pointer" title="{{ $studioMode === 'letter' ? 'Template Kertas Kop & Crop Surat' : 'Unggah Base Sertifikat' }}">
                    <i data-lucide="{{ $studioMode === 'letter' ? 'layout-template' : 'upload-cloud' }}" class="w-4 h-4"></i>
                    <span class="text-[9.5px] font-bold">{{ $studioMode === 'letter' ? 'Template' : 'Unggah' }}</span>
                </button>
                @if ($studioMode === 'letter')
                    <button type="button" data-tool-tab="letterhead" onclick="switchLeftTool('letterhead')"
                        class="rail-btn w-12 h-12 rounded-xl flex flex-col items-center justify-center gap-0.5 cursor-pointer" title="Kotak Penutup (Whiteout) &amp; Crop Ulang">
                        <i data-lucide="eraser" class="w-4 h-4"></i>
                        <span class="text-[9.5px] font-bold">Penutup</span>
                    </button>
                @endif
                <button type="button" data-tool-tab="text" onclick="switchLeftTool('text')"
                    class="rail-btn w-12 h-12 rounded-xl flex flex-col items-center justify-center gap-0.5 cursor-pointer" title="Nomor &amp; Teks">
                    <i data-lucide="type" class="w-4 h-4"></i>
                    <span class="text-[9.5px] font-bold">Teks</span>
                </button>
                <button type="button" data-tool-tab="signature" onclick="switchLeftTool('signature')"
                    class="rail-btn w-12 h-12 rounded-xl flex flex-col items-center justify-center gap-0.5 cursor-pointer" title="Tanda Tangan">
                    <i data-lucide="pen-tool" class="w-4 h-4"></i>
                    <span class="text-[9.5px] font-bold">TTD</span>
                </button>
                <button type="button" data-tool-tab="stamp" onclick="switchLeftTool('stamp')"
                    class="rail-btn w-12 h-12 rounded-xl flex flex-col items-center justify-center gap-0.5 cursor-pointer" title="Stempel">
                    <i data-lucide="stamp" class="w-4 h-4"></i>
                    <span class="text-[9.5px] font-bold">Stempel</span>
                </button>
                <button type="button" data-tool-tab="layers" onclick="switchLeftTool('layers')"
                    class="rail-btn relative w-12 h-12 rounded-xl flex flex-col items-center justify-center gap-0.5 cursor-pointer" title="Urutan &amp; Kunci Layer">
                    <i data-lucide="layers" class="w-4 h-4"></i>
                    <span class="text-[9.5px] font-bold">Layer</span>
                    <span id="railLayerCount" class="hidden absolute top-1 right-1 min-w-[15px] h-[15px] px-1 rounded-full bg-indigo-600 text-white text-[9px] font-bold flex items-center justify-center">0</span>
                </button>
            </nav>

            <button type="button" id="btnFocusMode" onclick="toggleFocusMode()"
                class="hidden lg:flex w-10 h-10 rounded-xl hover:bg-gray-100 text-gray-500 hover:text-gray-900 items-center justify-center transition-colors cursor-pointer"
                title="Mode Fokus (Sembunyikan Panel)">
                <i id="focusModeIcon" data-lucide="maximize-2" class="w-4 h-4"></i>
            </button>
        </aside>

        {{-- B. LEFT COLLAPSIBLE TOOL DRAWER (260px) --}}
        <aside id="leftToolDrawer" class="w-full lg:w-[260px] bg-white border-b lg:border-b-0 lg:border-r border-gray-200/80 flex flex-col max-h-72 lg:max-h-none min-h-0 z-10 flex-shrink-0">
            <div class="h-11 px-3.5 border-b border-gray-100 flex items-center justify-between flex-shrink-0">
                <span id="leftDrawerTitle" class="text-xs font-extrabold text-gray-900 uppercase tracking-wider">
                    {{ $studioMode === 'letter' ? 'Template Kop & Surat' : 'Unggah Sertifikat' }}
                </span>
                <button type="button" onclick="collapseLeftDrawer()" class="hidden lg:flex w-7 h-7 rounded-lg hover:bg-gray-100 text-gray-400 hover:text-gray-700 items-center justify-center cursor-pointer" title="Tutup Panel">
                    <i data-lucide="panel-left-close" class="w-3.5 h-3.5"></i>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto studio-scroll p-3.5 space-y-4">
                {{-- Tool Panel: Upload / Template Base --}}
                <div id="toolPanel-upload" class="space-y-3.5">
                    @if ($studioMode === 'letter')
                        {{-- 1. Pilih Template Kertas Kop sebagai Base --}}
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-extrabold uppercase tracking-wider text-gray-500">1. Template Kop (Base)</span>
                                <button type="button" onclick="openQuickUploadModal('letterhead')"
                                    class="text-[11px] font-bold text-emerald-700 hover:underline cursor-pointer">+ Upload Kop</button>
                            </div>
                            <div id="letterheadsGallery" class="space-y-2">
                                @forelse ($letterheads as $lh)
                                    <button type="button"
                                        onclick="selectLetterheadAsBase('{{ $lh->file_url }}', '{{ addslashes($lh->name) }}')"
                                        class="shortcut-chip w-full flex flex-col gap-1.5 p-2 rounded-xl bg-gray-50 hover:bg-emerald-50/70 border border-gray-200/80 hover:border-emerald-300 text-left cursor-pointer"
                                        title="Jadikan {{ $lh->name }} sebagai Base kanvas A4">
                                        <div class="w-full h-16 bg-white rounded-lg border border-gray-100 flex items-center justify-center overflow-hidden p-1">
                                            <img src="{{ $lh->file_url }}" alt="{{ $lh->name }}" class="max-h-full max-w-full object-contain">
                                        </div>
                                        <div class="flex items-center justify-between gap-1 px-0.5">
                                            <span class="text-[11px] font-bold text-gray-800 truncate">{{ $lh->name }}</span>
                                            <span class="text-[9.5px] font-bold text-emerald-700 bg-emerald-100 px-1.5 py-0.5 rounded flex-shrink-0">Pakai</span>
                                        </div>
                                    </button>
                                @empty
                                    <div class="text-center py-4 px-2.5 rounded-2xl bg-gray-50 border border-dashed border-gray-200 text-[11px] text-gray-500 space-y-2">
                                        <p>Belum ada Template Kop FEB UNJ.</p>
                                        <button type="button" onclick="openQuickUploadModal('letterhead')"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full bg-emerald-600 text-white text-[10.5px] font-bold cursor-pointer">
                                            <i data-lucide="upload" class="w-3 h-3"></i>
                                            <span>Upload PDF / PNG Kop</span>
                                        </button>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        {{-- 2. Upload & Crop Surat dari Universitas --}}
                        <div class="pt-3 border-t border-gray-100 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-extrabold uppercase tracking-wider text-gray-500">2. Surat Universitas</span>
                                <span id="baseDimsBadge" class="hidden text-[10px] font-mono font-semibold text-gray-500 bg-gray-100 px-1.5 py-0.5 rounded"></span>
                            </div>

                            <label for="univLetterFileInput" id="baseDropZone"
                                class="flex flex-col items-center justify-center gap-1.5 p-3.5 border-2 border-dashed border-emerald-200 hover:border-emerald-500 rounded-2xl bg-emerald-50/40 hover:bg-emerald-50/80 transition-all cursor-pointer text-center">
                                <div class="w-8 h-8 rounded-xl bg-white border border-emerald-200 flex items-center justify-center text-emerald-600 shadow-2xs">
                                    <i data-lucide="crop" class="w-4 h-4"></i>
                                </div>
                                <p class="text-xs font-bold text-gray-900">Upload &amp; Crop Isi Surat</p>
                                <p class="text-[10px] text-gray-500">Pilih PDF, Word (.doc/.docx/.rtf), atau gambar surat Universitas (maks. 5 MB untuk Word)</p>
                                <input type="file" id="univLetterFileInput" accept=".pdf,.doc,.docx,.rtf,.png,.jpg,.jpeg" class="hidden" onchange="openUnivCropModalFromFile(this.files[0])">
                            </label>

                            <p id="baseFileNameLabel" class="hidden text-[10.5px] text-gray-500 truncate"></p>
                            <input type="file" id="baseFileInput" multiple accept=".pdf,.png,.jpg,.jpeg" class="hidden" onchange="handleBaseFilesUpload(this.files, false)">
                            <input type="file" id="appendFileInput" multiple accept=".pdf,.png,.jpg,.jpeg" class="hidden" onchange="handleBaseFilesUpload(this.files, true)">

                            <div id="baseQueueActions" class="hidden flex items-center gap-2 pt-1">
                                <button type="button" onclick="document.getElementById('appendFileInput').click()"
                                    class="flex-1 inline-flex items-center justify-center gap-1 px-2.5 py-1.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 text-[11px] font-bold transition-colors cursor-pointer" title="Tambahkan halaman lampiran PDF/gambar">
                                    <i data-lucide="file-plus" class="w-3.5 h-3.5"></i>
                                    <span>+ Lampiran</span>
                                </button>
                                <button type="button" onclick="resetAllSlides()"
                                    class="inline-flex items-center justify-center gap-1 px-2.5 py-1.5 rounded-xl bg-gray-100 hover:bg-rose-50 text-gray-600 hover:text-rose-600 text-[11px] font-semibold transition-colors cursor-pointer"
                                    title="Reset semua halaman">
                                    <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                                </button>
                            </div>
                        </div>
                    @else
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold text-gray-600">File Desain</span>
                            <span id="baseDimsBadge" class="hidden text-[10px] font-mono font-semibold text-gray-500 bg-gray-100 px-1.5 py-0.5 rounded"></span>
                        </div>

                        <label for="baseFileInput" id="baseDropZone"
                            class="flex flex-col items-center justify-center gap-1.5 p-4 border-2 border-dashed border-gray-200 hover:border-[#FF4D00] rounded-2xl bg-gray-50/70 hover:bg-orange-50/40 transition-all cursor-pointer text-center">
                            <div class="w-9 h-9 rounded-xl bg-white border border-gray-200 flex items-center justify-center text-[#FF4D00] shadow-2xs">
                                <i data-lucide="upload-cloud" class="w-4 h-4"></i>
                            </div>
                            <p id="baseFileNameLabel" class="text-xs font-bold text-gray-800 truncate max-w-full">Pilih atau Tarik File</p>
                            <p class="text-[10px] text-gray-400">PDF multi-halaman, PNG, atau JPG</p>
                            <input type="file" id="baseFileInput" multiple accept=".pdf,.png,.jpg,.jpeg" class="hidden" onchange="handleBaseFilesUpload(this.files, false)">
                        </label>

                        <input type="file" id="appendFileInput" multiple accept=".pdf,.png,.jpg,.jpeg" class="hidden" onchange="handleBaseFilesUpload(this.files, true)">

                        <div id="baseQueueActions" class="hidden flex items-center gap-2">
                            <button type="button" onclick="document.getElementById('appendFileInput').click()"
                                class="flex-1 inline-flex items-center justify-center gap-1 px-3 py-2 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200/80 text-[11px] font-bold transition-colors cursor-pointer">
                                <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                                <span>Tambah File</span>
                            </button>
                            <button type="button" onclick="resetAllSlides()"
                                class="inline-flex items-center justify-center gap-1 px-2.5 py-2 rounded-xl bg-gray-100 hover:bg-rose-50 text-gray-600 hover:text-rose-600 text-[11px] font-semibold transition-colors cursor-pointer"
                                title="Reset semua sertifikat">
                                <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                            </button>
                        </div>
                    @endif
                </div>

                @if ($studioMode === 'letter')
                {{-- Tool Panel: Penutup (Whiteout) --}}
                <div id="toolPanel-letterhead" class="hidden space-y-4">
                    <button type="button" onclick="document.getElementById('univLetterFileInput').click()"
                        class="shortcut-chip w-full flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs cursor-pointer">
                        <i data-lucide="crop" class="w-4 h-4"></i>
                        <span>Upload &amp; Crop Surat Univ</span>
                    </button>

                    <div class="pt-3 border-t border-gray-100 space-y-2">
                        <span class="block text-[11px] font-extrabold uppercase tracking-wider text-gray-500">Alat Penutup (Whiteout)</span>
                        <div class="grid grid-cols-1 gap-1.5">
                            <button type="button" onclick="addWhiteoutLayer(0.395, 0.175, 0.35, 0.024, 'Penutup Nomor')"
                                class="shortcut-chip flex items-center gap-2 px-3 py-2 rounded-xl bg-gray-50 hover:bg-slate-100 border border-gray-200/80 text-xs font-semibold text-gray-700 cursor-pointer">
                                <i data-lucide="eraser" class="w-3.5 h-3.5 text-amber-600"></i>
                                <span>+ Kotak Penutup Bebas</span>
                            </button>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Tool Panel: Text & Quick Compose --}}
                <div id="toolPanel-text" class="hidden space-y-4">
                    <button type="button" onclick="smartAutoCompose()"
                        class="shortcut-chip w-full flex items-center justify-between gap-2 px-3.5 py-2.5 rounded-xl {{ $studioMode === 'letter' ? 'bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700' : 'bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700' }} text-white text-xs font-bold shadow-xs cursor-pointer">
                        <span class="flex items-center gap-2"><i data-lucide="wand-2" class="w-4 h-4"></i><span>{{ $studioMode === 'letter' ? 'Susun Kop & Nomor' : 'Susun Otomatis' }}</span></span>
                        <span class="text-[10px] bg-white/20 px-2 py-0.5 rounded-full">1 Klik</span>
                    </button>

                    <div class="pt-3 border-t border-gray-100 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-extrabold uppercase tracking-wider text-gray-500">Nomor Surat</span>
                            <button type="button" onclick="{{ $studioMode === 'letter' ? 'addNumberWithWhiteout(false)' : "addTextLayer('No. ' + LETTER_DATA.number, 0.5, 0.308, 'number', 'Nomor Sertifikat')" }}"
                                class="text-[11px] font-bold text-[#FF4D00] hover:underline cursor-pointer">+ Tempel</button>
                        </div>
                        <div class="grid grid-cols-1 gap-1.5">
                            @if ($studioMode === 'letter')
                                <button type="button" onclick="addNumberWithWhiteout(false)"
                                    class="shortcut-chip w-full text-left px-3 py-2 rounded-xl bg-emerald-50/70 hover:bg-emerald-100/70 border border-emerald-200 text-xs font-bold text-emerald-900 truncate cursor-pointer"
                                    title="Menempelkan nomor surat dengan latar putih otomatis untuk menutup nomor lama">
                                    {{ $letter->letter_number }} (Latar Putih)
                                </button>
                                <button type="button" onclick="addNumberWithWhiteout(true)"
                                    class="shortcut-chip w-full text-left px-3 py-2 rounded-xl bg-gray-50 hover:bg-orange-50/70 border border-gray-200/80 text-xs font-semibold text-gray-800 truncate cursor-pointer">
                                    Nomor: {{ $letter->letter_number }}
                                </button>
                            @else
                                <button type="button" onclick="addOrReplaceNumberText('No. ' + LETTER_DATA.number)"
                                    class="shortcut-chip w-full text-left px-3 py-2 rounded-xl bg-gray-50 hover:bg-orange-50/70 border border-gray-200/80 hover:border-orange-300 text-xs font-semibold text-gray-800 truncate cursor-pointer">
                                    No. {{ $letter->letter_number }}
                                </button>
                                <div class="grid grid-cols-2 gap-1.5">
                                    <button type="button" onclick="addOrReplaceNumberText(LETTER_DATA.number)"
                                        class="shortcut-chip px-2.5 py-1.5 rounded-xl bg-white border border-gray-200 hover:border-orange-300 text-[11px] font-semibold text-gray-700 cursor-pointer">
                                        Tanpa "No."
                                    </button>
                                    <button type="button" onclick="snapNumberUnderTitle()"
                                        class="shortcut-chip px-2.5 py-1.5 rounded-xl bg-orange-50 border border-orange-200 text-[11px] font-bold text-[#FF4D00] cursor-pointer">
                                        Bawah Judul
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="pt-3 border-t border-gray-100 space-y-2">
                        <span class="block text-[11px] font-extrabold uppercase tracking-wider text-gray-500">Teks Tambahan</span>
                        <div class="grid grid-cols-1 gap-1.5">
                            <button type="button" onclick="addTextLayer('Jakarta, ' + LETTER_DATA.formattedDate, 0.5, 0.64, 'custom_text', 'Tanggal Surat')"
                                class="shortcut-chip flex items-center gap-2 px-3 py-2 rounded-xl bg-gray-50 hover:bg-indigo-50/60 border border-gray-200/80 text-xs font-semibold text-gray-700 cursor-pointer">
                                <i data-lucide="calendar" class="w-3.5 h-3.5 text-indigo-500"></i>
                                <span>+ Tanggal Surat</span>
                            </button>
                            @if ($letter->signatory)
                                <button type="button" onclick="addSignatoryBlockShortcut()"
                                    class="shortcut-chip flex items-center gap-2 px-3 py-2 rounded-xl bg-gray-50 hover:bg-emerald-50/60 border border-gray-200/80 text-xs font-semibold text-gray-700 cursor-pointer">
                                    <i data-lucide="user-check" class="w-3.5 h-3.5 text-emerald-600"></i>
                                    <span>+ Nama &amp; NIP Pejabat</span>
                                </button>
                            @endif
                            <button type="button" onclick="addCustomTextShortcut()"
                                class="shortcut-chip flex items-center gap-2 px-3 py-2 rounded-xl bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 text-xs font-bold text-indigo-700 cursor-pointer">
                                <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                                <span>+ Teks Bebas</span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Tool Panel: Signatures --}}
                <div id="toolPanel-signature" class="hidden space-y-3">
                    @php
                        $currentUser = auth()->user();
                        $primarySigAllowed = $letter->signatory ? $letter->signatory->canUserUseSignature($currentUser) : false;
                    @endphp
                    @if ($letter->signatory)
                        <div class="p-3 rounded-2xl {{ $primarySigAllowed ? 'bg-emerald-50/50 border-emerald-200/80' : 'bg-gray-50 border-gray-200' }} border space-y-2">
                            <div class="min-w-0 flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <span class="text-[9.5px] font-extrabold uppercase tracking-wider {{ $primarySigAllowed ? 'text-emerald-700' : 'text-gray-500' }}">Penandatangan Surat</span>
                                    <p class="text-xs font-bold text-gray-900 truncate">{{ $letter->signatory->name }}</p>
                                    <p class="text-[10.5px] text-gray-500 truncate">{{ $letter->signatory->position }}</p>
                                </div>
                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[9px] font-bold {{ $primarySigAllowed ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }} flex-shrink-0" title="{{ $primarySigAllowed ? 'TTD disimpan aman di private server' : 'Akses TTD ini dibatasi' }}">
                                    <i data-lucide="{{ $primarySigAllowed ? 'shield-check' : 'lock' }}" class="w-2.5 h-2.5"></i>
                                </span>
                            </div>
                            <div id="primarySigSlot">
                                @if (!$primarySigAllowed)
                                    <div class="p-2 rounded-xl bg-amber-50 border border-amber-200 text-[10.5px] text-amber-800 font-semibold flex items-center gap-1.5">
                                        <i data-lucide="lock" class="w-3.5 h-3.5 flex-shrink-0"></i>
                                        <span>Anda belum terdaftar untuk memakai TTD pejabat ini.</span>
                                    </div>
                                @elseif ($letter->signatory->signature_path)
                                    <button type="button"
                                        onclick="addImageLayer('signature', '{{ $letter->signatory->signature_url }}', '{{ addslashes($letter->signatory->name) }}', 100, null, {{ $letter->signatory->id }})"
                                        class="shortcut-chip w-full flex items-center gap-2.5 p-2 rounded-xl bg-white border border-emerald-200 hover:border-emerald-400 cursor-pointer">
                                        <div class="w-9 h-8 rounded-lg bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-700 flex-shrink-0">
                                            <i data-lucide="shield-check" class="w-4 h-4"></i>
                                        </div>
                                        <div class="flex-1 text-left min-w-0">
                                            <span class="text-xs font-bold text-emerald-800 block">Tempel TTD</span>
                                            <span class="text-[9.5px] text-gray-400 block truncate">Terproteksi Server</span>
                                        </div>
                                        <i data-lucide="plus-circle" class="w-4 h-4 text-emerald-600 flex-shrink-0"></i>
                                    </button>
                                @else
                                    <button type="button"
                                        onclick="openQuickUploadModal('signature', {{ $letter->signatory->id }}, '{{ addslashes($letter->signatory->name) }}')"
                                        class="w-full flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl bg-white border border-dashed border-emerald-300 text-[11px] font-bold text-emerald-700 hover:bg-emerald-50 cursor-pointer">
                                        <i data-lucide="upload" class="w-3.5 h-3.5"></i>
                                        <span>Upload TTD Pejabat Ini</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if ($otherSigs->count() > 0)
                        <div class="space-y-1.5">
                            <span class="block text-[10.5px] font-extrabold uppercase tracking-wider text-gray-400">Pejabat Lainnya</span>
                            <div class="space-y-1.5" id="otherSignaturesList">
                                @foreach ($otherSigs as $sig)
                                    @php $canUseSig = $sig->canUserUseSignature($currentUser); @endphp
                                    @if ($canUseSig)
                                        <button type="button"
                                            onclick="addImageLayer('signature', '{{ $sig->signature_url }}', '{{ addslashes($sig->name) }}', 100, null, {{ $sig->id }})"
                                            class="shortcut-chip w-full flex items-center gap-2 p-2 rounded-xl bg-gray-50 hover:bg-emerald-50/70 border border-gray-200/80 hover:border-emerald-300 text-left cursor-pointer" title="Tempel gambar TTD terproteksi">
                                            <div class="w-7 h-7 rounded-lg bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 flex-shrink-0">
                                                <i data-lucide="pen-tool" class="w-3.5 h-3.5"></i>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-[11px] font-bold text-gray-800 truncate">{{ $sig->name }}</p>
                                                <p class="text-[10px] text-gray-400 truncate">{{ $sig->position }}</p>
                                            </div>
                                            <i data-lucide="plus-circle" class="w-3.5 h-3.5 text-emerald-600 flex-shrink-0"></i>
                                        </button>
                                    @else
                                        <div class="flex items-center gap-2 p-2 rounded-xl bg-gray-100/70 border border-gray-200/60 opacity-65" title="Akses TTD ini dibatasi hanya untuk user terdaftar">
                                            <div class="w-7 h-7 rounded-lg bg-gray-200 flex items-center justify-center text-gray-500 flex-shrink-0">
                                                <i data-lucide="lock" class="w-3.5 h-3.5"></i>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-[11px] font-bold text-gray-600 truncate">{{ $sig->name }}</p>
                                                <p class="text-[9.5px] text-amber-700 font-semibold truncate">Akses TTD Terbatas</p>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if (!$letter->signatory || $primarySigAllowed)
                        <button type="button" onclick="openQuickUploadModal('signature', {{ $letter->signatory_id ?? 'null' }}, '{{ addslashes($letter->signatory?->name ?? '') }}')"
                            class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl border border-dashed border-emerald-300 text-emerald-700 hover:bg-emerald-50 text-xs font-bold transition-colors cursor-pointer">
                            <i data-lucide="upload" class="w-3.5 h-3.5"></i>
                            <span>+ Upload TTD Baru</span>
                        </button>
                    @endif
                </div>

                {{-- Tool Panel: Stamps --}}
                <div id="toolPanel-stamp" class="hidden space-y-3">
                    <div id="stampsGallery" class="grid grid-cols-2 gap-2">
                        @forelse ($stamps as $stamp)
                            <button type="button"
                                onclick="addImageLayer('stamp', '{{ $stamp->file_url }}', '{{ addslashes($stamp->name) }}', {{ $stamp->default_opacity }})"
                                class="shortcut-chip flex flex-col items-center gap-1.5 p-2.5 rounded-2xl bg-gray-50 hover:bg-purple-50/70 border border-gray-200/80 hover:border-purple-300 text-center cursor-pointer"
                                title="Tempel {{ $stamp->name }}">
                                <div class="w-12 h-12 checkerboard-mini rounded-xl flex items-center justify-center p-1">
                                    <img src="{{ $stamp->file_url }}" alt="{{ $stamp->name }}" class="max-h-full max-w-full object-contain" style="opacity: {{ $stamp->default_opacity / 100 }}">
                                </div>
                                <span class="text-[11px] font-bold text-gray-800 truncate w-full">{{ $stamp->name }}</span>
                            </button>
                        @empty
                            <div class="col-span-2 text-center py-4 px-2 rounded-2xl bg-gray-50 border border-dashed border-gray-200 text-[11px] text-gray-500">
                                Belum ada stempel aktif.
                            </div>
                        @endforelse
                    </div>

                    <button type="button" onclick="openQuickUploadModal('stamp')"
                        class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl border border-dashed border-purple-300 text-purple-700 hover:bg-purple-50 text-xs font-bold transition-colors cursor-pointer">
                        <i data-lucide="upload" class="w-3.5 h-3.5"></i>
                        <span>+ Upload Stempel Baru</span>
                    </button>
                </div>

                {{-- Tool Panel: Layer Stack --}}
                <div id="toolPanel-layers" class="hidden space-y-2.5">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-extrabold uppercase tracking-wider text-gray-500">Urutan Layer</span>
                        <span id="layerCountBadge" class="text-[11px] font-bold text-gray-400">0 elemen</span>
                    </div>
                    <div id="layerListContainer" class="space-y-1.5 text-xs text-gray-500">
                        <p class="text-[11px] text-gray-400 italic">Belum ada elemen.</p>
                    </div>
                </div>
            </div>
        </aside>

        {{-- C. CENTER ARTBOARD STAGE --}}
        <section id="centerStudioCol" class="flex-1 flex flex-col min-w-0 min-h-0 bg-[#EBEEF3] relative">
            {{-- Floating Slim Sync Bar (Visible when slides.length > 1) --}}
            <div id="syncModeBar" class="hidden pt-2.5 px-3 flex justify-center flex-shrink-0 z-10">
                <div class="inline-flex flex-wrap items-center justify-center gap-2 px-3 py-1 rounded-full bg-white/95 backdrop-blur-md border border-gray-200/90 shadow-xs text-xs">
                    <span id="syncModePill" class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/80 flex-shrink-0">
                        <i data-lucide="link-2" class="w-3 h-3"></i>
                        <span>Sinkron Semua</span>
                    </span>
                    <button type="button" id="btnToggleCustomMode" onclick="toggleSlideCustomMode()"
                        class="px-2.5 py-1 rounded-full bg-gray-50 hover:bg-amber-50 text-amber-700 border border-amber-200/80 text-[11px] font-bold transition-colors cursor-pointer">
                        Edit Khusus Hal. Ini
                    </button>
                    <button type="button" id="btnSyncAllFromCurrent" onclick="applyCurrentSlideLayoutToAll()"
                        class="hidden px-2.5 py-1 rounded-full bg-indigo-600 hover:bg-indigo-700 text-white text-[11px] font-bold transition-colors cursor-pointer">
                        Samakan Semua
                    </button>
                </div>
            </div>

            {{-- Artboard Viewport --}}
            <div id="stageWrapper" class="flex-1 min-h-0 studio-stage-bg flex overflow-auto p-4 sm:p-6 relative">
                {{-- Empty State --}}
                <div id="emptyStageState" class="m-auto text-center max-w-xs sm:max-w-sm space-y-3.5 p-8 rounded-3xl bg-white/90 backdrop-blur-sm border border-gray-200/80 shadow-sm">
                    @if ($studioMode === 'letter')
                        <div class="w-14 h-14 rounded-2xl bg-emerald-50 border border-emerald-200/80 text-emerald-600 flex items-center justify-center mx-auto">
                            <i data-lucide="layout-template" class="w-7 h-7"></i>
                        </div>
                        <div class="space-y-1">
                            <h3 class="text-sm font-extrabold text-gray-900">Pilih Template Kertas Kop (Base)</h3>
                            <p class="text-[11.5px] text-gray-500">Pilih atau upload Template Kop FEB UNJ (PDF/PNG), lalu crop isi surat dari Universitas.</p>
                        </div>
                        <div class="flex flex-col gap-2">
                            @if ($letterheads->count() > 0)
                                <button type="button" onclick="selectLetterheadAsBase('{{ $letterheads->first()->file_url }}', '{{ addslashes($letterheads->first()->name) }}')"
                                    class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-full bg-gradient-to-r from-emerald-600 to-teal-600 text-white text-xs font-bold shadow-xs cursor-pointer">
                                    <i data-lucide="check-circle-2" class="w-4 h-4"></i>
                                    <span>Pakai Template "{{ $letterheads->first()->name }}"</span>
                                </button>
                                <button type="button" onclick="openQuickUploadModal('letterhead')"
                                    class="text-[11px] font-bold text-emerald-700 hover:underline cursor-pointer">
                                    + Upload Template Kop Baru
                                </button>
                            @else
                                <button type="button" onclick="openQuickUploadModal('letterhead')"
                                    class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-full bg-gradient-to-r from-emerald-600 to-teal-600 text-white text-xs font-bold shadow-xs cursor-pointer">
                                    <i data-lucide="upload" class="w-4 h-4"></i>
                                    <span>Upload Template Kop (PDF / PNG)</span>
                                </button>
                            @endif
                        </div>
                    @else
                        <div class="w-14 h-14 rounded-2xl bg-orange-50 border border-orange-200/80 text-[#FF4D00] flex items-center justify-center mx-auto">
                            <i data-lucide="file-badge" class="w-7 h-7"></i>
                        </div>
                        <div class="space-y-1">
                            <h3 class="text-sm font-extrabold text-gray-900">Upload Sertifikat untuk Memulai</h3>
                            <p class="text-[11.5px] text-gray-500">Pilih atau tarik 1 atau banyak PNG/JPG, atau PDF multi-halaman.</p>
                        </div>
                        <button type="button" onclick="document.getElementById('baseFileInput').click()"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-gradient-to-r from-[#FF4D00] to-amber-500 text-white text-xs font-bold shadow-xs cursor-pointer">
                            <i data-lucide="upload" class="w-4 h-4"></i>
                            <span>Pilih File</span>
                        </button>
                    @endif
                </div>

                {{-- Loading Spinner Overlay --}}
                <div id="stageLoadingOverlay" class="hidden absolute inset-0 z-20 bg-white/70 backdrop-blur-[2px] flex items-center justify-center">
                    <div class="flex items-center gap-2.5 px-4 py-2.5 rounded-2xl bg-white shadow-lg border border-gray-200 text-xs font-bold text-gray-800">
                        <span class="w-4 h-4 border-2 border-[#FF4D00] border-t-transparent rounded-full animate-spin"></span>
                        <span>Memuat halaman...</span>
                    </div>
                </div>

                {{-- Interactive Canvas --}}
                <canvas id="studioCanvas" oncontextmenu="return false" class="hidden m-auto flex-shrink-0 rounded-lg shadow-[0_12px_40px_rgba(15,23,42,0.14)] ring-1 ring-black/5 bg-white touch-none select-none"></canvas>
            </div>

            {{-- Bottom Filmstrip Dock --}}
            <div id="filmstripCard" class="hidden bg-white border-t border-gray-200/80 px-3.5 py-2 flex-shrink-0 space-y-2 z-10">
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <span class="text-[11px] font-extrabold text-gray-800 flex items-center gap-1.5">
                            <i data-lucide="gallery-horizontal-end" class="w-3.5 h-3.5 text-[#FF4D00]"></i>
                            <span>Halaman</span>
                        </span>
                        <span id="filmstripCountBadge" class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-700">0 halaman</span>
                    </div>

                    <div class="flex items-center gap-1.5">
                        <button type="button" onclick="switchSlide(activeSlideIndex - 1)" id="btnPrevSlide"
                            class="w-6 h-6 rounded-md bg-gray-100 hover:bg-gray-200 disabled:opacity-35 flex items-center justify-center text-gray-700 cursor-pointer" title="Halaman Sebelumnya (PageUp)">
                            <i data-lucide="chevron-left" class="w-3.5 h-3.5"></i>
                        </button>
                        <span id="filmstripPagePos" class="text-[11px] font-mono font-bold text-gray-700 px-1">1 / 1</span>
                        <button type="button" onclick="switchSlide(activeSlideIndex + 1)" id="btnNextSlide"
                            class="w-6 h-6 rounded-md bg-gray-100 hover:bg-gray-200 disabled:opacity-35 flex items-center justify-center text-gray-700 cursor-pointer" title="Halaman Berikutnya (PageDown)">
                            <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                        </button>
                        <button type="button" id="btnToggleFilmstrip" onclick="toggleFilmstripTray()"
                            class="ml-1 w-6 h-6 rounded-md hover:bg-gray-100 text-gray-400 hover:text-gray-700 flex items-center justify-center cursor-pointer" title="Lipat / Buka Strip Halaman">
                            <i id="filmstripToggleIcon" data-lucide="chevron-down" class="w-3.5 h-3.5"></i>
                        </button>
                    </div>
                </div>

                <div id="filmstripContainer" class="filmstrip-scroll flex items-stretch gap-2 overflow-x-auto pb-1"></div>
            </div>
        </section>

        {{-- D. RIGHT CONTEXTUAL PROPERTIES INSPECTOR (272px) --}}
        <aside id="rightStudioCol" class="w-full lg:w-[272px] bg-white border-t lg:border-t-0 lg:border-l border-gray-200/80 flex flex-col min-h-0 z-10 flex-shrink-0">
            <div class="h-11 px-3.5 border-b border-gray-100 flex items-center justify-between flex-shrink-0">
                <span class="text-xs font-extrabold text-gray-900 uppercase tracking-wider">Properti</span>
                <div class="flex items-center gap-1.5">
                    <button type="button" id="btnInspectorLock" onclick="toggleSelectedLock()" class="hidden px-2 py-0.5 rounded-md border text-[10px] font-bold cursor-pointer"></button>
                    <span id="selectedLayerTypeBadge" class="hidden px-2 py-0.5 rounded-md text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200"></span>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto studio-scroll">
                {{-- Empty State --}}
                <div id="inspectorEmpty" class="p-6 text-center space-y-3">
                    <div class="w-10 h-10 rounded-xl bg-gray-50 border border-gray-200/80 text-gray-400 flex items-center justify-center mx-auto">
                        <i data-lucide="mouse-pointer-click" class="w-5 h-5"></i>
                    </div>
                    <p class="text-[11.5px] text-gray-500 leading-relaxed">Klik elemen di kanvas untuk mengatur font, warna, ukuran, &amp; posisi.</p>
                    <button type="button" onclick="smartAutoCompose()"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-[11px] font-bold transition-colors cursor-pointer">
                        <i data-lucide="wand-2" class="w-3.5 h-3.5"></i>
                        <span>Susun Otomatis</span>
                    </button>
                </div>

                {{-- Selected Layer Quick Action Bar --}}
                <div id="inspectorCommon" class="hidden px-3.5 py-2.5 border-b border-gray-100 flex items-center justify-between gap-1 bg-gray-50/50">
                    <button type="button" onclick="centerSelectedLayer()" class="flex-1 h-7 rounded-lg bg-white hover:bg-blue-50 border border-gray-200/80 text-gray-700 hover:text-blue-700 flex items-center justify-center cursor-pointer" title="Tengah Horizontal (Ctrl+E)">
                        <i data-lucide="align-center-horizontal" class="w-3.5 h-3.5"></i>
                    </button>
                    <button type="button" onclick="bringSelectedForward()" class="flex-1 h-7 rounded-lg bg-white hover:bg-gray-100 border border-gray-200/80 text-gray-700 flex items-center justify-center cursor-pointer" title="Naikkan Layer">
                        <i data-lucide="arrow-up-to-line" class="w-3.5 h-3.5"></i>
                    </button>
                    <button type="button" onclick="sendSelectedBackward()" class="flex-1 h-7 rounded-lg bg-white hover:bg-gray-100 border border-gray-200/80 text-gray-700 flex items-center justify-center cursor-pointer" title="Turunkan Layer">
                        <i data-lucide="arrow-down-to-line" class="w-3.5 h-3.5"></i>
                    </button>
                    <button type="button" onclick="duplicateSelectedLayer()" class="flex-1 h-7 rounded-lg bg-white hover:bg-indigo-50 border border-gray-200/80 text-gray-700 hover:text-indigo-700 flex items-center justify-center cursor-pointer" title="Duplikat (Ctrl+D)">
                        <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                    </button>
                    <button type="button" onclick="deleteSelectedLayer()" class="flex-1 h-7 rounded-lg bg-white hover:bg-rose-50 border border-gray-200/80 text-rose-600 flex items-center justify-center cursor-pointer" title="Hapus Elemen">
                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                    </button>
                </div>

                {{-- Text Inspector --}}
                <div id="inspectorText" class="hidden divide-y divide-gray-100">
                    <div class="p-3.5 space-y-2.5">
                        <div>
                            <label class="block text-[10.5px] font-bold uppercase tracking-wider text-gray-400 mb-1">Teks</label>
                            <textarea id="propTextValue" rows="2" oninput="updateSelectedTextProp('text', this.value)"
                                class="w-full px-2.5 py-1.5 text-xs font-semibold border border-gray-200 rounded-xl focus:outline-none focus:border-[#FF4D00] resize-y"></textarea>
                        </div>

                        <div class="grid grid-cols-12 gap-2">
                            <div class="col-span-8">
                                <label class="block text-[10.5px] font-bold uppercase tracking-wider text-gray-400 mb-1">Font</label>
                                <select id="propFontFamily" onchange="changeSelectedFontFamily(this.value)"
                                    class="w-full px-2 py-1.5 text-xs border border-gray-200 rounded-xl bg-white focus:outline-none focus:border-[#FF4D00]">
                                    <optgroup label="Serif Resmi &amp; Piagam">
                                        <option value="Times New Roman, Liberation Serif, serif">Times New Roman</option>
                                        <option value="'EB Garamond', Georgia, serif">EB Garamond</option>
                                        <option value="'Playfair Display', Georgia, serif">Playfair Display</option>
                                        <option value="'Cinzel', Georgia, serif">Cinzel (Klasik Piagam)</option>
                                        <option value="'Merriweather', Georgia, serif">Merriweather</option>
                                        <option value="'Lora', Georgia, serif">Lora Serif</option>
                                        <option value="Georgia, serif">Georgia Serif</option>
                                    </optgroup>
                                    <optgroup label="Sans-Serif Modern">
                                        <option value="'Plus Jakarta Sans', sans-serif">Plus Jakarta Sans</option>
                                        <option value="'Montserrat', sans-serif">Montserrat</option>
                                        <option value="'Poppins', sans-serif">Poppins</option>
                                        <option value="Arial, sans-serif">Arial / Sans</option>
                                    </optgroup>
                                    <optgroup label="Kaligrafi / Script">
                                        <option value="'Great Vibes', cursive">Great Vibes</option>
                                        <option value="'Alex Brush', cursive">Alex Brush</option>
                                    </optgroup>
                                    <optgroup label="Monospace">
                                        <option value="'JetBrains Mono', monospace">JetBrains Mono</option>
                                    </optgroup>
                                </select>
                            </div>
                            <div class="col-span-4">
                                <label class="block text-[10.5px] font-bold uppercase tracking-wider text-gray-400 mb-1"><span id="propFontSizeLabel">48</span>px</label>
                                <input type="number" id="propFontSize" min="12" max="260" value="48" oninput="updateSelectedTextProp('fontSize', parseInt(this.value || 48))"
                                    class="w-full px-2 py-1.5 text-xs font-mono border border-gray-200 rounded-xl focus:outline-none">
                            </div>
                        </div>

                        <input type="range" id="propFontSizeSlider" min="14" max="180" value="48"
                            oninput="document.getElementById('propFontSize').value=this.value; updateSelectedTextProp('fontSize', parseInt(this.value))"
                            class="w-full accent-[#FF4D00]">
                    </div>

                    <div class="p-3.5 space-y-2.5">
                        <div class="flex items-center justify-between gap-1">
                            <div class="flex items-center gap-1">
                                <button type="button" id="btnPropBold" onclick="toggleSelectedTextStyle('bold')" class="w-7 h-7 text-xs font-extrabold border border-gray-200 rounded-lg cursor-pointer transition-colors" title="Tebal">B</button>
                                <button type="button" id="btnPropItalic" onclick="toggleSelectedTextStyle('italic')" class="w-7 h-7 text-xs italic font-serif border border-gray-200 rounded-lg cursor-pointer transition-colors" title="Miring">I</button>
                                <button type="button" id="btnPropUnderline" onclick="toggleSelectedTextStyle('underline')" class="w-7 h-7 text-xs underline font-semibold border border-gray-200 rounded-lg cursor-pointer transition-colors" title="Garis Bawah">U</button>
                                <button type="button" id="btnPropStrike" onclick="toggleSelectedTextStyle('strikethrough')" class="w-7 h-7 text-xs line-through font-semibold border border-gray-200 rounded-lg cursor-pointer transition-colors" title="Coret">S</button>
                                <button type="button" id="btnPropUpper" onclick="toggleSelectedTextStyle('uppercase')" class="w-7 h-7 text-[10px] font-bold border border-gray-200 rounded-lg cursor-pointer transition-colors" title="Kapital">AA</button>
                            </div>
                            <div class="flex items-center gap-1">
                                <button type="button" id="btnAlignLeft" onclick="updateSelectedTextProp('align', 'left'); pushHistory();" class="p-1.5 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer" title="Kiri"><i data-lucide="align-left" class="w-3.5 h-3.5"></i></button>
                                <button type="button" id="btnAlignCenter" onclick="updateSelectedTextProp('align', 'center'); pushHistory();" class="p-1.5 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer" title="Tengah"><i data-lucide="align-center" class="w-3.5 h-3.5"></i></button>
                                <button type="button" id="btnAlignRight" onclick="updateSelectedTextProp('align', 'right'); pushHistory();" class="p-1.5 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer" title="Kanan"><i data-lucide="align-right" class="w-3.5 h-3.5"></i></button>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 pt-1">
                            <div>
                                <div class="flex items-center justify-between text-[10.5px] font-bold text-gray-500 mb-1">
                                    <span>Spasi</span>
                                    <span id="propLetterSpacingLabel" class="font-mono">0px</span>
                                </div>
                                <input type="range" id="propLetterSpacing" min="-2" max="15" step="0.5" value="0"
                                    oninput="updateSelectedTextProp('letterSpacing', parseFloat(this.value))"
                                    class="w-full accent-[#FF4D00]">
                            </div>
                            <div>
                                <div class="flex items-center justify-between text-[10.5px] font-bold text-gray-500 mb-1">
                                    <span>Opacity</span>
                                    <span id="propTextOpacityLabel" class="font-mono">100%</span>
                                </div>
                                <input type="range" id="propTextOpacity" min="15" max="100" step="1" value="100"
                                    oninput="updateSelectedProp('opacity', parseInt(this.value))"
                                    class="w-full accent-indigo-600">
                            </div>
                        </div>
                    </div>

                    <div class="p-3.5 space-y-2.5">
                        <div class="flex items-center justify-between">
                            <span class="text-[10.5px] font-bold uppercase tracking-wider text-gray-400">Warna</span>
                            <label class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-gray-600 cursor-pointer">
                                <input type="checkbox" id="propTextShadow" onchange="updateSelectedTextProp('textShadow', this.checked); pushHistory();" class="rounded border-gray-300 text-indigo-600">
                                <span>Bayangan</span>
                            </label>
                        </div>
                        <div class="flex flex-wrap items-center gap-1.5">
                            <input type="color" id="propTextColor" value="#111827" oninput="updateSelectedTextProp('color', this.value)"
                                class="w-7 h-7 rounded-lg border border-gray-200 cursor-pointer p-0.5">
                            @foreach (['#111827', '#000000', '#1e3a8a', '#7c2d12', '#92400e', '#14532d', '#ffffff'] as $hex)
                                <button type="button" onclick="setTextColorPreset('{{ $hex }}')"
                                    class="w-5 h-5 rounded-full border border-gray-300 cursor-pointer shadow-2xs" style="background: {{ $hex }}" title="{{ $hex }}"></button>
                            @endforeach
                        </div>
                        <div class="pt-2 border-t border-gray-100 space-y-2">
                            <div class="flex items-center justify-between gap-2">
                                <label class="inline-flex items-center gap-1.5 text-[11px] font-bold text-gray-700 cursor-pointer" title="Beri kotak putih di belakang teks agar menimpa tulisan lama">
                                    <input type="checkbox" id="propTextBgFill" onchange="updateSelectedTextProp('bgFill', this.checked); syncInspector(); pushHistory();" class="rounded border-gray-300 text-emerald-600">
                                    <span>Latar Putih (Tutup Teks)</span>
                                </label>
                                <input type="color" id="propTextBgColor" value="#ffffff" oninput="updateSelectedTextProp('bgColor', this.value)"
                                    class="w-6 h-6 rounded-md border border-gray-200 cursor-pointer p-0.5" title="Warna latar penutup teks">
                            </div>
                            <div id="propTextBgWidthWrap" class="hidden">
                                <div class="flex items-center justify-between text-[10.5px] font-bold text-gray-500 mb-1">
                                    <span>Lebar Min. Penutup</span>
                                    <span id="propTextBgMinWLabel" class="font-mono">0%</span>
                                </div>
                                <input type="range" id="propTextBgMinW" min="0" max="60" step="1" value="0"
                                    oninput="updateSelectedTextProp('bgMinWidthPct', parseInt(this.value))"
                                    class="w-full accent-emerald-600">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Image Inspector (TTD / Stamp / Kop Surat) --}}
                <div id="inspectorImage" class="hidden divide-y divide-gray-100">
                    <div id="propLetterheadTools" class="hidden p-3.5 space-y-2 bg-emerald-50/40">
                        <button type="button" onclick="fitLetterheadFullWidth()"
                            class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-bold transition-colors cursor-pointer">
                            <i data-lucide="move-horizontal" class="w-3.5 h-3.5"></i>
                            <span>Sesuaikan Lebar Penuh (100%)</span>
                        </button>
                        <label class="flex items-center justify-between gap-2 text-[11px] font-semibold text-emerald-900 cursor-pointer">
                            <span>Latar Putih Belakang Kop</span>
                            <input type="checkbox" id="propWhiteBacking" onchange="updateSelectedProp('whiteBacking', this.checked); pushHistory();" class="rounded border-emerald-300 text-emerald-600">
                        </label>
                    </div>
                    <div class="p-3.5 space-y-3">
                        <div>
                            <div class="flex items-center justify-between text-[11px] font-bold text-gray-600 mb-1">
                                <span>Ukuran</span>
                                <span id="propImgScaleLabel" class="font-mono text-gray-900">18%</span>
                            </div>
                            <input type="range" id="propImgScale" min="4" max="105" step="0.5" value="18"
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
                                <button type="button" onclick="updateSelectedProp('rotation', 0); pushHistory();" class="px-2 py-0.5 text-[10px] font-bold bg-gray-100 rounded-md cursor-pointer">0°</button>
                            </div>
                        </div>

                        <div>
                            <div class="flex items-center justify-between text-[11px] font-bold text-gray-600 mb-1">
                                <span>Opacity</span>
                                <span id="propImgOpacityLabel" class="font-mono text-gray-900">100%</span>
                            </div>
                            <input type="range" id="propImgOpacity" min="15" max="100" step="1" value="100"
                                oninput="updateSelectedProp('opacity', parseInt(this.value))"
                                class="w-full accent-purple-600">
                        </div>
                    </div>

                    <div class="p-3.5">
                        <div class="p-2.5 rounded-xl bg-indigo-50/60 border border-indigo-200/70 space-y-2">
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
                                    <span>Sensitivitas</span>
                                    <span id="propBgThresholdLabel">225</span>
                                </div>
                                <input type="range" id="propBgThreshold" min="175" max="250" value="225"
                                    oninput="updateSelectedBgThreshold(parseInt(this.value))"
                                    class="w-full accent-indigo-600">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Whiteout Inspector --}}
                <div id="inspectorWhiteout" class="hidden divide-y divide-gray-100">
                    <div class="p-3.5 space-y-3">
                        <div>
                            <div class="flex items-center justify-between text-[11px] font-bold text-gray-600 mb-1">
                                <span>Lebar Penutup</span>
                                <span id="propWhiteoutWLabel" class="font-mono text-gray-900">40%</span>
                            </div>
                            <input type="range" id="propWhiteoutW" min="2" max="100" step="0.5" value="40"
                                oninput="updateSelectedWhiteoutSize('width', parseFloat(this.value))"
                                class="w-full accent-emerald-600">
                        </div>
                        <div>
                            <div class="flex items-center justify-between text-[11px] font-bold text-gray-600 mb-1">
                                <span>Tinggi Penutup</span>
                                <span id="propWhiteoutHLabel" class="font-mono text-gray-900">15%</span>
                            </div>
                            <input type="range" id="propWhiteoutH" min="1" max="60" step="0.5" value="15"
                                oninput="updateSelectedWhiteoutSize('height', parseFloat(this.value))"
                                class="w-full accent-emerald-600">
                        </div>
                        <div>
                            <div class="flex items-center justify-between text-[11px] font-bold text-gray-600 mb-1">
                                <span>Opacity</span>
                                <span id="propWhiteoutOpacityLabel" class="font-mono text-gray-900">100%</span>
                            </div>
                            <input type="range" id="propWhiteoutOpacity" min="20" max="100" step="1" value="100"
                                oninput="updateSelectedProp('opacity', parseInt(this.value))"
                                class="w-full accent-gray-700">
                        </div>
                    </div>
                    <div class="p-3.5 space-y-2">
                        <span class="block text-[10.5px] font-bold uppercase tracking-wider text-gray-400">Warna Kertas Penutup</span>
                        <div class="flex flex-wrap items-center gap-1.5">
                            <input type="color" id="propWhiteoutColor" value="#ffffff" oninput="updateSelectedWhiteoutColor(this.value)"
                                class="w-7 h-7 rounded-lg border border-gray-200 cursor-pointer p-0.5">
                            @foreach (['#ffffff', '#fcfcfd', '#f8fafc', '#f5f5f4', '#fefce8'] as $wHex)
                                <button type="button" onclick="setWhiteoutColorPreset('{{ $wHex }}')"
                                    class="w-5.5 h-5.5 rounded-full border border-gray-300 cursor-pointer shadow-2xs" style="background: {{ $wHex }}" title="{{ $wHex }}"></button>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Fine Nudge D-Pad --}}
                <div id="inspectorNudgeWrap" class="hidden p-3.5 border-t border-gray-100">
                    <div class="flex items-center justify-between bg-gray-50 border border-gray-200/70 rounded-xl px-3 py-2">
                        <span class="text-[11px] font-bold text-gray-600">Geser Halus</span>
                        <div class="flex items-center gap-1">
                            <button type="button" onclick="nudgeSelectedLayer(-4, 0)" class="w-6 h-6 rounded-lg bg-white border border-gray-200 hover:bg-gray-100 flex items-center justify-center text-gray-700 cursor-pointer" title="Kiri"><i data-lucide="arrow-left" class="w-3 h-3"></i></button>
                            <button type="button" onclick="nudgeSelectedLayer(0, -4)" class="w-6 h-6 rounded-lg bg-white border border-gray-200 hover:bg-gray-100 flex items-center justify-center text-gray-700 cursor-pointer" title="Atas"><i data-lucide="arrow-up" class="w-3 h-3"></i></button>
                            <button type="button" onclick="nudgeSelectedLayer(0, 4)" class="w-6 h-6 rounded-lg bg-white border border-gray-200 hover:bg-gray-100 flex items-center justify-center text-gray-700 cursor-pointer" title="Bawah"><i data-lucide="arrow-down" class="w-3 h-3"></i></button>
                            <button type="button" onclick="nudgeSelectedLayer(4, 0)" class="w-6 h-6 rounded-lg bg-white border border-gray-200 hover:bg-gray-100 flex items-center justify-center text-gray-700 cursor-pointer" title="Kanan"><i data-lucide="arrow-right" class="w-3 h-3"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </aside>
    </div>
</div>

{{-- Modal: Fullscreen Clean Preview --}}
<div id="previewLightboxModal" class="hidden fixed inset-0 z-[9997] flex items-center justify-center p-4 sm:p-6 bg-slate-950/85 backdrop-blur-md">
    <div class="max-w-6xl w-full flex flex-col gap-3 max-h-[94vh]">
        <div class="flex items-center justify-between text-white">
            <div class="flex items-center gap-2.5">
                <span class="px-2.5 py-1 rounded-full bg-white/10 text-xs font-bold">Pratinjau Hasil Akhir</span>
                <span id="lightboxSlideTitle" class="text-xs text-gray-300 font-mono"></span>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" id="lightboxPrevBtn" onclick="lightboxSwitch(-1)" class="hidden px-3 py-1.5 rounded-xl bg-white/10 hover:bg-white/20 text-xs font-bold cursor-pointer">&larr; Prev</button>
                <button type="button" id="lightboxNextBtn" onclick="lightboxSwitch(1)" class="hidden px-3 py-1.5 rounded-xl bg-white/10 hover:bg-white/20 text-xs font-bold cursor-pointer">Next &rarr;</button>
                <button type="button" onclick="closeFullscreenPreview()" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-white text-gray-900 text-xs font-bold hover:bg-gray-100 cursor-pointer">
                    <i data-lucide="x" class="w-3.5 h-3.5"></i>
                    <span>Tutup</span>
                </button>
            </div>
        </div>
        <div class="flex-1 overflow-auto flex items-center justify-center rounded-2xl bg-black/30 p-2">
            <img id="lightboxPreviewImg" src="" alt="Pratinjau Sertifikat" class="max-h-[82vh] max-w-full object-contain rounded-xl shadow-2xl">
        </div>
    </div>
</div>

{{-- Modal: Rename Slide --}}
<div id="renameSlideModal" class="hidden fixed inset-0 z-[9996] flex items-center justify-center p-4 bg-slate-900/45 backdrop-blur-sm">
    <div class="bg-white rounded-3xl shadow-2xl max-w-sm w-full border border-slate-200 overflow-hidden">
        <form onsubmit="submitRenameSlide(event)">
            <input type="hidden" id="renameSlideIndex" value="">
            <div class="p-6 space-y-4">
                <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                    <h3 class="text-sm font-bold text-gray-900">Ubah Nama File Sertifikat</h3>
                    <button type="button" onclick="closeRenameSlideModal()" class="p-1 text-gray-400 hover:text-gray-600 cursor-pointer"><i data-lucide="x" class="w-4 h-4"></i></button>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Nama Tampilan / Nama File Export ZIP</label>
                    <input type="text" id="renameSlideInput" required placeholder="Misal: Narasumber_Dr_Budi"
                        class="w-full px-3.5 py-2 text-xs border border-gray-200 rounded-xl focus:outline-none focus:border-[#FF4D00]">
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeRenameSlideModal()" class="px-4 py-2 text-xs font-semibold bg-gray-100 rounded-xl cursor-pointer">Batal</button>
                    <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-gray-900 hover:bg-gray-800 rounded-xl cursor-pointer">Simpan Nama</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Modal: Batch Progress --}}
<div id="batchProgressModal" class="hidden fixed inset-0 z-[9998] flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
    <div class="bg-white rounded-3xl shadow-2xl max-w-sm w-full border border-slate-200 p-6 space-y-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-orange-50 border border-orange-200 text-[#FF4D00] flex items-center justify-center flex-shrink-0">
                <span class="w-5 h-5 border-2 border-[#FF4D00] border-t-transparent rounded-full animate-spin"></span>
            </div>
            <div>
                <h3 id="batchProgressTitle" class="text-sm font-extrabold text-gray-900">Memproses Sertifikat...</h3>
                <p id="batchProgressText" class="text-xs text-gray-500 mt-0.5">Menyiapkan halaman 1 dari 1...</p>
            </div>
        </div>
        <div class="w-full h-2.5 bg-gray-100 rounded-full overflow-hidden">
            <div id="batchProgressBar" class="h-full bg-gradient-to-r from-[#FF4D00] to-amber-500 rounded-full transition-all duration-200" style="width: 0%"></div>
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
                        <label id="qaFileLabel" class="block text-xs font-bold text-gray-700 mb-1">File Gambar (PNG/JPG/WEBP, maks. 5 MB)</label>
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

{{-- Modal: Interactive Crop Surat Universitas --}}
<div id="univCropModal" class="hidden fixed inset-0 z-[9996] flex items-center justify-center p-3 sm:p-5 bg-slate-950/75 backdrop-blur-sm">
    <div class="bg-white rounded-3xl shadow-2xl max-w-4xl w-full max-h-[94vh] flex flex-col overflow-hidden border border-slate-200">
        <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between gap-3 flex-shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center">
                    <i data-lucide="crop" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-gray-900">Crop Isi Surat Universitas</h3>
                    <p class="text-[11px] text-gray-500">Tarik garis hijau untuk memilih bagian body ke bawah yang akan ditempel di Template Kop FEB</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <div id="cropPageSelectWrap" class="hidden inline-flex items-center gap-1 bg-gray-100 rounded-xl p-1 text-xs">
                    <button type="button" id="cropPrevPageBtn" onclick="switchCropPdfPage(-1)" class="w-6 h-6 rounded-lg hover:bg-white text-gray-700 font-bold cursor-pointer">&lsaquo;</button>
                    <span id="cropPageInfo" class="px-1.5 font-mono text-[11px] font-bold text-gray-700">Hal 1/1</span>
                    <button type="button" id="cropNextPageBtn" onclick="switchCropPdfPage(1)" class="w-6 h-6 rounded-lg hover:bg-white text-gray-700 font-bold cursor-pointer">&rsaquo;</button>
                </div>
                <button type="button" onclick="closeUnivCropModal()" class="p-1.5 text-gray-400 hover:text-gray-700 rounded-xl cursor-pointer">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        </div>

        {{-- Multi-Page Inclusion & Sync Strip --}}
        <div id="cropPagesBar" class="hidden px-5 py-2 bg-emerald-50/50 border-b border-emerald-100 flex items-center gap-2 overflow-x-auto filmstrip-scroll flex-shrink-0"></div>

        <div class="px-5 py-2 bg-gray-50 border-b border-gray-100 flex flex-wrap items-center justify-between gap-2 text-xs flex-shrink-0">
            <div class="flex flex-wrap items-center gap-1.5">
                <span class="text-[11px] font-bold text-gray-500 mr-1">Preset:</span>
                <button type="button" onclick="setCropPreset(0.14, 0.945, 0.03, 0.97)"
                    class="px-2.5 py-1 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 text-[11px] font-bold cursor-pointer">
                    Standar (Tanpa Kop &amp; Footer)
                </button>
                <button type="button" onclick="setCropPreset(0.14, 0.675, 0.03, 0.97)"
                    class="px-2.5 py-1 rounded-lg bg-white hover:bg-gray-100 text-gray-700 border border-gray-200 text-[11px] font-semibold cursor-pointer">
                    Tanpa Kop &amp; TTD Lama
                </button>
                <button type="button" onclick="setCropPreset(0, 1, 0, 1)"
                    class="px-2.5 py-1 rounded-lg bg-white hover:bg-gray-100 text-gray-700 border border-gray-200 text-[11px] font-semibold cursor-pointer">
                    Penuh
                </button>

                <div id="cropPageSyncWrap" class="hidden inline-flex items-center gap-2 ml-1 pl-2 border-l border-gray-200">
                    <label class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-800 cursor-pointer">
                        <input type="checkbox" id="cropUseSameAsP1" checked onchange="toggleCurrentCropPageSync(this.checked)" class="rounded border-emerald-300 text-emerald-600">
                        <span>Sama dg Hal. 1</span>
                    </label>
                    <button type="button" onclick="applyCurrentCropToAllPages()"
                        class="px-2 py-0.5 rounded-md bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 text-[10.5px] font-bold cursor-pointer"
                        title="Jadikan ukuran crop halaman ini sebagai standar semua halaman">
                        Samakan Semua
                    </button>
                </div>
            </div>
            <span id="cropCoordsLabel" class="text-[11px] font-mono text-gray-500">Atas: 14% · Bawah: 94%</span>
        </div>

        <div class="flex-1 overflow-auto bg-[#EBEEF3] p-4 flex items-center justify-center min-h-[360px]">
            <canvas id="univCropCanvas" class="rounded-lg shadow-xl bg-white touch-none select-none cursor-crosshair"></canvas>
        </div>

        <div class="px-5 py-3 border-t border-gray-100 flex items-center justify-between gap-3 flex-shrink-0 bg-white">
            <p class="text-[11px] text-gray-500 hidden sm:block">Tip: Centang halaman di atas jika ingin mengambil lebih dari 1 halaman sekaligus dengan Template Kop yang sama.</p>
            <div class="flex items-center gap-2 ml-auto">
                <button type="button" onclick="closeUnivCropModal()" class="px-4 py-2 text-xs font-semibold bg-gray-100 hover:bg-gray-200 rounded-xl cursor-pointer">Batal</button>
                <button type="button" onclick="applyUnivCropToCanvas()"
                    class="inline-flex items-center gap-1.5 px-5 py-2 text-xs font-bold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 rounded-xl shadow-xs cursor-pointer">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span id="btnApplyUnivCropLabel">Tempel Hasil Crop ke Kanvas</span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- PDF.js, jsPDF & JSZip CDNs --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<script>
    const LETTER_DATA = {
        id: {{ $letter->id }},
        mode: @json($studioMode),
        number: @json($letter->letter_number),
        studentName: @json($letter->student_name ?? ''),
        recipient: @json($letter->recipient ?? ''),
        purposeName: @json($letter->letterPurpose?->name ?? ''),
        subject: @json($letter->subject ?? ''),
        formattedDate: @json($formattedLetterDate),
        letterTypeId: {{ $letter->letter_type_id ?? 'null' }},
        primarySigId: {{ $letter->signatory_id ?? 'null' }},
        canUsePrimarySig: {{ $canUsePrimarySig ? 'true' : 'false' }},
        primarySigName: @json($letter->signatory?->name ?? ''),
        primarySigNip: @json($letter->signatory?->nip ?? ''),
        primarySigPosition: @json($letter->signatory?->position ?? ''),
        primarySigUrl: @json($canUsePrimarySig ? $letter->signatory?->signature_url : null),
        archiveUrl: @json(route('letters.update-archive-link', $letter)),
        logExportUrl: @json(route('letters.certificate-studio.log-export', $letter)),
        quickAssetUrl: @json(route('certificate-studio.quick-asset')),
        stampSignaturesUrl: @json(route('certificate-studio.stamp-signatures')),
        convertWordUrl: @json(route('certificate-studio.convert-word')),
        savePresetUrl: @json(route('certificate-studio.presets.store')),
        defaultStampUrl: @json($stamps->first()?->file_url),
        defaultStampName: @json($stamps->first()?->name ?? 'Stempel FEB UNJ'),
        defaultStampOpacity: {{ $stamps->first()?->default_opacity ?? 88 }},
        defaultLetterheadUrl: @json($letterheads->first()?->file_url),
        defaultLetterheadName: @json($letterheads->first()?->name ?? 'Kop Surat FEB UNJ'),
        gdriveReady: {{ $gdriveReady ? 'true' : 'false' }},
    };

    const PRESETS_DATA = @json($presets->keyBy('id'));

    if (typeof pdfjsLib !== 'undefined') {
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    }

    // ── Multi-Slide Studio State ──
    const canvas = document.getElementById('studioCanvas');
    const ctx = canvas.getContext('2d');

    let slides = [];
    let activeSlideIndex = -1;
    let masterNormalizedLayers = [];
    let activeBaseLetterhead = { url: null, name: null };
    const pdfDocsMap = new Map();

    let baseImage = null;
    let baseWidth = 1754;
    let baseHeight = 1240;

    let displayScale = 1;
    let manualZoom = 1;
    let isFocusMode = false;
    let activeLeftTool = 'upload';
    let isFilmstripCollapsed = false;
    let layers = [];
    let selectedLayerId = null;
    let historyStack = [];
    let redoStack = [];

    const LEFT_TOOL_TITLES = {
        upload: LETTER_DATA.mode === 'letter' ? 'Template Kop & Surat' : 'Unggah Sertifikat',
        letterhead: 'Penutup (Whiteout)',
        text: 'Nomor & Teks',
        signature: 'Tanda Tangan',
        stamp: 'Stempel Resmi',
        layers: 'Daftar Layer',
    };

    function switchLeftTool(tabName) {
        const drawer = document.getElementById('leftToolDrawer');
        const titleEl = document.getElementById('leftDrawerTitle');

        if (activeLeftTool === tabName && drawer && !drawer.classList.contains('hidden')) {
            collapseLeftDrawer();
            return;
        }

        activeLeftTool = tabName;
        if (isFocusMode) {
            isFocusMode = false;
            document.getElementById('rightStudioCol')?.classList.remove('hidden');
            const icon = document.getElementById('focusModeIcon');
            if (icon) {
                icon.setAttribute('data-lucide', 'maximize-2');
                if (typeof lucide !== 'undefined') lucide.createIcons();
            }
        }

        drawer?.classList.remove('hidden');
        if (titleEl) titleEl.textContent = LEFT_TOOL_TITLES[tabName] || 'Alat Studio';

        document.querySelectorAll('[id^="toolPanel-"]').forEach(el => {
            el.classList.toggle('hidden', el.id !== `toolPanel-${tabName}`);
        });

        document.querySelectorAll('[data-tool-tab]').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.toolTab === tabName);
        });

        if (baseImage) requestAnimationFrame(fitCanvasToWrapper);
    }

    function collapseLeftDrawer() {
        document.getElementById('leftToolDrawer')?.classList.add('hidden');
        document.querySelectorAll('[data-tool-tab]').forEach(btn => btn.classList.remove('active'));
        if (baseImage) requestAnimationFrame(fitCanvasToWrapper);
    }

    function togglePresetMenu() {
        closeExportMenu();
        closeShortcutPopover();
        document.getElementById('presetPopoverMenu')?.classList.toggle('hidden');
    }
    function closePresetMenu() {
        document.getElementById('presetPopoverMenu')?.classList.add('hidden');
    }

    function toggleExportMenu() {
        closePresetMenu();
        closeShortcutPopover();
        document.getElementById('exportPopoverMenu')?.classList.toggle('hidden');
    }
    function closeExportMenu() {
        document.getElementById('exportPopoverMenu')?.classList.add('hidden');
    }

    function toggleShortcutPopover() {
        closePresetMenu();
        closeExportMenu();
        document.getElementById('shortcutPopoverMenu')?.classList.toggle('hidden');
    }
    function closeShortcutPopover() {
        document.getElementById('shortcutPopoverMenu')?.classList.add('hidden');
    }

    function toggleFilmstripTray() {
        isFilmstripCollapsed = !isFilmstripCollapsed;
        document.getElementById('filmstripContainer')?.classList.toggle('hidden', isFilmstripCollapsed);
        const icon = document.getElementById('filmstripToggleIcon');
        if (icon) {
            icon.setAttribute('data-lucide', isFilmstripCollapsed ? 'chevron-up' : 'chevron-down');
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }
        if (baseImage) requestAnimationFrame(fitCanvasToWrapper);
    }

    let dragState = null;
    let snapGuides = { x: null, y: null };
    const imageCache = new Map();
    const cleanedCache = new Map();

    function uid(prefix = 'lyr_') {
        return prefix + Date.now().toString(36) + '_' + Math.random().toString(36).slice(2, 6);
    }

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

    function setBatchProgress(visible, title = '', text = '', percent = 0) {
        const modal = document.getElementById('batchProgressModal');
        if (!modal) return;
        if (!visible) {
            modal.classList.add('hidden');
            return;
        }
        modal.classList.remove('hidden');
        document.getElementById('batchProgressTitle').textContent = title;
        document.getElementById('batchProgressText').textContent = text;
        document.getElementById('batchProgressBar').style.width = `${Math.min(100, Math.max(0, percent))}%`;
    }

    // ── Focus Mode ──
    function toggleFocusMode() {
        isFocusMode = !isFocusMode;
        const drawer = document.getElementById('leftToolDrawer');
        const right = document.getElementById('rightStudioCol');
        const icon = document.getElementById('focusModeIcon');

        drawer?.classList.toggle('hidden', isFocusMode);
        right?.classList.toggle('hidden', isFocusMode);

        if (isFocusMode) {
            document.querySelectorAll('[data-tool-tab]').forEach(btn => btn.classList.remove('active'));
        } else if (activeLeftTool) {
            document.querySelectorAll('[data-tool-tab]').forEach(btn => {
                btn.classList.toggle('active', btn.dataset.toolTab === activeLeftTool);
            });
        }

        if (icon) {
            icon.setAttribute('data-lucide', isFocusMode ? 'minimize-2' : 'maximize-2');
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }
        if (baseImage) requestAnimationFrame(fitCanvasToWrapper);
    }

    // ── Normalization & Sync Master Helpers ──
    function normalizeLayers(pixelLayers, w, h) {
        const refW = w || 1754;
        const refH = h || 1240;
        return pixelLayers.map(l => ({
            id: l.id,
            type: l.type,
            role: l.role,
            name: l.name,
            locked: !!l.locked,
            text: l.text || '',
            xPct: l.x / refW,
            yPct: l.y / refH,
            fontSizePct: l.fontSize ? l.fontSize / refH : null,
            fontFamily: l.fontFamily || null,
            bold: !!l.bold,
            italic: !!l.italic,
            underline: !!l.underline,
            strikethrough: !!l.strikethrough,
            uppercase: !!l.uppercase,
            letterSpacing: l.letterSpacing ?? 0,
            textShadow: !!l.textShadow,
            color: l.color || (l.type === 'whiteout' ? '#ffffff' : '#111827'),
            bgFill: !!l.bgFill,
            bgColor: l.bgColor || '#ffffff',
            bgMinWidthPct: l.bgMinWidthPct || 0,
            align: l.align || 'center',
            widthPct: l.width ? l.width / refW : null,
            heightPct: l.height ? l.height / refH : null,
            aspect: l.aspect || 1,
            rotation: l.rotation || 0,
            opacity: l.opacity ?? 100,
            removeBg: !!l.removeBg,
            bgThreshold: l.bgThreshold || 225,
            whiteBacking: !!l.whiteBacking,
            signatoryId: l.signatoryId || null,
            url: l.url || null,
        }));
    }

    function materializeLayers(normLayers, w, h) {
        const refW = w || 1754;
        const refH = h || 1240;
        return (normLayers || []).map(n => {
            if (n.type === 'whiteout') {
                return {
                    id: n.id || uid('wht_'),
                    type: 'whiteout',
                    role: n.role || 'whiteout',
                    name: n.name || 'Kotak Penutup',
                    locked: !!n.locked,
                    x: refW * (n.xPct ?? 0.5),
                    y: refH * (n.yPct ?? 0.5),
                    width: Math.max(20, refW * (n.widthPct ?? 0.35)),
                    height: Math.max(12, refH * (n.heightPct ?? 0.05)),
                    color: n.color || '#ffffff',
                    opacity: n.opacity ?? 100,
                    rotation: n.rotation || 0,
                };
            }
            if (n.type === 'text') {
                return {
                    id: n.id || uid(),
                    type: 'text',
                    role: n.role || 'number',
                    name: n.name || 'Teks',
                    locked: !!n.locked,
                    text: n.text || '',
                    x: refW * (n.xPct ?? 0.5),
                    y: refH * (n.yPct ?? 0.308),
                    fontSize: Math.max(14, Math.round(refH * (n.fontSizePct ?? 0.025))),
                    fontFamily: n.fontFamily || 'Times New Roman, Liberation Serif, serif',
                    bold: !!n.bold,
                    italic: !!n.italic,
                    underline: !!n.underline,
                    strikethrough: !!n.strikethrough,
                    uppercase: !!n.uppercase,
                    letterSpacing: n.letterSpacing ?? 0,
                    textShadow: !!n.textShadow,
                    color: n.color || '#111827',
                    bgFill: !!n.bgFill,
                    bgColor: n.bgColor || '#ffffff',
                    bgMinWidthPct: n.bgMinWidthPct || 0,
                    align: n.align || 'center',
                    opacity: n.opacity ?? 100,
                    rotation: n.rotation || 0,
                };
            }
            const imgW = refW * (n.widthPct ?? 0.18);
            const aspect = n.aspect || 1;
            return {
                id: n.id || uid(),
                type: 'image',
                role: n.role,
                name: n.name || 'Aset',
                locked: !!n.locked,
                url: n.url,
                x: refW * (n.xPct ?? 0.5),
                y: refH * (n.yPct ?? 0.74),
                width: imgW,
                height: imgW * aspect,
                aspect: aspect,
                rotation: n.rotation || 0,
                opacity: n.opacity ?? 100,
                removeBg: !!n.removeBg,
                bgThreshold: n.bgThreshold || 225,
                whiteBacking: !!n.whiteBacking,
                signatoryId: n.signatoryId || null,
            };
        });
    }

    function getActiveSlide() {
        return activeSlideIndex >= 0 ? slides[activeSlideIndex] : null;
    }

    function syncCurrentLayersToState() {
        const slide = getActiveSlide();
        const norm = normalizeLayers(layers, baseWidth, baseHeight);
        if (!slide || slide.useMasterLayout) {
            masterNormalizedLayers = norm;
        } else {
            slide.customNormalizedLayers = norm;
        }
    }

    function updateHistoryButtons() {
        const btnUndo = document.getElementById('btnUndo');
        const btnRedo = document.getElementById('btnRedo');
        if (btnUndo) btnUndo.disabled = historyStack.length <= 1;
        if (btnRedo) btnRedo.disabled = redoStack.length === 0;
    }

    function pushHistory() {
        syncCurrentLayersToState();
        const snapshot = JSON.stringify(layers);
        if (historyStack.length === 0 || historyStack[historyStack.length - 1] !== snapshot) {
            historyStack.push(snapshot);
            if (historyStack.length > 35) historyStack.shift();
            redoStack = [];
        }
        updateHistoryButtons();
    }

    function undoCanvas() {
        if (historyStack.length <= 1) return;
        const current = historyStack.pop();
        redoStack.push(current);
        layers = JSON.parse(historyStack[historyStack.length - 1]);
        syncCurrentLayersToState();
        if (!layers.find(l => l.id === selectedLayerId)) {
            selectedLayerId = layers[layers.length - 1]?.id || null;
        }
        updateHistoryButtons();
        preloadLayerImages().then(() => {
            renderCanvas();
            syncInspector();
            renderLayerList();
        });
    }

    function redoCanvas() {
        if (redoStack.length === 0) return;
        const nextSnap = redoStack.pop();
        historyStack.push(nextSnap);
        layers = JSON.parse(nextSnap);
        syncCurrentLayersToState();
        if (!layers.find(l => l.id === selectedLayerId)) {
            selectedLayerId = layers[layers.length - 1]?.id || null;
        }
        updateHistoryButtons();
        preloadLayerImages().then(() => {
            renderCanvas();
            syncInspector();
            renderLayerList();
        });
    }

    // ── Multi-File & Multi-Page PDF Uploader ──
    async function readImageFileAsDataUrl(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onload = (e) => {
                const img = new Image();
                img.onload = () => resolve({ dataUrl: e.target.result, img });
                img.onerror = reject;
                img.src = e.target.result;
            };
            reader.onerror = reject;
            reader.readAsDataURL(file);
        });
    }

    async function handleBaseFilesUpload(fileList, append = false) {
        const files = Array.from(fileList || []);
        if (!files.length) return;

        if (!append) {
            slides = [];
            pdfDocsMap.clear();
            activeSlideIndex = -1;
        }

        const startIdx = slides.length;
        let addedCount = 0;

        setBatchProgress(true, 'Memuat Berkas Sertifikat...', `Memproses ${files.length} berkas...`, 10);

        try {
            for (let fIdx = 0; fIdx < files.length; fIdx++) {
                const file = files[fIdx];
                const ext = file.name.split('.').pop().toLowerCase();
                const cleanBase = file.name.replace(/\.[^/.]+$/, '');
                const pct = 10 + Math.round(((fIdx + 1) / files.length) * 80);

                if (ext === 'pdf') {
                    setBatchProgress(true, 'Membaca Dokumen PDF...', `Mengekstrak halaman dari "${file.name}"...`, pct);
                    const buffer = await file.arrayBuffer();
                    const pdfDoc = await pdfjsLib.getDocument({ data: buffer }).promise;
                    const docKey = uid('pdf_');
                    pdfDocsMap.set(docKey, pdfDoc);

                    for (let p = 1; p <= pdfDoc.numPages; p++) {
                        setBatchProgress(true, 'Membaca Dokumen PDF...', `"${file.name}" — Halaman ${p} dari ${pdfDoc.numPages}...`, pct);
                        const page = await pdfDoc.getPage(p);
                        const vpFull = page.getViewport({ scale: 3.0 });
                        const vpThumb = page.getViewport({ scale: 0.35 });

                        const thumbCanvas = document.createElement('canvas');
                        thumbCanvas.width = Math.max(1, Math.round(vpThumb.width));
                        thumbCanvas.height = Math.max(1, Math.round(vpThumb.height));
                        await page.render({ canvasContext: thumbCanvas.getContext('2d'), viewport: vpThumb }).promise;

                        const defaultUseMaster = LETTER_DATA.mode !== 'letter';
                        slides.push({
                            id: uid('sld_'),
                            label: pdfDoc.numPages > 1 ? `${cleanBase} (Hal. ${p})` : cleanBase,
                            fileName: pdfDoc.numPages > 1 ? `${cleanBase}_hal${p}` : cleanBase,
                            sourceType: 'pdf-page',
                            pdfDocId: docKey,
                            pageNum: p,
                            thumbUrl: thumbCanvas.toDataURL('image/jpeg', 0.8),
                            width: Math.round(vpFull.width),
                            height: Math.round(vpFull.height),
                            useMasterLayout: defaultUseMaster,
                            customNormalizedLayers: defaultUseMaster ? null : [],
                        });
                        addedCount++;
                    }
                } else if (['png', 'jpg', 'jpeg'].includes(ext)) {
                    setBatchProgress(true, 'Memuat Gambar...', `"${file.name}"...`, pct);
                    const { dataUrl, img } = await readImageFileAsDataUrl(file);
                    const w = img.naturalWidth || img.width;
                    const h = img.naturalHeight || img.height;

                    const tmp = document.createElement('canvas');
                    tmp.width = Math.min(w, 260);
                    tmp.height = Math.max(1, Math.round(h * (tmp.width / w)));
                    tmp.getContext('2d').drawImage(img, 0, 0, tmp.width, tmp.height);

                    const defaultUseMasterImg = LETTER_DATA.mode !== 'letter';
                    slides.push({
                        id: uid('sld_'),
                        label: cleanBase,
                        fileName: cleanBase,
                        sourceType: 'image',
                        dataUrl: dataUrl,
                        thumbUrl: tmp.toDataURL('image/jpeg', 0.8),
                        width: w,
                        height: h,
                        useMasterLayout: defaultUseMasterImg,
                        customNormalizedLayers: defaultUseMasterImg ? null : [],
                    });
                    addedCount++;
                }
            }

            setBatchProgress(false);

            if (addedCount > 0) {
                const targetIndex = append ? startIdx : 0;
                await switchSlide(targetIndex);
                if (!append && activeLeftTool === 'upload') {
                    switchLeftTool(LETTER_DATA.mode === 'letter' ? 'letterhead' : 'text');
                }
                showStudioToast(`${addedCount} halaman ${LETTER_DATA.mode === 'letter' ? 'surat' : 'sertifikat'} berhasil dimuat!`);
            } else {
                showStudioToast('Tidak ada berkas valid (PDF/PNG/JPG) yang dimuat.', true);
            }
        } catch (err) {
            console.error(err);
            setBatchProgress(false);
            showStudioToast('Gagal membaca berkas sertifikat. Pastikan file tidak rusak atau terpassword.', true);
        } finally {
            document.getElementById('baseFileInput').value = '';
            document.getElementById('appendFileInput').value = '';
        }
    }

    async function loadSlideHighResImage(slide) {
        if (slide.sourceType === 'image') {
            return new Promise((resolve, reject) => {
                const img = new Image();
                img.onload = () => resolve(img);
                img.onerror = reject;
                img.src = slide.dataUrl;
            });
        }

        if (slide._cachedHiResImg) return slide._cachedHiResImg;

        const pdfDoc = pdfDocsMap.get(slide.pdfDocId);
        const page = await pdfDoc.getPage(slide.pageNum);
        const viewport = page.getViewport({ scale: 3.0 });
        const off = document.createElement('canvas');
        off.width = Math.round(viewport.width);
        off.height = Math.round(viewport.height);
        await page.render({ canvasContext: off.getContext('2d'), viewport }).promise;

        const img = new Image();
        await new Promise((resolve) => {
            img.onload = resolve;
            img.src = off.toDataURL('image/png');
        });
        slide._cachedHiResImg = img;
        return img;
    }

    async function switchSlide(index) {
        if (index < 0 || index >= slides.length) return;

        if (activeSlideIndex >= 0 && slides[activeSlideIndex] && baseImage) {
            syncCurrentLayersToState();
        }

        const loadingEl = document.getElementById('stageLoadingOverlay');
        loadingEl?.classList.remove('hidden');

        try {
            activeSlideIndex = index;
            const slide = slides[activeSlideIndex];
            const img = await loadSlideHighResImage(slide);

            baseImage = img;
            baseWidth = slide.width || img.naturalWidth || img.width;
            baseHeight = slide.height || img.naturalHeight || img.height;

            const activeNorm = slide.useMasterLayout
                ? masterNormalizedLayers
                : (slide.customNormalizedLayers || masterNormalizedLayers);

            layers = materializeLayers(activeNorm, baseWidth, baseHeight);
            selectedLayerId = layers[0]?.id || null;
            historyStack = [JSON.stringify(layers)];
            redoStack = [];
            updateHistoryButtons();

            await preloadLayerImages();
            activateStage();
            renderFilmstrip();
            updateMultiSlideUI();
            syncInspector();
            renderLayerList();
        } finally {
            loadingEl?.classList.add('hidden');
        }
    }

    function moveSlideOrder(index, delta, evt) {
        if (evt) evt.stopPropagation();
        const target = index + delta;
        if (target < 0 || target >= slides.length) return;

        const currentId = slides[activeSlideIndex]?.id;
        [slides[index], slides[target]] = [slides[target], slides[index]];
        activeSlideIndex = slides.findIndex(s => s.id === currentId);
        renderFilmstrip();
        updateMultiSlideUI();
    }

    function openRenameSlideModal(index, evt) {
        if (evt) evt.stopPropagation();
        const slide = slides[index];
        if (!slide) return;
        document.getElementById('renameSlideIndex').value = index;
        document.getElementById('renameSlideInput').value = slide.fileName || slide.label;
        document.getElementById('renameSlideModal').classList.remove('hidden');
        document.getElementById('renameSlideInput').focus();
    }
    function closeRenameSlideModal() {
        document.getElementById('renameSlideModal').classList.add('hidden');
    }
    function submitRenameSlide(e) {
        e.preventDefault();
        const idx = parseInt(document.getElementById('renameSlideIndex').value);
        const newName = document.getElementById('renameSlideInput').value.trim();
        if (!newName || !slides[idx]) return;
        slides[idx].label = newName;
        slides[idx].fileName = newName;
        closeRenameSlideModal();
        renderFilmstrip();
        updateMultiSlideUI();
        showStudioToast(`Nama sertifikat diubah menjadi "${newName}".`);
    }

    function removeSlide(index, evt) {
        if (evt) evt.stopPropagation();
        if (index < 0 || index >= slides.length) return;

        slides.splice(index, 1);
        if (slides.length === 0) {
            resetAllSlides(false);
            return;
        }

        const nextIdx = Math.min(index, slides.length - 1);
        activeSlideIndex = -1;
        switchSlide(nextIdx);
    }

    function resetAllSlides(confirmAsk = true) {
        if (confirmAsk && slides.length > 0 && !confirm('Kosongkan seluruh antrean sertifikat yang sudah diunggah?')) {
            return;
        }
        slides = [];
        activeSlideIndex = -1;
        activeBaseLetterhead = { url: null, name: null };
        baseImage = null;
        layers = [];
        masterNormalizedLayers = [];
        selectedLayerId = null;
        historyStack = [];
        redoStack = [];
        updateHistoryButtons();
        pdfDocsMap.clear();

        document.getElementById('emptyStageState')?.classList.remove('hidden');
        canvas.classList.add('hidden');
        document.getElementById('baseDimsBadge')?.classList.add('hidden');
        document.getElementById('baseFileNameLabel').textContent = 'Pilih atau Tarik File';
        if (activeLeftTool !== 'upload') switchLeftTool('upload');

        updateMultiSlideUI();
        renderFilmstrip();
        syncInspector();
        renderLayerList();
    }

    function toggleSlideCustomMode() {
        const slide = getActiveSlide();
        if (!slide) return;

        if (slide.useMasterLayout) {
            slide.customNormalizedLayers = normalizeLayers(layers, baseWidth, baseHeight);
            slide.useMasterLayout = false;
            showStudioToast(`Halaman ${activeSlideIndex + 1} sekarang mode Custom.`);
        } else {
            slide.useMasterLayout = true;
            slide.customNormalizedLayers = null;
            layers = materializeLayers(masterNormalizedLayers, baseWidth, baseHeight);
            selectedLayerId = layers[0]?.id || null;
            historyStack = [JSON.stringify(layers)];
            redoStack = [];
            updateHistoryButtons();
            preloadLayerImages().then(() => {
                renderCanvas();
                syncInspector();
                renderLayerList();
            });
            showStudioToast(`Halaman ${activeSlideIndex + 1} dikembalikan ke Master Layout.`);
        }

        renderFilmstrip();
        updateMultiSlideUI();
    }

    function applyCurrentSlideLayoutToAll() {
        const norm = normalizeLayers(layers, baseWidth, baseHeight);
        masterNormalizedLayers = norm;
        slides.forEach(s => {
            s.useMasterLayout = true;
            s.customNormalizedLayers = null;
        });
        renderFilmstrip();
        updateMultiSlideUI();
        showStudioToast(`Tata letak halaman ini berhasil disamakan ke seluruh ${slides.length} sertifikat!`);
    }

    function updateMultiSlideUI() {
        const count = slides.length;
        const slide = getActiveSlide();

        const baseQueueActions = document.getElementById('baseQueueActions');
        const filmstripCard = document.getElementById('filmstripCard');
        const syncModeBar = document.getElementById('syncModeBar');
        const singleExportSection = document.getElementById('exportSingleSection');
        const batchExportSection = document.getElementById('exportBatchSection');
        const exportBtnLabel = document.getElementById('exportBtnLabel');
        const topQueueBadge = document.getElementById('topQueueBadge');
        const gdriveBtnLabel = document.getElementById('btnArchiveGdriveLabel');

        if (count > 0) {
            baseQueueActions?.classList.remove('hidden');
            filmstripCard?.classList.remove('hidden');
            document.getElementById('baseFileNameLabel').textContent = slide?.label || `${count} Sertifikat`;
        } else {
            baseQueueActions?.classList.add('hidden');
            filmstripCard?.classList.add('hidden');
        }

        if (count > 1) {
            syncModeBar?.classList.remove('hidden');
            singleExportSection?.classList.add('hidden');
            batchExportSection?.classList.remove('hidden');
            topQueueBadge?.classList.remove('hidden');
            if (topQueueBadge) topQueueBadge.textContent = `${count} Hal.`;
            if (exportBtnLabel) exportBtnLabel.textContent = `Export (${count})`;
            document.querySelectorAll('.batchCountLabel').forEach(el => el.textContent = count);
            if (gdriveBtnLabel) gdriveBtnLabel.textContent = `Arsipkan (${count})`;
        } else {
            syncModeBar?.classList.add('hidden');
            singleExportSection?.classList.remove('hidden');
            batchExportSection?.classList.add('hidden');
            topQueueBadge?.classList.add('hidden');
            if (exportBtnLabel) exportBtnLabel.textContent = 'Export';
            if (gdriveBtnLabel) gdriveBtnLabel.textContent = 'Simpan GDrive';
        }

        if (slide && count > 1) {
            const pill = document.getElementById('syncModePill');
            const btnCustom = document.getElementById('btnToggleCustomMode');
            const btnSyncAll = document.getElementById('btnSyncAllFromCurrent');

            if (slide.useMasterLayout) {
                pill.className = 'inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/80 flex-shrink-0';
                pill.innerHTML = `<i data-lucide="link-2" class="w-3 h-3"></i><span>Sinkron (${count} Hal.)</span>`;
                btnCustom.textContent = 'Edit Khusus Hal. Ini';
                btnCustom.className = 'px-2.5 py-1 rounded-full bg-gray-50 hover:bg-amber-50 text-amber-700 border border-amber-200/80 text-[11px] font-bold transition-colors cursor-pointer';
                btnSyncAll?.classList.add('hidden');
            } else {
                pill.className = 'inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200/80 flex-shrink-0';
                pill.innerHTML = `<i data-lucide="pencil" class="w-3 h-3"></i><span>Custom Hal. ${activeSlideIndex + 1}</span>`;
                btnCustom.textContent = 'Kembalikan ke Master';
                btnCustom.className = 'px-2.5 py-1 rounded-full bg-gray-50 hover:bg-emerald-50 text-emerald-700 border border-emerald-200/80 text-[11px] font-bold transition-colors cursor-pointer';
                btnSyncAll?.classList.remove('hidden');
            }
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }

        const countBadge = document.getElementById('filmstripCountBadge');
        const pagePos = document.getElementById('filmstripPagePos');
        const btnPrev = document.getElementById('btnPrevSlide');
        const btnNext = document.getElementById('btnNextSlide');
        if (countBadge) countBadge.textContent = `${count} hal.`;
        if (pagePos) pagePos.textContent = count > 0 ? `${activeSlideIndex + 1} / ${count}` : '0 / 0';
        if (btnPrev) btnPrev.disabled = activeSlideIndex <= 0;
        if (btnNext) btnNext.disabled = activeSlideIndex >= count - 1;
        if (baseImage) requestAnimationFrame(fitCanvasToWrapper);
    }

    function renderFilmstrip() {
        const container = document.getElementById('filmstripContainer');
        if (!container) return;
        if (slides.length === 0) {
            container.innerHTML = '';
            return;
        }

        const cardsHtml = slides.map((s, idx) => {
            const isActive = idx === activeSlideIndex;
            const dotClass = s.useMasterLayout ? 'bg-emerald-500' : 'bg-amber-500';
            const statusText = s.useMasterLayout ? 'Sinkron' : 'Custom';

            return `
                <div onclick="switchSlide(${idx})"
                    class="group relative w-28 flex-shrink-0 rounded-xl border p-1 transition-all cursor-pointer ${isActive ? 'border-[#FF4D00] ring-2 ring-orange-500/20 bg-orange-50/40 shadow-2xs' : 'border-gray-200 bg-gray-50/70 hover:bg-white hover:border-gray-300'}">
                    <div class="relative h-14 rounded-lg overflow-hidden bg-white border border-gray-100 flex items-center justify-center">
                        <img src="${s.thumbUrl}" alt="${s.label}" class="max-h-full max-w-full object-contain">
                        <span class="absolute top-1 left-1 px-1.5 py-0.5 rounded text-[9px] font-mono font-bold ${isActive ? 'bg-[#FF4D00] text-white' : 'bg-gray-900/75 text-white'}">${idx + 1}</span>
                        <div class="opacity-0 group-hover:opacity-100 absolute top-1 right-1 flex items-center gap-0.5 transition-opacity">
                            ${idx > 0 ? `<button type="button" onclick="moveSlideOrder(${idx}, -1, event)" class="w-4 h-4 rounded-full bg-gray-900/80 text-white flex items-center justify-center text-[10px] font-bold cursor-pointer" title="Geser ke kiri">&lsaquo;</button>` : ''}
                            ${idx < slides.length - 1 ? `<button type="button" onclick="moveSlideOrder(${idx}, 1, event)" class="w-4 h-4 rounded-full bg-gray-900/80 text-white flex items-center justify-center text-[10px] font-bold cursor-pointer" title="Geser ke kanan">&rsaquo;</button>` : ''}
                            <button type="button" onclick="removeSlide(${idx}, event)" class="w-4 h-4 rounded-full bg-rose-600 text-white flex items-center justify-center text-[10px] font-bold cursor-pointer" title="Hapus">&times;</button>
                        </div>
                    </div>
                    <div class="mt-1 px-0.5 flex items-center justify-between gap-1">
                        <span class="w-1.5 h-1.5 rounded-full ${dotClass} flex-shrink-0" title="${statusText}"></span>
                        <p class="text-[10px] font-bold text-gray-800 truncate flex-1" title="${s.label}">${s.label}</p>
                        <button type="button" onclick="openRenameSlideModal(${idx}, event)" class="text-gray-400 hover:text-[#FF4D00] cursor-pointer" title="Ubah nama file">
                            <i data-lucide="pencil" class="w-2.5 h-2.5"></i>
                        </button>
                    </div>
                </div>
            `;
        }).join('');

        const addCardHtml = `
            <button type="button" onclick="document.getElementById('appendFileInput').click()"
                class="w-20 flex-shrink-0 rounded-xl border-2 border-dashed border-gray-200 hover:border-[#FF4D00] bg-gray-50/60 hover:bg-orange-50/40 flex flex-col items-center justify-center gap-1 p-2 text-gray-500 hover:text-[#FF4D00] transition-all cursor-pointer">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span class="text-[10px] font-bold">Tambah</span>
            </button>
        `;

        container.innerHTML = cardsHtml + addCardHtml;
        if (typeof lucide !== 'undefined') lucide.createIcons();
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
        renderCanvas();
    }

    function fitCanvasToWrapper() {
        const wrapper = document.getElementById('stageWrapper');
        const availW = Math.max(280, (wrapper?.clientWidth || 720) - 48);
        const availH = Math.max(300, (wrapper?.clientHeight || 520) - 44);
        const fitScale = Math.min(availW / baseWidth, availH / baseHeight, 1);
        displayScale = fitScale * manualZoom;

        if (canvas.width !== baseWidth) canvas.width = baseWidth;
        if (canvas.height !== baseHeight) canvas.height = baseHeight;
        canvas.style.width = Math.round(baseWidth * displayScale) + 'px';
        canvas.style.height = Math.round(baseHeight * displayScale) + 'px';

        if (baseImage) renderCanvas();
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

    function ensureBaseReady() {
        if (!baseImage || slides.length === 0) {
            showStudioToast('Upload base sertifikat (PDF/PNG/JPG) terlebih dahulu!', true);
            return false;
        }
        return true;
    }

    // ── Layer Management ──
    function addTextLayer(textValue, xPct = 0.5, yPct = 0.308, role = 'number', name = 'Teks', extraOpts = {}) {
        if (!ensureBaseReady()) return;
        const defaultScale = role === 'number'
            ? (LETTER_DATA.mode === 'letter' ? 0.0145 : 0.025)
            : (LETTER_DATA.mode === 'letter' ? 0.0145 : 0.022);
        const fontSize = extraOpts.fontSize || Math.max(16, Math.round(baseHeight * defaultScale));
        const layer = {
            id: uid(),
            type: 'text',
            role: role,
            name: name,
            locked: false,
            text: textValue,
            x: baseWidth * xPct,
            y: baseHeight * yPct,
            fontSize: fontSize,
            fontFamily: extraOpts.fontFamily || 'Times New Roman, Liberation Serif, serif',
            bold: !!extraOpts.bold,
            italic: !!extraOpts.italic,
            underline: !!extraOpts.underline,
            strikethrough: !!extraOpts.strikethrough,
            uppercase: !!extraOpts.uppercase,
            letterSpacing: extraOpts.letterSpacing ?? 0,
            textShadow: !!extraOpts.textShadow,
            color: extraOpts.color || '#111827',
            bgFill: !!extraOpts.bgFill,
            bgColor: extraOpts.bgColor || '#ffffff',
            bgMinWidthPct: extraOpts.bgMinWidthPct || 0,
            align: extraOpts.align || (LETTER_DATA.mode === 'letter' ? 'left' : 'center'),
            opacity: extraOpts.opacity ?? 100,
            rotation: 0,
        };
        layers.push(layer);
        selectedLayerId = layer.id;
        pushHistory();
        renderCanvas();
        syncInspector();
        renderLayerList();
        return layer;
    }

    function addWhiteoutLayer(xPct = 0.5, yPct = 0.25, wPct = 0.35, hPct = 0.04, name = 'Kotak Penutup', role = 'whiteout') {
        if (!ensureBaseReady()) return null;
        const layer = {
            id: uid('wht_'),
            type: 'whiteout',
            role: role,
            name: name,
            locked: false,
            x: baseWidth * xPct,
            y: baseHeight * yPct,
            width: Math.max(24, Math.round(baseWidth * wPct)),
            height: Math.max(14, Math.round(baseHeight * hPct)),
            color: '#ffffff',
            opacity: 100,
            rotation: 0,
        };
        layers.push(layer);
        selectedLayerId = layer.id;
        pushHistory();
        renderCanvas();
        syncInspector();
        renderLayerList();
        return layer;
    }

    async function addLetterheadLayer(url, label = 'Kop Surat FEB UNJ') {
        if (!ensureBaseReady()) return null;
        if (!url) {
            showStudioToast('Gambar Kop Surat belum tersedia. Upload terlebih dahulu.', true);
            return null;
        }
        try {
            const img = await loadCachedImage(url);
            const aspect = (img.naturalHeight || 1) / (img.naturalWidth || 1);
            const widthPx = baseWidth * 1.0;
            const heightPx = widthPx * aspect;

            const layer = {
                id: uid('kop_'),
                type: 'image',
                role: 'letterhead',
                name: label,
                locked: false,
                url: url,
                x: baseWidth * 0.5,
                y: heightPx / 2,
                width: widthPx,
                height: heightPx,
                aspect: aspect,
                rotation: 0,
                opacity: 100,
                removeBg: false,
                bgThreshold: 225,
                whiteBacking: true,
            };

            layers.push(layer);
            selectedLayerId = layer.id;
            pushHistory();
            renderCanvas();
            syncInspector();
            renderLayerList();
            showStudioToast(`Kop "${label}" ditempelkan di bagian atas halaman.`);
            return layer;
        } catch (err) {
            showStudioToast('Gagal memuat gambar Kop Surat.', true);
            return null;
        }
    }

    function addNumberWithWhiteout(withPrefix = false) {
        const textVal = withPrefix ? ('Nomor: ' + LETTER_DATA.number) : LETTER_DATA.number;
        const existing = layers.find(l => l.type === 'text' && l.role === 'number');
        if (existing) {
            if (existing.locked) {
                showStudioToast('Layer Nomor sedang dikunci.', true);
                return;
            }
            existing.text = textVal;
            existing.bgFill = true;
            existing.bgColor = existing.bgColor || '#ffffff';
            existing.bgMinWidthPct = existing.bgMinWidthPct || 34;
            selectedLayerId = existing.id;
            pushHistory();
            renderCanvas();
            syncInspector();
            renderLayerList();
            return;
        }
        addTextLayer(textVal, 0.225, 0.175, 'number', 'Nomor Surat (Latar Putih)', {
            bgFill: true,
            bgColor: '#ffffff',
            bgMinWidthPct: 36,
            color: '#000000',
            align: 'left',
            fontSize: Math.max(14, Math.round(baseHeight * 0.0136)),
        });
    }

    async function addFullSignatureBlock(sigOverride = null) {
        if (!ensureBaseReady()) return;

        const sigId = sigOverride?.id ?? LETTER_DATA.primarySigId;
        const sigName = sigOverride?.name ?? LETTER_DATA.primarySigName;
        const sigNip = sigOverride?.nip ?? LETTER_DATA.primarySigNip;
        const sigPos = sigOverride?.position ?? LETTER_DATA.primarySigPosition;
        const sigUrl = sigOverride?.url ?? LETTER_DATA.primarySigUrl;
        const bodyFontSize = Math.max(14, Math.round(baseHeight * 0.0132));

        // 1. Kotak Penutup TTD Lama di kanan bawah (aman dari area Tembusan di kiri)
        addWhiteoutLayer(0.71, 0.775, 0.44, 0.185, 'Penutup TTD Lama', 'whiteout_sig');

        // 2. Jabatan Pejabat (tanpa baris tanggal karena tanggal sudah di kanan atas)
        if (sigPos) {
            addTextLayer(sigPos, 0.505, 0.712, 'sig_block_date', 'Jabatan TTD', {
                align: 'left',
                color: '#000000',
                fontSize: bodyFontSize,
            });
        }

        // 3. Gambar TTD Digital (jika ada & diizinkan)
        if (sigUrl) {
            await addImageLayer('signature', sigUrl, sigName || 'TTD Pejabat', 100, {
                wPct: 0.17,
                xPct: 0.62,
                yPct: 0.77,
            }, sigId);
        }

        // 4. Nama Lengkap (Bold) & NIP (Regular)
        if (sigName) {
            addTextLayer(sigName, 0.505, 0.822, 'sig_block_name', 'Nama Pejabat TTD', {
                align: 'left',
                bold: true,
                color: '#000000',
                fontSize: bodyFontSize,
            });
        }
        if (sigNip) {
            addTextLayer(`NIP : ${sigNip}`, 0.505, 0.848, 'sig_block_nip', 'NIP Pejabat TTD', {
                align: 'left',
                color: '#000000',
                fontSize: bodyFontSize,
            });
        }

        showStudioToast('Blok TTD baru FEB UNJ disusun! Geser sesuai posisi di surat.');
    }

    // ── Template Kop Base & Interactive Multi-Page Crop Modal untuk Surat Universitas ──
    const univCropState = {
        fileName: 'Surat_Universitas',
        pdfDoc: null,
        totalPages: 1,
        activeIdx: 0,
        masterCrop: { top: 0.14, bottom: 0.945, left: 0.03, right: 0.97 },
        pages: [],
        dispScale: 1,
        dragMode: null,
        startY: 0,
        startX: 0,
        origCrop: null,
    };

    function getActiveCropPage() {
        return univCropState.pages[univCropState.activeIdx] || null;
    }

    function getCropForPage(pageObj) {
        if (!pageObj) return { ...univCropState.masterCrop };
        return pageObj.useSameCrop
            ? { ...univCropState.masterCrop }
            : { ...(pageObj.customCrop || univCropState.masterCrop) };
    }

    function setActivePageCrop(nextRect, refreshUI = true) {
        const idx = univCropState.activeIdx;
        const pageObj = univCropState.pages[idx];
        if (!pageObj) return;

        if (idx === 0) {
            univCropState.masterCrop = { ...nextRect };
            pageObj.useSameCrop = true;
        } else {
            pageObj.useSameCrop = false;
            pageObj.customCrop = { ...nextRect };
        }

        if (refreshUI) {
            updateUnivCropPageUI();
            renderCropPagesBar();
        }
    }

    async function selectLetterheadAsBase(url, label = 'Kop Surat FEB UNJ') {
        if (!url) return false;
        const loadingEl = document.getElementById('stageLoadingOverlay');
        loadingEl?.classList.remove('hidden');
        try {
            const img = await loadCachedImage(url);
            const w = img.naturalWidth || img.width || 1654;
            const h = img.naturalHeight || img.height || 2339;

            const tmp = document.createElement('canvas');
            tmp.width = Math.min(w, 260);
            tmp.height = Math.max(1, Math.round(h * (tmp.width / w)));
            tmp.getContext('2d').drawImage(img, 0, 0, tmp.width, tmp.height);
            const thumbUrl = tmp.toDataURL('image/jpeg', 0.82);

            activeBaseLetterhead = { url, name: label };

            if (slides.length === 0) {
                slides.push({
                    id: uid('sld_'),
                    label: label,
                    fileName: label,
                    sourceType: 'image',
                    dataUrl: url,
                    thumbUrl: thumbUrl,
                    baseLetterheadUrl: url,
                    baseLetterheadName: label,
                    width: w,
                    height: h,
                    useMasterLayout: false,
                    customNormalizedLayers: [],
                });
                await switchSlide(0);
            } else {
                syncCurrentLayersToState();
                slides.forEach(s => {
                    s.sourceType = 'image';
                    s.dataUrl = url;
                    s.thumbUrl = thumbUrl;
                    s.baseLetterheadUrl = url;
                    s.baseLetterheadName = label;
                    s.width = w;
                    s.height = h;
                    s.useMasterLayout = false;
                    delete s._cachedHiResImg;
                });
                if (slides.length === 1) slides[0].label = label;
                await switchSlide(activeSlideIndex >= 0 ? activeSlideIndex : 0);
            }

            showStudioToast(`Template "${label}" aktif sebagai Base! Selanjutnya klik "Upload & Crop Isi Surat".`);
            return true;
        } catch (err) {
            showStudioToast('Gagal memuat template Kop Surat.', true);
            return false;
        } finally {
            loadingEl?.classList.add('hidden');
        }
    }

    const convertedWordPdfCache = new Map();
    let isConvertingWord = false;

    async function convertUniversityWordToPdfBuffer(file) {
        const cacheKey = `${file.name}|${file.size}|${file.lastModified}`;
        if (convertedWordPdfCache.has(cacheKey)) {
            return convertedWordPdfCache.get(cacheKey).slice().buffer;
        }

        const fd = new FormData();
        fd.append('document', file);

        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 28000);

        try {
            const resp = await fetch(LETTER_DATA.convertWordUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: fd,
                signal: controller.signal,
            });

            if (!resp.ok) {
                let errMsg = 'Gagal mengonversi dokumen Word. Simpan sebagai PDF di Word lalu unggah PDF-nya.';
                try {
                    const errJson = await resp.json();
                    if (errJson?.message) errMsg = errJson.message;
                } catch {}
                throw new Error(errMsg);
            }

            const pdfBuf = await resp.arrayBuffer();
            if (convertedWordPdfCache.size >= 10) {
                const oldestKey = convertedWordPdfCache.keys().next().value;
                if (oldestKey) convertedWordPdfCache.delete(oldestKey);
            }
            convertedWordPdfCache.set(cacheKey, new Uint8Array(pdfBuf).slice());
            return pdfBuf;
        } catch (err) {
            if (err?.name === 'AbortError') {
                throw new Error('Konversi Word melewati batas waktu. Coba ulangi atau simpan sebagai PDF di Word lalu unggah.');
            }
            throw err;
        } finally {
            clearTimeout(timeoutId);
        }
    }

    async function openUnivCropModalFromFile(file) {
        if (!file) return;
        const inputEl = document.getElementById('univLetterFileInput');
        if (inputEl) inputEl.value = '';

        const ext = (file.name.split('.').pop() || '').toLowerCase();
        const cleanBase = file.name.replace(/\.[^/.]+$/, '') || 'Surat_Universitas';
        const isWordFile = ['doc', 'docx', 'rtf'].includes(ext);

        if (isWordFile) {
            if (isConvertingWord) {
                showStudioToast('Konversi dokumen Word sebelumnya masih berjalan...', true);
                return;
            }
            if (file.size > 5 * 1024 * 1024) {
                showStudioToast('Ukuran file Word maksimal 5 MB. Simpan sebagai PDF jika file lebih besar.', true);
                return;
            }
            isConvertingWord = true;
            setBatchProgress(
                true,
                'Mengonversi Dokumen Word...',
                `Memproses "${file.name}" menjadi PDF via Google Drive (maks. 28 detik)...`,
                25
            );
        } else {
            setBatchProgress(true, 'Membuka Dokumen Universitas...', `Memuat "${file.name}"...`, 35);
        }

        try {
            univCropState.fileName = cleanBase;
            univCropState.masterCrop = { top: 0.14, bottom: 0.945, left: 0.03, right: 0.97 };
            univCropState.activeIdx = 0;

            if (ext === 'pdf' || isWordFile) {
                const buffer = isWordFile
                    ? await convertUniversityWordToPdfBuffer(file)
                    : await file.arrayBuffer();

                if (isWordFile) {
                    setBatchProgress(true, 'Merender Halaman PDF...', `Menyiapkan pratinjau "${file.name}" untuk di-crop...`, 75);
                }

                const pdfDoc = await pdfjsLib.getDocument({ data: buffer }).promise;
                univCropState.pdfDoc = pdfDoc;
                univCropState.totalPages = pdfDoc.numPages;
                univCropState.pages = Array.from({ length: pdfDoc.numPages }, (_, i) => ({
                    pageNum: i + 1,
                    included: true,
                    useSameCrop: true,
                    customCrop: null,
                    canvasCache: null,
                }));
                await ensureCropPageCanvas(0);
            } else if (['png', 'jpg', 'jpeg'].includes(ext)) {
                const { img } = await readImageFileAsDataUrl(file);
                const off = document.createElement('canvas');
                off.width = img.naturalWidth || img.width;
                off.height = img.naturalHeight || img.height;
                off.getContext('2d').drawImage(img, 0, 0);
                univCropState.pdfDoc = null;
                univCropState.totalPages = 1;
                univCropState.pages = [{
                    pageNum: 1,
                    included: true,
                    useSameCrop: true,
                    customCrop: null,
                    canvasCache: off,
                }];
            } else {
                setBatchProgress(false);
                showStudioToast('Format surat harus PDF, Word (.doc/.docx/.rtf), PNG, atau JPG.', true);
                return;
            }

            setBatchProgress(false);
            renderCropPagesBar();
            updateUnivCropPageUI();
            document.getElementById('univCropModal')?.classList.remove('hidden');
            requestAnimationFrame(drawUnivCropCanvas);
            if (typeof lucide !== 'undefined') lucide.createIcons();
        } catch (err) {
            console.error(err);
            setBatchProgress(false);
            showStudioToast(err?.message || 'Gagal membaca file surat Universitas.', true);
        } finally {
            if (isWordFile) isConvertingWord = false;
        }
    }

    async function ensureCropPageCanvas(idx) {
        const pageObj = univCropState.pages[idx];
        if (!pageObj) return null;
        if (pageObj.canvasCache) return pageObj.canvasCache;
        if (!univCropState.pdfDoc) return null;

        const page = await univCropState.pdfDoc.getPage(pageObj.pageNum);
        const vp = page.getViewport({ scale: 3.0 });
        const off = document.createElement('canvas');
        off.width = Math.round(vp.width);
        off.height = Math.round(vp.height);
        await page.render({ canvasContext: off.getContext('2d'), viewport: vp }).promise;
        pageObj.canvasCache = off;
        return off;
    }

    function renderCropPagesBar() {
        const bar = document.getElementById('cropPagesBar');
        const applyLabel = document.getElementById('btnApplyUnivCropLabel');
        const includedCount = univCropState.pages.filter(p => p.included).length;

        if (applyLabel) {
            applyLabel.textContent = includedCount > 1
                ? `Tempel ${includedCount} Halaman ke Kanvas`
                : 'Tempel Hasil Crop ke Kanvas';
        }

        if (!bar) return;
        if (univCropState.totalPages <= 1) {
            bar.classList.add('hidden');
            bar.innerHTML = '';
            return;
        }

        bar.classList.remove('hidden');
        bar.innerHTML = univCropState.pages.map((p, idx) => {
            const isActive = idx === univCropState.activeIdx;
            const syncText = idx === 0 ? 'Master' : (p.useSameCrop ? 'Sama' : 'Custom');
            const dotColor = idx === 0 || p.useSameCrop ? 'bg-emerald-500' : 'bg-amber-500';
            return `
                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl border text-[11px] flex-shrink-0 transition-all ${isActive ? 'bg-white border-emerald-500 ring-2 ring-emerald-500/20 shadow-2xs' : 'bg-white/75 border-gray-200 hover:bg-white'} ${p.included ? '' : 'opacity-50'}">
                    <input type="checkbox" ${p.included ? 'checked' : ''} onchange="toggleCropPageIncluded(${idx}, this.checked)"
                        class="rounded border-gray-300 text-emerald-600 cursor-pointer" title="Sertakan Halaman ${p.pageNum}">
                    <button type="button" onclick="selectCropModalPage(${idx})" class="inline-flex items-center gap-1.5 font-bold text-gray-800 cursor-pointer">
                        <span>Hal. ${p.pageNum}</span>
                        <span class="inline-flex items-center gap-1 text-[9.5px] font-semibold px-1.5 py-0.5 rounded-full bg-gray-100 text-gray-600">
                            <span class="w-1.5 h-1.5 rounded-full ${dotColor}"></span>${syncText}
                        </span>
                    </button>
                </div>
            `;
        }).join('');
    }

    function toggleCropPageIncluded(idx, checked) {
        if (!univCropState.pages[idx]) return;
        univCropState.pages[idx].included = checked;
        renderCropPagesBar();
    }

    function updateUnivCropPageUI() {
        const wrap = document.getElementById('cropPageSelectWrap');
        const info = document.getElementById('cropPageInfo');
        const prevBtn = document.getElementById('cropPrevPageBtn');
        const nextBtn = document.getElementById('cropNextPageBtn');
        const syncWrap = document.getElementById('cropPageSyncWrap');
        const sameCb = document.getElementById('cropUseSameAsP1');
        const curPage = getActiveCropPage();

        if (wrap) wrap.classList.toggle('hidden', univCropState.totalPages <= 1);
        if (info) info.textContent = `Hal ${univCropState.activeIdx + 1}/${univCropState.totalPages}`;
        if (prevBtn) prevBtn.disabled = univCropState.activeIdx <= 0;
        if (nextBtn) nextBtn.disabled = univCropState.activeIdx >= univCropState.totalPages - 1;
        if (syncWrap) syncWrap.classList.toggle('hidden', univCropState.totalPages <= 1 || univCropState.activeIdx === 0);
        if (sameCb && curPage) sameCb.checked = !!curPage.useSameCrop;
    }

    async function selectCropModalPage(idx) {
        if (idx < 0 || idx >= univCropState.totalPages) return;
        univCropState.activeIdx = idx;
        await ensureCropPageCanvas(idx);
        updateUnivCropPageUI();
        renderCropPagesBar();
        drawUnivCropCanvas();
    }

    async function switchCropPdfPage(delta) {
        await selectCropModalPage(univCropState.activeIdx + delta);
    }

    function toggleCurrentCropPageSync(useSame) {
        const p = getActiveCropPage();
        if (!p || univCropState.activeIdx === 0) return;
        p.useSameCrop = useSame;
        if (useSame) {
            p.customCrop = null;
        } else {
            p.customCrop = { ...univCropState.masterCrop };
        }
        updateUnivCropPageUI();
        renderCropPagesBar();
        drawUnivCropCanvas();
    }

    function applyCurrentCropToAllPages() {
        const cur = getCropForPage(getActiveCropPage());
        univCropState.masterCrop = { ...cur };
        univCropState.pages.forEach(p => {
            p.useSameCrop = true;
            p.customCrop = null;
        });
        updateUnivCropPageUI();
        renderCropPagesBar();
        drawUnivCropCanvas();
        showStudioToast('Ukuran crop halaman ini diterapkan ke seluruh halaman.');
    }

    function setCropPreset(top, bottom, left, right) {
        setActivePageCrop({ top, bottom, left, right }, true);
        drawUnivCropCanvas();
    }

    function drawUnivCropCanvas() {
        const pageObj = getActiveCropPage();
        const src = pageObj?.canvasCache;
        const c = document.getElementById('univCropCanvas');
        if (!src || !c) return;

        const maxW = Math.min(760, window.innerWidth - 80);
        const maxH = Math.max(340, window.innerHeight * 0.56);
        const scale = Math.min(maxW / src.width, maxH / src.height, 1);
        univCropState.dispScale = scale;

        c.width = Math.round(src.width * scale);
        c.height = Math.round(src.height * scale);

        const cctx = c.getContext('2d');
        cctx.clearRect(0, 0, c.width, c.height);
        cctx.drawImage(src, 0, 0, c.width, c.height);

        const { top, bottom, left, right } = getCropForPage(pageObj);
        const rx = left * c.width;
        const ry = top * c.height;
        const rw = (right - left) * c.width;
        const rh = (bottom - top) * c.height;

        cctx.fillStyle = 'rgba(15, 23, 42, 0.55)';
        cctx.fillRect(0, 0, c.width, ry);
        cctx.fillRect(0, ry + rh, c.width, c.height - (ry + rh));
        cctx.fillRect(0, ry, rx, rh);
        cctx.fillRect(rx + rw, ry, c.width - (rx + rw), rh);

        cctx.strokeStyle = '#10b981';
        cctx.lineWidth = 2.5;
        cctx.strokeRect(rx, ry, rw, rh);

        cctx.fillStyle = '#10b981';
        cctx.fillRect(rx + rw / 2 - 22, ry - 4, 44, 8);
        cctx.fillRect(rx + rw / 2 - 22, ry + rh - 4, 44, 8);
        cctx.fillRect(rx - 4, ry + rh / 2 - 18, 8, 36);
        cctx.fillRect(rx + rw - 4, ry + rh / 2 - 18, 8, 36);

        const lbl = document.getElementById('cropCoordsLabel');
        if (lbl) {
            lbl.textContent = `Atas: ${Math.round(top * 100)}% · Bawah: ${Math.round(bottom * 100)}%`;
        }
    }

    function initUnivCropInteraction() {
        const c = document.getElementById('univCropCanvas');
        if (!c || c.dataset.boundCrop) return;
        c.dataset.boundCrop = '1';

        const getNormPt = (evt) => {
            const rect = c.getBoundingClientRect();
            const cx = evt.touches ? evt.touches[0].clientX : evt.clientX;
            const cy = evt.touches ? evt.touches[0].clientY : evt.clientY;
            return {
                x: Math.max(0, Math.min(1, (cx - rect.left) / rect.width)),
                y: Math.max(0, Math.min(1, (cy - rect.top) / rect.height)),
            };
        };

        c.addEventListener('mousedown', (evt) => {
            const p = getNormPt(evt);
            const cr = getCropForPage(getActiveCropPage());
            const tol = 0.025;
            let mode = null;
            if (Math.abs(p.y - cr.top) < tol && p.x >= cr.left - tol && p.x <= cr.right + tol) mode = 'top';
            else if (Math.abs(p.y - cr.bottom) < tol && p.x >= cr.left - tol && p.x <= cr.right + tol) mode = 'bottom';
            else if (Math.abs(p.x - cr.left) < tol && p.y >= cr.top && p.y <= cr.bottom) mode = 'left';
            else if (Math.abs(p.x - cr.right) < tol && p.y >= cr.top && p.y <= cr.bottom) mode = 'right';
            else if (p.x > cr.left && p.x < cr.right && p.y > cr.top && p.y < cr.bottom) mode = 'move';
            if (!mode) return;
            evt.preventDefault();
            univCropState.dragMode = mode;
            univCropState.startX = p.x;
            univCropState.startY = p.y;
            univCropState.origCrop = { ...cr };
        });

        window.addEventListener('mousemove', (evt) => {
            if (!univCropState.dragMode) return;
            const p = getNormPt(evt);
            const o = univCropState.origCrop;
            const next = { ...o };
            const minSpan = 0.08;
            if (univCropState.dragMode === 'top') {
                next.top = Math.max(0, Math.min(o.bottom - minSpan, p.y));
            } else if (univCropState.dragMode === 'bottom') {
                next.bottom = Math.min(1, Math.max(o.top + minSpan, p.y));
            } else if (univCropState.dragMode === 'left') {
                next.left = Math.max(0, Math.min(o.right - minSpan, p.x));
            } else if (univCropState.dragMode === 'right') {
                next.right = Math.min(1, Math.max(o.left + minSpan, p.x));
            } else if (univCropState.dragMode === 'move') {
                const dx = p.x - univCropState.startX;
                const dy = p.y - univCropState.startY;
                const w = o.right - o.left;
                const h = o.bottom - o.top;
                const nl = Math.max(0, Math.min(1 - w, o.left + dx));
                const nt = Math.max(0, Math.min(1 - h, o.top + dy));
                next.left = nl;
                next.right = nl + w;
                next.top = nt;
                next.bottom = nt + h;
            }
            setActivePageCrop(next, false);
            drawUnivCropCanvas();
        });

        window.addEventListener('mouseup', () => {
            if (!univCropState.dragMode) return;
            univCropState.dragMode = null;
            updateUnivCropPageUI();
            renderCropPagesBar();
        });
    }

    function closeUnivCropModal() {
        document.getElementById('univCropModal')?.classList.add('hidden');
    }

    async function cropPageToBodyLayer(pageObj, tplW, tplH) {
        const src = await ensureCropPageCanvas(univCropState.pages.indexOf(pageObj));
        if (!src) return null;

        const { top, bottom, left, right } = getCropForPage(pageObj);
        const sx = Math.round(left * src.width);
        const sy = Math.round(top * src.height);
        const sw = Math.max(40, Math.round((right - left) * src.width));
        const sh = Math.max(40, Math.round((bottom - top) * src.height));

        const out = document.createElement('canvas');
        out.width = sw;
        out.height = sh;
        out.getContext('2d').drawImage(src, sx, sy, sw, sh, 0, 0, sw, sh);

        const croppedDataUrl = out.toDataURL('image/png');
        const img = await loadCachedImage(croppedDataUrl);
        const aspect = (img.naturalHeight || sh) / (img.naturalWidth || sw);

        const wPct = Math.min(0.98, Math.max(0.45, right - left));
        const widthPx = tplW * wPct;
        const heightPx = widthPx * aspect;
        const startYPx = tplH * 0.162;

        return {
            id: uid('body_'),
            type: 'image',
            role: 'body_crop',
            name: `Isi Surat Hal. ${pageObj.pageNum}`,
            locked: false,
            url: croppedDataUrl,
            x: tplW * 0.5,
            y: startYPx + heightPx / 2,
            width: widthPx,
            height: heightPx,
            aspect: aspect,
            rotation: 0,
            opacity: 100,
            removeBg: false,
            bgThreshold: 225,
            whiteBacking: false,
        };
    }

    async function applyUnivCropToCanvas() {
        const includedPages = univCropState.pages.filter(p => p.included);
        if (includedPages.length === 0) {
            showStudioToast('Centang minimal 1 halaman surat untuk ditempel ke kanvas.', true);
            return;
        }

        if ((!baseImage || slides.length === 0) && LETTER_DATA.defaultLetterheadUrl) {
            await selectLetterheadAsBase(LETTER_DATA.defaultLetterheadUrl, LETTER_DATA.defaultLetterheadName);
        }
        if (!ensureBaseReady()) return;

        const tplSlide = getActiveSlide();
        const tplUrl = tplSlide?.dataUrl || LETTER_DATA.defaultLetterheadUrl;
        const tplThumb = tplSlide?.thumbUrl;
        const tplW = tplSlide?.width || baseWidth;
        const tplH = tplSlide?.height || baseHeight;

        if (includedPages.length === 1) {
            const bodyLayer = await cropPageToBodyLayer(includedPages[0], baseWidth, baseHeight);
            if (!bodyLayer) return;

            layers = layers.filter(l => l.role !== 'body_crop');
            layers.unshift(bodyLayer);
            selectedLayerId = bodyLayer.id;
            pushHistory();
            renderCanvas();
            syncInspector();
            renderLayerList();
            closeUnivCropModal();
            switchLeftTool('text');
            showStudioToast('Isi surat Universitas berhasil ditempel di atas Template Kop!');
            return;
        }

        // Multi-page: generate slides using the same Template Kop Base + each page's cropped body
        setBatchProgress(true, 'Menyusun Multi-Halaman Surat...', `Memproses ${includedPages.length} halaman dengan Template Kop yang sama...`, 15);

        try {
            const existingOverlayNorm = normalizeLayers(layers.filter(l => l.role !== 'body_crop'), baseWidth, baseHeight);
            const newSlides = [];

            for (let i = 0; i < includedPages.length; i++) {
                const p = includedPages[i];
                const pct = 15 + Math.round(((i + 1) / includedPages.length) * 80);
                setBatchProgress(true, 'Menyusun Multi-Halaman Surat...', `Memotong halaman ${p.pageNum} (${i + 1}/${includedPages.length})...`, pct);

                const bodyLayer = await cropPageToBodyLayer(p, tplW, tplH);
                const pageLayersNorm = bodyLayer ? normalizeLayers([bodyLayer], tplW, tplH) : [];
                if (i === 0 && existingOverlayNorm.length > 0) {
                    pageLayersNorm.push(...existingOverlayNorm);
                }

                newSlides.push({
                    id: uid('sld_'),
                    label: `${univCropState.fileName} (Hal. ${p.pageNum})`,
                    fileName: `${univCropState.fileName}_hal${p.pageNum}`,
                    sourceType: 'image',
                    dataUrl: tplUrl,
                    thumbUrl: tplThumb,
                    baseLetterheadUrl: activeBaseLetterhead.url || tplSlide?.baseLetterheadUrl || tplUrl,
                    baseLetterheadName: activeBaseLetterhead.name || tplSlide?.baseLetterheadName || LETTER_DATA.defaultLetterheadName,
                    width: tplW,
                    height: tplH,
                    useMasterLayout: false,
                    customNormalizedLayers: pageLayersNorm,
                });
            }

            slides = newSlides;
            activeSlideIndex = -1;
            setBatchProgress(false);
            closeUnivCropModal();
            await switchSlide(0);
            switchLeftTool('text');
            showStudioToast(`${includedPages.length} halaman surat ditempel dengan Template Kop yang sama!`);
        } catch (err) {
            console.error(err);
            setBatchProgress(false);
            showStudioToast('Gagal menyusun multi-halaman hasil crop.', true);
        }
    }

    function fitLetterheadFullWidth() {
        const layer = layers.find(l => l.id === selectedLayerId && l.type === 'image');
        if (!layer) return;
        if (layer.locked) {
            showStudioToast('Layer sedang dikunci.', true);
            return;
        }
        layer.width = baseWidth;
        layer.height = baseWidth * (layer.aspect || 1);
        layer.x = baseWidth * 0.5;
        layer.y = layer.height / 2;
        pushHistory();
        syncInspector();
        renderCanvas();
    }

    function addCustomTextShortcut() {
        addTextLayer('Teks Tambahan', 0.5, 0.5, 'custom_text', 'Teks Bebas');
        setTimeout(() => document.getElementById('propTextValue')?.focus(), 50);
    }

    function addSignatoryBlockShortcut() {
        const lines = [LETTER_DATA.primarySigName];
        if (LETTER_DATA.primarySigNip) {
            lines.push(`NIP ${LETTER_DATA.primarySigNip}`);
        }
        addTextLayer(lines.join('\n'), 0.54, 0.85, 'custom_text', 'Nama & NIP');
    }

    function addOrReplaceNumberText(textValue) {
        if (!ensureBaseReady()) return;
        const existing = layers.find(l => l.type === 'text' && l.role === 'number');
        if (existing) {
            if (existing.locked) {
                showStudioToast('Layer Nomor sedang dikunci. Buka kunci terlebih dahulu.', true);
                return;
            }
            existing.text = textValue;
            selectedLayerId = existing.id;
            pushHistory();
            renderCanvas();
            syncInspector();
            renderLayerList();
        } else {
            addTextLayer(textValue, 0.5, 0.308, 'number', 'Nomor Sertifikat');
        }
    }

    function snapNumberUnderTitle() {
        if (!ensureBaseReady()) return;
        const existing = layers.find(l => l.type === 'text' && l.role === 'number');
        if (!existing) {
            addTextLayer('No. ' + LETTER_DATA.number, 0.5, 0.308, 'number', 'Nomor Sertifikat');
            return;
        }
        if (existing.locked) {
            showStudioToast('Layer Nomor sedang dikunci.', true);
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

    async function addImageLayer(role, url, label, opacity = 100, customPct = null, signatoryId = null) {
        if (!ensureBaseReady()) return;
        if (!url) {
            showStudioToast('Gambar belum tersedia. Silakan upload terlebih dahulu.', true);
            return;
        }

        try {
            const img = await loadCachedImage(url);
            const aspect = (img.naturalHeight || 1) / (img.naturalWidth || 1);

            const isLetterMode = LETTER_DATA.mode === 'letter';
            const defaultWidthPct = role === 'stamp'
                ? (isLetterMode ? 0.20 : 0.165)
                : (isLetterMode ? 0.16 : 0.21);
            const defaultXPct = role === 'stamp'
                ? (isLetterMode ? 0.515 : 0.425)
                : (isLetterMode ? 0.598 : 0.55);
            const defaultYPct = role === 'stamp'
                ? (isLetterMode ? 0.738 : 0.74)
                : (isLetterMode ? 0.738 : 0.745);

            const widthPx = baseWidth * (customPct?.wPct ?? defaultWidthPct);
            const heightPx = widthPx * aspect;

            const layer = {
                id: uid(),
                type: 'image',
                role: role,
                name: label || (role === 'stamp' ? 'Stempel' : (role === 'letterhead' ? 'Kop Surat' : 'Tanda Tangan')),
                locked: false,
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
                whiteBacking: role === 'letterhead',
                signatoryId: signatoryId || null,
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
        if (LETTER_DATA.mode === 'letter') {
            if ((!baseImage || slides.length === 0) && LETTER_DATA.defaultLetterheadUrl) {
                await selectLetterheadAsBase(LETTER_DATA.defaultLetterheadUrl, LETTER_DATA.defaultLetterheadName);
            }
            if (!ensureBaseReady()) return;

            if (!layers.find(l => l.type === 'text' && l.role === 'number')) {
                addNumberWithWhiteout(false);
            }

            showStudioToast('Nomor Surat berlatar putih ditempelkan! Geser tepat di atas nomor surat Universitas.');
            return;
        }

        if (!ensureBaseReady()) return;

        if (!layers.find(l => l.type === 'text' && l.role === 'number')) {
            addTextLayer('No. ' + LETTER_DATA.number, 0.5, 0.308, 'number', 'Nomor Sertifikat');
        }

        if (LETTER_DATA.canUsePrimarySig && LETTER_DATA.primarySigUrl && !layers.find(l => l.role === 'signature')) {
            await addImageLayer('signature', LETTER_DATA.primarySigUrl, LETTER_DATA.primarySigName || 'TTD Pejabat', 100, {
                wPct: 0.21, xPct: 0.54, yPct: 0.745
            }, LETTER_DATA.primarySigId);
        }

        if (LETTER_DATA.defaultStampUrl && !layers.find(l => l.role === 'stamp')) {
            await addImageLayer('stamp', LETTER_DATA.defaultStampUrl, LETTER_DATA.defaultStampName, LETTER_DATA.defaultStampOpacity, {
                wPct: 0.165, xPct: 0.425, yPct: 0.74
            });
        }

        showStudioToast(slides.length > 1
            ? `Nomor, TTD & Stempel disusun & disinkronkan ke ${slides.length} sertifikat!`
            : 'Komponen Nomor, TTD & Stempel disusun ke posisi default!'
        );
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

    // ── Rendering Engine (Supports Multiline, Underline, Strikethrough, Uppercase, LetterSpacing, TextShadow) ──
    function resolveTextLines(layer) {
        const raw = layer.uppercase ? (layer.text || '').toUpperCase() : (layer.text || '');
        const lines = raw.split(/\r?\n/);
        return lines.length ? lines : [''];
    }

    function measureLineWithSpacing(targetCtx, text, spacing) {
        if (!spacing || 'letterSpacing' in targetCtx) {
            return targetCtx.measureText(text).width;
        }
        let width = 0;
        for (let i = 0; i < text.length; i++) {
            width += targetCtx.measureText(text[i]).width + (i < text.length - 1 ? spacing : 0);
        }
        return width;
    }

    function drawLayersOnContext(targetCtx, targetW, targetH, bgImg, layerList, skipProtectedSignatures = false) {
        targetCtx.clearRect(0, 0, targetW, targetH);
        targetCtx.drawImage(bgImg, 0, 0, targetW, targetH);

        for (const layer of layerList) {
            if (skipProtectedSignatures && layer.type === 'image' && layer.role === 'signature' && layer.signatoryId) {
                continue;
            }
            targetCtx.save();
            targetCtx.globalAlpha = Math.max(0.05, Math.min(1, (layer.opacity ?? 100) / 100));

            if (layer.type === 'whiteout') {
                targetCtx.translate(layer.x, layer.y);
                if (layer.rotation) targetCtx.rotate((layer.rotation * Math.PI) / 180);
                targetCtx.fillStyle = layer.color || '#ffffff';
                targetCtx.fillRect(-layer.width / 2, -layer.height / 2, layer.width, layer.height);
            } else if (layer.type === 'text') {
                const stylePrefix = `${layer.italic ? 'italic ' : ''}${layer.bold ? 'bold ' : ''}`;
                targetCtx.font = `${stylePrefix}${layer.fontSize}px ${layer.fontFamily}`;
                targetCtx.textAlign = layer.align || 'center';
                targetCtx.textBaseline = 'middle';

                const spacing = Number(layer.letterSpacing || 0);
                if ('letterSpacing' in targetCtx) {
                    targetCtx.letterSpacing = `${spacing}px`;
                }

                targetCtx.translate(layer.x, layer.y);
                if (layer.rotation) targetCtx.rotate((layer.rotation * Math.PI) / 180);

                const lines = resolveTextLines(layer);
                const lineHeight = layer.fontSize * 1.3;
                const totalHeight = lines.length * lineHeight;
                const startY = -(totalHeight / 2) + (lineHeight / 2);

                if (layer.bgFill) {
                    let maxW = 24;
                    for (const lt of lines) {
                        maxW = Math.max(maxW, measureLineWithSpacing(targetCtx, lt, spacing));
                    }
                    const padX = Math.max(10, layer.fontSize * 0.42);
                    const padY = Math.max(4, layer.fontSize * 0.24);
                    const minBoxW = targetW * ((layer.bgMinWidthPct || 0) / 100);
                    const boxW = Math.max(maxW + padX * 2, minBoxW);
                    const boxH = totalHeight + padY * 2;
                    let bgLeft = -boxW / 2;
                    if ((layer.align || 'center') === 'left') bgLeft = -padX;
                    if ((layer.align || 'center') === 'right') bgLeft = -(boxW - padX);

                    targetCtx.fillStyle = layer.bgColor || '#ffffff';
                    targetCtx.fillRect(bgLeft, -(totalHeight / 2) - padY, boxW, boxH);
                }

                targetCtx.fillStyle = layer.color || '#111827';
                targetCtx.strokeStyle = layer.color || '#111827';

                if (layer.textShadow) {
                    targetCtx.shadowColor = 'rgba(0, 0, 0, 0.25)';
                    targetCtx.shadowBlur = Math.max(3, layer.fontSize * 0.08);
                    targetCtx.shadowOffsetX = 1;
                    targetCtx.shadowOffsetY = Math.max(1, layer.fontSize * 0.04);
                }

                lines.forEach((lineText, idx) => {
                    const ly = startY + idx * lineHeight;
                    targetCtx.fillText(lineText, 0, ly);

                    if ((layer.underline || layer.strikethrough) && lineText.length > 0) {
                        const lw = measureLineWithSpacing(targetCtx, lineText, spacing);
                        let lineStartX = -lw / 2;
                        if ((layer.align || 'center') === 'left') lineStartX = 0;
                        if ((layer.align || 'center') === 'right') lineStartX = -lw;

                        const strokeThick = Math.max(1.5, layer.fontSize * 0.06);
                        targetCtx.lineWidth = strokeThick;

                        if (layer.underline) {
                            const uy = ly + layer.fontSize * 0.48;
                            targetCtx.beginPath();
                            targetCtx.moveTo(lineStartX, uy);
                            targetCtx.lineTo(lineStartX + lw, uy);
                            targetCtx.stroke();
                        }

                        if (layer.strikethrough) {
                            targetCtx.beginPath();
                            targetCtx.moveTo(lineStartX, ly + strokeThick * 0.5);
                            targetCtx.lineTo(lineStartX + lw, ly + strokeThick * 0.5);
                            targetCtx.stroke();
                        }
                    }
                });
            } else if (layer.type === 'image') {
                const imgSource = getProcessedImage(layer);
                if (imgSource) {
                    targetCtx.translate(layer.x, layer.y);
                    if (layer.rotation) targetCtx.rotate((layer.rotation * Math.PI) / 180);
                    if (layer.whiteBacking) {
                        targetCtx.fillStyle = '#ffffff';
                        targetCtx.fillRect(-layer.width / 2, -layer.height / 2, layer.width, layer.height);
                    }
                    targetCtx.drawImage(imgSource, -layer.width / 2, -layer.height / 2, layer.width, layer.height);
                }
            }

            targetCtx.restore();
        }
    }

    function getTextBoxMetrics(layer) {
        ctx.save();
        const stylePrefix = `${layer.italic ? 'italic ' : ''}${layer.bold ? 'bold ' : ''}`;
        ctx.font = `${stylePrefix}${layer.fontSize}px ${layer.fontFamily}`;
        const spacing = Number(layer.letterSpacing || 0);
        if ('letterSpacing' in ctx) ctx.letterSpacing = `${spacing}px`;

        const lines = resolveTextLines(layer);
        let maxW = 40;
        for (const l of lines) {
            maxW = Math.max(maxW, measureLineWithSpacing(ctx, l, spacing));
        }
        ctx.restore();

        const w = maxW + 18;
        const h = Math.max(24, lines.length * layer.fontSize * 1.3);

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

    function drawStageGridOverlay() {
        if (!document.getElementById('gridToggle')?.checked) return;
        ctx.save();
        // Rule of thirds lines
        ctx.strokeStyle = 'rgba(99, 102, 241, 0.28)';
        ctx.lineWidth = Math.max(1, 1.2 / displayScale);
        for (const f of [1 / 3, 2 / 3]) {
            ctx.beginPath();
            ctx.moveTo(baseWidth * f, 0);
            ctx.lineTo(baseWidth * f, baseHeight);
            ctx.stroke();

            ctx.beginPath();
            ctx.moveTo(0, baseHeight * f);
            ctx.lineTo(baseWidth, baseHeight * f);
            ctx.stroke();
        }
        // 5% Safe Print Margin
        ctx.strokeStyle = 'rgba(249, 115, 22, 0.35)';
        ctx.setLineDash([6 / displayScale, 6 / displayScale]);
        ctx.strokeRect(baseWidth * 0.05, baseHeight * 0.05, baseWidth * 0.9, baseHeight * 0.9);
        ctx.restore();
    }

    function renderCanvas(forExport = false) {
        if (!baseImage) return;
        drawLayersOnContext(ctx, baseWidth, baseHeight, baseImage, layers);

        if (forExport) return;

        drawStageGridOverlay();

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

            ctx.strokeStyle = sel.locked ? '#f59e0b' : '#2563eb';
            ctx.lineWidth = Math.max(1.5, 2 / displayScale);
            if (sel.locked) ctx.setLineDash([6 / displayScale, 4 / displayScale]);
            ctx.strokeRect(-b.w / 2, -b.h / 2, b.w, b.h);

            if (!sel.locked) {
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
            }
            ctx.restore();
        }
    }

    // ── Canvas Pointer Interaction (Locked layers ignored on canvas click) ──
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

        if (sel && !sel.locked) {
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
            if (l.locked) continue; // Skip locked layers so underlying/overlapping items aren't trapped
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
        if (!layer || layer.locked) return;

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
            if (layer.type === 'whiteout') {
                const lp = toLocalPoint(pt, dragState.cx, dragState.cy, layer.rotation || 0);
                layer.width = Math.max(24, Math.round(Math.abs(lp.x) * 2));
                layer.height = Math.max(14, Math.round(Math.abs(lp.y) * 2));
            } else {
                const newDist = Math.max(15, Math.hypot(pt.x - dragState.cx, pt.y - dragState.cy));
                const ratio = newDist / dragState.origDist;
                if (layer.type === 'image') {
                    layer.width = Math.max(30, dragState.origW * ratio);
                    layer.height = layer.width * (layer.aspect || 1);
                } else {
                    layer.fontSize = Math.max(14, Math.min(260, Math.round(dragState.origFontSize * ratio)));
                }
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
    function syncStyleButton(btnId, active) {
        const btn = document.getElementById(btnId);
        if (!btn) return;
        btn.classList.toggle('bg-gray-900', !!active);
        btn.classList.toggle('text-white', !!active);
        btn.classList.toggle('border-gray-900', !!active);
    }

    function syncInspector() {
        const layer = layers.find(l => l.id === selectedLayerId);
        const empty = document.getElementById('inspectorEmpty');
        const txt = document.getElementById('inspectorText');
        const img = document.getElementById('inspectorImage');
        const wht = document.getElementById('inspectorWhiteout');
        const common = document.getElementById('inspectorCommon');
        const nudgeWrap = document.getElementById('inspectorNudgeWrap');
        const badge = document.getElementById('selectedLayerTypeBadge');
        const lockBtn = document.getElementById('btnInspectorLock');

        if (!layer) {
            empty.classList.remove('hidden');
            txt.classList.add('hidden');
            img.classList.add('hidden');
            wht?.classList.add('hidden');
            common.classList.add('hidden');
            nudgeWrap?.classList.add('hidden');
            badge.classList.add('hidden');
            lockBtn?.classList.add('hidden');
            return;
        }

        empty.classList.add('hidden');
        common.classList.remove('hidden');
        nudgeWrap?.classList.remove('hidden');
        badge.classList.remove('hidden');
        badge.textContent = layer.type === 'whiteout'
            ? 'Penutup'
            : (layer.type === 'text' ? (layer.role === 'number' ? 'Nomor' : 'Teks') : (layer.role === 'stamp' ? 'Stempel' : (layer.role === 'letterhead' ? 'Kop Surat' : 'TTD')));

        if (lockBtn) {
            lockBtn.classList.remove('hidden');
            lockBtn.className = layer.locked
                ? 'px-2 py-0.5 rounded-lg border border-amber-300 bg-amber-50 text-amber-700 text-[10px] font-bold cursor-pointer inline-flex items-center gap-1'
                : 'px-2 py-0.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-600 hover:bg-gray-100 text-[10px] font-bold cursor-pointer inline-flex items-center gap-1';
            lockBtn.innerHTML = `<i data-lucide="${layer.locked ? 'lock' : 'unlock'}" class="w-3 h-3"></i><span>${layer.locked ? 'Terkunci' : 'Kunci'}</span>`;
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }

        if (layer.type === 'whiteout') {
            txt.classList.add('hidden');
            img.classList.add('hidden');
            wht?.classList.remove('hidden');
            const wPct = +((layer.width / baseWidth) * 100).toFixed(1);
            const hPct = +((layer.height / baseHeight) * 100).toFixed(1);
            document.getElementById('propWhiteoutW').value = wPct;
            document.getElementById('propWhiteoutWLabel').textContent = wPct + '%';
            document.getElementById('propWhiteoutH').value = hPct;
            document.getElementById('propWhiteoutHLabel').textContent = hPct + '%';
            document.getElementById('propWhiteoutColor').value = layer.color || '#ffffff';
            document.getElementById('propWhiteoutOpacity').value = layer.opacity ?? 100;
            document.getElementById('propWhiteoutOpacityLabel').textContent = (layer.opacity ?? 100) + '%';
        } else if (layer.type === 'text') {
            txt.classList.remove('hidden');
            img.classList.add('hidden');
            wht?.classList.add('hidden');
            document.getElementById('propTextValue').value = layer.text || '';
            document.getElementById('propFontFamily').value = layer.fontFamily;
            document.getElementById('propFontSize').value = layer.fontSize;
            document.getElementById('propFontSizeSlider').value = Math.min(180, layer.fontSize);
            document.getElementById('propFontSizeLabel').textContent = layer.fontSize;
            document.getElementById('propTextColor').value = layer.color || '#111827';
            document.getElementById('propTextBgFill').checked = !!layer.bgFill;
            document.getElementById('propTextBgColor').value = layer.bgColor || '#ffffff';
            document.getElementById('propTextBgWidthWrap')?.classList.toggle('hidden', !layer.bgFill);
            const minWPct = layer.bgMinWidthPct || 0;
            const minWEl = document.getElementById('propTextBgMinW');
            const minWLbl = document.getElementById('propTextBgMinWLabel');
            if (minWEl) minWEl.value = minWPct;
            if (minWLbl) minWLbl.textContent = minWPct + '%';
            document.getElementById('propLetterSpacing').value = layer.letterSpacing ?? 0;
            document.getElementById('propLetterSpacingLabel').textContent = `${layer.letterSpacing ?? 0}px`;
            document.getElementById('propTextOpacity').value = layer.opacity ?? 100;
            document.getElementById('propTextOpacityLabel').textContent = `${layer.opacity ?? 100}%`;
            document.getElementById('propTextShadow').checked = !!layer.textShadow;

            syncStyleButton('btnPropBold', layer.bold);
            syncStyleButton('btnPropItalic', layer.italic);
            syncStyleButton('btnPropUnderline', layer.underline);
            syncStyleButton('btnPropStrike', layer.strikethrough);
            syncStyleButton('btnPropUpper', layer.uppercase);
            syncStyleButton('btnAlignLeft', layer.align === 'left');
            syncStyleButton('btnAlignCenter', (layer.align || 'center') === 'center');
            syncStyleButton('btnAlignRight', layer.align === 'right');
        } else {
            txt.classList.add('hidden');
            wht?.classList.add('hidden');
            img.classList.remove('hidden');
            document.getElementById('propLetterheadTools')?.classList.toggle('hidden', layer.role !== 'letterhead');
            const whiteBackingCb = document.getElementById('propWhiteBacking');
            if (whiteBackingCb) whiteBackingCb.checked = !!layer.whiteBacking;
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

    function updateSelectedWhiteoutSize(dim, pct) {
        const layer = layers.find(l => l.id === selectedLayerId && l.type === 'whiteout');
        if (!layer) return;
        if (dim === 'width') {
            layer.width = Math.max(20, baseWidth * (pct / 100));
            document.getElementById('propWhiteoutWLabel').textContent = pct + '%';
        } else {
            layer.height = Math.max(12, baseHeight * (pct / 100));
            document.getElementById('propWhiteoutHLabel').textContent = pct + '%';
        }
        syncCurrentLayersToState();
        renderCanvas();
    }

    function updateSelectedWhiteoutColor(hex) {
        const layer = layers.find(l => l.id === selectedLayerId && l.type === 'whiteout');
        if (!layer) return;
        layer.color = hex;
        syncCurrentLayersToState();
        renderCanvas();
    }

    function setWhiteoutColorPreset(hex) {
        document.getElementById('propWhiteoutColor').value = hex;
        updateSelectedWhiteoutColor(hex);
        pushHistory();
    }

    function updateSelectedTextProp(key, val) {
        const layer = layers.find(l => l.id === selectedLayerId);
        if (!layer || layer.type !== 'text') return;
        layer[key] = val;
        if (key === 'fontSize') {
            document.getElementById('propFontSizeLabel').textContent = val;
        }
        if (key === 'letterSpacing') {
            document.getElementById('propLetterSpacingLabel').textContent = `${val}px`;
        }
        if (key === 'bgMinWidthPct') {
            const lbl = document.getElementById('propTextBgMinWLabel');
            if (lbl) lbl.textContent = `${val}%`;
        }
        if (key === 'align') {
            syncInspector();
        }
        syncCurrentLayersToState();
        renderCanvas();
        renderLayerList();
    }

    async function changeSelectedFontFamily(fontFamily) {
        updateSelectedTextProp('fontFamily', fontFamily);
        const layer = layers.find(l => l.id === selectedLayerId);
        if (layer && document.fonts?.load) {
            try {
                await document.fonts.load(`400 ${layer.fontSize}px ${fontFamily}`);
            } catch {}
            renderCanvas();
        }
        pushHistory();
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
        syncCurrentLayersToState();
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
            if (layer.type === 'text') {
                document.getElementById('propTextOpacityLabel').textContent = val + '%';
            } else if (layer.type === 'whiteout') {
                document.getElementById('propWhiteoutOpacityLabel').textContent = val + '%';
            } else {
                document.getElementById('propImgOpacityLabel').textContent = val + '%';
            }
        }
        syncCurrentLayersToState();
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
        syncCurrentLayersToState();
        renderCanvas();
    }

    function toggleSelectedLock(targetId = null) {
        const id = targetId || selectedLayerId;
        const layer = layers.find(l => l.id === id);
        if (!layer) return;
        layer.locked = !layer.locked;
        pushHistory();
        renderCanvas();
        syncInspector();
        renderLayerList();
        showStudioToast(layer.locked ? `Layer "${layer.name}" dikunci.` : `Kunci layer "${layer.name}" dibuka.`);
    }

    function nudgeSelectedLayer(dx, dy) {
        const layer = layers.find(l => l.id === selectedLayerId);
        if (!layer) return;
        if (layer.locked) {
            showStudioToast('Layer sedang dikunci. Buka kunci terlebih dahulu.', true);
            return;
        }
        layer.x += dx;
        layer.y += dy;
        pushHistory();
        renderCanvas();
    }

    function duplicateSelectedLayer() {
        const layer = layers.find(l => l.id === selectedLayerId);
        if (!layer) return;
        const clone = JSON.parse(JSON.stringify(layer));
        clone.id = uid();
        clone.locked = false;
        clone.name = `${layer.name} (Salinan)`;
        clone.x += Math.round(baseWidth * 0.02);
        clone.y += Math.round(baseHeight * 0.02);
        layers.push(clone);
        selectedLayerId = clone.id;
        pushHistory();
        renderCanvas();
        syncInspector();
        renderLayerList();
        showStudioToast('Elemen diduplikat.');
    }

    function centerSelectedLayer() {
        const layer = layers.find(l => l.id === selectedLayerId);
        if (!layer) return;
        if (layer.locked) {
            showStudioToast('Layer sedang dikunci.', true);
            return;
        }
        layer.x = baseWidth / 2;
        if (layer.type === 'text') layer.align = 'center';
        pushHistory();
        syncInspector();
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
        const target = layers.find(l => l.id === selectedLayerId);
        if (target?.locked) {
            showStudioToast('Buka kunci layer sebelum menghapusnya.', true);
            return;
        }
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
        const railBadge = document.getElementById('railLayerCount');
        if (countBadge) countBadge.textContent = `${layers.length} elemen`;
        if (railBadge) {
            railBadge.textContent = layers.length;
            railBadge.classList.toggle('hidden', layers.length === 0);
        }
        if (!container) return;

        if (layers.length === 0) {
            container.innerHTML = '<p class="text-[11px] text-gray-400 italic">Belum ada elemen ditambahkan.</p>';
            return;
        }

        container.innerHTML = [...layers].reverse().map(l => {
            const active = l.id === selectedLayerId;
            const title = l.type === 'text' ? (l.text || 'Teks') : l.name;
            const badge = l.type === 'whiteout'
                ? 'Tutup'
                : (l.type === 'text' ? (l.role === 'number' ? 'No' : 'T') : (l.role === 'stamp' ? 'Cap' : (l.role === 'letterhead' ? 'Kop' : (l.role === 'body_crop' ? 'Isi' : 'TTD'))));
            return `
                <div onclick="selectLayerById('${l.id}')"
                    class="w-full flex items-center justify-between gap-1.5 px-2.5 py-2 rounded-xl border text-left transition-all cursor-pointer ${active ? 'bg-blue-50 border-blue-300 text-blue-900 font-bold' : 'bg-gray-50 border-gray-200/70 text-gray-700 hover:bg-gray-100'}">
                    <span class="truncate text-[11px] flex-1">${title}</span>
                    <button type="button" onclick="event.stopPropagation(); toggleSelectedLock('${l.id}')"
                        class="p-1 rounded-md ${l.locked ? 'text-amber-600 bg-amber-100/80' : 'text-gray-400 hover:text-gray-700'} cursor-pointer"
                        title="${l.locked ? 'Buka kunci' : 'Kunci posisi'}">
                        <i data-lucide="${l.locked ? 'lock' : 'unlock'}" class="w-3 h-3"></i>
                    </button>
                    <span class="px-1.5 py-0.5 rounded bg-white border border-gray-200 text-[9.5px] font-mono font-bold text-gray-500 flex-shrink-0">${badge}</span>
                </div>
            `;
        }).join('');
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function selectLayerById(id) {
        selectedLayerId = id;
        renderCanvas();
        syncInspector();
        renderLayerList();
    }

    // ── Lightbox Clean Fullscreen Preview ──
    function openFullscreenPreview() {
        if (!ensureBaseReady()) return;
        renderCanvas(true);
        const url = canvas.toDataURL('image/png');
        renderCanvas(false);

        document.getElementById('lightboxPreviewImg').src = url;
        document.getElementById('lightboxSlideTitle').textContent = slides.length > 1
            ? `${activeSlideIndex + 1} / ${slides.length} — ${getActiveSlide()?.label || ''}`
            : (getActiveSlide()?.label || '');

        document.getElementById('lightboxPrevBtn').classList.toggle('hidden', slides.length <= 1);
        document.getElementById('lightboxNextBtn').classList.toggle('hidden', slides.length <= 1);
        document.getElementById('previewLightboxModal').classList.remove('hidden');
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function closeFullscreenPreview() {
        document.getElementById('previewLightboxModal').classList.add('hidden');
    }

    async function lightboxSwitch(delta) {
        const next = activeSlideIndex + delta;
        if (next < 0 || next >= slides.length) return;
        await switchSlide(next);
        openFullscreenPreview();
    }

    // Keyboard Shortcuts
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeFullscreenPreview();
            closeRenameSlideModal();
            closeUnivCropModal();
            closePresetMenu();
            closeExportMenu();
            closeShortcutPopover();
            return;
        }
        if (['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement?.tagName)) return;
        if (e.key === 'PageUp' && slides.length > 1) {
            e.preventDefault();
            switchSlide(activeSlideIndex - 1);
            return;
        }
        if (e.key === 'PageDown' && slides.length > 1) {
            e.preventDefault();
            switchSlide(activeSlideIndex + 1);
            return;
        }
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'z' && !e.shiftKey) {
            e.preventDefault();
            undoCanvas();
            return;
        }
        if ((e.ctrlKey || e.metaKey) && (e.key.toLowerCase() === 'y' || (e.shiftKey && e.key.toLowerCase() === 'z'))) {
            e.preventDefault();
            redoCanvas();
            return;
        }
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'd') {
            e.preventDefault();
            duplicateSelectedLayer();
            return;
        }
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'e') {
            e.preventDefault();
            centerSelectedLayer();
            return;
        }
        if (!selectedLayerId) return;
        const layer = layers.find(l => l.id === selectedLayerId);
        if (!layer || layer.locked) return;

        const step = e.shiftKey ? 10 : 1;
        if (e.key === 'ArrowLeft') { e.preventDefault(); layer.x -= step; syncCurrentLayersToState(); renderCanvas(); }
        else if (e.key === 'ArrowRight') { e.preventDefault(); layer.x += step; syncCurrentLayersToState(); renderCanvas(); }
        else if (e.key === 'ArrowUp') { e.preventDefault(); layer.y -= step; syncCurrentLayersToState(); renderCanvas(); }
        else if (e.key === 'ArrowDown') { e.preventDefault(); layer.y += step; syncCurrentLayersToState(); renderCanvas(); }
        else if (e.key === 'Delete' || e.key === 'Backspace') { e.preventDefault(); deleteSelectedLayer(); }
    });

    // ── Presets ──
    function isPresetReusableLayer(layer) {
        if (!layer) return false;
        if (LETTER_DATA.mode === 'letter') {
            if (layer.role === 'body_crop') return false;
            if (layer.type === 'image' && String(layer.url || '').startsWith('data:')) return false;
        }
        return true;
    }

    function getLetterPageOnePresetSource() {
        syncCurrentLayersToState();
        if (LETTER_DATA.mode !== 'letter' || slides.length === 0 || activeSlideIndex === 0) {
            return { list: layers, refW: baseWidth, refH: baseHeight };
        }
        const slide0 = slides[0];
        const norm0 = slide0.useMasterLayout
            ? masterNormalizedLayers
            : (slide0.customNormalizedLayers || masterNormalizedLayers);
        return {
            list: materializeLayers(norm0, slide0.width, slide0.height),
            refW: slide0.width || baseWidth,
            refH: slide0.height || baseHeight,
        };
    }

    function openPresetModal() {
        const { list } = getLetterPageOnePresetSource();
        const reusable = list.filter(isPresetReusableLayer);
        const hasBaseKop = LETTER_DATA.mode === 'letter' && !!(activeBaseLetterhead.url || slides[0]?.baseLetterheadUrl || slides[0]?.dataUrl);

        if (!reusable.length && !hasBaseKop) {
            showStudioToast('Tambahkan minimal 1 elemen (TTD, stempel, nomor surat, penutup, atau base kop) sebelum menyimpan preset.', true);
            return;
        }
        document.getElementById('presetModal').classList.remove('hidden');
        document.getElementById('presetNameInput').focus();
    }
    function closePresetModal() {
        document.getElementById('presetModal').classList.add('hidden');
    }

    function serializeLayersAsPreset(sourceLayers = layers, refW = baseWidth, refH = baseHeight) {
        const safeW = refW || 1;
        const safeH = refH || 1;
        return (sourceLayers || [])
            .filter(isPresetReusableLayer)
            .map(l => ({
                type: l.type,
                role: l.role,
                name: l.name,
                locked: !!l.locked,
                text: l.text || '',
                xPct: l.x / safeW,
                yPct: l.y / safeH,
                fontSizePct: l.fontSize ? l.fontSize / safeH : null,
                fontFamily: l.fontFamily || null,
                bold: !!l.bold,
                italic: !!l.italic,
                underline: !!l.underline,
                strikethrough: !!l.strikethrough,
                uppercase: !!l.uppercase,
                letterSpacing: l.letterSpacing ?? 0,
                textShadow: !!l.textShadow,
                color: l.color || null,
                bgFill: !!l.bgFill,
                bgColor: l.bgColor || '#ffffff',
                bgMinWidthPct: l.bgMinWidthPct || 0,
                align: l.align || 'center',
                widthPct: l.width ? l.width / safeW : null,
                heightPct: l.height ? l.height / safeH : null,
                aspect: l.aspect || 1,
                rotation: l.rotation || 0,
                opacity: l.opacity ?? 100,
                removeBg: !!l.removeBg,
                bgThreshold: l.bgThreshold || 225,
                whiteBacking: !!l.whiteBacking,
                signatoryId: l.signatoryId || null,
                url: l.url || null,
                usePrefixNo: l.type === 'text' && l.role === 'number' && (l.text || '').startsWith('No.'),
                useNomorPrefix: l.type === 'text' && l.role === 'number' && (l.text || '').startsWith('Nomor:'),
            }));
    }

    async function submitSavePreset(e) {
        e.preventDefault();
        const name = document.getElementById('presetNameInput').value.trim();
        if (!name) return;

        const { list, refW, refH } = getLetterPageOnePresetSource();
        const configPayload = {
            mode: LETTER_DATA.mode,
            items: serializeLayersAsPreset(list, refW, refH),
        };
        if (LETTER_DATA.mode === 'letter') {
            configPayload.baseLetterheadUrl = activeBaseLetterhead.url || slides[0]?.baseLetterheadUrl || slides[0]?.dataUrl || null;
            configPayload.baseLetterheadName = activeBaseLetterhead.name || slides[0]?.baseLetterheadName || LETTER_DATA.defaultLetterheadName || 'Kop Surat FEB UNJ';
        }

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
                config: configPayload,
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
        const id = document.getElementById('presetSelect').value;
        const preset = PRESETS_DATA[id];
        if (!preset || !preset.config || !Array.isArray(preset.config.items)) {
            showStudioToast('Pilih preset posisi terlebih dahulu.', true);
            return;
        }

        if (LETTER_DATA.mode === 'letter' && preset.config.baseLetterheadUrl) {
            const currentBaseUrl = activeBaseLetterhead.url || slides[0]?.baseLetterheadUrl || slides[0]?.dataUrl;
            if (slides.length === 0 || currentBaseUrl !== preset.config.baseLetterheadUrl) {
                await selectLetterheadAsBase(
                    preset.config.baseLetterheadUrl,
                    preset.config.baseLetterheadName || 'Kop Surat FEB UNJ'
                );
            }
        }

        if (!ensureBaseReady()) return;

        if (LETTER_DATA.mode === 'letter' && slides.length > 0 && activeSlideIndex !== 0) {
            await switchSlide(0);
        }

        const normItems = [];
        for (const item of preset.config.items) {
            if (LETTER_DATA.mode === 'letter') {
                if (item.role === 'body_crop') continue;
                if (item.type === 'image' && String(item.url || '').startsWith('data:')) continue;
            }

            if (item.type === 'whiteout') {
                normItems.push({
                    id: uid('wht_'),
                    type: 'whiteout',
                    role: item.role || 'whiteout',
                    name: item.name || 'Kotak Penutup',
                    locked: !!item.locked,
                    xPct: item.xPct ?? 0.5,
                    yPct: item.yPct ?? 0.5,
                    widthPct: item.widthPct ?? 0.35,
                    heightPct: item.heightPct ?? 0.05,
                    color: item.color || '#ffffff',
                    opacity: item.opacity ?? 100,
                    rotation: item.rotation || 0,
                });
            } else if (item.type === 'text') {
                let resolvedText = item.text || '';
                if ((item.role || 'number') === 'number') {
                    resolvedText = item.useNomorPrefix
                        ? ('Nomor: ' + LETTER_DATA.number)
                        : (item.usePrefixNo ? ('No. ' + LETTER_DATA.number) : LETTER_DATA.number);
                } else if (item.role === 'sig_block_date') {
                    resolvedText = LETTER_DATA.primarySigPosition || item.text || '';
                } else if (item.role === 'sig_block_name' && LETTER_DATA.primarySigName) {
                    resolvedText = LETTER_DATA.primarySigName;
                } else if (item.role === 'sig_block_nip' && LETTER_DATA.primarySigNip) {
                    resolvedText = `NIP : ${LETTER_DATA.primarySigNip}`;
                }

                normItems.push({
                    id: uid(),
                    type: 'text',
                    role: item.role || 'number',
                    name: item.name || 'Teks',
                    locked: !!item.locked,
                    text: resolvedText,
                    xPct: item.xPct ?? 0.5,
                    yPct: item.yPct ?? 0.308,
                    fontSizePct: item.fontSizePct ?? 0.025,
                    fontFamily: item.fontFamily || 'Times New Roman, Liberation Serif, serif',
                    bold: !!item.bold,
                    italic: !!item.italic,
                    underline: !!item.underline,
                    strikethrough: !!item.strikethrough,
                    uppercase: !!item.uppercase,
                    letterSpacing: item.letterSpacing ?? 0,
                    textShadow: !!item.textShadow,
                    color: item.color || '#111827',
                    bgFill: !!item.bgFill,
                    bgColor: item.bgColor || '#ffffff',
                    bgMinWidthPct: item.bgMinWidthPct || 0,
                    align: item.align || 'center',
                    opacity: item.opacity ?? 100,
                    rotation: item.rotation || 0,
                });
            } else if (item.type === 'image') {
                let targetUrl = item.url;
                let targetSigId = item.signatoryId || null;
                if (item.role === 'signature' && LETTER_DATA.canUsePrimarySig && LETTER_DATA.primarySigUrl) {
                    targetUrl = LETTER_DATA.primarySigUrl;
                    targetSigId = LETTER_DATA.primarySigId || targetSigId;
                }
                if (!targetUrl) continue;
                try {
                    const img = await loadCachedImage(targetUrl);
                    const aspect = (img.naturalHeight || 1) / (img.naturalWidth || 1);
                    normItems.push({
                        id: uid(),
                        type: 'image',
                        role: item.role,
                        name: item.name || 'Aset',
                        locked: !!item.locked,
                        url: targetUrl,
                        xPct: item.xPct ?? 0.5,
                        yPct: item.yPct ?? 0.74,
                        widthPct: item.widthPct ?? 0.18,
                        aspect: aspect,
                        rotation: item.rotation || 0,
                        opacity: item.opacity ?? 100,
                        removeBg: !!item.removeBg,
                        bgThreshold: item.bgThreshold || 225,
                        whiteBacking: !!item.whiteBacking || item.role === 'letterhead',
                        signatoryId: targetSigId,
                    });
                } catch {}
            }
        }

        if (LETTER_DATA.mode === 'letter' && slides.length > 0) {
            const slide0 = slides[0];
            const existingBodyNorm = normalizeLayers(
                layers.filter(l => !isPresetReusableLayer(l)),
                baseWidth,
                baseHeight
            );
            const combinedNorm = [...existingBodyNorm, ...normItems];
            slide0.useMasterLayout = false;
            slide0.customNormalizedLayers = combinedNorm;
            layers = materializeLayers(combinedNorm, baseWidth, baseHeight);
        } else {
            masterNormalizedLayers = normItems;
            slides.forEach(s => {
                s.useMasterLayout = true;
                s.customNormalizedLayers = null;
            });
            layers = materializeLayers(masterNormalizedLayers, baseWidth, baseHeight);
        }

        await preloadLayerImages();
        selectedLayerId = layers[0]?.id || null;
        pushHistory();
        renderCanvas();
        syncInspector();
        renderLayerList();
        renderFilmstrip();
        updateMultiSlideUI();
        showStudioToast(LETTER_DATA.mode === 'letter'
            ? `Preset "${preset.name}" diterapkan pada Halaman 1 (base kop & elemen surat).`
            : `Preset "${preset.name}" diterapkan ke seluruh sertifikat.`);
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
        const qaFile = document.getElementById('qa_file');
        const qaFileLabel = document.getElementById('qaFileLabel');
        if (qaFile) qaFile.value = '';
        if (type === 'signature') {
            document.getElementById('qaModalTitle').textContent = 'Upload TTD Digital';
            document.getElementById('qaNameLabel').textContent = 'Nama Pejabat / Label TTD';
            if (qaFile) qaFile.accept = '.png,.jpg,.jpeg,.webp';
            if (qaFileLabel) qaFileLabel.textContent = 'File Gambar (PNG/JPG/WEBP, maks. 15 MB)';
        } else if (type === 'letterhead') {
            document.getElementById('qaModalTitle').textContent = 'Upload Template Kop Surat';
            document.getElementById('qaNameLabel').textContent = 'Nama Template (Misal: Kop FEB UNJ)';
            if (qaFile) qaFile.accept = '.pdf,.png,.jpg,.jpeg,.webp';
            if (qaFileLabel) qaFileLabel.textContent = 'File Template Kop (PDF hasil Save As Word / PNG / JPG)';
        } else {
            document.getElementById('qaModalTitle').textContent = 'Upload Stempel Sertifikat';
            document.getElementById('qaNameLabel').textContent = 'Nama Stempel';
            if (qaFile) qaFile.accept = '.png,.jpg,.jpeg,.webp';
            if (qaFileLabel) qaFileLabel.textContent = 'File Gambar (PNG/JPG/WEBP, maks. 15 MB)';
        }
        document.getElementById('qa_name').value = signatoryName || '';
        document.getElementById('qaOpacityWrap').classList.toggle('hidden', type !== 'stamp');
        document.getElementById('quickAssetModal').classList.remove('hidden');
    }

    function closeQuickUploadModal() {
        document.getElementById('quickAssetModal').classList.add('hidden');
    }

    async function convertPdfFirstPageToPngBlob(pdfFile) {
        const buffer = await pdfFile.arrayBuffer();
        const pdfDoc = await pdfjsLib.getDocument({ data: buffer }).promise;
        const page = await pdfDoc.getPage(1);
        const vp = page.getViewport({ scale: 3.0 });
        const off = document.createElement('canvas');
        off.width = Math.round(vp.width);
        off.height = Math.round(vp.height);
        await page.render({ canvasContext: off.getContext('2d'), viewport: vp }).promise;
        return new Promise((resolve) => off.toBlob(resolve, 'image/png'));
    }

    async function submitQuickAsset(e) {
        e.preventDefault();
        const btn = document.getElementById('qaSubmitBtn');
        btn.disabled = true;
        btn.textContent = 'Memproses...';

        try {
            const formEl = document.getElementById('quickAssetForm');
            const fd = new FormData(formEl);
            const assetType = document.getElementById('qa_type').value;
            const rawFile = document.getElementById('qa_file')?.files?.[0];

            if (assetType === 'letterhead' && rawFile && rawFile.name.toLowerCase().endsWith('.pdf')) {
                btn.textContent = 'Mengonversi PDF ke PNG...';
                const pngBlob = await convertPdfFirstPageToPngBlob(rawFile);
                if (pngBlob) {
                    fd.set('asset_file', pngBlob, 'kop_template.png');
                }
            }

            btn.textContent = 'Mengunggah...';
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
                if (data.asset.type === 'letterhead') {
                    LETTER_DATA.defaultLetterheadUrl = data.asset.url;
                    LETTER_DATA.defaultLetterheadName = data.asset.name;
                    const gallery = document.getElementById('letterheadsGallery');
                    if (gallery) {
                        const emptyMsg = gallery.querySelector('.border-dashed');
                        if (emptyMsg) emptyMsg.remove();
                        const card = document.createElement('button');
                        card.type = 'button';
                        card.className = 'shortcut-chip w-full flex flex-col gap-1.5 p-2 rounded-xl bg-gray-50 hover:bg-emerald-50/70 border border-gray-200/80 hover:border-emerald-300 text-left cursor-pointer';
                        card.onclick = () => selectLetterheadAsBase(data.asset.url, data.asset.name);
                        card.innerHTML = `
                            <div class="w-full h-16 bg-white rounded-lg border border-gray-100 flex items-center justify-center overflow-hidden p-1">
                                <img src="${data.asset.url}" alt="${data.asset.name}" class="max-h-full max-w-full object-contain">
                            </div>
                            <div class="flex items-center justify-between gap-1 px-0.5">
                                <span class="text-[11px] font-bold text-gray-800 truncate">${data.asset.name}</span>
                                <span class="text-[9.5px] font-bold text-emerald-700 bg-emerald-100 px-1.5 py-0.5 rounded flex-shrink-0">Pakai</span>
                            </div>`;
                        gallery.prepend(card);
                        if (typeof lucide !== 'undefined') lucide.createIcons();
                    }
                    closeQuickUploadModal();
                    await selectLetterheadAsBase(data.asset.url, data.asset.name);
                    return;
                }
                closeQuickUploadModal();
                showStudioToast(data.message);
                if (baseImage) {
                    await addImageLayer(
                        data.asset.type,
                        data.asset.url,
                        data.asset.name,
                        data.asset.default_opacity || 100,
                        null,
                        data.asset.signatory_id || null
                    );
                } else {
                    setTimeout(() => window.location.reload(), 700);
                }
            } else {
                showStudioToast(data.message || 'Upload gagal.', true);
            }
        } catch (err) {
            console.error(err);
            showStudioToast('Terjadi kesalahan saat mengunggah aset.', true);
        } finally {
            btn.disabled = false;
            btn.textContent = 'Upload & Simpan';
        }
    }

    // ── Offscreen Rendering & Export Helpers ──
    function sanitizeFileSegment(value, maxLength = 40) {
        let cleaned = String(value || '')
            .replace(/[<>:"\/\\|?*\x00-\x1F]+/g, '_')
            .replace(/\s+/g, '_')
            .replace(/_+/g, '_')
            .replace(/^[_-]+|[_-]+$/g, '');
        if (!cleaned) return '';
        if (cleaned.length <= maxLength) return cleaned;

        let cut = cleaned.slice(0, maxLength);
        const lastUnderscore = cut.lastIndexOf('_');
        if (lastUnderscore >= Math.floor(maxLength * 0.45)) {
            cut = cut.slice(0, lastUnderscore);
        }
        return cut.replace(/^[_-]+|[_-]+$/g, '') || cleaned.slice(0, maxLength);
    }

    function safeFilename(ext, suffix = '') {
        const fallbackPrefix = LETTER_DATA.mode === 'letter' ? 'Surat' : 'Sertifikat';
        let studentPart = sanitizeFileSegment(LETTER_DATA.studentName || LETTER_DATA.recipient, 40);
        let purposePart = sanitizeFileSegment(LETTER_DATA.purposeName || LETTER_DATA.subject, 40);
        const numberPart = sanitizeFileSegment(LETTER_DATA.number, 45);
        const cleanSuffix = String(suffix || '');

        const buildBase = () => [studentPart, purposePart, numberPart].filter(Boolean).join('-') + cleanSuffix;
        let base = buildBase();

        if (base.length > 120 && purposePart) {
            const excess = base.length - 120;
            purposePart = sanitizeFileSegment(purposePart, Math.max(12, purposePart.length - excess));
            base = buildBase();
        }
        if (base.length > 120 && studentPart) {
            const excess = base.length - 120;
            studentPart = sanitizeFileSegment(studentPart, Math.max(12, studentPart.length - excess));
            base = buildBase();
        }

        if (!base) base = fallbackPrefix + cleanSuffix;
        return `${base}.${ext}`;
    }

    function safeSlideSlug(label, idx) {
        const defaultBase = LETTER_DATA.mode === 'letter' ? 'Surat' : 'Sertifikat';
        const prefix = String(idx + 1).padStart(2, '0');
        const clean = (label || `${defaultBase}_${prefix}`).replace(/[^A-Za-z0-9\-_]+/g, '_').replace(/^_+|_+$/g, '');
        return `${prefix}_${clean || defaultBase}`;
    }

    async function stampProtectedSignaturesOnServer(cleanCanvas, layerList, w, h, format = 'png') {
        const protectedSigs = layerList.filter(l => l.type === 'image' && l.role === 'signature' && l.signatoryId);
        if (protectedSigs.length === 0) {
            return cleanCanvas;
        }

        const instructions = protectedSigs.map(l => ({
            signatory_id: l.signatoryId,
            xPct: l.x / w,
            yPct: l.y / h,
            widthPct: l.width / w,
            rotation: l.rotation || 0,
            opacity: l.opacity ?? 100,
            removeBg: !!l.removeBg,
            bgThreshold: l.bgThreshold || 225,
        }));

        const pageBlob = await new Promise(res => cleanCanvas.toBlob(res, 'image/png'));
        const fd = new FormData();
        fd.append('page_image', pageBlob, 'page.png');
        fd.append('signatures', JSON.stringify(instructions));
        fd.append('letter_id', LETTER_DATA.id);
        fd.append('format', format === 'jpg' ? 'jpg' : 'png');

        const resp = await fetch(LETTER_DATA.stampSignaturesUrl, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: fd,
        });

        if (!resp.ok) {
            let errMsg = 'Gagal menempelkan TTD terproteksi di server.';
            try {
                const errJson = await resp.json();
                if (errJson?.message) errMsg = errJson.message;
            } catch {}
            throw new Error(errMsg);
        }

        const stampedBlob = await resp.blob();
        const objUrl = URL.createObjectURL(stampedBlob);
        try {
            const stampedImg = await new Promise((resolve, reject) => {
                const img = new Image();
                img.onload = () => resolve(img);
                img.onerror = reject;
                img.src = objUrl;
            });

            const out = document.createElement('canvas');
            out.width = w;
            out.height = h;
            out.getContext('2d').drawImage(stampedImg, 0, 0, w, h);
            return out;
        } finally {
            URL.revokeObjectURL(objUrl);
        }
    }

    async function renderSlideToOffscreenCanvas(slide, forRealExport = true, outputFormat = 'png') {
        const bgImg = await loadSlideHighResImage(slide);
        const w = slide.width || bgImg.naturalWidth || bgImg.width;
        const h = slide.height || bgImg.naturalHeight || bgImg.height;

        const norm = slide.useMasterLayout
            ? masterNormalizedLayers
            : (slide.customNormalizedLayers || masterNormalizedLayers);
        const slideLayers = materializeLayers(norm, w, h);

        const hasProtectedSig = forRealExport && slideLayers.some(l => l.type === 'image' && l.role === 'signature' && l.signatoryId);

        const urls = [...new Set(slideLayers.filter(l => l.type === 'image' && !(hasProtectedSig && l.role === 'signature' && l.signatoryId)).map(l => l.url))];
        await Promise.all(urls.map(u => loadCachedImage(u).catch(() => null)));

        const off = document.createElement('canvas');
        off.width = w;
        off.height = h;
        const octx = off.getContext('2d');
        drawLayersOnContext(octx, w, h, bgImg, slideLayers, hasProtectedSig);

        const finalCanvas = hasProtectedSig
            ? await stampProtectedSignaturesOnServer(off, slideLayers, w, h, outputFormat)
            : off;

        return { canvas: finalCanvas, width: w, height: h };
    }

    function buildSinglePdfFromCanvas(srcCanvas, w, h, quality = 0.94) {
        const imgData = srcCanvas.toDataURL('image/jpeg', quality);
        const orientation = w >= h ? 'landscape' : 'portrait';
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF({
            orientation,
            unit: 'px',
            format: [w, h],
            hotfixes: ['px_scaling'],
        });
        pdf.addImage(imgData, 'JPEG', 0, 0, w, h);
        return pdf;
    }

    async function buildMultiPagePdf(jpegQuality = 0.91) {
        const { jsPDF } = window.jspdf;
        let pdf = null;

        for (let i = 0; i < slides.length; i++) {
            const slide = slides[i];
            const pct = Math.round(((i + 0.5) / slides.length) * 100);
            setBatchProgress(true, 'Menyusun PDF Gabungan...', `Memproses sertifikat ${i + 1} dari ${slides.length} (${slide.label})...`, pct);

            const rendered = await renderSlideToOffscreenCanvas(slide);
            const orientation = rendered.width >= rendered.height ? 'landscape' : 'portrait';
            const imgData = rendered.canvas.toDataURL('image/jpeg', jpegQuality);

            if (i === 0) {
                pdf = new jsPDF({
                    orientation,
                    unit: 'px',
                    format: [rendered.width, rendered.height],
                    hotfixes: ['px_scaling'],
                });
            } else {
                pdf.addPage([rendered.width, rendered.height], orientation);
            }

            pdf.addImage(imgData, 'JPEG', 0, 0, rendered.width, rendered.height);
        }

        return pdf;
    }

    function sendExportLog(format, pageCount = 1) {
        fetch(LETTER_DATA.logExportUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ format, page_count: pageCount }),
        }).catch(() => {});
    }

    async function exportSingleSlide(format) {
        if (!ensureBaseReady()) return;
        syncCurrentLayersToState();

        const suffix = slides.length > 1 ? `_Hal${activeSlideIndex + 1}` : '';
        const slide = getActiveSlide();

        try {
            setBatchProgress(true, 'Memproses Dokumen Final...', 'Menyiapkan hasil resolusi tinggi & TTD terproteksi...', 45);
            const rendered = await renderSlideToOffscreenCanvas(slide, true, format === 'jpg' ? 'jpg' : 'png');

            if (format === 'pdf') {
                const pdf = buildSinglePdfFromCanvas(rendered.canvas, rendered.width, rendered.height, 0.95);
                pdf.save(safeFilename('pdf', suffix));
            } else {
                const mime = format === 'jpg' ? 'image/jpeg' : 'image/png';
                const dataUrl = rendered.canvas.toDataURL(mime, 0.96);

                const link = document.createElement('a');
                link.download = safeFilename(format, suffix);
                link.href = dataUrl;
                link.click();
            }

            sendExportLog(format, 1);
            showStudioToast(`Dokumen halaman ${activeSlideIndex + 1} berhasil di-export (${format.toUpperCase()})!`);
        } catch (err) {
            console.error(err);
            showStudioToast(err?.message || 'Gagal mengekspor dokumen.', true);
        } finally {
            setBatchProgress(false);
        }
    }

    async function exportBatchPdf() {
        if (!ensureBaseReady()) return;
        syncCurrentLayersToState();

        try {
            const pdf = await buildMultiPagePdf(0.92);
            setBatchProgress(true, 'Menyelesaikan...', 'Menyimpan file PDF...', 100);
            pdf.save(safeFilename('pdf', `_${slides.length}Hal`));
            sendExportLog('pdf_batch', slides.length);
            showStudioToast(`${slides.length} sertifikat berhasil digabung menjadi 1 PDF!`);
        } catch (err) {
            console.error(err);
            showStudioToast('Gagal mengekspor PDF gabungan.', true);
        } finally {
            setBatchProgress(false);
        }
    }

    async function exportBatchZip(itemFormat) {
        if (!ensureBaseReady()) return;
        if (typeof JSZip === 'undefined') {
            showStudioToast('Library JSZip gagal dimuat. Periksa koneksi internet Anda.', true);
            return;
        }
        syncCurrentLayersToState();

        try {
            const zip = new JSZip();

            for (let i = 0; i < slides.length; i++) {
                const slide = slides[i];
                const pct = Math.round(((i + 0.5) / slides.length) * 90);
                setBatchProgress(true, `Menyiapkan ZIP (${itemFormat.toUpperCase()})...`, `Memproses sertifikat ${i + 1} dari ${slides.length} (${slide.label})...`, pct);

                const rendered = await renderSlideToOffscreenCanvas(slide);
                const fileSlug = safeSlideSlug(slide.fileName || slide.label, i);

                if (itemFormat === 'pdf') {
                    const singlePdf = buildSinglePdfFromCanvas(rendered.canvas, rendered.width, rendered.height, 0.94);
                    zip.file(`${fileSlug}.pdf`, singlePdf.output('blob'));
                } else {
                    const mime = itemFormat === 'jpg' ? 'image/jpeg' : 'image/png';
                    const blob = await new Promise(res => rendered.canvas.toBlob(res, mime, 0.95));
                    zip.file(`${fileSlug}.${itemFormat}`, blob);
                }
            }

            setBatchProgress(true, 'Mengompres ZIP...', 'Membuat arsip download...', 96);
            const zipBlob = await zip.generateAsync({ type: 'blob' });
            const url = URL.createObjectURL(zipBlob);
            const a = document.createElement('a');
            a.href = url;
            a.download = safeFilename('zip', `_${itemFormat.toUpperCase()}`);
            a.click();
            setTimeout(() => URL.revokeObjectURL(url), 5000);

            sendExportLog(`zip_${itemFormat}`, slides.length);
            showStudioToast(`${slides.length} file ${itemFormat.toUpperCase()} berhasil diunduh dalam 1 ZIP!`);
        } catch (err) {
            console.error(err);
            showStudioToast('Gagal membuat file ZIP sertifikat.', true);
        } finally {
            setBatchProgress(false);
        }
    }

    function markTopbarArchived(driveUrl) {
        const oldPill = document.getElementById('topGdriveStatusPill');
        if (!oldPill) return;
        const link = document.createElement('a');
        link.id = 'topGdriveStatusPill';
        link.href = driveUrl || '#';
        link.target = '_blank';
        link.rel = 'noopener noreferrer';
        link.className = 'hidden sm:inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/80 hover:bg-emerald-100 transition-colors whitespace-nowrap';
        link.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span><span>Terarsip</span><i data-lucide="external-link" class="w-2.5 h-2.5"></i>';
        oldPill.replaceWith(link);
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    async function exportAndArchiveToGdrive() {
        if (!ensureBaseReady()) return;
        if (!LETTER_DATA.gdriveReady) {
            showStudioToast('Google Drive belum terhubung. Hubungi Admin untuk menghubungkan di Pengaturan Sistem.', true);
            return;
        }
        syncCurrentLayersToState();

        const btn = document.getElementById('btnArchiveGdrive');
        const origHtml = btn.innerHTML;
        btn.disabled = true;

        try {
            let blob;
            let filename;

            if (slides.length > 1) {
                const pdf = await buildMultiPagePdf(0.88);
                setBatchProgress(true, 'Mengunggah ke Google Drive...', `Mengirim ${slides.length} halaman ke Google Drive...`, 96);
                blob = pdf.output('blob');
                filename = safeFilename('pdf', `_${slides.length}Hal`);
            } else {
                setBatchProgress(true, 'Menyiapkan PDF Final...', 'Menempelkan komponen & TTD terproteksi...', 45);
                const rendered = await renderSlideToOffscreenCanvas(getActiveSlide(), true, 'png');
                setBatchProgress(true, 'Mengunggah ke Google Drive...', 'Mengirim dokumen ke Google Drive...', 90);
                const pdf = buildSinglePdfFromCanvas(rendered.canvas, rendered.width, rendered.height, 0.93);
                blob = pdf.output('blob');
                filename = safeFilename('pdf');
            }

            const fd = new FormData();
            fd.append('archive_file', blob, filename);

            const resp = await fetch(LETTER_DATA.archiveUrl, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: fd,
            });
            const data = await resp.json();
            if (data.success) {
                if (data.data?.drive_url) {
                    markTopbarArchived(data.data.drive_url);
                }
                showStudioToast(data.message || 'Sertifikat berhasil disimpan & diarsipkan ke Google Drive!');
            } else {
                showStudioToast(data.message || 'Gagal mengarsipkan ke Google Drive.', true);
            }
        } catch (err) {
            showStudioToast('Terjadi kesalahan jaringan saat mengarsipkan sertifikat.', true);
        } finally {
            setBatchProgress(false);
            btn.disabled = false;
            btn.innerHTML = origHtml;
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }
    }

    function bindFileDropTarget(el, preferCropOnLetter = false) {
        if (!el) return;
        ['dragenter', 'dragover'].forEach(e => el.addEventListener(e, ev => {
            ev.preventDefault();
            el.classList.add('border-[#FF4D00]', 'bg-orange-50/60');
        }));
        ['dragleave', 'drop'].forEach(e => el.addEventListener(e, ev => {
            ev.preventDefault();
            el.classList.remove('border-[#FF4D00]', 'bg-orange-50/60');
        }));
        el.addEventListener('drop', ev => {
            if (ev.dataTransfer?.files?.length) {
                if (LETTER_DATA.mode === 'letter' && preferCropOnLetter) {
                    openUnivCropModalFromFile(ev.dataTransfer.files[0]);
                } else {
                    handleBaseFilesUpload(ev.dataTransfer.files, slides.length > 0);
                }
            }
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        if (typeof lucide !== 'undefined') lucide.createIcons();
        initUnivCropInteraction();

        document.addEventListener('click', (e) => {
            if (!document.getElementById('exportMenuWrap')?.contains(e.target)) {
                closeExportMenu();
            }
            if (!document.getElementById('presetPopoverWrap')?.contains(e.target)) {
                closePresetMenu();
            }
            if (!document.getElementById('shortcutPopoverWrap')?.contains(e.target)) {
                closeShortcutPopover();
            }
        });

        bindFileDropTarget(document.getElementById('baseDropZone'), true);
        bindFileDropTarget(document.getElementById('emptyStageState'), false);
    });
</script>
@endpush
