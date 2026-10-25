<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\RoomGroup;
use Illuminate\Http\Request;

class RoomGroupController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index(Request $request)
    {
        $query = RoomGroup::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $perPage = $request->input('per_page', 10);
        $roomGroups = $query->withCount('rooms')->orderBy('name')->paginate($perPage);
        $totalGroupsCount = RoomGroup::count();

        return view('master.room-groups.index', compact('roomGroups', 'totalGroupsCount'));
    }

    public function create()
    {
        return view('master.room-groups.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        RoomGroup::create($validated);

        return redirect()->route('master.room-groups.index')->with('success', 'Grup ruangan berhasil ditambahkan.');
    }

    public function edit(RoomGroup $roomGroup)
    {
        $roomGroup->loadCount('rooms');
        return view('master.room-groups.edit', compact('roomGroup'));
    }

    public function update(Request $request, RoomGroup $roomGroup)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $roomGroup->update($validated);

        return redirect()->route('master.room-groups.index')->with('success', 'Grup ruangan berhasil diperbarui.');
    }

    public function destroy(RoomGroup $roomGroup)
    {
        if ($roomGroup->rooms()->exists()) {
            return back()->with('error', 'Grup ruangan "' . $roomGroup->name . '" tidak dapat dihapus karena masih menaungi ' . $roomGroup->rooms()->count() . ' ruangan. Silakan pindahkan ruangan terlebih dahulu atau ubah status menjadi Nonaktif.');
        }

        $roomGroup->delete();

        return redirect()->route('master.room-groups.index')->with('success', 'Grup ruangan berhasil dihapus.');
    }
}
