<?php

declare(strict_types=1);

namespace Digikala\Storage;

use Digikala\Services\MemcachedService;

/**
 * Class MemcachedCacheStorage
 * @package Digikala\Storage
 */
class MemcachedCacheStorage implements CacheStorageInterface
{
    /**
     * @var MemcachedService
     */
    private $memcached;

    /**
     * MemcachedCacheStorage constructor.
     * @param MemcachedService $memcached
     */
    public function __construct(MemcachedService $memcached)
    {
        $this->memcached = $memcached;
    }

    /**
     * @param string $key
     * @param $value
     * @param int $ttl
     * @throws \Exception
     */
    public function set(string $key, $value, int $ttl)
    {
        $this->memcached->setExpire($key, $value, $ttl);
    }

    /**
     * @param string $key
     * @return bool|mixed|null
     * @throws \Exception
     */
    public function get(string $key)
    {
        return $this->memcached->get($key);
    }
}
