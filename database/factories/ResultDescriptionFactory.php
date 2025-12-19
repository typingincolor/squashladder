<?php

namespace Database\Factories;

use App\Models\ResultDescription;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ResultDescription>
 */
class ResultDescriptionFactory extends Factory
{
    protected $model = ResultDescription::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'description' => fake()->randomElement(['beat', 'drew with']),
        ];
    }

    /**
     * Create a "beat" result description
     */
    public function beat(): static
    {
        return $this->state(fn (array $attributes) => [
            'description' => 'beat',
        ]);
    }

    /**
     * Create a "drew with" result description
     */
    public function drew(): static
    {
        return $this->state(fn (array $attributes) => [
            'description' => 'drew with',
        ]);
    }
}
