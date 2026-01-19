<?php

namespace App\Services\News\Sources;

use App\Services\News\Data\NewsQuery;

class NewsApi implements NewsSourceInterface
{
    private const string API_KEY = '27a2f5a8f3ef4f6699ae25ddb5abfc9e';

    public function fetch(NewsQuery $newsQuery): NewsResourceCollection
    {
        
    }
}