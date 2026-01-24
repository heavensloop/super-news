<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserFilter>
 */
class UserFilterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'is_default' => false,
            'user_id' => fn() => User::factory()->create(),
            'settings' => $this->faker->randomElements([
                ['value' => 'sports', 'type' => ['id' => 'category', 'label' => 'Category', 'inputType' => 'select']],
                ['value' => 'technology', 'type' => ['id' => 'category', 'label' => 'Category', 'inputType' => 'select']],
                ['value' => 'bbc-news', 'type' => ['id' => 'source', 'label' => 'Source', 'inputType' => 'select']],
                ['value' => 'cnn', 'type' => ['id' => 'source', 'label' => 'Source', 'inputType' => 'select']],
            ], 2),
        ];
    }
}
