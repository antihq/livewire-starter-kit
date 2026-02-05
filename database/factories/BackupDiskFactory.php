<?php

namespace Database\Factories;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BackupDiskFactory extends Factory
{
    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            'creator_id' => User::factory(),
            'name' => $this->faker->word(),
            'driver' => 's3',
        ];
    }

    public function s3(): static
    {
        return $this->state(fn (array $attributes) => [
            'driver' => 's3',
            's3_bucket' => $this->faker->word().'.'.$this->faker->domainName(),
            's3_access_key' => $this->faker->uuid(),
            's3_secret_key' => $this->faker->password(32),
            's3_region' => $this->faker->randomElement(['us-east-1', 'us-west-2', 'eu-west-1']),
            's3_use_path_style_endpoint' => false,
            's3_custom_endpoint' => null,
        ]);
    }

    public function ftp(): static
    {
        return $this->state(fn (array $attributes) => [
            'driver' => 'ftp',
            'ftp_host' => $this->faker->ipv4(),
            'ftp_username' => $this->faker->userName(),
            'ftp_password' => $this->faker->password(),
        ]);
    }

    public function sftp(): static
    {
        return $this->state(fn (array $attributes) => [
            'driver' => 'sftp',
            'sftp_host' => $this->faker->ipv4(),
            'sftp_username' => $this->faker->userName(),
            'sftp_password' => $this->faker->password(),
            'sftp_use_server_key' => false,
        ]);
    }

    public function sftpWithServerKey(): static
    {
        return $this->state(fn (array $attributes) => [
            'driver' => 'sftp',
            'sftp_host' => $this->faker->ipv4(),
            'sftp_username' => $this->faker->userName(),
            'sftp_password' => null,
            'sftp_use_server_key' => true,
        ]);
    }
}
