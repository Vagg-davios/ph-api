<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FeedProcessorService;

class DownloadPornstarsFeed extends Command
{
    protected $signature = 'download:pornstars-feed';
    protected $description = 'Download and cache pornstar feed and thumbnails';

    /**
     * @throws \Exception
     */
    public function handle(FeedProcessorService $service): void
    {
        try {
            $this->info("Processing feed..");
            $service->processDailyFeed();
            $this->info('Pornstars downloaded and updated successfully.');
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            throw $e;
        }
    }
}
