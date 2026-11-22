<?php

namespace Database\Factories;

use App\Models\OraLesson;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OraLesson>
 */
class OraLessonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'trigger_phrases' => fake()->words(3, true),
            'answer' => fake()->paragraph(),
            'is_active' => true,
        ];
    }
}
