<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Server extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'public_ip',
        'creator_id',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sshKeys(): BelongsToMany
    {
        return $this->belongsToMany(SshKey::class);
    }

    public function sites(): HasMany
    {
        return $this->hasMany(Site::class);
    }
}
