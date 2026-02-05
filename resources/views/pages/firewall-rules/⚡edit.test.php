<?php

use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

it('allows editing firewall rule name', function () {
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

    Livewire::test('pages::firewall-rules.edit', ['firewallRule' => $firewallRule])
        ->set('name', 'HTTP Access Updated')
        ->call('update')
        ->assertHasNoErrors()
        ->assertRedirect(route('servers.firewall-rules.index', $server->id));

    expect($firewallRule->fresh()->name)->toBe('HTTP Access Updated')
        ->and($firewallRule->fresh()->action)->toBe('allow')
        ->and($firewallRule->fresh()->port)->toBe(80)
        ->and($firewallRule->fresh()->from_ip)->toBe('192.168.1.100');
});

it('validates required field when editing firewall rule name', function () {
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

    Livewire::test('pages::firewall-rules.edit', ['firewallRule' => $firewallRule])
        ->set('name', '')
        ->call('update')
        ->assertHasErrors(['name']);
});

it('displays read-only firewall rule details', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    $firewallRule = $server->firewallRules()->create([
        'name' => 'HTTP Access',
        'action' => 'deny',
        'port' => 443,
        'from_ip' => '10.0.0.1',
        'team_id' => $user->currentTeam->id,
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::firewall-rules.edit', ['firewallRule' => $firewallRule])
        ->assertOk()
        ->assertSee('HTTP Access')
        ->assertSee('deny')
        ->assertSee('443')
        ->assertSee('10.0.0.1');
});
