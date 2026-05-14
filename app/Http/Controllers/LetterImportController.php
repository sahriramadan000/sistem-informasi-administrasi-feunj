<?php

namespace App\Http\Controllers;

use App\Exports\LetterTemplateExport;
use App\Imports\LettersImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Exception;
use Illuminate\Support\Facades\Log;

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
     */
    public function import(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls|max:5120' // Max 5MB
        ]);

        try {
            Excel::import(new LettersImport, $request->file('file_excel'));

            return redirect()->route('letters.index')->with('success', 'Data surat berhasil diimport!');
        } catch (Exception $e) {
            Log::error('Error importing letters: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('letters.index')->with('error', 'Gagal mengimport data. Pesan: ' . $e->getMessage());
        }
    }
}
