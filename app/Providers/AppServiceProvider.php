<?php

namespace App\Providers;

use App\Contracts\FeedDownloaderInterface;
use App\Contracts\ThumbnailCacheInterface;
use App\Services\FeedDownloaderService;
use App\Services\PornstarFeedDownloader;
use App\Services\ThumbnailCacheService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(FeedDownloaderInterface::class, FeedDownloaderService::class);
        $this->app->bind(ThumbnailCacheInterface::class, ThumbnailCacheService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
