@extends('layouts.app')

@section('title', 'Master Data Grup Ruangan - FEB UNJ')
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

        /* Scoped Search & Filter Bar Layout */
        .search-input-col {
            flex: 1 1 auto;
            min-width: 0;
            width: 100%;
        }
        .status-select-col {
            width: 100%;
            flex: 0 0 auto;
        }
        @media (min-width: 640px) {
            .search-input-col {
                flex: 1 1 0%;
                min-width: 240px;
                width: auto;
            }
            .status-select-col {
                width: 155px !important;
                min-width: 145px !important;
                max-width: 165px !important;
                flex: 0 0 155px !important;
            }
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

        /* macOS Modal Transition */
        .mac-modal-backdrop {
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.25s ease-out;
        }
        .mac-modal-backdrop.show {
            opacity: 1;
            pointer-events: auto;
        }
        .mac-modal-content {
            transform: scale(0.95) translateY(10px);
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .mac-modal-backdrop.show .mac-modal-content {
            transform: scale(1) translateY(0);
        }
    </style>
@endpush

@section('content')
    @php
        $canManage = auth()->user()->isAdmin();
        $activeFiltersCount = (request('search') ? 1 : 0) + (request('status') ? 1 : 0);
    @endphp

    <div class="w-full space-y-6 pb-12 relative">
        {{-- Floating Toast Notification --}}
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
                    <i data-lucide="folder" class="w-5 h-5"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">Master Data Grup Ruangan</h1>
                    <p class="text-xs sm:text-sm text-gray-500">Kelola kategori/grup penataan gedung dan lantai ruangan FEB UNJ</p>
                </div>
            </div>

            @if ($canManage)
                <div class="flex items-center gap-2.5">
                    <a href="{{ route('master.room-groups.create') }}"
                        class="inline-flex items-center gap-2 px-4.5 py-2 text-xs sm:text-sm font-bold text-white bg-gradient-to-r from-[#FF4D00] to-amber-500 hover:from-[#e04400] hover:to-[#d97706] rounded-full shadow-xs hover:shadow-md transition-all active:scale-[0.98]">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        <span>Tambah Grup</span>
                    </a>
                </div>
            @endif
        </div>

        {{-- SEARCH BY TEXT & STATUS FILTER: Apple Spotlight Box --}}
        <div class="mac-card p-4 sm:p-5 relative overflow-hidden">
            <form method="GET" action="{{ route('master.room-groups.index') }}" class="space-y-3.5" id="searchForm">
                @if (request('per_page'))
                    <input type="hidden" name="per_page" value="{{ request('per_page') }}">
                @endif

                {{-- Spotlight Input Bar --}}
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 w-full">
                    {{-- 1. Search Text Input --}}
                    <div class="search-input-col relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-[#FF4D00] transition-colors">
                            <i data-lucide="search" class="w-5 h-5"></i>
                        </div>
                        <input type="text" name="search" id="searchInput" value="{{ request('search') }}"
                            class="w-full h-12 pl-12 pr-24 text-xs sm:text-sm bg-gray-50/80 hover:bg-gray-50 focus:bg-white border border-gray-200/90 focus:border-[#FF4D00] rounded-2xl focus:outline-none focus:ring-4 focus:ring-orange-500/10 shadow-2xs transition-all font-medium placeholder:text-gray-400 placeholder:font-normal"
                            placeholder="Cari berdasarkan nama grup gedung atau deskripsi cakupan lokasi..."
                            autocomplete="off">
                        
                        {{-- Clear Button & Shortcut Badge --}}
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center gap-1.5">
                            @if (request('search'))
                                <a href="{{ route('master.room-groups.index', array_merge(request()->except(['search', 'page']))) }}"
                                    class="w-6 h-6 rounded-full bg-gray-200/80 hover:bg-gray-300 text-gray-600 flex items-center justify-center transition-all cursor-pointer"
                                    title="Hapus pencarian teks">
                                    <i data-lucide="x" class="w-3.5 h-3.5"></i>
                                </a>
                            @endif
                            <kbd class="hidden md:inline-flex items-center px-2 py-0.5 text-[10px] font-mono font-medium text-gray-400 bg-white border border-gray-200 rounded-md shadow-2xs" title="Tekan / pada keyboard untuk mencari">
                                /
                            </kbd>
                        </div>
                    </div>

                    {{-- 2. Status Filter Dropdown --}}
                    <div class="status-select-col relative shrink-0">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                            <i data-lucide="sliders-horizontal" class="w-3.5 h-3.5"></i>
                        </div>
                        <select name="status" onchange="this.form.submit()"
                            class="w-full h-12 pl-9 pr-8 text-xs sm:text-sm font-medium bg-gray-50/80 hover:bg-gray-50 focus:bg-white border border-gray-200/90 focus:border-[#FF4D00] rounded-2xl focus:outline-none focus:ring-4 focus:ring-orange-500/10 shadow-2xs transition-all text-gray-700 cursor-pointer appearance-none">
                            <option value="">Semua Status</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400">
                            <i data-lucide="chevron-down" class="w-4 h-4"></i>
                        </div>
                    </div>

                    {{-- 3. Tombol Submit Cari --}}
                    <button type="submit"
                        class="w-full sm:w-auto h-12 px-6 shrink-0 inline-flex items-center justify-center gap-2 bg-gradient-to-r from-[#FF4D00] to-amber-500 hover:from-[#e04400] hover:to-[#d97706] text-white text-xs sm:text-sm font-bold rounded-2xl shadow-xs hover:shadow-md transition-all active:scale-[0.98] cursor-pointer">
                        <i data-lucide="search" class="w-4 h-4"></i>
                        <span>Cari</span>
                    </button>
                </div>

                {{-- Quick Presets Chips --}}
                <div class="flex flex-wrap items-center justify-between gap-3 pt-0.5 text-xs">
                    <div class="flex flex-wrap items-center gap-1.5">
                        <span class="text-gray-400 font-medium flex items-center gap-1">
                            <i data-lucide="sparkles" class="w-3.5 h-3.5 text-amber-500"></i>
                            Pintasan Kategori:
                        </span>
                        @php
                            $quickPresets = ['Gedung M', 'Gedung RA Kartini', 'Lantai 1', 'Lantai 2', 'Lantai 3', 'Lantai 4', 'Auditorium', 'Laboratorium'];
                        @endphp
                        @foreach ($quickPresets as $preset)
                            <a href="{{ route('master.room-groups.index', array_merge(request()->except(['search', 'page']), ['search' => $preset])) }}"
                                class="inline-flex items-center gap-1 px-3 py-1 rounded-xl text-xs font-semibold {{ request('search') == $preset ? 'bg-orange-100 text-[#FF4D00] border border-orange-300 shadow-2xs' : 'bg-gray-100/80 hover:bg-orange-50 text-gray-600 hover:text-[#FF4D00] border border-gray-200/60 hover:border-orange-200' }} transition-all active:scale-[0.96]"
                                title="Cari grup {{ $preset }}">
                                <span>{{ $preset }}</span>
                            </a>
                        @endforeach
                    </div>

                    @if ($activeFiltersCount > 0)
                        <div class="flex items-center gap-2">
                            <a href="{{ route('master.room-groups.index') }}"
                                class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-500 hover:text-orange-600 transition-colors">
                                <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                                <span>Reset Filter ({{ $activeFiltersCount }})</span>
                            </a>
                        </div>
                    @endif
                </div>
            </form>
        </div>

        {{-- TABLE CARD SECTION --}}
        <div class="mac-card overflow-hidden">
            {{-- Table Meta Header Bar --}}
            <div class="px-5 py-3.5 border-b border-gray-100 bg-gray-50/50 flex flex-wrap items-center justify-between gap-3 text-xs text-gray-500">
                <div class="flex items-center gap-3">
                    <span class="font-bold text-gray-700">Tampilkan</span>
                    <form method="GET" action="{{ route('master.room-groups.index') }}" class="inline-block m-0">
                        @if (request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                        @if (request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif

                        <select name="per_page" id="per_page"
                            class="px-2.5 py-1 text-xs border border-gray-200 rounded-lg bg-gray-50/50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-[#FF4D00] transition-colors cursor-pointer"
                            onchange="this.form.submit()">
                            <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ request('per_page', 10) == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page', 10) == 50 ? 'selected' : '' }}>50</option>
                        </select>
                    </form>
                    <span>grup per halaman</span>
                </div>

                <div class="flex items-center gap-2">
                    @if (request('search'))
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-orange-50 text-orange-800 border border-orange-200/80">
                            <i data-lucide="filter" class="w-3.5 h-3.5 text-[#FF4D00]"></i>
                            Pencarian: "<strong>{{ trim(request('search')) }}</strong>"
                        </span>
                    @endif
                    <span>Halaman {{ $roomGroups->currentPage() }} dari {{ $roomGroups->lastPage() }}</span>
                </div>
            </div>

            {{-- Table View --}}
            <div class="overflow-x-auto">
                @if ($roomGroups->count() > 0)
                    <table class="min-w-full divide-y divide-gray-100 text-left">
                        <thead>
                            <tr class="bg-gray-50/60 border-b border-gray-100">
                                <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider w-12 text-center">No</th>
                                <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider min-w-[240px]">Nama Grup Ruangan</th>
                                <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider min-w-[280px] hidden md:table-cell">Deskripsi &amp; Cakupan Lokasi</th>
                                <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider min-w-[140px] text-center">Total Ruangan</th>
                                <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider min-w-[120px] text-center">Status</th>
                                @if ($canManage)
                                    <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider text-center w-36">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach ($roomGroups as $index => $group)
                                <tr class="hover:bg-orange-50/30 transition-colors group">
                                    {{-- No --}}
                                    <td class="px-4 py-4 text-center text-xs font-semibold text-gray-400">
                                        {{ ($roomGroups->currentPage() - 1) * $roomGroups->perPage() + $index + 1 }}
                                    </td>

                                    {{-- Nama Grup Ruangan --}}
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-orange-100 to-amber-100 text-[#FF4D00] border border-orange-200/80 flex items-center justify-center font-bold text-xs shadow-2xs flex-shrink-0">
                                                <i data-lucide="folder" class="w-4 h-4"></i>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div class="flex items-center gap-1.5">
                                                    <p class="text-xs sm:text-sm font-bold text-gray-900 leading-snug truncate">
                                                        {{ $group->name }}
                                                    </p>
                                                    <button type="button" onclick="copyToClipboard('{{ addslashes($group->name) }}', 'Nama grup disalin!')"
                                                        class="opacity-0 group-hover:opacity-100 p-1 text-gray-400 hover:text-orange-600 transition-all rounded-md hover:bg-orange-50 cursor-pointer flex-shrink-0"
                                                        title="Salin Nama Grup">
                                                        <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                                                    </button>
                                                </div>
                                                <p class="text-[11px] text-gray-400 mt-0.5">
                                                    Pengelompokan Gedung / Lantai FEB UNJ
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Deskripsi & Cakupan Lokasi --}}
                                    <td class="px-4 py-4 hidden md:table-cell">
                                        <p class="text-xs text-gray-600 line-clamp-2 max-w-md leading-relaxed" title="{{ $group->description }}">
                                            {{ $group->description ?: '-' }}
                                        </p>
                                    </td>

                                    {{-- Total Ruangan Terhubung --}}
                                    <td class="px-4 py-4 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-gray-100/90 text-gray-700 border border-gray-200/80">
                                            <i data-lucide="door-open" class="w-3 h-3 text-orange-500"></i>
                                            {{ $group->rooms_count ?? $group->rooms()->count() }} Ruangan
                                        </span>
                                    </td>

                                    {{-- Status Aktif --}}
                                    <td class="px-4 py-4 text-center whitespace-nowrap">
                                        @if ($group->is_active)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/90 shadow-2xs">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                <span>Aktif</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200/90 shadow-2xs">
                                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                                <span>Nonaktif</span>
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Aksi --}}
                                    @if ($canManage)
                                        <td class="px-4 py-4 text-center whitespace-nowrap">
                                            <div class="flex items-center justify-center gap-2">
                                                {{-- Edit Button --}}
                                                <a href="{{ route('master.room-groups.edit', $group) }}"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-orange-50 hover:bg-orange-100 text-[#FF4D00] hover:text-orange-700 font-bold text-xs rounded-full border border-orange-200/80 shadow-2xs transition-all active:scale-[0.96]"
                                                    title="Edit Grup Ruangan">
                                                    <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                                                    <span>Edit</span>
                                                </a>

                                                {{-- Delete Button --}}
                                                <button type="button"
                                                    onclick="openDeleteModal('{{ route('master.room-groups.destroy', $group) }}', '{{ addslashes($group->name) }}', {{ $group->rooms_count ?? $group->rooms()->count() }})"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 hover:text-rose-800 font-bold text-xs rounded-full border border-rose-200/80 shadow-2xs transition-all active:scale-[0.96] cursor-pointer"
                                                    title="Hapus Grup Ruangan">
                                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                                    <span>Hapus</span>
                                                </button>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    {{-- Empty State (Apple Style) --}}
                    <div class="p-12 text-center">
                        <div class="w-14 h-14 mx-auto rounded-2xl bg-orange-50 text-[#FF4D00] flex items-center justify-center mb-3.5 shadow-2xs border border-orange-100">
                            <i data-lucide="folder-x" class="w-7 h-7"></i>
                        </div>
                        <h3 class="text-base font-bold text-gray-900 tracking-tight">Tidak Ada Data Grup Ruangan</h3>
                        <p class="text-xs sm:text-sm text-gray-500 max-w-sm mx-auto mt-1 leading-relaxed">
                            @if (request('search') || request('status'))
                                Tidak ada grup ruangan yang cocok dengan filter pencarian yang diterapkan. Silakan sesuaikan kata kunci Anda.
                            @else
                                Belum ada kategori grup ruangan yang terdaftar di sistem FEB UNJ.
                            @endif
                        </p>
                        <div class="mt-5 flex items-center justify-center gap-2.5">
                            @if ($activeFiltersCount > 0)
                                <a href="{{ route('master.room-groups.index') }}"
                                    class="inline-flex items-center gap-2 px-4 py-2 text-xs sm:text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-200 rounded-full shadow-2xs transition-all">
                                    <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                                    <span>Reset Filter</span>
                                </a>
                            @endif
                            @if ($canManage)
                                <a href="{{ route('master.room-groups.create') }}"
                                    class="inline-flex items-center gap-2 px-4.5 py-2 text-xs sm:text-sm font-bold text-white bg-gradient-to-r from-[#FF4D00] to-amber-500 rounded-full shadow-xs hover:shadow-md transition-all active:scale-[0.98]">
                                    <i data-lucide="plus" class="w-4 h-4"></i>
                                    <span>Tambah Grup Baru</span>
                                </a>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            {{-- Pagination Footer Bar --}}
            @if ($roomGroups->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/40">
                    {{ $roomGroups->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Delete Confirmation Modal (Apple Sheet Style) --}}
    <div id="deleteModalBackdrop" class="mac-modal-backdrop fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/40 backdrop-blur-xs">
        <div class="mac-modal-content bg-white rounded-3xl p-6 sm:p-7 text-left shadow-2xl border border-gray-100 sm:max-w-md w-full">
            <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center mb-4 shadow-2xs">
                <i data-lucide="alert-triangle" class="w-6 h-6"></i>
            </div>

            <h3 class="text-base sm:text-lg font-bold text-gray-900 tracking-tight">
                Konfirmasi Hapus Grup Ruangan
            </h3>

            <p class="text-xs sm:text-sm text-gray-600 leading-relaxed mt-2">
                Apakah Anda yakin ingin menghapus grup <strong id="deleteModalName" class="text-gray-900"></strong>?
            </p>

            <div id="deleteModalWarning" class="mt-3 p-3 rounded-xl bg-rose-50 border border-rose-200 text-[11px] text-rose-800 hidden items-start gap-2">
                <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600 flex-shrink-0 mt-0.5"></i>
                <span id="deleteModalWarningText"></span>
            </div>

            <div class="mt-6 flex items-center justify-end gap-2.5">
                <button type="button" onclick="closeDeleteModal()"
                    class="px-4 py-2 text-xs sm:text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-all cursor-pointer">
                    Batal
                </button>
                <form id="deleteModalForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="px-4.5 py-2 text-xs sm:text-sm font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl shadow-xs transition-all cursor-pointer">
                        Ya, Hapus Grup
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Keyboard shortcut / for search
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

    // Copy to clipboard helper
    function copyToClipboard(text, msg) {
        navigator.clipboard.writeText(text).then(() => {
            showToast(msg, 'success');
        }).catch(() => {
            showToast('Gagal menyalin teks', 'error');
        });
    }

    // Modal management
    function openDeleteModal(actionUrl, name, roomsCount) {
        const backdrop = document.getElementById('deleteModalBackdrop');
        const form = document.getElementById('deleteModalForm');
        const nameEl = document.getElementById('deleteModalName');
        const warningBox = document.getElementById('deleteModalWarning');
        const warningText = document.getElementById('deleteModalWarningText');

        if (!backdrop || !form) return;

        form.action = actionUrl;
        nameEl.textContent = name;

        if (roomsCount > 0) {
            warningBox.classList.remove('hidden');
            warningBox.classList.add('flex');
            warningText.textContent = `Peringatan: Grup ini memiliki ${roomsCount} ruangan terhubung. Sistem akan menolak penghapusan grup jika masih memiliki ruangan terkait demi integritas data.`;
        } else {
            warningBox.classList.add('hidden');
            warningBox.classList.remove('flex');
        }

        backdrop.classList.add('show');
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function closeDeleteModal() {
        const backdrop = document.getElementById('deleteModalBackdrop');
        if (backdrop) backdrop.classList.remove('show');
    }

    // Toast manager
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
        } else if (type === 'error') {
            toastIconWrap.className = 'w-7 h-7 rounded-xl flex items-center justify-center flex-shrink-0 bg-rose-100 text-rose-600';
            toastIcon.setAttribute('data-lucide', 'alert-circle');
        } else {
            toastIconWrap.className = 'w-7 h-7 rounded-xl flex items-center justify-center flex-shrink-0 bg-blue-100 text-blue-600';
            toastIcon.setAttribute('data-lucide', 'info');
        }

        if (typeof lucide !== 'undefined') lucide.createIcons();

        toast.classList.add('show');
        clearTimeout(toastTimeout);
        toastTimeout = setTimeout(hideToast, 3500);
    }

    function hideToast() {
        const toast = document.getElementById('toastPopup');
        if (toast) toast.classList.remove('show');
    }

    document.addEventListener('DOMContentLoaded', function() {
        if (typeof lucide !== 'undefined') lucide.createIcons();

        // Dismiss modal on backdrop click
        document.addEventListener('click', function(e) {
            const backdrop = document.getElementById('deleteModalBackdrop');
            if (backdrop && e.target === backdrop) {
                closeDeleteModal();
            }
        });

        // Esc key closes modal
        window.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeDeleteModal();
        });

        @if(session('success'))
            showToast("{{ session('success') }}", 'success');
        @endif
        @if(session('error'))
            showToast("{{ session('error') }}", 'error');
        @endif
    });
</script>
@endpush
