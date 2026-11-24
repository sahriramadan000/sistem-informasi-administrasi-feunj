<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Amankan file TTD digital ke storage/app/private/signatures dan tambahkan whitelist akses per user.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('signatories', 'restrict_signature_access')) {
            Schema::table('signatories', function (Blueprint $table) {
                $table->boolean('restrict_signature_access')
                    ->default(false)
                    ->after('signature_path');
            });
        }

        if (!Schema::hasTable('signatory_user_access')) {
            Schema::create('signatory_user_access', function (Blueprint $table) {
                $table->id();
                $table->foreignId('signatory_id')->constrained('signatories')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['signatory_id', 'user_id']);
            });
        }

        // Pindahkan file TTD lama dari public/uploads/... ke storage/app/private/signatures/
        $privateDir = storage_path('app/private/signatures');
        if (!File::isDirectory($privateDir)) {
            File::makeDirectory($privateDir, 0755, true);
        }

        $signatories = DB::table('signatories')
            ->whereNotNull('signature_path')
            ->where('signature_path', '!=', '')
            ->get();

        foreach ($signatories as $sig) {
            if (str_starts_with($sig->signature_path, 'private/')) {
                continue;
            }

            $oldPublicPath = public_path($sig->signature_path);
            if (File::exists($oldPublicPath)) {
                $filename = basename($sig->signature_path);
                $newPrivatePath = $privateDir . DIRECTORY_SEPARATOR . $filename;
                if (!File::exists($newPrivatePath)) {
                    File::move($oldPublicPath, $newPrivatePath);
                } else {
                    File::delete($oldPublicPath);
                }

                DB::table('signatories')
                    ->where('id', $sig->id)
                    ->update(['signature_path' => 'private/signatures/' . $filename]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('signatory_user_access');

        if (Schema::hasColumn('signatories', 'restrict_signature_access')) {
            Schema::table('signatories', function (Blueprint $table) {
                $table->dropColumn('restrict_signature_access');
            });
        }
    }
};
