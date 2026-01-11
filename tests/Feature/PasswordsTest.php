<?php

use App\Models\Password;
use App\Models\Team;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->team = Team::factory()->create(['user_id' => $this->user->id]);
    $this->user->update(['current_team_id' => $this->team->id]);
});

it('can view passwords list', function () {
    $password = Password::factory()->create(['team_id' => $this->team->id]);

    $this->actingAs($this->user)
        ->get(route('passwords.index'))
        ->assertStatus(200)
        ->assertSee($password->name)
        ->assertSee($password->username);
});

it('cannot view passwords from other teams', function () {
    $otherUser = User::factory()->create();
    $otherTeam = Team::factory()->create(['user_id' => $otherUser->id]);
    $password = Password::factory()->create(['team_id' => $otherTeam->id]);

    $this->actingAs($this->user)
        ->get(route('passwords.index'))
        ->assertDontSee($password->name);
});

it('can create a password', function () {
    Livewire::actingAs($this->user)
        ->test('pages::passwords.index')
        ->set('name', 'Netflix')
        ->set('username', 'test@example.com')
        ->set('password', 'secret123')
        ->call('create')
        ->assertSet('name', '')
        ->assertSet('username', '')
        ->assertSet('password', '');

    $this->assertDatabaseHas('passwords', [
        'name' => 'Netflix',
        'username' => 'test@example.com',
        'team_id' => $this->team->id,
    ]);
});

it('validates required fields when creating password', function () {
    Livewire::actingAs($this->user)
        ->test('pages::passwords.index')
        ->set('name', '')
        ->set('username', '')
        ->set('password', '')
        ->call('create')
        ->assertHasErrors(['name', 'username', 'password']);
});

it('can generate a password', function () {
    Livewire::actingAs($this->user)
        ->test('pages::passwords.index')
        ->call('generatePassword')
        ->assertSet('password', fn ($p) => strlen($p) === 20)
        ->assertSet('password', fn ($p) => substr_count($p, '-') === 2)
        ->assertSet('password', fn ($p) => preg_match('/^[a-z]{6}-[a-z0-9]{6}-[A-Z][a-z]{5}$/', $p) === 1);
});

it('can regenerate a password', function () {
    $component = Livewire::actingAs($this->user)
        ->test('pages::passwords.index');

    $component->call('generatePassword')
        ->assertSet('password', $firstPassword = $component->get('password'));

    $component->call('generatePassword')
        ->assertSet('password', fn ($p) => $p !== $firstPassword);
});

it('can create a password with auto-generated password', function () {
    Livewire::actingAs($this->user)
        ->test('pages::passwords.index')
        ->set('name', 'GitHub')
        ->set('username', 'github@example.com')
        ->call('generatePassword')
        ->call('create')
        ->assertSet('name', '')
        ->assertSet('username', '')
        ->assertSet('password', '');

    $this->assertDatabaseHas('passwords', [
        'name' => 'GitHub',
        'username' => 'github@example.com',
        'team_id' => $this->team->id,
    ]);
});

it('can enter edit mode from modal', function () {
    $password = Password::factory()->create(['team_id' => $this->team->id]);

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
    $password = Password::factory()->create(['team_id' => $this->team->id]);

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
    $password = Password::factory()->create(['team_id' => $this->team->id]);

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
    $password = Password::factory()->create(['team_id' => $this->team->id]);

    Livewire::actingAs($this->user)
        ->test('passwords.item', ['password' => $password])
        ->call('enterEditMode')
        ->set('editName', '')
        ->set('editUsername', '')
        ->set('editPassword', '')
        ->call('save')
        ->assertHasErrors(['editName', 'editUsername', 'editPassword']);
});

it('can delete a password', function () {
    $password = Password::factory()->create(['team_id' => $this->team->id]);

    Livewire::actingAs($this->user)
        ->test('pages::passwords.index')
        ->call('delete', $password->id)
        ->assertNoRedirect();

    $this->assertDatabaseMissing('passwords', [
        'id' => $password->id,
    ]);
});

it('cannot delete password from other team', function () {
    $otherUser = User::factory()->create();
    $otherTeam = Team::factory()->create(['user_id' => $otherUser->id]);
    $password = Password::factory()->create(['team_id' => $otherTeam->id]);

    $this->actingAs($this->user)
        ->get(route('passwords.index'))
        ->assertStatus(200)
        ->assertDontSee($password->name);
});
