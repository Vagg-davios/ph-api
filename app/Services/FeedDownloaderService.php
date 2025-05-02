<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\FeedDownloaderInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class FeedDownloaderService implements FeedDownloaderInterface
{
    /**
     * @var mixed|string
     */
    public string $feedUrl;

    public function __construct()
    {
        $this->feedUrl = Config::get('services.feed.url');
    }

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
        $response = Http::get($this->feedUrl);

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
