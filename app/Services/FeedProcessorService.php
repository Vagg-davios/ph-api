<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\FeedDownloaderInterface;
use App\Contracts\ThumbnailCacheInterface;
use App\Models\Pornstar;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class FeedProcessorService
{
    public const PROCESSING_LOCK_KEY = 'pornstar:feed:lock';
    public const LOCK_TIMEOUT = 3600;

    public function __construct(
        private readonly FeedDownloaderInterface $downloader,
        private readonly ThumbnailCacheInterface $thumbnailCache
    ) {}

    public function processDailyFeed(): void
    {
        if (!$this->acquireLock()) {
            Log::info('Feed processing already in progress');
            return;
        }

        try {
            $data = $this->downloader->download();
            $this->processItems($data['items']);
        } finally {
            $this->releaseLock();
        }
    }

    public function processItem(array $item): void
    {
        $pornstar = Pornstar::updateOrCreate(
            ['external_id' => $item['id']],
            [
                'name' => $item['name'],
                'license' => $item['license'] ?? null,
                'wl_status' => $item['wlStatus'] ?? null,
                'link' => $item['link'] ?? null,
                'attributes' => $item['attributes'] ?? null,
                'aliases' => $item['aliases'] ?? [],
            ]
        );

        if (!empty($item['thumbnails'])) {
            $this->thumbnailCache->cache($pornstar, $item['thumbnails']);
        }
    }

    private function processItems(array $items): void
    {
        foreach ($items as $item) {
            try {
                $this->processItem($item);
            } catch (\Exception $e) {
                Log::error("Failed processing item {$item['id']}: " . $e->getMessage());
            }
        }
    }

    private function acquireLock(): bool
    {
        return Redis::set(self::PROCESSING_LOCK_KEY, 1, 'NX', 'EX', self::LOCK_TIMEOUT);
    }

    private function releaseLock(): void
    {
        Redis::del(self::PROCESSING_LOCK_KEY);
    }
}
