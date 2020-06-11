<?php

declare(strict_types=1);

namespace Digikala\Repository\NonPersistence;

use Digikala\Storage\CacheStorageInterface;

interface RepositoryInterface
{
    public function __construct(CacheStorageInterface $cacheStorage);
    public static function cacheIndices(): array;
    public function getCacheStorage(): CacheStorageInterface;
}
