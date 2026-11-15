@extends('layouts.app')

@section('title', 'Detail Activity Log - FEB UNJ')

@push('styles')
    <style>
        .mac-card {
            background: #ffffff;
            border-radius: 1.5rem;
            border: 1px solid rgba(226, 232, 240, 0.85);
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.03), 0 2px 6px -1px rgba(0, 0, 0, 0.02);
        }
    </style>
@endpush

@section('content')
@php
    $badgeConfig = match($activityLog->action) {
        'create' => ['class' => 'bg-emerald-50 text-emerald-700 border-emerald-200/70', 'icon' => 'plus-circle'],
        'update' => ['class' => 'bg-amber-50 text-amber-700 border-amber-200/70',   'icon' => 'pencil'],
        'delete' => ['class' => 'bg-rose-50 text-rose-700 border-rose-200/70',       'icon' => 'trash-2'],
        default  => ['class' => 'bg-gray-100 text-gray-700 border-gray-200/70',      'icon' => 'circle'],
    };
@endphp

<div class="space-y-6 pb-12">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-gray-200/80">
        <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white shadow-xs flex-shrink-0">
                <i data-lucide="activity" class="w-5 h-5"></i>
            </div>
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">Detail Activity Log</h1>
                <p class="text-xs sm:text-sm text-gray-500">Informasi lengkap mengenai aktivitas yang tercatat di sistem</p>
            </div>
        </div>

        <a href="{{ route('admin.activity-logs.index') }}"
            class="inline-flex items-center gap-2 px-4 py-2 text-xs sm:text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-200 rounded-full shadow-2xs transition-all self-start sm:self-auto">
            <i data-lucide="arrow-left" class="w-4 h-4 text-gray-500"></i>
            <span>Kembali</span>
        </a>
    </div>

    {{-- Main Content Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left Col (2/3) --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Info Utama --}}
            <div class="mac-card p-6 space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-orange-50 border border-orange-200/70 flex items-center justify-center text-[#FF4D00]">
                            <i data-lucide="info" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <h2 class="text-sm font-bold text-gray-900">Informasi Utama</h2>
                            <p class="text-[11px] text-gray-500">Data inti dari aktivitas yang tercatat</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold border {{ $badgeConfig['class'] }}">
                        <i data-lucide="{{ $badgeConfig['icon'] }}" class="w-3.5 h-3.5"></i>
                        {{ ucfirst($activityLog->action) }}
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                    <div class="p-3.5 rounded-2xl bg-gray-50/80 border border-gray-100">
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Model</p>
                        <p class="text-sm font-bold text-gray-900 mt-1">{{ $activityLog->model }}</p>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-gray-50/80 border border-gray-100">
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Model ID</p>
                        <p class="text-sm font-mono font-bold text-gray-900 mt-1">{{ $activityLog->model_id ?? '-' }}</p>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-gray-50/80 border border-gray-100">
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Timestamp</p>
                        <p class="text-sm font-mono font-semibold text-gray-900 mt-1">{{ $activityLog->created_at->format('d/m/Y H:i:s') }}</p>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <p class="text-xs font-bold text-gray-700">Pelaku Aktivitas</p>
                    @if ($activityLog->user_id)
                        <div class="flex items-center gap-3 p-3.5 rounded-2xl bg-gray-50/80 border border-gray-100">
                            <div class="w-9 h-9 rounded-full bg-orange-100 text-orange-700 font-bold text-xs flex items-center justify-center flex-shrink-0">
                                <i data-lucide="user" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-900">{{ $activityLog->user_name }}</p>
                                @if ($activityLog->user)
                                    <p class="text-xs text-gray-500">{{ $activityLog->user->email }} &bull; <span class="capitalize font-semibold">{{ $activityLog->user->role }}</span></p>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="flex items-center gap-2 p-3.5 bg-gray-50/80 rounded-2xl border border-gray-100 text-xs text-gray-500 italic">
                            <i data-lucide="cpu" class="w-4 h-4 text-gray-400"></i>
                            <span>Sistem</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Changes Data --}}
            <div class="mac-card p-6 space-y-4">
                <div class="flex items-center gap-2.5 pb-3 border-b border-gray-100">
                    <div class="w-8 h-8 rounded-xl bg-slate-100 border border-slate-200/80 flex items-center justify-center text-slate-600">
                        <i data-lucide="git-diff" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-gray-900">Data Perubahan</h3>
                        <p class="text-[11px] text-gray-500">Detail payload data pada aktivitas ini</p>
                    </div>
                </div>

                @if ($activityLog->data)
                    <pre class="bg-gray-950 text-emerald-400 rounded-2xl p-4 overflow-auto text-xs font-mono whitespace-pre-wrap break-words max-h-80 leading-relaxed">{{ json_encode(is_string($activityLog->data) ? json_decode($activityLog->data, true) : $activityLog->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                @else
                    <div class="text-center py-8 text-xs text-gray-400">Tidak ada data perubahan tersedia</div>
                @endif
            </div>
        </div>

        {{-- Right Col (1/3) --}}
        <div class="lg:col-span-1">
            <div class="mac-card p-6 space-y-4 sticky top-6">
                <div class="flex items-center gap-2.5 pb-3 border-b border-gray-100">
                    <div class="w-8 h-8 rounded-xl bg-slate-100 border border-slate-200/80 flex items-center justify-center text-slate-600">
                        <i data-lucide="globe" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-gray-900">Informasi Request</h3>
                        <p class="text-[11px] text-gray-500">Detail teknis HTTP request</p>
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="p-3.5 rounded-2xl bg-gray-50/80 border border-gray-100">
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Method</p>
                        @php
                            $method = $activityLog->request_method ?? null;
                            $methodColor = match($method) {
                                'GET' => 'bg-blue-50 text-blue-700 border-blue-200/70',
                                'POST' => 'bg-emerald-50 text-emerald-700 border-emerald-200/70',
                                'PUT', 'PATCH' => 'bg-amber-50 text-amber-700 border-amber-200/70',
                                'DELETE' => 'bg-rose-50 text-rose-700 border-rose-200/70',
                                default => 'bg-gray-100 text-gray-700 border-gray-200/70',
                            };
                        @endphp
                        @if($method)
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-bold border {{ $methodColor }}">{{ $method }}</span>
                        @else
                            <span class="text-xs text-gray-400">-</span>
                        @endif
                    </div>

                    <div class="p-3.5 rounded-2xl bg-gray-50/80 border border-gray-100">
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">URL</p>
                        <code class="text-xs font-mono text-gray-700 break-all leading-relaxed">{{ $activityLog->request_url ?? '-' }}</code>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-gray-50/80 border border-gray-100">
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">IP Address</p>
                        <p class="text-sm font-mono font-semibold text-gray-800">{{ $activityLog->request_ip ?? '-' }}</p>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-gray-50/80 border border-gray-100">
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Dibuat Pada</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $activityLog->created_at->format('d M Y, H:i:s') }}</p>
                        <p class="text-[11px] text-gray-500 mt-0.5">{{ $activityLog->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Related Activities --}}
    @if ($relatedActivities->count() > 0)
        <div class="mac-card overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-slate-100 border border-slate-200/80 flex items-center justify-center text-slate-600">
                    <i data-lucide="list-tree" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-gray-900">Aktivitas Terkait</h3>
                    <p class="text-[11px] text-gray-500">Pengguna &amp; model yang sama dalam rentang &plusmn;1 jam</p>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-left">
                    <thead>
                        <tr class="bg-gray-50/60 border-b border-gray-100">
                            <th class="pl-6 pr-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider">Timestamp</th>
                            <th class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                            <th class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider">Model ID</th>
                            <th class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider">Pengguna</th>
                            <th class="pl-4 pr-6 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider text-right">Detail</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @foreach ($relatedActivities as $related)
                            @php
                                $relatedBadge = match($related->action) {
                                    'create' => ['class' => 'bg-emerald-50 text-emerald-700 border-emerald-200/70', 'icon' => 'plus-circle'],
                                    'update' => ['class' => 'bg-amber-50 text-amber-700 border-amber-200/70',   'icon' => 'pencil'],
                                    'delete' => ['class' => 'bg-rose-50 text-rose-700 border-rose-200/70',       'icon' => 'trash-2'],
                                    default  => ['class' => 'bg-gray-100 text-gray-700 border-gray-200/70',      'icon' => 'circle'],
                                };
                            @endphp
                            <tr class="transition-colors {{ $related->id === $activityLog->id ? 'bg-orange-50/40' : 'hover:bg-gray-50/70' }}">
                                <td class="pl-6 pr-4 py-3.5 whitespace-nowrap text-xs font-mono text-gray-600">
                                    {{ $related->created_at->format('H:i:s') }}
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-[11px] font-bold border {{ $relatedBadge['class'] }}">
                                        <i data-lucide="{{ $relatedBadge['icon'] }}" class="w-3 h-3"></i>
                                        {{ ucfirst($related->action) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap text-xs text-gray-600 font-mono">{{ $related->model_id ?? '-' }}</td>
                                <td class="px-4 py-3.5 whitespace-nowrap text-xs">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-gray-100 text-gray-800">
                                        {{ $related->user_name }}
                                    </span>
                                </td>
                                <td class="pl-4 pr-6 py-3.5 whitespace-nowrap text-right">
                                    @if ($related->id === $activityLog->id)
                                        <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-[11px] font-bold bg-orange-50 text-[#FF4D00] border border-orange-200/70">
                                            <i data-lucide="check" class="w-3 h-3"></i>
                                            Log Ini
                                        </span>
                                    @else
                                        <a href="{{ route('admin.activity-logs.show', $related->id) }}"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-[11px] font-bold text-gray-700 bg-gray-100/90 hover:bg-gray-900 hover:text-white transition-colors">
                                            <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                            <span>Detail</span>
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
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
@endpush
@endsection
