<?php

namespace Database\Factories;

use App\Enum\FilterType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use WeakMap;

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
            'settings' => $this->faker->randomElements($this->getRandomSettings(), $this->faker->numberBetween(3, 6)),
        ];
    }

    public function withTypes(array $types = []): static
    {
        $types = [] !== $types ? $types : FilterType::cases();

        return $this->state(function (array $attributes) use ($types) {
            $numTypes = count($types);
            $settings = $this->getRandomSettings($types);
            $numberOfElements = $this->faker->numberBetween(3, 6);

            return [
                'settings' => $this->faker->randomElements($settings, $numberOfElements),
            ];
        });
    }

    private function getRandomSettings(array $types = []): array
    {
        $types = [] !== $types ? $types : FilterType::cases();
        $typeMap = $this->getTypeMap();

        foreach ($types as $type) {
            foreach ($typeMap[$type] as $value) {
                $possibleSettings[] = [
                    'value' => $value,
                    'type' => [
                        'id' => $type->value,
                        'label' => $type->getReadable(),
                        'inputType' => $type->getInputType(),
                    ],
                ];
            }
        }

        return $possibleSettings;
    }

    private function getTypeMap(): WeakMap
    {
        $typeMap = new WeakMap();

        $typeMap[FilterType::CATEGORY] = ['sports', 'technology', 'business', 'entertainment'];
        $typeMap[FilterType::SOURCE] = ['bbc-news', 'cnn', 'the-verge', 'techcrunch'];
        $typeMap[FilterType::DATE_PUBLISHED] = ['2023-12-31', '2022-12-31', '2021-12-31', '2020-12-31'];
        $typeMap[FilterType::PUBLISHED_BETWEEN] = [
            ['from' => '2023-01-01', 'to' => '2023-12-31'],
            ['from' => '2022-01-01', 'to' => '2022-12-31'],
            ['from' => '2021-01-01', 'to' => '2021-12-31'],
            ['from' => '2020-01-01', 'to' => '2020-12-31'],
        ];
        $typeMap[FilterType::KEYWORD] = ['economy', 'election', 'climate', 'health'];
        $typeMap[FilterType::AUTHOR] = ['John Doe', 'Jane Smith', 'Alice Johnson', 'Bob Brown'];

        return $typeMap;
    }
}
