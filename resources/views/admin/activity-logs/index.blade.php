@extends('layouts.app')

@section('title', 'Activity Logs Dashboard')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Activity Logs Dashboard</h1>
            <p class="mt-2 text-sm text-gray-600">Track all user actions and system changes</p>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
            <p class="text-sm text-gray-600 font-medium">Today</p>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['total_today'] }}</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
            <p class="text-sm text-gray-600 font-medium">This Week</p>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['total_week'] }}</p>
        </div>

        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-purple-500">
            <p class="text-sm text-gray-600 font-medium">This Month</p>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['total_month'] }}</p>
        </div>

        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-emerald-500">
            <p class="text-sm text-gray-600 font-medium">Creates</p>
            <p class="text-3xl font-bold text-emerald-600">{{ $stats['creates'] }}</p>
        </div>

        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-yellow-500">
            <p class="text-sm text-gray-600 font-medium">Updates</p>
            <p class="text-3xl font-bold text-yellow-600">{{ $stats['updates'] }}</p>
        </div>

        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-red-500">
            <p class="text-sm text-gray-600 font-medium">Deletes</p>
            <p class="text-3xl font-bold text-red-600">{{ $stats['deletes'] }}</p>
        </div>
    </div>

    {{-- Filter Section --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Filters</h3>
        
        <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Search --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}"
                    placeholder="User name or model ID"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            {{-- Action Type --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Action Type</label>
                <select name="action" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Actions</option>
                    <option value="create" {{ request('action') === 'create' ? 'selected' : '' }}>Create</option>
                    <option value="update" {{ request('action') === 'update' ? 'selected' : '' }}>Update</option>
                    <option value="delete" {{ request('action') === 'delete' ? 'selected' : '' }}>Delete</option>
                </select>
            </div>

            {{-- Model --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Model</label>
                <select name="model" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Models</option>
                    @foreach ($models as $model)
                        <option value="{{ $model }}" {{ request('model') === $model ? 'selected' : '' }}>
                            {{ $model }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- User --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">User</label>
                <select name="user_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Users</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Date From --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date From</label>
                <input 
                    type="date" 
                    name="date_from" 
                    value="{{ request('date_from') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            {{-- Date To --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date To</label>
                <input 
                    type="date" 
                    name="date_to" 
                    value="{{ request('date_to') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            {{-- Buttons --}}
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                    <i data-lucide="search" class="h-4 w-4 inline mr-2"></i>
                    Search
                </button>
                <a href="{{ route('admin.activity-logs.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                    <i data-lucide="x" class="h-4 w-4 inline"></i>
                </a>
            </div>
        </form>
    </div>

    {{-- Activity Logs Table --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Timestamp</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Action</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Model</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Model ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">IP Address</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($activityLogs as $log)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $log->created_at->format('M d, Y H:i') }}
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $badgeClass = match($log->action) {
                                        'create' => 'bg-emerald-100 text-emerald-800',
                                        'update' => 'bg-yellow-100 text-yellow-800',
                                        'delete' => 'bg-red-100 text-red-800',
                                        default => 'bg-gray-100 text-gray-800',
                                    };
                                @endphp
                                <span class="px-2 py-1 rounded-full text-xs font-medium {{ $badgeClass }}">
                                    {{ ucfirst($log->action) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ $log->model }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700 font-mono">
                                {{ $log->model_id ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                @if ($log->user_id)
                                    <span class="text-gray-700">{{ $log->user_name }}</span>
                                    <span class="text-gray-500 text-xs">#{{ $log->user_id }}</span>
                                @else
                                    <span class="text-gray-400 italic">System</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 font-mono">
                                {{ $log->request_ip ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <a href="{{ route('admin.activity-logs.show', $log->id) }}" class="text-blue-600 hover:text-blue-900 font-medium">
                                    View Details
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                <i data-lucide="inbox" class="h-12 w-12 inline-block mb-2 opacity-50"></i>
                                <p>No activity logs found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="bg-gray-50 border-t border-gray-200 px-6 py-4">
            {{ $activityLogs->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
