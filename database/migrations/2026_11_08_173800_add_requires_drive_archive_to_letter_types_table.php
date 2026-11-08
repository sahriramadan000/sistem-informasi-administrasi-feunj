<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('letter_types', function (Blueprint $table) {
            $table->boolean('requires_drive_archive')
                ->default(false)
                ->after('requires_purpose')
                ->comment('Menentukan apakah jenis surat ini memerlukan pengarsipan tautan Google Drive');
        });

        // Set default true untuk jenis surat MOU dan IA
        DB::table('letter_types')
            ->where(function ($query) {
                $query->whereIn('code', ['MOU', 'IA'])
                    ->orWhere('name', 'like', '%MOU%')
                    ->orWhere('name', 'like', '%IA%');
            })
            ->update(['requires_drive_archive' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('letter_types', function (Blueprint $table) {
            $table->dropColumn('requires_drive_archive');
        });
    }
};

