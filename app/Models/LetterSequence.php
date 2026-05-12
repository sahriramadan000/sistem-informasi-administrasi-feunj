<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Model LetterSequence untuk manage nomor urut surat
 * Mencegah race condition pada concurrent requests
 */
class LetterSequence extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'signatory_id',
        'classification_id',
        'year',
        'next_number',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'year' => 'integer',
        'next_number' => 'integer',
    ];

    /**
     * Relasi ke penandatangan surat
     */
    public function signatory()
    {
        return $this->belongsTo(Signatory::class, 'signatory_id');
    }

    /**
     * Relasi ke klasifikasi surat
     */
    public function classification()
    {
        return $this->belongsTo(ClassificationLetter::class, 'classification_id');
    }

    /**
     * Ambil nomor urut berikutnya dengan pessimistic locking
     * 
     * Mencegah race condition dengan menggunakan database-level lock.
     * Jika ada 2 request bersamaan, request kedua akan menunggu sampai
     * request pertama selesai.
     * 
     * @param int $signatoryId ID penandatangan
     * @param int $classificationId ID klasifikasi
     * @param int $year Tahun surat
     * @return int Nomor urut berikutnya (mulai dari 1)
     * @throws \Exception Jika terjadi database error
     */
    public static function getNextNumber($signatoryId, $classificationId, $year): int
    {
        return DB::transaction(function () use ($signatoryId, $classificationId, $year) {
            // Kunci baris ini dengan lockForUpdate()
            // Request lain akan menunggu sampai transaksi ini selesai
            $sequence = self::where('signatory_id', $signatoryId)
                ->where('classification_id', $classificationId)
                ->where('year', $year)
                ->lockForUpdate()
                ->first();

            // Jika sequence belum ada, buat baru dengan next_number = 2 (karena kami akan pakai 1)
            if (!$sequence) {
                self::create([
                    'signatory_id' => $signatoryId,
                    'classification_id' => $classificationId,
                    'year' => $year,
                    'next_number' => 2,  // next_number di-set ke 2 karena kita akan return 1
                ]);

                return 1;
            }

            // Ambil nomor saat ini
            $currentNumber = $sequence->next_number;

            // Update next_number untuk request berikutnya
            $sequence->update(['next_number' => $currentNumber + 1]);

            return $currentNumber;
        });
    }

    /**
     * Reset sequence untuk tahun tertentu
     * Gunakan hanya untuk testing atau reset manual
     */
    public static function resetForYear($signatoryId, $classificationId, $year)
    {
        return self::where('signatory_id', $signatoryId)
            ->where('classification_id', $classificationId)
            ->where('year', $year)
            ->update(['next_number' => 1]);
    }
}

