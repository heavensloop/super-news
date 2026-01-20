<?php

namespace App\Services\News\Data;

use DateTimeInterface;

class NewsResource
{
    public DateTimeInterface $publishedAt;
    public string $title;
    public string $description;
    public string $content;
    public string $url;
    public string $imageUrl;
    public ?string $sourceId = null;
    public ?string $sourceName = null;
    public ?string $author = null;

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

    public function setSource(?string $id, ?string $name): void
    {
        $this->sourceId = $id;
        $this->sourceName = $name;
    }

    public function setAuthor(?string $author): void
    {
        $this->author = $author;
    }
}
