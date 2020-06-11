<?php

declare(strict_types=1);

namespace Digikala\Services;

class TimeService
{
    const SECOND = 1;
    const MINUTE = 60;
    const HOUR = 3600;
    const DAY = 86400;
    const WEEK = 604800;
    const MONTH = 2592000;
    const YEAR = 31104000;

    const NAMES = [
        self::YEAR => 'year',
        self::MONTH => 'month',
        self::WEEK => 'week',
        self::DAY => 'day',
        self::HOUR => 'hour',
        self::MINUTE => 'minute',
        self::SECOND => 'second',
    ];
}