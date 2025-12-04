<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\LetterType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Controller untuk manajemen master data jenis surat
 */
class LetterTypeController extends Controller
{
    /**
     * Inisialisasi middleware
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    /**
     * Menampilkan daftar jenis surat
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $letterTypes = LetterType::orderBy('name')->paginate(10);
        return view('master.letter_type.index', compact('letterTypes'));
    }

    /**
     * Menampilkan form tambah jenis surat
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('master.letter_type.create');
    }

    /**
     * Menyimpan jenis surat baru
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'code' => 'nullable|string|max:50|unique:letter_types,code',
                'name' => 'required|string|max:150',
                'description' => 'nullable|string',
                'is_active' => 'boolean',
                'requires_purpose' => 'boolean',
            ]);

            // Handle checkbox values (jika tidak dicentang, set false)
            $validated['is_active'] = $request->has('is_active');
            $validated['requires_purpose'] = $request->has('requires_purpose');

            // Simpan data menggunakan transaction
            DB::transaction(function () use ($validated) {
                LetterType::create($validated);
            });

            Log::info('Letter type created', [
                'code' => $validated['code'] ?? 'N/A',
                'name' => $validated['name'],
                'user_id' => auth()->id()
            ]);

            return redirect()
                ->route('master.letter-types.index')
                ->with('success', 'Jenis surat berhasil ditambahkan.');

        } catch (\Throwable $e) {
            Log::error('Error creating letter type', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.']);
        }
    }

    /**
     * Menampilkan form edit jenis surat
     * 
     * @param LetterType $letterType
     * @return \Illuminate\View\View
     */
    public function edit(LetterType $letterType)
    {
        return view('master.letter_type.edit', compact('letterType'));
    }

    /**
     * Mengupdate data jenis surat
     * 
     * @param Request $request
     * @param LetterType $letterType
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, LetterType $letterType)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'code' => 'nullable|string|max:50|unique:letter_types,code,' . $letterType->id,
                'name' => 'required|string|max:150',
                'description' => 'nullable|string',
                'is_active' => 'boolean',
                'requires_purpose' => 'boolean',
            ]);

            // Handle checkbox values (jika tidak dicentang, set false)
            $validated['is_active'] = $request->has('is_active');
            $validated['requires_purpose'] = $request->has('requires_purpose');

            // Update data menggunakan transaction
            DB::transaction(function () use ($letterType, $validated) {
                $letterType->update($validated);
            });

            Log::info('Letter type updated', [
                'id' => $letterType->id,
                'code' => $validated['code'] ?? 'N/A',
                'user_id' => auth()->id()
            ]);

            return redirect()
                ->route('master.letter-types.index')
                ->with('success', 'Jenis surat berhasil diperbarui.');

        } catch (\Throwable $e) {
            Log::error('Error updating letter type', [
                'id' => $letterType->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan saat memperbarui data. Silakan coba lagi.']);
        }
    }

    /**
     * Menghapus jenis surat
     * 
     * @param LetterType $letterType
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(LetterType $letterType)
    {
        try {
            // Cek apakah jenis surat digunakan oleh surat
            if ($letterType->letters()->exists()) {
                return back()
                    ->withErrors(['error' => 'Jenis surat tidak dapat dihapus karena sudah digunakan dalam surat.']);
            }

            // Hapus data menggunakan transaction
            DB::transaction(function () use ($letterType) {
                $letterType->delete();
            });

            Log::info('Letter type deleted', [
                'id' => $letterType->id,
                'code' => $letterType->code,
                'user_id' => auth()->id()
            ]);

            return redirect()
                ->route('master.letter-types.index')
                ->with('success', 'Jenis surat berhasil dihapus.');

        } catch (\Throwable $e) {
            Log::error('Error deleting letter type', [
                'id' => $letterType->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withErrors(['error' => 'Terjadi kesalahan saat menghapus data. Silakan coba lagi.']);
        }
    }

    /**
     * Toggle requires_purpose field via AJAX
     * 
     * @param Request $request
     * @param LetterType $letterType
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleRequiresPurpose(Request $request, LetterType $letterType)
    {
        try {
            $validated = $request->validate([
                'requires_purpose' => 'required|boolean',
            ]);

            DB::transaction(function () use ($letterType, $validated) {
                $letterType->update([
                    'requires_purpose' => $validated['requires_purpose']
                ]);
            });

            Log::info('Letter type requires_purpose toggled', [
                'id' => $letterType->id,
                'requires_purpose' => $validated['requires_purpose'],
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'requires_purpose' => $letterType->requires_purpose,
                'message' => 'Status keperluan surat berhasil diperbarui.'
            ]);

        } catch (\Throwable $e) {
            Log::error('Error toggling requires_purpose', [
                'id' => $letterType->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui status.'
            ], 500);
        }
    }
}