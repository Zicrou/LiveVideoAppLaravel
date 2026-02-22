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
        Schema::create('likes', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->uuid('owner_id')->nullable();
        $table->foreignUuid('user_id')->references('id')->on('users')->onDelete('cascade');
        $table->foreignUuid(column: 'video_id')->references('id')->on('videos')->onDelete('cascade');
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('likes');
    }
};
