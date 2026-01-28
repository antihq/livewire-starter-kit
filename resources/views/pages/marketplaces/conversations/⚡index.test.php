<?php

use App\Models\Conversation;
use App\Models\Listing;
use App\Models\Marketplace;
use App\Models\Message;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

it('displays user conversations for marketplace', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $marketplace = Marketplace::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    $otherMarketplace = Marketplace::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    $creator = User::factory()->withPersonalTeam()->create();

    $listing = Listing::factory()->create([
        'marketplace_id' => $marketplace->id,
        'user_id' => $creator->id,
    ]);

    $otherListing = Listing::factory()->create([
        'marketplace_id' => $otherMarketplace->id,
        'user_id' => $creator->id,
    ]);

    $conversation = Conversation::factory()->create([
        'listing_id' => $listing->id,
        'user_id' => $user->id,
        'listing_creator_id' => $creator->id,
    ]);

    $otherConversation = Conversation::factory()->create([
        'listing_id' => $otherListing->id,
        'user_id' => $user->id,
        'listing_creator_id' => $creator->id,
    ]);

    Message::factory()->create([
        'conversation_id' => $conversation->id,
        'user_id' => $user->id,
        'content' => 'Is this available?',
    ]);

    Message::factory()->create([
        'conversation_id' => $otherConversation->id,
        'user_id' => $user->id,
        'content' => 'Other marketplace message',
    ]);

    Livewire::test('pages::marketplaces.conversations.index', ['marketplace' => $marketplace])
        ->assertOk()
        ->assertSee($listing->title)
        ->assertSee($creator->name)
        ->assertDontSee($otherListing->title);
});

it('displays no conversations message when none exist in marketplace', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $marketplace = Marketplace::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Livewire::test('pages::marketplaces.conversations.index', ['marketplace' => $marketplace])
        ->assertOk()
        ->assertSee('No conversations yet');
});

it('displays conversations sorted by most recent message', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $marketplace = Marketplace::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    $creator = User::factory()->withPersonalTeam()->create();

    $listing1 = Listing::factory()->create([
        'marketplace_id' => $marketplace->id,
        'user_id' => $creator->id,
    ]);

    $listing2 = Listing::factory()->create([
        'marketplace_id' => $marketplace->id,
        'user_id' => $creator->id,
    ]);

    $conversation1 = Conversation::factory()->create([
        'listing_id' => $listing1->id,
        'user_id' => $user->id,
        'listing_creator_id' => $creator->id,
    ]);

    $conversation2 = Conversation::factory()->create([
        'listing_id' => $listing2->id,
        'user_id' => $user->id,
        'listing_creator_id' => $creator->id,
    ]);

    Message::factory()->create([
        'conversation_id' => $conversation1->id,
        'user_id' => $user->id,
        'content' => 'Old message',
        'created_at' => now()->subHours(2),
    ]);

    Message::factory()->create([
        'conversation_id' => $conversation2->id,
        'user_id' => $creator->id,
        'content' => 'Recent message',
        'created_at' => now(),
    ]);

    Livewire::test('pages::marketplaces.conversations.index', ['marketplace' => $marketplace])
        ->assertOk()
        ->assertSeeInOrder([$listing2->title, $listing1->title]);
});

it('only displays conversations where user is participant in marketplace', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $marketplace = Marketplace::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    $creator = User::factory()->withPersonalTeam()->create();
    $otherUser = User::factory()->withPersonalTeam()->create();

    $listing1 = Listing::factory()->create([
        'marketplace_id' => $marketplace->id,
        'user_id' => $creator->id,
    ]);

    $listing2 = Listing::factory()->create([
        'marketplace_id' => $marketplace->id,
        'user_id' => $creator->id,
    ]);

    $userConversation = Conversation::factory()->create([
        'listing_id' => $listing1->id,
        'user_id' => $user->id,
        'listing_creator_id' => $creator->id,
    ]);

    $otherConversation = Conversation::factory()->create([
        'listing_id' => $listing2->id,
        'user_id' => $otherUser->id,
        'listing_creator_id' => $creator->id,
    ]);

    Message::factory()->create([
        'conversation_id' => $userConversation->id,
        'user_id' => $user->id,
        'content' => 'My conversation',
    ]);

    Message::factory()->create([
        'conversation_id' => $otherConversation->id,
        'user_id' => $otherUser->id,
        'content' => 'Other conversation',
    ]);

    Livewire::test('pages::marketplaces.conversations.index', ['marketplace' => $marketplace])
        ->assertOk()
        ->assertSee($listing1->title)
        ->assertDontSee($listing2->title);
});

it('displays listing title and other user name', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $marketplace = Marketplace::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    $creator = User::factory()->withPersonalTeam()->create(['name' => 'John Doe']);

    $listing = Listing::factory()->create([
        'marketplace_id' => $marketplace->id,
        'title' => 'Test Listing',
        'user_id' => $creator->id,
    ]);

    Conversation::factory()->create([
        'listing_id' => $listing->id,
        'user_id' => $user->id,
        'listing_creator_id' => $creator->id,
    ]);

    Livewire::test('pages::marketplaces.conversations.index', ['marketplace' => $marketplace])
        ->assertOk()
        ->assertSee('Test Listing')
        ->assertSee('John Doe');
});

it('authorizes marketplace view', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $marketplace = Marketplace::factory()->create();

    Livewire::test('pages::marketplaces.conversations.index', ['marketplace' => $marketplace])
        ->assertForbidden();
});

it('displays marketplace name in heading', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $marketplace = Marketplace::factory()->create([
        'team_id' => $user->currentTeam->id,
        'name' => 'My Marketplace',
    ]);

    Livewire::test('pages::marketplaces.conversations.index', ['marketplace' => $marketplace])
        ->assertOk()
        ->assertSee('My Marketplace');
});
