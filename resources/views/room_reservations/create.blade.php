@extends('layouts.app')

@section('title', 'Buat Peminjaman Ruang - FEB UNJ')

@push('styles')
    <style>
        /* Scoped macOS / iOS Form Enhancements */
        .mac-card {
            background: #ffffff;
            border-radius: 1.5rem;
            border: 1px solid rgba(226, 232, 240, 0.85);
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.03), 0 2px 6px -1px rgba(0, 0, 0, 0.02);
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }

        /* macOS Inset Step Badge */
        .mac-step-badge {
            width: 36px;
            height: 36px;
            border-radius: 12px;
            background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%);
            border: 1px solid #fed7aa;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ea580c;
            font-weight: 700;
            font-size: 0.925rem;
            box-shadow: 0 2px 6px rgba(255, 77, 0, 0.08);
            flex-shrink: 0;
        }

        /* Live Booking Summary Warm Orange Apple Card */
        .summary-orange-card {
            background: linear-gradient(145deg, #fff7ed 0%, #fffaf5 50%, #ffedd5 100%);
            border-radius: 1.25rem;
            border: 1.5px solid #fed7aa;
            box-shadow: 0 8px 24px -4px rgba(255, 77, 0, 0.08), 0 2px 6px -1px rgba(255, 77, 0, 0.04);
        }

        /* Quick Duration Chips */
        .duration-chip {
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 600;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #64748b;
            transition: all 0.15s ease;
            cursor: pointer;
        }
        .duration-chip:hover, .duration-chip.active {
            background: #fff7ed;
            border-color: #fed7aa;
            color: #c2410c;
        }

        /* Select2 Apple Input Override */
        .select2-container--default .select2-selection--single {
            height: 44px !important;
            border-radius: 0.75rem !important;
            border: 1px solid #e5e7eb !important;
            background-color: #ffffff !important;
            display: flex !important;
            align-items: center !important;
            padding: 0 0.75rem !important;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.03) !important;
            transition: all 0.15s ease !important;
        }
        .select2-container--default.select2-container--focus .select2-selection--single,
        .select2-container--default.select2-container--open .select2-selection--single {
            border-color: #FF4D00 !important;
            box-shadow: 0 0 0 4px rgba(255, 77, 0, 0.1) !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #111827 !important;
            font-size: 0.875rem !important;
            padding-left: 0 !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 42px !important;
            right: 10px !important;
        }
        .select2-dropdown {
            border-radius: 0.875rem !important;
            border: 1px solid #e5e7eb !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08) !important;
            overflow: hidden !important;
        }
    </style>
@endpush

@section('content')
    <div class="max-w-4xl mx-auto space-y-6 pb-12 relative">
        {{-- Page Header (Apple macOS Toolbar Style) --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-gray-200/80">
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-[#FF4D00] to-amber-500 flex items-center justify-center text-white shadow-xs flex-shrink-0">
                    <i data-lucide="calendar-plus" class="w-5 h-5"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">Buat Peminjaman Ruang</h1>
                    <p class="text-xs sm:text-sm text-gray-500">Ajukan jadwal penggunaan ruang sidang, aula, atau fasilitas FEB UNJ</p>
                </div>
            </div>

            <div class="flex items-center gap-2.5">
                <a href="{{ route('room-reservations.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 text-xs sm:text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-200 rounded-full shadow-2xs hover:shadow-xs transition-all active:scale-[0.98]">
                    <i data-lucide="arrow-left" class="w-4 h-4 text-gray-500"></i>
                    <span>Kembali</span>
                </a>
            </div>
        </div>

        {{-- Main Form Card --}}
        <div class="mac-card overflow-hidden">
            <form method="POST" action="{{ route('room-reservations.store') }}" id="formReservation" class="p-6 sm:p-8 space-y-8">
                @csrf

                {{-- ── SECTION 1: Detail Ruangan & Waktu ── --}}
                <div class="space-y-5">
                    <div class="flex items-center gap-3 pb-3 border-b border-gray-100">
                        <div class="mac-step-badge">1</div>
                        <div>
                            <h2 class="text-base font-bold text-gray-900 tracking-tight">Detail Waktu &amp; Ruangan</h2>
                            <p class="text-xs text-gray-500">Tentukan ruangan, tanggal, dan rentang jam peminjaman</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                        {{-- Ruangan --}}
                        <div class="sm:col-span-2 space-y-1.5">
                            <label for="room_id" class="text-xs font-semibold text-gray-700 flex items-center gap-1.5">
                                <i data-lucide="map-pin" class="w-3.5 h-3.5 text-gray-400"></i>
                                Pilih Ruangan <span class="text-rose-500">*</span>
                            </label>
                            <select id="room_id" name="room_id" class="w-full @error('room_id') !border-rose-500 @enderror" required>
                                <option value="">-- Pilih Ruangan --</option>
                                @foreach($rooms as $room)
                                    <option value="{{ $room->id }}" data-name="{{ $room->name }}" data-capacity="{{ $room->capacity ?? '-' }}"
                                        {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                        {{ $room->name }} (Kapasitas: {{ $room->capacity ?? '-' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('room_id')
                                <p class="text-[11px] text-rose-600 flex items-center gap-1 mt-1">
                                    <i data-lucide="alert-circle" class="w-3 h-3"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Tanggal Peminjaman --}}
                        <div class="space-y-1.5">
                            <label for="reservation_date" class="text-xs font-semibold text-gray-700 flex items-center gap-1.5">
                                <i data-lucide="calendar" class="w-3.5 h-3.5 text-gray-400"></i>
                                Tanggal Peminjaman <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" id="reservation_date" name="reservation_date"
                                value="{{ old('reservation_date', today()->format('Y-m-d')) }}"
                                class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-orange-500/10 focus:border-[#FF4D00] shadow-2xs transition-all placeholder:text-gray-400 cursor-pointer flatpickr-date @error('reservation_date') !border-rose-500 !ring-rose-500/10 @enderror"
                                placeholder="Pilih tanggal" readonly required>
                            @error('reservation_date')
                                <p class="text-[11px] text-rose-600 flex items-center gap-1 mt-1">
                                    <i data-lucide="alert-circle" class="w-3 h-3"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Jam Mulai & Jam Selesai --}}
                        <div class="grid grid-cols-2 gap-3">
                            <div class="space-y-1.5">
                                <label for="start_time" class="text-xs font-semibold text-gray-700 flex items-center gap-1.5">
                                    <i data-lucide="clock" class="w-3.5 h-3.5 text-gray-400"></i>
                                    Jam Mulai <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" id="start_time" name="start_time"
                                    value="{{ old('start_time', '08:00') }}"
                                    class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-orange-500/10 focus:border-[#FF4D00] shadow-2xs transition-all placeholder:text-gray-400 cursor-pointer flatpickr-time @error('start_time') !border-rose-500 !ring-rose-500/10 @enderror"
                                    placeholder="08:00" readonly required>
                                @error('start_time')
                                    <p class="text-[11px] text-rose-600 flex items-center gap-1 mt-1">
                                        <i data-lucide="alert-circle" class="w-3 h-3"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div class="space-y-1.5">
                                <label for="end_time" class="text-xs font-semibold text-gray-700 flex items-center gap-1.5">
                                    <i data-lucide="clock-4" class="w-3.5 h-3.5 text-gray-400"></i>
                                    Jam Selesai <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" id="end_time" name="end_time"
                                    value="{{ old('end_time', '10:00') }}"
                                    class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-orange-500/10 focus:border-[#FF4D00] shadow-2xs transition-all placeholder:text-gray-400 cursor-pointer flatpickr-time @error('end_time') !border-rose-500 !ring-rose-500/10 @enderror"
                                    placeholder="10:00" readonly required>
                                @error('end_time')
                                    <p class="text-[11px] text-rose-600 flex items-center gap-1 mt-1">
                                        <i data-lucide="alert-circle" class="w-3 h-3"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                        {{-- Quick Duration Chips --}}
                        <div class="sm:col-span-2 flex flex-wrap items-center gap-2">
                            <span class="text-[11px] font-semibold text-gray-400">Durasi Cepat:</span>
                            <button type="button" onclick="applyQuickDuration(60)" class="duration-chip">+1 Jam</button>
                            <button type="button" onclick="applyQuickDuration(120)" class="duration-chip active">+2 Jam</button>
                            <button type="button" onclick="applyQuickDuration(180)" class="duration-chip">+3 Jam</button>
                            <button type="button" onclick="applyFullDay()" class="duration-chip">Seharian (08:00 - 16:00)</button>
                        </div>
                    </div>

                    {{-- Live Schedule Summary Box (Apple Warm Orange Card) --}}
                    <div class="summary-orange-card p-5">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-xl bg-white/90 border border-orange-200 flex items-center justify-center text-[#FF4D00] shadow-2xs flex-shrink-0">
                                    <i data-lucide="building-2" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <span class="text-[11px] font-bold uppercase tracking-wider text-orange-700">Ringkasan Jadwal Peminjaman</span>
                                    <p class="text-sm font-bold text-gray-900 mt-0.5" id="preview_room">Belum memilih ruangan</p>
                                    <p class="text-xs text-gray-600 mt-0.5" id="preview_schedule">-</p>
                                </div>
                            </div>

                            <div class="text-left sm:text-right border-t sm:border-t-0 pt-3 sm:pt-0 border-orange-200/70">
                                <p class="text-lg sm:text-xl font-extrabold text-gray-900 tracking-tight font-mono" id="preview_duration">
                                    2 Jam
                                </p>
                                <p class="text-[11px] text-gray-500 mt-0.5" id="preview_duration_note">Estimasi durasi peminjaman</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── SECTION 2: Informasi Kegiatan ── --}}
                <div class="space-y-5">
                    <div class="flex items-center gap-3 pb-3 border-b border-gray-100">
                        <div class="mac-step-badge">2</div>
                        <div>
                            <h2 class="text-base font-bold text-gray-900 tracking-tight">Informasi Kegiatan &amp; Pemohon</h2>
                            <p class="text-xs text-gray-500">Masukkan nama kegiatan, penanggung jawab, dan catatan kebutuhan lainnya</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                        {{-- Nama Kegiatan --}}
                        <div class="sm:col-span-2 space-y-1.5">
                            <label for="event_name" class="text-xs font-semibold text-gray-700 flex items-center gap-1.5">
                                <i data-lucide="align-left" class="w-3.5 h-3.5 text-gray-400"></i>
                                Nama Kegiatan <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" id="event_name" name="event_name" value="{{ old('event_name') }}"
                                class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-orange-500/10 focus:border-[#FF4D00] shadow-2xs transition-all placeholder:text-gray-400 @error('event_name') !border-rose-500 !ring-rose-500/10 @enderror"
                                placeholder="Misal: Ujian Sidang Skripsi atas nama..." required>
                            @error('event_name')
                                <p class="text-[11px] text-rose-600 flex items-center gap-1 mt-1">
                                    <i data-lucide="alert-circle" class="w-3 h-3"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Nama Pemohon --}}
                        <div class="sm:col-span-2 space-y-1.5">
                            <label for="requester_name" class="text-xs font-semibold text-gray-700 flex items-center gap-1.5">
                                <i data-lucide="user" class="w-3.5 h-3.5 text-gray-400"></i>
                                Nama Pemohon / Penanggung Jawab <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" id="requester_name" name="requester_name" value="{{ old('requester_name') }}"
                                class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-orange-500/10 focus:border-[#FF4D00] shadow-2xs transition-all placeholder:text-gray-400 @error('requester_name') !border-rose-500 !ring-rose-500/10 @enderror"
                                placeholder="Nama pribadi, unit kerja, atau organisasi..." required>
                            @error('requester_name')
                                <p class="text-[11px] text-rose-600 flex items-center gap-1 mt-1">
                                    <i data-lucide="alert-circle" class="w-3 h-3"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Keterangan --}}
                        <div class="sm:col-span-2 space-y-1.5">
                            <label for="remarks" class="text-xs font-semibold text-gray-700 flex items-center gap-1.5">
                                <i data-lucide="file-text" class="w-3.5 h-3.5 text-gray-400"></i>
                                Keterangan Lainnya <span class="text-gray-400 font-normal">(Opsional)</span>
                            </label>
                            <textarea id="remarks" name="remarks" rows="3"
                                class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-orange-500/10 focus:border-[#FF4D00] shadow-2xs transition-all placeholder:text-gray-400 @error('remarks') !border-rose-500 !ring-rose-500/10 @enderror"
                                placeholder="Catatan tambahan, kebutuhan proyektor, tata letak kursi, dll...">{{ old('remarks') }}</textarea>
                            @error('remarks')
                                <p class="text-[11px] text-rose-600 flex items-center gap-1 mt-1">
                                    <i data-lucide="alert-circle" class="w-3 h-3"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Form Actions Bar --}}
                <div class="flex items-center justify-between pt-6 border-t border-gray-100">
                    <a href="{{ route('room-reservations.index') }}"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-white hover:bg-gray-100 border border-gray-200 text-gray-700 font-semibold text-xs sm:text-sm rounded-xl shadow-2xs transition-all active:scale-[0.98]">
                        <i data-lucide="arrow-left" class="w-4 h-4"></i>
                        <span>Batal</span>
                    </a>

                    <button type="submit" id="btnSubmit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-[#FF4D00] to-amber-500 hover:from-[#e04400] hover:to-[#d97706] text-white font-bold text-xs sm:text-sm rounded-xl shadow-xs hover:shadow-md transition-all active:scale-[0.98] cursor-pointer">
                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                        <span>Simpan &amp; Ajukan Permohonan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let startTimePicker = null;
        let endTimePicker = null;

        function updateScheduleSummary() {
            const roomSelect = document.getElementById('room_id');
            const dateInput = document.getElementById('reservation_date');
            const startInput = document.getElementById('start_time');
            const endInput = document.getElementById('end_time');

            const previewRoom = document.getElementById('preview_room');
            const previewSchedule = document.getElementById('preview_schedule');
            const previewDuration = document.getElementById('preview_duration');
            const previewNote = document.getElementById('preview_duration_note');

            if (roomSelect && previewRoom) {
                const selectedOpt = roomSelect.options[roomSelect.selectedIndex];
                previewRoom.textContent = (selectedOpt && selectedOpt.value)
                    ? selectedOpt.text.trim()
                    : 'Belum memilih ruangan';
            }

            const rawDate = dateInput?.value || '';
            const startVal = startInput?.value || '08:00';
            const endVal = endInput?.value || '10:00';

            let formattedDate = rawDate;
            if (rawDate) {
                const parsed = new Date(rawDate + 'T00:00:00');
                if (!isNaN(parsed.getTime())) {
                    formattedDate = parsed.toLocaleDateString('id-ID', {
                        weekday: 'long',
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric'
                    });
                }
            }

            if (previewSchedule) {
                previewSchedule.textContent = `${formattedDate || '-'}  •  Pukul ${startVal} - ${endVal} WIB`;
            }

            if (previewDuration && previewNote) {
                const [sh, sm] = startVal.split(':').map(Number);
                const [eh, em] = endVal.split(':').map(Number);
                const diffMinutes = (eh * 60 + em) - (sh * 60 + sm);

                if (isNaN(diffMinutes) || diffMinutes <= 0) {
                    previewDuration.textContent = 'Waktu Tidak Valid';
                    previewDuration.className = 'text-base sm:text-lg font-extrabold text-rose-600 tracking-tight';
                    previewNote.textContent = 'Jam selesai harus lebih besar dari jam mulai';
                } else {
                    const hrs = Math.floor(diffMinutes / 60);
                    const mins = diffMinutes % 60;
                    const labelParts = [];
                    if (hrs > 0) labelParts.push(`${hrs} Jam`);
                    if (mins > 0) labelParts.push(`${mins} Menit`);
                    previewDuration.textContent = labelParts.join(' ');
                    previewDuration.className = 'text-lg sm:text-xl font-extrabold text-gray-900 tracking-tight font-mono';
                    previewNote.textContent = 'Estimasi durasi peminjaman';
                }
            }
        }

        function applyQuickDuration(minutesToAdd) {
            const startInput = document.getElementById('start_time');
            const endInput = document.getElementById('end_time');
            if (!startInput || !endInput) return;

            const [sh, sm] = (startInput.value || '08:00').split(':').map(Number);
            let totalMins = (sh * 60 + sm) + minutesToAdd;
            if (totalMins > 23 * 60 + 45) totalMins = 23 * 60 + 45;

            const eh = String(Math.floor(totalMins / 60)).padStart(2, '0');
            const em = String(totalMins % 60).padStart(2, '0');
            const newEnd = `${eh}:${em}`;

            if (endTimePicker) {
                endTimePicker.setDate(newEnd, true);
            } else {
                endInput.value = newEnd;
            }
            updateScheduleSummary();
        }

        function applyFullDay() {
            if (startTimePicker) startTimePicker.setDate('08:00', true);
            if (endTimePicker) endTimePicker.setDate('16:00', true);
            updateScheduleSummary();
        }

        document.addEventListener('DOMContentLoaded', function () {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }

            // Select2
            $('#room_id').select2({
                placeholder: '-- Pilih Ruangan --',
                allowClear: true,
                width: '100%'
            }).on('change', updateScheduleSummary);

            // Flatpickr untuk Tanggal
            flatpickr(".flatpickr-date", {
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d F Y",
                locale: "id",
                minDate: "today",
                onChange: updateScheduleSummary
            });

            // Flatpickr untuk Waktu
            startTimePicker = flatpickr("#start_time", {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                minuteIncrement: 15,
                onChange: updateScheduleSummary
            });

            endTimePicker = flatpickr("#end_time", {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: true,
                minuteIncrement: 15,
                onChange: updateScheduleSummary
            });

            updateScheduleSummary();

            const form = document.getElementById('formReservation');
            const btnSubmit = document.getElementById('btnSubmit');
            if (form && btnSubmit) {
                form.addEventListener('submit', function () {
                    btnSubmit.disabled = true;
                    btnSubmit.innerHTML = '<span class="inline-block w-4 h-4 border-2 border-white/40 border-t-white rounded-full animate-spin"></span> <span>Menyimpan...</span>';
                });
            }
        });
    </script>
@endpush
