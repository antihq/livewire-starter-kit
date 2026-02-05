<?php

use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

it('allows editing backup disk name', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $backupDisk = $user->currentTeam->backupDisks()->create([
        'name' => 'Old Name',
        'driver' => 's3',
        's3_bucket' => 'test-bucket',
        's3_access_key' => 'test-key',
        's3_secret_key' => 'test-secret',
        's3_region' => 'us-east-1',
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::backup-disks.edit', ['backupDisk' => $backupDisk])
        ->set('name', 'New Name')
        ->call('update')
        ->assertRedirect(route('backup-disks.index'));

    expect($backupDisk->fresh()->name)->toBe('New Name');
});

it('does not allow changing driver type', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $backupDisk = $user->currentTeam->backupDisks()->create([
        'name' => 'S3 Backup',
        'driver' => 's3',
        's3_bucket' => 'test-bucket',
        's3_access_key' => 'test-key',
        's3_secret_key' => 'test-secret',
        's3_region' => 'us-east-1',
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::backup-disks.edit', ['backupDisk' => $backupDisk])
        ->assertSet('name', 'S3 Backup')
        ->assertSee('S3');
});

it('allows editing s3 configuration', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $backupDisk = $user->currentTeam->backupDisks()->create([
        'name' => 'S3 Backup',
        'driver' => 's3',
        's3_bucket' => 'old-bucket',
        's3_access_key' => 'old-key',
        's3_secret_key' => 'old-secret',
        's3_region' => 'us-east-1',
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::backup-disks.edit', ['backupDisk' => $backupDisk])
        ->set('s3_bucket', 'new-bucket')
        ->set('s3_access_key', 'new-key')
        ->set('s3_region', 'eu-west-1')
        ->call('update')
        ->assertRedirect(route('backup-disks.index'));

    $disk = $backupDisk->fresh();
    expect($disk->s3_bucket)->toBe('new-bucket');
    expect($disk->s3_access_key)->toBe('new-key');
    expect($disk->s3_region)->toBe('eu-west-1');
});

it('keeps existing s3 secret key when empty', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $backupDisk = $user->currentTeam->backupDisks()->create([
        'name' => 'S3 Backup',
        'driver' => 's3',
        's3_bucket' => 'test-bucket',
        's3_access_key' => 'test-key',
        's3_secret_key' => 'old-secret',
        's3_region' => 'us-east-1',
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::backup-disks.edit', ['backupDisk' => $backupDisk])
        ->set('s3_bucket', 'new-bucket')
        ->set('s3_secret_key', '')
        ->call('update')
        ->assertRedirect(route('backup-disks.index'));

    expect($backupDisk->fresh()->s3_secret_key)->toBe('old-secret');
});

it('allows updating s3 secret key', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $backupDisk = $user->currentTeam->backupDisks()->create([
        'name' => 'S3 Backup',
        'driver' => 's3',
        's3_bucket' => 'test-bucket',
        's3_access_key' => 'test-key',
        's3_secret_key' => 'old-secret',
        's3_region' => 'us-east-1',
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::backup-disks.edit', ['backupDisk' => $backupDisk])
        ->set('s3_secret_key', 'new-secret')
        ->call('update')
        ->assertRedirect(route('backup-disks.index'));

    expect($backupDisk->fresh()->s3_secret_key)->toBe('new-secret');
});

it('allows editing ftp configuration', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $backupDisk = $user->currentTeam->backupDisks()->create([
        'name' => 'FTP Backup',
        'driver' => 'ftp',
        'ftp_host' => 'ftp.old.com',
        'ftp_username' => 'olduser',
        'ftp_password' => 'oldpass',
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::backup-disks.edit', ['backupDisk' => $backupDisk])
        ->set('ftp_host', 'ftp.new.com')
        ->set('ftp_username', 'newuser')
        ->call('update')
        ->assertRedirect(route('backup-disks.index'));

    $disk = $backupDisk->fresh();
    expect($disk->ftp_host)->toBe('ftp.new.com');
    expect($disk->ftp_username)->toBe('newuser');
});

it('keeps existing ftp password when empty', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $backupDisk = $user->currentTeam->backupDisks()->create([
        'name' => 'FTP Backup',
        'driver' => 'ftp',
        'ftp_host' => 'ftp.example.com',
        'ftp_username' => 'user',
        'ftp_password' => 'oldpass',
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::backup-disks.edit', ['backupDisk' => $backupDisk])
        ->set('ftp_password', '')
        ->call('update')
        ->assertRedirect(route('backup-disks.index'));

    expect($backupDisk->fresh()->ftp_password)->toBe('oldpass');
});

it('allows editing sftp configuration with password', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $backupDisk = $user->currentTeam->backupDisks()->create([
        'name' => 'SFTP Backup',
        'driver' => 'sftp',
        'sftp_host' => 'sftp.old.com',
        'sftp_username' => 'olduser',
        'sftp_password' => 'oldpass',
        'sftp_use_server_key' => false,
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::backup-disks.edit', ['backupDisk' => $backupDisk])
        ->set('sftp_host', 'sftp.new.com')
        ->set('sftp_username', 'newuser')
        ->set('sftp_password', 'newpass')
        ->call('update')
        ->assertRedirect(route('backup-disks.index'));

    $disk = $backupDisk->fresh();
    expect($disk->sftp_host)->toBe('sftp.new.com');
    expect($disk->sftp_password)->toBe('newpass');
    expect($disk->sftp_use_server_key)->toBeFalse();
});

it('allows toggling sftp to use server key', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $backupDisk = $user->currentTeam->backupDisks()->create([
        'name' => 'SFTP Backup',
        'driver' => 'sftp',
        'sftp_host' => 'sftp.example.com',
        'sftp_username' => 'user',
        'sftp_password' => 'oldpass',
        'sftp_use_server_key' => false,
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::backup-disks.edit', ['backupDisk' => $backupDisk])
        ->set('sftp_use_server_key', true)
        ->call('update')
        ->assertRedirect(route('backup-disks.index'));

    $disk = $backupDisk->fresh();
    expect($disk->sftp_use_server_key)->toBeTrue();
    expect($disk->sftp_password)->toBeNull();
});

it('allows toggling sftp from server key to password', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $backupDisk = $user->currentTeam->backupDisks()->create([
        'name' => 'SFTP Backup',
        'driver' => 'sftp',
        'sftp_host' => 'sftp.example.com',
        'sftp_username' => 'user',
        'sftp_password' => null,
        'sftp_use_server_key' => true,
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::backup-disks.edit', ['backupDisk' => $backupDisk])
        ->set('sftp_use_server_key', false)
        ->set('sftp_password', 'newpass')
        ->call('update')
        ->assertRedirect(route('backup-disks.index'));

    $disk = $backupDisk->fresh();
    expect($disk->sftp_use_server_key)->toBeFalse();
    expect($disk->sftp_password)->toBe('newpass');
});

it('validates required sftp fields when not using server key', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $backupDisk = $user->currentTeam->backupDisks()->create([
        'name' => 'SFTP Backup',
        'driver' => 'sftp',
        'sftp_host' => 'sftp.example.com',
        'sftp_username' => 'user',
        'sftp_password' => 'oldpass',
        'sftp_use_server_key' => false,
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::backup-disks.edit', ['backupDisk' => $backupDisk])
        ->set('sftp_password', '')
        ->call('update')
        ->assertHasErrors(['sftp_password']);
});
