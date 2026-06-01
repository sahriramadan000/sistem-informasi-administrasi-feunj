<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\LetterPurpose;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Controller untuk manajemen master data keperluan surat
 */
class LetterPurposeController extends Controller
{
    /**
     * Inisialisasi middleware
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin,operator');
        
        // Middleware khusus untuk method yang hanya admin yang bisa akses
        $this->middleware('role:admin')->only([
            'create',
            'store',
            'edit',
            'update',
            'destroy'
        ]);
    }

    /**
     * Menampilkan daftar keperluan surat
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        try {
            $query = LetterPurpose::query();

            // Search berdasarkan nama atau deskripsi
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            $letterPurposes = $query->orderBy('name')->paginate($request->get('per_page', 10));
            
            return view('master.letter-purposes.index', compact('letterPurposes'));
        } catch (\Throwable $e) {
            return $this->handleError($e, 'LetterPurposeController.index', 'Gagal memuat daftar keperluan surat.');
        }
    }

    /**
     * Menampilkan form tambah keperluan surat
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('master.letter-purposes.create');
    }

    /**
     * Menyimpan keperluan surat baru
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:letter_purposes,name',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        try {
            // Simpan data menggunakan transaction
            DB::transaction(function () use ($validated) {
                LetterPurpose::create($validated);
            });

            Log::info('Letter purpose created', [
                'name' => $validated['name'],
                'user_id' => auth()->id()
            ]);

            // Log to audit trail
            $letterPurpose = LetterPurpose::where('name', $validated['name'])->first();
            if ($letterPurpose) {
                AuditLogService::log('create', 'LetterPurpose', $letterPurpose->id, [
                    'name' => $validated['name'],
                    'description' => $validated['description'] ?? null,
                ]);
            }

            return redirect()
                ->route('master.letter-purposes.index')
                ->with('success', 'Keperluan surat berhasil ditambahkan.');

        } catch (\Throwable $e) {
            return $this->handleError($e, 'LetterPurposeController.store', 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.');
        }
    }

    /**
     * Menampilkan form edit keperluan surat
     * 
     * @param LetterPurpose $letterPurpose
     * @return \Illuminate\View\View
     */
    public function edit(LetterPurpose $letterPurpose)
    {
        return view('master.letter-purposes.edit', compact('letterPurpose'));
    }

    /**
     * Mengupdate data keperluan surat
     * 
     * @param Request $request
     * @param LetterPurpose $letterPurpose
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, LetterPurpose $letterPurpose)
    {
        // Validasi input
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:letter_purposes,name,' . $letterPurpose->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        try {
            // Update data menggunakan transaction
            DB::transaction(function () use ($letterPurpose, $validated) {
                $letterPurpose->update($validated);
            });

            Log::info('Letter purpose updated', [
                'id' => $letterPurpose->id,
                'name' => $validated['name'],
                'user_id' => auth()->id()
            ]);

            // Log to audit trail
            AuditLogService::log('update', 'LetterPurpose', $letterPurpose->id, [
                'name' => $validated['name'],
                'changes' => array_keys($validated),
            ]);

            return redirect()
                ->route('master.letter-purposes.index')
                ->with('success', 'Keperluan surat berhasil diperbarui.');

        } catch (\Throwable $e) {
            return $this->handleError($e, 'LetterPurposeController.update', 'Terjadi kesalahan saat memperbarui data. Silakan coba lagi.');
        }
    }

    /**
     * Menonaktifkan keperluan surat (soft delete)
     * 
     * @param LetterPurpose $letterPurpose
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(LetterPurpose $letterPurpose)
    {
        try {
            // Cek apakah keperluan sudah nonaktif
            if (!$letterPurpose->is_active) {
                return back()
                    ->withErrors(['error' => 'Keperluan surat sudah dalam status nonaktif.']);
            }

            // Nonaktifkan data menggunakan transaction
            DB::transaction(function () use ($letterPurpose) {
                $letterPurpose->update(['is_active' => false]);
            });

            Log::info('Letter purpose deactivated', [
                'id' => $letterPurpose->id,
                'name' => $letterPurpose->name,
                'user_id' => auth()->id()
            ]);

            // Log to audit trail
            AuditLogService::log('delete', 'LetterPurpose', $letterPurpose->id, [
                'name' => $letterPurpose->name,
            ]);

            return redirect()
                ->route('master.letter-purposes.index')
                ->with('success', 'Keperluan surat berhasil dinonaktifkan.');

        } catch (\Throwable $e) {
            Log::error('Error deactivating letter purpose', [
                'id' => $letterPurpose->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withErrors(['error' => 'Terjadi kesalahan saat menonaktifkan data. Silakan coba lagi.']);
        }
    }
}
