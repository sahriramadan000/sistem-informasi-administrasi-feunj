@extends('layouts.app')

@section('title', 'Master Jenjang Pendidikan - FEB UNJ')
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
        $canManage = auth()->user()->isAdmin() || auth()->user()->isOperator();
    @endphp

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
                    <i data-lucide="graduation-cap" class="w-5 h-5"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">Master Jenjang Pendidikan</h1>
                    <p class="text-xs sm:text-sm text-gray-500">Kelola master data jenjang studi (Sarjana, Magister, dll.) serta tarif per lembar legalisir FEB UNJ</p>
                </div>
            </div>

            @if ($canManage)
                <div class="flex items-center gap-2.5">
                    {{-- Add Button --}}
                    <a href="{{ route('master.education-levels.create') }}"
                        class="inline-flex items-center gap-2 px-4.5 py-2 text-xs sm:text-sm font-bold text-white bg-gradient-to-r from-[#FF4D00] to-amber-500 hover:from-[#e04400] hover:to-[#d97706] rounded-full shadow-xs hover:shadow-md transition-all active:scale-[0.98]">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        <span>Tambah Jenjang</span>
                    </a>
                </div>
            @endif
        </div>

        {{-- Apple Spotlight Search & Quick Filters Box --}}
        <div class="mac-card p-4 sm:p-5 relative overflow-hidden">
            <form method="GET" action="{{ route('master.education-levels.index') }}" class="space-y-3.5" id="searchForm">
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
                            placeholder="Cari nama jenjang pendidikan (Sarjana, Magister, Diploma, Doktor)..."
                            autocomplete="off">
                        
                        {{-- Clear Button & Shortcut Badge --}}
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center gap-1.5">
                            @if (request('search'))
                                <a href="{{ route('master.education-levels.index', array_merge(request()->except(['search', 'page']))) }}"
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
                        <span>Cari Jenjang</span>
                    </button>
                </div>

                {{-- Quick Preset Chips & Summary Row --}}
                <div class="flex flex-wrap items-center justify-between gap-3 pt-0.5 text-xs">
                    <div class="flex flex-wrap items-center gap-1.5">
                        <span class="text-gray-400 font-medium flex items-center gap-1">
                            <i data-lucide="sparkles" class="w-3.5 h-3.5 text-amber-500"></i>
                            Pintasan Jenjang:
                        </span>
                        @php
                            $quickLevels = ['Sarjana', 'Magister', 'Diploma', 'Doktor'];
                        @endphp
                        @foreach ($quickLevels as $preset)
                            <a href="{{ route('master.education-levels.index', array_merge(request()->except(['search', 'page']), ['search' => $preset])) }}"
                                class="inline-flex items-center gap-1 px-3 py-1 rounded-xl text-xs font-semibold {{ request('search') == $preset ? 'bg-orange-100 text-[#FF4D00] border border-orange-300 shadow-2xs' : 'bg-gray-100/80 hover:bg-orange-50 text-gray-600 hover:text-[#FF4D00] border border-gray-200/60 hover:border-orange-200' }} transition-all active:scale-[0.96]"
                                title="Cari jenjang {{ $preset }}">
                                <span>{{ $preset }}</span>
                            </a>
                        @endforeach
                    </div>

                    <div class="flex items-center gap-2">
                        @if (request('search'))
                            <a href="{{ route('master.education-levels.index') }}"
                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold text-gray-500 hover:text-red-600 bg-white border border-gray-200 hover:border-red-200 shadow-2xs transition-all">
                                <i data-lucide="rotate-ccw" class="w-3 h-3"></i>
                                <span>Reset Filter</span>
                            </a>
                        @endif

                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-white border border-gray-200 text-gray-700 shadow-2xs">
                            <i data-lucide="hash" class="w-3.5 h-3.5 text-orange-500"></i>
                            Total: {{ number_format($educationLevels->total(), 0, ',', '.') }} Jenjang
                        </span>
                    </div>
                </div>
            </form>
        </div>

        {{-- MAIN DATA CONTAINER (Table View) --}}
        <div class="mac-card overflow-hidden">
            {{-- Table Control Bar (Per Page & Status Info) --}}
            <div class="px-5 py-3 border-b border-gray-100 bg-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs text-gray-500">
                <div class="flex items-center gap-2">
                    <span>Tampilkan:</span>
                    <form method="GET" action="{{ route('master.education-levels.index') }}" class="inline-block m-0">
                        @if (request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
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
                    <span>Halaman {{ $educationLevels->currentPage() }} dari {{ $educationLevels->lastPage() }}</span>
                </div>
            </div>

            {{-- Table View --}}
            <div class="overflow-x-auto">
                @if ($educationLevels->count() > 0)
                    <table class="min-w-full divide-y divide-gray-100 text-left">
                        <thead>
                            <tr class="bg-gray-50/60 border-b border-gray-100">
                                <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider w-12 text-center">No</th>
                                <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider min-w-[260px]">Nama Jenjang Pendidikan</th>
                                <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider min-w-[200px]">Tarif per Lembar</th>
                                <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider min-w-[140px] text-center">Transaksi Legalisir</th>
                                @if ($canManage)
                                    <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider text-center w-36">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach ($educationLevels as $index => $level)
                                @php
                                    $initials = collect(explode(' ', $level->name))
                                        ->filter()
                                        ->take(2)
                                        ->map(fn($w) => strtoupper(substr($w, 0, 1)))
                                        ->join('');
                                    if (empty($initials)) {
                                        $initials = 'JP';
                                    }
                                @endphp
                                <tr class="hover:bg-orange-50/30 transition-colors group">
                                    {{-- No --}}
                                    <td class="px-4 py-4 text-center text-xs font-semibold text-gray-400">
                                        {{ ($educationLevels->currentPage() - 1) * $educationLevels->perPage() + $index + 1 }}
                                    </td>

                                    {{-- Nama Jenjang Pendidikan (Avatar + Nama) --}}
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-3">
                                            {{-- Initials Avatar --}}
                                            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-orange-100 to-amber-100 text-orange-700 border border-orange-200/80 flex items-center justify-center font-bold text-xs shadow-2xs flex-shrink-0">
                                                {{ $initials }}
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div class="flex items-center gap-1.5">
                                                    <p class="text-xs sm:text-sm font-bold text-gray-900 leading-snug truncate">
                                                        {{ $level->name }}
                                                    </p>
                                                    <button type="button" onclick="copyToClipboard('{{ addslashes($level->name) }}', 'Nama jenjang disalin!')"
                                                        class="opacity-0 group-hover:opacity-100 p-1 text-gray-400 hover:text-orange-600 transition-all rounded-md hover:bg-orange-50 cursor-pointer flex-shrink-0"
                                                        title="Salin Nama Jenjang">
                                                        <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                                                    </button>
                                                </div>
                                                <p class="text-[11px] text-gray-400 mt-0.5">
                                                    Kategori Jenjang Studi Resmi FEB UNJ
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Tarif per Lembar --}}
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div>
                                            <span class="font-mono text-xs sm:text-sm font-extrabold text-gray-900">
                                                Rp {{ number_format($level->price_per_page, 0, ',', '.') }}
                                            </span>
                                            <span class="text-xs text-gray-400 font-medium">/ lembar</span>
                                        </div>
                                        <p class="text-[11px] text-gray-400 font-medium mt-0.5">Tarif PNBP Legalitas</p>
                                    </td>

                                    {{-- Transaksi Legalisir Terkait --}}
                                    <td class="px-4 py-4 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-gray-100/90 text-gray-700 border border-gray-200/80">
                                            <i data-lucide="file-check-2" class="w-3 h-3 text-orange-500"></i>
                                            {{ $level->legalizations()->count() }} Berkas
                                        </span>
                                    </td>

                                    {{-- Aksi --}}
                                    @if ($canManage)
                                        <td class="px-4 py-4 text-center whitespace-nowrap">
                                            <div class="flex items-center justify-center gap-2">
                                                {{-- Edit Button --}}
                                                <a href="{{ route('master.education-levels.edit', $level) }}"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-orange-50 hover:bg-orange-100 text-[#FF4D00] hover:text-orange-700 font-bold text-xs rounded-full border border-orange-200/80 shadow-2xs transition-all active:scale-[0.96]"
                                                    title="Edit Jenjang Pendidikan">
                                                    <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                                                    <span>Edit</span>
                                                </a>

                                                {{-- Deactivate Button --}}
                                                <button type="button"
                                                    onclick="openDeactivateModal('{{ route('master.education-levels.destroy', $level) }}', '{{ addslashes($level->name) }}', 'Rp {{ number_format($level->price_per_page, 0, ',', '.') }}/lembar')"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 hover:text-rose-800 font-bold text-xs rounded-full border border-rose-200/80 shadow-2xs transition-all active:scale-[0.96] cursor-pointer"
                                                    title="Nonaktifkan Jenjang Pendidikan">
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
                    {{-- Empty State --}}
                    <div class="p-12 text-center">
                        <div class="w-14 h-14 mx-auto rounded-2xl bg-orange-50 text-[#FF4D00] flex items-center justify-center mb-3.5 shadow-2xs border border-orange-100">
                            <i data-lucide="graduation-cap" class="w-7 h-7"></i>
                        </div>
                        <h3 class="text-base font-bold text-gray-900 tracking-tight">Tidak Ada Data Jenjang Ditemukan</h3>
                        <p class="text-xs sm:text-sm text-gray-500 max-w-sm mx-auto mt-1 leading-relaxed">
                            @if (request('search'))
                                Tidak ada jenjang yang cocok dengan kriteria pencarian "{{ request('search') }}". Coba sesuaikan kata kunci Anda.
                            @else
                                Belum ada master jenjang pendidikan yang terdaftar di sistem legalisir FEB UNJ.
                            @endif
                        </p>
                        <div class="mt-5 flex items-center justify-center gap-2.5">
                            @if (request('search'))
                                <a href="{{ route('master.education-levels.index') }}"
                                    class="inline-flex items-center gap-2 px-4 py-2 text-xs sm:text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-200 rounded-full shadow-2xs transition-all">
                                    <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                                    <span>Reset Filter</span>
                                </a>
                            @endif
                            @if ($canManage)
                                <a href="{{ route('master.education-levels.create') }}"
                                    class="inline-flex items-center gap-2 px-4.5 py-2 text-xs sm:text-sm font-bold text-white bg-gradient-to-r from-[#FF4D00] to-amber-500 rounded-full shadow-xs hover:shadow-md transition-all active:scale-[0.98]">
                                    <i data-lucide="plus" class="w-4 h-4"></i>
                                    <span>Tambah Jenjang</span>
                                </a>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            {{-- Compact Apple Style Pagination --}}
            @if ($educationLevels->hasPages())
                <div class="px-5 py-4 border-t border-gray-100 bg-gray-50/40">
                    {{ $educationLevels->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Deactivate Confirmation Modal (Apple Sheet Style) --}}
    <div id="deactivateModalBackdrop" class="mac-modal-backdrop fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/40 backdrop-blur-xs">
        <div class="mac-modal-content bg-white rounded-3xl p-6 sm:p-7 text-left shadow-2xl border border-gray-100 sm:max-w-md w-full">
            <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center mb-4 shadow-2xs">
                <i data-lucide="alert-triangle" class="w-6 h-6"></i>
            </div>

            <h3 class="text-base sm:text-lg font-bold text-gray-900 tracking-tight">
                Konfirmasi Nonaktifkan Jenjang
            </h3>

            <p class="text-xs sm:text-sm text-gray-600 leading-relaxed mt-2">
                Apakah Anda yakin ingin menonaktifkan master jenjang <strong id="modalLevelName" class="text-gray-900"></strong> (<span id="modalLevelPrice" class="text-gray-700 font-mono"></span>)?
            </p>

            <div class="mt-3 p-3 rounded-xl bg-amber-50 border border-amber-200/80 text-[11px] text-amber-800 flex items-start gap-2">
                <i data-lucide="info" class="w-4 h-4 text-amber-600 flex-shrink-0 mt-0.5"></i>
                <span>Data riwayat transaksi legalisir yang pernah menggunakan jenjang ini tetap tersimpan aman di database dan tidak akan terhapus.</span>
            </div>

            <div class="mt-6 flex items-center justify-end gap-2.5">
                <button type="button" onclick="closeDeactivateModal()"
                    class="px-4 py-2 text-xs sm:text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-all cursor-pointer">
                    Batal
                </button>
                <form id="deactivateForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="px-4.5 py-2 text-xs sm:text-sm font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl shadow-xs transition-all cursor-pointer">
                        Ya, Nonaktifkan
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
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
            showToast('Gagal menyalin teks', 'error');
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
        } else if (type === 'error') {
            toastIconWrap.className = 'w-7 h-7 rounded-xl flex items-center justify-center flex-shrink-0 bg-rose-100 text-rose-600';
            toastIcon.setAttribute('data-lucide', 'alert-circle');
        } else {
            toastIconWrap.className = 'w-7 h-7 rounded-xl flex items-center justify-center flex-shrink-0 bg-blue-100 text-blue-600';
            toastIcon.setAttribute('data-lucide', 'info');
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

    // Modal Handlers (Apple Style)
    function openDeactivateModal(actionUrl, levelName, priceText) {
        const backdrop = document.getElementById('deactivateModalBackdrop');
        const form = document.getElementById('deactivateForm');
        const nameEl = document.getElementById('modalLevelName');
        const priceEl = document.getElementById('modalLevelPrice');

        if (backdrop && form) {
            form.action = actionUrl;
            if (nameEl) nameEl.textContent = levelName;
            if (priceEl) priceEl.textContent = priceText;
            backdrop.classList.add('show');
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }
    }

    function closeDeactivateModal() {
        const backdrop = document.getElementById('deactivateModalBackdrop');
        if (backdrop) {
            backdrop.classList.remove('show');
        }
    }

    // Close on backdrop click
    document.addEventListener('click', function(e) {
        const backdrop = document.getElementById('deactivateModalBackdrop');
        if (backdrop && e.target === backdrop) {
            closeDeactivateModal();
        }
    });

    // Close on Escape key
    window.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDeactivateModal();
        }
    });
</script>
@endpush
