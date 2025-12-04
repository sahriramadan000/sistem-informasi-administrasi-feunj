@extends('layouts.app')

@section('title', 'Edit Pengguna')

@section('content')
<div class="max-w-4xl mx-auto">
    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i data-lucide="user-cog" class="w-6 h-6 text-brand"></i>
                Edit Pengguna
            </h2>
            <p class="mt-1 text-sm text-gray-500">Perbarui data pengguna: {{ $user->name }}</p>
        </div>
        <a href="{{ route('master.users.index') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali
        </a>
    </div>

    {{-- Form Section --}}
    <div class="bg-white rounded-lg shadow border border-gray-200">
        <div class="p-6">
            <form method="POST" action="{{ route('master.users.update', $user) }}">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 gap-6">
                    {{-- Nama --}}
                    <div class="space-y-2">
                        <label for="name" class="label">
                            Nama Lengkap <span class="text-destructive">*</span>
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $user->name) }}" 
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
                               value="{{ old('email', $user->email) }}" 
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
                               value="{{ old('username', $user->username) }}" 
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
                            Password <span class="text-muted-foreground text-xs">(Kosongkan jika tidak ingin mengubah)</span>
                        </label>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               class="input @error('password') !border-red-500 @enderror" 
                               placeholder="Minimal 6 karakter">
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
                            Konfirmasi Password <span class="text-muted-foreground text-xs">(Jika password diisi)</span>
                        </label>
                        <input type="password" 
                               id="password_confirmation" 
                               name="password_confirmation" 
                               class="input @error('password_confirmation') !border-red-500 @enderror" 
                               placeholder="Ulangi password">
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
                            <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="operator" {{ old('role', $user->role) === 'operator' ? 'selected' : '' }}>Operator</option>
                            <option value="viewer" {{ old('role', $user->role) === 'viewer' ? 'selected' : '' }}>Viewer</option>
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
                                       {{ old('is_active', $user->is_active) ? 'checked' : '' }}
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
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <h6 class="font-semibold text-yellow-900 mb-2 flex items-center gap-2">
                                <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                                Peringatan
                            </h6>
                            <ul class="list-disc list-inside text-sm text-yellow-800 space-y-1">
                                <li>Password hanya akan diubah jika Anda mengisi field password</li>
                                <li>Menonaktifkan pengguna akan mencegah akses ke sistem</li>
                                <li>Mengubah role akan mengubah hak akses pengguna</li>
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
                        <i data-lucide="save" class="w-4 h-4"></i>
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Separate Delete Form --}}
    @if($user->id !== auth()->id())
    <div class="bg-white rounded-lg shadow border border-red-200 mt-6">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-red-900 flex items-center gap-2 mb-2">
                <i data-lucide="trash-2" class="w-5 h-5"></i>
                Hapus Pengguna
            </h3>
            <p class="text-sm text-gray-600 mb-4">
                Setelah pengguna dihapus, semua data dan informasinya akan dihapus secara permanen.
            </p>
            <form method="POST" 
                  action="{{ route('master.users.destroy', $user) }}" 
                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini? Tindakan ini tidak dapat dibatalkan.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                    Hapus Pengguna
                </button>
            </form>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script src="https://unpkg.com/lucide@latest"></script>
<script>
    lucide.createIcons();
</script>
@endpush
@endsection






