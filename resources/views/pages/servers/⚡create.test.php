<?php

use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

it('creates server with name and ip', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    Livewire::test('pages::servers.create')
        ->set('name', 'Test Server')
        ->set('public_ip', '192.168.1.100')
        ->call('create')
        ->assertHasNoErrors();

    expect($user->currentTeam->fresh()->servers)->toHaveCount(1);
    expect($user->currentTeam->fresh()->servers->first()->name)->toBe('Test Server');
    expect($user->currentTeam->fresh()->servers->first()->public_ip)->toBe('192.168.1.100');
    expect($user->currentTeam->fresh()->servers->first()->creator_id)->toBe($user->id);
});

it('creates server with ssh keys attached', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $sshKey = $user->currentTeam->sshKeys()->create([
        'name' => 'Test Key',
        'public_key' => 'ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAI user@test',
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::servers.create')
        ->set('name', 'Test Server')
        ->set('public_ip', '192.168.1.100')
        ->set('sshKeyIds', [$sshKey->id])
        ->call('create')
        ->assertHasNoErrors();

    $server = $user->currentTeam->fresh()->servers->first();
    expect($server->sshKeys)->toHaveCount(1);
    expect($server->sshKeys->first()->id)->toBe($sshKey->id);
});
