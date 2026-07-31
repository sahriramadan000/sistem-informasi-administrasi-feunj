@extends('layouts.app')

@section('title', 'Master Data Grup Ruangan')

@section('content')
    {{-- Page Header --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                <i data-lucide="folder" class="icon-brand mr-3 h-7 w-7"></i>
                Master Data Grup Ruangan
            </h2>
            <p class="mt-1 text-sm text-gray-500">Kelola kategori/grup ruangan</p>
        </div>
        <div class="flex items-center gap-2">
            @if (auth()->user()->isAdmin())
                <a href="{{ route('master.room-groups.create') }}" 
                class="inline-flex items-center rounded-lg btn-success focus:ring-offset-2 transition-colors">
                    <i data-lucide="plus-circle" class="mr-2 h-5 w-5"></i>
                    Tambah Grup
                </a>
            @endif
        </div>
    </div>

    {{-- Search Section --}}
    <div class="mb-6">
        <form method="GET" action="{{ route('master.room-groups.index') }}">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i data-lucide="search" class="h-5 w-5 text-gray-400"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}"
                    class="block w-full pl-12 pr-32 h-12 border border-gray-200 rounded-lg focus:ring-2 focus:ring-brand focus:border-transparent text-sm bg-white"
                    placeholder="Cari berdasarkan nama atau deskripsi...">
                <div class="absolute inset-y-0 right-0 flex items-center pr-2 gap-2">
                    @if (request('search'))
                        <a href="{{ route('master.room-groups.index') }}"
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
                    <h3 class="text-base font-semibold text-gray-900">Daftar Grup Ruangan</h3>
                    @if(request('search'))
                        <span class="inline-flex items-center rounded-full bg-brand-lighter px-2.5 py-0.5 text-xs font-medium text-brand-dark">
                            <i data-lucide="search" class="mr-1 h-3 w-3"></i>
                            Pencarian: "{{ request('search') }}"
                        </span>
                    @endif
                </div>

                <div class="flex items-center gap-2">
                    <label for="per_page" class="text-sm text-gray-600">Tampilkan:</label>
                    <form method="GET" action="{{ route('master.room-groups.index') }}" class="inline-block m-0">
                        @if (request('search'))
                            <input type="hidden" name="search" value="{{ request('search') }}">
                        @endif
                        <select name="per_page" id="per_page" class="px-3 py-1 text-sm border border-gray-300 rounded-md bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-brand focus:ring-offset-0" onchange="this.form.submit()">
                            <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ request('per_page', 10) == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page', 10) == 50 ? 'selected' : '' }}>50</option>
                        </select>
                    </form>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            @if($roomGroups->count() > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">No</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Grup</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Deskripsi</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            @if (auth()->user()->isAdmin())
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($roomGroups as $index => $group)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ ($roomGroups->currentPage() - 1) * $roomGroups->perPage() + $index + 1 }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $group->name }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 hidden lg:table-cell max-w-md">
                                <div class="break-words whitespace-normal">
                                    {{ $group->description ?? '-' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold {{ $group->is_active ? 'bg-success-lighter text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $group->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            
                            @if (auth()->user()->isAdmin())
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('master.room-groups.edit', $group) }}" 
                                       class="inline-flex items-center rounded-md border border-brand px-3 py-1.5 text-sm font-medium text-brand hover:bg-orange-50 focus:outline-none focus:ring-2 focus:ring-brand focus:ring-offset-2 transition-colors">
                                        <i data-lucide="edit" class="mr-1 h-4 w-4"></i>
                                        Edit
                                    </a>
                                    <button type="button" onclick="showDeleteModal({{ $group->id }}, '{{ addslashes($group->name) }}')"
                                            class="inline-flex items-center rounded-md border border-red-600 px-3 py-1.5 text-sm font-medium text-red-600 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
                                        <i data-lucide="trash-2" class="mr-1 h-4 w-4"></i>
                                        Hapus
                                    </button>
                                </div>
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="px-6 py-4">
                    {{ $roomGroups->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <i data-lucide="folder" class="mx-auto h-12 w-12 text-gray-400"></i>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada data grup ruangan</h3>
                    <p class="mt-1 text-sm text-gray-500">Belum ada grup ruangan yang terdaftar.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div id="deleteModal" class="fixed inset-0 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" id="deleteModalContent">
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i data-lucide="alert-triangle" class="h-6 w-6 text-red-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <h3 class="text-lg font-semibold leading-6 text-gray-900" id="modal-title">Hapus Grup Ruangan</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Apakah Anda yakin ingin menghapus grup <span id="deleteRoomName" class="font-bold text-gray-900"></span>?
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <form id="deleteForm" method="POST" action="">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto transition-colors">Ya, Hapus</button>
                    </form>
                    <button type="button" onclick="hideDeleteModal()" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto transition-colors">Batal</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            lucide.createIcons();
            document.getElementById('deleteModal')?.addEventListener('click', function(e) {
                if (e.target === this || e.target.classList.contains('bg-gray-500')) hideDeleteModal();
            });
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && !document.getElementById('deleteModal').classList.contains('hidden')) hideDeleteModal();
            });
        });
        function showDeleteModal(id, name) {
            const modal = document.getElementById('deleteModal');
            const modalContent = document.getElementById('deleteModalContent');
            const form = document.getElementById('deleteForm');
            const nameSpan = document.getElementById('deleteRoomName');
            nameSpan.textContent = name;
            form.action = `/master/room-groups/${id}`;
            modal.classList.remove('hidden');
            setTimeout(() => {
                modalContent.classList.remove('opacity-0', 'translate-y-4', 'sm:translate-y-0', 'sm:scale-95');
                modalContent.classList.add('opacity-100', 'translate-y-0', 'sm:scale-100');
            }, 10);
        }
        function hideDeleteModal() {
            const modal = document.getElementById('deleteModal');
            const modalContent = document.getElementById('deleteModalContent');
            modalContent.classList.remove('opacity-100', 'translate-y-0', 'sm:scale-100');
            modalContent.classList.add('opacity-0', 'translate-y-4', 'sm:translate-y-0', 'sm:scale-95');
            setTimeout(() => { modal.classList.add('hidden'); }, 300);
        }
    </script>
    @endpush
@endsection
