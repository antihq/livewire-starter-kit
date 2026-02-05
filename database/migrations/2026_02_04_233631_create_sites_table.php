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
        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('server_id')->index();
            $table->foreignId('team_id')->index();
            $table->foreignId('creator_id')->index();
            $table->string('hostname');
            $table->string('php_version');
            $table->enum('site_type', ['generic', 'laravel', 'static']);
            $table->boolean('zero_downtime_deployments')->default(false);
            $table->string('web_folder');
            $table->string('repository_url');
            $table->string('repository_branch');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sites');
    }
};
