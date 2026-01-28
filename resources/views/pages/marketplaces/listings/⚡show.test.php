<?php

use App\Models\Listing;
use App\Models\Marketplace;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

it('displays listing details', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $marketplace = Marketplace::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    $listing = Listing::factory()->create([
        'marketplace_id' => $marketplace->id,
        'team_id' => $user->currentTeam->id,
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::marketplaces.listings.show', ['marketplace' => $marketplace, 'listing' => $listing])
        ->assertOk()
        ->assertSee($listing->title)
        ->assertSee($listing->description);
});

it('shows view conversations button to listing creator', function () {
    actingAs($creator = User::factory()->withPersonalTeam()->create());

    $marketplace = Marketplace::factory()->create([
        'team_id' => $creator->currentTeam->id,
    ]);

    $listing = Listing::factory()->create([
        'marketplace_id' => $marketplace->id,
        'team_id' => $creator->currentTeam->id,
        'creator_id' => $creator->id,
    ]);

    Livewire::test('pages::marketplaces.listings.show', ['marketplace' => $marketplace, 'listing' => $listing])
        ->assertSee('View conversations')
        ->assertDontSee('Send message');
});

it('shows send message button to non-creator', function () {
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

    Livewire::test('pages::marketplaces.listings.show', ['marketplace' => $marketplace, 'listing' => $listing])
        ->assertSee('Send message')
        ->assertDontSee('View conversations');
});

it('does not show message buttons to guests', function () {
    $creator = User::factory()->withPersonalTeam()->create();

    $marketplace = Marketplace::factory()->create([
        'team_id' => $creator->currentTeam->id,
    ]);

    $listing = Listing::factory()->create([
        'marketplace_id' => $marketplace->id,
        'team_id' => $creator->currentTeam->id,
        'creator_id' => $creator->id,
    ]);

    Livewire::test('pages::marketplaces.listings.show', ['marketplace' => $marketplace, 'listing' => $listing])
        ->assertDontSee('Send message')
        ->assertDontSee('View conversations');
});
