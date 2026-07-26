@extends('layouts.app')

@section('title', 'Dashboard Peminjaman Ruang')

@section('content')
    <div class="mb-8">
        <div class="bg-blue-600 rounded-lg p-6 text-white shadow-lg relative overflow-hidden">
            <div class="absolute inset-0 bg-blue-700 opacity-20"></div>
            <div class="relative flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold mb-2 flex items-center">
                        <i data-lucide="layout-dashboard" class="mr-3 h-7 w-7"></i>
                        Dashboard Peminjaman Ruang
                    </h1>
                    <p class="text-blue-100">
                        Pantau jadwal peminjaman ruang sidang dan ketersediaan fasilitas.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid gap-6 mb-8 md:grid-cols-2 xl:grid-cols-4">
        {{-- Total Hari Ini --}}
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-shadow duration-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100">
                        <i data-lucide="calendar-check" class="h-6 w-6 text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Peminjaman Hari Ini</p>
                        <h3 class="text-2xl font-bold text-gray-900">{{ $totalToday }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Bulan Ini --}}
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-shadow duration-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-purple-100">
                        <i data-lucide="bar-chart-2" class="h-6 w-6 text-purple-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Bulan Ini</p>
                        <h3 class="text-2xl font-bold text-gray-900">{{ $totalThisMonth }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Ruangan Aktif --}}
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-shadow duration-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-100">
                        <i data-lucide="door-open" class="h-6 w-6 text-green-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Ruangan</p>
                        <h3 class="text-2xl font-bold text-gray-900">{{ $totalRooms }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Action --}}
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-shadow duration-200 flex items-center justify-center p-6">
            <a href="{{ route('room-reservations.create') }}" class="w-full flex items-center justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none">
                <i data-lucide="plus-circle" class="mr-2 h-5 w-5"></i>
                Buat Peminjaman
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8 mb-8">
        {{-- Kalender Penjadwalan --}}
        <div class="xl:col-span-2 bg-white rounded-lg border border-gray-200 shadow-sm flex flex-col">
            <div class="border-b border-gray-200 px-6 py-4 flex justify-between items-center flex-wrap gap-4">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i data-lucide="calendar-days" class="mr-2 h-5 w-5 text-blue-600"></i>
                    Kalender Penjadwalan
                </h3>
                <div class="flex items-center gap-2 text-sm text-gray-500 bg-gray-50 px-3 py-1.5 rounded-md border border-gray-100">
                    <i data-lucide="info" class="w-4 h-4 text-blue-500"></i>
                    <span><strong>Tips:</strong> Klik agenda/tanggal untuk detail.</span>
                </div>
            </div>
            <div class="p-6 flex-1">
                <div id="room-legend" class="hidden flex-wrap items-center gap-x-4 gap-y-1.5 mb-4 px-1"></div>
                <div id="calendar" class="min-h-[400px]"></div>
            </div>
        </div>

        {{-- Jadwal Baru Ditambahkan --}}
        <div class="xl:col-span-1 bg-white rounded-lg border border-gray-200 shadow-sm flex flex-col">
            <div class="border-b border-gray-200 px-6 py-4 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i data-lucide="clock" class="mr-2 h-5 w-5 text-blue-600"></i>
                    Baru Ditambahkan
                </h3>
                <a href="{{ route('room-reservations.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                    Lihat Semua
                </a>
            </div>
            <div class="p-6 flex-1">
                @if ($recentReservations->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jadwal & Ruangan</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kegiatan & Pemohon</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach ($recentReservations as $reservation)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-3">
                                            <div class="text-sm font-medium text-blue-700">{{ \Carbon\Carbon::parse($reservation->reservation_date)->format('d M Y') }}</div>
                                            <div class="text-xs text-gray-700 mt-0.5 font-medium">{{ \Carbon\Carbon::parse($reservation->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($reservation->end_time)->format('H:i') }}</div>
                                            <div class="text-xs text-gray-500 mt-0.5">{{ $reservation->room->name ?? '-' }}</div>
                                        </td>
                                        <td class="px-3 py-3">
                                            <div class="text-sm text-gray-900 font-medium line-clamp-1">{{ $reservation->event_name }}</div>
                                            <div class="text-xs text-gray-500 mt-0.5 flex flex-col gap-0.5">
                                                <span><i data-lucide="user" class="inline w-3 h-3 mr-0.5"></i> {{ $reservation->requester_name }}</span>
                                                <span class="text-blue-600"><i data-lucide="shield-check" class="inline w-3 h-3 mr-0.5"></i> Dibuat: {{ $reservation->creator->name ?? '-' }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-12">
                        <i data-lucide="calendar-x" class="mx-auto h-12 w-12 text-gray-400"></i>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Kosong</h3>
                        <p class="mt-1 text-sm text-gray-500">Belum ada jadwal yang ditambahkan.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendarEvents = {!! json_encode($calendarEvents) !!};

        function openReservationDetailModal(props) {
            Swal.fire({
                html: `
                    <div class="text-left -mx-6 -mt-2">
                        <!-- Header -->
                        <div class="px-5 pt-5 pb-3.5 border-b border-gray-100">
                            <p class="text-[11px] font-bold text-blue-600 uppercase tracking-wide mb-1">Detail Peminjaman</p>
                            <h4 class="font-bold text-gray-900 text-base leading-snug pr-2">${props.event_name}</h4>
                        </div>

                        <!-- Body -->
                        <div class="px-5 py-3.5">
                            <div class="divide-y divide-gray-50">
                                <div class="flex items-center justify-between py-2.5">
                                    <span class="flex items-center gap-2 text-xs text-gray-500">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Waktu
                                    </span>
                                    <span class="text-sm font-semibold text-gray-900">${props.time_range}</span>
                                </div>
                                <div class="flex items-center justify-between py-2.5">
                                    <span class="flex items-center gap-2 text-xs text-gray-500">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                        Ruangan
                                    </span>
                                    <span class="text-sm font-semibold text-gray-900 truncate max-w-[11rem] text-right">${props.room_name}</span>
                                </div>
                                <div class="flex items-center justify-between py-2.5">
                                    <span class="flex items-center gap-2 text-xs text-gray-500">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        Penanggung Jawab
                                    </span>
                                    <span class="text-sm font-semibold text-gray-900 truncate max-w-[11rem] text-right">${props.requester_name}</span>
                                </div>
                                <div class="flex items-center justify-between py-2.5">
                                    <span class="flex items-center gap-2 text-xs text-gray-500">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"></path></svg>
                                        Dibuat Oleh
                                    </span>
                                    <span class="text-sm font-semibold text-gray-900 truncate max-w-[11rem] text-right">${props.creator_name}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                `,
                showConfirmButton: false,
                showCancelButton: true,
                cancelButtonText: 'Tutup',
                buttonsStyling: false,
                padding: 0,
                width: '22rem',
                customClass: {
                    popup: 'rounded-2xl shadow-xl border border-gray-100 overflow-hidden !pb-4',
                    actions: 'px-5 !mt-0',
                    cancelButton: 'bg-gray-800 hover:bg-gray-900 active:scale-95 text-white font-bold tracking-wide rounded-xl px-5 py-2.5 transition-all w-full text-sm shadow-md'
                }
            });
        }

        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'id',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth'
            },
            buttonText: {
                today: 'Hari Ini',
                month: 'Bulan'
            },
            events: calendarEvents,
            dayMaxEvents: 2, // Limit events per day to 2, then show "+x more"
            eventTimeFormat: {
                hour: '2-digit',
                minute: '2-digit',
                meridiem: false,
                hour12: false
            },
            eventClick: function(info) {
                info.jsEvent.preventDefault();
                openReservationDetailModal(info.event.extendedProps);
            },
            dateClick: function(info) {
                const clickedDate = info.dateStr;
                const eventsOnDate = calendarEvents.filter(event => event.start.startsWith(clickedDate));

                if (eventsOnDate.length === 0) {
                    Swal.fire({
                        html: `
                            <div class="flex flex-col items-center py-2">
                                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-blue-50 mb-3">
                                    <svg class="w-7 h-7 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                                <h3 class="text-base font-bold text-gray-900">Ruangan Kosong</h3>
                                <p class="text-sm text-gray-500 mt-1 text-center px-2">Tidak ada jadwal peminjaman pada tanggal ini.</p>
                            </div>
                        `,
                        showCancelButton: true,
                        confirmButtonText: 'Buat Peminjaman',
                        cancelButtonText: 'Tutup',
                        buttonsStyling: false,
                        width: '22rem',
                        customClass: {
                            popup: 'rounded-2xl shadow-xl border border-gray-100',
                            confirmButton: 'bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl px-4 py-2 transition-colors text-sm mx-1',
                            cancelButton: 'bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl px-4 py-2 transition-colors text-sm mx-1'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "{{ route('room-reservations.create') }}";
                        }
                    });
                    return;
                }

                // Group by room, then sort each group by time
                const grouped = {};
                eventsOnDate.forEach(event => {
                    const room = event.extendedProps.room_name || 'Ruangan Lain';
                    if (!grouped[room]) grouped[room] = [];
                    grouped[room].push(event);
                });
                Object.keys(grouped).forEach(room => {
                    grouped[room].sort((a, b) => a.start.localeCompare(b.start));
                });

                let eventsHtml = '<div class="space-y-4 max-h-[24rem] overflow-y-auto pr-1.5 custom-scrollbar">';
                let uid = 0;
                const eventMap = {}; // simpan referensi extendedProps utk modal detail saat panah diklik

                Object.keys(grouped).sort().forEach(room => {
                    eventsHtml += `
                        <div class="room-group" data-room="${room.toLowerCase()}">
                            <div class="flex items-center gap-1.5 mb-1.5 px-0.5">
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                <span class="text-xs font-bold text-gray-500 uppercase tracking-wide">${room}</span>
                            </div>
                            <div class="space-y-1.5">
                    `;

                    grouped[room].forEach(event => {
                        const requester = event.extendedProps.requester_name || '-';
                        const creator = event.extendedProps.creator_name || '-';
                        const isSameCreator = requester.trim().toLowerCase() === creator.trim().toLowerCase();
                        const id = `agenda-${uid++}`;
                        eventMap[id] = event.extendedProps;

                        eventsHtml += `
                            <div class="agenda-item flex items-center gap-3 px-3 py-2.5 rounded-lg border border-gray-100 bg-white hover:bg-blue-50/50 hover:border-blue-200 transition-colors cursor-pointer" data-agenda-id="${id}">
                                <span class="shrink-0 text-[11px] font-bold text-blue-700 bg-blue-50 px-2 py-1 rounded-md whitespace-nowrap">${event.extendedProps.time_range}</span>
                                <div class="min-w-0 flex-1 searchable-text">
                                    <p class="text-sm font-semibold text-gray-900 truncate" title="${event.extendedProps.event_name}">${event.extendedProps.event_name}</p>

                                    <div class="flex items-center gap-1.5 mt-1 min-w-0">
                                        <svg class="w-3 h-3 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        <p class="text-xs text-gray-600 truncate min-w-0" title="${requester}">${requester}</p>
                                    </div>

                                    ${!isSameCreator ? `
                                    <div class="flex items-center gap-1.5 mt-0.5 min-w-0">
                                        <svg class="w-3 h-3 text-gray-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15"></path></svg>
                                        <p class="text-[11px] text-gray-400 truncate min-w-0" title="Dibuat oleh ${creator}">dibuat oleh ${creator}</p>
                                    </div>
                                    ` : ''}
                                </div>
                                <button type="button" class="agenda-detail-btn shrink-0 p-1 -m-1 rounded-full hover:bg-blue-100 transition-colors" data-agenda-id="${id}" aria-label="Lihat detail">
                                    <svg class="w-3.5 h-3.5 text-gray-300 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </button>
                            </div>
                        `;
                    });

                    eventsHtml += `</div></div>`;
                });
                eventsHtml += '</div>';

                const dateObj = new Date(clickedDate);
                const formattedDate = dateObj.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });

                Swal.fire({
                    html: `
                        <div class="text-left -mx-6 -mt-2">
                            <div class="px-6 pt-5 pb-3 border-b border-gray-100">
                                <div class="flex items-center justify-between mb-4">
                                    <div>
                                        <h3 class="text-base font-bold text-gray-900">Agenda Harian</h3>
                                        <p class="text-xs text-gray-500 mt-0.5">${formattedDate}</p>
                                    </div>
                                    <span class="text-xs font-bold text-blue-700 bg-blue-50 px-2.5 py-1 rounded-full">${eventsOnDate.length} agenda</span>
                                </div>

                                <!-- Search Input -->
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                    </div>
                                    <input type="text" id="search-agenda" class="block w-full pl-9 pr-3 py-2 border border-gray-200 rounded-lg text-sm placeholder-gray-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors bg-gray-50 focus:bg-white" placeholder="Cari kegiatan, ruangan, atau pemohon...">
                                </div>
                            </div>
                            <div class="px-6 py-4" id="agenda-list-container">
                                ${eventsHtml}
                                <div id="no-agenda-found" class="hidden text-center py-6">
                                    <svg class="mx-auto h-8 w-8 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                    <p class="text-sm font-medium text-gray-600">Pencarian tidak ditemukan</p>
                                </div>
                            </div>
                        </div>
                    `,
                    showConfirmButton: true,
                    confirmButtonText: 'Tutup',
                    buttonsStyling: false,
                    padding: 0,
                    width: '28rem',
                    customClass: {
                        popup: 'rounded-2xl shadow-xl border border-gray-100 overflow-hidden !pb-4',
                        actions: 'px-6 !mt-0',
                        confirmButton: 'bg-gray-800 hover:bg-gray-900 text-white font-bold tracking-wide rounded-xl px-5 py-2.5 transition-colors w-full text-sm shadow-md'
                    },
                    didOpen: () => {
                        const searchInput = document.getElementById('search-agenda');
                        const roomGroups = document.querySelectorAll('.room-group');
                        const noFoundMsg = document.getElementById('no-agenda-found');
                        const listContainer = document.getElementById('agenda-list-container');

                        searchInput.addEventListener('input', function(e) {
                            const term = e.target.value.toLowerCase();
                            let totalVisible = 0;

                            roomGroups.forEach(group => {
                                const roomName = group.getAttribute('data-room');
                                const items = group.querySelectorAll('.agenda-item');
                                let groupVisible = false;

                                items.forEach(item => {
                                    const textContent = item.querySelector('.searchable-text').textContent.toLowerCase();
                                    if (roomName.includes(term) || textContent.includes(term)) {
                                        item.style.display = 'flex';
                                        groupVisible = true;
                                        totalVisible++;
                                    } else {
                                        item.style.display = 'none';
                                    }
                                });

                                group.style.display = groupVisible ? 'block' : 'none';
                            });

                            noFoundMsg.classList.toggle('hidden', totalVisible !== 0);
                        });

                        // Satu listener terdelegasi untuk seluruh list agenda
                        listContainer.addEventListener('click', function(e) {
                            const item = e.target.closest('.agenda-item');
                            if (!item) return;

                            const id = item.getAttribute('data-agenda-id');
                            openReservationDetailModal(eventMap[id]);
                        });
                    }
                });
            },
            themeSystem: 'standard',
            height: 750
        });

        calendar.render();

        // Re-render icons if needed
        setTimeout(() => {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }, 500);
    });
    </script>
    <style>
        /* Custom Styling for FullCalendar to match the theme */
        .fc {
            --fc-page-bg-color: #ffffff;
            --fc-neutral-bg-color: #f9fafb;
            --fc-neutral-text-color: #6b7280;
            --fc-border-color: #f3f4f6;
            --fc-button-text-color: #374151;
            --fc-button-bg-color: #ffffff;
            --fc-button-border-color: #e5e7eb;
            --fc-button-hover-bg-color: #f3f4f6;
            --fc-button-hover-border-color: #d1d5db;
            --fc-button-active-bg-color: #e5e7eb;
            --fc-button-active-border-color: #d1d5db;
            --fc-event-bg-color: #2563eb;
            --fc-event-border-color: #1d4ed8;
            --fc-event-text-color: #ffffff;
            --fc-event-selected-overlay-color: rgba(0, 0, 0, 0.25);
            --fc-more-link-bg-color: #f3f4f6;
            --fc-more-link-text-color: #4b5563;
            --fc-event-resizer-thickness: 8px;
            --fc-event-resizer-dot-total-width: 8px;
            --fc-event-resizer-dot-border-width: 1px;
            --fc-non-business-color: rgba(215, 215, 215, 0.3);
            --fc-bg-event-color: rgb(143, 223, 130);
            --fc-bg-event-opacity: 0.3;
            --fc-highlight-color: rgba(37, 99, 235, 0.05);
            --fc-today-bg-color: rgba(37, 99, 235, 0.05);
            --fc-now-indicator-color: #ef4444;
        }
        
        .fc .fc-toolbar-title {
            font-size: 1.25rem !important;
            font-weight: 800 !important;
            color: #111827;
            letter-spacing: -0.025em;
        }
        .fc .fc-button {
            border-radius: 0.5rem;
            padding: 0.4rem 1rem;
            font-weight: 600;
            text-transform: capitalize;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            transition: all 0.2s;
        }
        .fc .fc-button-primary:not(:disabled).fc-button-active, 
        .fc .fc-button-primary:not(:disabled):active {
            background-color: #e5e7eb !important;
            border-color: #d1d5db !important;
            color: #111827 !important;
        }
        
        /* Modernize grid */
        .fc-theme-standard td, .fc-theme-standard th {
            border-color: #f3f4f6;
        }
        .fc-theme-standard .fc-scrollgrid {
            border-color: #f3f4f6;
            border-radius: 0.75rem;
            overflow: hidden;
            border-right: 1px solid #f3f4f6;
            border-bottom: 1px solid #f3f4f6;
        }
        .fc-scrollgrid-sync-table {
            border-collapse: separate !important;
        }
        
        /* Header cells */
        .fc .fc-col-header-cell-cushion {
            color: #6b7280;
            font-weight: 700;
            font-size: 0.875rem;
            padding: 12px 8px;
            text-decoration: none !important;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .fc .fc-col-header-cell {
            background-color: #f9fafb;
        }

        /* Day cells */
        .fc .fc-daygrid-day-number {
            color: #4b5563;
            font-weight: 600;
            font-size: 0.875rem;
            padding: 8px;
            text-decoration: none !important;
            width: 2rem;
            height: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 4px;
            border-radius: 9999px;
            transition: background-color 0.2s;
        }
        .fc .fc-daygrid-day-number:hover {
            background-color: #f3f4f6;
        }
        .fc .fc-daygrid-day.fc-day-today .fc-daygrid-day-number {
            background-color: #2563eb;
            color: white;
        }
        .fc .fc-daygrid-day.fc-day-today {
            background-color: #f0fdfa !important; /* Subtle teal for today */
        }
        .fc .fc-daygrid-day:hover {
            background-color: #f9fafb;
            cursor: pointer;
        }

        /* Events */
        .fc-daygrid-event-harness {
            margin: 0 4px !important;
        }
        .fc .fc-event {
            border-radius: 6px;
            padding: 3px 6px;
            font-size: 0.75rem;
            font-weight: 600;
            border: none;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease;
            margin-bottom: 3px !important;
        }
        .fc .fc-event:hover {
            transform: translateY(-1px) scale(1.02);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            cursor: pointer;
            z-index: 10;
        }
        
        /* "+ more" link */
        .fc .fc-daygrid-more-link {
            font-weight: 700;
            font-size: 0.75rem;
            color: #2563eb;
            background-color: #eff6ff;
            padding: 2px 6px;
            border-radius: 4px;
            display: inline-block;
            margin: 2px 4px;
            transition: all 0.2s;
            text-decoration: none !important;
        }
        .fc .fc-daygrid-more-link:hover {
            background-color: #dbeafe;
            color: #1d4ed8;
        }
        
        /* Custom scrollbar for SweetAlert modal */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1; 
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #c1c1c1; 
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8; 
        }
    </style>
@endpush
