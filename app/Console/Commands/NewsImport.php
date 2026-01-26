<?php

namespace App\Console\Commands;

use App\Console\Commands\Traits\HasNewsParameters;
use App\Enum\NewsCategory;
use App\Enum\NewsSource;
use App\Jobs\ProcessNewsImport;
use App\Services\News\Data\NewsQuery;
use App\Services\News\NewsSourceFactory;
use Illuminate\Console\Command;

class NewsImport extends Command
{
    use HasNewsParameters;

    public function __construct(private readonly NewsSourceFactory $newsSourceFactory)
    {
        parent::__construct();
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:news:import
        {category? : Category of the news to import}
        {--source= : News source type}
        {--age= : Age of the news in days}
        {--from= : Import news published from this date (Y-m-d)}
        {--to= : Import news published up to this date (Y-m-d)}
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
        $age = $this->option('age');
        $publishedFrom = $this->getDateInput('from');
        $publishedTo = $this->getDateInput('to');
        $category = $this->getCategoryInput();

        $query = new NewsQuery($category);

        if (null !== $age) {
            $days = (int) $age;
            $fromDate = (new \DateTimeImmutable())->sub(new \DateInterval("P{$days}D"));
            $query->setPublishedFrom($fromDate);
        }

        if (null !== $publishedFrom) {
            $query->setPublishedFrom($publishedFrom);
        }

        if (null !== $publishedTo) {
            $query->setPublishedTo($publishedTo);
        }

        $limit = $this->option('limit');

        if (null !== $limit) {
            $query->setLimit((int) $limit);
        }

        $sources = $this->getNewsSource();

        foreach ($sources as $source) {
            $this->startImport($query, $source);
        }
    }

    private function getDateInput(string $param): ?\DateTimeImmutable
    {
        $dateString = $this->option($param);

        if ($dateString === null) {
            return null;
        }

        try {
            return new \DateTimeImmutable($dateString);
        } catch (\Exception $e) {
            $this->error("Invalid date format: {$dateString}. Expected format: Y-m-d");
            return null;
        }
    }

    private function startImport(NewsQuery $query, NewsSource $source): void
    {
        $newsSource = $this->newsSourceFactory->create($source);

        $this->info(
            sprintf(
                '-> Import for category %s has initialized for source: %s.',
                $query->getCategory()->getReadable(),
                $source->getReadable()
            )
        );

        dispatch(new ProcessNewsImport($query, $source));
    }
}
