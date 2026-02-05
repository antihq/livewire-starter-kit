<?php

use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

it('creates s3 backup disk', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    Livewire::test('pages::backup-disks.create')
        ->set('name', 'Production S3')
        ->set('driver', 's3')
        ->set('s3_bucket', 'my-backups')
        ->set('s3_access_key', 'AKIAIOSFODNN7EXAMPLE')
        ->set('s3_secret_key', 'wJalrXUtnFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY')
        ->set('s3_region', 'us-east-1')
        ->call('create')
        ->assertHasNoErrors()
        ->assertRedirect(route('backup-disks.index'));

    $disk = $user->currentTeam->fresh()->backupDisks->first();
    expect($disk->name)->toBe('Production S3');
    expect($disk->driver)->toBe('s3');
    expect($disk->s3_bucket)->toBe('my-backups');
    expect($disk->s3_access_key)->toBe('AKIAIOSFODNN7EXAMPLE');
    expect($disk->s3_region)->toBe('us-east-1');
});

it('creates s3 backup disk with optional fields', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    Livewire::test('pages::backup-disks.create')
        ->set('name', 'Custom S3')
        ->set('driver', 's3')
        ->set('s3_bucket', 'my-backups')
        ->set('s3_access_key', 'AKIAIOSFODNN7EXAMPLE')
        ->set('s3_secret_key', 'wJalrXUtnFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY')
        ->set('s3_region', 'eu-west-1')
        ->set('s3_use_path_style_endpoint', true)
        ->set('s3_custom_endpoint', 'https://s3.example.com')
        ->call('create')
        ->assertHasNoErrors()
        ->assertRedirect(route('backup-disks.index'));

    $disk = $user->currentTeam->fresh()->backupDisks->first();
    expect($disk->s3_use_path_style_endpoint)->toBeTrue();
    expect($disk->s3_custom_endpoint)->toBe('https://s3.example.com');
});

it('creates ftp backup disk', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    Livewire::test('pages::backup-disks.create')
        ->set('name', 'FTP Backup')
        ->set('driver', 'ftp')
        ->set('ftp_host', 'ftp.example.com')
        ->set('ftp_username', 'backupuser')
        ->set('ftp_password', 'secret123')
        ->call('create')
        ->assertHasNoErrors()
        ->assertRedirect(route('backup-disks.index'));

    $disk = $user->currentTeam->fresh()->backupDisks->first();
    expect($disk->name)->toBe('FTP Backup');
    expect($disk->driver)->toBe('ftp');
    expect($disk->ftp_host)->toBe('ftp.example.com');
    expect($disk->ftp_username)->toBe('backupuser');
});

it('creates sftp backup disk with password', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    Livewire::test('pages::backup-disks.create')
        ->set('name', 'SFTP Backup')
        ->set('driver', 'sftp')
        ->set('sftp_host', 'sftp.example.com')
        ->set('sftp_username', 'backupuser')
        ->set('sftp_password', 'secret123')
        ->set('sftp_use_server_key', false)
        ->call('create')
        ->assertHasNoErrors()
        ->assertRedirect(route('backup-disks.index'));

    $disk = $user->currentTeam->fresh()->backupDisks->first();
    expect($disk->name)->toBe('SFTP Backup');
    expect($disk->driver)->toBe('sftp');
    expect($disk->sftp_host)->toBe('sftp.example.com');
    expect($disk->sftp_use_server_key)->toBeFalse();
});

it('creates sftp backup disk with server key', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    Livewire::test('pages::backup-disks.create')
        ->set('name', 'SFTP Server Key')
        ->set('driver', 'sftp')
        ->set('sftp_host', 'sftp.example.com')
        ->set('sftp_username', 'backupuser')
        ->set('sftp_password', '')
        ->set('sftp_use_server_key', true)
        ->call('create')
        ->assertHasNoErrors()
        ->assertRedirect(route('backup-disks.index'));

    $disk = $user->currentTeam->fresh()->backupDisks->first();
    expect($disk->name)->toBe('SFTP Server Key');
    expect($disk->driver)->toBe('sftp');
    expect($disk->sftp_use_server_key)->toBeTrue();
    expect($disk->sftp_password)->toBeNull();
});

it('validates required fields for s3', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    Livewire::test('pages::backup-disks.create')
        ->set('name', 'Test')
        ->set('driver', 's3')
        ->call('create')
        ->assertHasErrors(['s3_bucket', 's3_access_key', 's3_secret_key', 's3_region']);
});

it('validates required fields for ftp', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    Livewire::test('pages::backup-disks.create')
        ->set('name', 'Test')
        ->set('driver', 'ftp')
        ->call('create')
        ->assertHasErrors(['ftp_host', 'ftp_username', 'ftp_password']);
});

it('validates sftp requires password when not using server key', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    Livewire::test('pages::backup-disks.create')
        ->set('name', 'Test')
        ->set('driver', 'sftp')
        ->set('sftp_host', 'sftp.example.com')
        ->set('sftp_username', 'user')
        ->set('sftp_use_server_key', false)
        ->call('create')
        ->assertHasErrors(['sftp_password']);
});

it('does not require sftp password when using server key', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    Livewire::test('pages::backup-disks.create')
        ->set('name', 'Test')
        ->set('driver', 'sftp')
        ->set('sftp_host', 'sftp.example.com')
        ->set('sftp_username', 'user')
        ->set('sftp_use_server_key', true)
        ->call('create')
        ->assertHasNoErrors();
});
