<?php

use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

it('deletes backup', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    $backup = $server->backups()->create([
        'name' => 'Test Backup',
        'number_of_backups_to_retain' => 7,
        'frequency' => 'daily',
        'team_id' => $user->currentTeam->id,
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::servers.backups.index', ['server' => $server])
        ->call('delete', $backup->id)
        ->assertHasNoErrors();

    expect($server->fresh()->backups)->toHaveCount(0);
});
