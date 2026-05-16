@extends('layouts.app')

@section('title', 'Error Details: ' . $error['error_id'])

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <a href="{{ route('admin.error-logs.index') }}" class="text-blue-600 hover:text-blue-900 font-medium flex items-center gap-2 mb-4">
                <i data-lucide="arrow-left" class="h-4 w-4"></i>
                Back to Error Logs
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Error Details</h1>
        </div>
        <button 
            type="button"
            onclick="navigator.clipboard.writeText('{{ $error['error_id'] }}').then(() => { alert('Error ID copied!'); });"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium flex items-center gap-2">
            <i data-lucide="copy" class="h-5 w-5"></i>
            Copy Error ID
        </button>
    </div>

    {{-- Error Summary --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Main Error Info --}}
        <div class="lg:col-span-2 bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Error ID</p>
                    <code class="text-sm bg-gray-100 px-2 py-1 rounded text-gray-700 break-all">{{ $error['error_id'] }}</code>
                </div>
                <div>
                    <p class="text-sm text-gray-600 font-medium">Type</p>
                    @php
                        $isCritical = in_array($error['exception_type'], ['FatalError', 'ParseError', 'TypeError']);
                        $badgeClass = $isCritical ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800';
                    @endphp
                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $badgeClass }}">
                        {{ $error['exception_type'] }}
                    </span>
                </div>
                <div>
                    <p class="text-sm text-gray-600 font-medium">Code</p>
                    <p class="text-sm text-gray-700 font-mono">{{ $error['code'] ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 font-medium">Timestamp</p>
                    <p class="text-sm text-gray-700">{{ \Carbon\Carbon::parse($error['timestamp'])->format('M d, Y H:i:s') }}</p>
                </div>
            </div>

            <div class="mb-6">
                <p class="text-sm text-gray-600 font-medium mb-2">Context</p>
                <p class="text-sm bg-gray-50 px-3 py-2 rounded border border-gray-200 text-gray-700">
                    {{ $error['context'] }}
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-600 font-medium mb-2">Message</p>
                <p class="text-sm bg-red-50 px-3 py-2 rounded border border-red-200 text-red-700 break-words">
                    {{ $error['message'] }}
                </p>
            </div>
        </div>

        {{-- User & Request Info --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">User & Request</h3>
            
            <div class="space-y-4">
                <div class="border-b pb-4">
                    <p class="text-sm text-gray-600 font-medium">User</p>
                    @if ($error['user_id'])
                        <p class="text-sm text-gray-700">{{ $error['user_name'] }} <span class="text-gray-500">#{{ $error['user_id'] }}</span></p>
                    @else
                        <p class="text-sm text-gray-400 italic">System (No user)</p>
                    @endif
                </div>

                <div class="border-b pb-4">
                    <p class="text-sm text-gray-600 font-medium">Request Method</p>
                    <p class="text-sm text-gray-700">{{ $error['request_method'] }}</p>
                </div>

                <div class="border-b pb-4">
                    <p class="text-sm text-gray-600 font-medium">Request URL</p>
                    <code class="text-xs bg-gray-100 px-2 py-1 rounded text-gray-700 break-all">{{ $error['request_url'] }}</code>
                </div>

                <div class="border-b pb-4">
                    <p class="text-sm text-gray-600 font-medium">IP Address</p>
                    <p class="text-sm text-gray-700 font-mono">{{ $error['request_ip'] }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-600 font-medium">User Agent</p>
                    <p class="text-xs text-gray-600 break-words">{{ substr($error['user_agent'], 0, 80) }}...</p>
                </div>
            </div>
        </div>

        {{-- File Info --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">File Location</h3>
            
            <div class="space-y-4">
                <div>
                    <p class="text-sm text-gray-600 font-medium">File</p>
                    <code class="text-xs bg-gray-100 px-2 py-1 rounded text-gray-700 break-all">{{ $error['file'] }}</code>
                </div>

                <div>
                    <p class="text-sm text-gray-600 font-medium">Line</p>
                    <p class="text-sm text-gray-700 font-mono">{{ $error['line'] }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Stack Trace --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Stack Trace</h3>
        <pre class="bg-gray-50 border border-gray-200 rounded p-4 overflow-auto text-xs text-gray-700 whitespace-pre-wrap break-words max-h-96">{{ $error['stack_trace'] }}</pre>
    </div>

    {{-- Query Parameters --}}
    @if (!empty($error['query_parameters']))
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Query Parameters</h3>
            <pre class="bg-gray-50 border border-gray-200 rounded p-4 overflow-auto text-xs text-gray-700">{{ json_encode($error['query_parameters'], JSON_PRETTY_PRINT) }}</pre>
        </div>
    @endif

    {{-- Form Data --}}
    @if (!empty($error['form_data']))
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Form Data</h3>
            <pre class="bg-gray-50 border border-gray-200 rounded p-4 overflow-auto text-xs text-gray-700">{{ json_encode($error['form_data'], JSON_PRETTY_PRINT) }}</pre>
        </div>
    @endif

    {{-- Additional Data --}}
    @if (!empty($error['additional_data']))
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Additional Data</h3>
            <pre class="bg-gray-50 border border-gray-200 rounded p-4 overflow-auto text-xs text-gray-700">{{ json_encode($error['additional_data'], JSON_PRETTY_PRINT) }}</pre>
        </div>
    @endif

    {{-- Related Audit Logs --}}
    @if ($relatedAudits->count() > 0)
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Related Audit Logs (±5 minutes)</h3>
            
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase">Action</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase">Model</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase">Model ID</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase">Time</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($relatedAudits as $audit)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $audit->action }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 text-gray-700">{{ $audit->model }}</td>
                                <td class="px-4 py-2 text-gray-700 font-mono">{{ $audit->model_id ?? '-' }}</td>
                                <td class="px-4 py-2 text-gray-600">{{ $audit->created_at->format('M d, Y H:i:s') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
