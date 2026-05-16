@extends('layouts.app')

@section('title', 'Error Logs Dashboard')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Error Logs Dashboard</h1>
            <p class="mt-2 text-sm text-gray-600">Monitor and manage system errors (last 14 days)</p>
        </div>
        <a href="{{ route('admin.error-logs.statistics') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            <i data-lucide="bar-chart-3" class="h-5 w-5 mr-2"></i>
            View Statistics
        </a>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
            <p class="text-sm text-gray-600 font-medium">Total Errors</p>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['total'] }}</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-red-500">
            <p class="text-sm text-gray-600 font-medium">Critical</p>
            <p class="text-3xl font-bold text-red-600">{{ $stats['critical'] }}</p>
        </div>

        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-yellow-500">
            <p class="text-sm text-gray-600 font-medium">Warnings</p>
            <p class="text-3xl font-bold text-yellow-600">{{ $stats['warning'] }}</p>
        </div>

        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-purple-500">
            <p class="text-sm text-gray-600 font-medium">Exception Types</p>
            <p class="text-3xl font-bold text-purple-600">{{ $stats['unique_types'] }}</p>
        </div>

        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
            <p class="text-sm text-gray-600 font-medium">Users Affected</p>
            <p class="text-3xl font-bold text-green-600">{{ $stats['unique_users'] }}</p>
        </div>

        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-indigo-500">
            <p class="text-sm text-gray-600 font-medium">Avg/Day</p>
            <p class="text-3xl font-bold text-indigo-600">{{ $stats['avg_per_day'] }}</p>
        </div>
    </div>

    {{-- Filter Section --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Filters</h3>
        
        <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Search --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search (Error ID or Message)</label>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}"
                    placeholder="ERR_202605... or error message"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            {{-- Exception Type --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Exception Type</label>
                <select name="exception_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Types</option>
                    @foreach ($exceptionTypes as $type)
                        <option value="{{ $type }}" {{ request('exception_type') === $type ? 'selected' : '' }}>
                            {{ $type }}
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

            {{-- Context --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Context</label>
                <input 
                    type="text" 
                    name="context" 
                    value="{{ request('context') }}"
                    placeholder="e.g., LetterController.store"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            {{-- Buttons --}}
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                    <i data-lucide="search" class="h-4 w-4 inline mr-2"></i>
                    Search
                </button>
                <a href="{{ route('admin.error-logs.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                    <i data-lucide="x" class="h-4 w-4 inline"></i>
                </a>
            </div>
        </form>
    </div>

    {{-- Errors Table --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Error ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Context</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Message</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($errors as $error)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <code class="text-xs bg-gray-100 px-2 py-1 rounded text-gray-700">
                                    {{ $error['error_id'] }}
                                </code>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $isCritical = in_array($error['exception_type'], ['FatalError', 'ParseError', 'TypeError']);
                                    $badgeClass = $isCritical ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800';
                                @endphp
                                <span class="px-2 py-1 rounded-full text-xs font-medium {{ $badgeClass }}">
                                    {{ $error['exception_type'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ $error['context'] }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">
                                {{ substr($error['message'], 0, 50) }}{{ strlen($error['message']) > 50 ? '...' : '' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                @if ($error['user_id'])
                                    <span class="text-gray-700">{{ $error['user_name'] }}</span>
                                    <span class="text-gray-500 text-xs">#{{ $error['user_id'] }}</span>
                                @else
                                    <span class="text-gray-400 italic">System</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ \Carbon\Carbon::parse($error['timestamp'])->format('M d, Y H:i') }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <a href="{{ route('admin.error-logs.show', $error['error_id']) }}" class="text-blue-600 hover:text-blue-900 font-medium">
                                    View Details
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                <i data-lucide="inbox" class="h-12 w-12 inline-block mb-2 opacity-50"></i>
                                <p>No errors found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($totalPages > 1)
            <div class="bg-gray-50 border-t border-gray-200 px-6 py-4 flex justify-center">
                <nav class="flex items-center gap-2">
                    @if ($currentPage > 1)
                        <a href="{{ request()->fullUrlWithQuery(['page' => 1]) }}" class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-100">First</a>
                        <a href="{{ request()->fullUrlWithQuery(['page' => $currentPage - 1]) }}" class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-100">Previous</a>
                    @endif

                    <span class="text-gray-600">Page {{ $currentPage }} of {{ $totalPages }}</span>

                    @if ($currentPage < $totalPages)
                        <a href="{{ request()->fullUrlWithQuery(['page' => $currentPage + 1]) }}" class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-100">Next</a>
                        <a href="{{ request()->fullUrlWithQuery(['page' => $totalPages]) }}" class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-100">Last</a>
                    @endif
                </nav>
            </div>
        @endif
    </div>
</div>
@endsection
