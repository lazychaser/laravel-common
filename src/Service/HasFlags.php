<?php

namespace Kalnoy\LaravelCommon\Service;

use Illuminate\Support\Arr;

trait HasFlags
{
    /**
     * @param $flag
     * @param $value
     *
     * @return $this
     */
    public function toggleFlag($flag, $value)
    {
        $flags = $this->getFlags();

        if ($value) {
            $flags |= (1 << $flag);
        } else {
            $flags &= ~(1 << $flag);
        }

        $this->attributes[$this->getFlagsName()] = $flags;

        return $this;
    }

    /**
     * @param $flag
     *
     * @return bool
     */
    public function getFlag($flag)
    {
        return ($this->getFlags() & (1 << $flag)) > 0;
    }

    /**
     * @return mixed
     */
    protected function getFlags()
    {
        return Arr::get($this->attributes, $this->getFlagsName(), 0);
    }

    /**
     * @return string
     */
    protected function getFlagsName()
    {
        return 'flags';
    }
}