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
        Schema::create('daemons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('server_id')->index();
            $table->foreignId('team_id')->index();
            $table->foreignId('creator_id')->index();
            $table->string('command');
            $table->string('directory')->nullable();
            $table->string('user')->default('fuse');
            $table->integer('processes')->default(1);
            $table->integer('stop_wait_seconds')->default(10);
            $table->string('stop_signal')->default('TERM');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daemons');
    }
};
