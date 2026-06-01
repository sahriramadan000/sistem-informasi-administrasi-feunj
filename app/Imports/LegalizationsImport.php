<?php

namespace App\Imports;

use App\Models\Legalization;
use App\Models\EducationLevel;
use App\Models\LegalizationSequence;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Carbon\Carbon;
use Exception;

class LegalizationsImport implements ToModel, WithHeadingRow, SkipsEmptyRows
{
    /**
     * Store untuk mengelompokkan data per tahun
     * @var array
     */
    private static $bufferedLegalizations = [];

    /**
     * Store untuk mengumpulkan error validation
     * @var array
     */
    private static $importErrors = [];

    /**
     * Track nomor baris untuk error reporting
     * @var int
     */
    private static $currentRow = 1;

    /**
     * Cache master data untuk validasi
     * @var array
     */
    private $educationLevelsCache = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->loadMasterDataCache();
    }

    private function loadMasterDataCache()
    {
        // Cache object based on lowercased name for case-insensitive lookup
        $levels = EducationLevel::all();
        foreach ($levels as $level) {
            $this->educationLevelsCache[strtolower(trim($level->name))] = $level;
        }
    }

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

    public function model(array $row)
    {
        self::$currentRow++;

        // Skip empty rows (ignore nulls, empty strings, and whitespace)
        $hasData = false;
        foreach ($row as $cell) {
            if ($cell !== null && trim((string)$cell) !== '') {
                $hasData = true;
                break;
            }
        }

        if (!$hasData) {
            return null;
        }

        // ============================================
        // 1. VALIDASI REQUIRED FIELDS
        // ============================================
        
        if (empty($row['tanggal_transaksi'])) {
            $this->collectError('tanggal_transaksi', 'Kolom wajib diisi', '(kosong)', 'Gunakan format: YYYY-MM-DD');
            return null;
        }
        if (empty($row['nama_alumni'])) {
            $this->collectError('nama_alumni', 'Kolom wajib diisi', '(kosong)', 'Masukkan nama alumni lengkap');
            return null;
        }
        if (empty($row['tahun_lulus'])) {
            $this->collectError('tahun_lulus', 'Kolom wajib diisi', '(kosong)', 'Masukkan tahun kelulusan (4 digit angka)');
            return null;
        }
        if (empty($row['nama_jenjang'])) {
            $this->collectError('nama_jenjang', 'Kolom wajib diisi', '(kosong)', 'Masukkan nama jenjang pendidikan sesuai referensi');
            return null;
        }
        if (!isset($row['jumlah_lembar']) || $row['jumlah_lembar'] === '') {
            $this->collectError('jumlah_lembar', 'Kolom wajib diisi', '(kosong)', 'Masukkan jumlah lembar legalisir (angka bulat)');
            return null;
        }

        // ============================================
        // 2. VALIDASI FORMAT DATA
        // ============================================
        
        $date = null;
        try {
            if (is_numeric($row['tanggal_transaksi'])) {
                $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['tanggal_transaksi']);
            } else {
                $date = Carbon::parse($row['tanggal_transaksi']);
            }
        } catch (Exception $e) {
            $this->collectError('tanggal_transaksi', 'Format tanggal tidak valid', $row['tanggal_transaksi'], 'Format yang diterima: YYYY-MM-DD atau serial Excel');
            return null;
        }

        $year = $date->format('Y');

        if (!is_numeric($row['tahun_lulus']) || strlen(trim($row['tahun_lulus'])) != 4) {
            $this->collectError('tahun_lulus', 'Format tahun lulus tidak valid', $row['tahun_lulus'], 'Harus berupa 4 digit angka, misal: 2023');
            return null;
        }

        if (!is_numeric($row['jumlah_lembar']) || $row['jumlah_lembar'] < 1) {
            $this->collectError('jumlah_lembar', 'Jumlah lembar tidak valid', $row['jumlah_lembar'], 'Harus berupa angka minimal 1');
            return null;
        }

        // ============================================
        // 3. VALIDASI MASTER DATA & HITUNG HARGA
        // ============================================
        
        $jenjangKey = strtolower(trim($row['nama_jenjang']));
        if (!isset($this->educationLevelsCache[$jenjangKey])) {
            $availableNames = array_map(function($l) { return $l->name; }, array_values($this->educationLevelsCache));
            $this->collectError(
                'nama_jenjang',
                "Jenjang pendidikan '{$row['nama_jenjang']}' tidak ditemukan",
                $row['nama_jenjang'],
                'Valid: ' . implode(', ', $availableNames) . ' (Lihat Sheet Referensi)'
            );
            return null;
        }

        $educationLevel = $this->educationLevelsCache[$jenjangKey];
        $totalPrice = (int)$row['jumlah_lembar'] * $educationLevel->price_per_page;

        // ============================================
        // 4. PREPARE DATA FOR BUFFERING
        // ============================================
        
        $legalizationData = [
            'date' => $date,
            'year' => $year,
            'alumni_name' => trim($row['nama_alumni']),
            'graduation_year' => trim($row['tahun_lulus']),
            'education_level_id' => $educationLevel->id,
            'page_count' => (int)$row['jumlah_lembar'],
            'total_price' => $totalPrice,
            'status' => 'draft',
            'created_by' => auth()->id() ?? 1,
        ];

        // Buffer per tahun
        if (!isset(self::$bufferedLegalizations[$year])) {
            self::$bufferedLegalizations[$year] = [
                'year' => $year,
                'items' => [],
            ];
        }
        self::$bufferedLegalizations[$year]['items'][] = $legalizationData;

        return null;
    }

    public function hasErrors(): bool
    {
        return count(self::$importErrors) > 0;
    }

    public function getErrors(): array
    {
        return self::$importErrors;
    }

    public static function resetAll()
    {
        self::$importErrors = [];
        self::$bufferedLegalizations = [];
        self::$currentRow = 1;
    }

    public function processBufferedLegalizations()
    {
        foreach (self::$bufferedLegalizations as $year => $group) {
            LegalizationSequence::createLegalizationsWithSequence(
                $year,
                $group['items']
            );
        }

        self::$bufferedLegalizations = [];
    }

    public function getTotalImported(): int
    {
        $total = 0;
        foreach (self::$bufferedLegalizations as $group) {
            $total += count($group['items']);
        }
        return $total;
    }
}
