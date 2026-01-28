<?php

use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('can create marketplaces', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    get(route('marketplaces.create'))->assertOk();

    Livewire::test('pages::marketplaces.create')
        ->set('name', 'Test Marketplace')
        ->call('create')
        ->assertHasNoErrors();

    $marketplace = $user->currentTeam->marketplaces->first();

    expect($user->currentTeam->marketplaces)->toHaveCount(1);
    expect($marketplace->name)->toEqual('Test Marketplace');
    expect($marketplace->creator_id)->toEqual($user->id);
});
