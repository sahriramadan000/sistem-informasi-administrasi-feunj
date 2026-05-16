@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    {{-- Header --}}
    <div>
        <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
            <i data-lucide="user-circle" class="h-8 w-8 text-brand"></i>
            Edit Profile
        </h1>
        <p class="mt-2 text-sm text-gray-600">Kelola informasi profile dan keamanan akun Anda</p>
    </div>

    {{-- Profile Information Card --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        {{-- Card Header --}}
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-brand to-brand-600">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-white bg-opacity-20">
                    <i data-lucide="user" class="h-5 w-5 text-white"></i>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-white">Informasi Profile</h2>
                    <p class="text-sm text-white text-opacity-80">Perbarui nama, email, dan username Anda</p>
                </div>
            </div>
        </div>

        {{-- Card Body --}}
        <div class="p-6">
            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('PATCH')

                <div class="space-y-6">
                    {{-- Avatar Section --}}
                    <div class="flex items-center gap-4 pb-6 border-b border-gray-200">
                        <div class="flex h-20 w-20 items-center justify-center rounded-full bg-brand text-white text-2xl font-bold">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $user->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $user->email }}</p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-brand-lighter text-brand-dark mt-1">
                                {{ ucfirst($user->role) }}
                            </span>
                        </div>
                    </div>

                    {{-- Name Field --}}
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i data-lucide="user" class="h-5 w-5 text-gray-400"></i>
                                </div>
                                <input type="text" 
                                       name="name" 
                                       id="name" 
                                       value="{{ old('name', $user->name) }}"
                                       required
                                       class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand focus:border-transparent transition-colors @error('name') border-red-500 @enderror">
                            </div>
                            @error('name')
                                <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                    <i data-lucide="alert-circle" class="h-4 w-4"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Email Field --}}
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i data-lucide="mail" class="h-5 w-5 text-gray-400"></i>
                                </div>
                                <input type="email" 
                                       name="email" 
                                       id="email" 
                                       value="{{ old('email', $user->email) }}"
                                       required
                                       class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand focus:border-transparent transition-colors @error('email') border-red-500 @enderror">
                            </div>
                            @error('email')
                                <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                    <i data-lucide="alert-circle" class="h-4 w-4"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Username Field --}}
                        <div>
                            <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                                Username <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i data-lucide="at-sign" class="h-5 w-5 text-gray-400"></i>
                                </div>
                                <input type="text" 
                                       name="username" 
                                       id="username" 
                                       value="{{ old('username', $user->username) }}"
                                       required
                                       class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand focus:border-transparent transition-colors @error('username') border-red-500 @enderror">
                            </div>
                            @error('username')
                                <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                    <i data-lucide="alert-circle" class="h-4 w-4"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <div class="flex justify-end pt-4 border-t border-gray-200">
                        <button type="submit" 
                                class="inline-flex items-center px-6 py-3 bg-brand text-white font-medium rounded-lg hover:bg-brand-600 focus:outline-none focus:ring-2 focus:ring-brand focus:ring-offset-2 transition-colors">
                            <i data-lucide="save" class="mr-2 h-5 w-5"></i>
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Password Change Card --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        {{-- Card Header --}}
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-700 to-gray-900">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-white bg-opacity-20">
                    <i data-lucide="lock" class="h-5 w-5 text-white"></i>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-white">Ubah Password</h2>
                    <p class="text-sm text-white text-opacity-80">Perbarui password untuk keamanan akun Anda</p>
                </div>
            </div>
        </div>

        {{-- Card Body --}}
        <div class="p-6">
            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    {{-- Current Password --}}
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
                            Password Saat Ini <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i data-lucide="lock" class="h-5 w-5 text-gray-400"></i>
                            </div>
                            <input type="password" 
                                   name="current_password" 
                                   id="current_password" 
                                   required
                                   class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand focus:border-transparent transition-colors @error('current_password') border-red-500 @enderror">
                        </div>
                        @error('current_password')
                            <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                <i data-lucide="alert-circle" class="h-4 w-4"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- New Password --}}
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">
                                Password Baru <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i data-lucide="key" class="h-5 w-5 text-gray-400"></i>
                                </div>
                                <input type="password" 
                                       name="new_password" 
                                       id="new_password" 
                                       required
                                       class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand focus:border-transparent transition-colors @error('new_password') border-red-500 @enderror">
                            </div>
                            @error('new_password')
                                <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                    <i data-lucide="alert-circle" class="h-4 w-4"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                Konfirmasi Password Baru <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i data-lucide="key-round" class="h-5 w-5 text-gray-400"></i>
                                </div>
                                <input type="password" 
                                       name="new_password_confirmation" 
                                       id="new_password_confirmation" 
                                       required
                                       class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand focus:border-transparent transition-colors">
                            </div>
                        </div>
                    </div>

                    {{-- Password Requirements --}}
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-start gap-3">
                            <i data-lucide="info" class="h-5 w-5 text-blue-600 flex-shrink-0 mt-0.5"></i>
                            <div class="text-sm text-blue-800">
                                <p class="font-medium mb-2">Persyaratan Password:</p>
                                <ul class="space-y-1 text-xs">
                                    <li class="flex items-center gap-2">
                                        <i data-lucide="check" class="h-3 w-3"></i>
                                        Minimal 6 karakter
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <i data-lucide="check" class="h-3 w-3"></i>
                                        Tidak boleh sama dengan password saat ini
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <i data-lucide="check" class="h-3 w-3"></i>
                                        Gunakan kombinasi huruf, angka, dan simbol untuk keamanan lebih baik
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <div class="flex justify-end pt-4 border-t border-gray-200">
                        <button type="submit" 
                                class="inline-flex items-center px-6 py-3 bg-gray-800 text-white font-medium rounded-lg hover:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-800 focus:ring-offset-2 transition-colors">
                            <i data-lucide="shield-check" class="mr-2 h-5 w-5"></i>
                            Ubah Password
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Account Info Card --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-100">
                    <i data-lucide="info" class="h-5 w-5 text-gray-600"></i>
                </div>
                <h2 class="text-lg font-semibold text-gray-900">Informasi Akun</h2>
            </div>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-500">Role</p>
                    <p class="text-base font-semibold text-gray-900 capitalize">{{ $user->role }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-500">Status</p>
                    <p class="text-base font-semibold text-gray-900">
                        @if ($user->is_active)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Nonaktif
                            </span>
                        @endif
                    </p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-500">Terdaftar Sejak</p>
                    <p class="text-base font-semibold text-gray-900">{{ $user->created_at->format('d M Y, H:i') }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-500">Terakhir Diperbarui</p>
                    <p class="text-base font-semibold text-gray-900">{{ $user->updated_at->format('d M Y, H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
    });
</script>
@endpush
