<?php

namespace App\Http\Controllers;

use App\Models\Letter;
use App\Models\Signatory;
use App\Models\ClassificationLetter;
use App\Models\LetterType;
use App\Models\LetterPurpose;
use App\Models\UserLetterView;
use App\Models\LetterSequence;
use App\Models\User;
use App\Enums\SecurityClassification;
use App\Enums\LetterTarget;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

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
        try {
            // Query dasar dengan relasi - hanya surat aktif dan status final
            $query = Letter::with(['signatory', 'classification', 'letterType', 'creator', 'viewedByUsers'])
                ->where('status', 'final')
                ->active();

            // Filter berdasarkan parameter request
            if ($request->filled('date_range')) {
                $separator = strpos($request->date_range, ' to ') !== false ? ' to ' : ' - ';
                $dates = explode($separator, $request->date_range);
                if (count($dates) == 2) {
                    $query->whereBetween('letter_date', [trim($dates[0]), trim($dates[1])]);
                } elseif (count($dates) == 1) {
                    $query->whereDate('letter_date', trim($dates[0]));
                }
            }


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

            if ($request->filled('created_by')) {
                $query->where('created_by', $request->created_by);
            }

            // Pagination dengan mempertahankan query string, diurutkan berdasarkan running_number terbesar
            $perPage = $request->input('per_page', 10);
            // Validasi per_page untuk keamanan
            $allowedPerPage = [10, 25, 50, 100];
            if (!in_array($perPage, $allowedPerPage)) {
                $perPage = 10;
            }

            $letters = $query->orderBy('year', 'desc')
                ->orderBy('letter_date', 'desc')
                ->paginate($perPage);

            // TIDAK mark surat sebagai viewed di halaman index
            // Surat hanya di-mark viewed ketika user klik detail (di method show)

            // Data untuk dropdown filter
            $signatories = Signatory::active()->orderBy('name')->get();
            $classifications = ClassificationLetter::active()->orderBy('name')->get();
            $letterTypes = LetterType::active()->orderBy('name')->get();
            // Ambil semua users kecuali username 'admin'
            $users = User::where('username', '!=', 'admin')->orderBy('name')->get();

            return view('letters.index', compact(
                'letters',
                'signatories',
                'classifications',
                'letterTypes',
                'users'
            ));
        } catch (\Throwable $e) {
            return $this->handleError($e, 'LetterController.index', 'Gagal memuat daftar surat.');
        }
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
        $securityClassifications = SecurityClassification::options();
        $letterTargets = LetterTarget::options();
        $letterTypes = LetterType::active()->orderBy('name')->get();
        $letterPurposes = LetterPurpose::active()->orderBy('name')->get();

        return view('letters.create', compact(
            'signatories',
            'classifications',
            'securityClassifications',
            'letterTargets',
            'letterTypes',
            'letterPurposes'
        ));
    }

    /**
     * Menyimpan surat baru dan generate nomor surat
     *
     * Menggunakan LetterSequence::createLettersWithSequence() untuk atomic operation
     * yang mencegah race condition sepenuhnya. Nomor surat akan selalu berurutan
     * tanpa duplikasi bahkan dalam concurrent requests.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            // Cek apakah jenis surat memerlukan keperluan
            $letterType = LetterType::findOrFail($request->letter_type_id);

            // Ambil jumlah surat dan mode
            $quantity = $request->input('quantity', 1);
            $multipleMode = $request->input('multiple_mode', 'same');

            // Validasi input dasar
            $rules = [
                'signatory_id' => 'required|exists:signatories,id',
                'classification_id' => 'required|exists:classification_letters,id',
                'security_classification' => 'required|in:B,T,R,SR',
                'letter_target' => 'required|in:internal,external',
                'letter_type_id' => 'required|exists:letter_types,id',
                'letter_date' => 'required|date',
                'quantity' => 'nullable|integer|min:1|max:50',
                'multiple_mode' => 'nullable|in:same,different',
            ];

            // Validasi subjects dan recipients berdasarkan mode
            if ($quantity > 1 && $multipleMode === 'different') {
                // Mode berbeda: validasi array subjects dan recipients
                $rules['subjects'] = 'required|array|min:' . $quantity;
                $rules['subjects.*'] = 'required|string|max:255';
                $rules['recipients'] = 'required|array|min:' . $quantity;
                $rules['recipients.*'] = 'required|string|max:255';
            } else {
                // Mode sama atau single: validasi subject dan recipient tunggal
                $rules['subject'] = 'required|string|max:255';
                $rules['recipient'] = 'required|string|max:255';
            }

            // Jika jenis surat memerlukan keperluan, tambahkan validasi
            if ($letterType->requires_purpose) {
                $rules['letter_purpose_id'] = 'required|exists:letter_purposes,id';
                $rules['student_name'] = 'required|string|max:255';
            } else {
                $rules['letter_purpose_id'] = 'nullable|exists:letter_purposes,id';
                $rules['student_name'] = 'nullable|string|max:255';
            }

            // Custom validation messages
            $messages = [
                'subjects.required' => 'Perihal untuk setiap surat wajib diisi.',
                'subjects.*.required' => 'Perihal surat ke-:position wajib diisi.',
                'subjects.*.max' => 'Perihal surat ke-:position maksimal 255 karakter.',
                'recipients.required' => 'Tujuan untuk setiap surat wajib diisi.',
                'recipients.*.required' => 'Tujuan surat ke-:position wajib diisi.',
                'recipients.*.max' => 'Tujuan surat ke-:position maksimal 255 karakter.',
                'subject.required' => 'Perihal surat wajib diisi.',
                'recipient.required' => 'Tujuan surat wajib diisi.',
            ];

            // Validasi duplikat nomor surat berdasarkan letter_type
            // Nomor surat boleh sama ASALKAN letter_type berbeda
            $year = date('Y', strtotime($request->letter_date));
            $maxRunningNumber = Letter::where('letter_type_id', $request->letter_type_id)
                ->where('year', $year)
                ->where('is_active', true)
                ->where('status', 'final')
                ->max('running_number') ?? 0;  // null-safe, kalau belum ada = 0

            // Running number berikutnya
            $nextRunningNumber = $maxRunningNumber + 1;
            $duplicateCheck = Letter::where('letter_type_id', $request->letter_type_id)
                ->where('year', $year)
                ->where('running_number', $nextRunningNumber)
                ->where('is_active', true)
                ->first();

            if ($duplicateCheck) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'error' => "Nomor surat dengan running number {$nextRunningNumber} sudah ada untuk jenis surat '{$letterType->name}' tahun {$year}. Silakan hubungi administrator."
                    ]);
            }

            $validated = $request->validate($rules, $messages);

            // Hapus quantity dan multiple_mode dari validated data
            unset($validated['quantity']);
            unset($validated['multiple_mode']);

            // Siapkan data subjects dan recipients
            $subjectsArray = [];
            $recipientsArray = [];

            if ($quantity > 1 && $multipleMode === 'different') {
                // Mode berbeda: gunakan array dari form
                $subjectsArray = $validated['subjects'];
                $recipientsArray = $validated['recipients'];
                unset($validated['subjects']);
                unset($validated['recipients']);
            } else {
                // Mode sama: gunakan subject dan recipient yang sama untuk semua
                for ($i = 0; $i < $quantity; $i++) {
                    $subjectsArray[] = $validated['subject'];
                    $recipientsArray[] = $validated['recipient'];
                }
                unset($validated['subject']);
                unset($validated['recipient']);
            }

            // Ambil tahun dari tanggal surat
            $year = date('Y', strtotime($validated['letter_date']));

            // Siapkan array letter data untuk createLettersWithSequence()
            $lettersData = [];
            for ($i = 0; $i < $quantity; $i++) {
                $letterData = array_merge($validated, [
                    'subject' => $subjectsArray[$i],
                    'recipient' => $recipientsArray[$i],
                    'year' => $year,
                    'status' => 'final',
                    'is_active' => true,
                    'created_by' => auth()->id(),
                    // running_number & letter_number akan di-generate di createLettersWithSequence()
                ]);
                $lettersData[] = $letterData;
            }

            // PERBAIKAN: Gunakan method baru createLettersWithSequence()
            // yang mencegah race condition dengan atomic transaction + pessimistic lock
            $createdLetters = LetterSequence::createLettersWithSequence(
                $validated['letter_type_id'],
                $year,
                $lettersData
            );

            Log::info('Letters created successfully', [
                'quantity' => count($createdLetters),
                'letter_numbers' => $createdLetters->pluck('letter_number')->toArray(),
                'user_id' => auth()->id()
            ]);

            // Log to audit trail
            foreach ($createdLetters as $letter) {
                AuditLogService::log('create', 'Letter', $letter->id, [
                    'letter_number' => $letter->letter_number,
                    'subject' => $letter->subject,
                    'recipient' => $letter->recipient,
                ]);
            }

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
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Database error creating letter', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'user_id' => auth()->id()
            ]);

            // Check untuk unique constraint violation pada letter_number atau running_number
            if ($e->getCode() === '23000' || str_contains($e->getMessage(), 'Duplicate entry') || str_contains($e->getMessage(), 'UNIQUE')) {
                return back()
                    ->withInput()
                    ->withErrors(['error' => 'Nomor surat sudah ada. Silakan coba lagi.']);
            }

            // Check untuk lock timeout
            if (str_contains($e->getMessage(), 'Lock wait timeout exceeded') || $e->getCode() === '1205') {
                return back()
                    ->withInput()
                    ->withErrors(['error' => 'Sistem terlalu sibuk. Silakan coba lagi dalam beberapa detik.']);
            }

            return back()
                ->withInput()
                ->withErrors(['error' => 'Kesalahan database: ' . $e->getMessage()]);
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
        $securityClassifications = SecurityClassification::options();

        return view('letters.edit', compact(
            'letter',
            'signatories',
            'classifications',
            'letterTypes',
            'letterPurposes',
            'securityClassifications'
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
                'letter_target' => 'required|in:internal,external',
                'classification_id' => 'required|exists:classification_letters,id',
                'signatory_id' => 'required|exists:signatories,id',
                'security_classification' => 'required|in:B,T,R,SR',
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

            // Update field dan regenerate nomor surat jika ada komponen nomor surat yang berubah
            $numberComponentChanged = false;
            DB::transaction(function () use ($letter, $validated, &$numberComponentChanged) {
                // Check if components changed
                $numberComponentChanged = (
                    $letter->letter_target !== $validated['letter_target'] ||
                    $letter->classification_id != $validated['classification_id'] ||
                    $letter->signatory_id != $validated['signatory_id'] ||
                    $letter->security_classification !== $validated['security_classification']
                );

                if ($numberComponentChanged) {
                    // Regenerate letter number
                    $letterTarget = LetterTarget::from($validated['letter_target']);
                    $targetCode = $letterTarget->code();
                    
                    $signatory = \App\Models\Signatory::find($validated['signatory_id']);
                    $classification = \App\Models\ClassificationLetter::find($validated['classification_id']);
                    
                    // Mencegah double UN39 jika kode penandatangan sudah memiliki awalan UN39
                    if (str_contains($signatory->code, 'UN39')) {
                        $targetCode = '';
                    }
                    
                    $runningNumberFormatted = str_pad($letter->running_number, 3, '0', STR_PAD_LEFT);

                    // Generate nomor surat baru
                    $newLetterNumber = sprintf(
                        '%s/%s/%s%s/%s/%d',
                        $validated['security_classification'],
                        $runningNumberFormatted,
                        $targetCode,
                        $signatory->code,
                        $classification->code,
                        $letter->year
                    );

                    $validated['letter_number'] = $newLetterNumber;
                }

                $letter->update($validated);
            });

            Log::info('Letter updated successfully', [
                'letter_id' => $letter->id,
                'letter_number' => $letter->letter_number,
                'number_changed' => $numberComponentChanged,
                'user_id' => auth()->id()
            ]);

            // Log to audit trail
            AuditLogService::log('update', 'Letter', $letter->id, [
                'letter_number' => $letter->letter_number,
                'subject' => $letter->subject,
                'changes' => array_keys($validated),
            ]);

            return redirect()
                ->route('letters.show', $letter)
                ->with('success', 'Surat berhasil diperbarui.' . ($numberComponentChanged ? ' Nomor surat telah di-regenerate.' : ''));
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
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

            // Log to audit trail
            AuditLogService::log('delete', 'Letter', $letter->id, [
                'letter_number' => $letter->letter_number,
                'subject' => $letter->subject,
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
