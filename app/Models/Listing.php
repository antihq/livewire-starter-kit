<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Listing extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'marketplace_id',
        'team_id',
        'creator_id',
    ];

    public function marketplace(): BelongsTo
    {
        return $this->belongsTo(Marketplace::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }

    public function conversationWith(User $user): ?Conversation
    {
        return $this->conversations()
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhere('listing_creator_id', $user->id);
            })
            ->with('messages.user')
            ->first();
    }
}
