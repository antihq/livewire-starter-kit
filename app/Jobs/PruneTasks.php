<?php

namespace App\Jobs;

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class PruneTasks implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        Task::prune(Carbon::now()->subDays(21));
    }
}
