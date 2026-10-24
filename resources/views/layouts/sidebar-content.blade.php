{{-- iOS / macOS Aesthetic Sidebar Content Partial --}}
@php
    $isMobile = $isMobile ?? false;
@endphp

{{-- ── Top Header / Workspace Selector ── --}}
<div class="flex items-center justify-between px-3 py-3 flex-shrink-0" style="border-bottom: 1px solid #f1f5f9;">
    <div class="relative flex-1 min-w-0 mr-1">
        <button type="button" onclick="{{ $isMobile ? 'toggleMobileModuleDropdown()' : 'toggleModuleDropdown()' }}"
            class="ios-workspace-btn group">
            <div class="flex items-center space-x-2.5 min-w-0">
                <div class="w-8 h-8 rounded-xl bg-white border border-gray-200/90 p-1 flex items-center justify-center flex-shrink-0 shadow-xs">
                    <img src="{{ asset('logo-unj.png') }}" alt="Logo UNJ" class="w-7 h-7 object-contain">
                </div>
                <div class="min-w-0 flex-1 sidebar-hide-on-collapsed">
                    <div class="flex items-center space-x-1">
                        <span class="text-xs font-bold text-gray-900 truncate tracking-tight" style="font-size: 13px;">
                            @if (request()->routeIs('admin.*', 'master.users.*'))
                                Pengaturan Sistem
                            @elseif(request()->routeIs('legalizations.*', 'master.education-levels.*'))
                                Layanan Legalisir
                            @elseif(request()->routeIs('room-reservations.*', 'master.rooms.*', 'master.room-groups.*'))
                                Peminjaman Ruang
                            @else
                                Persuratan &amp; Arsip
                            @endif
                        </span>
                        <i data-lucide="chevron-down" style="width: 14px; height: 14px; color: #94a3b8; flex-shrink: 0;"></i>
                    </div>
                    <p class="text-gray-500 font-medium truncate" style="font-size: 11px;">SIADMIN FEB UNJ</p>
                </div>
            </div>
        </button>

        {{-- iOS Floating Module Quick Switcher Dropdown --}}
        <div id="{{ $isMobile ? 'mobile-module-dropdown' : 'module-dropdown' }}"
            class="hidden absolute top-full left-0 right-0 mt-2 bg-white rounded-2xl border border-gray-200 shadow-xl p-1.5 z-50 space-y-0.5">
            <div class="px-2.5 py-1.5 text-gray-400 font-bold uppercase tracking-wider" style="font-size: 10px;">Pilih Modul Aktif</div>

            <a href="{{ route('dashboard') }}"
                class="flex items-center justify-between px-2.5 py-2 text-xs font-semibold rounded-xl transition-colors {{ request()->routeIs('dashboard', 'letters.*', 'master.classification-letters.*', 'master.letter-types.*', 'master.letter-purposes.*', 'master.signatories.*') ? 'bg-orange-50 text-orange-600 font-bold' : 'text-gray-700 hover:bg-gray-100' }}">
                <span class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full {{ request()->routeIs('dashboard', 'letters.*') ? 'bg-orange-500' : 'bg-gray-300' }}"></span>
                    Persuratan &amp; Arsip
                </span>
                @if(request()->routeIs('dashboard', 'letters.*'))
                    <i data-lucide="check" style="width: 14px; height: 14px; color: #ea580c;"></i>
                @endif
            </a>

            @if(auth()->user()->can_access_legalizations || auth()->user()->isAdmin())
                <a href="{{ route('legalizations.dashboard') }}"
                    class="flex items-center justify-between px-2.5 py-2 text-xs font-semibold rounded-xl transition-colors {{ request()->routeIs('legalizations.*', 'master.education-levels.*') ? 'bg-orange-50 text-orange-600 font-bold' : 'text-gray-700 hover:bg-gray-100' }}">
                    <span class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full {{ request()->routeIs('legalizations.*') ? 'bg-orange-500' : 'bg-gray-300' }}"></span>
                        Layanan Legalisir
                    </span>
                    @if(request()->routeIs('legalizations.*'))
                        <i data-lucide="check" style="width: 14px; height: 14px; color: #ea580c;"></i>
                    @endif
                </a>
            @endif

            @if(auth()->user()->can_access_room_reservations || auth()->user()->isAdmin())
                <a href="{{ route('room-reservations.dashboard') }}"
                    class="flex items-center justify-between px-2.5 py-2 text-xs font-semibold rounded-xl transition-colors {{ request()->routeIs('room-reservations.*', 'master.rooms.*', 'master.room-groups.*') ? 'bg-orange-50 text-orange-600 font-bold' : 'text-gray-700 hover:bg-gray-100' }}">
                    <span class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full {{ request()->routeIs('room-reservations.*') ? 'bg-orange-500' : 'bg-gray-300' }}"></span>
                        Peminjaman Ruang
                    </span>
                    @if(request()->routeIs('room-reservations.*'))
                        <i data-lucide="check" style="width: 14px; height: 14px; color: #ea580c;"></i>
                    @endif
                </a>
            @endif

            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center justify-between px-2.5 py-2 text-xs font-semibold rounded-xl transition-colors {{ request()->routeIs('admin.*', 'master.users.*') ? 'bg-orange-50 text-orange-600 font-bold' : 'text-gray-700 hover:bg-gray-100' }}">
                    <span class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full {{ request()->routeIs('admin.*') ? 'bg-orange-500' : 'bg-gray-300' }}"></span>
                        Pengaturan Sistem
                    </span>
                    @if(request()->routeIs('admin.*'))
                        <i data-lucide="check" style="width: 14px; height: 14px; color: #ea580c;"></i>
                    @endif
                </a>
            @endif

            <div class="border-t border-gray-100 my-1"></div>
            <a href="{{ route('switcher') }}"
                class="flex items-center px-2.5 py-1.5 text-xs font-medium text-gray-600 hover:text-gray-900 rounded-xl hover:bg-gray-100">
                <i data-lucide="layout-grid" style="width: 14px; height: 14px; margin-right: 8px; color: #94a3b8;"></i>
                Semua Portal Modul
            </a>
        </div>
    </div>

    @if($isMobile)
        <button type="button" onclick="toggleMobileSidebar()"
            class="p-1.5 text-gray-400 hover:text-gray-700 rounded-lg transition-colors" title="Tutup Menu">
            <i data-lucide="x" style="width: 18px; height: 18px;"></i>
        </button>
    @endif
</div>

{{-- ── Search Bar (Matches iOS Reference Image) ── --}}
<div class="px-3 pt-3 pb-1 sidebar-hide-on-collapsed flex-shrink-0">
    <div class="ios-search-wrapper">
        <i data-lucide="search" class="ios-search-icon"></i>
        <input type="text" id="{{ $isMobile ? 'mobile-sidebar-search' : 'sidebar-search' }}" placeholder="Search menu..."
            oninput="filterSidebarMenu(this.value, '{{ $isMobile ? 'mobile-' : '' }}')"
            class="ios-search-input">
        <div class="flex items-center space-x-1 flex-shrink-0">
            <span class="ios-kbd-badge">⌘K</span>
            <span class="ios-kbd-badge">/</span>
        </div>
    </div>
</div>

{{-- ── Sidebar Navigation List ── --}}
<nav id="{{ $isMobile ? 'mobile-nav-container' : 'desktop-nav-container' }}" class="flex-1 px-3 py-2 space-y-0.5 ios-sidebar-scroll">

    @if (request()->routeIs('admin.*', 'master.users.*'))
        {{-- ========================================== --}}
        {{-- NAVIGASI MODUL PENGATURAN --}}
        {{-- ========================================== --}}
        <div class="ios-nav-section">
            <div class="ios-section-title sidebar-hide-on-collapsed">Main Menu</div>
            <a href="{{ route('admin.dashboard') }}" class="ios-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" title="Dashboard">
                <span class="ios-item-label">
                    <i data-lucide="layout-grid" class="ios-menu-icon"></i>
                    <span class="truncate sidebar-hide-on-collapsed">Dashboard</span>
                </span>
            </a>
        </div>

        <div class="ios-nav-section pt-2">
            <div class="ios-section-title sidebar-hide-on-collapsed">Akses &amp; Pengguna</div>
            <a href="{{ route('master.users.index') }}" class="ios-nav-item {{ request()->routeIs('master.users.*') ? 'active' : '' }}" title="Manajemen Pengguna">
                <span class="ios-item-label">
                    <i data-lucide="users" class="ios-menu-icon"></i>
                    <span class="truncate sidebar-hide-on-collapsed">Manajemen Pengguna</span>
                </span>
                <i data-lucide="chevron-right" class="ios-right-chevron sidebar-hide-on-collapsed"></i>
            </a>
        </div>

        <div class="ios-nav-section pt-2">
            <div class="ios-section-title sidebar-hide-on-collapsed">Monitoring Sistem</div>
            <a href="{{ route('admin.error-logs.index') }}" class="ios-nav-item {{ request()->routeIs('admin.error-logs.*') ? 'active' : '' }}" title="Error Logs">
                <span class="ios-item-label">
                    <i data-lucide="alert-triangle" class="ios-menu-icon"></i>
                    <span class="truncate sidebar-hide-on-collapsed">Error Logs</span>
                </span>
                <i data-lucide="chevron-right" class="ios-right-chevron sidebar-hide-on-collapsed"></i>
            </a>
            <a href="{{ route('admin.activity-logs.index') }}" class="ios-nav-item {{ request()->routeIs('admin.activity-logs.*') ? 'active' : '' }}" title="Activity Logs">
                <span class="ios-item-label">
                    <i data-lucide="activity" class="ios-menu-icon"></i>
                    <span class="truncate sidebar-hide-on-collapsed">Activity Logs</span>
                </span>
                <i data-lucide="chevron-right" class="ios-right-chevron sidebar-hide-on-collapsed"></i>
            </a>
        </div>

    @elseif(request()->routeIs('legalizations.*', 'master.education-levels.*'))
        {{-- ========================================== --}}
        {{-- NAVIGASI MODUL LEGALISIR --}}
        {{-- ========================================== --}}
        <div class="ios-nav-section">
            <div class="ios-section-title sidebar-hide-on-collapsed">Main Menu</div>
            <a href="{{ route('legalizations.dashboard') }}" class="ios-nav-item {{ request()->routeIs('legalizations.dashboard') ? 'active' : '' }}" title="Dashboard">
                <span class="ios-item-label">
                    <i data-lucide="layout-grid" class="ios-menu-icon"></i>
                    <span class="truncate sidebar-hide-on-collapsed">Dashboard</span>
                </span>
            </a>
        </div>

        <div class="ios-nav-section pt-2">
            <div class="ios-section-title sidebar-hide-on-collapsed">Transaksi</div>
            @if (auth()->user()->isAdmin() || auth()->user()->isOperator())
                <a href="{{ route('legalizations.create') }}" class="ios-nav-item {{ request()->routeIs('legalizations.create') ? 'active' : '' }}" title="Buat Legalisir">
                    <span class="ios-item-label">
                        <i data-lucide="plus-circle" class="ios-menu-icon"></i>
                        <span class="truncate sidebar-hide-on-collapsed">Buat Legalisir</span>
                    </span>
                </a>
            @endif
            <a href="{{ route('legalizations.index') }}" class="ios-nav-item {{ request()->routeIs('legalizations.index') ? 'active' : '' }}" title="Daftar Legalisir">
                <span class="ios-item-label">
                    <i data-lucide="files" class="ios-menu-icon"></i>
                    <span class="truncate sidebar-hide-on-collapsed">Daftar Legalisir</span>
                </span>
                <i data-lucide="chevron-right" class="ios-right-chevron sidebar-hide-on-collapsed"></i>
            </a>
        </div>

        @if (auth()->user()->isAdmin() || auth()->user()->isOperator())
            <div class="ios-nav-section pt-2">
                <div class="ios-section-title sidebar-hide-on-collapsed">Master Data</div>
                <a href="{{ route('master.education-levels.index') }}" class="ios-nav-item {{ request()->routeIs('master.education-levels.*') ? 'active' : '' }}" title="Jenjang Pendidikan">
                    <span class="ios-item-label">
                        <i data-lucide="graduation-cap" class="ios-menu-icon"></i>
                        <span class="truncate sidebar-hide-on-collapsed">Jenjang Pendidikan</span>
                    </span>
                    <i data-lucide="chevron-right" class="ios-right-chevron sidebar-hide-on-collapsed"></i>
                </a>
            </div>
        @endif

    @elseif(request()->routeIs('room-reservations.*', 'master.rooms.*', 'master.room-groups.*'))
        {{-- ========================================== --}}
        {{-- NAVIGASI MODUL PEMINJAMAN RUANG --}}
        {{-- ========================================== --}}
        @if(auth()->user()->can_access_room_reservations || auth()->user()->isAdmin())
            <div class="ios-nav-section">
                <div class="ios-section-title sidebar-hide-on-collapsed">Main Menu</div>
                <a href="{{ route('room-reservations.dashboard') }}" class="ios-nav-item {{ request()->routeIs('room-reservations.dashboard') ? 'active' : '' }}" title="Dashboard">
                    <span class="ios-item-label">
                        <i data-lucide="layout-grid" class="ios-menu-icon"></i>
                        <span class="truncate sidebar-hide-on-collapsed">Dashboard</span>
                    </span>
                </a>
            </div>

            <div class="ios-nav-section pt-2">
                <div class="ios-section-title sidebar-hide-on-collapsed">Transaksi</div>
                <a href="{{ route('room-reservations.create') }}" class="ios-nav-item {{ request()->routeIs('room-reservations.create') ? 'active' : '' }}" title="Buat Peminjaman">
                    <span class="ios-item-label">
                        <i data-lucide="plus-circle" class="ios-menu-icon"></i>
                        <span class="truncate sidebar-hide-on-collapsed">Buat Peminjaman</span>
                    </span>
                </a>
                <a href="{{ route('room-reservations.index') }}" class="ios-nav-item {{ request()->routeIs('room-reservations.index') ? 'active' : '' }}" title="Daftar Peminjaman">
                    <span class="ios-item-label">
                        <i data-lucide="list" class="ios-menu-icon"></i>
                        <span class="truncate sidebar-hide-on-collapsed">Daftar Peminjaman</span>
                    </span>
                    <i data-lucide="chevron-right" class="ios-right-chevron sidebar-hide-on-collapsed"></i>
                </a>
                <a href="{{ route('room-reservations.public-display.timegrid') }}" class="ios-nav-item {{ request()->routeIs('room-reservations.public-display*') ? 'active' : '' }}" target="_blank" title="Kalender Jadwal">
                    <span class="ios-item-label">
                        <i data-lucide="calendar" class="ios-menu-icon"></i>
                        <span class="truncate sidebar-hide-on-collapsed">Kalender Jadwal</span>
                    </span>
                    <i data-lucide="external-link" class="ios-right-chevron sidebar-hide-on-collapsed"></i>
                </a>
            </div>

            @if (auth()->user()->isAdmin())
                <div class="ios-nav-section pt-2">
                    <div class="ios-section-title sidebar-hide-on-collapsed">Master Data</div>
                    <a href="{{ route('master.rooms.index') }}" class="ios-nav-item {{ request()->routeIs('master.rooms.*') ? 'active' : '' }}" title="Master Ruangan">
                        <span class="ios-item-label">
                            <i data-lucide="box" class="ios-menu-icon"></i>
                            <span class="truncate sidebar-hide-on-collapsed">Master Ruangan</span>
                        </span>
                        <i data-lucide="chevron-right" class="ios-right-chevron sidebar-hide-on-collapsed"></i>
                    </a>
                    <a href="{{ route('master.room-groups.index') }}" class="ios-nav-item {{ request()->routeIs('master.room-groups.*') ? 'active' : '' }}" title="Master Grup Ruangan">
                        <span class="ios-item-label">
                            <i data-lucide="folder" class="ios-menu-icon"></i>
                            <span class="truncate sidebar-hide-on-collapsed">Master Grup Ruangan</span>
                        </span>
                        <i data-lucide="chevron-right" class="ios-right-chevron sidebar-hide-on-collapsed"></i>
                    </a>
                </div>
            @endif
        @endif

    @else
        {{-- ========================================== --}}
        {{-- NAVIGASI MODUL PERSURATAN --}}
        {{-- ========================================== --}}
        <div class="ios-nav-section">
            <div class="ios-section-title sidebar-hide-on-collapsed">Main Menu</div>
            <a href="{{ route('dashboard') }}" class="ios-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" title="Dashboard">
                <span class="ios-item-label">
                    <i data-lucide="layout-grid" class="ios-menu-icon"></i>
                    <span class="truncate sidebar-hide-on-collapsed">Dashboard</span>
                </span>
            </a>
        </div>

        <div class="ios-nav-section pt-2">
            <div class="ios-section-title sidebar-hide-on-collapsed">Surat</div>
            @if (auth()->user()->isAdmin() || auth()->user()->isOperator())
                <a href="{{ route('letters.create') }}" class="ios-nav-item {{ request()->routeIs('letters.create') ? 'active' : '' }}" title="Buat Surat Baru">
                    <span class="ios-item-label">
                        <i data-lucide="plus-circle" class="ios-menu-icon"></i>
                        <span class="truncate sidebar-hide-on-collapsed">Buat Surat Baru</span>
                    </span>
                </a>
            @endif
            <a href="{{ route('letters.index') }}" class="ios-nav-item {{ request()->routeIs('letters.index') ? 'active' : '' }}" title="Daftar Surat">
                <span class="ios-item-label">
                    <i data-lucide="mail" class="ios-menu-icon"></i>
                    <span class="truncate sidebar-hide-on-collapsed">Daftar Surat</span>
                </span>
                <i data-lucide="chevron-right" class="ios-right-chevron sidebar-hide-on-collapsed"></i>
            </a>
        </div>

        @if (auth()->user()->isAdmin() || auth()->user()->isOperator())
            <div class="ios-nav-section pt-2">
                <div class="ios-section-title sidebar-hide-on-collapsed">Master Data</div>
                <a href="{{ route('master.classification-letters.index') }}" class="ios-nav-item {{ request()->routeIs('master.classification-letters.*') ? 'active' : '' }}" title="Klasifikasi Surat">
                    <span class="ios-item-label">
                        <i data-lucide="tags" class="ios-menu-icon"></i>
                        <span class="truncate sidebar-hide-on-collapsed">Klasifikasi Surat</span>
                    </span>
                    <i data-lucide="chevron-right" class="ios-right-chevron sidebar-hide-on-collapsed"></i>
                </a>
                <a href="{{ route('master.letter-types.index') }}" class="ios-nav-item {{ request()->routeIs('master.letter-types.*') ? 'active' : '' }}" title="Jenis Surat">
                    <span class="ios-item-label">
                        <i data-lucide="file-text" class="ios-menu-icon"></i>
                        <span class="truncate sidebar-hide-on-collapsed">Jenis Surat</span>
                    </span>
                    <i data-lucide="chevron-right" class="ios-right-chevron sidebar-hide-on-collapsed"></i>
                </a>
                <a href="{{ route('master.letter-purposes.index') }}" class="ios-nav-item {{ request()->routeIs('master.letter-purposes.*') ? 'active' : '' }}" title="Keperluan Surat">
                    <span class="ios-item-label">
                        <i data-lucide="bookmark" class="ios-menu-icon"></i>
                        <span class="truncate sidebar-hide-on-collapsed">Keperluan Surat</span>
                    </span>
                    <i data-lucide="chevron-right" class="ios-right-chevron sidebar-hide-on-collapsed"></i>
                </a>
                <a href="{{ route('master.signatories.index') }}" class="ios-nav-item {{ request()->routeIs('master.signatories.*') ? 'active' : '' }}" title="Penandatangan">
                    <span class="ios-item-label">
                        <i data-lucide="user-check" class="ios-menu-icon"></i>
                        <span class="truncate sidebar-hide-on-collapsed">Penandatangan</span>
                    </span>
                    <i data-lucide="chevron-right" class="ios-right-chevron sidebar-hide-on-collapsed"></i>
                </a>
            </div>
        @endif
    @endif

    {{-- ── Pinned Modules Section (Matches Reference Image "Pinned Project") ── --}}
    <div class="sidebar-hide-on-collapsed" style="border-top: 1px solid #f1f5f9; margin-top: 14px; padding-top: 10px;">
        <div class="flex items-center justify-between px-2 pb-1.5">
            <span class="ios-section-title" style="padding: 0;">Akses Cepat</span>
            <a href="{{ route('switcher') }}" class="text-gray-400 hover:text-orange-600 transition-colors p-0.5 rounded" title="Portal Pilihan Modul">
                <i data-lucide="plus" style="width: 14px; height: 14px;"></i>
            </a>
        </div>
        <div class="space-y-0.5">
            {{-- Modul Persuratan --}}
            @if(!request()->routeIs('dashboard', 'letters.*', 'master.classification-letters.*', 'master.letter-types.*', 'master.letter-purposes.*', 'master.signatories.*'))
                <a href="{{ route('dashboard') }}" class="ios-nav-item group" title="Buka Modul Persuratan">
                    <span class="ios-item-label">
                        <span class="ios-pinned-badge" style="background-color: #ffedd5; color: #c2410c; border: 1px solid #fed7aa;">S</span>
                        <span class="truncate font-medium" style="font-size: 13px;">Modul Persuratan</span>
                    </span>
                    <i data-lucide="pin" class="ios-pin-icon sidebar-hide-on-collapsed"></i>
                </a>
            @endif

            {{-- Modul Legalisir --}}
            @if(!request()->routeIs('legalizations.*', 'master.education-levels.*') && (auth()->user()->can_access_legalizations || auth()->user()->isAdmin()))
                <a href="{{ route('legalizations.dashboard') }}" class="ios-nav-item group" title="Buka Modul Legalisir">
                    <span class="ios-item-label">
                        <span class="ios-pinned-badge" style="background-color: #d1fae5; color: #047857; border: 1px solid #a7f3d0;">L</span>
                        <span class="truncate font-medium" style="font-size: 13px;">Layanan Legalisir</span>
                    </span>
                    <i data-lucide="pin" class="ios-pin-icon sidebar-hide-on-collapsed"></i>
                </a>
            @endif

            {{-- Modul Peminjaman Ruang --}}
            @if(!request()->routeIs('room-reservations.*', 'master.rooms.*', 'master.room-groups.*') && (auth()->user()->can_access_room_reservations || auth()->user()->isAdmin()))
                <a href="{{ route('room-reservations.dashboard') }}" class="ios-nav-item group" title="Buka Peminjaman Ruang">
                    <span class="ios-item-label">
                        <span class="ios-pinned-badge" style="background-color: #dbeafe; color: #1d4ed8; border: 1px solid #bfdbfe;">R</span>
                        <span class="truncate font-medium" style="font-size: 13px;">Peminjaman Ruang</span>
                    </span>
                    <i data-lucide="pin" class="ios-pin-icon sidebar-hide-on-collapsed"></i>
                </a>
            @endif

            {{-- Modul Pengaturan Sistem --}}
            @if(!request()->routeIs('admin.*', 'master.users.*') && auth()->user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="ios-nav-item group" title="Buka Pengaturan Sistem">
                    <span class="ios-item-label">
                        <span class="ios-pinned-badge" style="background-color: #ede9fe; color: #6d28d9; border: 1px solid #ddd6fe;">P</span>
                        <span class="truncate font-medium" style="font-size: 13px;">Pengaturan Sistem</span>
                    </span>
                    <i data-lucide="pin" class="ios-pin-icon sidebar-hide-on-collapsed"></i>
                </a>
            @endif
        </div>
    </div>

</nav>

{{-- ── User Profile Capsule at Bottom (iOS Profile Style) ── --}}
<div class="p-2.5 flex-shrink-0" style="border-top: 1px solid #f1f5f9;">
    <div class="relative">
        <button type="button" onclick="{{ $isMobile ? 'toggleMobileUserMenu()' : 'toggleUserMenu()' }}"
            class="ios-user-capsule group">
            <div class="flex items-center space-x-2.5 min-w-0">
                <div class="w-8 h-8 rounded-full text-white flex items-center justify-center font-bold text-xs shadow-xs flex-shrink-0 relative ring-2 ring-orange-500/20"
                    style="background: linear-gradient(135deg, #FF4D00, #F59E0B); font-size: 11px;">
                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 rounded-full bg-emerald-500 ring-2 ring-white" title="Online"></span>
                </div>
                <div class="min-w-0 flex-1 sidebar-hide-on-collapsed">
                    <p class="font-semibold text-gray-900 truncate leading-tight" style="font-size: 13px;">{{ Auth::user()->name }}</p>
                    <p class="text-gray-500 capitalize truncate mt-0.5" style="font-size: 11px;">{{ Auth::user()->role }}</p>
                </div>
            </div>
            <i data-lucide="chevrons-up-down" class="sidebar-hide-on-collapsed" style="width: 15px; height: 15px; color: #94a3b8; flex-shrink: 0;" id="{{ $isMobile ? 'mobile-user-menu-icon' : 'user-menu-icon' }}"></i>
        </button>

        {{-- Dropdown Menu (iOS Card style) --}}
        <div id="{{ $isMobile ? 'mobile-user-menu-dropdown' : 'user-menu-dropdown' }}"
            class="hidden absolute bottom-full left-0 right-0 mb-2 rounded-2xl border border-gray-200 bg-white shadow-xl z-50 p-1.5 space-y-0.5">
            <div class="px-3 py-2 border-b border-gray-100">
                <p class="font-bold text-gray-400 uppercase tracking-wider" style="font-size: 10px;">Akun Terhubung</p>
                <p class="text-xs font-semibold text-gray-900 truncate mt-0.5">{{ Auth::user()->email }}</p>
            </div>

            {{-- Edit Profile --}}
            <a href="{{ route('profile.edit') }}"
                class="flex items-center rounded-xl px-2.5 py-2 text-xs font-medium text-gray-700 hover:bg-gray-100 transition-colors">
                <i data-lucide="user" style="width: 15px; height: 15px; margin-right: 8px; color: #64748b;"></i>
                Edit Profile
            </a>

            @if (auth()->user()->hasMultipleModules())
                <div class="border-t border-gray-100 my-1"></div>
                <a href="{{ route('switcher') }}"
                    class="flex items-center rounded-xl px-2.5 py-2 text-xs font-medium text-orange-600 hover:bg-orange-50 transition-colors">
                    <i data-lucide="arrow-left-right" style="width: 15px; height: 15px; margin-right: 8px; color: #ea580c;"></i>
                    Ganti Modul
                </a>
            @endif

            <div class="border-t border-gray-100 my-1"></div>

            {{-- Logout --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full flex items-center rounded-xl px-2.5 py-2 text-xs font-semibold text-red-600 hover:bg-red-50 transition-colors">
                    <i data-lucide="log-out" style="width: 15px; height: 15px; margin-right: 8px; color: #ef4444;"></i>
                    Keluar
                </button>
            </form>
        </div>
    </div>
</div>
