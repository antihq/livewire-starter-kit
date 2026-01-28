<?php

use App\Models\Conversation;
use App\Models\Listing;
use App\Models\Marketplace;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

it('allows listing creator to reply', function () {
    actingAs($creator = User::factory()->withPersonalTeam()->create());

    $marketplace = Marketplace::factory()->create([
        'team_id' => $creator->currentTeam->id,
    ]);

    $user = User::factory()->withPersonalTeam()->create();

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
    ]);

    Notification::fake();

    Livewire::test('pages::listings.conversation', ['listing' => $listing])
        ->set('content', 'Yes, it is available!')
        ->call('send')
        ->assertHasNoErrors();

    expect($conversation->fresh()->messages)->toHaveCount(1);
    expect($conversation->fresh()->messages->first()->content)->toEqual('Yes, it is available!');
    expect($listing->fresh()->conversations)->toHaveCount(1);

    Notification::assertSentTo($user, \App\Notifications\NewMessageNotification::class);
});

it('allows message sender to reply', function () {
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
    ]);

    Notification::fake();

    Livewire::test('pages::listings.conversation', ['listing' => $listing])
        ->set('content', 'Thanks for the info')
        ->call('send')
        ->assertHasNoErrors();

    expect($conversation->fresh()->messages)->toHaveCount(1);

    Notification::assertSentTo($creator, \App\Notifications\NewMessageNotification::class);
});
