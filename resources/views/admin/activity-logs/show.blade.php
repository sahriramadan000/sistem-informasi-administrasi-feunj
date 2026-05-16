@extends('layouts.app')

@section('title', 'Activity Log Details')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <a href="{{ route('admin.activity-logs.index') }}" class="text-blue-600 hover:text-blue-900 font-medium flex items-center gap-2 mb-4">
                <i data-lucide="arrow-left" class="h-4 w-4"></i>
                Back to Activity Logs
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Activity Log Details</h1>
        </div>
    </div>

    {{-- Activity Log Details --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Main Info --}}
        <div class="lg:col-span-2 bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Action</p>
                    @php
                        $badgeClass = match($activityLog->action) {
                            'create' => 'bg-emerald-100 text-emerald-800',
                            'update' => 'bg-yellow-100 text-yellow-800',
                            'delete' => 'bg-red-100 text-red-800',
                            default => 'bg-gray-100 text-gray-800',
                        };
                    @endphp
                    <span class="mt-1 inline-block px-3 py-1 rounded-full text-sm font-medium {{ $badgeClass }}">
                        {{ ucfirst($activityLog->action) }}
                    </span>
                </div>
                <div>
                    <p class="text-sm text-gray-600 font-medium">Model</p>
                    <p class="text-sm text-gray-700 font-semibold">{{ $activityLog->model }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 font-medium">Model ID</p>
                    <p class="text-sm text-gray-700 font-mono">{{ $activityLog->model_id ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 font-medium">Timestamp</p>
                    <p class="text-sm text-gray-700">{{ $activityLog->created_at->format('M d, Y H:i:s') }}</p>
                </div>
            </div>

            <div class="mb-6">
                <p class="text-sm text-gray-600 font-medium mb-2">User</p>
                @if ($activityLog->user_id)
                    <div class="bg-gray-50 px-4 py-3 rounded border border-gray-200">
                        <p class="text-sm text-gray-700 font-semibold">{{ $activityLog->user_name }}</p>
                        @if ($activityLog->user)
                            <p class="text-xs text-gray-500 mt-1">{{ $activityLog->user->email }}</p>
                            <p class="text-xs text-gray-500 capitalize">{{ $activityLog->user->role }}</p>
                        @endif
                    </div>
                @else
                    <p class="text-sm text-gray-400 italic">System</p>
                @endif
            </div>
        </div>

        {{-- Request Info --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Request Information</h3>
            
            <div class="space-y-4">
                <div class="border-b pb-4">
                    <p class="text-sm text-gray-600 font-medium">Request Method</p>
                    <p class="text-sm text-gray-700 font-mono">{{ $activityLog->request_method ?? 'N/A' }}</p>
                </div>

                <div class="border-b pb-4">
                    <p class="text-sm text-gray-600 font-medium">Request URL</p>
                    <code class="text-xs bg-gray-100 px-2 py-1 rounded text-gray-700 break-all">{{ $activityLog->request_url ?? 'N/A' }}</code>
                </div>

                <div class="border-b pb-4">
                    <p class="text-sm text-gray-600 font-medium">IP Address</p>
                    <p class="text-sm text-gray-700 font-mono">{{ $activityLog->request_ip ?? 'N/A' }}</p>
                </div>

                <div>
                    <p class="text-sm text-gray-600 font-medium">Created At</p>
                    <p class="text-sm text-gray-700">{{ $activityLog->created_at->format('M d, Y H:i:s') }}</p>
                </div>
            </div>
        </div>

        {{-- Changes Data --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Changes Data</h3>
            
            @if ($activityLog->data)
                <pre class="bg-gray-50 border border-gray-200 rounded p-4 overflow-auto text-xs text-gray-700 whitespace-pre-wrap break-words max-h-64">{{ json_encode(json_decode($activityLog->data), JSON_PRETTY_PRINT) }}</pre>
            @else
                <p class="text-gray-400 italic text-sm">No change data available</p>
            @endif
        </div>
    </div>

    {{-- Related Activities --}}
    @if ($relatedActivities->count() > 0)
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Related Activities (Same User & Model, ±1 hour)</h3>
            
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase">Timestamp</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase">Action</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase">Model ID</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase">User</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($relatedActivities as $related)
                            <tr class="hover:bg-gray-50 {{ $related->id === $activityLog->id ? 'bg-blue-50' : '' }}">
                                <td class="px-4 py-2 text-gray-600">{{ $related->created_at->format('H:i:s') }}</td>
                                <td class="px-4 py-2">
                                    @php
                                        $relatedBadgeClass = match($related->action) {
                                            'create' => 'bg-emerald-100 text-emerald-800',
                                            'update' => 'bg-yellow-100 text-yellow-800',
                                            'delete' => 'bg-red-100 text-red-800',
                                            default => 'bg-gray-100 text-gray-800',
                                        };
                                    @endphp
                                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $relatedBadgeClass }}">
                                        {{ ucfirst($related->action) }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 text-gray-700 font-mono">{{ $related->model_id ?? '-' }}</td>
                                <td class="px-4 py-2 text-gray-700">{{ $related->user_name }}</td>
                                <td class="px-4 py-2">
                                    <a href="{{ route('admin.activity-logs.show', $related->id) }}" class="text-blue-600 hover:text-blue-900 font-medium">
                                        View
                                    </a>
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
