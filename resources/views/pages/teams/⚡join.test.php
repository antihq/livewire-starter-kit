<?php

use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('allows users to join a team they do not belong to', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $owner = User::factory()->withPersonalTeam()->create();
    $team = $owner->currentTeam;

    get(route('teams.join', $team));

    Livewire::test('pages::teams.join', ['team' => $team])
        ->call('join')
        ->assertHasNoErrors();

    expect($team->fresh()->hasUser($user))->toBeTrue();
});

it('prevents users from joining their own team', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());
    $team = $user->currentTeam;

    Livewire::test('pages::teams.join', ['team' => $team])
        ->call('join')
        ->assertHasErrors(['team']);

    expect($team->fresh()->hasUser($user))->toBeTrue();
});

it('prevents users from joining a team they are already a member of', function () {
    $owner = User::factory()->withPersonalTeam()->create();
    $team = $owner->currentTeam;

    $user = User::factory()->create();
    $team->users()->attach($user, ['role' => 'admin']);

    actingAs($user);

    Livewire::test('pages::teams.join', ['team' => $team])
        ->call('join')
        ->assertHasErrors(['team']);

    expect($team->fresh()->hasUser($user))->toBeTrue();
});
