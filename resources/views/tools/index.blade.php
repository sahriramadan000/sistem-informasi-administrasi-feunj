@extends('layouts.app')

@section('title', 'Tools Administrasi')

@section('content')
<div class="space-y-6">
    {{-- Hero Header --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 via-slate-800 to-orange-950 text-white p-6 sm:p-8 shadow-lg">
        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
            <div class="space-y-2 max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 border border-white/15 text-[11px] font-bold tracking-wide text-orange-200">
                    <i data-lucide="zap" class="w-3.5 h-3.5 text-amber-400"></i>
                    <span>100% Proses Cepat di Browser &bull; Aman Tanpa Upload ke Situs Luar</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Tools Administrasi FEB UNJ</h2>
                <p class="text-sm text-slate-300 leading-relaxed">
                    Pusat utilitas harian staf administrasi: konversi PDF &amp; gambar, penggabung/pemisah PDF, kompresi berkas, watermark dokumen, QR Code &amp; Barcode studio berlogo, hingga helper penulisan administrasi.
                </p>
            </div>
            <div class="flex flex-wrap gap-2.5 flex-shrink-0">
                <a href="{{ route('tools.qr-barcode') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-[#FF4D00] to-amber-500 hover:from-[#e04400] hover:to-[#d97706] text-white text-xs font-bold shadow-md transition-all">
                    <i data-lucide="qr-code" class="w-4 h-4"></i>
                    <span>Buat QR / Barcode</span>
                </a>
                <a href="{{ route('tools.pdf-image') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/15 text-white text-xs font-bold transition-all">
                    <i data-lucide="file-image" class="w-4 h-4"></i>
                    <span>PDF &rarr; JPG / PNG</span>
                </a>
            </div>
        </div>
        <div class="absolute -right-16 -top-16 w-64 h-64 rounded-full bg-orange-500/15 blur-3xl pointer-events-none"></div>
        <div class="absolute -left-16 -bottom-16 w-56 h-56 rounded-full bg-emerald-500/10 blur-3xl pointer-events-none"></div>
    </div>

    {{-- Search & Category Filter --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-3.5 rounded-2xl border border-gray-200/80 shadow-2xs">
        <div class="flex flex-wrap items-center gap-1.5" id="toolsCategoryTabs">
            <button type="button" data-filter="all" class="tool-cat-btn px-3.5 py-1.5 rounded-xl text-xs font-bold bg-gray-900 text-white transition-colors cursor-pointer">
                Semua Tools (9)
            </button>
            <button type="button" data-filter="qr" class="tool-cat-btn px-3.5 py-1.5 rounded-xl text-xs font-semibold text-gray-600 hover:bg-gray-100 transition-colors cursor-pointer">
                QR &amp; Barcode
            </button>
            <button type="button" data-filter="pdf" class="tool-cat-btn px-3.5 py-1.5 rounded-xl text-xs font-semibold text-gray-600 hover:bg-gray-100 transition-colors cursor-pointer">
                Dokumen &amp; PDF
            </button>
            <button type="button" data-filter="helper" class="tool-cat-btn px-3.5 py-1.5 rounded-xl text-xs font-semibold text-gray-600 hover:bg-gray-100 transition-colors cursor-pointer">
                Helper Administrasi
            </button>
        </div>

        <div class="relative w-full sm:w-64">
            <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
            <input type="text" id="toolsHubSearch" placeholder="Cari tool (mis. qr, pdf, kompres)..."
                class="w-full pl-9 pr-3 py-2 text-xs bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#FF4D00] focus:bg-white">
        </div>
    </div>

    {{-- Tools Grid --}}
    <div id="toolsCardsGrid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        {{-- 1. QR Code Custom Studio --}}
        <a href="{{ route('tools.qr-barcode', ['tab' => 'qr']) }}" data-cat="qr" data-kw="qr code link url logo custom template png svg pdf cetak poster"
            class="tool-card group bg-white rounded-2xl border border-gray-200/80 hover:border-orange-300 p-5 shadow-2xs hover:shadow-md transition-all flex flex-col justify-between gap-4">
            <div class="space-y-3">
                <div class="flex items-start justify-between">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-orange-500 to-amber-500 text-white flex items-center justify-center shadow-xs">
                        <i data-lucide="qr-code" class="w-5 h-5"></i>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-orange-50 text-[#FF4D00] border border-orange-200/70">Unggulan</span>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-gray-900 group-hover:text-[#FF4D00] transition-colors">QR Code Studio Custom</h3>
                    <p class="text-xs text-gray-500 mt-1 leading-relaxed">Buat QR link, WA, WiFi, atau vCard lengkap dengan custom logo tengah (preset Logo UNJ) &amp; 6 template desain siap cetak.</p>
                </div>
            </div>
            <div class="flex items-center justify-between pt-3 border-t border-gray-100 text-[11px] font-bold text-gray-700">
                <span>Export PNG / SVG / PDF A4</span>
                <i data-lucide="arrow-right" class="w-4 h-4 text-gray-400 group-hover:text-[#FF4D00] group-hover:translate-x-0.5 transition-all"></i>
            </div>
        </a>

        {{-- 2. Barcode 1D Generator --}}
        <a href="{{ route('tools.qr-barcode', ['tab' => 'barcode']) }}" data-cat="qr" data-kw="barcode code128 code39 ean13 label inventaris arsip"
            class="tool-card group bg-white rounded-2xl border border-gray-200/80 hover:border-indigo-300 p-5 shadow-2xs hover:shadow-md transition-all flex flex-col justify-between gap-4">
            <div class="space-y-3">
                <div class="flex items-start justify-between">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-indigo-600 to-blue-600 text-white flex items-center justify-center shadow-xs">
                        <i data-lucide="barcode" class="w-5 h-5"></i>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200/70">Labeling</span>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-gray-900 group-hover:text-indigo-700 transition-colors">Barcode 1D Generator</h3>
                    <p class="text-xs text-gray-500 mt-1 leading-relaxed">Generate barcode Code128, Code39, atau EAN-13 berlabel teks untuk nomor inventaris aset, map arsip, dan dokumen.</p>
                </div>
            </div>
            <div class="flex items-center justify-between pt-3 border-t border-gray-100 text-[11px] font-bold text-gray-700">
                <span>Export PNG &amp; SVG Vektor</span>
                <i data-lucide="arrow-right" class="w-4 h-4 text-gray-400 group-hover:text-indigo-600 group-hover:translate-x-0.5 transition-all"></i>
            </div>
        </a>

        {{-- 3. PDF ke JPG / PNG --}}
        <a href="{{ route('tools.pdf-image', ['tab' => 'pdf-to-img']) }}" data-cat="pdf" data-kw="convert pdf ke jpg png gambar zip halaman"
            class="tool-card group bg-white rounded-2xl border border-gray-200/80 hover:border-emerald-300 p-5 shadow-2xs hover:shadow-md transition-all flex flex-col justify-between gap-4">
            <div class="space-y-3">
                <div class="flex items-start justify-between">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-emerald-600 to-teal-600 text-white flex items-center justify-center shadow-xs">
                        <i data-lucide="file-image" class="w-5 h-5"></i>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/70">Konverter</span>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-gray-900 group-hover:text-emerald-700 transition-colors">PDF &rarr; JPG / PNG</h3>
                    <p class="text-xs text-gray-500 mt-1 leading-relaxed">Ubah tiap halaman PDF menjadi gambar JPG atau PNG resolusi tinggi (hingga 300 DPI). Pilih halaman &amp; unduh 1 ZIP.</p>
                </div>
            </div>
            <div class="flex items-center justify-between pt-3 border-t border-gray-100 text-[11px] font-bold text-gray-700">
                <span>Multi-halaman + ZIP</span>
                <i data-lucide="arrow-right" class="w-4 h-4 text-gray-400 group-hover:text-emerald-600 group-hover:translate-x-0.5 transition-all"></i>
            </div>
        </a>

        {{-- 4. Gambar ke PDF --}}
        <a href="{{ route('tools.pdf-image', ['tab' => 'img-to-pdf']) }}" data-cat="pdf" data-kw="gambar jpg png ke pdf gabung scan ktp dokumen"
            class="tool-card group bg-white rounded-2xl border border-gray-200/80 hover:border-sky-300 p-5 shadow-2xs hover:shadow-md transition-all flex flex-col justify-between gap-4">
            <div class="space-y-3">
                <div class="flex items-start justify-between">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-sky-600 to-cyan-600 text-white flex items-center justify-center shadow-xs">
                        <i data-lucide="images" class="w-5 h-5"></i>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-sky-50 text-sky-700 border border-sky-200/70">Konverter</span>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-gray-900 group-hover:text-sky-700 transition-colors">Gambar &rarr; PDF</h3>
                    <p class="text-xs text-gray-500 mt-1 leading-relaxed">Satukan beberapa foto atau hasil scan JPG/PNG menjadi 1 dokumen PDF dengan pilihan ukuran A4, F4, atau pas gambar.</p>
                </div>
            </div>
            <div class="flex items-center justify-between pt-3 border-t border-gray-100 text-[11px] font-bold text-gray-700">
                <span>Atur Urutan &amp; Margin</span>
                <i data-lucide="arrow-right" class="w-4 h-4 text-gray-400 group-hover:text-sky-600 group-hover:translate-x-0.5 transition-all"></i>
            </div>
        </a>

        {{-- 5. Kompres Gambar & PDF --}}
        <a href="{{ route('tools.pdf-image', ['tab' => 'compress']) }}" data-cat="pdf" data-kw="kompres perkecil ukuran size pdf jpg png mb kb"
            class="tool-card group bg-white rounded-2xl border border-gray-200/80 hover:border-amber-300 p-5 shadow-2xs hover:shadow-md transition-all flex flex-col justify-between gap-4">
            <div class="space-y-3">
                <div class="flex items-start justify-between">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-amber-500 to-yellow-500 text-white flex items-center justify-center shadow-xs">
                        <i data-lucide="minimize-2" class="w-5 h-5"></i>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200/70">Optimasi</span>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-gray-900 group-hover:text-amber-700 transition-colors">Kompres Gambar &amp; PDF</h3>
                    <p class="text-xs text-gray-500 mt-1 leading-relaxed">Perkecil ukuran berkas PDF atau foto secara batch agar lolos batas unggah portal akademik &amp; formulir.</p>
                </div>
            </div>
            <div class="flex items-center justify-between pt-3 border-t border-gray-100 text-[11px] font-bold text-gray-700">
                <span>Hitung % Penghematan</span>
                <i data-lucide="arrow-right" class="w-4 h-4 text-gray-400 group-hover:text-amber-600 group-hover:translate-x-0.5 transition-all"></i>
            </div>
        </a>

        {{-- 6. Gabung PDF (Merge) --}}
        <a href="{{ route('tools.pdf-manager', ['tab' => 'merge']) }}" data-cat="pdf" data-kw="gabung merge pdf satukan berkas lampiran sk"
            class="tool-card group bg-white rounded-2xl border border-gray-200/80 hover:border-rose-300 p-5 shadow-2xs hover:shadow-md transition-all flex flex-col justify-between gap-4">
            <div class="space-y-3">
                <div class="flex items-start justify-between">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-rose-600 to-pink-600 text-white flex items-center justify-center shadow-xs">
                        <i data-lucide="files" class="w-5 h-5"></i>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200/70">Lossless</span>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-gray-900 group-hover:text-rose-700 transition-colors">Gabung PDF (Merge)</h3>
                    <p class="text-xs text-gray-500 mt-1 leading-relaxed">Satukan beberapa dokumen PDF (misalnya SK + lampiran + daftar hadir) dengan menjaga kualitas asli teks vektor.</p>
                </div>
            </div>
            <div class="flex items-center justify-between pt-3 border-t border-gray-100 text-[11px] font-bold text-gray-700">
                <span>Urutkan File &amp; Gabung</span>
                <i data-lucide="arrow-right" class="w-4 h-4 text-gray-400 group-hover:text-rose-600 group-hover:translate-x-0.5 transition-all"></i>
            </div>
        </a>

        {{-- 7. Pisah, Putar & Hapus Halaman PDF --}}
        <a href="{{ route('tools.pdf-manager', ['tab' => 'split']) }}" data-cat="pdf" data-kw="pisah split ekstrak putar rotate hapus halaman pdf"
            class="tool-card group bg-white rounded-2xl border border-gray-200/80 hover:border-purple-300 p-5 shadow-2xs hover:shadow-md transition-all flex flex-col justify-between gap-4">
            <div class="space-y-3">
                <div class="flex items-start justify-between">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-purple-600 to-violet-600 text-white flex items-center justify-center shadow-xs">
                        <i data-lucide="scissors" class="w-5 h-5"></i>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200/70">Organisir</span>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-gray-900 group-hover:text-purple-700 transition-colors">Pisah, Putar &amp; Hapus Halaman PDF</h3>
                    <p class="text-xs text-gray-500 mt-1 leading-relaxed">Putar halaman scan yang miring, buang halaman kosong, atau ekstrak halaman tertentu jadi PDF baru / ZIP.</p>
                </div>
            </div>
            <div class="flex items-center justify-between pt-3 border-t border-gray-100 text-[11px] font-bold text-gray-700">
                <span>Preview per Halaman</span>
                <i data-lucide="arrow-right" class="w-4 h-4 text-gray-400 group-hover:text-purple-600 group-hover:translate-x-0.5 transition-all"></i>
            </div>
        </a>

        {{-- 8. Watermark PDF & Gambar --}}
        <a href="{{ route('tools.pdf-manager', ['tab' => 'watermark']) }}" data-cat="pdf" data-kw="watermark cap salinan draft rahasia pdf gambar"
            class="tool-card group bg-white rounded-2xl border border-gray-200/80 hover:border-teal-300 p-5 shadow-2xs hover:shadow-md transition-all flex flex-col justify-between gap-4">
            <div class="space-y-3">
                <div class="flex items-start justify-between">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-teal-600 to-emerald-600 text-white flex items-center justify-center shadow-xs">
                        <i data-lucide="stamp" class="w-5 h-5"></i>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-teal-50 text-teal-700 border border-teal-200/70">Proteksi</span>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-gray-900 group-hover:text-teal-700 transition-colors">Watermark PDF &amp; Gambar</h3>
                    <p class="text-xs text-gray-500 mt-1 leading-relaxed">Tambahkan tanda air transparan ("SALINAN", "DRAFT", "RAHASIA", atau teks custom) ke PDF maupun gambar.</p>
                </div>
            </div>
            <div class="flex items-center justify-between pt-3 border-t border-gray-100 text-[11px] font-bold text-gray-700">
                <span>Single / Pola Berulang</span>
                <i data-lucide="arrow-right" class="w-4 h-4 text-gray-400 group-hover:text-teal-600 group-hover:translate-x-0.5 transition-all"></i>
            </div>
        </a>

        {{-- 9. Helper Administrasi Kampus --}}
        <a href="{{ route('tools.admin-helper') }}" data-cat="helper" data-kw="terbilang rupiah nama gelar capslock romawi tanggal pkl whatsapp gdrive link"
            class="tool-card group bg-white rounded-2xl border border-gray-200/80 hover:border-slate-400 p-5 shadow-2xs hover:shadow-md transition-all flex flex-col justify-between gap-4">
            <div class="space-y-3">
                <div class="flex items-start justify-between">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-slate-700 to-slate-900 text-white flex items-center justify-center shadow-xs">
                        <i data-lucide="sparkles" class="w-5 h-5"></i>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">Produktif</span>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-gray-900 group-hover:text-slate-900 transition-colors">Helper Administrasi Kampus</h3>
                    <p class="text-xs text-gray-500 mt-1 leading-relaxed">Terbilang Rupiah, perapih huruf besar Nama &amp; Gelar, konverter Romawi &amp; durasi PKL, serta Link WA/GDrive ke QR.</p>
                </div>
            </div>
            <div class="flex items-center justify-between pt-3 border-t border-gray-100 text-[11px] font-bold text-gray-700">
                <span>4 Utilitas Instan</span>
                <i data-lucide="arrow-right" class="w-4 h-4 text-gray-400 group-hover:text-slate-800 group-hover:translate-x-0.5 transition-all"></i>
            </div>
        </a>
    </div>

    <div id="toolsEmptyState" class="hidden text-center py-12 bg-white rounded-2xl border border-gray-200/80">
        <i data-lucide="search-x" class="w-8 h-8 text-gray-300 mx-auto mb-2"></i>
        <p class="text-sm font-bold text-gray-700">Tool tidak ditemukan</p>
        <p class="text-xs text-gray-500 mt-0.5">Coba kata kunci lain atau pilih tab Semua Tools.</p>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const cards = Array.from(document.querySelectorAll('#toolsCardsGrid .tool-card'));
    const catBtns = Array.from(document.querySelectorAll('#toolsCategoryTabs .tool-cat-btn'));
    const searchInput = document.getElementById('toolsHubSearch');
    const emptyState = document.getElementById('toolsEmptyState');
    let activeCat = 'all';

    function filterTools() {
        const q = (searchInput?.value || '').trim().toLowerCase();
        let shown = 0;
        cards.forEach(card => {
            const catOk = activeCat === 'all' || card.dataset.cat === activeCat;
            const hay = (card.textContent + ' ' + (card.dataset.kw || '')).toLowerCase();
            const qOk = !q || hay.includes(q);
            const show = catOk && qOk;
            card.classList.toggle('hidden', !show);
            if (show) shown++;
        });
        emptyState?.classList.toggle('hidden', shown > 0);
    }

    catBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            activeCat = btn.dataset.filter || 'all';
            catBtns.forEach(b => {
                const isAct = b === btn;
                b.classList.toggle('bg-gray-900', isAct);
                b.classList.toggle('text-white', isAct);
                b.classList.toggle('font-bold', isAct);
                b.classList.toggle('text-gray-600', !isAct);
                b.classList.toggle('hover:bg-gray-100', !isAct);
            });
            filterTools();
        });
    });

    searchInput?.addEventListener('input', filterTools);
});
</script>
@endpush
