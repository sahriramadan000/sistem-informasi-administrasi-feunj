<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('letter_types', 'is_certificate')) {
            Schema::table('letter_types', function (Blueprint $table) {
                $table->boolean('is_certificate')->default(false)->after('requires_drive_archive');
            });
        }

        // Otomatis aktifkan flag sertifikat untuk jenis surat bertipe Sertifikat
        DB::table('letter_types')
            ->where('code', 'SRTF')
            ->orWhere('name', 'like', '%Sertifikat%')
            ->update(['is_certificate' => true]);

        if (!Schema::hasColumn('signatories', 'signature_path')) {
            Schema::table('signatories', function (Blueprint $table) {
                $table->string('signature_path')->nullable()->after('nip');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('letter_types', 'is_certificate')) {
            Schema::table('letter_types', function (Blueprint $table) {
                $table->dropColumn('is_certificate');
            });
        }

        if (Schema::hasColumn('signatories', 'signature_path')) {
            Schema::table('signatories', function (Blueprint $table) {
                $table->dropColumn('signature_path');
            });
        }
    }
};
