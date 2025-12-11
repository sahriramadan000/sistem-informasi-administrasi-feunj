@extends('layouts.app')

@section('title', 'Buat Surat Baru')

@section('content')
    <div class="max-w-4xl mx-auto">
        {{-- Header --}}
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                    <i data-lucide="plus-circle" class="w-6 h-6 text-brand"></i>
                    Buat Surat Baru
                </h2>
                <p class="mt-1 text-sm text-gray-500">Isi formulir di bawah untuk membuat surat baru dan generate nomor
                    otomatis</p>
            </div>
            <a href="{{ route('letters.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Kembali
            </a>
        </div>

        {{-- Form Section --}}
        <div class="bg-white rounded-lg shadow border border-gray-200">
            <div class="p-8">
                <form method="POST" action="{{ route('letters.store') }}">
                    @csrf

                    {{-- SECTION 1: Informasi Dasar Surat --}}
                    <div class="mb-8">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="flex items-center justify-center w-10 h-10 rounded-full bg-brand-lighter">
                                <span class="text-brand font-bold text-lg">1</span>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Informasi Dasar Surat</h3>
                                <p class="text-sm text-gray-500">Pilih jenis dan klasifikasi surat yang akan dibuat</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pl-0 md:pl-13">
                            {{-- Jenis Surat --}}
                            <div class="space-y-2">
                                <label for="letter_type_id" class="label flex items-center gap-2">
                                    <i data-lucide="file-text" class="w-4 h-4 text-gray-400"></i>
                                    Jenis Surat <span class="text-destructive">*</span>
                                </label>
                                <select id="letter_type_id" name="letter_type_id"
                                    class="select @error('letter_type_id') !border-red-500 @enderror" required>
                                    <option value="">-- Pilih Jenis Surat --</option>
                                    @foreach ($letterTypes as $letterType)
                                        <option value="{{ $letterType->id }}"
                                            {{ old('letter_type_id') == $letterType->id ? 'selected' : '' }}>
                                            {{ $letterType->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('letter_type_id')
                                    <p class="text-sm text-destructive flex items-center gap-1.5">
                                        <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Klasifikasi Surat --}}
                            <div class="space-y-2">
                                <label for="classification_id" class="label flex items-center gap-2">
                                    <i data-lucide="folder" class="w-4 h-4 text-gray-400"></i>
                                    Klasifikasi Surat <span class="text-destructive">*</span>
                                </label>
                                <select id="classification_id" name="classification_id"
                                    class="select @error('classification_id') !border-red-500 @enderror" required>
                                    <option value="">-- Pilih Klasifikasi --</option>
                                    @foreach ($classifications as $classification)
                                        <option value="{{ $classification->id }}"
                                            {{ old('classification_id') == $classification->id ? 'selected' : '' }}>
                                            {{ $classification->name }} ({{ $classification->code }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('classification_id')
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

                    {{-- SECTION 2: Detail Administrasi --}}
                    <div class="mb-8">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="flex items-center justify-center w-10 h-10 rounded-full bg-brand-lighter">
                                <span class="text-brand font-bold text-lg">2</span>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Detail Administrasi</h3>
                                <p class="text-sm text-gray-500">Tentukan penandatangan, tanggal, dan jumlah surat</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pl-0 md:pl-13">
                            {{-- Penandatangan --}}
                            <div class="space-y-2">
                                <label for="signatory_id" class="label flex items-center gap-2">
                                    <i data-lucide="user-check" class="w-4 h-4 text-gray-400"></i>
                                    Penandatangan <span class="text-destructive">*</span>
                                </label>
                                <select id="signatory_id" name="signatory_id"
                                    class="select @error('signatory_id') !border-red-500 @enderror" required>
                                    <option value="">-- Pilih Penandatangan --</option>
                                    @foreach ($signatories as $signatory)
                                        <option value="{{ $signatory->id }}"
                                            {{ old('signatory_id') == $signatory->id ? 'selected' : '' }}>
                                            {{ $signatory->name }} - {{ $signatory->position }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('signatory_id')
                                    <p class="text-sm text-destructive flex items-center gap-1.5">
                                        <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Tanggal Surat --}}
                            <div class="space-y-2">
                                <label for="letter_date" class="label flex items-center gap-2">
                                    <i data-lucide="calendar" class="w-4 h-4 text-gray-400"></i>
                                    Tanggal Surat <span class="text-destructive">*</span>
                                </label>
                                <input type="text" id="letter_date" name="letter_date"
                                    value="{{ old('letter_date', now()->format('Y-m-d')) }}"
                                    class="input flatpickr-input @error('letter_date') !border-red-500 @enderror"
                                    placeholder="Pilih tanggal" readonly required>
                                @error('letter_date')
                                    <p class="text-sm text-destructive flex items-center gap-1.5">
                                        <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Jumlah Surat --}}
                            <div class="space-y-2 md:col-span-2">
                                <label for="quantity" class="label flex items-center gap-2">
                                    <i data-lucide="copy" class="w-4 h-4 text-gray-400"></i>
                                    Jumlah Surat
                                </label>
                                <div class="flex items-start gap-4">
                                    <input type="number" id="quantity" name="quantity" value="{{ old('quantity', 1) }}"
                                        min="1" max="50"
                                        class="input max-w-xs @error('quantity') !border-red-500 @enderror" placeholder="1">
                                    <div class="flex-1">
                                        <p class="text-sm text-gray-600 mt-2">
                                            <i data-lucide="info" class="w-4 h-4 inline-block text-blue-500 mr-1"></i>
                                            Buat beberapa surat dengan nomor berurutan sekaligus (maksimal 50 surat)
                                        </p>
                                    </div>
                                </div>
                                @error('quantity')
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
                                <p class="text-sm text-gray-500">Masukkan perihal dan tujuan surat</p>
                            </div>
                        </div>

                        <div class="space-y-6 pl-0 md:pl-13">
                            {{-- Perihal --}}
                            <div class="space-y-2">
                                <label for="subject" class="label flex items-center gap-2">
                                    <i data-lucide="align-left" class="w-4 h-4 text-gray-400"></i>
                                    Perihal / Hal Surat <span class="text-destructive">*</span>
                                </label>
                                <input type="text" id="subject" name="subject" value="{{ old('subject') }}"
                                    class="input @error('subject') !border-red-500 @enderror"
                                    placeholder="Contoh: Permohonan Izin Penelitian" required>
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
                                <input type="text" id="recipient" name="recipient" value="{{ old('recipient') }}"
                                    class="input @error('recipient') !border-red-500 @enderror"
                                    placeholder="Contoh: Dekan Fakultas Ekonomi" required>
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
                    <div id="additional_section" style="display: none;">
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
                                {{-- Keperluan Surat (Conditional) --}}
                                <div id="letter_purpose_field" class="space-y-2" style="display: none;">
                                    <label for="letter_purpose_id" class="label flex items-center gap-2">
                                        <i data-lucide="clipboard-list" class="w-4 h-4 text-gray-400"></i>
                                        Keperluan Surat <span class="text-destructive purpose-required">*</span>
                                    </label>
                                    <select id="letter_purpose_id" name="letter_purpose_id"
                                        class="select @error('letter_purpose_id') !border-red-500 @enderror">
                                        <option value="">-- Pilih Keperluan --</option>
                                        @foreach ($letterPurposes as $purpose)
                                            <option value="{{ $purpose->id }}"
                                                {{ old('letter_purpose_id') == $purpose->id ? 'selected' : '' }}>
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

                                {{-- Nama Mahasiswa (Conditional) --}}
                                <div id="student_name_field" class="space-y-2" style="display: none;">
                                    <label for="student_name" class="label flex items-center gap-2">
                                        <i data-lucide="user" class="w-4 h-4 text-gray-400"></i>
                                        Nama Mahasiswa <span class="text-destructive student-required">*</span>
                                    </label>
                                    <input type="text" id="student_name" name="student_name"
                                        value="{{ old('student_name') }}"
                                        class="input @error('student_name') !border-red-500 @enderror"
                                        placeholder="Masukkan nama lengkap mahasiswa">
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

                    {{-- Info Box --}}
                    <div class="mb-8">
                        <div class="bg-gradient-to-r from-orange-50 to-yellow-50 border-l-4 border-brand rounded-r-lg p-5">
                            <div class="flex gap-4">
                                <div class="flex-shrink-0">
                                    <div
                                        class="w-10 h-10 rounded-full bg-white flex items-center justify-center shadow-sm">
                                        <i data-lucide="info" class="w-5 h-5 text-brand"></i>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="font-semibold text-gray-900 mb-2">Informasi Penting</h6>
                                    <ul class="text-sm text-gray-700 space-y-1">
                                        <li class="flex items-start gap-2">
                                            <i data-lucide="check"
                                                class="w-4 h-4 text-green-600 mt-0.5 flex-shrink-0"></i>
                                            <span>Nomor surat akan di-generate otomatis setelah formulir disimpan</span>
                                        </li>
                                        <li class="flex items-start gap-2">
                                            <i data-lucide="check"
                                                class="w-4 h-4 text-green-600 mt-0.5 flex-shrink-0"></i>
                                            <span>Format nomor:
                                                <strong>[000]/[kode_penandatangan]/[kode_klasifikasi]/[tahun]</strong></span>
                                        </li>
                                        <li class="flex items-start gap-2">
                                            <i data-lucide="check"
                                                class="w-4 h-4 text-green-600 mt-0.5 flex-shrink-0"></i>
                                            <span>Pastikan semua data sudah benar sebelum menyimpan</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Form Actions --}}
                    <div class="flex justify-between items-center pt-6 border-t">
                        <a href="{{ route('letters.index') }}" class="btn-outline gap-2">
                            <i data-lucide="arrow-left" class="w-4 h-4"></i>
                            Kembali
                        </a>
                        <button type="submit" class="btn-primary gap-2 px-8 py-3 text-base">
                            <i data-lucide="check-circle" class="w-5 h-5"></i>
                            Simpan & Generate Nomor
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://unpkg.com/lucide@latest"></script>
        <script>
            lucide.createIcons();

            // Letter types data with requires_purpose flag
            const letterTypesData = {
                @foreach ($letterTypes as $type)
                    {{ $type->id }}: {
                        requires_purpose: {{ $type->requires_purpose ? 'true' : 'false' }}
                    },
                @endforeach
            };

            // Function to toggle conditional fields
            function toggleConditionalFields(letterTypeId) {
                const additionalSection = document.getElementById('additional_section');
                const letterPurposeField = document.getElementById('letter_purpose_field');
                const studentNameField = document.getElementById('student_name_field');
                const letterPurposeSelect = document.getElementById('letter_purpose_id');
                const studentNameInput = document.getElementById('student_name');

                if (letterTypeId && letterTypesData[letterTypeId] && letterTypesData[letterTypeId].requires_purpose) {
                    // Show section and fields
                    additionalSection.style.display = 'block';
                    letterPurposeField.style.display = 'block';
                    studentNameField.style.display = 'block';
                    letterPurposeSelect.setAttribute('required', 'required');
                    studentNameInput.setAttribute('required', 'required');
                } else {
                    // Hide section and fields
                    additionalSection.style.display = 'none';
                    letterPurposeField.style.display = 'none';
                    studentNameField.style.display = 'none';
                    letterPurposeSelect.removeAttribute('required');
                    studentNameInput.removeAttribute('required');
                    letterPurposeSelect.value = '';
                    studentNameInput.value = '';
                }
            }

            // Initialize Select2 and event handlers
            $(document).ready(function() {
                // Jenis Surat with change handler
                $('#letter_type_id').select2({
                    placeholder: '-- Pilih Jenis Surat --',
                    allowClear: false,
                    width: '100%'
                }).on('change', function() {
                    toggleConditionalFields(this.value);
                });

                // Keperluan Surat
                $('#letter_purpose_id').select2({
                    placeholder: '-- Pilih Keperluan --',
                    allowClear: true,
                    width: '100%'
                });

                // Klasifikasi Surat
                $('#classification_id').select2({
                    placeholder: '-- Pilih Klasifikasi --',
                    allowClear: false,
                    width: '100%'
                });

                // Penandatangan
                $('#signatory_id').select2({
                    placeholder: '-- Pilih Penandatangan --',
                    allowClear: false,
                    width: '100%'
                });

                // Initialize Flatpickr for date input
                flatpickr("#letter_date", {
                    dateFormat: "Y-m-d",
                    locale: "id",
                    altInput: true,
                    altFormat: "d F Y",
                    allowInput: true,
                    disableMobile: true,
                    defaultDate: "{{ old('letter_date', now()->format('Y-m-d')) }}"
                });

                // Add error class if validation fails
                @if ($errors->has('letter_type_id'))
                    $('#letter_type_id').next('.select2-container').addClass('select2-error');
                @endif

                @if ($errors->has('classification_id'))
                    $('#classification_id').next('.select2-container').addClass('select2-error');
                @endif

                @if ($errors->has('signatory_id'))
                    $('#signatory_id').next('.select2-container').addClass('select2-error');
                @endif

                @if ($errors->has('letter_purpose_id'))
                    $('#letter_purpose_id').next('.select2-container').addClass('select2-error');
                @endif

                // Handle initial state (for old input after validation error)
                @if (old('letter_type_id'))
                    toggleConditionalFields({{ old('letter_type_id') }});
                @endif
            });
        </script>
    @endpush
@endsection
