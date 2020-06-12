<?php

namespace Digikala\Lib\Notifications;

use Digikala\Lib\Notifications\Exceptions\NotificationFailedException;

/**
 * Class Sms
 * @package Digikala\Lib\Notifications
 */
class Sms implements NotificationInterface
{
    /**
     * @param string $destination
     * @param string $body
     */
    public function send(string $destination, string $body): void
    {
        // it is just a mock
        $number = rand(33,45487986444444);
        if ($number % 2 == 0) {
            throw new NotificationFailedException('');
        }
    }
}