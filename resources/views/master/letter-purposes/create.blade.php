@extends('layouts.app')

@section('title', 'Tambah Keperluan Surat')

@section('content')
<div class="max-w-4xl mx-auto">
    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i data-lucide="plus-circle" class="w-6 h-6 text-brand"></i>
                Tambah Keperluan Surat
            </h2>
            <p class="mt-1 text-sm text-gray-500">Buat keperluan baru untuk surat mahasiswa</p>
        </div>
        <a href="{{ route('master.letter-purposes.index') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali
        </a>
    </div>

    {{-- Form Section --}}
    <div class="bg-white rounded-lg shadow border border-gray-200">
        <div class="p-6">
            <form method="POST" action="{{ route('master.letter-purposes.store') }}">
                @csrf
                
                <div class="grid grid-cols-1 gap-6">
                    {{-- Nama --}}
                    <div class="space-y-2">
                        <label for="name" class="label">
                            Nama Keperluan <span class="text-destructive">*</span>
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name') }}" 
                               class="input @error('name') !border-red-500 @enderror" 
                               placeholder="Contoh: Legalisir Ijazah"
                               required>
                        @error('name')
                            <p class="text-sm text-destructive flex items-center gap-1.5">
                                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Deskripsi --}}
                    <div class="space-y-2">
                        <label for="description" class="label">
                            Deskripsi <span class="text-muted-foreground text-xs">(Opsional)</span>
                        </label>
                        <textarea id="description" 
                                  name="description" 
                                  rows="3" 
                                  class="input resize-none @error('description') !border-red-500 @enderror" 
                                  placeholder="Masukkan deskripsi keperluan">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-sm text-destructive flex items-center gap-1.5">
                                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Status --}}
                    <div>
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" 
                                       id="is_active" 
                                       name="is_active" 
                                       value="1" 
                                       {{ old('is_active', true) ? 'checked' : '' }}
                                       class="w-4 h-4 text-brand border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-colors">
                            </div>
                            <div class="ml-3">
                                <label for="is_active" class="font-medium text-gray-700">Aktif</label>
                                <p class="text-sm text-muted-foreground">Centang jika keperluan ini dapat digunakan</p>
                            </div>
                        </div>
                    </div>

                    {{-- Informasi Tambahan --}}
                    <div>
                        <div class="bg-orange-50 border border-blue-200 rounded-lg p-4">
                            <h6 class="font-semibold text-brand-darker mb-2 flex items-center gap-2">
                                <i data-lucide="info" class="w-4 h-4"></i>
                                Informasi
                            </h6>
                            <ul class="list-disc list-inside text-sm text-brand-darker space-y-1">
                                <li>Keperluan surat akan ditampilkan saat jenis surat memerlukan keperluan</li>
                                <li>Nama keperluan harus jelas dan spesifik</li>
                                <li>Keperluan yang aktif akan muncul di dropdown saat membuat surat</li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="flex justify-between items-center pt-6 border-t border-gray-200 mt-6">
                    <a href="{{ route('master.letter-purposes.index') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        <i data-lucide="x-circle" class="w-4 h-4"></i>
                        Batal
                    </a>
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                        Simpan
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
</script>
@endpush
@endsection






