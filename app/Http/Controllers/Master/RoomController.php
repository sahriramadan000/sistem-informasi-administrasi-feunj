<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index(Request $request)
    {
        $query = Room::with('roomGroup');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }

        $perPage = $request->input('per_page', 10);
        $rooms = $query->orderBy('name')->paginate($perPage);

        return view('master.rooms.index', compact('rooms'));
    }

    public function create()
    {
        $roomGroups = \App\Models\RoomGroup::where('is_active', true)->orderBy('name')->get();
        return view('master.rooms.create', compact('roomGroups'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'room_group_id' => 'nullable|exists:room_groups,id',
            'capacity' => 'nullable|integer|min:1',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        Room::create($validated);

        return redirect()->route('master.rooms.index')->with('success', 'Ruangan berhasil ditambahkan.');
    }

    public function edit(Room $room)
    {
        $roomGroups = \App\Models\RoomGroup::where('is_active', true)->orderBy('name')->get();
        return view('master.rooms.edit', compact('room', 'roomGroups'));
    }

    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'room_group_id' => 'nullable|exists:room_groups,id',
            'capacity' => 'nullable|integer|min:1',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $room->update($validated);

        return redirect()->route('master.rooms.index')->with('success', 'Ruangan berhasil diperbarui.');
    }

    public function destroy(Room $room)
    {
        // Check if room is used in reservations
        if ($room->reservations()->exists()) {
            return back()->withErrors(['error' => 'Ruangan tidak dapat dihapus karena sudah memiliki histori peminjaman. Silakan nonaktifkan ruangan tersebut.']);
        }

        $room->delete();

        return redirect()->route('master.rooms.index')->with('success', 'Ruangan berhasil dihapus.');
    }
}
