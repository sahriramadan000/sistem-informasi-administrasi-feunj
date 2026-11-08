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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'can_access_archives')) {
                $table->boolean('can_access_archives')->default(false);
            }
        });

        // Pengguna yang sudah ada yang memiliki akses persuratan atau admin otomatis diberi akses arsip
        DB::table('users')->where('can_access_letters', true)->orWhere('role', 'admin')->update(['can_access_archives' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'can_access_archives')) {
                $table->dropColumn('can_access_archives');
            }
        });
    }
};
