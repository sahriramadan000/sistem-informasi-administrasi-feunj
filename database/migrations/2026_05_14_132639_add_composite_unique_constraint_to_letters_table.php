<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Tambah composite unique constraint pada (letter_type_id, running_number, year)
     * untuk double-check mencegah duplikasi running_number dalam jenis surat yang sama di tahun yang sama
     */
    public function up(): void
    {
        Schema::table('letters', function (Blueprint $table) {
            // Tambah unique constraint untuk (letter_type_id, running_number, year)
            // Menjamin tidak ada 2 surat dengan running_number sama dalam letter_type & year yang sama
            $table->unique(['letter_type_id', 'running_number', 'year'], 'unique_running_number_per_type_year');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('letters', function (Blueprint $table) {
            // Hapus unique constraint
            $table->dropUnique('unique_running_number_per_type_year');
        });
    }
};
