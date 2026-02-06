<?php

use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

it('prepopulates form with existing backup values', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    $backup = $server->backups()->create([
        'name' => 'Test Backup',
        'number_of_backups_to_retain' => 7,
        'frequency' => 'daily',
        'directories' => "/var/www/html/storage\n/home/user/uploads",
        'notification_on_failure' => true,
        'notification_on_success' => false,
        'notification_email' => 'admin@example.com',
        'team_id' => $user->currentTeam->id,
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::backups.edit', ['backup' => $backup])
        ->assertOk()
        ->assertSet('name', 'Test Backup')
        ->assertSet('numberOfBackupsToRetain', 7)
        ->assertSet('frequency', 'daily')
        ->assertSet('directories', "/var/www/html/storage\n/home/user/uploads")
        ->assertSet('notificationOnFailure', true)
        ->assertSet('notificationOnSuccess', false)
        ->assertSet('notificationEmail', 'admin@example.com');
});

it('can update backup name', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    $backup = $server->backups()->create([
        'name' => 'Test Backup',
        'number_of_backups_to_retain' => 7,
        'frequency' => 'daily',
        'team_id' => $user->currentTeam->id,
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::backups.edit', ['backup' => $backup])
        ->set('name', 'Updated Backup')
        ->call('update')
        ->assertRedirect(route('servers.backups.index', $server->id));

    expect($backup->fresh()->name)->toBe('Updated Backup');
});

it('can update backup frequency', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    $backup = $server->backups()->create([
        'name' => 'Test Backup',
        'number_of_backups_to_retain' => 7,
        'frequency' => 'daily',
        'team_id' => $user->currentTeam->id,
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::backups.edit', ['backup' => $backup])
        ->set('frequency', 'weekly')
        ->call('update')
        ->assertRedirect(route('servers.backups.index', $server->id));

    expect($backup->fresh()->frequency)->toBe('weekly');
});

it('can update backup with custom frequency', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    $backup = $server->backups()->create([
        'name' => 'Test Backup',
        'number_of_backups_to_retain' => 7,
        'frequency' => 'daily',
        'team_id' => $user->currentTeam->id,
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::backups.edit', ['backup' => $backup])
        ->set('frequency', 'custom')
        ->set('customCron', '0 0 * * *')
        ->call('update')
        ->assertRedirect(route('servers.backups.index', $server->id));

    expect($backup->fresh()->frequency)->toBe('custom');
    expect($backup->fresh()->custom_cron)->toBe('0 0 * * *');
});

it('can update backup directories', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    $backup = $server->backups()->create([
        'name' => 'Test Backup',
        'number_of_backups_to_retain' => 7,
        'frequency' => 'daily',
        'directories' => '/var/www/html/storage',
        'team_id' => $user->currentTeam->id,
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::backups.edit', ['backup' => $backup])
        ->set('directories', "/var/www/html/storage\n/home/user/uploads\n/home/user/public")
        ->call('update')
        ->assertRedirect(route('servers.backups.index', $server->id));

    expect($backup->fresh()->directories)->toBe("/var/www/html/storage\n/home/user/uploads\n/home/user/public");
});

it('can update backup retention count', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    $backup = $server->backups()->create([
        'name' => 'Test Backup',
        'number_of_backups_to_retain' => 7,
        'frequency' => 'daily',
        'team_id' => $user->currentTeam->id,
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::backups.edit', ['backup' => $backup])
        ->set('numberOfBackupsToRetain', 14)
        ->call('update')
        ->assertRedirect(route('servers.backups.index', $server->id));

    expect($backup->fresh()->number_of_backups_to_retain)->toBe(14);
});

it('can update backup databases', function () {
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

    $backup = $server->backups()->create([
        'name' => 'Test Backup',
        'number_of_backups_to_retain' => 7,
        'frequency' => 'daily',
        'team_id' => $user->currentTeam->id,
        'creator_id' => $user->id,
    ]);

    $backup->databases()->attach($database1);

    Livewire::test('pages::backups.edit', ['backup' => $backup])
        ->set('selectedDatabases', [$database1->id, $database2->id])
        ->call('update')
        ->assertRedirect(route('servers.backups.index', $server->id));

    $freshBackup = $backup->fresh()->load('databases');
    expect($freshBackup->databases)->toHaveCount(2);
    expect($freshBackup->databases->pluck('id')->toArray())->toBe([$database1->id, $database2->id]);
});

it('can update backup disk', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    $backupDisk1 = $user->currentTeam->backupDisks()->create([
        'name' => 'S3 Backup Disk 1',
        'driver' => 's3',
        's3_bucket' => 'my-backups',
        's3_access_key' => 'key',
        's3_secret_key' => 'secret',
        's3_region' => 'us-east-1',
        'creator_id' => $user->id,
    ]);

    $backupDisk2 = $user->currentTeam->backupDisks()->create([
        'name' => 'S3 Backup Disk 2',
        'driver' => 's3',
        's3_bucket' => 'my-backups-2',
        's3_access_key' => 'key',
        's3_secret_key' => 'secret',
        's3_region' => 'us-east-1',
        'creator_id' => $user->id,
    ]);

    $backup = $server->backups()->create([
        'name' => 'Test Backup',
        'number_of_backups_to_retain' => 7,
        'frequency' => 'daily',
        'backup_disk_id' => $backupDisk1->id,
        'team_id' => $user->currentTeam->id,
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::backups.edit', ['backup' => $backup])
        ->set('backupDiskId', $backupDisk2->id)
        ->call('update')
        ->assertRedirect(route('servers.backups.index', $server->id));

    expect($backup->fresh()->backup_disk_id)->toBe($backupDisk2->id);
});

it('can update notifications', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    $backup = $server->backups()->create([
        'name' => 'Test Backup',
        'number_of_backups_to_retain' => 7,
        'frequency' => 'daily',
        'notification_on_failure' => false,
        'notification_on_success' => false,
        'notification_email' => null,
        'team_id' => $user->currentTeam->id,
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::backups.edit', ['backup' => $backup])
        ->set('notificationOnFailure', true)
        ->set('notificationOnSuccess', true)
        ->set('notificationEmail', 'admin@example.com')
        ->call('update')
        ->assertRedirect(route('servers.backups.index', $server->id));

    $freshBackup = $backup->fresh();
    expect($freshBackup->notification_on_failure)->toBeTrue();
    expect($freshBackup->notification_on_success)->toBeTrue();
    expect($freshBackup->notification_email)->toBe('admin@example.com');
});

it('clears custom_cron when switching from custom to predefined frequency', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    $backup = $server->backups()->create([
        'name' => 'Test Backup',
        'number_of_backups_to_retain' => 7,
        'frequency' => 'custom',
        'custom_cron' => '0 0 * * *',
        'team_id' => $user->currentTeam->id,
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::backups.edit', ['backup' => $backup])
        ->set('frequency', 'daily')
        ->call('update')
        ->assertRedirect(route('servers.backups.index', $server->id));

    expect($backup->fresh()->frequency)->toBe('daily');
    expect($backup->fresh()->custom_cron)->toBeNull();
});

it('removes databases when updating with empty selection', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    $database = $server->databases()->create([
        'name' => 'database1',
        'team_id' => $user->currentTeam->id,
        'creator_id' => $user->id,
    ]);

    $backup = $server->backups()->create([
        'name' => 'Test Backup',
        'number_of_backups_to_retain' => 7,
        'frequency' => 'daily',
        'team_id' => $user->currentTeam->id,
        'creator_id' => $user->id,
    ]);

    $backup->databases()->attach($database);

    Livewire::test('pages::backups.edit', ['backup' => $backup])
        ->set('selectedDatabases', [])
        ->call('update')
        ->assertRedirect(route('servers.backups.index', $server->id));

    $freshBackup = $backup->fresh()->load('databases');
    expect($freshBackup->databases)->toHaveCount(0);
});

it('validates required fields on update', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    $backup = $server->backups()->create([
        'name' => 'Test Backup',
        'number_of_backups_to_retain' => 7,
        'frequency' => 'daily',
        'team_id' => $user->currentTeam->id,
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::backups.edit', ['backup' => $backup])
        ->set('name', '')
        ->call('update')
        ->assertHasErrors(['name']);
});

it('requires email when notifications are enabled on update', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    $backup = $server->backups()->create([
        'name' => 'Test Backup',
        'number_of_backups_to_retain' => 7,
        'frequency' => 'daily',
        'notification_on_failure' => false,
        'notification_on_success' => false,
        'team_id' => $user->currentTeam->id,
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::backups.edit', ['backup' => $backup])
        ->set('notificationOnFailure', true)
        ->call('update')
        ->assertHasErrors(['notificationEmail']);
});

it('requires custom_cron when frequency is custom on update', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    $backup = $server->backups()->create([
        'name' => 'Test Backup',
        'number_of_backups_to_retain' => 7,
        'frequency' => 'daily',
        'team_id' => $user->currentTeam->id,
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::backups.edit', ['backup' => $backup])
        ->set('frequency', 'custom')
        ->call('update')
        ->assertHasErrors(['customCron']);
});
