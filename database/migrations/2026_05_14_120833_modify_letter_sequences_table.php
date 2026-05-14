<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('letter_sequences', function (Blueprint $table) {
            // Hapus foreign key constraints yang lama
            $table->dropForeign(['signatory_id']);
            $table->dropForeign(['classification_id']);
            $table->dropIndex(['signatory_id', 'classification_id', 'year']);
            $table->dropUnique(['signatory_id', 'classification_id', 'year']);
            
            // Hapus kolom yang lama
            $table->dropColumn(['signatory_id', 'classification_id']);
            
            // Tambah kolom letter_type_id
            $table->foreignId('letter_type_id')->constrained('letter_types');
            
            // Tambah unique constraint baru: hanya per (letter_type, tahun)
            $table->unique(['letter_type_id', 'year']);
            
            // Tambah index untuk pencarian cepat
            $table->index(['letter_type_id', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('letter_sequences', function (Blueprint $table) {
            // Reverse changes
            $table->dropForeign(['letter_type_id']);
            $table->dropIndex(['letter_type_id', 'year']);
            $table->dropUnique(['letter_type_id', 'year']);
            $table->dropColumn('letter_type_id');
            
            // Restore kolom lama
            $table->foreignId('signatory_id')->constrained('signatories');
            $table->foreignId('classification_id')->constrained('classification_letters');
            $table->unique(['signatory_id', 'classification_id', 'year']);
            $table->index(['signatory_id', 'classification_id', 'year']);
        });
    }
};
