<?php

use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

it('allows deleting databases', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    $database = $server->databases()->create([
        'name' => 'app_production',
        'team_id' => $user->currentTeam->id,
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::servers.databases.index', ['server' => $server])
        ->call('delete', $database->id)
        ->assertHasNoErrors();

    expect($server->fresh()->databases)->toHaveCount(0);
});
