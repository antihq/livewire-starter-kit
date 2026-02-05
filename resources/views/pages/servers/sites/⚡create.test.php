<?php

use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

it('creates site with all required fields', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::servers.sites.create', ['server' => $server])
        ->set('hostname', 'example.com')
        ->set('phpVersion', '8.3')
        ->set('siteType', 'laravel')
        ->set('zeroDowntimeDeployments', true)
        ->set('webFolder', '/public')
        ->set('repositoryUrl', 'https://github.com/user/repo.git')
        ->set('repositoryBranch', 'main')
        ->call('create')
        ->assertRedirect(route('servers.show', $server->id));

    expect($user->currentTeam->fresh()->sites)->toHaveCount(1);
    expect($user->currentTeam->fresh()->sites->first()->hostname)->toBe('example.com');
    expect($user->currentTeam->fresh()->sites->first()->server_id)->toBe($server->id);
    expect($user->currentTeam->fresh()->sites->first()->php_version)->toBe('8.3');
    expect($user->currentTeam->fresh()->sites->first()->site_type)->toBe('laravel');
    expect($user->currentTeam->fresh()->sites->first()->zero_downtime_deployments)->toBe(true);
    expect($user->currentTeam->fresh()->sites->first()->web_folder)->toBe('/public');
    expect($user->currentTeam->fresh()->sites->first()->repository_url)->toBe('https://github.com/user/repo.git');
    expect($user->currentTeam->fresh()->sites->first()->repository_branch)->toBe('main');
    expect($user->currentTeam->fresh()->sites->first()->creator_id)->toBe($user->id);
});
