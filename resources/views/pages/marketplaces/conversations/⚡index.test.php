<?php

use App\Models\Conversation;
use App\Models\Listing;
use App\Models\Marketplace;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

it('displays conversations for authenticated user in marketplace', function () {
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

    Livewire::test('pages::marketplaces.conversations.index', ['marketplace' => $marketplace])
        ->assertSee($conversation->listing->title)
        ->assertSee($creator->name ?? $creator->email);
});

it('hides conversations from non-participants', function () {
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

    Livewire::test('pages::marketplaces.conversations.index', ['marketplace' => $marketplace])
        ->assertDontSee($conversation->listing->title);
});

it('filters conversations by marketplace', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $marketplace1 = Marketplace::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    $marketplace2 = Marketplace::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    $creator = User::factory()->withPersonalTeam()->create();

    $listing1 = Listing::factory()->create([
        'marketplace_id' => $marketplace1->id,
        'team_id' => $creator->currentTeam->id,
        'creator_id' => $creator->id,
    ]);

    $listing2 = Listing::factory()->create([
        'marketplace_id' => $marketplace2->id,
        'team_id' => $creator->currentTeam->id,
        'creator_id' => $creator->id,
    ]);

    $conversation1 = Conversation::factory()->create([
        'team_id' => $creator->currentTeam->id,
        'listing_id' => $listing1->id,
        'user_id' => $user->id,
        'listing_creator_id' => $creator->id,
        'marketplace_id' => $marketplace1->id,
    ]);

    $conversation2 = Conversation::factory()->create([
        'team_id' => $creator->currentTeam->id,
        'listing_id' => $listing2->id,
        'user_id' => $user->id,
        'listing_creator_id' => $creator->id,
        'marketplace_id' => $marketplace2->id,
    ]);

    Livewire::test('pages::marketplaces.conversations.index', ['marketplace' => $marketplace1])
        ->assertSee($conversation1->listing->title)
        ->assertDontSee($conversation2->listing->title);
});
