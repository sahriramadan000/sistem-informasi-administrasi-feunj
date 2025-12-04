<?php

namespace App\Imports;

use App\Models\ClassificationLetter;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Validators\Failure;
use Illuminate\Support\Facades\Log;

class ClassificationLetterImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure
{
    use Importable;

    protected $errors = [];
    protected $failures = [];
    protected $importedCount = 0;
    protected $skippedCount = 0;

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Skip empty rows
        if (empty($row['kode']) && empty($row['nama'])) {
            return null;
        }

        try {
            $this->importedCount++;

            return new ClassificationLetter([
                'code' => $row['kode'],
                'name' => $row['nama'],
                'description' => $row['deskripsi'] ?? null,
                'is_active' => $this->parseBoolean($row['status'] ?? 'aktif'),
            ]);
        } catch (\Exception $e) {
            $this->skippedCount++;
            $this->errors[] = [
                'row' => $this->importedCount + $this->skippedCount + 1, // +1 for header row
                'code' => $row['kode'] ?? 'N/A',
                'error' => $e->getMessage()
            ];
            return null;
        }
    }

    /**
     * Validation rules
     */
    public function rules(): array
    {
        return [
            'kode' => 'required|string|max:20|unique:classification_letters,code',
            'nama' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'status' => 'nullable|string|in:aktif,nonaktif,Aktif,Nonaktif,AKTIF,NONAKTIF',
        ];
    }

    /**
     * Custom validation messages
     */
    public function customValidationMessages()
    {
        return [
            'kode.required' => 'Kode wajib diisi',
            'kode.max' => 'Kode maksimal 20 karakter',
            'kode.unique' => 'Kode sudah terdaftar di database',
            'nama.required' => 'Nama wajib diisi',
            'nama.max' => 'Nama maksimal 100 karakter',
            'status.in' => 'Status harus Aktif atau Nonaktif',
        ];
    }

    /**
     * Handle error during import
     */
    public function onError(\Throwable $e)
    {
        $this->errors[] = [
            'row' => 'Unknown',
            'code' => 'N/A',
            'error' => $e->getMessage()
        ];
        
        Log::error('Excel import error', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }

    /**
     * Handle validation failure
     */
    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $this->skippedCount++;
            
            $this->failures[] = [
                'row' => $failure->row(),
                'attribute' => $failure->attribute(),
                'errors' => $failure->errors(),
                'values' => $failure->values()
            ];
        }
    }

    /**
     * Parse boolean value from string
     */
    protected function parseBoolean($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        $value = strtolower(trim($value));
        return in_array($value, ['aktif', 'active', '1', 'true', 'yes']);
    }

    /**
     * Get import statistics
     */
    public function getStats(): array
    {
        return [
            'imported' => $this->importedCount,
            'skipped' => $this->skippedCount,
            'errors' => $this->errors,
            'failures' => $this->failures,
        ];
    }

    /**
     * Check if import has errors
     */
    public function hasErrors(): bool
    {
        return !empty($this->errors) || !empty($this->failures);
    }

    /**
     * Get formatted error messages for display
     */
    public function getErrors(): array
    {
        $errorMessages = [];

        // Format validation failures (from Laravel validation rules)
        foreach ($this->failures as $failure) {
            $attribute = $this->translateAttribute($failure['attribute']);
            $row = $failure['row'];
            $code = $failure['values']['kode'] ?? 'N/A';
            
            foreach ($failure['errors'] as $error) {
                // Add code information for better context
                if ($failure['attribute'] === 'kode' && str_contains($error, 'sudah terdaftar')) {
                    $errorMessages[] = "Baris {$row}: Kode '{$code}' sudah terdaftar di database. Silakan gunakan kode yang berbeda.";
                } else {
                    $errorMessages[] = "Baris {$row}, kolom {$attribute}: {$error}";
                }
            }
        }

        // Format general errors
        foreach ($this->errors as $error) {
            $code = $error['code'] !== 'N/A' ? " (Kode: {$error['code']})" : '';
            $errorMessages[] = "Baris {$error['row']}{$code}: {$error['error']}";
        }

        return $errorMessages;
    }

    /**
     * Translate column names to Indonesian
     */
    protected function translateAttribute(string $attribute): string
    {
        $translations = [
            'kode' => 'Kode',
            'nama' => 'Nama',
            'deskripsi' => 'Deskripsi',
            'status' => 'Status',
        ];

        return $translations[$attribute] ?? ucfirst($attribute);
    }
}
