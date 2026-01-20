<?php

namespace App\Console\Commands;

use App\Enum\NewsCategory;
use App\Enum\NewsSource;
use App\Jobs\ProcessNewsImport;
use App\Services\News\Data\NewsQuery;
use App\Services\News\NewsSourceFactory;
use Illuminate\Console\Command;

class NewsImport extends Command
{
    private const string ARRAY_DELIMITER_REGEX = '/,\s*/';

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

        $sources = $this->getRequestedSources();

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

    private function getCategoryInput(): NewsCategory
    {
        $categoryString = $this->argument('category');

        if ($categoryString === null) {
            return $this->askForCategory();
        }

        $category = NewsCategory::tryFrom($categoryString);

        if ($category === null) {
            return $this->askForCategory($categoryString);
        }

        return $category;
    }

    private function getRequestedSources(): array
    {
        $sourceOption = $this->option('source');

        if ($sourceOption === null) {
            return NewsSource::cases();
        }

        $sourceOptions = collect(\preg_split(self::ARRAY_DELIMITER_REGEX, $sourceOption, -1, PREG_SPLIT_NO_EMPTY))
            ->map(fn(string $s) => NewsSource::tryFrom($s))
            ->filter(fn(?NewsSource $s) => null !== $s)
            ->unique()
            ->toArray();

        if ([] === $sourceOptions) {
            $this->error("No valid news sources found for option: {$this->option('source')}");
            $sourceOptions = $this->askForNewsSources();
        }

        return $sourceOptions;
    }

    private function askForCategory(?string $invalidValue = null): NewsCategory
    {
        if ($invalidValue !== null) {
            $this->error("Invalid category: {$invalidValue}");
        }

        $categoryValues = collect(NewsCategory::cases())
            ->map(
                fn(NewsCategory $category) =>
                sprintf('%s - (%s)', $category->value, $category->getReadable()),
            )
            ->toArray();

        $chosenCategory = $this->choice(
            'Please select a valid news category:',
            $categoryValues,
            0
        );

        $chosenValue = \explode(' - ', $chosenCategory)[0];

        return NewsCategory::from($chosenValue);
    }

    public function askForNewsSources(): array
    {
        $sourceValues = collect(NewsSource::cases())
            ->map(fn($source) => sprintf('%s - (%s)', $source->value, $source->getReadable()))
            ->toArray();

        $chosenSources = $this->choice(
            'Please select news sources to import from (comma separated for multiple):',
            $sourceValues,
            null,
            null,
            true
        );

        $sources = [];

        if (!is_array($chosenSources)) {
            $chosenSources = [$chosenSources];
        }

        foreach ($chosenSources as $chosenSource) {
            $chosenValue = \explode(' - ', $chosenSource)[0];
            $sources[] = NewsSource::from($chosenValue);
        }

        return $sources;
    }

    private function startImport(NewsQuery $query, NewsSource $source): void
    {
        $newsSource = $this->newsSourceFactory->create($source);

        $this->info('Import initialized for source: ' . $source->getReadable());

        dispatch(new ProcessNewsImport($query, $source));

        $this->info('Jobs have been dispatched for ' . $source->getReadable());
    }
}
