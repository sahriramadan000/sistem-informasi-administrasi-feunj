<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('letters', function (Blueprint $table) {
            $table->string('drive_file_id')->nullable()->after('drive_url')->index();
            $table->string('drive_file_name')->nullable()->after('drive_file_id');
        });
    }

    public function down(): void
    {
        Schema::table('letters', function (Blueprint $table) {
            $table->dropIndex(['drive_file_id']);
            $table->dropColumn(['drive_file_id', 'drive_file_name']);
        });
    }
};
