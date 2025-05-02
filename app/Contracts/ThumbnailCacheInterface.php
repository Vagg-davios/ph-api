<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\Pornstar;

interface ThumbnailCacheInterface
{
    public function cache(Pornstar $pornstar, array $thumbnails): void;
}
