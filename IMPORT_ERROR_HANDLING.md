# Import Error Handling Documentation

## Overview

Sistem import data surat sekarang memiliki error handling yang komprehensif. Ketika ada error validasi saat import, sistem akan:

1. ✅ **Mengumpulkan semua error** tanpa throw exception
2. ✅ **Rollback semua** (tidak ada partial success)
3. ✅ **Menampilkan detail error** dalam modal yang user-friendly
4. ✅ **Memberikan solusi** untuk setiap error

---

## Architecture

### Flow Diagram

```
USER UPLOAD EXCEL
    ↓
LettersImport::model() [per baris]
    ├─ Validasi required fields
    ├─ Parse & validate tanggal
    ├─ Lookup master data
    ├─ Validate enum values
    │
    ├─ IF ERROR → collectError() + return null
    ├─ IF VALID → buffer data + return null
    └─ Continue next row
    ↓
Excel::import() selesai [semua baris diproses]
    ↓
LetterImportController::import()
    ├─ Check: hasErrors()?
    │
    ├─ YES → redirect dengan import_errors session
    │   └─ View render error modal
    │       ├─ Show semua error details
    │       ├─ Provide suggestions
    │       └─ Link ke template
    │
    └─ NO → processBufferedLetters()
        ├─ Lock sequence per (letter_type, year)
        ├─ Create batch dengan transaction
        └─ Return success
```

---

## Error Types & Validation

### 1. Required Fields

**Fields yang wajib:**
- `tanggal_surat` - Tanggal surat (format: YYYY-MM-DD)
- `kode_penandatangan` - ID penandatangan (angka)
- `kode_klasifikasi_surat` - Kode klasifikasi (text, contoh: AK, BK)
- `kode_jenis_surat` - Kode jenis surat (ST, SK, SP, SR, SU)

**Error Message:**
```
Baris 5: kode_penandatangan
Error: Kolom wajib diisi
Nilai: (kosong)
Solusi: Gunakan ID penandatangan (angka). Contoh: 1, 2, 3
```

---

### 2. Date Format Validation

**Accepted Formats:**
- `YYYY-MM-DD` (ISO format)
- `DD/MM/YYYY` (European format)
- Excel serial number (auto-converted)

**Error Example:**
```
Baris 12: tanggal_surat
Error: Format tanggal tidak valid
Nilai: 14/05/2026
Solusi: Format yang diterima: YYYY-MM-DD, DD/MM/YYYY, atau serial Excel
```

---

### 3. Master Data Lookup

**What is validated:**
- `kode_penandatangan` → must exist in `signatories` table
- `kode_klasifikasi_surat` → must exist in `classification_letters` table
- `kode_jenis_surat` → must exist in `letter_types` table

**Error Example - Signatory Not Found:**
```
Baris 5: kode_penandatangan
Error: Penandatangan dengan ID '99' tidak ditemukan
Nilai: 99
Solusi: Available IDs: 1, 2, 3, 4, 5

Action: Get correct ID from master Penandatangan page
```

**Error Example - Classification Not Found:**
```
Baris 8: kode_klasifikasi_surat
Error: Klasifikasi dengan kode 'XYZ' tidak ditemukan
Nilai: XYZ
Solusi: Available codes: AK, BK, CK, DK

Action: Get correct code from master Klasifikasi page
```

---

### 4. Enum Value Validation

**Valid Enum Values:**

| Field | Valid Values | Example |
|-------|--------------|---------|
| `sasaran_surat` | internal, external | internal |
| `klasifikasi_keamanan` | B, T, R, SR | B (Biasa) |
| `status` | draft, final | final |

**Error Example:**
```
Baris 15: klasifikasi_keamanan
Error: Nilai 'X' tidak valid
Nilai: X
Solusi: Valid: B (Biasa), T (Terbatas), R (Rahasia), SR (Sangat Rahasia)
```

---

## Error Modal Display

### Components

1. **Header**
   - Icon: Alert/Error icon
   - Title: "Import Gagal"
   - Count: Jumlah baris dengan error

2. **Error List (max-height: scrollable)**
   - Per error item:
     * Row number badge (merah)
     * Field name (bold)
     * Error message (deskripsi masalah)
     * Value yang diinput (code block)
     * Suggestions (solusi perbaikan - warna hijau)

3. **Tips Section**
   - Download template untuk referensi
   - Checklist tips umum

4. **Footer Buttons**
   - "Download Template" - Link ke template export
   - "Tutup" - Close modal

### Visual Example

```
┌─ Import Gagal ─────────────────────────────────────────────┐
│ Ada 3 baris yang memiliki error. Perbaiki dan coba lagi.  │
├──────────────────────────────────────────────────────────┤
│                                                            │
│ [Baris 5]  kode_penandatangan                            │
│ ✗ Penandatangan dengan ID '99' tidak ditemukan           │
│   Anda masukkan: 99                                       │
│   Solusi: Available IDs: 1, 2, 3, 4, 5                   │
│                                                            │
│ [Baris 12] tanggal_surat                                 │
│ ✗ Format tanggal tidak valid                             │
│   Anda masukkan: 14/05/2026                              │
│   Solusi: Format yang diterima: YYYY-MM-DD, DD/MM/YYYY  │
│                                                            │
│ [Baris 15] status                                         │
│ ✗ Nilai 'invalid' tidak valid                            │
│   Anda masukkan: invalid                                  │
│   Solusi: Valid: draft, final                            │
│                                                            │
├─ Tips ────────────────────────────────────────────────────┤
│ • Download template untuk referensi format kolom          │
│ • Pastikan semua field yang wajib diisi sudah terisi     │
│ • Periksa kembali format tanggal dan kode master data    │
│ • Gunakan nilai yang tersedia di sistem                  │
├──────────────────────────────────────────────────────────┤
│ [Download Template]              [Tutup]                 │
└──────────────────────────────────────────────────────────┘
```

---

## Implementation Details

### LettersImport.php

#### Error Collection System

```php
// Static properties
private static $importErrors = [];    // Collect all errors
private static $currentRow = 1;       // Track row number
private static $bufferedLetters = []; // Buffer valid data

// Methods
collectError(field, message, value, suggestions)
  └─ Simpan error ke static array

hasErrors(): bool
  └─ Check apakah ada error

getErrors(): array
  └─ Return semua error details

resetAll()
  └─ Clear errors + buffer (untuk fresh import)

getTotalImported(): int
  └─ Count total valid letters buffered
```

#### Validation Flow

```php
public function model(array $row)
{
    // 1. Skip empty rows
    if (!array_filter($row)) return null;
    
    // 2. Validate required fields
    if (empty($row['tanggal_surat'])) {
        $this->collectError(...);
        return null;
    }
    
    // 3. Parse & validate date
    try {
        $date = parse_date($row['tanggal_surat']);
    } catch (Exception $e) {
        $this->collectError(...);
        return null;
    }
    
    // 4. Lookup master data
    $signatory = Signatory::find($row['kode_penandatangan']);
    if (!$signatory) {
        $this->collectError(...);
        return null;
    }
    
    // 5. Validate enum values
    if (!in_array($status, ['draft', 'final'])) {
        $this->collectError(...);
        return null;
    }
    
    // 6. Buffer valid data
    self::$bufferedLetters[$key][] = $letterData;
    return null;
}
```

---

### LetterImportController.php

#### Error Checking Before Batch Processing

```php
public function import(Request $request)
{
    // 1. Validate file
    $request->validate([...]);
    
    try {
        // 2. Reset for fresh import
        LettersImport::resetAll();
        $importer = new LettersImport;
        
        // 3. Process all rows (collect errors, buffer valid data)
        Excel::import($importer, $file);
        
        // 4. Check if has errors
        if ($importer->hasErrors()) {
            // Rollback - return error modal
            return redirect()
                ->with('import_errors', $importer->getErrors())
                ->with('error', 'Import gagal! Ada ' . count($errors) . ' error.');
        }
        
        // 5. No errors - process batch
        $totalLetters = $importer->getTotalImported();
        $importer->processBufferedLetters();
        
        return redirect()
            ->with('success', "Berhasil import {$totalLetters} surat!");
            
    } catch (Exception $e) {
        // Database error, transaction rollback
        return redirect()->with('error', 'Error: ' . $e->getMessage());
    }
}
```

---

### Blade View (index.blade.php)

#### Error Modal Rendering

```blade
@if (session('import_errors') && count(session('import_errors')) > 0)
    <div id="errorModal" class="fixed inset-0 z-50 overflow-y-auto">
        <!-- Modal header with error count -->
        <h3>Import Gagal</h3>
        <p>Ada {{ count(session('import_errors')) }} baris dengan error</p>
        
        <!-- Error list (scrollable) -->
        <div class="max-h-96 overflow-y-auto">
            @foreach (session('import_errors') as $error)
                <div class="error-item">
                    <span class="row-badge">Baris {{ $error['row'] }}</span>
                    <span class="field-name">{{ $error['field'] }}</span>
                    
                    <div class="error-message">{{ $error['message'] }}</div>
                    
                    @if ($error['value'])
                        <div class="input-value">
                            Anda masukkan: <code>{{ $error['value'] }}</code>
                        </div>
                    @endif
                    
                    @if ($error['suggestions'])
                        <div class="suggestions">
                            Solusi: {{ $error['suggestions'] }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
        
        <!-- Tips section -->
        <div class="tips">Tips perbaikan...</div>
        
        <!-- Footer buttons -->
        <a href="{{ route('letters.import.template') }}">Download Template</a>
        <button onclick="close()">Tutup</button>
    </div>
    
    <!-- Auto-show on page load -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('errorModal').classList.remove('hidden');
        });
    </script>
@endif
```

---

## Safety Mechanisms

### 1. No Partial Success (Rollback All)

**Before:** Bisa ada 100 baris, 50 berhasil insert, 50 error

**After:** Jika ada error di validasi:
- Tidak ada yang ter-insert
- Tidak ada sequence update
- User harus fix dan import ulang

**Code:**
```php
// Check error sebelum processBufferedLetters()
if ($importer->hasErrors()) {
    // Return early - no batch processing
    return redirect()->with('import_errors', $errors);
}
// Hanya sampai sini jika NO ERROR
$importer->processBufferedLetters();
```

### 2. Pessimistic Locking

Saat batch processing (jika tidak ada error validasi), sequence di-lock:

```php
// LetterSequence::createLettersWithSequence()
$sequence = self::where('letter_type_id', $letterTypeId)
    ->where('year', $year)
    ->lockForUpdate()  // LOCK sebelum update
    ->first();

// Update sequence
$sequence->update(['next_number' => $newValue]);

// Create letters (masih dalam lock)
foreach ($lettersData as $data) {
    Letter::create($data);
}
// Lock release setelah transaction commit
```

### 3. Transaction Atomicity

Seluruh batch processing dalam 1 transaction:
- Jika ada error di Letter ke-3 dari 10
- ROLLBACK semua 10 Letter + sequence update
- Database kembali ke state sebelum import

---

## Common Error Scenarios

### Scenario 1: Missing Master Data

**File Content:**
```
tanggal_surat | kode_penandatangan | kode_klasifikasi_surat | kode_jenis_surat
2026-05-14    | 99                 | AK                     | ST
```

**Available Data:**
```
Signatories: IDs 1, 2, 3, 4, 5
```

**Error Display:**
```
Baris 2: kode_penandatangan
Error: Penandatangan dengan ID '99' tidak ditemukan
Nilai: 99
Solusi: Available IDs: 1, 2, 3, 4, 5
```

**Fix:** Change `99` to one of: `1, 2, 3, 4, 5`

---

### Scenario 2: Invalid Date Format

**File Content:**
```
tanggal_surat | kode_penandatangan | kode_klasifikasi_surat | kode_jenis_surat
14-05-2026    | 1                  | AK                     | ST
```

**Error Display:**
```
Baris 2: tanggal_surat
Error: Format tanggal tidak valid
Nilai: 14-05-2026
Solusi: Format yang diterima: YYYY-MM-DD, DD/MM/YYYY, atau serial Excel
```

**Fix:** Change format to `2026-05-14` or `14/05/2026`

---

### Scenario 3: Invalid Enum Value

**File Content:**
```
... | klasifikasi_keamanan | ...
... | INVALID              | ...
```

**Error Display:**
```
Baris 2: klasifikasi_keamanan
Error: Nilai 'INVALID' tidak valid
Nilai: INVALID
Solusi: Valid: B (Biasa), T (Terbatas), R (Rahasia), SR (Sangat Rahasia)
```

**Fix:** Change to one of: `B, T, R, SR`

---

### Scenario 4: Multiple Errors in Same Row

If row has multiple errors, only first error is reported per row:

```
Baris 2: kode_penandatangan
Error: Penandatangan dengan ID '99' tidak ditemukan
```

Fix this → import again → next error will be reported

---

### Scenario 5: Empty Required Field

**Error Display:**
```
Baris 5: kode_jenis_surat
Error: Kolom wajib diisi
Nilai: (kosong)
Solusi: Valid: ST, SK, SP, SR, SU
```

**Fix:** Fill in the field

---

## Testing Error Handling

### Test Case 1: Missing Required Field

```
File: test_missing_required.xlsx
Content:
  tanggal_surat | kode_penandatangan | kode_klasifikasi_surat | kode_jenis_surat
  2026-05-14    | 1                  | (kosong)               | ST

Expected:
  - Error modal appears
  - Shows: "Baris 2: kode_klasifikasi_surat - Kolom wajib diisi"
  - No data imported
```

### Test Case 2: Invalid Date

```
File: test_invalid_date.xlsx
Content:
  tanggal_surat | kode_penandatangan | kode_klasifikasi_surat | kode_jenis_surat
  invalid-date  | 1                  | AK                     | ST

Expected:
  - Error modal appears
  - Shows error with helpful date format suggestions
  - No data imported
```

### Test Case 3: Success Import

```
File: test_valid.xlsx
Content:
  tanggal_surat | kode_penandatangan | kode_klasifikasi_surat | kode_jenis_surat
  2026-05-14    | 1                  | AK                     | ST
  2026-05-15    | 1                  | BK                     | ST

Expected:
  - No error modal
  - Success toast: "Berhasil mengimport 2 surat!"
  - 2 new letters appear in table
  - letter_sequences updated: (ST, 2026, next_number = 3)
```

---

## How to Use

### For End Users

1. **Prepare Excel File**
   - Download template from "Template" button
   - Fill data according to template format
   - Ensure all required fields are filled

2. **Upload File**
   - Click "Import Excel" button
   - Select prepared file
   - Click "Import Data"

3. **Handle Errors (if any)**
   - Error modal appears with details
   - Read "Solusi" column for each error
   - Fix data in Excel
   - Re-upload file

4. **Verify Success**
   - Success message appears
   - New letters appear in table
   - Letter sequences updated

### For Developers

#### To Add New Validation

Edit `LettersImport.php::model()` method:

```php
// Add new validation check
if (invalid_condition) {
    $this->collectError(
        'field_name',
        'Error message yang deskriptif',
        $row['field_name'],
        'Helpful suggestion untuk fix'
    );
    return null;
}
```

#### To Customize Error Display

Edit `resources/views/letters/index.blade.php` error modal section

#### To Change Rollback Behavior

Edit `LetterImportController.php::import()` method - currently no partial success

---

## Related Files

| File | Purpose |
|------|---------|
| `app/Imports/LettersImport.php` | Error collection, validation logic |
| `app/Http/Controllers/LetterImportController.php` | Error checking, batch processing orchestration |
| `resources/views/letters/index.blade.php` | Error modal UI rendering |
| `app/Exports/LetterTemplateExport.php` | Template download for reference |
| `app/Models/LetterSequence.php` | Batch create with locking |
| `app/Models/Letter.php` | Auto-generate letter_number |

---

## Logs

All import activities are logged to `storage/logs/laravel.log`:

```
[2026-05-16 10:30:45] local.WARNING: Import data surat gagal dengan 3 error baris
[2026-05-16 10:30:45] local.WARNING: errors=[...], user_id=1

[2026-05-16 10:35:12] local.INFO: Import data surat berhasil
[2026-05-16 10:35:12] local.INFO: total_letters=50, user_id=1
```

Check logs for debugging import issues.

---

## Future Improvements

1. **Export Error Report as Excel**
   - Generate error report file
   - Download for offline review

2. **Batch Process with Partial Success**
   - Option to import valid rows
   - Error rows shown separately

3. **Template Validation Preview**
   - Show validation results before actual import
   - Dry-run mode

4. **Bulk Correction Mode**
   - Re-upload same file
   - Persist error state
   - Fix and re-import only error rows

5. **Error Analytics**
   - Track common error patterns
   - Suggest template improvements

---

## Summary

Error handling system sekarang:
- ✅ Comprehensive validation (required, format, master data, enum)
- ✅ Detailed error messages dengan solusi
- ✅ User-friendly modal display
- ✅ No partial success (safe rollback all)
- ✅ Sequence lock protection
- ✅ Transaction atomicity
- ✅ Logging untuk debugging
