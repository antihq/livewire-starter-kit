<?php

use App\Models\Server;
use App\Models\Task;
use App\Models\Team;
use Illuminate\Support\Facades\Artisan;

it('can create a task', function () {
    $task = Task::factory()->create();

    expect($task->exists)->toBeTrue();
});

it('belongs to a team', function () {
    $task = Task::factory()->create();

    expect($task->team)->toBeInstanceOf(Team::class);
});

it('belongs to a server', function () {
    $task = Task::factory()->create();

    expect($task->server)->toBeInstanceOf(Server::class);
});

it('determines if successful', function () {
    $successful = Task::factory()->finished()->create();
    $failed = Task::factory()->failed()->create();

    expect($successful->successful())->toBeTrue()
        ->and($failed->successful())->toBeFalse();
});

it('gets timeout from options', function () {
    $task = Task::factory()->create(['options' => ['timeout' => 600]]);

    expect($task->timeout())->toBe(600);
});

it('uses default timeout when not in options', function () {
    $task = Task::factory()->create();

    expect($task->timeout())->toBe(3600);
});

it('generates signed callback URL', function () {
    $task = Task::factory()->create();

    $url = $task->callbackUrl();

    expect($url)->toContain('api/tasks/'.$task->id.'/callback')
        ->toContain('signature=');
});

it('prunes old tasks', function () {
    Task::factory()->create(['created_at' => now()->subDays(22)]);
    $recent = Task::factory()->create(['created_at' => now()->subDays(20)]);

    Artisan::call('model:prune');

    expect(Task::count())->toBe(1)
        ->and(Task::first()->id)->toBe($recent->id);
});
