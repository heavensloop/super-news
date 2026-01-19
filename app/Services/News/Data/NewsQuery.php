<?php

namespace App\Services\News\Data;

class NewsQuery
{
    private ?\DateTimeInterface $publishedFrom = null;
    private ?\DateTimeInterface $publishedTo = null;
    private int $limit = 1000;

    public function setPublishedFrom(\DateTimeInterface $from): void
    {
        $this->publishedFrom = $from;
    }
    
    public function setPublishedTo(\DateTimeInterface $to): void
    {
        $this->publishedTo = $to;
    }

    public function setLimit(int $limit): void
    {
        $this->limit = $limit;
    }
}