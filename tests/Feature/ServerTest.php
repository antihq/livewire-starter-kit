<?php

use App\Jobs\ProvisionServer;
use App\Models\Server;
use App\Models\Team;
use App\Scripts\ProvisionServer as ProvisionServerScript;
use Illuminate\Support\Facades\Queue;

beforeEach(function () {
    Queue::fake();
});

it('dispatches ProvisionServer job and updates timestamp', function () {
    $team = Team::factory()->create();
    $server = Server::factory()->for($team)->create([
        'status' => 'pending',
        'public_ip' => '192.168.1.1',
    ]);

    expect($server->provisioning_job_dispatched_at)->toBeNull();

    $server->provision();

    Queue::assertPushed(ProvisionServer::class, function (ProvisionServer $job) use ($server) {
        return $job->server->is($server);
    });

    expect($server->fresh()->provisioning_job_dispatched_at)->not->toBeNull();
});

it('is ready for provisioning when public_ip exists', function () {
    $team = Team::factory()->create();
    $server = Server::factory()->for($team)->create([
        'public_ip' => '192.168.1.1',
    ]);

    expect($server->isReadyForProvisioning())->toBeTrue();
});

it('is provisioning when status is provisioning', function () {
    $team = Team::factory()->create();
    $server = Server::factory()->for($team)->create([
        'status' => 'provisioning',
    ]);

    expect($server->isProvisioning())->toBeTrue();
});

it('is provisioned when status is provisioned', function () {
    $team = Team::factory()->create();
    $server = Server::factory()->for($team)->create([
        'status' => 'provisioned',
    ]);

    expect($server->isProvisioned())->toBeTrue();
});

it('can be marked as provisioning', function () {
    $team = Team::factory()->create();
    $server = Server::factory()->for($team)->create([
        'status' => 'pending',
    ]);

    $server->markAsProvisioning();

    expect($server->fresh()->status)->toBe('provisioning');
});

it('knows when provisioning job has been dispatched', function () {
    $team = Team::factory()->create();
    $server = Server::factory()->for($team)->create([
        'provisioning_job_dispatched_at' => now(),
    ]);

    expect($server->provisioningJobDispatched())->toBeTrue();
});

it('returns ProvisionServer instance', function () {
    $team = Team::factory()->create();
    $server = Server::factory()->for($team)->create();

    $script = $server->provisioningScript();

    expect($script)->toBeInstanceOf(ProvisionServerScript::class);
    expect($script->name())->toContain($server->name);
});

it('is older than given minutes', function () {
    $team = Team::factory()->create();
    $server = Server::factory()->for($team)->create([
        'created_at' => now()->subMinutes(20),
    ]);

    expect($server->olderThan(15))->toBeTrue();
});

it('is not older than given minutes', function () {
    $team = Team::factory()->create();
    $server = Server::factory()->for($team)->create([
        'created_at' => now()->subMinutes(10),
    ]);

    expect($server->olderThan(15))->toBeFalse();
});
