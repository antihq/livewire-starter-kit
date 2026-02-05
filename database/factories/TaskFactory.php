<?php

namespace Database\Factories;

use App\Models\Server;
use App\Models\Task;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            'server_id' => Server::factory(),
            'name' => 'Test Task',
            'user' => 'fuse',
            'status' => 'pending',
            'exit_code' => null,
            'script' => '#!/bin/bash'.PHP_EOL.'echo "Hello World"',
            'output' => null,
            'options' => [],
        ];
    }

    public function running(): static
    {
        return $this->state(['status' => 'running']);
    }

    public function finished(): static
    {
        return $this->state(['status' => 'finished', 'exit_code' => 0, 'output' => 'Done']);
    }

    public function failed(): static
    {
        return $this->state(['status' => 'finished', 'exit_code' => 1, 'output' => 'Error']);
    }

    public function timeout(): static
    {
        return $this->state(['status' => 'timeout', 'exit_code' => 1, 'output' => 'Timed out']);
    }
}
