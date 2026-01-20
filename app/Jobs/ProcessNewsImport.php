<?php

namespace App\Jobs;

use App\Enum\NewsCategory;
use App\Enum\NewsSource;
use App\Services\ArticleImporter;
use App\Services\News\Data\NewsQuery;
use App\Services\News\NewsSourceFactory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Psl\Type;

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
    public function handle(NewsSourceFactory $newsFactory, ArticleImporter $articleImporter,): void
    {
        $newsSource = $newsFactory->create($this->source);
        $newsCollection = $newsSource->fetch($this->query);
        $category = Type\instance_of(NewsCategory::class)->assert($this->query->getCategory());

        $articleImporter->import($newsCollection, $category, $this->source);
    }
}
