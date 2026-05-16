# Quick Reference: Import Error Handling

## Error Messages & Solutions

### Required Field Missing
```
Error: Kolom wajib diisi
Solusi: Pastikan field ini terisi di Excel
Fields wajib: tanggal_surat, kode_penandatangan, 
             kode_klasifikasi_surat, kode_jenis_surat
```

### Invalid Date Format
```
Error: Format tanggal tidak valid
Accepted: YYYY-MM-DD (2026-05-14)
          DD/MM/YYYY (14/05/2026)
          Excel serial number
```

### Master Data Not Found
```
Error: [Nama] dengan [identifier] '[value]' tidak ditemukan

Examples:
- Penandatangan dengan ID '99' tidak ditemukan
  → Use available IDs: 1, 2, 3, 4, 5
  
- Klasifikasi dengan kode 'XYZ' tidak ditemukan
  → Use available codes: AK, BK, CK, DK
  
- Jenis surat dengan kode 'XX' tidak ditemukan
  → Use: ST, SK, SP, SR, SU
```

### Invalid Enum Value
```
Error: Nilai '[value]' tidak valid

klasifikasi_keamanan:
  Valid: B (Biasa), T (Terbatas), R (Rahasia), SR (Sangat Rahasia)

sasaran_surat:
  Valid: internal, external

status:
  Valid: draft, final
```

---

## Validation Checklist

Before uploading, verify:

- [ ] All required fields filled (no empty cells)
- [ ] Date format correct (YYYY-MM-DD or DD/MM/YYYY)
- [ ] kode_penandatangan uses ID from Signatories
- [ ] kode_klasifikasi_surat uses code from Classifications
- [ ] kode_jenis_surat uses: ST, SK, SP, SR, SU
- [ ] klasifikasi_keamanan uses: B, T, R, SR
- [ ] status uses: draft, final
- [ ] sasaran_surat uses: internal, external

---

## Import Flow

1. **Download Template** → Reference format
2. **Prepare Excel** → Fill required fields
3. **Validate Data** → Check format
4. **Upload File** → Click "Import Excel"
5. **Check Results:**
   - ✅ Success → Data appears in table
   - ❌ Error → Modal shows what to fix

---

## Common Mistakes

| Mistake | Fix |
|---------|-----|
| Empty required field | Fill all wajib fields |
| Date: `14-05-2026` | Use: `2026-05-14` or `14/05/2026` |
| Signatory: `DEP-XYT` | Use ID: `1`, `2`, `3` |
| Status: `Published` | Use: `draft` or `final` |
| Security: `Normal` | Use: `B`, `T`, `R`, `SR` |

---

## Error Modal Actions

When error appears:

1. **Read each error** - Row number, field, message
2. **Check "Solusi"** - Shows valid values
3. **Fix in Excel** - Correct the data
4. **Re-upload** - Retry import
5. **Or Download Template** - Get format reference

---

## Contact Support

If error unclear:
1. Take screenshot of error modal
2. Download template from modal
3. Compare your data with template format
4. Contact admin for master data verification
