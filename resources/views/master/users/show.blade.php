@extends('layouts.app')

@section('title', 'Detail Pengguna - FEB UNJ')

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
@php
    $initials = collect(explode(' ', $user->name))
        ->filter()
        ->take(2)
        ->map(fn($w) => strtoupper(substr($w, 0, 1)))
        ->join('') ?: 'U';
@endphp

<div class="max-w-4xl mx-auto space-y-6 pb-12">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-gray-200/80">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#FF4D00] to-amber-500 flex items-center justify-center text-white font-extrabold text-base shadow-xs flex-shrink-0">
                {{ $initials }}
            </div>
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">{{ $user->name }}</h1>
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
                </div>
                <p class="text-xs sm:text-sm text-gray-500 mt-0.5">
                    <span class="font-mono font-semibold text-gray-700">{{ '@' . $user->username }}</span> &bull; {{ $user->email }}
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('master.users.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2 text-xs sm:text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-200 rounded-full shadow-2xs hover:shadow-xs transition-all active:scale-[0.98]">
                <i data-lucide="arrow-left" class="w-4 h-4 text-gray-500"></i>
                <span>Kembali</span>
            </a>
            <a href="{{ route('master.users.edit', $user) }}"
                class="inline-flex items-center gap-2 px-4.5 py-2 text-xs sm:text-sm font-bold text-white bg-gradient-to-r from-[#FF4D00] to-amber-500 hover:from-[#e04400] hover:to-[#d97706] rounded-full shadow-xs hover:shadow-md transition-all active:scale-[0.98]">
                <i data-lucide="edit-3" class="w-4 h-4"></i>
                <span>Edit Pengguna</span>
            </a>
        </div>
    </div>

    {{-- User Information Card --}}
    <div class="mac-card p-6 sm:p-7">
        <div class="flex items-center gap-2.5 pb-4 mb-5 border-b border-gray-100">
            <div class="w-8 h-8 rounded-xl bg-orange-50 border border-orange-200 flex items-center justify-center text-orange-600">
                <i data-lucide="id-card" class="w-4 h-4"></i>
            </div>
            <div>
                <h2 class="text-sm font-bold text-gray-900">Informasi Akun Pengguna</h2>
                <p class="text-[11px] text-gray-500">Detail profil, peran, dan waktu pembaruan akun</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="p-3.5 rounded-2xl bg-gray-50/80 border border-gray-100">
                <span class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Nama Lengkap</span>
                <p class="text-sm font-bold text-gray-900 mt-1">{{ $user->name }}</p>
            </div>

            <div class="p-3.5 rounded-2xl bg-gray-50/80 border border-gray-100">
                <span class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Username</span>
                <p class="text-sm font-mono font-bold text-[#FF4D00] mt-1">{{ $user->username }}</p>
            </div>

            <div class="p-3.5 rounded-2xl bg-gray-50/80 border border-gray-100">
                <span class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Email</span>
                <p class="text-sm font-semibold text-gray-900 mt-1 truncate" title="{{ $user->email }}">{{ $user->email }}</p>
            </div>

            <div class="p-3.5 rounded-2xl bg-gray-50/80 border border-gray-100">
                <span class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Role</span>
                <div class="mt-1.5">
                    @if($user->role === 'admin')
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-50 text-purple-700 border border-purple-200/70">
                            <i data-lucide="shield" class="w-3.5 h-3.5"></i> Admin
                        </span>
                    @elseif($user->role === 'operator')
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-orange-50 text-orange-700 border border-orange-200/70">
                            <i data-lucide="edit-3" class="w-3.5 h-3.5"></i> Operator
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-700 border border-gray-200/70">
                            <i data-lucide="eye" class="w-3.5 h-3.5"></i> Viewer
                        </span>
                    @endif
                </div>
            </div>

            <div class="p-3.5 rounded-2xl bg-gray-50/80 border border-gray-100">
                <span class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Tanggal Dibuat</span>
                <p class="text-sm font-semibold text-gray-900 mt-1">{{ $user->created_at->format('d F Y, H:i') }}</p>
            </div>

            <div class="p-3.5 rounded-2xl bg-gray-50/80 border border-gray-100">
                <span class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Terakhir Diupdate</span>
                <p class="text-sm font-semibold text-gray-900 mt-1">{{ $user->updated_at->format('d F Y, H:i') }}</p>
            </div>
        </div>
    </div>

    {{-- Hak Akses Modul & Role --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="mac-card p-6">
            <h3 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
                <i data-lucide="layers" class="w-4 h-4 text-[#FF4D00]"></i>
                Hak Akses Modul
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                @php
                    $modules = [
                        ['label' => 'Persuratan', 'allowed' => $user->can_access_letters || $user->isAdmin(), 'icon' => 'mail'],
                        ['label' => 'Arsip Digital', 'allowed' => $user->can_access_archives || $user->isAdmin(), 'icon' => 'archive'],
                        ['label' => 'Legalisir', 'allowed' => $user->can_access_legalizations || $user->isAdmin(), 'icon' => 'stamp'],
                        ['label' => 'Peminjaman Ruang', 'allowed' => $user->can_access_room_reservations || $user->isAdmin(), 'icon' => 'calendar'],
                        ['label' => 'Studio Sertifikat', 'allowed' => $user->canAccessCertificateStudio(), 'icon' => 'award'],
                    ];
                @endphp
                @foreach($modules as $mod)
                    <div class="flex items-center justify-between p-3 rounded-xl border {{ $mod['allowed'] ? 'bg-emerald-50/50 border-emerald-200/70' : 'bg-gray-50 border-gray-200/70 opacity-70' }}">
                        <span class="text-xs font-bold text-gray-800 flex items-center gap-2">
                            <i data-lucide="{{ $mod['icon'] }}" class="w-3.5 h-3.5 {{ $mod['allowed'] ? 'text-emerald-600' : 'text-gray-400' }}"></i>
                            {{ $mod['label'] }}
                        </span>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $mod['allowed'] ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-200 text-gray-600' }}">
                            {{ $mod['allowed'] ? 'Diizinkan' : 'Terkunci' }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mac-card p-6 bg-orange-50/30">
            <h3 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
                <i data-lucide="info" class="w-4 h-4 text-[#FF4D00]"></i>
                Cakupan Role ({{ ucfirst($user->role) }})
            </h3>
            <ul class="list-disc list-inside text-xs text-gray-700 space-y-1.5 leading-relaxed">
                @if($user->role === 'admin')
                    <li>Mengelola seluruh data master (Klasifikasi, Jenis Surat, Penandatangan, Pengguna)</li>
                    <li>Membuat, mengedit, dan menghapus data di semua modul</li>
                    <li>Melihat dashboard analitik, Activity Logs, dan Error Logs</li>
                @elseif($user->role === 'operator')
                    <li>Membuat dan mengelola transaksi pada modul yang diizinkan</li>
                    <li>Mengedit data yang dibuat sesuai wewenang operasional</li>
                    <li>Tidak memiliki akses ke manajemen pengguna &amp; log sistem</li>
                @else
                    <li>Akses baca (read-only) untuk melihat data pada modul yang diizinkan</li>
                    <li>Tidak dapat menambah, mengubah, atau menghapus data</li>
                @endif
            </ul>
        </div>
    </div>

    {{-- Statistics Card (if available) --}}
    @if($user->letters()->exists())
        <div class="mac-card p-6">
            <div class="flex items-center gap-2.5 pb-4 mb-4 border-b border-gray-100">
                <div class="w-8 h-8 rounded-xl bg-blue-50 border border-blue-200/80 flex items-center justify-center text-blue-600">
                    <i data-lucide="bar-chart-2" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-gray-900">Statistik Kontribusi Surat</h3>
                    <p class="text-[11px] text-gray-500">Ringkasan jumlah surat yang dibuat oleh pengguna ini</p>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="p-4 rounded-2xl bg-orange-50/60 border border-orange-200/60 text-center">
                    <p class="text-2xl sm:text-3xl font-extrabold text-[#FF4D00]">{{ $user->letters()->count() }}</p>
                    <p class="text-xs font-medium text-gray-600 mt-1">Total Surat Dibuat</p>
                </div>
                <div class="p-4 rounded-2xl bg-emerald-50/60 border border-emerald-200/60 text-center">
                    <p class="text-2xl sm:text-3xl font-extrabold text-emerald-600">{{ $user->letters()->whereYear('created_at', date('Y'))->count() }}</p>
                    <p class="text-xs font-medium text-gray-600 mt-1">Surat Tahun Ini</p>
                </div>
                <div class="p-4 rounded-2xl bg-blue-50/60 border border-blue-200/60 text-center">
                    <p class="text-2xl sm:text-3xl font-extrabold text-blue-600">{{ $user->letters()->whereMonth('created_at', date('m'))->count() }}</p>
                    <p class="text-xs font-medium text-gray-600 mt-1">Surat Bulan Ini</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Action Buttons --}}
    @if($user->id !== auth()->id())
        <div class="flex items-center justify-end pt-2">
            <form method="POST"
                action="{{ route('master.users.destroy', $user) }}"
                onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?')">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200/80 text-xs font-bold rounded-xl transition-all cursor-pointer">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                    <span>Hapus Pengguna</span>
                </button>
            </form>
        </div>
    @endif
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
