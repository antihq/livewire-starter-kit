<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Site>
 */
class SiteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'hostname' => fake()->domainName(),
            'php_version' => fake()->randomElement(['8.1', '8.2', '8.3', '8.4', '8.5']),
            'site_type' => fake()->randomElement(['generic', 'laravel', 'static']),
            'zero_downtime_deployments' => fake()->boolean(),
            'web_folder' => fake()->randomElement(['/public', '/www', '/var/www/html']),
            'repository_url' => 'https://github.com/'.fake()->userName().'/'.fake()->word().'.git',
            'repository_branch' => fake()->randomElement(['main', 'master', 'develop', 'staging']),
        ];
    }
}
