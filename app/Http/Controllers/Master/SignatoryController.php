<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Signatory;
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
        $this->middleware('role:admin');
    }

    /**
     * Menampilkan daftar penandatangan
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $signatories = Signatory::orderBy('name')->paginate(10);
        return view('master.signatory.index', compact('signatories'));
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
                'code' => 'required|string|max:50|unique:signatories,code',
                'name' => 'required|string|max:150',
                'position' => 'required|string|max:150',
                'nip' => 'nullable|string|max:50',
                'is_active' => 'boolean',
            ]);

            // Simpan data menggunakan transaction
            DB::transaction(function () use ($validated) {
                Signatory::create($validated);
            });

            Log::info('Signatory created', [
                'code' => $validated['code'],
                'name' => $validated['name'],
                'user_id' => auth()->id()
            ]);

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
                'code' => 'required|string|max:50|unique:signatories,code,' . $signatory->id,
                'name' => 'required|string|max:150',
                'position' => 'required|string|max:150',
                'nip' => 'nullable|string|max:50',
                'is_active' => 'boolean',
            ]);

            // Update data menggunakan transaction
            DB::transaction(function () use ($signatory, $validated) {
                $signatory->update($validated);
            });

            Log::info('Signatory updated', [
                'id' => $signatory->id,
                'code' => $validated['code'],
                'user_id' => auth()->id()
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
     * Menghapus penandatangan
     * 
     * @param Signatory $signatory
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Signatory $signatory)
    {
        try {
            // Cek apakah penandatangan digunakan oleh surat
            if ($signatory->letters()->exists()) {
                return back()
                    ->withErrors(['error' => 'Penandatangan tidak dapat dihapus karena sudah digunakan dalam surat.']);
            }

            // Hapus data menggunakan transaction
            DB::transaction(function () use ($signatory) {
                $signatory->delete();
            });

            Log::info('Signatory deleted', [
                'id' => $signatory->id,
                'code' => $signatory->code,
                'user_id' => auth()->id()
            ]);

            return redirect()
                ->route('master.signatories.index')
                ->with('success', 'Penandatangan berhasil dihapus.');

        } catch (\Throwable $e) {
            Log::error('Error deleting signatory', [
                'id' => $signatory->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withErrors(['error' => 'Terjadi kesalahan saat menghapus data. Silakan coba lagi.']);
        }
    }
}