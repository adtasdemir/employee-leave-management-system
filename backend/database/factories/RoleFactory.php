<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Role>
 */
class RoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Randomly assign the title as 'user' by default
        return [
            'title' => $this->faker->randomElement(['admin', 'user']),
            'description' => $this->faker->sentence(),
        ];
    }
}
