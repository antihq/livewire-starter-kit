<?php

use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

it('creates cronjob with every minute frequency', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::servers.cronjobs.create', ['server' => $server])
        ->set('command', 'php /var/www/html/artisan schedule:run')
        ->set('systemUser', 'fuse')
        ->set('frequency', 'every_minute')
        ->call('create')
        ->assertRedirect(route('servers.cronjobs.index', $server->id));

    expect($user->currentTeam->fresh()->cronjobs)->toHaveCount(1);
    expect($user->currentTeam->fresh()->cronjobs->first()->frequency)->toBe('every_minute');
});

it('creates cronjob with every 5 minutes frequency', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::servers.cronjobs.create', ['server' => $server])
        ->set('command', 'php /var/www/html/artisan schedule:run')
        ->set('systemUser', 'fuse')
        ->set('frequency', 'every_5_minutes')
        ->call('create')
        ->assertRedirect(route('servers.cronjobs.index', $server->id));

    expect($user->currentTeam->fresh()->cronjobs)->toHaveCount(1);
    expect($user->currentTeam->fresh()->cronjobs->first()->frequency)->toBe('every_5_minutes');
});

it('creates cronjob with hourly frequency', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::servers.cronjobs.create', ['server' => $server])
        ->set('command', 'php /var/www/html/artisan schedule:run')
        ->set('systemUser', 'fuse')
        ->set('frequency', 'hourly')
        ->call('create')
        ->assertRedirect(route('servers.cronjobs.index', $server->id));

    expect($user->currentTeam->fresh()->cronjobs)->toHaveCount(1);
    expect($user->currentTeam->fresh()->cronjobs->first()->frequency)->toBe('hourly');
});

it('creates cronjob with daily frequency', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::servers.cronjobs.create', ['server' => $server])
        ->set('command', 'php /var/www/html/artisan schedule:run')
        ->set('systemUser', 'fuse')
        ->set('frequency', 'daily')
        ->call('create')
        ->assertRedirect(route('servers.cronjobs.index', $server->id));

    expect($user->currentTeam->fresh()->cronjobs)->toHaveCount(1);
    expect($user->currentTeam->fresh()->cronjobs->first()->frequency)->toBe('daily');
});

it('creates cronjob with weekly frequency', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::servers.cronjobs.create', ['server' => $server])
        ->set('command', 'php /var/www/html/artisan schedule:run')
        ->set('systemUser', 'fuse')
        ->set('frequency', 'weekly')
        ->call('create')
        ->assertRedirect(route('servers.cronjobs.index', $server->id));

    expect($user->currentTeam->fresh()->cronjobs)->toHaveCount(1);
    expect($user->currentTeam->fresh()->cronjobs->first()->frequency)->toBe('weekly');
});

it('creates cronjob with monthly frequency', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::servers.cronjobs.create', ['server' => $server])
        ->set('command', 'php /var/www/html/artisan schedule:run')
        ->set('systemUser', 'fuse')
        ->set('frequency', 'monthly')
        ->call('create')
        ->assertRedirect(route('servers.cronjobs.index', $server->id));

    expect($user->currentTeam->fresh()->cronjobs)->toHaveCount(1);
    expect($user->currentTeam->fresh()->cronjobs->first()->frequency)->toBe('monthly');
});

it('creates cronjob with on reboot frequency', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::servers.cronjobs.create', ['server' => $server])
        ->set('command', 'php /var/www/html/artisan schedule:run')
        ->set('systemUser', 'fuse')
        ->set('frequency', 'on_reboot')
        ->call('create')
        ->assertRedirect(route('servers.cronjobs.index', $server->id));

    expect($user->currentTeam->fresh()->cronjobs)->toHaveCount(1);
    expect($user->currentTeam->fresh()->cronjobs->first()->frequency)->toBe('on_reboot');
});

it('creates cronjob with custom frequency', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::servers.cronjobs.create', ['server' => $server])
        ->set('command', 'php /var/www/html/artisan schedule:run')
        ->set('systemUser', 'fuse')
        ->set('frequency', 'custom')
        ->set('custom_cron', '0 0 * * *')
        ->call('create')
        ->assertRedirect(route('servers.cronjobs.index', $server->id));

    expect($user->currentTeam->fresh()->cronjobs)->toHaveCount(1);
    expect($user->currentTeam->fresh()->cronjobs->first()->frequency)->toBe('custom');
    expect($user->currentTeam->fresh()->cronjobs->first()->custom_cron)->toBe('0 0 * * *');
});

it('uses default user of fuse', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::servers.cronjobs.create', ['server' => $server])
        ->set('command', 'php /var/www/html/artisan schedule:run')
        ->set('frequency', 'daily')
        ->call('create')
        ->assertHasNoErrors()
        ->assertRedirect(route('servers.cronjobs.index', $server->id));

    expect($user->currentTeam->fresh()->cronjobs->first()->user)->toBe('fuse');
});

it('validates required fields', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::servers.cronjobs.create', ['server' => $server])
        ->set('systemUser', '')
        ->set('frequency', '')
        ->call('create')
        ->assertHasErrors(['command', 'systemUser', 'frequency']);
});

it('requires custom_cron when frequency is custom', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::servers.cronjobs.create', ['server' => $server])
        ->set('command', 'php /var/www/html/artisan schedule:run')
        ->set('systemUser', 'fuse')
        ->set('frequency', 'custom')
        ->call('create')
        ->assertHasErrors(['custom_cron']);
});
