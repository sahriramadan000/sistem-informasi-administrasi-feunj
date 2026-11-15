<?php

namespace App\Services;

use App\Exceptions\GoogleDriveException;
use App\Models\DriveFolder;
use App\Models\Letter;
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
     * Pastikan struktur folder {Root}/{Tahun}/{Tipe Surat} tersedia di Google Drive.
     */
    public function ensureFolderHierarchy(int $year, LetterType $letterType): array
    {
        $rootFolderId = $this->getRootFolderId();
        if (empty($rootFolderId)) {
            throw new GoogleDriveException(
                GoogleDriveException::ROOT_FOLDER_MISSING,
                'Root Folder Google Drive belum diatur oleh Admin. Silakan hubungi Admin atau gunakan Link Manual.'
            );
        }

        $token = $this->getAccessToken();

        // Cek cache database untuk folder Tipe Surat pada Tahun terkait
        $cachedTypeFolder = DriveFolder::where('year', $year)
            ->where('letter_type_id', $letterType->id)
            ->first();

        if ($cachedTypeFolder && $this->folderExists($token, $cachedTypeFolder->folder_id)) {
            return [
                'folder_id' => $cachedTypeFolder->folder_id,
                'folder_path' => "{$year} / {$cachedTypeFolder->folder_name}",
            ];
        }

        try {
            // 1. Folder Tahun
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

            // 2. Folder Tipe Surat (misal: "SK - Surat Keputusan")
            $typeLabel = trim(($letterType->code ? "{$letterType->code} - " : '') . $letterType->name);
            $typeFolder = $this->findOrCreateFolder($token, $typeLabel, $yearFolderId);

            DriveFolder::updateOrCreate(
                ['year' => $year, 'letter_type_id' => $letterType->id],
                [
                    'folder_id' => $typeFolder['id'],
                    'folder_name' => $typeLabel,
                    'parent_folder_id' => $yearFolderId,
                ]
            );

            return [
                'folder_id' => $typeFolder['id'],
                'folder_path' => "{$year} / {$typeLabel}",
            ];
        } catch (GoogleDriveException $e) {
            throw $e;
        } catch (Throwable $e) {
            $errorId = ErrorTrackingService::logError($e, 'GoogleDriveService.ensureFolderHierarchy', [
                'year' => $year,
                'letter_type_id' => $letterType->id,
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
     * Unggah file arsip surat ke Google Drive sesuai Tahun & Tipe Surat.
     */
    public function uploadLetterFile(UploadedFile $file, Letter $letter): array
    {
        $letter->loadMissing('letterType');

        if (!$letter->letterType) {
            throw new GoogleDriveException(
                GoogleDriveException::FOLDER_FAILED,
                'Jenis surat tidak ditemukan sehingga folder tujuan Google Drive tidak dapat ditentukan.'
            );
        }

        $year = (int) ($letter->year ?: ($letter->letter_date?->year ?? date('Y')));
        $hierarchy = $this->ensureFolderHierarchy($year, $letter->letterType);
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
     * Susun nama file standar: {YYYY-MM-DD}_{NomorSurat}_{SlugPerihal}.{ext}
     */
    public function buildStandardFileName(UploadedFile $file, Letter $letter): string
    {
        $datePart = $letter->letter_date ? $letter->letter_date->format('Y-m-d') : date('Y-m-d');
        $numberPart = preg_replace('/[^A-Za-z0-9\-]+/', '-', (string) $letter->letter_number);
        $numberPart = trim((string) $numberPart, '-') ?: 'SURAT';
        $subjectPart = Str::slug(Str::limit((string) $letter->subject, 60, ''));
        $ext = strtolower($file->getClientOriginalExtension() ?: 'pdf');

        return collect([$datePart, $numberPart, $subjectPart])
            ->filter()
            ->implode('_') . ".{$ext}";
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
