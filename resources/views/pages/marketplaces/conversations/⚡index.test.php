<?php

use App\Models\Conversation;
use App\Models\Listing;
use App\Models\Marketplace;
use App\Models\Message;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

it('authorizes marketplace view', function () {
    actingAs($user = User::factory()->withPersonalTeam()->create());

    $marketplace = Marketplace::factory()->create();

    Livewire::test('pages::marketplaces.conversations.index', ['marketplace' => $marketplace])
        ->assertForbidden();
});
