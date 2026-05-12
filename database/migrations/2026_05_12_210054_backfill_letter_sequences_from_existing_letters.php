<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Backfill tabel letter_sequences dari data surat yang sudah ada.
     * Untuk setiap kombinasi (signatory_id, classification_id, year),
     * catat running_number tertinggi + 1 sebagai next_number.
     */
    public function up(): void
    {
        // Group surat berdasarkan signatory, classification, dan year
        // Ambil running_number maksimal untuk setiap grup
        $sequences = DB::table('letters')
            ->select('signatory_id', 'classification_id', 'year')
            ->selectRaw('MAX(running_number) as max_running_number')
            ->groupBy('signatory_id', 'classification_id', 'year')
            ->get();

        foreach ($sequences as $seq) {
            // Insert ke letter_sequences dengan next_number = max_running_number + 1
            DB::table('letter_sequences')->insert([
                'signatory_id' => $seq->signatory_id,
                'classification_id' => $seq->classification_id,
                'year' => $seq->year,
                'next_number' => $seq->max_running_number + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus semua data yang di-insert oleh migration ini
        // Kita tidak bisa tahu mana yang di-insert oleh migration ini,
        // jadi kita hapus semua letter_sequences (karena baru dibuat)
        DB::table('letter_sequences')->truncate();
    }
};

