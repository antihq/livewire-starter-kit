<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Site extends Model
{
    use HasFactory;

    protected $fillable = [
        'hostname',
        'server_id',
        'team_id',
        'php_version',
        'site_type',
        'zero_downtime_deployments',
        'web_folder',
        'repository_url',
        'repository_branch',
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

    protected function casts(): array
    {
        return [
            'zero_downtime_deployments' => 'boolean',
        ];
    }
}
