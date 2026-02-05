<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('backup_disks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->index();
            $table->foreignId('creator_id')->index();
            $table->string('name');
            $table->enum('driver', ['s3', 'ftp', 'sftp']);

            $table->string('s3_bucket')->nullable();
            $table->text('s3_access_key')->nullable();
            $table->text('s3_secret_key')->nullable();
            $table->string('s3_region')->nullable();
            $table->boolean('s3_use_path_style_endpoint')->default(false);
            $table->string('s3_custom_endpoint')->nullable();

            $table->string('ftp_host')->nullable();
            $table->string('ftp_username')->nullable();
            $table->text('ftp_password')->nullable();

            $table->string('sftp_host')->nullable();
            $table->string('sftp_username')->nullable();
            $table->text('sftp_password')->nullable();
            $table->boolean('sftp_use_server_key')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('backup_disks');
    }
};
