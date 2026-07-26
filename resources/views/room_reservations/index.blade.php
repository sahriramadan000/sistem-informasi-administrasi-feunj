@extends('layouts.app')

@section('title', 'Daftar Peminjaman Ruang')

@section('content')
    {{-- Page Header --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 flex items-center">
                <i data-lucide="list" class="mr-3 h-7 w-7 text-brand"></i>
                Daftar Peminjaman Ruang
            </h2>
            <p class="mt-1 text-sm text-gray-500">Kelola dan lihat semua permohonan peminjaman ruangan</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('room-reservations.create') }}"
                class="inline-flex items-center rounded-lg btn-success focus:ring-offset-2 transition-colors ml-2">
                <i data-lucide="plus-circle" class="mr-2 h-5 w-5"></i>
                Buat Permohonan
            </a>
        </div>
    </div>



    {{-- ============================================================ --}}
    {{-- TABS + FILTER + TABLE dalam satu card --}}
    {{-- ============================================================ --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">

        {{-- Tabs Ruangan --}}
        <div class="relative border-b border-gray-200">
            {{-- Fade kiri --}}
            <div id="tab-fade-left" class="pointer-events-none absolute left-0 top-0 bottom-0 w-8 bg-gradient-to-r from-white to-transparent z-10 hidden"></div>
            {{-- Fade kanan --}}
            <div id="tab-fade-right" class="pointer-events-none absolute right-0 top-0 bottom-0 w-8 bg-gradient-to-l from-white to-transparent z-10"></div>

            <div id="tab-scroll-container" class="overflow-x-auto py-1 scroll-smooth" style="-webkit-overflow-scrolling: touch; scrollbar-width: none; -ms-overflow-style: none;">
                <nav class="flex px-4 min-w-max" aria-label="Tabs">
                    <a href="{{ route('room-reservations.index', array_merge(request()->except(['room_id', 'page']))) }}"
                       data-active="{{ !request('room_id') ? '1' : '0' }}"
                       class="tab-item whitespace-nowrap flex items-center gap-2 py-3.5 px-4 border-b-2 text-sm font-medium -mb-px transition-all
                           {{ !request('room_id') ? 'border-brand text-brand' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        <i data-lucide="layers" class="h-4 w-4"></i>
                        Semua Ruangan
                    </a>
                    @foreach($rooms as $room)
                        <a href="{{ route('room-reservations.index', array_merge(request()->except(['room_id', 'page']), ['room_id' => $room->id])) }}"
                           data-active="{{ request('room_id') == $room->id ? '1' : '0' }}"
                           class="tab-item whitespace-nowrap flex items-center gap-2 py-3.5 px-4 border-b-2 text-sm font-medium -mb-px transition-all
                               {{ request('room_id') == $room->id ? 'border-brand text-brand' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            <i data-lucide="door-open" class="h-4 w-4"></i>
                            {{ $room->name }}
                        </a>
                    @endforeach
                </nav>
            </div>
        </div>


        {{-- Filter Section --}}
        <div class="border-b border-gray-200">
            <button type="button" onclick="toggleFilter()"
                class="w-full flex items-center justify-between px-6 py-4 text-left hover:bg-gray-50 transition-colors">
                <div class="flex items-center">
                    <i data-lucide="sliders-horizontal" class="mr-2 h-5 w-5 text-brand"></i>
                    <h3 class="text-base font-semibold text-gray-900">Filter Data</h3>
                    @if (request()->hasAny(['date', 'search']))
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
                    <form method="GET" action="{{ route('room-reservations.index') }}">
                        {{-- Preserve active room tab --}}
                        @if (request('room_id'))
                            <input type="hidden" name="room_id" value="{{ request('room_id') }}">
                        @endif

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-2 items-end">
                            {{-- Date Filter --}}
                            <div class="space-y-2">
                                <label for="date" class="block text-sm font-medium text-gray-700">Tanggal Booking</label>
                                <input type="text" id="date" name="date" value="{{ $activeDate }}"
                                    class="input w-full" placeholder="Pilih tanggal...">
                            </div>

                            {{-- Search Filter --}}
                            <div class="space-y-2">
                                <label for="search" class="block text-sm font-medium text-gray-700">Cari Kegiatan / Pemohon</label>
                                <input type="text" id="search" name="search" value="{{ request('search') }}"
                                    class="input w-full rounded-md border-gray-300 shadow-sm" placeholder="Ketik kata kunci...">
                            </div>

                            {{-- Action Buttons --}}
                            <div class="flex items-center gap-3 col-span-full">
                                <button type="submit" class="btn-primary flex-1 sm:flex-none justify-center px-6">
                                    <i data-lucide="filter" class="mr-2 h-4 w-4"></i>
                                    Filter
                                </button>
                                <a href="{{ route('room-reservations.index', request()->has('room_id') ? ['room_id' => request('room_id')] : []) }}"
                                    class="btn-outline flex-1 sm:flex-none justify-center px-6">
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
                <form method="GET" action="{{ route('room-reservations.index') }}" class="inline-block">
                    {{-- Preserve all current query parameters --}}
                    @if (request('room_id'))
                        <input type="hidden" name="room_id" value="{{ request('room_id') }}">
                    @endif
                    <input type="hidden" name="date" value="{{ $activeDate }}">
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
            <div class="flex items-center gap-3">
                {{-- Active date badge --}}
                <span class="inline-flex items-center gap-1.5 text-xs font-medium px-2.5 py-1 rounded-full
                    {{ $activeDate === today()->toDateString() ? 'bg-blue-50 text-blue-700' : 'bg-amber-50 text-amber-700' }}">
                    <i data-lucide="calendar" class="w-3 h-3"></i>
                    {{ $activeDate === today()->toDateString() ? 'Hari Ini' : \Carbon\Carbon::parse($activeDate)->isoFormat('D MMM Y') }}
                </span>
                <span class="text-sm text-gray-500">
                    Total: <span class="font-semibold">{{ $reservations->total() }}</span> data
                </span>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            @if ($reservations->count() > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50">No</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50">Tanggal & Waktu</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50">Ruangan</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50 hidden lg:table-cell">Kegiatan</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50 hidden lg:table-cell">Pemohon</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50 hidden lg:table-cell">Dibuat Oleh</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($reservations as $index => $reservation)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ ($reservations->currentPage() - 1) * $reservations->perPage() + $index + 1 }}
                                </td>
                                <td class="px-3 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($reservation->reservation_date)->format('d M Y') }}</div>
                                    <div class="text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($reservation->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($reservation->end_time)->format('H:i') }}
                                    </div>
                                </td>
                                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <span class="font-semibold text-blue-800">{{ $reservation->room->name ?? '-' }}</span>
                                </td>
                                <td class="px-3 py-4 text-sm text-gray-900 hidden lg:table-cell">
                                    <div class="perihal-display font-medium">{{ $reservation->event_name }}</div>

                                </td>
                                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-900 hidden lg:table-cell">
                                    {{ $reservation->requester_name }}
                                </td>
                                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-900 hidden lg:table-cell">
                                    {{ $reservation->creator->name ?? 'Sistem' }}
                                </td>
                                <td class="px-3 py-4 whitespace-nowrap text-sm">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('room-reservations.show', $reservation) }}"
                                            class="inline-flex items-center rounded-md bg-blue-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                                            <i data-lucide="eye" class="mr-1.5 h-3.5 w-3.5"></i>
                                            Detail
                                        </a>
                                        @if(auth()->user()->isAdmin() || auth()->user()->id == $reservation->created_by)
                                            <a href="{{ route('room-reservations.edit', $reservation) }}" class="inline-flex items-center rounded-md bg-amber-500 px-3 py-1.5 text-xs font-medium text-white hover:bg-amber-600 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 transition-colors">
                                                <i data-lucide="edit-2" class="mr-1.5 h-3.5 w-3.5"></i>
                                                Edit
                                            </a>
                                            <form action="{{ route('room-reservations.destroy', $reservation) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus permohonan ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center rounded-md bg-red-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
                                                    <i data-lucide="trash-2" class="mr-1.5 h-3.5 w-3.5"></i>
                                                    Hapus
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
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $reservations->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <i data-lucide="inbox" class="mx-auto h-12 w-12 text-gray-400"></i>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada data peminjaman</h3>
                    <p class="mt-1 text-sm text-gray-500">Belum ada peminjaman yang sesuai dengan filter yang dipilih.</p>
                    <div class="mt-6">
                        <a href="{{ route('room-reservations.create') }}"
                            class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700">
                            <i data-lucide="plus-circle" class="mr-2 h-4 w-4"></i>
                            Buat Permohonan Baru
                        </a>
                    </div>
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
                @if(request()->hasAny(['date', 'search']))
                    const content = document.getElementById('filter-content');
                    const icon = document.getElementById('filter-icon');
                    content.classList.remove('hidden');
                    icon.style.transform = 'rotate(180deg)';
                @endif

                flatpickr("#date", {
                    dateFormat: "Y-m-d",
                    altInput: true,
                    altFormat: "d M Y",
                    locale: "id",
                    placeholder: "Pilih tanggal..."
                });

                lucide.createIcons();

                // === Tab scroll logic ===
                const tabContainer = document.getElementById('tab-scroll-container');
                const fadeLeft = document.getElementById('tab-fade-left');
                const fadeRight = document.getElementById('tab-fade-right');

                function updateFades() {
                    const { scrollLeft, scrollWidth, clientWidth } = tabContainer;
                    fadeLeft.classList.toggle('hidden', scrollLeft <= 4);
                    fadeRight.classList.toggle('hidden', scrollLeft + clientWidth >= scrollWidth - 4);
                }

                // Scroll tab aktif ke tengah layar
                const activeTab = tabContainer.querySelector('.tab-item[data-active="1"]');
                if (activeTab) {
                    const containerWidth = tabContainer.clientWidth;
                    const tabLeft = activeTab.offsetLeft;
                    const tabWidth = activeTab.offsetWidth;
                    tabContainer.scrollLeft = tabLeft - containerWidth / 2 + tabWidth / 2;
                }

                updateFades();
                tabContainer.addEventListener('scroll', updateFades);
                window.addEventListener('resize', updateFades);
            });
        </script>
    @endpush
@endsection
