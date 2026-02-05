<?php

use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

it('allows creating daemons', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::servers.daemons.create', ['server' => $server])
        ->set('command', 'php /var/www/html/artisan horizon')
        ->set('directory', '/var/www/html')
        ->set('systemUser', 'fuse')
        ->set('processes', 1)
        ->set('stop_wait_seconds', 10)
        ->set('stop_signal', 'TERM')
        ->call('create')
        ->assertHasNoErrors()
        ->assertRedirect(route('servers.daemons.index', $server->id));

    expect($server->fresh()->daemons)->toHaveCount(1)
        ->and($server->fresh()->daemons->first()->command)->toBe('php /var/www/html/artisan horizon')
        ->and($server->fresh()->daemons->first()->directory)->toBe('/var/www/html')
        ->and($server->fresh()->daemons->first()->user)->toBe('fuse')
        ->and($server->fresh()->daemons->first()->processes)->toBe(1)
        ->and($server->fresh()->daemons->first()->stop_wait_seconds)->toBe(10)
        ->and($server->fresh()->daemons->first()->stop_signal)->toBe('TERM');
});

it('validates required fields when creating daemons', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::servers.daemons.create', ['server' => $server])
        ->set('command', '')
        ->set('systemUser', '')
        ->set('processes', 0)
        ->set('stop_wait_seconds', -1)
        ->call('create')
        ->assertHasErrors(['command', 'systemUser', 'processes', 'stop_wait_seconds']);
});

it('allows creating daemons without directory', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::servers.daemons.create', ['server' => $server])
        ->set('command', 'php /var/www/html/artisan horizon')
        ->set('directory', null)
        ->set('systemUser', 'fuse')
        ->set('processes', 1)
        ->set('stop_wait_seconds', 10)
        ->set('stop_signal', 'TERM')
        ->call('create')
        ->assertHasNoErrors()
        ->assertRedirect(route('servers.daemons.index', $server->id));

    expect($server->fresh()->daemons->first()->directory)->toBeNull();
});
