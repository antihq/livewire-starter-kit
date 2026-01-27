<?php

namespace App\Models;

use App\Events\MarketplaceCreated;
use App\Events\MarketplaceDeleted;
use App\Events\MarketplaceUpdated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Marketplace extends Model
{
    /** @use HasFactory<\Database\Factories\MarketplaceFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'team_id',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
