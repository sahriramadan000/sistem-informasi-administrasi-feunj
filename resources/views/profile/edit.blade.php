@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i data-lucide="user-circle" class="w-6 h-6 text-brand"></i>
                Edit Profile
            </h2>
            <p class="mt-1 text-sm text-gray-500">Perbarui informasi profile dan keamanan akun Anda</p>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- CARD 1: Profile Information --}}
    {{-- ============================================================ --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

        {{-- Gradient Header --}}
        <div class="px-6 py-4 bg-gradient-to-r from-gray-700 to-gray-900">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-white/20">
                    <i data-lucide="user" class="h-5 w-5 text-white"></i>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-white">Informasi Profile</h3>
                    <p class="text-xs text-white/80">Perbarui nama, email, dan username Anda</p>
                </div>
            </div>
        </div>

        <div class="p-6">
            {{-- Avatar Section --}}
            <div class="flex items-center gap-4 mb-6 pb-6 border-b border-gray-100">
                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-brand text-white text-xl font-bold flex-shrink-0 shadow-md">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
                <div>
                    <h3 class="text-base font-semibold text-gray-900">{{ $user->name }}</h3>
                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-700 mt-1">
                        {{ ucfirst($user->role) }}
                    </span>
                </div>
            </div>

            {{-- Form --}}
            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('PATCH')

                <div class="grid grid-cols-1 gap-5">

                    {{-- Nama Lengkap --}}
                    <div class="space-y-1.5">
                        <label for="name" class="block text-sm font-medium text-gray-700">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                                <i data-lucide="user" class="h-4 w-4 text-gray-400"></i>
                            </span>
                            <input type="text"
                                   name="name"
                                   id="name"
                                   value="{{ old('name', $user->name) }}"
                                   required
                                   placeholder="Contoh: John Doe"
                                   class="block w-full rounded-lg border border-gray-300 py-2.5 pl-10 pr-4 text-sm text-gray-900 placeholder-gray-400 focus:border-brand focus:ring-2 focus:ring-brand/20 transition-colors @error('name') border-red-400 bg-red-50 @enderror">
                        </div>
                        @error('name')
                            <p class="text-xs text-red-600 flex items-center gap-1">
                                <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="space-y-1.5">
                        <label for="email" class="block text-sm font-medium text-gray-700">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                                <i data-lucide="mail" class="h-4 w-4 text-gray-400"></i>
                            </span>
                            <input type="email"
                                   name="email"
                                   id="email"
                                   value="{{ old('email', $user->email) }}"
                                   required
                                   placeholder="contoh@email.com"
                                   class="block w-full rounded-lg border border-gray-300 py-2.5 pl-10 pr-4 text-sm text-gray-900 placeholder-gray-400 focus:border-brand focus:ring-2 focus:ring-brand/20 transition-colors @error('email') border-red-400 bg-red-50 @enderror">
                        </div>
                        @error('email')
                            <p class="text-xs text-red-600 flex items-center gap-1">
                                <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Username --}}
                    <div class="space-y-1.5">
                        <label for="username" class="block text-sm font-medium text-gray-700">
                            Username <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                                <i data-lucide="at-sign" class="h-4 w-4 text-gray-400"></i>
                            </span>
                            <input type="text"
                                   name="username"
                                   id="username"
                                   value="{{ old('username', $user->username) }}"
                                   required
                                   placeholder="johndoe"
                                   class="block w-full rounded-lg border border-gray-300 py-2.5 pl-10 pr-4 text-sm text-gray-900 placeholder-gray-400 focus:border-brand focus:ring-2 focus:ring-brand/20 transition-colors @error('username') border-red-400 bg-red-50 @enderror">
                        </div>
                        @error('username')
                            <p class="text-xs text-red-600 flex items-center gap-1">
                                <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                </div>

                {{-- Actions --}}
                <div class="flex justify-end items-center pt-5 mt-5 border-t border-gray-100">
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2 bg-brand text-white rounded-lg text-sm font-medium hover:bg-orange-600 transition-colors shadow-sm">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        Simpan Perubahan
                    </button>
                </div>

            </form>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- CARD 2: Change Password --}}
    {{-- ============================================================ --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

        {{-- Gradient Header --}}
        <div class="px-6 py-4 bg-gradient-to-r from-gray-700 to-gray-900">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-white/20">
                    <i data-lucide="lock" class="h-5 w-5 text-white"></i>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-white">Ubah Password</h3>
                    <p class="text-xs text-white/80">Perbarui password untuk keamanan akun Anda</p>
                </div>
            </div>
        </div>

        <div class="p-6">
            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 gap-5">

                    {{-- Current Password --}}
                    <div class="space-y-1.5">
                        <label for="current_password" class="block text-sm font-medium text-gray-700">
                            Password Saat Ini <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                                <i data-lucide="lock" class="h-4 w-4 text-gray-400"></i>
                            </span>
                            <input type="password"
                                   name="current_password"
                                   id="current_password"
                                   required
                                   placeholder="Masukkan password saat ini"
                                   class="block w-full rounded-lg border border-gray-300 py-2.5 pl-10 pr-4 text-sm text-gray-900 placeholder-gray-400 focus:border-gray-600 focus:ring-2 focus:ring-gray-200 transition-colors @error('current_password') border-red-400 bg-red-50 @enderror">
                        </div>
                        @error('current_password')
                            <p class="text-xs text-red-600 flex items-center gap-1">
                                <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- New Password & Confirmation --}}
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div class="space-y-1.5">
                            <label for="new_password" class="block text-sm font-medium text-gray-700">
                                Password Baru <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                                    <i data-lucide="key" class="h-4 w-4 text-gray-400"></i>
                                </span>
                                <input type="password"
                                       name="new_password"
                                       id="new_password"
                                       required
                                       placeholder="Minimal 6 karakter"
                                       class="block w-full rounded-lg border border-gray-300 py-2.5 pl-10 pr-4 text-sm text-gray-900 placeholder-gray-400 focus:border-gray-600 focus:ring-2 focus:ring-gray-200 transition-colors @error('new_password') border-red-400 bg-red-50 @enderror">
                            </div>
                            @error('new_password')
                                <p class="text-xs text-red-600 flex items-center gap-1">
                                    <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700">
                                Konfirmasi Password Baru <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                                    <i data-lucide="key-round" class="h-4 w-4 text-gray-400"></i>
                                </span>
                                <input type="password"
                                       name="new_password_confirmation"
                                       id="new_password_confirmation"
                                       required
                                       placeholder="Ulangi password baru"
                                       class="block w-full rounded-lg border border-gray-300 py-2.5 pl-10 pr-4 text-sm text-gray-900 placeholder-gray-400 focus:border-gray-600 focus:ring-2 focus:ring-gray-200 transition-colors">
                            </div>
                        </div>
                    </div>

                    {{-- Warning Box --}}
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <h6 class="font-semibold text-yellow-900 mb-2 flex items-center gap-2 text-sm">
                            <i data-lucide="alert-triangle" class="w-4 h-4 flex-shrink-0"></i>
                            Persyaratan Password
                        </h6>
                        <ul class="list-disc list-inside text-xs text-yellow-800 space-y-1">
                            <li>Minimal 6 karakter</li>
                            <li>Tidak boleh sama dengan password saat ini</li>
                            <li>Gunakan kombinasi huruf, angka, dan simbol untuk keamanan lebih baik</li>
                        </ul>
                    </div>

                </div>

                {{-- Actions --}}
                <div class="flex justify-end items-center pt-5 mt-5 border-t border-gray-100">
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2 bg-gray-800 text-white rounded-lg text-sm font-medium hover:bg-gray-900 transition-colors shadow-sm">
                        <i data-lucide="shield-check" class="w-4 h-4"></i>
                        Ubah Password
                    </button>
                </div>

            </form>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- CARD 3: Account Info --}}
    {{-- ============================================================ --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

        <div class="px-6 py-4 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-100">
                    <i data-lucide="info" class="h-5 w-5 text-gray-500"></i>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-gray-900">Informasi Akun</h3>
                    <p class="text-xs text-gray-500">Detail akun dan status pengguna</p>
                </div>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                    <p class="text-xs text-gray-500 mb-1">Role</p>
                    <p class="text-sm font-semibold text-gray-900 capitalize">{{ $user->role }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                    <p class="text-xs text-gray-500 mb-1">Status</p>
                    @if ($user->is_active)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                            Aktif
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span>
                            Nonaktif
                        </span>
                    @endif
                </div>
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                    <p class="text-xs text-gray-500 mb-1">Terdaftar Sejak</p>
                    <p class="text-sm font-semibold text-gray-900">{{ $user->created_at->format('d M Y, H:i') }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                    <p class="text-xs text-gray-500 mb-1">Terakhir Diperbarui</p>
                    <p class="text-sm font-semibold text-gray-900">{{ $user->updated_at->format('d M Y, H:i') }}</p>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        lucide.createIcons();
    });
</script>
@endpush