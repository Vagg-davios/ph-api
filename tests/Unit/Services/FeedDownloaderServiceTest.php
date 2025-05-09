<?php

namespace Tests\Unit\Services;

use App\Contracts\FeedDownloaderInterface;
use App\Services\FeedDownloaderService;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(FeedDownloaderService::class)]
class FeedDownloaderServiceTest extends TestCase
{
    private FeedDownloaderInterface $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new FeedDownloaderService();
    }

    public function testDownloadFunctionIsSuccessful()
    {
        Http::fake([
            $this->service->feedUrl => Http::response(
                '{"items": [{"id": 1, "name": "Test"}]}',
                200
            )
        ]);

        $result = $this->service->download();

        $this->assertEquals(['items' => [['id' => 1, 'name' => 'Test']]], $result);
    }

    public function testDownloadFunctionClearsInvalidCharacters()
    {
        Http::fake([
            $this->service->feedUrl => Http::response(
                '{"items": [{"id": 1, "name": "Test\xF0"}]}',
                200
            )
        ]);

        $result = $this->service->download();

        $this->assertEquals(['items' => [['id' => 1, 'name' => 'Test']]], $result);
    }

    public function testRuntimeExceptionIsThrownWhenResponseIsNull()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Feed download failed with status: 500');

        Http::fake([
            $this->service->feedUrl => Http::response(null, 500)
        ]);

        $this->service->download();
    }
}
