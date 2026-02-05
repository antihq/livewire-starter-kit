<?php

use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

it('allows deleting cronjobs', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    $cronjob = $server->cronjobs()->create([
        'command' => 'php /var/www/html/artisan schedule:run',
        'user' => 'fuse',
        'frequency' => 'daily',
        'team_id' => $user->currentTeam->id,
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::servers.cronjobs.index', ['server' => $server])
        ->call('delete', $cronjob->id)
        ->assertHasNoErrors();

    expect($server->fresh()->cronjobs)->toHaveCount(0);
});

it('displays cronjobs in table', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    $cronjob = $server->cronjobs()->create([
        'command' => 'php /var/www/html/artisan schedule:run',
        'user' => 'fuse',
        'frequency' => 'daily',
        'team_id' => $user->currentTeam->id,
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::servers.cronjobs.index', ['server' => $server])
        ->assertOk()
        ->assertSee($cronjob->command)
        ->assertSee('fuse')
        ->assertSee('Daily')
        ->assertSee($user->name);
});

it('shows empty state when no cronjobs exist', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::servers.cronjobs.index', ['server' => $server])
        ->assertOk()
        ->assertSee('No cronjobs configured on this server.');
});
