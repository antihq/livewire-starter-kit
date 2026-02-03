<?php

use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

it('creates ssh key', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    Livewire::test('pages::ssh-keys.create')
        ->set('name', 'Test Key')
        ->set('public_key', 'ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAI user@test')
        ->call('create')
        ->assertHasNoErrors();

    expect($user->currentTeam->fresh()->sshKeys)->toHaveCount(1);
    expect($user->currentTeam->fresh()->sshKeys->first()->name)->toBe('Test Key');
    expect($user->currentTeam->fresh()->sshKeys->first()->creator_id)->toBe($user->id);
});
