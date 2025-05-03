<?php

namespace Tests\Unit\Services;

use App\Contracts\FeedDownloaderInterface;
use App\Contracts\ThumbnailCacheInterface;
use App\Services\FeedProcessorService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Redis;
use Mockery;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(FeedProcessorService::class)]
class FeedProcessorServiceTest extends TestCase
{
    use DatabaseTransactions;

    private FeedProcessorService $service;
    private $downloaderMock;
    private $thumbnailCacheMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');

        $this->downloaderMock = Mockery::mock(FeedDownloaderInterface::class);
        $this->thumbnailCacheMock = Mockery::mock(ThumbnailCacheInterface::class);

        $this->service = new FeedProcessorService(
            $this->downloaderMock,
            $this->thumbnailCacheMock
        );

        Redis::flushall();
    }

    public function testProcessDailyFeedWorksWithLocks(): void
    {
        Redis::shouldReceive('set')
            ->with(FeedProcessorService::PROCESSING_LOCK_KEY, 1, 'NX', 'EX', FeedProcessorService::LOCK_TIMEOUT)
            ->once()
            ->andReturn(true);
        Redis::shouldReceive('del')
            ->with(FeedProcessorService::PROCESSING_LOCK_KEY)
            ->once()
            ->andReturn(1);

        $this->downloaderMock->shouldReceive('download')
            ->once()
            ->andReturn(['items' => []]);

        $this->thumbnailCacheMock->shouldNotReceive('cache');
        $this->service->processDailyFeed();

        Redis::shouldReceive('exists')
            ->with(FeedProcessorService::PROCESSING_LOCK_KEY)
            ->andReturn(false);

        $this->assertFalse(Redis::exists(FeedProcessorService::PROCESSING_LOCK_KEY));
    }

    public function testProcessDailyFeedSkipsIfProcessAlreadyProcessing(): void
    {
        Redis::shouldReceive('set')
            ->with(FeedProcessorService::PROCESSING_LOCK_KEY, 1, 'NX', 'EX', FeedProcessorService::LOCK_TIMEOUT)
            ->andReturn(false); // lock exists

        $this->downloaderMock->shouldNotReceive('download');
        $this->service->processDailyFeed();

        $this->assertTrue(true); // to prevent phpunit from crying
    }

    public function testProcessItemCreatesUnitAccordingToGivenJSON()
    {
        $item = [
            'id' => 1,
            'name' => 'Test',
            'license' => 'PH',
            'wlStatus' => 'active',
            'thumbnails' => [['urls' => ['http://test.com/image.jpg']]]
        ];

        $this->thumbnailCacheMock->shouldReceive('cache')
            ->once();
        $this->service->processItem($item);

        $this->assertDatabaseHas('pornstars', [
            'external_id' => 1,
            'name' => 'Test'
        ]);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
