@extends('layouts.app')

@section('title', 'Edit Penandatangan')

@section('content')
<div class="max-w-4xl mx-auto">
    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
            <i data-lucide="pencil" class="w-6 h-6 text-brand"></i>
            Edit Penandatangan
        </h2>
        <a href="{{ route('master.signatories.index') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali
        </a>
    </div>

    {{-- Form Section --}}
    <div class="bg-white rounded-lg shadow border border-gray-200">
        <div class="p-6">
            <form method="POST" action="{{ route('master.signatories.update', $signatory) }}">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Kode --}}
                    <div class="space-y-2">
                        <label for="code" class="label">
                            Kode <span class="text-destructive">*</span>
                        </label>
                        <input type="text" 
                               class="input @error('code') !border-red-500 @enderror" 
                               id="code" 
                               name="code" 
                               value="{{ old('code', $signatory->code) }}" 
                               placeholder="Contoh: DEP-XTY"
                               required>
                        <p class="text-sm text-muted-foreground">Kode unik untuk penandatangan (digunakan dalam nomor surat)</p>
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
                            Nama Penandatangan <span class="text-destructive">*</span>
                        </label>
                        <input type="text" 
                               class="input @error('name') !border-red-500 @enderror" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $signatory->name) }}" 
                               placeholder="Contoh: Dr. Ahmad Wijaya, S.E., M.M."
                               required>
                        @error('name')
                            <p class="text-sm text-destructive flex items-center gap-1.5">
                                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Jabatan --}}
                    <div class="space-y-2">
                        <label for="position" class="label">
                            Jabatan <span class="text-destructive">*</span>
                        </label>
                        <input type="text" 
                               class="input @error('position') !border-red-500 @enderror" 
                               id="position" 
                               name="position" 
                               value="{{ old('position', $signatory->position) }}" 
                               placeholder="Contoh: Dekan FEB UNJ"
                               required>
                        @error('position')
                            <p class="text-sm text-destructive flex items-center gap-1.5">
                                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- NIP --}}
                    <div class="space-y-2">
                        <label for="nip" class="label">
                            NIP <span class="text-muted-foreground text-xs">(Opsional)</span>
                        </label>
                        <input type="text" 
                               class="input @error('nip') !border-red-500 @enderror" 
                               id="nip" 
                               name="nip" 
                               value="{{ old('nip', $signatory->nip) }}" 
                               placeholder="Contoh: 197501012000031001">
                        <p class="text-sm text-muted-foreground">Nomor Induk Pegawai (opsional)</p>
                        @error('nip')
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
                                       {{ old('is_active', $signatory->is_active) ? 'checked' : '' }}>
                            </div>
                            <div class="ml-3">
                                <label for="is_active" class="font-medium text-gray-700">Aktif</label>
                                <p class="text-sm text-muted-foreground">Centang jika penandatangan ini dapat digunakan untuk membuat surat</p>
                                @error('is_active')
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
                                <li>Kode penandatangan akan digunakan dalam format nomor surat</li>
                                <li>Contoh format: 001/<strong>DEP-XTY</strong>.VAL-ZJ/2025</li>
                                <li>Nonaktifkan penandatangan jika tidak lagi bertugas</li>
                                <li>Perubahan kode akan mempengaruhi nomor surat yang akan dibuat</li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="flex justify-between items-center pt-6 border-t border-gray-200 mt-6">
                    <a href="{{ route('master.signatories.index') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
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
                  action="{{ route('master.signatories.destroy', $signatory) }}" 
                  class="mt-6 pt-6 border-t border-gray-200"
                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus penandatangan ini?')">
                @csrf
                @method('DELETE')
                <div class="flex items-center justify-between bg-red-50 border border-red-200 rounded-lg p-4">
                    <div>
                        <h6 class="font-semibold text-red-900 mb-1">Hapus Penandatangan</h6>
                        <p class="text-sm text-red-700">Tindakan ini tidak dapat dibatalkan. Pastikan tidak ada surat yang menggunakan penandatangan ini.</p>
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






