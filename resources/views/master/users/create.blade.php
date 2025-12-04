@extends('layouts.app')

@section('title', 'Tambah Pengguna')

@section('content')
<div class="max-w-4xl mx-auto">
    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i data-lucide="user-plus" class="w-6 h-6 text-brand"></i>
                Tambah Pengguna
            </h2>
            <p class="mt-1 text-sm text-gray-500">Buat pengguna baru untuk sistem</p>
        </div>
        <a href="{{ route('master.users.index') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali
        </a>
    </div>

    {{-- Form Section --}}
    <div class="bg-white rounded-lg shadow border border-gray-200">
        <div class="p-6">
            <form method="POST" action="{{ route('master.users.store') }}">
                @csrf
                
                <div class="grid grid-cols-1 gap-6">
                    {{-- Nama --}}
                    <div class="space-y-2">
                        <label for="name" class="label">
                            Nama Lengkap <span class="text-destructive">*</span>
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name') }}" 
                               class="input @error('name') !border-red-500 @enderror" 
                               placeholder="Contoh: John Doe"
                               required>
                        @error('name')
                            <p class="text-sm text-destructive flex items-center gap-1.5">
                                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="space-y-2">
                        <label for="email" class="label">
                            Email <span class="text-destructive">*</span>
                        </label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               class="input @error('email') !border-red-500 @enderror" 
                               placeholder="contoh@email.com"
                               required>
                        @error('email')
                            <p class="text-sm text-destructive flex items-center gap-1.5">
                                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Username --}}
                    <div class="space-y-2">
                        <label for="username" class="label">
                            Username <span class="text-destructive">*</span>
                        </label>
                        <input type="text" 
                               id="username" 
                               name="username" 
                               value="{{ old('username') }}" 
                               class="input @error('username') !border-red-500 @enderror" 
                               placeholder="johndoe"
                               required>
                        @error('username')
                            <p class="text-sm text-destructive flex items-center gap-1.5">
                                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="space-y-2">
                        <label for="password" class="label">
                            Password <span class="text-destructive">*</span>
                        </label>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               class="input @error('password') !border-red-500 @enderror" 
                               placeholder="Minimal 6 karakter"
                               required>
                        @error('password')
                            <p class="text-sm text-destructive flex items-center gap-1.5">
                                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div class="space-y-2">
                        <label for="password_confirmation" class="label">
                            Konfirmasi Password <span class="text-destructive">*</span>
                        </label>
                        <input type="password" 
                               id="password_confirmation" 
                               name="password_confirmation" 
                               class="input @error('password_confirmation') !border-red-500 @enderror" 
                               placeholder="Ulangi password"
                               required>
                        @error('password_confirmation')
                            <p class="text-sm text-destructive flex items-center gap-1.5">
                                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Role --}}
                    <div class="space-y-2">
                        <label for="role" class="label">
                            Role <span class="text-destructive">*</span>
                        </label>
                        <select id="role" 
                                name="role" 
                                class="select @error('role') !border-red-500 @enderror"
                                required>
                            <option value="">Pilih Role</option>
                            <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="operator" {{ old('role') === 'operator' ? 'selected' : '' }}>Operator</option>
                            <option value="viewer" {{ old('role') === 'viewer' ? 'selected' : '' }}>Viewer</option>
                        </select>
                        @error('role')
                            <p class="text-sm text-destructive flex items-center gap-1.5">
                                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                {{ $message }}
                            </p>
                        @enderror
                        <p class="text-sm text-muted-foreground">
                            <strong>Admin:</strong> Akses penuh, 
                            <strong>Operator:</strong> Buat & edit surat, 
                            <strong>Viewer:</strong> Hanya melihat
                        </p>
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
                                <p class="text-sm text-muted-foreground">Centang jika pengguna dapat mengakses sistem</p>
                            </div>
                        </div>
                    </div>

                    {{-- Informasi Tambahan --}}
                    <div>
                        <div class="bg-orange-50 border border-blue-200 rounded-lg p-4">
                            <h6 class="font-semibold text-brand-darker mb-2 flex items-center gap-2">
                                <i data-lucide="info" class="w-4 h-4"></i>
                                Informasi Role
                            </h6>
                            <ul class="list-disc list-inside text-sm text-brand-darker space-y-1">
                                <li><strong>Admin:</strong> Dapat mengelola semua data master dan pengguna</li>
                                <li><strong>Operator:</strong> Dapat membuat dan mengelola surat</li>
                                <li><strong>Viewer:</strong> Hanya dapat melihat data surat tanpa dapat mengubah</li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="flex justify-between items-center pt-6 border-t border-gray-200 mt-6">
                    <a href="{{ route('master.users.index') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
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






