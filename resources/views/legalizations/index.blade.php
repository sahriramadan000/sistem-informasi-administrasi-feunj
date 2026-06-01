@extends('layouts.app')

@section('title', 'Daftar Legalisir')

@section('content')
    {{-- Page Header --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                <i data-lucide="files" class="icon-brand mr-3 h-7 w-7 text-blue-600"></i>
                Daftar Legalisir
            </h2>
            <p class="mt-1 text-sm text-gray-500">Kelola data transaksi legalisir dokumen alumni</p>
        </div>
        <div class="flex items-center gap-2">
            @if (auth()->user()->isAdmin() || auth()->user()->isOperator())
                @if (auth()->user()->isAdmin())
                <a href="{{ route('legalizations.import.template') }}"
                    class="inline-flex items-center rounded-lg bg-white border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                    <i data-lucide="download" class="mr-2 h-4 w-4 text-gray-500"></i>
                    Template
                </a>
                <button type="button" onclick="document.getElementById('importModal').classList.remove('hidden')"
                    class="inline-flex items-center rounded-lg bg-white border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                    <i data-lucide="upload" class="mr-2 h-4 w-4 text-gray-500"></i>
                    Import Excel
                </button>
                @endif
                {{-- Add Button --}}
                <a href="{{ route('legalizations.create') }}"
                    class="inline-flex items-center rounded-lg btn-success bg-green-600 px-4 py-2 text-white hover:bg-green-700 focus:ring-offset-2 transition-colors ml-2">
                    <i data-lucide="plus-circle" class="mr-2 h-5 w-5"></i>
                    Buat Legalisir
                </a>
            @endif
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- FILTER + TABLE dalam satu card --}}
    {{-- ============================================================ --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        
        {{-- Filter Section --}}
        <div class="border-b border-gray-200">
            <button type="button" onclick="toggleFilter()"
                class="w-full flex items-center justify-between px-6 py-4 text-left hover:bg-gray-50 transition-colors">
                <div class="flex items-center">
                    <i data-lucide="sliders-horizontal" class="mr-2 h-5 w-5 text-blue-600"></i>
                    <h3 class="text-base font-semibold text-gray-900">Filter Data</h3>
                    @if (request()->hasAny(['date_range', 'education_level_id', 'search']))
                        <span class="ml-3 inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800">
                            <i data-lucide="filter" class="mr-1 h-3 w-3"></i>
                            Aktif
                        </span>
                    @endif
                </div>
                <i data-lucide="chevron-down" id="filter-icon"
                    class="h-5 w-5 text-gray-400 transition-transform duration-200"></i>
            </button>

            <div id="filter-content" class="hidden border-t border-gray-100">
                <div class="p-6">
                    <form method="GET" action="{{ route('legalizations.index') }}">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 items-end">
                            {{-- Date Range Filter --}}
                            <div class="space-y-2">
                                <label for="date_range" class="label">Tanggal Legalisir</label>
                                <input type="text" id="date_range" name="date_range" value="{{ request('date_range') }}"
                                    class="input w-full" placeholder="Pilih rentang tanggal...">
                            </div>

                            {{-- Education Level Filter --}}
                            <div class="space-y-2">
                                <label for="education_level_id" class="label">Jenjang</label>
                                <select id="education_level_id" name="education_level_id" class="select">
                                    <option value="">Semua Jenjang</option>
                                    @foreach ($educationLevels as $level)
                                        <option value="{{ $level->id }}" {{ request('education_level_id') == $level->id ? 'selected' : '' }}>
                                            {{ $level->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Search Filter --}}
                            <div class="space-y-2">
                                <label for="search" class="label">Cari Nama Alumni</label>
                                <input type="text" id="search" name="search" value="{{ request('search') }}"
                                    class="input w-full" placeholder="Ketik nama alumni...">
                            </div>

                            {{-- Action Buttons --}}
                            <div class="flex items-center gap-3 col-span-full sm:col-span-3 lg:mt-4">
                                <button type="submit" class="btn-primary flex-1 justify-center">
                                    <i data-lucide="filter" class="mr-2 h-4 w-4"></i>
                                    Filter
                                </button>
                                <a href="{{ route('legalizations.index') }}"
                                    class="btn-outline flex-1 justify-center">
                                    <i data-lucide="x" class="mr-2 h-4 w-4"></i>
                                    Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Per Page Selector --}}
        <div class="border-b border-gray-200 px-6 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <label for="per_page" class="text-sm text-gray-600">Tampilkan:</label>
                <form method="GET" action="{{ route('legalizations.index') }}" class="inline-block">
                    {{-- Preserve all current query parameters --}}
                    @if (request('date_range'))
                        <input type="hidden" name="date_range" value="{{ request('date_range') }}">
                    @endif
                    @if (request('education_level_id'))
                        <input type="hidden" name="education_level_id" value="{{ request('education_level_id') }}">
                    @endif
                    @if (request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif

                    <select name="per_page" id="per_page" class="px-3 py-1 text-sm border border-gray-300 rounded-md bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-0" onchange="this.form.submit()">
                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page', 10) == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page', 10) == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page', 10) == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </form>
                <span class="text-sm text-gray-600">data per halaman</span>
            </div>
            <span class="text-sm text-gray-500">
                Total: <span class="font-semibold">{{ $legalizations->total() }}</span> data
            </span>
        </div>

        <div class="overflow-x-auto">
            @if ($legalizations->count() > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">
                                No</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No.
                                Legalisir</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tanggal</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama
                                Alumni</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tahun
                                Lulus</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Jenjang</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Lembar</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total
                                Rp</th>
                            @if (auth()->user()->isAdmin() || auth()->user()->isOperator())
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($legalizations as $index => $legalization)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ ($legalizations->currentPage() - 1) * $legalizations->perPage() + $index + 1 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <code
                                        class="px-2 py-1 text-sm font-mono bg-blue-50 text-blue-700 rounded border border-blue-200">
                                        {{ str_pad($legalization->running_number, 2, '0', STR_PAD_LEFT) }}
                                    </code>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $legalization->date->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $legalization->alumni_name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $legalization->graduation_year }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $legalization->educationLevel->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $legalization->page_count }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    Rp {{ number_format($legalization->total_price, 0, ',', '.') }}
                                </td>

                                @if (auth()->user()->isAdmin() || auth()->user()->isOperator())
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center space-x-2">
                                            <a href="{{ route('legalizations.show', $legalization) }}"
                                                class="inline-flex items-center rounded-md border border-blue-600 px-2.5 py-1.5 text-xs font-medium text-blue-600 hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                                                <i data-lucide="eye" class="mr-1 h-3.5 w-3.5"></i>
                                                Detail
                                            </a>
                                            <a href="{{ route('legalizations.edit', $legalization) }}"
                                                class="inline-flex items-center rounded-md border border-orange-600 px-2.5 py-1.5 text-xs font-medium text-orange-600 hover:bg-orange-50 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-colors">
                                                <i data-lucide="edit" class="mr-1 h-3.5 w-3.5"></i>
                                                Edit
                                            </a>
                                            <form method="POST"
                                                action="{{ route('legalizations.destroy', $legalization) }}"
                                                onsubmit="return confirm('Apakah Anda yakin ingin menonaktifkan data legalisir ini?')"
                                                class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="inline-flex items-center rounded-md border border-red-600 px-2.5 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
                                                    <i data-lucide="trash-2" class="mr-1 h-3.5 w-3.5"></i>
                                                    Nonaktifkan
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Pagination --}}
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $legalizations->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <i data-lucide="files" class="mx-auto h-12 w-12 text-gray-400"></i>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada data legalisir</h3>
                    <p class="mt-1 text-sm text-gray-500">Belum ada transaksi legalisir yang tercatat atau sesuai filter.</p>
                    @if (auth()->user()->isAdmin() || auth()->user()->isOperator())
                        <div class="mt-6">
                            <a href="{{ route('legalizations.create') }}"
                                class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700">
                                <i data-lucide="plus-circle" class="mr-2 h-4 w-4"></i>
                                Buat Legalisir
                            </a>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
    {{-- ============================================================ --}}

    @push('scripts')
        <script>
            function toggleFilter() {
                const content = document.getElementById('filter-content');
                const icon = document.getElementById('filter-icon');
                content.classList.toggle('hidden');
                icon.style.transform = content.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
            }

            // Auto-buka filter jika ada filter aktif
            document.addEventListener('DOMContentLoaded', function () {
                @if(request()->hasAny(['date_range', 'education_level_id', 'search']))
                    const content = document.getElementById('filter-content');
                    const icon = document.getElementById('filter-icon');
                    content.classList.remove('hidden');
                    icon.style.transform = 'rotate(180deg)';
                @endif

                if (typeof flatpickr !== 'undefined') {
                    flatpickr("#date_range", {
                        mode: "range",
                        dateFormat: "Y-m-d",
                        altInput: true,
                        altFormat: "d M Y",
                        locale: "id",
                        placeholder: "Pilih tanggal start - end"
                    });
                }

                if (typeof $ !== 'undefined' && $.fn.select2) {
                    $('#education_level_id').select2({ placeholder: 'Semua Jenjang', allowClear: true, width: '100%' });
                }

                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            });
        </script>
    @endpush
    
    {{-- Import Modal --}}
    <div id="importModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="document.getElementById('importModal').classList.add('hidden')"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="relative z-10 inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div>
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100">
                        <i data-lucide="upload" class="h-6 w-6 text-blue-600"></i>
                    </div>
                    <div class="mt-3 text-center sm:mt-5">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Import Data Legalisir
                        </h3>
                        <div class="mt-2 text-sm text-gray-500">
                            <p>Unggah file Excel (xls, xlsx) yang berisi data transaksi legalisir. Pastikan format kolom sesuai dengan template yang disediakan.</p>
                        </div>
                    </div>
                </div>
                <form action="{{ route('legalizations.import') }}" method="POST" enctype="multipart/form-data" class="mt-5 sm:mt-6">
                    @csrf
                    <div class="mb-4">
                        <label for="file_excel" class="block text-sm font-medium text-gray-700">File Excel</label>
                        <input type="file" name="file_excel" id="file_excel" accept=".xls,.xlsx" required
                            class="mt-1 block w-full text-sm text-gray-500
                            file:mr-4 file:py-2 file:px-4
                            file:rounded-md file:border-0
                            file:text-sm file:font-medium
                            file:bg-blue-50 file:text-blue-700
                            hover:file:bg-blue-100">
                    </div>
                     <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                         <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:col-start-2 sm:text-sm">
                             Import Data
                         </button>
                         <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                             Batal
                         </button>
                     </div>
                 </form>
             </div>
         </div>
     </div>

     {{-- ERROR MODAL - IMPORT ERRORS DETAIL --}}
     @if (session('import_errors') && count(session('import_errors')) > 0)
         <div id="errorModal" class="fixed inset-0 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
             <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                 <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" 
                      onclick="document.getElementById('errorModal').classList.add('hidden')"></div>

                 <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl">
                     {{-- Header --}}
                     <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                         <div class="sm:flex sm:items-start">
                             <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                 <i data-lucide="alert-circle" class="h-6 w-6 text-red-600"></i>
                             </div>
                             <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                 <h3 class="text-lg font-semibold leading-6 text-gray-900" id="modal-title">
                                     Import Gagal
                                 </h3>
                                 <div class="mt-2">
                                     <p class="text-sm text-gray-600">
                                         Ada <span class="font-semibold text-red-600">{{ count(session('import_errors')) }} baris</span> yang memiliki error. Perbaiki masalah di bawah dan coba lagi.
                                     </p>
                                 </div>
                             </div>
                         </div>
                     </div>

                     {{-- Error List --}}
                     <div class="px-4 pb-4 sm:px-6">
                         <div class="max-h-96 overflow-y-auto space-y-3">
                             @foreach (session('import_errors') as $error)
                                 <div class="p-4 border-2 border-red-200 bg-red-50 rounded-lg">
                                     <div class="flex items-center gap-2 mb-2">
                                         <span class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-sm font-semibold text-red-800">
                                             Baris {{ $error['row'] }}
                                         </span>
                                         <span class="text-base font-bold text-gray-900">
                                             {{ $error['field'] }}
                                         </span>
                                     </div>

                                     <div class="flex items-start gap-2 mb-2">
                                         <i data-lucide="x-circle" class="h-4 w-4 text-red-600 mt-0.5 flex-shrink-0"></i>
                                         <p class="text-sm font-medium text-red-700">
                                             {{ $error['message'] }}
                                         </p>
                                     </div>

                                     @if ($error['value'])
                                         <div class="ml-6 mb-2">
                                             <span class="text-xs text-gray-600">Anda masukkan:</span>
                                             <code class="block mt-1 bg-white px-3 py-2 rounded border border-red-200 text-sm font-mono text-gray-800">
                                                 {{ $error['value'] }}
                                             </code>
                                         </div>
                                     @endif

                                     @if ($error['suggestions'])
                                         <div class="ml-6 mt-3 p-3 bg-green-50 border border-green-200 rounded">
                                             <div class="flex items-start gap-2">
                                                 <i data-lucide="lightbulb" class="h-4 w-4 text-green-600 mt-0.5 flex-shrink-0"></i>
                                                 <div>
                                                     <p class="text-xs font-semibold text-green-800 mb-1">Solusi:</p>
                                                     <p class="text-xs text-green-700">{{ $error['suggestions'] }}</p>
                                                 </div>
                                             </div>
                                         </div>
                                     @endif
                                 </div>
                             @endforeach
                         </div>
                     </div>

                     {{-- Tips --}}
                     <div class="px-4 pb-4 sm:px-6">
                         <div class="p-4 bg-blue-50 border-2 border-blue-200 rounded-lg">
                             <div class="flex items-start gap-3">
                                 <i data-lucide="info" class="h-5 w-5 text-blue-600 flex-shrink-0 mt-0.5"></i>
                                 <div class="text-sm text-blue-800">
                                     <p class="font-semibold mb-2">Tips:</p>
                                     <ul class="space-y-1 text-xs">
                                         <li>• Download template untuk referensi format kolom yang benar</li>
                                         <li>• Pastikan semua field yang wajib diisi sudah terisi</li>
                                         <li>• Periksa kembali format tanggal (YYYY-MM-DD) dan tahun (4 digit)</li>
                                         <li>• Gunakan nama jenjang yang persis sama di sheet Referensi</li>
                                     </ul>
                                 </div>
                             </div>
                         </div>
                     </div>

                     {{-- Footer Buttons --}}
                     <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 gap-2">
                         <button type="button" 
                                 onclick="document.getElementById('errorModal').classList.add('hidden')"
                                 class="inline-flex w-full justify-center rounded-md bg-orange-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-orange-500 sm:ml-3 sm:w-auto">
                             Tutup
                         </button>
                         <a href="{{ route('legalizations.import.template') }}" 
                            class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-4 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">
                             <i data-lucide="download" class="h-4 w-4 mr-2"></i>
                             Download Template
                         </a>
                     </div>
                 </div>
             </div>
         </div>

         {{-- Auto-show error modal on page load --}}
         <script>
             document.addEventListener('DOMContentLoaded', function() {
                 var errorModal = document.getElementById('errorModal');
                 if (errorModal) {
                     errorModal.classList.remove('hidden');
                 }
             });
         </script>
     @endif
@endsection
