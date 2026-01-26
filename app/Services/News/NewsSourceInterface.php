<?php

namespace App\Services\News;

use App\Enum\NewsSource;
use App\Models\Article;
use App\Services\News\Data\NewsQuery;
use App\Services\News\Data\NewsResourceCollection;
use App\Services\News\Exceptions\InvalidNewsSourceRequestException;

interface NewsSourceInterface
{
    /** @throws new InvalidNewsSourceRequestException */
    public function fetch(NewsQuery $newsQuery): NewsResourceCollection;

    public function getType(): NewsSource;

    /**
     * Crawls the article page url and populates the content locally
     *
     * @param Article $article
     * @return void
     */
    public function populate(Article $article): void;
}
