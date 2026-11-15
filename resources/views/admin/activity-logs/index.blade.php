@extends('layouts.app')

@section('title', 'Activity Logs - FEB UNJ')

@push('styles')
    <style>
        .mac-card {
            background: #ffffff;
            border-radius: 1.5rem;
            border: 1px solid rgba(226, 232, 240, 0.85);
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.03), 0 2px 6px -1px rgba(0, 0, 0, 0.02);
        }
        .kpi-card {
            background: #ffffff;
            border-radius: 1.25rem;
            border: 1px solid rgba(226, 232, 240, 0.85);
            box-shadow: 0 4px 16px -2px rgba(0, 0, 0, 0.03);
            transition: all 0.2s ease;
        }
        .kpi-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 22px -4px rgba(0, 0, 0, 0.05);
        }
    </style>
@endpush

@section('content')
<div class="space-y-6 pb-12">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-gray-200/80">
        <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white shadow-xs flex-shrink-0">
                <i data-lucide="activity" class="w-5 h-5"></i>
            </div>
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">Activity Logs</h1>
                <p class="text-xs sm:text-sm text-gray-500">Pantau rekam jejak aktivitas pengguna dan perubahan data sistem</p>
            </div>
        </div>

        <a href="{{ route('admin.dashboard') }}"
            class="inline-flex items-center gap-2 px-4 py-2 text-xs sm:text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-200 rounded-full shadow-2xs hover:shadow-xs transition-all self-start sm:self-auto">
            <i data-lucide="arrow-left" class="w-4 h-4 text-gray-500"></i>
            <span>Pengaturan</span>
        </a>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <div class="kpi-card p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Hari Ini</span>
                <div class="w-7 h-7 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <i data-lucide="calendar-days" class="w-3.5 h-3.5"></i>
                </div>
            </div>
            <p class="text-2xl font-extrabold text-gray-900">{{ $stats['total_today'] }}</p>
        </div>

        <div class="kpi-card p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Minggu Ini</span>
                <div class="w-7 h-7 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i data-lucide="calendar-range" class="w-3.5 h-3.5"></i>
                </div>
            </div>
            <p class="text-2xl font-extrabold text-gray-900">{{ $stats['total_week'] }}</p>
        </div>

        <div class="kpi-card p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Bulan Ini</span>
                <div class="w-7 h-7 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                    <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                </div>
            </div>
            <p class="text-2xl font-extrabold text-gray-900">{{ $stats['total_month'] }}</p>
        </div>

        <div class="kpi-card p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Creates</span>
                <div class="w-7 h-7 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i>
                </div>
            </div>
            <p class="text-2xl font-extrabold text-emerald-600">{{ $stats['creates'] }}</p>
        </div>

        <div class="kpi-card p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Updates</span>
                <div class="w-7 h-7 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                </div>
            </div>
            <p class="text-2xl font-extrabold text-amber-600">{{ $stats['updates'] }}</p>
        </div>

        <div class="kpi-card p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Deletes</span>
                <div class="w-7 h-7 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                </div>
            </div>
            <p class="text-2xl font-extrabold text-rose-600">{{ $stats['deletes'] }}</p>
        </div>
    </div>

    {{-- Filter + Table Card --}}
    <div class="mac-card overflow-hidden">
        {{-- Collapsible Filter --}}
        <div class="border-b border-gray-100">
            <button type="button" onclick="toggleFilter()"
                class="w-full flex items-center justify-between px-6 py-4 text-left hover:bg-gray-50/70 transition-colors cursor-pointer">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-emerald-50 border border-emerald-200/70 flex items-center justify-center text-emerald-600">
                        <i data-lucide="sliders-horizontal" class="w-4 h-4"></i>
                    </div>
                    <h3 class="text-sm font-bold text-gray-900">Filter Activity Logs</h3>
                    @if (request()->hasAny(['search', 'action', 'model', 'user_id', 'date_from', 'date_to']))
                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 border border-emerald-200/70 px-2.5 py-0.5 text-[11px] font-bold text-emerald-700">
                            <i data-lucide="filter" class="w-3 h-3"></i>
                            Aktif
                        </span>
                    @endif
                </div>
                <i data-lucide="chevron-down" id="filter-icon" class="w-4 h-4 text-gray-400 transition-transform duration-200"></i>
            </button>

            <div id="filter-content" class="hidden border-t border-gray-100 bg-gray-50/40 p-6">
                <form method="GET" action="{{ route('admin.activity-logs.index') }}">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 items-end">
                        <div class="space-y-1.5">
                            <label for="search" class="text-xs font-semibold text-gray-700 block">Pencarian</label>
                            <input type="text" id="search" name="search" value="{{ request('search') }}"
                                placeholder="Nama pengguna atau ID model"
                                class="w-full px-3.5 py-2 text-xs sm:text-sm bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500">
                        </div>

                        <div class="space-y-1.5">
                            <label for="action" class="text-xs font-semibold text-gray-700 block">Tipe Aksi</label>
                            <select id="action" name="action"
                                class="w-full px-3.5 py-2 text-xs sm:text-sm bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500">
                                <option value="">Semua Aksi</option>
                                <option value="create" {{ request('action') === 'create' ? 'selected' : '' }}>Create</option>
                                <option value="update" {{ request('action') === 'update' ? 'selected' : '' }}>Update</option>
                                <option value="delete" {{ request('action') === 'delete' ? 'selected' : '' }}>Delete</option>
                            </select>
                        </div>

                        <div class="space-y-1.5">
                            <label for="model" class="text-xs font-semibold text-gray-700 block">Model</label>
                            <select id="model" name="model"
                                class="w-full px-3.5 py-2 text-xs sm:text-sm bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500">
                                <option value="">Semua Model</option>
                                @foreach ($models as $model)
                                    <option value="{{ $model }}" {{ request('model') === $model ? 'selected' : '' }}>
                                        {{ $model }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-1.5">
                            <label for="user_id" class="text-xs font-semibold text-gray-700 block">Pengguna</label>
                            <select id="user_id" name="user_id"
                                class="w-full px-3.5 py-2 text-xs sm:text-sm bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500">
                                <option value="">Semua Pengguna</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-1.5">
                            <label for="date_from" class="text-xs font-semibold text-gray-700 block">Tanggal Dari</label>
                            <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}"
                                class="w-full px-3.5 py-2 text-xs sm:text-sm bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500">
                        </div>

                        <div class="space-y-1.5">
                            <label for="date_to" class="text-xs font-semibold text-gray-700 block">Tanggal Sampai</label>
                            <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}"
                                class="w-full px-3.5 py-2 text-xs sm:text-sm bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500">
                        </div>

                        <div class="flex items-center gap-2.5 sm:col-span-2 lg:col-span-2">
                            <button type="submit"
                                class="inline-flex items-center justify-center gap-1.5 px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white text-xs font-bold rounded-xl shadow-xs transition-all cursor-pointer flex-1">
                                <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                                <span>Terapkan Filter</span>
                            </button>
                            <a href="{{ route('admin.activity-logs.index') }}"
                                class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 bg-white hover:bg-gray-100 border border-gray-200 text-gray-700 text-xs font-semibold rounded-xl shadow-2xs transition-all">
                                <i data-lucide="x" class="w-3.5 h-3.5"></i>
                                <span>Reset</span>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Per Page Bar --}}
        <div class="border-b border-gray-100 px-6 py-3 flex items-center justify-between text-xs text-gray-500">
            <div class="flex items-center gap-2">
                <span>Tampilkan:</span>
                <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="inline-block m-0">
                    @if (request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                    @if (request('action')) <input type="hidden" name="action" value="{{ request('action') }}"> @endif
                    @if (request('model')) <input type="hidden" name="model" value="{{ request('model') }}"> @endif
                    @if (request('user_id')) <input type="hidden" name="user_id" value="{{ request('user_id') }}"> @endif
                    @if (request('date_from')) <input type="hidden" name="date_from" value="{{ request('date_from') }}"> @endif
                    @if (request('date_to')) <input type="hidden" name="date_to" value="{{ request('date_to') }}"> @endif

                    <select name="per_page" id="per_page"
                        class="px-2.5 py-1 text-xs font-semibold border border-gray-200 rounded-lg bg-gray-50/70 hover:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 cursor-pointer"
                        onchange="this.form.submit()">
                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page', 10) == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page', 10) == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page', 10) == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </form>
                <span>data per halaman</span>
            </div>
            <span>Total: <span class="font-bold text-gray-800">{{ number_format($activityLogs->total(), 0, ',', '.') }}</span> aktivitas</span>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            @if ($activityLogs->count() > 0)
                <table class="min-w-full divide-y divide-gray-100 text-left">
                    <thead>
                        <tr class="bg-gray-50/60 border-b border-gray-100">
                            <th scope="col" class="pl-6 pr-3 py-3 text-center text-[11px] font-bold text-gray-500 uppercase tracking-wider w-12">No</th>
                            <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider">Timestamp</th>
                            <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                            <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider hidden lg:table-cell">Model</th>
                            <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider hidden lg:table-cell">Model ID</th>
                            <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider">Pengguna</th>
                            <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider hidden xl:table-cell">IP Address</th>
                            <th scope="col" class="pl-4 pr-6 py-3 text-right text-[11px] font-bold text-gray-500 uppercase tracking-wider">Detail</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @foreach ($activityLogs as $index => $log)
                            @php
                                $badgeConfig = match($log->action) {
                                    'create' => ['class' => 'bg-emerald-50 text-emerald-700 border-emerald-200/70', 'icon' => 'plus-circle'],
                                    'update' => ['class' => 'bg-amber-50 text-amber-700 border-amber-200/70',   'icon' => 'pencil'],
                                    'delete' => ['class' => 'bg-rose-50 text-rose-700 border-rose-200/70',       'icon' => 'trash-2'],
                                    default  => ['class' => 'bg-gray-100 text-gray-700 border-gray-200/70',      'icon' => 'circle'],
                                };
                            @endphp
                            <tr class="hover:bg-emerald-50/20 transition-colors">
                                <td class="pl-6 pr-3 py-3.5 text-center text-xs font-semibold text-gray-400">
                                    {{ ($activityLogs->currentPage() - 1) * $activityLogs->perPage() + $index + 1 }}
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap text-xs font-mono text-gray-600">
                                    {{ $log->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-[11px] font-bold border {{ $badgeConfig['class'] }}">
                                        <i data-lucide="{{ $badgeConfig['icon'] }}" class="w-3 h-3"></i>
                                        {{ ucfirst($log->action) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap text-xs font-semibold text-gray-800 hidden lg:table-cell">
                                    {{ $log->model }}
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap text-xs text-gray-600 font-mono hidden lg:table-cell">
                                    {{ $log->model_id ?? '-' }}
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap text-xs">
                                    @if ($log->user_id)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-gray-100 text-gray-800">
                                            {{ $log->user_name }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-gray-100 text-gray-400 italic">
                                            Sistem
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap text-xs text-gray-500 font-mono hidden xl:table-cell">
                                    {{ $log->request_ip ?? '-' }}
                                </td>
                                <td class="pl-4 pr-6 py-3.5 whitespace-nowrap text-right">
                                    <a href="{{ route('admin.activity-logs.show', $log->id) }}"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-[11px] font-bold text-gray-700 bg-gray-100/90 hover:bg-gray-900 hover:text-white transition-colors">
                                        <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                        <span>Detail</span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Pagination --}}
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/40">
                    {{ $activityLogs->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-14 px-6">
                    <div class="w-14 h-14 mx-auto rounded-2xl bg-gray-100 flex items-center justify-center text-gray-400 mb-3">
                        <i data-lucide="inbox" class="w-7 h-7"></i>
                    </div>
                    <h3 class="text-sm font-bold text-gray-900">Tidak ada log aktivitas</h3>
                    <p class="mt-1 text-xs text-gray-500">Belum ada data yang sesuai dengan filter yang dipilih.</p>
                    <div class="mt-5">
                        <a href="{{ route('admin.activity-logs.index') }}"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-200 bg-white text-xs font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
                            <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i>
                            <span>Reset Filter</span>
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
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
        @if(request()->hasAny(['search', 'action', 'model', 'user_id', 'date_from', 'date_to']))
            const content = document.getElementById('filter-content');
            const icon = document.getElementById('filter-icon');
            content.classList.remove('hidden');
            icon.style.transform = 'rotate(180deg)';
        @endif

        if (typeof $ !== 'undefined') {
            if ($('#action').length) $('#action').select2({ placeholder: 'Semua Aksi', allowClear: true, width: '100%' });
            if ($('#model').length) $('#model').select2({ placeholder: 'Semua Model', allowClear: true, width: '100%' });
            if ($('#user_id').length) $('#user_id').select2({ placeholder: 'Semua Pengguna', allowClear: true, width: '100%' });
        }

        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
@endpush
@endsection
