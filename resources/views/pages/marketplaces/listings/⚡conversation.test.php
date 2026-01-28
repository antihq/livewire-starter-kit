<?php

use App\Models\Conversation;
use App\Models\Listing;
use App\Models\Marketplace;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

it('displays conversation for listing creator', function () {
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
        'listing_id' => $listing->id,
        'user_id' => $user->id,
        'listing_creator_id' => $creator->id,
    ]);

    Message::factory()->create([
        'conversation_id' => $conversation->id,
        'user_id' => $user->id,
        'content' => 'Is this available?',
    ]);

    Livewire::test('pages::marketplaces.listings.conversation', ['marketplace' => $marketplace, 'listing' => $listing])
        ->assertOk()
        ->assertSee('Is this available?');
});

it('displays conversation for message sender', function () {
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
        'listing_id' => $listing->id,
        'user_id' => $user->id,
        'listing_creator_id' => $creator->id,
    ]);

    Message::factory()->create([
        'conversation_id' => $conversation->id,
        'user_id' => $user->id,
        'content' => 'Question about item',
    ]);

    Livewire::test('pages::marketplaces.listings.conversation', ['marketplace' => $marketplace, 'listing' => $listing])
        ->assertOk()
        ->assertSee('Question about item');
});

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
        'listing_id' => $listing->id,
        'user_id' => $user->id,
        'listing_creator_id' => $creator->id,
    ]);

    Notification::fake();

    Livewire::test('pages::marketplaces.listings.conversation', ['marketplace' => $marketplace, 'listing' => $listing])
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
        'listing_id' => $listing->id,
        'user_id' => $user->id,
        'listing_creator_id' => $creator->id,
    ]);

    Notification::fake();

    Livewire::test('pages::marketplaces.listings.conversation', ['marketplace' => $marketplace, 'listing' => $listing])
        ->set('content', 'Thanks for the info')
        ->call('send')
        ->assertHasNoErrors();

    expect($conversation->fresh()->messages)->toHaveCount(1);

    Notification::assertSentTo($creator, \App\Notifications\NewMessageNotification::class);
});

it('displays all messages in conversation', function () {
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
        'listing_id' => $listing->id,
        'user_id' => $user->id,
        'listing_creator_id' => $creator->id,
    ]);

    Message::factory()->create([
        'conversation_id' => $conversation->id,
        'user_id' => $user->id,
        'content' => 'First message',
    ]);

    Message::factory()->create([
        'conversation_id' => $conversation->id,
        'user_id' => $creator->id,
        'content' => 'Reply to first message',
    ]);

    Livewire::test('pages::marketplaces.listings.conversation', ['marketplace' => $marketplace, 'listing' => $listing])
        ->assertSee('First message')
        ->assertSee('Reply to first message');
});

it('clears content after sending message', function () {
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

    Conversation::factory()->create([
        'listing_id' => $listing->id,
        'user_id' => $user->id,
        'listing_creator_id' => $creator->id,
    ]);

    Notification::fake();

    Livewire::test('pages::marketplaces.listings.conversation', ['marketplace' => $marketplace, 'listing' => $listing])
        ->set('content', 'Test message')
        ->call('send')
        ->assertSet('content', '');
});

it('shows no conversation message when none exists', function () {
    actingAs($creator = User::factory()->withPersonalTeam()->create());

    $marketplace = Marketplace::factory()->create([
        'team_id' => $creator->currentTeam->id,
    ]);

    $listing = Listing::factory()->create([
        'marketplace_id' => $marketplace->id,
        'team_id' => $creator->currentTeam->id,
        'creator_id' => $creator->id,
    ]);

    Livewire::test('pages::marketplaces.listings.conversation', ['marketplace' => $marketplace, 'listing' => $listing])
        ->assertOk()
        ->assertDontSee('Send');
});
