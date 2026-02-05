<?php

namespace Database\Factories;

use App\Models\Server;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FirewallRule>
 */
class FirewallRuleFactory extends Factory
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
            'name' => $this->faker->word(),
            'action' => $this->faker->randomElement(['allow', 'deny', 'reject']),
            'port' => $this->faker->numberBetween(1, 65535),
            'from_ip' => $this->faker->optional()->ipv4(),
        ];
    }
}
