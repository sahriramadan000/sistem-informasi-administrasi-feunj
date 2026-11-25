@extends('layouts.app')

@section('title', 'PDF <-> Gambar & Kompresi')

@section('content')
<div class="space-y-5">
    {{-- Header & Tabs --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 bg-white p-4 rounded-2xl border border-gray-200/80 shadow-2xs">
        <div class="flex items-center gap-3">
            <a href="{{ route('tools.index') }}" class="w-9 h-9 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 flex items-center justify-center transition-colors" title="Kembali ke Semua Tools">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
            </a>
            <div>
                <h2 class="text-base font-extrabold text-gray-900">Konverter PDF &harr; Gambar &amp; Kompresi</h2>
                <p class="text-xs text-gray-500">Proses langsung di browser Anda — cepat, aman, dan tanpa mengunggah dokumen rahasia ke server luar.</p>
            </div>
        </div>

        <div class="flex flex-wrap p-1 rounded-xl bg-gray-100 border border-gray-200/80 self-start md:self-auto gap-1">
            <button type="button" id="piTabBtn-pdf-to-img" onclick="switchPiTab('pdf-to-img')" class="px-3 py-1.5 rounded-lg text-xs font-bold cursor-pointer transition-all">
                PDF &rarr; JPG / PNG
            </button>
            <button type="button" id="piTabBtn-img-to-pdf" onclick="switchPiTab('img-to-pdf')" class="px-3 py-1.5 rounded-lg text-xs font-bold cursor-pointer transition-all">
                Gambar &rarr; PDF
            </button>
            <button type="button" id="piTabBtn-compress" onclick="switchPiTab('compress')" class="px-3 py-1.5 rounded-lg text-xs font-bold cursor-pointer transition-all">
                Kompres File
            </button>
        </div>
    </div>

    {{-- Progress Overlay --}}
    <div id="piProgressBar" class="hidden bg-orange-50 border border-orange-200 rounded-2xl p-4 flex items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <span class="w-5 h-5 border-2 border-[#FF4D00] border-t-transparent rounded-full animate-spin flex-shrink-0"></span>
            <div>
                <p id="piProgressTitle" class="text-xs font-extrabold text-gray-900">Memproses...</p>
                <p id="piProgressDesc" class="text-[11px] text-gray-600"></p>
            </div>
        </div>
        <span id="piProgressPct" class="text-xs font-mono font-bold text-[#FF4D00]">0%</span>
    </div>

    {{-- ======================= TAB 1: PDF -> JPG / PNG ======================= --}}
    <div id="piPanel-pdf-to-img" class="space-y-4">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-start">
            <div class="lg:col-span-4 bg-white rounded-2xl border border-gray-200/80 p-5 space-y-4 shadow-2xs">
                <label class="flex flex-col items-center justify-center gap-2 p-5 border-2 border-dashed border-emerald-300 hover:border-emerald-500 rounded-2xl bg-emerald-50/40 hover:bg-emerald-50 transition-all cursor-pointer text-center">
                    <div class="w-10 h-10 rounded-xl bg-white border border-emerald-200 text-emerald-600 flex items-center justify-center shadow-2xs">
                        <i data-lucide="file-up" class="w-5 h-5"></i>
                    </div>
                    <span class="text-xs font-extrabold text-gray-900">Pilih File PDF</span>
                    <span id="p2iFileName" class="text-[11px] text-gray-500 truncate max-w-full">Belum ada file dipilih</span>
                    <input type="file" id="p2iInput" accept=".pdf" class="hidden" onchange="loadPdfForImages(this.files[0])">
                </label>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold text-gray-600 mb-1">Format Output</label>
                        <select id="p2iFormat" onchange="toggleP2iQuality()" class="w-full px-3 py-2 text-xs bg-gray-50 border border-gray-200 rounded-xl font-semibold">
                            <option value="jpeg">JPG (Ukuran Ringan)</option>
                            <option value="png">PNG (Kualitas Penuh)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-gray-600 mb-1">Resolusi (DPI)</label>
                        <select id="p2iDpi" class="w-full px-3 py-2 text-xs bg-gray-50 border border-gray-200 rounded-xl font-semibold">
                            <option value="2">150 DPI (Standar)</option>
                            <option value="2.75" selected>200 DPI (Jelas)</option>
                            <option value="4">300 DPI (Cetak HD)</option>
                        </select>
                    </div>
                </div>

                <div id="p2iQualityWrap">
                    <div class="flex justify-between text-xs font-semibold text-gray-700 mb-1">
                        <span>Kualitas JPG</span>
                        <span id="p2iQualLabel" class="font-mono text-[#FF4D00]">92%</span>
                    </div>
                    <input type="range" id="p2iQuality" min="60" max="98" value="92" oninput="document.getElementById('p2iQualLabel').textContent = this.value + '%'"
                        class="w-full accent-[#FF4D00]">
                </div>

                <button type="button" id="p2iDownloadZipBtn" onclick="exportSelectedPdfPagesAsZip()" disabled
                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-[#FF4D00] to-amber-500 disabled:opacity-40 text-white text-xs font-bold shadow-xs cursor-pointer">
                    <i data-lucide="folder-archive" class="w-4 h-4"></i>
                    <span>Download Halaman Terpilih (ZIP)</span>
                </button>
            </div>

            <div class="lg:col-span-8 bg-white rounded-2xl border border-gray-200/80 p-5 space-y-3 shadow-2xs min-h-[340px]">
                <div class="flex flex-wrap items-center justify-between gap-2 pb-2 border-b border-gray-100">
                    <span id="p2iPageCountInfo" class="text-xs font-bold text-gray-700">Pratinjau Halaman PDF</span>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="toggleAllP2i(true)" class="px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-gray-200 text-[11px] font-bold text-gray-700 cursor-pointer">Pilih Semua</button>
                        <button type="button" onclick="toggleAllP2i(false)" class="px-2.5 py-1 rounded-lg bg-gray-100 hover:bg-gray-200 text-[11px] font-bold text-gray-700 cursor-pointer">Batal Pilih</button>
                    </div>
                </div>

                <div id="p2iGrid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                    <div class="col-span-full py-16 text-center text-xs text-gray-400">
                        Unggah file PDF di sebelah kiri untuk melihat semua halaman dan mengekspornya ke JPG/PNG.
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ======================= TAB 2: GAMBAR -> PDF ======================= --}}
    <div id="piPanel-img-to-pdf" class="hidden space-y-4">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-start">
            <div class="lg:col-span-4 bg-white rounded-2xl border border-gray-200/80 p-5 space-y-4 shadow-2xs">
                <label class="flex flex-col items-center justify-center gap-2 p-5 border-2 border-dashed border-sky-300 hover:border-sky-500 rounded-2xl bg-sky-50/40 hover:bg-sky-50 transition-all cursor-pointer text-center">
                    <div class="w-10 h-10 rounded-xl bg-white border border-sky-200 text-sky-600 flex items-center justify-center shadow-2xs">
                        <i data-lucide="images" class="w-5 h-5"></i>
                    </div>
                    <span class="text-xs font-extrabold text-gray-900">Tambah Gambar (Bisa Banyak)</span>
                    <span class="text-[11px] text-gray-500">Mendukung JPG, PNG, WebP</span>
                    <input type="file" id="i2pInput" accept="image/*" multiple class="hidden" onchange="addImagesToPdfQueue(this.files)">
                </label>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold text-gray-600 mb-1">Ukuran Kertas</label>
                        <select id="i2pPaper" class="w-full px-3 py-2 text-xs bg-gray-50 border border-gray-200 rounded-xl font-semibold">
                            <option value="a4">A4 (210 &times; 297 mm)</option>
                            <option value="f4">F4 / Folio (210 &times; 330 mm)</option>
                            <option value="fit">Sesuai Ukuran Gambar</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-gray-600 mb-1">Orientasi</label>
                        <select id="i2pOrient" class="w-full px-3 py-2 text-xs bg-gray-50 border border-gray-200 rounded-xl font-semibold">
                            <option value="auto">Otomatis per Foto</option>
                            <option value="p">Portrait (Tegak)</option>
                            <option value="l">Landscape (Mendatar)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-gray-600 mb-1">Margin Tepi (mm)</label>
                    <select id="i2pMargin" class="w-full px-3 py-2 text-xs bg-gray-50 border border-gray-200 rounded-xl font-semibold">
                        <option value="0">0 mm (Tanpa Margin / Penuh)</option>
                        <option value="10" selected>10 mm (Standar Rapi)</option>
                        <option value="20">20 mm (Margin Lebar)</option>
                    </select>
                </div>

                <button type="button" id="i2pExportBtn" onclick="exportImagesQueueToPdf()" disabled
                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-[#FF4D00] to-amber-500 disabled:opacity-40 text-white text-xs font-bold shadow-xs cursor-pointer">
                    <i data-lucide="file-down" class="w-4 h-4"></i>
                    <span>Gabungkan &amp; Download PDF</span>
                </button>
            </div>

            <div class="lg:col-span-8 bg-white rounded-2xl border border-gray-200/80 p-5 space-y-3 shadow-2xs min-h-[340px]">
                <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                    <span id="i2pCountLabel" class="text-xs font-bold text-gray-700">Antrean Gambar (0)</span>
                    <button type="button" onclick="clearImagesQueue()" class="text-[11px] font-bold text-rose-600 hover:underline cursor-pointer">Kosongkan</button>
                </div>

                <div id="i2pGrid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                    <div class="col-span-full py-16 text-center text-xs text-gray-400">
                        Pilih beberapa foto atau hasil scan di sebelah kiri, lalu atur urutannya.
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ======================= TAB 3: KOMPRES FILE ======================= --}}
    <div id="piPanel-compress" class="hidden grid grid-cols-1 lg:grid-cols-2 gap-5 items-start">
        {{-- Kompres Gambar --}}
        <div class="bg-white rounded-2xl border border-gray-200/80 p-5 space-y-4 shadow-2xs">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-extrabold text-gray-900">Kompres Gambar (JPG / PNG / WebP)</h3>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">Batch</span>
            </div>

            <label class="flex flex-col items-center justify-center gap-1.5 p-4 border-2 border-dashed border-amber-300 hover:border-amber-500 rounded-2xl bg-amber-50/40 cursor-pointer text-center">
                <i data-lucide="image-down" class="w-5 h-5 text-amber-600"></i>
                <span class="text-xs font-bold text-gray-800">Pilih 1 atau Banyak Gambar</span>
                <input type="file" id="cmpImgInput" accept="image/*" multiple class="hidden" onchange="runImageCompression(this.files)">
            </label>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <div class="flex justify-between text-xs font-semibold text-gray-700 mb-1">
                        <span>Kualitas</span>
                        <span id="cmpImgQualLbl" class="font-mono text-[#FF4D00]">75%</span>
                    </div>
                    <input type="range" id="cmpImgQual" min="25" max="95" value="75" oninput="document.getElementById('cmpImgQualLbl').textContent = this.value + '%'" class="w-full accent-[#FF4D00]">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-gray-600 mb-1">Lebar Maksimal</label>
                    <select id="cmpImgMaxW" class="w-full px-2.5 py-2 text-xs bg-gray-50 border border-gray-200 rounded-xl font-semibold">
                        <option value="0">Tetap (Asli)</option>
                        <option value="1920" selected>Maks. 1920 px (Full HD)</option>
                        <option value="1280">Maks. 1280 px (Web/Portal)</option>
                        <option value="800">Maks. 800 px (Ringan)</option>
                    </select>
                </div>
            </div>

            <div id="cmpImgResults" class="space-y-2 max-h-64 overflow-y-auto"></div>

            <button type="button" id="cmpImgZipBtn" onclick="downloadCompressedImagesZip()" disabled
                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-gray-900 disabled:opacity-40 text-white text-xs font-bold cursor-pointer">
                <i data-lucide="download" class="w-4 h-4"></i>
                <span>Download Semua Hasil Kompres (ZIP)</span>
            </button>
        </div>

        {{-- Kompres PDF --}}
        <div class="bg-white rounded-2xl border border-gray-200/80 p-5 space-y-4 shadow-2xs">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-extrabold text-gray-900">Kompres Dokumen PDF</h3>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">Scan / Surat</span>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-bold text-gray-600 mb-1">Tingkat Kompresi</label>
                    <select id="cmpPdfPreset" class="w-full px-3 py-2 text-xs bg-gray-50 border border-gray-200 rounded-xl font-semibold">
                        <option value="low">Ringan (Tajam, hemat ~30%)</option>
                        <option value="medium" selected>Seimbang (Rekomendasi, hemat ~60%)</option>
                        <option value="extreme">Ekstrem (Sangat kecil, hemat ~80%)</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <label class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-r from-[#FF4D00] to-amber-500 text-white text-xs font-bold cursor-pointer shadow-xs">
                        <i data-lucide="file-up" class="w-4 h-4"></i>
                        <span>Pilih PDF &amp; Kompres</span>
                        <input type="file" id="cmpPdfInput" accept=".pdf" class="hidden" onchange="compressSinglePdf(this.files[0])">
                    </label>
                </div>
            </div>

            <div id="cmpPdfResultBox" class="hidden p-4 rounded-2xl bg-emerald-50/70 border border-emerald-200 space-y-3">
                <div class="flex items-center justify-between text-xs">
                    <div>
                        <p id="cmpPdfName" class="font-extrabold text-gray-900 truncate max-w-[240px]"></p>
                        <p id="cmpPdfStats" class="text-[11px] text-emerald-800 font-semibold mt-0.5"></p>
                    </div>
                    <span id="cmpPdfSavedBadge" class="px-2.5 py-1 rounded-full bg-emerald-600 text-white text-[11px] font-extrabold"></span>
                </div>
                <button type="button" id="cmpPdfDownloadBtn"
                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold cursor-pointer">
                    <i data-lucide="download" class="w-4 h-4"></i>
                    <span>Download PDF Hasil Kompres</span>
                </button>
            </div>

            <p class="text-[11px] text-gray-500 leading-relaxed bg-gray-50 p-3 rounded-xl border border-gray-100">
                Cocok untuk memperkecil PDF hasil scan yang ukurannya puluhan MB agar muat di portal unggah kampus atau WhatsApp.
            </p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script>
    if (typeof pdfjsLib !== 'undefined') {
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    }

    function setPiProgress(show, title = '', desc = '', pct = 0) {
        const bar = document.getElementById('piProgressBar');
        bar.classList.toggle('hidden', !show);
        if (show) {
            document.getElementById('piProgressTitle').textContent = title;
            document.getElementById('piProgressDesc').textContent = desc;
            document.getElementById('piProgressPct').textContent = `${pct}%`;
        }
    }

    function fmtBytes(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / 1048576).toFixed(2) + ' MB';
    }

    function switchPiTab(tab) {
        ['pdf-to-img', 'img-to-pdf', 'compress'].forEach(t => {
            const btn = document.getElementById(`piTabBtn-${t}`);
            const panel = document.getElementById(`piPanel-${t}`);
            const act = t === tab;
            panel.classList.toggle('hidden', !act);
            btn.className = act
                ? 'px-3 py-1.5 rounded-lg text-xs font-bold bg-white text-gray-900 shadow-2xs cursor-pointer transition-all'
                : 'px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-500 hover:text-gray-800 cursor-pointer transition-all';
        });
    }

    // ── 1. PDF -> JPG/PNG ──
    let p2iPdfDoc = null;
    let p2iBaseName = 'dokumen';
    let p2iPages = [];

    function toggleP2iQuality() {
        const fmt = document.getElementById('p2iFormat').value;
        document.getElementById('p2iQualityWrap').classList.toggle('hidden', fmt !== 'jpeg');
    }

    async function loadPdfForImages(file) {
        if (!file) return;
        document.getElementById('p2iInput').value = '';
        p2iBaseName = (file.name.replace(/\.pdf$/i, '') || 'dokumen').replace(/[^A-Za-z0-9\-_]+/g, '_');
        document.getElementById('p2iFileName').textContent = `${file.name} (${fmtBytes(file.size)})`;

        setPiProgress(true, 'Memuat PDF...', 'Membaca halaman PDF...', 15);
        try {
            const buf = await file.arrayBuffer();
            p2iPdfDoc = await pdfjsLib.getDocument({ data: buf }).promise;
            p2iPages = [];
            const grid = document.getElementById('p2iGrid');
            grid.innerHTML = '';

            for (let i = 1; i <= p2iPdfDoc.numPages; i++) {
                setPiProgress(true, 'Merender Pratinjau...', `Halaman ${i} dari ${p2iPdfDoc.numPages}`, Math.round((i / p2iPdfDoc.numPages) * 90));
                const page = await p2iPdfDoc.getPage(i);
                const vp = page.getViewport({ scale: 0.45 });
                const c = document.createElement('canvas');
                c.width = vp.width;
                c.height = vp.height;
                await page.render({ canvasContext: c.getContext('2d'), viewport: vp }).promise;
                const thumbUrl = c.toDataURL('image/jpeg', 0.8);
                p2iPages.push({ pageNum: i, selected: true, thumbUrl });
            }

            renderP2iGrid();
            document.getElementById('p2iPageCountInfo').textContent = `${p2iPdfDoc.numPages} Halaman Siap Diekspor`;
            document.getElementById('p2iDownloadZipBtn').disabled = false;
        } catch (err) {
            alert('Gagal membaca file PDF. Pastikan PDF tidak rusak atau terproteksi password.');
        } finally {
            setPiProgress(false);
        }
    }

    function renderP2iGrid() {
        const grid = document.getElementById('p2iGrid');
        grid.innerHTML = p2iPages.map((p, idx) => `
            <div class="rounded-xl border ${p.selected ? 'border-emerald-400 bg-emerald-50/40' : 'border-gray-200 bg-gray-50 opacity-60'} p-2 flex flex-col gap-2 transition-all">
                <div class="relative aspect-[3/4] bg-white rounded-lg overflow-hidden border border-gray-100 flex items-center justify-center">
                    <img src="${p.thumbUrl}" alt="Hal ${p.pageNum}" class="max-w-full max-h-full object-contain">
                    <label class="absolute top-1.5 left-1.5 inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-slate-900/80 text-white text-[10px] font-bold cursor-pointer">
                        <input type="checkbox" ${p.selected ? 'checked' : ''} onchange="p2iPages[${idx}].selected = this.checked; renderP2iGrid();" class="rounded">
                        <span>Hal ${p.pageNum}</span>
                    </label>
                </div>
                <button type="button" onclick="downloadSinglePdfPage(${p.pageNum})"
                    class="w-full py-1.5 rounded-lg bg-white hover:bg-emerald-600 hover:text-white border border-gray-200 text-[11px] font-bold text-gray-700 transition-colors cursor-pointer">
                    Download
                </button>
            </div>
        `).join('');
    }

    function toggleAllP2i(state) {
        p2iPages.forEach(p => p.selected = state);
        renderP2iGrid();
    }

    async function renderPdfPageToBlob(pageNum, scale, format, quality) {
        const page = await p2iPdfDoc.getPage(pageNum);
        const vp = page.getViewport({ scale });
        const c = document.createElement('canvas');
        c.width = vp.width;
        c.height = vp.height;
        const ctx = c.getContext('2d');
        ctx.fillStyle = '#FFFFFF';
        ctx.fillRect(0, 0, c.width, c.height);
        await page.render({ canvasContext: ctx, viewport: vp }).promise;
        const mime = format === 'png' ? 'image/png' : 'image/jpeg';
        return new Promise(res => c.toBlob(res, mime, quality));
    }

    async function downloadSinglePdfPage(pageNum) {
        if (!p2iPdfDoc) return;
        const fmt = document.getElementById('p2iFormat').value;
        const scale = parseFloat(document.getElementById('p2iDpi').value || '2.75');
        const qual = parseInt(document.getElementById('p2iQuality').value || '92', 10) / 100;
        const ext = fmt === 'png' ? 'png' : 'jpg';
        setPiProgress(true, `Mengekspor Halaman ${pageNum}...`, `Format ${ext.toUpperCase()}`, 50);
        try {
            const blob = await renderPdfPageToBlob(pageNum, scale, fmt, qual);
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.download = `${p2iBaseName}_Hal${String(pageNum).padStart(2, '0')}.${ext}`;
            a.href = url;
            a.click();
            setTimeout(() => URL.revokeObjectURL(url), 1000);
        } finally {
            setPiProgress(false);
        }
    }

    async function exportSelectedPdfPagesAsZip() {
        const targets = p2iPages.filter(p => p.selected);
        if (!targets.length || !p2iPdfDoc) {
            alert('Pilih minimal 1 halaman untuk diunduh.');
            return;
        }
        const fmt = document.getElementById('p2iFormat').value;
        const scale = parseFloat(document.getElementById('p2iDpi').value || '2.75');
        const qual = parseInt(document.getElementById('p2iQuality').value || '92', 10) / 100;
        const ext = fmt === 'png' ? 'png' : 'jpg';

        const zip = new JSZip();
        try {
            for (let i = 0; i < targets.length; i++) {
                const p = targets[i];
                setPiProgress(true, 'Menyiapkan ZIP...', `Merender halaman ${p.pageNum} (${i + 1}/${targets.length})`, Math.round(((i + 1) / targets.length) * 90));
                const blob = await renderPdfPageToBlob(p.pageNum, scale, fmt, qual);
                zip.file(`${p2iBaseName}_Hal${String(p.pageNum).padStart(2, '0')}.${ext}`, blob);
            }
            const zipBlob = await zip.generateAsync({ type: 'blob' });
            const url = URL.createObjectURL(zipBlob);
            const a = document.createElement('a');
            a.download = `${p2iBaseName}_${ext.toUpperCase()}.zip`;
            a.href = url;
            a.click();
            setTimeout(() => URL.revokeObjectURL(url), 2000);
        } finally {
            setPiProgress(false);
        }
    }

    // ── 2. Gambar -> PDF ──
    let i2pQueue = [];

    function readFileAsImage(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onload = (e) => {
                const img = new Image();
                img.onload = () => resolve({ dataUrl: e.target.result, width: img.naturalWidth, height: img.naturalHeight });
                img.onerror = reject;
                img.src = e.target.result;
            };
            reader.onerror = reject;
            reader.readAsDataURL(file);
        });
    }

    async function addImagesToPdfQueue(fileList) {
        if (!fileList?.length) return;
        for (const f of Array.from(fileList)) {
            if (!f.type.startsWith('image/')) continue;
            const info = await readFileAsImage(f);
            i2pQueue.push({ name: f.name, size: f.size, ...info });
        }
        document.getElementById('i2pInput').value = '';
        renderI2pGrid();
    }

    function renderI2pGrid() {
        const grid = document.getElementById('i2pGrid');
        document.getElementById('i2pCountLabel').textContent = `Antrean Gambar (${i2pQueue.length})`;
        document.getElementById('i2pExportBtn').disabled = i2pQueue.length === 0;

        if (!i2pQueue.length) {
            grid.innerHTML = '<div class="col-span-full py-16 text-center text-xs text-gray-400">Pilih beberapa foto atau hasil scan di sebelah kiri, lalu atur urutannya.</div>';
            return;
        }

        grid.innerHTML = i2pQueue.map((item, idx) => `
            <div class="rounded-xl border border-gray-200 bg-gray-50 p-2 space-y-2">
                <div class="relative aspect-[3/4] bg-white rounded-lg overflow-hidden border border-gray-100 flex items-center justify-center">
                    <img src="${item.dataUrl}" alt="${item.name}" class="max-w-full max-h-full object-contain">
                    <span class="absolute top-1 left-1 px-1.5 py-0.5 rounded bg-gray-900/80 text-white text-[10px] font-mono font-bold">${idx + 1}</span>
                </div>
                <p class="text-[10.5px] font-bold text-gray-700 truncate">${item.name}</p>
                <div class="flex items-center justify-between gap-1">
                    <button type="button" onclick="moveI2p(${idx}, -1)" ${idx === 0 ? 'disabled' : ''} class="flex-1 py-1 rounded bg-white border border-gray-200 text-xs font-bold disabled:opacity-30 cursor-pointer">&larr;</button>
                    <button type="button" onclick="moveI2p(${idx}, 1)" ${idx === i2pQueue.length - 1 ? 'disabled' : ''} class="flex-1 py-1 rounded bg-white border border-gray-200 text-xs font-bold disabled:opacity-30 cursor-pointer">&rarr;</button>
                    <button type="button" onclick="i2pQueue.splice(${idx}, 1); renderI2pGrid();" class="px-2 py-1 rounded bg-rose-50 text-rose-600 text-xs font-bold cursor-pointer">&times;</button>
                </div>
            </div>
        `).join('');
    }

    function moveI2p(idx, delta) {
        const tgt = idx + delta;
        if (tgt < 0 || tgt >= i2pQueue.length) return;
        [i2pQueue[idx], i2pQueue[tgt]] = [i2pQueue[tgt], i2pQueue[idx]];
        renderI2pGrid();
    }

    function clearImagesQueue() {
        i2pQueue = [];
        renderI2pGrid();
    }

    async function exportImagesQueueToPdf() {
        if (!i2pQueue.length) return;
        const paper = document.getElementById('i2pPaper').value;
        const orientOpt = document.getElementById('i2pOrient').value;
        const margin = parseInt(document.getElementById('i2pMargin').value || '10', 10);
        const { jsPDF } = window.jspdf;

        setPiProgress(true, 'Menyusun PDF...', `Memproses ${i2pQueue.length} gambar...`, 35);
        try {
            let pdf = null;
            for (let i = 0; i < i2pQueue.length; i++) {
                const it = i2pQueue[i];
                const isLand = orientOpt === 'l' || (orientOpt === 'auto' && it.width > it.height);
                const orient = isLand ? 'l' : 'p';

                let pageW, pageH;
                if (paper === 'fit') {
                    pageW = Math.max(40, it.width * 0.264583);
                    pageH = Math.max(40, it.height * 0.264583);
                } else {
                    const baseW = 210;
                    const baseH = paper === 'f4' ? 330 : 297;
                    pageW = isLand ? baseH : baseW;
                    pageH = isLand ? baseW : baseH;
                }

                if (!pdf) {
                    pdf = new jsPDF({ orientation: orient, unit: 'mm', format: [pageW, pageH] });
                } else {
                    pdf.addPage([pageW, pageH], orient);
                }

                const useMargin = paper === 'fit' ? 0 : margin;
                const availW = pageW - useMargin * 2;
                const availH = pageH - useMargin * 2;
                const imgRatio = it.width / it.height;
                let drawW = availW;
                let drawH = drawW / imgRatio;
                if (drawH > availH) {
                    drawH = availH;
                    drawW = drawH * imgRatio;
                }
                const dx = (pageW - drawW) / 2;
                const dy = (pageH - drawH) / 2;

                // Normalize via canvas as JPEG to guarantee jsPDF compatibility (WebP/PNG/orientation safe)
                const c = document.createElement('canvas');
                c.width = it.width;
                c.height = it.height;
                const ctx = c.getContext('2d');
                ctx.fillStyle = '#FFFFFF';
                ctx.fillRect(0, 0, c.width, c.height);
                const im = new Image();
                im.src = it.dataUrl;
                await new Promise(r => im.complete ? r() : (im.onload = r));
                ctx.drawImage(im, 0, 0);
                pdf.addImage(c.toDataURL('image/jpeg', 0.92), 'JPEG', dx, dy, drawW, drawH);
            }
            pdf.save(`Gabungan_Gambar_${Date.now()}.pdf`);
        } finally {
            setPiProgress(false);
        }
    }

    // ── 3. Kompres Gambar & PDF ──
    let compressedImgs = [];

    async function runImageCompression(fileList) {
        if (!fileList?.length) return;
        compressedImgs = [];
        const qual = parseInt(document.getElementById('cmpImgQual').value || '75', 10) / 100;
        const maxW = parseInt(document.getElementById('cmpImgMaxW').value || '0', 10);
        const box = document.getElementById('cmpImgResults');
        box.innerHTML = '';

        setPiProgress(true, 'Mengompres Gambar...', `Memproses ${fileList.length} file...`, 30);
        try {
            for (const f of Array.from(fileList)) {
                const info = await readFileAsImage(f);
                let w = info.width, h = info.height;
                if (maxW > 0 && w > maxW) {
                    h = Math.round(h * (maxW / w));
                    w = maxW;
                }
                const c = document.createElement('canvas');
                c.width = w;
                c.height = h;
                const ctx = c.getContext('2d');
                ctx.fillStyle = '#FFFFFF';
                ctx.fillRect(0, 0, w, h);
                const im = new Image();
                im.src = info.dataUrl;
                await new Promise(r => im.complete ? r() : (im.onload = r));
                ctx.drawImage(im, 0, 0, w, h);

                const blob = await new Promise(res => c.toBlob(res, 'image/jpeg', qual));
                const outName = f.name.replace(/\.[^/.]+$/, '') + '_kompres.jpg';
                const saved = Math.max(0, Math.round(((f.size - blob.size) / f.size) * 100));
                compressedImgs.push({ name: outName, blob });

                const row = document.createElement('div');
                row.className = 'flex items-center justify-between gap-2 p-2.5 rounded-xl bg-gray-50 border border-gray-200 text-xs';
                row.innerHTML = `
                    <div class="min-w-0">
                        <p class="font-bold text-gray-800 truncate">${outName}</p>
                        <p class="text-[11px] text-gray-500">${fmtBytes(f.size)} &rarr; <strong class="text-emerald-700">${fmtBytes(blob.size)}</strong> (-${saved}%)</p>
                    </div>
                    <button type="button" class="px-2.5 py-1 rounded-lg bg-white border border-gray-200 text-[11px] font-bold text-gray-700 hover:bg-gray-100 cursor-pointer flex-shrink-0">Unduh</button>
                `;
                row.querySelector('button').onclick = () => {
                    const u = URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.download = outName;
                    a.href = u;
                    a.click();
                    setTimeout(() => URL.revokeObjectURL(u), 1000);
                };
                box.appendChild(row);
            }
            document.getElementById('cmpImgZipBtn').disabled = compressedImgs.length === 0;
        } finally {
            document.getElementById('cmpImgInput').value = '';
            setPiProgress(false);
        }
    }

    async function downloadCompressedImagesZip() {
        if (!compressedImgs.length) return;
        const zip = new JSZip();
        compressedImgs.forEach(item => zip.file(item.name, item.blob));
        const blob = await zip.generateAsync({ type: 'blob' });
        const u = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.download = `Gambar_Terkompres_${Date.now()}.zip`;
        a.href = u;
        a.click();
        setTimeout(() => URL.revokeObjectURL(u), 2000);
    }

    async function compressSinglePdf(file) {
        if (!file) return;
        document.getElementById('cmpPdfInput').value = '';
        const preset = document.getElementById('cmpPdfPreset').value;
        const cfg = preset === 'extreme'
            ? { scale: 1.15, quality: 0.48 }
            : (preset === 'low' ? { scale: 1.8, quality: 0.80 } : { scale: 1.45, quality: 0.65 });

        try {
            const buf = await file.arrayBuffer();
            const doc = await pdfjsLib.getDocument({ data: buf }).promise;
            const { jsPDF } = window.jspdf;
            let outPdf = null;

            for (let i = 1; i <= doc.numPages; i++) {
                setPiProgress(true, 'Mengompres PDF...', `Halaman ${i} dari ${doc.numPages}`, Math.round((i / doc.numPages) * 95));
                const page = await doc.getPage(i);
                const vp1 = page.getViewport({ scale: 1 });
                const mmW = vp1.width * 0.352778;
                const mmH = vp1.height * 0.352778;
                const orient = mmW > mmH ? 'l' : 'p';

                const vp = page.getViewport({ scale: cfg.scale });
                const c = document.createElement('canvas');
                c.width = vp.width;
                c.height = vp.height;
                const ctx = c.getContext('2d');
                ctx.fillStyle = '#FFFFFF';
                ctx.fillRect(0, 0, c.width, c.height);
                await page.render({ canvasContext: ctx, viewport: vp }).promise;

                const dataUrl = c.toDataURL('image/jpeg', cfg.quality);
                if (!outPdf) outPdf = new jsPDF({ orientation: orient, unit: 'mm', format: [mmW, mmH] });
                else outPdf.addPage([mmW, mmH], orient);
                outPdf.addImage(dataUrl, 'JPEG', 0, 0, mmW, mmH);
            }

            const outBlob = outPdf.output('blob');
            const savedPct = Math.round(((file.size - outBlob.size) / file.size) * 100);
            const outName = file.name.replace(/\.pdf$/i, '') + '_kompres.pdf';

            document.getElementById('cmpPdfName').textContent = outName;
            document.getElementById('cmpPdfStats').textContent = `${fmtBytes(file.size)} \u2192 ${fmtBytes(outBlob.size)}`;
            document.getElementById('cmpPdfSavedBadge').textContent = savedPct > 0 ? `Hemat ${savedPct}%` : 'Selesai';
            document.getElementById('cmpPdfResultBox').classList.remove('hidden');

            document.getElementById('cmpPdfDownloadBtn').onclick = () => {
                const u = URL.createObjectURL(outBlob);
                const a = document.createElement('a');
                a.download = outName;
                a.href = u;
                a.click();
                setTimeout(() => URL.revokeObjectURL(u), 2000);
            };
        } catch (err) {
            alert('Gagal mengompres PDF. Pastikan file PDF valid dan tidak terkunci password.');
        } finally {
            setPiProgress(false);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        switchPiTab(@json($initialTab));
        toggleP2iQuality();
    });
</script>
@endpush
