<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Listing extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'marketplace_id',
    ];

    public function marketplace(): BelongsTo
    {
        return $this->belongsTo(Marketplace::class);
    }
}
