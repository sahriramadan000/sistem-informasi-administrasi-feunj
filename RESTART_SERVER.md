# 🔄 Restart Development Server

## Masalah yang Terjadi:
Error log menunjukkan `required_roles":["admin"]` padahal middleware sudah diupdate menjadi `admin,operator,viewer`.

## Penyebabnya:
- **OPcache** atau **cached compiled files** masih menyimpan kode lama
- **Web server** perlu di-restart untuk load perubahan controller

---

## ✅ Solusi: Restart Server

### Jika menggunakan `php artisan serve`:

1. **Stop server** dengan `Ctrl + C`
2. **Clear semua cache**:
   ```bash
   php artisan optimize:clear
   composer dump-autoload
   ```
3. **Restart server**:
   ```bash
   php artisan serve
   ```

### Jika menggunakan XAMPP/WAMP/Laragon:

1. **Stop Apache** dari control panel
2. **Clear cache** di terminal:
   ```bash
   cd E:\Sahri\Unique\sistem-informasi-administrasi-FEB-UNJ
   php artisan optimize:clear
   composer dump-autoload
   ```
3. **Start Apache** kembali

### Jika menggunakan Nginx/Apache Production:

1. **Clear Laravel cache**:
   ```bash
   php artisan optimize:clear
   composer dump-autoload
   ```

2. **Clear PHP OPcache** (pilih salah satu):
   
   **Untuk Apache:**
   ```bash
   sudo service apache2 restart
   ```
   
   **Untuk Nginx:**
   ```bash
   sudo service nginx restart
   sudo service php8.2-fpm restart  # sesuaikan versi PHP
   ```

3. **Clear browser cache** juga: `Ctrl + Shift + Delete`

---

## 🧪 Test Setelah Restart:

1. Login sebagai **operator**
2. Buka halaman **Daftar Surat**
3. Klik tombol **"Buat Surat Baru"**
4. Seharusnya **TIDAK** ada error "Unauthorized access"
5. Cek `storage/logs/laravel.log` - seharusnya tidak ada log error lagi

---

## ✅ Yang Sudah Diperbaiki:

File: `app/Http/Controllers/LetterController.php`

```php
public function __construct()
{
    $this->middleware('auth');
    // ✅ Semua role bisa melihat index dan show
    $this->middleware('role:admin,operator,viewer')->only(['index', 'show']);
    // ✅ Hanya admin dan operator yang bisa create, store, edit, update, destroy
    $this->middleware('role:admin,operator')->only(['create', 'store', 'edit', 'update', 'destroy']);
}
```

**Perubahan dari:**
```php
// ❌ SALAH - middleware saling menimpa
$this->middleware('role:operator')->only([...]);
$this->middleware('role:viewer')->only([...]);
$this->middleware('role:admin')->only([...]); // Hanya ini yang berlaku
```

---

## 📊 Akses yang Benar Sekarang:

| Action | Admin | Operator | Viewer |
|--------|-------|----------|--------|
| Lihat Daftar | ✅ | ✅ | ✅ |
| Detail Surat | ✅ | ✅ | ✅ |
| Buat Surat | ✅ | ✅ | ❌ |
| Edit Surat | ✅ | ✅ | ❌ |
| Hapus Surat | ✅ | ✅ | ❌ |

---

## ⚠️ Jika Masih Error Setelah Restart:

Jalankan command ini secara berurutan:

```bash
# 1. Clear semua cache
php artisan optimize:clear

# 2. Regenerate autoload
composer dump-autoload -o

# 3. Clear config cache
php artisan config:clear

# 4. Clear route cache  
php artisan route:clear

# 5. Clear view cache
php artisan view:clear

# 6. Verify middleware terdaftar
php artisan route:list --path=letters/create

# 7. Restart server
php artisan serve
```

Kemudian test lagi dengan user **operator**.

---

## 📝 Catatan Tambahan:

- ✅ Cache sudah di-clear dengan `php artisan optimize:clear`
- ✅ Autoload sudah di-regenerate dengan `composer dump-autoload`
- ✅ Middleware sudah benar di controller
- ✅ RoleMiddleware sudah support multiple roles dengan koma

**Tinggal restart web server** dan seharusnya sudah berfungsi! 🚀
