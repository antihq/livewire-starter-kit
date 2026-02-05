<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DatabaseUser extends Model
{
    use HasFactory;

    protected $table = 'database_users';

    protected $fillable = [
        'username',
        'password',
        'server_id',
        'team_id',
        'creator_id',
    ];

    protected $hidden = [
        'password',
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

    public function databases(): BelongsToMany
    {
        return $this->belongsToMany(Database::class, 'database_user');
    }

    protected function casts(): array
    {
        return [
            'password' => 'encrypted',
        ];
    }
}
