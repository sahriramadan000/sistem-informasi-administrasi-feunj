<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\EducationLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Services\AuditLogService;

class EducationLevelController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin,operator')->except(['index']);
    }

    public function index(Request $request)
    {
        try {
            $query = EducationLevel::query();
            if ($request->filled('search')) {
                $query->where('name', 'like', "%{$request->search}%");
            }
            $educationLevels = $query->orderBy('name')->paginate($request->get('per_page', 10));
            return view('master.education_levels.index', compact('educationLevels'));
        } catch (\Throwable $e) {
            return $this->handleError($e, 'EducationLevelController.index', 'Gagal memuat daftar jenjang pendidikan.');
        }
    }

    public function create()
    {
        try {
            return view('master.education_levels.create');
        } catch (\Throwable $e) {
            return $this->handleError($e, 'EducationLevelController.create', 'Gagal memuat form tambah jenjang pendidikan.');
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:education_levels,name',
            'price_per_page' => 'required|numeric|min:0',
        ]);

        try {
            $educationLevel = DB::transaction(function () use ($validated) {
                return EducationLevel::create($validated);
            });

            Log::info('Education level created', [
                'name' => $validated['name'],
                'user_id' => auth()->id()
            ]);

            AuditLogService::log('create', 'EducationLevel', $educationLevel->id, [
                'name' => $validated['name'],
                'price_per_page' => $validated['price_per_page'],
            ]);

            return redirect()->route('master.education-levels.index')->with('success', 'Data jenjang berhasil ditambahkan.');
        } catch (\Throwable $e) {
            return $this->handleError($e, 'EducationLevelController.store', 'Terjadi kesalahan saat menyimpan data jenjang pendidikan.');
        }
    }

    public function edit(EducationLevel $educationLevel)
    {
        try {
            return view('master.education_levels.edit', compact('educationLevel'));
        } catch (\Throwable $e) {
            return $this->handleError($e, 'EducationLevelController.edit', 'Gagal memuat form edit jenjang pendidikan.');
        }
    }

    public function update(Request $request, EducationLevel $educationLevel)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:education_levels,name,' . $educationLevel->id,
            'price_per_page' => 'required|numeric|min:0',
        ]);

        try {
            DB::transaction(function () use ($educationLevel, $validated) {
                $educationLevel->update($validated);
            });

            Log::info('Education level updated', [
                'id' => $educationLevel->id,
                'name' => $validated['name'],
                'user_id' => auth()->id()
            ]);

            AuditLogService::log('update', 'EducationLevel', $educationLevel->id, [
                'name' => $validated['name'],
                'changes' => array_keys($validated),
            ]);

            return redirect()->route('master.education-levels.index')->with('success', 'Data jenjang berhasil diperbarui.');
        } catch (\Throwable $e) {
            return $this->handleError($e, 'EducationLevelController.update', 'Terjadi kesalahan saat memperbarui data jenjang pendidikan.');
        }
    }

    public function destroy(EducationLevel $educationLevel)
    {
        try {
            DB::transaction(function () use ($educationLevel) {
                $educationLevel->delete();
            });

            Log::info('Education level deactivated', [
                'id' => $educationLevel->id,
                'user_id' => auth()->id()
            ]);

            AuditLogService::log('delete', 'EducationLevel', $educationLevel->id, [
                'name' => $educationLevel->name,
            ]);

            return redirect()->route('master.education-levels.index')->with('success', 'Data jenjang berhasil dinonaktifkan (soft delete).');
        } catch (\Throwable $e) {
            return $this->handleError($e, 'EducationLevelController.destroy', 'Terjadi kesalahan saat menonaktifkan data jenjang pendidikan.');
        }
    }
}
