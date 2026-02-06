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
        Schema::create('backups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('server_id')->constrained()->onDelete('cascade');
            $table->foreignId('team_id')->constrained()->onDelete('cascade');
            $table->foreignId('creator_id')->index();
            $table->foreignId('backup_disk_id')->nullable()->constrained()->onDelete('set null');
            $table->string('name');
            $table->text('directories')->nullable();
            $table->integer('number_of_backups_to_retain');
            $table->string('frequency');
            $table->string('custom_cron')->nullable();
            $table->boolean('notification_on_failure')->default(false);
            $table->boolean('notification_on_success')->default(false);
            $table->string('notification_email')->nullable();
            $table->timestamps();

            $table->index(['team_id', 'server_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backups');
    }
};
