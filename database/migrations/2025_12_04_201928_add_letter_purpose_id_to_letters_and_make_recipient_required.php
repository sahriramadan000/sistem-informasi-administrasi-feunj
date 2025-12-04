<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, update any NULL recipients to a default value
        DB::table('letters')
            ->whereNull('recipient')
            ->update(['recipient' => 'Tidak Ditentukan']);
        
        Schema::table('letters', function (Blueprint $table) {
            // Add letter_purpose_id foreign key (nullable for conditional requirement)
            $table->foreignId('letter_purpose_id')->nullable()->after('letter_type_id')->constrained('letter_purposes')->onDelete('restrict');
            
            // Make recipient required (change from nullable to not null)
            $table->string('recipient', 255)->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('letters', function (Blueprint $table) {
            // Drop foreign key and column
            $table->dropForeign(['letter_purpose_id']);
            $table->dropColumn('letter_purpose_id');
            
            // Make recipient nullable again
            $table->string('recipient', 255)->nullable()->change();
        });
    }
};
