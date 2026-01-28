<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'listing_id',
        'user_id',
        'listing_creator_id',
    ];

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function listingCreator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'listing_creator_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function otherUser(?User $currentUser): ?User
    {
        if ($currentUser === null) {
            return null;
        }

        return $this->user_id === $currentUser->id ? $this->listingCreator : $this->user;
    }
}
