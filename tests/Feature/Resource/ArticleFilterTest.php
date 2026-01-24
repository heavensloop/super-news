<?php

namespace Tests\Feature;

use App\Enum\FilterType;
use App\Enum\NewsCategory;
use App\Enum\NewsSource;
use App\Models\Article;
use App\Models\UserFilter;
use Database\Seeders\ArticleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ArticleFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_query_strings_are_decoded(): void
    {
        Article::factory()->count(5)->create();

        $user = $this->createUser();
        $this->actingAs($user);

        $settings = $this->getRandomFilters(2);
        $queryString = $this->encodeFiltersToQueryString($settings);
        $response = $this->get('/api/v1/articles/filtered/?filters=' . $queryString);
        $response->assertStatus(200);
    }

    public function test_fetch_filter_articles(): void
    {
        $user = $this->createUser();

        $this->actingAs($user);
        Article::factory()->count(5)->create([
            'source' => NewsSource::NEW_YORK_TIMES->value,
            'category' => NewsCategory::BUSINESS->value,
        ]);

        Article::factory()->count(7)->create([
            'source' => NewsSource::NEW_YORK_TIMES->value,
            'category' => NewsCategory::POLITICS->value,
        ]);

        $this->assertCount(12, Article::all());

        $settings = [
            'category' => [NewsCategory::CRIME_JUSTICE, NewsCategory::ENTERTAINMENT],
            'source' => [NewsSource::THE_GUARDIAN->value, NewsSource::NEWS_API],
        ];

        Article::factory()->createOne(['source' => NewsSource::THE_GUARDIAN->value, 'category' => NewsCategory::CRIME_JUSTICE->value]);
        Article::factory()->createOne(['category' => NewsCategory::ENTERTAINMENT->value]);
        Article::factory()->createOne(['source' => NewsSource::NEWS_API, 'category' => NewsCategory::CRIME_JUSTICE->value]);
        Article::factory()->createOne(['source' => NewsSource::NEWS_API]);

        $this->assertCount(16, Article::all());

        $queryString = $this->encodeFiltersToQueryString($settings);
        $response = $this->get('/api/v1/articles/filtered/?filters=' . $queryString);
        $response->assertStatus(200);

        $response->assertJsonCount(4, 'data');
    }

    /** @return array<string, string[]>*/
    private function getRandomFilters(int $valueCount, ?array $filterTypes = null): array
    {
        $filters = UserFilter::factory()->withTypes($filterTypes)->create();

        return $this->groupSettings($filters->toArray(), $valueCount);
    }

    /** @param list<UserFilter> $settings */
    private function groupSettings(array $settings, $valueCount = 1): array
    {
        $grouped = [];

        foreach ($settings['settings'] as $setting) {
            $id = $setting['type']['id'];
            $value = $setting['value'];

            if (count($grouped[$id] ?? []) >= $valueCount) {
                continue;
            }

            if (!isset($grouped[$id])) {
                $grouped[$id] = [];
            }

            if (!in_array($value, $grouped[$id])) {
                $grouped[$id][] = $value;
            }
        }

        return $grouped;
    }

    private function encodeFiltersToQueryString(array $groupedFilters): string
    {
        return urlencode(json_encode($groupedFilters));
    }
}
