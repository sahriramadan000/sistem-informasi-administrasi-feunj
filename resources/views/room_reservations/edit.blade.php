@extends('layouts.app')

@section('title', 'Edit Peminjaman Ruang')

@section('content')
    <div class="max-w-4xl mx-auto">
        {{-- Header --}}
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                    <i data-lucide="edit-2" class="w-6 h-6 text-brand"></i>
                    Edit Peminjaman Ruang
                </h2>
                <p class="mt-1 text-sm text-gray-500">Perbarui informasi peminjaman atau status persetujuan.</p>
            </div>
            <a href="{{ route('room-reservations.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Kembali
            </a>
        </div>



        {{-- Form Section --}}
        <div class="bg-white rounded-lg shadow border border-gray-200">
            <div class="p-8">
                <form method="POST" action="{{ route('room-reservations.update', $roomReservation) }}">
                    @csrf
                    @method('PUT')

                    {{-- SECTION 1: Detail Ruangan dan Waktu --}}
                    <div class="mb-8">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="flex items-center justify-center w-10 h-10 rounded-full bg-brand-lighter">
                                <span class="text-brand font-bold text-lg">1</span>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Detail Waktu & Ruangan</h3>
                                <p class="text-sm text-gray-500">Ubah ruangan, tanggal, dan jam peminjaman</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pl-0 md:pl-13">
                            {{-- Ruangan --}}
                            <div class="space-y-2 md:col-span-2">
                                <label for="room_id" class="label flex items-center gap-2">
                                    <i data-lucide="map-pin" class="w-4 h-4 text-gray-400"></i>
                                    Pilih Ruangan <span class="text-destructive">*</span>
                                </label>
                                <select id="room_id" name="room_id" class="select @error('room_id') !border-red-500 @enderror" required>
                                    <option value="">-- Pilih Ruangan --</option>
                                    @foreach($rooms as $room)
                                        <option value="{{ $room->id }}" {{ old('room_id', $roomReservation->room_id) == $room->id ? 'selected' : '' }}>
                                            {{ $room->name }} (Kapasitas: {{ $room->capacity ?? '-' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('room_id')
                                    <p class="text-sm text-destructive flex items-center gap-1.5">
                                        <i data-lucide="alert-circle" class="w-4 h-4"></i> {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Tanggal --}}
                            <div class="space-y-2">
                                <label for="reservation_date" class="label flex items-center gap-2">
                                    <i data-lucide="calendar" class="w-4 h-4 text-gray-400"></i>
                                    Tanggal Peminjaman <span class="text-destructive">*</span>
                                </label>
                                <input type="text" id="reservation_date" name="reservation_date" 
                                    value="{{ old('reservation_date', $roomReservation->reservation_date->format('Y-m-d')) }}"
                                    class="input flatpickr-date @error('reservation_date') !border-red-500 @enderror"
                                    placeholder="Pilih tanggal" readonly required>
                                @error('reservation_date')
                                    <p class="text-sm text-destructive flex items-center gap-1.5">
                                        <i data-lucide="alert-circle" class="w-4 h-4"></i> {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                {{-- Jam Mulai --}}
                                <div class="space-y-2">
                                    <label for="start_time" class="label flex items-center gap-2">
                                        <i data-lucide="clock" class="w-4 h-4 text-gray-400"></i>
                                        Jam Mulai <span class="text-destructive">*</span>
                                    </label>
                                    <input type="text" id="start_time" name="start_time" 
                                        value="{{ old('start_time', \Carbon\Carbon::parse($roomReservation->start_time)->format('H:i')) }}"
                                        class="input flatpickr-time @error('start_time') !border-red-500 @enderror"
                                        placeholder="Pilih jam" readonly required>
                                </div>
                                {{-- Jam Selesai --}}
                                <div class="space-y-2">
                                    <label for="end_time" class="label flex items-center gap-2">
                                        <i data-lucide="clock" class="w-4 h-4 text-gray-400"></i>
                                        Jam Selesai <span class="text-destructive">*</span>
                                    </label>
                                    <input type="text" id="end_time" name="end_time" 
                                        value="{{ old('end_time', \Carbon\Carbon::parse($roomReservation->end_time)->format('H:i')) }}"
                                        class="input flatpickr-time @error('end_time') !border-red-500 @enderror"
                                        placeholder="Pilih jam" readonly required>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Divider --}}
                    <div class="border-t border-gray-200 my-8"></div>

                    {{-- SECTION 2: Informasi Kegiatan --}}
                    <div class="mb-8">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="flex items-center justify-center w-10 h-10 rounded-full bg-brand-lighter">
                                <span class="text-brand font-bold text-lg">2</span>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Informasi Kegiatan</h3>
                                <p class="text-sm text-gray-500">Ubah detail kegiatan, nama pemohon, dan kebutuhan lainnya</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pl-0 md:pl-13">
                            {{-- Nama Kegiatan --}}
                            <div class="space-y-2 md:col-span-2">
                                <label for="event_name" class="label flex items-center gap-2">
                                    <i data-lucide="align-left" class="w-4 h-4 text-gray-400"></i>
                                    Nama Kegiatan <span class="text-destructive">*</span>
                                </label>
                                <input type="text" id="event_name" name="event_name" value="{{ old('event_name', $roomReservation->event_name) }}"
                                    class="input @error('event_name') !border-red-500 @enderror" required>
                                @error('event_name')
                                    <p class="text-sm text-destructive flex items-center gap-1.5">
                                        <i data-lucide="alert-circle" class="w-4 h-4"></i> {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Nama Pemohon --}}
                            <div class="space-y-2 md:col-span-2">
                                <label for="requester_name" class="label flex items-center gap-2">
                                    <i data-lucide="user" class="w-4 h-4 text-gray-400"></i>
                                    Nama Pemohon <span class="text-destructive">*</span>
                                </label>
                                <input type="text" id="requester_name" name="requester_name" value="{{ old('requester_name', $roomReservation->requester_name) }}"
                                    class="input @error('requester_name') !border-red-500 @enderror" required>
                                @error('requester_name')
                                    <p class="text-sm text-destructive flex items-center gap-1.5">
                                        <i data-lucide="alert-circle" class="w-4 h-4"></i> {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Keterangan --}}
                            <div class="space-y-2 md:col-span-2">
                                <label for="remarks" class="label flex items-center gap-2">
                                    <i data-lucide="file-text" class="w-4 h-4 text-gray-400"></i>
                                    Keterangan Lainnya
                                </label>
                                <textarea id="remarks" name="remarks" rows="2"
                                    class="input @error('remarks') !border-red-500 @enderror">{{ old('remarks', $roomReservation->remarks) }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Form Actions --}}
                    <div class="flex justify-between items-center pt-6 border-t">
                        <a href="{{ route('room-reservations.index') }}" class="btn-outline gap-2">
                            <i data-lucide="arrow-left" class="w-4 h-4"></i>
                            Batal
                        </a>
                        <button type="submit" class="btn-primary gap-2 px-8 py-3 text-base">
                            <i data-lucide="save" class="w-5 h-5"></i>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function toggleRejectionReason(status) {
                const container = document.getElementById('rejection_reason_container');
                const textarea = document.getElementById('rejection_reason');
                if (status === 'rejected') {
                    container.style.display = 'block';
                    textarea.required = true;
                } else {
                    container.style.display = 'none';
                    textarea.required = false;
                }
            }

            document.addEventListener('DOMContentLoaded', function () {
                lucide.createIcons();

                // Select2
                $('#room_id').select2({
                    placeholder: '-- Pilih Ruangan --',
                    allowClear: true,
                    width: '100%'
                });

                // Flatpickr untuk Tanggal
                flatpickr(".flatpickr-date", {
                    dateFormat: "Y-m-d",
                    altInput: true,
                    altFormat: "d F Y",
                    locale: "id"
                });

                // Flatpickr untuk Waktu
                flatpickr(".flatpickr-time", {
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: "H:i",
                    time_24hr: true,
                    minuteIncrement: 15
                });
            });
        </script>
    @endpush
@endsection
