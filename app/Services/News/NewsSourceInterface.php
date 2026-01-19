<?php

namespace App\Services\News;

interface NewsSourceInterface
{
    public function fetch(NewsQuery $newsQuery);
}