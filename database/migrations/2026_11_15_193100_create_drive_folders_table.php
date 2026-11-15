<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drive_folders', function (Blueprint $table) {
            $table->id();
            $table->integer('year')->index();
            $table->foreignId('letter_type_id')->nullable()->constrained('letter_types')->nullOnDelete();
            $table->string('folder_id')->unique();
            $table->string('folder_name');
            $table->string('parent_folder_id')->nullable();
            $table->timestamps();

            $table->unique(['year', 'letter_type_id'], 'drive_folders_year_type_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drive_folders');
    }
};
