<?php

use App\Enum\NewsCategory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schedule;

(function (int $importStartHour) {
    $timeToStart = Carbon::now()->setHour($importStartHour);

    collect(NewsCategory::cases())->each(function (NewsCategory $category, $key) use ($timeToStart) {
        $from = now()->subDays(1);
        $to = now();
        $startMinute = $key * 10; // Stagger start times by category
        $timeToStart->setMinute($startMinute)->setSecond(0);

        Schedule::command(sprintf(
            'app:news:import --from=%s --to=%s --category=%s',
            $from->format('Y-m-d'),
            $to->format('Y-m-d'),
            $category->value
        ))->dailyAt($timeToStart->format('H:i'))
            ->name(sprintf('News import for %s category for news between %s and %s from all sources', $category->value, $from->format('F j, Y'), $to->format('F j, Y')));
    });
})((int) config('services.news.import_start_hour', 1));

Schedule::command('app:news:crawl --limit=100')
    ->dailyAt('03:00')
    ->name('Populate news articles with images and content');
