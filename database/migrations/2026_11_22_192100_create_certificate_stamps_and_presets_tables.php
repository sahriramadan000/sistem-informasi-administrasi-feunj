<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('certificate_stamps')) {
            Schema::create('certificate_stamps', function (Blueprint $table) {
                $table->id();
                $table->string('name', 150);
                $table->string('code', 50)->nullable();
                $table->string('file_path');
                $table->unsignedTinyInteger('default_opacity')->default(88);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('certificate_presets')) {
            Schema::create('certificate_presets', function (Blueprint $table) {
                $table->id();
                $table->string('name', 150);
                $table->foreignId('letter_type_id')->nullable()->constrained('letter_types')->nullOnDelete();
                $table->json('config');
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('certificate_presets');
        Schema::dropIfExists('certificate_stamps');
    }
};
