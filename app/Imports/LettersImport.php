<?php

namespace App\Imports;

use App\Models\Letter;
use App\Models\Signatory;
use App\Models\ClassificationLetter;
use App\Models\LetterType;
use App\Models\LetterPurpose;
use App\Models\LetterSequence;
use App\Enums\LetterTarget;
use App\Enums\SecurityClassification;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;
use Exception;

/**
 * Import class untuk membaca data surat dari Excel
 * 
 * Validation dilakukan di method model() per baris
 * Bukan di WithValidation interface (agar custom error handling bekerja)
 */
class LettersImport implements ToModel, WithHeadingRow
{
    /**
     * Store untuk mengelompokkan data per (letter_type_id, year)
     * agar dapat di-process dalam batch yang aman dengan LetterSequence lock
     * 
     * @var array
     */
    private static $bufferedLetters = [];

    /**
     * Store untuk mengumpulkan error validation
     * Format: [
     *     'row' => nomor baris,
     *     'field' => nama field,
     *     'message' => pesan error,
     *     'value' => nilai yang diinput,
     *     'suggestions' => saran perbaikan
     * ]
     * 
     * @var array
     */
    private static $importErrors = [];

    /**
     * Track nomor baris untuk error reporting
     * 
     * @var int
     */
    private static $currentRow = 1;

    /**
     * Cache master data untuk validasi dan error messages
     * 
     * @var array
     */
    private $masterDataCache = [];

    /**
     * Constructor - load master data cache
     */
    public function __construct()
    {
        $this->loadMasterDataCache();
    }

    /**
     * Load master data ke cache untuk performa dan error suggestions
     */
    private function loadMasterDataCache()
    {
        $this->masterDataCache = [
            'signatories' => Signatory::all()->mapWithKeys(function ($item) {
                return [$item->id => $item->code];
            })->toArray(),
            'letter_types' => LetterType::all()->mapWithKeys(function ($item) {
                return [$item->code => $item->id];
            })->toArray(),
            'classifications' => ClassificationLetter::all()->mapWithKeys(function ($item) {
                return [$item->code => $item->id];
            })->toArray(),
        ];
    }

    /**
     * Collect error dari validasi
     */
    private function collectError($field, $message, $value = null, $suggestions = null)
    {
        self::$importErrors[] = [
            'row' => self::$currentRow,
            'field' => $field,
            'message' => $message,
            'value' => $value,
            'suggestions' => $suggestions,
        ];
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Increment row counter (mulai dari 2 karena 1 adalah header)
        self::$currentRow++;

        // Skip empty rows
        if (!array_filter($row)) {
            return null;
        }

        // ============================================
        // 1. VALIDASI REQUIRED FIELDS
        // ============================================
        
        // Check tanggal_surat
        if (empty($row['tanggal_surat'])) {
            $this->collectError(
                'tanggal_surat',
                'Kolom wajib diisi',
                $row['tanggal_surat'] ?? '(kosong)',
                'Gunakan format: YYYY-MM-DD atau DD/MM/YYYY'
            );
            return null;
        }

        // Check kode_penandatangan
        if (empty($row['kode_penandatangan'])) {
            $this->collectError(
                'kode_penandatangan',
                'Kolom wajib diisi',
                $row['kode_penandatangan'] ?? '(kosong)',
                'Gunakan ID penandatangan (angka). Contoh: 1, 2, 3'
            );
            return null;
        }

        // Check kode_klasifikasi_surat
        if (empty($row['kode_klasifikasi_surat'])) {
            $this->collectError(
                'kode_klasifikasi_surat',
                'Kolom wajib diisi',
                $row['kode_klasifikasi_surat'] ?? '(kosong)',
                'Contoh kode: AK, BK, CK, DK'
            );
            return null;
        }

        // Check kode_jenis_surat
        if (empty($row['kode_jenis_surat'])) {
            $this->collectError(
                'kode_jenis_surat',
                'Kolom wajib diisi',
                $row['kode_jenis_surat'] ?? '(kosong)',
                'Valid: ST, SK, SP, SR, SU'
            );
            return null;
        }

        // ============================================
        // 2. VALIDASI & PARSE TANGGAL
        // ============================================
        $date = null;
        try {
            if (is_numeric($row['tanggal_surat'])) {
                $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['tanggal_surat']);
            } else {
                $date = Carbon::parse($row['tanggal_surat']);
            }
        } catch (Exception $e) {
            $this->collectError(
                'tanggal_surat',
                'Format tanggal tidak valid. ' . $e->getMessage(),
                $row['tanggal_surat'],
                'Format yang diterima: YYYY-MM-DD, DD/MM/YYYY, atau serial Excel'
            );
            return null;
        }

        $year = $date->format('Y');

        // ============================================
        // 3. VALIDASI MASTER DATA LOOKUP
        // ============================================
        
        // Lookup Signatory by ID
        $signatory = Signatory::find($row['kode_penandatangan']);
        if (!$signatory) {
            $availableIds = Signatory::pluck('id')->toArray();
            $this->collectError(
                'kode_penandatangan',
                "Penandatangan dengan ID '{$row['kode_penandatangan']}' tidak ditemukan",
                $row['kode_penandatangan'],
                'Available IDs: ' . implode(', ', $availableIds)
            );
            return null;
        }

        // Lookup Classification by code
        $classification = ClassificationLetter::where('code', $row['kode_klasifikasi_surat'])->first();
        if (!$classification) {
            $availableCodes = ClassificationLetter::pluck('code')->toArray();
            $this->collectError(
                'kode_klasifikasi_surat',
                "Klasifikasi dengan kode '{$row['kode_klasifikasi_surat']}' tidak ditemukan",
                $row['kode_klasifikasi_surat'],
                'Available codes: ' . implode(', ', $availableCodes)
            );
            return null;
        }

        // Lookup Letter Type by code
        $letterType = LetterType::where('code', $row['kode_jenis_surat'])->first();
        if (!$letterType) {
            $availableCodes = LetterType::pluck('code')->toArray();
            $this->collectError(
                'kode_jenis_surat',
                "Jenis surat dengan kode '{$row['kode_jenis_surat']}' tidak ditemukan",
                $row['kode_jenis_surat'],
                'Valid: ' . implode(', ', $availableCodes)
            );
            return null;
        }

        // ============================================
        // 4. VALIDASI ENUM VALUES
        // ============================================
        
        $target = strtolower(trim($row['sasaran_surat'] ?? 'internal'));
        if (!in_array($target, ['internal', 'external'])) {
            $this->collectError(
                'sasaran_surat',
                "Nilai '{$target}' tidak valid",
                $row['sasaran_surat'] ?? 'internal',
                'Valid: internal, external'
            );
            return null;
        }

        $security = strtoupper(trim($row['klasifikasi_keamanan'] ?? 'B'));
        if (!in_array($security, ['B', 'T', 'R', 'SR'])) {
            $this->collectError(
                'klasifikasi_keamanan',
                "Nilai '{$security}' tidak valid",
                $row['klasifikasi_keamanan'] ?? 'B',
                'Valid: B (Biasa), T (Terbatas), R (Rahasia), SR (Sangat Rahasia)'
            );
            return null;
        }

        $status = strtolower(trim($row['status'] ?? 'final'));
        if (!in_array($status, ['draft', 'final'])) {
            $this->collectError(
                'status',
                "Nilai '{$status}' tidak valid",
                $row['status'] ?? 'final',
                'Valid: draft, final'
            );
            return null;
        }

        // ============================================
        // 5. OPTIONAL: LOOKUP PURPOSE
        // ============================================
        
        $purposeId = null;
        if (!empty($row['nama_keperluan'])) {
            $purpose = LetterPurpose::where('name', 'like', "%" . trim($row['nama_keperluan']) . "%")->first();
            if ($purpose) {
                $purposeId = $purpose->id;
            }
            // Jika tidak ketemu, skip saja (optional field)
        }

        // ============================================
        // 6. PERSIAPKAN DATA UNTUK BUFFER
        // ============================================
        
        $letterNumber = $row['nomor_surat'] ?? null;

        $letterData = [
            'letter_number' => $letterNumber,
            'year' => $year,
            'signatory_id' => $signatory->id,
            'classification_id' => $classification->id,
            'security_classification' => $security,
            'letter_target' => $target,
            'letter_type_id' => $letterType->id,
            'letter_purpose_id' => $purposeId,
            'letter_date' => $date,
            'subject' => $row['perihal'] ?? null,
            'recipient' => $row['tujuan'] ?? null,
            'student_name' => $row['nama_mahasiswa'] ?? null,
            'status' => $status,
            'is_active' => true,
            'created_by' => auth()->id() ?? 1,
        ];

        // Buffer data berdasarkan (letter_type_id, year) key
        $key = "{$letterType->id}_{$year}";
        if (!isset(self::$bufferedLetters[$key])) {
            self::$bufferedLetters[$key] = [
                'letter_type_id' => $letterType->id,
                'year' => $year,
                'letters' => [],
            ];
        }
        self::$bufferedLetters[$key]['letters'][] = $letterData;

        // Return null (jangan create langsung)
        return null;
    }

    /**
     * Check apakah ada error saat import
     */
    public function hasErrors(): bool
    {
        return count(self::$importErrors) > 0;
    }

    /**
     * Get semua errors
     */
    public function getErrors(): array
    {
        return self::$importErrors;
    }

    /**
     * Reset errors dan buffer (gunakan untuk test ulang)
     */
    public static function resetAll()
    {
        self::$importErrors = [];
        self::$bufferedLetters = [];
        self::$currentRow = 1;
    }

    /**
     * Proses seluruh batch surat dengan sequence locking
     * 
     * Method ini dipanggil setelah semua row diproses dan tidak ada error
     */
    public function processBufferedLetters()
    {
        foreach (self::$bufferedLetters as $groupKey => $group) {
            // Setiap (letter_type_id, year) diproses dalam 1 transaction
            // Dengan pessimistic lock pada LetterSequence
            LetterSequence::createLettersWithSequence(
                $group['letter_type_id'],
                $group['year'],
                $group['letters']
            );
        }

        // Clear buffer setelah selesai
        self::$bufferedLetters = [];
    }

    /**
     * Get buffered letters (untuk testing atau debug)
     */
    public static function getBufferedLetters()
    {
        return self::$bufferedLetters;
    }

    /**
     * Get total imported letters
     */
    public function getTotalImported(): int
    {
        $total = 0;
        foreach (self::$bufferedLetters as $group) {
            $total += count($group['letters']);
        }
        return $total;
    }
}
