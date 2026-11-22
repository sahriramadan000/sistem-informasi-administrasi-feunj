<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'can_access_certificates')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('can_access_certificates')->default(false)->after('can_access_room_reservations');
            });

            DB::table('users')->where('role', 'admin')->update(['can_access_certificates' => true]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'can_access_certificates')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('can_access_certificates');
            });
        }
    }
};
