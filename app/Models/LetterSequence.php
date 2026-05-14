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
        'letter_type_id',
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
     * Relasi ke jenis surat
     */
    public function letterType()
    {
        return $this->belongsTo(LetterType::class, 'letter_type_id');
    }

    /**
     * Ambil nomor urut berikutnya dengan pessimistic locking
     * 
     * Nomor urut dikelola per (letter_type, tahun) - bersifat independent terhadap penanda tangan atau klasifikasi.
     * Mencegah race condition dengan menggunakan database-level lock.
     * Jika ada 2 request bersamaan, request kedua akan menunggu sampai request pertama selesai.
     * 
     * Method ini mendukung bulk creation dengan parameter $quantity.
     * Saat membuat N surat sekaligus, sequence akan langsung di-update sebesar N,
     * bukan 1 per 1 yang akan menyebabkan race condition.
     * 
     * @param int $letterTypeId ID jenis surat
     * @param int $year Tahun surat
     * @param int $quantity Jumlah surat yang akan dibuat (default: 1)
     * @return int Nomor urut berikutnya (mulai dari 1 untuk first record)
     * @throws \Exception Jika terjadi database error
     */
    public static function getNextNumber($letterTypeId, $year, $quantity = 1): int
    {
        return DB::transaction(function () use ($letterTypeId, $year, $quantity) {
            // Kunci baris ini dengan lockForUpdate()
            // Request lain akan menunggu sampai transaksi ini selesai
            $sequence = self::where('letter_type_id', $letterTypeId)
                ->where('year', $year)
                ->lockForUpdate()
                ->first();

            // Jika sequence belum ada, buat baru dengan next_number = $quantity + 1
            // Langsung set ke $quantity + 1 agar request berikutnya tidak tabrakan
            if (!$sequence) {
                self::create([
                    'letter_type_id' => $letterTypeId,
                    'year' => $year,
                    'next_number' => $quantity + 1,  // Return 1, so next should be 1 + $quantity = $quantity + 1
                ]);

                return 1;
            }

            // Ambil nomor saat ini
            $currentNumber = $sequence->next_number;

            // Update next_number untuk request berikutnya
            // Increment sebesar $quantity agar semua nomor dalam batch terlindungi
            $sequence->update(['next_number' => $currentNumber + $quantity]);

            return $currentNumber;
        });
    }

    /**
     * Buat multiple surat dengan sequence yang aman dari race condition
     *
     * Semua operasi dalam 1 transaction dengan pessimistic lock
     * untuk mencegah race condition sepenuhnya. Lock dipertahankan
     * dari ambil nomor urut sampai selesai create semua Letter.
     *
     * @param int $letterTypeId ID jenis surat
     * @param int $year Tahun surat
     * @param array $lettersData Array of letter data to create
     * @return \Illuminate\Support\Collection Created letters
     * @throws \Exception Jika terjadi database error atau constraint violation
     */
    public static function createLettersWithSequence(
        int $letterTypeId,
        int $year,
        array $lettersData
    ): \Illuminate\Support\Collection
    {
        return DB::transaction(function () use ($letterTypeId, $year, $lettersData) {
            // 1. LOCK LetterSequence row untuk letter_type ini
            // Lock ini akan dipertahankan sampai transaction selesai
            $sequence = self::where('letter_type_id', $letterTypeId)
                ->where('year', $year)
                ->lockForUpdate()
                ->first();

            // 2. Jika tidak ada sequence, buat baru
            if (!$sequence) {
                $sequence = self::create([
                    'letter_type_id' => $letterTypeId,
                    'year' => $year,
                    'next_number' => count($lettersData) + 1,
                ]);
                $startingNumber = 1;
            } else {
                // Ambil nomor awal
                $startingNumber = $sequence->next_number;
                // Update untuk request berikutnya
                $sequence->update([
                    'next_number' => $startingNumber + count($lettersData),
                ]);
            }

            // 3. Buat semua Letter dalam loop (masih dalam lock!)
            // Setiap Letter::create() akan trigger model booted() untuk generate letter_number
            $createdLetters = collect();
            foreach ($lettersData as $index => $data) {
                $runningNumber = $startingNumber + $index;
                $data['running_number'] = $runningNumber;

                // Letter::create() dijalankan dalam pessimistic lock
                // Menjamin atomicity complete
                $letter = Letter::create($data);
                $createdLetters->push($letter);
            }

            // 4. Lock release hanya setelah semua Letter::create() berhasil
            // Jika ada error di tengah loop, seluruh transaction akan rollback
            return $createdLetters;
        });
    }

    /**
     * Reset sequence untuk letter type tertentu di tahun tertentu
     * Gunakan hanya untuk testing atau reset manual
     */
    public static function resetForYear($letterTypeId, $year)
    {
        return self::where('letter_type_id', $letterTypeId)
            ->where('year', $year)
            ->update(['next_number' => 1]);
    }
}

