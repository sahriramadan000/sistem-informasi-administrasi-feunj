@extends('layouts.app')

@section('title', 'Pengaturan Google Drive')

@push('styles')
    <style>
        .mac-card {
            background: #ffffff;
            border-radius: 1.5rem;
            border: 1px solid rgba(226, 232, 240, 0.85);
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.03), 0 2px 6px -1px rgba(0, 0, 0, 0.02);
        }
    </style>
@endpush

@section('content')
@php
    $quotaLimit = (int) ($driveInfo['storageQuota']['limit'] ?? 0);
    $quotaUsage = (int) ($driveInfo['storageQuota']['usage'] ?? 0);
    $quotaPercent = $quotaLimit > 0 ? min(100, round(($quotaUsage / $quotaLimit) * 100, 2)) : 0;
    $formatBytes = function (int $bytes): string {
        if ($bytes <= 0) return '0 B';
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $power = min(count($units) - 1, (int) floor(log($bytes, 1024)));
        return round($bytes / pow(1024, $power), 2) . ' ' . $units[$power];
    };
@endphp

<div class="space-y-6 pb-12">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-gray-200/80">
        <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white shadow-xs flex-shrink-0">
                <i data-lucide="hard-drive" class="w-5 h-5"></i>
            </div>
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">Integrasi Google Drive</h1>
                <p class="text-xs sm:text-sm text-gray-500">Pengarsipan satu pintu otomatis berdasarkan Tahun &amp; Tipe Surat</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('admin.dashboard') }}"
                class="inline-flex items-center gap-2 px-4 py-2 text-xs sm:text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-200 rounded-full shadow-2xs transition-all">
                <i data-lucide="arrow-left" class="w-4 h-4 text-gray-500"></i>
                <span>Dashboard Admin</span>
            </a>
        </div>
    </div>

    {{-- Alerts --}}
    @if (session('success'))
        <div class="rounded-2xl bg-emerald-50 border border-emerald-200/80 px-5 py-3.5 flex items-start gap-3 text-emerald-800 text-sm">
            <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5"></i>
            <div class="font-medium">{{ session('success') }}</div>
        </div>
    @endif

    @if (session('error'))
        <div class="rounded-2xl bg-rose-50 border border-rose-200/80 px-5 py-3.5 flex items-start gap-3 text-rose-800 text-sm">
            <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 flex-shrink-0 mt-0.5"></i>
            <div class="space-y-1">
                <p class="font-semibold">{{ session('error') }}</p>
                <a href="{{ route('admin.error-logs.index') }}" class="inline-flex items-center gap-1 text-xs font-bold text-rose-700 underline">
                    Lihat detail di Error Logs
                    <i data-lucide="arrow-right" class="w-3 h-3"></i>
                </a>
            </div>
        </div>
    @endif

    @if ($lastError && !session('error'))
        <div class="rounded-2xl bg-amber-50 border border-amber-200/80 px-5 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-amber-900 text-sm">
            <div class="flex items-start gap-3">
                <i data-lucide="alert-triangle" class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5"></i>
                <div>
                    <p class="font-bold">Terdeteksi Kendala Terakhir pada Google Drive</p>
                    <p class="text-xs text-amber-800 mt-0.5">{{ $lastError }}</p>
                </div>
            </div>
            @if ($isConfigured)
                <a href="{{ route('admin.gdrive.connect') }}"
                    class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-white bg-amber-600 hover:bg-amber-700 rounded-full transition-colors flex-shrink-0">
                    <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i>
                    <span>Hubungkan Ulang</span>
                </a>
            @endif
        </div>
    @endif

    {{-- Main Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left Column: Connection & Storage --}}
        <div class="lg:col-span-1 space-y-6">
            {{-- Connection Card --}}
            <div class="mac-card p-6 space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl {{ $isConnected ? 'bg-emerald-50 text-emerald-600 border-emerald-200/70' : 'bg-gray-100 text-gray-500 border-gray-200/70' }} border flex items-center justify-center">
                            <i data-lucide="{{ $isConnected ? 'cloud-check' : 'cloud-off' }}" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <h2 class="text-sm font-bold text-gray-900">Status Koneksi</h2>
                            <p class="text-[11px] text-gray-500">OAuth 2.0 Akun Induk</p>
                        </div>
                    </div>

                    @if ($isConnected)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/70">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Terhubung
                        </span>
                    @elseif ($isConfigured)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200/70">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                            Belum Terhubung
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200/70">
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                            Belum Dikonfigurasi
                        </span>
                    @endif
                </div>

                @if (!$isConfigured)
                    <div class="p-4 rounded-2xl bg-rose-50/70 border border-rose-200/70 text-xs text-rose-800 space-y-2 leading-relaxed">
                        <p class="font-bold">Kredensial `.env` Belum Diisi</p>
                        <p>Tambahkan variabel berikut pada file <code class="font-mono bg-white/80 px-1.5 py-0.5 rounded">.env</code> server Anda:</p>
                        <pre class="bg-gray-900 text-emerald-300 rounded-xl p-3 text-[11px] font-mono overflow-x-auto">GOOGLE_DRIVE_CLIENT_ID=...
GOOGLE_DRIVE_CLIENT_SECRET=...
GOOGLE_DRIVE_REDIRECT_URI={{ $redirectUri }}</pre>
                    </div>
                @else
                    <div class="space-y-3">
                        <div class="p-3.5 rounded-2xl bg-gray-50/80 border border-gray-100">
                            <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Akun Google Terhubung</p>
                            <p class="text-sm font-bold text-gray-900 mt-1 break-all">
                                {{ $connectedEmail ?: 'Belum ada akun terhubung' }}
                            </p>
                        </div>

                        @if ($driveInfo && $quotaLimit > 0)
                            <div class="p-3.5 rounded-2xl bg-gray-50/80 border border-gray-100 space-y-2">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-semibold text-gray-500">Kapasitas Penyimpanan</span>
                                    <span class="font-bold text-gray-900">{{ $quotaPercent }}%</span>
                                </div>
                                <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full" style="width: {{ $quotaPercent }}%"></div>
                                </div>
                                <p class="text-[11px] text-gray-500">
                                    Terpakai <strong>{{ $formatBytes($quotaUsage) }}</strong> dari <strong>{{ $formatBytes($quotaLimit) }}</strong>
                                </p>
                            </div>
                        @endif

                        <div class="p-3.5 rounded-2xl bg-gray-50/80 border border-gray-100">
                            <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Redirect URI Terdaftar</p>
                            <code class="text-[11px] font-mono text-gray-600 break-all mt-1 block">{{ $redirectUri }}</code>
                        </div>
                    </div>

                    <div class="flex flex-col gap-2.5 pt-2">
                        <a href="{{ route('admin.gdrive.connect') }}"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 text-xs sm:text-sm font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 rounded-full shadow-xs transition-all">
                            <i data-lucide="{{ $isConnected ? 'refresh-cw' : 'link' }}" class="w-4 h-4"></i>
                            <span>{{ $isConnected ? 'Hubungkan Ulang Akun Google' : 'Hubungkan Akun Google Drive' }}</span>
                        </a>

                        @if ($isConnected)
                            <div class="grid grid-cols-2 gap-2">
                                <form action="{{ route('admin.gdrive.test') }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-200 rounded-full transition-colors cursor-pointer">
                                        <i data-lucide="activity" class="w-3.5 h-3.5 text-emerald-600"></i>
                                        <span>Test Koneksi</span>
                                    </button>
                                </form>

                                <form action="{{ route('admin.gdrive.disconnect') }}" method="POST"
                                      onsubmit="return confirm('Putuskan koneksi Google Drive? Staf sementara hanya bisa memakai link manual hingga dihubungkan ulang.')">
                                    @csrf
                                    <button type="submit"
                                        class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-semibold text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-200/70 rounded-full transition-colors cursor-pointer">
                                        <i data-lucide="unlink" class="w-3.5 h-3.5"></i>
                                        <span>Putuskan</span>
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        {{-- Right Column: Folder & Upload Behavior --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Root Folder & Sharing Settings --}}
            <div class="mac-card p-6 sm:p-7 space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-orange-50 border border-orange-200/70 flex items-center justify-center text-[#FF4D00]">
                            <i data-lucide="folder-tree" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <h2 class="text-sm font-bold text-gray-900">Pengaturan Folder Induk &amp; Akses</h2>
                            <p class="text-[11px] text-gray-500">Semua berkas yang diunggah akan otomatis tersusun di dalam folder ini</p>
                        </div>
                    </div>
                    <span class="text-[11px] font-semibold text-gray-400">Cache folder: {{ $cachedFoldersCount }}</span>
                </div>

                <form action="{{ route('admin.gdrive.settings') }}" method="POST" class="space-y-5">
                    @csrf
                    <div>
                        <label for="gdrive_root_folder_id" class="block text-xs font-bold text-gray-700 mb-1.5">
                            Root Folder ID atau URL Folder Google Drive
                        </label>
                        <input type="text" id="gdrive_root_folder_id" name="gdrive_root_folder_id"
                            value="{{ old('gdrive_root_folder_id', $rootFolderId) }}"
                            placeholder="Contoh: 1AbCdEfGhIjKlMnOpQrStUvWxYz atau https://drive.google.com/drive/folders/..."
                            class="w-full px-4 py-2.5 text-sm bg-gray-50/70 border border-gray-200 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 font-mono">
                        <p class="text-[11px] text-gray-500 mt-1.5">
                            Anda bisa langsung menempelkan URL folder Google Drive atau hanya ID foldernya saja.
                        </p>

                        @if ($rootFolderInfo)
                            <div class="mt-2.5 inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-emerald-50 border border-emerald-200/70 text-xs text-emerald-800">
                                <i data-lucide="folder-check" class="w-4 h-4 text-emerald-600"></i>
                                <span>Folder aktif: <strong>{{ $rootFolderInfo['name'] }}</strong></span>
                                @if (!empty($rootFolderInfo['webViewLink']))
                                    <a href="{{ $rootFolderInfo['webViewLink'] }}" target="_blank" rel="noopener noreferrer" class="underline font-semibold text-emerald-700">Buka</a>
                                @endif
                            </div>
                        @endif
                    </div>

                    <label class="flex items-start gap-3 p-4 rounded-2xl bg-gray-50/80 border border-gray-200/70 cursor-pointer hover:bg-gray-50 transition-colors">
                        <input type="checkbox" name="gdrive_auto_share" value="1" {{ $autoShare ? 'checked' : '' }}
                            class="mt-0.5 w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500/30">
                        <div>
                            <p class="text-xs sm:text-sm font-bold text-gray-900">Otomatis Izinkan Pratinjau (Anyone with link can view)</p>
                            <p class="text-xs text-gray-500 mt-0.5">
                                Disarankan aktif agar staf dan pimpinan dapat langsung membuka/pratinjau dokumen dari Portal Arsip Digital tanpa harus login Google terlebih dahulu.
                            </p>
                        </div>
                    </label>

                    <div class="flex justify-end">
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 text-xs sm:text-sm font-bold text-white bg-gray-900 hover:bg-gray-800 rounded-full shadow-xs transition-all cursor-pointer">
                            <i data-lucide="save" class="w-4 h-4"></i>
                            <span>Simpan &amp; Verifikasi Pengaturan</span>
                        </button>
                    </div>
                </form>

                {{-- Quick Auto-Create Root Folder --}}
                @if ($isConnected)
                    <div class="pt-5 border-t border-gray-100">
                        <form action="{{ route('admin.gdrive.create-root-folder') }}" method="POST"
                              class="flex flex-col sm:flex-row sm:items-center gap-3 p-4 rounded-2xl bg-blue-50/50 border border-blue-200/70">
                            @csrf
                            <div class="flex-1">
                                <p class="text-xs font-bold text-blue-900">Belum punya folder induk di Google Drive?</p>
                                <p class="text-[11px] text-blue-700 mt-0.5">Buat otomatis folder baru di Google Drive 5 TB Anda dan langsung tetapkan sebagai Root Folder.</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="text" name="folder_name" value="Arsip Surat FEB UNJ"
                                    class="px-3 py-2 text-xs bg-white border border-blue-200 rounded-xl focus:outline-none focus:border-blue-500 w-44">
                                <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-full transition-colors whitespace-nowrap cursor-pointer">
                                    <i data-lucide="folder-plus" class="w-3.5 h-3.5"></i>
                                    <span>Buat Folder</span>
                                </button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>

            {{-- Structure Preview --}}
            <div class="mac-card p-6 space-y-3">
                <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                    <i data-lucide="git-branch" class="w-4 h-4 text-[#FF4D00]"></i>
                    Pola Struktur Folder Otomatis di Google Drive
                </h3>
                <div class="bg-gray-950 text-gray-200 rounded-2xl p-4 font-mono text-xs leading-relaxed">
                    <div>📁 {{ $rootFolderInfo['name'] ?? 'Arsip Surat FEB UNJ (Root)' }}</div>
                    <div class="pl-4">└── 📁 {{ date('Y') }} <span class="text-gray-500">(Tahun Surat)</span></div>
                    <div class="pl-8">├── 📁 SK - Surat Keputusan</div>
                    <div class="pl-12 text-emerald-400">└── 📄 {{ date('Y-m-d') }}_042-UN39-SK_Penetapan-Panitia.pdf</div>
                    <div class="pl-8">└── 📁 ST - Surat Tugas</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
@endpush
