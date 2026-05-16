@extends('layouts.app')

@section('title', 'Detail Error: ' . $error['error_id'])

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i data-lucide="alert-triangle" class="w-6 h-6 text-brand"></i>
                Detail Error
            </h2>
            <p class="mt-1 text-sm text-gray-500">{{ $error['error_id'] }}</p>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" id="copyBtn"
                onclick="copyErrorId()"
                class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 bg-white hover:bg-gray-50 transition-colors shadow-sm">
                <i data-lucide="copy" class="w-4 h-4"></i>
                Salin Error ID
            </button>
            <a href="{{ route('admin.error-logs.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Kembali
            </a>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- Error Summary Card --}}
    {{-- ============================================================ --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

        {{-- Card Header --}}
        <div class="px-6 py-4 bg-gradient-to-r from-red-600 to-red-800">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-white/20">
                    <i data-lucide="bug" class="h-5 w-5 text-white"></i>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-white">Ringkasan Error</h3>
                    <p class="text-xs text-white/80">Informasi utama exception yang terjadi</p>
                </div>
            </div>
        </div>

        <div class="p-6 space-y-5">

            {{-- Meta Info Grid --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                    <p class="text-xs text-gray-500 mb-1">Error ID</p>
                    <code class="text-xs font-mono text-gray-800 break-all">{{ $error['error_id'] }}</code>
                </div>
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                    <p class="text-xs text-gray-500 mb-2">Tipe Exception</p>
                    @php
                        $isCritical = in_array($error['exception_type'], ['FatalError', 'ParseError', 'TypeError']);
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        {{ $isCritical ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ $error['exception_type'] }}
                    </span>
                </div>
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                    <p class="text-xs text-gray-500 mb-1">Kode Error</p>
                    <p class="text-sm font-mono font-semibold text-gray-800">{{ $error['code'] ?? 'N/A' }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                    <p class="text-xs text-gray-500 mb-1">Waktu Terjadi</p>
                    <p class="text-sm font-semibold text-gray-800">
                        {{ \Carbon\Carbon::parse($error['timestamp'])->format('d M Y, H:i:s') }}
                    </p>
                </div>
            </div>

            {{-- Context --}}
            <div class="space-y-1.5">
                <p class="text-sm font-medium text-gray-700">Context</p>
                <div class="bg-gray-50 border border-gray-200 rounded-lg px-4 py-3 text-sm text-gray-700 font-mono">
                    {{ $error['context'] }}
                </div>
            </div>

            {{-- Message --}}
            <div class="space-y-1.5">
                <p class="text-sm font-medium text-gray-700">Pesan Error</p>
                <div class="bg-red-50 border border-red-200 rounded-lg px-4 py-3 text-sm text-red-800 break-words">
                    {{ $error['message'] }}
                </div>
            </div>

        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- User & Request + File Location (2 cols) --}}
    {{-- ============================================================ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- User & Request Info --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-700 to-gray-900">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-white/20">
                        <i data-lucide="user" class="h-5 w-5 text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-white">User & Request</h3>
                        <p class="text-xs text-white/80">Informasi pengguna dan permintaan HTTP</p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 gap-4">

                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                        <p class="text-xs text-gray-500 mb-1">Pengguna</p>
                        @if ($error['user_id'])
                            <p class="text-sm font-semibold text-gray-800">{{ $error['user_name'] }}</p>
                            <p class="text-xs text-gray-400">#{{ $error['user_id'] }}</p>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-500 italic">
                                System (Tidak ada user)
                            </span>
                        @endif
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                        <p class="text-xs text-gray-500 mb-1">Method</p>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                            {{ $error['request_method'] }}
                        </span>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                        <p class="text-xs text-gray-500 mb-1">URL</p>
                        <code class="text-xs font-mono text-gray-700 break-all">{{ $error['request_url'] }}</code>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                            <p class="text-xs text-gray-500 mb-1">IP Address</p>
                            <p class="text-sm font-mono font-semibold text-gray-800">{{ $error['request_ip'] }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                            <p class="text-xs text-gray-500 mb-1">User Agent</p>
                            <p class="text-xs text-gray-600 break-words line-clamp-3" title="{{ $error['user_agent'] }}">
                                {{ Str::limit($error['user_agent'], 80) }}
                            </p>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- File Location --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-700 to-gray-900">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-white/20">
                        <i data-lucide="file-code" class="h-5 w-5 text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-white">Lokasi File</h3>
                        <p class="text-xs text-white/80">File dan baris terjadinya error</p>
                    </div>
                </div>
            </div>

            <div class="p-6 space-y-4">

                <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                    <p class="text-xs text-gray-500 mb-2">Path File</p>
                    <code class="text-xs font-mono text-gray-700 break-all leading-relaxed">{{ $error['file'] }}</code>
                </div>

                <div class="bg-red-50 rounded-lg p-4 border border-red-100">
                    <p class="text-xs text-red-500 mb-1">Baris Error</p>
                    <p class="text-2xl font-bold text-red-700 font-mono">{{ $error['line'] }}</p>
                </div>

                {{-- Severity indicator --}}
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                    <p class="text-xs text-gray-500 mb-2">Tingkat Keparahan</p>
                    @if ($isCritical)
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-red-500 flex-shrink-0"></span>
                            <span class="text-sm font-semibold text-red-700">Critical — Perlu penanganan segera</span>
                        </div>
                    @else
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-yellow-500 flex-shrink-0"></span>
                            <span class="text-sm font-semibold text-yellow-700">Warning — Perlu ditindaklanjuti</span>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- Stack Trace --}}
    {{-- ============================================================ --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-700 to-gray-900">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-white/20">
                    <i data-lucide="terminal" class="h-5 w-5 text-white"></i>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-white">Stack Trace</h3>
                    <p class="text-xs text-white/80">Jejak pemanggilan fungsi saat error terjadi</p>
                </div>
            </div>
        </div>
        <div class="p-6">
            <pre class="bg-gray-950 text-green-400 rounded-lg p-5 overflow-auto text-xs whitespace-pre-wrap break-words max-h-96 leading-relaxed font-mono">{{ $error['stack_trace'] }}</pre>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- Query Parameters --}}
    {{-- ============================================================ --}}
    @if (!empty($error['query_parameters']))
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-100">
                        <i data-lucide="search-code" class="h-5 w-5 text-gray-600"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">Query Parameters</h3>
                        <p class="text-xs text-gray-500">Parameter URL saat request terjadi</p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <pre class="bg-gray-50 border border-gray-200 rounded-lg p-4 overflow-auto text-xs text-gray-700 font-mono leading-relaxed">{{ json_encode($error['query_parameters'], JSON_PRETTY_PRINT) }}</pre>
            </div>
        </div>
    @endif

    {{-- ============================================================ --}}
    {{-- Form Data --}}
    {{-- ============================================================ --}}
    @if (!empty($error['form_data']))
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-100">
                        <i data-lucide="clipboard-list" class="h-5 w-5 text-gray-600"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">Form Data</h3>
                        <p class="text-xs text-gray-500">Data form yang dikirim saat request</p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <pre class="bg-gray-50 border border-gray-200 rounded-lg p-4 overflow-auto text-xs text-gray-700 font-mono leading-relaxed">{{ json_encode($error['form_data'], JSON_PRETTY_PRINT) }}</pre>
            </div>
        </div>
    @endif

    {{-- ============================================================ --}}
    {{-- Additional Data --}}
    {{-- ============================================================ --}}
    @if (!empty($error['additional_data']))
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-100">
                        <i data-lucide="database" class="h-5 w-5 text-gray-600"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">Data Tambahan</h3>
                        <p class="text-xs text-gray-500">Informasi konteks tambahan saat error</p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <pre class="bg-gray-50 border border-gray-200 rounded-lg p-4 overflow-auto text-xs text-gray-700 font-mono leading-relaxed">{{ json_encode($error['additional_data'], JSON_PRETTY_PRINT) }}</pre>
            </div>
        </div>
    @endif

    {{-- ============================================================ --}}
    {{-- Related Audit Logs --}}
    {{-- ============================================================ --}}
    @if ($relatedAudits->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-brand to-orange-400">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-white/20">
                        <i data-lucide="history" class="h-5 w-5 text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-white">Audit Log Terkait</h3>
                        <p class="text-xs text-white/80">Aktivitas dalam ±5 menit dari waktu error</p>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Model</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Model ID</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Waktu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($relatedAudits as $audit)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $audit->action }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $audit->model }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700 font-mono">{{ $audit->model_id ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600 whitespace-nowrap">
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
        lucide.createIcons();
    });

    function copyErrorId() {
        const errorId = '{{ $error['error_id'] }}';
        navigator.clipboard.writeText(errorId).then(() => {
            const btn = document.getElementById('copyBtn');
            const original = btn.innerHTML;
            btn.innerHTML = '<i data-lucide="check" class="w-4 h-4"></i> Tersalin!';
            btn.classList.add('bg-green-50', 'border-green-300', 'text-green-700');
            lucide.createIcons();
            setTimeout(() => {
                btn.innerHTML = original;
                btn.classList.remove('bg-green-50', 'border-green-300', 'text-green-700');
                lucide.createIcons();
            }, 2000);
        });
    }
</script>
@endpush