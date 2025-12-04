@extends('layouts.app')

@section('title', 'Master Jenis Surat')

@section('content')
    {{-- Page Header --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                <i data-lucide="file-text" class="mr-3 h-7 w-7 text-brand"></i>
                Master Jenis Surat
            </h2>
            <p class="mt-1 text-sm text-gray-500">Kelola jenis-jenis surat yang tersedia</p>
        </div>
        <a href="{{ route('master.letter-types.create') }}" 
           class="inline-flex items-center rounded-lg btn-success focus:ring-offset-2 transition-colors">
            <i data-lucide="plus-circle" class="mr-2 h-5 w-5"></i>
            Tambah Jenis Surat
        </a>
    </div>

    {{-- Table Section --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-base font-semibold text-gray-900">Daftar Jenis Surat</h3>
        </div>
        <div class="overflow-x-auto">
            @if($letterTypes->count() > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">No</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Jenis Surat</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Deskripsi</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keperluan Surat</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($letterTypes as $index => $letterType)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ ($letterTypes->currentPage() - 1) * $letterTypes->perPage() + $index + 1 }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($letterType->code)
                                    <code class="px-2 py-1 text-sm font-mono bg-gray-100 text-brand rounded">{{ $letterType->code }}</code>
                                @else
                                    <span class="text-gray-400 text-sm">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $letterType->name }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 hidden lg:table-cell">
                                {{ Str::limit($letterType->description, 60) ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" 
                                           class="sr-only peer toggle-requires-purpose" 
                                           data-id="{{ $letterType->id }}"
                                           {{ $letterType->requires_purpose ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                    <span class="ms-3 text-sm font-medium text-gray-700">{{ $letterType->requires_purpose ? 'Ya' : 'Tidak' }}</span>
                                </label>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold {{ $letterType->is_active ? 'bg-success-lighter text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $letterType->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('master.letter-types.edit', $letterType) }}" 
                                       class="inline-flex items-center rounded-md border border-brand px-3 py-1.5 text-sm font-medium text-brand hover:bg-orange-50 focus:outline-none focus:ring-2 focus:ring-brand focus:ring-offset-2 transition-colors">
                                        <i data-lucide="edit" class="mr-1 h-4 w-4"></i>
                                        Edit
                                    </a>
                                    <form method="POST" 
                                          action="{{ route('master.letter-types.destroy', $letterType) }}" 
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus jenis surat ini?')"
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
                {{ $letterTypes->links() }}
            @else
                <div class="text-center py-12">
                    <i data-lucide="file-text" class="mx-auto h-12 w-12 text-gray-400"></i>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada data jenis surat</h3>
                    <p class="mt-1 text-sm text-gray-500">Belum ada jenis surat yang terdaftar dalam sistem.</p>
                    <div class="mt-6">
                        <a href="{{ route('master.letter-types.create') }}" 
                           class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700">
                            <i data-lucide="plus-circle" class="mr-2 h-4 w-4"></i>
                            Tambah Jenis Surat
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

        // Handle toggle requires_purpose
        document.addEventListener('DOMContentLoaded', function() {
            const toggles = document.querySelectorAll('.toggle-requires-purpose');
            
            toggles.forEach(toggle => {
                toggle.addEventListener('change', function() {
                    const letterTypeId = this.dataset.id;
                    const isChecked = this.checked;
                    const label = this.nextElementSibling.nextElementSibling;
                    
                    // Update label text immediately for better UX
                    label.textContent = isChecked ? 'Ya' : 'Tidak';
                    
                    // Send AJAX request
                    fetch(`/master/letter-types/${letterTypeId}/toggle-requires-purpose`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            requires_purpose: isChecked
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Show success notification (optional)
                            console.log('Toggle berhasil diupdate');
                        } else {
                            // Revert toggle if failed
                            this.checked = !isChecked;
                            label.textContent = !isChecked ? 'Ya' : 'Tidak';
                            alert('Gagal mengupdate status. Silakan coba lagi.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        // Revert toggle on error
                        this.checked = !isChecked;
                        label.textContent = !isChecked ? 'Ya' : 'Tidak';
                        alert('Terjadi kesalahan. Silakan coba lagi.');
                    });
                });
            });
        });
    </script>
    @endpush
@endsection






