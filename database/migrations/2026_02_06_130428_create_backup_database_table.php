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
        Schema::create('backup_database', function (Blueprint $table) {
            $table->foreignId('backup_id')->constrained()->onDelete('cascade');
            $table->foreignId('database_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['backup_id', 'database_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backup_database');
    }
};
