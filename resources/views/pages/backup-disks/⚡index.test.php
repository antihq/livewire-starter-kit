<?php

use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

it('allows deleting backup disks', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $backupDisk = $user->currentTeam->backupDisks()->create([
        'name' => 'Test Backup',
        'driver' => 's3',
        's3_bucket' => 'test-bucket',
        's3_access_key' => 'test-key',
        's3_secret_key' => 'test-secret',
        's3_region' => 'us-east-1',
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::backup-disks.index')
        ->call('delete', $backupDisk->id)
        ->assertHasNoErrors();

    expect($user->currentTeam->fresh()->backupDisks)->toHaveCount(0);
});

it('prevents unauthorized users from deleting disks from other teams', function () {
    $user1 = User::factory()->withPersonalTeam()->create();
    $user2 = User::factory()->withPersonalTeam()->create();

    $backupDisk = $user1->currentTeam->backupDisks()->create([
        'name' => 'Test Backup',
        'driver' => 's3',
        's3_bucket' => 'test-bucket',
        's3_access_key' => 'test-key',
        's3_secret_key' => 'test-secret',
        's3_region' => 'us-east-1',
        'creator_id' => $user1->id,
    ]);

    expect(fn () => Livewire::actingAs($user2)->test('pages::backup-disks.index')->call('delete', $backupDisk->id))
        ->toThrow(ModelNotFoundException::class);
});

it('lists backup disks', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $disk1 = $user->currentTeam->backupDisks()->create([
        'name' => 'S3 Backup',
        'driver' => 's3',
        's3_bucket' => 'bucket1',
        's3_access_key' => 'key1',
        's3_secret_key' => 'secret1',
        's3_region' => 'us-east-1',
        'creator_id' => $user->id,
    ]);

    $disk2 = $user->currentTeam->backupDisks()->create([
        'name' => 'FTP Backup',
        'driver' => 'ftp',
        'ftp_host' => 'ftp.example.com',
        'ftp_username' => 'user1',
        'ftp_password' => 'password1',
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::backup-disks.index')
        ->assertOk()
        ->assertSee('S3 Backup')
        ->assertSee('FTP Backup')
        ->assertSee('S3')
        ->assertSee('FTP');
});
