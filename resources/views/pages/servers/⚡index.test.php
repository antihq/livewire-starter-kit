<?php

use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

it('allows deleting servers', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $server = $user->currentTeam->servers()->create([
        'name' => 'Test Server',
        'public_ip' => '192.168.1.100',
        'creator_id' => $user->id,
    ]);

    Livewire::test('pages::servers.index')
        ->call('delete', $server->id)
        ->assertHasNoErrors();

    expect($user->currentTeam->fresh()->servers)->toHaveCount(0);
});
