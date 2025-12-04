<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Sistem Informasi Administrasi FEB-UNJ</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Lucide Icons --}}
    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body class="bg-gray-50 antialiased">
    <div class="min-h-screen flex items-center justify-center p-4 sm:p-6 md:p-8">
        <div class="w-full max-w-md">
            {{-- Logo & Header --}}
            <div class="text-center mb-6 sm:mb-8">
                {{-- Logo UNJ --}}
                <div class="inline-flex items-center justify-center w-20 h-20 sm:w-28 sm:h-28 mb-4 sm:mb-4">
                    <img src="{{ asset('logo-unj.png') }}" alt="" width="100%">
                </div>

                {{-- Title --}}
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-3 uppercase tracking-tight">Selamat Datang
                </h1>
                <p class="text-sm sm:text-base text-gray-600 leading-relaxed">Sistem Informasi Administrasi</p>
                <p class="text-base sm:text-lg font-semibold text-brand mt-1">Fakultas Ekonomi dan Bisnis UNJ</p>
            </div>

            {{-- Login Card --}}
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 card-hover">
                <div class="p-6 sm:p-8">
                    {{-- Alert Error --}}
                    @if (session('error'))
                        <div class="mb-5 sm:mb-6 p-3 sm:p-4 bg-red-50 border border-red-200 rounded-lg alert-slide-in">
                            <div class="flex items-start sm:items-center gap-2 sm:gap-3">
                                <i data-lucide="alert-circle"
                                    class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5 sm:mt-0"></i>
                                <p class="text-xs sm:text-sm text-red-800">{{ session('error') }}</p>
                            </div>
                        </div>
                    @endif

                    {{-- Alert Success --}}
                    @if (session('success'))
                        <div
                            class="mb-5 sm:mb-6 p-3 sm:p-4 bg-green-50 border border-green-200 rounded-lg alert-slide-in">
                            <div class="flex items-start sm:items-center gap-2 sm:gap-3">
                                <i data-lucide="check-circle"
                                    class="w-5 h-5 text-success flex-shrink-0 mt-0.5 sm:mt-0"></i>
                                <p class="text-xs sm:text-sm text-green-800">{{ session('success') }}</p>
                            </div>
                        </div>
                    @endif

                    {{-- Login Form --}}
                    <form method="POST" action="{{ route('login') }}" class="space-y-5">
                        @csrf

                        {{-- Email/Username Input --}}
                        <div class="space-y-2">
                            <label for="login" class="block text-sm font-medium text-gray-900">
                                Email atau Username
                            </label>
                            <div class="relative">
                                <input type="text" id="login" name="login" value="{{ old('login') }}"
                                    class="login-input @error('login') login-input-error @enderror"
                                    placeholder="Masukkan email atau username" required autofocus>
                                <div class="login-input-icon">
                                    <i data-lucide="user" class="w-5 h-5"></i>
                                </div>
                            </div>
                            @error('login')
                                <p class="text-sm text-red-600 flex items-center gap-1.5 mt-1">
                                    <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>

                        {{-- Password Input --}}
                        <div class="space-y-2">
                            <label for="password" class="block text-sm font-medium text-gray-900">
                                Password
                            </label>
                            <div class="relative">
                                <input type="password" id="password" name="password"
                                    class="login-input pr-11 @error('password') login-input-error @enderror"
                                    placeholder="Masukkan password" required>
                                <div class="login-input-icon">
                                    <i data-lucide="lock" class="w-5 h-5"></i>
                                </div>
                                <div class="password-toggle" onclick="togglePassword()">
                                    <i data-lucide="eye" class="w-5 h-5" id="toggleIcon"></i>
                                </div>
                            </div>
                            @error('password')
                                <p class="text-sm text-red-600 flex items-center gap-1.5 mt-1">
                                    <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>

                        {{-- Remember Me --}}
                        <div class="flex items-center">
                            <input type="checkbox" id="remember" name="remember" class="checkbox-brand">
                            <label for="remember" class="ml-2 text-sm text-gray-700 cursor-pointer select-none">
                                Ingat saya
                            </label>
                        </div>

                        {{-- Submit Button --}}
                        <div class="pt-1">
                            <button type="submit"
                                class="btn-brand w-full space-y-0.5 flex items-center justify-center">
                                <i data-lucide="log-in" class="w-5 h-5 mt-1"></i>
                                <span>Masuk</span>
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Footer --}}
                <div class="px-6 sm:px-8 py-3 sm:py-3 bg-gray-50 border-t border-gray-200 rounded-b-xl">
                    <p class="text-xs text-center text-gray-600">
                        Lupa password? Hubungi administrator sistem
                    </p>
                </div>
            </div>

            {{-- Copyright --}}
            <div class="mt-4 sm:mt-6 text-center">
                <p class="text-xs sm:text-sm text-gray-600">
                    © {{ date('Y') }} Fakultas Ekonomi dan Bisnis UNJ
                </p>
                <p class="text-xs text-gray-500 mt-1">
                    All rights reserved
                </p>
            </div>
        </div>
    </div>

    <script>
        // Initialize Lucide icons
        lucide.createIcons();

        // Toggle password visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.setAttribute('data-lucide', 'eye-off');
            } else {
                passwordInput.type = 'password';
                toggleIcon.setAttribute('data-lucide', 'eye');
            }

            // Reinitialize icons to update the changed icon
            lucide.createIcons();
        }
    </script>
</body>

</html>
