<?php

namespace Kalnoy\LaravelCommon\Service;

class Helpers
{
    /**
     * @param string $value
     *
     * @return string
     */
    public static function parseNumber($value)
    {
        if (is_numeric($value)) return $value;

        // Replace decimal separator
        $value = str_replace(',', '.', $value);

        $value = filter_var($value,
                            FILTER_SANITIZE_NUMBER_FLOAT,
                            FILTER_FLAG_ALLOW_FRACTION);

        return $value;
    }

    /**
     * @param $value
     *
     * @return int
     */
    public static function isPercents($value)
    {
        return preg_match('/^\d+\s*%$/', $value);
    }
}