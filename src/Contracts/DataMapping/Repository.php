<?php

namespace Kalnoy\LaravelCommon\Contracts\DataMapping;

interface Repository
{
    /**
     * Get only existing items.
     */
    const EXISTING = 1;

    /**
     * Get only non-existing items.
     */
    const NON_EXISTING = 2;

    /**
     * Include both existing and new items.
     */
    const ALL = 3;

    /**
     * @param string $key
     * @param int $mode
     *
     * @return mixed
     */
    public function retrieve($key, $mode = self::ALL);

    /**
     * @param array $keys
     * @param int $mode
     *
     * @return mixed
     */
    public function retrieveMany(array $keys, $mode = self::ALL);

    /**
     * @param array|string $keys
     *
     * @return $this
     */
    public function preload($keys);

    /**
     * @return string
     */
    public function primaryKey();

    /**
     * @param mixed $key
     *
     * @return mixed
     */
    public function parseKey($key);

}