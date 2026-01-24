<?php

namespace Database\Factories;

use App\Enum\NewsCategory;
use App\Enum\NewsSource;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'published_at' => $this->faker->dateTime(),
            'title' => $this->faker->sentence(),
            'description' => $this->faker->sentence(),
            'content' => $this->faker->sentences(asText: true),
            'url' => $this->faker->unique()->url(),
            'image_url' => $this->faker->imageUrl(),
            'author' => $this->faker->name(),
            'category' => $this->faker->randomElement(
                collect(NewsCategory::cases())
                    ->map(fn(NewsCategory $category) => $category->value)->toArray()
            ),
            'source' => $this->faker->randomElement(
                collect(NewsSource::cases())
                    ->map(fn(NewsSource $source) => $source->value)->toArray()
            ),
            'reference_hash' => $this->faker->unique()->md5(),
        ];
    }
}
