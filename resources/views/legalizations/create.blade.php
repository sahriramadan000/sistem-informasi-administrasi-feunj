@extends('layouts.app')

@section('title', 'Buat Legalisir')

@section('content')
    <div class="max-w-4xl mx-auto">
        {{-- Header --}}
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                    <i data-lucide="plus-circle" class="w-6 h-6 text-brand"></i>
                    Buat Transaksi Legalisir
                </h2>
                <p class="mt-1 text-sm text-gray-500">Catat transaksi legalisir dokumen alumni baru</p>
            </div>
            <a href="{{ route('legalizations.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Kembali
            </a>
        </div>

        {{-- Form Section --}}
        <div class="bg-white rounded-lg shadow border border-gray-200">
            <div class="p-8">
                <form method="POST" action="{{ route('legalizations.store') }}" id="formLegalisir">
                    @csrf

                    {{-- SECTION 1: Detail Alumni --}}
                    <div class="mb-8">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="flex items-center justify-center w-10 h-10 rounded-full bg-brand-lighter">
                                <span class="text-brand font-bold text-lg">1</span>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Detail Alumni & Tanggal</h3>
                                <p class="text-sm text-gray-500">Informasi identitas alumni dan waktu legalisir</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pl-0 md:pl-13">
                            {{-- Tanggal --}}
                            <div class="space-y-2">
                                <label for="date" class="label flex items-center gap-2">
                                    <i data-lucide="calendar" class="w-4 h-4 text-gray-400"></i>
                                    Tanggal <span class="text-destructive">*</span>
                                </label>
                                <input type="text" id="date" name="date" value="{{ old('date', date('Y-m-d')) }}"
                                    class="input flatpickr-input @error('date') !border-red-500 @enderror" placeholder="Pilih tanggal" readonly required>
                                @error('date')
                                    <p class="text-sm text-destructive flex items-center gap-1.5">
                                        <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Tahun Lulus --}}
                            <div class="space-y-2">
                                <label for="graduation_year" class="label flex items-center gap-2">
                                    <i data-lucide="graduation-cap" class="w-4 h-4 text-gray-400"></i>
                                    Tahun Lulus <span class="text-destructive">*</span>
                                </label>
                                <input type="number" id="graduation_year" name="graduation_year"
                                    value="{{ old('graduation_year', date('Y')) }}" min="1900" max="{{ date('Y') + 1 }}"
                                    class="input @error('graduation_year') !border-red-500 @enderror" required>
                                @error('graduation_year')
                                    <p class="text-sm text-destructive flex items-center gap-1.5">
                                        <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Nama Alumni --}}
                            <div class="space-y-2 md:col-span-2">
                                <label for="alumni_name" class="label flex items-center gap-2">
                                    <i data-lucide="user" class="w-4 h-4 text-gray-400"></i>
                                    Nama Alumni <span class="text-destructive">*</span>
                                </label>
                                <input type="text" id="alumni_name" name="alumni_name" value="{{ old('alumni_name') }}"
                                    class="input @error('alumni_name') !border-red-500 @enderror"
                                    placeholder="Nama lengkap alumni" required>
                                @error('alumni_name')
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

                    {{-- SECTION 2: Detail Legalisir --}}
                    <div class="mb-8">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="flex items-center justify-center w-10 h-10 rounded-full bg-brand-lighter">
                                <span class="text-brand font-bold text-lg">2</span>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Dokumen & Biaya</h3>
                                <p class="text-sm text-gray-500">Pilih jenjang pendidikan dan jumlah lembar untuk kalkulasi
                                    tagihan</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pl-0 md:pl-13">
                            {{-- Jenjang --}}
                            <div class="space-y-2">
                                <label for="education_level_id" class="label flex items-center gap-2">
                                    <i data-lucide="book-open" class="w-4 h-4 text-gray-400"></i>
                                    Jenjang Pendidikan <span class="text-destructive">*</span>
                                </label>
                                <select id="education_level_id" name="education_level_id"
                                    class="select @error('education_level_id') !border-red-500 @enderror" required
                                    onchange="calculateTotal()">
                                    <option value="">-- Pilih Jenjang --</option>
                                    @foreach ($educationLevels as $level)
                                        <option value="{{ $level->id }}" data-price="{{ $level->price_per_page }}"
                                            {{ old('education_level_id') == $level->id ? 'selected' : '' }}>
                                            {{ $level->name }} - Rp
                                            {{ number_format($level->price_per_page, 0, ',', '.') }}/lembar
                                        </option>
                                    @endforeach
                                </select>
                                @error('education_level_id')
                                    <p class="text-sm text-destructive flex items-center gap-1.5">
                                        <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Jumlah Lembar --}}
                            <div class="space-y-2">
                                <label for="page_count" class="label flex items-center gap-2">
                                    <i data-lucide="files" class="w-4 h-4 text-gray-400"></i>
                                    Jumlah Lembar <span class="text-destructive">*</span>
                                </label>
                                <input type="number" id="page_count" name="page_count" value="{{ old('page_count', 1) }}"
                                    min="1" class="input @error('page_count') !border-red-500 @enderror" required
                                    oninput="calculateTotal()">
                                @error('page_count')
                                    <p class="text-sm text-destructive flex items-center gap-1.5">
                                        <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                        {{-- Total Harga Card --}}
                        <div class="mt-8 pl-0 md:pl-13">
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
                                                <p class="text-sm font-semibold text-orange-800 uppercase tracking-wide">
                                                    Total Tagihan Legalisir</p>
                                            </div>
                                            <p class="text-gray-600 text-sm">
                                                Dihitung otomatis berdasarkan jenjang dan jumlah lembar.
                                            </p>
                                        </div>
                                    </div>

                                    <div
                                        class="text-right w-full md:w-auto mt-4 md:mt-0 pt-4 md:pt-0 border-t md:border-t-0 border-orange-200/50">
                                        <p class="text-3xl font-bold text-gray-900" id="total_price_display">
                                            Rp 0
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Form Actions --}}
                    <div class="flex justify-between items-center pt-6 border-t">
                        <a href="{{ route('legalizations.index') }}" class="btn-outline gap-2">
                            <i data-lucide="arrow-left" class="w-4 h-4"></i>
                            Kembali
                        </a>
                        <button type="submit" id="btnSubmit" class="btn-primary gap-2 px-8 py-3 text-base">
                            <i data-lucide="check-circle" class="w-5 h-5"></i>
                            <span>Simpan & Terbitkan No. Legalisir</span>
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

            function calculateTotal() {
                const select = document.getElementById('education_level_id');
                const pageCountInput = document.getElementById('page_count');
                const display = document.getElementById('total_price_display');

                let price = 0;
                let count = parseInt(pageCountInput.value) || 0;

                if (select && select.selectedIndex > 0) {
                    const option = select.options[select.selectedIndex];
                    price = parseFloat(option.getAttribute('data-price')) || 0;
                }

                const total = price * count;

                // Format to Rupiah
                display.textContent = 'Rp ' + total.toLocaleString('id-ID');
            }

            // Call on load in case of old values
            document.addEventListener('DOMContentLoaded', function() {
                calculateTotal();

                // Initialize Flatpickr
                flatpickr("#date", {
                    dateFormat: "Y-m-d",
                    locale: "id",
                    altInput: true,
                    altFormat: "d F Y",
                    allowInput: true,
                    disableMobile: true,
                    defaultDate: "{{ old('date', date('Y-m-d')) }}"
                });

                // Re-initialize select2 if available, like in letters.create
                if (typeof jQuery !== 'undefined' && $.fn.select2) {
                    $('#education_level_id').select2({
                        placeholder: '-- Pilih Jenjang --',
                        allowClear: true,
                        width: '100%'
                    }).on('change', function() {
                        calculateTotal();
                    });
                }

                // Prevent double submit
                const form = document.getElementById('formLegalisir');
                if (form) {
                    form.addEventListener('submit', function() {
                        const btn = document.getElementById('btnSubmit');
                        if (btn) {
                            btn.disabled = true;
                            btn.classList.add('opacity-75', 'cursor-not-allowed');
                            btn.innerHTML = '<i data-lucide="loader-2" class="w-5 h-5 animate-spin"></i><span>Menyimpan...</span>';
                            lucide.createIcons();
                        }
                    });
                }
            });
        </script>
    @endpush
@endsection
