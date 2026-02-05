<?php

namespace Database\Factories;

use App\Models\Server;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Daemon>
 */
class DaemonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            'creator_id' => User::factory(),
            'server_id' => Server::factory(),
            'command' => $this->faker->command(),
            'directory' => $this->faker->optional()->filePath(),
            'user' => 'fuse',
            'processes' => 1,
            'stop_wait_seconds' => 10,
            'stop_signal' => 'TERM',
        ];
    }
}
