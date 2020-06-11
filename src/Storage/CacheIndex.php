<?php

declare(strict_types=1);

namespace Digikala\Storage;

class CacheIndex
{
    const KEY_PREFIX = 'Storage';

    private $field;

    public function __construct(string $field)
    {
        $this->field = $field;
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getKey(string $index, string $value)
    {
        return self::KEY_PREFIX . ':' . $index . ':' . $this->field . ':' . $value;
    }
}
