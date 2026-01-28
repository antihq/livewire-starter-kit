<?php

use App\Models\Conversation;
use App\Models\Listing;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

it('displays conversations for authenticated user', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $creator = User::factory()->withPersonalTeam()->create();

    $listing = Listing::factory()->create([
        'team_id' => $creator->currentTeam->id,
        'creator_id' => $creator->id,
    ]);

    $conversation = Conversation::factory()->create([
        'team_id' => $creator->currentTeam->id,
        'listing_id' => $listing->id,
        'user_id' => $user->id,
        'listing_creator_id' => $creator->id,
    ]);

    Livewire::test('pages::conversations.index')
        ->assertSee($conversation->listing->title)
        ->assertSee($creator->name ?? $creator->email);
});

it('hides conversations from non-participants', function () {
    actingAs($nonParticipant = User::factory()->withPersonalTeam()->create());

    $user1 = User::factory()->withPersonalTeam()->create();
    $user2 = User::factory()->withPersonalTeam()->create();

    $listing = Listing::factory()->create([
        'team_id' => $user1->currentTeam->id,
        'creator_id' => $user1->id,
    ]);

    $conversation = Conversation::factory()->create([
        'team_id' => $user1->currentTeam->id,
        'listing_id' => $listing->id,
        'user_id' => $user1->id,
        'listing_creator_id' => $user2->id,
    ]);

    Livewire::test('pages::conversations.index')
        ->assertDontSee($conversation->listing->title);
});
