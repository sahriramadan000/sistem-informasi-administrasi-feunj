@extends('layouts.app')

@section('title', 'Activity Logs Dashboard')

@section('content')
<div class="space-y-6">

    {{-- ============================================================ --}}
    {{-- Page Header --}}
    {{-- ============================================================ --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                <i data-lucide="activity" class="mr-3 h-7 w-7 text-brand"></i>
                Activity Logs
            </h2>
            <p class="mt-1 text-sm text-gray-500">Pantau semua aktivitas pengguna dan perubahan sistem</p>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- Statistics Cards --}}
    {{-- ============================================================ --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">

        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 border-l-4 border-l-blue-500">
            <div class="flex items-center gap-2 mb-1">
                <i data-lucide="calendar-days" class="h-4 w-4 text-blue-400"></i>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Hari Ini</p>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['total_today'] }}</p>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 border-l-4 border-l-green-500">
            <div class="flex items-center gap-2 mb-1">
                <i data-lucide="calendar-range" class="h-4 w-4 text-green-400"></i>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Minggu Ini</p>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['total_week'] }}</p>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 border-l-4 border-l-purple-500">
            <div class="flex items-center gap-2 mb-1">
                <i data-lucide="calendar" class="h-4 w-4 text-purple-400"></i>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Bulan Ini</p>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['total_month'] }}</p>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 border-l-4 border-l-emerald-500">
            <div class="flex items-center gap-2 mb-1">
                <i data-lucide="plus-circle" class="h-4 w-4 text-emerald-400"></i>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Creates</p>
            </div>
            <p class="text-3xl font-bold text-emerald-600">{{ $stats['creates'] }}</p>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 border-l-4 border-l-yellow-500">
            <div class="flex items-center gap-2 mb-1">
                <i data-lucide="pencil" class="h-4 w-4 text-yellow-400"></i>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Updates</p>
            </div>
            <p class="text-3xl font-bold text-yellow-600">{{ $stats['updates'] }}</p>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 border-l-4 border-l-red-500">
            <div class="flex items-center gap-2 mb-1">
                <i data-lucide="trash-2" class="h-4 w-4 text-red-400"></i>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Deletes</p>
            </div>
            <p class="text-3xl font-bold text-red-600">{{ $stats['deletes'] }}</p>
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
                    @if (request()->hasAny(['search', 'action', 'model', 'user_id', 'date_from', 'date_to']))
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
                    <form method="GET" action="{{ route('admin.activity-logs.index') }}">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 items-end">

                            {{-- Search --}}
                            <div class="space-y-2">
                                <label for="search" class="label">Pencarian</label>
                                <input
                                    type="text"
                                    id="search"
                                    name="search"
                                    value="{{ request('search') }}"
                                    placeholder="Nama pengguna atau ID model"
                                    class="input w-full">
                            </div>

                            {{-- Action Type --}}
                            <div class="space-y-2">
                                <label for="action" class="label">Tipe Aksi</label>
                                <select id="action" name="action" class="select">
                                    <option value="">Semua Aksi</option>
                                    <option value="create" {{ request('action') === 'create' ? 'selected' : '' }}>Create</option>
                                    <option value="update" {{ request('action') === 'update' ? 'selected' : '' }}>Update</option>
                                    <option value="delete" {{ request('action') === 'delete' ? 'selected' : '' }}>Delete</option>
                                </select>
                            </div>

                            {{-- Model --}}
                            <div class="space-y-2">
                                <label for="model" class="label">Model</label>
                                <select id="model" name="model" class="select">
                                    <option value="">Semua Model</option>
                                    @foreach ($models as $model)
                                        <option value="{{ $model }}" {{ request('model') === $model ? 'selected' : '' }}>
                                            {{ $model }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- User --}}
                            <div class="space-y-2">
                                <label for="user_id" class="label">Pengguna</label>
                                <select id="user_id" name="user_id" class="select">
                                    <option value="">Semua Pengguna</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Date From --}}
                            <div class="space-y-2">
                                <label for="date_from" class="label">Tanggal Dari</label>
                                <input
                                    type="date"
                                    id="date_from"
                                    name="date_from"
                                    value="{{ request('date_from') }}"
                                    class="input w-full">
                            </div>

                            {{-- Date To --}}
                            <div class="space-y-2">
                                <label for="date_to" class="label">Tanggal Sampai</label>
                                <input
                                    type="date"
                                    id="date_to"
                                    name="date_to"
                                    value="{{ request('date_to') }}"
                                    class="input w-full">
                            </div>

                            {{-- Action Buttons --}}
                            <div class="flex items-center gap-3 sm:col-span-2 lg:col-span-2">
                                <button type="submit" class="btn-primary flex-1 justify-center">
                                    <i data-lucide="filter" class="mr-2 h-4 w-4"></i>
                                    Filter
                                </button>
                                <a href="{{ route('admin.activity-logs.index') }}" class="btn-outline flex-1 justify-center">
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
                <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="inline-block">
                    @if (request('search'))
                        <input type="hidden" name="search"    value="{{ request('search') }}">
                    @endif
                    @if (request('action'))
                        <input type="hidden" name="action"    value="{{ request('action') }}">
                    @endif
                    @if (request('model'))
                        <input type="hidden" name="model"     value="{{ request('model') }}">
                    @endif
                    @if (request('user_id'))
                        <input type="hidden" name="user_id"   value="{{ request('user_id') }}">
                    @endif
                    @if (request('date_from'))
                        <input type="hidden" name="date_from" value="{{ request('date_from') }}">
                    @endif
                    @if (request('date_to'))
                        <input type="hidden" name="date_to"   value="{{ request('date_to') }}">
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
                Total: <span class="font-semibold">{{ $activityLogs->total() }}</span> data
            </span>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            @if ($activityLogs->count() > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50">No</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50">Timestamp</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50">Aksi</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50 hidden lg:table-cell">Model</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50 hidden lg:table-cell">Model ID</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50">Pengguna</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50 hidden xl:table-cell">IP Address</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50">Detail</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($activityLogs as $index => $log)
                            <tr class="hover:bg-gray-50 transition-colors">

                                {{-- No --}}
                                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ ($activityLogs->currentPage() - 1) * $activityLogs->perPage() + $index + 1 }}
                                </td>

                                {{-- Timestamp --}}
                                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $log->created_at->format('d/m/Y H:i') }}
                                </td>

                                {{-- Action Badge — pola ring-1 ring-inset seperti Daftar Surat --}}
                                <td class="px-3 py-4 whitespace-nowrap">
                                    @php
                                        $badgeConfig = match($log->action) {
                                            'create' => ['class' => 'bg-emerald-100 text-emerald-800 ring-emerald-600/20', 'icon' => 'plus-circle'],
                                            'update' => ['class' => 'bg-yellow-100 text-yellow-800 ring-yellow-600/20',   'icon' => 'pencil'],
                                            'delete' => ['class' => 'bg-red-100 text-red-800 ring-red-600/20',             'icon' => 'trash-2'],
                                            default  => ['class' => 'bg-gray-100 text-gray-700 ring-gray-600/20',          'icon' => 'circle'],
                                        };
                                    @endphp
                                    <span class="inline-flex items-center gap-1.5 rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset {{ $badgeConfig['class'] }}">
                                        <i data-lucide="{{ $badgeConfig['icon'] }}" class="h-3 w-3"></i>
                                        {{ ucfirst($log->action) }}
                                    </span>
                                </td>

                                {{-- Model --}}
                                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500 hidden lg:table-cell">
                                    {{ $log->model }}
                                </td>

                                {{-- Model ID --}}
                                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500 font-mono hidden lg:table-cell">
                                    {{ $log->model_id ?? '-' }}
                                </td>

                                {{-- User — pola rounded-full bg-gray-100 seperti kolom "Dibuat Oleh" Daftar Surat --}}
                                <td class="px-3 py-4 whitespace-nowrap text-sm">
                                    @if ($log->user_id)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ $log->user_name }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-400 italic">
                                            Sistem
                                        </span>
                                    @endif
                                </td>

                                {{-- IP Address --}}
                                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500 font-mono hidden xl:table-cell">
                                    {{ $log->request_ip ?? '-' }}
                                </td>

                                {{-- Detail — pola tombol biru seperti Daftar Surat --}}
                                <td class="px-3 py-4 whitespace-nowrap text-sm">
                                    <a href="{{ route('admin.activity-logs.show', $log->id) }}"
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
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $activityLogs->appends(request()->query())->links() }}
                </div>

            @else
                {{-- Empty State — pola Daftar Surat --}}
                <div class="text-center py-12">
                    <i data-lucide="inbox" class="mx-auto h-12 w-12 text-gray-400"></i>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada log aktivitas</h3>
                    <p class="mt-1 text-sm text-gray-500">Belum ada data yang sesuai dengan filter yang dipilih.</p>
                    <div class="mt-6">
                        <a href="{{ route('admin.activity-logs.index') }}"
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
        // Auto-buka filter jika ada filter aktif
        @if(request()->hasAny(['search', 'action', 'model', 'user_id', 'date_from', 'date_to']))
            const content = document.getElementById('filter-content');
            const icon = document.getElementById('filter-icon');
            content.classList.remove('hidden');
            icon.style.transform = 'rotate(180deg)';
        @endif

        $('#action').select2({ placeholder: 'Semua Aksi', allowClear: true, width: '100%' });
        $('#model').select2({ placeholder: 'Semua Model', allowClear: true, width: '100%' });
        $('#user_id').select2({ placeholder: 'Semua Pengguna', allowClear: true, width: '100%' });

        lucide.createIcons();
    });
</script>
@endpush
@endsection