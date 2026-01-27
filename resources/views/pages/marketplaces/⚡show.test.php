<?php

use App\Models\Listing;
use App\Models\Marketplace;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('can update marketplace names', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $marketplace = Marketplace::factory()->create([
        'team_id' => $user->currentTeam->id,
        'name' => 'Original Name',
    ]);

    get(route('marketplaces.edit', $marketplace))->assertOk();

    Livewire::test('pages::marketplaces.show', ['marketplace' => $marketplace])
        ->set('name', 'Updated Name')
        ->call('update');

    expect($marketplace->fresh()->name)->toEqual('Updated Name');
});

it('deletes marketplaces', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $marketplace = Marketplace::factory()->create([
        'team_id' => $user->currentTeam->id,
        'name' => 'Test Marketplace',
    ]);

    Livewire::test('pages::marketplaces.show', ['marketplace' => $marketplace])
        ->call('delete');

    expect($marketplace->fresh())->toBeNull();
});
