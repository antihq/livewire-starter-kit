<?php

namespace App\Http\Controllers\Api;

use App\Jobs\FinishTask;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TaskCallbackController extends Controller
{
    public function handle(Request $request, Task $task): void
    {
        abort_unless($task->status === 'running', 404);

        FinishTask::dispatch($task, (int) $request->query('exit_code'));
    }
}
