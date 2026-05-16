@extends('layouts.app')

@section('title', 'Error Statistics')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <a href="{{ route('admin.error-logs.index') }}" class="text-blue-600 hover:text-blue-900 font-medium flex items-center gap-2 mb-4">
                <i data-lucide="arrow-left" class="h-4 w-4"></i>
                Back to Error Logs
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Error Statistics</h1>
        </div>
    </div>

    {{-- Statistics Overview --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <p class="text-sm text-gray-600 font-medium">Total Errors (14 days)</p>
            <p class="text-4xl font-bold text-blue-600 mt-2">{{ $stats['total'] }}</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
            <p class="text-sm text-gray-600 font-medium">Critical Errors</p>
            <p class="text-4xl font-bold text-red-600 mt-2">{{ $stats['critical'] }}</p>
            <p class="text-xs text-gray-500 mt-2">{{ round(($stats['critical'] / $stats['total'] * 100), 1) }}% of total</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
            <p class="text-sm text-gray-600 font-medium">Warning Errors</p>
            <p class="text-4xl font-bold text-yellow-600 mt-2">{{ $stats['warning'] }}</p>
            <p class="text-xs text-gray-500 mt-2">{{ round(($stats['warning'] / $stats['total'] * 100), 1) }}% of total</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
            <p class="text-sm text-gray-600 font-medium">Exception Types</p>
            <p class="text-4xl font-bold text-purple-600 mt-2">{{ $stats['unique_types'] }}</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <p class="text-sm text-gray-600 font-medium">Affected Users</p>
            <p class="text-4xl font-bold text-green-600 mt-2">{{ $stats['unique_users'] }}</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-indigo-500">
            <p class="text-sm text-gray-600 font-medium">Average Per Day</p>
            <p class="text-4xl font-bold text-indigo-600 mt-2">{{ $stats['avg_per_day'] }}</p>
        </div>
    </div>

    {{-- Top Errors --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Top 5 Most Occurring Errors</h3>
            
            @if (count($topErrors) > 0)
                <div class="space-y-3">
                    @foreach ($topErrors as $error => $count)
                        @php
                            $percentage = round(($count / $stats['total'] * 100), 1);
                        @endphp
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <p class="text-sm font-medium text-gray-700 truncate">{{ $error }}</p>
                                <span class="text-sm font-bold text-gray-900">{{ $count }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-red-500 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">{{ $percentage }}% of total</p>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-sm">No errors recorded</p>
            @endif
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Top 5 Users with Errors</h3>
            
            @if (count($topUsers) > 0)
                <div class="space-y-3">
                    @foreach ($topUsers as $user => $count)
                        @php
                            $percentage = round(($count / $stats['total'] * 100), 1);
                        @endphp
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <p class="text-sm font-medium text-gray-700 truncate">{{ $user }}</p>
                                <span class="text-sm font-bold text-gray-900">{{ $count }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">{{ $percentage }}% of total</p>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-sm">No user errors recorded</p>
            @endif
        </div>
    </div>

    {{-- Error Trend (Daily) --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Error Trend (Last 14 Days)</h3>
        
        <div class="overflow-x-auto">
            <div class="inline-flex gap-1 min-w-full">
                @foreach ($errorsByDay as $date => $count)
                    @php
                        $maxCount = max($errorsByDay);
                        $height = $maxCount > 0 ? ($count / $maxCount * 100) : 0;
                        $dateObj = \Carbon\Carbon::parse($date);
                        $isToday = $dateObj->isToday();
                    @endphp
                    <div class="flex-1 flex flex-col items-center gap-2 min-w-[3rem]">
                        <div 
                            class="w-full rounded-t {{ $isToday ? 'bg-blue-600' : 'bg-gray-300' }} hover:{{ $isToday ? 'bg-blue-700' : 'bg-gray-400' }} transition"
                            style="height: {{ max($height, 20) }}px;"
                            title="{{ $date }}: {{ $count }} errors">
                        </div>
                        <p class="text-xs text-gray-600 text-center">{{ $dateObj->format('M d') }}</p>
                        <p class="text-xs font-bold text-gray-900">{{ $count }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Error Distribution by Type --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Error Distribution</h3>
        
        <div class="space-y-2">
            <div>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-medium text-gray-700">Critical Errors</span>
                    <span class="text-sm font-bold text-red-600">{{ $stats['critical'] }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div class="bg-red-500 h-3 rounded-full" style="width: {{ $stats['total'] > 0 ? round(($stats['critical'] / $stats['total'] * 100), 1) : 0 }}%"></div>
                </div>
            </div>

            <div>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-medium text-gray-700">Warning Errors</span>
                    <span class="text-sm font-bold text-yellow-600">{{ $stats['warning'] }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div class="bg-yellow-500 h-3 rounded-full" style="width: {{ $stats['total'] > 0 ? round(($stats['warning'] / $stats['total'] * 100), 1) : 0 }}%"></div>
                </div>
            </div>

            <div>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-medium text-gray-700">Other Errors</span>
                    <span class="text-sm font-bold text-blue-600">{{ $stats['total'] - $stats['critical'] - $stats['warning'] }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div class="bg-blue-500 h-3 rounded-full" style="width: {{ $stats['total'] > 0 ? round((($stats['total'] - $stats['critical'] - $stats['warning']) / $stats['total'] * 100), 1) : 0 }}%"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Help Section --}}
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <div class="flex gap-4">
            <i data-lucide="info" class="h-6 w-6 text-blue-600 flex-shrink-0"></i>
            <div>
                <h4 class="font-semibold text-blue-900 mb-2">Understanding Error Statistics</h4>
                <ul class="text-sm text-blue-800 space-y-1">
                    <li>• <strong>Critical Errors:</strong> Fatal errors, parse errors, and type errors that require immediate attention</li>
                    <li>• <strong>Warning Errors:</strong> Notices and deprecation warnings that may indicate problems</li>
                    <li>• <strong>Error Trend:</strong> Visual representation of errors over the last 14 days</li>
                    <li>• <strong>Top Errors:</strong> Most frequently occurring error messages</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
