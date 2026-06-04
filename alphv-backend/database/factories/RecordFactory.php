<?php

namespace Database\Factories;

use App\Models\Record;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Record>
 *
 * Generates fake Record rows constrained to the same valid values
 * that the API validation rules enforce:
 *   shape → triangle | square | circle
 *   color → red | blue | green | yellow
 */
class RecordFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // A realistic two-word name, e.g. "Blue Diamond" or "Red Prism"
            'name'  => fake()->words(2, asText: true),
            // Pick randomly from the three API-allowed shapes
            'shape' => fake()->randomElement(['triangle', 'square', 'circle']),
            // Pick randomly from the four API-allowed colors
            'color' => fake()->randomElement(['red', 'blue', 'green', 'yellow']),
        ];
    }
}
