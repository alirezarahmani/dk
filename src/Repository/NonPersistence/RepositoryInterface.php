<?php

declare(strict_types=1);

namespace Digikala\Repository\NonPersistence;

use Digikala\Storage\CacheStorageInterface;

/**
 * Interface RepositoryInterface
 * @package Digikala\Repository\NonPersistence
 */
interface RepositoryInterface
{
    /**
     * RepositoryInterface constructor.
     * @param CacheStorageInterface $cacheStorage
     */
    public function __construct(CacheStorageInterface $cacheStorage);

    /**
     * @return array
     */
    public static function cacheIndices(): array;

    /**
     * @return CacheStorageInterface
     */
    public function getCacheStorage(): CacheStorageInterface;
}
