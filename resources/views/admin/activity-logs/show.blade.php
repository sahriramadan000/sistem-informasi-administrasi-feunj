@extends('layouts.app')

@section('title', 'Detail Activity Log')

@section('content')
<div class="space-y-6">

    {{-- ============================================================ --}}
    {{-- Page Header --}}
    {{-- ============================================================ --}}
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i data-lucide="activity" class="w-6 h-6 text-brand"></i>
                Detail Activity Log
            </h2>
            <p class="mt-1 text-sm text-gray-500">Informasi lengkap mengenai aktivitas yang tercatat</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.activity-logs.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Kembali
            </a>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- Main Content Grid --}}
    {{-- ============================================================ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ============================================================ --}}
        {{-- LEFT COL (2/3): Info Utama + Perubahan Data --}}
        {{-- ============================================================ --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Info Utama --}}
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                {{-- Section Header --}}
                <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full bg-brand-lighter">
                        <i data-lucide="info" class="h-5 w-5 text-brand"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">Informasi Utama</h3>
                        <p class="text-sm text-gray-500">Data inti dari aktivitas yang tercatat</p>
                    </div>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">

                        {{-- Action --}}
                        <div class="space-y-1.5">
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide flex items-center gap-1.5">
                                <i data-lucide="zap" class="h-3.5 w-3.5"></i>
                                Aksi
                            </p>
                            @php
                                $badgeConfig = match($activityLog->action) {
                                    'create' => ['class' => 'bg-emerald-100 text-emerald-800 ring-emerald-600/20', 'icon' => 'plus-circle'],
                                    'update' => ['class' => 'bg-yellow-100 text-yellow-800 ring-yellow-600/20',   'icon' => 'pencil'],
                                    'delete' => ['class' => 'bg-red-100 text-red-800 ring-red-600/20',             'icon' => 'trash-2'],
                                    default  => ['class' => 'bg-gray-100 text-gray-700 ring-gray-600/20',          'icon' => 'circle'],
                                };
                            @endphp
                            <span class="inline-flex items-center gap-1.5 rounded-md px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $badgeConfig['class'] }}">
                                <i data-lucide="{{ $badgeConfig['icon'] }}" class="h-3.5 w-3.5"></i>
                                {{ ucfirst($activityLog->action) }}
                            </span>
                        </div>

                        {{-- Model --}}
                        <div class="space-y-1.5">
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide flex items-center gap-1.5">
                                <i data-lucide="layers" class="h-3.5 w-3.5"></i>
                                Model
                            </p>
                            <p class="text-sm font-semibold text-gray-800">{{ $activityLog->model }}</p>
                        </div>

                        {{-- Model ID --}}
                        <div class="space-y-1.5">
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide flex items-center gap-1.5">
                                <i data-lucide="hash" class="h-3.5 w-3.5"></i>
                                Model ID
                            </p>
                            <p class="text-sm text-gray-700 font-mono">{{ $activityLog->model_id ?? '-' }}</p>
                        </div>

                        {{-- Timestamp --}}
                        <div class="space-y-1.5">
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide flex items-center gap-1.5">
                                <i data-lucide="clock" class="h-3.5 w-3.5"></i>
                                Timestamp
                            </p>
                            <p class="text-sm text-gray-700">{{ $activityLog->created_at->format('d/m/Y H:i:s') }}</p>
                        </div>

                    </div>

                    {{-- Divider --}}
                    <div class="border-t border-gray-100 my-6"></div>

                    {{-- User Info --}}
                    <div class="space-y-2">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide flex items-center gap-1.5">
                            <i data-lucide="user" class="h-3.5 w-3.5"></i>
                            Pengguna
                        </p>
                        @if ($activityLog->user_id)
                            <div class="flex items-start gap-3 bg-gray-50 rounded-lg border border-gray-200 px-4 py-3">
                                <div class="w-9 h-9 rounded-full bg-brand-lighter flex items-center justify-center flex-shrink-0">
                                    <i data-lucide="user" class="h-4 w-4 text-brand"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">{{ $activityLog->user_name }}</p>
                                    @if ($activityLog->user)
                                        <p class="text-xs text-gray-500 mt-0.5">{{ $activityLog->user->email }}</p>
                                        <span class="inline-flex items-center mt-1 px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700 capitalize">
                                            {{ $activityLog->user->role }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="flex items-center gap-2 px-4 py-3 bg-gray-50 rounded-lg border border-gray-200">
                                <i data-lucide="cpu" class="h-4 w-4 text-gray-400"></i>
                                <span class="text-sm text-gray-400 italic">Sistem</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Changes Data --}}
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                {{-- Section Header --}}
                <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-100">
                        <i data-lucide="git-diff" class="h-5 w-5 text-gray-600"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">Data Perubahan</h3>
                        <p class="text-sm text-gray-500">Detail data yang berubah pada aktivitas ini</p>
                    </div>
                </div>

                <div class="p-6">
                    @if ($activityLog->data)
                        <pre class="bg-gray-50 border border-gray-200 rounded-lg p-4 overflow-auto text-xs text-gray-700 font-mono whitespace-pre-wrap break-words max-h-80 leading-relaxed">{{ json_encode(is_string($activityLog->data) ? json_decode($activityLog->data, true) : $activityLog->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    @else
                        <div class="flex flex-col items-center py-8 text-gray-400">
                            <i data-lucide="file-x" class="h-10 w-10 mb-2 opacity-50"></i>
                            <p class="text-sm">Tidak ada data perubahan tersedia</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>

        {{-- ============================================================ --}}
        {{-- RIGHT COL (1/3): Request Information --}}
        {{-- ============================================================ --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden sticky top-6">

                {{-- Section Header --}}
                <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-100">
                        <i data-lucide="globe" class="h-5 w-5 text-gray-600"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">Informasi Request</h3>
                        <p class="text-sm text-gray-500">Detail teknis HTTP request</p>
                    </div>
                </div>

                <div class="p-6 space-y-5">

                    {{-- Method --}}
                    <div class="space-y-1.5">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide flex items-center gap-1.5">
                            <i data-lucide="send" class="h-3.5 w-3.5"></i>
                            Method
                        </p>
                        @php
                            $method = $activityLog->request_method ?? null;
                            $methodColor = match($method) {
                                'GET'    => 'bg-blue-100 text-blue-800 ring-blue-600/20',
                                'POST'   => 'bg-emerald-100 text-emerald-800 ring-emerald-600/20',
                                'PUT', 'PATCH' => 'bg-yellow-100 text-yellow-800 ring-yellow-600/20',
                                'DELETE' => 'bg-red-100 text-red-800 ring-red-600/20',
                                default  => 'bg-gray-100 text-gray-700 ring-gray-600/20',
                            };
                        @endphp
                        @if($method)
                            <span class="inline-flex items-center rounded-md px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $methodColor }}">
                                {{ $method }}
                            </span>
                        @else
                            <span class="text-sm text-gray-400">-</span>
                        @endif
                    </div>

                    {{-- URL --}}
                    <div class="space-y-1.5">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide flex items-center gap-1.5">
                            <i data-lucide="link" class="h-3.5 w-3.5"></i>
                            URL
                        </p>
                        <code class="block text-xs bg-gray-50 border border-gray-200 rounded-md px-3 py-2 text-gray-700 break-all leading-relaxed">
                            {{ $activityLog->request_url ?? '-' }}
                        </code>
                    </div>

                    {{-- IP Address --}}
                    <div class="space-y-1.5">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide flex items-center gap-1.5">
                            <i data-lucide="monitor" class="h-3.5 w-3.5"></i>
                            IP Address
                        </p>
                        <p class="text-sm text-gray-700 font-mono">{{ $activityLog->request_ip ?? '-' }}</p>
                    </div>

                    {{-- Divider --}}
                    <div class="border-t border-gray-100"></div>

                    {{-- Created At --}}
                    <div class="space-y-1.5">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide flex items-center gap-1.5">
                            <i data-lucide="calendar" class="h-3.5 w-3.5"></i>
                            Dibuat Pada
                        </p>
                        <p class="text-sm text-gray-700">{{ $activityLog->created_at->format('d M Y') }}</p>
                        <p class="text-xs text-gray-500">{{ $activityLog->created_at->format('H:i:s') }} &bull; {{ $activityLog->created_at->diffForHumans() }}</p>
                    </div>

                </div>
            </div>
        </div>

    </div>

    {{-- ============================================================ --}}
    {{-- Related Activities --}}
    {{-- ============================================================ --}}
    @if ($relatedActivities->count() > 0)
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">

            {{-- Section Header --}}
            <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-100">
                    <i data-lucide="list-tree" class="h-5 w-5 text-gray-600"></i>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-gray-900">Aktivitas Terkait</h3>
                    <p class="text-sm text-gray-500">Pengguna & model yang sama, dalam rentang ±1 jam</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50">Timestamp</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50">Aksi</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50">Model ID</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50">Pengguna</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50">Detail</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($relatedActivities as $related)
                            <tr class="transition-colors {{ $related->id === $activityLog->id ? 'bg-brand-lighter' : 'hover:bg-gray-50' }}">

                                {{-- Timestamp --}}
                                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div class="flex items-center gap-1.5">
                                        @if ($related->id === $activityLog->id)
                                            <i data-lucide="arrow-right" class="h-3.5 w-3.5 text-brand flex-shrink-0"></i>
                                        @endif
                                        {{ $related->created_at->format('H:i:s') }}
                                    </div>
                                </td>

                                {{-- Action Badge --}}
                                <td class="px-3 py-4 whitespace-nowrap">
                                    @php
                                        $relatedBadge = match($related->action) {
                                            'create' => ['class' => 'bg-emerald-100 text-emerald-800 ring-emerald-600/20', 'icon' => 'plus-circle'],
                                            'update' => ['class' => 'bg-yellow-100 text-yellow-800 ring-yellow-600/20',   'icon' => 'pencil'],
                                            'delete' => ['class' => 'bg-red-100 text-red-800 ring-red-600/20',             'icon' => 'trash-2'],
                                            default  => ['class' => 'bg-gray-100 text-gray-700 ring-gray-600/20',          'icon' => 'circle'],
                                        };
                                    @endphp
                                    <span class="inline-flex items-center gap-1.5 rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset {{ $relatedBadge['class'] }}">
                                        <i data-lucide="{{ $relatedBadge['icon'] }}" class="h-3 w-3"></i>
                                        {{ ucfirst($related->action) }}
                                    </span>
                                </td>

                                {{-- Model ID --}}
                                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">
                                    {{ $related->model_id ?? '-' }}
                                </td>

                                {{-- User --}}
                                <td class="px-3 py-4 whitespace-nowrap text-sm">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ $related->user_name }}
                                    </span>
                                </td>

                                {{-- Detail --}}
                                <td class="px-3 py-4 whitespace-nowrap text-sm">
                                    @if ($related->id === $activityLog->id)
                                        <span class="inline-flex items-center rounded-md px-3 py-1.5 text-xs font-medium bg-brand-lighter text-brand-dark ring-1 ring-inset ring-brand/20">
                                            <i data-lucide="check" class="mr-1.5 h-3.5 w-3.5"></i>
                                            Log Ini
                                        </span>
                                    @else
                                        <a href="{{ route('admin.activity-logs.show', $related->id) }}"
                                            class="inline-flex items-center rounded-md bg-blue-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                                            <i data-lucide="eye" class="mr-1.5 h-3.5 w-3.5"></i>
                                            Detail
                                        </a>
                                    @endif
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    @endif

</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        lucide.createIcons();
    });
</script>
@endpush
@endsection