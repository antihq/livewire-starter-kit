<?php

use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

it('allows deleting ssh keys', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $sshKey = $user->currentTeam->sshKeys()->create([
        'name' => 'Test Key',
        'public_key' => 'ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAI user@test',
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::ssh-keys.index')
        ->call('delete', $sshKey->id)
        ->assertHasNoErrors();

    expect($user->currentTeam->fresh()->sshKeys)->toHaveCount(0);
});

it('prevents unauthorized users from deleting keys from other teams', function () {
    $user1 = User::factory()->withPersonalTeam()->create();
    $user2 = User::factory()->withPersonalTeam()->create();

    $sshKey = $user1->currentTeam->sshKeys()->create([
        'name' => 'Test Key',
        'public_key' => 'ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAI user@test',
        'creator_id' => $user1->id,
    ]);

    expect(fn () => Livewire::actingAs($user2)->test('pages::ssh-keys.index')->call('delete', $sshKey->id))
        ->toThrow(ModelNotFoundException::class);
});

it('detaches ssh keys from servers when deleted', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $sshKey = $user->currentTeam->sshKeys()->create([
        'name' => 'Test Key',
        'public_key' => 'ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAI user@test',
        'creator_id' => $user->id,
    ]);

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.1',
        'creator_id' => $user->id,
    ]);

    $sshKey->servers()->attach($server->id);

    expect($server->fresh()->sshKeys)->toHaveCount(1);

    Livewire::test('pages::ssh-keys.index')
        ->call('delete', $sshKey->id)
        ->assertHasNoErrors();

    expect($server->fresh()->sshKeys)->toHaveCount(0);
});
