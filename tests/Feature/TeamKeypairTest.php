<?php

use App\Actions\Fortify\CreateNewUser;
use App\Models\Team;
use App\Models\User;

it('creates team with ssh keypair', function () {
    $creator = new CreateNewUser;

    $user = $creator->create([
        'email' => 'test@example.com',
    ]);

    expect($user)->toBeInstanceOf(User::class)
        ->and($user->ownedTeams)->toHaveCount(1);

    $team = $user->fresh()->ownedTeams->first();

    expect($team->public_key)->not->toBeEmpty()
        ->and($team->public_key)->toContain('ssh-rsa')
        ->and($team->public_key)->toContain('fuse@antihq.com')
        ->and($team->private_key)->not->toBeEmpty()
        ->and($team->private_key)->toContain('BEGIN OPENSSH PRIVATE KEY');
});

it('team keypair mutator maps object to database columns', function () {
    $keypair = (object) [
        'publicKey' => 'test-public-key',
        'privateKey' => 'test-private-key',
    ];

    $team = new Team;
    $team->name = 'Test Team';
    $team->user_id = 1;
    $team->personal_team = true;
    $team->public_key = $keypair->publicKey;
    $team->private_key = $keypair->privateKey;
    $team->save();

    expect($team->public_key)->toBe('test-public-key')
        ->and($team->private_key)->toBe('test-private-key');
});

it('encrypts private key automatically', function () {
    $keypair = (object) [
        'publicKey' => 'test-public-key',
        'privateKey' => 'test-private-key',
    ];

    $team = new Team;
    $team->name = 'Test Team 2';
    $team->user_id = 1;
    $team->personal_team = true;
    $team->public_key = $keypair->publicKey;
    $team->private_key = $keypair->privateKey;
    $team->save();

    expect($team->private_key)->toBe('test-private-key')
        ->and($team->getAttributes()['private_key'])->not->toBe('test-private-key');
});
