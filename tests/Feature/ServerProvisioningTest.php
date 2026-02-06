<?php

use App\Models\Server;
use App\Models\Team;
use App\Models\User;

test('server provision script url returns signed url', function () {
    $user = User::factory()->create();
    $team = Team::factory()->create([
        'user_id' => $user->id,
    ]);
    $server = Server::factory()->create([
        'team_id' => $team->id,
        'status' => 'pending',
    ]);

    $url = $server->provisionScriptUrl();

    expect($url)->toBeString()
        ->and($url)->toContain('provision-script')
        ->and($url)->toContain('signature=');
});

test('server provision command returns wget command', function () {
    $user = User::factory()->create();
    $team = Team::factory()->create([
        'user_id' => $user->id,
    ]);
    $server = Server::factory()->create([
        'team_id' => $team->id,
        'status' => 'pending',
    ]);

    $command = $server->provisionCommand();

    expect($command->toHtml())->toContain('wget --no-verbose -O -')
        ->and($command->toHtml())->toContain($server->provisionScriptUrl())
        ->and($command->toHtml())->toContain('| bash');
});

test('provision script endpoint returns bash script for pending server', function () {
    $user = User::factory()->create();
    $team = Team::factory()->create([
        'user_id' => $user->id,
        'public_key' => 'ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABAQC test@example.com',
    ]);
    $server = Server::factory()->create([
        'team_id' => $team->id,
        'status' => 'pending',
    ]);

    $url = $server->provisionScriptUrl();

    $response = $this->get($url);

    $response->assertStatus(200)
        ->assertHeader('content-type', 'text/plain; charset=UTF-8')
        ->assertSee('set -e')
        ->assertSee('Setup SSH keys for root')
        ->assertSee('/root/.ssh/authorized_keys')
        ->assertSee($team->public_key)
        ->assertSee('chmod 700 /root/.ssh')
        ->assertSee('chmod 600 /root/.ssh/authorized_keys');
});

test('provision script endpoint returns 404 for provisioned server', function () {
    $user = User::factory()->create();
    $team = Team::factory()->create([
        'user_id' => $user->id,
    ]);
    $server = Server::factory()->create([
        'team_id' => $team->id,
        'status' => 'provisioned',
    ]);

    $url = $server->provisionScriptUrl();

    $this->get($url)->assertStatus(404);
});

test('provision script endpoint requires valid signature', function () {
    $user = User::factory()->create();
    $team = Team::factory()->create([
        'user_id' => $user->id,
    ]);
    $server = Server::factory()->create([
        'team_id' => $team->id,
        'status' => 'pending',
    ]);

    $this->get(route('servers.provision-script', $server))
        ->assertStatus(403);
});

test('server show page displays provision command when pending', function () {
    $user = User::factory()->create();
    $team = Team::factory()->create([
        'user_id' => $user->id,
        'public_key' => 'ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABAQC test@example.com',
    ]);
    $server = Server::factory()->create([
        'team_id' => $team->id,
        'status' => 'pending',
    ]);

    $this->actingAs($user)
        ->get(route('servers.show', $server))
        ->assertStatus(200)
        ->assertSee('Provision Server')
        ->assertSee('Run this command as root on your server to authorize Fuse to manage it')
        ->assertSee('wget --no-verbose -O -')
        ->assertSee('| bash');
});

test('server show page does not display provision command when provisioned', function () {
    $user = User::factory()->create();
    $team = Team::factory()->create([
        'user_id' => $user->id,
    ]);
    $server = Server::factory()->create([
        'team_id' => $team->id,
        'status' => 'provisioned',
    ]);

    $this->actingAs($user)
        ->get(route('servers.show', $server))
        ->assertStatus(200)
        ->assertDontSee('Provision Server')
        ->assertDontSee('Run this command as root on your server to authorize Fuse to manage it');
});

test('authorize server key script uses team public key', function () {
    $user = User::factory()->create();
    $team = Team::factory()->create([
        'user_id' => $user->id,
        'public_key' => 'ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABAQC test@example.com',
    ]);
    $server = Server::factory()->create([
        'team_id' => $team->id,
        'status' => 'pending',
    ]);

    $url = $server->provisionScriptUrl();

    $response = $this->get($url);

    $response->assertStatus(200)
        ->assertSee('ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABAQC test@example.com');
});
