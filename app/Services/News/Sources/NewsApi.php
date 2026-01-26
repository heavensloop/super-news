<?php

namespace App\Services\News\Sources;

use App\Enum\ContentStatus;
use App\Enum\NewsCategory;
use App\Enum\NewsSource;
use App\Models\Article;
use App\Services\News\Crawlers\GenericGenericCrawler;
use App\Services\News\Data\NewsQuery;
use App\Services\News\Data\NewsResource;
use App\Services\News\Data\NewsResourceCollection;
use App\Services\News\Exceptions\InvalidNewsSourceRequestException;
use App\Services\News\NewsSourceInterface;
use Illuminate\Support\Facades\Http;

class NewsApi implements NewsSourceInterface
{
    private string $apiKey ;
    private string $baseUrl;

    public function __construct(private readonly GenericGenericCrawler $crawler)
    {
        $this->apiKey = config('sources.newsapi.key');
        $this->baseUrl = rtrim(config('sources.newsapi.base_url'), '/');
    }

    public function getType(): NewsSource
    {
        return NewsSource::NEWS_API;
    }

    public function fetch(NewsQuery $newsQuery): NewsResourceCollection
    {
        $queryParams = [
            'apiKey' => $this->apiKey,
            'pageSize' => $newsQuery->getLimit(),
            'sortBy' => 'publishedAt',
        ];

        if (null !== $newsQuery->getPublishedFrom()) {
            $queryParams['from'] = $newsQuery->getPublishedFrom()->format('Y-m-d\TH:i:s\Z');
        }

        if (null !== $newsQuery->getPublishedTo()) {
            $queryParams['to'] = $newsQuery->getPublishedTo()->format('Y-m-d\TH:i:s\Z');
        }

        if (null !== $newsQuery->getCategory()) {
            $queryParams['category'] = $newsQuery->getCategory()->value;
        }

        $url = sprintf('%s/top-headlines', rtrim($this->baseUrl));

        $response = Http::get($url, $queryParams);

        if ($response->failed()) {
            $code = $response->json('code', 'error');
            $message = $response->json('message', 'An error occurred while fetching news data.');

            throw new InvalidNewsSourceRequestException(sprintf('%s - %s', $code, $message), $this->getType());
        }

        $data = $response->json('articles', []);

        return $this->transFormData($data, $newsQuery->getCategory());
    }

    private function transFormData(array $data, NewsCategory $newsCategory): NewsResourceCollection
    {
        $collection = new NewsResourceCollection();

        foreach ($data as $item) {
            $newsResource = new NewsResource($newsCategory, $this->getType());
            $newsResource->setPublishedAt(new \DateTimeImmutable($item['publishedAt']));
            $newsResource->setTitle($item['title'] ?? '');
            $newsResource->setDescription($item['description'] ?? '');
            $newsResource->setContent($item['content'] ?? '');
            $newsResource->setUrl($item['url'] ?? '');
            $newsResource->setImageUrl($item['urlToImage'] ?? '');
            $newsResource->setAuthor($item['author'] ?? '');

            $collection->add($newsResource);
        }

        return $collection;
    }

    public function populate(Article $article): void
    {
        $data = $this->crawler->scrape($article->url);
        $article->title = $data['title'];
        $article->image_url = $data['featured_image'];
        $article->content_status = ContentStatus::POPULATED;
    }
}
