<?php

namespace Digikala\Services;

use Symfony\Component\DependencyInjection\Container;

/**
 * Class QueueService
 * @package Digikala\Services
 */
class QueueService
{
    /**
     * @var RedisService
     */
    private RedisService $redisService;

    /**
     * QueueService constructor.
     * @param RedisService $redisService
     */
    public function __construct(RedisService $redisService)
    {
        $this->redisService = $redisService;
    }

    /**
     * @param string $queueName
     * @param array $element
     * @return $this
     * @throws \Exception
     */
    public function add(string $queueName, array $element): self
    {
        $element = json_encode($element);
        $redis = $this->redisService;
        $redis->sAdd($queueName . ':set', $element);
        $redis->lPush($queueName, $element);
        return $this;
    }

    /**
     * @param string $queueName
     * @param int $numElements
     * @return array|mixed|null
     * @throws \Exception
     */
    public function getAndRemove(string $queueName, int $numElements = 1)
    {
        $redis = $this->redisService;
        if ($numElements > 1) {
            $values = $redis->lRange($queueName, 0, $numElements - 1);
            $redis->lTrim($queueName, $numElements, -1);
            return array_map(
                function ($v) {
                    return json_decode($v, true);
                },
                $values
            );
        }

        if ($key = $redis->rPop($queueName)) {
            $removed = $redis->sRem($queueName. ':set', $key);
            if ($removed) {
                return json_decode($key, true);
            }
        }
        return null;
    }
}
