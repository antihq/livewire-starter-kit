<?php

use App\Models\Password;
use App\Models\Team;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->user = User::factory()->withPersonalTeam()->create();
});

it('can enter edit mode from modal', function () {
    $password = Password::factory()->create(['team_id' => $this->user->currentTeam->id]);

    $component = Livewire::actingAs($this->user)
        ->test('passwords.item', ['password' => $password]);

    $component->assertSet('isEditing', false);

    $component->call('enterEditMode');

    $component->assertSet('isEditing', true)
        ->assertSet('editName', $password->name)
        ->assertSet('editUsername', $password->username)
        ->assertSet('editPassword', $password->password);
});

it('can cancel edit mode', function () {
    $password = Password::factory()->create(['team_id' => $this->user->currentTeam->id]);

    $component = Livewire::actingAs($this->user)
        ->test('passwords.item', ['password' => $password]);

    $component->call('enterEditMode')
        ->assertSet('isEditing', true);

    $component->call('cancelEdit');

    $component->assertSet('isEditing', false)
        ->assertSet('editName', '')
        ->assertSet('editUsername', '')
        ->assertSet('editPassword', '');
});

it('can update password from modal', function () {
    $password = Password::factory()->create(['team_id' => $this->user->currentTeam->id]);

    Livewire::actingAs($this->user)
        ->test('passwords.item', ['password' => $password])
        ->call('enterEditMode')
        ->set('editName', 'Updated Name')
        ->set('editUsername', 'updated@example.com')
        ->set('editPassword', 'newpassword123')
        ->call('save')
        ->assertSet('isEditing', false);

    $this->assertDatabaseHas('passwords', [
        'id' => $password->id,
        'name' => 'Updated Name',
        'username' => 'updated@example.com',
    ]);

    $password->refresh();
    expect($password->password)->toBe('newpassword123');
});

it('validates required fields when updating password', function () {
    $password = Password::factory()->create(['team_id' => $this->user->currentTeam->id]);

    Livewire::actingAs($this->user)
        ->test('passwords.item', ['password' => $password])
        ->call('enterEditMode')
        ->set('editName', '')
        ->set('editUsername', '')
        ->set('editPassword', '')
        ->call('save')
        ->assertHasErrors(['editName', 'editUsername', 'editPassword']);
});
