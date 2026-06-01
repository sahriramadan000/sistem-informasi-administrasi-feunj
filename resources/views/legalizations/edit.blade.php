@extends('layouts.app')

@section('title', 'Edit Legalisir')

@section('content')
    <div class="max-w-4xl mx-auto">
        {{-- Header --}}
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                    <i data-lucide="edit" class="w-6 h-6 text-brand"></i>
                    Edit Legalisir
                </h2>
                <p class="mt-1 text-sm text-gray-500">Perbarui informasi transaksi legalisir dokumen</p>
            </div>
            <a href="{{ route('legalizations.show', $legalization) }}"
                class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
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
                    <p class="text-sm text-amber-700 mb-2">
                        Nomor seri berjalan tidak dapat diubah untuk menjaga konsistensi urutan legalisir.
                    </p>
                </div>
            </div>
        </div>

        {{-- Form Section --}}
        <div class="bg-white rounded-lg shadow border border-gray-200">
            <div class="p-8">
                <form action="{{ route('legalizations.update', $legalization) }}" method="POST" id="legalizationForm">
                    @csrf
                    @method('PUT')

                    {{-- SECTION 1: Detail Alumni --}}
                    <div class="mb-8">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="flex items-center justify-center w-10 h-10 rounded-full bg-brand-lighter">
                                <span class="text-brand font-bold text-lg">1</span>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Detail Alumni & Tanggal</h3>
                                <p class="text-sm text-gray-500">Informasi pemohon legalisir</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pl-0 md:pl-13">
                            {{-- Tanggal --}}
                            <div class="space-y-2">
                                <label for="date" class="label flex items-center gap-2">
                                    <i data-lucide="calendar" class="w-4 h-4 text-gray-400"></i>
                                    Tanggal Transaksi <span class="text-destructive">*</span>
                                </label>
                                <input type="text" id="date" name="date"
                                    value="{{ old('date', $legalization->date->format('Y-m-d')) }}"
                                    class="input flatpickr-input @error('date') !border-red-500 @enderror"
                                    placeholder="Pilih tanggal" readonly required>
                                @error('date')
                                    <p class="text-sm text-destructive flex items-center gap-1.5 mt-1">
                                        <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Nama Alumni --}}
                            <div class="space-y-2">
                                <label for="alumni_name" class="label flex items-center gap-2">
                                    <i data-lucide="user" class="w-4 h-4 text-gray-400"></i>
                                    Nama Lengkap Alumni <span class="text-destructive">*</span>
                                </label>
                                <input type="text" id="alumni_name" name="alumni_name"
                                    class="input @error('alumni_name') !border-red-500 @enderror"
                                    value="{{ old('alumni_name', $legalization->alumni_name) }}"
                                    placeholder="Masukkan nama alumni" required>
                                @error('alumni_name')
                                    <p class="text-sm text-destructive flex items-center gap-1.5 mt-1">
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
                                    class="input @error('graduation_year') !border-red-500 @enderror"
                                    value="{{ old('graduation_year', $legalization->graduation_year) }}"
                                    placeholder="Contoh: 2023" min="1900" max="{{ date('Y') + 1 }}" required>
                                @error('graduation_year')
                                    <p class="text-sm text-destructive flex items-center gap-1.5 mt-1">
                                        <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Divider --}}
                    <div class="border-t border-gray-200 my-8"></div>

                    {{-- SECTION 2: Dokumen & Biaya --}}
                    <div class="mb-8">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="flex items-center justify-center w-10 h-10 rounded-full bg-brand-lighter">
                                <span class="text-brand font-bold text-lg">2</span>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Dokumen & Biaya</h3>
                                <p class="text-sm text-gray-500">Tentukan tarif berdasarkan jenjang dan jumlah dokumen</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pl-0 md:pl-13">
                            {{-- Jenjang Pendidikan --}}
                            <div class="space-y-2">
                                <label for="education_level_id" class="label flex items-center gap-2">
                                    <i data-lucide="book-open" class="w-4 h-4 text-gray-400"></i>
                                    Jenjang Pendidikan <span class="text-destructive">*</span>
                                </label>
                                <select id="education_level_id" name="education_level_id"
                                    class="select @error('education_level_id') !border-red-500 @enderror" required>
                                    <option value="">Pilih Jenjang...</option>
                                    @foreach ($educationLevels as $level)
                                        <option value="{{ $level->id }}" data-price="{{ $level->price_per_page }}"
                                            {{ old('education_level_id', $legalization->education_level_id) == $level->id ? 'selected' : '' }}>
                                            {{ $level->name }} - Rp
                                            {{ number_format($level->price_per_page, 0, ',', '.') }}/lembar
                                        </option>
                                    @endforeach
                                </select>
                                @error('education_level_id')
                                    <p class="text-sm text-destructive flex items-center gap-1.5 mt-1">
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
                                <input type="number" id="page_count" name="page_count"
                                    class="input @error('page_count') !border-red-500 @enderror"
                                    value="{{ old('page_count', $legalization->page_count) }}" placeholder="Contoh: 5"
                                    min="1" required>
                                @error('page_count')
                                    <p class="text-sm text-destructive flex items-center gap-1.5 mt-1">
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
                                                    Total Tagihan</p>
                                                <span
                                                    class="md:hidden text-xs px-2 py-1 bg-white rounded-md font-medium text-orange-700 shadow-sm border border-orange-200"
                                                    id="mobile-calc-summary">
                                                    0 × Rp 0
                                                </span>
                                            </div>
                                            <p class="text-gray-600 text-sm hidden md:block" id="calc-summary">
                                                <span id="summary-pages">0</span> Lembar × Rp <span
                                                    id="summary-price">0</span>
                                            </p>
                                        </div>
                                    </div>

                                    <div
                                        class="text-right w-full md:w-auto mt-4 md:mt-0 pt-4 md:pt-0 border-t md:border-t-0 border-orange-200/50">
                                        <p class="text-sm font-medium text-orange-700 mb-1">Total Biaya Legalisir</p>
                                        <p class="text-3xl font-bold text-gray-900">
                                            Rp <span id="display-total">0</span>
                                        </p>
                                        <input type="hidden" name="total_price" id="total_price"
                                            value="{{ $legalization->total_price }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Form Actions --}}
                    <div class="flex justify-between items-center pt-6 border-t border-gray-200 mt-8">
                        <a href="{{ route('legalizations.show', $legalization) }}" class="btn-outline gap-2">
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

            document.addEventListener('DOMContentLoaded', function() {
                // Initialize Flatpickr
                flatpickr("#date", {
                    dateFormat: "Y-m-d",
                    locale: "id",
                    altInput: true,
                    altFormat: "d F Y",
                    allowInput: true,
                    disableMobile: true,
                    defaultDate: "{{ old('date', $legalization->date->format('Y-m-d')) }}"
                });

                const educationLevelSelect = document.getElementById('education_level_id');
                const pageCountInput = document.getElementById('page_count');
                const displayTotal = document.getElementById('display-total');
                const hiddenTotal = document.getElementById('total_price');

                const summaryPages = document.getElementById('summary-pages');
                const summaryPrice = document.getElementById('summary-price');
                const mobileCalcSummary = document.getElementById('mobile-calc-summary');

                function formatRupiah(number) {
                    return new Intl.NumberFormat('id-ID').format(number);
                }

                function calculateTotal() {
                    const selectedOption = educationLevelSelect.options[educationLevelSelect.selectedIndex];
                    const pricePerPage = selectedOption.value ? parseFloat(selectedOption.dataset.price) : 0;
                    const pageCount = parseInt(pageCountInput.value) || 0;

                    const total = pricePerPage * pageCount;

                    displayTotal.textContent = formatRupiah(total);
                    hiddenTotal.value = total;

                    summaryPages.textContent = pageCount;
                    summaryPrice.textContent = formatRupiah(pricePerPage);
                    mobileCalcSummary.textContent = `${pageCount} × Rp ${formatRupiah(pricePerPage)}`;
                }

                educationLevelSelect.addEventListener('change', calculateTotal);
                pageCountInput.addEventListener('input', calculateTotal);

                // Initial calculation
                calculateTotal();
            });
        </script>
    @endpush
@endsection
