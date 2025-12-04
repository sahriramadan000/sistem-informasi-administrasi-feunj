@extends('layouts.app')

@section('title', 'Edit Jenis Surat')

@section('content')
<div class="max-w-4xl mx-auto">
    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
            <i data-lucide="pencil" class="w-6 h-6 text-brand"></i>
            Edit Jenis Surat
        </h2>
        <a href="{{ route('master.letter-types.index') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali
        </a>
    </div>

    {{-- Form Section --}}
    <div class="bg-white rounded-lg shadow border border-gray-200">
        <div class="p-6">
            <form method="POST" action="{{ route('master.letter-types.update', $letterType) }}">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Kode --}}
                    <div class="space-y-2">
                        <label for="code" class="label">
                            Kode <span class="text-muted-foreground text-xs">(Opsional)</span>
                        </label>
                        <input type="text" 
                               class="input @error('code') !border-red-500 @enderror" 
                               id="code" 
                               name="code" 
                               value="{{ old('code', $letterType->code) }}" 
                               placeholder="Contoh: ST, SK">
                        <p class="text-sm text-muted-foreground">Kode unik untuk jenis surat (opsional)</p>
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
                            Nama Jenis Surat <span class="text-destructive">*</span>
                        </label>
                        <input type="text" 
                               class="input @error('name') !border-red-500 @enderror" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $letterType->name) }}" 
                               placeholder="Contoh: Surat Tugas"
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
                        <label for="description" class="label">Deskripsi</label>
                        <textarea class="input resize-none @error('description') !border-red-500 @enderror" 
                                  id="description" 
                                  name="description" 
                                  rows="3" 
                                  placeholder="Masukkan deskripsi jenis surat (opsional)">{{ old('description', $letterType->description) }}</textarea>
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
                                <input class="w-4 h-4 text-brand border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-colors @error('is_active') border-red-500 @enderror" 
                                       type="checkbox" 
                                       id="is_active" 
                                       name="is_active" 
                                       value="1" 
                                       {{ old('is_active', $letterType->is_active) ? 'checked' : '' }}>
                            </div>
                            <div class="ml-3">
                                <label for="is_active" class="font-medium text-gray-700">Aktif</label>
                                <p class="text-sm text-muted-foreground">Centang jika jenis surat ini dapat digunakan untuk membuat surat</p>
                                @error('is_active')
                                    <p class="text-sm text-destructive flex items-center gap-1.5">
                                        <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Memerlukan Keperluan --}}
                    <div class="md:col-span-2">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input class="w-4 h-4 text-brand border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-colors @error('requires_purpose') border-red-500 @enderror" 
                                       type="checkbox" 
                                       id="requires_purpose" 
                                       name="requires_purpose" 
                                       value="1" 
                                       {{ old('requires_purpose', $letterType->requires_purpose) ? 'checked' : '' }}>
                            </div>
                            <div class="ml-3">
                                <label for="requires_purpose" class="font-medium text-gray-700">Memerlukan Keperluan Surat</label>
                                <p class="text-sm text-muted-foreground">Centang jika jenis surat ini memerlukan keperluan dan nama mahasiswa</p>
                                @error('requires_purpose')
                                    <p class="text-sm text-destructive flex items-center gap-1.5">
                                        <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
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
                                <li>Perubahan kode akan mempengaruhi surat yang sudah dibuat</li>
                                <li>Nonaktifkan jenis surat jika tidak lagi digunakan</li>
                                <li>Jenis surat yang aktif akan muncul di dropdown saat membuat surat</li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="flex justify-between items-center pt-6 border-t border-gray-200 mt-6">
                    <a href="{{ route('master.letter-types.index') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
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
            <form method="POST" 
                  action="{{ route('master.letter-types.destroy', $letterType) }}" 
                  class="mt-6 pt-6 border-t border-gray-200"
                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus jenis surat ini?')">
                @csrf
                @method('DELETE')
                <div class="flex items-center justify-between bg-red-50 border border-red-200 rounded-lg p-4">
                    <div>
                        <h6 class="font-semibold text-red-900 mb-1">Hapus Jenis Surat</h6>
                        <p class="text-sm text-red-700">Tindakan ini tidak dapat dibatalkan. Pastikan tidak ada surat yang menggunakan jenis ini.</p>
                    </div>
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                        Hapus
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






