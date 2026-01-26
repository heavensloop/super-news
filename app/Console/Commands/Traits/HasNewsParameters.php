<?php

namespace App\Console\Commands\Traits;

use App\Enum\NewsCategory;
use App\Enum\NewsSource;

trait HasNewsParameters
{
    protected const string ARRAY_DELIMITER_REGEX = '/,\s*/';

    protected function getCategoryInput(bool $isRequired = true): ?NewsCategory
    {
        $categoryString = $this->argument('category');

        if ($categoryString === null && $isRequired) {
            return $this->askForCategory();
        }

        return NewsCategory::tryFrom($categoryString);
    }

    protected function getNewsSource(): array
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

    private function askForNewsSources(): array
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
}
