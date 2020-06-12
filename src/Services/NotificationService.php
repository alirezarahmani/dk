<?php

declare(strict_types=1);

namespace Digikala\Services;

use Assert\Assertion;
use Digikala\Lib\Notifications\Exceptions\NotificationFailedException;
use Digikala\Lib\Notifications\NotificationInterface;
use Digikala\Repository\Persistence\NotificationRepository;

/**
 * Class NotificationService
 * @package Digikala\Services
 */
class NotificationService
{
    /**
     * @var NotificationInterface
     */
    private NotificationInterface $notification;

    /**
     * @var NotificationRepository
     */
    private NotificationRepository $repository;

    /**
     * @var QueueService
     */
    private QueueService $queue;

    /**
     * NotificationService constructor.
     * @param NotificationInterface $notification
     * @param NotificationRepository $notificationRepository
     * @param QueueService $queue
     */
    public function __construct(NotificationInterface $notification, NotificationRepository $notificationRepository, QueueService $queue)
    {
        $this->notification = $notification;
        $this->repository = $notificationRepository;
        $this->queue = $queue;
    }

    /**
     * @param string $destination
     * @param string $body
     * @throws \Assert\AssertionFailedException
     */
    public function send(string $destination , string $body)
    {
        Assertion::notEmpty($destination, 'wrong number');
        Assertion::notEmpty($body, 'there must be a body');
        Assertion::eq(strlen($destination), 11, 'number should be 11');

        try {
            $this->notification->send($destination, $body);
            $this->repository->store([
                'mobile' => $destination,
                'body' => $body,
                'status' => NotificationRepository::SENT
            ]);
        } catch (NotificationFailedException $e) {
            $id = $this->repository->store(
                [
                    'mobile' => $destination,
                    'body' => $body,
                    'status' => NotificationRepository::FAILED
                ]
            );
            $this->queue->add(NotificationRepository::QUEUE,
                [
                    'id' => $id
                ]
            );
        }
    }

    /**
     * @param int $id
     * @throws \Assert\AssertionFailedException
     * @throws NotificationFailedException
     */
    public function resend(int $id)
    {
        try {
            $result = $this->repository->find($id);
            Assertion::notEmpty($result, $id . ' is not valid');
            $this->notification->send($result['destination'], $result['body']);
            $result['status'] = NotificationRepository::SENT;
            $this->repository->store($result);
        } catch (NotificationFailedException | \InvalidArgumentException $e) {
            $this->queue->add(NotificationRepository::QUEUE,
                [
                    'id' => $id
                ]
            );
        }
    }
}