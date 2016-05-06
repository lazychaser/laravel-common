<?php

namespace Kalnoy\LaravelCommon\DataMapping\Eloquent\Scheme\Helpers;

use Kalnoy\LaravelCommon\DataMapping\Eloquent\Scheme\ScalarAttribute;

/**
 * Class Scalar
 *
 * @method static ScalarAttribute string($name, $attribute = null)
 * @method static ScalarAttribute bool($name, $attribute = null)
 * @method static ScalarAttribute int($name, $attribute = null)
 * @method static ScalarAttribute float($name, $attribute = null)
 * @method static ScalarAttribute number($name, $attribute = null)
 * 
 * @package Kalnoy\LaravelCommon\DataMapping\Eloquent\Scheme\Helpers
 */
class Scalar
{
    /**
     * @return ScalarAttribute
     */
    public static function __callStatic($name, $arguments)
    {
        $attribute = isset($arguments[1]) ? $arguments[1] : null;
        
        $model = new ScalarAttribute($arguments[0], $attribute);
        
        return $model->$name();
    }

}