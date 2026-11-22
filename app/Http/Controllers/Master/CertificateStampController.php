<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\CertificateStamp;
use App\Services\AuditLogService;
use App\Services\ErrorTrackingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Controller untuk manajemen master stempel sertifikat digital.
 */
class CertificateStampController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin,operator');
        $this->middleware(function ($request, $next) {
            if (!auth()->user()?->canAccessCertificateStudio()) {
                abort(403, 'Anda tidak memiliki izin akses ke Master Stempel Sertifikat.');
            }
            return $next($request);
        });
    }

    /**
     * Tampilkan daftar master stempel sertifikat.
     */
    public function index(Request $request)
    {
        try {
            $query = CertificateStamp::query();

            if ($request->filled('search')) {
                $search = trim((string) $request->search);
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%");
                });
            }

            if ($request->filled('status')) {
                if ($request->status === 'active') {
                    $query->where('is_active', true);
                } elseif ($request->status === 'inactive') {
                    $query->where('is_active', false);
                }
            }

            $stamps = $query->orderByDesc('is_active')->orderBy('name')->get();
            $totalStamps = CertificateStamp::count();
            $activeStamps = CertificateStamp::active()->count();

            return view('master.certificate_stamp.index', compact(
                'stamps',
                'totalStamps',
                'activeStamps'
            ));
        } catch (\Throwable $e) {
            $errorId = ErrorTrackingService::logError($e, 'CertificateStampController.index');
            return back()->withErrors(['error' => "Gagal memuat daftar stempel (Kode Error: {$errorId})."]);
        }
    }

    /**
     * Simpan stempel baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'code' => 'nullable|string|max:50',
            'default_opacity' => 'nullable|integer|min:10|max:100',
            'stamp_file' => 'required|image|mimes:png,webp,jpg,jpeg|max:5120',
            'is_active' => 'nullable|boolean',
        ], [
            'stamp_file.required' => 'Berkas gambar stempel wajib diunggah.',
            'stamp_file.mimes' => 'Format stempel harus PNG (disarankan transparan), WEBP, JPG, atau JPEG.',
            'stamp_file.max' => 'Ukuran file stempel maksimal 5 MB.',
        ]);

        try {
            $relativePath = $this->storeStampFile($request->file('stamp_file'), $validated['name']);

            $stamp = CertificateStamp::create([
                'name' => $validated['name'],
                'code' => $validated['code'] ? strtoupper(trim($validated['code'])) : null,
                'file_path' => $relativePath,
                'default_opacity' => (int) ($validated['default_opacity'] ?? 88),
                'is_active' => $request->has('is_active') ? $request->boolean('is_active') : true,
            ]);

            AuditLogService::log('create', 'CertificateStamp', $stamp->id, [
                'name' => $stamp->name,
                'code' => $stamp->code,
                'file_path' => $stamp->file_path,
            ]);

            return redirect()
                ->route('master.certificate-stamps.index')
                ->with('success', "Stempel \"{$stamp->name}\" berhasil ditambahkan.");
        } catch (\Throwable $e) {
            $errorId = ErrorTrackingService::logError($e, 'CertificateStampController.store');
            return back()->withInput()->withErrors(['error' => "Gagal menyimpan stempel (Kode Error: {$errorId})."]);
        }
    }

    /**
     * Perbarui data stempel.
     */
    public function update(Request $request, CertificateStamp $certificateStamp)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'code' => 'nullable|string|max:50',
            'default_opacity' => 'nullable|integer|min:10|max:100',
            'stamp_file' => 'nullable|image|mimes:png,webp,jpg,jpeg|max:5120',
            'is_active' => 'nullable|boolean',
        ]);

        try {
            $data = [
                'name' => $validated['name'],
                'code' => $validated['code'] ? strtoupper(trim($validated['code'])) : null,
                'default_opacity' => (int) ($validated['default_opacity'] ?? $certificateStamp->default_opacity),
                'is_active' => $request->boolean('is_active'),
            ];

            if ($request->hasFile('stamp_file')) {
                $oldPath = public_path($certificateStamp->file_path);
                $data['file_path'] = $this->storeStampFile($request->file('stamp_file'), $validated['name']);
                if ($certificateStamp->file_path && File::exists($oldPath)) {
                    File::delete($oldPath);
                }
            }

            $certificateStamp->update($data);

            AuditLogService::log('update', 'CertificateStamp', $certificateStamp->id, [
                'name' => $certificateStamp->name,
                'is_active' => $certificateStamp->is_active,
            ]);

            return redirect()
                ->route('master.certificate-stamps.index')
                ->with('success', "Stempel \"{$certificateStamp->name}\" berhasil diperbarui.");
        } catch (\Throwable $e) {
            $errorId = ErrorTrackingService::logError($e, 'CertificateStampController.update', ['id' => $certificateStamp->id]);
            return back()->withErrors(['error' => "Gagal memperbarui stempel (Kode Error: {$errorId})."]);
        }
    }

    /**
     * Hapus stempel sertifikat.
     */
    public function destroy(CertificateStamp $certificateStamp)
    {
        try {
            $name = $certificateStamp->name;
            $filePath = $certificateStamp->file_path ? public_path($certificateStamp->file_path) : null;

            $certificateStamp->delete();

            if ($filePath && File::exists($filePath)) {
                File::delete($filePath);
            }

            AuditLogService::log('delete', 'CertificateStamp', $certificateStamp->id, [
                'name' => $name,
            ]);

            return redirect()
                ->route('master.certificate-stamps.index')
                ->with('success', "Stempel \"{$name}\" berhasil dihapus.");
        } catch (\Throwable $e) {
            $errorId = ErrorTrackingService::logError($e, 'CertificateStampController.destroy', ['id' => $certificateStamp->id]);
            return back()->withErrors(['error' => "Gagal menghapus stempel (Kode Error: {$errorId})."]);
        }
    }

    protected function storeStampFile($file, string $name): string
    {
        $dir = public_path('uploads/certificates/stamps');
        if (!File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        $ext = strtolower($file->getClientOriginalExtension() ?: 'png');
        $slug = Str::slug($name) ?: 'stempel';
        $filename = "stamp_{$slug}_" . time() . '_' . Str::random(6) . ".{$ext}";
        $file->move($dir, $filename);

        return "uploads/certificates/stamps/{$filename}";
    }
}
