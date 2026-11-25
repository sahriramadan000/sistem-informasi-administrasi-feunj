@extends('layouts.app')

@section('title', 'QR Code & Barcode Studio')

@push('styles')
<style>
    .checkerboard-qr {
        background-color: #f8fafc;
        background-image:
            linear-gradient(45deg, #e2e8f0 25%, transparent 25%),
            linear-gradient(-45deg, #e2e8f0 25%, transparent 25%),
            linear-gradient(45deg, transparent 75%, #e2e8f0 75%),
            linear-gradient(-45deg, transparent 75%, #e2e8f0 75%);
        background-size: 14px 14px;
        background-position: 0 0, 0 7px, 7px -7px, -7px 0;
    }
    .tpl-chip.active {
        border-color: #FF4D00;
        background-color: #FFF7ED;
        box-shadow: 0 0 0 2px rgba(255, 77, 0, 0.16);
    }
</style>
@endpush

@section('content')
<div class="space-y-5">
    {{-- Header & Tab Switcher --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-4 rounded-2xl border border-gray-200/80 shadow-2xs">
        <div class="flex items-center gap-3">
            <a href="{{ route('tools.index') }}" class="w-9 h-9 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 flex items-center justify-center transition-colors" title="Kembali ke Semua Tools">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
            </a>
            <div>
                <h2 class="text-base font-extrabold text-gray-900">QR Code &amp; Barcode Studio</h2>
                <p class="text-xs text-gray-500">Buat QR custom gambar/logo tengah dengan template desain siap cetak atau Barcode 1D inventaris.</p>
            </div>
        </div>

        <div class="inline-flex p-1 rounded-xl bg-gray-100 border border-gray-200/80 self-start sm:self-auto">
            <button type="button" id="mainTabBtn-qr" onclick="switchMainStudioTab('qr')"
                class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer">
                <span class="inline-flex items-center gap-1.5"><i data-lucide="qr-code" class="w-3.5 h-3.5"></i><span>QR Code Custom</span></span>
            </button>
            <button type="button" id="mainTabBtn-barcode" onclick="switchMainStudioTab('barcode')"
                class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer">
                <span class="inline-flex items-center gap-1.5"><i data-lucide="barcode" class="w-3.5 h-3.5"></i><span>Barcode 1D</span></span>
            </button>
        </div>
    </div>

    {{-- Toast Notification --}}
    <div id="qrToast" class="hidden fixed bottom-5 right-5 z-50 px-4 py-2.5 rounded-xl bg-slate-900 text-white text-xs font-semibold shadow-xl flex items-center gap-2">
        <i data-lucide="info" class="w-4 h-4 text-amber-400"></i>
        <span id="qrToastText"></span>
    </div>

    {{-- Hidden Raw Renderer for QRCodeStyling --}}
    <div id="qrRawStaging" class="hidden"></div>

    {{-- ======================= PANEL 1: QR CODE STUDIO ======================= --}}
    <div id="mainPanel-qr" class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
        {{-- Left Controls --}}
        <div class="lg:col-span-7 space-y-4">
            {{-- 1. Suggest Template Desain --}}
            <div class="bg-white rounded-2xl border border-gray-200/80 p-4 sm:p-5 space-y-3 shadow-2xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-extrabold uppercase tracking-wider text-gray-500 flex items-center gap-1.5">
                        <i data-lucide="sparkles" class="w-3.5 h-3.5 text-[#FF4D00]"></i>
                        <span>Rekomendasi Template Desain</span>
                    </span>
                    <span class="text-[11px] text-gray-400">Klik untuk terapkan gaya instan</span>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                    <button type="button" onclick="applyQrTemplate('unj_official', this)"
                        class="tpl-chip active text-left p-3 rounded-xl border border-gray-200 hover:border-emerald-400 transition-all cursor-pointer">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="w-4 h-4 rounded-full bg-gradient-to-br from-[#006B3C] to-emerald-500 flex-shrink-0"></span>
                            <span class="text-xs font-extrabold text-gray-900 truncate">Resmi UNJ</span>
                        </div>
                        <p class="text-[10.5px] text-gray-500 leading-snug">Hijau-Emas + Logo UNJ &amp; bingkai resmi</p>
                    </button>

                    <button type="button" onclick="applyQrTemplate('feb_modern', this)"
                        class="tpl-chip text-left p-3 rounded-xl border border-gray-200 hover:border-orange-400 transition-all cursor-pointer">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="w-4 h-4 rounded-full bg-gradient-to-br from-[#EA580C] to-amber-500 flex-shrink-0"></span>
                            <span class="text-xs font-extrabold text-gray-900 truncate">FEB Modern</span>
                        </div>
                        <p class="text-[10.5px] text-gray-500 leading-snug">Oranye-Amber, dot membulat modern</p>
                    </button>

                    <button type="button" onclick="applyQrTemplate('minimal_letter', this)"
                        class="tpl-chip text-left p-3 rounded-xl border border-gray-200 hover:border-slate-500 transition-all cursor-pointer">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="w-4 h-4 rounded-full bg-slate-900 flex-shrink-0"></span>
                            <span class="text-xs font-extrabold text-gray-900 truncate">Minimalis Surat</span>
                        </div>
                        <p class="text-[10.5px] text-gray-500 leading-snug">Hitam polos tegas untuk cetak surat</p>
                    </button>

                    <button type="button" onclick="applyQrTemplate('event_poster', this)"
                        class="tpl-chip text-left p-3 rounded-xl border border-gray-200 hover:border-blue-400 transition-all cursor-pointer">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="w-4 h-4 rounded-full bg-gradient-to-br from-blue-800 to-blue-500 flex-shrink-0"></span>
                            <span class="text-xs font-extrabold text-gray-900 truncate">Poster Presensi</span>
                        </div>
                        <p class="text-[10.5px] text-gray-500 leading-snug">Kartu meja event &amp; absensi kegiatan</p>
                    </button>

                    <button type="button" onclick="applyQrTemplate('room_service', this)"
                        class="tpl-chip text-left p-3 rounded-xl border border-gray-200 hover:border-teal-400 transition-all cursor-pointer">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="w-4 h-4 rounded-full bg-gradient-to-br from-teal-700 to-cyan-500 flex-shrink-0"></span>
                            <span class="text-xs font-extrabold text-gray-900 truncate">Layanan Ruang</span>
                        </div>
                        <p class="text-[10.5px] text-gray-500 leading-snug">Stiker jadwal ruangan / WiFi kelas</p>
                    </button>

                    <button type="button" onclick="applyQrTemplate('doc_verify', this)"
                        class="tpl-chip text-left p-3 rounded-xl border border-gray-200 hover:border-indigo-400 transition-all cursor-pointer">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="w-4 h-4 rounded-full bg-gradient-to-br from-indigo-700 to-violet-500 flex-shrink-0"></span>
                            <span class="text-xs font-extrabold text-gray-900 truncate">Verifikasi Berkas</span>
                        </div>
                        <p class="text-[10.5px] text-gray-500 leading-snug">Badge verifikasi dokumen &amp; sertifikat</p>
                    </button>
                </div>
            </div>

            {{-- 2. Konten / Link Tujuan --}}
            <div class="bg-white rounded-2xl border border-gray-200/80 p-4 sm:p-5 space-y-3.5 shadow-2xs">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <span class="text-xs font-extrabold uppercase tracking-wider text-gray-500">1. Konten QR Code</span>
                    <div class="flex flex-wrap gap-1">
                        @foreach (['url' => 'Link / URL', 'wa' => 'WhatsApp', 'wifi' => 'WiFi', 'email' => 'Email', 'text' => 'Teks Bebas'] as $typeKey => $typeLabel)
                            <button type="button" onclick="setQrPayloadType('{{ $typeKey }}')" id="qrTypeBtn-{{ $typeKey }}"
                                class="qr-type-btn px-2.5 py-1 rounded-lg text-[11px] font-bold transition-colors cursor-pointer">
                                {{ $typeLabel }}
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Dynamic Payload Forms --}}
                <div id="payloadForm-url" class="space-y-1.5">
                    <label class="block text-xs font-semibold text-gray-700">Alamat Website / Google Drive / Google Form</label>
                    <input type="text" id="qrInputUrl" value="{{ $initialUrl !== '' ? $initialUrl : 'https://fe.unj.ac.id' }}"
                        placeholder="https://drive.google.com/..." oninput="scheduleQrRender()"
                        class="w-full px-3 py-2 text-xs font-medium bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#FF4D00] focus:bg-white">
                </div>

                <div id="payloadForm-wa" class="hidden grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Nomor WhatsApp (cth: 0812...)</label>
                        <input type="text" id="qrInputWaPhone" placeholder="081234567890" oninput="scheduleQrRender()"
                            class="w-full px-3 py-2 text-xs bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#FF4D00]">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Pesan Pembuka (Opsional)</label>
                        <input type="text" id="qrInputWaMsg" placeholder="Halo Layanan Akademik FEB UNJ..." oninput="scheduleQrRender()"
                            class="w-full px-3 py-2 text-xs bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#FF4D00]">
                    </div>
                </div>

                <div id="payloadForm-wifi" class="hidden grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Nama WiFi (SSID)</label>
                        <input type="text" id="qrInputWifiSsid" placeholder="FEB-UNJ-Guest" oninput="scheduleQrRender()"
                            class="w-full px-3 py-2 text-xs bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#FF4D00]">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Password WiFi</label>
                        <input type="text" id="qrInputWifiPass" placeholder="********" oninput="scheduleQrRender()"
                            class="w-full px-3 py-2 text-xs bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#FF4D00]">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Keamanan</label>
                        <select id="qrInputWifiEnc" onchange="scheduleQrRender()"
                            class="w-full px-3 py-2 text-xs bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#FF4D00]">
                            <option value="WPA">WPA / WPA2</option>
                            <option value="WEP">WEP</option>
                            <option value="nopass">Tanpa Password</option>
                        </select>
                    </div>
                </div>

                <div id="payloadForm-email" class="hidden grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Alamat Email Tujuan</label>
                        <input type="email" id="qrInputEmailTo" placeholder="tu.feb@unj.ac.id" oninput="scheduleQrRender()"
                            class="w-full px-3 py-2 text-xs bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#FF4D00]">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Subjek Email</label>
                        <input type="text" id="qrInputEmailSubj" placeholder="Permohonan Surat" oninput="scheduleQrRender()"
                            class="w-full px-3 py-2 text-xs bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#FF4D00]">
                    </div>
                </div>

                <div id="payloadForm-text" class="hidden">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Isi Teks / Nomor Surat / Informasi</label>
                    <textarea id="qrInputText" rows="2" placeholder="Masukkan teks apa saja..." oninput="scheduleQrRender()"
                        class="w-full px-3 py-2 text-xs bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#FF4D00]"></textarea>
                </div>
            </div>

            {{-- 3. Custom Logo / Image Tengah --}}
            <div class="bg-white rounded-2xl border border-gray-200/80 p-4 sm:p-5 space-y-3.5 shadow-2xs">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-extrabold uppercase tracking-wider text-gray-500">2. Gambar / Logo Tengah (Opsional)</span>
                    <span class="text-[10.5px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full">Auto Koreksi Error Level H</span>
                </div>

                <div class="flex flex-wrap items-center gap-2.5">
                    <label class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-gray-900 hover:bg-gray-800 text-white text-xs font-bold cursor-pointer transition-colors">
                        <i data-lucide="image-plus" class="w-4 h-4"></i>
                        <span>Upload Gambar Sendiri</span>
                        <input type="file" id="qrLogoFileInput" accept="image/*" class="hidden" onchange="handleQrLogoUpload(this.files[0])">
                    </label>

                    <button type="button" onclick="useUnjPresetLogo()"
                        class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 text-xs font-bold cursor-pointer transition-colors">
                        <img src="{{ asset('logo-unj.png') }}" alt="UNJ" class="w-4 h-4 object-contain">
                        <span>Pakai Logo UNJ</span>
                    </button>

                    <button type="button" onclick="clearQrLogo()"
                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-gray-100 hover:bg-rose-50 text-gray-600 hover:text-rose-600 text-xs font-semibold cursor-pointer transition-colors">
                        <i data-lucide="x" class="w-3.5 h-3.5"></i>
                        <span>Tanpa Logo</span>
                    </button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                    <div>
                        <div class="flex justify-between text-xs font-semibold text-gray-700 mb-1">
                            <span>Ukuran Logo Tengah</span>
                            <span id="qrLogoSizeLabel" class="font-mono text-[#FF4D00]">26%</span>
                        </div>
                        <input type="range" id="qrLogoSize" min="18" max="34" value="26" oninput="document.getElementById('qrLogoSizeLabel').textContent = this.value + '%'; scheduleQrRender()"
                            class="w-full accent-[#FF4D00]">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Margin Bersih Sekitar Logo (px)</label>
                        <input type="range" id="qrLogoMargin" min="0" max="14" value="6" oninput="scheduleQrRender()"
                            class="w-full accent-[#FF4D00]">
                    </div>
                </div>
            </div>

            {{-- 4. Gaya Visual & Bingkai Poster --}}
            <div class="bg-white rounded-2xl border border-gray-200/80 p-4 sm:p-5 space-y-4 shadow-2xs">
                <span class="block text-xs font-extrabold uppercase tracking-wider text-gray-500">3. Gaya QR &amp; Bingkai Poster</span>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold text-gray-600 mb-1">Bentuk Titik (Dots)</label>
                        <select id="qrDotStyle" onchange="scheduleQrRender()" class="w-full px-2.5 py-2 text-xs bg-gray-50 border border-gray-200 rounded-xl">
                            <option value="rounded">Rounded (Membulat)</option>
                            <option value="dots">Dots (Lingkaran)</option>
                            <option value="classy-rounded">Classy Rounded</option>
                            <option value="extra-rounded">Extra Rounded</option>
                            <option value="square">Kotak Tegas</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-gray-600 mb-1">Kotak Sudut Luar</label>
                        <select id="qrCornerStyle" onchange="scheduleQrRender()" class="w-full px-2.5 py-2 text-xs bg-gray-50 border border-gray-200 rounded-xl">
                            <option value="extra-rounded">Extra Rounded</option>
                            <option value="dot">Lingkaran</option>
                            <option value="square">Kotak Tegas</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-gray-600 mb-1">Titik Sudut Dalam</label>
                        <select id="qrCornerDotStyle" onchange="scheduleQrRender()" class="w-full px-2.5 py-2 text-xs bg-gray-50 border border-gray-200 rounded-xl">
                            <option value="dot">Lingkaran</option>
                            <option value="square">Kotak</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 items-end">
                    <div>
                        <label class="block text-[11px] font-bold text-gray-600 mb-1">Warna Utama</label>
                        <input type="color" id="qrColorPrimary" value="#006B3C" onchange="scheduleQrRender()" class="w-full h-9 rounded-lg border border-gray-200 cursor-pointer">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-gray-600 mb-1">Warna Gradien 2</label>
                        <input type="color" id="qrColorSecondary" value="#047857" onchange="scheduleQrRender()" class="w-full h-9 rounded-lg border border-gray-200 cursor-pointer">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-gray-600 mb-1">Background QR</label>
                        <input type="color" id="qrColorBg" value="#FFFFFF" onchange="scheduleQrRender()" class="w-full h-9 rounded-lg border border-gray-200 cursor-pointer">
                    </div>
                    <label class="inline-flex items-center gap-2 h-9 px-3 rounded-xl bg-gray-50 border border-gray-200 text-xs font-semibold text-gray-700 cursor-pointer">
                        <input type="checkbox" id="qrUseGradient" checked onchange="scheduleQrRender()" class="rounded text-[#FF4D00]">
                        <span>Gradien</span>
                    </label>
                </div>

                <div class="pt-3 border-t border-gray-100 space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="inline-flex items-center gap-2 text-xs font-bold text-gray-800 cursor-pointer">
                            <input type="checkbox" id="qrShowFrame" checked onchange="scheduleQrRender()" class="rounded text-[#FF4D00]">
                            <span>Tampilkan Bingkai Kartu &amp; Label Teks (Siap Cetak)</span>
                        </label>
                        <input type="color" id="qrFrameAccent" value="#006B3C" onchange="scheduleQrRender()" title="Warna Aksen Bingkai" class="w-8 h-7 rounded border border-gray-200 cursor-pointer">
                    </div>

                    <div id="qrFrameInputs" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-600 mb-1">Judul Atas Kartu</label>
                            <input type="text" id="qrFrameTitle" value="FAKULTAS EKONOMI & BISNIS UNJ" oninput="scheduleQrRender()"
                                class="w-full px-3 py-2 text-xs bg-gray-50 border border-gray-200 rounded-xl">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-600 mb-1">Teks Badge Bawah</label>
                            <input type="text" id="qrFrameSubtitle" value="SCAN DI SINI" oninput="scheduleQrRender()"
                                class="w-full px-3 py-2 text-xs bg-gray-50 border border-gray-200 rounded-xl">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Sticky Preview & Export --}}
        <div class="lg:col-span-5 lg:sticky lg:top-20 space-y-4">
            <div class="bg-white rounded-2xl border border-gray-200/80 p-5 shadow-2xs space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-extrabold uppercase tracking-wider text-gray-500">Pratinjau Live</span>
                    <span id="qrStatusPill" class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Siap Pindai</span>
                </div>

                <div class="checkerboard-qr rounded-2xl border border-gray-200/80 p-5 flex items-center justify-center min-h-[340px]">
                    <canvas id="qrPosterCanvas" class="max-w-full max-h-[380px] rounded-xl shadow-md"></canvas>
                </div>

                <div class="space-y-2.5 pt-1">
                    <div class="grid grid-cols-2 gap-2">
                        <select id="qrExportScale" class="px-3 py-2 text-xs font-bold bg-gray-50 border border-gray-200 rounded-xl text-gray-700">
                            <option value="1000">Resolusi 1000px (Standar)</option>
                            <option value="1600" selected>Resolusi 1600px (HD)</option>
                            <option value="2200">Resolusi 2200px (Cetak Tajam)</option>
                        </select>
                        <button type="button" onclick="downloadQrPng()"
                            class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl bg-gradient-to-r from-[#FF4D00] to-amber-500 hover:from-[#e04400] hover:to-[#d97706] text-white text-xs font-bold shadow-xs cursor-pointer">
                            <i data-lucide="download" class="w-3.5 h-3.5"></i>
                            <span>Download PNG</span>
                        </button>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <button type="button" onclick="downloadQrSvg()"
                            class="inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl bg-gray-900 hover:bg-gray-800 text-white text-xs font-bold cursor-pointer">
                            <i data-lucide="file-code-2" class="w-3.5 h-3.5"></i>
                            <span>Download SVG Vektor</span>
                        </button>

                        <div class="flex gap-1.5">
                            <select id="qrPdfLayout" class="flex-1 min-w-0 px-2.5 py-2 text-xs font-semibold bg-gray-50 border border-gray-200 rounded-xl text-gray-700">
                                <option value="single">PDF: 1 Poster A4</option>
                                <option value="grid6">PDF: 6 Stiker (2&times;3)</option>
                                <option value="grid12">PDF: 12 Stiker (3&times;4)</option>
                            </select>
                            <button type="button" onclick="downloadQrPdf()"
                                class="px-3 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold cursor-pointer flex-shrink-0">
                                PDF
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ======================= PANEL 2: BARCODE 1D ======================= --}}
    <div id="mainPanel-barcode" class="hidden grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
        <div class="lg:col-span-6 bg-white rounded-2xl border border-gray-200/80 p-5 space-y-4 shadow-2xs">
            <span class="block text-xs font-extrabold uppercase tracking-wider text-gray-500">Konfigurasi Barcode 1D (Inventaris / Arsip)</span>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Format Barcode</label>
                    <select id="bcFormat" onchange="renderBarcode()" class="w-full px-3 py-2 text-xs bg-gray-50 border border-gray-200 rounded-xl font-semibold">
                        <option value="CODE128">CODE128 (Huruf &amp; Angka Umum)</option>
                        <option value="CODE39">CODE39 (Kode Aset / Surat)</option>
                        <option value="EAN13">EAN-13 (13 Digit Angka)</option>
                        <option value="ITF14">ITF-14 (14 Digit Angka)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Label Header (Opsional)</label>
                    <input type="text" id="bcTopLabel" value="ARSIP FEB UNJ" oninput="renderBarcode()"
                        class="w-full px-3 py-2 text-xs bg-gray-50 border border-gray-200 rounded-xl">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Isi Kode / Nomor</label>
                <input type="text" id="bcValue" value="FEB-UNJ-2026-001" oninput="renderBarcode()"
                    placeholder="Contoh: B/3960/UN39.7/2026 atau 8991234567890"
                    class="w-full px-3 py-2 text-xs font-mono bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-[#FF4D00]">
                <p id="bcErrorMsg" class="hidden text-[11px] font-semibold text-rose-600 mt-1"></p>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 items-end">
                <div>
                    <label class="block text-[11px] font-bold text-gray-600 mb-1">Ketebalan Bar</label>
                    <input type="number" id="bcWidth" min="1" max="5" step="0.5" value="2" oninput="renderBarcode()"
                        class="w-full px-2.5 py-2 text-xs bg-gray-50 border border-gray-200 rounded-xl">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-gray-600 mb-1">Tinggi Bar</label>
                    <input type="number" id="bcHeight" min="40" max="180" value="90" oninput="renderBarcode()"
                        class="w-full px-2.5 py-2 text-xs bg-gray-50 border border-gray-200 rounded-xl">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-gray-600 mb-1">Warna Garis</label>
                    <input type="color" id="bcLineColor" value="#0F172A" onchange="renderBarcode()" class="w-full h-9 rounded-lg border border-gray-200 cursor-pointer">
                </div>
                <label class="inline-flex items-center gap-2 h-9 px-3 rounded-xl bg-gray-50 border border-gray-200 text-xs font-semibold text-gray-700 cursor-pointer">
                    <input type="checkbox" id="bcDisplayText" checked onchange="renderBarcode()" class="rounded text-[#FF4D00]">
                    <span>Teks Kode</span>
                </label>
            </div>
        </div>

        <div class="lg:col-span-6 bg-white rounded-2xl border border-gray-200/80 p-5 space-y-4 shadow-2xs">
            <span class="block text-xs font-extrabold uppercase tracking-wider text-gray-500">Pratinjau &amp; Download Barcode</span>
            <div class="checkerboard-qr rounded-2xl border border-gray-200/80 p-6 flex flex-col items-center justify-center min-h-[260px]">
                <div class="bg-white px-6 py-4 rounded-xl shadow-sm border border-gray-100 text-center space-y-1 max-w-full overflow-x-auto">
                    <div id="bcTopPreview" class="text-[11px] font-extrabold tracking-wider text-slate-700 uppercase">ARSIP FEB UNJ</div>
                    <svg id="barcodeSvg" class="mx-auto"></svg>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <button type="button" onclick="downloadBarcodePng()"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-[#FF4D00] to-amber-500 text-white text-xs font-bold shadow-xs cursor-pointer">
                    <i data-lucide="download" class="w-4 h-4"></i>
                    <span>Download PNG</span>
                </button>
                <button type="button" onclick="downloadBarcodeSvg()"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-gray-900 hover:bg-gray-800 text-white text-xs font-bold cursor-pointer">
                    <i data-lucide="file-code-2" class="w-4 h-4"></i>
                    <span>Download SVG</span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/qr-code-styling@1.6.0-rc.1/lib/qr-code-styling.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
    let activeMainTab = @json($initialTab);
    let activePayloadType = 'url';
    let currentLogoDataUrl = null;
    let qrRenderTimer = null;
    let qrStyledInstance = null;

    const UNJ_LOGO_URL = @json(asset('logo-unj.png'));

    const QR_TEMPLATES = {
        unj_official: {
            dotStyle: 'rounded',
            cornerStyle: 'extra-rounded',
            cornerDotStyle: 'dot',
            primary: '#006B3C',
            secondary: '#059669',
            bg: '#FFFFFF',
            useGradient: true,
            showFrame: true,
            frameAccent: '#006B3C',
            title: 'FAKULTAS EKONOMI & BISNIS UNJ',
            subtitle: 'SCAN DI SINI',
            logoMode: 'unj',
        },
        feb_modern: {
            dotStyle: 'rounded',
            cornerStyle: 'extra-rounded',
            cornerDotStyle: 'dot',
            primary: '#EA580C',
            secondary: '#F59E0B',
            bg: '#FFFFFF',
            useGradient: true,
            showFrame: true,
            frameAccent: '#EA580C',
            title: 'FEB UNIVERSITAS NEGERI JAKARTA',
            subtitle: 'PINDAI UNTUK AKSES',
            logoMode: 'keep',
        },
        minimal_letter: {
            dotStyle: 'square',
            cornerStyle: 'square',
            cornerDotStyle: 'square',
            primary: '#0F172A',
            secondary: '#0F172A',
            bg: '#FFFFFF',
            useGradient: false,
            showFrame: false,
            frameAccent: '#0F172A',
            title: '',
            subtitle: '',
            logoMode: 'none',
        },
        event_poster: {
            dotStyle: 'classy-rounded',
            cornerStyle: 'extra-rounded',
            cornerDotStyle: 'dot',
            primary: '#1E3A8A',
            secondary: '#2563EB',
            bg: '#FFFFFF',
            useGradient: true,
            showFrame: true,
            frameAccent: '#1D4ED8',
            title: 'ABSENSI / REGISTRASI KEGIATAN',
            subtitle: 'Arahkan kamera ke QR Code ini',
            logoMode: 'unj',
        },
        room_service: {
            dotStyle: 'dots',
            cornerStyle: 'extra-rounded',
            cornerDotStyle: 'dot',
            primary: '#0F766E',
            secondary: '#0891B2',
            bg: '#FFFFFF',
            useGradient: true,
            showFrame: true,
            frameAccent: '#0F766E',
            title: 'LAYANAN & JADWAL RUANGAN',
            subtitle: 'Scan untuk cek ketersediaan',
            logoMode: 'keep',
        },
        doc_verify: {
            dotStyle: 'rounded',
            cornerStyle: 'extra-rounded',
            cornerDotStyle: 'dot',
            primary: '#3730A3',
            secondary: '#6D28D9',
            bg: '#FFFFFF',
            useGradient: true,
            showFrame: true,
            frameAccent: '#4338CA',
            title: 'VERIFIKASI DOKUMEN RESMI',
            subtitle: 'SIADMIN FEB UNJ',
            logoMode: 'unj',
        },
    };

    function showQrToast(msg) {
        const el = document.getElementById('qrToast');
        document.getElementById('qrToastText').textContent = msg;
        el.classList.remove('hidden');
        clearTimeout(el._t);
        el._t = setTimeout(() => el.classList.add('hidden'), 2800);
    }

    function switchMainStudioTab(tab) {
        activeMainTab = tab;
        ['qr', 'barcode'].forEach(t => {
            const btn = document.getElementById(`mainTabBtn-${t}`);
            const panel = document.getElementById(`mainPanel-${t}`);
            const isAct = t === tab;
            panel.classList.toggle('hidden', !isAct);
            btn.className = isAct
                ? 'px-3.5 py-1.5 rounded-lg text-xs font-bold bg-white text-gray-900 shadow-2xs transition-all cursor-pointer'
                : 'px-3.5 py-1.5 rounded-lg text-xs font-semibold text-gray-500 hover:text-gray-800 transition-all cursor-pointer';
        });
        if (tab === 'barcode') renderBarcode();
        else scheduleQrRender();
    }

    function setQrPayloadType(type) {
        activePayloadType = type;
        ['url', 'wa', 'wifi', 'email', 'text'].forEach(k => {
            document.getElementById(`payloadForm-${k}`)?.classList.toggle('hidden', k !== type);
            const btn = document.getElementById(`qrTypeBtn-${k}`);
            const isAct = k === type;
            btn.className = isAct
                ? 'qr-type-btn px-2.5 py-1 rounded-lg text-[11px] font-bold bg-[#FF4D00] text-white cursor-pointer'
                : 'qr-type-btn px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-gray-100 text-gray-600 hover:bg-gray-200 cursor-pointer';
        });
        scheduleQrRender();
    }

    function buildQrPayloadString() {
        if (activePayloadType === 'url') {
            return (document.getElementById('qrInputUrl').value || '').trim() || 'https://fe.unj.ac.id';
        }
        if (activePayloadType === 'wa') {
            let num = (document.getElementById('qrInputWaPhone').value || '').replace(/\D+/g, '');
            if (num.startsWith('0')) num = '62' + num.slice(1);
            const msg = (document.getElementById('qrInputWaMsg').value || '').trim();
            return `https://wa.me/${num || '62'}${msg ? '?text=' + encodeURIComponent(msg) : ''}`;
        }
        if (activePayloadType === 'wifi') {
            const ssid = document.getElementById('qrInputWifiSsid').value || 'FEB-UNJ';
            const pass = document.getElementById('qrInputWifiPass').value || '';
            const enc = document.getElementById('qrInputWifiEnc').value || 'WPA';
            return `WIFI:T:${enc};S:${ssid};P:${enc === 'nopass' ? '' : pass};;`;
        }
        if (activePayloadType === 'email') {
            const to = (document.getElementById('qrInputEmailTo').value || '').trim();
            const subj = (document.getElementById('qrInputEmailSubj').value || '').trim();
            return `mailto:${to}${subj ? '?subject=' + encodeURIComponent(subj) : ''}`;
        }
        return (document.getElementById('qrInputText').value || '').trim() || 'SIADMIN FEB UNJ';
    }

    async function urlToDataUrl(url) {
        const resp = await fetch(url);
        const blob = await resp.blob();
        return new Promise((resolve) => {
            const reader = new FileReader();
            reader.onload = () => resolve(reader.result);
            reader.readAsDataURL(blob);
        });
    }

    async function useUnjPresetLogo() {
        try {
            currentLogoDataUrl = await urlToDataUrl(UNJ_LOGO_URL);
            scheduleQrRender();
            showQrToast('Logo UNJ dipasang di tengah QR Code.');
        } catch {
            currentLogoDataUrl = UNJ_LOGO_URL;
            scheduleQrRender();
        }
    }

    function handleQrLogoUpload(file) {
        if (!file) return;
        const reader = new FileReader();
        reader.onload = (e) => {
            currentLogoDataUrl = e.target.result;
            scheduleQrRender();
            showQrToast(`Gambar "${file.name}" dipasang di tengah QR Code.`);
        };
        reader.readAsDataURL(file);
        document.getElementById('qrLogoFileInput').value = '';
    }

    function clearQrLogo() {
        currentLogoDataUrl = null;
        scheduleQrRender();
    }

    async function applyQrTemplate(key, btnEl) {
        const tpl = QR_TEMPLATES[key];
        if (!tpl) return;
        document.querySelectorAll('.tpl-chip').forEach(b => b.classList.remove('active'));
        if (btnEl) btnEl.classList.add('active');

        document.getElementById('qrDotStyle').value = tpl.dotStyle;
        document.getElementById('qrCornerStyle').value = tpl.cornerStyle;
        document.getElementById('qrCornerDotStyle').value = tpl.cornerDotStyle;
        document.getElementById('qrColorPrimary').value = tpl.primary;
        document.getElementById('qrColorSecondary').value = tpl.secondary;
        document.getElementById('qrColorBg').value = tpl.bg;
        document.getElementById('qrUseGradient').checked = tpl.useGradient;
        document.getElementById('qrShowFrame').checked = tpl.showFrame;
        document.getElementById('qrFrameAccent').value = tpl.frameAccent;
        document.getElementById('qrFrameTitle').value = tpl.title;
        document.getElementById('qrFrameSubtitle').value = tpl.subtitle;

        if (tpl.logoMode === 'unj') {
            await useUnjPresetLogo();
        } else if (tpl.logoMode === 'none') {
            currentLogoDataUrl = null;
            scheduleQrRender();
        } else {
            scheduleQrRender();
        }
    }

    function getQrStylingConfig(sizePx = 600) {
        const primary = document.getElementById('qrColorPrimary').value;
        const secondary = document.getElementById('qrColorSecondary').value;
        const useGrad = document.getElementById('qrUseGradient').checked;
        const bg = document.getElementById('qrColorBg').value;
        const logoSize = Number(document.getElementById('qrLogoSize').value || 26) / 100;
        const logoMargin = Number(document.getElementById('qrLogoMargin').value || 6);

        const colorOpt = useGrad
            ? {
                gradient: {
                    type: 'linear',
                    rotation: Math.PI / 4,
                    colorStops: [
                        { offset: 0, color: primary },
                        { offset: 1, color: secondary },
                    ],
                },
            }
            : { color: primary };

        return {
            width: sizePx,
            height: sizePx,
            type: 'canvas',
            data: buildQrPayloadString(),
            margin: Math.round(sizePx * 0.03),
            qrOptions: {
                errorCorrectionLevel: currentLogoDataUrl ? 'H' : 'M',
            },
            image: currentLogoDataUrl || undefined,
            imageOptions: {
                crossOrigin: 'anonymous',
                margin: logoMargin,
                imageSize: logoSize,
                hideBackgroundDots: true,
            },
            dotsOptions: {
                type: document.getElementById('qrDotStyle').value,
                ...colorOpt,
            },
            cornersSquareOptions: {
                type: document.getElementById('qrCornerStyle').value,
                color: primary,
            },
            cornersDotOptions: {
                type: document.getElementById('qrCornerDotStyle').value,
                color: useGrad ? secondary : primary,
            },
            backgroundOptions: {
                color: bg,
            },
        };
    }

    function scheduleQrRender() {
        clearTimeout(qrRenderTimer);
        qrRenderTimer = setTimeout(() => renderPosterToCanvas(document.getElementById('qrPosterCanvas'), 720), 80);
    }

    function roundRectPath(c, x, y, w, h, r) {
        c.beginPath();
        c.moveTo(x + r, y);
        c.lineTo(x + w - r, y);
        c.quadraticCurveTo(x + w, y, x + w, y + r);
        c.lineTo(x + w, y + h - r);
        c.quadraticCurveTo(x + w, y + h, x + w - r, y + h);
        c.lineTo(x + r, y + h);
        c.quadraticCurveTo(x, y + h, x, y + h - r);
        c.lineTo(x, y + r);
        c.quadraticCurveTo(x, y, x + r, y);
        c.closePath();
    }

    async function renderPosterToCanvas(targetCanvas, qrSizePx = 720) {
        if (typeof QRCodeStyling === 'undefined' || !targetCanvas) return;

        const showFrame = document.getElementById('qrShowFrame').checked;
        document.getElementById('qrFrameInputs')?.classList.toggle('opacity-50', !showFrame);

        const cfg = getQrStylingConfig(qrSizePx);
        qrStyledInstance = new QRCodeStyling(cfg);
        const rawBlob = await qrStyledInstance.getRawData('png');
        const qrUrl = URL.createObjectURL(rawBlob);

        try {
            const qrImg = await new Promise((res, rej) => {
                const im = new Image();
                im.onload = () => res(im);
                im.onerror = rej;
                im.src = qrUrl;
            });

            const c = targetCanvas.getContext('2d');
            if (!showFrame) {
                targetCanvas.width = qrSizePx;
                targetCanvas.height = qrSizePx;
                c.drawImage(qrImg, 0, 0, qrSizePx, qrSizePx);
                return;
            }

            const accent = document.getElementById('qrFrameAccent').value;
            const title = (document.getElementById('qrFrameTitle').value || '').trim();
            const subtitle = (document.getElementById('qrFrameSubtitle').value || '').trim();
            const pad = Math.round(qrSizePx * 0.09);
            const topH = title ? Math.round(qrSizePx * 0.14) : Math.round(qrSizePx * 0.04);
            const botH = subtitle ? Math.round(qrSizePx * 0.17) : Math.round(qrSizePx * 0.05);

            const totalW = qrSizePx + pad * 2;
            const totalH = qrSizePx + topH + botH + pad;
            targetCanvas.width = totalW;
            targetCanvas.height = totalH;

            // Card background
            c.fillStyle = '#FFFFFF';
            roundRectPath(c, 0, 0, totalW, totalH, Math.round(qrSizePx * 0.05));
            c.fill();

            // Outer frame accent border
            c.strokeStyle = accent;
            c.lineWidth = Math.max(4, Math.round(qrSizePx * 0.012));
            const inset = c.lineWidth / 2;
            roundRectPath(c, inset, inset, totalW - inset * 2, totalH - inset * 2, Math.round(qrSizePx * 0.05));
            c.stroke();

            // Top accent header bar
            if (title) {
                const headerH = Math.round(qrSizePx * 0.11);
                c.save();
                roundRectPath(c, inset, inset, totalW - inset * 2, headerH, Math.round(qrSizePx * 0.045));
                c.fillStyle = accent;
                c.fill();
                c.fillRect(inset, headerH * 0.5, totalW - inset * 2, headerH * 0.5 + inset);

                c.fillStyle = '#FFFFFF';
                c.textAlign = 'center';
                c.textBaseline = 'middle';
                c.font = `800 ${Math.round(qrSizePx * 0.042)}px Inter, sans-serif`;
                c.fillText(title, totalW / 2, headerH / 2 + inset * 0.5, totalW - pad * 1.4);
                c.restore();
            }

            // QR Image
            c.drawImage(qrImg, pad, topH + Math.round(pad * 0.35), qrSizePx, qrSizePx);

            // Bottom badge pill
            if (subtitle) {
                const pillW = Math.min(totalW - pad * 1.4, Math.max(qrSizePx * 0.72, subtitle.length * qrSizePx * 0.029));
                const pillH = Math.round(qrSizePx * 0.095);
                const pillX = (totalW - pillW) / 2;
                const pillY = topH + Math.round(pad * 0.35) + qrSizePx + Math.round(qrSizePx * 0.035);

                roundRectPath(c, pillX, pillY, pillW, pillH, pillH / 2);
                c.fillStyle = accent;
                c.fill();

                c.fillStyle = '#FFFFFF';
                c.textAlign = 'center';
                c.textBaseline = 'middle';
                c.font = `800 ${Math.round(qrSizePx * 0.04)}px Inter, sans-serif`;
                c.fillText(subtitle, totalW / 2, pillY + pillH / 2 + 1, pillW - 24);
            }
        } finally {
            URL.revokeObjectURL(qrUrl);
        }
    }

    async function downloadQrPng() {
        const scale = parseInt(document.getElementById('qrExportScale').value || '1600', 10);
        const off = document.createElement('canvas');
        await renderPosterToCanvas(off, scale);
        const a = document.createElement('a');
        a.download = `QR_FEB_UNJ_${Date.now()}.png`;
        a.href = off.toDataURL('image/png');
        a.click();
        showQrToast(`QR Code PNG (${scale}px) berhasil diunduh.`);
    }

    async function downloadQrSvg() {
        const svgInstance = new QRCodeStyling({ ...getQrStylingConfig(800), type: 'svg' });
        await svgInstance.download({ name: `QR_FEB_UNJ_${Date.now()}`, extension: 'svg' });
        showQrToast('QR Code SVG vektor berhasil diunduh.');
    }

    async function downloadQrPdf() {
        if (typeof window.jspdf === 'undefined') {
            showQrToast('Library PDF belum siap, muat ulang halaman.');
            return;
        }
        const layout = document.getElementById('qrPdfLayout').value;
        const off = document.createElement('canvas');
        await renderPosterToCanvas(off, 1200);
        const imgData = off.toDataURL('image/png');
        const ratio = off.height / off.width;

        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF({ orientation: 'portrait', unit: 'mm', format: 'a4' });
        const pageW = 210, pageH = 297;

        if (layout === 'single') {
            const w = 130;
            const h = w * ratio;
            pdf.addImage(imgData, 'PNG', (pageW - w) / 2, (pageH - h) / 2, w, h);
        } else {
            const cols = layout === 'grid6' ? 2 : 3;
            const rows = layout === 'grid6' ? 3 : 4;
            const margin = 12;
            const gap = 8;
            const cellW = (pageW - margin * 2 - gap * (cols - 1)) / cols;
            const cellH = (pageH - margin * 2 - gap * (rows - 1)) / rows;

            for (let r = 0; r < rows; r++) {
                for (let col = 0; col < cols; col++) {
                    const cx = margin + col * (cellW + gap);
                    const cy = margin + r * (cellH + gap);
                    let drawW = cellW - 4;
                    let drawH = drawW * ratio;
                    if (drawH > cellH - 4) {
                        drawH = cellH - 4;
                        drawW = drawH / ratio;
                    }
                    pdf.setDrawColor(210, 215, 224);
                    pdf.setLineWidth(0.2);
                    pdf.rect(cx, cy, cellW, cellH);
                    pdf.addImage(imgData, 'PNG', cx + (cellW - drawW) / 2, cy + (cellH - drawH) / 2, drawW, drawH);
                }
            }
        }

        pdf.save(`QR_Cetak_FEB_UNJ_${layout}.pdf`);
        showQrToast('PDF QR siap cetak berhasil diunduh.');
    }

    // ── Barcode 1D ──
    function renderBarcode() {
        if (typeof JsBarcode === 'undefined') return;
        const fmt = document.getElementById('bcFormat').value;
        const val = (document.getElementById('bcValue').value || '').trim() || 'FEB-UNJ';
        const top = (document.getElementById('bcTopLabel').value || '').trim();
        const errEl = document.getElementById('bcErrorMsg');
        const topEl = document.getElementById('bcTopPreview');
        topEl.textContent = top;
        topEl.classList.toggle('hidden', !top);

        try {
            JsBarcode('#barcodeSvg', val, {
                format: fmt,
                width: Number(document.getElementById('bcWidth').value || 2),
                height: Number(document.getElementById('bcHeight').value || 90),
                displayValue: document.getElementById('bcDisplayText').checked,
                lineColor: document.getElementById('bcLineColor').value || '#0F172A',
                background: '#FFFFFF',
                margin: 10,
                fontOptions: 'bold',
            });
            errEl.classList.add('hidden');
        } catch (err) {
            errEl.textContent = `Format ${fmt} tidak cocok dengan isi kode saat ini (mis. EAN-13 wajib 12-13 digit angka).`;
            errEl.classList.remove('hidden');
        }
    }

    function downloadBarcodeSvg() {
        const svg = document.getElementById('barcodeSvg');
        const serializer = new XMLSerializer();
        const blob = new Blob([serializer.serializeToString(svg)], { type: 'image/svg+xml;charset=utf-8' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.download = `Barcode_${Date.now()}.svg`;
        a.href = url;
        a.click();
        setTimeout(() => URL.revokeObjectURL(url), 1000);
        showQrToast('Barcode SVG berhasil diunduh.');
    }

    function downloadBarcodePng() {
        const svg = document.getElementById('barcodeSvg');
        const svgData = new XMLSerializer().serializeToString(svg);
        const svgBlob = new Blob([svgData], { type: 'image/svg+xml;charset=utf-8' });
        const url = URL.createObjectURL(svgBlob);
        const img = new Image();
        img.onload = () => {
            const top = (document.getElementById('bcTopLabel').value || '').trim();
            const scale = 2;
            const pad = 28;
            const topOffset = top ? 36 : 0;
            const c = document.createElement('canvas');
            c.width = (img.width + pad * 2) * scale;
            c.height = (img.height + pad * 2 + topOffset) * scale;
            const ctx = c.getContext('2d');
            ctx.scale(scale, scale);
            ctx.fillStyle = '#FFFFFF';
            ctx.fillRect(0, 0, c.width / scale, c.height / scale);
            if (top) {
                ctx.fillStyle = '#1E293B';
                ctx.font = 'bold 14px Inter, sans-serif';
                ctx.textAlign = 'center';
                ctx.fillText(top.toUpperCase(), (c.width / scale) / 2, pad + 14);
            }
            ctx.drawImage(img, pad, pad + topOffset);
            const a = document.createElement('a');
            a.download = `Barcode_${Date.now()}.png`;
            a.href = c.toDataURL('image/png');
            a.click();
            URL.revokeObjectURL(url);
            showQrToast('Barcode PNG berhasil diunduh.');
        };
        img.src = url;
    }

    document.addEventListener('DOMContentLoaded', async () => {
        switchMainStudioTab(activeMainTab);
        setQrPayloadType('url');
        await useUnjPresetLogo();
    });
</script>
@endpush
