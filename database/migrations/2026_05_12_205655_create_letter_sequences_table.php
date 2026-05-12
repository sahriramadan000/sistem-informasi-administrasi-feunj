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
        Schema::create('letter_sequences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('signatory_id')->constrained('signatories');
            $table->foreignId('classification_id')->constrained('classification_letters');
            $table->integer('year'); // Tahun surat
            $table->integer('next_number')->default(1); // Nomor urut berikutnya
            $table->timestamps();
            
            // Unique constraint: hanya boleh 1 sequence per (signatory, klasifikasi, tahun)
            $table->unique(['signatory_id', 'classification_id', 'year']);
            
            // Index untuk pencarian cepat
            $table->index(['signatory_id', 'classification_id', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('letter_sequences');
    }
};
