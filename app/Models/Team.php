<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Jetstream\Events\TeamCreated;
use Laravel\Jetstream\Events\TeamDeleted;
use Laravel\Jetstream\Events\TeamUpdated;
use Laravel\Jetstream\Team as JetstreamTeam;

class Team extends JetstreamTeam
{
    /** @use HasFactory<\Database\Factories\TeamFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'personal_team',
        'public_key',
        'private_key',
        'user_id',
    ];

    /**
     * The event map for the model.
     *
     * @var array<string, class-string>
     */
    protected $dispatchesEvents = [
        'created' => TeamCreated::class,
        'updated' => TeamUpdated::class,
        'deleted' => TeamDeleted::class,
    ];

    protected $hidden = [
        'public_key',
        'private_key',
    ];

    public function sshKeys(): HasMany
    {
        return $this->hasMany(SshKey::class);
    }

    public function servers(): HasMany
    {
        return $this->hasMany(Server::class);
    }

    public function sites(): HasMany
    {
        return $this->hasMany(Site::class);
    }

    public function databases(): HasMany
    {
        return $this->hasMany(Database::class);
    }

    public function databaseUsers(): HasMany
    {
        return $this->hasMany(DatabaseUser::class);
    }

    public function cronjobs(): HasMany
    {
        return $this->hasMany(Cronjob::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'personal_team' => 'boolean',
            'private_key' => 'encrypted',
        ];
    }
}
