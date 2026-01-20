<?php

namespace App\Services\News\Data;

use App\Enum\NewsCategory;
use App\Enum\NewsSource;
use DateTimeInterface;

class NewsResource
{
    public DateTimeInterface $publishedAt;
    public string $title;
    public string $description;
    public string $content;
    public string $url;
    public string $imageUrl;
    public ?string $author = null;

    public function __construct(
        private readonly NewsCategory $category,
        private readonly NewsSource $source
    )
    {}

    public function setPublishedAt(DateTimeInterface $publishedAt): void
    {
        $this->publishedAt = $publishedAt;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    public function setImageUrl(string $imageUrl): void
    {
        $this->imageUrl = $imageUrl;
    }

    public function getSource(NewsSource $source): NewsSource
    {
        return $this->source;
    }

    public function setAuthor(?string $author): void
    {
        $this->author = $author;
    }

    public function getCategory(): NewsCategory
    {
        return $this->category;
    }
}
