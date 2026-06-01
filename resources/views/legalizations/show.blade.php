@extends('layouts.app')

@section('title', 'Detail Legalisir')

@section('content')
    {{-- Page Header - Simple & Modern --}}
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6 pb-6 border-b-2 border-gray-200">
            {{-- Left: Title & Info --}}
            <div class="flex items-start gap-4">
                <div
                    class="flex-shrink-0 w-16 h-16 rounded-2xl bg-gradient-to-br from-orange-500 to-amber-500 flex items-center justify-center shadow-xl">
                    <i data-lucide="file-check-2" class="w-8 h-8 text-white"></i>
                </div>
                <div class="flex-1 pt-1">
                    <h1 class="text-3xl font-bold text-gray-900 mb-1">Detail Legalisir</h1>
                    <p class="text-base text-gray-600">Informasi lengkap transaksi legalisir yang dipilih</p>
                </div>
            </div>

            {{-- Right: Back Button --}}
            <div class="flex-shrink-0 sm:pt-1">
                <a href="{{ route('legalizations.index') }}"
                    class="inline-flex items-center gap-2.5 px-6 py-3 bg-gray-900 hover:bg-gray-800 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 group">
                    <i data-lucide="arrow-left"
                        class="w-5 h-5 group-hover:-translate-x-1 transition-transform duration-200"></i>
                    <span>Kembali</span>
                </a>
            </div>
        </div>
    </div>

    {{-- NOMOR SURAT CARD - Highlight dengan Copy Button --}}
    <div
        class="bg-gradient-to-br from-orange-50 via-amber-50 to-yellow-50 rounded-2xl border-2 border-orange-200 shadow-lg overflow-hidden mb-6">
        <div class="p-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-12 h-12 rounded-xl bg-white shadow-md flex items-center justify-center">
                            <i data-lucide="hash" class="w-6 h-6 text-brand"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-orange-700 uppercase tracking-wide">Nomor Seri Legalisir</p>
                            <p class="text-xs text-orange-600">Generated automatically</p>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl p-4 shadow-sm border border-orange-200 inline-block">
                        <p id="runningNumber" class="text-2xl md:text-3xl font-bold text-gray-900 font-mono tracking-tight">
                            {{ str_pad($legalization->running_number, 2, '0', STR_PAD_LEFT) }}
                        </p>
                    </div>
                </div>
                <div class="flex flex-col gap-3">
                    <button onclick="copyRunningNumber()"
                        class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-white border-2 border-orange-300 rounded-xl text-brand font-semibold hover:bg-orange-50 hover:border-orange-400 transition-all duration-200 shadow-md hover:shadow-lg">
                        <i data-lucide="copy" class="w-5 h-5"></i>
                        <span>Copy Nomor</span>
                    </button>
                    <p id="copyFeedback"
                        class="text-sm text-center text-green-600 font-medium opacity-0 transition-opacity duration-300">
                        ✓ Berhasil disalin!
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        {{-- SECTION 1: Informasi Utama --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Card Informasi Dasar --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-brand-lighter flex items-center justify-center">
                            <span class="text-brand font-bold">1</span>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">Informasi Pemohon</h3>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        {{-- Nama Alumni --}}
                        <div class="flex items-start gap-4">
                            <div
                                class="flex-shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br from-blue-100 to-cyan-100 flex items-center justify-center">
                                <i data-lucide="user" class="w-6 h-6 text-blue-600"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Nama Alumni</p>
                                <p class="text-base font-semibold text-gray-900">{{ $legalization->alumni_name }}</p>
                            </div>
                        </div>

                        {{-- Tanggal Permohonan --}}
                        <div class="flex items-start gap-4">
                            <div
                                class="flex-shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br from-green-100 to-emerald-100 flex items-center justify-center">
                                <i data-lucide="calendar" class="w-6 h-6 text-green-600"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Tanggal Transaksi
                                </p>
                                <p class="text-base font-semibold text-gray-900">{{ $legalization->date->format('d F Y') }}
                                </p>
                            </div>
                        </div>

                        {{-- Jenjang --}}
                        <div class="flex items-start gap-4">
                            <div
                                class="flex-shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br from-purple-100 to-violet-100 flex items-center justify-center">
                                <i data-lucide="graduation-cap" class="w-6 h-6 text-purple-600"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Jenjang Pendidikan
                                </p>
                                <p class="text-base font-semibold text-gray-900">
                                    {{ $legalization->educationLevel->name ?? '-' }}</p>
                            </div>
                        </div>

                        {{-- Tahun Lulus --}}
                        <div class="flex items-start gap-4">
                            <div
                                class="flex-shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br from-amber-100 to-orange-100 flex items-center justify-center">
                                <i data-lucide="book-open" class="w-6 h-6 text-amber-600"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Tahun Kelulusan
                                </p>
                                <p class="text-base font-semibold text-gray-900">{{ $legalization->graduation_year }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card Detail Biaya --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-brand-lighter flex items-center justify-center">
                            <span class="text-brand font-bold">2</span>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">Rincian Transaksi</h3>
                    </div>
                </div>
                <div class="p-6">
                    <div
                        class="bg-gradient-to-br from-orange-50/50 to-white rounded-2xl p-6 border border-orange-200 shadow-sm relative overflow-hidden">
                        <div
                            class="absolute right-0 top-0 w-32 h-32 bg-orange-50 rounded-full blur-2xl translate-x-1/2 -translate-y-1/2">
                        </div>
                        <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
                            <div class="flex items-center gap-5 w-full md:w-auto">
                                <div
                                    class="w-14 h-14 rounded-2xl bg-white shadow-sm flex items-center justify-center border border-orange-200 flex-shrink-0">
                                    <i data-lucide="receipt" class="w-7 h-7 text-brand"></i>
                                </div>
                                <div class="w-full">
                                    <div class="flex justify-between items-center mb-1">
                                        <p class="text-sm font-semibold text-orange-800 uppercase tracking-wide">Rincian</p>
                                    </div>
                                    <p class="text-gray-600 text-sm">
                                        {{ $legalization->page_count }} Lembar × Rp
                                        {{ number_format($legalization->educationLevel->price_per_page ?? 0, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                            <div
                                class="text-right w-full md:w-auto mt-4 md:mt-0 pt-4 md:pt-0 border-t md:border-t-0 border-orange-200/50">
                                <p class="text-sm font-medium text-orange-700 mb-1">Total Tagihan</p>
                                <p class="text-3xl font-bold text-gray-900">
                                    Rp <span
                                        id="display-total">{{ number_format($legalization->total_price, 0, ',', '.') }}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- SECTION 2: Sidebar Info --}}
        <div class="space-y-6">
            {{-- Quick Actions Card --}}
            @if (auth()->user()->isAdmin() || auth()->user()->isOperator())
                <div
                    class="bg-gradient-to-br from-orange-50 to-amber-50 rounded-xl border border-orange-200 shadow-sm overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <i data-lucide="zap" class="w-4 h-4 text-orange-600"></i>
                            Aksi Cepat
                        </h3>
                        <div class="space-y-2">
                            {{-- Edit Button --}}
                            <a href="{{ route('legalizations.edit', $legalization) }}"
                                class="flex items-center gap-3 px-4 py-3 bg-white rounded-lg border border-orange-200 hover:border-orange-300 hover:shadow-md transition-all duration-200 group">
                                <div
                                    class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                                    <i data-lucide="edit" class="w-4 h-4 text-blue-600"></i>
                                </div>
                                <span class="text-sm font-medium text-gray-700">Edit Data</span>
                            </a>

                            {{-- Nonaktifkan Button --}}
                            <form id="deleteForm" method="POST"
                                action="{{ route('legalizations.destroy', $legalization) }}">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="showDeleteModal()"
                                    class="w-full flex items-center gap-3 px-4 py-3 bg-white rounded-lg border border-orange-200 hover:border-red-300 hover:shadow-md transition-all duration-200 group">
                                    <div
                                        class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center group-hover:bg-red-200 transition-colors">
                                        <i data-lucide="trash-2" class="w-4 h-4 text-red-600"></i>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">Nonaktifkan</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Card Metadata --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-white shadow-sm flex items-center justify-center">
                            <i data-lucide="info" class="w-5 h-5 text-gray-600"></i>
                        </div>
                        <h3 class="text-base font-semibold text-gray-900">Informasi Sistem</h3>
                    </div>
                </div>
                <div class="p-6 space-y-4">
                    {{-- Dibuat oleh --}}
                    <div class="flex items-start gap-3 pb-4 border-b border-gray-100">
                        <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center">
                            <i data-lucide="user" class="w-5 h-5 text-indigo-600"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Dibuat Oleh</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $legalization->creator->name ?? '-' }}</p>
                        </div>
                    </div>

                    {{-- Tanggal dibuat --}}
                    <div class="flex items-start gap-3 pb-4 border-b border-gray-100">
                        <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center">
                            <i data-lucide="clock" class="w-5 h-5 text-green-600"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Tanggal Dibuat</p>
                            <p class="text-sm font-semibold text-gray-900">
                                {{ $legalization->created_at->format('d/m/Y') }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $legalization->created_at->format('H:i') }} WIB</p>
                        </div>
                    </div>

                    {{-- Terakhir diupdate --}}
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center">
                            <i data-lucide="edit" class="w-5 h-5 text-amber-600"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Terakhir Diupdate</p>
                            <p class="text-sm font-semibold text-gray-900">
                                {{ $legalization->updated_at->format('d/m/Y') }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $legalization->updated_at->format('H:i') }} WIB</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div id="deleteModal"
        class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center p-4 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full transform transition-all scale-95 opacity-0"
            id="deleteModalContent">
            {{-- Modal Header --}}
            <div class="bg-gradient-to-r from-red-500 to-red-600 px-6 py-5 rounded-t-2xl">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center">
                        <i data-lucide="alert-triangle" class="w-6 h-6 text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white">Konfirmasi Nonaktifkan</h3>
                        <p class="text-red-100 text-sm mt-0.5">Tindakan ini menyembunyikan data (Soft Delete)</p>
                    </div>
                </div>
            </div>

            {{-- Modal Body --}}
            <div class="p-6">
                <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-5">
                    <p class="text-gray-800 leading-relaxed">
                        Apakah Anda yakin ingin <span class="font-bold text-red-600">menonaktifkan</span> legalisir ini?
                    </p>
                    <div class="mt-3 pt-3 border-t border-red-200">
                        <p class="text-sm text-gray-600 flex items-center gap-2">
                            <i data-lucide="info" class="w-4 h-4 text-red-500"></i>
                            <span>Nomor Legalisir: <span
                                    class="font-mono font-semibold text-gray-900">{{ str_pad($legalization->running_number, 4, '0', STR_PAD_LEFT) }}</span></span>
                        </p>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex gap-3">
                    <button type="button" onclick="hideDeleteModal()"
                        class="flex-1 px-5 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-all duration-200 border border-gray-300">
                        <span>Batal</span>
                    </button>
                    <button type="button" onclick="confirmDelete()"
                        class="flex-1 px-5 py-3 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-semibold rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl">
                        <span>Ya, Nonaktifkan</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Copy running number to clipboard
            function copyRunningNumber() {
                const num = document.getElementById('runningNumber').textContent.trim();
                const feedback = document.getElementById('copyFeedback');

                navigator.clipboard.writeText(num).then(function() {
                    feedback.classList.remove('opacity-0');
                    feedback.classList.add('opacity-100');
                    setTimeout(function() {
                        feedback.classList.remove('opacity-100');
                        feedback.classList.add('opacity-0');
                    }, 2000);
                }).catch(function(err) {
                    alert('Gagal menyalin nomor legalisir');
                });
            }

            function showDeleteModal() {
                const modal = document.getElementById('deleteModal');
                const modalContent = document.getElementById('deleteModalContent');
                modal.classList.remove('hidden');
                setTimeout(() => {
                    modalContent.classList.remove('scale-95', 'opacity-0');
                    modalContent.classList.add('scale-100', 'opacity-100');
                }, 10);
            }

            function hideDeleteModal() {
                const modal = document.getElementById('deleteModal');
                const modalContent = document.getElementById('deleteModalContent');
                modalContent.classList.remove('scale-100', 'opacity-100');
                modalContent.classList.add('scale-95', 'opacity-0');
                setTimeout(() => {
                    modal.classList.add('hidden');
                }, 200);
            }

            function confirmDelete() {
                document.getElementById('deleteForm').submit();
            }

            document.getElementById('deleteModal')?.addEventListener('click', function(e) {
                if (e.target === this) hideDeleteModal();
            });
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') hideDeleteModal();
            });
            lucide.createIcons();
        </script>
    @endpush
@endsection
