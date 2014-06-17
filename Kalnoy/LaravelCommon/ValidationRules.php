<?php

namespace Kalnoy\LaravelCommon;

/**
 * Validation rules collection.
 */
class ValidationRules {

    /**
     * Validates a value to be a slug. This one accepts only unicode lower-case letters, numbers
     * dash and underscores.
     *
     * @param $attribute
     * @param $value
     * @param $parameters
     *
     * @return bool
     */
    public static function slug($attribute, $value, $parameters)
    {
        return empty($value) or preg_match('/^[\pLl\d\-_]+$/u', $value);
    }

    /**
     * Validate phone number in international format which is
     * `+[1-3 digits country code][10 digits number]`.
     */
    public static function phone($attribute, $value, $parameters)
    {
        return empty($value) or preg_match(PHONE_REGEX, $value);
    }

}