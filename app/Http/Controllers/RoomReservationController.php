<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\RoomGroup;
use App\Models\RoomReservation;
class RoomReservationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['publicDisplay', 'publicDisplayTimeGrid']);
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->can_access_room_reservations && !auth()->user()->isAdmin()) {
                abort(403, 'Anda tidak memiliki akses ke Modul Peminjaman Ruang.');
            }
            return $next($request);
        })->except(['publicDisplay', 'publicDisplayTimeGrid']);
    }

    public function dashboard()
    {
        $today = today();
        $totalToday = RoomReservation::whereDate('reservation_date', $today)->count();
        $totalThisMonth = RoomReservation::whereMonth('reservation_date', $today->month)
            ->whereYear('reservation_date', $today->year)
            ->count();
        $totalRooms = Room::where('is_active', true)->count();
        $totalAll = RoomReservation::count();
        $totalUpcoming = RoomReservation::whereDate('reservation_date', '>=', $today)->count();

        // Agenda Hari Ini
        $todayReservations = RoomReservation::with(['room', 'creator'])
            ->whereDate('reservation_date', $today)
            ->orderBy('start_time', 'asc')
            ->get();

        // 8 Aktivitas Peminjaman Terbaru
        $recentReservations = RoomReservation::with(['room', 'creator'])
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        // Ruangan Terpopuler (Top 5)
        $topRooms = Room::withCount('reservations')
            ->where('is_active', true)
            ->orderByDesc('reservations_count')
            ->take(5)
            ->get();

        // List Ruangan & Pemetaan Warna Apple
        $rooms = Room::where('is_active', true)->orderBy('name')->get();
        $palette = [
            '#ea580c', '#2563eb', '#059669', '#7c3aed', 
            '#d97706', '#0284c7', '#db2777', '#16a34a', 
            '#4f46e5', '#ca8a04'
        ];
        $roomColors = [];
        foreach ($rooms as $idx => $r) {
            $roomColors[$r->id] = $palette[$idx % count($palette)];
        }

        // Calendar Events dengan Color Coding per Ruangan
        $allReservations = RoomReservation::with(['room', 'creator'])->get();
        $calendarEvents = $allReservations->map(function($res) use ($roomColors) {
            $roomColor = $roomColors[$res->room_id] ?? '#ea580c';
            return [
                'id' => $res->id,
                'title' => ($res->room->name ?? 'Ruangan Dihapus') . ' - ' . $res->event_name,
                'start' => $res->reservation_date->format('Y-m-d') . 'T' . \Carbon\Carbon::parse($res->start_time)->format('H:i:s'),
                'end' => $res->reservation_date->format('Y-m-d') . 'T' . \Carbon\Carbon::parse($res->end_time)->format('H:i:s'),
                'backgroundColor' => $roomColor,
                'borderColor' => $roomColor,
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'id' => $res->id,
                    'room_name' => $res->room->name ?? 'Ruangan Dihapus',
                    'event_name' => $res->event_name,
                    'requester_name' => $res->requester_name,
                    'contact_person' => $res->contact_person ?? '-',
                    'facilities' => $res->facilities ?? '-',
                    'remarks' => $res->remarks ?? '-',
                    'creator_name' => $res->creator->name ?? 'Sistem',
                    'time_range' => \Carbon\Carbon::parse($res->start_time)->format('H:i') . ' - ' . \Carbon\Carbon::parse($res->end_time)->format('H:i') . ' WIB',
                    'date_formatted' => $res->reservation_date->translatedFormat('l, d F Y'),
                    'detail_url' => route('room-reservations.show', $res->id),
                    'status' => $res->status ?? 'approved',
                ]
            ];
        });

        return view('room_reservations.dashboard', compact(
            'recentReservations', 
            'todayReservations',
            'totalToday', 
            'totalThisMonth', 
            'totalRooms', 
            'totalAll',
            'totalUpcoming',
            'topRooms',
            'rooms',
            'roomColors',
            'calendarEvents'
        ));
    }

    public function publicDisplay(Request $request)
    {
        // 1. Fetch Today's Data
        $query = RoomReservation::with(['room', 'creator'])
            ->whereDate('reservation_date', today())
            ->where('status', '!=', 'rejected')
            ->where('status', '!=', 'cancelled');

        $selectedRoomId = null;
        if ($request->filled('room_id')) {
            try {
                $selectedRoomId = decrypt($request->room_id);
                $query->where('room_id', $selectedRoomId);
            } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                // Invalid encrypted payload, ignore
            }
        }

        $reservations = $query->orderBy('start_time')->get();

        $scheduleData = $reservations->map(function($r) {
            return [
                'id' => $r->id,
                'event_name' => $r->event_name,
                'requester_name' => $r->requester_name,
                'contact_person' => $r->contact_person,
                'room_id' => $r->room_id,
                'room_name' => $r->room->name ?? 'Lainnya',
                'start' => \Carbon\Carbon::parse($r->start_time)->valueOf(),
                'end' => \Carbon\Carbon::parse($r->end_time)->valueOf(),
                'start_label' => \Carbon\Carbon::parse($r->start_time)->format('H:i'),
                'end_label' => \Carbon\Carbon::parse($r->end_time)->format('H:i'),
            ];
        })->sortBy('start')->values();

        // 2. Fetch Monthly Data for Calendar
        $startOfMonth = today()->startOfMonth();
        $endOfMonth = today()->endOfMonth();
        
        $monthlyQuery = RoomReservation::with(['room'])
            ->whereBetween('reservation_date', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
            ->where('status', '!=', 'rejected')
            ->where('status', '!=', 'cancelled');

        if ($selectedRoomId) {
            $monthlyQuery->where('room_id', $selectedRoomId);
        }

        $monthlyReservations = $monthlyQuery->orderBy('reservation_date')->orderBy('start_time')->get();

        $monthlyScheduleData = $monthlyReservations->map(function($r) {
            return [
                'id' => $r->id,
                'event_name' => $r->event_name,
                'room_name' => $r->room->name ?? 'Lainnya',
                'requester_name' => $r->requester_name ?? 'Umum',
                'creator_name' => $r->creator->name ?? 'Sistem',
                'date' => \Carbon\Carbon::parse($r->reservation_date)->format('Y-m-d'),
                'start_label' => \Carbon\Carbon::parse($r->start_time)->format('H:i'),
                'end_label' => \Carbon\Carbon::parse($r->end_time)->format('H:i'),
                'title' => ($r->room->name ?? 'Lainnya') . ' - ' . $r->event_name,
                'start' => \Carbon\Carbon::parse($r->reservation_date)->format('Y-m-d') . 'T' . \Carbon\Carbon::parse($r->start_time)->format('H:i:s'),
                'end' => \Carbon\Carbon::parse($r->reservation_date)->format('Y-m-d') . 'T' . \Carbon\Carbon::parse($r->end_time)->format('H:i:s'),
                'backgroundColor' => '#ea580c', // orange-600 for public display theme
                'borderColor' => '#c2410c', // orange-700
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'room_name' => $r->room->name ?? 'Lainnya',
                    'event_name' => $r->event_name,
                    'requester_name' => $r->requester_name,
                    'creator_name' => $r->creator->name ?? 'Sistem',
                    'time_range' => \Carbon\Carbon::parse($r->start_time)->format('H:i') . ' - ' . \Carbon\Carbon::parse($r->end_time)->format('H:i'),
                ]
            ];
        })->values();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'schedule' => $scheduleData,
                'monthlySchedule' => $monthlyScheduleData
            ]);
        }

        // Ambil data untuk hirarki tampilan grid ruangan
        $totalRooms = Room::where('is_active', true)->count();
        $roomGroups = RoomGroup::with(['rooms' => function($q) {
            $q->where('is_active', true)->orderBy('name');
        }])->where('is_active', true)->orderBy('name')->get();

        $ungroupedRooms = Room::where('is_active', true)
            ->whereNull('room_group_id')
            ->orderBy('name')->get();
        
        $selectedRoom = null;
        if ($selectedRoomId) {
            $selectedRoom = Room::find($selectedRoomId);
        }

        return view('room_reservations.public_display', compact(
            'reservations', 
            'roomGroups', 
            'ungroupedRooms', 
            'totalRooms',
            'selectedRoom', 
            'scheduleData', 
            'monthlyScheduleData'
        ));
    }

    public function publicDisplayTimeGrid(Request $request)
    {
        $date = $request->input('date', today()->toDateString());
        $parsedDate = \Carbon\Carbon::parse($date);
        
        $query = RoomReservation::with(['room', 'creator'])
            ->whereDate('reservation_date', $parsedDate)
            ->where('status', '!=', 'rejected')
            ->where('status', '!=', 'cancelled');
            
        $selectedRoom = null;
        if ($request->filled('room_id')) {
            try {
                $selectedRoomId = decrypt($request->room_id);
                $query->where('room_id', $selectedRoomId);
                $selectedRoom = \App\Models\Room::find($selectedRoomId);
            } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                // Invalid encrypted payload, ignore
            }
        }
            
        $reservations = $query->orderBy('start_time')->get();
            
        $rooms = \App\Models\Room::where('is_active', true)->orderBy('name')->get();
        
        return view('room_reservations.partials.timegrid', compact('reservations', 'rooms', 'parsedDate', 'selectedRoom'));
    }

    public function index(Request $request)
    {
        $rooms = Room::where('is_active', true)->orderBy('name')->get();

        // Default to today if no date filter provided
        $activeDate = $request->filled('date') ? $request->date : today()->toDateString();

        $query = RoomReservation::with(['room', 'creator', 'approver']);

        // Tab filter by room
        if ($request->filled('room_id')) {
            $query->where('room_id', $request->room_id);
        }

        // Always filter by date (default: today)
        $query->whereDate('reservation_date', $activeDate);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('event_name', 'like', "%{$search}%")
                  ->orWhere('requester_name', 'like', "%{$search}%");
            });
        }

        $perPage = $request->input('per_page', 10);
        $reservations = $query->orderBy('reservation_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->paginate($perPage);

        return view('room_reservations.index', compact('reservations', 'rooms', 'activeDate'));
    }

    public function create()
    {
        $rooms = Room::where('is_active', true)->orderBy('name')->get();
        return view('room_reservations.create', compact('rooms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'reservation_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'event_name' => 'required|string|max:255',
            'requester_name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:50',
            'facilities' => 'nullable|string',
            'remarks' => 'nullable|string',
        ], [
            'end_time.after' => 'Waktu selesai harus lebih besar/setelah waktu mulai.',
        ]);

        // Cek Bentrok (Double booking check)
        $conflict = RoomReservation::with('creator')->where('room_id', $validated['room_id'])
            ->whereDate('reservation_date', $validated['reservation_date'])
            ->where('status', '!=', 'rejected')
            ->where('status', '!=', 'cancelled')
            ->where(function($query) use ($validated) {
                $query->whereBetween('start_time', [$validated['start_time'], $validated['end_time']])
                      ->orWhereBetween('end_time', [$validated['start_time'], $validated['end_time']])
                      ->orWhere(function($q) use ($validated) {
                          $q->where('start_time', '<=', $validated['start_time'])
                            ->where('end_time', '>=', $validated['end_time']);
                      });
            })
            ->first();

        if ($conflict) {
            $creatorName = $conflict->creator ? $conflict->creator->name : 'Seseorang';
            $startTime = \Carbon\Carbon::parse($conflict->start_time)->format('H:i');
            $endTime = \Carbon\Carbon::parse($conflict->end_time)->format('H:i');
            return back()->withInput()->withErrors(['conflict' => "Ruangan sudah dibooking oleh <strong>{$creatorName}</strong> dari jam <strong>{$startTime}</strong> sampai <strong>{$endTime}</strong>."]);
        }

        $validated['created_by'] = auth()->id();
        $validated['status'] = 'approved';

        RoomReservation::create($validated);

        return redirect()->route('room-reservations.index')->with('success', 'Peminjaman ruang berhasil dibuat.');
    }

    public function show(RoomReservation $roomReservation)
    {
        $roomReservation->load(['room', 'creator', 'approver']);
        return view('room_reservations.show', compact('roomReservation'));
    }

    public function edit(RoomReservation $roomReservation)
    {
        if (!auth()->user()->isAdmin() && auth()->id() !== $roomReservation->created_by) {
            abort(403, 'Anda tidak diizinkan mengubah peminjaman ini.');
        }

        $rooms = Room::where('is_active', true)->orderBy('name')->get();
        return view('room_reservations.edit', compact('roomReservation', 'rooms'));
    }

    public function update(Request $request, RoomReservation $roomReservation)
    {
        if (!auth()->user()->isAdmin() && auth()->id() !== $roomReservation->created_by) {
            abort(403, 'Anda tidak diizinkan mengubah peminjaman ini.');
        }

        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'reservation_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'event_name' => 'required|string|max:255',
            'requester_name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:50',
            'facilities' => 'nullable|string',
            'remarks' => 'nullable|string',
        ], [
            'end_time.after' => 'Waktu selesai harus lebih besar/setelah waktu mulai.',
        ]);

        // Cek Bentrok jika jadwal diubah
        $conflict = RoomReservation::with('creator')->where('room_id', $validated['room_id'])
            ->where('id', '!=', $roomReservation->id)
            ->whereDate('reservation_date', $validated['reservation_date'])
            ->where('status', '!=', 'rejected')
            ->where('status', '!=', 'cancelled')
            ->where(function($query) use ($validated) {
                $query->whereBetween('start_time', [$validated['start_time'], $validated['end_time']])
                      ->orWhereBetween('end_time', [$validated['start_time'], $validated['end_time']])
                      ->orWhere(function($q) use ($validated) {
                          $q->where('start_time', '<=', $validated['start_time'])
                            ->where('end_time', '>=', $validated['end_time']);
                      });
            })
            ->first();

        if ($conflict) {
            $creatorName = $conflict->creator ? $conflict->creator->name : 'Seseorang';
            $startTime = \Carbon\Carbon::parse($conflict->start_time)->format('H:i');
            $endTime = \Carbon\Carbon::parse($conflict->end_time)->format('H:i');
            return back()->withInput()->withErrors(['conflict' => "Ruangan sudah dibooking oleh <strong>{$creatorName}</strong> dari jam <strong>{$startTime}</strong> sampai <strong>{$endTime}</strong>."]);
        }

        $roomReservation->update($validated);

        return redirect()->route('room-reservations.show', $roomReservation)->with('success', 'Data peminjaman berhasil diperbarui.');
    }

    public function destroy(RoomReservation $roomReservation)
    {
        if (!auth()->user()->isAdmin() && auth()->id() !== $roomReservation->created_by) {
            abort(403, 'Anda tidak diizinkan menghapus peminjaman ini.');
        }

        $roomReservation->delete();
        return redirect()->route('room-reservations.index')->with('success', 'Data peminjaman berhasil dihapus.');
    }
}
