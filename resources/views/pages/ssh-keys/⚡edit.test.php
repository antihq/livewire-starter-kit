<?php

use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

it('preselects currently attached servers', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server1 = $user->currentTeam->servers()->create([
        'name' => 'Server 1',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    $server2 = $user->currentTeam->servers()->create([
        'name' => 'Server 2',
        'public_ip' => '192.168.1.101',
        'creator_id' => $user->id,
    ]);

    $sshKey = $user->currentTeam->sshKeys()->create([
        'name' => 'Test Key',
        'public_key' => 'ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAI user@test',
        'creator_id' => $user->id,
    ]);

    $sshKey->servers()->attach([$server1->id]);

    Livewire::test('pages::ssh-keys.edit', ['sshKey' => $sshKey])
        ->assertOk()
        ->assertSet('serverIds', [$server1->id]);
});

it('can attach servers to ssh key', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    $sshKey = $user->currentTeam->sshKeys()->create([
        'name' => 'Test Key',
        'public_key' => 'ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAI user@test',
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::ssh-keys.edit', ['sshKey' => $sshKey])
        ->set('serverIds', [$server->id])
        ->call('update')
        ->assertRedirect(route('ssh-keys.index'));

    expect($sshKey->fresh()->servers)->toHaveCount(1);
    expect($sshKey->fresh()->servers->first()->id)->toBe($server->id);
});

it('can detach servers from ssh key', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    $sshKey = $user->currentTeam->sshKeys()->create([
        'name' => 'Test Key',
        'public_key' => 'ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAI user@test',
        'creator_id' => $user->id,
    ]);

    $sshKey->servers()->attach([$server->id]);

    Livewire::test('pages::ssh-keys.edit', ['sshKey' => $sshKey])
        ->assertSet('serverIds', [$server->id])
        ->set('serverIds', [])
        ->call('update')
        ->assertRedirect(route('ssh-keys.index'));

    expect($sshKey->fresh()->servers)->toHaveCount(0);
});

it('can change attached servers', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server1 = $user->currentTeam->servers()->create([
        'name' => 'Server 1',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    $server2 = $user->currentTeam->servers()->create([
        'name' => 'Server 2',
        'public_ip' => '192.168.1.101',
        'creator_id' => $user->id,
    ]);

    $sshKey = $user->currentTeam->sshKeys()->create([
        'name' => 'Test Key',
        'public_key' => 'ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAI user@test',
        'creator_id' => $user->id,
    ]);

    $sshKey->servers()->attach([$server1->id]);

    Livewire::test('pages::ssh-keys.edit', ['sshKey' => $sshKey])
        ->assertSet('serverIds', [$server1->id])
        ->set('serverIds', [$server2->id])
        ->call('update')
        ->assertRedirect(route('ssh-keys.index'));

    expect($sshKey->fresh()->servers)->toHaveCount(1);
    expect($sshKey->fresh()->servers->first()->id)->toBe($server2->id);
});
