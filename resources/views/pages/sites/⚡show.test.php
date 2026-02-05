<?php

use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

it('allows deleting site from show page', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    $site = $user->currentTeam->sites()->create([
        'hostname' => 'example.com',
        'server_id' => $server->id,
        'php_version' => '8.3',
        'site_type' => 'laravel',
        'zero_downtime_deployments' => false,
        'web_folder' => '/public',
        'repository_url' => 'https://github.com/user/repo.git',
        'repository_branch' => 'main',
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::sites.show', ['site' => $site])
        ->call('delete')
        ->assertRedirect(route('servers.sites.index', $server->id));

    expect($user->currentTeam->fresh()->sites)->toHaveCount(0);
});
