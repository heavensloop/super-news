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

class NewYorkTimes implements NewsSourceInterface
{
    private string $apiKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('sources.nytimes.key');
        $this->baseUrl = rtrim(config('sources.nytimes.base_url'), '/');
    }

    public function getType(): NewsSource
    {
        return NewsSource::NEW_YORK_TIMES;
    }

    public function fetch(NewsQuery $newsQuery): NewsResourceCollection
    {
        $queryParams = [
            'api-key' => $this->apiKey,
            'q' => $newsQuery->getCategory()->value,
            'sort' => 'newest',
        ];

        if (null !== $newsQuery->getPublishedFrom()) {
            $queryParams['begin_date'] = $newsQuery->getPublishedFrom()->format('Ymd');
        }

        if (null !== $newsQuery->getPublishedTo()) {
            $queryParams['end_date'] = $newsQuery->getPublishedTo()->format('Ymd');
        }

        $url = sprintf('%s/articlesearch.json', rtrim($this->baseUrl));
        $response = Http::get($url, $queryParams);

        if ($response->failed()) {
            $code = $response->json('fault.detail.errorcode', 'error');
            $message = $response->json('fault.faultString', 'An error occurred while fetching news data.');

            throw new InvalidNewsSourceRequestException(sprintf('%s - %s', $code, $message), $this->getType());
        }

        $data = $response->json('response.docs', []);

        return $this->transFormData($data, $newsQuery->getCategory());
    }

    private function transFormData(array $data, NewsCategory $newsCategory): NewsResourceCollection
    {
        $collection = new NewsResourceCollection();

        foreach ($data as $item) {
            $newsResource = new NewsResource($newsCategory, $this->getType());
            $newsResource->setPublishedAt(new \DateTimeImmutable($item['pub_date'] ?? 'now'));
            $newsResource->setTitle(($item['headline']['main'] ?? '') ?: ($item['headline']['name'] ?? ''));
            $newsResource->setDescription($item['abstract'] ?? '');
            $newsResource->setContent(isset($item['lead_paragraph']) ? $item['lead_paragraph'] : ($item['snippet'] ?? ''));
            $newsResource->setUrl($item['web_url']);

            // Find first multimedia item with type 'image' and a valid url
            $imageUrl = null;
            if (!empty($item['multimedia']) && is_array($item['multimedia'])) {
                foreach ($item['multimedia'] as $media) {
                    if ((isset($media['type']) && $media['type'] === 'image') && !empty($media['url'])) {
                        $imageUrl = 'https://www.nytimes.com/' . ltrim($media['url'], '/');
                        break;
                    }
                }
            }
            $newsResource->setImageUrl($imageUrl);

            $newsResource->setAuthor($item['byline']['original'] ?? null);

            $collection->add($newsResource);
        }

        return $collection;
    }
}
