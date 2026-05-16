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
            // Drop the global unique constraint on letter_number
            $table->dropUnique(['letter_number']);
            
            // Add composite unique constraint: letter_number must be unique per letter_type
            $table->unique(
                ['letter_type_id', 'letter_number'], 
                'unique_letter_number_per_type'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('letters', function (Blueprint $table) {
            // Drop composite unique constraint
            $table->dropUnique('unique_letter_number_per_type');
            
            // Restore global unique constraint
            $table->unique('letter_number');
        });
    }
};
