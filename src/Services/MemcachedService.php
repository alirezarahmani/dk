<?php

declare(strict_types=1);

namespace Digikala\Services;

use InvalidArgumentException;

/**
 * Class MemcachedService
 * @package SocialNetwork\Application\Services
 */
class MemcachedService
{
    /**
     * @var \Memcached
     */
    private $memcached;

    /**
     * @param string $key
     * @return bool|mixed|null
     * @throws \Exception
     */
    public function get(string $key)
    {
        $value = $this->executeCommand('get', [$key]);
        if (!$value) {
            if ($this->getMemcached()->getResultCode() == \Memcached::RES_NOTFOUND) {
                return null;
            }
            return false;
        }
        return $value;
    }

    /**
     * @param string $key
     * @param $value
     * @throws \Exception
     */
    public function set(string $key, $value): void
    {
        $this->executeCommand('set', [$key, $value, 0]);
    }

    /**
     * @param string $key
     * @param $value
     * @param int $ttl
     * @throws \Exception
     */
    public function setExpire(string $key, $value, int $ttl): void
    {
        if ($ttl > 2592000) {
            throw new InvalidArgumentException("TTL too big: $ttl");
        }
        $this->executeCommand('set', [$key, $value, $ttl]);
    }

    /**
     * @param string $key
     * @return mixed
     * @throws \Exception
     */
    public function delete(string $key)
    {
        return $this->executeCommand('delete', [$key]);
    }

    /**
     * @param string $command
     * @param array $args
     * @return mixed
     * @throws \Exception
     */
    private function executeCommand(string $command, array $args = [])
    {
        $memcached = $this->getMemcached();
        $value = call_user_func_array([$memcached, $command], $args);
        $resultCode = $memcached->getResultCode();
        if ($resultCode != \Memcached::RES_SUCCESS) {
            if ($resultCode != \Memcached::RES_NOTFOUND && $resultCode != \Memcached::RES_NOTSTORED) {
                throw new \Exception("Invalid response from memcached: " . $memcached->getResultMessage());
            }
        }
        return $value;
    }

    /**
     * @return \Memcached
     */
    private function getMemcached(): \Memcached
    {
        if ($this->memcached === null) {
            preg_match('/(?P<protocol>\w+):\/\/(?P<host>[0-9a-z._]*):(?P<port>\d+)/', 'memcached:5000', $matches);
            $this->memcached = new \Memcached();
            $this->memcached->setOption(\Memcached::OPT_BINARY_PROTOCOL, false);
            $this->memcached->addServer('memcached', 5000);
        }
        return $this->memcached;
    }
}
