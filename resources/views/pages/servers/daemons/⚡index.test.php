<?php

use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

it('allows deleting daemons', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    $daemon = $server->daemons()->create([
        'command' => 'php /var/www/html/artisan horizon',
        'directory' => '/var/www/html',
        'user' => 'fuse',
        'processes' => 1,
        'stop_wait_seconds' => 10,
        'stop_signal' => 'TERM',
        'team_id' => $user->currentTeam->id,
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::servers.daemons.index', ['server' => $server])
        ->call('delete', $daemon->id)
        ->assertHasNoErrors();

    expect($server->fresh()->daemons)->toHaveCount(0);
});

it('displays daemons in table', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    $daemon = $server->daemons()->create([
        'command' => 'php /var/www/html/artisan horizon',
        'directory' => '/var/www/html',
        'user' => 'fuse',
        'processes' => 1,
        'stop_wait_seconds' => 10,
        'stop_signal' => 'TERM',
        'team_id' => $user->currentTeam->id,
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::servers.daemons.index', ['server' => $server])
        ->assertOk()
        ->assertSee($daemon->command)
        ->assertSee('/var/www/html')
        ->assertSee('fuse')
        ->assertSee('1')
        ->assertSee('TERM')
        ->assertSee($user->name);
});

it('shows empty state when no daemons exist', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::servers.daemons.index', ['server' => $server])
        ->assertOk()
        ->assertSee('No daemons configured on this server.');
});
