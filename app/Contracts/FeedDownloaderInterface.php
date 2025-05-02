<?php

declare(strict_types=1);

namespace App\Contracts;

interface FeedDownloaderInterface
{
    public function download(): array;
}
