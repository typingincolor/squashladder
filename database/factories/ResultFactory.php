<?php

namespace Database\Factories;

use App\Models\Player;
use App\Models\Result;
use App\Models\ResultDescription;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Result>
 */
class ResultFactory extends Factory
{
    protected $model = Result::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'match_date' => fake()->dateTimeBetween('-30 days', 'now'),
            'player1_id' => Player::factory(),
            'player2_id' => Player::factory(),
            'result_description_id' => ResultDescription::factory(),
        ];
    }

    /**
     * Create a result with a specific match date
     */
    public function date(string $date): static
    {
        return $this->state(fn (array $attributes) => [
            'match_date' => $date,
        ]);
    }

    /**
     * Create a result where player1 won
     */
    public function player1Won(): static
    {
        return $this->state(fn (array $attributes) => [
            'result_description_id' => ResultDescription::BEAT,
        ]);
    }

    /**
     * Create a result that was a draw
     */
    public function draw(): static
    {
        return $this->state(fn (array $attributes) => [
            'result_description_id' => ResultDescription::DREW,
        ]);
    }

    /**
     * Create a result between specific players
     */
    public function between(Player $player1, Player $player2): static
    {
        return $this->state(fn (array $attributes) => [
            'player1_id' => $player1->id,
            'player2_id' => $player2->id,
        ]);
    }

    /**
     * Create a recent result (within last 7 days)
     */
    public function recent(): static
    {
        return $this->state(fn (array $attributes) => [
            'match_date' => fake()->dateTimeBetween('-7 days', 'now'),
        ]);
    }
}
