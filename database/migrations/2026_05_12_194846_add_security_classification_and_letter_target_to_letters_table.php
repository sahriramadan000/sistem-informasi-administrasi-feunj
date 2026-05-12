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
        Schema::table('letters', function (Blueprint $table) {
            $table->enum('security_classification', ['B', 'T', 'R', 'SR'])
                ->nullable()
                ->after('letter_type_id')
                ->comment('B=Biasa, T=Terbatas, R=Rahasia, SR=Sangat Rahasia');

            $table->enum('letter_target', ['internal', 'external'])
                ->nullable()
                ->after('security_classification')
                ->comment('Internal or External');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('letters', function (Blueprint $table) {
            $table->dropColumn(['security_classification', 'letter_target']);
        });
    }
};
