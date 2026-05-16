@extends('layouts.app')

@section('title', 'Daftar Surat')

@section('content')
    {{-- Page Header --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                <i data-lucide="mail" class="mr-3 h-7 w-7 text-brand"></i>
                Daftar Surat
            </h2>
            <p class="mt-1 text-sm text-gray-500">Kelola dan lihat semua surat yang telah dibuat</p>
        </div>
        @if (auth()->user()->isAdmin() || auth()->user()->isOperator())
            <div class="flex items-center gap-2">
                <a href="{{ route('letters.import.template') }}"
                    class="inline-flex items-center rounded-lg bg-white border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-brand focus:ring-offset-2 transition-colors">
                    <i data-lucide="download" class="mr-2 h-4 w-4 text-gray-500"></i>
                    Template
                </a>
                <button type="button" onclick="document.getElementById('importModal').classList.remove('hidden')"
                    class="inline-flex items-center rounded-lg bg-white border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-brand focus:ring-offset-2 transition-colors">
                    <i data-lucide="upload" class="mr-2 h-4 w-4 text-gray-500"></i>
                    Import Excel
                </button>
                <a href="{{ route('letters.create') }}"
                    class="inline-flex items-center rounded-lg btn-success focus:ring-offset-2 transition-colors ml-2">
                    <i data-lucide="plus-circle" class="mr-2 h-5 w-5"></i>
                    Buat Surat Baru
                </a>
            </div>
        @endif
    </div>

    {{-- ============================================================ --}}
    {{-- TABS + FILTER + TABLE dalam satu card --}}
    {{-- ============================================================ --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">

        {{-- Tabs Jenis Surat --}}
        <div class="overflow-x-auto border-b border-gray-200 py-1">
            <nav class="flex px-4" aria-label="Tabs">
                <a href="{{ route('letters.index', array_merge(request()->except(['letter_type_id', 'page']))) }}"
                   class="whitespace-nowrap flex items-center gap-2 py-3.5 px-4 border-b-2 text-sm font-medium -mb-px transition-all
                       {{ !request('letter_type_id') ? 'border-brand text-brand' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <i data-lucide="layers" class="h-4 w-4"></i>
                    Semua Jenis
                </a>
                @foreach($letterTypes as $type)
                    <a href="{{ route('letters.index', array_merge(request()->except(['letter_type_id', 'page']), ['letter_type_id' => $type->id])) }}"
                       class="whitespace-nowrap flex items-center gap-2 py-3.5 px-4 border-b-2 text-sm font-medium -mb-px transition-all
                           {{ request('letter_type_id') == $type->id ? 'border-brand text-brand' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        <i data-lucide="file-text" class="h-4 w-4"></i>
                        {{ $type->name }}
                    </a>
                @endforeach
            </nav>
        </div>

        {{-- Filter Section --}}
        <div class="border-b border-gray-200">
            <button type="button" onclick="toggleFilter()"
                class="w-full flex items-center justify-between px-6 py-4 text-left hover:bg-gray-50 transition-colors">
                <div class="flex items-center">
                    <i data-lucide="sliders-horizontal" class="mr-2 h-5 w-5 text-brand"></i>
                    <h3 class="text-base font-semibold text-gray-900">Filter Data</h3>
                    @if (request()->hasAny(['date_range', 'classification_id', 'signatory_id', 'search']))
                        <span class="ml-3 inline-flex items-center rounded-full bg-brand-lighter px-2.5 py-0.5 text-xs font-medium text-brand-dark">
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
                    <form method="GET" action="{{ route('letters.index') }}">
                        {{-- Preserve letter_type_id tab --}}
                        @if (request('letter_type_id'))
                            <input type="hidden" name="letter_type_id" value="{{ request('letter_type_id') }}">
                        @endif

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 items-end">
                            {{-- Date Range Filter --}}
                            <div class="space-y-2">
                                <label for="date_range" class="label">Tanggal Surat</label>
                                <input type="text" id="date_range" name="date_range" value="{{ request('date_range') }}"
                                    class="input w-full" placeholder="Pilih rentang tanggal...">
                            </div>

                            {{-- Classification Filter --}}
                            <div class="space-y-2">
                                <label for="classification_id" class="label">Klasifikasi</label>
                                <select id="classification_id" name="classification_id" class="select">
                                    <option value="">Semua Klasifikasi</option>
                                    @foreach ($classifications as $classification)
                                        <option value="{{ $classification->id }}" {{ request('classification_id') == $classification->id ? 'selected' : '' }}>
                                            {{ $classification->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Signatory Filter --}}
                            <div class="space-y-2">
                                <label for="signatory_id" class="label">Penandatangan</label>
                                <select id="signatory_id" name="signatory_id" class="select">
                                    <option value="">Semua Penandatangan</option>
                                    @foreach ($signatories as $signatory)
                                        <option value="{{ $signatory->id }}" {{ request('signatory_id') == $signatory->id ? 'selected' : '' }}>
                                            {{ $signatory->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="flex items-center gap-3">
                                <button type="submit" class="btn-primary flex-1 justify-center">
                                    <i data-lucide="filter" class="mr-2 h-4 w-4"></i>
                                    Filter
                                </button>
                                <a href="{{ route('letters.index', request()->has('letter_type_id') ? ['letter_type_id' => request('letter_type_id')] : []) }}"
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
                <form method="GET" action="{{ route('letters.index') }}" class="inline-block">
                    {{-- Preserve all current query parameters --}}
                    @if (request('letter_type_id'))
                        <input type="hidden" name="letter_type_id" value="{{ request('letter_type_id') }}">
                    @endif
                    @if (request('date_range'))
                        <input type="hidden" name="date_range" value="{{ request('date_range') }}">
                    @endif
                    @if (request('classification_id'))
                        <input type="hidden" name="classification_id" value="{{ request('classification_id') }}">
                    @endif
                    @if (request('signatory_id'))
                        <input type="hidden" name="signatory_id" value="{{ request('signatory_id') }}">
                    @endif
                    @if (request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif

                    <select name="per_page" id="per_page" class="px-3 py-1 text-sm border border-gray-300 rounded-md bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-brand focus:ring-offset-0" onchange="this.form.submit()">
                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page', 10) == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page', 10) == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page', 10) == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </form>
                <span class="text-sm text-gray-600">data per halaman</span>
            </div>
            <span class="text-sm text-gray-500">
                Total: <span class="font-semibold">{{ $letters->total() }}</span> data
            </span>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            @if ($letters->count() > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50">No</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50">Nomor Surat</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50">Tanggal</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50 hidden lg:table-cell">Jenis</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50 hidden xl:table-cell">Klasifikasi</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50 hidden lg:table-cell">Penandatangan</th>
                             <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50">Sasaran Surat</th>
                             <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50">Keamanan</th>
                             <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50">Dibuat Oleh</th>
                             <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($letters as $index => $letter)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ ($letters->currentPage() - 1) * $letters->perPage() + $index + 1 }}
                                </td>
                                <td class="px-3 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 flex items-center gap-2">
                                        @php
                                            $parts = explode('/', $letter->letter_number);
                                        @endphp
                                        @if(count($parts) > 1)
                                            <div>
                                                {{ $parts[0] }}/<span class="bg-yellow-100 text-yellow-800 font-bold px-1 rounded">{{ $parts[1] }}</span>/{{ implode('/', array_slice($parts, 2)) }}
                                            </div>
                                        @else
                                            <div>{{ $letter->letter_number }}</div>
                                        @endif

                                    </div>
                                </td>
                                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $letter->letter_date->format('d/m/Y') }}
                                </td>
                                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500 hidden lg:table-cell">
                                    {{ $letter->letterType->name }}
                                </td>
                                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500 hidden xl:table-cell">
                                    {{ $letter->classification->name }}
                                </td>
                                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500 hidden lg:table-cell">
                                    <span class="font-semibold text-blue-800">{{ $letter->signatory->position }}</span> - {{ $letter->signatory->name }}
                                </td>
                                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @php
                                        $targetValue = $letter->letter_target;
                                    @endphp
                                    @if ($targetValue)
                                        @php
                                            $targetLabel = \App\Enums\LetterTarget::from($targetValue)->label();
                                            $targetColor = $targetValue === 'internal' ? 'bg-blue-100 text-blue-800 ring-blue-600/20' : 'bg-purple-100 text-purple-800 ring-purple-600/20';
                                        @endphp
                                        <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium {{ $targetColor }} ring-1 ring-inset">
                                            {{ $targetLabel }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-3 py-4 text-sm text-gray-900">
                                    @php
                                        $securityValue = $letter->security_classification;
                                    @endphp
                                    @if ($securityValue)
                                        @php
                                            $securityLabel = \App\Enums\SecurityClassification::from($securityValue)->label();
                                            $securityColor = match($securityValue) {
                                                'B' => 'bg-green-100 text-green-800 ring-green-600/20',
                                                'T' => 'bg-yellow-100 text-yellow-800 ring-yellow-600/20',
                                                'R' => 'bg-orange-100 text-orange-800 ring-orange-600/20',
                                                'SR' => 'bg-red-100 text-red-800 ring-red-600/20',
                                                default => 'bg-gray-100 text-gray-800 ring-gray-600/20'
                                            };
                                        @endphp
                                        <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium {{ $securityColor }} ring-1 ring-inset">
                                            {{ $securityLabel }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-600">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ $letter->creator?->name ?? 'Sistem' }}
                                    </span>
                                </td>
                                <td class="px-3 py-4 whitespace-nowrap text-sm">
                                    <a href="{{ route('letters.show', $letter) }}"
                                        class="inline-flex items-center rounded-md bg-blue-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                                        <i data-lucide="eye" class="mr-1.5 h-3.5 w-3.5"></i>
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Pagination --}}
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $letters->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <i data-lucide="inbox" class="mx-auto h-12 w-12 text-gray-400"></i>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada data surat</h3>
                    <p class="mt-1 text-sm text-gray-500">Belum ada surat yang sesuai dengan filter yang dipilih.</p>
                    @if (auth()->user()->isAdmin() || auth()->user()->isOperator())
                        <div class="mt-6">
                            <a href="{{ route('letters.create') }}"
                                class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700">
                                <i data-lucide="plus-circle" class="mr-2 h-4 w-4"></i>
                                Buat Surat Baru
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
                @if(request()->hasAny(['date_range', 'classification_id', 'signatory_id', 'search']))
                    const content = document.getElementById('filter-content');
                    const icon = document.getElementById('filter-icon');
                    content.classList.remove('hidden');
                    icon.style.transform = 'rotate(180deg)';
                @endif

                flatpickr("#date_range", {
                    mode: "range",
                    dateFormat: "Y-m-d",
                    altInput: true,
                    altFormat: "d M Y",
                    locale: "id",
                    placeholder: "Pilih tanggal start - end"
                });

                $('#letter_type_id').select2({ placeholder: 'Semua Jenis', allowClear: true, width: '100%' });
                $('#classification_id').select2({ placeholder: 'Semua Klasifikasi', allowClear: true, width: '100%' });
                $('#signatory_id').select2({ placeholder: 'Semua Penandatangan', allowClear: true, width: '100%' });

                lucide.createIcons();
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
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-brand-lighter">
                        <i data-lucide="upload" class="h-6 w-6 text-brand"></i>
                    </div>
                    <div class="mt-3 text-center sm:mt-5">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Import Data Surat
                        </h3>
                        <div class="mt-2 text-sm text-gray-500">
                            <p>Unggah file Excel (xls, xlsx) yang berisi data surat lama. Pastikan format kolom sesuai dengan template yang disediakan.</p>
                        </div>
                    </div>
                </div>
                <form action="{{ route('letters.import') }}" method="POST" enctype="multipart/form-data" class="mt-5 sm:mt-6">
                    @csrf
                    <div class="mb-4">
                        <label for="file_excel" class="block text-sm font-medium text-gray-700">File Excel</label>
                        <input type="file" name="file_excel" id="file_excel" accept=".xls,.xlsx" required
                            class="mt-1 block w-full text-sm text-gray-500
                            file:mr-4 file:py-2 file:px-4
                            file:rounded-md file:border-0
                            file:text-sm file:font-medium
                            file:bg-brand-50 file:text-brand
                            hover:file:bg-brand-100">
                    </div>
                     <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                         <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 btn-primary text-base font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 sm:col-start-2 sm:text-sm">
                             Import Data
                         </button>
                         <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand sm:mt-0 sm:col-start-1 sm:text-sm">
                             Batal
                         </button>
                     </div>
                 </form>
             </div>
         </div>
     </div>

     {{-- ERROR MODAL - IMPORT ERRORS DETAIL --}}
     @if (session('import_errors') && count(session('import_errors')) > 0)
         <div id="errorModal" class="fixed inset-0 z-50 overflow-y-auto hidden">
             <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                 <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" 
                      onclick="document.getElementById('errorModal').classList.add('hidden')"></div>

                 <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                 <div class="relative z-10 inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full sm:p-6">
                     {{-- Header --}}
                     <div>
                         <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                             <i data-lucide="alert-circle" class="h-6 w-6 text-red-600"></i>
                         </div>
                         <div class="mt-3 text-center sm:mt-5">
                             <h3 class="text-lg leading-6 font-medium text-gray-900">
                                 Import Gagal
                             </h3>
                             <div class="mt-2 text-sm text-gray-500">
                                 <p>Ada <span class="font-semibold text-red-600">{{ count(session('import_errors')) }} baris</span> yang memiliki error. Perbaiki masalah di bawah dan coba lagi.</p>
                             </div>
                         </div>
                     </div>

                     {{-- Error List --}}
                     <div class="mt-6 max-h-96 overflow-y-auto">
                         <div class="space-y-3">
                             @foreach (session('import_errors') as $error)
                                 <div class="p-3 border border-red-200 bg-red-50 rounded-lg">
                                     {{-- Row Number + Field --}}
                                     <div class="flex items-start">
                                         <div class="flex-1">
                                             <div class="flex items-center gap-2">
                                                 <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800">
                                                     Baris {{ $error['row'] }}
                                                 </span>
                                                 <span class="text-sm font-semibold text-gray-900">
                                                     {{ $error['field'] }}
                                                 </span>
                                             </div>

                                             {{-- Error Message --}}
                                             <div class="mt-1 text-sm text-red-700">
                                                 <i data-lucide="x" class="inline h-4 w-4 mr-1"></i>
                                                 {{ $error['message'] }}
                                             </div>

                                             {{-- Value yang diinput (jika ada) --}}
                                             @if ($error['value'])
                                                 <div class="mt-1.5 text-sm">
                                                     <span class="text-gray-600">Anda masukkan:</span>
                                                     <code class="inline bg-gray-100 px-2 py-1 rounded text-xs font-mono text-gray-800">
                                                         {{ $error['value'] }}
                                                     </code>
                                                 </div>
                                             @endif

                                             {{-- Suggestions --}}
                                             @if ($error['suggestions'])
                                                 <div class="mt-2 text-sm">
                                                     <span class="text-green-700 font-medium">Solusi:</span>
                                                     <span class="text-gray-700">{{ $error['suggestions'] }}</span>
                                                 </div>
                                             @endif
                                         </div>
                                     </div>
                                 </div>
                             @endforeach
                         </div>
                     </div>

                     {{-- Tips --}}
                     <div class="mt-6 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                         <div class="flex items-start">
                             <i data-lucide="info" class="h-5 w-5 text-blue-600 mr-2 flex-shrink-0 mt-0.5"></i>
                             <div class="text-sm text-blue-700">
                                 <p class="font-medium">Tips:</p>
                                 <ul class="mt-1 ml-4 space-y-1 text-xs">
                                     <li>• Download template untuk referensi format kolom yang benar</li>
                                     <li>• Pastikan semua field yang wajib diisi sudah terisi</li>
                                     <li>• Periksa kembali format tanggal dan kode master data</li>
                                     <li>• Gunakan nilai yang tersedia di sistem (lihat "Solusi" untuk detail)</li>
                                 </ul>
                             </div>
                         </div>
                     </div>

                     {{-- Footer Buttons --}}
                     <div class="mt-6 sm:mt-8 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                         <a href="{{ route('letters.import.template') }}" 
                            class="w-full inline-flex justify-center items-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand transition-colors">
                             <i data-lucide="download" class="h-4 w-4 mr-2"></i>
                             Download Template
                         </a>
                         <button type="button" 
                                 onclick="document.getElementById('errorModal').classList.add('hidden')"
                                 class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 btn-primary text-base font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors">
                             Tutup
                         </button>
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