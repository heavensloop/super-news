<?php

namespace App\Jobs;

use App\Enum\NewsSource;
use App\Services\News\Data\NewsQuery;
use App\Services\News\NewsSourceFactory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessNewsImport implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private readonly NewsQuery $query,
        private readonly NewsSource $source,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $newsFactory = app(NewsSourceFactory::class);
        $newsSource = $newsFactory->create($this->source);
        $newsCollection = $newsSource->fetch($this->query);

        foreach ($newsCollection->getItems() as $newsItem) {
            // Here you would typically save the news item to the database.
            dump("Imported: " . $newsItem->title);
        }
    }
}
