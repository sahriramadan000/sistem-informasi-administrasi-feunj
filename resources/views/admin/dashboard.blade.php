@extends('layouts.app')

@section('title', 'Dashboard Pengaturan Sistem')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
    {{-- Welcome Section --}}
    <div class="mb-8">
        <div class="gradient-brand rounded-lg p-6 text-white shadow-lg relative overflow-hidden">
            <!-- Decorative background elements for enhanced UI -->
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl"></div>
            <div class="absolute bottom-0 right-24 w-24 h-24 bg-white opacity-10 rounded-full blur-xl"></div>
            
            <div class="relative flex flex-col md:flex-row md:items-center justify-between z-10">
                <div>
                    <h1 class="text-2xl font-bold mb-2 flex items-center">
                        <i data-lucide="settings" class="mr-3 h-7 w-7"></i>
                        Dashboard Pengaturan Sistem
                    </h1>
                    <p class="text-orange-50 max-w-2xl">
                        Selamat datang, <span class="font-semibold">{{ auth()->user()->name }}</span> 👋. Kelola pengguna dan pantau log aktivitas serta error sistem di sini.
                    </p>
                </div>
                <div class="mt-4 md:mt-0 text-left md:text-right">
                    <p class="text-sm font-medium text-orange-100">{{ now()->translatedFormat('l, d F Y') }}</p>
                    <p class="flex items-center md:justify-end text-orange-50 mt-1">
                        <i data-lucide="clock" class="mr-1 h-4 w-4"></i>
                        {{ now()->format('H:i') }} WIB
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <!-- Users Card -->
        <div class="group bg-white rounded-lg border border-gray-200 p-6 shadow-sm hover:shadow-md hover:border-brand-300 transition-all duration-300 relative overflow-hidden flex flex-col justify-between">
            <div class="absolute right-0 top-0 w-24 h-24 bg-brand-50 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110 opacity-50"></div>
            <div class="relative z-10 flex items-center justify-between mb-4">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Total Pengguna</p>
                    <h3 class="text-3xl font-bold text-gray-900">{{ $totalUsers ?? 0 }}</h3>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-brand-lighter group-hover:bg-brand transition-colors duration-300 shadow-sm">
                    <i data-lucide="users" class="h-6 w-6 text-brand group-hover:text-white transition-colors"></i>
                </div>
            </div>
            <div class="relative z-10 mt-auto pt-4 border-t border-gray-100">
                <a href="{{ route('master.users.index') }}" class="text-sm text-brand hover:text-brand-700 font-medium flex items-center group-hover:translate-x-1 transition-transform">
                    Kelola Pengguna
                    <i data-lucide="arrow-right" class="w-4 h-4 ml-1 transition-transform"></i>
                </a>
            </div>
        </div>

        <!-- Activity Logs Card -->
        <div class="group bg-white rounded-lg border border-gray-200 p-6 shadow-sm hover:shadow-md hover:border-success-300 transition-all duration-300 relative overflow-hidden flex flex-col justify-between">
            <div class="absolute right-0 top-0 w-24 h-24 bg-success-50 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110 opacity-50"></div>
            <div class="relative z-10 flex items-center justify-between mb-4">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Activity Logs</p>
                    <h3 class="text-3xl font-bold text-gray-900">{{ $totalActivityLogs ?? 0 }}</h3>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-success-lighter group-hover:bg-success transition-colors duration-300 shadow-sm">
                    <i data-lucide="activity" class="h-6 w-6 text-success group-hover:text-white transition-colors"></i>
                </div>
            </div>
            <div class="relative z-10 mt-auto pt-4 border-t border-gray-100">
                <a href="{{ route('admin.activity-logs.index') }}" class="text-sm text-success hover:text-success-700 font-medium flex items-center group-hover:translate-x-1 transition-transform">
                    Lihat Activity Logs
                    <i data-lucide="arrow-right" class="w-4 h-4 ml-1 transition-transform"></i>
                </a>
            </div>
        </div>
        
        <!-- Error Logs Card -->
        <div class="group bg-white rounded-lg border border-gray-200 p-6 shadow-sm hover:shadow-md hover:border-red-300 transition-all duration-300 relative overflow-hidden flex flex-col justify-between">
            <div class="absolute right-0 top-0 w-24 h-24 bg-red-50 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110 opacity-50"></div>
            <div class="relative z-10 flex items-center justify-between mb-4">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Error Logs</p>
                    <h3 class="text-3xl font-bold text-gray-900">{{ $totalErrorLogs ?? 0 }}</h3>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-red-100 group-hover:bg-red-600 transition-colors duration-300 shadow-sm">
                    <i data-lucide="alert-triangle" class="h-6 w-6 text-red-600 group-hover:text-white transition-colors"></i>
                </div>
            </div>
            <div class="relative z-10 mt-auto pt-4 border-t border-gray-100">
                <a href="{{ route('admin.error-logs.index') }}" class="text-sm text-red-600 hover:text-red-700 font-medium flex items-center group-hover:translate-x-1 transition-transform">
                    Lihat Error Logs
                    <i data-lucide="arrow-right" class="w-4 h-4 ml-1 transition-transform"></i>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
