<?php

namespace App\Http\Controllers;

use App\Models\CertificatePreset;
use App\Models\CertificateStamp;
use App\Models\Letter;
use App\Models\Signatory;
use App\Models\StudioLetterhead;
use App\Services\AuditLogService;
use App\Services\ErrorTrackingService;
use App\Services\GoogleDriveService;
use App\Services\SecureSignatureService;
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
            if (!auth()->user()?->canAccessAnyStudio()) {
                abort(403, 'Anda tidak memiliki izin akses ke fitur Studio.');
            }
            return $next($request);
        });
    }

    /**
     * Tampilkan workspace interaktif Studio Sertifikat / Studio Surat untuk surat tertentu.
     */
    public function show(Letter $letter, Request $request)
    {
        $letter->load(['letterType', 'signatory', 'classification', 'creator']);

        $user = auth()->user();
        $canCert = $user->canAccessCertificateStudio();
        $canLetter = $user->canAccessLetterStudio();

        $isLetterDefault = (bool) (
            $letter->letterType?->supports_letter_studio
            || str_contains(strtolower($letter->letterType?->name ?? ''), 'sipermawa')
            || strtolower($letter->letterType?->code ?? '') === 'smw'
        );

        $requestedMode = $request->query('mode');
        if ($requestedMode === 'letter') {
            if (!$canLetter) {
                abort(403, 'Anda tidak memiliki izin akses ke Studio Surat (SIPERMAWA).');
            }
            $studioMode = 'letter';
        } elseif ($requestedMode === 'certificate') {
            if (!$canCert) {
                abort(403, 'Anda tidak memiliki izin akses ke Studio Sertifikat.');
            }
            $studioMode = 'certificate';
        } else {
            if ($isLetterDefault && $canLetter) {
                $studioMode = 'letter';
            } elseif ($canCert) {
                $studioMode = 'certificate';
            } else {
                $studioMode = 'letter';
            }
        }

        $signatories = Signatory::active()
            ->with('allowedSignatureUsers')
            ->orderByRaw('CASE WHEN id = ? THEN 0 ELSE 1 END', [$letter->signatory_id ?? 0])
            ->orderBy('name')
            ->get();

        $letter->signatory?->load('allowedSignatureUsers');
        $canUsePrimarySig = $letter->signatory ? $letter->signatory->canUserUseSignature($user) : false;

        $stamps = CertificateStamp::active()
            ->orderBy('name')
            ->get();

        $letterheads = StudioLetterhead::active()
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
            'letterheads',
            'presets',
            'gdriveReady',
            'studioMode',
            'canCert',
            'canLetter',
            'canUsePrimarySig'
        ));
    }

    /**
     * Upload cepat aset TTD Penandatangan, Stempel, atau Kop Surat langsung dari Studio.
     */
    public function uploadQuickAsset(Request $request)
    {
        $validated = $request->validate([
            'asset_type' => 'required|in:signature,stamp,letterhead',
            'name' => 'nullable|string|max:150',
            'signatory_id' => 'nullable|exists:signatories,id',
            'default_opacity' => 'nullable|integer|min:10|max:100',
            'asset_file' => 'required|image|mimes:png,webp,jpg,jpeg|max:15360',
        ], [
            'asset_file.required' => 'Pilih gambar berkas terlebih dahulu.',
            'asset_file.mimes' => 'Format file harus PNG, WEBP, JPG, atau JPEG.',
            'asset_file.max' => 'Ukuran gambar maksimal 15 MB.',
        ]);

        try {
            $type = $validated['asset_type'];
            $file = $request->file('asset_file');
            $ext = strtolower($file->getClientOriginalExtension() ?: 'png');

            if ($type === 'letterhead') {
                if (!auth()->user()?->canAccessLetterStudio()) {
                    abort(403, 'Anda tidak memiliki izin mengunggah Kop Surat.');
                }

                $kopName = trim((string) ($validated['name'] ?? '')) ?: 'Kop Surat FEB UNJ';
                $dir = public_path('uploads/studio/letterheads');
                if (!File::isDirectory($dir)) {
                    File::makeDirectory($dir, 0755, true);
                }

                $slug = Str::slug($kopName) ?: 'kop';
                $filename = "kop_{$slug}_" . time() . '_' . Str::random(6) . ".{$ext}";
                $file->move($dir, $filename);
                $relativePath = "uploads/studio/letterheads/{$filename}";

                $letterhead = StudioLetterhead::create([
                    'name' => $kopName,
                    'file_path' => $relativePath,
                    'is_active' => true,
                ]);

                AuditLogService::log('create', 'StudioLetterhead', $letterhead->id, [
                    'action' => 'letter_studio_letterhead_uploaded',
                    'name' => $letterhead->name,
                    'file_path' => $relativePath,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => "Kop Surat \"{$letterhead->name}\" berhasil ditambahkan & siap ditempel.",
                    'asset' => [
                        'id' => $letterhead->id,
                        'type' => 'letterhead',
                        'name' => $letterhead->name,
                        'url' => $letterhead->file_url,
                        'default_opacity' => 100,
                    ],
                ]);
            }

            if ($type === 'signature') {
                $signatory = !empty($validated['signatory_id'])
                    ? Signatory::find($validated['signatory_id'])
                    : null;

                if ($signatory && !$signatory->canUserUseSignature(auth()->user())) {
                    abort(403, 'Anda tidak memiliki izin mengubah spesimen TTD pejabat ini.');
                }

                $slug = Str::slug($signatory?->code ?? ($validated['name'] ?? 'ttd'));
                $filename = "ttd_{$slug}_" . time() . '_' . Str::random(6) . ".{$ext}";

                if ($signatory) {
                    $dir = storage_path('app/private/signatures');
                    if (!File::isDirectory($dir)) {
                        File::makeDirectory($dir, 0755, true);
                    }
                    $file->move($dir, $filename);
                    $relativePath = "private/signatures/{$filename}";

                    $oldPath = $signatory->getAbsoluteSignaturePath();
                    $signatory->update(['signature_path' => $relativePath]);
                    if ($oldPath && File::exists($oldPath)) {
                        File::delete($oldPath);
                    }

                    AuditLogService::log('update', 'Signatory', $signatory->id, [
                        'action' => 'certificate_studio_signature_uploaded',
                        'signature_path' => $relativePath,
                    ]);
                } else {
                    $dir = public_path('uploads/certificates/signatures');
                    if (!File::isDirectory($dir)) {
                        File::makeDirectory($dir, 0755, true);
                    }
                    $file->move($dir, $filename);
                    $relativePath = "uploads/certificates/signatures/{$filename}";

                    AuditLogService::log('create', 'CertificateAsset', null, [
                        'action' => 'certificate_studio_temp_signature_uploaded',
                        'file_path' => $relativePath,
                    ]);
                }

                return response()->json([
                    'success' => true,
                    'message' => $signatory
                        ? "TTD untuk {$signatory->name} berhasil disimpan di penyimpanan privat & siap dipakai."
                        : 'TTD sementara berhasil diunggah.',
                    'asset' => [
                        'type' => 'signature',
                        'signatory_id' => $signatory?->id,
                        'name' => $signatory?->name ?? ($validated['name'] ?: 'Tanda Tangan'),
                        'url' => $signatory ? route('studio.signature-preview', $signatory) : asset($relativePath),
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
            'format' => 'required|in:pdf,png,jpg,pdf_batch,zip_pdf,zip_png,zip_jpg',
            'page_count' => 'nullable|integer|min:1|max:500',
        ]);

        AuditLogService::log('update', 'Letter', $letter->id, [
            'letter_number' => $letter->letter_number,
            'action' => 'certificate_exported',
            'format' => strtoupper($validated['format']),
            'page_count' => (int) ($validated['page_count'] ?? 1),
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Sajikan pratinjau TTD digital ber-watermark & rendah resolusi khusus user berizin.
     */
    public function previewSignature(Signatory $signatory, SecureSignatureService $signatureService)
    {
        $user = auth()->user();
        if (!$signatory->canUserUseSignature($user)) {
            abort(403, 'Anda tidak terdaftar untuk mengakses tanda tangan pejabat ini.');
        }

        $path = $signatory->getAbsoluteSignaturePath();
        if (!$path || !File::exists($path)) {
            abort(404, 'Berkas tanda tangan tidak ditemukan.');
        }

        $previewBinary = $signatureService->renderWatermarkedPreview($path);

        return response($previewBinary, 200, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, private',
            'Pragma' => 'no-cache',
            'Expires' => '0',
            'Content-Disposition' => 'inline; filename="preview.png"',
            'X-Robots-Tag' => 'noindex, nofollow',
        ]);
    }

    /**
     * Tempel TTD asli dari private storage di sisi server (Zero-Exposure) lalu kembalikan hasil flatten.
     */
    public function stampSignatures(Request $request, SecureSignatureService $signatureService)
    {
        $validated = $request->validate([
            'page_image' => 'required|file|image|mimes:png,jpg,jpeg|max:25600',
            'signatures' => 'required|string',
            'letter_id' => 'nullable|integer|exists:letters,id',
            'format' => 'nullable|in:png,jpg',
        ]);

        $items = json_decode($validated['signatures'], true);
        if (!is_array($items) || empty($items)) {
            return response()->json([
                'success' => false,
                'message' => 'Data instruksi penempelan TTD tidak valid.',
            ], 422);
        }

        $user = auth()->user();
        $sigIds = collect($items)->pluck('signatory_id')->filter()->unique()->values();
        $signatories = Signatory::with('allowedSignatureUsers')->whereIn('id', $sigIds)->get()->keyBy('id');

        $instructions = [];
        foreach ($items as $item) {
            $sigId = (int) ($item['signatory_id'] ?? 0);
            $signatory = $signatories->get($sigId);
            if (!$signatory) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pejabat penandatangan tidak ditemukan.',
                ], 404);
            }

            if (!$signatory->canUserUseSignature($user)) {
                return response()->json([
                    'success' => false,
                    'message' => "Akses ditolak: Anda tidak terdaftar sebagai pengguna TTD {$signatory->name}.",
                ], 403);
            }

            $absPath = $signatory->getAbsoluteSignaturePath();
            if (!$absPath || !File::exists($absPath)) {
                return response()->json([
                    'success' => false,
                    'message' => "Berkas TTD {$signatory->name} tidak ditemukan di server.",
                ], 404);
            }

            $instructions[] = [
                'absolute_path' => $absPath,
                'xPct' => (float) ($item['xPct'] ?? 0.5),
                'yPct' => (float) ($item['yPct'] ?? 0.75),
                'widthPct' => (float) ($item['widthPct'] ?? 0.18),
                'rotation' => (float) ($item['rotation'] ?? 0),
                'opacity' => (int) ($item['opacity'] ?? 100),
                'removeBg' => (bool) ($item['removeBg'] ?? false),
                'bgThreshold' => (int) ($item['bgThreshold'] ?? 225),
            ];
        }

        $pageBinary = File::get($request->file('page_image')->getRealPath());
        $outputFormat = $validated['format'] ?? 'png';
        $stampedBinary = $signatureService->stampSignaturesOnPage($pageBinary, $instructions, $outputFormat);

        AuditLogService::log('update', 'Signatory', $sigIds->first(), [
            'action' => 'secure_signature_stamped',
            'letter_id' => $validated['letter_id'] ?? null,
            'signatory_ids' => $sigIds->all(),
            'ip' => $request->ip(),
        ]);

        return response($stampedBinary, 200, [
            'Content-Type' => $outputFormat === 'jpg' ? 'image/jpeg' : 'image/png',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, private',
        ]);
    }
}
