<?php

use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

it('allows deleting sites', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    $site = $server->sites()->create([
        'hostname' => 'example.com',
        'php_version' => '8.3',
        'site_type' => 'laravel',
        'zero_downtime_deployments' => false,
        'web_folder' => '/public',
        'repository_url' => 'https://github.com/user/repo.git',
        'repository_branch' => 'main',
        'team_id' => $user->currentTeam->id,
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::servers.sites.index', ['server' => $server])
        ->call('delete', $site->id)
        ->assertHasNoErrors();

    expect($server->fresh()->sites)->toHaveCount(0);
});
