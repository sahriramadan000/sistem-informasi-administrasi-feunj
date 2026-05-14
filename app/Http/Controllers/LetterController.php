<?php

namespace App\Http\Controllers;

use App\Models\Letter;
use App\Models\Signatory;
use App\Models\ClassificationLetter;
use App\Models\LetterType;
use App\Models\LetterPurpose;
use App\Models\UserLetterView;
use App\Models\LetterSequence;
use App\Enums\SecurityClassification;
use App\Enums\LetterTarget;
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
             
             // DEBUG: Log quantity from form submission
             $debugQuantityAtSubmit = $request->input('DEBUG_quantity_at_submit');
             Log::info('DEBUG: Quantity tracking', [
                 'quantity_from_request' => $quantity,
                 'DEBUG_quantity_at_submit_from_form' => $debugQuantityAtSubmit,
                 'match' => $quantity === $debugQuantityAtSubmit ? 'SAME' : 'DIFFERENT!',
             ]);

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

            $validated = $request->validate($rules, $messages);

            // DEBUG: Log quantity input
            Log::info('DEBUG: Quantity input', [
                'quantity' => $quantity,
                'multipleMode' => $multipleMode,
                'user_id' => auth()->id()
            ]);

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

            // DEBUG: Log arrays prepared
            Log::info('DEBUG: Arrays prepared', [
                'subjectsArray_count' => count($subjectsArray),
                'recipientsArray_count' => count($recipientsArray),
                'expected_quantity' => $quantity,
                'subjectsArray_first_3' => array_slice($subjectsArray, 0, 3),
                'recipientsArray_first_3' => array_slice($recipientsArray, 0, 3),
            ]);

            // Generate nomor surat menggunakan transaction untuk mencegah race condition
            $createdLetters = DB::transaction(function () use ($validated, $quantity, $subjectsArray, $recipientsArray) {
                // Ambil tahun dari tanggal surat
                $year = date('Y', strtotime($validated['letter_date']));

                // Gunakan LetterSequence untuk mendapatkan running number yang aman
                // Method ini menggunakan pessimistic locking di level database
                // untuk mencegah race condition saat concurrent requests
                // Nomor urut dikelola per (letter_type, tahun) - independent dari signatory/classification
                // Pass $quantity agar sequence di-update sebesar jumlah surat yang dibuat
                $startingRunningNumber = LetterSequence::getNextNumber(
                    $validated['letter_type_id'],
                    $year,
                    $quantity
                );

                // Ambil kode penandatangan dan klasifikasi
                $signatory = Signatory::findOrFail($validated['signatory_id']);
                $classification = ClassificationLetter::findOrFail($validated['classification_id']);
                $securityCode = $validated['security_classification'];
                
                // Ambil kode target (UN39. untuk internal, kosong untuk external)
                $letterTarget = LetterTarget::from($validated['letter_target']);
                $targetCode = $letterTarget->code();

                // Array untuk menyimpan surat yang dibuat
                $letters = [];

                // DEBUG: Log before loop starts
                Log::info('DEBUG: Starting letter creation loop', [
                    'quantity' => $quantity,
                    'startingRunningNumber' => $startingRunningNumber,
                    'subjectsArray_count' => count($subjectsArray),
                    'recipientsArray_count' => count($recipientsArray),
                ]);

                // Loop untuk membuat surat sejumlah quantity
                for ($i = 0; $i < $quantity; $i++) {
                    $runningNumber = $startingRunningNumber + $i;
                    $runningNumberFormatted = str_pad($runningNumber, 3, '0', STR_PAD_LEFT);

                    // Generate nomor surat dengan format: [SEC]/[RUNNING]/[TARGET_CODE][SIGNATORY]/[CLASS]/[YEAR]
                    // Internal: B/001/UN39.DEP-XYT/VAL-ZJ/2026
                    // External: B/001/DEP-XYT/VAL-ZJ/2026
                    $letterNumber = sprintf(
                        '%s/%s/%s%s/%s/%d',
                        $securityCode,
                        $runningNumberFormatted,
                        $targetCode,
                        $signatory->code,
                        $classification->code,
                        $year
                    );

                    // DEBUG: Log before create
                    Log::info('DEBUG: About to create letter', [
                        'iteration' => $i,
                        'runningNumber' => $runningNumber,
                        'letterNumber' => $letterNumber,
                        'subject' => isset($subjectsArray[$i]) ? $subjectsArray[$i] : 'NOT_SET',
                        'recipient' => isset($recipientsArray[$i]) ? $recipientsArray[$i] : 'NOT_SET',
                    ]);

                    // Siapkan data surat dengan subject dan recipient yang sesuai
                    $letterData = array_merge($validated, [
                        'letter_number' => $letterNumber,
                        'running_number' => $runningNumber,
                        'year' => $year,
                        'subject' => $subjectsArray[$i],
                        'recipient' => $recipientsArray[$i],
                        'status' => 'final',
                        'is_active' => true,
                        'created_by' => auth()->id(),
                    ]);

                    try {
                        // Simpan surat
                        $createdLetter = Letter::create($letterData);
                        $letters[] = $createdLetter;
                        
                        // DEBUG: Log after successful create
                        Log::info('DEBUG: Letter created successfully', [
                            'iteration' => $i,
                            'letter_id' => $createdLetter->id,
                            'letter_number' => $createdLetter->letter_number,
                        ]);
                    } catch (\Exception $innerE) {
                        // DEBUG: Log if create fails
                        Log::error('DEBUG: Letter creation failed in loop', [
                            'iteration' => $i,
                            'error' => $innerE->getMessage(),
                            'letterNumber' => $letterNumber,
                        ]);
                        throw $innerE;  // Re-throw untuk rollback transaction
                    }
                }

                // DEBUG: Log after loop completes
                Log::info('DEBUG: Loop completed', [
                    'total_created' => count($letters),
                    'expected_quantity' => $quantity,
                    'match' => count($letters) === $quantity ? 'YES' : 'NO',
                ]);

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
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Database error creating letter', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'user_id' => auth()->id()
            ]);

            // Check untuk unique constraint violation pada letter_number
            if ($e->getCode() === '23000' || str_contains($e->getMessage(), 'Duplicate entry') || str_contains($e->getMessage(), 'UNIQUE')) {
                return back()
                    ->withInput()
                    ->withErrors(['error' => 'Nomor surat sudah terpakai. Silakan coba lagi (kemungkinan race condition).']);
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
                 'letter_target' => 'required|in:internal,external',
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

              // Update field dan regenerate nomor surat jika letter_target berubah
              $targetChanged = false;
              DB::transaction(function () use ($letter, $validated, &$targetChanged) {
                  // Check if letter_target changed
                  $targetChanged = $letter->letter_target !== $validated['letter_target'];

                  if ($targetChanged) {
                      // Regenerate letter number dengan target code yang baru
                      $letterTarget = LetterTarget::from($validated['letter_target']);
                      $targetCode = $letterTarget->code();

                      // Parse existing nomor surat untuk extract bagian-bagiannya
                      $parts = explode('/', $letter->letter_number);
                      // Format: [SEC]/[RUNNING]/[SIGNATORY_OR_UN39.SIGNATORY]/[CLASS]/[YEAR]
                      
                      $securityCode = $parts[0];
                      $runningNumber = $parts[1];
                      $signatory = $letter->signatory;
                      $classification = $letter->classification;
                      $year = $letter->year;

                      // Extract clean signatory code (remove UN39. if exists)
                      $signatoryPart = $parts[2];
                      $cleanSignatoryCode = str_replace('UN39.', '', $signatoryPart);

                      // Generate nomor surat dengan target code yang baru
                      $newLetterNumber = sprintf(
                          '%s/%s/%s%s/%s/%d',
                          $securityCode,
                          $runningNumber,
                          $targetCode,
                          $cleanSignatoryCode,
                          $classification->code,
                          $year
                      );

                      $validated['letter_number'] = $newLetterNumber;
                  }

                  $letter->update($validated);
              });

             Log::info('Letter updated successfully', [
                 'letter_id' => $letter->id,
                 'letter_number' => $letter->letter_number,
                 'target_changed' => $targetChanged,
                 'user_id' => auth()->id()
             ]);

             return redirect()
                 ->route('letters.show', $letter)
                 ->with('success', 'Surat berhasil diperbarui.' . ($targetChanged ? ' Nomor surat telah di-regenerate.' : ''));
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
