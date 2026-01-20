<?php

use App\Enum\NewsCategory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schedule;

(function (int $importStartHour) {
    $timeToStart = Carbon::now()->setHour($importStartHour);

    collect(NewsCategory::cases())->each(function (NewsCategory $category, $key) use ($timeToStart) {
        $from = now()->subDays(1)->format('Y-m-d');
        $to = now()->format('Y-m-d');
        $startMinute = $key * 10; // Stagger start times by category
        $timeToStart->setMinute($startMinute)->setSecond(0);

        Schedule::command(sprintf(
            'app:news:import --from=%s --to=%s --category=%s',
            $from,
            $to,
            $category->value
        ))->dailyAt($timeToStart->format('H:i'))
        ->name(sprintf('news:import:%s', $category->value));
    });
})((int) config('services.news.import_start_hour', 1));
