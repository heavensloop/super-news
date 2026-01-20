<?php

namespace Tests\Feature;

use App\Enum\NewsCategory;
use App\Enum\NewsSource;
use App\Models\Article;
use App\Services\ArticleImporter;
use App\Services\News\Data\NewsResource;
use App\Services\News\Data\NewsResourceCollection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleImporterTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_imports_articles_successfully()
    {
        $newsCollection = new NewsResourceCollection();

        $resource1 = new NewsResource(NewsCategory::BUSINESS, NewsSource::NEWS_API);
        $resource1->setTitle('Test Article 1');
        $resource1->setDescription('Description 1');
        $resource1->setContent('Content 1');
        $resource1->setUrl('https://example.com/1');
        $resource1->setImageUrl('https://example.com/img1.jpg');
        $resource1->setAuthor('Author 1');
        $resource1->setPublishedAt(now());
        $newsCollection->add($resource1);

        $resource2 = new NewsResource(NewsCategory::BUSINESS, NewsSource::NEWS_API);
        $resource2->setTitle('Test Article 2');
        $resource2->setDescription('Description 2');
        $resource2->setContent('Content 2');
        $resource2->setUrl('https://example.com/2');
        $resource2->setImageUrl('https://example.com/img2.jpg');
        $resource2->setAuthor('Author 2');
        $resource2->setPublishedAt(now());
        $newsCollection->add($resource2);

        $importer = new ArticleImporter();
        $importer->import($newsCollection, NewsCategory::BUSINESS, NewsSource::NEWS_API);

        $this->assertDatabaseCount('articles', 2);
        $this->assertDatabaseHas('articles', [
            'title' => 'Test Article 1',
            'category' => NewsCategory::BUSINESS,
            'source' => NewsSource::NEWS_API,
        ]);
        $this->assertDatabaseHas('articles', [
            'title' => 'Test Article 2',
            'category' => NewsCategory::BUSINESS,
            'source' => NewsSource::NEWS_API,
        ]);
    }

    public function test_it_handles_empty_import_gracefully()
    {
        $newsCollection = new NewsResourceCollection();
        $importer = new ArticleImporter();
        $importer->import($newsCollection, NewsCategory::BUSINESS, NewsSource::NEWS_API);
        $this->assertDatabaseCount('articles', 0);
    }
}
