@extends('layouts.app')

@section('title', 'Error Logs')

@section('content')
<div class="space-y-6">

    {{-- ============================================================ --}}
    {{-- Page Header --}}
    {{-- ============================================================ --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                <i data-lucide="alert-triangle" class="mr-3 h-7 w-7 text-brand"></i>
                Error Logs
            </h2>
            <p class="mt-1 text-sm text-gray-500">Monitor dan kelola error sistem (14 hari terakhir)</p>
        </div>
        <a href="{{ route('admin.error-logs.statistics') }}"
            class="inline-flex items-center rounded-lg btn-primary focus:ring-offset-2 transition-colors">
            <i data-lucide="bar-chart-3" class="mr-2 h-4 w-4"></i>
            Lihat Statistik
        </a>
    </div>

    {{-- ============================================================ --}}
    {{-- Statistics Cards --}}
    {{-- ============================================================ --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">

        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 border-l-4 border-l-blue-500">
            <div class="flex items-center gap-2 mb-1">
                <i data-lucide="activity" class="h-4 w-4 text-blue-400"></i>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Error</p>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['total'] }}</p>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 border-l-4 border-l-red-500">
            <div class="flex items-center gap-2 mb-1">
                <i data-lucide="alert-octagon" class="h-4 w-4 text-red-400"></i>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Critical</p>
            </div>
            <p class="text-3xl font-bold text-red-600">{{ $stats['critical'] }}</p>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 border-l-4 border-l-yellow-500">
            <div class="flex items-center gap-2 mb-1">
                <i data-lucide="alert-triangle" class="h-4 w-4 text-yellow-400"></i>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Warnings</p>
            </div>
            <p class="text-3xl font-bold text-yellow-600">{{ $stats['warning'] }}</p>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 border-l-4 border-l-purple-500">
            <div class="flex items-center gap-2 mb-1">
                <i data-lucide="code" class="h-4 w-4 text-purple-400"></i>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Tipe Exception</p>
            </div>
            <p class="text-3xl font-bold text-purple-600">{{ $stats['unique_types'] }}</p>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 border-l-4 border-l-green-500">
            <div class="flex items-center gap-2 mb-1">
                <i data-lucide="users" class="h-4 w-4 text-green-400"></i>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">User Terdampak</p>
            </div>
            <p class="text-3xl font-bold text-green-600">{{ $stats['unique_users'] }}</p>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 border-l-4 border-l-indigo-500">
            <div class="flex items-center gap-2 mb-1">
                <i data-lucide="trending-up" class="h-4 w-4 text-indigo-400"></i>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Rata-rata/Hari</p>
            </div>
            <p class="text-3xl font-bold text-indigo-600">{{ $stats['avg_per_day'] }}</p>
        </div>

    </div>

    {{-- ============================================================ --}}
    {{-- Filter + Table dalam satu card (pola Daftar Surat) --}}
    {{-- ============================================================ --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">

        {{-- Filter Section — Collapsible --}}
        <div class="border-b border-gray-200">
            <button type="button" onclick="toggleFilter()"
                class="w-full flex items-center justify-between px-6 py-4 text-left hover:bg-gray-50 transition-colors">
                <div class="flex items-center">
                    <i data-lucide="sliders-horizontal" class="mr-2 h-5 w-5 text-brand"></i>
                    <h3 class="text-base font-semibold text-gray-900">Filter Data</h3>
                    @if(request()->hasAny(['search', 'exception_type', 'date_from', 'date_to', 'context']))
                        <span class="ml-3 inline-flex items-center rounded-full bg-brand-lighter px-2.5 py-0.5 text-xs font-medium text-brand-dark">
                            <i data-lucide="filter" class="mr-1 h-3 w-3"></i>
                            Aktif
                        </span>
                    @endif
                </div>
                <i data-lucide="chevron-down" id="filter-icon"
                    class="h-5 w-5 text-gray-400 transition-transform duration-200"></i>
            </button>

            <div id="filter-content" class="hidden border-t border-gray-100">
                <div class="p-6">
                    <form method="GET" action="{{ route('admin.error-logs.index') }}">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 items-end">

                            {{-- Search --}}
                            <div class="space-y-2">
                                <label for="search" class="label">Cari Error ID / Pesan</label>
                                <input type="text" id="search" name="search" value="{{ request('search') }}"
                                    placeholder="ERR_202605... atau pesan error"
                                    class="input w-full">
                            </div>

                            {{-- Exception Type --}}
                            <div class="space-y-2">
                                <label for="exception_type" class="label">Tipe Exception</label>
                                <select id="exception_type" name="exception_type" class="select">
                                    <option value="">Semua Tipe</option>
                                    @foreach ($exceptionTypes as $type)
                                        <option value="{{ $type }}" {{ request('exception_type') === $type ? 'selected' : '' }}>
                                            {{ $type }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Context --}}
                            <div class="space-y-2">
                                <label for="context" class="label">Context</label>
                                <input type="text" id="context" name="context" value="{{ request('context') }}"
                                    placeholder="misal: LetterController.store"
                                    class="input w-full">
                            </div>

                            {{-- Date From --}}
                            <div class="space-y-2">
                                <label for="date_from" class="label">Tanggal Dari</label>
                                <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}"
                                    class="input w-full">
                            </div>

                            {{-- Date To --}}
                            <div class="space-y-2">
                                <label for="date_to" class="label">Tanggal Sampai</label>
                                <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}"
                                    class="input w-full">
                            </div>

                            {{-- Action Buttons --}}
                            <div class="flex items-center gap-3 sm:col-span-2 lg:col-span-3">
                                <button type="submit" class="btn-primary flex-1 justify-center">
                                    <i data-lucide="filter" class="mr-2 h-4 w-4"></i>
                                    Filter
                                </button>
                                <a href="{{ route('admin.error-logs.index') }}" class="btn-outline flex-1 justify-center">
                                    <i data-lucide="x" class="mr-2 h-4 w-4"></i>
                                    Reset
                                </a>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Per Page Selector --}}
        <div class="border-b border-gray-200 px-6 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <label for="per_page" class="text-sm text-gray-600">Tampilkan:</label>
                <form method="GET" action="{{ route('admin.error-logs.index') }}" class="inline-block">
                    @if (request('search'))
                        <input type="hidden" name="search"         value="{{ request('search') }}">
                    @endif
                    @if (request('exception_type'))
                        <input type="hidden" name="exception_type" value="{{ request('exception_type') }}">
                    @endif
                    @if (request('context'))
                        <input type="hidden" name="context"        value="{{ request('context') }}">
                    @endif
                    @if (request('date_from'))
                        <input type="hidden" name="date_from"      value="{{ request('date_from') }}">
                    @endif
                    @if (request('date_to'))
                        <input type="hidden" name="date_to"        value="{{ request('date_to') }}">
                    @endif

                    <select name="per_page" id="per_page"
                        class="px-3 py-1 text-sm border border-gray-300 rounded-md bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-brand focus:ring-offset-0"
                        onchange="this.form.submit()">
                        <option value="10"  {{ request('per_page', 10) == 10  ? 'selected' : '' }}>10</option>
                        <option value="25"  {{ request('per_page', 10) == 25  ? 'selected' : '' }}>25</option>
                        <option value="50"  {{ request('per_page', 10) == 50  ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page', 10) == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </form>
                <span class="text-sm text-gray-600">data per halaman</span>
            </div>
            <span class="text-sm text-gray-500">
                Total: <span class="font-semibold">{{ $stats['total'] }}</span> data
            </span>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            @if (count($errors) > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50">No</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50">Error ID</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50">Tipe</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50 hidden lg:table-cell">Context</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50">Pesan</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50 hidden xl:table-cell">Pengguna</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50 hidden lg:table-cell">Waktu</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50">Detail</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($errors as $index => $error)
                            @php
                                $isCritical = in_array($error['exception_type'], ['FatalError', 'ParseError', 'TypeError']);
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors {{ $isCritical ? 'border-l-2 border-l-red-400' : '' }}">

                                {{-- No --}}
                                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ ($currentPage - 1) * request('per_page', 10) + $index + 1 }}
                                </td>

                                {{-- Error ID --}}
                                <td class="px-3 py-4 whitespace-nowrap">
                                    <code class="text-xs bg-gray-100 px-2 py-1 rounded-md text-gray-700 font-mono">
                                        {{ $error['error_id'] }}
                                    </code>
                                </td>

                                {{-- Exception Type Badge --}}
                                <td class="px-3 py-4 whitespace-nowrap">
                                    @if ($isCritical)
                                        <span class="inline-flex items-center gap-1.5 rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset bg-red-100 text-red-800 ring-red-600/20">
                                            <i data-lucide="alert-octagon" class="h-3 w-3"></i>
                                            {{ $error['exception_type'] }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset bg-yellow-100 text-yellow-800 ring-yellow-600/20">
                                            <i data-lucide="alert-triangle" class="h-3 w-3"></i>
                                            {{ $error['exception_type'] }}
                                        </span>
                                    @endif
                                </td>

                                {{-- Context --}}
                                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500 hidden lg:table-cell">
                                    {{ $error['context'] }}
                                </td>

                                {{-- Pesan --}}
                                <td class="px-3 py-4 text-sm text-gray-600 max-w-xs">
                                    <span class="truncate block max-w-[220px]" title="{{ $error['message'] }}">
                                        {{ Str::limit($error['message'], 55) }}
                                    </span>
                                </td>

                                {{-- User --}}
                                <td class="px-3 py-4 whitespace-nowrap text-sm hidden xl:table-cell">
                                    @if ($error['user_id'])
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ $error['user_name'] }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-400 italic">
                                            Sistem
                                        </span>
                                    @endif
                                </td>

                                {{-- Waktu --}}
                                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500 hidden lg:table-cell">
                                    {{ \Carbon\Carbon::parse($error['timestamp'])->format('d/m/Y H:i') }}
                                </td>

                                {{-- Detail --}}
                                <td class="px-3 py-4 whitespace-nowrap text-sm">
                                    <a href="{{ route('admin.error-logs.show', $error['error_id']) }}"
                                        class="inline-flex items-center rounded-md bg-blue-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                                        <i data-lucide="eye" class="mr-1.5 h-3.5 w-3.5"></i>
                                        Detail
                                    </a>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Pagination --}}
                @if ($totalPages > 1)
                    <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
                        <p class="text-sm text-gray-500">
                            Halaman <span class="font-semibold text-gray-800">{{ $currentPage }}</span> dari
                            <span class="font-semibold text-gray-800">{{ $totalPages }}</span>
                        </p>
                        <nav class="flex items-center gap-1">
                            @if ($currentPage > 1)
                                <a href="{{ request()->fullUrlWithQuery(['page' => 1]) }}"
                                    class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-50 transition-colors">
                                    Pertama
                                </a>
                                <a href="{{ request()->fullUrlWithQuery(['page' => $currentPage - 1]) }}"
                                    class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-50 transition-colors">
                                    <i data-lucide="chevron-left" class="h-4 w-4"></i>
                                </a>
                            @endif
                            @if ($currentPage < $totalPages)
                                <a href="{{ request()->fullUrlWithQuery(['page' => $currentPage + 1]) }}"
                                    class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-50 transition-colors">
                                    <i data-lucide="chevron-right" class="h-4 w-4"></i>
                                </a>
                                <a href="{{ request()->fullUrlWithQuery(['page' => $totalPages]) }}"
                                    class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-50 transition-colors">
                                    Terakhir
                                </a>
                            @endif
                        </nav>
                    </div>
                @endif

            @else
                {{-- Empty State --}}
                <div class="text-center py-12">
                    <i data-lucide="inbox" class="mx-auto h-12 w-12 text-gray-400"></i>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada error ditemukan</h3>
                    <p class="mt-1 text-sm text-gray-500">Belum ada data yang sesuai dengan filter yang dipilih.</p>
                    <div class="mt-6">
                        <a href="{{ route('admin.error-logs.index') }}"
                            class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition-colors">
                            <i data-lucide="refresh-cw" class="mr-2 h-4 w-4"></i>
                            Reset Filter
                        </a>
                    </div>
                </div>
            @endif
        </div>

    </div>
    {{-- ============================================================ --}}

</div>

@push('scripts')
<script>
    function toggleFilter() {
        const content = document.getElementById('filter-content');
        const icon = document.getElementById('filter-icon');
        content.classList.toggle('hidden');
        icon.style.transform = content.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
    }

    document.addEventListener('DOMContentLoaded', function () {
        @if(request()->hasAny(['search', 'exception_type', 'date_from', 'date_to', 'context']))
            const content = document.getElementById('filter-content');
            const icon = document.getElementById('filter-icon');
            content.classList.remove('hidden');
            icon.style.transform = 'rotate(180deg)';
        @endif

        $('#exception_type').select2({ placeholder: 'Semua Tipe', allowClear: true, width: '100%' });

        lucide.createIcons();
    });
</script>
@endpush
@endsection