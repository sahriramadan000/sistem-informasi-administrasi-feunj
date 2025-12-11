<?php

namespace App\Http\Controllers;

use App\Models\Letter;
use App\Models\Signatory;
use App\Models\ClassificationLetter;
use App\Models\LetterType;
use App\Models\LetterPurpose;
use App\Models\UserLetterView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Controller untuk manajemen penerbitan nomor surat
 */
class LetterController extends Controller
{
    /**
     * Inisialisasi middleware
     */
    public function __construct()
    {
        $this->middleware('auth');
        // Semua role bisa melihat index dan show
        // $this->middleware('role:admin,operator,viewer')->only(['index', 'show']);
        // // Hanya admin dan operator yang bisa create, store, edit, update, destroy
        // $this->middleware('role:admin,operator')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    /**
     * Menampilkan daftar surat dengan filter
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Query dasar dengan relasi - hanya surat aktif
        $query = Letter::with(['signatory', 'classification', 'letterType', 'creator', 'viewedByUsers'])->active();

        // Filter berdasarkan parameter request
        if ($request->filled('year')) {
            $query->year($request->year);
        }

        if ($request->filled('signatory_id')) {
            $query->signatory($request->signatory_id);
        }

        if ($request->filled('classification_id')) {
            $query->classification($request->classification_id);
        }

        if ($request->filled('letter_type_id')) {
            $query->letterType($request->letter_type_id);
        }

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Pagination dengan mempertahankan query string
        $letters = $query->orderBy('created_at', 'desc')->paginate(10);

        // TIDAK mark surat sebagai viewed di halaman index
        // Surat hanya di-mark viewed ketika user klik detail (di method show)

        // Data untuk dropdown filter
        $signatories = Signatory::active()->orderBy('name')->get();
        $classifications = ClassificationLetter::active()->orderBy('name')->get();
        $letterTypes = LetterType::active()->orderBy('name')->get();

        return view('letters.index', compact(
            'letters',
            'signatories',
            'classifications',
            'letterTypes'
        ));
    }

    /**
     * Menampilkan form pembuatan surat baru
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Data untuk dropdown
        $signatories = Signatory::active()->orderBy('name')->get();
        $classifications = ClassificationLetter::active()->orderBy('name')->get();
        $letterTypes = LetterType::active()->orderBy('name')->get();
        $letterPurposes = LetterPurpose::active()->orderBy('name')->get();

        return view('letters.create', compact(
            'signatories',
            'classifications',
            'letterTypes',
            'letterPurposes'
        ));
    }

    /**
     * Menyimpan surat baru dan generate nomor surat
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            // Cek apakah jenis surat memerlukan keperluan
            $letterType = LetterType::findOrFail($request->letter_type_id);

            // Validasi input dasar
            $rules = [
                'signatory_id' => 'required|exists:signatories,id',
                'classification_id' => 'required|exists:classification_letters,id',
                'letter_type_id' => 'required|exists:letter_types,id',
                'letter_date' => 'required|date',
                'subject' => 'required|string|max:255',
                'recipient' => 'required|string|max:255',
                'quantity' => 'nullable|integer|min:1|max:50',
            ];

            // Jika jenis surat memerlukan keperluan, tambahkan validasi
            if ($letterType->requires_purpose) {
                $rules['letter_purpose_id'] = 'required|exists:letter_purposes,id';
                $rules['student_name'] = 'required|string|max:255';
            } else {
                $rules['letter_purpose_id'] = 'nullable|exists:letter_purposes,id';
                $rules['student_name'] = 'nullable|string|max:255';
            }

            $validated = $request->validate($rules);

            // Ambil jumlah surat yang akan dibuat (default 1)
            $quantity = $validated['quantity'] ?? 1;
            unset($validated['quantity']); // Hapus quantity dari validated data

            // Generate nomor surat menggunakan transaction untuk mencegah race condition
            $createdLetters = DB::transaction(function () use ($validated, $quantity) {
                // Ambil tahun dari tanggal surat
                $year = date('Y', strtotime($validated['letter_date']));

                // Ambil running number terakhir dengan locking untuk mencegah race condition
                $lastLetter = Letter::where('signatory_id', $validated['signatory_id'])
                    ->where('classification_id', $validated['classification_id'])
                    ->where('year', $year)
                    ->lockForUpdate()
                    ->orderBy('running_number', 'desc')
                    ->first();

                // Hitung running number berikutnya
                $startingRunningNumber = $lastLetter ? $lastLetter->running_number + 1 : 1;

                // Ambil kode penandatangan dan klasifikasi
                $signatory = Signatory::findOrFail($validated['signatory_id']);
                $classification = ClassificationLetter::findOrFail($validated['classification_id']);

                // Array untuk menyimpan surat yang dibuat
                $letters = [];

                // Loop untuk membuat surat sejumlah quantity
                for ($i = 0; $i < $quantity; $i++) {
                    $runningNumber = $startingRunningNumber + $i;
                    $runningNumberFormatted = str_pad($runningNumber, 3, '0', STR_PAD_LEFT);

                    // Generate nomor surat dengan format: [running_3_digit]/[kode_penandatangan]/[kode_klasifikasi]/[tahun]
                    $letterNumber = sprintf(
                        '%s/%s/%s/%d',
                        $runningNumberFormatted,
                        $signatory->code,
                        $classification->code,
                        $year
                    );

                    // Siapkan data surat
                    $letterData = array_merge($validated, [
                        'letter_number' => $letterNumber,
                        'running_number' => $runningNumber,
                        'year' => $year,
                        'status' => 'final',
                        'is_active' => true,
                        'created_by' => auth()->id(),
                    ]);

                    // Simpan surat
                    $letters[] = Letter::create($letterData);
                }

                return $letters;
            });

            Log::info('Letters created successfully', [
                'quantity' => count($createdLetters),
                'letter_numbers' => array_map(fn($l) => $l->letter_number, $createdLetters),
                'user_id' => auth()->id()
            ]);

            // Jika hanya 1 surat, redirect ke detail surat tersebut
            if (count($createdLetters) == 1) {
                return redirect()
                    ->route('letters.show', $createdLetters[0])
                    ->with('success', 'Surat berhasil dibuat dengan nomor: ' . $createdLetters[0]->letter_number);
            }

            // Jika lebih dari 1, redirect ke index dengan pesan sukses
            return redirect()
                ->route('letters.index')
                ->with('success', sprintf(
                    '%d surat berhasil dibuat dengan nomor: %s s/d %s',
                    count($createdLetters),
                    $createdLetters[0]->letter_number,
                    $createdLetters[count($createdLetters) - 1]->letter_number
                ));
        } catch (\Throwable $e) {
            Log::error('Error creating letter', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id()
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan saat membuat surat. Silakan coba lagi.']);
        }
    }

    /**
     * Menampilkan detail surat
     *
     * @param Letter $letter
     * @return \Illuminate\View\View
     */
    public function show(Letter $letter)
    {
        // Load relasi
        $letter->load(['signatory', 'classification', 'letterType', 'letterPurpose', 'creator']);

        // Mark surat sebagai viewed oleh user yang sedang login
        UserLetterView::firstOrCreate([
            'user_id' => auth()->id(),
            'letter_id' => $letter->id,
        ], [
            'viewed_at' => now(),
        ]);

        return view('letters.show', compact('letter'));
    }

    /**
     * Menampilkan form edit surat
     *
     * @param Letter $letter
     * @return \Illuminate\View\View
     */
    public function edit(Letter $letter)
    {
        // Data untuk dropdown
        $signatories = Signatory::active()->orderBy('name')->get();
        $classifications = ClassificationLetter::active()->orderBy('name')->get();
        $letterTypes = LetterType::active()->orderBy('name')->get();
        $letterPurposes = LetterPurpose::active()->orderBy('name')->get();

        return view('letters.edit', compact(
            'letter',
            'signatories',
            'classifications',
            'letterTypes',
            'letterPurposes'
        ));
    }

    /**
     * Update data surat
     *
     * @param Request $request
     * @param Letter $letter
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Letter $letter)
    {
        try {
            // Cek apakah jenis surat memerlukan keperluan
            $letterType = $letter->letterType;

            // Validasi input dasar
            $rules = [
                'letter_date' => 'required|date',
                'subject' => 'required|string|max:255',
                'recipient' => 'required|string|max:255',
            ];

            // Jika jenis surat memerlukan keperluan, tambahkan validasi
            if ($letterType->requires_purpose) {
                $rules['letter_purpose_id'] = 'required|exists:letter_purposes,id';
                $rules['student_name'] = 'required|string|max:255';
            } else {
                $rules['letter_purpose_id'] = 'nullable|exists:letter_purposes,id';
                $rules['student_name'] = 'nullable|string|max:255';
            }

            $validated = $request->validate($rules);

            // Update hanya field yang diizinkan (tidak termasuk nomor surat)
            DB::transaction(function () use ($letter, $validated) {
                $letter->update($validated);
            });

            Log::info('Letter updated successfully', [
                'letter_id' => $letter->id,
                'letter_number' => $letter->letter_number,
                'user_id' => auth()->id()
            ]);

            return redirect()
                ->route('letters.show', $letter)
                ->with('success', 'Surat berhasil diperbarui.');
        } catch (\Throwable $e) {
            Log::error('Error updating letter', [
                'letter_id' => $letter->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id()
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan saat memperbarui surat. Silakan coba lagi.']);
        }
    }

    /**
     * Soft delete surat (set is_active = false)
     *
     * @param Letter $letter
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Letter $letter)
    {
        try {
            // Soft delete dengan set is_active = false
            DB::transaction(function () use ($letter) {
                $letter->update(['is_active' => false]);
            });

            Log::info('Letter deactivated successfully', [
                'letter_id' => $letter->id,
                'letter_number' => $letter->letter_number,
                'user_id' => auth()->id()
            ]);

            return redirect()
                ->route('letters.index')
                ->with('success', 'Surat berhasil dinonaktifkan.');
        } catch (\Throwable $e) {
            Log::error('Error deactivating letter', [
                'letter_id' => $letter->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id()
            ]);

            return back()
                ->withErrors(['error' => 'Terjadi kesalahan saat menonaktifkan surat. Silakan coba lagi.']);
        }
    }
}
