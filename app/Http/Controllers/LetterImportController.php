<?php

namespace App\Http\Controllers;

use App\Exports\LetterTemplateExport;
use App\Imports\LettersImport;
use App\Services\ErrorTrackingService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Exception;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Validators\ValidationException;

class LetterImportController extends Controller
{
    /**
     * Download template excel untuk import data surat
     */
    public function template()
    {
        return Excel::download(new LetterTemplateExport, 'Template_Import_Surat_FEB_UNJ.xlsx');
    }

    /**
     * Import data excel
     * 
     * Flow:
     * 1. Validasi file (required, format xlsx/xls, max 5MB)
     * 2. Reset importer (clear buffer + errors)
     * 3. Proses setiap row dari Excel:
     *    - Validasi data (required fields, master data lookup, enum values)
     *    - Jika error: collect error (jangan throw exception)
     *    - Jika valid: buffer data per (letter_type, year)
     * 4. Cek apakah ada error:
     *    - YES: return dengan error modal (show detail per baris)
     *    - NO: process buffered letters dengan LetterSequence lock
     * 5. Return success atau error
     * 
     * Safety:
     * - Rollback all jika ada error (no partial success)
     * - Sequence lock untuk thread-safety
     * - Clear error jika file baru di-upload
     */
    public function import(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls|max:5120' // Max 5MB
        ]);

        try {
            // Reset importer untuk fresh start
            LettersImport::resetAll();
            
            // Instance importer
            $importer = new LettersImport;
            
            // Proses setiap row dari Excel - data akan di-buffer per (letter_type, year)
            Excel::import($importer, $request->file('file_excel'));

            // ============================================
            // CEK APAKAH ADA ERROR SAAT IMPORT
            // ============================================
            if ($importer->hasErrors()) {
                $errors = $importer->getErrors();
                $errorCount = count($errors);
                
                Log::warning("Import data surat gagal dengan {$errorCount} error baris", [
                    'errors' => $errors,
                    'user_id' => auth()->id(),
                ]);

                // Return redirect dengan error details untuk ditampilkan di modal
                return redirect()->route('letters.index')
                    ->with('import_errors', $errors)
                    ->with('error', "Import gagal! Ada {$errorCount} baris dengan error. Perbaiki dan coba lagi.");
            }

            // ============================================
            // TIDAK ADA ERROR - PROCESS BATCH
            // ============================================
            
            // Total surat yang akan diimport
            $totalLetters = $importer->getTotalImported();
            
            if ($totalLetters === 0) {
                return redirect()->route('letters.index')
                    ->with('warning', 'File Excel tidak memiliki data surat yang valid untuk diimport.');
            }

            // Proses batch dengan LetterSequence lock
            $importer->processBufferedLetters();

            Log::info("Import data surat berhasil", [
                'total_letters' => $totalLetters,
                'user_id' => auth()->id(),
            ]);

            return redirect()->route('letters.index')
                ->with('success', "Berhasil mengimport {$totalLetters} surat! Data telah ditambahkan ke sistem.");
                
        } catch (ValidationException $e) {
            // Catch validation exception dari Maatwebsite Excel
            // Convert ke format error modal kami
            $maatwebsiteErrors = [];
            
            foreach ($e->failures() as $failure) {
                $maatwebsiteErrors[] = [
                    'row' => $failure->row(),
                    'field' => $failure->attribute(),
                    'message' => implode(', ', $failure->errors()),
                    'value' => $failure->value(),
                    'suggestions' => 'Periksa format data di Excel. Lihat template untuk referensi.',
                ];
            }
            
            Log::warning('Maatwebsite validation error', [
                'total_errors' => count($maatwebsiteErrors),
                'errors' => $maatwebsiteErrors,
                'user_id' => auth()->id(),
            ]);
            
            return redirect()->route('letters.index')
                ->with('import_errors', $maatwebsiteErrors)
                ->with('error', 'Import gagal! Ada ' . count($maatwebsiteErrors) . ' error validasi. Perbaiki dan coba lagi.');
                
        } catch (Exception $e) {
            // Log error using ErrorTrackingService
            $errorId = ErrorTrackingService::logError($e, 'LetterImportController.import', [
                'file_name' => $request->file('file_excel')?->getClientOriginalName(),
                'file_size' => $request->file('file_excel')?->getSize(),
            ]);
            
            return redirect()->route('letters.index')
                ->with('error', "Terjadi error saat import. Error ID: {$errorId}");
        }
    }
}
