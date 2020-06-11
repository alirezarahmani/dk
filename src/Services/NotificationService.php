<?php

declare(strict_types=1);

namespace Digikala\Services;

use Digikala\Lib\Notifications\Exceptions\NotificationFailedException;
use Digikala\Lib\Notifications\NotificationInterface;

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
     * NotificationService constructor.
     * @param NotificationInterface $notification
     */
    public function __construct(NotificationInterface $notification)
    {
        $this->notification = $notification;
    }

    public function send(string $destination , string $body)
    {
        try {
            $this->notification->send($destination, $body);

        } catch (NotificationFailedException $e) {

        }
    }
}