<?php

use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

it('prepopulates form with existing cronjob values', function () {
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

    Livewire::test('pages::cronjobs.edit', ['cronjob' => $cronjob])
        ->assertOk()
        ->assertSet('command', 'php /var/www/html/artisan schedule:run')
        ->assertSet('systemUser', 'fuse')
        ->assertSet('frequency', 'daily');
});

it('can update cronjob command', function () {
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

    Livewire::test('pages::cronjobs.edit', ['cronjob' => $cronjob])
        ->set('command', 'php /var/www/html/artisan backup:run')
        ->call('update')
        ->assertRedirect(route('servers.cronjobs.index', $server->id));

    expect($cronjob->fresh()->command)->toBe('php /var/www/html/artisan backup:run');
});

it('can update cronjob user', function () {
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

    Livewire::test('pages::cronjobs.edit', ['cronjob' => $cronjob])
        ->set('systemUser', 'root')
        ->call('update')
        ->assertRedirect(route('servers.cronjobs.index', $server->id));

    expect($cronjob->fresh()->user)->toBe('root');
});

it('can update cronjob frequency', function () {
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

    Livewire::test('pages::cronjobs.edit', ['cronjob' => $cronjob])
        ->set('frequency', 'hourly')
        ->call('update')
        ->assertRedirect(route('servers.cronjobs.index', $server->id));

    expect($cronjob->fresh()->frequency)->toBe('hourly');
});

it('can update cronjob with custom frequency', function () {
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

    Livewire::test('pages::cronjobs.edit', ['cronjob' => $cronjob])
        ->set('frequency', 'custom')
        ->set('custom_cron', '0 0 * * *')
        ->call('update')
        ->assertRedirect(route('servers.cronjobs.index', $server->id));

    expect($cronjob->fresh()->frequency)->toBe('custom');
    expect($cronjob->fresh()->custom_cron)->toBe('0 0 * * *');
});

it('clears custom_cron when switching from custom to predefined frequency', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    $cronjob = $server->cronjobs()->create([
        'command' => 'php /var/www/html/artisan schedule:run',
        'user' => 'fuse',
        'frequency' => 'custom',
        'custom_cron' => '0 0 * * *',
        'team_id' => $user->currentTeam->id,
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::cronjobs.edit', ['cronjob' => $cronjob])
        ->set('frequency', 'daily')
        ->call('update')
        ->assertRedirect(route('servers.cronjobs.index', $server->id));

    expect($cronjob->fresh()->frequency)->toBe('daily');
    expect($cronjob->fresh()->custom_cron)->toBeNull();
});

it('validates required fields on update', function () {
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

    Livewire::test('pages::cronjobs.edit', ['cronjob' => $cronjob])
        ->set('command', '')
        ->call('update')
        ->assertHasErrors(['command']);
});
