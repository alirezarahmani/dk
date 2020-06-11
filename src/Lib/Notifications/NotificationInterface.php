<?php

namespace Digikala\Lib\Notifications;

/**
 * Interface NotificationInterface
 * @package Digikala\Lib\Notifications
 */
interface NotificationInterface {
    /**
     * @param string $destination
     * @param string $body
     */
    public function send(string $destination, string $body):void;

}