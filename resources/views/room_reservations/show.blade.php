@extends('layouts.app')

@section('title', 'Detail Peminjaman Ruang')

@section('content')
    {{-- Page Header - Simple & Modern --}}
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6 pb-6 border-b-2 border-gray-200">
            {{-- Left: Title & Info --}}
            <div class="flex items-start gap-4">
                <div
                    class="flex-shrink-0 w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-500 flex items-center justify-center shadow-xl">
                    <i data-lucide="file-text" class="w-8 h-8 text-white"></i>
                </div>
                <div class="flex-1 pt-1">
                    <h1 class="text-3xl font-bold text-gray-900 mb-1">Detail Peminjaman</h1>
                    <p class="text-base text-gray-600">Informasi lengkap permohonan peminjaman ruangan</p>
                </div>
            </div>

            {{-- Right: Back Button --}}
            <div class="flex-shrink-0 sm:pt-1">
                <a href="{{ route('room-reservations.index') }}"
                    class="inline-flex items-center gap-2.5 px-6 py-3 bg-gray-900 hover:bg-gray-800 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 group">
                    <i data-lucide="arrow-left"
                        class="w-5 h-5 group-hover:-translate-x-1 transition-transform duration-200"></i>
                    <span>Kembali</span>
                </a>
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
                        <h3 class="text-lg font-semibold text-gray-900">Jadwal & Status</h3>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        {{-- Tanggal Booking --}}
                        <div class="flex items-start gap-4">
                            <div
                                class="flex-shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br from-green-100 to-emerald-100 flex items-center justify-center">
                                <i data-lucide="calendar" class="w-6 h-6 text-green-600"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Tanggal Peminjaman</p>
                                <p class="text-base font-semibold text-gray-900">{{ \Carbon\Carbon::parse($roomReservation->reservation_date)->format('d F Y') }}</p>
                            </div>
                        </div>

                        {{-- Waktu --}}
                        <div class="flex items-start gap-4">
                            <div
                                class="flex-shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br from-blue-100 to-cyan-100 flex items-center justify-center">
                                <i data-lucide="clock" class="w-6 h-6 text-blue-600"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Waktu</p>
                                <p class="text-base font-semibold text-gray-900">
                                    {{ \Carbon\Carbon::parse($roomReservation->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($roomReservation->end_time)->format('H:i') }} WIB
                                </p>
                            </div>
                        </div>

                        {{-- Ruangan --}}
                        <div class="flex items-start gap-4">
                            <div
                                class="flex-shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br from-purple-100 to-violet-100 flex items-center justify-center">
                                <i data-lucide="map-pin" class="w-6 h-6 text-purple-600"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Ruangan</p>
                                <p class="text-base font-semibold text-gray-900">{{ $roomReservation->room->name ?? '-' }}</p>
                            </div>
                        </div>

                        {{-- Status --}}
                        <div class="flex items-start gap-4">
                            <div
                                class="flex-shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br from-amber-100 to-orange-100 flex items-center justify-center">
                                <i data-lucide="check-circle" class="w-6 h-6 text-amber-600"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Status</p>
                                <span
                                    class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-sm font-semibold
                                    @if ($roomReservation->status == 'approved') bg-green-100 text-green-800 border border-green-200
                                    @elseif($roomReservation->status == 'pending') bg-yellow-100 text-yellow-800 border border-yellow-200
                                    @elseif($roomReservation->status == 'rejected') bg-red-100 text-red-800 border border-red-200
                                    @else bg-gray-100 text-gray-800 border border-gray-200 @endif">
                                    @if ($roomReservation->status == 'approved')
                                        <i data-lucide="check" class="w-3.5 h-3.5"></i> Disetujui
                                    @elseif($roomReservation->status == 'pending')
                                        <i data-lucide="clock" class="w-3.5 h-3.5"></i> Menunggu
                                    @elseif($roomReservation->status == 'rejected')
                                        <i data-lucide="x" class="w-3.5 h-3.5"></i> Ditolak
                                    @else
                                        <i data-lucide="slash" class="w-3.5 h-3.5"></i> Dibatalkan
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    @if($roomReservation->status == 'rejected' && $roomReservation->rejection_reason)
                        <div class="mt-6 border-t border-red-100 bg-red-50 p-4 rounded-xl border border-red-200">
                            <div class="flex items-start gap-3">
                                <i data-lucide="alert-circle" class="w-5 h-5 text-red-600 mt-0.5"></i>
                                <div>
                                    <p class="text-sm font-semibold text-red-800 uppercase tracking-wide mb-1">Alasan Penolakan</p>
                                    <p class="text-red-700 leading-relaxed">{{ $roomReservation->rejection_reason }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Card Detail Kegiatan --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-brand-lighter flex items-center justify-center">
                            <span class="text-brand font-bold">2</span>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">Detail Kegiatan</h3>
                    </div>
                </div>
                <div class="p-6 space-y-5">
                    {{-- Nama Kegiatan --}}
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <i data-lucide="align-left" class="w-4 h-4 text-gray-400"></i>
                            <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Nama Kegiatan</h4>
                        </div>
                        <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-4 border border-gray-200">
                            <p class="text-gray-900 leading-relaxed font-semibold">{{ $roomReservation->event_name }}</p>
                        </div>
                    </div>



                    {{-- Keterangan --}}
                    @if ($roomReservation->remarks)
                        <div>
                            <div class="flex items-center gap-2 mb-3">
                                <i data-lucide="file-text" class="w-4 h-4 text-gray-400"></i>
                                <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Keterangan Tambahan</h4>
                            </div>
                            <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-4 border border-gray-200">
                                <p class="text-gray-900 leading-relaxed whitespace-pre-line">{{ $roomReservation->remarks }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- SECTION 2: Sidebar Info --}}
        <div class="space-y-6">
            {{-- Quick Actions Card --}}
            @if (auth()->user()->isAdmin() || (auth()->id() === $roomReservation->created_by))
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl border border-blue-200 shadow-sm overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <i data-lucide="zap" class="w-4 h-4 text-blue-600"></i>
                            Quick Actions
                        </h3>
                        <div class="space-y-2">
                            {{-- Edit Button --}}
                            <a href="{{ route('room-reservations.edit', $roomReservation) }}"
                                class="flex items-center gap-3 px-4 py-3 bg-white rounded-lg border border-blue-200 hover:border-blue-300 hover:shadow-md transition-all duration-200 group">
                                <div
                                    class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center group-hover:bg-amber-200 transition-colors">
                                    <i data-lucide="edit" class="w-4 h-4 text-amber-600"></i>
                                </div>
                                <span class="text-sm font-medium text-gray-700">Edit Permohonan</span>
                            </a>

                            {{-- Hapus Button --}}
                            <form id="deleteForm" method="POST" action="{{ route('room-reservations.destroy', $roomReservation) }}">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="showDeleteModal()"
                                    class="w-full flex items-center gap-3 px-4 py-3 bg-white rounded-lg border border-blue-200 hover:border-red-300 hover:shadow-md transition-all duration-200 group">
                                    <div
                                        class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center group-hover:bg-red-200 transition-colors">
                                        <i data-lucide="trash-2" class="w-4 h-4 text-red-600"></i>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">Hapus Permohonan</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Card Pemohon --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-white shadow-sm flex items-center justify-center">
                            <i data-lucide="user" class="w-5 h-5 text-gray-600"></i>
                        </div>
                        <h3 class="text-base font-semibold text-gray-900">Pemohon</h3>
                    </div>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Nama Pemohon / Instansi</p>
                            <p class="text-base font-bold text-gray-900">{{ $roomReservation->requester_name }}</p>
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
                        <h3 class="text-base font-semibold text-gray-900">Riwayat</h3>
                    </div>
                </div>
                <div class="p-6 space-y-4">
                    {{-- Dibuat oleh --}}
                    <div class="flex items-start gap-3 pb-4 border-b border-gray-100">
                        <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center">
                            <i data-lucide="user-plus" class="w-5 h-5 text-indigo-600"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Dibuat Oleh</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $roomReservation->creator->name ?? 'Sistem' }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $roomReservation->created_at->format('d/m/Y H:i') }} WIB</p>
                        </div>
                    </div>

                    {{-- Terakhir diupdate --}}
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center">
                            <i data-lucide="check-circle" class="w-5 h-5 text-green-600"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Diproses / Update Oleh</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $roomReservation->approver->name ?? '-' }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $roomReservation->updated_at->format('d/m/Y H:i') }} WIB</p>
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
                        <h3 class="text-xl font-bold text-white">Konfirmasi Hapus</h3>
                        <p class="text-red-100 text-sm mt-0.5">Tindakan ini tidak dapat dibatalkan</p>
                    </div>
                </div>
            </div>

            {{-- Modal Body --}}
            <div class="p-6">
                <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-5">
                    <p class="text-gray-800 leading-relaxed">
                        Apakah Anda yakin ingin <span class="font-bold text-red-600">menghapus</span> permohonan peminjaman ruangan ini?
                    </p>
                    <div class="mt-3 pt-3 border-t border-red-200">
                        <p class="text-sm text-gray-600 flex flex-col gap-1">
                            <span>Kegiatan: <span class="font-semibold text-gray-900">{{ $roomReservation->event_name }}</span></span>
                            <span>Ruangan: <span class="font-semibold text-gray-900">{{ $roomReservation->room->name ?? '-' }}</span></span>
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
                        <span>Ya, Hapus</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Show delete modal with animation
            function showDeleteModal() {
                const modal = document.getElementById('deleteModal');
                const modalContent = document.getElementById('deleteModalContent');
                modal.classList.remove('hidden');
                setTimeout(() => {
                    modalContent.classList.remove('scale-95', 'opacity-0');
                    modalContent.classList.add('scale-100', 'opacity-100');
                }, 10);
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
