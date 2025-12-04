@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    {{-- Welcome Section --}}
    <div class="mb-8">
        <div class="gradient-brand rounded-lg p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold mb-2 flex items-center">
                        <i data-lucide="layout-dashboard" class="mr-3 h-7 w-7"></i>
                        Dashboard
                    </h1>
                    <p class="text-orange-50">
                        Selamat datang, <span class="font-semibold">{{ Auth::user()->name }}</span>!
                        <span
                            class="ml-2 inline-flex items-center rounded-full bg-white px-3 py-1 text-xs font-medium text-brand-dark">
                            {{ ucfirst(Auth::user()->role) }}
                        </span>
                    </p>
                </div>
                <div class="hidden sm:block text-right">
                    <p class="text-sm text-orange-100">{{ now()->format('l, d F Y') }}</p>
                    <p class="flex items-center justify-end text-orange-50">
                        <i data-lucide="clock" class="mr-1 h-4 w-4"></i>
                        {{ now()->format('H:i') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid gap-6 mb-8 md:grid-cols-2 xl:grid-cols-4">
        {{-- Total Letters --}}
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-shadow duration-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-brand-lighter">
                        <i data-lucide="mail" class="h-6 w-6 text-brand"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Surat</p>
                        <h3 class="text-2xl font-bold text-gray-900">{{ $totalLetters }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- This Year Letters --}}
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-shadow duration-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-success-lighter">
                        <i data-lucide="calendar" class="h-6 w-6 text-success"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Surat Tahun Ini</p>
                        <h3 class="text-2xl font-bold text-gray-900">{{ $currentYearLetters }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- This Month Letters --}}
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-shadow duration-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-cyan-100">
                        <i data-lucide="calendar-days" class="h-6 w-6 text-cyan-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Surat Bulan Ini</p>
                        <h3 class="text-2xl font-bold text-gray-900">{{ $currentMonthLetters }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- User Role --}}
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-shadow duration-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-warning-lighter">
                        <i data-lucide="user-check" class="h-6 w-6 text-warning"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Role Anda</p>
                        <h3 class="text-2xl font-bold text-gray-900 capitalize">{{ Auth::user()->role }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    @if (auth()->user()->isAdmin() || auth()->user()->isOperator())
        <div class="mb-8">
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i data-lucide="zap" class="mr-2 h-5 w-5 text-brand"></i>
                        Aksi Cepat
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        @if (auth()->user()->isAdmin())
                        <a href="{{ route('master.classification-letters.index') }}"
                            class="flex flex-col items-center justify-center rounded-lg border-2 border-gray-200 p-6 text-center transition-all hover:border-brand hover:bg-orange-50">
                            <i data-lucide="tags" class="mb-3 h-8 w-8 text-brand"></i>
                            <span class="font-medium text-gray-900">Klasifikasi</span>
                        </a>
                        <a href="{{ route('master.letter-types.index') }}"
                            class="flex flex-col items-center justify-center rounded-lg border-2 border-gray-200 p-6 text-center transition-all hover:border-success hover:bg-success-lighter">
                            <i data-lucide="file-text" class="mb-3 h-8 w-8 text-success"></i>
                            <span class="font-medium text-gray-900">Jenis Surat</span>
                        </a>
                        <a href="{{ route('master.signatories.index') }}"
                            class="flex flex-col items-center justify-center rounded-lg border-2 border-gray-200 p-6 text-center transition-all hover:border-warning hover:bg-warning-lighter">
                            <i data-lucide="user-check" class="mb-3 h-8 w-8 text-warning"></i>
                            <span class="font-medium text-gray-900">Penandatangan</span>
                        </a>
                    @endif
                    <a href="{{ route('letters.create') }}"
                        class="flex flex-col items-center justify-center rounded-lg border-2 border-brand bg-brand p-6 text-center transition-all hover:bg-brand-600">
                        <i data-lucide="plus-circle" class="mb-3 h-8 w-8 text-white"></i>
                        <span class="font-medium text-white">Buat Surat</span>
                    </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Monitoring Surat per Jenis --}}
    <div class="mb-8">
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
            <div class="border-b border-gray-200 px-6 py-4">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i data-lucide="pie-chart" class="mr-2 h-5 w-5 text-brand"></i>
                    Monitoring Surat per Jenis (Tahun {{ now()->year }})
                </h3>
            </div>
            <div class="p-6">
                @if ($lettersByTypeThisYear->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Surat</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Persentase</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Progress</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach ($lettersByTypeThisYear as $stat)
                                    @php
                                        $percentage = $totalLettersThisYear > 0 ? round(($stat->count / $totalLettersThisYear) * 100, 1) : 0;
                                    @endphp
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-3 py-4 text-sm font-medium text-gray-900">{{ $stat->letterType->name }}</td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <span class="inline-flex items-center rounded-full bg-brand-lighter px-2.5 py-0.5 text-xs font-medium text-brand-darker">
                                                {{ $stat->count }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">{{ $percentage }}%</td>
                                        <td class="px-3 py-4 hidden md:table-cell">
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="bg-brand h-2 rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                <tr class="bg-gray-50 font-semibold">
                                    <td class="px-3 py-4 text-sm text-gray-900">Total</td>
                                    <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <span class="inline-flex items-center rounded-full bg-brand px-2.5 py-0.5 text-xs font-medium text-white">
                                            {{ $totalLettersThisYear }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-900">100%</td>
                                    <td class="px-3 py-4 hidden md:table-cell">
                                        <div class="w-full bg-brand rounded-full h-2"></div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-12">
                        <i data-lucide="pie-chart" class="mx-auto h-12 w-12 text-gray-400"></i>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada data monitoring</h3>
                        <p class="mt-1 text-sm text-gray-500">Belum ada surat yang dibuat di tahun {{ now()->year }}.</p>
                            @if (auth()->user()->isAdmin() || auth()->user()->isOperator())
                                <div class="mt-6">
                                    <a href="{{ route('letters.create') }}"
                                        class="inline-flex items-center rounded-md bg-brand px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-brand-600">
                                        <i data-lucide="plus-circle" class="mr-2 h-4 w-4"></i>
                                        Buat Surat
                                    </a>
                                </div>
                            @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="grid gap-8 lg:grid-cols-3">
        {{-- Recent Letters --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="border-b border-gray-200 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i data-lucide="clock" class="mr-2 h-5 w-5 text-brand"></i>
                            Surat Terbaru
                        </h3>
                        <a href="{{ route('letters.index') }}"
                            class="inline-flex items-center rounded-md bg-brand px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-brand-600 focus:outline-none focus:ring-2 focus:ring-brand focus:ring-offset-2">
                            Lihat Semua
                            <i data-lucide="arrow-right" class="ml-2 h-4 w-4"></i>
                        </a>
                    </div>
                </div>
                <div class="p-6">
                    @if ($recentLetters->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th
                                            class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Nomor Surat</th>
                                        <th
                                            class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Perihal</th>
                                        <th
                                            class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">
                                            Penandatangan</th>
                                        <th
                                            class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Tanggal</th>
                                        <th
                                            class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach ($recentLetters as $letter)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-3 py-4 whitespace-nowrap">
                                                <a href="{{ route('letters.show', $letter) }}"
                                                    class="font-medium text-brand hover:text-brand-darker">
                                                    {{ $letter->letter_number }}
                                                </a>
                                            </td>
                                            <td class="px-3 py-4 text-sm text-gray-900">
                                                {{ Str::limit($letter->subject, 50) }}</td>
                                            <td class="px-3 py-4 text-sm text-gray-500 hidden lg:table-cell">
                                                {{ $letter->signatory->name }}</td>
                                            <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $letter->letter_date->format('d/m/Y') }}</td>
                                            <td class="px-3 py-4 whitespace-nowrap">
                                                <span
                                                    class="inline-flex rounded-full px-2 py-1 text-xs font-semibold
                                                    @if ($letter->status == 'final') bg-success-lighter text-green-800
                                                    @elseif($letter->status == 'draft') bg-yellow-100 text-yellow-800
                                                    @else bg-red-100 text-red-800 @endif">
                                                    {{ ucfirst($letter->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <i data-lucide="inbox" class="mx-auto h-12 w-12 text-gray-400"></i>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada data surat</h3>
                            <p class="mt-1 text-sm text-gray-500">Belum ada surat yang dibuat dalam sistem.</p>
                            @if (auth()->user()->isAdmin() || auth()->user()->isOperator())
                                <div class="mt-6">
                                    <a href="{{ route('letters.create') }}"
                                        class="inline-flex items-center rounded-md bg-brand px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-brand-600">
                                        <i data-lucide="plus-circle" class="mr-2 h-4 w-4"></i>
                                        Buat Surat Pertama
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Statistics by Year --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i data-lucide="bar-chart-3" class="mr-2 h-5 w-5 text-brand"></i>
                        Statistik per Tahun
                    </h3>
                </div>
                <div class="p-6">
                    @if ($lettersByYear->count() > 0)
                        <div class="space-y-4">
                            @foreach ($lettersByYear as $stat)
                                <div class="flex items-center justify-between rounded-lg bg-gray-50 p-4">
                                    <span class="font-medium text-gray-900">{{ $stat->year }}</span>
                                    <span
                                        class="inline-flex items-center rounded-full bg-brand-lighter px-3 py-1 text-sm font-medium text-brand-darker">
                                        {{ $stat->count }} surat
                                    </span>
                                </div>
                            @endforeach

                            {{-- Progress Bar --}}
                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <div class="flex justify-between text-sm mb-2">
                                    <span class="text-gray-500">Total Surat: {{ $lettersByYear->sum('count') }}</span>
                                    <span class="font-medium text-brand">{{ $currentYearLetters }} tahun ini</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-3">
                                    <div class="bg-brand h-3 rounded-full transition-all duration-500"
                                        style="width: {{ min(100, ($currentYearLetters / max(1, $lettersByYear->sum('count'))) * 100) }}%">
                                    </div>
                                </div>
                                <p class="text-center text-xs text-gray-500 mt-2">
                                    {{ round(($currentYearLetters / max(1, $lettersByYear->sum('count'))) * 100, 1) }}%
                                    dari total
                                </p>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i data-lucide="bar-chart-3" class="mx-auto h-12 w-12 text-gray-400"></i>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada data statistik</h3>
                            <p class="mt-1 text-sm text-gray-500">Belum ada surat yang dibuat dalam sistem.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Re-initialize Lucide icons
            lucide.createIcons();
        </script>
    @endpush
@endsection





