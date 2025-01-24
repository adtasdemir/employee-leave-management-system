<?php

namespace Database\Factories;

use App\Models\User;
use App\Enums\LeaveRequestStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LeaveRequest>
 */
class LeaveRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-1 year', '+1 year')->format('Y-m-d');
        $endDate = $this->faker->dateTimeBetween($startDate, '+1 month')->format('Y-m-d');

        return [
            'user_id' => User::factory(), 
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => LeaveRequestStatus::PENDING->value, 
        ];

        
    }
}
