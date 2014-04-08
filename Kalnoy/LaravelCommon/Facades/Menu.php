<?php

namespace Kalnoy\LaravelCommon\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Kalnoy\LaravelCommon\Html\MenuBuilder
 */
class Menu extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'menu'; }
}