<?php

namespace App\Services\News\Sources;

use App\Enum\NewsCategory;
use App\Enum\NewsSource;
use App\Services\News\Data\NewsQuery;
use App\Services\News\Data\NewsResource;
use App\Services\News\Data\NewsResourceCollection;
use App\Services\News\Exceptions\InvalidNewsSourceRequestException;
use App\Services\News\NewsSourceInterface;
use Illuminate\Support\Facades\Http;

class TheGuardian implements NewsSourceInterface
{
    private string $apiKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('sources.theguardian.key');
        $this->baseUrl = rtrim(config('sources.theguardian.base_url'), '/');
    }

    public function getType(): NewsSource
    {
        return NewsSource::THE_GUARDIAN;
    }

    public function fetch(NewsQuery $newsQuery): NewsResourceCollection
    {
        $queryParams = [
            'api-key' => $this->apiKey,
            'page-size' => $newsQuery->getLimit(),
            'category' => $newsQuery->getCategory()->value,
            'sortBy' => 'webPublicationDate',
            'show-fields' => 'thumbnail,trailText,standfirst',
        ];

        if (null !== $newsQuery->getPublishedFrom()) {
            $queryParams['from-date'] = $newsQuery->getPublishedFrom()->format('Y-m-d');
        }

        if (null !== $newsQuery->getPublishedTo()) {
            $queryParams['to-date'] = $newsQuery->getPublishedTo()->format('Y-m-d');
        }

        $url = sprintf('%s/search', rtrim($this->baseUrl));

        $response = Http::get($url, $queryParams);

        if ($response->failed()) {
            $message = $response->json('message', 'An error occurred while fetching news data.');

            throw new InvalidNewsSourceRequestException($message, $this->getType());
        }

        $data = $response->json('response.results', []);

        return $this->transFormData($data, $newsQuery->getCategory());
    }

    private function transFormData(array $data, NewsCategory $newsCategory): NewsResourceCollection
    {
        $collection = new NewsResourceCollection();

        foreach ($data as $item) {
            $newsResource = new NewsResource($newsCategory, $this->getType());
            $newsResource->setPublishedAt(new \DateTimeImmutable($item['webPublicationDate']));
            $newsResource->setTitle($item['webTitle']);
            $newsResource->setUrl($item['webUrl']);
            $newsResource->setContent($item['field']['standfirst'] ?? '');
            $newsResource->setDescription($item['fields']['trailText'] ?? '');
            $newsResource->setImageUrl($item['fields']['thumbnail']);

            $collection->add($newsResource);
        }

        return $collection;
    }
}
