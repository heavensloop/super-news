<?php

namespace Tests\Feature;

use App\Enum\NewsCategory;
use App\Enum\NewsSource;
use App\Jobs\ProcessNewsImport;
use App\Services\ArticleImporter;
use App\Services\News\Data\NewsQuery;
use App\Services\News\Data\NewsResourceCollection;
use App\Services\News\NewsSourceFactory;
use Tests\TestCase;

// TODO: Add more tests for different scenarios
class ProcessNewsImportTest extends TestCase
{
    public function test_handle(): void
    {
        $query = new NewsQuery(NewsCategory::BUSINESS);
        $query->setLimit(5);

        $articleImporter = $this->createMock(ArticleImporter::class);
        $articleImporter->method('import')
            ->with($this->isInstanceOf(NewsResourceCollection::class), NewsCategory::BUSINESS, NewsSource::NEWS_API);

        $job = new ProcessNewsImport($query, NewsSource::NEWS_API, $articleImporter);
        $job->handle(
            app(NewsSourceFactory::class),
            $articleImporter
        );
    }
}
