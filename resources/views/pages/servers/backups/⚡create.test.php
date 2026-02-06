<?php

use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

it('creates backup with daily frequency', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::servers.backups.create', ['server' => $server])
        ->set('name', 'Test Backup')
        ->set('numberOfBackupsToRetain', 7)
        ->set('frequency', 'daily')
        ->call('create')
        ->assertRedirect(route('servers.backups.index', $server->id));

    expect($user->currentTeam->fresh()->backups)->toHaveCount(1);
    expect($user->currentTeam->fresh()->backups->first()->frequency)->toBe('daily');
});

it('creates backup with every minute frequency', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::servers.backups.create', ['server' => $server])
        ->set('name', 'Test Backup')
        ->set('numberOfBackupsToRetain', 7)
        ->set('frequency', 'every_minute')
        ->call('create')
        ->assertRedirect(route('servers.backups.index', $server->id));

    expect($user->currentTeam->fresh()->backups)->toHaveCount(1);
    expect($user->currentTeam->fresh()->backups->first()->frequency)->toBe('every_minute');
});

it('creates backup with every 5 minutes frequency', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::servers.backups.create', ['server' => $server])
        ->set('name', 'Test Backup')
        ->set('numberOfBackupsToRetain', 7)
        ->set('frequency', 'every_5_minutes')
        ->call('create')
        ->assertRedirect(route('servers.backups.index', $server->id));

    expect($user->currentTeam->fresh()->backups)->toHaveCount(1);
    expect($user->currentTeam->fresh()->backups->first()->frequency)->toBe('every_5_minutes');
});

it('creates backup with hourly frequency', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::servers.backups.create', ['server' => $server])
        ->set('name', 'Test Backup')
        ->set('numberOfBackupsToRetain', 7)
        ->set('frequency', 'hourly')
        ->call('create')
        ->assertRedirect(route('servers.backups.index', $server->id));

    expect($user->currentTeam->fresh()->backups)->toHaveCount(1);
    expect($user->currentTeam->fresh()->backups->first()->frequency)->toBe('hourly');
});

it('creates backup with weekly frequency', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::servers.backups.create', ['server' => $server])
        ->set('name', 'Test Backup')
        ->set('numberOfBackupsToRetain', 7)
        ->set('frequency', 'weekly')
        ->call('create')
        ->assertRedirect(route('servers.backups.index', $server->id));

    expect($user->currentTeam->fresh()->backups)->toHaveCount(1);
    expect($user->currentTeam->fresh()->backups->first()->frequency)->toBe('weekly');
});

it('creates backup with monthly frequency', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::servers.backups.create', ['server' => $server])
        ->set('name', 'Test Backup')
        ->set('numberOfBackupsToRetain', 7)
        ->set('frequency', 'monthly')
        ->call('create')
        ->assertRedirect(route('servers.backups.index', $server->id));

    expect($user->currentTeam->fresh()->backups)->toHaveCount(1);
    expect($user->currentTeam->fresh()->backups->first()->frequency)->toBe('monthly');
});

it('creates backup with on reboot frequency', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::servers.backups.create', ['server' => $server])
        ->set('name', 'Test Backup')
        ->set('numberOfBackupsToRetain', 7)
        ->set('frequency', 'on_reboot')
        ->call('create')
        ->assertRedirect(route('servers.backups.index', $server->id));

    expect($user->currentTeam->fresh()->backups)->toHaveCount(1);
    expect($user->currentTeam->fresh()->backups->first()->frequency)->toBe('on_reboot');
});

it('creates backup with custom frequency', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::servers.backups.create', ['server' => $server])
        ->set('name', 'Test Backup')
        ->set('numberOfBackupsToRetain', 7)
        ->set('frequency', 'custom')
        ->set('customCron', '0 0 * * *')
        ->call('create')
        ->assertRedirect(route('servers.backups.index', $server->id));

    expect($user->currentTeam->fresh()->backups)->toHaveCount(1);
    expect($user->currentTeam->fresh()->backups->first()->frequency)->toBe('custom');
    expect($user->currentTeam->fresh()->backups->first()->custom_cron)->toBe('0 0 * * *');
});

it('creates backup with databases selected', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    $database1 = $server->databases()->create([
        'name' => 'database1',
        'team_id' => $user->currentTeam->id,
        'creator_id' => $user->id,
    ]);

    $database2 = $server->databases()->create([
        'name' => 'database2',
        'team_id' => $user->currentTeam->id,
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::servers.backups.create', ['server' => $server])
        ->set('name', 'Test Backup')
        ->set('numberOfBackupsToRetain', 7)
        ->set('frequency', 'daily')
        ->set('selectedDatabases', [$database1->id, $database2->id])
        ->call('create')
        ->assertRedirect(route('servers.backups.index', $server->id));

    $backup = $user->currentTeam->fresh()->backups->first();
    expect($backup->databases)->toHaveCount(2);
    expect($backup->databases->pluck('id')->toArray())->toBe([$database1->id, $database2->id]);
});

it('creates backup with directories', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::servers.backups.create', ['server' => $server])
        ->set('name', 'Test Backup')
        ->set('numberOfBackupsToRetain', 7)
        ->set('frequency', 'daily')
        ->set('directories', "/var/www/html/storage\n/home/user/uploads")
        ->call('create')
        ->assertRedirect(route('servers.backups.index', $server->id));

    expect($user->currentTeam->fresh()->backups->first()->directories)->toBe("/var/www/html/storage\n/home/user/uploads");
});

it('creates backup with notifications on failure only', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::servers.backups.create', ['server' => $server])
        ->set('name', 'Test Backup')
        ->set('numberOfBackupsToRetain', 7)
        ->set('frequency', 'daily')
        ->set('notificationOnFailure', true)
        ->set('notificationEmail', 'admin@example.com')
        ->call('create')
        ->assertRedirect(route('servers.backups.index', $server->id));

    $backup = $user->currentTeam->fresh()->backups->first();
    expect($backup->notification_on_failure)->toBeTrue();
    expect($backup->notification_on_success)->toBeFalse();
    expect($backup->notification_email)->toBe('admin@example.com');
});

it('creates backup with notifications on success only', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::servers.backups.create', ['server' => $server])
        ->set('name', 'Test Backup')
        ->set('numberOfBackupsToRetain', 7)
        ->set('frequency', 'daily')
        ->set('notificationOnSuccess', true)
        ->set('notificationEmail', 'admin@example.com')
        ->call('create')
        ->assertRedirect(route('servers.backups.index', $server->id));

    $backup = $user->currentTeam->fresh()->backups->first();
    expect($backup->notification_on_failure)->toBeFalse();
    expect($backup->notification_on_success)->toBeTrue();
    expect($backup->notification_email)->toBe('admin@example.com');
});

it('creates backup with notifications on both failure and success', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::servers.backups.create', ['server' => $server])
        ->set('name', 'Test Backup')
        ->set('numberOfBackupsToRetain', 7)
        ->set('frequency', 'daily')
        ->set('notificationOnFailure', true)
        ->set('notificationOnSuccess', true)
        ->set('notificationEmail', 'admin@example.com')
        ->call('create')
        ->assertRedirect(route('servers.backups.index', $server->id));

    $backup = $user->currentTeam->fresh()->backups->first();
    expect($backup->notification_on_failure)->toBeTrue();
    expect($backup->notification_on_success)->toBeTrue();
    expect($backup->notification_email)->toBe('admin@example.com');
});

it('requires email when notifications are enabled', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::servers.backups.create', ['server' => $server])
        ->set('name', 'Test Backup')
        ->set('numberOfBackupsToRetain', 7)
        ->set('frequency', 'daily')
        ->set('notificationOnFailure', true)
        ->call('create')
        ->assertHasErrors(['notificationEmail']);
});

it('requires custom_cron when frequency is custom', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::servers.backups.create', ['server' => $server])
        ->set('name', 'Test Backup')
        ->set('numberOfBackupsToRetain', 7)
        ->set('frequency', 'custom')
        ->call('create')
        ->assertHasErrors(['customCron']);
});

it('validates required fields', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::servers.backups.create', ['server' => $server])
        ->set('name', '')
        ->set('numberOfBackupsToRetain', 0)
        ->set('frequency', '')
        ->call('create')
        ->assertHasErrors(['name', 'numberOfBackupsToRetain', 'frequency']);
});
