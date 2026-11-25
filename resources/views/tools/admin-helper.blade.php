@extends('layouts.app')

@section('title', 'Helper Administrasi Kampus')

@section('content')
<div class="space-y-5">
    {{-- Header --}}
    <div class="flex items-center justify-between gap-3 bg-white p-4 rounded-2xl border border-gray-200/80 shadow-2xs">
        <div class="flex items-center gap-3">
            <a href="{{ route('tools.index') }}" class="w-9 h-9 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 flex items-center justify-center transition-colors" title="Kembali ke Semua Tools">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
            </a>
            <div>
                <h2 class="text-base font-extrabold text-gray-900">Helper Administrasi Kampus</h2>
                <p class="text-xs text-gray-500">Utilitas cepat untuk penulisan surat, RAB/kuitansi, perapihan data SIAKAD, dan pembuatan tautan siap QR.</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 items-start">
        {{-- 1. Terbilang Rupiah --}}
        <div class="bg-white rounded-2xl border border-gray-200/80 p-5 space-y-3.5 shadow-2xs">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center">
                    <i data-lucide="banknote" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-gray-900">Konverter Terbilang Rupiah</h3>
                    <p class="text-[11px] text-gray-500">Untuk pengisian nominal kuitansi, RAB, atau dokumen keuangan.</p>
                </div>
            </div>

            <input type="text" id="terbilangInput" value="15750000" oninput="updateTerbilang()"
                placeholder="Masukkan angka nominal (cth: 15750000)"
                class="w-full px-3.5 py-2.5 text-sm font-mono font-bold bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-emerald-500">

            <div class="p-3.5 rounded-xl bg-emerald-50/60 border border-emerald-200/80 space-y-2">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-gray-500 font-semibold">Format Rupiah:</span>
                    <span id="terbilangRp" class="font-mono font-extrabold text-gray-900"></span>
                </div>
                <p id="terbilangWords" class="text-xs font-bold text-emerald-900 italic leading-relaxed"></p>
            </div>

            <button type="button" onclick="copyTextEl('terbilangWords', this)"
                class="w-full py-2 rounded-xl bg-gray-900 hover:bg-gray-800 text-white text-xs font-bold cursor-pointer">
                Salin Teks Terbilang
            </button>
        </div>

        {{-- 2. Pembersih Kapitalisasi Nama & Gelar --}}
        <div class="bg-white rounded-2xl border border-gray-200/80 p-5 space-y-3.5 shadow-2xs">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-orange-50 text-[#FF4D00] flex items-center justify-center">
                    <i data-lucide="type" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-gray-900">Pembersih Kapitalisasi Nama &amp; Gelar</h3>
                    <p class="text-[11px] text-gray-500">Ubah huruf kapital semua (SIAKAD) ke Title Case sambil menjaga gelar akademik.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-bold text-gray-600 mb-1">Input (Bisa Banyak Baris)</label>
                    <textarea id="nameCleanInput" rows="4" oninput="updateNameCleaner()"
                        class="w-full px-3 py-2 text-xs bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#FF4D00]">SALAAMAH NAASYIATUL LAILI
PROF. DR. MOHAMAD RIZAN, S.E., M.M.
DARMA RIKA SWARAMARINDA, S.PD., M.SE., PH.D.</textarea>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-gray-600 mb-1">Hasil Rapi</label>
                    <textarea id="nameCleanOutput" rows="4" readonly
                        class="w-full px-3 py-2 text-xs font-semibold bg-orange-50/40 border border-orange-200/80 rounded-xl text-gray-900"></textarea>
                </div>
            </div>

            <button type="button" onclick="copyTextEl('nameCleanOutput', this, true)"
                class="w-full py-2 rounded-xl bg-gradient-to-r from-[#FF4D00] to-amber-500 text-white text-xs font-bold cursor-pointer">
                Salin Semua Hasil Rapi
            </button>
        </div>

        {{-- 3. Romawi & Kalkulator Durasi Tanggal (PKL/SK) --}}
        <div class="bg-white rounded-2xl border border-gray-200/80 p-5 space-y-3.5 shadow-2xs">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-700 flex items-center justify-center">
                    <i data-lucide="calendar-range" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-gray-900">Kalkulator Durasi PKL/SK &amp; Angka Romawi</h3>
                    <p class="text-[11px] text-gray-500">Hitung hari kalender, hari kerja (Senin&ndash;Jumat), &amp; konversi angka Romawi surat.</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[11px] font-bold text-gray-600 mb-1">Tanggal Mulai</label>
                    <input type="date" id="durStart" onchange="updateDuration()" class="w-full px-3 py-2 text-xs bg-gray-50 border border-gray-200 rounded-xl">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-gray-600 mb-1">Tanggal Selesai</label>
                    <input type="date" id="durEnd" onchange="updateDuration()" class="w-full px-3 py-2 text-xs bg-gray-50 border border-gray-200 rounded-xl">
                </div>
            </div>

            <div id="durResultBox" class="p-3.5 rounded-xl bg-indigo-50/60 border border-indigo-200/80 text-xs space-y-1">
                <p id="durSummary" class="font-extrabold text-indigo-950"></p>
                <p id="durFormalText" class="text-[11px] text-indigo-800"></p>
            </div>

            <div class="pt-2 border-t border-gray-100 flex items-center gap-3">
                <div class="flex-1">
                    <label class="block text-[11px] font-bold text-gray-600 mb-1">Konversi Angka &rarr; Romawi</label>
                    <input type="number" id="romanInput" min="1" max="3999" value="{{ date('n') }}" oninput="updateRoman()"
                        class="w-full px-3 py-2 text-xs font-mono bg-gray-50 border border-gray-200 rounded-xl">
                </div>
                <div class="px-4 py-2 rounded-xl bg-gray-900 text-white text-center min-w-[90px]">
                    <span class="block text-[9px] text-gray-400 uppercase">Romawi</span>
                    <span id="romanOutput" class="text-sm font-extrabold font-mono"></span>
                </div>
            </div>
        </div>

        {{-- 4. Generator Link WA & Google Drive Direct -> 1 Klik QR --}}
        <div class="bg-white rounded-2xl border border-gray-200/80 p-5 space-y-4 shadow-2xs">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-teal-50 text-teal-700 flex items-center justify-center">
                    <i data-lucide="link" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-gray-900">Generator Link WhatsApp &amp; GDrive Direct</h3>
                    <p class="text-[11px] text-gray-500">Buat tautan rapi lalu ubah langsung ke QR Code Studio dalam 1 klik.</p>
                </div>
            </div>

            {{-- WhatsApp Link --}}
            <div class="p-3.5 rounded-xl bg-gray-50 border border-gray-200/80 space-y-2">
                <span class="text-[11px] font-extrabold text-gray-700 uppercase">Link WhatsApp Otomatis</span>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <input type="text" id="waGenPhone" placeholder="No. HP (cth: 08123456789)" oninput="updateWaLink()" class="px-3 py-1.5 text-xs bg-white border border-gray-200 rounded-lg">
                    <input type="text" id="waGenMsg" placeholder="Pesan otomatis (opsional)" oninput="updateWaLink()" class="px-3 py-1.5 text-xs bg-white border border-gray-200 rounded-lg">
                </div>
                <div class="flex items-center gap-1.5">
                    <input type="text" id="waGenOutput" readonly class="flex-1 px-2.5 py-1.5 text-[11px] font-mono bg-white border border-gray-200 rounded-lg text-gray-700">
                    <button type="button" onclick="copyTextEl('waGenOutput', this, true)" class="px-2.5 py-1.5 rounded-lg bg-gray-800 text-white text-[11px] font-bold cursor-pointer">Salin</button>
                    <button type="button" onclick="sendToQrStudio('waGenOutput')" class="px-2.5 py-1.5 rounded-lg bg-[#FF4D00] text-white text-[11px] font-bold cursor-pointer">QR &rarr;</button>
                </div>
            </div>

            {{-- GDrive Direct Download Link --}}
            <div class="p-3.5 rounded-xl bg-gray-50 border border-gray-200/80 space-y-2">
                <span class="text-[11px] font-extrabold text-gray-700 uppercase">Konverter Link Google Drive Direct Download</span>
                <input type="text" id="gdShareInput" placeholder="Tempel link https://drive.google.com/file/d/.../view" oninput="updateGdriveLink()"
                    class="w-full px-3 py-1.5 text-xs bg-white border border-gray-200 rounded-lg">
                <div class="flex items-center gap-1.5">
                    <input type="text" id="gdDirectOutput" readonly placeholder="Link direct download muncul di sini..." class="flex-1 px-2.5 py-1.5 text-[11px] font-mono bg-white border border-gray-200 rounded-lg text-gray-700">
                    <button type="button" onclick="copyTextEl('gdDirectOutput', this, true)" class="px-2.5 py-1.5 rounded-lg bg-gray-800 text-white text-[11px] font-bold cursor-pointer">Salin</button>
                    <button type="button" onclick="sendToQrStudio('gdDirectOutput')" class="px-2.5 py-1.5 rounded-lg bg-[#FF4D00] text-white text-[11px] font-bold cursor-pointer">QR &rarr;</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const QR_STUDIO_BASE = @json(route('tools.qr-barcode'));

    function copyTextEl(id, btn, isInput = false) {
        const el = document.getElementById(id);
        const val = isInput ? el.value : el.textContent;
        if (!val) return;
        navigator.clipboard?.writeText(val);
        const orig = btn.textContent;
        btn.textContent = 'Tersalin!';
        setTimeout(() => { btn.textContent = orig; }, 1400);
    }

    function sendToQrStudio(inputId) {
        const url = (document.getElementById(inputId).value || '').trim();
        if (!url) return;
        window.location.href = `${QR_STUDIO_BASE}?url=${encodeURIComponent(url)}`;
    }

    // ── 1. Terbilang Rupiah ──
    function angkaKeKata(n) {
        const satuan = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];
        if (n < 12) return satuan[n];
        if (n < 20) return angkaKeKata(n - 10) + ' Belas';
        if (n < 100) return angkaKeKata(Math.floor(n / 10)) + ' Puluh' + (n % 10 ? ' ' + angkaKeKata(n % 10) : '');
        if (n < 200) return 'Seratus' + (n % 100 ? ' ' + angkaKeKata(n - 100) : '');
        if (n < 1000) return angkaKeKata(Math.floor(n / 100)) + ' Ratus' + (n % 100 ? ' ' + angkaKeKata(n % 100) : '');
        if (n < 2000) return 'Seribu' + (n % 1000 ? ' ' + angkaKeKata(n - 1000) : '');
        if (n < 1000000) return angkaKeKata(Math.floor(n / 1000)) + ' Ribu' + (n % 1000 ? ' ' + angkaKeKata(n % 1000) : '');
        if (n < 1000000000) return angkaKeKata(Math.floor(n / 1000000)) + ' Juta' + (n % 1000000 ? ' ' + angkaKeKata(n % 1000000) : '');
        if (n < 1000000000000) return angkaKeKata(Math.floor(n / 1000000000)) + ' Miliar' + (n % 1000000000 ? ' ' + angkaKeKata(n % 1000000000) : '');
        if (n < 1000000000000000) return angkaKeKata(Math.floor(n / 1000000000000)) + ' Triliun' + (n % 1000000000000 ? ' ' + angkaKeKata(n % 1000000000000) : '');
        return '';
    }

    function updateTerbilang() {
        const digits = (document.getElementById('terbilangInput').value || '').replace(/\D+/g, '');
        const num = digits ? parseInt(digits, 10) : 0;
        document.getElementById('terbilangRp').textContent = 'Rp ' + num.toLocaleString('id-ID') + ',-';
        document.getElementById('terbilangWords').textContent = (num === 0 ? 'Nol' : angkaKeKata(num).trim()) + ' Rupiah';
    }

    // ── 2. Pembersih Kapitalisasi Nama & Gelar ──
    const TITLE_MAP = {
        'prof.': 'Prof.', 'dr.': 'Dr.', 'drs.': 'Drs.', 'dra.': 'Dra.', 'ir.': 'Ir.', 'h.': 'H.', 'hj.': 'Hj.',
        's.e.': 'S.E.', 's.pd.': 'S.Pd.', 's.kom.': 'S.Kom.', 's.si.': 'S.Si.', 's.sos.': 'S.Sos.', 's.h.': 'S.H.',
        's.ak.': 'S.Ak.', 's.m.': 'S.M.', 's.ab.': 'S.AB.', 's.t.': 'S.T.', 's.psi.': 'S.Psi.', 's.tr.': 'S.Tr.',
        'm.m.': 'M.M.', 'm.se.': 'M.SE.', 'm.pd.': 'M.Pd.', 'm.si.': 'M.Si.', 'm.kom.': 'M.Kom.', 'm.ak.': 'M.Ak.',
        'm.h.': 'M.H.', 'm.t.': 'M.T.', 'm.sc.': 'M.Sc.', 'm.ba.': 'M.BA.', 'mba.': 'MBA.', 'm.a.': 'M.A.',
        'ph.d.': 'Ph.D.', 'akt.': 'Akt.', 'ca.': 'CA.', 'cpa.': 'CPA.', 'cfr.': 'CFR.',
    };

    function formatPersonNameLine(line) {
        return line.split(/\s+/).map(token => {
            if (!token) return '';
            const trailingComma = token.endsWith(',') ? ',' : '';
            const core = trailingComma ? token.slice(0, -1) : token;
            const lower = core.toLowerCase();
            if (TITLE_MAP[lower]) return TITLE_MAP[lower] + trailingComma;
            if (TITLE_MAP[lower + '.']) return TITLE_MAP[lower + '.'] + trailingComma;
            if (core.includes('.')) {
                // Gelar lain dengan titik: pertahankan kapitalisasi umum S.X.
                return core.split('.').map(p => p ? p.charAt(0).toUpperCase() + p.slice(1).toLowerCase() : '').join('.') + trailingComma;
            }
            return lower.charAt(0).toUpperCase() + lower.slice(1) + trailingComma;
        }).join(' ');
    }

    function updateNameCleaner() {
        const lines = (document.getElementById('nameCleanInput').value || '').split(/\r?\n/);
        document.getElementById('nameCleanOutput').value = lines.map(formatPersonNameLine).join('\n');
    }

    // ── 3. Durasi Tanggal & Angka Romawi ──
    function updateDuration() {
        const sVal = document.getElementById('durStart').value;
        const eVal = document.getElementById('durEnd').value;
        if (!sVal || !eVal) return;

        const d1 = new Date(sVal + 'T00:00:00');
        const d2 = new Date(eVal + 'T00:00:00');
        if (d2 < d1) {
            document.getElementById('durSummary').textContent = 'Tanggal selesai harus lebih besar atau sama dengan tanggal mulai.';
            document.getElementById('durFormalText').textContent = '';
            return;
        }

        const totalDays = Math.round((d2 - d1) / 86400000) + 1;
        let workDays = 0;
        const cur = new Date(d1);
        while (cur <= d2) {
            const day = cur.getDay();
            if (day !== 0 && day !== 6) workDays++;
            cur.setDate(cur.getDate() + 1);
        }

        const monthsApprox = (totalDays / 30).toFixed(1).replace('.0', '');
        const weeks = Math.floor(totalDays / 7);
        const remDays = totalDays % 7;

        const fmtOpt = { day: '2-digit', month: 'long', year: 'numeric' };
        document.getElementById('durSummary').textContent =
            `${totalDays} hari kalender (${workDays} hari kerja Sen\u2013Jum) \u2022 \u00B1${monthsApprox} bulan / ${weeks} minggu ${remDays} hari`;
        document.getElementById('durFormalText').textContent =
            `Format Surat: ${d1.toLocaleDateString('id-ID', fmtOpt)} sampai dengan ${d2.toLocaleDateString('id-ID', fmtOpt)}`;
    }

    function updateRoman() {
        let num = parseInt(document.getElementById('romanInput').value || '1', 10);
        if (isNaN(num) || num < 1) num = 1;
        if (num > 3999) num = 3999;
        const table = [
            [1000, 'M'], [900, 'CM'], [500, 'D'], [400, 'CD'],
            [100, 'C'], [90, 'XC'], [50, 'L'], [40, 'XL'],
            [10, 'X'], [9, 'IX'], [5, 'V'], [4, 'IV'], [1, 'I']
        ];
        let res = '';
        for (const [val, sym] of table) {
            while (num >= val) { res += sym; num -= val; }
        }
        document.getElementById('romanOutput').textContent = res;
    }

    // ── 4. WA & GDrive Links ──
    function updateWaLink() {
        let phone = (document.getElementById('waGenPhone').value || '').replace(/\D+/g, '');
        if (phone.startsWith('0')) phone = '62' + phone.slice(1);
        const msg = (document.getElementById('waGenMsg').value || '').trim();
        document.getElementById('waGenOutput').value = phone
            ? `https://wa.me/${phone}${msg ? '?text=' + encodeURIComponent(msg) : ''}`
            : '';
    }

    function updateGdriveLink() {
        const raw = (document.getElementById('gdShareInput').value || '').trim();
        const m = raw.match(/\/d\/([a-zA-Z0-9_-]+)/) || raw.match(/[?&]id=([a-zA-Z0-9_-]+)/);
        document.getElementById('gdDirectOutput').value = m
            ? `https://drive.google.com/uc?export=download&id=${m[1]}`
            : '';
    }

    document.addEventListener('DOMContentLoaded', () => {
        const now = new Date();
        const nextMonth = new Date(now.getFullYear(), now.getMonth() + 3, now.getDate());
        document.getElementById('durStart').value = now.toISOString().slice(0, 10);
        document.getElementById('durEnd').value = nextMonth.toISOString().slice(0, 10);

        updateTerbilang();
        updateNameCleaner();
        updateDuration();
        updateRoman();
    });
</script>
@endpush
