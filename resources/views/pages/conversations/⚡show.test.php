<?php

use App\Models\Conversation;
use App\Models\Listing;
use App\Models\Marketplace;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

it('allows user to send message', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $marketplace = Marketplace::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    $creator = User::factory()->withPersonalTeam()->create();

    $listing = Listing::factory()->create([
        'marketplace_id' => $marketplace->id,
        'team_id' => $creator->currentTeam->id,
        'creator_id' => $creator->id,
    ]);

    $conversation = Conversation::factory()->create([
        'team_id' => $creator->currentTeam->id,
        'listing_id' => $listing->id,
        'user_id' => $user->id,
        'listing_creator_id' => $creator->id,
        'marketplace_id' => $marketplace->id,
    ]);

    Notification::fake();

    Livewire::test('pages::conversations.show', ['conversation' => $conversation])
        ->set('content', 'New message')
        ->call('send')
        ->assertHasNoErrors()
        ->assertSet('content', '');

    expect($conversation->fresh()->messages)->toHaveCount(1);
    expect($conversation->fresh()->messages->first()->content)->toEqual('New message');

    Notification::assertSentTo($creator, \App\Notifications\NewMessageNotification::class);
});

it('forbids non-participants from viewing conversation', function () {
    actingAs($nonParticipant = User::factory()->withPersonalTeam()->create());

    $marketplace = Marketplace::factory()->create([
        'team_id' => $nonParticipant->currentTeam->id,
    ]);

    $user1 = User::factory()->withPersonalTeam()->create();
    $user2 = User::factory()->withPersonalTeam()->create();

    $listing = Listing::factory()->create([
        'marketplace_id' => $marketplace->id,
        'team_id' => $user1->currentTeam->id,
        'creator_id' => $user1->id,
    ]);

    $conversation = Conversation::factory()->create([
        'team_id' => $user1->currentTeam->id,
        'listing_id' => $listing->id,
        'user_id' => $user1->id,
        'listing_creator_id' => $user2->id,
        'marketplace_id' => $marketplace->id,
    ]);

    Livewire::test('pages::conversations.show', ['conversation' => $conversation])
        ->assertForbidden();
});
