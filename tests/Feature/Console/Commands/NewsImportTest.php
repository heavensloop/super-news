<?php

namespace Tests\Feature\Console\Commands;

use App\Enum\NewsCategory;
use App\Enum\NewsSource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsImportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fakeNewsFactory();
    }

    public function test_import_with_source(): void
    {
        $this->artisan('app:news:import', [
            'category' => NewsCategory::BUSINESS->value,
            '--source' => NewsSource::NEWS_API->value,
            '--limit' => 3,
        ])->assertExitCode(0);
    }

    public function test_asks_prompts_with_options_for_category(): void
    {
        $this->artisan('app:news:import', [
            '--source' => NewsSource::NEWS_API->value,
            '--limit' => 1,
        ])
            ->expectsChoice(
                'Please select a valid news category:',
                NewsCategory::HEALTH->value . ' - (' . NewsCategory::HEALTH->getReadable() . ')',
                collect(NewsCategory::cases())
                    ->map(fn(NewsCategory $category) =>
                    sprintf('%s - (%s)', $category->value, $category->getReadable()))
                    ->toArray()
            )
            ->assertExitCode(0);
    }

    public function test_prompts_for_news_sources_when_invalid_source_option_provided(): void
    {
        $this->artisan('app:news:import', [
            'category' => NewsCategory::BUSINESS->value,
            '--source' => 'invalid_source',
            '--limit' => 1,
        ])
            ->expectsOutput('No valid news sources found for option: invalid_source')
            ->expectsChoice(
                'Please select news sources to import from (comma separated for multiple):',
                NewsSource::NEWS_API->value . ' - (' . NewsSource::NEWS_API->getReadable() . ')',
                collect(NewsSource::cases())
                    ->map(fn($source) => sprintf('%s - (%s)', $source->value, $source->getReadable()))
                    ->toArray()
            )
            ->assertExitCode(0);
    }

    public function test_import_with_category(): void
    {
        $this->artisan('app:news:import', [
            'category' => NewsCategory::SCIENCE_TECH->value,
            '--source' => NewsSource::NEWS_API->value,
            '--limit' => 2,
        ])->assertExitCode(0);
    }

    public function test_import_with_published_date_range(): void
    {
        $this->artisan('app:news:import', [
            'category' => NewsCategory::SCIENCE_TECH->value,
            '--source' => NewsSource::NEWS_API->value,
            '--from' => '2024-01-01',
            '--to' => '2024-12-31',
            '--limit' => 5,
        ])->assertExitCode(0);
    }

    public function test_import_with_age_parameter(): void
    {
        $this->artisan('app:news:import', [
            'category' => NewsCategory::SCIENCE_TECH->value,
            '--source' => NewsSource::NEWS_API->value,
            '--age' => '3',
            '--limit' => 5,
        ])->assertExitCode(0);
    }
}
