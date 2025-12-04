@extends('layouts.app')

@section('title', 'Detail Pengguna')

@section('content')
<div class="max-w-4xl mx-auto">
    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i data-lucide="user" class="w-6 h-6 text-brand"></i>
                Detail Pengguna
            </h2>
            <p class="mt-1 text-sm text-gray-500">Informasi lengkap pengguna</p>
        </div>
        <a href="{{ route('master.users.index') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali
        </a>
    </div>

    {{-- User Information Card --}}
    <div class="bg-white rounded-lg shadow border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-900">Informasi Pengguna</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Nama Lengkap --}}
                <div>
                    <label class="text-sm font-medium text-gray-500 mb-1 block">Nama Lengkap</label>
                    <p class="text-base text-gray-900 font-semibold">{{ $user->name }}</p>
                </div>

                {{-- Username --}}
                <div>
                    <label class="text-sm font-medium text-gray-500 mb-1 block">Username</label>
                    <p class="text-base text-gray-900">
                        <code class="px-2 py-1 text-sm font-mono bg-gray-100 text-brand rounded">{{ $user->username }}</code>
                    </p>
                </div>

                {{-- Email --}}
                <div>
                    <label class="text-sm font-medium text-gray-500 mb-1 block">Email</label>
                    <p class="text-base text-gray-900">{{ $user->email }}</p>
                </div>

                {{-- Role --}}
                <div>
                    <label class="text-sm font-medium text-gray-500 mb-1 block">Role</label>
                    <div>
                        @if($user->role === 'admin')
                            <span class="inline-flex rounded-full px-3 py-1 text-sm font-semibold bg-info-lighter text-purple-800">
                                <i data-lucide="shield" class="w-4 h-4 mr-1"></i>
                                Admin
                            </span>
                        @elseif($user->role === 'operator')
                            <span class="inline-flex rounded-full px-3 py-1 text-sm font-semibold bg-brand-lighter text-brand-darker">
                                <i data-lucide="edit" class="w-4 h-4 mr-1"></i>
                                Operator
                            </span>
                        @else
                            <span class="inline-flex rounded-full px-3 py-1 text-sm font-semibold bg-gray-100 text-gray-800">
                                <i data-lucide="eye" class="w-4 h-4 mr-1"></i>
                                Viewer
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Status --}}
                <div>
                    <label class="text-sm font-medium text-gray-500 mb-1 block">Status</label>
                    <div>
                        <span class="inline-flex rounded-full px-3 py-1 text-sm font-semibold {{ $user->is_active ? 'bg-success-lighter text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            @if($user->is_active)
                                <i data-lucide="check-circle" class="w-4 h-4 mr-1"></i>
                                Aktif
                            @else
                                <i data-lucide="x-circle" class="w-4 h-4 mr-1"></i>
                                Nonaktif
                            @endif
                        </span>
                    </div>
                </div>

                {{-- Tanggal Dibuat --}}
                <div>
                    <label class="text-sm font-medium text-gray-500 mb-1 block">Tanggal Dibuat</label>
                    <p class="text-base text-gray-900">{{ $user->created_at->format('d F Y, H:i') }}</p>
                </div>

                {{-- Terakhir Diupdate --}}
                <div>
                    <label class="text-sm font-medium text-gray-500 mb-1 block">Terakhir Diupdate</label>
                    <p class="text-base text-gray-900">{{ $user->updated_at->format('d F Y, H:i') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Role Description Card --}}
    <div class="bg-orange-50 border border-blue-200 rounded-lg p-4 mb-6">
        <h6 class="font-semibold text-brand-darker mb-2 flex items-center gap-2">
            <i data-lucide="info" class="w-4 h-4"></i>
            Hak Akses Role: 
            @if($user->role === 'admin')
                Admin
            @elseif($user->role === 'operator')
                Operator
            @else
                Viewer
            @endif
        </h6>
        <ul class="list-disc list-inside text-sm text-brand-darker space-y-1">
            @if($user->role === 'admin')
                <li>Dapat mengelola semua data master (Klasifikasi, Jenis Surat, Penandatangan, Keperluan, Pengguna)</li>
                <li>Dapat membuat, mengedit, dan menghapus surat</li>
                <li>Dapat melihat semua laporan dan statistik</li>
                <li>Akses penuh ke seluruh sistem</li>
            @elseif($user->role === 'operator')
                <li>Dapat membuat surat baru</li>
                <li>Dapat mengedit surat yang dibuat sendiri</li>
                <li>Dapat melihat semua surat</li>
                <li>Tidak dapat mengelola data master dan pengguna</li>
            @else
                <li>Hanya dapat melihat daftar surat</li>
                <li>Tidak dapat membuat atau mengedit surat</li>
                <li>Tidak dapat mengelola data master</li>
                <li>Akses read-only ke sistem</li>
            @endif
        </ul>
    </div>

    {{-- Statistics Card (if available) --}}
    @if($user->letters()->exists())
    <div class="bg-white rounded-lg shadow border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-900">Statistik</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <p class="text-3xl font-bold text-brand">{{ $user->letters()->count() }}</p>
                    <p class="text-sm text-gray-500 mt-1">Total Surat Dibuat</p>
                </div>
                <div class="text-center">
                    <p class="text-3xl font-bold text-success">{{ $user->letters()->whereYear('created_at', date('Y'))->count() }}</p>
                    <p class="text-sm text-gray-500 mt-1">Surat Tahun Ini</p>
                </div>
                <div class="text-center">
                    <p class="text-3xl font-bold text-info">{{ $user->letters()->whereMonth('created_at', date('m'))->count() }}</p>
                    <p class="text-sm text-gray-500 mt-1">Surat Bulan Ini</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Action Buttons --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('master.users.edit', $user) }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            <i data-lucide="edit" class="w-4 h-4"></i>
            Edit Pengguna
        </a>
        
        @if($user->id !== auth()->id())
        <form method="POST" 
              action="{{ route('master.users.destroy', $user) }}" 
              onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?')"
              class="inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                <i data-lucide="trash-2" class="w-4 h-4"></i>
                Hapus Pengguna
            </button>
        </form>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/lucide@latest"></script>
<script>
    lucide.createIcons();
</script>
@endpush
@endsection





