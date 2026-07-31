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
        Schema::table('rooms', function (Blueprint $table) {
            if (!Schema::hasColumn('rooms', 'room_group_id')) {
                $table->foreignId('room_group_id')->nullable()->constrained('room_groups')->nullOnDelete();
            } else {
                $table->foreign('room_group_id')->references('id')->on('room_groups')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropForeign(['room_group_id']);
            $table->dropColumn('room_group_id');
        });
    }
};
