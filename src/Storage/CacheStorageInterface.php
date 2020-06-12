<?php
declare(strict_types=1);
namespace Digikala\Storage;
/**
 * Interface CacheStorageInterface
 * @package Digikala\Storage
 */
interface CacheStorageInterface
{
    /**
     * @param string $key
     * @param $value
     * @param int $ttl
     * @return mixed
     */
    public function set(string $key, $value, int $ttl);

    /**
     * @param string $key
     * @return mixed
     */
    public function get(string $key);
}
