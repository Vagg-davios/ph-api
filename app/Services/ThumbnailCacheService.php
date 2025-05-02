<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\ThumbnailCacheInterface;
use App\Models\Pornstar;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

class ThumbnailCacheService implements ThumbnailCacheInterface
{
    public const CACHE_PREFIX = 'pornstar:thumbnail:';
    private const CACHE_TTL = 86400; // 24h

    public function cache(Pornstar $pornstar, array $thumbnails): void
    {
        $url = $this->extractUrl($thumbnails);
        if (!$url) return;

        $cacheKey = self::CACHE_PREFIX . md5($url);
        $filename = 'thumbnails/' . $pornstar->external_id . '_' . basename($url);

        if ($cached = Redis::get($cacheKey)) {
            $this->updateThumbnail($pornstar, $cached);
            return;
        }

        if (Storage::disk('public')->exists($filename)) {
            Redis::setex($cacheKey, self::CACHE_TTL, $filename);
            $this->updateThumbnail($pornstar, $filename);
            return;
        }

        $this->downloadAndCache($pornstar, $url, $filename, $cacheKey);
    }

    private function extractUrl(array $thumbnails): ?string
    {
        return $thumbnails[0]['urls'][0] ?? null;
    }

    private function downloadAndCache(Pornstar $pornstar, string $url, string $filename, string $cacheKey): void
    {
        try {
            $response = Http::timeout(30)->get($url);

            if ($response->successful()) {
                Storage::disk('public')->put($filename, $response->body());
                Redis::setex($cacheKey, self::CACHE_TTL, $filename);
                $this->updateThumbnail($pornstar, $filename);
            }
        } catch (\Exception $e) {
            Log::error("Thumbnail download failed: {$url} - " . $e->getMessage());
        }
    }

    private function updateThumbnail(Pornstar $pornstar, string $path): void
    {
        $pornstar->update(['thumbnail_path' => $path]);
    }
}
