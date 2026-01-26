<?php

namespace App\Services\News\Sources;

use App\Enum\ContentStatus;
use App\Enum\NewsCategory;
use App\Enum\NewsSource;
use App\Models\Article;
use App\Services\CrawlerClient;
use App\Services\News\Data\NewsQuery;
use App\Services\News\Data\NewsResource;
use App\Services\News\Data\NewsResourceCollection;
use App\Services\News\Exceptions\InvalidNewsSourceRequestException;
use App\Services\News\NewsSourceInterface;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class TheGuardian implements NewsSourceInterface
{
    private string $apiKey;
    private string $baseUrl;

    public function __construct(private readonly CrawlerClient $client)
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

    public function populate(Article $article): void
    {
        $url = $article->url;

        $response = $this->client->get($url);

        if (200 !== $response->getStatusCode()) {
            throw new \Exception("Failed to fetch URL: {$url}");
        }

        $html = (string) $response->getBody();

        $crawler = new Crawler($html);
        $title = $crawler->filter('h1')->first()->text('');

        $featuredImage = '';
        $crawler->filter('meta[property="og:image"]')
            ->each(function ($node) use (&$featuredImage) {
                $featuredImage = $node->attr('content') ?: '';
            });

        $content = [];

        $crawler->filter('.article-body-commercial-selector p, .live-blog-body p')
            ->each(function ($node) use (&$content) {
                $text = trim($node->text());
                if ($text) {
                    $content[] = $text;
                }
            });

        $article->image_url = $featuredImage;
        $article->content = implode("\n\n", $content);
        $article->content_status = ContentStatus::POPULATED;

        $article->save();
    }
}
