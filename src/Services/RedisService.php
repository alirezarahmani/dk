<?php

namespace Digikala\Services;

/**
 * Class RedisService
 * @package Digikala\Services
 */
class RedisService
{
    /**
     * @var string
     */
    private string $name;
    /**
     * @var string
     */
    private string $keyNamespace;
    /**
     * @var
     */
    private $redis;

    /**
     * RedisService constructor.
     * @param string $poolName
     * @param string $keyNamespace
     */
    public function __construct(string $poolName = 'test', string $keyNamespace = 'tests')
    {
        $this->name = $poolName;
        $this->keyNamespace = $keyNamespace;
    }

    /**
     * @param string $command
     * @param array $args
     * @param bool $disableSerializer
     * @return mixed
     */
    private function executeCommand(string $command, array $args = [], bool $disableSerializer = false)
    {
        if ($disableSerializer) {
            $serializer = $this->getRedis()->getOption(\Redis::OPT_SERIALIZER);
            $this->getRedis()->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_NONE);
            $value = call_user_func_array([$this->getRedis(), $command], $args);
            $this->getRedis()->setOption(\Redis::OPT_SERIALIZER, $serializer);
        } else {
            $value = call_user_func_array([$this->getRedis(), $command], $args);
        }
        return $value;
    }

    /**
     * @return \Redis
     */
    private function getRedis(): \Redis
    {
        if (!$this->name) {
            throw new \InvalidArgumentException('Redis name is not specified');
        }
        if ($this->redis === null) {
            preg_match('/(?P<protocol>\w+):\/\/(?P<host>[0-9a-z._]*):(?P<port>\d+)/', 'tcp://redis:6379', $matches);
            $this->redis = new \Redis();
            $this->redis->connect($matches['host'], $matches['port'], 2);
            $this->redis->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_IGBINARY);
            $prefix = $this->keyNamespace;
            $this->redis->setOption(\Redis::OPT_PREFIX, $prefix);
        }
        return $this->redis;
    }

    /**
     * @param string $key
     * @param $value1
     * @param null $value2
     * @param null $valueN
     * @return int
     */
    public function sAdd(string $key, $value1, $value2 = null, $valueN = null): int
    {
        return $this->executeCommand('sAdd', func_get_args());
    }

    /**
     * @param string $key
     * @param $value1
     * @param null $value2
     * @param null $valueN
     * @return mixed
     */
    public function lPush(string $key, $value1, $value2 = null, $valueN = null)
    {
        return $this->executeCommand('lPush', func_get_args());
    }

    /**
     * @param string $key
     * @param int $start
     * @param int $end
     * @return array
     */
    public function lRange(string $key, int $start, int $end): array
    {
        return $this->executeCommand('lRange', func_get_args());
    }

    /**
     * @param string $key
     * @param int $start
     * @param int $stop
     * @return mixed
     */
    public function lTrim(string $key, int $start, int $stop)
    {
        return $this->executeCommand('lTrim', func_get_args());
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function rPop(string $key)
    {
        return $this->executeCommand('rPop', func_get_args());
    }

    /**
     * @param string $key
     * @param $member1
     * @param null $member2
     * @param null $memberN
     * @return int
     */
    public function sRem(string $key, $member1, $member2 = null, $memberN = null): int
    {
        return $this->executeCommand('sRem', func_get_args());
    }

}
