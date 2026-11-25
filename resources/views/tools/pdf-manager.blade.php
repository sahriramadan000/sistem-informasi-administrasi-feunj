@extends('layouts.app')

@section('title', 'Kelola & Watermark PDF')

@section('content')
<div class="space-y-5">
    {{-- Header & Tab Switcher --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 bg-white p-4 rounded-2xl border border-gray-200/80 shadow-2xs">
        <div class="flex items-center gap-3">
            <a href="{{ route('tools.index') }}" class="w-9 h-9 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 flex items-center justify-center transition-colors" title="Kembali ke Semua Tools">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
            </a>
            <div>
                <h2 class="text-base font-extrabold text-gray-900">Kelola Halaman &amp; Watermark PDF</h2>
                <p class="text-xs text-gray-500">Gabung PDF tanpa menurunkan kualitas teks, putar/pisah/hapus halaman, serta pasang watermark dokumen.</p>
            </div>
        </div>

        <div class="flex flex-wrap p-1 rounded-xl bg-gray-100 border border-gray-200/80 self-start md:self-auto gap-1">
            <button type="button" id="pmTabBtn-merge" onclick="switchPmTab('merge')" class="px-3 py-1.5 rounded-lg text-xs font-bold cursor-pointer transition-all">
                Gabung PDF (Merge)
            </button>
            <button type="button" id="pmTabBtn-split" onclick="switchPmTab('split')" class="px-3 py-1.5 rounded-lg text-xs font-bold cursor-pointer transition-all">
                Pisah / Putar / Hapus
            </button>
            <button type="button" id="pmTabBtn-watermark" onclick="switchPmTab('watermark')" class="px-3 py-1.5 rounded-lg text-xs font-bold cursor-pointer transition-all">
                Watermark Dokumen
            </button>
        </div>
    </div>

    <div id="pmProgress" class="hidden bg-orange-50 border border-orange-200 rounded-2xl p-4 flex items-center gap-3">
        <span class="w-5 h-5 border-2 border-[#FF4D00] border-t-transparent rounded-full animate-spin flex-shrink-0"></span>
        <span id="pmProgressText" class="text-xs font-bold text-gray-800">Memproses dokumen...</span>
    </div>

    {{-- ======================= TAB 1: MERGE PDF ======================= --}}
    <div id="pmPanel-merge" class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-start">
        <div class="lg:col-span-4 bg-white rounded-2xl border border-gray-200/80 p-5 space-y-4 shadow-2xs">
            <label class="flex flex-col items-center justify-center gap-2 p-5 border-2 border-dashed border-rose-300 hover:border-rose-500 rounded-2xl bg-rose-50/40 hover:bg-rose-50 transition-all cursor-pointer text-center">
                <div class="w-10 h-10 rounded-xl bg-white border border-rose-200 text-rose-600 flex items-center justify-center shadow-2xs">
                    <i data-lucide="file-plus-2" class="w-5 h-5"></i>
                </div>
                <span class="text-xs font-extrabold text-gray-900">Tambah File PDF (Bisa Banyak)</span>
                <span class="text-[11px] text-gray-500">Kualitas teks vektor asli tetap terjaga 100%</span>
                <input type="file" id="mergePdfInput" accept=".pdf" multiple class="hidden" onchange="addMergePdfs(this.files)">
            </label>

            <button type="button" id="mergeExportBtn" onclick="executeMergePdf()" disabled
                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-[#FF4D00] to-amber-500 disabled:opacity-40 text-white text-xs font-bold shadow-xs cursor-pointer">
                <i data-lucide="files" class="w-4 h-4"></i>
                <span>Gabungkan &amp; Download 1 PDF</span>
            </button>
        </div>

        <div class="lg:col-span-8 bg-white rounded-2xl border border-gray-200/80 p-5 space-y-3 shadow-2xs min-h-[300px]">
            <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                <span id="mergeCountLabel" class="text-xs font-bold text-gray-700">Daftar PDF (0 file)</span>
                <button type="button" onclick="mergeQueue = []; renderMergeList();" class="text-[11px] font-bold text-rose-600 hover:underline cursor-pointer">Kosongkan</button>
            </div>
            <div id="mergeFileList" class="space-y-2">
                <div class="py-14 text-center text-xs text-gray-400">Tambahkan minimal 2 file PDF di sebelah kiri untuk digabungkan berurutan.</div>
            </div>
        </div>
    </div>

    {{-- ======================= TAB 2: SPLIT / ROTATE / DELETE ======================= --}}
    <div id="pmPanel-split" class="hidden grid grid-cols-1 lg:grid-cols-12 gap-4 items-start">
        <div class="lg:col-span-4 bg-white rounded-2xl border border-gray-200/80 p-5 space-y-4 shadow-2xs">
            <label class="flex flex-col items-center justify-center gap-2 p-5 border-2 border-dashed border-purple-300 hover:border-purple-500 rounded-2xl bg-purple-50/40 hover:bg-purple-50 transition-all cursor-pointer text-center">
                <div class="w-10 h-10 rounded-xl bg-white border border-purple-200 text-purple-600 flex items-center justify-center shadow-2xs">
                    <i data-lucide="scissors" class="w-5 h-5"></i>
                </div>
                <span class="text-xs font-extrabold text-gray-900">Pilih 1 File PDF</span>
                <span id="splitFileLabel" class="text-[11px] text-gray-500 truncate max-w-full">Belum ada file dipilih</span>
                <input type="file" id="splitPdfInput" accept=".pdf" class="hidden" onchange="loadPdfForSplit(this.files[0])">
            </label>

            <div>
                <label class="block text-[11px] font-bold text-gray-600 mb-1">Pilih Cepat Rentang Halaman</label>
                <div class="flex gap-1.5">
                    <input type="text" id="splitRangeInput" placeholder="contoh: 1-3, 5"
                        class="flex-1 px-3 py-2 text-xs bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-purple-500">
                    <button type="button" onclick="applySplitRangeSelection()" class="px-3 py-2 rounded-xl bg-purple-50 hover:bg-purple-100 text-purple-700 text-xs font-bold cursor-pointer">Pilih</button>
                </div>
            </div>

            <div class="space-y-2 pt-1">
                <button type="button" id="splitSavePdfBtn" onclick="exportSplitMergedPdf()" disabled
                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-[#FF4D00] to-amber-500 disabled:opacity-40 text-white text-xs font-bold shadow-xs cursor-pointer">
                    <i data-lucide="file-check" class="w-4 h-4"></i>
                    <span>Simpan Halaman Terpilih (1 PDF)</span>
                </button>
                <button type="button" id="splitSaveZipBtn" onclick="exportSplitZip()" disabled
                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-gray-900 disabled:opacity-40 text-white text-xs font-bold cursor-pointer">
                    <i data-lucide="folder-archive" class="w-4 h-4"></i>
                    <span>Pisah Tiap Halaman ke ZIP</span>
                </button>
            </div>
        </div>

        <div class="lg:col-span-8 bg-white rounded-2xl border border-gray-200/80 p-5 space-y-3 shadow-2xs min-h-[340px]">
            <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                <span id="splitInfoLabel" class="text-xs font-bold text-gray-700">Kelola Halaman PDF</span>
                <div class="flex gap-2">
                    <button type="button" onclick="rotateAllSplitPages()" class="px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-gray-200 text-[11px] font-bold text-gray-700 cursor-pointer">Putar Semua 90&deg;</button>
                    <button type="button" onclick="splitPages.forEach(p => p.included = true); renderSplitGrid();" class="px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-gray-200 text-[11px] font-bold text-gray-700 cursor-pointer">Centang Semua</button>
                </div>
            </div>
            <div id="splitPagesGrid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                <div class="col-span-full py-16 text-center text-xs text-gray-400">
                    Unggah file PDF di sebelah kiri untuk memutar, menghapus, atau memisahkan halaman secara lossless.
                </div>
            </div>
        </div>
    </div>

    {{-- ======================= TAB 3: WATERMARK PDF & GAMBAR ======================= --}}
    <div id="pmPanel-watermark" class="hidden grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
        <div class="lg:col-span-5 bg-white rounded-2xl border border-gray-200/80 p-5 space-y-4 shadow-2xs">
            <label class="flex flex-col items-center justify-center gap-1.5 p-4 border-2 border-dashed border-teal-300 hover:border-teal-500 rounded-2xl bg-teal-50/40 cursor-pointer text-center">
                <i data-lucide="stamp" class="w-5 h-5 text-teal-600"></i>
                <span class="text-xs font-extrabold text-gray-900">Pilih File PDF atau Gambar (JPG/PNG)</span>
                <span id="wmFileName" class="text-[11px] text-gray-500 truncate max-w-full">Belum ada file dipilih</span>
                <input type="file" id="wmInput" accept=".pdf,image/*" class="hidden" onchange="loadWatermarkTarget(this.files[0])">
            </label>

            <div>
                <label class="block text-[11px] font-bold text-gray-600 mb-1.5">Preset Teks Watermark Cepat</label>
                <div class="flex flex-wrap gap-1.5">
                    @foreach (['SALINAN', 'DRAFT', 'RAHASIA', 'ARSIP FEB UNJ', 'LEGALISIR'] as $wmPreset)
                        <button type="button" onclick="document.getElementById('wmText').value='{{ $wmPreset }}'; renderWatermarkPreview();"
                            class="px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-teal-50 hover:text-teal-700 text-[11px] font-bold text-gray-700 cursor-pointer">
                            {{ $wmPreset }}
                        </button>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Teks Watermark</label>
                <input type="text" id="wmText" value="ARSIP FEB UNJ" oninput="renderWatermarkPreview()"
                    class="w-full px-3 py-2 text-xs font-bold bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-teal-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <div class="flex justify-between text-[11px] font-bold text-gray-600 mb-1">
                        <span>Transparansi</span><span id="wmOpLbl" class="font-mono">24%</span>
                    </div>
                    <input type="range" id="wmOpacity" min="8" max="75" value="24" oninput="document.getElementById('wmOpLbl').textContent = this.value + '%'; renderWatermarkPreview();" class="w-full accent-teal-600">
                </div>
                <div>
                    <div class="flex justify-between text-[11px] font-bold text-gray-600 mb-1">
                        <span>Ukuran Teks</span><span id="wmSizeLbl" class="font-mono">46px</span>
                    </div>
                    <input type="range" id="wmSize" min="18" max="110" value="46" oninput="document.getElementById('wmSizeLbl').textContent = this.value + 'px'; renderWatermarkPreview();" class="w-full accent-teal-600">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-2.5 items-end">
                <div>
                    <label class="block text-[11px] font-bold text-gray-600 mb-1">Warna</label>
                    <input type="color" id="wmColor" value="#DC2626" onchange="renderWatermarkPreview()" class="w-full h-9 rounded-lg border border-gray-200 cursor-pointer">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-gray-600 mb-1">Sudut (&deg;)</label>
                    <input type="number" id="wmAngle" min="-90" max="90" value="-35" oninput="renderWatermarkPreview()" class="w-full px-2.5 py-2 text-xs bg-gray-50 border border-gray-200 rounded-xl">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-gray-600 mb-1">Pola</label>
                    <select id="wmPattern" onchange="renderWatermarkPreview()" class="w-full px-2 py-2 text-xs bg-gray-50 border border-gray-200 rounded-xl font-semibold">
                        <option value="single">1 di Tengah</option>
                        <option value="tiled">Pola Penuh</option>
                    </select>
                </div>
            </div>

            <button type="button" id="wmExportBtn" onclick="exportWatermarkedFile()" disabled
                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-[#FF4D00] to-amber-500 disabled:opacity-40 text-white text-xs font-bold shadow-xs cursor-pointer">
                <i data-lucide="download" class="w-4 h-4"></i>
                <span>Terapkan &amp; Download Hasil Watermark</span>
            </button>
        </div>

        <div class="lg:col-span-7 bg-white rounded-2xl border border-gray-200/80 p-5 space-y-3 shadow-2xs">
            <span class="block text-xs font-extrabold uppercase tracking-wider text-gray-500">Pratinjau Halaman 1</span>
            <div class="bg-slate-100 rounded-2xl border border-gray-200/80 p-4 flex items-center justify-center min-h-[380px]">
                <canvas id="wmPreviewCanvas" class="hidden max-w-full max-h-[460px] rounded-lg shadow-md bg-white"></canvas>
                <p id="wmEmptyHint" class="text-xs text-gray-400 text-center">Unggah PDF atau Gambar untuk melihat pratinjau watermark secara langsung.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/pdf-lib@1.17.1/dist/pdf-lib.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script>
    if (typeof pdfjsLib !== 'undefined') {
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    }

    function setPmProgress(show, text = '') {
        const el = document.getElementById('pmProgress');
        el.classList.toggle('hidden', !show);
        if (show) document.getElementById('pmProgressText').textContent = text;
    }

    function fmtSize(b) {
        return b < 1048576 ? (b / 1024).toFixed(1) + ' KB' : (b / 1048576).toFixed(2) + ' MB';
    }

    function switchPmTab(tab) {
        ['merge', 'split', 'watermark'].forEach(t => {
            const btn = document.getElementById(`pmTabBtn-${t}`);
            const pnl = document.getElementById(`pmPanel-${t}`);
            const act = t === tab;
            pnl.classList.toggle('hidden', !act);
            btn.className = act
                ? 'px-3 py-1.5 rounded-lg text-xs font-bold bg-white text-gray-900 shadow-2xs cursor-pointer transition-all'
                : 'px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-500 hover:text-gray-800 cursor-pointer transition-all';
        });
    }

    // ── 1. MERGE PDF ──
    let mergeQueue = [];

    async function addMergePdfs(files) {
        if (!files?.length) return;
        setPmProgress(true, 'Membaca file PDF...');
        try {
            for (const f of Array.from(files)) {
                const bytes = await f.arrayBuffer();
                const doc = await PDFLib.PDFDocument.load(bytes);
                mergeQueue.push({ name: f.name, size: f.size, pageCount: doc.getPageCount(), bytes });
            }
            renderMergeList();
        } catch (err) {
            alert('Salah satu file PDF gagal dimuat atau diproteksi password.');
        } finally {
            document.getElementById('mergePdfInput').value = '';
            setPmProgress(false);
        }
    }

    function renderMergeList() {
        const box = document.getElementById('mergeFileList');
        const totalPages = mergeQueue.reduce((s, f) => s + f.pageCount, 0);
        document.getElementById('mergeCountLabel').textContent = `Daftar PDF (${mergeQueue.length} file \u2022 ${totalPages} hal.)`;
        document.getElementById('mergeExportBtn').disabled = mergeQueue.length < 2;

        if (!mergeQueue.length) {
            box.innerHTML = '<div class="py-14 text-center text-xs text-gray-400">Tambahkan minimal 2 file PDF di sebelah kiri untuk digabungkan berurutan.</div>';
            return;
        }

        box.innerHTML = mergeQueue.map((item, idx) => `
            <div class="flex items-center justify-between gap-3 p-3 rounded-xl bg-gray-50 border border-gray-200/80">
                <div class="flex items-center gap-3 min-w-0">
                    <span class="w-7 h-7 rounded-lg bg-rose-100 text-rose-700 text-xs font-extrabold flex items-center justify-center flex-shrink-0">${idx + 1}</span>
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-gray-900 truncate">${item.name}</p>
                        <p class="text-[11px] text-gray-500">${item.pageCount} halaman &bull; ${fmtSize(item.size)}</p>
                    </div>
                </div>
                <div class="flex items-center gap-1 flex-shrink-0">
                    <button type="button" onclick="moveMerge(${idx}, -1)" ${idx === 0 ? 'disabled' : ''} class="px-2 py-1 rounded-lg bg-white border border-gray-200 text-xs font-bold disabled:opacity-30 cursor-pointer">&uarr;</button>
                    <button type="button" onclick="moveMerge(${idx}, 1)" ${idx === mergeQueue.length - 1 ? 'disabled' : ''} class="px-2 py-1 rounded-lg bg-white border border-gray-200 text-xs font-bold disabled:opacity-30 cursor-pointer">&darr;</button>
                    <button type="button" onclick="mergeQueue.splice(${idx}, 1); renderMergeList();" class="px-2 py-1 rounded-lg bg-rose-50 text-rose-600 text-xs font-bold cursor-pointer">&times;</button>
                </div>
            </div>
        `).join('');
    }

    function moveMerge(idx, d) {
        const t = idx + d;
        if (t < 0 || t >= mergeQueue.length) return;
        [mergeQueue[idx], mergeQueue[t]] = [mergeQueue[t], mergeQueue[idx]];
        renderMergeList();
    }

    async function executeMergePdf() {
        if (mergeQueue.length < 2) return;
        setPmProgress(true, 'Menggabungkan semua file PDF...');
        try {
            const outDoc = await PDFLib.PDFDocument.create();
            for (const item of mergeQueue) {
                const src = await PDFLib.PDFDocument.load(item.bytes);
                const copied = await outDoc.copyPages(src, src.getPageIndices());
                copied.forEach(p => outDoc.addPage(p));
            }
            const outBytes = await outDoc.save();
            const url = URL.createObjectURL(new Blob([outBytes], { type: 'application/pdf' }));
            const a = document.createElement('a');
            a.download = `Gabungan_PDF_${Date.now()}.pdf`;
            a.href = url;
            a.click();
            setTimeout(() => URL.revokeObjectURL(url), 2000);
        } finally {
            setPmProgress(false);
        }
    }

    // ── 2. SPLIT / ROTATE / DELETE ──
    let splitRawBytes = null;
    let splitBaseName = 'dokumen';
    let splitPages = [];

    async function loadPdfForSplit(file) {
        if (!file) return;
        document.getElementById('splitPdfInput').value = '';
        splitBaseName = (file.name.replace(/\.pdf$/i, '') || 'dokumen').replace(/[^A-Za-z0-9\-_]+/g, '_');
        document.getElementById('splitFileLabel').textContent = `${file.name} (${fmtSize(file.size)})`;

        setPmProgress(true, 'Merender thumbnail halaman PDF...');
        try {
            splitRawBytes = await file.arrayBuffer();
            const pdfJsDoc = await pdfjsLib.getDocument({ data: splitRawBytes.slice(0) }).promise;
            splitPages = [];
            for (let i = 1; i <= pdfJsDoc.numPages; i++) {
                const pg = await pdfJsDoc.getPage(i);
                const vp = pg.getViewport({ scale: 0.42 });
                const c = document.createElement('canvas');
                c.width = vp.width;
                c.height = vp.height;
                await pg.render({ canvasContext: c.getContext('2d'), viewport: vp }).promise;
                splitPages.push({ pageNum: i, rotation: 0, included: true, thumbUrl: c.toDataURL('image/jpeg', 0.8) });
            }
            document.getElementById('splitInfoLabel').textContent = `${splitPages.length} Halaman`;
            document.getElementById('splitSavePdfBtn').disabled = false;
            document.getElementById('splitSaveZipBtn').disabled = false;
            renderSplitGrid();
        } catch (err) {
            alert('Gagal membuka file PDF.');
        } finally {
            setPmProgress(false);
        }
    }

    function renderSplitGrid() {
        const grid = document.getElementById('splitPagesGrid');
        grid.innerHTML = splitPages.map((p, idx) => `
            <div class="rounded-xl border ${p.included ? 'border-purple-400 bg-purple-50/40' : 'border-gray-200 bg-gray-100 opacity-55'} p-2 space-y-2">
                <div class="relative aspect-[3/4] bg-white rounded-lg overflow-hidden border border-gray-100 flex items-center justify-center">
                    <img src="${p.thumbUrl}" alt="Hal ${p.pageNum}" style="transform: rotate(${p.rotation}deg);" class="max-w-full max-h-full object-contain transition-transform">
                    <label class="absolute top-1.5 left-1.5 inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-slate-900/80 text-white text-[10px] font-bold cursor-pointer">
                        <input type="checkbox" ${p.included ? 'checked' : ''} onchange="splitPages[${idx}].included = this.checked; renderSplitGrid();" class="rounded">
                        <span>Hal ${p.pageNum}</span>
                    </label>
                </div>
                <button type="button" onclick="splitPages[${idx}].rotation = (splitPages[${idx}].rotation + 90) % 360; renderSplitGrid();"
                    class="w-full py-1.5 rounded-lg bg-white hover:bg-purple-600 hover:text-white border border-gray-200 text-[11px] font-bold text-gray-700 cursor-pointer transition-colors">
                    Putar 90&deg; (${p.rotation}&deg;)
                </button>
            </div>
        `).join('');
    }

    function rotateAllSplitPages() {
        splitPages.forEach(p => { p.rotation = (p.rotation + 90) % 360; });
        renderSplitGrid();
    }

    function applySplitRangeSelection() {
        const raw = (document.getElementById('splitRangeInput').value || '').trim();
        if (!raw || !splitPages.length) return;
        const selectedSet = new Set();
        raw.split(',').forEach(part => {
            const p = part.trim();
            if (p.includes('-')) {
                const [a, b] = p.split('-').map(n => parseInt(n, 10));
                if (!isNaN(a) && !isNaN(b)) {
                    for (let k = Math.min(a, b); k <= Math.max(a, b); k++) selectedSet.add(k);
                }
            } else {
                const n = parseInt(p, 10);
                if (!isNaN(n)) selectedSet.add(n);
            }
        });
        splitPages.forEach(p => { p.included = selectedSet.has(p.pageNum); });
        renderSplitGrid();
    }

    async function exportSplitMergedPdf() {
        const chosen = splitPages.filter(p => p.included);
        if (!chosen.length || !splitRawBytes) return;
        setPmProgress(true, 'Menyusun PDF hasil pilihan...');
        try {
            const src = await PDFLib.PDFDocument.load(splitRawBytes);
            const out = await PDFLib.PDFDocument.create();
            for (const item of chosen) {
                const [copied] = await out.copyPages(src, [item.pageNum - 1]);
                if (item.rotation) {
                    const curRot = copied.getRotation().angle || 0;
                    copied.setRotation(PDFLib.degrees((curRot + item.rotation) % 360));
                }
                out.addPage(copied);
            }
            const bytes = await out.save();
            const u = URL.createObjectURL(new Blob([bytes], { type: 'application/pdf' }));
            const a = document.createElement('a');
            a.download = `${splitBaseName}_Terpilih.pdf`;
            a.href = u;
            a.click();
            setTimeout(() => URL.revokeObjectURL(u), 2000);
        } finally {
            setPmProgress(false);
        }
    }

    async function exportSplitZip() {
        const chosen = splitPages.filter(p => p.included);
        if (!chosen.length || !splitRawBytes) return;
        setPmProgress(true, 'Memisahkan halaman ke ZIP...');
        try {
            const src = await PDFLib.PDFDocument.load(splitRawBytes);
            const zip = new JSZip();
            for (const item of chosen) {
                const single = await PDFLib.PDFDocument.create();
                const [copied] = await single.copyPages(src, [item.pageNum - 1]);
                if (item.rotation) {
                    const curRot = copied.getRotation().angle || 0;
                    copied.setRotation(PDFLib.degrees((curRot + item.rotation) % 360));
                }
                single.addPage(copied);
                const bytes = await single.save();
                zip.file(`${splitBaseName}_Hal${String(item.pageNum).padStart(2, '0')}.pdf`, bytes);
            }
            const blob = await zip.generateAsync({ type: 'blob' });
            const u = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.download = `${splitBaseName}_Terpisah.zip`;
            a.href = u;
            a.click();
            setTimeout(() => URL.revokeObjectURL(u), 2000);
        } finally {
            setPmProgress(false);
        }
    }

    // ── 3. WATERMARK PDF & GAMBAR ──
    let wmTarget = null; // { type: 'pdf'|'image', name, rawBytes, previewImg, width, height }

    async function loadWatermarkTarget(file) {
        if (!file) return;
        document.getElementById('wmInput').value = '';
        document.getElementById('wmFileName').textContent = file.name;
        setPmProgress(true, 'Memuat pratinjau dokumen...');

        try {
            if (file.type === 'application/pdf' || /\.pdf$/i.test(file.name)) {
                const rawBytes = await file.arrayBuffer();
                const doc = await pdfjsLib.getDocument({ data: rawBytes.slice(0) }).promise;
                const pg = await doc.getPage(1);
                const vp = pg.getViewport({ scale: 1.4 });
                const off = document.createElement('canvas');
                off.width = vp.width;
                off.height = vp.height;
                const ctx = off.getContext('2d');
                ctx.fillStyle = '#FFFFFF';
                ctx.fillRect(0, 0, off.width, off.height);
                await pg.render({ canvasContext: ctx, viewport: vp }).promise;
                const previewImg = new Image();
                previewImg.src = off.toDataURL('image/png');
                await new Promise(r => previewImg.onload = r);
                wmTarget = { type: 'pdf', name: file.name, rawBytes, previewImg, width: off.width, height: off.height };
            } else {
                const dataUrl = await new Promise((res, rej) => {
                    const fr = new FileReader();
                    fr.onload = e => res(e.target.result);
                    fr.onerror = rej;
                    fr.readAsDataURL(file);
                });
                const previewImg = new Image();
                previewImg.src = dataUrl;
                await new Promise(r => previewImg.onload = r);
                wmTarget = { type: 'image', name: file.name, previewImg, width: previewImg.naturalWidth, height: previewImg.naturalHeight };
            }

            document.getElementById('wmExportBtn').disabled = false;
            document.getElementById('wmEmptyHint').classList.add('hidden');
            document.getElementById('wmPreviewCanvas').classList.remove('hidden');
            renderWatermarkPreview();
        } catch {
            alert('Gagal memuat file untuk diberi watermark.');
        } finally {
            setPmProgress(false);
        }
    }

    function drawWatermarkOnCanvas(c, baseImg, w, h) {
        c.width = w;
        c.height = h;
        const ctx = c.getContext('2d');
        ctx.drawImage(baseImg, 0, 0, w, h);

        const text = (document.getElementById('wmText').value || 'ARSIP FEB UNJ').trim();
        const opacity = parseInt(document.getElementById('wmOpacity').value || '24', 10) / 100;
        const fontSize = Math.round((parseInt(document.getElementById('wmSize').value || '46', 10) / 800) * w);
        const color = document.getElementById('wmColor').value || '#DC2626';
        const angleRad = (parseFloat(document.getElementById('wmAngle').value || '-35') * Math.PI) / 180;
        const pattern = document.getElementById('wmPattern').value;

        ctx.save();
        ctx.globalAlpha = opacity;
        ctx.fillStyle = color;
        ctx.font = `800 ${Math.max(14, fontSize)}px Inter, Helvetica, sans-serif`;
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';

        if (pattern === 'single') {
            ctx.translate(w / 2, h / 2);
            ctx.rotate(angleRad);
            ctx.fillText(text, 0, 0);
        } else {
            const stepX = Math.max(w * 0.42, fontSize * text.length * 0.65);
            const stepY = Math.max(h * 0.22, fontSize * 3.2);
            for (let y = -h * 0.2; y < h * 1.2; y += stepY) {
                for (let x = -w * 0.2; x < w * 1.2; x += stepX) {
                    ctx.save();
                    ctx.translate(x, y);
                    ctx.rotate(angleRad);
                    ctx.fillText(text, 0, 0);
                    ctx.restore();
                }
            }
        }
        ctx.restore();
    }

    function renderWatermarkPreview() {
        if (!wmTarget) return;
        drawWatermarkOnCanvas(document.getElementById('wmPreviewCanvas'), wmTarget.previewImg, wmTarget.width, wmTarget.height);
    }

    function hexToRgbUnit(hex) {
        const clean = hex.replace('#', '');
        const r = parseInt(clean.slice(0, 2), 16) / 255;
        const g = parseInt(clean.slice(2, 4), 16) / 255;
        const b = parseInt(clean.slice(4, 6), 16) / 255;
        return PDFLib.rgb(isNaN(r) ? 0.8 : r, isNaN(g) ? 0.1 : g, isNaN(b) ? 0.1 : b);
    }

    async function exportWatermarkedFile() {
        if (!wmTarget) return;
        setPmProgress(true, 'Menempelkan watermark ke seluruh halaman...');
        try {
            if (wmTarget.type === 'image') {
                const off = document.createElement('canvas');
                drawWatermarkOnCanvas(off, wmTarget.previewImg, wmTarget.width, wmTarget.height);
                const a = document.createElement('a');
                a.download = wmTarget.name.replace(/\.[^/.]+$/, '') + '_watermark.png';
                a.href = off.toDataURL('image/png');
                a.click();
                return;
            }

            const pdfDoc = await PDFLib.PDFDocument.load(wmTarget.rawBytes);
            const font = await pdfDoc.embedFont(PDFLib.StandardFonts.HelveticaBold);
            const text = (document.getElementById('wmText').value || 'ARSIP FEB UNJ').trim();
            const opacity = parseInt(document.getElementById('wmOpacity').value || '24', 10) / 100;
            const rawSize = parseInt(document.getElementById('wmSize').value || '46', 10);
            const color = hexToRgbUnit(document.getElementById('wmColor').value || '#DC2626');
            const angleDeg = -parseFloat(document.getElementById('wmAngle').value || '-35');
            const pattern = document.getElementById('wmPattern').value;

            for (const page of pdfDoc.getPages()) {
                const { width, height } = page.getSize();
                const scaledSize = Math.max(12, Math.round((rawSize / 800) * width));
                const textWidth = font.widthOfTextAtSize(text, scaledSize);

                if (pattern === 'single') {
                    page.drawText(text, {
                        x: (width - textWidth * Math.cos((angleDeg * Math.PI) / 180)) / 2,
                        y: (height - textWidth * Math.sin((angleDeg * Math.PI) / 180)) / 2,
                        size: scaledSize,
                        font,
                        color,
                        opacity,
                        rotate: PDFLib.degrees(angleDeg),
                    });
                } else {
                    const stepX = Math.max(width * 0.45, textWidth + 50);
                    const stepY = Math.max(height * 0.24, scaledSize * 3.5);
                    for (let y = 40; y < height; y += stepY) {
                        for (let x = 20; x < width; x += stepX) {
                            page.drawText(text, {
                                x,
                                y,
                                size: scaledSize,
                                font,
                                color,
                                opacity,
                                rotate: PDFLib.degrees(angleDeg),
                            });
                        }
                    }
                }
            }

            const outBytes = await pdfDoc.save();
            const u = URL.createObjectURL(new Blob([outBytes], { type: 'application/pdf' }));
            const a = document.createElement('a');
            a.download = wmTarget.name.replace(/\.pdf$/i, '') + '_watermark.pdf';
            a.href = u;
            a.click();
            setTimeout(() => URL.revokeObjectURL(u), 2000);
        } finally {
            setPmProgress(false);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        switchPmTab(@json($initialTab));
    });
</script>
@endpush
