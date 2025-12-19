<?php

namespace Database\Factories;

use App\Models\Player;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Player>
 */
class PlayerFactory extends Factory
{
    protected $model = Player::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $forename = fake()->firstName();
        $surname = fake()->lastName();
        $email = fake()->unique()->safeEmail();

        return [
            'user_id' => User::factory()->create([
                'name' => "{$forename} {$surname}",
                'email' => $email,
            ]),
            'forename' => $forename,
            'surname' => $surname,
            'email' => $email,
            'rank' => fake()->numberBetween(1, 100),
        ];
    }

    /**
     * Create a player with a specific rank
     */
    public function rank(int $rank): static
    {
        return $this->state(fn (array $attributes) => [
            'rank' => $rank,
        ]);
    }

    /**
     * Create a player with specific name
     */
    public function named(string $forename, string $surname): static
    {
        return $this->state(fn (array $attributes) => [
            'forename' => $forename,
            'surname' => $surname,
        ]);
    }

    /**
     * Create a player without a user account
     */
    public function withoutUser(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => null,
        ]);
    }
}
