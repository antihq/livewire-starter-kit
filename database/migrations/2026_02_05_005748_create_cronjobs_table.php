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
        Schema::create('cronjobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('server_id')->index();
            $table->foreignId('team_id')->index();
            $table->foreignId('creator_id')->index();
            $table->string('command');
            $table->string('user')->default('fuse');
            $table->string('frequency')->default('daily');
            $table->string('custom_cron')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cronjobs');
    }
};
