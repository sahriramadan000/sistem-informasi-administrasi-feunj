<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LegalizationSequence extends Model
{
    use HasFactory;

    protected $fillable = [
        'year',
        'next_number',
    ];

    protected $casts = [
        'year' => 'integer',
        'next_number' => 'integer',
    ];

    public static function getNextNumber($year): int
    {
        return DB::transaction(function () use ($year) {
            $sequence = self::where('year', $year)->lockForUpdate()->first();

            if (!$sequence) {
                self::create([
                    'year' => $year,
                    'next_number' => 2,
                ]);

                return 1;
            }

            $currentNumber = $sequence->next_number;
            $sequence->update(['next_number' => $currentNumber + 1]);

            return $currentNumber;
        });
    }

    /**
     * Buat multiple legalisir dengan sequence yang aman dari race condition
     *
     * @param int $year Tahun legalisir
     * @param array $legalizationsData Array of legalization data to create
     * @return \Illuminate\Support\Collection Created legalizations
     * @throws \Exception Jika terjadi database error
     */
    public static function createLegalizationsWithSequence(
        int $year,
        array $legalizationsData
    ): \Illuminate\Support\Collection
    {
        return DB::transaction(function () use ($year, $legalizationsData) {
            // 1. LOCK LegalizationSequence row untuk tahun ini
            $sequence = self::where('year', $year)
                ->lockForUpdate()
                ->first();

            // 2. Jika tidak ada sequence, buat baru
            if (!$sequence) {
                $sequence = self::create([
                    'year' => $year,
                    'next_number' => count($legalizationsData) + 1,
                ]);
                $startingNumber = 1;
            } else {
                // Ambil nomor awal
                $startingNumber = $sequence->next_number;
                // Update untuk request berikutnya
                $sequence->update([
                    'next_number' => $startingNumber + count($legalizationsData),
                ]);
            }

            // 3. Buat semua Legalization dalam loop
            $createdLegalizations = collect();
            foreach ($legalizationsData as $index => $data) {
                $runningNumber = $startingNumber + $index;
                $data['running_number'] = $runningNumber;

                // Legalization::create() dijalankan dalam pessimistic lock
                $legalization = Legalization::create($data);
                $createdLegalizations->push($legalization);
            }

            return $createdLegalizations;
        });
    }
}
