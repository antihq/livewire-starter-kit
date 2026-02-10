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
        ->set('price', '99.99')
        ->set('address', '123 Main Street, New York, NY 10001')
        ->set('latitude', '40.712776')
        ->set('longitude', '-74.005974')
        ->call('create')
        ->assertHasNoErrors();

    expect($marketplace->fresh()->listings)->toHaveCount(1);
    expect($marketplace->fresh()->listings->first()->title)->toEqual('Test Listing');
    expect($marketplace->fresh()->listings->first()->description)->toEqual('This is a test description');
    expect($marketplace->fresh()->listings->first()->address)->toEqual('123 Main Street, New York, NY 10001');
    expect($marketplace->fresh()->listings->first()->latitude)->toEqual('40.712776');
    expect($marketplace->fresh()->listings->first()->longitude)->toEqual('-74.005974');
    expect($marketplace->fresh()->listings->first()->creator_id)->toEqual($user->id);
});

it('validates address is required', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $marketplace = Marketplace::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Livewire::test('pages::marketplaces.listings.create', ['marketplace' => $marketplace])
        ->set('title', 'Test Listing')
        ->set('description', 'This is a test description')
        ->set('price', '99.99')
        ->set('latitude', '40.712776')
        ->set('longitude', '-74.005974')
        ->call('create')
        ->assertHasErrors(['address']);
});

it('validates latitude is required', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $marketplace = Marketplace::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Livewire::test('pages::marketplaces.listings.create', ['marketplace' => $marketplace])
        ->set('title', 'Test Listing')
        ->set('description', 'This is a test description')
        ->set('price', '99.99')
        ->set('address', '123 Main Street, New York, NY 10001')
        ->set('longitude', '-74.005974')
        ->call('create')
        ->assertHasErrors(['latitude']);
});

it('validates longitude is required', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $marketplace = Marketplace::factory()->create([
        'team_id' => $user->currentTeam->id,
    ]);

    Livewire::test('pages::marketplaces.listings.create', ['marketplace' => $marketplace])
        ->set('title', 'Test Listing')
        ->set('description', 'This is a test description')
        ->set('price', '99.99')
        ->set('address', '123 Main Street, New York, NY 10001')
        ->set('latitude', '40.712776')
        ->call('create')
        ->assertHasErrors(['longitude']);
});
