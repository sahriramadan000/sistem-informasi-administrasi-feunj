@extends('layouts.app')

@section('title', 'Detail Surat')

@section('content')
    {{-- Page Header - Simple & Modern --}}
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6 pb-6 border-b-2 border-gray-200">
            {{-- Left: Title & Info --}}
            <div class="flex items-start gap-4">
                <div
                    class="flex-shrink-0 w-16 h-16 rounded-2xl bg-gradient-to-br from-orange-500 to-amber-500 flex items-center justify-center shadow-xl">
                    <i data-lucide="file-text" class="w-8 h-8 text-white"></i>
                </div>
                <div class="flex-1 pt-1">
                    <h1 class="text-3xl font-bold text-gray-900 mb-1">Detail Surat</h1>
                    <p class="text-base text-gray-600">Informasi lengkap surat yang dipilih</p>
                </div>
            </div>

            {{-- Right: Back Button --}}
            <div class="flex-shrink-0 sm:pt-1">
                <a href="{{ route('letters.index') }}"
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
                            <p class="text-sm font-medium text-orange-700 uppercase tracking-wide">Nomor Surat Resmi</p>
                            <p class="text-xs text-orange-600">Generated automatically</p>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl p-4 shadow-sm border border-orange-100">
                        <p id="letterNumber" class="text-2xl md:text-3xl font-bold text-gray-900 font-mono tracking-tight">
                            {{ $letter->letter_number }}
                        </p>
                    </div>
                </div>
                <div class="flex flex-col gap-3">
                    <button onclick="copyLetterNumber()"
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
                        <h3 class="text-lg font-semibold text-gray-900">Informasi Dasar</h3>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        {{-- Tanggal Surat --}}
                        <div class="flex items-start gap-4">
                            <div
                                class="flex-shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br from-green-100 to-emerald-100 flex items-center justify-center">
                                <i data-lucide="calendar" class="w-6 h-6 text-green-600"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Tanggal Surat</p>
                                <p class="text-base font-semibold text-gray-900">{{ $letter->letter_date->format('d F Y') }}
                                </p>
                            </div>
                        </div>

                        {{-- Status --}}
                        <div class="flex items-start gap-4">
                            <div
                                class="flex-shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br from-blue-100 to-cyan-100 flex items-center justify-center">
                                <i data-lucide="check-circle" class="w-6 h-6 text-blue-600"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Status</p>
                                <span
                                    class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-sm font-semibold
                                    @if ($letter->status == 'final') bg-green-100 text-green-800 border border-green-200
                                    @elseif($letter->status == 'draft') bg-yellow-100 text-yellow-800 border border-yellow-200
                                    @else bg-red-100 text-red-800 border border-red-200 @endif">
                                    @if ($letter->status == 'final')
                                        <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                    @elseif($letter->status == 'draft')
                                        <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                                    @else
                                        <i data-lucide="x" class="w-3.5 h-3.5"></i>
                                    @endif
                                    {{ ucfirst($letter->status) }}
                                </span>
                            </div>
                        </div>

                        {{-- Jenis Surat --}}
                        <div class="flex items-start gap-4">
                            <div
                                class="flex-shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br from-purple-100 to-violet-100 flex items-center justify-center">
                                <i data-lucide="file-type" class="w-6 h-6 text-purple-600"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Jenis Surat</p>
                                <p class="text-base font-semibold text-gray-900">{{ $letter->letterType->name }}</p>
                            </div>
                        </div>

                        {{-- Klasifikasi --}}
                        <div class="flex items-start gap-4">
                            <div
                                class="flex-shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br from-amber-100 to-orange-100 flex items-center justify-center">
                                <i data-lucide="folder" class="w-6 h-6 text-amber-600"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Klasifikasi</p>
                                <p class="text-base font-semibold text-gray-900">{{ $letter->classification->name }}</p>
                                <p class="text-sm text-gray-500 font-mono mt-0.5">{{ $letter->classification->code }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card Isi Surat --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-brand-lighter flex items-center justify-center">
                            <span class="text-brand font-bold">2</span>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">Isi Surat</h3>
                    </div>
                </div>
                <div class="p-6 space-y-5">
                    {{-- Perihal --}}
                    @if ($letter->subject)
                        <div>
                            <div class="flex items-center gap-2 mb-3">
                                <i data-lucide="align-left" class="w-4 h-4 text-gray-400"></i>
                                <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Perihal / Hal</h4>
                            </div>
                            <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-4 border border-gray-200">
                                <p class="text-gray-900 leading-relaxed">{{ $letter->subject }}</p>
                            </div>
                        </div>
                    @endif

                    {{-- Tujuan --}}
                    @if ($letter->recipient)
                        <div>
                            <div class="flex items-center gap-2 mb-3">
                                <i data-lucide="send" class="w-4 h-4 text-gray-400"></i>
                                <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Ditujukan Kepada
                                </h4>
                            </div>
                            <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-4 border border-gray-200">
                                <p class="text-gray-900 leading-relaxed">{{ $letter->recipient }}</p>
                            </div>
                        </div>
                    @endif

                    {{-- Keperluan Surat --}}
                    @if ($letter->letterPurpose)
                        <div>
                            <div class="flex items-center gap-2 mb-3">
                                <i data-lucide="clipboard-list" class="w-4 h-4 text-gray-400"></i>
                                <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Keperluan</h4>
                            </div>
                            <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-4 border border-gray-200">
                                <p class="text-gray-900 leading-relaxed">{{ $letter->letterPurpose->name }}</p>
                            </div>
                        </div>
                    @endif

                    {{-- Nama Mahasiswa --}}
                    @if ($letter->student_name)
                        <div>
                            <div class="flex items-center gap-2 mb-3">
                                <i data-lucide="user" class="w-4 h-4 text-gray-400"></i>
                                <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Nama Mahasiswa</h4>
                            </div>
                            <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-4 border border-gray-200">
                                <p class="text-gray-900 leading-relaxed font-medium">{{ $letter->student_name }}</p>
                            </div>
                        </div>
                    @endif
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
                            Quick Actions
                        </h3>
                        <div class="space-y-2">
                            {{-- Edit Button --}}
                            <a href="{{ route('letters.edit', $letter) }}"
                                class="flex items-center gap-3 px-4 py-3 bg-white rounded-lg border border-orange-200 hover:border-orange-300 hover:shadow-md transition-all duration-200 group">
                                <div
                                    class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                                    <i data-lucide="edit" class="w-4 h-4 text-blue-600"></i>
                                </div>
                                <span class="text-sm font-medium text-gray-700">Edit Surat</span>
                            </a>

                            {{-- Nonaktifkan Button --}}
                            <form id="deleteForm" method="POST" action="{{ route('letters.destroy', $letter) }}">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="showDeleteModal()"
                                    class="w-full flex items-center gap-3 px-4 py-3 bg-white rounded-lg border border-orange-200 hover:border-red-300 hover:shadow-md transition-all duration-200 group">
                                    <div
                                        class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center group-hover:bg-red-200 transition-colors">
                                        <i data-lucide="trash-2" class="w-4 h-4 text-red-600"></i>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">Nonaktifkan Surat</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Card Penandatangan --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="bg-gradient-to-r from-cyan-50 to-blue-50 px-6 py-4 border-b border-cyan-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-white shadow-sm flex items-center justify-center">
                            <i data-lucide="user-check" class="w-5 h-5 text-cyan-600"></i>
                        </div>
                        <h3 class="text-base font-semibold text-gray-900">Penandatangan</h3>
                    </div>
                </div>
                <div class="p-6">
                    <div class="text-center">
                        <div
                            class="w-20 h-20 rounded-full bg-gradient-to-br from-cyan-100 to-blue-200 flex items-center justify-center mx-auto mb-4 shadow-lg">
                            <i data-lucide="pen-tool" class="w-10 h-10 text-cyan-700"></i>
                        </div>
                        <p class="text-lg font-bold text-gray-900 mb-1">{{ $letter->signatory->name }}</p>
                        <p class="text-sm text-gray-600 mb-3">{{ $letter->signatory->position }}</p>
                        <div
                            class="inline-flex items-center gap-2 px-3 py-1.5 bg-cyan-50 rounded-lg border border-cyan-200">
                            <i data-lucide="shield-check" class="w-4 h-4 text-cyan-600"></i>
                            <span class="text-xs font-semibold text-cyan-700 uppercase">Verified</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card Metadata --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-white shadow-sm flex items-center justify-center">
                            <i data-lucide="info" class="w-5 h-5 text-gray-600"></i>
                        </div>
                        <h3 class="text-base font-semibold text-gray-900">Informasi Lainnya</h3>
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
                            <p class="text-sm font-semibold text-gray-900">{{ $letter->creator->name }}</p>
                        </div>
                    </div>

                    {{-- Tanggal dibuat --}}
                    <div class="flex items-start gap-3 pb-4 border-b border-gray-100">
                        <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center">
                            <i data-lucide="clock" class="w-5 h-5 text-green-600"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Tanggal Dibuat</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $letter->created_at->format('d/m/Y') }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $letter->created_at->format('H:i') }} WIB</p>
                        </div>
                    </div>

                    {{-- Terakhir diupdate --}}
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center">
                            <i data-lucide="edit" class="w-5 h-5 text-amber-600"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Terakhir Diupdate</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $letter->updated_at->format('d/m/Y') }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $letter->updated_at->format('H:i') }} WIB</p>
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
                        <p class="text-red-100 text-sm mt-0.5">Tindakan ini tidak dapat dibatalkan</p>
                    </div>
                </div>
            </div>

            {{-- Modal Body --}}
            <div class="p-6">
                <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-5">
                    <p class="text-gray-800 leading-relaxed">
                        Apakah Anda yakin ingin <span class="font-bold text-red-600">menonaktifkan</span> surat ini?
                    </p>
                    <div class="mt-3 pt-3 border-t border-red-200">
                        <p class="text-sm text-gray-600 flex items-center gap-2">
                            <i data-lucide="info" class="w-4 h-4 text-red-500"></i>
                            <span>Nomor Surat: <span
                                    class="font-mono font-semibold text-gray-900">{{ $letter->letter_number }}</span></span>
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
            // Copy letter number to clipboard
            function copyLetterNumber() {
                const letterNumber = document.getElementById('letterNumber').textContent.trim();
                const feedback = document.getElementById('copyFeedback');

                navigator.clipboard.writeText(letterNumber).then(function() {
                    // Show success feedback
                    feedback.classList.remove('opacity-0');
                    feedback.classList.add('opacity-100');

                    // Hide after 2 seconds
                    setTimeout(function() {
                        feedback.classList.remove('opacity-100');
                        feedback.classList.add('opacity-0');
                    }, 2000);
                }).catch(function(err) {
                    console.error('Failed to copy:', err);
                    alert('Gagal menyalin nomor surat');
                });
            }

            // Show delete modal with animation
            function showDeleteModal() {
                const modal = document.getElementById('deleteModal');
                const modalContent = document.getElementById('deleteModalContent');

                modal.classList.remove('hidden');

                // Trigger animation
                setTimeout(() => {
                    modalContent.classList.remove('scale-95', 'opacity-0');
                    modalContent.classList.add('scale-100', 'opacity-100');
                }, 10);

                // Re-initialize Lucide icons in modal
                lucide.createIcons();
            }

            // Hide delete modal with animation
            function hideDeleteModal() {
                const modal = document.getElementById('deleteModal');
                const modalContent = document.getElementById('deleteModalContent');

                modalContent.classList.remove('scale-100', 'opacity-100');
                modalContent.classList.add('scale-95', 'opacity-0');

                setTimeout(() => {
                    modal.classList.add('hidden');
                }, 200);
            }

            // Confirm delete and submit form
            function confirmDelete() {
                document.getElementById('deleteForm').submit();
            }

            // Close modal when clicking outside
            document.getElementById('deleteModal')?.addEventListener('click', function(e) {
                if (e.target === this) {
                    hideDeleteModal();
                }
            });

            // Close modal with ESC key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    hideDeleteModal();
                }
            });

            // Re-initialize Lucide icons
            lucide.createIcons();
        </script>
    @endpush
@endsection
