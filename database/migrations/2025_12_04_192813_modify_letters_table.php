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
        Schema::table('letters', function (Blueprint $table) {
            // Hapus kolom body_text
            $table->dropColumn('body_text');
            
            // Ubah subject menjadi subject_id dan nullable
            $table->dropColumn('subject');
            $table->foreignId('subject_id')->nullable()->after('letter_date')->constrained('subjects');
            
            // Tambah kolom student_name (nullable/optional)
            $table->string('student_name', 255)->nullable()->after('recipient');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('letters', function (Blueprint $table) {
            // Kembalikan kolom body_text
            $table->text('body_text')->nullable();
            
            // Kembalikan kolom subject
            $table->dropForeign(['subject_id']);
            $table->dropColumn('subject_id');
            $table->string('subject', 255)->after('letter_date');
            
            // Hapus kolom student_name
            $table->dropColumn('student_name');
        });
    }
};
