@extends('layouts.app')

@section('title', 'Edit Klasifikasi Surat')

@section('content')
<div class="max-w-4xl mx-auto">
    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i data-lucide="pencil" class="w-6 h-6 text-brand"></i>
                Edit Klasifikasi Surat
            </h2>
            <p class="mt-1 text-sm text-gray-500">Ubah informasi klasifikasi yang ada</p>
        </div>
        <a href="{{ route('master.classification-letters.index') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali
        </a>
    </div>

    {{-- Form Section --}}
    <div class="bg-white rounded-lg shadow border border-gray-200">
        <div class="p-6">
            <form method="POST" action="{{ route('master.classification-letters.update', $classificationLetter) }}">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Kode --}}
                    <div class="space-y-2">
                        <label for="code" class="label">
                            Kode <span class="text-destructive">*</span>
                        </label>
                        <input type="text" 
                               id="code" 
                               name="code" 
                               value="{{ old('code', $classificationLetter->code) }}" 
                               class="input @error('code') !border-red-500 @enderror" 
                               placeholder="Contoh: VAL-ZJ"
                               required>
                        <p class="text-sm text-muted-foreground">Kode unik untuk klasifikasi surat</p>
                        @error('code')
                            <p class="text-sm text-destructive flex items-center gap-1.5">
                                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Nama --}}
                    <div class="space-y-2">
                        <label for="name" class="label">
                            Nama Klasifikasi <span class="text-destructive">*</span>
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $classificationLetter->name) }}" 
                               class="input @error('name') !border-red-500 @enderror" 
                               placeholder="Contoh: Validasi Berkas Ijazah"
                               required>
                        @error('name')
                            <p class="text-sm text-destructive flex items-center gap-1.5">
                                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Deskripsi --}}
                    <div class="md:col-span-2 space-y-2">
                        <label for="description" class="label">
                            Deskripsi <span class="text-muted-foreground text-xs">(Opsional)</span>
                        </label>
                        <textarea id="description" 
                                  name="description" 
                                  rows="3" 
                                  class="input resize-none @error('description') !border-red-500 @enderror" 
                                  placeholder="Masukkan deskripsi klasifikasi">{{ old('description', $classificationLetter->description) }}</textarea>
                        @error('description')
                            <p class="text-sm text-destructive flex items-center gap-1.5">
                                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Status --}}
                    <div class="md:col-span-2">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input type="checkbox" 
                                       id="is_active" 
                                       name="is_active" 
                                       value="1" 
                                       {{ old('is_active', $classificationLetter->is_active) ? 'checked' : '' }}
                                       class="w-4 h-4 text-brand border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-colors">
                            </div>
                            <div class="ml-3">
                                <label for="is_active" class="font-medium text-gray-700">Aktif</label>
                                <p class="text-sm text-muted-foreground">Centang jika klasifikasi ini dapat digunakan untuk membuat surat</p>
                            </div>
                        </div>
                    </div>

                    {{-- Informasi Tambahan --}}
                    <div class="md:col-span-2">
                        <div class="bg-orange-50 border border-blue-200 rounded-lg p-4">
                            <h6 class="font-semibold text-brand-darker mb-2 flex items-center gap-2">
                                <i data-lucide="info" class="w-4 h-4"></i>
                                Informasi Penting
                            </h6>
                            <ul class="list-disc list-inside text-sm text-brand-darker space-y-1">
                                <li>Kode klasifikasi akan digunakan dalam format nomor surat</li>
                                <li>Contoh format: 001/DEP-XTY.<strong>{{ $classificationLetter->code }}</strong>/2025</li>
                                <li>Nonaktifkan klasifikasi jika tidak lagi digunakan</li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="flex justify-between items-center pt-6 border-t border-gray-200 mt-6">
                    <a href="{{ route('master.classification-letters.index') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        <i data-lucide="x-circle" class="w-4 h-4"></i>
                        Batal
                    </a>
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </form>

            {{-- Delete Form (Outside Update Form) --}}
            @if(auth()->user()->isAdmin())
                <form method="POST" 
                      action="{{ route('master.classification-letters.destroy', $classificationLetter) }}" 
                      class="mt-6 pt-6 border-t border-gray-200"
                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus klasifikasi ini?')">
                    @csrf
                    @method('DELETE')
                    <div class="flex items-center justify-between bg-red-50 border border-red-200 rounded-lg p-4">
                        <div>
                            <h6 class="font-semibold text-red-900 mb-1">Hapus Klasifikasi Surat</h6>
                            <p class="text-sm text-red-700">Tindakan ini tidak dapat dibatalkan. Pastikan tidak ada surat yang menggunakan klasifikasi ini.</p>
                        </div>
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                            Hapus
                        </button>
                    </div>
                </form>
            @endif
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






