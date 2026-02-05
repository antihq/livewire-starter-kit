<?php

use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

it('allows editing daemons', function () {
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

    Livewire::test('pages::daemons.edit', ['daemon' => $daemon])
        ->set('command', 'php /var/www/html/artisan horizon:start')
        ->set('directory', '/var/www/html/new')
        ->set('systemUser', 'www-data')
        ->set('processes', 2)
        ->set('stop_wait_seconds', 15)
        ->set('stop_signal', 'KILL')
        ->call('update')
        ->assertHasNoErrors()
        ->assertRedirect(route('servers.daemons.index', $server->id));

    expect($daemon->fresh()->command)->toBe('php /var/www/html/artisan horizon:start')
        ->and($daemon->fresh()->directory)->toBe('/var/www/html/new')
        ->and($daemon->fresh()->user)->toBe('www-data')
        ->and($daemon->fresh()->processes)->toBe(2)
        ->and($daemon->fresh()->stop_wait_seconds)->toBe(15)
        ->and($daemon->fresh()->stop_signal)->toBe('KILL');
});

it('validates required fields when editing daemons', function () {
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

    Livewire::test('pages::daemons.edit', ['daemon' => $daemon])
        ->set('command', '')
        ->set('systemUser', '')
        ->set('processes', 0)
        ->set('stop_wait_seconds', -1)
        ->call('update')
        ->assertHasErrors(['command', 'systemUser', 'processes', 'stop_wait_seconds']);
});

it('allows clearing directory when editing daemons', function () {
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

    Livewire::test('pages::daemons.edit', ['daemon' => $daemon])
        ->set('directory', null)
        ->call('update')
        ->assertHasNoErrors()
        ->assertRedirect(route('servers.daemons.index', $server->id));

    expect($daemon->fresh()->directory)->toBeNull();
});
