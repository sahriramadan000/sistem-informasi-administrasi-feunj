@extends('layouts.app')

@section('title', 'Edit Surat')

@section('content')
<div class="max-w-4xl mx-auto">
    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i data-lucide="edit" class="w-6 h-6 text-brand"></i>
                Edit Surat
            </h2>
            <p class="mt-1 text-sm text-gray-500">Perbarui informasi surat {{ $letter->letter_number }}</p>
        </div>
        <a href="{{ route('letters.show', $letter) }}" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali
        </a>
    </div>

    {{-- Info Alert --}}
    <div class="mb-6 rounded-lg bg-amber-50 border-l-4 border-amber-500 p-4">
        <div class="flex gap-3">
            <div class="flex-shrink-0">
                <i data-lucide="alert-triangle" class="h-5 w-5 text-amber-500"></i>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-amber-800 mb-1">Informasi Penting</h3>
                <p class="text-sm text-amber-700">
                    <strong>Nomor surat, jenis, klasifikasi, dan penandatangan</strong> tidak dapat diubah. Anda hanya dapat mengubah tanggal, perihal, tujuan, dan informasi tambahan.
                </p>
            </div>
        </div>
    </div>

    {{-- Form Section --}}
    <div class="bg-white rounded-lg shadow border border-gray-200">
        <div class="p-8">
            <form method="POST" action="{{ route('letters.update', $letter) }}">
                @csrf
                @method('PUT')
                
                {{-- SECTION 1: Informasi Dasar (Read-Only) --}}
                <div class="mb-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-100">
                            <span class="text-gray-600 font-bold text-lg">1</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Informasi Dasar Surat</h3>
                            <p class="text-sm text-gray-500">Data dasar yang tidak dapat diubah</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pl-0 md:pl-13">
                        {{-- Nomor Surat --}}
                        <div class="space-y-2">
                            <label class="label flex items-center gap-2">
                                <i data-lucide="hash" class="w-4 h-4 text-gray-400"></i>
                                Nomor Surat
                            </label>
                            <input type="text" value="{{ $letter->letter_number }}" class="input bg-gray-50 cursor-not-allowed" readonly>
                        </div>

                        {{-- Jenis Surat --}}
                        <div class="space-y-2">
                            <label class="label flex items-center gap-2">
                                <i data-lucide="file-text" class="w-4 h-4 text-gray-400"></i>
                                Jenis Surat
                            </label>
                            <input type="text" value="{{ $letter->letterType->name }}" class="input bg-gray-50 cursor-not-allowed" readonly>
                        </div>

                        {{-- Klasifikasi --}}
                        <div class="space-y-2">
                            <label class="label flex items-center gap-2">
                                <i data-lucide="folder" class="w-4 h-4 text-gray-400"></i>
                                Klasifikasi
                            </label>
                            <input type="text" value="{{ $letter->classification->name }} ({{ $letter->classification->code }})" class="input bg-gray-50 cursor-not-allowed" readonly>
                        </div>

                        {{-- Penandatangan --}}
                        <div class="space-y-2">
                            <label class="label flex items-center gap-2">
                                <i data-lucide="user-check" class="w-4 h-4 text-gray-400"></i>
                                Penandatangan
                            </label>
                            <input type="text" value="{{ $letter->signatory->name }} - {{ $letter->signatory->position }}" class="input bg-gray-50 cursor-not-allowed" readonly>
                        </div>
                    </div>
                </div>

                {{-- Divider --}}
                <div class="border-t border-gray-200 my-8"></div>

                {{-- SECTION 2: Detail Administrasi --}}
                <div class="mb-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="flex items-center justify-center w-10 h-10 rounded-full bg-brand-lighter">
                            <span class="text-brand font-bold text-lg">2</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Detail Administrasi</h3>
                            <p class="text-sm text-gray-500">Perbarui tanggal surat</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pl-0 md:pl-13">
                        {{-- Tanggal Surat --}}
                        <div class="space-y-2">
                            <label for="letter_date" class="label flex items-center gap-2">
                                <i data-lucide="calendar" class="w-4 h-4 text-gray-400"></i>
                                Tanggal Surat <span class="text-destructive">*</span>
                            </label>
                            <input type="text" 
                                   id="letter_date" 
                                   name="letter_date" 
                                   value="{{ old('letter_date', $letter->letter_date->format('Y-m-d')) }}"
                                   class="input flatpickr-input @error('letter_date') !border-red-500 @enderror"
                                   placeholder="Pilih tanggal"
                                   readonly
                                   required>
                            @error('letter_date')
                                <p class="text-sm text-destructive flex items-center gap-1.5">
                                    <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Divider --}}
                <div class="border-t border-gray-200 my-8"></div>

                {{-- SECTION 3: Isi Surat --}}
                <div class="mb-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="flex items-center justify-center w-10 h-10 rounded-full bg-brand-lighter">
                            <span class="text-brand font-bold text-lg">3</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Isi Surat</h3>
                            <p class="text-sm text-gray-500">Perbarui perihal dan tujuan surat</p>
                        </div>
                    </div>
                    
                    <div class="space-y-6 pl-0 md:pl-13">
                        {{-- Perihal --}}
                        <div class="space-y-2">
                            <label for="subject" class="label flex items-center gap-2">
                                <i data-lucide="align-left" class="w-4 h-4 text-gray-400"></i>
                                Perihal / Hal Surat <span class="text-destructive">*</span>
                            </label>
                            <input type="text" 
                                   id="subject" 
                                   name="subject" 
                                   value="{{ old('subject', $letter->subject) }}"
                                   class="input @error('subject') !border-red-500 @enderror" 
                                   placeholder="Contoh: Permohonan Izin Penelitian"
                                   required>
                            <p class="text-xs text-gray-500 flex items-start gap-1.5">
                                <i data-lucide="lightbulb" class="w-3.5 h-3.5 mt-0.5 text-yellow-500"></i>
                                <span>Jelaskan secara singkat maksud/tujuan surat ini dibuat</span>
                            </p>
                            @error('subject')
                                <p class="text-sm text-destructive flex items-center gap-1.5">
                                    <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Tujuan --}}
                        <div class="space-y-2">
                            <label for="recipient" class="label flex items-center gap-2">
                                <i data-lucide="send" class="w-4 h-4 text-gray-400"></i>
                                Ditujukan Kepada <span class="text-destructive">*</span>
                            </label>
                            <input type="text" 
                                   id="recipient" 
                                   name="recipient" 
                                   value="{{ old('recipient', $letter->recipient) }}"
                                   class="input @error('recipient') !border-red-500 @enderror" 
                                   placeholder="Contoh: Dekan Fakultas Ekonomi"
                                   required>
                            <p class="text-xs text-gray-500 flex items-start gap-1.5">
                                <i data-lucide="lightbulb" class="w-3.5 h-3.5 mt-0.5 text-yellow-500"></i>
                                <span>Nama jabatan atau instansi tujuan surat</span>
                            </p>
                            @error('recipient')
                                <p class="text-sm text-destructive flex items-center gap-1.5">
                                    <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- SECTION 4: Informasi Tambahan (Conditional) --}}
                @if($letter->letterType->requires_purpose)
                <div id="additional_section">
                    {{-- Divider --}}
                    <div class="border-t border-gray-200 my-8"></div>

                    <div class="mb-8">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="flex items-center justify-center w-10 h-10 rounded-full bg-brand-lighter">
                                <span class="text-brand font-bold text-lg">4</span>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Informasi Tambahan</h3>
                                <p class="text-sm text-gray-500">Data khusus untuk jenis surat tertentu</p>
                            </div>
                        </div>
                        
                        <div class="space-y-6 pl-0 md:pl-13">
                            {{-- Keperluan Surat --}}
                            <div class="space-y-2">
                                <label for="letter_purpose_id" class="label flex items-center gap-2">
                                    <i data-lucide="clipboard-list" class="w-4 h-4 text-gray-400"></i>
                                    Keperluan Surat <span class="text-destructive">*</span>
                                </label>
                                <select id="letter_purpose_id" 
                                        name="letter_purpose_id" 
                                        class="select @error('letter_purpose_id') !border-red-500 @enderror"
                                        required>
                                    <option value="">-- Pilih Keperluan --</option>
                                    @foreach($letterPurposes as $purpose)
                                        <option value="{{ $purpose->id }}" {{ old('letter_purpose_id', $letter->letter_purpose_id) == $purpose->id ? 'selected' : '' }}>
                                            {{ $purpose->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('letter_purpose_id')
                                    <p class="text-sm text-destructive flex items-center gap-1.5">
                                        <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Nama Mahasiswa --}}
                            <div class="space-y-2">
                                <label for="student_name" class="label flex items-center gap-2">
                                    <i data-lucide="user" class="w-4 h-4 text-gray-400"></i>
                                    Nama Mahasiswa <span class="text-destructive">*</span>
                                </label>
                                <input type="text" 
                                       id="student_name" 
                                       name="student_name" 
                                       value="{{ old('student_name', $letter->student_name) }}"
                                       class="input @error('student_name') !border-red-500 @enderror" 
                                       placeholder="Masukkan nama lengkap mahasiswa"
                                       required>
                                @error('student_name')
                                    <p class="text-sm text-destructive flex items-center gap-1.5">
                                        <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Info Box --}}
                <div class="mb-8">
                    <div class="bg-gradient-to-r from-blue-50 to-cyan-50 border-l-4 border-blue-500 rounded-r-lg p-5">
                        <div class="flex gap-4">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center shadow-sm">
                                    <i data-lucide="info" class="w-5 h-5 text-blue-500"></i>
                                </div>
                            </div>
                            <div>
                                <h6 class="font-semibold text-gray-900 mb-2">Catatan Perubahan</h6>
                                <ul class="text-sm text-gray-700 space-y-1">
                                    <li class="flex items-start gap-2">
                                        <i data-lucide="check" class="w-4 h-4 text-blue-600 mt-0.5 flex-shrink-0"></i>
                                        <span>Perubahan hanya berlaku pada data yang dapat diedit</span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <i data-lucide="check" class="w-4 h-4 text-blue-600 mt-0.5 flex-shrink-0"></i>
                                        <span>Nomor surat tetap sama: <strong class="font-mono">{{ $letter->letter_number }}</strong></span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <i data-lucide="check" class="w-4 h-4 text-blue-600 mt-0.5 flex-shrink-0"></i>
                                        <span>Pastikan semua data sudah benar sebelum menyimpan</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="flex justify-between items-center pt-6 border-t">
                    <a href="{{ route('letters.show', $letter) }}" class="btn-outline gap-2">
                        <i data-lucide="x" class="w-4 h-4"></i>
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
    lucide.createIcons();
    
    // Initialize Select2 and Flatpickr
    $(document).ready(function() {
        // Initialize Select2 for letter purpose (if exists)
        @if($letter->letterType->requires_purpose)
        $('#letter_purpose_id').select2({
            placeholder: '-- Pilih Keperluan --',
            allowClear: true,
            width: '100%'
        });
        @endif

        // Initialize Flatpickr for date input
        flatpickr("#letter_date", {
            dateFormat: "Y-m-d",
            locale: "id",
            altInput: true,
            altFormat: "d F Y",
            allowInput: true,
            disableMobile: true,
            defaultDate: "{{ old('letter_date', $letter->letter_date->format('Y-m-d')) }}"
        });

        // Add error class if validation fails
        @if($errors->has('letter_purpose_id'))
            $('#letter_purpose_id').next('.select2-container').addClass('select2-error');
        @endif
    });
</script>
@endpush
@endsection
