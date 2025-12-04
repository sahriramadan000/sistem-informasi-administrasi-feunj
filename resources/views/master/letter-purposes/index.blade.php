@extends('layouts.app')

@section('title', 'Master Keperluan Surat')

@section('content')
    {{-- Page Header --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                <i data-lucide="file-text" class="mr-3 h-7 w-7 text-brand"></i>
                Master Keperluan Surat
            </h2>
            <p class="mt-1 text-sm text-gray-500">Kelola keperluan surat untuk mahasiswa</p>
        </div>
        <a href="{{ route('master.letter-purposes.create') }}" 
           class="inline-flex items-center rounded-lg btn-success focus:ring-offset-2 transition-colors">
            <i data-lucide="plus-circle" class="mr-2 h-5 w-5"></i>
            Tambah Keperluan
        </a>
    </div>

    {{-- Table Section --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-base font-semibold text-gray-900">Daftar Keperluan</h3>
        </div>
        <div class="overflow-x-auto">
            @if($letterPurposes->count() > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">No</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Keperluan</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Deskripsi</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($letterPurposes as $index => $purpose)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ ($letterPurposes->currentPage() - 1) * $letterPurposes->perPage() + $index + 1 }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $purpose->name }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 hidden lg:table-cell">
                                {{ Str::limit($purpose->description, 60) ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold {{ $purpose->is_active ? 'bg-success-lighter text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $purpose->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('master.letter-purposes.edit', $purpose) }}" 
                                       class="inline-flex items-center rounded-md border border-brand px-3 py-1.5 text-sm font-medium text-brand hover:bg-orange-50 focus:outline-none focus:ring-2 focus:ring-brand focus:ring-offset-2 transition-colors">
                                        <i data-lucide="edit" class="mr-1 h-4 w-4"></i>
                                        Edit
                                    </a>
                                    <form method="POST" 
                                          action="{{ route('master.letter-purposes.destroy', $purpose) }}" 
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus keperluan ini?')"
                                          class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="inline-flex items-center rounded-md border border-red-600 px-3 py-1.5 text-sm font-medium text-red-600 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
                                            <i data-lucide="trash-2" class="mr-1 h-4 w-4"></i>
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Pagination --}}
                {{ $letterPurposes->links() }}
            @else
                <div class="text-center py-12">
                    <i data-lucide="file-text" class="mx-auto h-12 w-12 text-gray-400"></i>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada data keperluan</h3>
                    <p class="mt-1 text-sm text-gray-500">Belum ada keperluan surat yang terdaftar.</p>
                    <div class="mt-6">
                        <a href="{{ route('master.letter-purposes.create') }}" 
                           class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700">
                            <i data-lucide="plus-circle" class="mr-2 h-4 w-4"></i>
                            Tambah Keperluan
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        // Re-initialize Lucide icons
        lucide.createIcons();
    </script>
    @endpush
@endsection






