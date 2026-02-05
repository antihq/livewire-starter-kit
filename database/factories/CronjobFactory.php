<?php

namespace Database\Factories;

use App\Models\Server;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CronjobFactory extends Factory
{
    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            'creator_id' => User::factory(),
            'server_id' => Server::factory(),
            'command' => $this->faker->command(),
            'user' => 'fuse',
            'frequency' => 'daily',
        ];
    }
}
