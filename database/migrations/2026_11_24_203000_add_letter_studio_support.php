<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan dukungan Studio Surat (SIPERMAWA) terpisah hak aksesnya dari Studio Sertifikat,
     * serta tabel master Kop Surat (studio_letterheads).
     */
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'can_access_letter_studio')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('can_access_letter_studio')
                    ->default(false)
                    ->after('can_access_certificates');
            });
        }

        if (!Schema::hasColumn('letter_types', 'supports_letter_studio')) {
            Schema::table('letter_types', function (Blueprint $table) {
                $table->boolean('supports_letter_studio')
                    ->default(false)
                    ->after('is_certificate');
            });

            DB::table('letter_types')
                ->whereRaw('LOWER(code) = ?', ['smw'])
                ->orWhereRaw('LOWER(name) LIKE ?', ['%sipermawa%'])
                ->update(['supports_letter_studio' => true]);
        }

        if (!Schema::hasTable('studio_letterheads')) {
            Schema::create('studio_letterheads', function (Blueprint $table) {
                $table->id();
                $table->string('name', 150);
                $table->string('file_path', 255);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('studio_letterheads');

        if (Schema::hasColumn('letter_types', 'supports_letter_studio')) {
            Schema::table('letter_types', function (Blueprint $table) {
                $table->dropColumn('supports_letter_studio');
            });
        }

        if (Schema::hasColumn('users', 'can_access_letter_studio')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('can_access_letter_studio');
            });
        }
    }
};
