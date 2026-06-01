@extends('layouts.app')

@section('title', 'Dashboard Legalisir')

@section('content')
    {{-- Welcome Section --}}
    <div class="mb-8">
        <div class="gradient-brand rounded-lg p-6 text-white shadow-lg relative overflow-hidden">
            <!-- Decorative background elements for enhanced UI -->
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl"></div>
            <div class="absolute bottom-0 right-24 w-24 h-24 bg-white opacity-10 rounded-full blur-xl"></div>

            <div class="relative flex flex-col md:flex-row md:items-center justify-between z-10">
                <div>
                    <h1 class="text-2xl font-bold mb-2 flex items-center">
                        <i data-lucide="layout-dashboard" class="mr-3 h-7 w-7"></i>
                        Dashboard Legalisir
                    </h1>
                    <p class="text-orange-50 max-w-2xl">
                        Pantau ringkasan aktivitas, statistik, dan transaksi legalisir dokumen alumni secara real-time.
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

    {{-- Stats Grid --}}
    <div class="grid gap-6 mb-8 md:grid-cols-2 xl:grid-cols-4">
        {{-- Total Hari Ini --}}
        <div
            class="group bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md hover:border-brand-300 transition-all duration-300 relative overflow-hidden">
            <div
                class="absolute right-0 top-0 w-24 h-24 bg-brand-50 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110 opacity-50">
            </div>
            <div class="p-6 relative">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">Legalisir Hari Ini</p>
                        <h3 class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_today'], 0, ',', '.') }}
                        </h3>
                    </div>
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-lg bg-brand-lighter group-hover:bg-brand transition-colors duration-300 shadow-sm">
                        <i data-lucide="file-check-2"
                            class="h-6 w-6 text-brand group-hover:text-white transition-colors"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Bulan Ini --}}
        <div
            class="group bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md hover:border-success-300 transition-all duration-300 relative overflow-hidden">
            <div
                class="absolute right-0 top-0 w-24 h-24 bg-success-50 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110 opacity-50">
            </div>
            <div class="p-6 relative">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">Legalisir Bulan Ini</p>
                        <h3 class="text-2xl font-bold text-gray-900">
                            {{ number_format($stats['total_this_month'], 0, ',', '.') }}</h3>
                    </div>
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-lg bg-success-lighter group-hover:bg-success transition-colors duration-300 shadow-sm">
                        <i data-lucide="files" class="h-6 w-6 text-success group-hover:text-white transition-colors"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pendapatan Hari Ini --}}
        <div
            class="group bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md hover:border-warning-300 transition-all duration-300 relative overflow-hidden">
            <div
                class="absolute right-0 top-0 w-24 h-24 bg-warning-50 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110 opacity-50">
            </div>
            <div class="p-6 relative">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">Pendapatan Hari Ini</p>
                        <div class="flex items-baseline space-x-1">
                            <span class="text-sm font-semibold text-gray-400">Rp</span>
                            <h3 class="text-2xl font-bold text-gray-900">
                                {{ number_format($stats['revenue_today'], 0, ',', '.') }}</h3>
                        </div>
                    </div>
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-lg bg-warning-lighter group-hover:bg-warning transition-colors duration-300 shadow-sm">
                        <i data-lucide="banknote" class="h-6 w-6 text-warning group-hover:text-white transition-colors"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pendapatan Bulan Ini --}}
        <div
            class="group bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md hover:border-cyan-300 transition-all duration-300 relative overflow-hidden">
            <div
                class="absolute right-0 top-0 w-24 h-24 bg-cyan-50 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-110 opacity-50">
            </div>
            <div class="p-6 relative">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">Pendapatan Bulan Ini</p>
                        <div class="flex items-baseline space-x-1">
                            <span class="text-sm font-semibold text-gray-400">Rp</span>
                            <h3 class="text-2xl font-bold text-gray-900">
                                {{ number_format($stats['revenue_this_month'], 0, ',', '.') }}</h3>
                        </div>
                    </div>
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-lg bg-cyan-100 group-hover:bg-cyan-600 transition-colors duration-300 shadow-sm">
                        <i data-lucide="wallet" class="h-6 w-6 text-cyan-600 group-hover:text-white transition-colors"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Activity --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="border-b border-gray-200 px-6 py-4 flex justify-between items-center bg-white">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <i data-lucide="activity" class="mr-2 h-5 w-5 text-brand"></i>
                Aktivitas Legalisir Terbaru
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No.
                            Legalisir</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama
                            Alumni</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenjang
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nominal
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($recentLegalizations as $legalization)
                        <tr class="hover:bg-gray-50 transition-colors duration-200 group">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <code
                                    class="px-2 py-1 text-sm font-mono bg-blue-50 text-blue-700 rounded border border-blue-200">
                                    {{ str_pad($legalization->running_number, 2, '0', STR_PAD_LEFT) }}
                                </code>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center text-sm text-gray-500">
                                    {{ $legalization->date->format('d/m/Y') }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $legalization->alumni_name }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-brand-lighter text-brand-darker">
                                    {{ $legalization->educationLevel->name ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-700">
                                    Rp {{ number_format($legalization->total_price, 0, ',', '.') }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="bg-gray-50 rounded-full p-4 mb-4">
                                        <i data-lucide="inbox" class="w-8 h-8 text-gray-400"></i>
                                    </div>
                                    <p class="text-base font-medium text-gray-900">Belum ada aktivitas legalisir</p>
                                    <p class="text-sm text-gray-500 mt-1">Data transaksi legalisir terbaru akan muncul di
                                        sini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
