<?php

namespace App\Callbacks;

use App\Models\Task;

class MarkAsProvisioned
{
    public function handle(Task $task): void
    {
        if ($task->server && $task->successful()) {
            $task->server->markAsProvisioned();
        }
    }
}
