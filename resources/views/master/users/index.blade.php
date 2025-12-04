@extends('layouts.app')

@section('title', 'Master Pengguna')

@section('content')
    {{-- Page Header --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                <i data-lucide="users" class="mr-3 h-7 w-7 text-brand"></i>
                Master Pengguna
            </h2>
            <p class="mt-1 text-sm text-gray-500">Kelola data pengguna sistem</p>
        </div>
        <a href="{{ route('master.users.create') }}" 
           class="inline-flex items-center rounded-lg btn-success focus:ring-offset-2 transition-colors">
            <i data-lucide="user-plus" class="mr-2 h-5 w-5"></i>
            Tambah Pengguna
        </a>
    </div>

    {{-- Table Section --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-base font-semibold text-gray-900">Daftar Pengguna</h3>
        </div>
        <div class="overflow-x-auto">
            @if($users->count() > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">No</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Email</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($users as $index => $user)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ ($users->currentPage() - 1) * $users->perPage() + $index + 1 }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <code class="px-2 py-1 text-sm font-mono bg-gray-100 text-brand rounded">{{ $user->username }}</code>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 hidden lg:table-cell">
                                {{ $user->email }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($user->role === 'admin')
                                    <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold bg-info-lighter text-purple-800">
                                        Admin
                                    </span>
                                @elseif($user->role === 'operator')
                                    <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold bg-brand-lighter text-brand-darker">
                                        Operator
                                    </span>
                                @else
                                    <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold bg-gray-100 text-gray-800">
                                        Viewer
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold {{ $user->is_active ? 'bg-success-lighter text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('master.users.show', $user) }}" 
                                       class="inline-flex items-center rounded-md border border-green-600 px-3 py-1.5 text-sm font-medium text-success hover:bg-green-50 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors">
                                        <i data-lucide="eye" class="mr-1 h-4 w-4"></i>
                                        Detail
                                    </a>
                                    <a href="{{ route('master.users.edit', $user) }}" 
                                       class="inline-flex items-center rounded-md border border-brand px-3 py-1.5 text-sm font-medium text-brand hover:bg-orange-50 focus:outline-none focus:ring-2 focus:ring-brand focus:ring-offset-2 transition-colors">
                                        <i data-lucide="edit" class="mr-1 h-4 w-4"></i>
                                        Edit
                                    </a>
                                    @if($user->id !== auth()->id())
                                    <form method="POST" 
                                          action="{{ route('master.users.destroy', $user) }}" 
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?')"
                                          class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="inline-flex items-center rounded-md border border-red-600 px-3 py-1.5 text-sm font-medium text-red-600 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
                                            <i data-lucide="trash-2" class="mr-1 h-4 w-4"></i>
                                            Hapus
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
                {{ $users->links() }}
            @else
                <div class="text-center py-12">
                    <i data-lucide="users" class="mx-auto h-12 w-12 text-gray-400"></i>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada data pengguna</h3>
                    <p class="mt-1 text-sm text-gray-500">Belum ada pengguna yang terdaftar dalam sistem.</p>
                    <div class="mt-6">
                        <a href="{{ route('master.users.create') }}" 
                           class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700">
                            <i data-lucide="user-plus" class="mr-2 h-4 w-4"></i>
                            Tambah Pengguna
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






