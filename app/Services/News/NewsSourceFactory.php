<?php

namespace App\Services\News;

use App\Enum\NewsSource;

class NewsSourceFactory
{
    public function __construct(private readonly iterable $newSourceGenerator)
    {
    }

    /**
     * @return NewsSourceInterface[]
     */
    public function getAll(): array
    {
        return iterator_to_array($this->newSourceGenerator);
    }

    public function get(NewsSource $type): NewsSourceInterface
    {
        foreach ($this->getAll() as $newsSource) {
            if ($newsSource->getType() === $type) {
                return $newsSource;
            }
        }

        throw new \InvalidArgumentException("News source of type {$type->value} not found.");
    }

    public function has(NewsSource $type): bool
    {
        foreach ($this->getAll() as $newsSource) {
            if ($newsSource->getType() === $type) {
                return true;
            }
        }

        return false;
    }

    public function create(NewsSource $type): NewsSourceInterface
    {
        return $this->get($type);
    }
}
