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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_name')->nullable()->default('System');
            $table->string('action'); // create, update, delete, view
            $table->string('model'); // Letter, User, etc.
            $table->unsignedBigInteger('model_id')->nullable();
            $table->json('data')->nullable(); // Changes made
            $table->string('request_url')->nullable();
            $table->string('request_method')->nullable();
            $table->string('request_ip')->nullable();
            $table->timestamps();

            // Indexes for efficient querying
            $table->index('user_id');
            $table->index('action');
            $table->index('model');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
