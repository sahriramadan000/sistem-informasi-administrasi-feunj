<?php

namespace App\Services;

use App\Exceptions\GoogleDriveException;
use App\Models\DriveFolder;
use App\Models\Letter;
use App\Models\LetterPurpose;
use App\Models\LetterType;
use App\Models\SystemSetting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

class GoogleDriveService
{
    protected const OAUTH_AUTH_URL = 'https://accounts.google.com/o/oauth2/v2/auth';
    protected const OAUTH_TOKEN_URL = 'https://oauth2.googleapis.com/token';
    protected const DRIVE_API_BASE = 'https://www.googleapis.com/drive/v3';
    protected const DRIVE_UPLOAD_BASE = 'https://www.googleapis.com/upload/drive/v3';
    protected const TOKEN_CACHE_KEY = 'google_drive_access_token';
    protected const ORPHAN_CONVERT_CACHE_KEY = 'gdrive_convert_orphan_file_ids';

    public function isConfigured(): bool
    {
        return !empty(config('services.google_drive.client_id'))
            && !empty(config('services.google_drive.client_secret'));
    }

    public function isConnected(): bool
    {
        return $this->isConfigured()
            && !empty(SystemSetting::getValue('gdrive_refresh_token'));
    }

    public function getConnectedEmail(): ?string
    {
        return SystemSetting::getValue('gdrive_connected_email');
    }

    public function getRootFolderId(): ?string
    {
        return SystemSetting::getValue('gdrive_root_folder_id');
    }

    public function isAutoShareEnabled(): bool
    {
        return SystemSetting::getValue('gdrive_auto_share', '1') === '1';
    }

    public function getLastError(): ?string
    {
        return SystemSetting::getValue('gdrive_last_error');
    }

    /**
     * Buat URL otorisasi OAuth 2.0 Google.
     */
    public function getAuthUrl(string $state): string
    {
        if (!$this->isConfigured()) {
            throw new GoogleDriveException(
                GoogleDriveException::NOT_CONFIGURED,
                'Kredensial Google Drive (Client ID / Client Secret) belum dikonfigurasi di .env.'
            );
        }

        $params = http_build_query([
            'client_id' => config('services.google_drive.client_id'),
            'redirect_uri' => config('services.google_drive.redirect_uri'),
            'response_type' => 'code',
            'scope' => config('services.google_drive.scope'),
            'access_type' => 'offline',
            'prompt' => 'consent',
            'include_granted_scopes' => 'true',
            'state' => $state,
        ]);

        return self::OAUTH_AUTH_URL . '?' . $params;
    }

    /**
     * Proses callback OAuth 2.0 dan simpan Refresh Token terenkripsi.
     */
    public function handleCallback(string $code): array
    {
        try {
            $response = Http::asForm()->timeout(20)->post(self::OAUTH_TOKEN_URL, [
                'code' => $code,
                'client_id' => config('services.google_drive.client_id'),
                'client_secret' => config('services.google_drive.client_secret'),
                'redirect_uri' => config('services.google_drive.redirect_uri'),
                'grant_type' => 'authorization_code',
            ]);

            $payload = $response->json();

            if (!$response->successful() || empty($payload['access_token'])) {
                $errDesc = $payload['error_description'] ?? $payload['error'] ?? $response->body();
                throw new \RuntimeException("Gagal menukar authorization code: {$errDesc}");
            }

            $accessToken = $payload['access_token'];
            $refreshToken = $payload['refresh_token'] ?? SystemSetting::getValue('gdrive_refresh_token');

            if (empty($refreshToken)) {
                throw new \RuntimeException('Google tidak mengembalikan refresh_token. Silakan cabut akses aplikasi di akun Google lalu hubungkan ulang.');
            }

            SystemSetting::setValue('gdrive_refresh_token', $refreshToken, true);
            Cache::put(self::TOKEN_CACHE_KEY, $accessToken, now()->addMinutes(50));

            $about = $this->fetchAboutWithToken($accessToken);
            $email = $about['user']['emailAddress'] ?? null;
            if ($email) {
                SystemSetting::setValue('gdrive_connected_email', $email);
            }

            SystemSetting::removeKey('gdrive_last_error');

            return [
                'email' => $email,
                'storage_quota' => $about['storageQuota'] ?? null,
            ];
        } catch (Throwable $e) {
            $errorId = ErrorTrackingService::logError($e, 'GoogleDriveService.handleCallback');
            SystemSetting::setValue('gdrive_last_error', "Otorisasi gagal ({$errorId}): {$e->getMessage()}");

            throw new GoogleDriveException(
                GoogleDriveException::TOKEN_EXPIRED,
                'Gagal menghubungkan akun Google Drive. Silakan coba otorisasi ulang.',
                $e->getMessage(),
                $errorId,
                422,
                $e
            );
        }
    }

    /**
     * Ambil access token aktif (refresh otomatis bila kedaluwarsa).
     */
    public function getAccessToken(): string
    {
        if (!$this->isConfigured()) {
            throw new GoogleDriveException(
                GoogleDriveException::NOT_CONFIGURED,
                'Integrasi Google Drive belum dikonfigurasi oleh Administrator.'
            );
        }

        $cached = Cache::get(self::TOKEN_CACHE_KEY);
        if (!empty($cached)) {
            return $cached;
        }

        $refreshToken = SystemSetting::getValue('gdrive_refresh_token');
        if (empty($refreshToken)) {
            throw new GoogleDriveException(
                GoogleDriveException::NOT_CONNECTED,
                'Akun Google Drive belum dihubungkan oleh Admin. Hubungi Admin atau gunakan Link Manual sementara.'
            );
        }

        try {
            $response = Http::asForm()->timeout(20)->post(self::OAUTH_TOKEN_URL, [
                'client_id' => config('services.google_drive.client_id'),
                'client_secret' => config('services.google_drive.client_secret'),
                'refresh_token' => $refreshToken,
                'grant_type' => 'refresh_token',
            ]);

            $payload = $response->json();

            if (!$response->successful() || empty($payload['access_token'])) {
                $errCode = $payload['error'] ?? 'unknown_error';
                $errDesc = $payload['error_description'] ?? $response->body();
                throw new \RuntimeException("OAuth refresh token gagal [{$errCode}]: {$errDesc}");
            }

            $accessToken = $payload['access_token'];
            $ttl = max(60, ((int) ($payload['expires_in'] ?? 3600)) - 300);
            Cache::put(self::TOKEN_CACHE_KEY, $accessToken, now()->addSeconds($ttl));
            SystemSetting::removeKey('gdrive_last_error');

            return $accessToken;
        } catch (Throwable $e) {
            $errorId = ErrorTrackingService::logError($e, 'GoogleDriveService.getAccessToken');
            SystemSetting::setValue('gdrive_last_error', "Token Google Drive bermasalah ({$errorId}): {$e->getMessage()}");

            throw new GoogleDriveException(
                GoogleDriveException::TOKEN_EXPIRED,
                'Koneksi Google Drive perlu dihubungkan ulang oleh Admin di menu Pengaturan Sistem.',
                $e->getMessage(),
                $errorId,
                422,
                $e
            );
        }
    }

    /**
     * Ambil informasi akun & kuota penyimpanan 5 TB.
     */
    public function getDriveInfo(): array
    {
        $token = $this->getAccessToken();

        try {
            return $this->fetchAboutWithToken($token);
        } catch (Throwable $e) {
            $errorId = ErrorTrackingService::logError($e, 'GoogleDriveService.getDriveInfo');
            throw new GoogleDriveException(
                GoogleDriveException::NETWORK_ERROR,
                'Gagal mengambil informasi kuota Google Drive.',
                $e->getMessage(),
                $errorId,
                502,
                $e
            );
        }
    }

    /**
     * Validasi apakah Root Folder ID valid dan tidak berada di Trash.
     */
    public function validateRootFolder(?string $folderId = null): array
    {
        $folderId = trim((string) ($folderId ?? $this->getRootFolderId()));

        if (empty($folderId)) {
            throw new GoogleDriveException(
                GoogleDriveException::ROOT_FOLDER_MISSING,
                'Root Folder Google Drive belum diatur oleh Admin. Silakan atur di Pengaturan Google Drive.'
            );
        }

        $token = $this->getAccessToken();

        try {
            $response = Http::withToken($token)->timeout(15)->get(self::DRIVE_API_BASE . "/files/{$folderId}", [
                'fields' => 'id,name,mimeType,trashed,webViewLink',
            ]);

            $data = $response->json();

            if (!$response->successful() || empty($data['id'])) {
                throw new \RuntimeException($data['error']['message'] ?? 'Folder tidak ditemukan atau tidak dapat diakses.');
            }

            if (!empty($data['trashed'])) {
                throw new \RuntimeException('Folder berada di dalam Trash Google Drive.');
            }

            if (($data['mimeType'] ?? '') !== 'application/vnd.google-apps.folder') {
                throw new \RuntimeException('ID yang dimasukkan bukan merupakan folder Google Drive.');
            }

            return $data;
        } catch (Throwable $e) {
            $errorId = ErrorTrackingService::logError($e, 'GoogleDriveService.validateRootFolder', [
                'folder_id' => $folderId,
            ]);

            throw new GoogleDriveException(
                GoogleDriveException::ROOT_FOLDER_MISSING,
                'Root Folder Google Drive tidak valid atau tidak dapat diakses. Periksa kembali Folder ID di Pengaturan.',
                $e->getMessage(),
                $errorId,
                422,
                $e
            );
        }
    }

    /**
     * Buat Root Folder baru secara otomatis di My Drive akun terhubung.
     */
    public function createRootFolder(string $folderName = 'Arsip Surat FEB UNJ'): array
    {
        $token = $this->getAccessToken();

        try {
            $folder = $this->findOrCreateFolder($token, $folderName, null);
            SystemSetting::setValue('gdrive_root_folder_id', $folder['id']);
            DriveFolder::query()->delete();

            return $folder;
        } catch (Throwable $e) {
            $errorId = ErrorTrackingService::logError($e, 'GoogleDriveService.createRootFolder', [
                'folder_name' => $folderName,
            ]);

            throw new GoogleDriveException(
                GoogleDriveException::FOLDER_FAILED,
                'Gagal membuat folder induk baru di Google Drive.',
                $e->getMessage(),
                $errorId,
                422,
                $e
            );
        }
    }

    /**
     * Pastikan struktur folder {Root}/{Tahun}/{Tipe Surat}[/{Keperluan Surat}] tersedia di Google Drive.
     * Catatan: Tabel drive_folders tetap menyimpan {Tahun}/{Tipe Surat} untuk kompatibilitas Arsip Digital,
     * sedangkan subfolder {Keperluan Surat} di-cache langsung melalui Laravel Cache khusus saat upload ke Google Drive.
     */
    public function ensureFolderHierarchy(int $year, LetterType $letterType, ?LetterPurpose $letterPurpose = null): array
    {
        $rootFolderId = $this->getRootFolderId();
        if (empty($rootFolderId)) {
            throw new GoogleDriveException(
                GoogleDriveException::ROOT_FOLDER_MISSING,
                'Root Folder Google Drive belum diatur oleh Admin. Silakan hubungi Admin atau gunakan Link Manual.'
            );
        }

        $token = $this->getAccessToken();
        $typeLabel = trim(($letterType->code ? "{$letterType->code} - " : '') . $letterType->name);

        try {
            // 1. Pastikan Folder Tipe Surat ({Tahun}/{Tipe Surat}) via cache tabel drive_folders
            $cachedTypeFolder = DriveFolder::where('year', $year)
                ->where('letter_type_id', $letterType->id)
                ->first();

            if ($cachedTypeFolder && $this->folderExists($token, $cachedTypeFolder->folder_id)) {
                $typeFolderId = $cachedTypeFolder->folder_id;
                $typeLabel = $cachedTypeFolder->folder_name ?: $typeLabel;
            } else {
                $cachedYearFolder = DriveFolder::where('year', $year)
                    ->whereNull('letter_type_id')
                    ->first();

                if ($cachedYearFolder && $this->folderExists($token, $cachedYearFolder->folder_id)) {
                    $yearFolderId = $cachedYearFolder->folder_id;
                } else {
                    $yearFolder = $this->findOrCreateFolder($token, (string) $year, $rootFolderId);
                    $yearFolderId = $yearFolder['id'];

                    DriveFolder::updateOrCreate(
                        ['year' => $year, 'letter_type_id' => null],
                        [
                            'folder_id' => $yearFolderId,
                            'folder_name' => (string) $year,
                            'parent_folder_id' => $rootFolderId,
                        ]
                    );
                }

                $typeFolder = $this->findOrCreateFolder($token, $typeLabel, $yearFolderId);
                $typeFolderId = $typeFolder['id'];

                DriveFolder::updateOrCreate(
                    ['year' => $year, 'letter_type_id' => $letterType->id],
                    [
                        'folder_id' => $typeFolderId,
                        'folder_name' => $typeLabel,
                        'parent_folder_id' => $yearFolderId,
                    ]
                );
            }

            // 2. Subfolder Keperluan Surat di Google Drive (di-cache via Laravel Cache, tanpa mengubah DB drive_folders)
            if ($letterPurpose) {
                $cleanPurpose = str_replace(['/', '\\'], '-', (string) $letterPurpose->name);
                $cleanPurpose = preg_replace('/\s+/', ' ', trim($cleanPurpose));
                $purposeLabel = trim(Str::limit($cleanPurpose ?: 'Lainnya', 100, ''));

                $cacheKey = "gdrive_purpose_folder:{$typeFolderId}:" . md5($purposeLabel);
                $cachedPurposeId = Cache::get($cacheKey);

                if (is_string($cachedPurposeId) && $cachedPurposeId !== '' && $this->folderExists($token, $cachedPurposeId)) {
                    $purposeFolderId = $cachedPurposeId;
                } else {
                    $purposeFolder = $this->findOrCreateFolder($token, $purposeLabel, $typeFolderId);
                    $purposeFolderId = $purposeFolder['id'];
                    Cache::put($cacheKey, $purposeFolderId, now()->addHours(12));
                }

                return [
                    'folder_id' => $purposeFolderId,
                    'folder_path' => "{$year} / {$typeLabel} / {$purposeLabel}",
                ];
            }

            return [
                'folder_id' => $typeFolderId,
                'folder_path' => "{$year} / {$typeLabel}",
            ];
        } catch (GoogleDriveException $e) {
            throw $e;
        } catch (Throwable $e) {
            $errorId = ErrorTrackingService::logError($e, 'GoogleDriveService.ensureFolderHierarchy', [
                'year' => $year,
                'letter_type_id' => $letterType->id,
                'letter_purpose_id' => $letterPurpose?->id,
                'root_folder_id' => $rootFolderId,
            ]);

            throw new GoogleDriveException(
                GoogleDriveException::FOLDER_FAILED,
                'Gagal menyiapkan folder arsip otomatis di Google Drive.',
                $e->getMessage(),
                $errorId,
                422,
                $e
            );
        }
    }

    /**
     * Unggah file arsip surat ke Google Drive sesuai Tahun, Tipe Surat, dan Keperluan Surat.
     */
    public function uploadLetterFile(UploadedFile $file, Letter $letter): array
    {
        $letter->loadMissing(['letterType', 'letterPurpose']);

        if (!$letter->letterType) {
            throw new GoogleDriveException(
                GoogleDriveException::FOLDER_FAILED,
                'Jenis surat tidak ditemukan sehingga folder tujuan Google Drive tidak dapat ditentukan.'
            );
        }

        $year = (int) ($letter->year ?: ($letter->letter_date?->year ?? date('Y')));
        $hierarchy = $this->ensureFolderHierarchy($year, $letter->letterType, $letter->letterPurpose);
        $targetFolderId = $hierarchy['folder_id'];
        $standardName = $this->buildStandardFileName($file, $letter);
        $token = $this->getAccessToken();

        try {
            $mimeType = $file->getMimeType() ?: 'application/octet-stream';
            $fileSize = $file->getSize() ?: 0;

            if ($fileSize <= 5 * 1024 * 1024) {
                $uploaded = $this->uploadMultipart($token, $file, $targetFolderId, $standardName, $mimeType);
            } else {
                $uploaded = $this->uploadResumable($token, $file, $targetFolderId, $standardName, $mimeType, $fileSize);
            }

            if ($this->isAutoShareEnabled()) {
                $this->setAnyoneReaderPermission($token, $uploaded['id']);
            }

            $webViewLink = $uploaded['webViewLink']
                ?? "https://drive.google.com/file/d/{$uploaded['id']}/view?usp=sharing";

            return [
                'file_id' => $uploaded['id'],
                'file_name' => $uploaded['name'] ?? $standardName,
                'web_view_link' => $webViewLink,
                'folder_path' => $hierarchy['folder_path'],
                'file_size' => $fileSize,
            ];
        } catch (GoogleDriveException $e) {
            throw $e;
        } catch (Throwable $e) {
            $errorId = ErrorTrackingService::logError($e, 'GoogleDriveService.uploadLetterFile', [
                'letter_id' => $letter->id,
                'letter_number' => $letter->letter_number,
                'target_folder_id' => $targetFolderId,
                'original_name' => $file->getClientOriginalName(),
            ]);

            throw new GoogleDriveException(
                GoogleDriveException::UPLOAD_FAILED,
                'Gagal mengunggah berkas ke Google Drive. Silakan coba kembali beberapa saat lagi.',
                $e->getMessage(),
                $errorId,
                502,
                $e
            );
        }
    }

    /**
     * Pindahkan file lama di Google Drive ke Trash (non-fatal jika file sudah tidak ada).
     */
    public function deleteFile(?string $fileId): bool
    {
        if (empty($fileId)) {
            return true;
        }

        try {
            $token = $this->getAccessToken();
            $response = Http::withToken($token)->timeout(15)->patch(
                self::DRIVE_API_BASE . "/files/{$fileId}",
                ['trashed' => true]
            );

            if ($response->status() === 404) {
                return true;
            }

            if (!$response->successful()) {
                throw new \RuntimeException($response->body());
            }

            return true;
        } catch (Throwable $e) {
            ErrorTrackingService::logError($e, 'GoogleDriveService.deleteFile', [
                'file_id' => $fileId,
            ]);

            return false;
        }
    }

    /**
     * Deteksi MIME type asli dari magic bytes file Word/RTF (termasuk .doc SIPERMAWA yang sering berformat RTF/HTML).
     */
    public function detectWordMimeType(string $realPath, string $extension = ''): ?string
    {
        if ($realPath === '' || !file_exists($realPath)) {
            return null;
        }

        $header = @file_get_contents($realPath, false, null, 0, 128);
        if ($header === false || $header === '') {
            return null;
        }

        if (str_starts_with($header, "PK\x03\x04")) {
            return 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
        }

        if (str_starts_with($header, "\xD0\xCF\x11\xE0")) {
            return 'application/msword';
        }

        $trimmed = ltrim($header, "\xEF\xBB\xBF \t\r\n");
        if (str_starts_with($trimmed, '{\rtf')) {
            return 'application/rtf';
        }

        $lower = strtolower($trimmed);
        foreach (['<html', '<!doctype', '<?xml', '<meta', '<head', '<body', '<div'] as $prefix) {
            if (str_starts_with($lower, $prefix)) {
                return 'text/html';
            }
        }

        return null;
    }

    /**
     * Hapus permanen file sementara di Google Drive (tanpa masuk Trash).
     */
    public function permanentlyDeleteFile(?string $fileId, ?string $token = null): bool
    {
        if (empty($fileId)) {
            return true;
        }

        try {
            $accessToken = $token ?: $this->getAccessToken();
            $response = Http::withToken($accessToken)
                ->connectTimeout(3)
                ->timeout(5)
                ->delete(self::DRIVE_API_BASE . "/files/{$fileId}");

            if ($response->successful() || $response->status() === 404) {
                return true;
            }

            throw new \RuntimeException("HTTP {$response->status()}: " . $response->body());
        } catch (Throwable $e) {
            ErrorTrackingService::logError($e, 'GoogleDriveService.permanentlyDeleteFile', [
                'file_id' => $fileId,
            ]);

            return false;
        }
    }

    /**
     * Bersihkan sisa file sementara yang mungkin gagal dihapus pada konversi sebelumnya (maks. 3 per eksekusi).
     */
    protected function cleanupOrphanConvertFiles(string $token): void
    {
        $orphans = Cache::get(self::ORPHAN_CONVERT_CACHE_KEY, []);
        if (!is_array($orphans) || empty($orphans)) {
            return;
        }

        $remaining = [];
        $processed = 0;

        foreach ($orphans as $fileId) {
            if (!is_string($fileId) || $fileId === '') {
                continue;
            }
            if ($processed >= 3) {
                $remaining[] = $fileId;
                continue;
            }

            $processed++;
            $response = Http::withToken($token)
                ->connectTimeout(2)
                ->timeout(3)
                ->delete(self::DRIVE_API_BASE . "/files/{$fileId}");

            if (!$response->successful() && $response->status() !== 404) {
                $remaining[] = $fileId;
            }
        }

        if (empty($remaining)) {
            Cache::forget(self::ORPHAN_CONVERT_CACHE_KEY);
        } else {
            Cache::put(self::ORPHAN_CONVERT_CACHE_KEY, array_slice(array_values(array_unique($remaining)), 0, 50), now()->addHours(24));
        }
    }

    /**
     * Konversi sementara file Word/RTF (.doc, .docx, .rtf) ke PDF menggunakan Google Drive API,
     * lalu langsung hapus permanen file sementara dari Google Drive.
     */
    public function convertWordToPdf(UploadedFile $file): string
    {
        if (!$this->isConnected()) {
            throw new GoogleDriveException(
                GoogleDriveException::NOT_CONNECTED,
                'Konversi file Word memerlukan koneksi Google Drive aktif. Hubungi Admin atau simpan file Word sebagai PDF terlebih dahulu.'
            );
        }

        $sourceMime = $this->detectWordMimeType(
            (string) $file->getRealPath(),
            strtolower((string) $file->getClientOriginalExtension())
        );

        if (!$sourceMime) {
            throw new GoogleDriveException(
                GoogleDriveException::UPLOAD_FAILED,
                'Format isi file Word tidak valid atau rusak. Pastikan file .doc/.docx/.rtf dapat dibuka di Microsoft Word atau simpan sebagai PDF.'
            );
        }

        $token = $this->getAccessToken();

        try {
            $this->cleanupOrphanConvertFiles($token);
        } catch (Throwable $e) {
            // Non-blocking orphan cleanup
        }

        $tempFileId = null;

        try {
            $boundary = '-------' . Str::random(24);
            $metadata = json_encode([
                'name' => '[TMP_CONVERT]_' . Str::random(12),
                'mimeType' => 'application/vnd.google-apps.document',
            ]);

            $rawContent = file_get_contents((string) $file->getRealPath());
            if ($rawContent === false || $rawContent === '') {
                throw new GoogleDriveException(
                    GoogleDriveException::UPLOAD_FAILED,
                    'File Word kosong atau tidak dapat dibaca.'
                );
            }

            $body = "--{$boundary}\r\n"
                . "Content-Type: application/json; charset=UTF-8\r\n\r\n"
                . "{$metadata}\r\n"
                . "--{$boundary}\r\n"
                . "Content-Type: {$sourceMime}\r\n\r\n"
                . $rawContent . "\r\n"
                . "--{$boundary}--";

            $uploadResp = Http::withToken($token)
                ->connectTimeout(5)
                ->timeout(12)
                ->withBody($body, "multipart/related; boundary={$boundary}")
                ->post(self::DRIVE_UPLOAD_BASE . '/files?uploadType=multipart&fields=id');

            $uploadJson = $uploadResp->json() ?? [];
            if (!$uploadResp->successful() || empty($uploadJson['id'])) {
                $status = $uploadResp->status();
                $errMsg = $uploadJson['error']['message'] ?? $uploadResp->body();

                if ($status === 429 || str_contains(strtolower((string) $errMsg), 'rate limit')) {
                    throw new GoogleDriveException(
                        GoogleDriveException::UPLOAD_FAILED,
                        'Batas permintaan Google Drive tercapai sementara. Tunggu beberapa detik atau simpan file Word sebagai PDF.',
                        (string) $errMsg,
                        null,
                        429
                    );
                }

                throw new \RuntimeException("Upload konversi gagal [{$status}]: {$errMsg}");
            }

            $tempFileId = $uploadJson['id'];

            $exportResp = Http::withToken($token)
                ->connectTimeout(5)
                ->timeout(12)
                ->get(self::DRIVE_API_BASE . "/files/{$tempFileId}/export", [
                    'mimeType' => 'application/pdf',
                ]);

            if (!$exportResp->successful()) {
                $status = $exportResp->status();
                if ($status === 429) {
                    throw new GoogleDriveException(
                        GoogleDriveException::UPLOAD_FAILED,
                        'Batas ekspor Google Drive tercapai sementara. Coba lagi sebentar atau simpan sebagai PDF di Word.',
                        $exportResp->body(),
                        null,
                        429
                    );
                }
                throw new \RuntimeException("Ekspor PDF gagal [{$status}]: " . $exportResp->body());
            }

            $pdfBinary = $exportResp->body();
            if (!str_starts_with($pdfBinary, '%PDF')) {
                throw new \RuntimeException('Respons ekspor Google Drive bukan PDF yang valid.');
            }

            return $pdfBinary;
        } catch (GoogleDriveException $e) {
            throw $e;
        } catch (Throwable $e) {
            $errorId = ErrorTrackingService::logError($e, 'GoogleDriveService.convertWordToPdf', [
                'filename' => $file->getClientOriginalName(),
                'source_mime' => $sourceMime,
            ]);

            throw new GoogleDriveException(
                GoogleDriveException::UPLOAD_FAILED,
                'Gagal mengonversi file Word lewat Google Drive. Pastikan file tidak terproteksi password, atau simpan sebagai PDF di Word lalu unggah PDF-nya.',
                $e->getMessage(),
                $errorId,
                502,
                $e
            );
        } finally {
            if ($tempFileId) {
                $deleted = $this->permanentlyDeleteFile($tempFileId, $token);
                if (!$deleted) {
                    $orphans = Cache::get(self::ORPHAN_CONVERT_CACHE_KEY, []);
                    $orphans = is_array($orphans) ? $orphans : [];
                    $orphans[] = $tempFileId;
                    Cache::put(self::ORPHAN_CONVERT_CACHE_KEY, array_slice(array_values(array_unique($orphans)), 0, 50), now()->addHours(24));
                }
            }
        }
    }

    /**
     * Putuskan koneksi OAuth Google Drive.
     */
    public function disconnect(): void
    {
        Cache::forget(self::TOKEN_CACHE_KEY);
        SystemSetting::removeKey('gdrive_refresh_token');
        SystemSetting::removeKey('gdrive_connected_email');
        SystemSetting::removeKey('gdrive_last_error');
    }

    /**
     * Bersihkan segmen nama file dan pangkas secara rapi di batas kata jika melebihi batas panjang.
     */
    protected function sanitizeFileSegment(?string $value, int $maxLength): string
    {
        $cleaned = preg_replace('/[<>:"\/\\\\|?*\x00-\x1F]+/', '_', (string) $value);
        $cleaned = preg_replace('/\s+/', '_', (string) $cleaned);
        $cleaned = preg_replace('/_+/', '_', (string) $cleaned);
        $cleaned = trim((string) $cleaned, "_- \t\n\r");

        if ($cleaned === '') {
            return '';
        }

        if (mb_strlen($cleaned) <= $maxLength) {
            return $cleaned;
        }

        $cut = mb_substr($cleaned, 0, $maxLength);
        $lastUnderscore = mb_strrpos($cut, '_');
        if ($lastUnderscore !== false && $lastUnderscore >= (int) floor($maxLength * 0.45)) {
            $cut = mb_substr($cut, 0, $lastUnderscore);
        }

        return trim($cut, '_-') ?: mb_substr($cleaned, 0, $maxLength);
    }

    /**
     * Susun nama file standar: {nama_mahasiswa}-{keperluan_surat}-{nomor_surat}.{ext}
     * dengan pembatasan panjang agar aman di Google Drive maupun sistem file lokal.
     */
    public function buildStandardFileName(UploadedFile $file, Letter $letter): string
    {
        $letter->loadMissing('letterPurpose');

        $rawStudent = $letter->student_name ?: $letter->recipient;
        $rawPurpose = $letter->letterPurpose?->name ?: $letter->subject;
        $rawNumber = $letter->letter_number;

        $studentPart = $this->sanitizeFileSegment($rawStudent, 40);
        $purposePart = $this->sanitizeFileSegment($rawPurpose, 40);
        $numberPart = $this->sanitizeFileSegment($rawNumber, 45);

        $join = fn() => collect([$studentPart, $purposePart, $numberPart])
            ->filter(fn($s) => $s !== '')
            ->implode('-');

        $base = $join();
        if (mb_strlen($base) > 120 && $purposePart !== '') {
            $excess = mb_strlen($base) - 120;
            $purposePart = $this->sanitizeFileSegment($purposePart, max(12, mb_strlen($purposePart) - $excess));
            $base = $join();
        }
        if (mb_strlen($base) > 120 && $studentPart !== '') {
            $excess = mb_strlen($base) - 120;
            $studentPart = $this->sanitizeFileSegment($studentPart, max(12, mb_strlen($studentPart) - $excess));
            $base = $join();
        }

        $base = $base !== '' ? $base : 'Surat';
        $ext = strtolower($file->getClientOriginalExtension() ?: 'pdf');

        return "{$base}.{$ext}";
    }

    protected function fetchAboutWithToken(string $token): array
    {
        $response = Http::withToken($token)->timeout(15)->get(self::DRIVE_API_BASE . '/about', [
            'fields' => 'user(displayName,emailAddress),storageQuota(limit,usage,usageInDrive)',
        ]);

        if (!$response->successful()) {
            throw new \RuntimeException($response->body());
        }

        return $response->json() ?? [];
    }

    protected function folderExists(string $token, string $folderId): bool
    {
        $response = Http::withToken($token)->timeout(10)->get(self::DRIVE_API_BASE . "/files/{$folderId}", [
            'fields' => 'id,trashed',
        ]);

        if (!$response->successful()) {
            return false;
        }

        $data = $response->json();
        return !empty($data['id']) && empty($data['trashed']);
    }

    protected function findOrCreateFolder(string $token, string $name, ?string $parentId): array
    {
        $escapedName = str_replace("'", "\\'", $name);
        $q = "mimeType='application/vnd.google-apps.folder' and name='{$escapedName}' and trashed=false";
        $q .= $parentId ? " and '{$parentId}' in parents" : " and 'root' in parents";

        $search = Http::withToken($token)->timeout(15)->get(self::DRIVE_API_BASE . '/files', [
            'q' => $q,
            'fields' => 'files(id,name,webViewLink)',
            'pageSize' => 1,
        ]);

        if ($search->successful()) {
            $existing = $search->json('files.0');
            if (!empty($existing['id'])) {
                return $existing;
            }
        }

        $body = [
            'name' => $name,
            'mimeType' => 'application/vnd.google-apps.folder',
        ];
        if ($parentId) {
            $body['parents'] = [$parentId];
        }

        $create = Http::withToken($token)->timeout(15)->post(self::DRIVE_API_BASE . '/files?fields=id,name,webViewLink', $body);

        if (!$create->successful() || empty($create->json('id'))) {
            throw new \RuntimeException("Gagal membuat folder '{$name}': " . $create->body());
        }

        return $create->json();
    }

    protected function uploadMultipart(
        string $token,
        UploadedFile $file,
        string $folderId,
        string $fileName,
        string $mimeType
    ): array {
        $boundary = '-------' . Str::random(24);
        $metadata = json_encode([
            'name' => $fileName,
            'parents' => [$folderId],
        ]);

        $content = file_get_contents($file->getRealPath());

        $body = "--{$boundary}\r\n"
            . "Content-Type: application/json; charset=UTF-8\r\n\r\n"
            . "{$metadata}\r\n"
            . "--{$boundary}\r\n"
            . "Content-Type: {$mimeType}\r\n\r\n"
            . $content . "\r\n"
            . "--{$boundary}--";

        $response = Http::withToken($token)
            ->timeout(60)
            ->withBody($body, "multipart/related; boundary={$boundary}")
            ->post(self::DRIVE_UPLOAD_BASE . '/files?uploadType=multipart&fields=id,name,webViewLink');

        if (!$response->successful() || empty($response->json('id'))) {
            throw new \RuntimeException("Upload multipart gagal: " . $response->body());
        }

        return $response->json();
    }

    protected function uploadResumable(
        string $token,
        UploadedFile $file,
        string $folderId,
        string $fileName,
        string $mimeType,
        int $fileSize
    ): array {
        $init = Http::withToken($token)
            ->timeout(20)
            ->withHeaders([
                'X-Upload-Content-Type' => $mimeType,
                'X-Upload-Content-Length' => (string) $fileSize,
            ])
            ->post(self::DRIVE_UPLOAD_BASE . '/files?uploadType=resumable&fields=id,name,webViewLink', [
                'name' => $fileName,
                'parents' => [$folderId],
            ]);

        $uploadUrl = $init->header('Location');
        if (!$init->successful() || empty($uploadUrl)) {
            throw new \RuntimeException("Inisiasi resumable upload gagal: " . $init->body());
        }

        $stream = file_get_contents($file->getRealPath());
        $upload = Http::timeout(180)
            ->withHeaders([
                'Content-Length' => (string) $fileSize,
                'Content-Type' => $mimeType,
            ])
            ->withBody($stream, $mimeType)
            ->put($uploadUrl);

        if (!$upload->successful() || empty($upload->json('id'))) {
            throw new \RuntimeException("Upload resumable gagal: " . $upload->body());
        }

        return $upload->json();
    }

    protected function setAnyoneReaderPermission(string $token, string $fileId): void
    {
        $response = Http::withToken($token)->timeout(15)->post(
            self::DRIVE_API_BASE . "/files/{$fileId}/permissions",
            [
                'role' => 'reader',
                'type' => 'anyone',
            ]
        );

        if (!$response->successful()) {
            ErrorTrackingService::logError(
                new \RuntimeException("Gagal set permission anyone-reader pada file {$fileId}: " . $response->body()),
                'GoogleDriveService.setAnyoneReaderPermission',
                ['file_id' => $fileId]
            );
        }
    }
}
