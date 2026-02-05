<?php

use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

it('allows creating firewall rules', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::servers.firewall-rules.create', ['server' => $server])
        ->set('name', 'HTTP Access')
        ->set('action', 'allow')
        ->set('port', 80)
        ->set('from_ip', '192.168.1.100')
        ->call('create')
        ->assertHasNoErrors()
        ->assertRedirect(route('servers.firewall-rules.index', $server->id));

    expect($server->fresh()->firewallRules)->toHaveCount(1)
        ->and($server->fresh()->firewallRules->first()->name)->toBe('HTTP Access')
        ->and($server->fresh()->firewallRules->first()->action)->toBe('allow')
        ->and($server->fresh()->firewallRules->first()->port)->toBe(80)
        ->and($server->fresh()->firewallRules->first()->from_ip)->toBe('192.168.1.100');
});

it('validates required fields when creating firewall rules', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::servers.firewall-rules.create', ['server' => $server])
        ->set('name', '')
        ->set('port', 0)
        ->call('create')
        ->assertHasErrors(['name', 'port']);
});

it('validates port range when creating firewall rules', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::servers.firewall-rules.create', ['server' => $server])
        ->set('name', 'HTTP Access')
        ->set('action', 'allow')
        ->set('port', 70000)
        ->call('create')
        ->assertHasErrors(['port']);
});

it('allows creating firewall rules without from_ip', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::servers.firewall-rules.create', ['server' => $server])
        ->set('name', 'HTTP Access')
        ->set('action', 'allow')
        ->set('port', 80)
        ->set('from_ip', null)
        ->call('create')
        ->assertHasNoErrors()
        ->assertRedirect(route('servers.firewall-rules.index', $server->id));

    expect($server->fresh()->firewallRules->first()->from_ip)->toBeNull();
});
