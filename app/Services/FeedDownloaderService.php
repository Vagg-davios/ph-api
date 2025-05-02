<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\FeedDownloaderInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class FeedDownloaderService implements FeedDownloaderInterface
{
    public const FEED_URL = 'REMOVED';

    /**
     * @throws \JsonException
     */
    public function download(): array
    {
        $response = $this->fetchFeed();
        $json = $this->cleanJson($response->body());

        return json_decode($json, true, 512, JSON_THROW_ON_ERROR);
    }

    private function fetchFeed(): Response
    {
        $response = Http::get(self::FEED_URL);

        if (!$response->successful()) {
            throw new \RuntimeException("Feed download failed with status: {$response->status()}");
        }

        return $response;
    }

    private function cleanJson(string $json): string
    {
        return preg_replace('/\\\\x[0-9A-Fa-f]{2}/', '', $json);
    }
}
