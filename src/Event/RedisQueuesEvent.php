<?php
namespace Digikala\Event;

use Digikala\Services\RedisService;
use Symfony\Contracts\EventDispatcher\Event;

class RedisQueuesEvent extends Event
{
    private $queues = [];

    public function addToQueue(string $name, string $redisServiceCode = RedisService::class) : self
    {
        $this->queues[$name] = [
            'type' => 'list',
            'redis' => $redisServiceCode
        ];
        return $this;
    }

    public function getQueues() : array
    {
        return $this->queues;
    }
}
