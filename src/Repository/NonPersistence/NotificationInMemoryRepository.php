<?php

declare(strict_types=1);

namespace Digikala\Repositories\NonPersistence;

use Digikala\Repository\NonPersistence\RepositoryInterface;
use Digikala\Storage\CacheIndex;
use Digikala\Storage\CacheStorageInterface;

/**
 * Class NotificationInMemoryRepository
 * @package Digikala\Repositories\NonPersistence
 */
class NotificationInMemoryRepository extends InMemoryRepository implements RepositoryInterface
{
    /**
     * @var CacheStorageInterface
     */
    private CacheStorageInterface $cacheStorage;

    const READ_INDEX = 'username_index';
    const FOLLOWS_INDEX = 'follows_index';
    const TIMELINE_INDEX = 'time_index';

    public function __construct(CacheStorageInterface $cacheStorage)
    {
        $this->cacheStorage = $cacheStorage;
    }

    public static function cacheIndices(): array
    {
        return [
            self::READ_INDEX => new CacheIndex('username'),
            self::TIMELINE_INDEX => new CacheIndex('username'),
            self::FOLLOWS_INDEX => new CacheIndex('follows')
        ];
    }

    public function getCacheStorage(): CacheStorageInterface
    {
        return $this->cacheStorage;
    }
}