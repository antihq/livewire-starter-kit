<?php

use App\Actions\Fortify\CreateNewUser;
use App\Models\User;
use App\Notifications\WelcomeNotification;
use Illuminate\Support\Facades\Notification;

use function Pest\Laravel\assertAuthenticated;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

it('can render registration screen', function () {
    $response = get(route('register'));

    $response->assertStatus(200);
});

it('can register new users', function () {
    Notification::fake();

    $response = post(route('register.store'), [
        'email' => 'test@example.com',
    ]);

    $response->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    assertAuthenticated();

    Notification::assertSentTo(
        User::where('email', 'test@example.com')->first(),
        WelcomeNotification::class
    );
});

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
