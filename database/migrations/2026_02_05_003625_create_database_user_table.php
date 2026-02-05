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
        Schema::create('database_user', function (Blueprint $table) {
            $table->foreignId('database_id')->constrained()->onDelete('cascade');
            $table->foreignId('database_user_id')->constrained('database_users')->onDelete('cascade');
            $table->primary(['database_id', 'database_user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('database_user');
    }
};
