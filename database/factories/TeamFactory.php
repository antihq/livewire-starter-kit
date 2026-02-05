<?php

namespace Database\Factories;

use App\Models\User;
use App\Support\SecureShellKey;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Team>
 */
class TeamFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $keypair = SecureShellKey::make();

        return [
            'name' => $this->faker->unique()->company(),
            'user_id' => User::factory(),
            'personal_team' => true,
            'public_key' => $keypair->publicKey,
            'private_key' => $keypair->privateKey,
        ];
    }
}
