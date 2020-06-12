<?php

namespace Digikala\Services;

class RedisService
{
    private string $name;
    private string $keyNamespace;
    private $redis;

    public function __construct(string $poolName = 'test', string $keyNamespace = 'tests')
    {
        $this->name = $poolName;
        $this->keyNamespace = $keyNamespace;
    }

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

    public function sAdd(string $key, $value1, $value2 = null, $valueN = null): int
    {
        return $this->executeCommand('sAdd', func_get_args());
    }

    public function lPush(string $key, $value1, $value2 = null, $valueN = null)
    {
        return $this->executeCommand('lPush', func_get_args());
    }

    public function lRange(string $key, int $start, int $end): array
    {
        return $this->executeCommand('lRange', func_get_args());
    }

    public function lTrim(string $key, int $start, int $stop)
    {
        return $this->executeCommand('lTrim', func_get_args());
    }

    public function rPop(string $key)
    {
        return $this->executeCommand('rPop', func_get_args());
    }

    public function sRem(string $key, $member1, $member2 = null, $memberN = null): int
    {
        return $this->executeCommand('sRem', func_get_args());
    }

}
