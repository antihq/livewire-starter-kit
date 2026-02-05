<?php

use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

it('allows deleting database users', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    $databaseUser = $server->databaseUsers()->create([
        'username' => 'db_user',
        'password' => 'secure_password',
        'team_id' => $user->currentTeam->id,
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::servers.database-users.index', ['server' => $server])
        ->call('delete', $databaseUser->id)
        ->assertHasNoErrors();

    expect($server->fresh()->databaseUsers)->toHaveCount(0);
});
