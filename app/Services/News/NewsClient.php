<?php

namespace App\Services\News;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class NewsClient
{
    public function get(string $url, array $params = []): Response
    {
        $response = Http::get($url, $params);

        $rawUrl = $url . '?' . http_build_query($params);

        if ($response->failed()) {
            $code = $response->json('code', 'error');
            $message = $response->json('message', 'An error occurred while fetching news data.');

            throw new \RuntimeException(sprintf('%s - %s', $code, $message), 500);
        }

        return $response;
    }
}
