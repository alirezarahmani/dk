<?php

declare(strict_types=1);

namespace Digikala\Repository\NonPersistence;

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

    const MOBILE_INDEX = 'mobile_index';
    const ID_INDEX = 'id_index';
    const ALL_MSG = 'msg';
    const TOP_TEN =  'top_ten';
    const API_USAGE = 'api_usage';
    const API_FAULT = 'api_fault';

    /**
     * NotificationInMemoryRepository constructor.
     * @param CacheStorageInterface $cacheStorage
     */
    public function __construct(CacheStorageInterface $cacheStorage)
    {
        $this->cacheStorage = $cacheStorage;
    }

    /**
     * @return array
     */
    public static function cacheIndices(): array
    {
        return [
            self::MOBILE_INDEX => new CacheIndex('mobile'),
            self::ID_INDEX => new CacheIndex('id'),
        ];
    }

    /**
     * @return CacheStorageInterface
     */
    public function getCacheStorage(): CacheStorageInterface
    {
        return $this->cacheStorage;
    }
}