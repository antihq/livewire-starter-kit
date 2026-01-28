<?php

use App\Models\Marketplace;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('can create listings', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $marketplace = Marketplace::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    get(route('marketplaces.listings.create', $marketplace))->assertOk();

    Livewire::test('pages::marketplaces.listings.create', ['marketplace' => $marketplace])
        ->set('title', 'Test Listing')
        ->set('description', 'This is a test description')
        ->call('create')
        ->assertHasNoErrors();

    expect($marketplace->fresh()->listings)->toHaveCount(1);
    expect($marketplace->fresh()->listings->first()->title)->toEqual('Test Listing');
    expect($marketplace->fresh()->listings->first()->description)->toEqual('This is a test description');
    expect($marketplace->fresh()->listings->first()->creator_id)->toEqual($user->id);
});
