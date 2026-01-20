<?php

namespace App\Services\News;

use App\Enum\NewsSource;
use App\Services\News\Data\NewsQuery;
use App\Services\News\Data\NewsResourceCollection;

interface NewsSourceInterface
{
    /** @throws new \RuntimeException */
    public function fetch(NewsQuery $newsQuery): NewsResourceCollection;

    public function getType(): NewsSource;
}
