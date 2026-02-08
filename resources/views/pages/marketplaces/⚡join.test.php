<?php

use App\Models\Marketplace;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('allows users to join a team they do not belong to', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $owner = User::factory()->withPersonalTeam()->create();
    $team = $owner->currentTeam;
    $marketplace = Marketplace::factory()->for($team)->create();

    get(route('marketplaces.join', $marketplace));

    Livewire::test('pages::marketplaces.join', ['marketplace' => $marketplace])
        ->call('join')
        ->assertHasNoErrors();

    expect($team->fresh()->hasUser($user))->toBeTrue();
});

it('prevents users from joining their own team', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());
    $team = $user->currentTeam;
    $marketplace = Marketplace::factory()->for($team)->create();

    Livewire::test('pages::marketplaces.join', ['marketplace' => $marketplace])
        ->call('join')
        ->assertHasErrors(['team']);

    expect($team->fresh()->hasUser($user))->toBeTrue();
});

it('prevents users from joining a team they are already a member of', function () {
    $owner = User::factory()->withPersonalTeam()->create();
    $team = $owner->currentTeam;
    $marketplace = Marketplace::factory()->for($team)->create();

    $user = User::factory()->create();
    $team->users()->attach($user, ['role' => 'admin']);

    actingAs($user);

    Livewire::test('pages::marketplaces.join', ['marketplace' => $marketplace])
        ->call('join')
        ->assertHasErrors(['team']);

    expect($team->fresh()->hasUser($user))->toBeTrue();
});
