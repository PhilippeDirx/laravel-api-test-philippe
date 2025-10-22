<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Appointment>
 */
class AppointmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "title" => "Appointment ".$this->faker->company(),
            "event_date" => $this->faker->dateTimeBetween('-5 years', '+5 years'),
        ];
    }

    public function past(): static
    {
        return $this->state(function () {
            return [
                "event_date" => $this->faker->dateTimeBetween('-2 years', 'now'),
            ];
        });
    }

    public function future(): static
    {
        return $this->state(function () {
            return [
                "event_date" => $this->faker->dateTimeBetween('now', '+2 years'),
            ];
        });
    }
}
