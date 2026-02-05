<?php

use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

it('creates database with name field', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::servers.databases.create', ['server' => $server])
        ->set('name', 'app_production')
        ->call('create')
        ->assertRedirect(route('servers.show', $server->id));

    expect($user->currentTeam->fresh()->databases)->toHaveCount(1);
    expect($user->currentTeam->fresh()->databases->first()->name)->toBe('app_production');
    expect($user->currentTeam->fresh()->databases->first()->server_id)->toBe($server->id);
    expect($user->currentTeam->fresh()->databases->first()->creator_id)->toBe($user->id);
});
