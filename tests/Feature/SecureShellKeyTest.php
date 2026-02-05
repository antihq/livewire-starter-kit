<?php

use App\Support\SecureShellKey;

it('generates a valid ssh keypair', function () {
    $keypair = SecureShellKey::make();

    expect($keypair)->toHaveProperty('publicKey')
        ->and($keypair)->toHaveProperty('privateKey')
        ->and($keypair->publicKey)->not->toBeEmpty()
        ->and($keypair->privateKey)->not->toBeEmpty()
        ->and($keypair->publicKey)->toContain('ssh-rsa')
        ->and($keypair->publicKey)->toContain('fuse@antihq.com');
});

it('generates a keypair with a password', function () {
    $keypair = SecureShellKey::make('test-password');

    expect($keypair)->toHaveProperty('publicKey')
        ->and($keypair)->toHaveProperty('privateKey')
        ->and($keypair->publicKey)->not->toBeEmpty()
        ->and($keypair->privateKey)->not->toBeEmpty()
        ->and($keypair->publicKey)->toContain('ssh-rsa')
        ->and($keypair->publicKey)->toContain('fuse@antihq.com');
});

it('creates a 4096 bit rsa key', function () {
    $keypair = SecureShellKey::make();

    expect($keypair->publicKey)->toContain('ssh-rsa')
        ->and($keypair->privateKey)->toContain('BEGIN OPENSSH PRIVATE KEY');
});
