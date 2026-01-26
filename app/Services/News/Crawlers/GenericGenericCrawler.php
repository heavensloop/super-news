<?php

namespace App\Services\News\Crawlers;

use App\Services\CrawlerClient;
use Symfony\Component\DomCrawler\Crawler;

class GenericGenericCrawler
{
    public function __construct(private readonly CrawlerClient $client)
    {}

    /**
     * Scrape a news article.
     *
     * @param string $url
     * @return array
     */
    public function scrape(string $url): array
    {
        $response = $this->client->get($url);
        $html = (string) $response->getBody();

        $crawler = new Crawler($html);

        // Title (fallback to <title> if no <h1>)
        $title = $crawler->filter('h1')->count()
            ? $crawler->filter('h1')->first()->text()
            : $crawler->filter('title')->first()->text();

        // Featured Image (most common patterns)
        $featuredImage = $this->extractImage($crawler);

        // Publish date (if exists)
        $publishDate = $this->extractPublishDate($crawler);

        // Main article content paragraphs
        $content = $this->extractContent($crawler);

        return [
            'title' => $title,
            'featured_image' => $featuredImage,
            'publish_date' => $publishDate,
            'content' => implode("\n\n", $content),
            'source_url' => $url,
        ];
    }

    protected function extractImage(Crawler $crawler): string
    {
        // Try common Open Graph tag
        if ($crawler->filter('meta[property="og:image"]')->count()) {
            return $crawler->filter('meta[property="og:image"]')->first()->attr('content');
        }

        // Fallback: Twitter image
        if ($crawler->filter('meta[name="twitter:image"]')->count()) {
            return $crawler->filter('meta[name="twitter:image"]')->first()->attr('content');
        }

        return '';
    }

    protected function extractPublishDate(Crawler $crawler): string
    {
        $date = '';

        // Check common published date selectors
        $dateSelectors = [
            'meta[property="article:published_time"]',
            'meta[name="pubdate"]',
            'time[datetime]',
            '.published-date',
            '.article-date'
        ];

        foreach ($dateSelectors as $selector) {
            if ($crawler->filter($selector)->count()) {
                $date = $crawler->filter($selector)->first()->attr('datetime') ??
                        $crawler->filter($selector)->first()->attr('content') ??
                        $crawler->filter($selector)->first()->text();
                break;
            }
        }

        return $date;
    }

    protected function extractContent(Crawler $crawler): array
    {
        $content = [];

        // Common article body selectors
        $paragraphSelectors = [
            'article p',                     // Standard article
            '.article-body p',               // many news sites
            '.entry-content p',              // WordPress
            '.post-content p',               // generic
            '.news-body p',
            '.content-text p'
        ];

        foreach ($paragraphSelectors as $selector) {
            $crawler->filter($selector)->each(function ($node) use (&$content) {
                $text = trim($node->text());
                if (!empty($text)) {
                    $content[] = $text;
                }
            });
            if (!empty($content)) {
                break;
            }
        }

        return $content;
    }
}
