@extends('layouts.app')

@section('title', 'Master Jenjang Pendidikan')

@section('content')
    {{-- Page Header --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                <i data-lucide="graduation-cap" class="mr-3 h-7 w-7 text-brand"></i>
                Master Jenjang Pendidikan
            </h2>
            <p class="mt-1 text-sm text-gray-500">Kelola master data jenjang untuk legalisir (Sarjana, Magister, dll)</p>
        </div>
        @if (auth()->user()->isAdmin() || auth()->user()->isOperator())
        <a href="{{ route('master.education-levels.create') }}" 
           class="inline-flex items-center rounded-lg btn-success focus:ring-offset-2 transition-colors">
            <i data-lucide="plus-circle" class="mr-2 h-5 w-5"></i>
            Tambah Jenjang
        </a>
        @endif
    </div>

    {{-- Search Section --}}
    <div class="mb-6">
        <form method="GET" action="{{ route('master.education-levels.index') }}">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i data-lucide="search" class="h-5 w-5 text-gray-400"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}"
                    class="block w-full pl-12 pr-32 h-12 border border-gray-200 rounded-lg focus:ring-2 focus:ring-brand focus:border-transparent text-sm bg-white"
                    placeholder="Cari berdasarkan nama jenjang...">
                <div class="absolute inset-y-0 right-0 flex items-center pr-2 gap-2">
                    @if (request('search'))
                        <a href="{{ route('master.education-levels.index') }}"
                            class="inline-flex items-center rounded-md px-3 py-1.5 text-xs font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition-colors">
                            <i data-lucide="x" class="h-3.5 w-3.5 mr-1"></i>
                            Clear
                        </a>
                    @endif
                    <button type="submit"
                        class="inline-flex items-center rounded-md bg-brand px-4 py-1.5 text-xs font-medium text-white hover:bg-brand-600 transition-colors">
                        <i data-lucide="search" class="h-3.5 w-3.5 mr-1.5"></i>
                        Cari
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Table Section --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <h3 class="text-base font-semibold text-gray-900">Daftar Jenjang Pendidikan</h3>
                    @if(request('search'))
                        <span class="inline-flex items-center rounded-full bg-brand-lighter px-2.5 py-0.5 text-xs font-medium text-brand-dark">
                            <i data-lucide="search" class="mr-1 h-3 w-3"></i>
                            Pencarian: "{{ request('search') }}"
                        </span>
                    @endif
                </div>

                {{-- Per Page Selector --}}
                <div class="flex items-center gap-2">
                    <label for="per_page" class="text-sm text-gray-600">Tampilkan:</label>
                    <form method="GET" action="{{ route('master.education-levels.index') }}" class="inline-block m-0">
                        @if (request('search'))
                            <input type="hidden" name="search" value="{{ request('search') }}">
                        @endif
                        <select name="per_page" id="per_page" class="px-3 py-1 text-sm border border-gray-300 rounded-md bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-brand focus:ring-offset-0" onchange="this.form.submit()">
                            <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ request('per_page', 10) == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page', 10) == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page', 10) == 100 ? 'selected' : '' }}>100</option>
                        </select>
                    </form>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            @if($educationLevels->count() > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">No</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Jenjang</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga per Lembar (Rp)</th>
                            @if (auth()->user()->isAdmin() || auth()->user()->isOperator())
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($educationLevels as $index => $level)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ ($educationLevels->currentPage() - 1) * $educationLevels->perPage() + $index + 1 }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $level->name }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                Rp {{ number_format($level->price_per_page, 0, ',', '.') }}
                            </td>
                            
                            @if (auth()->user()->isAdmin() || auth()->user()->isOperator())
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('master.education-levels.edit', $level) }}" 
                                       class="inline-flex items-center rounded-md border border-brand px-3 py-1.5 text-sm font-medium text-brand hover:bg-orange-50 focus:outline-none focus:ring-2 focus:ring-brand focus:ring-offset-2 transition-colors">
                                        <i data-lucide="edit" class="mr-1 h-4 w-4"></i>
                                        Edit
                                    </a>
                                    
                                    <form method="POST" 
                                          action="{{ route('master.education-levels.destroy', $level) }}" 
                                          onsubmit="return confirm('Apakah Anda yakin ingin menonaktifkan data jenjang ini?')"
                                          class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="inline-flex items-center rounded-md border border-red-600 px-3 py-1.5 text-sm font-medium text-red-600 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
                                            <i data-lucide="trash-2" class="mr-1 h-4 w-4"></i>
                                            Nonaktifkan
                                        </button>
                                    </form>
                                </div>
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Pagination --}}
                <div class="px-6 py-4">
                    {{ $educationLevels->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <i data-lucide="graduation-cap" class="mx-auto h-12 w-12 text-gray-400"></i>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada data jenjang</h3>
                    <p class="mt-1 text-sm text-gray-500">Belum ada data jenjang yang terdaftar.</p>
                    @if (auth()->user()->isAdmin() || auth()->user()->isOperator())
                    <div class="mt-6">
                        <a href="{{ route('master.education-levels.create') }}" 
                           class="inline-flex items-center rounded-lg bg-brand px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-brand-600">
                            <i data-lucide="plus-circle" class="mr-2 h-4 w-4"></i>
                            Tambah Jenjang
                        </a>
                    </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
@endsection
