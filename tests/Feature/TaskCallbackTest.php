<?php

use App\Jobs\FinishTask;
use App\Models\Task;
use Illuminate\Support\Facades\Queue;

it('handles task callback with valid signed URL', function () {
    $task = Task::factory()->running()->create();
    $url = $task->callbackUrl();

    Queue::fake();

    $this->post($url, ['exit_code' => 0])->assertStatus(200);

    Queue::assertPushed(FinishTask::class, fn ($job) => $job->exitCode === 0);
});

it('rejects callbacks for non-running tasks', function () {
    $task = Task::factory()->finished()->create();
    $url = $task->callbackUrl();

    $this->post($url, ['exit_code' => 0])->assertStatus(404);
});

it('rejects invalid signed URLs', function () {
    $task = Task::factory()->create();
    $url = url('/api/tasks/'.$task->id.'/callback?signature=invalid');

    $this->post($url, ['exit_code' => 0])->assertStatus(404);
});
