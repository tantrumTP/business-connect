<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Business>
 */
class BusinessFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'description' => fake()->text(),
            'direction' => fake()->address(),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->email(),
            'hours' => json_encode(['Monday' => '9am-5pm', 'Tuesday' => '9am-5pm']),
            'website' => fake()->domainName(),
            'social_networks' => json_encode(['social1' => fake()->url(), 'social2' => fake()->url()]),
            'characteristics' => json_encode(['Feature1', 'Feature2']),
            'covered_areas' => json_encode(['Area1', 'Area2']),
            'status' => $this->faker->randomElement(['active', 'inactive']),
        ];
    }
}
