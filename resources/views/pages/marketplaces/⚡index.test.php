<?php

use App\Models\Marketplace;
use App\Models\Team;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('displays marketplaces for current team', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    get(route('marketplaces.index'))->assertOk();
});
