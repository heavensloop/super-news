<?php

namespace App\Jobs;

use App\Models\Article;
use App\Services\News\NewsSourceFactory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class PopulateArticleContent implements ShouldQueue
{
    use Queueable;

    public $backoff = [2, 15, 32];

    /**
     * Create a new job instance.
     */
    public function __construct(private readonly Article $article) {}

    /**
     * Execute the job.
     */
    public function handle(NewsSourceFactory $newsSourceFactory): void
    {
        $newsSource = $newsSourceFactory->create($this->article->source);

        try {
            $newsSource->populate($this->article);
        } catch (\Exception $e) {
            // Log the error or handle it as needed
            logger()->error("Failed to populate article content for Article ID {$this->article->id}: " . $e->getMessage());
            throw $e;
        }
    }
}
