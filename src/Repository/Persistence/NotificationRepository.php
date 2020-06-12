<?php

declare(strict_types=1);

namespace Digikala\Repository\Persistence;

use DB;
use Digikala\Repository\NonPersistence\NotificationInMemoryRepository;
use Digikala\Services\MemcachedService;
use Digikala\Storage\CacheStorageInterface;
use Digikala\Storage\MemcachedCacheStorage;

/**
 * Class NotificationRepository
 * @package Digikala\Repository\Persistence
 */
class NotificationRepository
{
    const SMS = 1;
    const SENT = 1;
    const FAILED = 0;
    const QUEUE = 'failed_message';
    private const TABLE = 'notifications';

    private NotificationInMemoryRepository $inMemory;
    private $memcached;

    public function __construct()
    {   /** @var CacheStorageInterface $cashStorage */
        $cashStorage = new MemcachedCacheStorage(new MemcachedService());
        $this->inMemory = new NotificationInMemoryRepository($cashStorage);
        $this->memcached = $cashStorage;
    }

    /**
     * @param array $inputs
     * @return mixed
     * @throws \Assert\AssertionFailedException
     */
    public function store(array $inputs)
    {
        $data = [
            // we have only sms for now
            'type' => self::SMS,
            'mobile' => $inputs['mobile'],
            'body' => $inputs['body'],
            'synced' => 0,
            'status' => $inputs['status'],
            //for now just default value
            'server' => '0.0.0.0',
            'port'   => '80'
        ];
        $id = DB::insertId(self::TABLE, $data);
        $data['id'] = $id;
        $this->addToInMemory($data);
        return $id;
    }

    /**
     * @param $id
     * @param $inputs
     * @throws \Assert\AssertionFailedException
     */
    public function update($id, $inputs)
    {
        $data = [
            // we have only sms for now
            'type' => self::SMS,
            'mobile' => $inputs['mobile'],
            'body' => $inputs['body'],
            'synced' => 0,
            'status' => $inputs['status'],
            //for now just default value
            'server' => '0.0.0.0',
            'port'   => '80',
            'created_at' => new \DateTime()
        ];

        DB::update(self::TABLE, $data, "id=%s", $id);
        $data['id'] = $id;
        $this->addToInMemory($data);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function find($id)
    {
        return DB::query("SELECT * FROM " . self::TABLE . "  WHERE id=%s", $id);
    }

    /**
     * @param array $ids
     * @return mixed
     */
    public function findByIds(array $ids)
    {
        return DB::query("SELECT * FROM " . self::TABLE . "  WHERE id IN (%s)", implode(",", $ids));
    }

    /**
     * @param array $data
     * @throws \Assert\AssertionFailedException
     */
    private function addToInMemory(array $data)
    {
        $this->inMemory->addByIndex(NotificationInMemoryRepository::ID_INDEX, $data);
        $this->inMemory->addByIndex(NotificationInMemoryRepository::MOBILE_INDEX, $data);
    }

    /**
     * just simplifies
     * @throws \Exception
     */
    public function report()
    {
        $ttl = 2592000;
        $data =  DB::query("SELECT count(*) as countAll FROM " . self::TABLE );
        $this->memcached->set(NotificationInMemoryRepository::ALL_MSG, $data['countAll'], $ttl);
        $data =  DB::query("SELECT mobile FROM " . self::TABLE . " group by mobile order by count(mobile) limit 1,10 " );
        $this->memcached->set(NotificationInMemoryRepository::TOP_TEN, $data, $ttl);
        $data =  DB::query("SELECT * FROM " . self::TABLE . "  WHERE server=%s and port=%si", '0.0.0.0', '80');
        $this->memcached->set(NotificationInMemoryRepository::API_USAGE, $data, $ttl);
        $data =  DB::query("SELECT * FROM " . self::TABLE . "  WHERE server=%s and port=%si and status=%se", '0.0.0.0', '80', self::FAILED);
        $this->memcached->set(NotificationInMemoryRepository::API_USAGE, $data, $ttl);
    }
}