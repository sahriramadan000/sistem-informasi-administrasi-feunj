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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    {{-- ICON --}}
    <link rel="icon" type="image/png" href="{{ asset('logo-unj.png') }}">

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

        /* User Menu Name Truncate */
        .user-name-display {
            max-width: 120px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* Perihal Column - Limited width with wrapping */
        .perihal-display {
            max-width: 400px;
            word-wrap: break-word;
            overflow-wrap: break-word;
            word-break: break-word;
            line-height: 1.4;
        }

        /* ── Apple iOS / macOS Style Sidebar (Pure White Modern Edition) ── */
        .ios-sidebar-aside {
            width: 260px !important;
            flex-shrink: 0 !important;
            transition: width 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .ios-sidebar-inner {
            width: 260px !important;
            height: 100vh !important;
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            bottom: 0 !important;
            background-color: #ffffff !important; /* Pure White Sidebar */
            border-right: 1px solid #e5e7eb !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: space-between !important;
            z-index: 20 !important;
            box-sizing: border-box !important;
            transition: width 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .ios-mobile-sidebar {
            width: 280px !important;
            height: 100vh !important;
            background-color: #ffffff !important; /* Pure White Mobile Sidebar */
            border-right: 1px solid #e5e7eb !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: space-between !important;
            box-sizing: border-box !important;
        }

        /* Collapsed sidebar mode */
        #sidebar.sidebar-collapsed {
            width: 68px !important;
        }
        #sidebar.sidebar-collapsed .ios-sidebar-inner {
            width: 68px !important;
        }
        #sidebar.sidebar-collapsed .sidebar-hide-on-collapsed {
            display: none !important;
        }
        #sidebar.sidebar-collapsed .ios-nav-item {
            justify-content: center !important;
            padding: 9px 0 !important;
        }
        #sidebar.sidebar-collapsed .ios-nav-item .ios-menu-icon {
            margin-right: 0 !important;
        }
        #sidebar.sidebar-collapsed .ios-item-label {
            justify-content: center !important;
        }

        /* Workspace Header Button */
        .ios-workspace-btn {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            padding: 6px 8px;
            border-radius: 12px;
            background: transparent;
            border: 1px solid transparent;
            cursor: pointer;
            transition: all 0.15s ease;
            text-align: left;
        }
        .ios-workspace-btn:hover {
            background-color: #f8fafc;
            border-color: #e5e7eb;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
        }

        /* Search Bar (Contrasting Light Grey Pill inside White Sidebar) */
        .ios-search-wrapper {
            display: flex;
            align-items: center;
            background-color: #f3f4f6;
            border-radius: 12px;
            padding: 6px 10px;
            transition: all 0.15s ease;
            border: 1px solid #e5e7eb;
        }
        .ios-search-wrapper:focus-within {
            background-color: #ffffff;
            border-color: #ea580c;
            box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.12);
        }
        .ios-search-icon {
            width: 15px !important;
            height: 15px !important;
            min-width: 15px !important;
            min-height: 15px !important;
            color: #94a3b8 !important;
            flex-shrink: 0 !important;
        }
        .ios-search-input {
            width: 100%;
            background: transparent;
            border: none;
            outline: none;
            font-size: 13px;
            color: #1e293b;
            margin-left: 8px;
        }
        .ios-search-input::placeholder {
            color: #94a3b8;
        }
        .ios-kbd-badge {
            font-size: 10px;
            font-weight: 600;
            color: #64748b;
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 1px 5px;
            line-height: 1.2;
            box-shadow: 0 1px 1px rgba(0, 0, 0, 0.04);
            margin-left: 2px;
            flex-shrink: 0;
        }

        /* Section Headings */
        .ios-section-title {
            font-size: 11px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 10px 10px 4px 10px;
        }

        /* Nav Items */
        .ios-nav-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 7px 10px;
            margin-bottom: 3px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 500;
            color: #475569;
            text-decoration: none;
            transition: all 0.15s cubic-bezier(0.16, 1, 0.3, 1);
            border: 1px solid transparent;
            outline: none;
            box-sizing: border-box;
            position: relative;
        }
        .ios-nav-item:hover {
            background-color: #f8fafc;
            color: #0f172a;
            border-color: #f1f5f9;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
        }

        /* ACTIVE ITEM: Warm Brand Tint Capsule on White Canvas */
        .ios-nav-item.active {
            background-color: #fff7ed !important;
            color: #9a3412 !important;
            font-weight: 600 !important;
            border-color: #ffedd5 !important;
            box-shadow: 0 1px 3px rgba(255, 77, 0, 0.06), 0 1px 2px rgba(0, 0, 0, 0.02) !important;
        }
        .ios-nav-item.active::before {
            content: '';
            position: absolute;
            left: -2px;
            top: 6px;
            bottom: 6px;
            width: 3.5px;
            border-radius: 4px;
            background: linear-gradient(to bottom, #FF4D00, #ea580c);
            box-shadow: 0 0 6px rgba(255, 77, 0, 0.4);
        }
        .ios-nav-item.active .ios-menu-icon {
            color: #ea580c !important;
        }

        .ios-item-label {
            display: flex;
            align-items: center;
            min-width: 0;
            flex: 1;
        }

        /* Menu Icons */
        .ios-menu-icon {
            width: 18px !important;
            height: 18px !important;
            min-width: 18px !important;
            min-height: 18px !important;
            margin-right: 10px !important;
            stroke-width: 1.9 !important;
            color: #64748b;
            flex-shrink: 0 !important;
            transition: color 0.15s ease;
        }
        .ios-nav-item:hover .ios-menu-icon {
            color: #0f172a;
        }

        /* Right Chevrons */
        .ios-right-chevron {
            width: 14px !important;
            height: 14px !important;
            min-width: 14px !important;
            min-height: 14px !important;
            stroke-width: 2 !important;
            color: #cbd5e1;
            flex-shrink: 0 !important;
            transition: transform 0.15s ease, color 0.15s ease;
        }
        .ios-nav-item:hover .ios-right-chevron {
            color: #94a3b8;
        }

        /* Pinned Section */
        .ios-pinned-badge {
            width: 24px !important;
            height: 24px !important;
            min-width: 24px !important;
            min-height: 24px !important;
            border-radius: 7px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 11.5px;
            font-weight: 700;
            margin-right: 10px;
            flex-shrink: 0;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
        }
        .ios-pin-icon {
            width: 13px !important;
            height: 13px !important;
            min-width: 13px !important;
            min-height: 13px !important;
            stroke-width: 1.8 !important;
            color: #cbd5e1;
            flex-shrink: 0 !important;
            transform: rotate(35deg);
            transition: color 0.15s ease;
        }
        .ios-nav-item:hover .ios-pin-icon {
            color: #94a3b8;
        }

        /* User Profile Capsule */
        .ios-user-capsule {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            padding: 8px 10px;
            border-radius: 14px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
            cursor: pointer;
            transition: all 0.15s ease;
            text-align: left;
        }
        .ios-user-capsule:hover {
            background-color: #f1f5f9;
            border-color: #cbd5e1;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
        }

        /* ── Top Header / Navbar (Pure White Apple macOS Style) ── */
        .ios-header {
            background-color: #ffffff !important;
            border-bottom: 1px solid #e5e7eb !important;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.02) !important;
        }

        /* macOS Spotlight Quick Command Capsule */
        .ios-spotlight-btn {
            display: inline-flex;
            align-items: center;
            justify-content: space-between;
            width: 260px;
            padding: 6px 12px;
            border-radius: 9999px;
            background-color: #f3f4f6;
            border: 1px solid #e5e7eb;
            color: #64748b;
            font-size: 12px;
            transition: all 0.15s ease;
            cursor: pointer;
        }
        .ios-spotlight-btn:hover {
            background-color: #ffffff;
            border-color: #cbd5e1;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            color: #1e293b;
        }

        /* macOS Status / Live Capsule */
        .ios-status-capsule {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 9999px;
            background-color: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #065f46;
            font-size: 11.5px;
            font-weight: 600;
            letter-spacing: -0.01em;
            box-shadow: 0 1px 2px rgba(16, 185, 129, 0.04);
        }

        /* macOS Clock Capsule */
        .ios-clock-capsule {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 5px 12px;
            border-radius: 9999px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #334155;
            font-size: 12px;
            font-weight: 500;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
            transition: all 0.15s ease;
        }
        .ios-clock-capsule:hover {
            background-color: #ffffff;
            border-color: #cbd5e1;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        /* macOS Round Action Button */
        .ios-header-icon-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 9999px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #475569;
            transition: all 0.15s ease;
            cursor: pointer;
            text-decoration: none;
            position: relative;
        }
        .ios-header-icon-btn:hover {
            background-color: #ffffff;
            border-color: #cbd5e1;
            color: #0f172a;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }
        .ios-header-icon-btn:active {
            transform: scale(0.96);
        }

        /* macOS User Profile Capsule */
        .ios-header-user-btn {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px 4px 4px;
            border-radius: 9999px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            transition: all 0.15s ease;
            cursor: pointer;
            text-decoration: none;
        }
        .ios-header-user-btn:hover {
            background-color: #ffffff;
            border-color: #cbd5e1;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        /* macOS Flyout Dropdown Card */
        .ios-flyout-card {
            border-radius: 18px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
            animation: iosDropdownFade 0.15s cubic-bezier(0.16, 1, 0.3, 1);
        }
        @keyframes iosDropdownFade {
            from {
                opacity: 0;
                transform: translateY(-6px) scale(0.98);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* Custom Scrollbar */
        .ios-sidebar-scroll {
            overflow-y: auto;
            overflow-x: hidden;
        }
        .ios-sidebar-scroll::-webkit-scrollbar {
            width: 4px;
        }
        .ios-sidebar-scroll::-webkit-scrollbar-track {
            background: transparent;
        }
        .ios-sidebar-scroll::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.08);
            border-radius: 4px;
        }
        .ios-sidebar-scroll::-webkit-scrollbar-thumb:hover {
            background: rgba(0, 0, 0, 0.16);
        }
    </style>

    @stack('styles')
</head>

<body class="h-full">
    @auth
        <div class="min-h-screen flex bg-gray-50/50">
            {{-- Desktop Sidebar (iOS / macOS Style) --}}
            <aside id="sidebar" class="hidden lg:flex ios-sidebar-aside">
                <div class="ios-sidebar-inner">
                    @include('layouts.sidebar-content', ['isMobile' => false])
                </div>
            </aside>

            {{-- Mobile Sidebar Overlay --}}
            <div id="sidebar-overlay" class="fixed inset-0 z-40 bg-gray-900/40 lg:hidden hidden transition-opacity"></div>

            {{-- Mobile Sidebar (Slide-over with iOS Style) --}}
            <aside id="mobile-sidebar"
                class="fixed inset-y-0 left-0 z-50 transform -translate-x-full transition-transform duration-300 ease-in-out lg:hidden ios-mobile-sidebar">
                @include('layouts.sidebar-content', ['isMobile' => true])
            </aside>

            {{-- Main Content --}}
            <div class="flex flex-1 flex-col min-w-0">
                {{-- Top Header (Minimal Apple macOS Toolbar) --}}
                <header class="sticky top-0 z-10 flex h-16 flex-shrink-0 items-center ios-header">
                    <div class="flex flex-1 justify-between items-center px-4 sm:px-6 lg:px-6">
                        {{-- Left: macOS Sidebar Controls + Apple Two-Tier Page Header --}}
                        <div class="flex items-center min-w-0 space-x-2 sm:space-x-2.5">
                            {{-- Mobile Sidebar Toggle --}}
                            <button type="button" onclick="toggleMobileSidebar()"
                                class="lg:hidden p-2 text-gray-500 hover:text-gray-900 rounded-xl hover:bg-gray-100 transition-colors"
                                title="Buka Navigasi">
                                <i data-lucide="menu" class="h-5 w-5"></i>
                            </button>

                            {{-- Desktop Sidebar Toggle (macOS Window Control Style) --}}
                            <button type="button" onclick="toggleSidebarCollapse()"
                                class="hidden lg:inline-flex items-center justify-center w-8 h-8 rounded-xl text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition-all"
                                title="Perluas / Sembunyikan Sidebar (⌘B)">
                                <i data-lucide="panel-left" class="w-4 h-4"></i>
                            </button>

                            @php
                                $modName = 'Persuratan';
                                if (request()->routeIs('legalizations.*', 'master.education-levels.*')) {
                                    $modName = 'Legalisir';
                                } elseif (request()->routeIs('room-reservations.*', 'master.rooms.*', 'master.room-groups.*')) {
                                    $modName = 'Ruangan';
                                } elseif (request()->routeIs('admin.*', 'master.users.*')) {
                                    $modName = 'Pengaturan';
                                }
                            @endphp

                            {{-- Clean Inline Context & Page Title (13px matching sidebar typography) --}}
                            <div class="flex items-center space-x-1.5 min-w-0">
                                <span class="hidden sm:inline text-[13px] font-medium text-gray-500 truncate">{{ $modName }}</span>
                                <span class="hidden sm:inline text-gray-300 text-[13px] font-light">/</span>
                                <h1 class="text-[13px] sm:text-[13.5px] font-bold text-gray-900 truncate tracking-tight">@yield('title', 'Dashboard')</h1>
                            </div>
                        </div>

                        {{-- Right: Essential Apple macOS Toolbar Controls (NO Module Switcher) --}}
                        <div class="flex items-center space-x-2.5 flex-shrink-0">
                            {{-- Apple macOS Menu Bar Date & Live Clock --}}
                            <div class="hidden sm:inline-flex ios-clock-capsule" title="Waktu Sistem">
                                <i data-lucide="calendar" style="width: 13px; height: 13px; color: #94a3b8;"></i>
                                <span class="text-[11.5px] font-medium text-gray-700">{{ now()->format('d M Y') }}</span>
                                <span style="width: 3px; height: 3px; border-radius: 50%; background: #cbd5e1;"></span>
                                <i data-lucide="clock" style="width: 13px; height: 13px; color: #ea580c;"></i>
                                <span id="current-time" class="text-[11.5px] font-bold text-gray-900 tracking-wide font-mono">{{ now()->format('H:i') }}</span>
                            </div>

                            {{-- 3. Apple Notification Center Button with iOS Dropdown --}}
                            <div class="relative">
                                <button type="button" onclick="toggleNotificationDropdown()" class="ios-header-icon-btn" title="Pusat Pemberitahuan">
                                    <i data-lucide="bell" style="width: 15px; height: 15px; color: #475569;"></i>
                                    <span class="absolute top-2 right-2 w-2 h-2 rounded-full bg-orange-500 ring-2 ring-white"></span>
                                </button>

                                {{-- Notification Flyout Card --}}
                                <div id="notification-dropdown"
                                    class="hidden absolute right-0 mt-2 w-80 ios-flyout-card z-50 p-3 space-y-2">
                                    <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                                        <div class="flex items-center gap-1.5">
                                            <i data-lucide="bell" style="width: 14px; height: 14px; color: #ea580c;"></i>
                                            <span class="text-xs font-bold text-gray-900">Pusat Pemberitahuan</span>
                                        </div>
                                        <span class="px-2 py-0.5 rounded-full bg-orange-100 text-orange-700 text-[10px] font-bold">1 Baru</span>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-orange-50/70 border border-orange-100/90 flex items-start space-x-2.5">
                                        <div class="w-7 h-7 rounded-lg bg-orange-100 flex items-center justify-center flex-shrink-0 text-orange-600 mt-0.5">
                                            <i data-lucide="check-circle-2" style="width: 15px; height: 15px;"></i>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-xs font-semibold text-gray-900">Sistem Berjalan Optimal</p>
                                            <p class="text-[11px] text-gray-500 mt-0.5 leading-snug">Semua layanan persuratan, legalisir, dan ruangan siap digunakan.</p>
                                            <p class="text-[10px] text-orange-600 font-medium mt-1">Hari ini • {{ now()->format('H:i') }} WIB</p>
                                        </div>
                                    </div>
                                    <button type="button" onclick="toggleNotificationDropdown()"
                                        class="w-full text-center py-1.5 text-[11px] font-semibold text-gray-500 hover:text-gray-900 rounded-lg hover:bg-gray-50 transition-colors">
                                        Tutup
                                    </button>
                                </div>
                            </div>

                            {{-- 4. Apple User Profile Capsule with Control Center Dropdown (NO MODULE SWITCHER) --}}
                            <div class="relative">
                                <button type="button" onclick="toggleHeaderUserMenu()"
                                    class="ios-header-user-btn group"
                                    title="Menu Akun ({{ Auth::user()->name }})">
                                    <div class="w-7 h-7 rounded-full text-white flex items-center justify-center font-bold text-[10px] shadow-xs mr-2 ring-1 ring-orange-500/20"
                                        style="background: linear-gradient(135deg, #FF4D00, #F59E0B);">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                                    </div>
                                    <div class="text-left hidden md:block mr-1">
                                        <span class="text-xs font-bold text-gray-800 block leading-tight max-w-[100px] truncate">
                                            {{ explode(' ', Auth::user()->name)[0] }}
                                        </span>
                                        <span class="text-[10px] text-gray-500 block leading-none capitalize">
                                            {{ Auth::user()->role }}
                                        </span>
                                    </div>
                                    <i data-lucide="chevron-down" style="width: 14px; height: 14px; color: #94a3b8;" class="group-hover:text-gray-700 transition-transform duration-200" id="header-user-chevron"></i>
                                </button>

                                {{-- macOS Control Center Style User Card --}}
                                <div id="header-user-dropdown"
                                    class="hidden absolute right-0 mt-2 w-64 ios-flyout-card z-50 p-2 space-y-1">
                                    <div class="p-2.5 rounded-xl bg-gray-50 border border-gray-100 mb-1">
                                        <div class="flex items-center space-x-2.5">
                                            <div class="w-9 h-9 rounded-full text-white flex items-center justify-center font-bold text-xs shadow-xs flex-shrink-0"
                                                style="background: linear-gradient(135deg, #FF4D00, #F59E0B);">
                                                {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-xs font-bold text-gray-900 truncate">{{ Auth::user()->name }}</p>
                                                <p class="text-[11px] text-gray-500 truncate">{{ Auth::user()->email }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Edit Profile --}}
                                    <a href="{{ route('profile.edit') }}"
                                        class="flex items-center px-3 py-2 text-xs font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100 rounded-xl transition-colors">
                                        <i data-lucide="user" style="width: 15px; height: 15px; margin-right: 8px; color: #64748b;"></i>
                                        Pengaturan Profil
                                    </a>

                                    <div class="border-t border-gray-100 my-1"></div>

                                    {{-- Logout --}}
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit"
                                            class="w-full flex items-center px-3 py-2 text-xs font-semibold text-red-600 hover:bg-red-50 rounded-xl transition-colors">
                                            <i data-lucide="log-out" style="width: 15px; height: 15px; margin-right: 8px; color: #ef4444;"></i>
                                            Keluar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                {{-- Main Content Area --}}
                <main class="flex-1 overflow-y-auto">
                    {{-- Flash Messages --}}
                    @if (!View::hasSection('disable_layout_alerts'))
                        @if (session('success'))
                            <div class="mx-4 mt-4 sm:mx-6 lg:mx-8 sm:mt-6 lg:mt-8">
                                <x-alert type="success" message="{{ session('success') }}" />
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="mx-4 mt-4 sm:mx-6 lg:mx-8 sm:mt-6 lg:mt-8">
                                <x-alert type="error" message="{{ session('error') }}" :errorId="session('last_error_id')" />
                            </div>
                        @endif

                        @if (session('warning'))
                            <div class="mx-4 mt-4 sm:mx-6 lg:mx-8 sm:mt-6 lg:mt-8">
                                <x-alert type="warning" message="{{ session('warning') }}" />
                            </div>
                        @endif

                        @if (is_object($errors) && $errors->any())
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
                                                    <li>{!! $error !!}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        <div class="ml-auto pl-3">
                                            <div class="-mx-1.5 -my-1.5">
                                                <button type="button" onclick="this.closest('.rounded-lg').remove()"
                                                    class="inline-flex rounded-md p-1.5 text-red-500 hover:bg-red-100 focus:outline-none">
                                                    <i data-lucide="x" class="h-5 w-5"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
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

        // Module Dropdown Toggle (Desktop)
        function toggleModuleDropdown() {
            const dropdown = document.getElementById('module-dropdown');
            if (dropdown) {
                dropdown.classList.toggle('hidden');
                lucide.createIcons();
            }
        }

        // Module Dropdown Toggle (Mobile)
        function toggleMobileModuleDropdown() {
            const dropdown = document.getElementById('mobile-module-dropdown');
            if (dropdown) {
                dropdown.classList.toggle('hidden');
                lucide.createIcons();
            }
        }

        // Desktop Sidebar Collapse Toggle (macOS style)
        function toggleSidebarCollapse() {
            const sidebar = document.getElementById('sidebar');
            if (sidebar) {
                sidebar.classList.toggle('sidebar-collapsed');
                const isCollapsed = sidebar.classList.contains('sidebar-collapsed');
                localStorage.setItem('feb_sidebar_collapsed', isCollapsed ? '1' : '0');
            }
        }

        // Restore sidebar state
        document.addEventListener('DOMContentLoaded', () => {
            if (localStorage.getItem('feb_sidebar_collapsed') === '1') {
                document.getElementById('sidebar')?.classList.add('sidebar-collapsed');
            }
        });

        // Filter Sidebar Menu in Real-Time
        function filterSidebarMenu(query, prefix = '') {
            const q = query.toLowerCase().trim();
            const containerId = prefix ? 'mobile-nav-container' : 'desktop-nav-container';
            const container = document.getElementById(containerId);
            if (!container) return;

            const sections = container.querySelectorAll('.ios-nav-section');
            const pinned = container.querySelector('.pinned-section');

            sections.forEach(section => {
                const items = section.querySelectorAll('.ios-nav-item');
                let sectionHasMatch = false;

                items.forEach(item => {
                    const text = item.textContent.toLowerCase();
                    if (!q || text.includes(q)) {
                        item.style.display = '';
                        sectionHasMatch = true;
                    } else {
                        item.style.display = 'none';
                    }
                });

                section.style.display = sectionHasMatch || !q ? '' : 'none';
            });

            if (pinned) {
                pinned.style.display = q ? 'none' : '';
            }
        }

        // Focus search input from header Spotlight button or shortcuts
        function focusSearch() {
            const sidebar = document.getElementById('sidebar');
            if (sidebar && sidebar.classList.contains('sidebar-collapsed')) {
                toggleSidebarCollapse();
            }
            const search = document.getElementById('sidebar-search') || document.getElementById('mobile-sidebar-search');
            if (search) {
                search.focus();
                search.select();
            }
        }

        // Keyboard Shortcut ⌘K or / to Focus Search
        document.addEventListener('keydown', (e) => {
            if (((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k') || 
                (e.key === '/' && document.activeElement.tagName !== 'INPUT' && document.activeElement.tagName !== 'TEXTAREA')) {
                e.preventDefault();
                focusSearch();
            }
        });

        // Top Header Notification Dropdown Toggle
        function toggleNotificationDropdown() {
            const dropdown = document.getElementById('notification-dropdown');
            if (dropdown) {
                dropdown.classList.toggle('hidden');
                lucide.createIcons();
            }
        }

        // Top Header User Menu Toggle
        function toggleHeaderUserMenu() {
            const dropdown = document.getElementById('header-user-dropdown');
            const chevron = document.getElementById('header-user-chevron');
            if (dropdown) {
                dropdown.classList.toggle('hidden');
                if (chevron) {
                    chevron.style.transform = dropdown.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
                }
                lucide.createIcons();
            }
        }

        // User Menu Toggle (Desktop Sidebar Bottom)
        function toggleUserMenu() {
            const dropdown = document.getElementById('user-menu-dropdown');
            const icon = document.getElementById('user-menu-icon');

            dropdown.classList.toggle('hidden');
            icon.style.transform = dropdown.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';

            // Re-initialize Lucide icons for dropdown
            lucide.createIcons();
        }

        // User Menu Toggle (Mobile Sidebar Bottom)
        function toggleMobileUserMenu() {
            const dropdown = document.getElementById('mobile-user-menu-dropdown');
            const icon = document.getElementById('mobile-user-menu-icon');

            dropdown.classList.toggle('hidden');
            icon.style.transform = dropdown.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';

            // Re-initialize Lucide icons for dropdown
            lucide.createIcons();
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            // Header User Dropdown
            const headerUserBtn = event.target.closest('button[onclick*="toggleHeaderUserMenu"]');
            const headerUserDropdown = document.getElementById('header-user-dropdown');
            if (!headerUserBtn && headerUserDropdown && !headerUserDropdown.contains(event.target)) {
                headerUserDropdown.classList.add('hidden');
                const chevron = document.getElementById('header-user-chevron');
                if (chevron) chevron.style.transform = 'rotate(0deg)';
            }

            // Header Notification Dropdown
            const notifBtn = event.target.closest('button[onclick*="toggleNotificationDropdown"]');
            const notifDropdown = document.getElementById('notification-dropdown');
            if (!notifBtn && notifDropdown && !notifDropdown.contains(event.target)) {
                notifDropdown.classList.add('hidden');
            }

            // Sidebar User dropdowns
            const userMenuButton = event.target.closest('button[onclick="toggleUserMenu()"]');
            const userMenuDropdown = document.getElementById('user-menu-dropdown');
            const mobileUserMenuButton = event.target.closest('button[onclick="toggleMobileUserMenu()"]');
            const mobileUserMenuDropdown = document.getElementById('mobile-user-menu-dropdown');

            if (!userMenuButton && userMenuDropdown && !userMenuDropdown.contains(event.target)) {
                userMenuDropdown.classList.add('hidden');
                const icon = document.getElementById('user-menu-icon');
                if (icon) icon.style.transform = 'rotate(0deg)';
            }

            if (!mobileUserMenuButton && mobileUserMenuDropdown && !mobileUserMenuDropdown.contains(event.target)) {
                mobileUserMenuDropdown.classList.add('hidden');
                const icon = document.getElementById('mobile-user-menu-icon');
                if (icon) icon.style.transform = 'rotate(0deg)';
            }

            // Module dropdowns (Sidebar Workspace)
            const moduleBtn = event.target.closest('button[onclick*="toggleModuleDropdown"]');
            const moduleDropdown = document.getElementById('module-dropdown');
            const mobileModuleBtn = event.target.closest('button[onclick*="toggleMobileModuleDropdown"]');
            const mobileModuleDropdown = document.getElementById('mobile-module-dropdown');

            if (!moduleBtn && moduleDropdown && !moduleDropdown.contains(event.target)) {
                moduleDropdown.classList.add('hidden');
            }
            if (!mobileModuleBtn && mobileModuleDropdown && !mobileModuleDropdown.contains(event.target)) {
                mobileModuleDropdown.classList.add('hidden');
            }
        });

        // Update time every 10 seconds for live accuracy
        setInterval(() => {
            const timeElement = document.getElementById('current-time');
            if (timeElement) {
                const now = new Date();
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                timeElement.textContent = `${hours}:${minutes}`;
            }
        }, 10000);

        // Re-initialize Lucide icons after any dynamic content changes
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });
    </script>

    @stack('scripts')
</body>

</html>
