<?php

use App\Models\Conversation;
use App\Models\Listing;
use App\Models\Marketplace;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

it('displays conversation for participant in marketplace', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $marketplace = Marketplace::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    $creator = User::factory()->withPersonalTeam()->create();

    $listing = Listing::factory()->create([
        'marketplace_id' => $marketplace->id,
        'user_id' => $creator->id,
    ]);

    $conversation = Conversation::factory()->create([
        'listing_id' => $listing->id,
        'user_id' => $user->id,
        'listing_creator_id' => $creator->id,
    ]);

    Message::factory()->create([
        'conversation_id' => $conversation->id,
        'user_id' => $creator->id,
        'content' => 'Hello there!',
    ]);

    Livewire::test('pages::marketplaces.conversations.show', ['marketplace' => $marketplace, 'conversation' => $conversation])
        ->assertOk()
        ->assertSee('Hello there!')
        ->assertSee($listing->title)
        ->assertSee($creator->name);
});

it('displays all messages in conversation', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $marketplace = Marketplace::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    $creator = User::factory()->withPersonalTeam()->create();

    $listing = Listing::factory()->create([
        'marketplace_id' => $marketplace->id,
        'user_id' => $creator->id,
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

    Livewire::test('pages::marketplaces.conversations.show', ['marketplace' => $marketplace, 'conversation' => $conversation])
        ->assertSee('First message')
        ->assertSee('Reply to first message');
});

it('allows user to send message', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $marketplace = Marketplace::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    $creator = User::factory()->withPersonalTeam()->create();

    $listing = Listing::factory()->create([
        'marketplace_id' => $marketplace->id,
        'user_id' => $creator->id,
    ]);

    $conversation = Conversation::factory()->create([
        'listing_id' => $listing->id,
        'user_id' => $user->id,
        'listing_creator_id' => $creator->id,
    ]);

    Notification::fake();

    Livewire::test('pages::marketplaces.conversations.show', ['marketplace' => $marketplace, 'conversation' => $conversation])
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
        'user_id' => $user1->id,
    ]);

    $conversation = Conversation::factory()->create([
        'listing_id' => $listing->id,
        'user_id' => $user1->id,
        'listing_creator_id' => $user2->id,
    ]);

    Livewire::test('pages::marketplaces.conversations.show', ['marketplace' => $marketplace, 'conversation' => $conversation])
        ->assertForbidden();
});

it('shows no messages when conversation is empty', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $marketplace = Marketplace::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    $creator = User::factory()->withPersonalTeam()->create();

    $listing = Listing::factory()->create([
        'marketplace_id' => $marketplace->id,
        'user_id' => $creator->id,
    ]);

    $conversation = Conversation::factory()->create([
        'listing_id' => $listing->id,
        'user_id' => $user->id,
        'listing_creator_id' => $creator->id,
    ]);

    Livewire::test('pages::marketplaces.conversations.show', ['marketplace' => $marketplace, 'conversation' => $conversation])
        ->assertOk()
        ->assertSee('No messages yet');
});

it('validates message content', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $marketplace = Marketplace::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    $creator = User::factory()->withPersonalTeam()->create();

    $listing = Listing::factory()->create([
        'marketplace_id' => $marketplace->id,
        'user_id' => $creator->id,
    ]);

    $conversation = Conversation::factory()->create([
        'listing_id' => $listing->id,
        'user_id' => $user->id,
        'listing_creator_id' => $creator->id,
    ]);

    Livewire::test('pages::marketplaces.conversations.show', ['marketplace' => $marketplace, 'conversation' => $conversation])
        ->set('content', '')
        ->call('send')
        ->assertHasErrors(['content' => 'required']);
});

it('displays back button to marketplace conversations index', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $marketplace = Marketplace::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    $creator = User::factory()->withPersonalTeam()->create();

    $listing = Listing::factory()->create([
        'marketplace_id' => $marketplace->id,
        'user_id' => $creator->id,
    ]);

    $conversation = Conversation::factory()->create([
        'listing_id' => $listing->id,
        'user_id' => $user->id,
        'listing_creator_id' => $creator->id,
    ]);

    Livewire::test('pages::marketplaces.conversations.show', ['marketplace' => $marketplace, 'conversation' => $conversation])
        ->assertOk()
        ->assertSee('Back');
});

it('clears content after sending message', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $marketplace = Marketplace::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    $creator = User::factory()->withPersonalTeam()->create();

    $listing = Listing::factory()->create([
        'marketplace_id' => $marketplace->id,
        'user_id' => $creator->id,
    ]);

    $conversation = Conversation::factory()->create([
        'listing_id' => $listing->id,
        'user_id' => $user->id,
        'listing_creator_id' => $creator->id,
    ]);

    Notification::fake();

    Livewire::test('pages::marketplaces.conversations.show', ['marketplace' => $marketplace, 'conversation' => $conversation])
        ->set('content', 'Test message')
        ->call('send')
        ->assertSet('content', '');
});

it('returns 404 if conversation does not belong to marketplace', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $marketplace = Marketplace::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    $otherMarketplace = Marketplace::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    $creator = User::factory()->withPersonalTeam()->create();

    $listing = Listing::factory()->create([
        'marketplace_id' => $otherMarketplace->id,
        'user_id' => $creator->id,
    ]);

    $conversation = Conversation::factory()->create([
        'listing_id' => $listing->id,
        'user_id' => $user->id,
        'listing_creator_id' => $creator->id,
    ]);

    Livewire::test('pages::marketplaces.conversations.show', ['marketplace' => $marketplace, 'conversation' => $conversation])
        ->assertNotFound();
});

it('authorizes marketplace view', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $marketplace = Marketplace::factory()->create();

    $creator = User::factory()->withPersonalTeam()->create();

    $listing = Listing::factory()->create([
        'marketplace_id' => $marketplace->id,
        'user_id' => $creator->id,
    ]);

    $conversation = Conversation::factory()->create([
        'listing_id' => $listing->id,
        'user_id' => $user->id,
        'listing_creator_id' => $creator->id,
    ]);

    Livewire::test('pages::marketplaces.conversations.show', ['marketplace' => $marketplace, 'conversation' => $conversation])
        ->assertForbidden();
});
