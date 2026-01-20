<?php

namespace App\Services\News\Data;

use Illuminate\Support\Collection;

class NewsResourceCollection
{
    /** @var Collection<NewsResource> */
    private Collection $articles;

    public function __construct()
    {
        $this->articles = new Collection();
    }

     /** @param NewsResource $article */

    public function add(NewsResource $article): void
    {
        $this->articles->push($article);
    }

    /** @return list<NewsResource> */
    public function getItems(): array
    {
        return $this->articles->toArray();
    }
}
