<?php

namespace App\Services\News\Sources;

use App\Enum\NewsCategory;
use App\Enum\NewsSource;
use App\Services\News\Data\NewsQuery;
use App\Services\News\Data\NewsResource;
use App\Services\News\Data\NewsResourceCollection;
use App\Services\News\NewsClient;
use App\Services\News\NewsSourceInterface;

class NewsApi implements NewsSourceInterface
{
    private string $apiKey ;
    private string $baseUrl;

    public function __construct(private readonly NewsClient $client)
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

        $response = $this->client->get($url, $queryParams);
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
}
