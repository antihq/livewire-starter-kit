<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Database extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'server_id',
        'team_id',
        'creator_id',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function databaseUsers(): BelongsToMany
    {
        return $this->belongsToMany(DatabaseUser::class, 'database_user');
    }

    public function backups(): BelongsToMany
    {
        return $this->belongsToMany(Backup::class, 'backup_database');
    }
}
