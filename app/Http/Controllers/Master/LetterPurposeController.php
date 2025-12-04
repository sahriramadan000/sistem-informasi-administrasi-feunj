<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\LetterPurpose;
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
        $this->middleware('role:admin');
    }

    /**
     * Menampilkan daftar keperluan surat
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $letterPurposes = LetterPurpose::orderBy('name')->paginate(10);
        return view('master.letter-purposes.index', compact('letterPurposes'));
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
        try {
            // Validasi input
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:letter_purposes,name',
                'description' => 'nullable|string',
                'is_active' => 'boolean',
            ]);

            // Simpan data menggunakan transaction
            DB::transaction(function () use ($validated) {
                LetterPurpose::create($validated);
            });

            Log::info('Letter purpose created', [
                'name' => $validated['name'],
                'user_id' => auth()->id()
            ]);

            return redirect()
                ->route('master.letter-purposes.index')
                ->with('success', 'Keperluan surat berhasil ditambahkan.');

        } catch (\Throwable $e) {
            Log::error('Error creating letter purpose', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.']);
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
        try {
            // Validasi input
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:letter_purposes,name,' . $letterPurpose->id,
                'description' => 'nullable|string',
                'is_active' => 'boolean',
            ]);

            // Update data menggunakan transaction
            DB::transaction(function () use ($letterPurpose, $validated) {
                $letterPurpose->update($validated);
            });

            Log::info('Letter purpose updated', [
                'id' => $letterPurpose->id,
                'name' => $validated['name'],
                'user_id' => auth()->id()
            ]);

            return redirect()
                ->route('master.letter-purposes.index')
                ->with('success', 'Keperluan surat berhasil diperbarui.');

        } catch (\Throwable $e) {
            Log::error('Error updating letter purpose', [
                'id' => $letterPurpose->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan saat memperbarui data. Silakan coba lagi.']);
        }
    }

    /**
     * Menghapus keperluan surat
     * 
     * @param LetterPurpose $letterPurpose
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(LetterPurpose $letterPurpose)
    {
        try {
            // Cek apakah keperluan digunakan oleh surat
            if ($letterPurpose->letters()->exists()) {
                return back()
                    ->withErrors(['error' => 'Keperluan tidak dapat dihapus karena sudah digunakan dalam surat.']);
            }

            // Hapus data menggunakan transaction
            DB::transaction(function () use ($letterPurpose) {
                $letterPurpose->delete();
            });

            Log::info('Letter purpose deleted', [
                'id' => $letterPurpose->id,
                'name' => $letterPurpose->name,
                'user_id' => auth()->id()
            ]);

            return redirect()
                ->route('master.letter-purposes.index')
                ->with('success', 'Keperluan surat berhasil dihapus.');

        } catch (\Throwable $e) {
            Log::error('Error deleting letter purpose', [
                'id' => $letterPurpose->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withErrors(['error' => 'Terjadi kesalahan saat menghapus data. Silakan coba lagi.']);
        }
    }
}
