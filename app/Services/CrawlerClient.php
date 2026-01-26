<?php

namespace App\Services;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class CrawlerClient
{
    function __construct(private readonly Client $client) {}

    public function get(string $url): ResponseInterface
    {
        return $this->client->get($url, [
            'headers' => [
                'User-Agent' =>
                'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) ' .
                    'AppleWebKit/537.36 (KHTML, like Gecko) ' .
                    'Chrome/121.0.0.0 Safari/537.36',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language' => 'en-US,en;q=0.9',
                'Cache-Control' => 'no-cache',
                'Pragma' => 'no-cache',
                'Referer' => 'https://www.google.com/',
                'DNT' => '1',
                'Upgrade-Insecure-Requests' => '1',
            ],
            'http_errors' => false,
        ]);
    }
}
