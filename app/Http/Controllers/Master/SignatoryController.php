<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Signatory;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Controller untuk manajemen master data penandatangan surat
 */
class SignatoryController extends Controller
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
     * Menampilkan daftar penandatangan
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        try {
            $query = Signatory::query();

            // Search berdasarkan nama, jabatan, atau NIP
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('position', 'like', "%{$search}%")
                      ->orWhere('nip', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%");
                });
            }

            $signatories = $query->orderBy('code')->paginate(10);
            
            return view('master.signatory.index', compact('signatories'));
        } catch (\Throwable $e) {
            return $this->handleError($e, 'SignatoryController.index', 'Gagal memuat daftar penandatangan.');
        }
    }

    /**
     * Menampilkan form tambah penandatangan
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('master.signatory.create');
    }

    /**
     * Menyimpan penandatangan baru
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'code' => 'required|string|max:50',
                'name' => 'required|string|max:150',
                'position' => 'required|string|max:150',
                'nip' => 'nullable|string|max:50',
                'is_active' => 'boolean',
            ]);

            // Set is_active explicitly since unchecked checkboxes send no data
            $validated['is_active'] = $request->has('is_active');

            // Simpan data menggunakan transaction
            DB::transaction(function () use ($validated) {
                Signatory::create($validated);
            });

            Log::info('Signatory created', [
                'code' => $validated['code'],
                'name' => $validated['name'],
                'user_id' => auth()->id()
            ]);

            // Log to audit trail
            $signatory = Signatory::where('code', $validated['code'])->first();
            if ($signatory) {
                AuditLogService::log('create', 'Signatory', $signatory->id, [
                    'code' => $validated['code'],
                    'name' => $validated['name'],
                    'position' => $validated['position'],
                ]);
            }

            return redirect()
                ->route('master.signatories.index')
                ->with('success', 'Penandatangan berhasil ditambahkan.');

        } catch (\Throwable $e) {
            Log::error('Error creating signatory', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.']);
        }
    }

    /**
     * Menampilkan form edit penandatangan
     * 
     * @param Signatory $signatory
     * @return \Illuminate\View\View
     */
    public function edit(Signatory $signatory)
    {
        return view('master.signatory.edit', compact('signatory'));
    }

    /**
     * Mengupdate data penandatangan
     * 
     * @param Request $request
     * @param Signatory $signatory
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Signatory $signatory)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'code' => 'required|string|max:50',
                'name' => 'required|string|max:150',
                'position' => 'required|string|max:150',
                'nip' => 'nullable|string|max:50',
                'is_active' => 'boolean',
            ]);

            // Set is_active explicitly since unchecked checkboxes send no data
            $validated['is_active'] = $request->has('is_active');

            // Update data menggunakan transaction
            DB::transaction(function () use ($signatory, $validated) {
                $signatory->update($validated);
            });

            Log::info('Signatory updated', [
                'id' => $signatory->id,
                'code' => $validated['code'],
                'user_id' => auth()->id()
            ]);

            // Log to audit trail
            AuditLogService::log('update', 'Signatory', $signatory->id, [
                'code' => $validated['code'],
                'name' => $validated['name'],
                'changes' => array_keys($validated),
            ]);

            return redirect()
                ->route('master.signatories.index')
                ->with('success', 'Penandatangan berhasil diperbarui.');

        } catch (\Throwable $e) {
            Log::error('Error updating signatory', [
                'id' => $signatory->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan saat memperbarui data. Silakan coba lagi.']);
        }
    }

    /**
     * Menonaktifkan penandatangan (soft delete)
     * 
     * @param Signatory $signatory
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Signatory $signatory)
    {
        try {
            // Cek apakah penandatangan sudah nonaktif
            if (!$signatory->is_active) {
                return back()
                    ->withErrors(['error' => 'Penandatangan sudah dalam status nonaktif.']);
            }

            // Nonaktifkan data menggunakan transaction
            DB::transaction(function () use ($signatory) {
                $signatory->update(['is_active' => false]);
            });

            Log::info('Signatory deactivated', [
                'id' => $signatory->id,
                'code' => $signatory->code,
                'user_id' => auth()->id()
            ]);

            // Log to audit trail
            AuditLogService::log('delete', 'Signatory', $signatory->id, [
                'code' => $signatory->code,
                'name' => $signatory->name,
            ]);

            return redirect()
                ->route('master.signatories.index')
                ->with('success', 'Penandatangan berhasil dinonaktifkan.');

        } catch (\Throwable $e) {
            Log::error('Error deactivating signatory', [
                'id' => $signatory->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withErrors(['error' => 'Terjadi kesalahan saat menonaktifkan data. Silakan coba lagi.']);
        }
    }
}