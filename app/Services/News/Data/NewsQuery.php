<?php

namespace App\Services\News\Data;

use App\Enum\NewsCategory;

class NewsQuery
{
    private ?\DateTimeInterface $publishedFrom = null;
    private ?\DateTimeInterface $publishedTo = null;
    private int $limit = 100;

    public function __construct(private readonly NewsCategory $category)
    {}

    public function setPublishedFrom(?\DateTimeInterface $from): void
    {
        $this->publishedFrom = $from;
    }

    public function getPublishedFrom(): ?\DateTimeInterface
    {
        return $this->publishedFrom;
    }

    public function setPublishedTo(?\DateTimeInterface $to): void
    {
        $this->publishedTo = $to;
    }

    public function getPublishedTo(): ?\DateTimeInterface
    {
        return $this->publishedTo;
    }

    public function getCategory(): NewsCategory
    {
        return $this->category;
    }

    public function setLimit(int $limit): void
    {
        $this->limit = $limit;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }
}
