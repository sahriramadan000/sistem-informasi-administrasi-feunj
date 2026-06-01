<?php

namespace App\Http\Controllers;

use App\Exports\LegalizationTemplateExport;
use App\Imports\LegalizationsImport;
use App\Services\ErrorTrackingService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Exception;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Validators\ValidationException;

class LegalizationImportController extends Controller
{
    /**
     * Download template excel untuk import data legalisir
     */
    public function template()
    {
        return Excel::download(new LegalizationTemplateExport, 'Template_Import_Legalisir_FEB_UNJ.xlsx');
    }

    /**
     * Import data excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls|max:5120' // Max 5MB
        ]);

        try {
            // Reset importer untuk fresh start
            LegalizationsImport::resetAll();
            
            // Instance importer
            $importer = new LegalizationsImport;
            
            // Proses setiap row dari Excel
            Excel::import($importer, $request->file('file_excel'));

            // ============================================
            // CEK APAKAH ADA ERROR SAAT IMPORT
            // ============================================
            if ($importer->hasErrors()) {
                $errors = $importer->getErrors();
                $errorCount = count($errors);
                
                Log::warning("Import data legalisir gagal dengan {$errorCount} error baris", [
                    'errors' => $errors,
                    'user_id' => auth()->id(),
                ]);

                // Return redirect dengan error details untuk ditampilkan di modal
                return redirect()->route('legalizations.index')
                    ->with('import_errors', $errors)
                    ->with('error', "Import gagal! Ada {$errorCount} baris dengan error. Perbaiki dan coba lagi.");
            }

            // ============================================
            // TIDAK ADA ERROR - PROCESS BATCH
            // ============================================
            
            // Total legalisir yang akan diimport
            $totalLegalizations = $importer->getTotalImported();
            
            if ($totalLegalizations === 0) {
                return redirect()->route('legalizations.index')
                    ->with('warning', 'File Excel tidak memiliki data legalisir yang valid untuk diimport.');
            }

            // Proses batch dengan Sequence lock
            $importer->processBufferedLegalizations();

            Log::info("Import data legalisir berhasil", [
                'total_legalizations' => $totalLegalizations,
                'user_id' => auth()->id(),
            ]);

            return redirect()->route('legalizations.index')
                ->with('success', "Berhasil mengimport {$totalLegalizations} transaksi legalisir! Data telah ditambahkan ke sistem.");
                
        } catch (ValidationException $e) {
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
            
            Log::warning('Maatwebsite validation error on legalizations import', [
                'total_errors' => count($maatwebsiteErrors),
                'errors' => $maatwebsiteErrors,
                'user_id' => auth()->id(),
            ]);
            
            return redirect()->route('legalizations.index')
                ->with('import_errors', $maatwebsiteErrors)
                ->with('error', 'Import gagal! Ada ' . count($maatwebsiteErrors) . ' error validasi. Perbaiki dan coba lagi.');
                
        } catch (Exception $e) {
            // Log error using ErrorTrackingService
            $errorId = ErrorTrackingService::logError($e, 'LegalizationImportController.import', [
                'file_name' => $request->file('file_excel')?->getClientOriginalName(),
                'file_size' => $request->file('file_excel')?->getSize(),
            ]);
            
            return redirect()->route('legalizations.index')
                ->with('error', "Terjadi error saat import. Error ID: {$errorId}");
        }
    }
}
