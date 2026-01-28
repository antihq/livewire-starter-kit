<?php

namespace Database\Factories;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    protected $model = Message::class;

    public function definition(): array
    {
        $conversation = Conversation::factory()->create();

        return [
            'conversation_id' => $conversation->id,
            'user_id' => User::factory(),
            'team_id' => $conversation->team_id,
            'content' => fake()->paragraph(),
        ];
    }
}
