<?php

namespace App\Providers;

use App\Services\News\NewsSourceFactory;
use App\Services\News\NewsSourceInterface;
use Carbon\CarbonImmutable;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Spatie\StructureDiscoverer\Discover;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $newsSources = Discover::in(app_path('Services/News/Sources'))->implementing(NewsSourceInterface::class)->get();

        foreach ($newsSources as $newsSource) {
            $this->app->tag($newsSource, NewsSourceInterface::class);
        }

        $this->app->singleton(NewsSourceFactory::class, function ($app) {
            return new NewsSourceFactory($app->tagged(NewsSourceInterface::class));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();

        JsonResource::withoutWrapping();
    }

    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null
        );
    }
}
