<?php

namespace Tests\Unit\Jobs;

use App\Enum\NewsCategory;
use App\Enum\NewsSource;
use App\Jobs\ProcessNewsImport;
use App\Services\ArticleImporter;
use App\Services\News\Data\NewsQuery;
use App\Services\News\Data\NewsResourceCollection;
use App\Services\News\NewsSourceFactory;
use App\Services\News\NewsSourceInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ProcessNewsImport::class)]
class ProcessNewsImportTest extends TestCase
{
    public function test_handle(): void
    {
        $mockNewsFactory = $this->createMock(NewsSourceFactory::class);

        $newsSource = $this->createMock(NewsSourceInterface::class);
        $newsSource->method('fetch')
            ->willReturn(new NewsResourceCollection([]));

        $mockNewsFactory->method('create')
            ->with(NewsSource::NEWS_API)
            ->willReturn($newsSource);

        $mockArticleImporter = $this->createMock(ArticleImporter::class);
        $mockArticleImporter->method('import')
            ->with($this->isInstanceOf(NewsResourceCollection::class), NewsCategory::BUSINESS, NewsSource::NEWS_API);

        app()->instance(NewsSourceFactory::class, $mockNewsFactory);

        $query = new NewsQuery(NewsCategory::BUSINESS);
        $query->setLimit(5);

        $job = new ProcessNewsImport($query, NewsSource::NEWS_API);
        $job->handle($mockNewsFactory, $mockArticleImporter);
    }
}
