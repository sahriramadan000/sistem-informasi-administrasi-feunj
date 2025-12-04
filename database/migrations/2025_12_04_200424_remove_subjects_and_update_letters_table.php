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
        // Drop foreign key and subject_id column from letters table
        Schema::table('letters', function (Blueprint $table) {
            $table->dropForeign(['subject_id']);
            $table->dropColumn('subject_id');
        });

        // Add subject as string column (required)
        Schema::table('letters', function (Blueprint $table) {
            $table->string('subject')->after('letter_date');
        });

        // Drop subjects table
        Schema::dropIfExists('subjects');

        // Drop requires_subject column from letter_types table
        Schema::table('letter_types', function (Blueprint $table) {
            $table->dropColumn('requires_subject');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate subjects table
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Add requires_subject back to letter_types
        Schema::table('letter_types', function (Blueprint $table) {
            $table->boolean('requires_subject')->default(false)->after('description');
        });

        // Remove subject string column from letters
        Schema::table('letters', function (Blueprint $table) {
            $table->dropColumn('subject');
        });

        // Add subject_id back to letters
        Schema::table('letters', function (Blueprint $table) {
            $table->foreignId('subject_id')->nullable()->after('letter_date')->constrained('subjects')->onDelete('set null');
        });
    }
};
