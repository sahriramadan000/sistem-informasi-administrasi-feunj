@extends('layouts.app')

@section('title', 'Edit Master Ruangan')

@section('content')
    <div class="max-w-4xl mx-auto">
        {{-- Header --}}
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                    <i data-lucide="edit" class="w-6 h-6 text-brand"></i>
                    Edit Master Ruangan
                </h2>
                <p class="mt-1 text-sm text-gray-500">Perbarui data ruangan dalam sistem</p>
            </div>
            <a href="{{ route('master.rooms.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Kembali
            </a>
        </div>



        {{-- Form Section --}}
        <div class="bg-white rounded-lg shadow border border-gray-200">
            <div class="p-8">
                <form id="room-form" action="{{ route('master.rooms.update', $room) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    {{-- SECTION 1: Informasi Dasar --}}
                    <div class="mb-8">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="flex items-center justify-center w-10 h-10 rounded-full bg-brand-lighter">
                                <span class="text-brand font-bold text-lg">1</span>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Informasi Ruangan</h3>
                                <p class="text-sm text-gray-500">Ubah detail ruangan beserta kapasitasnya</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pl-0 md:pl-13">
                            
                            {{-- Nama Ruangan --}}
                            <div class="space-y-2 md:col-span-1">
                                <label for="name" class="label flex items-center gap-2">
                                    <i data-lucide="box" class="w-4 h-4 text-gray-400"></i>
                                    Nama Ruangan <span class="text-destructive">*</span>
                                </label>
                                <input type="text" name="name" id="name" required value="{{ old('name', $room->name) }}"
                                    class="input @error('name') !border-red-500 @enderror"
                                    placeholder="Misal: Ruang Sidang Utama">
                                @error('name')
                                    <p class="text-sm text-destructive flex items-center gap-1.5">
                                        <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Grup Ruangan --}}
                            <div class="space-y-2 md:col-span-1">
                                <label for="room_group_id" class="label flex items-center gap-2">
                                    <i data-lucide="folder" class="w-4 h-4 text-gray-400"></i>
                                    Grup Ruangan
                                </label>
                                <select id="room_group_id" name="room_group_id" class="select @error('room_group_id') !border-red-500 @enderror">
                                    <option value="">-- Tidak ada grup --</option>
                                    @foreach($roomGroups as $group)
                                        <option value="{{ $group->id }}" {{ old('room_group_id', $room->room_group_id) == $group->id ? 'selected' : '' }}>
                                            {{ $group->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('room_group_id')
                                    <p class="text-sm text-destructive flex items-center gap-1.5">
                                        <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Kapasitas --}}
                            <div class="space-y-2">
                                <label for="capacity" class="label flex items-center gap-2">
                                    <i data-lucide="users" class="w-4 h-4 text-gray-400"></i>
                                    Kapasitas (Orang)
                                </label>
                                <input type="number" name="capacity" id="capacity" min="1" value="{{ old('capacity', $room->capacity) }}"
                                    class="input @error('capacity') !border-red-500 @enderror"
                                    placeholder="Misal: 50">
                                @error('capacity')
                                    <p class="text-sm text-destructive flex items-center gap-1.5">
                                        <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Status Aktif --}}
                            <div class="space-y-2">
                                <label for="is_active" class="label flex items-center gap-2">
                                    <i data-lucide="activity" class="w-4 h-4 text-gray-400"></i>
                                    Status Ruangan <span class="text-destructive">*</span>
                                </label>
                                <select id="is_active" name="is_active"
                                    class="select @error('is_active') !border-red-500 @enderror" required>
                                    <option value="1" {{ old('is_active', $room->is_active) == '1' ? 'selected' : '' }}>Aktif (Tersedia untuk dipinjam)</option>
                                    <option value="0" {{ old('is_active', $room->is_active) == '0' ? 'selected' : '' }}>Nonaktif (Sedang renovasi / Tidak tersedia)</option>
                                </select>
                                @error('is_active')
                                    <p class="text-sm text-destructive flex items-center gap-1.5">
                                        <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                        </div>
                    </div>

                    {{-- Divider --}}
                    <div class="border-t border-gray-200 my-8"></div>

                    {{-- SECTION 2: Detail Tambahan --}}
                    <div class="mb-8">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="flex items-center justify-center w-10 h-10 rounded-full bg-brand-lighter">
                                <span class="text-brand font-bold text-lg">2</span>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Detail Tambahan</h3>
                                <p class="text-sm text-gray-500">Tuliskan fasilitas dan kelengkapan ruangan</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pl-0 md:pl-13">
                            {{-- Deskripsi --}}
                            <div class="space-y-2 md:col-span-2">
                                <label for="description" class="label flex items-center gap-2">
                                    <i data-lucide="info" class="w-4 h-4 text-gray-400"></i>
                                    Deskripsi / Fasilitas (Opsional)
                                </label>
                                <textarea id="description" name="description" rows="4"
                                    class="input @error('description') !border-red-500 @enderror"
                                    placeholder="Masukkan deskripsi singkat atau fasilitas ruangan (opsional)">{{ old('description', $room->description) }}</textarea>
                                @error('description')
                                    <p class="text-sm text-destructive flex items-center gap-1.5">
                                        <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                                <p class="text-xs text-gray-500 flex items-start gap-1.5 mt-1">
                                    <i data-lucide="info" class="w-3.5 h-3.5 mt-0.5 text-blue-500"></i>
                                    <span>Bisa mencantumkan fasilitas seperti Proyektor, AC, Papan Tulis, dll.</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Form Actions --}}
                    <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200">
                        <a href="{{ route('master.rooms.index') }}"
                            class="px-6 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition-colors">
                            Batal
                        </a>
                        <button type="submit"
                            class="px-6 py-2.5 bg-brand hover:bg-brand-dark text-white rounded-lg font-medium shadow-sm shadow-brand/30 transition-all flex items-center gap-2">
                            <i data-lucide="save" class="w-4 h-4"></i>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            lucide.createIcons();
        });
    </script>
    @endpush
@endsection
