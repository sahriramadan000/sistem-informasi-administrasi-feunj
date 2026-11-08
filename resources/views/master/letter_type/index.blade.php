@extends('layouts.app')

@section('title', 'Master Jenis Surat - FEB UNJ')
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
                    <i data-lucide="file-text" class="w-5 h-5"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">Master Jenis Surat</h1>
                    <p class="text-xs sm:text-sm text-gray-500">Kelola jenis-jenis surat dinas, konfigurasi keperluan surat, dan kode penomoran FEB UNJ</p>
                </div>
            </div>

            @if (auth()->user()->isAdmin())
                <div class="flex items-center gap-2.5">
                    {{-- Add Button --}}
                    <a href="{{ route('master.letter-types.create') }}"
                        class="inline-flex items-center gap-2 px-4.5 py-2 text-xs sm:text-sm font-bold text-white bg-gradient-to-r from-[#FF4D00] to-amber-500 hover:from-[#e04400] hover:to-[#d97706] rounded-full shadow-xs hover:shadow-md transition-all active:scale-[0.98]">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        <span>Tambah Jenis Surat</span>
                    </a>
                </div>
            @endif
        </div>

        {{-- Apple Segmented Pill Tabs (Status Filter) --}}
        <div class="bg-gray-100/70 p-1.5 rounded-2xl border border-gray-200/60 backdrop-blur-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <nav class="flex items-center gap-1.5 overflow-x-auto scrollbar-none" aria-label="Tabs Status Jenis Surat">
                <a href="{{ route('master.letter-types.index', array_merge(request()->except(['status', 'page']))) }}"
                   class="whitespace-nowrap px-4 py-2 rounded-xl text-xs sm:text-sm transition-all flex items-center gap-2 {{ !request('status') ? 'mac-tab-active text-[#FF4D00]' : 'mac-tab-inactive' }}">
                    <i data-lucide="layers" class="w-4 h-4"></i>
                    <span>Semua Status</span>
                </a>
                <a href="{{ route('master.letter-types.index', array_merge(request()->except(['status', 'page']), ['status' => 'active'])) }}"
                   class="whitespace-nowrap px-4 py-2 rounded-xl text-xs sm:text-sm transition-all flex items-center gap-2 {{ request('status') === 'active' ? 'mac-tab-active text-emerald-600' : 'mac-tab-inactive' }}">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>Aktif</span>
                </a>
                <a href="{{ route('master.letter-types.index', array_merge(request()->except(['status', 'page']), ['status' => 'inactive'])) }}"
                   class="whitespace-nowrap px-4 py-2 rounded-xl text-xs sm:text-sm transition-all flex items-center gap-2 {{ request('status') === 'inactive' ? 'mac-tab-active text-gray-700' : 'mac-tab-inactive' }}">
                    <span class="w-2 h-2 rounded-full bg-gray-400"></span>
                    <span>Nonaktif</span>
                </a>
            </nav>

            {{-- Total Count Capsule --}}
            <div class="hidden sm:flex items-center gap-2 pr-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-white border border-gray-200 text-gray-700 shadow-2xs">
                    <i data-lucide="hash" class="w-3.5 h-3.5 text-orange-500"></i>
                    Total: {{ number_format($letterTypes->total(), 0, ',', '.') }} Jenis Surat
                </span>
            </div>
        </div>

        {{-- Apple Spotlight Search & Quick Filters Box --}}
        <div class="mac-card p-4 sm:p-5 relative overflow-hidden">
            <form method="GET" action="{{ route('master.letter-types.index') }}" class="space-y-3.5" id="searchForm">
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
                            placeholder="Cari kode (ST, SK, SU), nama jenis surat, atau deskripsi..."
                            autocomplete="off">
                        
                        {{-- Clear Button & Shortcut Badge --}}
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center gap-1.5">
                            @if (request('search'))
                                <a href="{{ route('master.letter-types.index', array_merge(request()->except(['search', 'page']))) }}"
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
                        <span>Cari Jenis Surat</span>
                    </button>
                </div>

                {{-- Quick Filter Chips --}}
                <div class="flex flex-wrap items-center justify-between gap-3 pt-0.5 text-xs">
                    <div class="flex flex-wrap items-center gap-1.5">
                        <span class="text-gray-400 font-medium flex items-center gap-1">
                            <i data-lucide="sparkles" class="w-3.5 h-3.5 text-amber-500"></i>
                            Pintasan Jenis:
                        </span>
                        @php
                            $quickTypes = [
                                ['code' => 'ST', 'label' => 'Surat Tugas'],
                                ['code' => 'SK', 'label' => 'Surat Keputusan'],
                                ['code' => 'SU', 'label' => 'Surat Umum'],
                                ['code' => 'UND', 'label' => 'Undangan'],
                                ['code' => 'SMW', 'label' => 'SIPERMAWA'],
                                ['code' => 'MOU', 'label' => 'MOU / IA'],
                                ['code' => 'SRTF', 'label' => 'Sertifikat'],
                            ];
                        @endphp
                        @foreach ($quickTypes as $quick)
                            <a href="{{ route('master.letter-types.index', array_merge(request()->except(['search', 'page']), ['search' => $quick['code']])) }}"
                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-xs font-semibold {{ request('search') == $quick['code'] ? 'bg-orange-100 text-[#FF4D00] border border-orange-300 shadow-2xs' : 'bg-gray-100/80 hover:bg-orange-50 text-gray-600 hover:text-[#FF4D00] border border-gray-200/60 hover:border-orange-200' }} transition-all active:scale-[0.96]"
                                title="Cari {{ $quick['label'] }}">
                                <span class="font-mono font-bold">{{ $quick['code'] }}</span>
                                <span class="text-[11px] opacity-75 hidden sm:inline">({{ $quick['label'] }})</span>
                            </a>
                        @endforeach
                    </div>

                    @if (request('search') || request('status'))
                        <a href="{{ route('master.letter-types.index') }}"
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
                    <form method="GET" action="{{ route('master.letter-types.index') }}" class="inline-block m-0">
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
                            <i data-lucide="filter" class="w-3.5 h-3.5 text-[#FF4D00]"></i>
                            Pencarian: "<strong>{{ trim(request('search')) }}</strong>"
                        </span>
                    @endif
                    <span>Halaman {{ $letterTypes->currentPage() }} dari {{ $letterTypes->lastPage() }}</span>
                </div>
            </div>

            {{-- Table View --}}
            <div class="overflow-x-auto">
                @if ($letterTypes->count() > 0)
                    <table class="min-w-full divide-y divide-gray-100 text-left">
                        <thead>
                            <tr class="bg-gray-50/60 border-b border-gray-100">
                                <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider w-12 text-center">No</th>
                                <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider min-w-[130px]">Kode</th>
                                <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider min-w-[200px]">Nama Jenis Surat</th>
                                <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider min-w-[240px] hidden md:table-cell">Deskripsi</th>
                                @if (auth()->user()->isAdmin())
                                    <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider min-w-[150px] text-center">Keperluan Surat</th>
                                    <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider min-w-[130px] text-center">Arsip Drive</th>
                                @endif
                                <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider min-w-[100px] text-center">Status</th>
                                @if (auth()->user()->isAdmin())
                                    <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider text-center w-36">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach ($letterTypes as $index => $letterType)
                                <tr class="hover:bg-orange-50/30 transition-colors group">
                                    {{-- No --}}
                                    <td class="px-4 py-4 text-center text-xs font-semibold text-gray-400">
                                        {{ ($letterTypes->currentPage() - 1) * $letterTypes->perPage() + $index + 1 }}
                                    </td>

                                    {{-- Kode --}}
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="inline-flex items-center gap-2">
                                            @if ($letterType->code)
                                                <span class="font-mono text-xs sm:text-[13px] font-bold text-gray-900 bg-gray-100/90 group-hover:bg-orange-100/70 group-hover:text-[#FF4D00] px-2.5 py-1 rounded-lg border border-gray-200/80 group-hover:border-orange-200 transition-colors">
                                                    {{ $letterType->code }}
                                                </span>
                                                <button type="button" onclick="copyToClipboard('{{ addslashes($letterType->code) }}', 'Kode disalin!')"
                                                    class="opacity-0 group-hover:opacity-100 p-1 text-gray-400 hover:text-orange-600 transition-all rounded-md hover:bg-orange-50 cursor-pointer flex-shrink-0"
                                                    title="Salin Kode">
                                                    <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                                                </button>
                                            @else
                                                <span class="text-xs text-gray-400 italic font-mono">-</span>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Nama Jenis Surat --}}
                                    <td class="px-4 py-4">
                                        <p class="text-xs sm:text-sm font-semibold text-gray-900 leading-snug">
                                            {{ $letterType->name }}
                                        </p>
                                    </td>

                                    {{-- Deskripsi --}}
                                    <td class="px-4 py-4 hidden md:table-cell">
                                        <p class="text-xs text-gray-500 leading-relaxed break-words max-w-lg">
                                            {{ $letterType->description ?: '-' }}
                                        </p>
                                    </td>

                                    {{-- Keperluan Surat (Apple iOS Toggle Switch with AJAX) --}}
                                    @if (auth()->user()->isAdmin())
                                        <td class="px-4 py-4 whitespace-nowrap text-center">
                                            <div class="inline-flex items-center justify-center gap-2">
                                                <label class="relative inline-flex items-center cursor-pointer">
                                                    <input type="checkbox" 
                                                           class="sr-only peer toggle-requires-purpose" 
                                                           data-id="{{ $letterType->id }}"
                                                           data-name="{{ $letterType->name }}"
                                                           {{ $letterType->requires_purpose ? 'checked' : '' }}>
                                                    <div class="w-10 h-5.5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4.5 after:w-4.5 after:transition-all peer-checked:bg-emerald-500 shadow-inner"></div>
                                                </label>
                                                <span class="text-xs font-semibold {{ $letterType->requires_purpose ? 'text-emerald-700' : 'text-gray-400' }} label-requires-purpose min-w-[32px] text-left">
                                                    {{ $letterType->requires_purpose ? 'Ya' : 'Tidak' }}
                                                </span>
                                            </div>
                                        </td>

                                        {{-- Arsip Google Drive (Apple iOS Toggle Switch with AJAX) --}}
                                        <td class="px-4 py-4 whitespace-nowrap text-center">
                                            <div class="inline-flex items-center justify-center gap-2">
                                                <label class="relative inline-flex items-center cursor-pointer">
                                                    <input type="checkbox" 
                                                           class="sr-only peer toggle-requires-drive" 
                                                           data-id="{{ $letterType->id }}"
                                                           data-name="{{ $letterType->name }}"
                                                           {{ $letterType->requires_drive_archive ? 'checked' : '' }}>
                                                    <div class="w-10 h-5.5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4.5 after:w-4.5 after:transition-all peer-checked:bg-emerald-500 shadow-inner"></div>
                                                </label>
                                                <span class="text-xs font-semibold {{ $letterType->requires_drive_archive ? 'text-emerald-700' : 'text-gray-400' }} label-requires-drive min-w-[32px] text-left">
                                                    {{ $letterType->requires_drive_archive ? 'Ya' : 'Tidak' }}
                                                </span>
                                            </div>
                                        </td>
                                    @endif

                                    {{-- Status --}}
                                    <td class="px-4 py-4 text-center whitespace-nowrap">
                                        @if ($letterType->is_active)
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
                                                <a href="{{ route('master.letter-types.edit', $letterType) }}"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-orange-50 hover:bg-orange-100 text-[#FF4D00] hover:text-orange-700 font-bold text-xs rounded-full border border-orange-200/80 shadow-2xs transition-all active:scale-[0.96]"
                                                    title="Edit Jenis Surat">
                                                    <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                                                    <span>Edit</span>
                                                </a>

                                                {{-- Deactivate Button --}}
                                                @if ($letterType->is_active)
                                                    <form method="POST" action="{{ route('master.letter-types.destroy', $letterType) }}"
                                                        onsubmit="return confirm('Apakah Anda yakin ingin menonaktifkan jenis surat ini? Data tidak akan dihapus dari database.')"
                                                        class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 hover:text-rose-800 font-bold text-xs rounded-full border border-rose-200/80 shadow-2xs transition-all active:scale-[0.96] cursor-pointer"
                                                            title="Nonaktifkan Jenis Surat">
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
                @else
                    {{-- Empty State --}}
                    <div class="p-12 text-center">
                        <div class="w-14 h-14 mx-auto rounded-2xl bg-orange-50 text-[#FF4D00] flex items-center justify-center mb-3.5 shadow-2xs border border-orange-100">
                            <i data-lucide="file-question" class="w-7 h-7"></i>
                        </div>
                        <h3 class="text-base font-bold text-gray-900 tracking-tight">Tidak Ada Jenis Surat Ditemukan</h3>
                        <p class="text-xs sm:text-sm text-gray-500 max-w-sm mx-auto mt-1 leading-relaxed">
                            @if (request('search') || request('status'))
                                Tidak ada data yang cocok dengan kriteria filter yang Anda pilih. Coba sesuaikan kata kunci atau reset filter.
                            @else
                                Belum ada jenis surat yang terdaftar di dalam sistem administrasi FEB UNJ.
                            @endif
                        </p>
                        <div class="mt-5 flex items-center justify-center gap-2.5">
                            @if (request('search') || request('status'))
                                <a href="{{ route('master.letter-types.index') }}"
                                    class="inline-flex items-center gap-2 px-4 py-2 text-xs sm:text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-200 rounded-full shadow-2xs transition-all">
                                    <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                                    <span>Reset Filter</span>
                                </a>
                            @endif
                            @if (auth()->user()->isAdmin())
                                <a href="{{ route('master.letter-types.create') }}"
                                    class="inline-flex items-center gap-2 px-4.5 py-2 text-xs sm:text-sm font-bold text-white bg-gradient-to-r from-[#FF4D00] to-amber-500 rounded-full shadow-xs hover:shadow-md transition-all active:scale-[0.98]">
                                    <i data-lucide="plus" class="w-4 h-4"></i>
                                    <span>Tambah Jenis Surat</span>
                                </a>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            {{-- Compact Apple Style Pagination --}}
            @if ($letterTypes->hasPages())
                <div class="px-5 py-4 border-t border-gray-100 bg-gray-50/40">
                    {{ $letterTypes->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://unpkg.com/lucide@latest"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        // Keyboard Shortcut: '/' to focus search input
        window.addEventListener('keydown', function(e) {
            if (e.key === '/' && !['INPUT', 'TEXTAREA'].includes(document.activeElement.tagName)) {
                e.preventDefault();
                const searchInput = document.getElementById('searchInput');
                if (searchInput) {
                    searchInput.focus();
                    searchInput.select();
                }
            }
        });

        // Handle AJAX toggle for requires_purpose
        const toggles = document.querySelectorAll('.toggle-requires-purpose');
        toggles.forEach(toggle => {
            toggle.addEventListener('change', function() {
                const letterTypeId = this.dataset.id;
                const letterTypeName = this.dataset.name || 'Jenis surat';
                const isChecked = this.checked;
                const label = this.closest('td').querySelector('.label-requires-purpose');
                
                // Update label text and color immediately
                if (label) {
                    label.textContent = isChecked ? 'Ya' : 'Tidak';
                    label.className = `text-xs font-semibold ${isChecked ? 'text-emerald-700' : 'text-gray-400'} label-requires-purpose min-w-[32px] text-left`;
                }

                // AJAX Request
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                
                fetch(`/master/letter-types/${letterTypeId}/toggle-requires-purpose`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken || ''
                    },
                    body: JSON.stringify({
                        requires_purpose: isChecked
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const statusText = isChecked ? 'diwajibkan' : 'dinonaktifkan';
                        showToast(`Keperluan surat untuk "${letterTypeName}" ${statusText}`, 'success');
                    } else {
                        // Revert on error
                        toggle.checked = !isChecked;
                        if (label) {
                            label.textContent = !isChecked ? 'Ya' : 'Tidak';
                            label.className = `text-xs font-semibold ${!isChecked ? 'text-emerald-700' : 'text-gray-400'} label-requires-purpose min-w-[32px] text-left`;
                        }
                        showToast(data.message || 'Gagal mengubah status keperluan surat.', 'error');
                    }
                })
                .catch(err => {
                    console.error('Error toggling requires_purpose:', err);
                    toggle.checked = !isChecked;
                    if (label) {
                        label.textContent = !isChecked ? 'Ya' : 'Tidak';
                        label.className = `text-xs font-semibold ${!isChecked ? 'text-emerald-700' : 'text-gray-400'} label-requires-purpose min-w-[32px] text-left`;
                    }
                    showToast('Terjadi kesalahan jaringan saat memperbarui status.', 'error');
                });
            });
        });

        // Handle AJAX toggle for requires_drive_archive
        const driveToggles = document.querySelectorAll('.toggle-requires-drive');
        driveToggles.forEach(toggle => {
            toggle.addEventListener('change', function() {
                const letterTypeId = this.dataset.id;
                const letterTypeName = this.dataset.name || 'Jenis surat';
                const isChecked = this.checked;
                const label = this.closest('td').querySelector('.label-requires-drive');
                
                // Update label text and color immediately
                if (label) {
                    label.textContent = isChecked ? 'Ya' : 'Tidak';
                    label.className = `text-xs font-semibold ${isChecked ? 'text-emerald-700' : 'text-gray-400'} label-requires-drive min-w-[32px] text-left`;
                }

                // AJAX Request
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                
                fetch(`/master/letter-types/${letterTypeId}/toggle-requires-drive-archive`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken || ''
                    },
                    body: JSON.stringify({
                        requires_drive_archive: isChecked
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const statusText = isChecked ? 'diwajibkan' : 'dinonaktifkan';
                        showToast(`Pengarsipan Google Drive untuk "${letterTypeName}" ${statusText}`, 'success');
                    } else {
                        // Revert on error
                        toggle.checked = !isChecked;
                        if (label) {
                            label.textContent = !isChecked ? 'Ya' : 'Tidak';
                            label.className = `text-xs font-semibold ${!isChecked ? 'text-emerald-700' : 'text-gray-400'} label-requires-drive min-w-[32px] text-left`;
                        }
                        showToast(data.message || 'Gagal mengubah status pengarsipan Drive.', 'error');
                    }
                })
                .catch(err => {
                    console.error('Error toggling requires_drive_archive:', err);
                    toggle.checked = !isChecked;
                    if (label) {
                        label.textContent = !isChecked ? 'Ya' : 'Tidak';
                        label.className = `text-xs font-semibold ${!isChecked ? 'text-emerald-700' : 'text-gray-400'} label-requires-drive min-w-[32px] text-left`;
                    }
                    showToast('Terjadi kesalahan jaringan saat memperbarui status.', 'error');
                });
            });
        });

        // Flash message from session
        @if(session('success'))
            showToast("{{ session('success') }}", 'success');
        @endif
        @if(session('error'))
            showToast("{{ session('error') }}", 'error');
        @endif
    });

    // Copy to Clipboard
    function copyToClipboard(text, successMsg = 'Teks disalin ke clipboard') {
        if (!navigator.clipboard) {
            const textarea = document.createElement('textarea');
            textarea.value = text;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
            showToast(successMsg, 'success');
            return;
        }

        navigator.clipboard.writeText(text).then(() => {
            showToast(successMsg, 'success');
        }).catch(err => {
            console.error('Failed to copy: ', err);
            showToast('Gagal menyalin kode', 'error');
        });
    }

    // Floating Toast Notification
    let toastTimeout;
    function showToast(msg, type = 'success') {
        const toast = document.getElementById('toastPopup');
        const toastMsg = document.getElementById('toastMessage');
        const toastIconWrap = document.getElementById('toastIconWrap');
        const toastIcon = document.getElementById('toastIcon');

        if (!toast) return;

        toastMsg.textContent = msg;

        if (type === 'success') {
            toastIconWrap.className = 'w-7 h-7 rounded-xl flex items-center justify-center flex-shrink-0 bg-emerald-100 text-emerald-600';
            toastIcon.setAttribute('data-lucide', 'check');
        } else {
            toastIconWrap.className = 'w-7 h-7 rounded-xl flex items-center justify-center flex-shrink-0 bg-red-100 text-red-600';
            toastIcon.setAttribute('data-lucide', 'alert-circle');
        }

        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        toast.classList.add('show');

        clearTimeout(toastTimeout);
        toastTimeout = setTimeout(() => {
            hideToast();
        }, 3200);
    }

    function hideToast() {
        const toast = document.getElementById('toastPopup');
        if (toast) {
            toast.classList.remove('show');
        }
    }
</script>
@endpush
