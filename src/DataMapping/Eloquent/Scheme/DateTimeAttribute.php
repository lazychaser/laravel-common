<?php

namespace Kalnoy\LaravelCommon\DataMapping\Eloquent\Scheme;

use Carbon\Carbon;

class DateTimeAttribute extends BasicAttribute
{
    /**
     * @var string
     */
    protected $format;

    /**
     * DateTimeAttribute constructor.
     *
     * @param $id
     * @param string $format
     * @param string $attribute
     */
    public function __construct($id, $format, $attribute = null)
    {
        parent::__construct($id, $attribute);

        $this->format = $format;
    }

    /**
     * @param string $value
     *
     * @return Carbon|null
     */
    public function parse($value)
    {
        return empty($value)
            ? null
            : Carbon::createFromFormat($this->format, $value);
    }

}