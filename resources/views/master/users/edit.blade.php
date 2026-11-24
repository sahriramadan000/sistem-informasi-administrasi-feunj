@extends('layouts.app')

@section('title', 'Edit Pengguna - FEB UNJ')

@push('styles')
    <style>
        .mac-card {
            background: #ffffff;
            border-radius: 1.5rem;
            border: 1px solid rgba(226, 232, 240, 0.85);
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.03), 0 2px 6px -1px rgba(0, 0, 0, 0.02);
        }
        .mac-step-badge {
            width: 36px;
            height: 36px;
            border-radius: 12px;
            background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%);
            border: 1px solid #fed7aa;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ea580c;
            font-weight: 700;
            font-size: 0.925rem;
            box-shadow: 0 2px 6px rgba(255, 77, 0, 0.08);
            flex-shrink: 0;
        }
        .perm-card {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            padding: 0.9rem 1rem;
            border-radius: 1rem;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .perm-card:hover {
            background: #fff7ed;
            border-color: #fed7aa;
        }
    </style>
@endpush

@section('content')
<div class="max-w-4xl mx-auto space-y-6 pb-12">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-gray-200/80">
        <div class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-[#FF4D00] to-amber-500 flex items-center justify-center text-white shadow-xs flex-shrink-0">
                <i data-lucide="user-cog" class="w-5 h-5"></i>
            </div>
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">Edit Pengguna</h1>
                <p class="text-xs sm:text-sm text-gray-500">Perbarui profil, peran, dan hak akses untuk <span class="font-semibold text-gray-700">{{ $user->name }}</span></p>
            </div>
        </div>

        <a href="{{ route('master.users.index') }}"
            class="inline-flex items-center gap-2 px-4 py-2 text-xs sm:text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-200 rounded-full shadow-2xs hover:shadow-xs transition-all active:scale-[0.98] self-start sm:self-auto">
            <i data-lucide="arrow-left" class="w-4 h-4 text-gray-500"></i>
            <span>Kembali</span>
        </a>
    </div>

    {{-- Form Card --}}
    <div class="mac-card overflow-hidden">
        <form method="POST" action="{{ route('master.users.update', $user) }}" class="p-6 sm:p-8 space-y-8">
            @csrf
            @method('PUT')

            {{-- Section 1: Informasi Akun --}}
            <div class="space-y-5">
                <div class="flex items-center gap-3 pb-3 border-b border-gray-100">
                    <div class="mac-step-badge">1</div>
                    <div>
                        <h2 class="text-base font-bold text-gray-900 tracking-tight">Informasi Identitas Akun</h2>
                        <p class="text-xs text-gray-500">Data nama lengkap, email resmi, dan username pengguna</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                    {{-- Nama --}}
                    <div class="sm:col-span-2 space-y-1.5">
                        <label for="name" class="text-xs font-semibold text-gray-700 flex items-center gap-1.5">
                            <i data-lucide="user" class="w-3.5 h-3.5 text-gray-400"></i>
                            Nama Lengkap <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                            class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-orange-500/10 focus:border-[#FF4D00] shadow-2xs transition-all placeholder:text-gray-400 @error('name') !border-rose-500 !ring-rose-500/10 @enderror"
                            placeholder="Contoh: Budi Santoso" required>
                        @error('name')
                            <p class="text-[11px] text-rose-600 flex items-center gap-1 mt-1">
                                <i data-lucide="alert-circle" class="w-3 h-3"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Username --}}
                    <div class="space-y-1.5">
                        <label for="username" class="text-xs font-semibold text-gray-700 flex items-center gap-1.5">
                            <i data-lucide="at-sign" class="w-3.5 h-3.5 text-gray-400"></i>
                            Username <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="username" name="username" value="{{ old('username', $user->username) }}"
                            class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-orange-500/10 focus:border-[#FF4D00] shadow-2xs transition-all placeholder:text-gray-400 @error('username') !border-rose-500 !ring-rose-500/10 @enderror"
                            placeholder="budisantoso" required>
                        @error('username')
                            <p class="text-[11px] text-rose-600 flex items-center gap-1 mt-1">
                                <i data-lucide="alert-circle" class="w-3 h-3"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="space-y-1.5">
                        <label for="email" class="text-xs font-semibold text-gray-700 flex items-center gap-1.5">
                            <i data-lucide="mail" class="w-3.5 h-3.5 text-gray-400"></i>
                            Email <span class="text-rose-500">*</span>
                        </label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                            class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-orange-500/10 focus:border-[#FF4D00] shadow-2xs transition-all placeholder:text-gray-400 @error('email') !border-rose-500 !ring-rose-500/10 @enderror"
                            placeholder="budi@unj.ac.id" required>
                        @error('email')
                            <p class="text-[11px] text-rose-600 flex items-center gap-1 mt-1">
                                <i data-lucide="alert-circle" class="w-3 h-3"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Section 2: Keamanan Password --}}
            <div class="space-y-5">
                <div class="flex items-center gap-3 pb-3 border-b border-gray-100">
                    <div class="mac-step-badge">2</div>
                    <div>
                        <h2 class="text-base font-bold text-gray-900 tracking-tight">Ubah Password <span class="text-xs font-normal text-gray-400">(Opsional)</span></h2>
                        <p class="text-xs text-gray-500">Biarkan kosong jika tidak ingin mengganti password saat ini</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                    {{-- Password --}}
                    <div class="space-y-1.5">
                        <label for="password" class="text-xs font-semibold text-gray-700 flex items-center gap-1.5">
                            <i data-lucide="lock" class="w-3.5 h-3.5 text-gray-400"></i>
                            Password Baru
                        </label>
                        <input type="password" id="password" name="password"
                            class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-orange-500/10 focus:border-[#FF4D00] shadow-2xs transition-all placeholder:text-gray-400 @error('password') !border-rose-500 !ring-rose-500/10 @enderror"
                            placeholder="Minimal 6 karakter">
                        @error('password')
                            <p class="text-[11px] text-rose-600 flex items-center gap-1 mt-1">
                                <i data-lucide="alert-circle" class="w-3 h-3"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div class="space-y-1.5">
                        <label for="password_confirmation" class="text-xs font-semibold text-gray-700 flex items-center gap-1.5">
                            <i data-lucide="shield-check" class="w-3.5 h-3.5 text-gray-400"></i>
                            Konfirmasi Password Baru
                        </label>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                            class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-orange-500/10 focus:border-[#FF4D00] shadow-2xs transition-all placeholder:text-gray-400 @error('password_confirmation') !border-rose-500 !ring-rose-500/10 @enderror"
                            placeholder="Ulangi password baru">
                        @error('password_confirmation')
                            <p class="text-[11px] text-rose-600 flex items-center gap-1 mt-1">
                                <i data-lucide="alert-circle" class="w-3 h-3"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Section 3: Role & Hak Akses Modul --}}
            <div class="space-y-5">
                <div class="flex items-center gap-3 pb-3 border-b border-gray-100">
                    <div class="mac-step-badge">3</div>
                    <div>
                        <h2 class="text-base font-bold text-gray-900 tracking-tight">Peran &amp; Hak Akses Modul</h2>
                        <p class="text-xs text-gray-500">Sesuaikan level peran, status aktif akun, dan modul yang diizinkan</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                    {{-- Role --}}
                    <div class="space-y-1.5">
                        <label for="role" class="text-xs font-semibold text-gray-700 flex items-center gap-1.5">
                            <i data-lucide="badge-check" class="w-3.5 h-3.5 text-gray-400"></i>
                            Role Pengguna <span class="text-rose-500">*</span>
                        </label>
                        <select id="role" name="role"
                            class="w-full px-3.5 py-2.5 text-xs sm:text-sm bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-orange-500/10 focus:border-[#FF4D00] shadow-2xs transition-all cursor-pointer @error('role') !border-rose-500 !ring-rose-500/10 @enderror"
                            required>
                            <option value="">-- Pilih Role --</option>
                            <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="operator" {{ old('role', $user->role) === 'operator' ? 'selected' : '' }}>Operator</option>
                            <option value="viewer" {{ old('role', $user->role) === 'viewer' ? 'selected' : '' }}>Viewer</option>
                        </select>
                        @error('role')
                            <p class="text-[11px] text-rose-600 flex items-center gap-1 mt-1">
                                <i data-lucide="alert-circle" class="w-3 h-3"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Status Aktif --}}
                    <div class="space-y-1.5">
                        <span class="text-xs font-semibold text-gray-700 flex items-center gap-1.5">
                            <i data-lucide="power" class="w-3.5 h-3.5 text-gray-400"></i>
                            Status Akun
                        </span>
                        <label for="is_active" class="perm-card !py-2.5">
                            <input type="checkbox" id="is_active" name="is_active" value="1"
                                {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                                class="mt-0.5 w-4 h-4 text-[#FF4D00] border-gray-300 rounded focus:ring-orange-500/30 cursor-pointer">
                            <div>
                                <span class="text-xs font-bold text-gray-900 block">Akun Aktif</span>
                                <span class="text-[11px] text-gray-500">Pengguna dapat login ke sistem</span>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Akses Modul --}}
                <div class="space-y-2">
                    <label class="text-xs font-semibold text-gray-700 block">Akses Modul <span class="text-rose-500">*</span></label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label for="can_access_letters" class="perm-card">
                            <input type="checkbox" id="can_access_letters" name="can_access_letters" value="1"
                                {{ old('can_access_letters', $user->can_access_letters) ? 'checked' : '' }}
                                class="mt-0.5 w-4 h-4 text-[#FF4D00] border-gray-300 rounded focus:ring-orange-500/30 cursor-pointer">
                            <div>
                                <span class="text-xs font-bold text-gray-900 block">Modul Persuratan</span>
                                <span class="text-[11px] text-gray-500">Fitur penomoran &amp; pengelolaan arsip surat</span>
                            </div>
                        </label>

                        <label for="can_access_archives" class="perm-card">
                            <input type="checkbox" id="can_access_archives" name="can_access_archives" value="1"
                                {{ old('can_access_archives', $user->can_access_archives) ? 'checked' : '' }}
                                class="mt-0.5 w-4 h-4 text-[#FF4D00] border-gray-300 rounded focus:ring-orange-500/30 cursor-pointer">
                            <div>
                                <span class="text-xs font-bold text-gray-900 block">Modul Arsip Digital</span>
                                <span class="text-[11px] text-gray-500">Akses lihat &amp; pratinjau arsip digital (read-only)</span>
                            </div>
                        </label>

                        <label for="can_access_legalizations" class="perm-card">
                            <input type="checkbox" id="can_access_legalizations" name="can_access_legalizations" value="1"
                                {{ old('can_access_legalizations', $user->can_access_legalizations) ? 'checked' : '' }}
                                class="mt-0.5 w-4 h-4 text-[#FF4D00] border-gray-300 rounded focus:ring-orange-500/30 cursor-pointer">
                            <div>
                                <span class="text-xs font-bold text-gray-900 block">Modul Legalisir</span>
                                <span class="text-[11px] text-gray-500">Fitur pencatatan &amp; laporan legalisir dokumen</span>
                            </div>
                        </label>

                        <label for="can_access_room_reservations" class="perm-card">
                            <input type="checkbox" id="can_access_room_reservations" name="can_access_room_reservations" value="1"
                                {{ old('can_access_room_reservations', $user->can_access_room_reservations) ? 'checked' : '' }}
                                class="mt-0.5 w-4 h-4 text-[#FF4D00] border-gray-300 rounded focus:ring-orange-500/30 cursor-pointer">
                            <div>
                                <span class="text-xs font-bold text-gray-900 block">Modul Peminjaman Ruang</span>
                                <span class="text-[11px] text-gray-500">Fitur reservasi jadwal &amp; peminjaman ruang</span>
                            </div>
                        </label>

                        <label for="can_access_certificates" class="perm-card">
                            <input type="checkbox" id="can_access_certificates" name="can_access_certificates" value="1"
                                {{ old('can_access_certificates', $user->can_access_certificates) ? 'checked' : '' }}
                                class="mt-0.5 w-4 h-4 text-[#FF4D00] border-gray-300 rounded focus:ring-orange-500/30 cursor-pointer">
                            <div>
                                <span class="text-xs font-bold text-gray-900 block">Fitur Studio Sertifikat</span>
                                <span class="text-[11px] text-gray-500">Akses khusus tempel nomor, TTD &amp; stempel pada sertifikat</span>
                            </div>
                        </label>

                        <label for="can_access_letter_studio" class="perm-card">
                            <input type="checkbox" id="can_access_letter_studio" name="can_access_letter_studio" value="1"
                                {{ old('can_access_letter_studio', $user->can_access_letter_studio) ? 'checked' : '' }}
                                class="mt-0.5 w-4 h-4 text-[#FF4D00] border-gray-300 rounded focus:ring-orange-500/30 cursor-pointer">
                            <div>
                                <span class="text-xs font-bold text-gray-900 block">Fitur Studio Surat (SIPERMAWA)</span>
                                <span class="text-[11px] text-gray-500">Akses khusus ganti kop surat, nomor, &amp; blok TTD pada surat Universitas</span>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Peringatan --}}
                <div class="rounded-2xl bg-amber-50/80 border border-amber-200/80 p-4">
                    <h6 class="text-xs font-bold text-amber-900 mb-2 flex items-center gap-1.5">
                        <i data-lucide="alert-triangle" class="w-4 h-4 text-amber-600"></i>
                        Catatan Perubahan
                    </h6>
                    <ul class="text-xs text-amber-800/90 space-y-1 list-disc list-inside">
                        <li>Password hanya akan diperbarui jika kolom password baru diisi</li>
                        <li>Menonaktifkan akun akan langsung mencegah pengguna login ke sistem</li>
                        <li>Mengubah role atau centang modul akan mengubah hak akses pengguna</li>
                    </ul>
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="flex items-center justify-between pt-6 border-t border-gray-100">
                <a href="{{ route('master.users.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-white hover:bg-gray-100 border border-gray-200 text-gray-700 font-semibold text-xs sm:text-sm rounded-xl shadow-2xs transition-all active:scale-[0.98]">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    <span>Batal</span>
                </a>
                <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-[#FF4D00] to-amber-500 hover:from-[#e04400] hover:to-[#d97706] text-white font-bold text-xs sm:text-sm rounded-xl shadow-xs hover:shadow-md transition-all active:scale-[0.98] cursor-pointer">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>

    {{-- Separate Delete / Deactivate Zone --}}
    @if($user->id !== auth()->id())
        <div class="mac-card p-6 border-rose-200/80 bg-rose-50/20">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h3 class="text-sm font-bold text-rose-900 flex items-center gap-2">
                        <i data-lucide="trash-2" class="w-4 h-4 text-rose-600"></i>
                        Hapus / Nonaktifkan Pengguna
                    </h3>
                    <p class="text-xs text-gray-600 mt-1">
                        Setelah pengguna dihapus atau dinonaktifkan, akses akun ini ke seluruh modul akan dihentikan.
                    </p>
                </div>
                <form method="POST"
                    action="{{ route('master.users.destroy', $user) }}"
                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini? Tindakan ini tidak dapat dibatalkan.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl shadow-2xs transition-all active:scale-[0.98] cursor-pointer whitespace-nowrap">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                        <span>Hapus Pengguna</span>
                    </button>
                </form>
            </div>
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
