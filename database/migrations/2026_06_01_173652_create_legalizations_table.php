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
        Schema::create('legalizations', function (Blueprint $table) {
            $table->id();
            $table->integer('running_number');
            $table->integer('year');
            $table->date('date');
            $table->string('alumni_name');
            $table->integer('graduation_year');
            $table->foreignId('education_level_id')->constrained('education_levels')->onDelete('restrict');
            $table->integer('page_count');
            $table->integer('total_price');
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('legalizations');
    }
};
