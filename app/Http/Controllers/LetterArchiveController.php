<?php

namespace App\Http\Controllers;

use App\Models\Letter;
use App\Models\LetterType;
use App\Models\ClassificationLetter;
use App\Models\Signatory;
use App\Enums\SecurityClassification;
use App\Enums\LetterTarget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Controller untuk Menu Arsip Digital Surat (Google Drive Integration)
 */
class LetterArchiveController extends Controller
{
    /**
     * Inisialisasi middleware
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Tampilan Modul Arsip Digital (Dedicated Read-Only Portal)
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function portal(Request $request)
    {
        $request->merge([
            'portal' => 1,
            'archive_status' => 'archived',
        ]);

        return $this->index($request);
    }

    /**
     * Tampilan utama Arsip Digital
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Cek hak akses ke modul arsip atau persuratan
        if (!$user->isAdmin() && !$user->can_access_archives && !$user->can_access_letters) {
            abort(403, 'Anda tidak memiliki akses ke Modul Arsip Digital.');
        }

        // Tentukan apakah mode portal (read-only) aktif:
        // 1. Lewat route 'archives.*'
        // 2. Parameter ?portal=1
        // 3. User hanya punya izin can_access_archives (tanpa can_access_letters dan bukan admin)
        $isReadOnlyPortal = $request->routeIs('archives.*') 
            || $request->boolean('portal')
            || (!$user->isAdmin() && !$user->can_access_letters && $user->can_access_archives);

        // Jika mode portal read-only, kunci archive_status HANYA ke 'archived'
        if ($isReadOnlyPortal) {
            $archiveStatus = 'archived';
        } else {
            $archiveStatus = $request->input('archive_status', 'archived');
        }

        $baseRouteName = $isReadOnlyPortal ? 'archives.index' : 'letter-archives.index';

        $viewMode = $request->input('view_mode', 'grid');
        if (!in_array($viewMode, ['grid', 'list'])) {
            $viewMode = 'grid';
        }

        // Base Query (Hanya menampilkan jenis surat yang memerlukan pengarsipan Google Drive)
        $query = Letter::with(['signatory', 'classification', 'letterType', 'creator', 'archivedBy'])
            ->where('letters.status', 'final')
            ->active()
            ->archivable();

        // Filter Status Arsip
        if ($archiveStatus === 'archived') {
            $query->archived();
        } elseif ($archiveStatus === 'unarchived') {
            $query->unarchived();
        }
        // Jika 'all', tidak difilter drive_url

        // Filter Tahun
        if ($request->filled('year')) {
            $query->year($request->year);
        }

        // Filter Rentang Tanggal
        if ($request->filled('date_range')) {
            $separator = strpos($request->date_range, ' to ') !== false ? ' to ' : ' - ';
            $dates = explode($separator, $request->date_range);
            if (count($dates) == 2) {
                $query->whereBetween('letter_date', [trim($dates[0]), trim($dates[1])]);
            } elseif (count($dates) == 1) {
                $query->whereDate('letter_date', trim($dates[0]));
            }
        }

        // Filter Jenis Surat
        if ($request->filled('letter_type_id')) {
            $query->letterType($request->letter_type_id);
        }

        // Filter Klasifikasi
        if ($request->filled('classification_id')) {
            $query->classification($request->classification_id);
        }

        // Filter Penandatangan
        if ($request->filled('signatory_id')) {
            $query->signatory($request->signatory_id);
        }

        // Filter Klasifikasi Keamanan (B, T, R, SR)
        if ($request->filled('security_classification')) {
            $query->securityClassification($request->security_classification);
        }

        // Filter Sasaran Surat (internal / external)
        if ($request->filled('letter_target')) {
            $query->letterTarget($request->letter_target);
        }

        // Filter Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Pagination
        $perPage = (int) $request->input('per_page', 18);
        $allowedPerPage = [12, 18, 24, 48, 96];
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 18;
        }

        $letters = $query->orderBy('year', 'desc')
            ->orderBy('letter_date', 'desc')
            ->orderBy('running_number', 'desc')
            ->paginate($perPage);

        // Statistik Keseluruhan (Hanya jenis surat yang wajib/bisa diarsip)
        $baseStats = Letter::where('letters.status', 'final')->active()->archivable();
        $totalAllLetters = (clone $baseStats)->count();
        $totalArchivedLetters = (clone $baseStats)->archived()->count();
        $totalUnarchivedLetters = (clone $baseStats)->unarchived()->count();

        // ── Hierarchical Explorer Navigation (Level 0: Tahun -> Level 1: Jenis Surat -> Level 2: Berkas Dokumen) ──
        $currentYear = $request->input('year');
        $currentLetterTypeId = $request->input('letter_type_id');
        $currentClassificationId = $request->input('classification_id');

        $activeLetterType = $currentLetterTypeId ? LetterType::find($currentLetterTypeId) : null;
        $activeClassification = $currentClassificationId ? ClassificationLetter::find($currentClassificationId) : null;

        // Base query untuk menghitung jumlah berkas per folder
        $subfolderBase = Letter::where('letters.status', 'final')->active()->archivable();
        if ($archiveStatus === 'archived') {
            $subfolderBase->archived();
        } elseif ($archiveStatus === 'unarchived') {
            $subfolderBase->unarchived();
        }

        $folderLevel = 'root'; // 'root' | 'year' | 'type'
        $explorerFolders = collect();
        $parentUrl = null;
        $parentName = null;

        if (!$currentYear) {
            // Level 0 (Root): Tampilkan Folder Tahun
            $folderLevel = 'root';
            $explorerFolders = (clone $subfolderBase)
                ->select('letters.year', DB::raw('count(*) as total'))
                ->groupBy('letters.year')
                ->orderBy('letters.year', 'desc')
                ->get()
                ->map(function ($item) use ($baseRouteName) {
                    return [
                        'type' => 'year',
                        'id' => $item->year,
                        'name' => 'Tahun ' . $item->year,
                        'short_name' => (string) $item->year,
                        'total' => $item->total,
                        'url' => route($baseRouteName, array_merge(request()->except(['page', 'classification_id', 'letter_type_id']), ['year' => $item->year])),
                    ];
                });
        } elseif ($currentYear && !$currentLetterTypeId) {
            // Level 1: Di dalam Tahun, tampilkan Folder Jenis Surat yang memerlukan pengarsipan
            $folderLevel = 'year';
            $parentUrl = route($baseRouteName, request()->except(['year', 'page', 'letter_type_id', 'classification_id']));
            $parentName = 'Semua Tahun';

            $explorerFolders = LetterType::active()
                ->requiresDriveArchive()
                ->whereHas('letters', function ($q) use ($currentYear, $archiveStatus) {
                    $q->where('letters.year', $currentYear)
                        ->where('letters.status', 'final')
                        ->active();
                    if ($archiveStatus === 'archived') {
                        $q->archived();
                    } elseif ($archiveStatus === 'unarchived') {
                        $q->unarchived();
                    }
                })
                ->withCount(['letters as total' => function ($q) use ($currentYear, $archiveStatus) {
                    $q->where('letters.year', $currentYear)
                        ->where('letters.status', 'final')
                        ->active();
                    if ($archiveStatus === 'archived') {
                        $q->archived();
                    } elseif ($archiveStatus === 'unarchived') {
                        $q->unarchived();
                    }
                }])
                ->orderBy('total', 'desc')
                ->get()
                ->map(function ($item) use ($currentYear, $baseRouteName) {
                    return [
                        'type' => 'letter_type',
                        'id' => $item->id,
                        'name' => $item->name,
                        'short_name' => $item->name,
                        'total' => $item->total,
                        'url' => route($baseRouteName, array_merge(request()->except(['page', 'classification_id']), ['year' => $currentYear, 'letter_type_id' => $item->id])),
                    ];
                });
        } else {
            // Level 2: Di dalam Jenis Surat (Level terdalam, langsung menampilkan berkas dokumen)
            $folderLevel = 'type';
            $parentUrl = route($baseRouteName, array_merge(request()->except(['letter_type_id', 'classification_id', 'page']), ['year' => $currentYear]));
            $parentName = 'Tahun ' . $currentYear;
            $explorerFolders = collect();
        }

        // Virtual Year Folders untuk Quick Switcher jika diperlukan
        $yearFolders = (clone $subfolderBase)
            ->select('letters.year', DB::raw('count(*) as total'))
            ->groupBy('letters.year')
            ->orderBy('letters.year', 'desc')
            ->get();

        // Data untuk Filter Dropdown (Hanya jenis surat yang wajib/bisa diarsip)
        $letterTypes = LetterType::active()->requiresDriveArchive()->orderBy('name')->get();
        $classifications = ClassificationLetter::active()->orderBy('name')->get();
        $signatories = Signatory::active()->orderBy('name')->get();
        $securityClassifications = SecurityClassification::options();
        $letterTargets = LetterTarget::options();

        // List semua tahun yang tersedia di database (dari jenis surat yang bisa diarsip)
        $availableYears = Letter::where('letters.status', 'final')
            ->active()
            ->archivable()
            ->select('letters.year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        // Files ditampilkan jika sudah berada di level jenis surat (terdalam),
        // atau jika tidak ada lagi subfolder, atau jika sedang melakukan pencarian / filter spesifik
        $showFiles = ($folderLevel === 'type')
            || $explorerFolders->isEmpty()
            || $request->filled('search')
            || $request->filled('classification_id')
            || $request->filled('signatory_id')
            || $request->filled('security_classification')
            || $request->filled('letter_target');

        return view('letter_archives.index', compact(
            'letters',
            'archiveStatus',
            'viewMode',
            'totalAllLetters',
            'totalArchivedLetters',
            'totalUnarchivedLetters',
            'yearFolders',
            'explorerFolders',
            'folderLevel',
            'showFiles',
            'parentUrl',
            'parentName',
            'activeLetterType',
            'activeClassification',
            'currentYear',
            'letterTypes',
            'classifications',
            'signatories',
            'securityClassifications',
            'letterTargets',
            'availableYears',
            'isReadOnlyPortal',
            'baseRouteName'
        ));
    }
}

