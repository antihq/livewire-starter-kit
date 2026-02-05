<?php

use App\Models\Server;
use App\Scripts\Script;
use Illuminate\Support\Facades\Process;

it('can run a synchronous task on server', function () {
    $server = Server::factory()->create();

    $script = new class extends Script
    {
        public function name(): string
        {
            return 'test';
        }

        public function script(): string
        {
            return 'echo "test"';
        }
    };

    Process::fake(['*' => Process::result('test', '', 0)]);

    $task = $server->run($script);

    expect($task->status)->toBe('finished')
        ->and($task->output)->toContain('test');
});

it('can run a background task on server', function () {
    $server = Server::factory()->create();

    $script = new class extends Script
    {
        public function name(): string
        {
            return 'test';
        }

        public function script(): string
        {
            return 'echo "test"';
        }
    };

    Process::fake(['*' => Process::result('', '', 0)]);

    $task = $server->runInBackground($script);

    expect($task->status)->toBe('running');
});

it('handles task failure', function () {
    $server = Server::factory()->create();

    $script = new class extends Script
    {
        public function name(): string
        {
            return 'test';
        }

        public function script(): string
        {
            return 'exit 1';
        }
    };

    Process::fake(['*' => Process::result('Error', 'Command failed', 1)]);

    $task = $server->run($script);

    expect($task->status)->toBe('finished')
        ->and($task->exit_code)->toBe(1)
        ->and($task->successful())->toBeFalse();
});
