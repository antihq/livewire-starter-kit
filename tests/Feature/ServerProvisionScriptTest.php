<?php

use App\Models\Server;
use App\Models\Team;
use App\Models\User;

use function Pest\Laravel\get;

it('returns a bash script for pending servers', function () {
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

    $response = get($url);

    $response->assertStatus(200)
        ->assertHeader('content-type', 'text/plain; charset=UTF-8')
        ->assertSee('set -e')
        ->assertSee('Setup SSH keys for root')
        ->assertSee('/root/.ssh/authorized_keys')
        ->assertSee($team->public_key)
        ->assertSee('chmod 700 /root/.ssh')
        ->assertSee('chmod 600 /root/.ssh/authorized_keys');
});

it('requires a valid signature for the provision script endpoint', function () {
    $user = User::factory()->create();
    $team = Team::factory()->create([
        'user_id' => $user->id,
    ]);
    $server = Server::factory()->create([
        'team_id' => $team->id,
        'status' => 'pending',
    ]);

    get(route('servers.provision-script', $server))
        ->assertStatus(403);
});
