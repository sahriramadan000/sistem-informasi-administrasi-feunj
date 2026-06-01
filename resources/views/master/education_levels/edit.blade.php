@extends('layouts.app')

@section('title', 'Edit Jenjang Pendidikan')

@section('content')
<div class="max-w-4xl mx-auto">
    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i data-lucide="edit" class="w-6 h-6 text-blue-600"></i>
                Edit Jenjang Pendidikan
            </h2>
            <p class="mt-1 text-sm text-gray-500">Perbarui data jenjang pendidikan: {{ $educationLevel->name }}</p>
        </div>
        <a href="{{ route('master.education-levels.index') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali
        </a>
    </div>

    {{-- Form Section --}}
    <div class="bg-white rounded-lg shadow border border-gray-200">
        <div class="p-6">
            <form method="POST" action="{{ route('master.education-levels.update', $educationLevel) }}">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 gap-6">
                    {{-- Nama Jenjang --}}
                    <div class="space-y-2">
                        <label for="name" class="block text-sm font-medium text-gray-700">
                            Nama Jenjang <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $educationLevel->name) }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror" 
                               placeholder="Contoh: Sarjana (S1)"
                               required>
                        @error('name')
                            <p class="text-sm text-red-500 flex items-center gap-1.5 mt-1">
                                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Harga per Lembar --}}
                    <div class="space-y-2">
                        <label for="price_per_page" class="block text-sm font-medium text-gray-700">
                            Harga per Lembar (Rp) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               id="price_per_page" 
                               name="price_per_page" 
                               value="{{ old('price_per_page', $educationLevel->price_per_page) }}" 
                               min="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('price_per_page') border-red-500 @enderror" 
                               placeholder="Contoh: 5000"
                               required>
                        @error('price_per_page')
                            <p class="text-sm text-red-500 flex items-center gap-1.5 mt-1">
                                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                {{ $message }}
                            </p>
                        @enderror
                        <p class="text-sm text-gray-500 mt-1">Isi 0 jika gratis.</p>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="flex justify-between items-center pt-6 border-t border-gray-200 mt-6">
                    <a href="{{ route('master.education-levels.index') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        <i data-lucide="x-circle" class="w-4 h-4"></i>
                        Batal
                    </a>
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
