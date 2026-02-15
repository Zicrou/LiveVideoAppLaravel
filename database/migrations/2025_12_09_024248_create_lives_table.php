<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLivesTable extends Migration
{
    public function up()
    {
        Schema::create('lives', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('channel_name')->unique();
            $table->unsignedBigInteger('host_user_id')->nullable();
            $table->string('title')->nullable();
            $table->enum('status', ['scheduled','live','ended'])->default('scheduled');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->string('cdn_push_url')->nullable();
            $table->unsignedInteger('viewers_count')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lives');
    }
}