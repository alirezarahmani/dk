<?php
declare(strict_types=1);
namespace Digikala\Supernova\Service\Profiler\Storage;

use Clockwork\Request\Request;
use Clockwork\Storage\Storage;
use Digikala\Supernova\Service\RedisService;
use Digikala\Supernova\Service\TimeService;

class RedisStorage
{

    private $redis;

    public function __construct(RedisService $redis)
    {
        $this->redis = $redis;
    }

    public function all()
    {
        $redisKey = $this->getRedisKey();
        $values = $this->redis->hGetAll($this->getRedisKey());
        $keys = array_keys($values);
        if ($keys) {
            $this->redis->hDel($redisKey, ...$keys);
        }
        $requests = [];
        foreach ($values as $key => $value) {
            $requests[] = new Request(json_decode($value, true));
        }

        return $requests;
    }

    public function find($id)
    {
        $redisKey = $this->getRedisKey();
        $value = $this->redis->hGet($redisKey, $id);
        if (!$value) {
            return null;
        }
        $this->redis->hDel($redisKey, $id);
        return new Request(json_decode($value, true));
    }

    public function latest()
    {
        $redisKey = $this->getRedisKey();
        $values = $this->redis->hGetAll($this->getRedisKey());
        $keys = array_keys($values);
        if ($keys) {
            $this->redis->hDel($redisKey, ...$keys);
        } else {
            return null;
        }
        $last = array_pop($keys);
        $value = $this->redis->hGet($redisKey, $last);
        if (!$value) {
            return null;
        }
        $this->redis->hDel($redisKey, $last);
        return new Request(json_decode($value, true));
    }



    private function getRedisKey()
    {
        return 'profiler_store';
    }
}
