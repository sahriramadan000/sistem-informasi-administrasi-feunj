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
                <form id="letter-form" method="POST" action="{{ route('letters.store') }}">
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
                            {{-- Klasifikasi Keamanan --}}
                            <div class="space-y-2">
                                <label for="security_classification" class="label flex items-center gap-2">
                                    <i data-lucide="shield" class="w-4 h-4 text-gray-400"></i>
                                    Klasifikasi Keamanan <span class="text-destructive">*</span>
                                </label>
                                <select id="security_classification" name="security_classification"
                                    class="select @error('security_classification') !border-red-500 @enderror" required>
                                    <option value="">-- Pilih Klasifikasi Keamanan --</option>
                                    @foreach ($securityClassifications as $code => $name)
                                        <option value="{{ $code }}"
                                            {{ old('security_classification') == $code ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('security_classification')
                                    <p class="text-sm text-destructive flex items-center gap-1.5">
                                        <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Sasaran Surat --}}
                            <div class="space-y-2">
                                <label for="letter_target" class="label flex items-center gap-2">
                                    <i data-lucide="target" class="w-4 h-4 text-gray-400"></i>
                                    Sasaran Surat <span class="text-destructive">*</span>
                                </label>
                                <select id="letter_target" name="letter_target"
                                    class="select @error('letter_target') !border-red-500 @enderror" required>
                                    <option value="">-- Pilih Sasaran Surat --</option>
                                    @foreach ($letterTargets as $code => $name)
                                        <option value="{{ $code }}"
                                            {{ old('letter_target') == $code ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('letter_target')
                                    <p class="text-sm text-destructive flex items-center gap-1.5">
                                        <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                                <p class="text-xs text-gray-500 flex items-start gap-1.5">
                                    <i data-lucide="info" class="w-3.5 h-3.5 mt-0.5 text-blue-500"></i>
                                    <span>External akan menambahkan kode "UN39." ke nomor surat</span>
                                </p>
                            </div>

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

                                {{-- Mode Selection for Multiple Letters --}}
                                <div id="multiple_mode_selection" class="mt-4 p-4 bg-blue-50 rounded-lg border border-blue-200" style="display: none;">
                                    <p class="text-sm font-medium text-blue-800 mb-3 flex items-center gap-2">
                                        <i data-lucide="settings" class="w-4 h-4"></i>
                                        Pengaturan untuk surat lebih dari 1
                                    </p>
                                    <div class="flex flex-wrap gap-3">
                                        <label class="flex items-center gap-2 cursor-pointer group">
                                            <input type="radio" name="multiple_mode" value="same" class="w-4 h-4 text-brand focus:ring-brand" checked>
                                            <span class="text-sm text-gray-700 group-hover:text-brand transition-colors">
                                                <strong>Sama semua</strong> - Perihal dan tujuan sama untuk semua surat
                                            </span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer group">
                                            <input type="radio" name="multiple_mode" value="different" class="w-4 h-4 text-brand focus:ring-brand">
                                            <span class="text-sm text-gray-700 group-hover:text-brand transition-colors">
                                                <strong>Berbeda</strong> - Isi perihal dan tujuan berbeda per surat
                                            </span>
                                        </label>
                                    </div>
                                </div>
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
                                <p class="text-sm text-gray-500" id="section3_description">Masukkan perihal dan tujuan surat</p>
                            </div>
                        </div>

                        {{-- Single Mode (Default) --}}
                        <div id="single_letter_content" class="space-y-6 pl-0 md:pl-13">
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

                        {{-- Multiple Different Mode (Hidden by default) --}}
                        <div id="multiple_letter_content" class="pl-0 md:pl-13" style="display: none;">
                            {{-- Info Banner --}}
                            <div class="mb-6 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-200">
                                <div class="flex items-start gap-3">
                                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                                        <i data-lucide="list-ordered" class="w-4 h-4 text-blue-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-blue-800">Mode Input Berbeda untuk Setiap Surat</p>
                                        <p class="text-xs text-blue-600 mt-1">Isi perihal dan tujuan untuk masing-masing surat di bawah. Nomor surat akan di-generate berurutan.</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Validation Errors for Multiple Mode --}}
                            @if ($errors->has('subjects') || $errors->has('subjects.*') || $errors->has('recipients') || $errors->has('recipients.*'))
                                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                                    <div class="flex items-start gap-2">
                                        <i data-lucide="alert-triangle" class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5"></i>
                                        <div>
                                            <p class="text-sm font-medium text-red-800">Terdapat kesalahan pada isian surat:</p>
                                            <ul class="mt-2 text-sm text-red-700 list-disc list-inside space-y-1">
                                                @foreach ($errors->all() as $error)
                                                    @if (str_contains($error, 'Perihal') || str_contains($error, 'Tujuan') || str_contains($error, 'subjects') || str_contains($error, 'recipients'))
                                                        <li>{{ $error }}</li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Dynamic Letter Items Container --}}
                            <div id="letter_items_container" class="space-y-4">
                                {{-- Letter items will be generated dynamically --}}
                            </div>

                            {{-- Quick Actions --}}
                            <div class="mt-4 flex flex-wrap gap-2">
                                <button type="button" id="btn_copy_first_to_all" class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-md transition-colors">
                                    <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                                    Salin dari Surat 1 ke Semua
                                </button>
                                <button type="button" id="btn_clear_all" class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 hover:bg-red-100 rounded-md transition-colors">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                    Kosongkan Semua
                                </button>
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

            // Handle form submission - remove 'required' from hidden inputs to avoid HTML5 validation errors
            document.getElementById('letter-form').addEventListener('submit', function(e) {
                // DEBUG: Capture quantity value at exact moment before submission
                const quantityInput = document.getElementById('quantity');
                const quantityValue = quantityInput.value;
                const multipleMode = document.querySelector('input[name="multiple_mode"]:checked');
                const selectedMode = multipleMode ? multipleMode.value : 'none';
                
                console.log('[DEBUG SUBMIT] Form submission triggered');
                console.log('[DEBUG SUBMIT] Quantity input .value:', quantityValue);
                console.log('[DEBUG SUBMIT] Selected mode:', selectedMode);
                console.log('[DEBUG SUBMIT] currentQuantity (global):', currentQuantity);
                console.log('[DEBUG SUBMIT] currentMode (global):', currentMode);
                
                // Create a hidden input to send the DEBUG value
                const debugInput = document.createElement('input');
                debugInput.type = 'hidden';
                debugInput.name = 'DEBUG_quantity_at_submit';
                debugInput.value = quantityValue;
                this.appendChild(debugInput);
                
                const debugModeInput = document.createElement('input');
                debugModeInput.type = 'hidden';
                debugModeInput.name = 'DEBUG_mode_at_submit';
                debugModeInput.value = selectedMode;
                this.appendChild(debugModeInput);
                
                // Get all inputs with 'required' attribute
                const inputs = document.querySelectorAll('input[required], select[required], textarea[required]');
                
                // Remove required attribute from inputs that are not visible (display: none or parent hidden)
                inputs.forEach(input => {
                    // Check if input or any parent is hidden
                    let isHidden = false;
                    let element = input;
                    
                    while (element && !isHidden) {
                        if (element.offsetParent === null || window.getComputedStyle(element).display === 'none') {
                            isHidden = true;
                        }
                        element = element.parentElement;
                    }
                    
                    // Store original required state and remove if hidden
                    if (isHidden && input.hasAttribute('required')) {
                        input.dataset.originalRequired = true;
                        input.removeAttribute('required');
                    }
                });
            });

            // Letter types data with requires_purpose flag
            const letterTypesData = {
                @foreach ($letterTypes as $type)
                    {{ $type->id }}: {
                        requires_purpose: {{ $type->requires_purpose ? 'true' : 'false' }}
                    },
                @endforeach
            };

            // State management
            let currentQuantity = 1;
            let currentMode = 'same';

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

            // Function to create a letter item card
            function createLetterItemCard(index) {
                const cardNumber = index + 1;
                return `
                    <div class="letter-item-card bg-white border border-gray-200 rounded-lg p-4 hover:border-brand/50 hover:shadow-sm transition-all" data-index="${index}">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="flex items-center justify-center w-8 h-8 rounded-full bg-gradient-to-br from-brand to-orange-500 text-dark text-sm font-bold shadow-sm border border-dark">
                                ${cardNumber}
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-semibold text-gray-800">Surat ke-${cardNumber}</h4>
                                <p class="text-xs text-gray-500">Nomor surat akan di-generate otomatis</p>
                            </div>
                            <button type="button" class="btn-copy-previous p-1.5 text-gray-400 hover:text-brand hover:bg-orange-50 rounded transition-colors" title="Salin dari surat sebelumnya" ${index === 0 ? 'style="display:none"' : ''}>
                                <i data-lucide="copy-check" class="w-4 h-4"></i>
                            </button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="text-xs font-medium text-gray-600 flex items-center gap-1">
                                    <i data-lucide="align-left" class="w-3 h-3"></i>
                                    Perihal <span class="text-destructive">*</span>
                                </label>
                                <input type="text" 
                                    name="subjects[${index}]" 
                                    class="input input-sm text-sm" 
                                    placeholder="Masukkan perihal surat"
                                    required>
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-xs font-medium text-gray-600 flex items-center gap-1">
                                    <i data-lucide="send" class="w-3 h-3"></i>
                                    Ditujukan Kepada <span class="text-destructive">*</span>
                                </label>
                                <input type="text" 
                                    name="recipients[${index}]" 
                                    class="input input-sm text-sm" 
                                    placeholder="Masukkan tujuan surat"
                                    required>
                            </div>
                        </div>
                    </div>
                `;
            }

            // Function to generate letter items based on quantity
            function generateLetterItems(quantity) {
                console.log('[DEBUG] generateLetterItems called with quantity:', quantity);
                const container = document.getElementById('letter_items_container');
                container.innerHTML = '';
                
                for (let i = 0; i < quantity; i++) {
                    container.insertAdjacentHTML('beforeend', createLetterItemCard(i));
                }
                
                console.log('[DEBUG] Generated letter items count:', container.querySelectorAll('.letter-item-card').length);
                
                // Reinitialize Lucide icons for new elements
                lucide.createIcons();
                
                // Add copy from previous functionality
                document.querySelectorAll('.btn-copy-previous').forEach((btn, index) => {
                    btn.addEventListener('click', function() {
                        if (index > 0) {
                            const prevCard = document.querySelector(`.letter-item-card[data-index="${index - 1}"]`);
                            const currentCard = document.querySelector(`.letter-item-card[data-index="${index}"]`);
                            
                            if (prevCard && currentCard) {
                                const prevSubject = prevCard.querySelector('input[name^="subjects"]').value;
                                const prevRecipient = prevCard.querySelector('input[name^="recipients"]').value;
                                
                                currentCard.querySelector('input[name^="subjects"]').value = prevSubject;
                                currentCard.querySelector('input[name^="recipients"]').value = prevRecipient;
                                
                                // Visual feedback
                                currentCard.classList.add('ring-2', 'ring-brand/50');
                                setTimeout(() => {
                                    currentCard.classList.remove('ring-2', 'ring-brand/50');
                                }, 500);
                            }
                        }
                    });
                });
            }

            // Function to update UI based on quantity and mode
            function updateMultipleLetterUI() {
                const quantity = parseInt(document.getElementById('quantity').value) || 1;
                const modeSelection = document.getElementById('multiple_mode_selection');
                const singleContent = document.getElementById('single_letter_content');
                const multipleContent = document.getElementById('multiple_letter_content');
                const subjectInput = document.getElementById('subject');
                const recipientInput = document.getElementById('recipient');
                const section3Desc = document.getElementById('section3_description');

                currentQuantity = quantity;

                if (quantity > 1) {
                    // Show mode selection
                    modeSelection.style.display = 'block';
                    
                    // Get selected mode
                    const selectedMode = document.querySelector('input[name="multiple_mode"]:checked').value;
                    currentMode = selectedMode;
                    
                    if (selectedMode === 'different') {
                        // Show multiple letter content, hide single
                        singleContent.style.display = 'none';
                        multipleContent.style.display = 'block';
                        
                        // Remove required from single inputs
                        subjectInput.removeAttribute('required');
                        recipientInput.removeAttribute('required');
                        
                        // Generate letter items
                        generateLetterItems(quantity);
                        
                        // Update description
                        section3Desc.textContent = `Masukkan perihal dan tujuan untuk ${quantity} surat`;
                    } else {
                        // Show single letter content, hide multiple
                        singleContent.style.display = 'block';
                        multipleContent.style.display = 'none';
                        
                        // Add required back to single inputs
                        subjectInput.setAttribute('required', 'required');
                        recipientInput.setAttribute('required', 'required');
                        
                        // Update description
                        section3Desc.textContent = `Perihal dan tujuan yang sama akan digunakan untuk ${quantity} surat`;
                    }
                } else {
                    // Hide mode selection for single letter
                    modeSelection.style.display = 'none';
                    singleContent.style.display = 'block';
                    multipleContent.style.display = 'none';
                    
                    // Add required back to single inputs
                    subjectInput.setAttribute('required', 'required');
                    recipientInput.setAttribute('required', 'required');
                    
                    // Reset description
                    section3Desc.textContent = 'Masukkan perihal dan tujuan surat';
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

                // Klasifikasi Keamanan
                $('#security_classification').select2({
                    placeholder: '-- Pilih Klasifikasi Keamanan --',
                    allowClear: false,
                    width: '100%'
                });

                // Sasaran Surat
                $('#letter_target').select2({
                    placeholder: '-- Pilih Sasaran Surat --',
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

                // Quantity change handler
                const quantityInput = document.getElementById('quantity');
                
                quantityInput.addEventListener('input', function(e) {
                    console.log('[DEBUG QUANTITY] Input event - value changed to:', this.value);
                    console.log('[DEBUG QUANTITY] Event type:', e.type);
                    updateMultipleLetterUI();
                });
                
                quantityInput.addEventListener('change', function(e) {
                    console.log('[DEBUG QUANTITY] Change event - value is:', this.value);
                    console.log('[DEBUG QUANTITY] Event type:', e.type);
                });
                
                quantityInput.addEventListener('blur', function(e) {
                    console.log('[DEBUG QUANTITY] Blur event - final value:', this.value);
                });
                
                quantityInput.addEventListener('focus', function(e) {
                    console.log('[DEBUG QUANTITY] Focus event - current value:', this.value);
                });

                // Mode change handler
                document.querySelectorAll('input[name="multiple_mode"]').forEach(radio => {
                    radio.addEventListener('change', function() {
                        console.log('[DEBUG MODE] Mode changed to:', this.value);
                        const quantityInput = document.getElementById('quantity');
                        console.log('[DEBUG MODE] Quantity at mode change:', quantityInput.value);
                        updateMultipleLetterUI();
                        console.log('[DEBUG MODE] After updateMultipleLetterUI, quantity:', quantityInput.value);
                    });
                });

                // Copy first to all button
                document.getElementById('btn_copy_first_to_all').addEventListener('click', function() {
                    const firstCard = document.querySelector('.letter-item-card[data-index="0"]');
                    if (!firstCard) return;
                    
                    const firstSubject = firstCard.querySelector('input[name^="subjects"]').value;
                    const firstRecipient = firstCard.querySelector('input[name^="recipients"]').value;
                    
                    document.querySelectorAll('.letter-item-card').forEach((card, index) => {
                        if (index > 0) {
                            card.querySelector('input[name^="subjects"]').value = firstSubject;
                            card.querySelector('input[name^="recipients"]').value = firstRecipient;
                        }
                    });
                    
                    // Visual feedback
                    this.innerHTML = '<i data-lucide="check" class="w-3.5 h-3.5"></i> Berhasil disalin!';
                    this.classList.remove('text-gray-600', 'bg-gray-100');
                    this.classList.add('text-green-600', 'bg-green-100');
                    lucide.createIcons();
                    
                    setTimeout(() => {
                        this.innerHTML = '<i data-lucide="copy" class="w-3.5 h-3.5"></i> Salin dari Surat 1 ke Semua';
                        this.classList.remove('text-green-600', 'bg-green-100');
                        this.classList.add('text-gray-600', 'bg-gray-100');
                        lucide.createIcons();
                    }, 2000);
                });

                // Clear all button
                document.getElementById('btn_clear_all').addEventListener('click', function() {
                    if (confirm('Apakah Anda yakin ingin mengosongkan semua isian perihal dan tujuan?')) {
                        document.querySelectorAll('.letter-item-card input').forEach(input => {
                            input.value = '';
                        });
                    }
                });

                // Add error class if validation fails
                @if ($errors->has('letter_type_id'))
                    $('#letter_type_id').next('.select2-container').addClass('select2-error');
                @endif

                @if ($errors->has('classification_id'))
                    $('#classification_id').next('.select2-container').addClass('select2-error');
                @endif

                @if ($errors->has('security_classification'))
                    $('#security_classification').next('.select2-container').addClass('select2-error');
                @endif

                @if ($errors->has('letter_target'))
                    $('#letter_target').next('.select2-container').addClass('select2-error');
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

                // Initialize multiple letter UI based on old values
                @if (old('quantity', 1) > 1)
                    updateMultipleLetterUI();
                @endif
            });
        </script>
    @endpush
@endsection
