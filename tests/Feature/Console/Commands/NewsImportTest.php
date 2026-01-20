<?php

namespace Tests\Feature\Console\Commands;

use App\Enum\NewsCategory;
use App\Enum\NewsSource;
use App\Services\News\NewsClient;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_with_source(): void
    {
        $this->mockNewsClient([
            'limit' => 3,
            'category' => NewsCategory::BUSINESS->value,
        ]);

        $this->artisan('app:news:import', [
            'category' => NewsCategory::BUSINESS->value,
            '--source' => NewsSource::NEWS_API->value,
            '--limit' => 3,
        ])->assertExitCode(0);

        $this->assertDatabaseCount('articles', 3);
    }

    public function test_asks_prompts_with_options_for_category(): void
    {
        $this->mockNewsClient([
            'limit' => 1,
            'category' => NewsCategory::HEALTH->value,
        ]);

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

        $this->assertDatabaseCount('articles', 1);
    }

    public function test_prompts_for_news_sources_when_invalid_source_option_provided(): void
    {
        $this->mockNewsClient([
            'limit' => 1,
            'category' => NewsCategory::BUSINESS->value,
        ]);

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

        $this->assertDatabaseCount('articles', 1);
    }

    public function test_import_with_category(): void
    {
        $this->mockNewsClient([
            'limit' => 2,
            'category' => NewsCategory::SCIENCE_TECH->value,
        ]);

        $this->artisan('app:news:import', [
            'category' => NewsCategory::SCIENCE_TECH->value,
            '--source' => NewsSource::NEWS_API->value,
            '--limit' => 2,
        ])->assertExitCode(0);

        $this->assertDatabaseCount('articles', 2);
    }

    public function test_import_with_published_date_range(): void
    {
        $this->mockNewsClient([
            'category' => NewsCategory::SCIENCE_TECH->value,
            'from' => '2024-01-01T00:00:00Z',
            'to' => '2024-12-31T00:00:00Z',
            'limit' => 5,
        ]);

        $this->artisan('app:news:import', [
            'category' => NewsCategory::SCIENCE_TECH->value,
            '--source' => NewsSource::NEWS_API->value,
            '--from' => '2024-01-01',
            '--to' => '2024-12-31',
            '--limit' => 5,
        ])->assertExitCode(0);

        $this->assertDatabaseCount('articles', 5);
    }

    public function test_import_with_age_parameter(): void
    {
        $this->mockNewsClient([
            'category' => NewsCategory::SCIENCE_TECH->value,
            'from' => Carbon::now()->subDays(3)->format('Y-m-d\TH:i:s\Z'),
            'limit' => 5,
        ]);

        $this->artisan('app:news:import', [
            'category' => NewsCategory::SCIENCE_TECH->value,
            '--source' => NewsSource::NEWS_API->value,
            '--age' => '3',
            '--limit' => 5,
        ])->assertExitCode(0);

        $this->assertDatabaseCount('articles', 5);
    }

    private function mockNewsClient(array $expectedParams = []): void
    {
        $data = json_decode(file_get_contents(base_path('tests/Fixtures/News/Responses/newsapi_response.json')), true);
        $data['articles'] = array_slice($data['articles'], 0, $expectedParams['limit'] ?? count($data['articles']));

        $this->mock(NewsClient::class, function ($mock) use ($data, $expectedParams) {
            $message = $this->createMock(\Psr\Http\Message\ResponseInterface::class);
            $response = $this->createMock(\Illuminate\Http\Client\Response::class);
            $response->method('json')
                ->willReturn($data['articles']);

            $mock->shouldReceive('get')
                ->withArgs(function (string $url, array $params) use ($expectedParams) {
                    $skipped = ['limit', 'source'];

                    foreach ($expectedParams as $key => $value) {
                        if (in_array($key, $skipped, true)) {
                            continue;
                        }

                        if (!isset($params[$key]) || (string)$params[$key] !== (string)$value) {
                            return false;
                        }
                    }

                    return true;
                })
                ->andReturn($response);
        });
    }
}
