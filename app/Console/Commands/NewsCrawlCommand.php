<?php

namespace App\Console\Commands;

use App\Console\Commands\Traits\HasNewsParameters;
use App\Enum\ContentStatus;
use App\Jobs\PopulateArticleContent;
use App\Models\Article;
use Illuminate\Console\Command;

class NewsCrawlCommand extends Command
{
    use HasNewsParameters;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:news:crawl
        {category? : Category of the news to import}
        {--source= : News source type}
        {--limit= : Maximum number of news items to import}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import news from external sources';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sources = $this->getNewsSource();
        $category = $this->getCategoryInput(false);
        $limit = $this->option('limit');
        $query = Article::where('content_status', ContentStatus::PENDING);

        if (null !== $category) {
            $query->where('category', $category->value);
        }

        if (!empty($sources)) {
            $sourceValues = array_map(fn($s) => $s->value, $sources);
            $query->whereIn('source', $sourceValues);
        }

        if (null !== $limit) {
            $query->limit((int) $limit);
        }

        foreach($query->get() as $article) {
            dispatch(new PopulateArticleContent($article));
        }
    }
}
