<?php

namespace App\Http\Controllers;

use App\Models\Legalization;
use App\Models\EducationLevel;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\AuditLogService;
use App\Exports\LegalizationReconciliationExport;
use Maatwebsite\Excel\Facades\Excel;

class LegalizationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        try {
            $query = Legalization::with(['educationLevel', 'creator'])->orderBy('running_number', 'desc');

            if ($request->filled('search')) {
                $query->where('alumni_name', 'like', "%{$request->search}%");
            }

            if ($request->filled('date_range')) {
                $separator = strpos($request->date_range, ' to ') !== false ? ' to ' : ' - ';
                $dates = explode($separator, $request->date_range);
                if (count($dates) == 2) {
                    $query->whereBetween('date', [trim($dates[0]), trim($dates[1])]);
                } elseif (count($dates) == 1) {
                    $query->whereDate('date', trim($dates[0]));
                }
            }

            if ($request->filled('education_level_id')) {
                $query->where('education_level_id', $request->education_level_id);
            }

            $perPage = $request->input('per_page', 10);
            $legalizations = $query->paginate($perPage);

            $educationLevels = EducationLevel::orderBy('name')->get();

            return view('legalizations.index', compact('legalizations', 'educationLevels'));
        } catch (\Throwable $e) {
            return $this->handleError($e, 'LegalizationController.index', 'Gagal memuat daftar legalisir.');
        }
    }

    public function create()
    {
        try {
            $educationLevels = EducationLevel::orderBy('name')->get();
            return view('legalizations.create', compact('educationLevels'));
        } catch (\Throwable $e) {
            return $this->handleError($e, 'LegalizationController.create', 'Gagal memuat form legalisir.');
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'alumni_name' => 'required|string|max:150',
            'graduation_year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'education_level_id' => 'required|exists:education_levels,id',
            'page_count' => 'required|integer|min:1',
        ]);

        try {
            $legalization = DB::transaction(function () use ($validated) {
                $educationLevel = EducationLevel::findOrFail($validated['education_level_id']);
                $totalPrice = $educationLevel->price_per_page * $validated['page_count'];

                $date = Carbon::parse($validated['date']);

                return Legalization::create([
                    'date' => $date,
                    'year' => $date->year,
                    'alumni_name' => $validated['alumni_name'],
                    'graduation_year' => $validated['graduation_year'],
                    'education_level_id' => $validated['education_level_id'],
                    'page_count' => $validated['page_count'],
                    'total_price' => $totalPrice,
                    'created_by' => auth()->id(),
                ]);
            });

            Log::info('Legalization created successfully', [
                'legalization_id' => $legalization->id,
                'running_number' => $legalization->running_number,
                'user_id' => auth()->id()
            ]);

            AuditLogService::log('create', 'Legalization', $legalization->id, [
                'alumni_name' => $legalization->alumni_name,
                'graduation_year' => $legalization->graduation_year,
            ]);

            return redirect()->route('legalizations.index')->with('success', 'Data legalisir berhasil ditambahkan.');
        } catch (\Throwable $e) {
            return $this->handleError($e, 'LegalizationController.store', 'Gagal menyimpan data legalisir.');
        }
    }

    public function show(Legalization $legalization)
    {
        try {
            $legalization->load(['educationLevel', 'creator']);
            return view('legalizations.show', compact('legalization'));
        } catch (\Throwable $e) {
            return $this->handleError($e, 'LegalizationController.show', 'Gagal memuat detail legalisir.');
        }
    }

    public function edit(Legalization $legalization)
    {
        try {
            $educationLevels = EducationLevel::orderBy('name')->get();
            return view('legalizations.edit', compact('legalization', 'educationLevels'));
        } catch (\Throwable $e) {
            return $this->handleError($e, 'LegalizationController.edit', 'Gagal memuat form edit legalisir.');
        }
    }

    public function update(Request $request, Legalization $legalization)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'alumni_name' => 'required|string|max:150',
            'graduation_year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'education_level_id' => 'required|exists:education_levels,id',
            'page_count' => 'required|integer|min:1',
        ]);

        try {
            DB::transaction(function () use ($validated, $legalization) {
                $educationLevel = EducationLevel::findOrFail($validated['education_level_id']);
                $totalPrice = $educationLevel->price_per_page * $validated['page_count'];

                $date = Carbon::parse($validated['date']);

                $legalization->update([
                    'date' => $date,
                    'alumni_name' => $validated['alumni_name'],
                    'graduation_year' => $validated['graduation_year'],
                    'education_level_id' => $validated['education_level_id'],
                    'page_count' => $validated['page_count'],
                    'total_price' => $totalPrice,
                    // year and running_number shouldn't change to preserve sequence
                ]);
            });

            Log::info('Legalization updated successfully', [
                'legalization_id' => $legalization->id,
                'user_id' => auth()->id()
            ]);

            AuditLogService::log('update', 'Legalization', $legalization->id, [
                'alumni_name' => $legalization->alumni_name,
                'changes' => array_keys($validated),
            ]);

            return redirect()->route('legalizations.show', $legalization)->with('success', 'Data legalisir berhasil diperbarui.');
        } catch (\Throwable $e) {
            return $this->handleError($e, 'LegalizationController.update', 'Gagal memperbarui data legalisir.');
        }
    }

    public function destroy(Legalization $legalization)
    {
        try {
            DB::transaction(function () use ($legalization) {
                $legalization->delete();
            });

            Log::info('Legalization deactivated successfully', [
                'legalization_id' => $legalization->id,
                'user_id' => auth()->id()
            ]);

            AuditLogService::log('delete', 'Legalization', $legalization->id, [
                'alumni_name' => $legalization->alumni_name,
            ]);

            return redirect()->route('legalizations.index')->with('success', 'Data legalisir berhasil dinonaktifkan.');
        } catch (\Throwable $e) {
            return $this->handleError($e, 'LegalizationController.destroy', 'Gagal menonaktifkan data legalisir.');
        }
    }

    /**
     * Export laporan rekonsiliasi transaksi legalisir ke format Excel (.xlsx)
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\RedirectResponse
     */
    public function export(Request $request)
    {
        try {
            $filters = $request->only(['date_range', 'education_level_id', 'search']);

            // Format label periode & nama file
            $periodLabel = 'Semua Periode';
            $fileSuffix = 'Semua_Periode_' . now()->format('Y-m-d');

            if (!empty($filters['date_range'])) {
                $separator = strpos($filters['date_range'], ' to ') !== false ? ' to ' : ' - ';
                $dates = explode($separator, $filters['date_range']);
                if (count($dates) === 2) {
                    $start = trim($dates[0]);
                    $end = trim($dates[1]);
                    $periodLabel = Carbon::parse($start)->format('d/m/Y') . ' s.d. ' . Carbon::parse($end)->format('d/m/Y');
                    $fileSuffix = $start . '_sd_' . $end;
                } elseif (count($dates) === 1) {
                    $single = trim($dates[0]);
                    $periodLabel = Carbon::parse($single)->format('d/m/Y');
                    $fileSuffix = $single;
                }
            }

            $levelLabel = 'Semua Jenjang';
            if (!empty($filters['education_level_id'])) {
                $level = EducationLevel::withTrashed()->find($filters['education_level_id']);
                if ($level) {
                    $levelLabel = $level->name;
                }
            }

            $exportedBy = auth()->user()->name ?? 'Administrator';
            $fileName = 'Laporan_Rekon_Legalisir_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $fileSuffix) . '.xlsx';

            AuditLogService::log('export', 'Legalization', null, [
                'period' => $periodLabel,
                'education_level' => $levelLabel,
                'search' => $filters['search'] ?? null,
            ]);

            return Excel::download(
                new LegalizationReconciliationExport($filters, $periodLabel, $levelLabel, $exportedBy),
                $fileName
            );
        } catch (\Throwable $e) {
            return $this->handleError($e, 'LegalizationController.export', 'Gagal mengunduh laporan Excel legalisir.');
        }
    }
}
