<?php

use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

it('allows deleting firewall rules', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    $firewallRule = $server->firewallRules()->create([
        'name' => 'HTTP Access',
        'action' => 'allow',
        'port' => 80,
        'from_ip' => '192.168.1.100',
        'team_id' => $user->currentTeam->id,
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::servers.firewall-rules.index', ['server' => $server])
        ->call('delete', $firewallRule->id)
        ->assertHasNoErrors();

    expect($server->fresh()->firewallRules)->toHaveCount(0);
});

it('displays firewall rules in table', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    $firewallRule = $server->firewallRules()->create([
        'name' => 'HTTP Access',
        'action' => 'allow',
        'port' => 80,
        'from_ip' => '192.168.1.100',
        'team_id' => $user->currentTeam->id,
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::servers.firewall-rules.index', ['server' => $server])
        ->assertOk()
        ->assertSee('HTTP Access')
        ->assertSee('allow')
        ->assertSee('80')
        ->assertSee('192.168.1.100')
        ->assertSee($user->name);
});

it('shows empty state when no firewall rules exist', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::servers.firewall-rules.index', ['server' => $server])
        ->assertOk()
        ->assertSee('No firewall rules configured on this server.');
});
