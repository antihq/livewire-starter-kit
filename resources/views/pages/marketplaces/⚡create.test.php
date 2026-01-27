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

    expect($user->currentTeam->marketplaces)->toHaveCount(1);
    expect($user->currentTeam->marketplaces->first()->name)->toEqual('Test Marketplace');
});
