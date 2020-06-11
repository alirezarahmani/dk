<?php

namespace Digikala\Services;

use Digikala\Event\RedisQueuesEvent;

/**
 * Class QueueService
 * @package Digikala\Services
 */
class QueueService
{
    public const REDIS_QUEUES = 'redis_queue';
    /**
     * @var array
     */
    private array $definitions = [];

    public function __construct()
    {
        $event = new RedisQueuesEvent();
        $this->serviceEventDispatcher()->dispatch(REDIS_QUEUES, $event);
        $this->definitions = $event->getQueues();
    }

    public function add(string $queueName, array $element, int $score = null): self
    {
        $element = json_encode($element);
        $definition = $this->getDefinition($queueName);
        $redis = $this->getRedis($definition['redis']);
        if ($definition['type'] == 'set_ordered') {
            $redis->sAdd($queueName . ':set', $element);
            $redis->lPush($queueName, $element);
        } elseif ($definition['type'] == 'set_unordered') {
            $redis->sAdd($queueName, $element);
        } elseif ($definition['type'] == 'set_score') {
            $redis->zAdd($queueName, [$element => $score]);
        } else {
            $redis->lPush($queueName, $element);
        }
        return $this;
    }

    public function getAndRemove(string $queueName, int $numElements = 1, int $maxScore = null)
    {
        $definition = $this->getDefinition($queueName);
        $redis = $this->getRedis($definition['redis']);
        if ($numElements > 1 && $definition['type'] != 'set_score') {
            $values = $redis->lRange($queueName, 0, $numElements - 1);
            $redis->lTrim($queueName, $numElements, -1);
            return array_map(
                function ($v) {
                    return json_decode($v, true);
                },
                $values
            );
        }

        if ($definition['type'] == 'set_ordered') {
            if ($key = $redis->rPop($queueName)) {
                $removed = $redis->sRem($queueName . ':set', $key);
                if ($removed) {
                    return json_decode($key, true);
                }
            }
            return null;
        } elseif ($definition['type'] == 'set_unordered') {
            $value = $redis->sPop($queueName);
            return $value ? json_decode($value, true) : null;
        } elseif ($definition['type'] == 'set_score') {
            if ($values = $redis->zRangeByScore($queueName, '-inf', $maxScore, ['limit' => [0, $numElements]])) {
                $redis->zRemRangeByRank($queueName, 0, count($values) - 1);
            }
            if (!$values) {
                return null;
            }
            if ($numElements == 1) {
                return json_decode($values[0], true);
            }
            return array_map(
                function ($v) {
                    return json_decode($v, true);
                },
                $values
            );
        }
        $value = $redis->rPop($queueName);
        return $value ? json_decode($value, true) : null;
    }


    private function getDefinition(string $name): array
    {
        if (!isset($this->definitions[$name])) {
            throw new \InvalidArgumentException("redis queue $name is not registered");
        }
        return $this->definitions[$name];
    }

    private function getRedis(string $serviceCode): RedisService
    {
        return $this->service($serviceCode);
    }
}
