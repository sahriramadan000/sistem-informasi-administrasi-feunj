@extends('layouts.app')

@section('title', 'Master Penandatangan')

@section('content')
    {{-- Page Header --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                <i data-lucide="user-check" class="mr-3 h-7 w-7 text-brand"></i>
                Master Penandatangan
            </h2>
            <p class="mt-1 text-sm text-gray-500">Kelola penandatangan surat resmi</p>
        </div>
        <a href="{{ route('master.signatories.create') }}" 
           class="inline-flex items-center rounded-lg btn-success focus:ring-offset-2 transition-colors">
            <i data-lucide="plus-circle" class="mr-2 h-5 w-5"></i>
            Tambah Penandatangan
        </a>
    </div>

    {{-- Search Section --}}
    <div class="mb-6">
        <form method="GET" action="{{ route('master.signatories.index') }}">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i data-lucide="search" class="h-5 w-5 text-gray-400"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}"
                    class="block w-full pl-12 pr-32 h-12 border border-gray-200 rounded-lg focus:ring-2 focus:ring-brand focus:border-transparent text-sm bg-white"
                    placeholder="Cari berdasarkan kode, nama, jabatan, atau NIP...">
                <div class="absolute inset-y-0 right-0 flex items-center pr-2 gap-2">
                    @if (request('search'))
                        <a href="{{ route('master.signatories.index') }}"
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
                <h3 class="text-base font-semibold text-gray-900">Daftar Penandatangan</h3>
                @if ($signatories->count() > 0)
                    <span class="text-sm text-gray-500">
                        {{ $signatories->total() }} penandatangan ditemukan
                        @if(request('search'))
                            <span class="ml-2 inline-flex items-center rounded-full bg-brand-lighter px-2.5 py-0.5 text-xs font-medium text-brand-dark">
                                <i data-lucide="search" class="mr-1 h-3 w-3"></i>
                                Pencarian: "{{ request('search') }}"
                            </span>
                        @endif
                    </span>
                @endif
            </div>
        </div>
        <div class="overflow-x-auto">
            @if($signatories->count() > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">No</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Penandatangan</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Jabatan</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden xl:table-cell">NIP</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($signatories as $index => $signatory)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ ($signatories->currentPage() - 1) * $signatories->perPage() + $index + 1 }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <code class="px-2 py-1 text-sm font-mono bg-gray-100 text-brand rounded">{{ $signatory->code }}</code>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-brand-lighter flex items-center justify-center">
                                            <span class="text-sm font-medium text-brand">{{ substr($signatory->name, 0, 2) }}</span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $signatory->name }}</div>
                                        <div class="text-sm text-gray-500 lg:hidden">{{ $signatory->position }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 hidden lg:table-cell">
                                {{ $signatory->position }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden xl:table-cell">
                                {{ $signatory->nip ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold {{ $signatory->is_active ? 'bg-success-lighter text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $signatory->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('master.signatories.edit', $signatory) }}" 
                                       class="inline-flex items-center rounded-md border border-brand px-3 py-1.5 text-sm font-medium text-brand hover:bg-orange-50 focus:outline-none focus:ring-2 focus:ring-brand focus:ring-offset-2 transition-colors">
                                        <i data-lucide="edit" class="mr-1 h-4 w-4"></i>
                                        Edit
                                    </a>
                                    @if($signatory->is_active)
                                    <form method="POST" 
                                          action="{{ route('master.signatories.destroy', $signatory) }}" 
                                          onsubmit="return confirm('Apakah Anda yakin ingin menonaktifkan penandatangan ini? Data tidak akan dihapus dari database.')"
                                          class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="inline-flex items-center rounded-md border border-red-600 px-3 py-1.5 text-sm font-medium text-red-600 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
                                            <i data-lucide="x-circle" class="mr-1 h-4 w-4"></i>
                                            Nonaktifkan
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Pagination --}}
                <div class="px-6 py-4">
                    {{ $signatories->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <i data-lucide="user-check" class="mx-auto h-12 w-12 text-gray-400"></i>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada data penandatangan</h3>
                    <p class="mt-1 text-sm text-gray-500">Belum ada penandatangan yang terdaftar dalam sistem.</p>
                    <div class="mt-6">
                        <a href="{{ route('master.signatories.create') }}" 
                           class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700">
                            <i data-lucide="plus-circle" class="mr-2 h-4 w-4"></i>
                            Tambah Penandatangan
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        // Re-initialize Lucide icons on page load
        document.addEventListener('DOMContentLoaded', function() {
            lucide.createIcons();
        });
    </script>
    @endpush
@endsection






