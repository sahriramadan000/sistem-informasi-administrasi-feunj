# Test Data untuk Error Handling Import

## Data Master yang Tersedia

### Signatories (Penandatangan)
```
ID 1: UN39.5.FEB
ID 2: UN39.8.FEB
ID 3: UN39.6.FEB
ID 4: UN39.7.FEB
ID 5: 6.FEB
ID 6: 7.FEB
ID 7: 8.FEB
ID 8: 9.FEB
```

### Letter Types (Jenis Surat)
```
ST     - Surat Tugas
SMW    - SIPERMAWA
SK     - Surat Keputusan
MOU    - MOU dan IA
UND    - Surat Undangan
SU     - Surat Umum
SRTF   - Sertifikat
```

### Classifications (Klasifikasi Surat) - Sample
```
VAL-ZJ    - Validasi Berkas Ijazah
SKL-MS    - Surat Keterangan Lulus
KPT-MK    - Keterangan Pindah Kuliah
PKL-MB    - Perizinan Kegiatan Mahasiswa
RGS-PR    - Registrasi Ulang
PR        - PERENCANAAN
DL        - PENDIDIKAN DAN PELATIHAN
TM        - PENERIMAAN MAHASISWA
... (dan banyak lagi)
```

---

## Test Cases untuk Error Handling

### Test 1: Valid Data (Success Case)

**File: test_valid.xlsx**

| tanggal_surat | kode_penandatangan | kode_klasifikasi_surat | kode_jenis_surat | perihal | tujuan | nama_mahasiswa | sasaran_surat | klasifikasi_keamanan | status |
|---|---|---|---|---|---|---|---|---|---|
| 2026-05-14 | 1 | VAL-ZJ | ST | Surat Tugas | Rektorat | Budi Santoso | internal | B | final |
| 2026-05-15 | 2 | SKL-MS | SK | Surat Keputusan | BAAK | Ani Wijaya | internal | B | draft |

**Expected Result:**
- ✅ Success message
- ✅ 2 letters imported
- ✅ letter_sequences updated

---

### Test 2: Missing Required Field - tanggal_surat

**File: test_missing_date.xlsx**

| tanggal_surat | kode_penandatangan | kode_klasifikasi_surat | kode_jenis_surat |
|---|---|---|---|
| (kosong) | 1 | VAL-ZJ | ST |
| 2026-05-15 | 2 | SKL-MS | SK |

**Expected Error Modal:**
```
Baris 2: tanggal_surat
✗ Kolom wajib diisi
Nilai: (kosong)
Solusi: Gunakan format: YYYY-MM-DD atau DD/MM/YYYY
```

---

### Test 3: Missing Required Field - kode_penandatangan

**File: test_missing_signatory.xlsx**

| tanggal_surat | kode_penandatangan | kode_klasifikasi_surat | kode_jenis_surat |
|---|---|---|---|
| 2026-05-14 | (kosong) | VAL-ZJ | ST |

**Expected Error Modal:**
```
Baris 2: kode_penandatangan
✗ Kolom wajib diisi
Nilai: (kosong)
Solusi: Gunakan ID penandatangan (angka). Contoh: 1, 2, 3, 4, 5, 6, 7, 8
```

---

### Test 4: Invalid Date Format

**File: test_invalid_date.xlsx**

| tanggal_surat | kode_penandatangan | kode_klasifikasi_surat | kode_jenis_surat |
|---|---|---|---|
| 14-05-2026 | 1 | VAL-ZJ | ST |

**Expected Error Modal:**
```
Baris 2: tanggal_surat
✗ Format tanggal tidak valid
Nilai: 14-05-2026
Solusi: Format yang diterima: YYYY-MM-DD, DD/MM/YYYY, atau serial Excel
```

---

### Test 5: Master Data Not Found - Invalid Signatory ID

**File: test_invalid_signatory.xlsx**

| tanggal_surat | kode_penandatangan | kode_klasifikasi_surat | kode_jenis_surat |
|---|---|---|---|
| 2026-05-14 | 99 | VAL-ZJ | ST |

**Expected Error Modal:**
```
Baris 2: kode_penandatangan
✗ Penandatangan dengan ID '99' tidak ditemukan
Nilai: 99
Solusi: Available IDs: 1, 2, 3, 4, 5, 6, 7, 8
```

---

### Test 6: Master Data Not Found - Invalid Classification Code

**File: test_invalid_classification.xlsx**

| tanggal_surat | kode_penandatangan | kode_klasifikasi_surat | kode_jenis_surat |
|---|---|---|---|
| 2026-05-14 | 1 | XYZ-INVALID | ST |

**Expected Error Modal:**
```
Baris 2: kode_klasifikasi_surat
✗ Klasifikasi dengan kode 'XYZ-INVALID' tidak ditemukan
Nilai: XYZ-INVALID
Solusi: Available codes: VAL-ZJ, SKL-MS, KPT-MK, PKL-MB, RGS-PR, PR, DL, TM, ...
```

---

### Test 7: Master Data Not Found - Invalid Letter Type Code

**File: test_invalid_letter_type.xlsx**

| tanggal_surat | kode_penandatangan | kode_klasifikasi_surat | kode_jenis_surat |
|---|---|---|---|
| 2026-05-14 | 1 | VAL-ZJ | XX |

**Expected Error Modal:**
```
Baris 2: kode_jenis_surat
✗ Jenis surat dengan kode 'XX' tidak ditemukan
Nilai: XX
Solusi: Valid: ST, SMW, SK, MOU, UND, SU, SRTF
```

---

### Test 8: Invalid Enum - sasaran_surat

**File: test_invalid_target.xlsx**

| tanggal_surat | kode_penandatangan | kode_klasifikasi_surat | kode_jenis_surat | sasaran_surat |
|---|---|---|---|---|
| 2026-05-14 | 1 | VAL-ZJ | ST | invalid |

**Expected Error Modal:**
```
Baris 2: sasaran_surat
✗ Nilai 'invalid' tidak valid
Nilai: invalid
Solusi: Valid: internal, external
```

---

### Test 9: Invalid Enum - klasifikasi_keamanan

**File: test_invalid_security.xlsx**

| tanggal_surat | kode_penandatangan | kode_klasifikasi_surat | kode_jenis_surat | klasifikasi_keamanan |
|---|---|---|---|---|
| 2026-05-14 | 1 | VAL-ZJ | ST | X |

**Expected Error Modal:**
```
Baris 2: klasifikasi_keamanan
✗ Nilai 'X' tidak valid
Nilai: X
Solusi: Valid: B (Biasa), T (Terbatas), R (Rahasia), SR (Sangat Rahasia)
```

---

### Test 10: Invalid Enum - status

**File: test_invalid_status.xlsx**

| tanggal_surat | kode_penandatangan | kode_klasifikasi_surat | kode_jenis_surat | status |
|---|---|---|---|---|
| 2026-05-14 | 1 | VAL-ZJ | ST | published |

**Expected Error Modal:**
```
Baris 2: status
✗ Nilai 'published' tidak valid
Nilai: published
Solusi: Valid: draft, final
```

---

### Test 11: Multiple Errors - First Error Only

**File: test_multiple_errors.xlsx**

| tanggal_surat | kode_penandatangan | kode_klasifikasi_surat | kode_jenis_surat |
|---|---|---|---|
| 14-05-2026 | (kosong) | XYZ | XX |

**Expected Error Modal:**
Will show FIRST error encountered (tanggal_surat):
```
Baris 2: tanggal_surat
✗ Format tanggal tidak valid
Nilai: 14-05-2026
Solusi: Format yang diterima: YYYY-MM-DD, DD/MM/YYYY, atau serial Excel
```
(User must fix and re-import to see next errors)

---

### Test 12: Mixed Valid & Invalid (All Rollback)

**File: test_mixed_rows.xlsx**

| tanggal_surat | kode_penandatangan | kode_klasifikasi_surat | kode_jenis_surat |
|---|---|---|---|
| 2026-05-14 | 1 | VAL-ZJ | ST |
| 2026-05-15 | 99 | VAL-ZJ | SK |
| 2026-05-16 | 2 | SKL-MS | SU |

**Expected Result:**
- ❌ Import fails
- ❌ Error shown for baris 3 (invalid signatory 99)
- ❌ NOTHING imported (even baris 2 & 4 are valid)
- ❌ No letter_sequences updated

---

## Manual Testing Steps

### Step 1: Download Template
1. Go to Letters list page
2. Click "Template" button
3. Save template as `test_template.xlsx`

### Step 2: Test Each Case
1. Prepare test file (copy template, modify data)
2. Click "Import Excel" button
3. Select test file
4. Click "Import Data"
5. Observe error modal (if error) or success message (if valid)

### Step 3: Verify Results
- If error: Check error details match expected
- If success: Verify data appears in letters table
- Check letter_sequences table updated

### Step 4: Check Logs
```bash
tail -f storage/logs/laravel.log
```

---

## Expected Error Count by Test Case

| Test Case | Error Count | Status |
|---|---|---|
| Test 1 - Valid Data | 0 | ✅ Success |
| Test 2 - Missing Date | 1 | ❌ Fail |
| Test 3 - Missing Signatory | 1 | ❌ Fail |
| Test 4 - Invalid Date | 1 | ❌ Fail |
| Test 5 - Invalid Signatory ID | 1 | ❌ Fail |
| Test 6 - Invalid Classification | 1 | ❌ Fail |
| Test 7 - Invalid Letter Type | 1 | ❌ Fail |
| Test 8 - Invalid Target | 1 | ❌ Fail |
| Test 9 - Invalid Security | 1 | ❌ Fail |
| Test 10 - Invalid Status | 1 | ❌ Fail |
| Test 11 - Multiple Errors | 1 | ❌ Fail (only first shown) |
| Test 12 - Mixed Valid/Invalid | 1 | ❌ Fail (rollback all) |

---

## Debugging Tips

### If Error Modal Doesn't Show
1. Check browser console for JavaScript errors
2. Verify session data: Check `session('import_errors')`
3. Check Laravel logs for import errors

### If Wrong Error Message
1. Check `collectError()` calls in LettersImport.php
2. Verify master data in database
3. Check enum values in model

### If Data Still Imports Despite Errors
1. Verify `hasErrors()` check in controller
2. Ensure `processBufferedLetters()` not called when errors exist
3. Check transaction handling

### If Sequence Not Updated
1. Check `LetterSequence::createLettersWithSequence()` logic
2. Verify sequence lock working
3. Check database transaction

---

## Performance Notes

- **Import 100 valid rows**: ~5-10 seconds (includes validation + batch processing + sequence lock)
- **Import with 50 errors**: ~2-3 seconds (validation stops on first error per row)
- **Error modal display**: Instant (client-side)
- **Database queries**: ~N+3 (N = number of rows)
  - 1 query per master data lookup (Signatory, Classification, LetterType)
  - +1 for LetterSequence lock + update
  - +N for batch Letter creation

---

## Custom Error Scenarios (Optional)

Feel free to test additional scenarios:
- Duplicate letter numbers
- Very long strings (>255 chars)
- Special characters in codes
- Whitespace in required fields
- Very large Excel files (1000+ rows)
- Different date formats in same file
- Mixed Indonesian/English month names
