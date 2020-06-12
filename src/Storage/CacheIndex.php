<?php

declare(strict_types=1);

namespace Digikala\Storage;

/**
 * Class CacheIndex
 * @package Digikala\Storage
 */
class CacheIndex
{
    const KEY_PREFIX = 'Storage';

    /**
     * @var string
     */
    private $field;

    /**
     * CacheIndex constructor.
     * @param string $field
     */
    public function __construct(string $field)
    {
        $this->field = $field;
    }

    /**
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @param string $index
     * @param $value
     * @return string
     */
    public function getKey(string $index, $value)
    {
        return self::KEY_PREFIX . ':' . $index . ':' . $this->field . ':' . $value;
    }
}
