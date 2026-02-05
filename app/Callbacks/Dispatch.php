<?php

namespace App\Callbacks;

use App\Models\Task;

class Dispatch
{
    public function __construct(public string $class) {}

    public function handle(Task $task): void
    {
        if ($task->server && $task->successful()) {
            dispatch(new $this->class($task->server));
        }
    }
}
