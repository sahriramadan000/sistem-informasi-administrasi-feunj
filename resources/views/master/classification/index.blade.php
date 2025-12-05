@extends('layouts.app')

@section('title', 'Master Klasifikasi Surat')

@section('content')
    {{-- Page Header --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                <i data-lucide="tags" class="icon-brand mr-3 h-7 w-7"></i>
                Master Klasifikasi Surat
            </h2>
            <p class="mt-1 text-sm text-gray-500">Kelola klasifikasi surat untuk penomoran otomatis</p>
        </div>
        <div class="flex items-center gap-2">
            {{-- Download Template Button --}}
            <button onclick="document.getElementById('download-modal').classList.remove('hidden')" 
                    class="inline-flex items-center rounded-lg btn-success focus:ring-offset-2 transition-colors">
                <i data-lucide="download" class="mr-2 h-5 w-5"></i>
                Download Template
            </button>
            
            {{-- Import Button --}}
            <button onclick="document.getElementById('import-modal').classList.remove('hidden')" 
                    class="inline-flex items-center rounded-lg btn-info focus:ring-offset-2 transition-colors">
                <i data-lucide="upload" class="mr-2 h-5 w-5"></i>
                Import Excel
            </button>
            
            {{-- Add Button --}}
            <a href="{{ route('master.classification-letters.create') }}" 
               class="inline-flex items-center rounded-lg btn-success focus:ring-offset-2 transition-colors">
                <i data-lucide="plus-circle" class="mr-2 h-5 w-5"></i>
                Tambah Klasifikasi
            </a>
        </div>
    </div>

    {{-- Search Section --}}
    <div class="mb-6">
        <form method="GET" action="{{ route('master.classification-letters.index') }}">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i data-lucide="search" class="h-5 w-5 text-gray-400"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}"
                    class="block w-full pl-12 pr-32 h-12 border border-gray-200 rounded-lg focus:ring-2 focus:ring-brand focus:border-transparent text-sm bg-white"
                    placeholder="Cari berdasarkan kode, nama, atau deskripsi klasifikasi...">
                <div class="absolute inset-y-0 right-0 flex items-center pr-2 gap-2">
                    @if (request('search'))
                        <a href="{{ route('master.classification-letters.index') }}"
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
                <h3 class="text-base font-semibold text-gray-900">Daftar Klasifikasi</h3>
                @if ($classifications->count() > 0)
                    <span class="text-sm text-gray-500">
                        {{ $classifications->total() }} klasifikasi ditemukan
                        @if(request('search'))
                            <span class="ml-2 inline-flex items-center rounded-full bg-brand-lighter px-2.5 py-0.5 text-xs font-medium text-brand-dark">
                                <i data-lucide="search" class="mr-1 h-3 w-3"></i>
                                Pencarian: "{{ request('search') }}"
                            </span>
                        @endif
                    </span>
                @endif
            </div>
        </div>
        <div class="overflow-x-auto">
            @if($classifications->count() > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">No</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Klasifikasi</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Deskripsi</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($classifications as $index => $classification)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ ($classifications->currentPage() - 1) * $classifications->perPage() + $index + 1 }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <code class="px-2 py-1 text-sm font-mono bg-gray-100 text-brand rounded">{{ $classification->code }}</code>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $classification->name }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 hidden lg:table-cell">
                                {{ Str::limit($classification->description, 60) ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold {{ $classification->is_active ? 'bg-success-lighter text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $classification->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('master.classification-letters.edit', $classification) }}" 
                                       class="inline-flex items-center rounded-md border border-brand px-3 py-1.5 text-sm font-medium text-brand hover:bg-orange-50 focus:outline-none focus:ring-2 focus:ring-brand focus:ring-offset-2 transition-colors">
                                        <i data-lucide="edit" class="mr-1 h-4 w-4"></i>
                                        Edit
                                    </a>
                                    @if($classification->is_active)
                                    <form method="POST" 
                                          action="{{ route('master.classification-letters.destroy', $classification) }}" 
                                          onsubmit="return confirm('Apakah Anda yakin ingin menonaktifkan klasifikasi ini? Data tidak akan dihapus dari database.')"
                                          class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="inline-flex items-center rounded-md border border-red-600 px-3 py-1.5 text-sm font-medium text-red-600 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
                                            <i data-lucide="x-circle" class="mr-1 h-4 w-4"></i>
                                            Nonaktifkan
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
                <div class="px-6 py-4">
                    {{ $classifications->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <i data-lucide="tags" class="mx-auto h-12 w-12 text-gray-400"></i>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada data klasifikasi</h3>
                    <p class="mt-1 text-sm text-gray-500">Belum ada klasifikasi surat yang terdaftar.</p>
                    <div class="mt-6">
                        <a href="{{ route('master.classification-letters.create') }}" 
                           class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700">
                            <i data-lucide="plus-circle" class="mr-2 h-4 w-4"></i>
                            Tambah Klasifikasi
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Download Template Modal --}}
    <div id="download-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-lg bg-white">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                    <i data-lucide="info" class="w-5 h-5 text-success"></i>
                    Petunjuk Pengisian Template Excel
                </h3>
                <button onclick="document.getElementById('download-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <div class="mb-6">
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                    <h4 class="text-sm font-semibold text-green-900 mb-3 flex items-center gap-2">
                        <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
                        Format Template Excel
                    </h4>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-xs border border-green-300">
                            <thead>
                                <tr class="bg-success-lighter">
                                    <th class="px-3 py-2 border border-green-300 text-left font-semibold text-green-900">Kode</th>
                                    <th class="px-3 py-2 border border-green-300 text-left font-semibold text-green-900">Nama</th>
                                    <th class="px-3 py-2 border border-green-300 text-left font-semibold text-green-900">Deskripsi</th>
                                    <th class="px-3 py-2 border border-green-300 text-left font-semibold text-green-900">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white">
                                <tr class="text-green-700">
                                    <td class="px-3 py-2 border border-green-300"><code class="bg-green-50 px-1 rounded">UM</code></td>
                                    <td class="px-3 py-2 border border-green-300">Surat Umum</td>
                                    <td class="px-3 py-2 border border-green-300">Surat yang bersifat umum...</td>
                                    <td class="px-3 py-2 border border-green-300">Aktif</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-orange-50 border border-blue-200 rounded-lg p-4 mb-4">
                    <h4 class="text-sm font-semibold text-brand-darker mb-2 flex items-center gap-2">
                        <i data-lucide="list-checks" class="w-4 h-4"></i>
                        Aturan Pengisian
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs text-brand-darker">
                        <div class="flex items-start gap-2">
                            <i data-lucide="check-circle" class="w-4 h-4 text-brand mt-0.5 flex-shrink-0"></i>
                            <div>
                                <strong>Kode:</strong> Wajib diisi, maksimal 20 karakter, harus unik (tidak boleh sama dengan kode yang sudah ada)
                            </div>
                        </div>
                        <div class="flex items-start gap-2">
                            <i data-lucide="check-circle" class="w-4 h-4 text-brand mt-0.5 flex-shrink-0"></i>
                            <div>
                                <strong>Nama:</strong> Wajib diisi, maksimal 100 karakter
                            </div>
                        </div>
                        <div class="flex items-start gap-2">
                            <i data-lucide="check-circle" class="w-4 h-4 text-brand mt-0.5 flex-shrink-0"></i>
                            <div>
                                <strong>Deskripsi:</strong> Opsional, boleh dikosongkan
                            </div>
                        </div>
                        <div class="flex items-start gap-2">
                            <i data-lucide="check-circle" class="w-4 h-4 text-brand mt-0.5 flex-shrink-0"></i>
                            <div>
                                <strong>Status:</strong> Wajib diisi, pilih <strong>"Aktif"</strong> atau <strong>"Nonaktif"</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                    <h4 class="text-sm font-semibold text-yellow-900 mb-2 flex items-center gap-2">
                        <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                        Catatan Penting
                    </h4>
                    <ul class="text-xs text-yellow-800 space-y-1.5 list-disc list-inside">
                        <li>Template sudah berisi 3 baris contoh data. <strong>Hapus baris contoh tersebut</strong> sebelum mengisi data Anda</li>
                        <li>Jangan ubah nama kolom header (Kode, Nama, Deskripsi, Status)</li>
                        <li>Mulai mengisi data dari baris ke-2 (setelah header)</li>
                        <li>Jika ada kode yang sudah terdaftar di database, data tersebut akan <strong>dilewati</strong></li>
                    </ul>
                </div>

                <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                    <p class="text-xs text-red-800 flex items-start gap-2">
                        <i data-lucide="x-circle" class="w-4 h-4 text-red-600 mt-0.5 flex-shrink-0"></i>
                        <span><strong>Peringatan:</strong> Data yang tidak sesuai format atau memiliki kode duplikat akan dilewati dan tidak diimport ke database.</span>
                    </p>
                </div>
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" 
                        onclick="document.getElementById('download-modal').classList.add('hidden')"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <a href="{{ route('master.classification-letters.download-template') }}" 
                   class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center gap-2">
                    <i data-lucide="download" class="w-4 h-4"></i>
                    OK, Download Template
                </a>
            </div>
        </div>
    </div>

    {{-- Import Modal --}}
    <div id="import-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-lg bg-white">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                    <i data-lucide="upload" class="w-5 h-5 text-info"></i>
                    Import Klasifikasi Surat
                </h3>
                <button onclick="document.getElementById('import-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form action="{{ route('master.classification-letters.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-4">
                    <label for="file" class="block text-sm font-medium text-gray-700 mb-2">
                        Pilih File Excel <span class="text-red-500">*</span>
                    </label>
                    <input type="file" 
                           id="file" 
                           name="file" 
                           accept=".xlsx,.xls"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                           required>
                    <p class="mt-1 text-xs text-gray-500">Format: .xlsx atau .xls (Max: 2MB)</p>
                </div>

                <div class="bg-orange-50 border border-blue-200 rounded-lg p-3 mb-4">
                    <h4 class="text-sm font-semibold text-brand-darker mb-1 flex items-center gap-1">
                        <i data-lucide="info" class="w-4 h-4"></i>
                        Petunjuk Import:
                    </h4>
                    <ul class="text-xs text-brand-darker space-y-1 list-disc list-inside">
                        <li>Download template Excel terlebih dahulu</li>
                        <li>Isi data sesuai format yang disediakan</li>
                        <li>Kode harus unik (tidak boleh sama)</li>
                        <li>Jika kode sudah ada, data akan dilewati</li>
                    </ul>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" 
                            onclick="document.getElementById('import-modal').classList.add('hidden')"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors flex items-center gap-2">
                        <i data-lucide="upload" class="w-4 h-4"></i>
                        Import
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        // Re-initialize Lucide icons on page load
        document.addEventListener('DOMContentLoaded', function() {
            lucide.createIcons();
        });

        // Close modals when clicking outside
        document.getElementById('import-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
            }
        });

        document.getElementById('download-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
            }
        });

        // Re-initialize icons when modals open
        const importObserver = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (!mutation.target.classList.contains('hidden')) {
                    lucide.createIcons();
                }
            });
        });
        importObserver.observe(document.getElementById('import-modal'), {
            attributes: true,
            attributeFilter: ['class']
        });

        const downloadObserver = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (!mutation.target.classList.contains('hidden')) {
                    lucide.createIcons();
                }
            });
        });
        downloadObserver.observe(document.getElementById('download-modal'), {
            attributes: true,
            attributeFilter: ['class']
        });
    </script>
    @endpush
@endsection






