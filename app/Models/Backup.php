<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Backup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'server_id',
        'team_id',
        'creator_id',
        'backup_disk_id',
        'directories',
        'number_of_backups_to_retain',
        'frequency',
        'custom_cron',
        'notification_on_failure',
        'notification_on_success',
        'notification_email',
    ];

    protected function casts(): array
    {
        return [
            'notification_on_failure' => 'boolean',
            'notification_on_success' => 'boolean',
        ];
    }

    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function backupDisk(): BelongsTo
    {
        return $this->belongsTo(BackupDisk::class);
    }

    public function databases(): BelongsToMany
    {
        return $this->belongsToMany(Database::class, 'backup_database');
    }
}
