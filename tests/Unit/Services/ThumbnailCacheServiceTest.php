<?php

namespace Tests\Unit\Services;

use App\Models\Pornstar;
use App\Services\ThumbnailCacheService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(ThumbnailCacheService::class)]
class ThumbnailCacheServiceTest extends TestCase
{
    use RefreshDatabase;

    private ThumbnailCacheService $service;
    private Pornstar $testPornstar;

    protected function setUp(): void
    {
        parent::setUp();

        // fs config
        config(['filesystems.disks.public.root' => storage_path('framework/testing/disks/public')]);

        $this->service = new ThumbnailCacheService();

        $this->testPornstar = Pornstar::create([
            'external_id' => 123,
            'name' => 'Test Star',
            'license' => 'PH',
            'wl_status' => true,
            'thumbnail_path' => null
        ]);

        Storage::fake('public');
        Redis::flushall();
    }

    public function testThumbnailUrlCachingUsesRedisCache()
    {
        $url = 'http://test.com/image.jpg';
        $cacheKey = ThumbnailCacheService::CACHE_PREFIX . md5($url);
        $filename = 'thumbnails/123_image.jpg';

        Redis::setex($cacheKey, 3600, $filename);

        $this->service->cache($this->testPornstar, [
            ['urls' => [$url]]
        ]);

        $this->assertEquals($filename, $this->testPornstar->fresh()->thumbnail_path);
    }

    public function testCacheUsesExistingFileInsteadOfWrite()
    {
        $filename = 'thumbnails/123_image.jpg';
        Storage::disk('public')->put($filename, 'test_content');

        $this->service->cache($this->testPornstar, [
            ['urls' => ['http://test.com/image.jpg']]
        ]);

        $this->assertEquals($filename, $this->testPornstar->fresh()->thumbnail_path);
    }

    public function testCacheStoresNewThumbnailWhenNotCached()
    {
        Http::fake([
            'http://test.com/image.jpg' => Http::response('image_content', 200)
        ]);

        $this->service->cache($this->testPornstar, [
            ['urls' => ['http://test.com/image.jpg']]
        ]);

        $expectedFilename = 'thumbnails/123_image.jpg';
        Storage::disk('public')->assertExists($expectedFilename);
        $this->assertEquals($expectedFilename, $this->testPornstar->fresh()->thumbnail_path);
    }

    public function testCachingHandlesServerError()
    {
        Http::fake([
            'http://test.com/image.jpg' => Http::response(null, 500)
        ]);

        $this->service->cache($this->testPornstar, [
            ['urls' => ['http://test.com/image.jpg']]
        ]);

        $this->assertNull($this->testPornstar->fresh()->thumbnail_path);
    }
}
