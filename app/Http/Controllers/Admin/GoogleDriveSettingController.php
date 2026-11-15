<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\GoogleDriveException;
use App\Http\Controllers\Controller;
use App\Models\DriveFolder;
use App\Models\SystemSetting;
use App\Services\AuditLogService;
use App\Services\ErrorTrackingService;
use App\Services\GoogleDriveService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Throwable;

class GoogleDriveSettingController extends Controller
{
    public function __construct(protected GoogleDriveService $driveService)
    {
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Halaman Pengaturan Integrasi Google Drive.
     */
    public function index()
    {
        $isConfigured = $this->driveService->isConfigured();
        $isConnected = $this->driveService->isConnected();
        $connectedEmail = $this->driveService->getConnectedEmail();
        $rootFolderId = $this->driveService->getRootFolderId();
        $autoShare = $this->driveService->isAutoShareEnabled();
        $lastError = $this->driveService->getLastError();
        $redirectUri = config('services.google_drive.redirect_uri');

        $driveInfo = null;
        $rootFolderInfo = null;

        if ($isConnected) {
            try {
                $driveInfo = $this->driveService->getDriveInfo();
                if ($rootFolderId) {
                    $rootFolderInfo = $this->driveService->validateRootFolder($rootFolderId);
                }
            } catch (GoogleDriveException $e) {
                $lastError = $e->getFormattedUserMessage();
            } catch (Throwable $e) {
                $errorId = ErrorTrackingService::logError($e, 'GoogleDriveSettingController.index');
                $lastError = "Gagal memeriksa status Google Drive (Kode Error: {$errorId}).";
            }
        }

        $cachedFoldersCount = DriveFolder::count();

        return view('admin.gdrive.index', compact(
            'isConfigured',
            'isConnected',
            'connectedEmail',
            'rootFolderId',
            'autoShare',
            'lastError',
            'redirectUri',
            'driveInfo',
            'rootFolderInfo',
            'cachedFoldersCount'
        ));
    }

    /**
     * Redirect Admin ke halaman otorisasi OAuth 2.0 Google.
     */
    public function connect()
    {
        try {
            $state = Str::random(40);
            session(['gdrive_oauth_state' => $state]);

            return redirect()->away($this->driveService->getAuthUrl($state));
        } catch (GoogleDriveException $e) {
            return redirect()->route('admin.gdrive.index')
                ->with('error', $e->getFormattedUserMessage());
        } catch (Throwable $e) {
            $errorId = ErrorTrackingService::logError($e, 'GoogleDriveSettingController.connect');
            return redirect()->route('admin.gdrive.index')
                ->with('error', "Gagal memulai otorisasi Google Drive (Kode Error: {$errorId}).");
        }
    }

    /**
     * Callback OAuth 2.0 dari Google.
     */
    public function callback(Request $request)
    {
        if ($request->filled('error')) {
            $error = $request->input('error');
            $errorId = ErrorTrackingService::logError(
                new \RuntimeException("Google OAuth dibatalkan/ditolak: {$error}"),
                'GoogleDriveSettingController.callback'
            );

            return redirect()->route('admin.gdrive.index')
                ->with('error', "Otorisasi Google dibatalkan atau ditolak ({$error}). Kode Error: {$errorId}");
        }

        $expectedState = session()->pull('gdrive_oauth_state');
        if (empty($expectedState) || !hash_equals($expectedState, (string) $request->input('state'))) {
            $errorId = ErrorTrackingService::logError(
                new \RuntimeException('Invalid OAuth state parameter pada Google Drive callback.'),
                'GoogleDriveSettingController.callback'
            );

            return redirect()->route('admin.gdrive.index')
                ->with('error', "Sesi otorisasi tidak valid atau kedaluwarsa. Silakan coba lagi (Kode Error: {$errorId}).");
        }

        $code = (string) $request->input('code');
        if (empty($code)) {
            return redirect()->route('admin.gdrive.index')
                ->with('error', 'Kode otorisasi dari Google tidak ditemukan.');
        }

        try {
            $result = $this->driveService->handleCallback($code);

            AuditLogService::log('update', 'SystemSetting', null, [
                'action' => 'gdrive_connected',
                'connected_email' => $result['email'] ?? null,
            ]);

            $emailLabel = $result['email'] ? " ({$result['email']})" : '';
            return redirect()->route('admin.gdrive.index')
                ->with('success', "Akun Google Drive{$emailLabel} berhasil dihubungkan!");
        } catch (GoogleDriveException $e) {
            return redirect()->route('admin.gdrive.index')
                ->with('error', $e->getFormattedUserMessage());
        } catch (Throwable $e) {
            $errorId = ErrorTrackingService::logError($e, 'GoogleDriveSettingController.callback');
            return redirect()->route('admin.gdrive.index')
                ->with('error', "Terjadi kesalahan saat menghubungkan Google Drive (Kode Error: {$errorId}).");
        }
    }

    /**
     * Simpan pengaturan Root Folder ID & Auto-Share.
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'gdrive_root_folder_id' => 'nullable|string|max:500',
            'gdrive_auto_share' => 'nullable|boolean',
        ]);

        try {
            $rawFolder = trim((string) $request->input('gdrive_root_folder_id', ''));
            $folderId = $this->extractFolderId($rawFolder);
            $oldFolderId = $this->driveService->getRootFolderId();

            if (!empty($folderId) && $this->driveService->isConnected()) {
                $folderInfo = $this->driveService->validateRootFolder($folderId);
                SystemSetting::setValue('gdrive_root_folder_id', $folderInfo['id']);
            } else {
                SystemSetting::setValue('gdrive_root_folder_id', $folderId ?: null);
            }

            if ($oldFolderId !== $folderId) {
                DriveFolder::query()->delete();
            }

            $autoShare = $request->boolean('gdrive_auto_share');
            SystemSetting::setValue('gdrive_auto_share', $autoShare ? '1' : '0');

            AuditLogService::log('update', 'SystemSetting', null, [
                'action' => 'gdrive_settings_updated',
                'root_folder_id' => $folderId,
                'auto_share' => $autoShare,
            ]);

            return redirect()->route('admin.gdrive.index')
                ->with('success', 'Pengaturan Google Drive berhasil disimpan dan diverifikasi.');
        } catch (GoogleDriveException $e) {
            return redirect()->route('admin.gdrive.index')
                ->withInput()
                ->with('error', $e->getFormattedUserMessage());
        } catch (Throwable $e) {
            $errorId = ErrorTrackingService::logError($e, 'GoogleDriveSettingController.updateSettings');
            return redirect()->route('admin.gdrive.index')
                ->with('error', "Gagal menyimpan pengaturan Google Drive (Kode Error: {$errorId}).");
        }
    }

    /**
     * Buat folder induk baru otomatis di Google Drive.
     */
    public function createRootFolder(Request $request)
    {
        $request->validate([
            'folder_name' => 'nullable|string|max:120',
        ]);

        $folderName = trim((string) $request->input('folder_name', 'Arsip Surat FEB UNJ')) ?: 'Arsip Surat FEB UNJ';

        try {
            $folder = $this->driveService->createRootFolder($folderName);

            AuditLogService::log('create', 'SystemSetting', null, [
                'action' => 'gdrive_root_folder_created',
                'folder_id' => $folder['id'],
                'folder_name' => $folder['name'] ?? $folderName,
            ]);

            return redirect()->route('admin.gdrive.index')
                ->with('success', "Folder induk \"{$folderName}\" berhasil dibuat di Google Drive dan langsung diterapkan.");
        } catch (GoogleDriveException $e) {
            return redirect()->route('admin.gdrive.index')
                ->with('error', $e->getFormattedUserMessage());
        } catch (Throwable $e) {
            $errorId = ErrorTrackingService::logError($e, 'GoogleDriveSettingController.createRootFolder');
            return redirect()->route('admin.gdrive.index')
                ->with('error', "Gagal membuat folder induk (Kode Error: {$errorId}).");
        }
    }

    /**
     * Uji koneksi Google Drive dan akses Root Folder.
     */
    public function testConnection()
    {
        try {
            $info = $this->driveService->getDriveInfo();
            $email = $info['user']['emailAddress'] ?? 'Tidak diketahui';

            $rootId = $this->driveService->getRootFolderId();
            $folderMsg = 'Root Folder belum diatur.';
            if ($rootId) {
                $folder = $this->driveService->validateRootFolder($rootId);
                $folderMsg = "Root Folder \"{$folder['name']}\" dapat diakses.";
            }

            return redirect()->route('admin.gdrive.index')
                ->with('success', "Koneksi berhasil! Terhubung ke {$email}. {$folderMsg}");
        } catch (GoogleDriveException $e) {
            return redirect()->route('admin.gdrive.index')
                ->with('error', $e->getFormattedUserMessage());
        } catch (Throwable $e) {
            $errorId = ErrorTrackingService::logError($e, 'GoogleDriveSettingController.testConnection');
            return redirect()->route('admin.gdrive.index')
                ->with('error', "Uji koneksi gagal (Kode Error: {$errorId}).");
        }
    }

    /**
     * Putuskan koneksi Google Drive.
     */
    public function disconnect()
    {
        try {
            $email = $this->driveService->getConnectedEmail();
            $this->driveService->disconnect();

            AuditLogService::log('delete', 'SystemSetting', null, [
                'action' => 'gdrive_disconnected',
                'previous_email' => $email,
            ]);

            return redirect()->route('admin.gdrive.index')
                ->with('success', 'Koneksi Google Drive berhasil diputuskan.');
        } catch (Throwable $e) {
            $errorId = ErrorTrackingService::logError($e, 'GoogleDriveSettingController.disconnect');
            return redirect()->route('admin.gdrive.index')
                ->with('error', "Gagal memutuskan koneksi Google Drive (Kode Error: {$errorId}).");
        }
    }

    /**
     * Ekstrak Folder ID jika admin menempelkan URL penuh Google Drive folder.
     */
    protected function extractFolderId(string $input): string
    {
        if (empty($input)) {
            return '';
        }

        if (preg_match('#/folders/([a-zA-Z0-9_-]+)#', $input, $matches)) {
            return $matches[1];
        }

        if (preg_match('#[?&]id=([a-zA-Z0-9_-]+)#', $input, $matches)) {
            return $matches[1];
        }

        return $input;
    }
}
