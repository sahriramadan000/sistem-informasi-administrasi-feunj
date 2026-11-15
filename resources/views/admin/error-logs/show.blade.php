@extends('layouts.app')

@section('title', 'Detail Error: ' . $error['error_id'])

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
    $isCritical = in_array($error['exception_type'], ['FatalError', 'ParseError', 'TypeError']);
@endphp

<div class="space-y-6 pb-12">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-gray-200/80">
        <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-rose-500 to-red-600 flex items-center justify-center text-white shadow-xs flex-shrink-0">
                <i data-lucide="bug" class="w-5 h-5"></i>
            </div>
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">Detail Error</h1>
                <p class="text-xs sm:text-sm font-mono text-gray-500">{{ $error['error_id'] }}</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <button type="button" id="copyBtn" onclick="copyErrorId()"
                class="inline-flex items-center gap-2 px-4 py-2 text-xs sm:text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-200 rounded-full shadow-2xs transition-all cursor-pointer">
                <i data-lucide="copy" class="w-4 h-4 text-gray-500"></i>
                <span>Salin Error ID</span>
            </button>
            <a href="{{ route('admin.error-logs.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2 text-xs sm:text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-200 rounded-full shadow-2xs transition-all">
                <i data-lucide="arrow-left" class="w-4 h-4 text-gray-500"></i>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    {{-- Error Summary Card --}}
    <div class="mac-card p-6 sm:p-7 space-y-5">
        <div class="flex items-center justify-between pb-4 border-b border-gray-100">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-rose-50 border border-rose-200/70 flex items-center justify-center text-rose-600">
                    <i data-lucide="alert-octagon" class="w-4 h-4"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-gray-900">Ringkasan Error</h2>
                    <p class="text-[11px] text-gray-500">Informasi utama exception yang terjadi</p>
                </div>
            </div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold {{ $isCritical ? 'bg-rose-50 text-rose-700 border border-rose-200/70' : 'bg-amber-50 text-amber-700 border border-amber-200/70' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ $isCritical ? 'bg-rose-500' : 'bg-amber-500' }}"></span>
                {{ $error['exception_type'] }}
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
            <div class="p-3.5 rounded-2xl bg-gray-50/80 border border-gray-100">
                <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Error ID</p>
                <code class="text-xs font-mono font-bold text-gray-800 break-all mt-1 block">{{ $error['error_id'] }}</code>
            </div>
            <div class="p-3.5 rounded-2xl bg-gray-50/80 border border-gray-100">
                <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Kode Error</p>
                <p class="text-sm font-mono font-bold text-gray-900 mt-1">{{ $error['code'] ?? 'N/A' }}</p>
            </div>
            <div class="p-3.5 rounded-2xl bg-gray-50/80 border border-gray-100">
                <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Waktu Terjadi</p>
                <p class="text-sm font-semibold text-gray-900 mt-1">{{ \Carbon\Carbon::parse($error['timestamp'])->format('d M Y, H:i:s') }}</p>
            </div>
            <div class="p-3.5 rounded-2xl bg-gray-50/80 border border-gray-100">
                <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Context</p>
                <p class="text-xs font-mono font-semibold text-gray-800 mt-1 truncate" title="{{ $error['context'] }}">{{ $error['context'] }}</p>
            </div>
        </div>

        <div class="space-y-1.5">
            <p class="text-xs font-bold text-gray-700">Pesan Error</p>
            <div class="bg-rose-50/70 border border-rose-200/80 rounded-2xl px-4 py-3.5 text-xs sm:text-sm text-rose-800 break-words font-medium">
                {{ $error['message'] }}
            </div>
        </div>
    </div>

    {{-- User & Request + File Location --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- User & Request Info --}}
        <div class="mac-card p-6 space-y-4">
            <div class="flex items-center gap-2.5 pb-3 border-b border-gray-100">
                <div class="w-8 h-8 rounded-xl bg-slate-100 border border-slate-200/80 flex items-center justify-center text-slate-600">
                    <i data-lucide="globe" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-gray-900">User &amp; Request</h3>
                    <p class="text-[11px] text-gray-500">Informasi pengguna dan permintaan HTTP</p>
                </div>
            </div>

            <div class="space-y-3">
                <div class="p-3.5 rounded-2xl bg-gray-50/80 border border-gray-100">
                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Pengguna</p>
                    @if ($error['user_id'])
                        <p class="text-sm font-bold text-gray-900">{{ $error['user_name'] }} <span class="text-xs font-normal text-gray-400">#{{ $error['user_id'] }}</span></p>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs bg-gray-100 text-gray-500 italic">System (Tidak ada user)</span>
                    @endif
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="p-3.5 rounded-2xl bg-gray-50/80 border border-gray-100">
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Method</p>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200/70">
                            {{ $error['request_method'] }}
                        </span>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-gray-50/80 border border-gray-100">
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">IP Address</p>
                        <p class="text-sm font-mono font-semibold text-gray-800">{{ $error['request_ip'] }}</p>
                    </div>
                </div>

                <div class="p-3.5 rounded-2xl bg-gray-50/80 border border-gray-100">
                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">URL</p>
                    <code class="text-xs font-mono text-gray-700 break-all">{{ $error['request_url'] }}</code>
                </div>

                <div class="p-3.5 rounded-2xl bg-gray-50/80 border border-gray-100">
                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">User Agent</p>
                    <p class="text-xs text-gray-600 break-words" title="{{ $error['user_agent'] }}">{{ Str::limit($error['user_agent'], 110) }}</p>
                </div>
            </div>
        </div>

        {{-- File Location --}}
        <div class="mac-card p-6 space-y-4">
            <div class="flex items-center gap-2.5 pb-3 border-b border-gray-100">
                <div class="w-8 h-8 rounded-xl bg-slate-100 border border-slate-200/80 flex items-center justify-center text-slate-600">
                    <i data-lucide="file-code" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-gray-900">Lokasi File</h3>
                    <p class="text-[11px] text-gray-500">File dan baris kode terjadinya error</p>
                </div>
            </div>

            <div class="space-y-3">
                <div class="p-3.5 rounded-2xl bg-gray-50/80 border border-gray-100">
                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Path File</p>
                    <code class="text-xs font-mono text-gray-700 break-all leading-relaxed">{{ $error['file'] }}</code>
                </div>

                <div class="p-3.5 rounded-2xl bg-rose-50/60 border border-rose-200/70">
                    <p class="text-[11px] font-semibold text-rose-500 uppercase tracking-wider mb-1">Baris Error</p>
                    <p class="text-2xl font-extrabold text-rose-700 font-mono">Baris {{ $error['line'] }}</p>
                </div>

                <div class="p-3.5 rounded-2xl bg-gray-50/80 border border-gray-100">
                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Tingkat Keparahan</p>
                    @if ($isCritical)
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-500 flex-shrink-0"></span>
                            <span class="text-xs sm:text-sm font-bold text-rose-700">Critical — Perlu penanganan segera</span>
                        </div>
                    @else
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500 flex-shrink-0"></span>
                            <span class="text-xs sm:text-sm font-bold text-amber-700">Warning — Perlu ditindaklanjuti</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Stack Trace --}}
    <div class="mac-card p-6 space-y-4">
        <div class="flex items-center gap-2.5 pb-3 border-b border-gray-100">
            <div class="w-8 h-8 rounded-xl bg-gray-900 text-white flex items-center justify-center">
                <i data-lucide="terminal" class="w-4 h-4"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-gray-900">Stack Trace</h3>
                <p class="text-[11px] text-gray-500">Jejak pemanggilan fungsi saat exception terjadi</p>
            </div>
        </div>
        <pre class="bg-gray-950 text-emerald-400 rounded-2xl p-5 overflow-auto text-xs whitespace-pre-wrap break-words max-h-96 leading-relaxed font-mono">{{ $error['stack_trace'] }}</pre>
    </div>

    {{-- Query Parameters --}}
    @if (!empty($error['query_parameters']))
        <div class="mac-card p-6 space-y-3">
            <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                <i data-lucide="search-code" class="w-4 h-4 text-gray-500"></i>
                Query Parameters
            </h3>
            <pre class="bg-gray-50 border border-gray-200/80 rounded-2xl p-4 overflow-auto text-xs text-gray-700 font-mono leading-relaxed">{{ json_encode($error['query_parameters'], JSON_PRETTY_PRINT) }}</pre>
        </div>
    @endif

    {{-- Form Data --}}
    @if (!empty($error['form_data']))
        <div class="mac-card p-6 space-y-3">
            <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                <i data-lucide="clipboard-list" class="w-4 h-4 text-gray-500"></i>
                Form Data
            </h3>
            <pre class="bg-gray-50 border border-gray-200/80 rounded-2xl p-4 overflow-auto text-xs text-gray-700 font-mono leading-relaxed">{{ json_encode($error['form_data'], JSON_PRETTY_PRINT) }}</pre>
        </div>
    @endif

    {{-- Additional Data --}}
    @if (!empty($error['additional_data']))
        <div class="mac-card p-6 space-y-3">
            <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                <i data-lucide="database" class="w-4 h-4 text-gray-500"></i>
                Data Tambahan
            </h3>
            <pre class="bg-gray-50 border border-gray-200/80 rounded-2xl p-4 overflow-auto text-xs text-gray-700 font-mono leading-relaxed">{{ json_encode($error['additional_data'], JSON_PRETTY_PRINT) }}</pre>
        </div>
    @endif

    {{-- Related Audit Logs --}}
    @if (!empty($relatedAudits) && count($relatedAudits) > 0)
        <div class="mac-card overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-orange-50 border border-orange-200/70 flex items-center justify-center text-[#FF4D00]">
                    <i data-lucide="history" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-gray-900">Audit Log Terkait</h3>
                    <p class="text-[11px] text-gray-500">Aktivitas dalam &plusmn;5 menit dari waktu error</p>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-left">
                    <thead>
                        <tr class="bg-gray-50/60">
                            <th class="px-6 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                            <th class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider">Model</th>
                            <th class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider">Model ID</th>
                            <th class="px-6 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider text-right">Waktu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @foreach ($relatedAudits as $audit)
                            <tr class="hover:bg-gray-50/70 transition-colors">
                                <td class="px-6 py-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200/70">
                                        {{ $audit->action }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-xs font-semibold text-gray-800">{{ $audit->model }}</td>
                                <td class="px-4 py-3 text-xs text-gray-600 font-mono">{{ $audit->model_id ?? '-' }}</td>
                                <td class="px-6 py-3 text-xs text-gray-500 text-right font-mono whitespace-nowrap">
                                    {{ $audit->created_at->format('d M Y, H:i:s') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });

    function copyErrorId() {
        const errorId = '{{ $error['error_id'] }}';
        navigator.clipboard.writeText(errorId).then(() => {
            const btn = document.getElementById('copyBtn');
            const original = btn.innerHTML;
            btn.innerHTML = '<i data-lucide="check" class="w-4 h-4"></i> <span>Tersalin!</span>';
            btn.classList.add('!bg-emerald-50', '!border-emerald-300', '!text-emerald-700');
            if (typeof lucide !== 'undefined') lucide.createIcons();
            setTimeout(() => {
                btn.innerHTML = original;
                btn.classList.remove('!bg-emerald-50', '!border-emerald-300', '!text-emerald-700');
                if (typeof lucide !== 'undefined') lucide.createIcons();
            }, 2000);
        });
    }
</script>
@endpush
