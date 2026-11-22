<?php

namespace App\Http\Controllers;

use App\Models\CertificatePreset;
use App\Models\CertificateStamp;
use App\Models\Letter;
use App\Models\Signatory;
use App\Services\AuditLogService;
use App\Services\ErrorTrackingService;
use App\Services\GoogleDriveService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Controller untuk Studio Sertifikat (Penomoran, TTD, & Stempel Digital)
 */
class CertificateStudioController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin,operator']);
        $this->middleware(function ($request, $next) {
            if (!auth()->user()?->canAccessCertificateStudio()) {
                abort(403, 'Anda tidak memiliki izin akses ke fitur Studio Sertifikat.');
            }
            return $next($request);
        });
    }

    /**
     * Tampilkan workspace interaktif Studio Sertifikat untuk surat tertentu.
     */
    public function show(Letter $letter)
    {
        $letter->load(['letterType', 'signatory', 'classification', 'creator']);

        $signatories = Signatory::active()
            ->orderByRaw('CASE WHEN id = ? THEN 0 ELSE 1 END', [$letter->signatory_id ?? 0])
            ->orderBy('name')
            ->get();

        $stamps = CertificateStamp::active()
            ->orderBy('name')
            ->get();

        $presets = CertificatePreset::where(function ($q) use ($letter) {
            $q->whereNull('letter_type_id')
              ->orWhere('letter_type_id', $letter->letter_type_id);
        })
            ->orderBy('name')
            ->get();

        $driveService = app(GoogleDriveService::class);
        $gdriveReady = $driveService->isConnected() && !empty($driveService->getRootFolderId());

        return view('certificates.studio', compact(
            'letter',
            'signatories',
            'stamps',
            'presets',
            'gdriveReady'
        ));
    }

    /**
     * Upload cepat aset TTD Penandatangan atau Stempel langsung dari Studio Sertifikat.
     */
    public function uploadQuickAsset(Request $request)
    {
        $validated = $request->validate([
            'asset_type' => 'required|in:signature,stamp',
            'name' => 'nullable|string|max:150',
            'signatory_id' => 'nullable|exists:signatories,id',
            'default_opacity' => 'nullable|integer|min:10|max:100',
            'asset_file' => 'required|image|mimes:png,webp,jpg,jpeg|max:5120',
        ], [
            'asset_file.required' => 'Pilih gambar berkas terlebih dahulu.',
            'asset_file.mimes' => 'Format file harus PNG, WEBP, JPG, atau JPEG.',
            'asset_file.max' => 'Ukuran gambar maksimal 5 MB.',
        ]);

        try {
            $type = $validated['asset_type'];
            $file = $request->file('asset_file');
            $ext = strtolower($file->getClientOriginalExtension() ?: 'png');

            if ($type === 'signature') {
                $dir = public_path('uploads/certificates/signatures');
                if (!File::isDirectory($dir)) {
                    File::makeDirectory($dir, 0755, true);
                }

                $signatory = !empty($validated['signatory_id'])
                    ? Signatory::find($validated['signatory_id'])
                    : null;

                $slug = Str::slug($signatory?->code ?? ($validated['name'] ?? 'ttd'));
                $filename = "ttd_{$slug}_" . time() . '_' . Str::random(6) . ".{$ext}";
                $file->move($dir, $filename);
                $relativePath = "uploads/certificates/signatures/{$filename}";

                if ($signatory) {
                    $oldPath = $signatory->signature_path ? public_path($signatory->signature_path) : null;
                    $signatory->update(['signature_path' => $relativePath]);
                    if ($oldPath && File::exists($oldPath)) {
                        File::delete($oldPath);
                    }

                    AuditLogService::log('update', 'Signatory', $signatory->id, [
                        'action' => 'certificate_studio_signature_uploaded',
                        'signature_path' => $relativePath,
                    ]);
                } else {
                    AuditLogService::log('create', 'CertificateAsset', null, [
                        'action' => 'certificate_studio_temp_signature_uploaded',
                        'file_path' => $relativePath,
                    ]);
                }

                return response()->json([
                    'success' => true,
                    'message' => $signatory
                        ? "TTD untuk {$signatory->name} berhasil disimpan & siap ditempel."
                        : 'TTD berhasil diunggah.',
                    'asset' => [
                        'type' => 'signature',
                        'signatory_id' => $signatory?->id,
                        'name' => $signatory?->name ?? ($validated['name'] ?: 'Tanda Tangan'),
                        'url' => asset($relativePath),
                        'default_opacity' => 100,
                    ],
                ]);
            }

            // Upload Stamp
            $stampName = trim((string) ($validated['name'] ?? '')) ?: 'Stempel Baru';
            $dir = public_path('uploads/certificates/stamps');
            if (!File::isDirectory($dir)) {
                File::makeDirectory($dir, 0755, true);
            }

            $slug = Str::slug($stampName) ?: 'stempel';
            $filename = "stamp_{$slug}_" . time() . '_' . Str::random(6) . ".{$ext}";
            $file->move($dir, $filename);
            $relativePath = "uploads/certificates/stamps/{$filename}";

            $stamp = CertificateStamp::create([
                'name' => $stampName,
                'code' => strtoupper(Str::limit($slug, 15, '')),
                'file_path' => $relativePath,
                'default_opacity' => (int) ($validated['default_opacity'] ?? 88),
                'is_active' => true,
            ]);

            AuditLogService::log('create', 'CertificateStamp', $stamp->id, [
                'action' => 'certificate_studio_stamp_uploaded',
                'name' => $stamp->name,
                'file_path' => $relativePath,
            ]);

            return response()->json([
                'success' => true,
                'message' => "Stempel \"{$stamp->name}\" berhasil ditambahkan ke galeri.",
                'asset' => [
                    'id' => $stamp->id,
                    'type' => 'stamp',
                    'name' => $stamp->name,
                    'url' => $stamp->file_url,
                    'default_opacity' => $stamp->default_opacity,
                ],
            ]);
        } catch (\Throwable $e) {
            $errorId = ErrorTrackingService::logError($e, 'CertificateStudioController.uploadQuickAsset');

            return response()->json([
                'success' => false,
                'message' => "Gagal mengunggah aset (Kode Error: {$errorId}).",
            ], 500);
        }
    }

    /**
     * Simpan preset koordinat & gaya posisi elemen sertifikat.
     */
    public function savePreset(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'letter_type_id' => 'nullable|exists:letter_types,id',
            'config' => 'required|array',
        ]);

        try {
            $preset = CertificatePreset::create([
                'name' => trim($validated['name']),
                'letter_type_id' => $validated['letter_type_id'] ?? null,
                'config' => $validated['config'],
                'created_by' => auth()->id(),
            ]);

            AuditLogService::log('create', 'CertificatePreset', $preset->id, [
                'name' => $preset->name,
                'letter_type_id' => $preset->letter_type_id,
            ]);

            return response()->json([
                'success' => true,
                'message' => "Preset \"{$preset->name}\" berhasil disimpan.",
                'preset' => $preset,
            ]);
        } catch (\Throwable $e) {
            $errorId = ErrorTrackingService::logError($e, 'CertificateStudioController.savePreset');

            return response()->json([
                'success' => false,
                'message' => "Gagal menyimpan preset (Kode Error: {$errorId}).",
            ], 500);
        }
    }

    /**
     * Hapus preset posisi sertifikat.
     */
    public function deletePreset(CertificatePreset $preset)
    {
        try {
            $name = $preset->name;
            $preset->delete();

            AuditLogService::log('delete', 'CertificatePreset', $preset->id, [
                'name' => $name,
            ]);

            return response()->json([
                'success' => true,
                'message' => "Preset \"{$name}\" berhasil dihapus.",
            ]);
        } catch (\Throwable $e) {
            $errorId = ErrorTrackingService::logError($e, 'CertificateStudioController.deletePreset', ['id' => $preset->id]);

            return response()->json([
                'success' => false,
                'message' => "Gagal menghapus preset (Kode Error: {$errorId}).",
            ], 500);
        }
    }

    /**
     * Catat aktivitas export sertifikat ke Activity Logs.
     */
    public function logExport(Request $request, Letter $letter)
    {
        $validated = $request->validate([
            'format' => 'required|in:pdf,png,jpg',
        ]);

        AuditLogService::log('update', 'Letter', $letter->id, [
            'letter_number' => $letter->letter_number,
            'action' => 'certificate_exported',
            'format' => strtoupper($validated['format']),
        ]);

        return response()->json(['success' => true]);
    }
}
