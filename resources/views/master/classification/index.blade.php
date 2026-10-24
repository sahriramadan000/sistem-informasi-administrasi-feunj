@extends('layouts.app')

@section('title', 'Master Klasifikasi Surat - FEB UNJ')
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

        /* Segmented Pill Tabs */
        .mac-tab-active {
            background: #ffffff;
            color: #111827;
            font-weight: 700;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06), 0 1px 2px rgba(0, 0, 0, 0.04);
            border: 1px solid rgba(226, 232, 240, 0.9);
        }
        .mac-tab-inactive {
            color: #64748b;
            font-weight: 500;
            background: transparent;
            border: 1px solid transparent;
        }
        .mac-tab-inactive:hover {
            color: #0f172a;
            background: rgba(255, 255, 255, 0.6);
        }

        /* Floating Toast Notification */
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

        /* Modal Animation */
        .mac-modal-enter {
            animation: macModalIn 0.2s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        @keyframes macModalIn {
            from {
                opacity: 0;
                transform: scale(0.97) translateY(8px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }
    </style>
@endpush

@section('content')
    <div class="w-full space-y-6 pb-12 relative">
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

        {{-- Page Header (Apple macOS Toolbar Style) --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-gray-200/80">
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-[#FF4D00] to-amber-500 flex items-center justify-center text-white shadow-xs flex-shrink-0">
                    <i data-lucide="tags" class="w-5 h-5"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">Master Klasifikasi Surat</h1>
                    <p class="text-xs sm:text-sm text-gray-500">Kelola dan organisasikan kode klasifikasi arsip surat FEB UNJ</p>
                </div>
            </div>

            @if (auth()->user()->isAdmin())
                <div class="flex flex-wrap items-center gap-2.5">
                    {{-- Download Template Button --}}
                    <button type="button" onclick="openDownloadModal()"
                        class="inline-flex items-center gap-2 px-3.5 py-2 text-xs sm:text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-200 rounded-full shadow-2xs hover:shadow-xs transition-all active:scale-[0.98] cursor-pointer"
                        title="Unduh Template Excel">
                        <i data-lucide="download" class="w-4 h-4 text-gray-500"></i>
                        <span>Template</span>
                    </button>

                    {{-- Import Excel Button --}}
                    <button type="button" onclick="openImportModal()"
                        class="inline-flex items-center gap-2 px-3.5 py-2 text-xs sm:text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-200 rounded-full shadow-2xs hover:shadow-xs transition-all active:scale-[0.98] cursor-pointer"
                        title="Import Klasifikasi dari Excel">
                        <i data-lucide="upload" class="w-4 h-4 text-gray-500"></i>
                        <span>Import Excel</span>
                    </button>

                    {{-- Add Button --}}
                    <a href="{{ route('master.classification-letters.create') }}"
                        class="inline-flex items-center gap-2 px-4.5 py-2 text-xs sm:text-sm font-bold text-white bg-gradient-to-r from-[#FF4D00] to-amber-500 hover:from-[#e04400] hover:to-[#d97706] rounded-full shadow-xs hover:shadow-md transition-all active:scale-[0.98]">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        <span>Tambah Klasifikasi</span>
                    </a>
                </div>
            @endif
        </div>

        {{-- Apple Segmented Pill Tabs (Status Filter) --}}
        <div class="bg-gray-100/70 p-1.5 rounded-2xl border border-gray-200/60 backdrop-blur-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <nav class="flex items-center gap-1.5 overflow-x-auto scrollbar-none" aria-label="Tabs Status Klasifikasi">
                <a href="{{ route('master.classification-letters.index', array_merge(request()->except(['status', 'page']))) }}"
                   class="whitespace-nowrap px-4 py-2 rounded-xl text-xs sm:text-sm transition-all flex items-center gap-2 {{ !request('status') ? 'mac-tab-active text-[#FF4D00]' : 'mac-tab-inactive' }}">
                    <i data-lucide="layers" class="w-4 h-4"></i>
                    <span>Semua Status</span>
                </a>
                <a href="{{ route('master.classification-letters.index', array_merge(request()->except(['status', 'page']), ['status' => 'active'])) }}"
                   class="whitespace-nowrap px-4 py-2 rounded-xl text-xs sm:text-sm transition-all flex items-center gap-2 {{ request('status') === 'active' ? 'mac-tab-active text-emerald-600' : 'mac-tab-inactive' }}">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>Aktif</span>
                </a>
                <a href="{{ route('master.classification-letters.index', array_merge(request()->except(['status', 'page']), ['status' => 'inactive'])) }}"
                   class="whitespace-nowrap px-4 py-2 rounded-xl text-xs sm:text-sm transition-all flex items-center gap-2 {{ request('status') === 'inactive' ? 'mac-tab-active text-gray-700' : 'mac-tab-inactive' }}">
                    <span class="w-2 h-2 rounded-full bg-gray-400"></span>
                    <span>Nonaktif</span>
                </a>
            </nav>

            {{-- Total Count Capsule --}}
            <div class="hidden sm:flex items-center gap-2 pr-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-white border border-gray-200 text-gray-700 shadow-2xs">
                    <i data-lucide="hash" class="w-3.5 h-3.5 text-orange-500"></i>
                    Total: {{ number_format($classifications->total(), 0, ',', '.') }} Klasifikasi
                </span>
            </div>
        </div>

        {{-- Apple Spotlight Search & Quick Filters Box --}}
        <div class="mac-card p-4 sm:p-5 relative overflow-hidden">
            <form method="GET" action="{{ route('master.classification-letters.index') }}" class="space-y-3.5" id="searchForm">
                @if (request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                @if (request('per_page'))
                    <input type="hidden" name="per_page" value="{{ request('per_page') }}">
                @endif

                {{-- Spotlight Input Bar --}}
                <div class="flex flex-col sm:flex-row items-stretch gap-2.5">
                    <div class="relative flex-1 group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-[#FF4D00] transition-colors">
                            <i data-lucide="search" class="w-5 h-5"></i>
                        </div>
                        <input type="text" name="search" id="searchInput" value="{{ request('search') }}"
                            class="w-full h-12 pl-12 pr-24 text-xs sm:text-sm bg-gray-50/80 hover:bg-gray-50 focus:bg-white border border-gray-200/90 focus:border-[#FF4D00] rounded-2xl focus:outline-none focus:ring-4 focus:ring-orange-500/10 shadow-2xs transition-all font-medium placeholder:text-gray-400 placeholder:font-normal"
                            placeholder="Cari kode klasifikasi (contoh: KM, PP, KP), nama, atau deskripsi..."
                            autocomplete="off">
                        
                        {{-- Clear Button & Shortcut Badge --}}
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center gap-1.5">
                            @if (request('search'))
                                <a href="{{ route('master.classification-letters.index', array_merge(request()->except(['search', 'page']))) }}"
                                    class="w-6 h-6 rounded-full bg-gray-200/80 hover:bg-gray-300 text-gray-600 flex items-center justify-center transition-all cursor-pointer"
                                    title="Hapus pencarian">
                                    <i data-lucide="x" class="w-3.5 h-3.5"></i>
                                </a>
                            @endif
                            <kbd class="hidden md:inline-flex items-center px-2 py-0.5 text-[10px] font-mono font-medium text-gray-400 bg-white border border-gray-200 rounded-md shadow-2xs" title="Tekan / pada keyboard untuk mencari">
                                /
                            </kbd>
                        </div>
                    </div>

                    <button type="submit"
                        class="h-12 px-6 inline-flex items-center justify-center gap-2 bg-gradient-to-r from-[#FF4D00] to-amber-500 hover:from-[#e04400] hover:to-[#d97706] text-white text-xs sm:text-sm font-bold rounded-2xl shadow-xs hover:shadow-md transition-all active:scale-[0.98] cursor-pointer flex-shrink-0">
                        <i data-lucide="search" class="w-4 h-4"></i>
                        <span>Cari Klasifikasi</span>
                    </button>
                </div>

                {{-- Quick Filter Chips --}}
                <div class="flex flex-wrap items-center justify-between gap-3 pt-0.5 text-xs">
                    <div class="flex flex-wrap items-center gap-1.5">
                        <span class="text-gray-400 font-medium flex items-center gap-1">
                            <i data-lucide="sparkles" class="w-3.5 h-3.5 text-amber-500"></i>
                            Pintasan Kode:
                        </span>
                        @php
                            $quickCodes = [
                                ['code' => 'KM', 'label' => 'Kemahasiswaan'],
                                ['code' => 'PP', 'label' => 'Pendidikan'],
                                ['code' => 'KP', 'label' => 'Kepegawaian'],
                                ['code' => 'KU', 'label' => 'Keuangan'],
                                ['code' => 'HK', 'label' => 'Hukum / SK'],
                                ['code' => 'HM', 'label' => 'Humas'],
                            ];
                        @endphp
                        @foreach ($quickCodes as $quick)
                            <a href="{{ route('master.classification-letters.index', array_merge(request()->except(['search', 'page']), ['search' => $quick['code']])) }}"
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-xs font-semibold {{ request('search') == $quick['code'] ? 'bg-orange-100 text-[#FF4D00] border border-orange-300 shadow-2xs' : 'bg-gray-100/80 hover:bg-orange-50 text-gray-600 hover:text-[#FF4D00] border border-gray-200/60 hover:border-orange-200' }} transition-all active:scale-[0.96]"
                                title="Cari kode {{ $quick['code'] }}">
                                <span class="font-mono font-bold">{{ $quick['code'] }}</span>
                                <span class="text-[11px] opacity-75 hidden sm:inline">({{ $quick['label'] }})</span>
                            </a>
                        @endforeach
                    </div>

                    @if (request('search') || request('status'))
                        <a href="{{ route('master.classification-letters.index') }}"
                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold text-gray-500 hover:text-red-600 bg-white border border-gray-200 hover:border-red-200 shadow-2xs transition-all">
                            <i data-lucide="rotate-ccw" class="w-3 h-3"></i>
                            <span>Reset Semua Filter</span>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- MAIN DATA CONTAINER (Table View) --}}
        <div class="mac-card overflow-hidden">
            {{-- Table Control Bar (Per Page & Status Info) --}}
            <div class="px-5 py-3 border-b border-gray-100 bg-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs text-gray-500">
                <div class="flex items-center gap-2">
                    <span>Tampilkan:</span>
                    <form method="GET" action="{{ route('master.classification-letters.index') }}" class="inline-block m-0">
                        @if (request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                        @if (request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
                        <select name="per_page" id="per_page"
                            class="px-2.5 py-1 text-xs border border-gray-200 rounded-lg bg-gray-50/50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-[#FF4D00] transition-colors cursor-pointer"
                            onchange="this.form.submit()">
                            <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ request('per_page', 10) == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page', 10) == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page', 10) == 100 ? 'selected' : '' }}>100</option>
                        </select>
                    </form>
                    <span>data per halaman</span>
                </div>

                <div class="flex items-center gap-2">
                    @if (request('search'))
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-orange-50 text-orange-800 border border-orange-200/80">
                            <i data-lucide="filter" class="w-3 h-3 text-[#FF4D00]"></i>
                            Pencarian: "<strong>{{ request('search') }}</strong>"
                        </span>
                    @endif
                    <span>Halaman {{ $classifications->currentPage() }} dari {{ $classifications->lastPage() }}</span>
                </div>
            </div>

            {{-- Table View --}}
            <div class="overflow-x-auto">
                @if ($classifications->count() > 0)
                    <table class="min-w-full divide-y divide-gray-100 text-left">
                        <thead>
                            <tr class="bg-gray-50/60 border-b border-gray-100">
                                <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider w-12 text-center">No</th>
                                <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider min-w-[170px]">Kode Klasifikasi</th>
                                <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider min-w-[220px]">Nama Klasifikasi</th>
                                <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider min-w-[260px] hidden md:table-cell">Deskripsi</th>
                                <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider min-w-[110px] text-center">Status</th>
                                @if (auth()->user()->isAdmin())
                                    <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider text-center w-36">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach ($classifications as $index => $classification)
                                <tr class="hover:bg-orange-50/30 transition-colors group">
                                    {{-- No --}}
                                    <td class="px-4 py-4 text-center text-xs font-semibold text-gray-400">
                                        {{ ($classifications->currentPage() - 1) * $classifications->perPage() + $index + 1 }}
                                    </td>

                                    {{-- Kode Klasifikasi --}}
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="inline-flex items-center gap-2">
                                            <span class="font-mono text-xs sm:text-[13px] font-bold text-gray-900 bg-gray-100/90 group-hover:bg-orange-100/70 group-hover:text-[#FF4D00] px-2.5 py-1 rounded-lg border border-gray-200/80 group-hover:border-orange-200 transition-colors">
                                                {{ $classification->code }}
                                            </span>
                                            <button type="button" onclick="copyToClipboard('{{ addslashes($classification->code) }}', 'Kode klasifikasi disalin!')"
                                                class="opacity-0 group-hover:opacity-100 p-1 text-gray-400 hover:text-orange-600 transition-all rounded-md hover:bg-orange-50 cursor-pointer flex-shrink-0"
                                                title="Salin Kode">
                                                <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                                            </button>
                                        </div>
                                    </td>

                                    {{-- Nama Klasifikasi --}}
                                    <td class="px-4 py-4">
                                        <p class="text-xs sm:text-sm font-semibold text-gray-900 leading-snug">
                                            {{ $classification->name }}
                                        </p>
                                    </td>

                                    {{-- Deskripsi --}}
                                    <td class="px-4 py-4 hidden md:table-cell">
                                        <p class="text-xs text-gray-500 leading-relaxed break-words max-w-xl">
                                            {{ $classification->description ?: '-' }}
                                        </p>
                                    </td>

                                    {{-- Status --}}
                                    <td class="px-4 py-4 text-center whitespace-nowrap">
                                        @if ($classification->is_active)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/80 shadow-2xs">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200/80">
                                                <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                                Nonaktif
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Aksi --}}
                                    @if (auth()->user()->isAdmin())
                                        <td class="px-4 py-4 text-center whitespace-nowrap">
                                            <div class="flex items-center justify-center gap-2">
                                                {{-- Edit Button --}}
                                                <a href="{{ route('master.classification-letters.edit', $classification) }}"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-orange-50 hover:bg-orange-100 text-[#FF4D00] hover:text-orange-700 font-bold text-xs rounded-full border border-orange-200/80 shadow-2xs transition-all active:scale-[0.96]"
                                                    title="Edit Klasifikasi">
                                                    <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                                                    <span>Edit</span>
                                                </a>

                                                {{-- Deactivate Button --}}
                                                @if ($classification->is_active)
                                                    <form method="POST" action="{{ route('master.classification-letters.destroy', $classification) }}"
                                                        onsubmit="return confirm('Apakah Anda yakin ingin menonaktifkan klasifikasi ini? Data tidak akan dihapus dari database.')"
                                                        class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 hover:text-rose-800 font-bold text-xs rounded-full border border-rose-200/80 shadow-2xs transition-all active:scale-[0.96] cursor-pointer"
                                                            title="Nonaktifkan Klasifikasi">
                                                            <i data-lucide="power" class="w-3.5 h-3.5"></i>
                                                            <span>Nonaktif</span>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{-- Modern Pagination Footer (Compact & Single Info) --}}
                    <div class="border-t border-gray-100">
                        {{ $classifications->appends(request()->query())->links() }}
                    </div>
                @else
                    {{-- Empty State (Apple Style) --}}
                    <div class="text-center py-16 px-4">
                        <div class="w-16 h-16 rounded-3xl bg-orange-50 border border-orange-200/80 flex items-center justify-center mx-auto mb-4 text-[#FF4D00] shadow-2xs">
                            <i data-lucide="tags" class="w-8 h-8"></i>
                        </div>
                        <h3 class="text-base font-bold text-gray-900">Tidak Ada Data Klasifikasi</h3>
                        <p class="text-xs sm:text-sm text-gray-500 mt-1 max-w-sm mx-auto">
                            @if (request('search'))
                                Tidak ditemukan klasifikasi yang cocok dengan kata kunci "{{ request('search') }}".
                            @else
                                Belum ada klasifikasi surat yang terdaftar di dalam sistem.
                            @endif
                        </p>
                        <div class="mt-5 flex items-center justify-center gap-3">
                            @if (request('search'))
                                <a href="{{ route('master.classification-letters.index') }}"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-white hover:bg-gray-50 border border-gray-200 text-gray-700 font-semibold text-xs rounded-full shadow-2xs transition-all cursor-pointer">
                                    <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                                    <span>Reset Pencarian</span>
                                </a>
                            @endif
                            @if (auth()->user()->isAdmin())
                                <a href="{{ route('master.classification-letters.create') }}"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-[#FF4D00] to-amber-500 hover:from-[#e04400] hover:to-[#d97706] text-white font-bold text-xs rounded-full shadow-xs hover:shadow-md transition-all active:scale-[0.98]">
                                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                                    <span>Tambah Klasifikasi Baru</span>
                                </a>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Download Template Modal (Apple macOS Sheet Style) --}}
        <div id="download-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/40 backdrop-blur-xs transition-opacity">
            <div class="mac-modal-enter bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-7 border border-gray-100 shadow-2xl space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0">
                            <i data-lucide="file-spreadsheet" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-900">Petunjuk Template Excel</h3>
                            <p class="text-xs text-gray-500">Format pengisian berkas untuk import klasifikasi surat</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeDownloadModal()" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 flex items-center justify-center transition-colors cursor-pointer">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>

                {{-- Table Format Guide --}}
                <div class="space-y-4 text-xs text-gray-600 max-h-[60vh] overflow-y-auto pr-1">
                    <div class="p-3.5 rounded-2xl bg-emerald-50/70 border border-emerald-200/80">
                        <h4 class="font-bold text-emerald-900 mb-2 flex items-center gap-1.5">
                            <i data-lucide="layout" class="w-4 h-4 text-emerald-600"></i>
                            Struktur Kolom Excel
                        </h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-[11px] border border-emerald-200 rounded-lg overflow-hidden bg-white">
                                <thead class="bg-emerald-100/70 text-emerald-900">
                                    <tr>
                                        <th class="px-2.5 py-1.5 border-b border-emerald-200 text-left font-bold">Kode</th>
                                        <th class="px-2.5 py-1.5 border-b border-emerald-200 text-left font-bold">Nama</th>
                                        <th class="px-2.5 py-1.5 border-b border-emerald-200 text-left font-bold">Deskripsi</th>
                                        <th class="px-2.5 py-1.5 border-b border-emerald-200 text-left font-bold">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="px-2.5 py-1.5 border-b border-gray-100 font-mono font-bold text-gray-900">UM</td>
                                        <td class="px-2.5 py-1.5 border-b border-gray-100">Surat Umum</td>
                                        <td class="px-2.5 py-1.5 border-b border-gray-100 text-gray-500">Surat bersifat umum...</td>
                                        <td class="px-2.5 py-1.5 border-b border-gray-100 font-semibold text-emerald-700">Aktif</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Rules Checklist --}}
                    <div class="p-3.5 rounded-2xl bg-gray-50 border border-gray-200/80 space-y-2">
                        <h4 class="font-bold text-gray-900 flex items-center gap-1.5">
                            <i data-lucide="check-circle-2" class="w-4 h-4 text-orange-500"></i>
                            Ketentuan Pengisian:
                        </h4>
                        <ul class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-[11px] text-gray-600">
                            <li class="flex items-start gap-1.5">
                                <i data-lucide="check" class="w-3.5 h-3.5 text-emerald-600 flex-shrink-0 mt-0.5"></i>
                                <span><strong>Kode:</strong> Wajib diisi, unik, maks 20 karakter</span>
                            </li>
                            <li class="flex items-start gap-1.5">
                                <i data-lucide="check" class="w-3.5 h-3.5 text-emerald-600 flex-shrink-0 mt-0.5"></i>
                                <span><strong>Nama:</strong> Wajib diisi, maks 100 karakter</span>
                            </li>
                            <li class="flex items-start gap-1.5">
                                <i data-lucide="check" class="w-3.5 h-3.5 text-emerald-600 flex-shrink-0 mt-0.5"></i>
                                <span><strong>Deskripsi:</strong> Opsional, boleh dikosongkan</span>
                            </li>
                            <li class="flex items-start gap-1.5">
                                <i data-lucide="check" class="w-3.5 h-3.5 text-emerald-600 flex-shrink-0 mt-0.5"></i>
                                <span><strong>Status:</strong> Isi "Aktif" atau "Nonaktif"</span>
                            </li>
                        </ul>
                    </div>

                    {{-- Warnings --}}
                    <div class="p-3 rounded-2xl bg-amber-50 border border-amber-200/80 text-[11px] text-amber-900 flex items-start gap-2.5">
                        <i data-lucide="alert-circle" class="w-4 h-4 text-amber-600 flex-shrink-0 mt-0.5"></i>
                        <p class="leading-relaxed">
                            Template sudah memuat 3 baris data sampel. Silakan <strong>hapus baris sampel</strong> tersebut sebelum mengunggah. Data dengan kode yang telah terdaftar akan dilewati secara otomatis.
                        </p>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-gray-100">
                    <button type="button" onclick="closeDownloadModal()"
                        class="px-4 py-2 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-full transition-colors cursor-pointer">
                        Tutup
                    </button>
                    <a href="{{ route('master.classification-letters.download-template') }}"
                        class="inline-flex items-center gap-2 px-5 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-full shadow-xs hover:shadow-md transition-all active:scale-[0.98]">
                        <i data-lucide="download" class="w-4 h-4"></i>
                        <span>Unduh Template Excel</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- Import Excel Modal (Apple macOS Sheet Style) --}}
        <div id="import-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/40 backdrop-blur-xs transition-opacity">
            <div class="mac-modal-enter bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 border border-gray-100 shadow-2xl space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-orange-100 text-[#FF4D00] flex items-center justify-center flex-shrink-0">
                            <i data-lucide="upload-cloud" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-900">Import Klasifikasi</h3>
                            <p class="text-xs text-gray-500">Unggah berkas Excel data klasifikasi surat</p>
                        </div>
                    </div>
                    <button type="button" onclick="closeImportModal()" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-500 flex items-center justify-center transition-colors cursor-pointer">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>

                <form action="{{ route('master.classification-letters.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    
                    {{-- File Drop Area --}}
                    <div>
                        <label for="excel_file" class="block text-xs font-bold text-gray-700 mb-2">Pilih File Excel (.xlsx, .xls) <span class="text-red-500">*</span></label>
                        <div class="relative border-2 border-dashed border-gray-300 hover:border-[#FF4D00] rounded-2xl p-6 text-center transition-colors cursor-pointer group bg-gray-50/50 hover:bg-orange-50/20">
                            <input type="file" id="excel_file" name="file" accept=".xlsx,.xls" required
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                onchange="handleFileSelected(this)">
                            <div class="space-y-2">
                                <div class="w-10 h-10 mx-auto rounded-xl bg-orange-50 text-[#FF4D00] flex items-center justify-center group-hover:scale-105 transition-transform">
                                    <i data-lucide="file-up" class="w-5 h-5"></i>
                                </div>
                                <p id="fileNamePreview" class="text-xs font-semibold text-gray-700">Tarik file ke sini atau <span class="text-[#FF4D00]">Pilih File</span></p>
                                <p class="text-[11px] text-gray-400">Maksimum ukuran berkas: 2MB</p>
                            </div>
                        </div>
                    </div>

                    {{-- Tips Box --}}
                    <div class="p-3 rounded-2xl bg-orange-50/70 border border-orange-100 text-[11px] text-orange-950 space-y-1">
                        <p class="font-bold flex items-center gap-1.5 text-orange-900">
                            <i data-lucide="info" class="w-3.5 h-3.5 text-orange-600"></i>
                            Penting Sebelum Import:
                        </p>
                        <ul class="list-disc list-inside space-y-0.5 text-orange-800 pl-1">
                            <li>Pastikan menggunakan format template resmi</li>
                            <li>Kode harus unik dan belum terdaftar</li>
                            <li>Data duplikat akan otomatis dilewati</li>
                        </ul>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-gray-100">
                        <button type="button" onclick="closeImportModal()"
                            class="px-4 py-2 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-full transition-colors cursor-pointer">
                            Batal
                        </button>
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2 text-xs font-bold text-white bg-gradient-to-r from-[#FF4D00] to-amber-500 hover:from-[#e04400] hover:to-[#d97706] rounded-full shadow-xs hover:shadow-md transition-all active:scale-[0.98] cursor-pointer">
                            <i data-lucide="upload" class="w-4 h-4"></i>
                            <span>Mulai Import</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Modal Handlers
        function openDownloadModal() {
            document.getElementById('download-modal').classList.remove('hidden');
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }
        function closeDownloadModal() {
            document.getElementById('download-modal').classList.add('hidden');
        }

        function openImportModal() {
            document.getElementById('import-modal').classList.remove('hidden');
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }
        function closeImportModal() {
            document.getElementById('import-modal').classList.add('hidden');
        }

        function handleFileSelected(input) {
            const preview = document.getElementById('fileNamePreview');
            if (input.files && input.files[0]) {
                preview.innerHTML = 'File dipilih: <strong class="text-gray-900">' + input.files[0].name + '</strong>';
            }
        }

        // Close on backdrop click
        ['download-modal', 'import-modal'].forEach(function(modalId) {
            const modalEl = document.getElementById(modalId);
            if (modalEl) {
                modalEl.addEventListener('click', function(e) {
                    if (e.target === this) {
                        this.classList.add('hidden');
                    }
                });
            }
        });

        // Floating Toast Notification
        let toastTimeout = null;
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toastPopup');
            const toastMsg = document.getElementById('toastMessage');
            const toastIconWrap = document.getElementById('toastIconWrap');
            const toastIcon = document.getElementById('toastIcon');

            if (!toast || !toastMsg) return;

            toastMsg.textContent = message;

            if (type === 'success') {
                toastIconWrap.className = 'w-7 h-7 rounded-xl flex items-center justify-center flex-shrink-0 bg-emerald-100 text-emerald-600';
                toastIcon.setAttribute('data-lucide', 'check');
            } else {
                toastIconWrap.className = 'w-7 h-7 rounded-xl flex items-center justify-center flex-shrink-0 bg-rose-100 text-rose-600';
                toastIcon.setAttribute('data-lucide', 'alert-circle');
            }

            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }

            toast.classList.add('show');

            if (toastTimeout) clearTimeout(toastTimeout);
            toastTimeout = setTimeout(() => {
                hideToast();
            }, 3000);
        }

        function hideToast() {
            const toast = document.getElementById('toastPopup');
            if (toast) toast.classList.remove('show');
        }

        // Quick Copy to Clipboard
        function copyToClipboard(text, message) {
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(() => {
                    showToast(message, 'success');
                }).catch(() => {
                    fallbackCopy(text, message);
                });
            } else {
                fallbackCopy(text, message);
            }
        }

        function fallbackCopy(text, message) {
            const textArea = document.createElement("textarea");
            textArea.value = text;
            textArea.style.position = "fixed";
            textArea.style.left = "-999999px";
            textArea.style.top = "-999999px";
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            try {
                document.execCommand('copy');
                showToast(message, 'success');
            } catch (err) {
                showToast('Gagal menyalin kode', 'error');
            }
            textArea.remove();
        }

        // Keyboard Shortcut: press '/' anywhere to focus search
        document.addEventListener('keydown', function(e) {
            if (e.key === '/' && document.activeElement.tagName !== 'INPUT' && document.activeElement.tagName !== 'TEXTAREA') {
                e.preventDefault();
                const searchInput = document.getElementById('searchInput');
                if (searchInput) {
                    searchInput.focus();
                    searchInput.select();
                }
            }
        });

        // Trigger flash message toast on load if available
        document.addEventListener('DOMContentLoaded', function() {
            @if (session('success'))
                showToast("{{ session('success') }}", 'success');
            @endif
            @if (session('error'))
                showToast("{{ session('error') }}", 'error');
            @endif
        });
    </script>
    @endpush
@endsection
