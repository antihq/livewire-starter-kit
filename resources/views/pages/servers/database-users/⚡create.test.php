<?php

use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

it('creates database user with username and password and attaches to multiple databases', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    $db1 = $server->databases()->create([
        'name' => 'app_production',
        'team_id' => $user->currentTeam->id,
        'creator_id' => $user->id,
    ]);

    $db2 = $server->databases()->create([
        'name' => 'app_staging',
        'team_id' => $user->currentTeam->id,
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::servers.database-users.create', ['server' => $server])
        ->set('username', 'db_user_1')
        ->set('password', 'secure_password')
        ->set('selectedDatabases', [$db1->id, $db2->id])
        ->call('create')
        ->assertRedirect(route('servers.database-users.index', $server->id));

    expect($user->currentTeam->fresh()->databaseUsers)->toHaveCount(1);
    expect($user->currentTeam->fresh()->databaseUsers->first()->username)->toBe('db_user_1');
    expect($user->currentTeam->fresh()->databaseUsers->first()->password)->toBe('secure_password');
    expect($user->currentTeam->fresh()->databaseUsers->first()->server_id)->toBe($server->id);
    expect($user->currentTeam->fresh()->databaseUsers->first()->creator_id)->toBe($user->id);
    expect($user->currentTeam->fresh()->databaseUsers->first()->databases)->toHaveCount(2);
});
