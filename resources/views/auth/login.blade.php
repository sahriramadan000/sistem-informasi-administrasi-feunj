<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Sistem Informasi Administrasi FEB UNJ</title>

    {{-- Fonts: Plus Jakarta Sans & Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- ICON --}}
    <link rel="icon" type="image/png" href="{{ asset('logo-unj.png') }}">
    
    {{-- Lucide Icons --}}
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #FAF7F2;
            background-image: 
                radial-gradient(circle at 50% 48%, rgba(255, 255, 255, 0.96) 0%, rgba(250, 247, 242, 0.7) 50%),
                radial-gradient(circle at 12% 18%, rgba(254, 215, 170, 0.22) 0%, transparent 35%),
                radial-gradient(circle at 88% 82%, rgba(254, 215, 170, 0.22) 0%, transparent 35%),
                radial-gradient(rgba(210, 198, 180, 0.4) 1.3px, transparent 1.3px);
            background-size: auto, auto, auto, 28px 28px;
            background-attachment: fixed;
        }

        .auth-card-shadow {
            box-shadow: 0 20px 60px -15px rgba(60, 45, 25, 0.08), 
                        0 0 0 1px rgba(230, 222, 210, 0.6);
        }

        .input-subtle {
            background-color: #FCFBF9;
            border: 1.5px solid #EAE5DC;
            transition: all 0.2s ease-in-out;
        }

        .input-subtle:hover {
            border-color: #D6CEC1;
            background-color: #FFFFFF;
        }

        .input-subtle:focus {
            border-color: #FF5500;
            background-color: #FFFFFF;
            box-shadow: 0 0 0 4px rgba(255, 85, 0, 0.12);
            outline: none;
        }

        .input-subtle-error {
            border-color: #EF4444 !important;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15) !important;
        }

        .btn-warm-brand {
            background: linear-gradient(135deg, #FF5500 0%, #FF6E1A 100%);
            box-shadow: 0 10px 25px -5px rgba(255, 85, 0, 0.35);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-warm-brand:hover {
            background: linear-gradient(135deg, #E64D00 0%, #F55F0A 100%);
            box-shadow: 0 14px 28px -5px rgba(255, 85, 0, 0.45);
            transform: translateY(-1px);
        }

        .btn-warm-brand:active {
            transform: translateY(0);
            box-shadow: 0 6px 15px -3px rgba(255, 85, 0, 0.3);
        }

        /* Subtle floating animation for doodles */
        @keyframes subtleFloat {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-6px); }
        }

        .float-doodle {
            animation: subtleFloat 6s ease-in-out infinite;
        }

        .float-doodle-delayed {
            animation: subtleFloat 7s ease-in-out 1.5s infinite;
        }

        /* Clean Input Error State */
        .input-error {
            border-color: #EF4444 !important;
            background-color: #FEF2F2 !important;
        }

        .input-error:focus {
            border-color: #EF4444 !important;
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.12) !important;
        }

        /* Floating Toast / Popup Notification */
        .toast-popup {
            transform: translate(-50%, -24px);
            opacity: 0;
            pointer-events: none;
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .toast-popup.show {
            transform: translate(-50%, 0);
            opacity: 1;
            pointer-events: auto;
        }
    </style>
</head>

<body class="min-h-screen flex flex-col justify-between text-gray-800 antialiased selection:bg-[#FF5500] selection:text-white relative overflow-x-hidden">

    {{-- Floating Toast / Popup Notification (Auto Dismiss) --}}
    <div id="toastPopup" class="toast-popup fixed top-6 left-1/2 z-50 flex items-center gap-3 px-4.5 py-3 rounded-2xl bg-white/95 backdrop-blur-md border border-[#EAE4D9] shadow-xl text-xs sm:text-sm font-medium">
        <div id="toastIconWrap" class="w-7 h-7 rounded-xl flex items-center justify-center flex-shrink-0 bg-red-100 text-red-600">
            <i id="toastIcon" data-lucide="alert-circle" class="w-4 h-4"></i>
        </div>
        <span id="toastMessage" class="text-gray-800"></span>
        <button type="button" onclick="hideToast()" class="text-gray-400 hover:text-gray-600 ml-1 p-0.5 rounded-lg transition-colors cursor-pointer">
            <i data-lucide="x" class="w-3.5 h-3.5"></i>
        </button>
    </div>

    {{-- Top Navigation Bar --}}
    <header class="w-full px-6 sm:px-12 py-5 sm:py-6 flex items-center justify-between z-20">
        {{-- Brand Info (Left) --}}
        <div class="flex items-center gap-3.5">
            <a href="/" class="flex items-center gap-3 group">
                <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-white p-1.5 shadow-sm border border-[#EBE4D8] flex items-center justify-center transition-transform group-hover:scale-105">
                    <img src="{{ asset('logo-unj.png') }}" alt="Logo UNJ" class="w-full h-full object-contain">
                </div>
                <div>
                    <span class="block text-base sm:text-lg font-bold text-gray-900 tracking-tight leading-tight group-hover:text-[#FF5500] transition-colors">
                        FEB UNJ
                    </span>
                    <span class="block text-xs font-medium text-gray-500 leading-tight">
                        Sistem Informasi Administrasi
                    </span>
                </div>
            </a>
        </div>

        {{-- Contact / Help Desk (Right) --}}
        <div class="flex items-center gap-3">
            <a href="mailto:feb@unj.ac.id" class="hidden sm:inline-flex items-center gap-1.5 text-xs font-medium text-gray-500 hover:text-gray-900 transition-colors py-2 px-3 rounded-full hover:bg-white/60">
                <i data-lucide="mail" class="w-3.5 h-3.5 text-[#FF5500]"></i>
                <span>feb@unj.ac.id</span>
            </a>
            <div class="hidden sm:block w-px h-4 bg-[#E0D8CB]"></div>
            <a href="https://feb.unj.ac.id" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-700 bg-white/80 hover:bg-white border border-[#E6E0D4] hover:border-[#D6CEC0] px-4 py-2 rounded-full shadow-sm transition-all hover:shadow">
                <span>Portal FEB</span>
                <i data-lucide="external-link" class="w-3 h-3 text-gray-400"></i>
            </a>
        </div>
    </header>

    {{-- Main Area with Illustrations and Floating Card --}}
    <main class="flex-1 flex items-center justify-center relative px-4 sm:px-8 py-4 sm:py-6 overflow-hidden">
        {{-- Wide Canvas Framing Container --}}
        <div class="w-full max-w-[1560px] mx-auto relative flex items-center justify-center min-h-[580px]">

            {{-- Floating Academic & Doodle Accent Elements in Negative Space --}}
            {{-- Paper Airplane with Dotted Flight Loop (Moved to Open Left Sky, completely clear of card) --}}
            <div class="hidden xl:block absolute left-[3%] xl:left-[7%] 2xl:left-[11%] top-[1%] xl:top-[2%] pointer-events-none select-none z-0 float-doodle" style="animation-duration: 10s;">
                <svg width="220" height="80" viewBox="0 0 220 80" fill="none" class="opacity-85">
                    <path d="M 10 58 C 50 58 75 16 105 16 C 130 16 138 48 115 54 C 96 56 90 32 125 22 L 175 14" stroke="#1C1917" stroke-width="2" stroke-dasharray="3 4" stroke-linecap="round"/>
                    <g transform="translate(175, 4) rotate(-14)">
                        <polygon points="0,17 36,0 14,21" fill="#FFFDF9" stroke="#1C1917" stroke-width="2.2" stroke-linejoin="round"/>
                        <polygon points="14,21 36,0 26,26" fill="#FED7AA" stroke="#1C1917" stroke-width="2.2" stroke-linejoin="round"/>
                        <polygon points="14,21 26,26 18,24" fill="#FF5500" stroke="#1C1917" stroke-width="1.8" stroke-linejoin="round"/>
                    </g>
                </svg>
            </div>

            {{-- Top-Left: Diploma / Certificate Scroll Doodle & Sparkle --}}
            <div class="hidden xl:block absolute left-[2%] xl:left-[4%] 2xl:left-[6%] top-[18%] pointer-events-none select-none z-0 float-doodle" style="animation-duration: 8s;">
                <svg width="68" height="68" viewBox="0 0 68 68" fill="none" class="opacity-85">
                    <path d="M12 28 C12 20 20 16 32 16 L50 16 C56 16 58 20 54 26 L46 40 C42 46 34 48 24 48 L14 48 C8 48 8 42 12 36 Z" fill="#FFFDF9" stroke="#1C1917" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                    <line x1="20" y1="26" x2="42" y2="26" stroke="#D6D3D1" stroke-width="2" stroke-linecap="round"/>
                    <line x1="20" y1="33" x2="36" y2="33" stroke="#D6D3D1" stroke-width="2" stroke-linecap="round"/>
                    <circle cx="34" cy="34" r="5.5" fill="#FED7AA" stroke="#FF5500" stroke-width="2"/>
                    <path d="M34 39.5 L30 49 L34.5 45.5 L39 49 Z" fill="#FF5500"/>
                    <path d="M56 6 Q56 12 62 12 Q56 12 56 18 Q56 12 50 12 Q56 12 56 6" fill="#FF7A18"/>
                </svg>
            </div>

            {{-- Academic Graduation Cap Doodle (Left Zone) --}}
            <div class="hidden 2xl:block absolute left-[18%] xl:left-[20%] 2xl:left-[23%] top-[11%] pointer-events-none select-none z-0 float-doodle-delayed" style="animation-duration: 9s;">
                <svg width="58" height="48" viewBox="0 0 58 48" fill="none" class="opacity-80">
                    <polygon points="29,8 54,18 29,28 4,18" fill="#1C1917" stroke="#1C1917" stroke-width="2" stroke-linejoin="round"/>
                    <path d="M14 23 L14 33 C14 39 44 39 44 33 L44 23" fill="#FFFDF9" stroke="#1C1917" stroke-width="2" stroke-linejoin="round"/>
                    <circle cx="29" cy="18" r="2.5" fill="#FF5500"/>
                    <path d="M29 18 C38 20 44 26 46 35" stroke="#FF5500" stroke-width="2" fill="none" stroke-linecap="round"/>
                    <circle cx="46" cy="36" r="2.2" fill="#FF5500"/>
                </svg>
            </div>

            {{-- Left Mid: Squiggly Line with Orange Accent Dot --}}
            <div class="hidden 2xl:block absolute left-[14%] 2xl:left-[18%] top-[28%] pointer-events-none select-none z-0 opacity-75">
                <svg width="74" height="34" viewBox="0 0 74 34" fill="none">
                    <path d="M4 17 Q 18 5 32 17 T 60 17" stroke="#1C1917" stroke-width="2.2" stroke-linecap="round"/>
                    <circle cx="68" cy="17" r="3" fill="#FF5500"/>
                </svg>
            </div>

            {{-- Top-Right: Open Book / Notebook Doodle & Sparkle --}}
            <div class="hidden xl:block absolute right-[12%] 2xl:right-[15%] top-[6%] pointer-events-none select-none z-0 float-doodle-delayed" style="animation-duration: 9s;">
                <svg width="68" height="68" viewBox="0 0 68 68" fill="none" class="opacity-85">
                    <path d="M12 24 C20 20 30 22 34 26 C38 22 48 20 56 24 L56 46 C48 42 38 44 34 48 C30 44 20 42 12 46 Z" fill="#FFFDF9" stroke="#1C1917" stroke-width="2.2" stroke-linejoin="round"/>
                    <path d="M34 26 L34 48" stroke="#1C1917" stroke-width="2.2"/>
                    <line x1="18" y1="30" x2="28" y2="30" stroke="#E7E5E4" stroke-width="1.8" stroke-linecap="round"/>
                    <line x1="18" y1="36" x2="26" y2="36" stroke="#E7E5E4" stroke-width="1.8" stroke-linecap="round"/>
                    <line x1="40" y1="30" x2="50" y2="30" stroke="#E7E5E4" stroke-width="1.8" stroke-linecap="round"/>
                    <path d="M12 10 Q12 15 17 15 Q12 15 12 20 Q12 15 7 15 Q12 15 12 10" fill="#F59E0B"/>
                </svg>
            </div>

            {{-- Administration Checklist Sticky Note (Right-Center) --}}
            <div class="hidden 2xl:block absolute right-[26%] top-[10%] pointer-events-none select-none z-0 float-doodle" style="animation-duration: 8.5s;">
                <svg width="105" height="92" viewBox="0 0 105 92" fill="none" class="opacity-85">
                    <rect x="10" y="12" width="84" height="68" rx="8" fill="#FFFDF5" stroke="#1C1917" stroke-width="2" transform="rotate(4 52 46)"/>
                    <rect x="42" y="6" width="22" height="9" rx="2" fill="#FED7AA" stroke="#1C1917" stroke-width="1.5" transform="rotate(4 52 46)"/>
                    <path d="M 24 33 L 28 37 L 36 29" stroke="#FF5500" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                    <line x1="42" y1="33" x2="78" y2="33" stroke="#1C1917" stroke-width="2" stroke-linecap="round"/>
                    <line x1="26" y1="46" x2="78" y2="46" stroke="#A8A29E" stroke-width="1.8" stroke-linecap="round"/>
                    <line x1="26" y1="58" x2="65" y2="58" stroke="#A8A29E" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
            </div>

            {{-- Right Mid: Curled Doodle Swirl --}}
            <div class="hidden 2xl:block absolute right-[24%] top-[25%] pointer-events-none select-none z-0 opacity-75">
                <svg width="64" height="42" viewBox="0 0 64 42" fill="none">
                    <path d="M6 32 C 16 10 30 8 34 20 C 38 32 50 28 58 14" stroke="#1C1917" stroke-width="2.2" stroke-linecap="round"/>
                    <circle cx="58" cy="14" r="3" fill="#FB923C"/>
                </svg>
            </div>

            {{-- Studio Ground Horizon Baseline --}}
            <div class="hidden lg:block absolute left-4 right-4 bottom-0 z-0 pointer-events-none opacity-60">
                <svg width="100%" height="16" viewBox="0 0 1200 16" preserveAspectRatio="none" fill="none">
                    <line x1="0" y1="12" x2="1200" y2="12" stroke="#E2DDD0" stroke-width="2" stroke-linecap="round"/>
                    <line x1="140" y1="15" x2="190" y2="15" stroke="#C8C0B0" stroke-width="1.5" stroke-linecap="round"/>
                    <line x1="520" y1="15" x2="570" y2="15" stroke="#C8C0B0" stroke-width="1.5" stroke-linecap="round"/>
                    <line x1="940" y1="15" x2="1010" y2="15" stroke="#C8C0B0" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
            </div>

            {{-- Left Illustration (FEB Analytics & Administration Documents) --}}
            <div class="hidden lg:block absolute left-0 xl:left-4 2xl:left-0 bottom-0 w-[310px] xl:w-[380px] 2xl:w-[500px] pointer-events-none select-none z-0 float-doodle">
                <img src="{{ asset('images/auth-analytics.png') }}" alt="Ilustrasi Administrasi & Ekonomi FEB UNJ" class="w-full h-auto object-contain drop-shadow-sm">
            </div>

            {{-- Right Illustration (Student Working with Laptop & Geometric Pedestal) --}}
            <div class="hidden lg:block absolute right-0 xl:right-4 2xl:right-0 bottom-0 w-[310px] xl:w-[380px] 2xl:w-[500px] pointer-events-none select-none z-0 float-doodle-delayed">
                <img src="{{ asset('images/auth-character.png') }}" alt="Ilustrasi Mahasiswa FEB UNJ" class="w-full h-auto object-contain drop-shadow-sm">
            </div>

            {{-- Center Login Card --}}
            <div id="loginCard" class="w-full max-w-[440px] bg-white/95 backdrop-blur-md rounded-[32px] sm:rounded-[36px] p-8 sm:p-10 auth-card-shadow relative z-10 transition-all my-2 border border-[#EFEAE1]">

                {{-- Card Header --}}
                <div class="text-center mb-7 sm:mb-8">
                    <h1 class="text-2xl sm:text-[28px] font-extrabold text-gray-900 tracking-tight leading-tight">
                        Masuk ke Akun
                    </h1>
                    <p class="text-xs sm:text-sm text-gray-500 mt-2 leading-relaxed max-w-xs mx-auto">
                        Silakan masukkan akun Anda untuk mengakses portal administrasi FEB UNJ.
                    </p>
                </div>

                {{-- Login Form --}}
                <form id="loginForm" method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

                    {{-- Email / Username Input --}}
                    <div>
                        <label for="login" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2 ml-1">
                            Email atau Username
                        </label>
                        <div class="relative">
                            <input type="text" id="login" name="login" value="{{ old('login') }}"
                                class="input-subtle block w-full h-[50px] pl-11 pr-4 text-sm rounded-2xl text-gray-900 placeholder:text-gray-400 font-medium @error('login') input-error @enderror"
                                placeholder="Masukkan email atau username" required autofocus autocomplete="username">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                <i data-lucide="user" class="w-4 h-4"></i>
                            </div>
                        </div>
                    </div>

                    {{-- Password Input --}}
                    <div>
                        <div class="flex items-center justify-between mb-2 ml-1">
                            <label for="password" class="block text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Password
                            </label>
                        </div>
                        <div class="relative">
                            <input type="password" id="password" name="password"
                                class="input-subtle block w-full h-[50px] pl-11 pr-20 text-sm rounded-2xl text-gray-900 placeholder:text-gray-400 font-medium @error('password') input-error @enderror"
                                placeholder="Masukkan password" required autocomplete="current-password">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                <i data-lucide="lock" class="w-4 h-4"></i>
                            </div>
                            <button type="button" class="absolute inset-y-0 right-0 pr-3.5 flex items-center gap-1 text-xs font-semibold text-gray-400 hover:text-gray-700 focus:outline-none transition-colors cursor-pointer" onclick="togglePassword()">
                                <i data-lucide="eye" class="w-4 h-4" id="toggleIcon"></i>
                                <span id="toggleText" class="text-[11px] select-none text-gray-400 hover:text-gray-600">Lihat</span>
                            </button>
                        </div>
                    </div>

                    {{-- Remember Me & Forgot Password --}}
                    <div class="flex items-center justify-between pt-1.5 pb-1">
                        <label class="flex items-center gap-2.5 cursor-pointer select-none group">
                            <input type="checkbox" id="remember" name="remember" 
                                class="w-4 h-4 rounded-md border-[#D9D3C7] text-[#FF5500] focus:ring-[#FF5500]/30 transition-all cursor-pointer">
                            <span class="text-xs font-medium text-gray-600 group-hover:text-gray-900 transition-colors">Ingat saya</span>
                        </label>
                        <a href="mailto:feb@unj.ac.id?subject=Bantuan%20Reset%20Password%20SIA%20FEB%20UNJ" class="text-xs font-semibold text-[#FF5500] hover:text-[#E64D00] hover:underline transition-colors">
                            Lupa password?
                        </a>
                    </div>

                    {{-- Submit Button --}}
                    <div class="pt-2.5">
                        <button type="submit" id="submitBtn"
                            class="btn-warm-brand w-full h-[50px] rounded-2xl text-white font-bold text-sm tracking-wide flex items-center justify-center gap-2 cursor-pointer shadow-lg shadow-orange-500/25 active:scale-[0.99] transition-all disabled:opacity-75 disabled:cursor-wait">
                            <span id="btnText">Masuk Sekarang</span>
                            <i data-lucide="arrow-right" id="btnIcon" class="w-4 h-4 stroke-[2.5]"></i>
                        </button>
                    </div>
                </form>

                {{-- Divider & Help Notice --}}
                <div class="mt-6 pt-5 border-t border-[#F2ECE1] text-center">
                    <p class="text-xs text-gray-500">
                        Kendala saat login? Hubungi
                        <a href="mailto:feb@unj.ac.id" class="text-[#FF5500] font-semibold hover:underline transition-colors">
                            Admin SIA FEB UNJ
                        </a>
                    </p>
                </div>
            </div>
    </div>
    </main>

    {{-- Bottom Footer --}}
    <footer class="w-full py-5 px-6 text-center text-xs text-gray-500 z-10">
        <p>
            Copyright &copy; {{ date('Y') }} Fakultas Ekonomi dan Bisnis Universitas Negeri Jakarta
        </p>
        <p class="text-[11px] text-gray-400 mt-1">
            Sistem Informasi Administrasi Terpadu &bull; All rights reserved
        </p>
    </footer>

    <script>
        // Initialize Lucide icons
        lucide.createIcons();

        // Toggle password visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            const toggleText = document.getElementById('toggleText');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.setAttribute('data-lucide', 'eye-off');
                if (toggleText) toggleText.textContent = 'Tutup';
            } else {
                passwordInput.type = 'password';
                toggleIcon.setAttribute('data-lucide', 'eye');
                if (toggleText) toggleText.textContent = 'Lihat';
            }

            lucide.createIcons();
        }

        // Floating Toast Popup Manager
        let toastTimer = null;

        function showToast(message, type = 'error') {
            const toast = document.getElementById('toastPopup');
            const toastMsg = document.getElementById('toastMessage');
            const toastIconWrap = document.getElementById('toastIconWrap');
            const toastIcon = document.getElementById('toastIcon');

            if (!toast || !toastMsg) return;

            toastMsg.textContent = message;

            if (type === 'error') {
                toast.className = 'toast-popup show fixed top-6 left-1/2 z-50 flex items-center gap-3 px-4.5 py-3 rounded-2xl bg-white/95 backdrop-blur-md border border-red-200/90 text-gray-800 shadow-xl shadow-red-500/10 text-xs sm:text-sm font-medium';
                toastIconWrap.className = 'w-7 h-7 rounded-xl bg-red-100 text-red-600 flex items-center justify-center flex-shrink-0';
                toastIcon.setAttribute('data-lucide', 'alert-circle');
            } else {
                toast.className = 'toast-popup show fixed top-6 left-1/2 z-50 flex items-center gap-3 px-4.5 py-3 rounded-2xl bg-white/95 backdrop-blur-md border border-emerald-200/90 text-gray-800 shadow-xl shadow-emerald-500/10 text-xs sm:text-sm font-medium';
                toastIconWrap.className = 'w-7 h-7 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0';
                toastIcon.setAttribute('data-lucide', 'check');
            }

            lucide.createIcons();

            clearTimeout(toastTimer);
            toastTimer = setTimeout(() => {
                hideToast();
            }, 3500);
        }

        function hideToast() {
            const toast = document.getElementById('toastPopup');
            if (toast) {
                toast.classList.remove('show');
            }
        }

        // Handle Interactive Form Submit
        const loginForm = document.getElementById('loginForm');
        const submitBtn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');
        const btnIcon = document.getElementById('btnIcon');
        const loginInput = document.getElementById('login');
        const passwordInput = document.getElementById('password');

        // Clear red input state as soon as user types
        loginInput.addEventListener('input', () => {
            loginInput.classList.remove('input-error');
        });
        passwordInput.addEventListener('input', () => {
            passwordInput.classList.remove('input-error');
        });

        loginForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            const loginVal = loginInput.value.trim();
            const passwordVal = passwordInput.value;

            // Simple empty check
            if (!loginVal) {
                loginInput.classList.add('input-error');
                loginInput.focus();
                showToast('Silakan masukkan email atau username Anda.', 'error');
                return;
            }

            if (!passwordVal) {
                passwordInput.classList.add('input-error');
                passwordInput.focus();
                showToast('Silakan masukkan password Anda.', 'error');
                return;
            }

            // Remove red border while submitting
            loginInput.classList.remove('input-error');
            passwordInput.classList.remove('input-error');

            // Button subtle processing state
            submitBtn.disabled = true;
            if (btnText) btnText.textContent = 'Memverifikasi...';

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') 
                                || document.querySelector('input[name="_token"]')?.value;

                const response = await fetch(loginForm.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        login: loginVal,
                        password: passwordVal,
                        remember: document.getElementById('remember')?.checked ? 1 : 0
                    })
                });

                const data = await response.json().catch(() => null);

                if (response.ok && data && data.success) {
                    // Success Popup
                    showToast('Login berhasil! Mengalihkan ke portal...', 'success');
                    if (btnText) btnText.textContent = 'Mengalihkan...';

                    // Smooth redirect without changing card contents
                    setTimeout(() => {
                        window.location.href = data.redirect || '{{ route('switcher') }}';
                    }, 750);

                } else {
                    // Reset Button
                    submitBtn.disabled = false;
                    if (btnText) btnText.textContent = 'Masuk Sekarang';

                    // Highlight fields red
                    loginInput.classList.add('input-error');
                    passwordInput.classList.add('input-error');
                    passwordInput.value = '';
                    passwordInput.focus();

                    // Show Popup that auto-dismisses
                    const errorMsg = data?.message || 'Email/Username atau password salah.';
                    showToast(errorMsg, 'error');
                }
            } catch (err) {
                console.error('Login error:', err);
                loginForm.submit();
            }
        });

        // Trigger toast for server-rendered session messages if any
        @if (session('error'))
            showToast("{{ session('error') }}", 'error');
        @elseif (session('success'))
            showToast("{{ session('success') }}", 'success');
        @elseif ($errors->any())
            showToast("{{ $errors->first() }}", 'error');
        @endif
    </script>
</body>

</html>

