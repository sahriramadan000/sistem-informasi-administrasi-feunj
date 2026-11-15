@extends('layouts.app')

@section('title', 'Master Pengguna - FEB UNJ')

@push('styles')
    <style>
        .mac-card {
            background: #ffffff;
            border-radius: 1.5rem;
            border: 1px solid rgba(226, 232, 240, 0.85);
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.03), 0 2px 6px -1px rgba(0, 0, 0, 0.02);
        }
    </style>
@endpush

@section('content')
<div class="space-y-6 pb-12">
    {{-- Page Header (Apple macOS Toolbar Style) --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-gray-200/80">
        <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-[#FF4D00] to-amber-500 flex items-center justify-center text-white shadow-xs flex-shrink-0">
                <i data-lucide="users" class="w-5 h-5"></i>
            </div>
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">Master Pengguna</h1>
                <p class="text-xs sm:text-sm text-gray-500">Kelola akun pengguna, peran (role), dan hak akses modul SIADMIN FEB</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('admin.dashboard') }}"
                class="inline-flex items-center gap-2 px-4 py-2 text-xs sm:text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-200 rounded-full shadow-2xs hover:shadow-xs transition-all active:scale-[0.98]">
                <i data-lucide="arrow-left" class="w-4 h-4 text-gray-500"></i>
                <span>Pengaturan</span>
            </a>
            <a href="{{ route('master.users.create') }}"
                class="inline-flex items-center gap-2 px-4.5 py-2 text-xs sm:text-sm font-bold text-white bg-gradient-to-r from-[#FF4D00] to-amber-500 hover:from-[#e04400] hover:to-[#d97706] rounded-full shadow-xs hover:shadow-md transition-all active:scale-[0.98]">
                <i data-lucide="user-plus" class="w-4 h-4"></i>
                <span>Tambah Pengguna</span>
            </a>
        </div>
    </div>

    {{-- Main Card: Search, Control Bar & Table --}}
    <div class="mac-card overflow-hidden">
        {{-- Search & Per-Page Control Bar --}}
        <div class="p-5 sm:px-6 border-b border-gray-100 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3.5">
            <form method="GET" action="{{ route('master.users.index') }}" class="flex-1 max-w-xl">
                @if (request('per_page'))
                    <input type="hidden" name="per_page" value="{{ request('per_page') }}">
                @endif
                <div class="relative flex items-center gap-2">
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i data-lucide="search" class="w-4 h-4 text-gray-400"></i>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="w-full pl-10 pr-20 py-2.5 text-xs sm:text-sm bg-gray-50/70 hover:bg-white focus:bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-orange-500/10 focus:border-[#FF4D00] transition-all placeholder:text-gray-400"
                            placeholder="Cari nama, username, atau email...">
                        @if (request('search'))
                            <a href="{{ route('master.users.index') }}"
                                class="absolute inset-y-0 right-2 flex items-center px-2 text-xs font-semibold text-gray-400 hover:text-gray-700"
                                title="Hapus pencarian">
                                <i data-lucide="x" class="w-4 h-4"></i>
                            </a>
                        @endif
                    </div>
                    <button type="submit"
                        class="inline-flex items-center gap-1.5 px-4 py-2.5 text-xs sm:text-sm font-bold text-white bg-gray-900 hover:bg-gray-800 rounded-xl shadow-2xs transition-all active:scale-[0.98] cursor-pointer">
                        <i data-lucide="search" class="w-3.5 h-3.5"></i>
                        <span>Cari</span>
                    </button>
                </div>
            </form>

            <div class="flex items-center justify-between lg:justify-end gap-3 text-xs text-gray-500">
                @if(request('search'))
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-orange-50 text-orange-700 border border-orange-200/70 font-semibold">
                        <i data-lucide="filter" class="w-3 h-3"></i>
                        "{{ request('search') }}"
                    </span>
                @endif

                <div class="flex items-center gap-2">
                    <label for="per_page" class="font-medium">Tampilkan:</label>
                    <form method="GET" action="{{ route('master.users.index') }}" class="inline-block m-0">
                        @if (request('search'))
                            <input type="hidden" name="search" value="{{ request('search') }}">
                        @endif
                        <select name="per_page" id="per_page"
                            class="px-2.5 py-1.5 text-xs font-semibold border border-gray-200 rounded-lg bg-gray-50/70 hover:bg-white focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-[#FF4D00] transition-colors cursor-pointer"
                            onchange="this.form.submit()">
                            <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ request('per_page', 10) == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page', 10) == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page', 10) == 100 ? 'selected' : '' }}>100</option>
                        </select>
                    </form>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            @if($users->count() > 0)
                <table class="min-w-full divide-y divide-gray-100 text-left">
                    <thead>
                        <tr class="bg-gray-50/60 border-b border-gray-100">
                            <th scope="col" class="pl-6 pr-3 py-3 text-center text-[11px] font-bold text-gray-500 uppercase tracking-wider w-12">No</th>
                            <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider">Pengguna</th>
                            <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider">Username</th>
                            <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider hidden lg:table-cell">Email</th>
                            <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider">Role</th>
                            <th scope="col" class="px-4 py-3 text-[11px] font-bold text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="pl-4 pr-6 py-3 text-right text-[11px] font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @foreach($users as $index => $user)
                            @php
                                $initials = collect(explode(' ', $user->name))
                                    ->filter()
                                    ->take(2)
                                    ->map(fn($w) => strtoupper(substr($w, 0, 1)))
                                    ->join('') ?: 'U';
                            @endphp
                            <tr class="hover:bg-orange-50/25 transition-colors group">
                                <td class="pl-6 pr-3 py-3.5 text-center text-xs font-semibold text-gray-400">
                                    {{ ($users->currentPage() - 1) * $users->perPage() + $index + 1 }}
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-orange-100 to-amber-100 border border-orange-200/70 text-orange-700 font-bold text-xs flex items-center justify-center flex-shrink-0">
                                            {{ $initials }}
                                        </div>
                                        <div>
                                            <a href="{{ route('master.users.show', $user) }}" class="text-xs sm:text-sm font-bold text-gray-900 group-hover:text-[#FF4D00] transition-colors">
                                                {{ $user->name }}
                                            </a>
                                            <p class="text-[11px] text-gray-500 lg:hidden">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <code class="px-2.5 py-1 text-xs font-mono font-semibold bg-gray-100/90 text-gray-800 rounded-lg border border-gray-200/60">
                                        {{ $user->username }}
                                    </code>
                                </td>
                                <td class="px-4 py-3.5 text-xs sm:text-sm text-gray-600 hidden lg:table-cell">
                                    {{ $user->email }}
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    @if($user->role === 'admin')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-purple-50 text-purple-700 border border-purple-200/70">
                                            Admin
                                        </span>
                                    @elseif($user->role === 'operator')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-orange-50 text-orange-700 border border-orange-200/70">
                                            Operator
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-gray-100 text-gray-700 border border-gray-200/70">
                                            Viewer
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    @if($user->is_active)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/70">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-gray-100 text-gray-500 border border-gray-200/70">
                                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="pl-4 pr-6 py-3.5 whitespace-nowrap text-right">
                                    <div class="inline-flex items-center gap-1.5">
                                        <a href="{{ route('master.users.show', $user) }}"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl text-[11px] font-semibold text-gray-700 bg-gray-100/80 hover:bg-gray-200/80 transition-colors"
                                            title="Detail Pengguna">
                                            <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                            <span>Detail</span>
                                        </a>
                                        <a href="{{ route('master.users.edit', $user) }}"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl text-[11px] font-semibold text-orange-700 bg-orange-50 hover:bg-orange-100 border border-orange-200/70 transition-colors"
                                            title="Edit Pengguna">
                                            <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                                            <span>Edit</span>
                                        </a>
                                        @if($user->id !== auth()->id() && $user->is_active)
                                            <form method="POST"
                                                action="{{ route('master.users.destroy', $user) }}"
                                                onsubmit="return confirm('Apakah Anda yakin ingin menonaktifkan pengguna ini? Data tidak akan dihapus dari database.')"
                                                class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl text-[11px] font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200/70 transition-colors cursor-pointer"
                                                    title="Nonaktifkan Pengguna">
                                                    <i data-lucide="user-x" class="w-3.5 h-3.5"></i>
                                                    <span>Nonaktifkan</span>
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
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/40">
                    {{ $users->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-14 px-6">
                    <div class="w-14 h-14 mx-auto rounded-2xl bg-gray-100 flex items-center justify-center text-gray-400 mb-3">
                        <i data-lucide="users" class="w-7 h-7"></i>
                    </div>
                    <h3 class="text-sm font-bold text-gray-900">Tidak ada data pengguna</h3>
                    <p class="mt-1 text-xs text-gray-500">Belum ada pengguna yang cocok dengan pencarian atau terdaftar di sistem.</p>
                    <div class="mt-5">
                        <a href="{{ route('master.users.create') }}"
                            class="inline-flex items-center gap-2 px-5 py-2.5 text-xs font-bold text-white bg-gradient-to-r from-[#FF4D00] to-amber-500 rounded-xl shadow-xs hover:shadow-md transition-all">
                            <i data-lucide="user-plus" class="w-4 h-4"></i>
                            <span>Tambah Pengguna</span>
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
@endpush
@endsection
