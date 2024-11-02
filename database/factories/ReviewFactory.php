<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'content' => $this->generateHtmlContent(),
            'rating' => fake()->numberBetween(0, 10),
            'reviewable_id' => fake()->numberBetween(0, 10),
            'reviewable_type' => fake()->randomElement($this->getReviewableTypes()),
            'user_id' => User::factory(),
        ];
    }

    private function getReviewableTypes()
    {
        return [
            'App\Models\Product',
            'App\Models\Service',
        ];
    }

    private function generateHtmlContent()
    {
        $paragraphs = fake()->numberBetween(2, 5);
        $content = '';

        for ($i = 0; $i < $paragraphs; $i++) {
            $content .= "<p>" . fake()->paragraph(3, true) . "</p>";
        }

        return $content;
    }
}
