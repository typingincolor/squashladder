<?php

namespace Database\Factories;

use App\Models\Challenge;
use App\Models\Player;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChallengeFactory extends Factory
{
    protected $model = Challenge::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'challenger_id' => Player::factory(),
            'opponent_id' => Player::factory(),
            'message' => $this->faker->sentence(10),
            'status' => Challenge::STATUS_PENDING,
        ];
    }

    /**
     * Indicate that the challenge is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Challenge::STATUS_PENDING,
            'accepted_at' => null,
            'rejected_at' => null,
        ]);
    }

    /**
     * Indicate that the challenge has been accepted.
     */
    public function accepted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Challenge::STATUS_ACCEPTED,
            'accepted_at' => now(),
            'rejected_at' => null,
        ]);
    }

    /**
     * Indicate that the challenge has been rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Challenge::STATUS_REJECTED,
            'accepted_at' => null,
            'rejected_at' => now(),
        ]);
    }

    /**
     * Indicate that the challenge has been completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Challenge::STATUS_COMPLETED,
            'accepted_at' => now()->subDays(3),
        ]);
    }
}
