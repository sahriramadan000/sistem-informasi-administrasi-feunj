<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-50">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Informasi Administrasi') - FEB UNJ</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    {{-- jQuery (Required for Select2) --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    {{-- Select2 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    
    {{-- Flatpickr CSS (Datepicker) --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    
    {{-- Lucide Icons (Modern alternative to Bootstrap Icons) --}}
    <script src="https://unpkg.com/lucide@latest"></script>
    
    {{-- Vite Assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Custom CSS --}}
    {{-- Custom CSS --}}
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        /* Select2 Custom Styling to match Tailwind */
        .select2-container--default .select2-selection--single {
            height: 42px !important;
            border: 2px solid #d1d5db !important;
            border-radius: 0.5rem !important;
            padding: 0.5rem 0.75rem !important;
            font-size: 0.875rem !important;
            transition: all 0.15s ease-in-out !important;
        }

        .select2-container--default .select2-selection--single:hover {
            border-color: #9ca3af !important;
        }

        .select2-container--default.select2-container--focus .select2-selection--single,
        .select2-container--default.select2-container--open .select2-selection--single {
            border-color: #FF4D00 !important;
            outline: none !important;
            box-shadow: 0 0 0 3px rgba(255, 77, 0, 0.1) !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 26px !important;
            padding-left: 0 !important;
            color: #374151 !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #9ca3af !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px !important;
            right: 8px !important;
        }

        .select2-dropdown {
            border: 2px solid #d1d5db !important;
            border-radius: 0.5rem !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
        }

        .select2-container--default .select2-search--dropdown .select2-search__field {
            border: 2px solid #d1d5db !important;
            border-radius: 0.375rem !important;
            padding: 0.5rem !important;
            font-size: 0.875rem !important;
        }

        .select2-container--default .select2-search--dropdown .select2-search__field:focus {
            border-color: #FF4D00 !important;
            outline: none !important;
        }

        .select2-container--default .select2-results__option {
            padding: 0.5rem 0.75rem !important;
            font-size: 0.875rem !important;
        }

        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #FF4D00 !important;
            color: white !important;
        }

        .select2-container--default .select2-results__option[aria-selected=true] {
            background-color: #FFF5F0 !important;
            color: #992D00 !important;
        }

        .select2-container {
            width: 100% !important;
        }

        /* Error state for Select2 */
        .select2-error .select2-selection {
            border-color: #ef4444 !important;
        }

        .select2-error .select2-selection:focus {
            border-color: #ef4444 !important;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1) !important;
        }

        /* Flatpickr Custom Styling - Simple & Clean */
        .flatpickr-input {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 24 24' fill='none' stroke='%239ca3af' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Crect x='3' y='4' width='18' height='18' rx='2' ry='2'%3E%3C/rect%3E%3Cline x1='16' y1='2' x2='16' y2='6'%3E%3C/line%3E%3Cline x1='8' y1='2' x2='8' y2='6'%3E%3C/line%3E%3Cline x1='3' y1='10' x2='21' y2='10'%3E%3C/line%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 1.25rem;
            padding-right: 2.5rem !important;
            cursor: pointer;
        }

        .flatpickr-calendar {
            border-radius: 0.5rem !important;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
            border: 1px solid #e5e7eb !important;
        }

        /* Header Container */
        .flatpickr-months {
            border-radius: 0.5rem 0.5rem 0 0 !important;
            background: #FF4D00 !important;
            padding: 0.75rem 1rem !important;
            position: relative !important;
            display: flex !important;
            align-items: center !important;
        }

        .flatpickr-month {
            height: auto !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        .flatpickr-current-month {
            position: static !important;
            width: auto !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 0.5rem !important;
            padding: 0 !important;
            height: auto !important;
            line-height: 1 !important;
        }

        /* Navigation Arrows - Positioned Absolute */
        .flatpickr-prev-month,
        .flatpickr-next-month {
            position: absolute !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            fill: white !important;
            padding: 0.5rem !important;
            border-radius: 0.375rem !important;
            width: 32px !important;
            height: 32px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            z-index: 3 !important;
        }

        .flatpickr-prev-month {
            left: 0.75rem !important;
        }

        .flatpickr-next-month {
            right: 0.75rem !important;
        }

        .flatpickr-prev-month:hover,
        .flatpickr-next-month:hover {
            background: rgba(255, 255, 255, 0.2) !important;
        }

        .flatpickr-prev-month svg,
        .flatpickr-next-month svg {
            width: 14px !important;
            height: 14px !important;
        }

        /* Month Dropdown */
        .flatpickr-current-month .flatpickr-monthDropdown-months {
            background: rgba(255, 255, 255, 0.2) !important;
            color: white !important;
            border: 1px solid rgba(255, 255, 255, 0.3) !important;
            border-radius: 0.375rem !important;
            padding: 0.5rem 0.75rem !important;
            font-size: 0.875rem !important;
            font-weight: 600 !important;
            cursor: pointer !important;
            appearance: none !important;
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
            min-width: 110px !important;
            height: 36px !important;
            margin: 0 !important;
            line-height: 1 !important;
        }

        .flatpickr-current-month .flatpickr-monthDropdown-months:hover {
            background: rgba(255, 255, 255, 0.3) !important;
        }

        .flatpickr-monthDropdown-months option {
            background: white !important;
            color: #374151 !important;
            padding: 0.5rem !important;
        }

        /* Year Input Wrapper */
        .flatpickr-current-month .numInputWrapper {
            width: 70px !important;
            height: 36px !important;
            display: inline-block !important;
            margin: 0 !important;
            position: relative !important;
        }

        /* Year Input */
        .flatpickr-current-month .numInputWrapper input {
            background: rgba(255, 255, 255, 0.2) !important;
            color: white !important;
            border: 1px solid rgba(255, 255, 255, 0.3) !important;
            border-radius: 0.375rem !important;
            padding: 0.5rem 1.25rem 0.5rem 0.5rem !important;
            font-size: 0.875rem !important;
            font-weight: 600 !important;
            width: 70px !important;
            height: 36px !important;
            text-align: center !important;
            line-height: 1 !important;
            margin: 0 !important;
        }

        .flatpickr-current-month .numInputWrapper input:hover {
            background: rgba(255, 255, 255, 0.3) !important;
        }

        .flatpickr-current-month .numInputWrapper input:focus {
            outline: none !important;
            background: rgba(255, 255, 255, 0.4) !important;
        }

        /* Year Arrow Buttons */
        .numInputWrapper span {
            position: absolute !important;
            right: 2px !important;
            width: 14px !important;
            height: 50% !important;
            border: none !important;
            background: transparent !important;
            padding: 0 4px 0 2px !important;
            opacity: 0.8 !important;
            cursor: pointer !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        .numInputWrapper span:hover {
            opacity: 1 !important;
        }

        .numInputWrapper span.arrowUp {
            top: 0 !important;
            border-bottom: 0 !important;
        }

        .numInputWrapper span.arrowDown {
            bottom: 0 !important;
            border-top: 0 !important;
        }

        .numInputWrapper span.arrowUp:after {
            border-bottom-color: white !important;
            border-bottom-width: 5px !important;
            border-left: 4px solid transparent !important;
            border-right: 4px solid transparent !important;
        }

        .numInputWrapper span.arrowDown:after {
            border-top-color: white !important;
            border-top-width: 5px !important;
            border-left: 4px solid transparent !important;
            border-right: 4px solid transparent !important;
        }

        /* Weekdays */
        .flatpickr-weekdays {
            margin-top: 0.5rem !important;
        }

        .flatpickr-weekday {
            color: #6b7280 !important;
            font-weight: 600 !important;
            font-size: 0.875rem !important;
        }

        /* Days */
        .flatpickr-day {
            border-radius: 0.375rem !important;
            color: #374151 !important;
        }

        .flatpickr-day:hover {
            background: #FFE8DB !important;
            border-color: #FFE8DB !important;
        }

        .flatpickr-day.today {
            border-color: #FF4D00 !important;
            background: #FFF5F0 !important;
            color: #992D00 !important;
        }

        .flatpickr-day.selected {
            background: #FF4D00 !important;
            border-color: #FF4D00 !important;
            color: white !important;
        }

        .flatpickr-day.selected:hover {
            background: #E64500 !important;
            border-color: #E64500 !important;
        }
    </style>

    @stack('styles')
</head>

<body class="h-full">
    @auth
        <div class="min-h-screen flex bg-gray-50">
            {{-- Sidebar --}}
            <aside id="sidebar" class="hidden lg:flex lg:flex-shrink-0 lg:w-64">
                <div class="flex flex-col w-64 border-r border-gray-200 bg-white fixed h-full">
                    {{-- Sidebar Header --}}
                    <div class="flex h-16 items-center justify-between px-6 border-b border-gray-200 flex-shrink-0">
                        <div class="flex items-center space-x-2">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand">
                                <i data-lucide="building" class="h-5 w-5 text-white"></i>
                            </div>
                            <span class="text-lg font-semibold text-gray-900">SIA FEB UNJ</span>
                        </div>
                    </div>

                    {{-- Sidebar Navigation --}}
                    <nav class="flex-1 space-y-1 px-3 py-4 overflow-y-auto">
                        {{-- Dashboard --}}
                        <a href="{{ route('dashboard') }}" 
                           class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-brand text-white' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                            <i data-lucide="layout-dashboard" class="mr-3 h-5 w-5"></i>
                            Dashboard
                        </a>

                        {{-- Letters Section --}}
                        <div class="pt-2">
                            <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Surat</h3>
                            <div class="mt-2 space-y-1">
                                @if (auth()->user()->isAdmin() || auth()->user()->isOperator())
                                    <a href="{{ route('letters.create') }}" 
                                       class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('letters.create') ? 'bg-brand text-white' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                                        <i data-lucide="plus-circle" class="mr-3 h-5 w-5"></i>
                                        Buat Surat Baru
                                    </a>
                                @endif
                                <a href="{{ route('letters.index') }}" 
                                   class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('letters.index') ? 'bg-brand text-white' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                                    <i data-lucide="mail" class="mr-3 h-5 w-5"></i>
                                    Daftar Surat
                                </a>
                            </div>
                        </div>

                        {{-- Master Data Section (Admin Only) --}}
                        @if (auth()->user()->isAdmin())
                            <div class="pt-4">
                                <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Master Data</h3>
                                <div class="mt-2 space-y-1">
                                    <a href="{{ route('master.classification-letters.index') }}" 
                                       class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('master.classification-letters.*') ? 'bg-brand text-white' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                                        <i data-lucide="tags" class="mr-3 h-5 w-5"></i>
                                        Klasifikasi Surat
                                    </a>
                                    <a href="{{ route('master.letter-types.index') }}" 
                                       class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('master.letter-types.*') ? 'bg-brand text-white' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                                        <i data-lucide="file-text" class="mr-3 h-5 w-5"></i>
                                        Jenis Surat
                                    </a>
                                    <a href="{{ route('master.letter-purposes.index') }}" 
                                       class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('master.letter-purposes.*') ? 'bg-brand text-white' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                                        <i data-lucide="bookmark" class="mr-3 h-5 w-5"></i>
                                        Keperluan Surat
                                    </a>
                                    <a href="{{ route('master.signatories.index') }}" 
                                       class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('master.signatories.*') ? 'bg-brand text-white' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                                        <i data-lucide="user-check" class="mr-3 h-5 w-5"></i>
                                        Penandatangan
                                    </a>
                                    <a href="{{ route('master.users.index') }}" 
                                       class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('master.users.*') ? 'bg-brand text-white' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                                        <i data-lucide="users" class="mr-3 h-5 w-5"></i>
                                        Pengguna
                                    </a>
                                </div>
                            </div>
                        @endif
                    </nav>

                    {{-- User Menu at Bottom --}}
                    <div class="border-t border-gray-200 p-3 flex-shrink-0">
                        <div class="relative">
                            <button onclick="toggleUserMenu()" class="w-full flex items-center rounded-lg px-3 py-2 text-sm transition-colors hover:bg-gray-100">
                                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-brand">
                                    <span class="text-xs font-semibold text-white">{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</span>
                                </div>
                                <div class="ml-3 flex-1 text-left">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-gray-500 capitalize">{{ Auth::user()->role }}</p>
                                </div>
                                <i data-lucide="chevron-up" class="h-4 w-4 text-gray-400 transition-transform duration-200" id="user-menu-icon"></i>
                            </button>
                            
                            {{-- Dropdown Menu --}}
                            <div id="user-menu-dropdown" class="hidden absolute bottom-full left-0 right-0 mb-2 rounded-lg border border-gray-200 bg-white shadow-lg z-50">
                                <div class="p-2 space-y-1">
                                    <div class="px-3 py-2 border-b border-gray-100">
                                        <p class="text-xs font-medium text-gray-500">Akun</p>
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ Auth::user()->email }}</p>
                                    </div>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full flex items-center rounded-md px-3 py-2 text-sm font-medium text-red-700 hover:bg-red-50 transition-colors">
                                            <i data-lucide="log-out" class="mr-2 h-4 w-4"></i>
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>

            {{-- Mobile Sidebar Overlay --}}
            <div id="sidebar-overlay" class="fixed inset-0 z-40 bg-gray-600 bg-opacity-75 lg:hidden hidden"></div>

            {{-- Mobile Sidebar --}}
            <aside id="mobile-sidebar" class="fixed inset-y-0 left-0 z-50 w-64 transform -translate-x-full transition-transform duration-300 ease-in-out lg:hidden bg-white border-r border-gray-200">
                <div class="flex h-full flex-col">
                    {{-- Mobile Sidebar Header --}}
                    <div class="flex h-16 items-center justify-between px-6 border-b border-gray-200">
                        <div class="flex items-center space-x-2">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-brand">
                                <i data-lucide="building" class="h-5 w-5 text-white"></i>
                            </div>
                            <span class="text-lg font-semibold text-gray-900">SIA FEB UNJ</span>
                        </div>
                        <button onclick="toggleMobileSidebar()" class="text-gray-400 hover:text-gray-500">
                            <i data-lucide="x" class="h-6 w-6"></i>
                        </button>
                    </div>

                    {{-- Mobile Navigation (Same as desktop) --}}
                    <nav class="flex-1 space-y-1 px-3 py-4 overflow-y-auto">
                        <a href="{{ route('dashboard') }}" 
                           class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-brand text-white' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                            <i data-lucide="layout-dashboard" class="mr-3 h-5 w-5"></i>
                            Dashboard
                        </a>

                        <div class="pt-2">
                            <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Surat</h3>
                            <div class="mt-2 space-y-1">
                                @if (auth()->user()->isAdmin() || auth()->user()->isOperator())
                                    <a href="{{ route('letters.create') }}" 
                                       class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('letters.create') ? 'bg-brand text-white' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                                        <i data-lucide="plus-circle" class="mr-3 h-5 w-5"></i>
                                        Buat Surat Baru
                                    </a>
                                @endif
                                <a href="{{ route('letters.index') }}" 
                                   class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('letters.index') ? 'bg-brand text-white' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                                    <i data-lucide="mail" class="mr-3 h-5 w-5"></i>
                                    Daftar Surat
                                </a>
                            </div>
                        </div>

                        @if (auth()->user()->isAdmin())
                            <div class="pt-4">
                                <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Master Data</h3>
                                <div class="mt-2 space-y-1">
                                    <a href="{{ route('master.classification-letters.index') }}" 
                                       class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('master.classification-letters.*') ? 'bg-brand text-white' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                                        <i data-lucide="tags" class="mr-3 h-5 w-5"></i>
                                        Klasifikasi Surat
                                    </a>
                                    <a href="{{ route('master.letter-types.index') }}" 
                                       class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('master.letter-types.*') ? 'bg-brand text-white' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                                        <i data-lucide="file-text" class="mr-3 h-5 w-5"></i>
                                        Jenis Surat
                                    </a>
                                    <a href="{{ route('master.letter-purposes.index') }}" 
                                       class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('master.letter-purposes.*') ? 'bg-brand text-white' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                                        <i data-lucide="bookmark" class="mr-3 h-5 w-5"></i>
                                        Keperluan Surat
                                    </a>
                                    <a href="{{ route('master.signatories.index') }}" 
                                       class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('master.signatories.*') ? 'bg-brand text-white' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                                        <i data-lucide="user-check" class="mr-3 h-5 w-5"></i>
                                        Penandatangan
                                    </a>
                                    <a href="{{ route('master.users.index') }}" 
                                       class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('master.users.*') ? 'bg-brand text-white' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                                        <i data-lucide="users" class="mr-3 h-5 w-5"></i>
                                        Pengguna
                                    </a>
                                </div>
                            </div>
                        @endif
                    </nav>

                    {{-- Mobile User Menu --}}
                    <div class="border-t border-gray-200 p-3">
                        <div class="relative">
                            <button onclick="toggleMobileUserMenu()" class="w-full flex items-center rounded-lg px-3 py-2 text-sm transition-colors hover:bg-gray-100">
                                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-primary text-primary-foreground">
                                    <span class="text-xs font-semibold">{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</span>
                                </div>
                                <div class="ml-3 flex-1 text-left">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-gray-500 capitalize">{{ Auth::user()->role }}</p>
                                </div>
                                <i data-lucide="chevron-up" class="h-4 w-4 text-gray-400 transition-transform duration-200" id="mobile-user-menu-icon"></i>
                            </button>
                            
                            {{-- Mobile Dropdown Menu --}}
                            <div id="mobile-user-menu-dropdown" class="hidden absolute bottom-full left-0 right-0 mb-2 rounded-lg border border-gray-200 bg-white shadow-lg">
                                <div class="p-2 space-y-1">
                                    <div class="px-3 py-2 border-b border-gray-100">
                                        <p class="text-xs font-medium text-gray-500">Akun</p>
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ Auth::user()->email }}</p>
                                    </div>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full flex items-center rounded-md px-3 py-2 text-sm font-medium text-red-700 hover:bg-red-50 transition-colors">
                                            <i data-lucide="log-out" class="mr-2 h-4 w-4"></i>
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>

            {{-- Main Content --}}
            <div class="flex flex-1 flex-col">
                {{-- Top Header --}}
                <header class="sticky top-0 z-10 flex h-16 flex-shrink-0 items-center border-b border-gray-200 bg-white">
                    <div class="flex flex-1 justify-between px-4 sm:px-6 lg:px-8">
                        <div class="flex flex-1 items-center">
                            <button onclick="toggleMobileSidebar()" class="lg:hidden p-2 text-gray-400 hover:text-gray-500">
                                <i data-lucide="menu" class="h-6 w-6"></i>
                            </button>
                            <h1 class="ml-4 lg:ml-0 text-lg font-semibold text-gray-900">@yield('title', 'Dashboard')</h1>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="hidden sm:flex items-center text-sm text-gray-500">
                                <i data-lucide="calendar" class="mr-2 h-4 w-4"></i>
                                <span>{{ now()->format('d M Y') }}</span>
                            </div>
                            <div class="hidden sm:flex items-center text-sm text-gray-500">
                                <i data-lucide="clock" class="mr-2 h-4 w-4"></i>
                                <span id="current-time">{{ now()->format('H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </header>

                {{-- Main Content Area --}}
                <main class="flex-1 overflow-y-auto">
                    {{-- Flash Messages --}}
                    @if (session('success'))
                        <div class="mx-4 mt-4 sm:mx-6 lg:mx-8 sm:mt-6 lg:mt-8">
                            <div class="rounded-lg bg-green-50 p-4 border border-green-200">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <i data-lucide="check-circle" class="h-5 w-5 text-green-400"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                                    </div>
                                    <div class="ml-auto pl-3">
                                        <div class="-mx-1.5 -my-1.5">
                                            <button type="button" onclick="this.closest('.rounded-lg').remove()" class="inline-flex rounded-md p-1.5 text-green-500 hover:bg-success-lighter focus:outline-none">
                                                <i data-lucide="x" class="h-5 w-5"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mx-4 mt-4 sm:mx-6 lg:mx-8 sm:mt-6 lg:mt-8">
                            <div class="rounded-lg bg-red-50 p-4 border border-red-200">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <i data-lucide="alert-circle" class="h-5 w-5 text-red-400"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                                    </div>
                                    <div class="ml-auto pl-3">
                                        <div class="-mx-1.5 -my-1.5">
                                            <button type="button" onclick="this.closest('.rounded-lg').remove()" class="inline-flex rounded-md p-1.5 text-red-500 hover:bg-red-100 focus:outline-none">
                                                <i data-lucide="x" class="h-5 w-5"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mx-4 mt-4 sm:mx-6 lg:mx-8 sm:mt-6 lg:mt-8">
                            <div class="rounded-lg bg-red-50 p-4 border border-red-200">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <i data-lucide="alert-triangle" class="h-5 w-5 text-red-400"></i>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-red-800">Terjadi kesalahan:</h3>
                                        <ul class="mt-2 list-disc list-inside text-sm text-red-700">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <div class="ml-auto pl-3">
                                        <div class="-mx-1.5 -my-1.5">
                                            <button type="button" onclick="this.closest('.rounded-lg').remove()" class="inline-flex rounded-md p-1.5 text-red-500 hover:bg-red-100 focus:outline-none">
                                                <i data-lucide="x" class="h-5 w-5"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Page Content --}}
                    <div class="p-4 sm:p-6 lg:p-8">
                        @yield('content')
                    </div>
                </main>
            </div>
        </div>
    @else
        {{-- Login Page (Full Width) --}}
        <main class="flex min-h-screen items-center justify-center bg-gray-50 px-4 sm:px-6 lg:px-8">
            @yield('content')
        </main>
    @endauth

    {{-- JavaScript --}}
    {{-- Select2 JS --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    {{-- Flatpickr JS (Datepicker) --}}
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
    
    <script>
        // Initialize Lucide Icons
        lucide.createIcons();

        // CSRF Token Auto-Refresh
        // Refresh CSRF token setiap 60 menit untuk mencegah expired
        setInterval(function() {
            fetch('/sanctum/csrf-cookie')
                .then(response => {
                    console.log('CSRF token refreshed');
                })
                .catch(error => {
                    console.error('Failed to refresh CSRF token:', error);
                });
        }, 60 * 60 * 1000); // 60 menit

        // Handle Logout dengan error handling
        function handleLogout(event) {
            event.preventDefault();
            const form = event.target;
            
            // Ambil CSRF token terbaru
            const metaToken = document.querySelector('meta[name="csrf-token"]');
            const csrfInput = form.querySelector('input[name="_token"]');
            
            if (metaToken && csrfInput) {
                csrfInput.value = metaToken.content;
            }
            
            // Submit form
            form.submit();
        }

        // Attach logout handler to all logout forms
        document.addEventListener('DOMContentLoaded', function() {
            const logoutForms = document.querySelectorAll('form[action*="logout"]');
            logoutForms.forEach(form => {
                form.addEventListener('submit', handleLogout);
            });
        });

        // Mobile Sidebar Toggle
        function toggleMobileSidebar() {
            const sidebar = document.getElementById('mobile-sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        // Close mobile sidebar when clicking overlay
        document.getElementById('sidebar-overlay')?.addEventListener('click', toggleMobileSidebar);

        // User Menu Toggle (Desktop)
        function toggleUserMenu() {
            const dropdown = document.getElementById('user-menu-dropdown');
            const icon = document.getElementById('user-menu-icon');
            
            dropdown.classList.toggle('hidden');
            icon.style.transform = dropdown.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
            
            // Re-initialize Lucide icons for dropdown
            lucide.createIcons();
        }

        // User Menu Toggle (Mobile)
        function toggleMobileUserMenu() {
            const dropdown = document.getElementById('mobile-user-menu-dropdown');
            const icon = document.getElementById('mobile-user-menu-icon');
            
            dropdown.classList.toggle('hidden');
            icon.style.transform = dropdown.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
            
            // Re-initialize Lucide icons for dropdown
            lucide.createIcons();
        }

        // Close user menu when clicking outside
        document.addEventListener('click', function(event) {
            const userMenuButton = event.target.closest('button[onclick="toggleUserMenu()"]');
            const userMenuDropdown = document.getElementById('user-menu-dropdown');
            const mobileUserMenuButton = event.target.closest('button[onclick="toggleMobileUserMenu()"]');
            const mobileUserMenuDropdown = document.getElementById('mobile-user-menu-dropdown');
            
            if (!userMenuButton && userMenuDropdown && !userMenuDropdown.contains(event.target)) {
                userMenuDropdown.classList.add('hidden');
                document.getElementById('user-menu-icon').style.transform = 'rotate(0deg)';
            }
            
            if (!mobileUserMenuButton && mobileUserMenuDropdown && !mobileUserMenuDropdown.contains(event.target)) {
                mobileUserMenuDropdown.classList.add('hidden');
                document.getElementById('mobile-user-menu-icon').style.transform = 'rotate(0deg)';
            }
        });

        // Update time every minute
        setInterval(() => {
            const timeElement = document.getElementById('current-time');
            if (timeElement) {
                const now = new Date();
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                timeElement.textContent = `${hours}:${minutes}`;
            }
        }, 60000);

        // Re-initialize Lucide icons after any dynamic content changes
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });
    </script>

    @stack('scripts')
</body>

</html>





