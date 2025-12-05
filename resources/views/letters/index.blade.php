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
            <a href="{{ route('letters.create') }}"
                class="inline-flex items-center rounded-lg btn-success focus:ring-offset-2 transition-colors">
                <i data-lucide="plus-circle" class="mr-2 h-5 w-5"></i>
                Buat Surat Baru
            </a>
        @endif
    </div>

    {{-- Filter Section --}}
    <div class="mb-6">
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
            <button type="button" onclick="toggleFilter()"
                class="w-full flex items-center justify-between px-6 py-4 text-left hover:bg-gray-50 transition-colors">
                <div class="flex items-center">
                    <i data-lucide="sliders-horizontal" class="mr-2 h-5 w-5 text-brand"></i>
                    <h3 class="text-base font-semibold text-gray-900">Filter Data</h3>
                    @if (request()->hasAny(['year', 'letter_type_id', 'classification_id', 'signatory_id']))
                        <span
                            class="ml-3 inline-flex items-center rounded-full bg-brand-lighter px-2.5 py-0.5 text-xs font-medium text-brand-dark">
                            <i data-lucide="filter" class="mr-1 h-3 w-3"></i>
                            Aktif
                        </span>
                    @endif
                </div>
                <i data-lucide="chevron-down" id="filter-icon"
                    class="h-5 w-5 text-gray-400 transition-transform duration-200"></i>
            </button>

            <div id="filter-content" class="hidden border-t border-gray-200">
                <div class="p-6">
                    <form method="GET" action="{{ route('letters.index') }}">
                        {{-- Preserve search parameter --}}
                        @if (request('search'))
                            <input type="hidden" name="search" value="{{ request('search') }}">
                        @endif

                        {{-- Filter Grid --}}
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-6">
                            {{-- Year Filter --}}
                            <div class="space-y-2">
                                <label for="year" class="label">Tahun</label>
                                <select id="year" name="year" class="select">
                                    <option value="">Semua Tahun</option>
                                    @for ($year = date('Y'); $year >= date('Y') - 5; $year--)
                                        <option value="{{ $year }}"
                                            {{ request('year') == $year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endfor
                                </select>
                            </div>

                            {{-- Letter Type Filter --}}
                            <div class="space-y-2">
                                <label for="letter_type_id" class="label">Jenis Surat</label>
                                <select id="letter_type_id" name="letter_type_id" class="select">
                                    <option value="">Semua Jenis</option>
                                    @foreach ($letterTypes as $letterType)
                                        <option value="{{ $letterType->id }}"
                                            {{ request('letter_type_id') == $letterType->id ? 'selected' : '' }}>
                                            {{ $letterType->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Classification Filter --}}
                            <div class="space-y-2">
                                <label for="classification_id" class="label">Klasifikasi</label>
                                <select id="classification_id" name="classification_id" class="select">
                                    <option value="">Semua Klasifikasi</option>
                                    @foreach ($classifications as $classification)
                                        <option value="{{ $classification->id }}"
                                            {{ request('classification_id') == $classification->id ? 'selected' : '' }}>
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
                                        <option value="{{ $signatory->id }}"
                                            {{ request('signatory_id') == $signatory->id ? 'selected' : '' }}>
                                            {{ $signatory->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex items-center gap-3 pt-4 border-t border-gray-200">
                            <button type="submit" class="btn-primary">
                                <i data-lucide="filter" class="mr-2 h-4 w-4"></i>
                                Terapkan Filter
                            </button>
                            <a href="{{ route('letters.index') }}?{{ request('search') ? 'search=' . request('search') : '' }}"
                                class="btn-outline">
                                <i data-lucide="x" class="mr-2 h-4 w-4"></i>
                                Reset Filter
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Search Section --}}
    <div class="mb-6">
        <form method="GET" action="{{ route('letters.index') }}">
            {{-- Preserve filter parameters --}}
            @if (request('year'))
                <input type="hidden" name="year" value="{{ request('year') }}">
            @endif
            @if (request('letter_type_id'))
                <input type="hidden" name="letter_type_id" value="{{ request('letter_type_id') }}">
            @endif
            @if (request('classification_id'))
                <input type="hidden" name="classification_id" value="{{ request('classification_id') }}">
            @endif
            @if (request('signatory_id'))
                <input type="hidden" name="signatory_id" value="{{ request('signatory_id') }}">
            @endif

            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <i data-lucide="search" class="h-5 w-5 text-gray-400"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}"
                    class="block w-full pl-12 pr-32 h-12 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent text-sm bg-white"
                    placeholder="Cari nomor surat atau perihal...">
                <div class="absolute inset-y-0 right-0 flex items-center pr-2 gap-2">
                    @if (request('search'))
                        <a href="{{ route('letters.index') }}?{{ http_build_query(request()->except('search')) }}"
                            class="inline-flex items-center rounded-md px-3 py-1.5 text-xs font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition-colors">
                            <i data-lucide="x" class="h-3.5 w-3.5 mr-1"></i>
                            Clear
                        </a>
                    @endif
                    <button type="submit"
                        class="inline-flex items-center rounded-md bg-blue-600 px-4 py-1.5 text-xs font-medium text-white hover:bg-blue-700 transition-colors">
                        <i data-lucide="search" class="h-3.5 w-3.5 mr-1.5"></i>
                        Cari
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Letters Table --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-900">Hasil Pencarian</h3>
                @if ($letters->count() > 0)
                    <span class="text-sm text-gray-500">
                        {{ $letters->total() }} surat ditemukan
                    </span>
                @endif
            </div>
        </div>
        <div class="overflow-x-auto">
            @if ($letters->count() > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th scope="col"
                                class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50">
                                No</th>
                            <th scope="col"
                                class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50">
                                Nomor Surat</th>
                            <th scope="col"
                                class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50">
                                Tanggal</th>
                            <th scope="col"
                                class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50 hidden lg:table-cell">
                                Jenis</th>
                            <th scope="col"
                                class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50 hidden xl:table-cell">
                                Klasifikasi</th>
                            <th scope="col"
                                class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50 hidden lg:table-cell">
                                Penandatangan</th>
                            <th scope="col"
                                class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50">
                                Perihal</th>
                            <th scope="col"
                                class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50">
                                Status</th>
                            <th scope="col"
                                class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50">
                                Aksi</th>
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
                                        {{ $letter->letter_number }}
                                        {{-- Debug: created_at={{ $letter->created_at }}, created_by={{ $letter->created_by }}, auth_id={{ auth()->id() }}, isNew={{ $letter->isNewFor(auth()->id()) ? 'true' : 'false' }} --}}
                                        {{-- {{ dd($letter->isNewFor(auth()->id())) }} --}}
                                        @if ($letter->isNewFor(auth()->id()))
                                            <span
                                                class="inline-flex items-center rounded-full bg-red-500 px-2.5 py-0.5 text-xs font-bold text-white shadow-sm animate-pulse">
                                                NEW
                                            </span>
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
                                    {{ $letter->signatory->name }}
                                </td>
                                <td class="px-3 py-4 text-sm text-gray-900">
                                    <div class="flex items-start gap-2">
                                        <span>{{ Str::limit($letter->subject, 50) }}</span>
                                    </div>
                                </td>
                                <td class="px-3 py-4 whitespace-nowrap">
                                    <span
                                        class="inline-flex rounded-full px-2 py-1 text-xs font-semibold
                                    @if ($letter->status == 'final') bg-success-lighter text-green-800
                                    @elseif($letter->status == 'draft') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800 @endif">
                                        {{ ucfirst($letter->status) }}
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
                {{ $letters->links() }}
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

    @push('scripts')
        <script>
            // Toggle filter visibility
            function toggleFilter() {
                const content = document.getElementById('filter-content');
                const icon = document.getElementById('filter-icon');

                content.classList.toggle('hidden');
                if (content.classList.contains('hidden')) {
                    icon.style.transform = 'rotate(0deg)';
                } else {
                    icon.style.transform = 'rotate(180deg)';
                }
            }

            // Initialize Select2 for filter selects
            $(document).ready(function() {
                $('#year').select2({
                    placeholder: 'Semua Tahun',
                    allowClear: true,
                    width: '100%'
                });

                $('#letter_type_id').select2({
                    placeholder: 'Semua Jenis',
                    allowClear: true,
                    width: '100%'
                });

                $('#classification_id').select2({
                    placeholder: 'Semua Klasifikasi',
                    allowClear: true,
                    width: '100%'
                });

                $('#signatory_id').select2({
                    placeholder: 'Semua Penandatangan',
                    allowClear: true,
                    width: '100%'
                });
            });

            // Re-initialize Lucide icons
            lucide.createIcons();
        </script>
    @endpush
@endsection
