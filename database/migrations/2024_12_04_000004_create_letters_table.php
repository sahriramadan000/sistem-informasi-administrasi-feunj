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
        Schema::create('letters', function (Blueprint $table) {
            $table->id();
            $table->string('letter_number', 255)->unique(); // Nomor surat unik
            $table->integer('running_number'); // Nomor urut
            $table->integer('year'); // Tahun surat
            $table->foreignId('signatory_id')->constrained('signatories');
            $table->foreignId('classification_id')->constrained('classification_letters');
            $table->foreignId('letter_type_id')->constrained('letter_types');
            $table->date('letter_date');
            $table->string('subject', 255);
            $table->string('recipient', 255)->nullable();
            $table->text('body_text')->nullable();
            $table->enum('status', ['draft', 'final', 'cancelled'])->default('final');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            
            // Index untuk performa query dan filter
            $table->index('year');
            $table->index(['signatory_id', 'classification_id', 'year']);
            $table->index('status');
            $table->index('letter_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('letters');
    }
};