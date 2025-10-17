<?php

namespace Database\Factories;

use App\Models\Guide;
use App\Models\HuntingBooking;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HuntingBooking>
 */
class HuntingBookingFactory extends Factory
{
    protected $model = HuntingBooking::class;

    public function definition(): array
    {
        return [
            'tour_name' => $this->faker->sentence(3),
            'hunter_name' => $this->faker->name(),
            'guide_id' => Guide::factory(),
            'date' => $this->faker->dateTimeBetween('+1 days', '+1 month')->format('Y-m-d'),
            'participants_count' => $this->faker->numberBetween(1, 10),
        ];
    }
}


