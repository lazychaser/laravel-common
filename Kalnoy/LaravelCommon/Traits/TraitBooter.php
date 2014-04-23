<?php

namespace Kalnoy\LaravelCommon\Traits;

use ReflectionClass;

trait TraitBooter {

    public static function boot()
    {
        parent::boot();

        $reflection = new ReflectionClass(__CLASS__);

        foreach ($reflection->getTraits() as $trait)
        {
            $method = 'boot'.$trait->getShortName().'Trait';

            if (method_exists(__CLASS__, $method))
            {
                forward_static_call([ __CLASS__, $method ]);
            }
        }
    }
}