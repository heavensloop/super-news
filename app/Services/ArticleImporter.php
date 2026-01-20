<?php

namespace App\Services;

use App\Enum\NewsCategory;

use App\Enum\NewsSource;
use App\Models\Article;
use App\Services\News\Data\NewsResourceCollection;
use Illuminate\Support\Facades\DB;

class ArticleImporter
{
    public const int BATCH_SIZE = 100;
    public function import(NewsResourceCollection $newsCollection, NewsCategory $category, NewsSource $source): void
    {
        $articles = [];

        foreach ($newsCollection->getItems() as $newsItem) {
            $articles[] = [
                'title' => $newsItem->title,
                'description' => $newsItem->description,
                'content' => $newsItem->content,
                'url' => $newsItem->url,
                'image_url' => $newsItem->imageUrl,
                'source' => $source,
                'api_source' => $source->value,
                'author' => $newsItem->author,
                'published_at' => $newsItem->publishedAt,
                'category' => $category,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($articles) >= self::BATCH_SIZE) {
                $this->insertChunk($articles);
                $articles = [];
            }
        }

        if (!empty($articles)) {
            $this->insertChunk($articles);
        }
    }

    private function insertChunk(array $articles): void
    {
        DB::table((new Article())->getTable())->insert($articles);
    }
}
