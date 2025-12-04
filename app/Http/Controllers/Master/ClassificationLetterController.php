<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\ClassificationLetter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Imports\ClassificationLetterImport;
use App\Exports\ClassificationLetterTemplateExport;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Controller untuk manajemen master data klasifikasi surat
 */
class ClassificationLetterController extends Controller
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
     * Menampilkan daftar klasifikasi surat
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $classifications = ClassificationLetter::orderBy('name')->paginate(10);
        return view('master.classification.index', compact('classifications'));
    }

    /**
     * Menampilkan form tambah klasifikasi surat
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('master.classification.create');
    }

    /**
     * Menyimpan klasifikasi surat baru
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'code' => 'required|string|max:50|unique:classification_letters,code',
                'name' => 'required|string|max:150',
                'description' => 'nullable|string',
                'is_active' => 'boolean',
            ]);

            // Simpan data menggunakan transaction
            DB::transaction(function () use ($validated) {
                ClassificationLetter::create($validated);
            });

            Log::info('Classification letter created', [
                'code' => $validated['code'],
                'name' => $validated['name'],
                'user_id' => auth()->id()
            ]);

            return redirect()
                ->route('master.classification-letters.index')
                ->with('success', 'Klasifikasi surat berhasil ditambahkan.');
        } catch (\Throwable $e) {
            Log::error('Error creating classification letter', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.']);
        }
    }

    /**
     * Menampilkan form edit klasifikasi surat
     *
     * @param ClassificationLetter $classificationLetter
     * @return \Illuminate\View\View
     */
    public function edit(ClassificationLetter $classificationLetter)
    {
        return view('master.classification.edit', compact('classificationLetter'));
    }

    /**
     * Mengupdate data klasifikasi surat
     *
     * @param Request $request
     * @param ClassificationLetter $classificationLetter
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, ClassificationLetter $classificationLetter)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'code' => 'required|string|max:50|unique:classification_letters,code,' . $classificationLetter->id,
                'name' => 'required|string|max:150',
                'description' => 'nullable|string',
                'is_active' => 'boolean',
            ]);

            // Update data menggunakan transaction
            DB::transaction(function () use ($classificationLetter, $validated) {
                $classificationLetter->update($validated);
            });

            Log::info('Classification letter updated', [
                'id' => $classificationLetter->id,
                'code' => $validated['code'],
                'user_id' => auth()->id()
            ]);

            return redirect()
                ->route('master.classification-letters.index')
                ->with('success', 'Klasifikasi surat berhasil diperbarui.');
        } catch (\Throwable $e) {
            Log::error('Error updating classification letter', [
                'id' => $classificationLetter->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan saat memperbarui data. Silakan coba lagi.']);
        }
    }

    /**
     * Menghapus klasifikasi surat
     *
     * @param ClassificationLetter $classificationLetter
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(ClassificationLetter $classificationLetter)
    {
        try {
            // Cek apakah klasifikasi digunakan oleh surat
            if ($classificationLetter->letters()->exists()) {
                return back()
                    ->withErrors(['error' => 'Klasifikasi tidak dapat dihapus karena sudah digunakan dalam surat.']);
            }

            // Hapus data menggunakan transaction
            DB::transaction(function () use ($classificationLetter) {
                $classificationLetter->delete();
            });

            Log::info('Classification letter deleted', [
                'id' => $classificationLetter->id,
                'code' => $classificationLetter->code,
                'user_id' => auth()->id()
            ]);

            return redirect()
                ->route('master.classification-letters.index')
                ->with('success', 'Klasifikasi surat berhasil dihapus.');
        } catch (\Throwable $e) {
            Log::error('Error deleting classification letter', [
                'id' => $classificationLetter->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withErrors(['error' => 'Terjadi kesalahan saat menghapus data. Silakan coba lagi.']);
        }
    }

    /**
     * Download template Excel untuk import
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadTemplate()
    {
        try {
            $fileName = 'Template_Klasifikasi_Surat_' . date('Y-m-d_His') . '.xlsx';

            Log::info('Template downloaded', [
                'filename' => $fileName,
                'user_id' => auth()->id()
            ]);

            return Excel::download(new ClassificationLetterTemplateExport, $fileName);
        } catch (\Throwable $e) {
            Log::error('Error downloading template', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withErrors(['error' => 'Terjadi kesalahan saat mengunduh template: ' . $e->getMessage()]);
        }
    }

    /**
     * Import data dari Excel
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function import(Request $request)
    {
        try {
            // Validasi file
            $request->validate([
                'file' => 'required|file|mimes:xlsx,xls|max:2048',
            ], [
                'file.required' => 'File Excel wajib diupload',
                'file.mimes' => 'File harus berformat Excel (.xlsx atau .xls)',
                'file.max' => 'Ukuran file maksimal 2MB',
            ]);

            $import = new ClassificationLetterImport();
            Excel::import($import, $request->file('file'));

            $stats = $import->getStats();

            Log::info('Excel import completed', [
                'imported' => $stats['imported'],
                'skipped' => $stats['skipped'],
                'user_id' => auth()->id()
            ]);

            // Check if there are errors
            if ($stats['skipped'] > 0) {
                return back()
                    ->with('warning', "Import selesai dengan {$stats['imported']} data berhasil dan {$stats['skipped']} data dilewati.")
                    ->withErrors($import->getErrors());
            }

            return redirect()
                ->route('master.classification-letters.index')
                ->with('success', "Import berhasil! {$stats['imported']} klasifikasi surat berhasil ditambahkan.");
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Throwable $e) {
            Log::error('Error importing Excel', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withErrors(['error' => 'Terjadi kesalahan saat mengimport file: ' . $e->getMessage()]);
        }
    }
}
