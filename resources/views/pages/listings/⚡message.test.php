<?php

use App\Models\Listing;
use App\Models\Marketplace;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

it('creates conversation and message on submit', function () {
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

    Notification::fake();

    Livewire::test('pages::listings.message', ['listing' => $listing])
        ->set('content', 'Is this item still available?')
        ->call('send')
        ->assertHasNoErrors()
        ->assertRedirect(route('listings.show', $listing));

    expect($listing->fresh()->conversations)->toHaveCount(1);
    expect($listing->conversationWith($user)->messages)->toHaveCount(1);

    Notification::assertSentTo($creator, \App\Notifications\NewMessageNotification::class);
});

it('reuses existing conversation', function () {
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

    $conversation = $listing->conversations()->create([
        'user_id' => $user->id,
        'listing_creator_id' => $creator->id,
    ]);

    Notification::fake();

    Livewire::test('pages::listings.message', ['listing' => $listing])
        ->set('content', 'Another question')
        ->call('send')
        ->assertHasNoErrors();

    expect($listing->fresh()->conversations)->toHaveCount(1);
    expect($conversation->fresh()->messages)->toHaveCount(1);
});
